<?php
/*
www.mobantu.com
82708210@qq.com
*/
if ( !defined('ABSPATH') ) {exit;}
if(!is_user_logged_in()){
	exit;
}

$total_trade   = $wpdb->get_var("select count(DISTINCT ice_post) as aa from $wpdb->icealipay where ice_success>0");
/////////////////////////////////////////////////www.mobantu.com   82708210@qq.com
$ice_perpage = 20;
$pages = ceil($total_trade / $ice_perpage);
$page=isset($_GET['paged']) ?intval($_GET['paged']) :1;
$offset = $ice_perpage*($page-1);
$list = $wpdb->get_results("select ice_post,ice_title,count(ice_id) as ice_total,sum(ice_price) as ice_money from $wpdb->icealipay where ice_success>0 group by ice_post order by ice_total DESC limit $offset,$ice_perpage");

?>
<div class="wrap">
	<h2>销售排行</h2>
	<p><?php printf(('共<strong>%s</strong>个资源'), $total_trade); ?></p>
	<table class="widefat fixed striped posts">
		<thead>
			<tr>
				<th width="65%">资源名称</th>
				<th width="10%">销量</th>
				<th width="15%">销售额(<?php echo get_option('ice_name_alipay');?>)</th>
				<th width="10%">管理</th>		
			</tr>
		</thead>
		<tbody>
			<?php
			if($list) {
				foreach($list as $value){
					echo "<tr>\n";
					echo "<td><a target=_blank href='".get_permalink($value->ice_post)."'>".get_post($value->ice_post)->post_title."</a></td>\n";
					echo "<td>".getProductSales($value->ice_post)."</td>";
					echo "<td>".intval($value->ice_money)."</td>";
					echo "<td><a target=_blank href='".get_bloginfo('wpurl')."/wp-admin/post.php?post=".$value->ice_post."&action=edit'>编辑</a></td>\n";
					echo "</tr>";  
				}
			}else{
				echo '<tr><td colspan="4" align="center"><strong>没有销售记录</strong></td></tr>';
			}
			?>
		</tbody>
	</table>
	<?php echo erphp_admin_pagenavi($total_trade,$ice_perpage);?>
</div>
