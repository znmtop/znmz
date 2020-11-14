<?php
/*
www.mobantu.com
82708210@qq.com
*/
if ( !defined('ABSPATH') ) {exit;}
if(!is_user_logged_in())
{
	wp_die('请登录系统');
}

$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->icemoney WHERE ice_success>0");

//分页计算
$ice_perpage = 20;
$pages = ceil($total_trade / $ice_perpage);
$page=isset($_GET['paged']) ?intval($_GET['paged']) :1;
$offset = $ice_perpage*($page-1);
?>

<div class="wrap">
	<?php
	
	
	$adds=$wpdb->get_results("SELECT * FROM $wpdb->icemoney where ice_success=1 order by ice_time DESC limit $offset,$ice_perpage");
	
	?>
	<h2>充值记录</h2><table class="widefat fixed striped posts">

		<thead>
			<tr>
				<th>用户ID</th>
				<th>时间</th>
				<th><?php echo get_option('ice_name_alipay');?></th>
				<th>方式</th>
				<th>管理</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if($adds) {
				foreach($adds as $value)
				{
					echo "<tr>\n";
					echo "<td>".get_the_author_meta( 'user_login', $value->ice_user_id )."</td>";
					echo "<td>".$value->ice_time."</td>";
					echo "<td>$value->ice_money</td>\n";
					if(intval($value->ice_note)==0)
					{
						echo "<td><font color=green>在线充值</font></td>\n";
					}elseif(intval($value->ice_note)==1)
					{
						echo "<td>后台充值</td>\n";
					}
					elseif(intval($value->ice_note)==2)
					{
						echo "<td><font color=blue>转账收款</font></td>\n";
					}
					elseif(intval($value->ice_note)==3)
					{
						echo "<td><font color=orange>转账付款</font></td>\n";
					}
					elseif(intval($value->ice_note)==4)
					{
						echo "<td><font color=orange>mycred兑换</font></td>\n";
					}
					elseif(intval($value->ice_note)==6)
					{
						echo "<td><font color=orange>充值卡</font></td>\n";
					}
					echo '<td><a href="javascript:;" class="delorder" data-id="'.$value->ice_id.'">删除</a></td>';
					echo "</tr>";
				}
			}
			else
			{
				echo '<tr><td colspan="5" align="center"><strong>没有记录</strong></td></tr>';
			}
			?>
		</tbody>
	</table>
	<?php echo erphp_admin_pagenavi($total_trade,$ice_perpage);?>
	　　		
</div>
<script>
	jQuery(".delorder").click(function(){
		if(confirm('确定删除？')){
			var that = jQuery(this);
			that.text("删除中...");
			jQuery.ajax({
				type: "post",
				url: "<?php echo constant("erphpdown");?>admin/action/order.php",
				data: "do=delchong&id=" + jQuery(this).data("id"),
				dataType: "html",
				success: function (data) {
					if(jQuery.trim(data) == '1'){
						that.parent().parent().remove();
					}
				},
				error: function (request) {
					that.text("删除");
					alert("删除失败");
				}
			});
		}
	});
</script>