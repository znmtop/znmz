<?php
/*
*作者：mobantu.com
*/
if ( !defined('ABSPATH') ) {exit;}
$user_info=wp_get_current_user();
$issearch = 0;
if(current_user_can('administrator')){
	if(isset($_POST['action'])){
		$action = $_POST['action'];
		if($action == 1){
			$result = $wpdb->query("update $wpdb->iceinfo set endTime = adddate(endTime,interval ".$wpdb->escape($_POST['adddate'])." day) where userType > 0");
			if($result){
				echo '<div class="updated settings-error"><p>批量延长（减少）VIP天数成功！</p></div>';
			}else{
				echo '<div class="error settings-error"><p>操作失败！</p></div>';
			}
		}elseif($action == 2){
			$user = get_user_by('login',$_POST['username']);
			if($user){
				$suid = $user->ID;
				$issearch = 1;
			}else{
				echo '<div class="error settings-error"><p>用户不存在！</p></div>';
			}
		}
	}
	if($issearch){
		$total_trade  = $wpdb->get_var("select count(ice_id) from  ".$wpdb->iceinfo." where userType > 0 and ice_user_id=".$suid);
	}else{
		$total_trade  = $wpdb->get_var("select count(ice_id) from  ".$wpdb->iceinfo." where userType > 0");
	}
	$ice_perpage = 20;
	$pages = ceil($total_trade / $ice_perpage);
	$page=isset($_GET['paged']) ?intval($_GET['paged']) :1;
	$offset = $ice_perpage*($page-1);
	if($issearch){
		$list = $wpdb->get_results("select * from  ".$wpdb->iceinfo." where userType > 0 and ice_user_id=".$suid." order by ice_id DESC limit $offset,$ice_perpage");
	}else{
		$list = $wpdb->get_results("select * from  ".$wpdb->iceinfo." where userType > 0 order by ice_id DESC limit $offset,$ice_perpage");
	}
	?>
	<div class="wrap">
		<h2>VIP用户<?php if($issearch) echo '（当前查询用户：'.$_POST['username'].'）';?></h2>
		<p><?php echo '共有<strong>'.$total_trade.'</strong>个VIP用户'; ?></p>
		<div>
			<h3>批量操作</h3>
			<form method="post">给所有VIP都延长（减少）<input type="number" name="adddate" placeholder="整数天数"> 天的VIP权限 <input type="submit" value="确定操作" class="button"><input type="hidden" name="action" value="1"> （输入负数表示减少天数）</form><br>
			<h3>单用户查询</h3>
			<form method="post">搜索用户：<input type="text" name="username" placeholder="例如：admin"><input type="submit" value="查询" class="button"><input type="hidden" name="action" value="2"></form><br>
		</div>
		<table class="widefat fixed striped posts">
			<thead>
				<tr>
					<th width="15%">用户ID</th>
					<th width="15%">VIP类型</th>
					<th width="15%">到期时间</th>	
					<th width="15%">操作</th>				
				</tr>
			</thead>
			<tbody>
				<?php
				if($list) {
					$erphp_life_name    = get_option('erphp_life_name')?'('.get_option('erphp_life_name').')':'';
					$erphp_year_name    = get_option('erphp_year_name')?'('.get_option('erphp_year_name').')':'';
					$erphp_quarter_name = get_option('erphp_quarter_name')?'('.get_option('erphp_quarter_name').')':'';
					$erphp_month_name  = get_option('erphp_month_name')?'('.get_option('erphp_month_name').')':'';
					$erphp_day_name  = get_option('erphp_day_name')?'('.get_option('erphp_day_name').')':'';
					foreach($list as $value){
						if($value->userType == 6) $typeName = '体验'.$erphp_day_name;
						else {$typeName=$value->userType==7 ?'包月'.$erphp_month_name :($value->userType==8 ?'包季'.$erphp_quarter_name : ($value->userType==10 ?'终身'.$erphp_life_name : '包年'.$erphp_year_name));}
						echo "<tr class=\"vip-$value->ice_user_id\">\n";
						echo "<td>".get_the_author_meta( 'user_login', $value->ice_user_id )."</td>\n";
						echo "<td>$typeName</td>\n";
						echo "<td><input type=text name=p_price_$value->ice_id id=p_price_$value->ice_id value=$value->endTime style=\"width:120px;\" /><input type=button id=editpricebtn_$value->ice_id onclick=editPrice($value->ice_id) value=修改 class=button></td>";
						echo '<td><a href="javascript:;" class="delvip" data-id="'.$value->ice_user_id.'" onclick="delvip('.$value->ice_user_id.')">删除VIP权限</a></td>';
						echo "</tr>";
					}
				}else{
					echo '<tr><td colspan="4" align="center"><strong>没有交易记录</strong></td></tr>';
				}
				?>
			</tbody>
		</table>
		<?php echo erphp_admin_pagenavi($total_trade,$ice_perpage);?>
	</div>
	<script type="text/javascript">
		function delvip(id){
			if(confirm('确认删除VIP权限?')){
				jQuery.ajax({
					type: "post",
					url: "<?php echo constant("erphpdown");?>admin/action/vip.php",
					data: "do=del&uid=" + id,
					date:"",
					dataType: "html",
					success: function (data) {
						if(data == 'success'){
							jQuery('.vip-'+id).remove();
						}
					},
					error: function (request) {
						
				//alert("修改失败");
			}
		});
			}
		}

		function editPrice(id){
			jQuery("#editpricebtn_"+id).val("修改中..");
			jQuery.ajax({
				type: "post",
				url: "<?php echo constant("erphpdown");?>admin/action/vip.php",
				data: "do=edit&id=" + id + "&new_date=" + jQuery("#p_price_"+id).val(),
				date:"",
				dataType: "html",
				success: function (data) {
					if(data == 'success'){
						jQuery("#editpricebtn_"+id).val("修改成功");
					}
				},
				error: function (request) {
					jQuery("#editpricebtn_"+id).val("修改");
					alert("修改失败");
				}
			});
		}

	</script>
<?php }?>
