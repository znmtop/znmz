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

$user_info=wp_get_current_user();
$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->down");

//分页计算
$ice_perpage = 20;
$pages = ceil($total_trade / $ice_perpage);
$page=isset($_GET['paged']) ?intval($_GET['paged']) :1;
$offset = $ice_perpage*($page-1);
?>

<div class="wrap">
	<?php
	

	$adds=$wpdb->get_results("SELECT * FROM $wpdb->down order by ice_time DESC limit $offset,$ice_perpage");

	?>
	<h2>免费下载/查看记录</h2><table class="widefat fixed striped posts">
    <p>不统计VIP用户下载对全站用户免费的资源</p>
		<thead>
			<tr>
				<th width="55%">资源名称</th>
				<th width="15%">用户ID</th>
				<th width="20%">下载时间</th>
				<th width="10%">下载IP</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if($adds) {
				foreach($adds as $value)
				{
					echo "<tr>\n";
					echo "<td><a href='".get_permalink($value->ice_post_id)."' target=_blank>".get_post($value->ice_post_id)->post_title."</a></td>";
					echo "<td>".get_user_by("id",$value->ice_user_id)->user_login."</td>\n";
					echo "<td>$value->ice_time</td>\n";
					echo "<td>$value->ice_ip</td>\n";
					echo "</tr>";
				}
			}
			else
			{
				echo '<tr><td colspan="4" align="center"><strong>没有记录</strong></td></tr>';
			}
			?>
		</tbody>
	</table>
	<?php echo erphp_admin_pagenavi($total_trade,$ice_perpage);?>
	　　		
</div>