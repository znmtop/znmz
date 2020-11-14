<?php
/*
www.mobantu.com
82708210@qq.com
*/
if ( !defined('ABSPATH') ) {exit;}
if(!is_user_logged_in()){
	exit;
}

$total_trade   = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->posts WHERE post_status='publish' and post_type='post' ");
/////////////////////////////////////////////////www.mobantu.com   82708210@qq.com
$ice_perpage = 50;
$pages = ceil($total_trade / $ice_perpage);
$page=isset($_GET['paged']) ?intval($_GET['paged']) :1;
$offset = $ice_perpage*($page-1);
$list = $wpdb->get_results("SELECT post_title,ID as ice_id,post_date FROM $wpdb->posts WHERE post_status='publish' and post_type='post' order by post_date DESC limit $offset,$ice_perpage");

?>
<div class="wrap">
	<h2>批量处理</h2>
	<form method="post" style="margin:0 0 10px">
		【收费类型】
		<input type="radio" name="start_down" value="4" checked />不启用 &nbsp;
		<input type="radio" name="start_down" value="1" />下载 &nbsp;
		<input type="radio" name="start_down" value="5" />免登录&nbsp;
		<input type="radio" name="start_down" value="2" />查看&nbsp;
		<input type="radio" name="start_down" value="3" />部分查看<br>
		【VIP优惠】
		<input type="radio" name="viptype" value="1" checked/>无
		<input type="radio" name="viptype" value="4" />VIP专享 &nbsp;
		<input type="radio" name="viptype" value="3" />VIP免费 &nbsp;
		<input type="radio" name="viptype" value="2" />VIP5折&nbsp;
		<input type="radio" name="viptype" value="5" />VIP8折&nbsp;
		<input type="radio" name="viptype" value="6" />年费VIP免费&nbsp;
		<input type="radio" name="viptype" value="7" />终身VIP免费&nbsp;
		<input type="radio" name="viptype" value="8" />年费VIP专享&nbsp;
		<input type="radio" name="viptype" value="9" />终身VIP专享&nbsp;<br>
		【价格】
		<input type="text" id="price" placeholder="留空则不修改价格"><br>
		<input type="button" name="Submit" value="确认批量处理" class="button-primary viptypedo">
	</form>
	<table class="widefat fixed striped posts">
		<thead>
			<tr>
				<th width="3%"><input type="checkbox" id="checkbox" onclick="selectAll()" style='margin-left:0'></th>
				<th>标题</th>
				<th>价格</th>
				<th>VIP优惠</th>
				<th>收费类型</th>
				<th>发布时间</th>
				<th>管理</th>		
			</tr>
		</thead>
		<tbody>
			<?php
			if($list) {
				foreach($list as $value){
					$ice_price = get_post_meta($value->ice_id,"down_price",true);
					$ice_price = $ice_price?$ice_price:'';

					echo "<tr>\n";
					echo "<td><input type='checkbox' class='checkbox' value='".$value->ice_id."'></td>";
					echo "<td><a target=_blank href='".get_permalink($value->ice_id)."'>$value->post_title</a></td>\n";
					echo '<td><input type="number" min="0" step="0.01" name="p_price_'.$value->ice_id.'" id="p_price_'.$value->ice_id.'" value="'.$ice_price.'" style="width:60px;" /><a href="javascript:;" id="editpricebtn_'.$value->ice_id.'" onclick="javascript:editPrice('.$value->ice_id.')" >修改</a></td>';
					echo "<td>".getProductMember($value->ice_id)."</td>";
					echo "<td>".getProductDownType($value->ice_id)."</td>";
					echo "<td>$value->post_date</td>\n";
					echo "<td><a target=_blank href='".get_bloginfo('wpurl')."/wp-admin/post.php?post=".$value->ice_id."&action=edit'>编辑</a></td>\n";
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

	jQuery(".viptypedo").click(function(){
		var that = jQuery(this);
		var ids = '';
		jQuery(".checkbox").each(function() {
			if (jQuery(this).is(':checked')) {
		      ids += ',' + jQuery(this).val();
		  }
		});
		ids = ids.substring(1);
		if (ids.length == 0) {
			alert('请至少选择一项！');
		} else {
			if (confirm("确定操作？")) {
				that.attr("disabled","disabled").val("处理中...");
				jQuery.ajax({
					type: "post",
					url: "<?php echo constant("erphpdown");?>admin/action/vip.php",
					data: "do=type&ids=" + ids+"&price="+jQuery("#price").val() + "&type=" + jQuery("input[name='viptype']:checked").val()+ "&down=" + jQuery("input[name='start_down']:checked").val(),
					date:"",
					dataType: "html",
					success: function (data) {
						if(data == 'success'){
							alert("操作成功");
							location.reload();
						}
					},
					error: function (request) {
						that.attr("disabled","").val("确认");
						alert("操作失败，请稍后重试！");
					}
				});
			}
		}
		return false;
	});

	function editPrice(id){	
		jQuery("#editpricebtn_"+id).text("修改中..");
		jQuery.ajax({
			type: "post",
			url: "<?php echo constant("erphpdown");?>admin/action/price.php",
			data: "do=editprice&postid=" + id + "&new_price=" + jQuery("#p_price_"+id).val(),
			date:"",
			dataType: "html",
			success: function (data) {
				if(data == 'success'){
					jQuery("#editpricebtn_"+id).text("修改成功");
					setTimeout("editsuccess("+id+")",3000)
				}
			},
			error: function (request) {
				jQuery("#editpricebtn_"+id).text("修改");
				alert("修改失败");
			}
		});
	}

	function editsuccess(id){
		jQuery("#editpricebtn_"+id).text("修改");
	}

	function selectAll(){
		if (jQuery('#checkbox').is(':checked')) {
			jQuery(".checkbox").attr("checked", true);
		} else {
			jQuery(".checkbox").attr("checked", false);
		}

	}
</script>
