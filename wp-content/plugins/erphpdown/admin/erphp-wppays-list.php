<?php
/*
*作者：mobantu.com
*/
if ( !defined('ABSPATH') ) {exit;}
if(!is_user_logged_in()){
	exit;
}
global $wpdb, $wppay_table_name;
$total   = $wpdb->get_var("SELECT COUNT(id) FROM $wppay_table_name WHERE order_status=1");
$perpage = 20;
$pages = ceil($total / $perpage);
$page=isset($_GET['paged']) ?intval($_GET['paged']) :1;
$offset = $perpage*($page-1);
$list = $wpdb->get_results("SELECT * FROM $wppay_table_name WHERE order_status=1 ORDER BY order_time DESC limit $offset,$perpage");
?>
<div class="wrap">
	<h2>所有订单</h2>
	<table class="widefat fixed striped posts">
		<thead>
			<tr>
				<th>订单号</th>
				<th>商品名称</th>
				<th>价格</th>
				<th>IP地址</th>
				<th>交易时间</th>	
				<th>用户ID</th>	
				<th>管理</th>
			</tr>
		</thead>
		<tbody>
	<?php
		if($list) {
			foreach($list as $value){
				echo "<tr>\n";
				echo "<td>".$value->order_num."</td>";
				echo "<td><a target='_blank' href='".get_permalink($value->post_id)."'>".get_the_title($value->post_id)."</a></td>\n";
				echo "<td>$value->post_price 元</td>\n";
				echo "<td>$value->ip_address</td>\n";
				echo "<td>$value->order_time</td>\n";
				if($value->user_id){
					echo "<td>".get_user_by('id',$value->user_id)->user_login."</td>";
				}else{
					echo "<td>游客</td>";
				}
				echo '<td><a href="javascript:;" class="delorder" data-id="'.$value->id.'">删除</a></td>';
				echo "</tr>";
			}
		}
		else{
			echo '<tr><td colspan="6" align="center"><strong>没有订单</strong></td></tr>';
		}
	?>
	</tbody>
	</table>
    <?php echo erphp_admin_pagenavi($total,$perpage);?>
</div>
<script>
	jQuery(".delorder").click(function(){
		if(confirm('确定删除？')){
			var that = jQuery(this);
			that.text("删除中...");
			jQuery.ajax({
				type: "post",
				url: "<?php echo constant("erphpdown");?>admin/action/order.php",
				data: "do=delwppay&id=" + jQuery(this).data("id"),
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
