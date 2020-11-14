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

$total_trade   = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->checkin");

//分页计算
$ice_perpage = 20;
$pages = ceil($total_trade / $ice_perpage);
$page=isset($_GET['paged']) ?intval($_GET['paged']) :1;
$offset = $ice_perpage*($page-1);
?>

<div class="wrap">
	<?php
	
	
	$adds=$wpdb->get_results("SELECT * FROM $wpdb->checkin order by create_time DESC limit $offset,$ice_perpage");
	
	?>
	<h2>签到记录</h2><table class="widefat fixed striped posts">

		<thead>
			<tr>
				<th>用户ID</th>
				<th>时间</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if($adds) {
				foreach($adds as $value)
				{
					echo "<tr>\n";
					echo "<td>".get_the_author_meta( 'user_login', $value->user_id )."</td>";
					echo "<td>".$value->create_time."</td>";
					
					echo "</tr>";
				}
			}
			else
			{
				echo '<tr><td colspan="2" align="center"><strong>没有记录</strong></td></tr>';
			}
			?>
		</tbody>
	</table>
	<?php echo erphp_admin_pagenavi($total_trade,$ice_perpage);?>
	　　		
</div>