<?php
/*
*作者：mobantu.com
*/
if ( !defined('ABSPATH') ) {exit;}
$user_info=wp_get_current_user();
$issearch = 0;
if(isset($_POST['username'])){
	$user = get_user_by('login',$_POST['username']);
	if($user){
		$suid = $user->ID;
		$issearch = 1;
	}else{
		$suid = 0;
		echo '<div class="error settings-error"><p>用户不存在！</p></div>';
	}
	$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->vip where ice_user_id=".$suid);
	$total_success = $wpdb->get_var("SELECT sum(ice_price) FROM $wpdb->vip where ice_user_id=".$suid);
}else{
	$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->vip");
	$total_success = $wpdb->get_var("SELECT sum(ice_price) FROM $wpdb->vip");
}

//分页计算
$ice_perpage = 20;
$pages = ceil($total_trade / $ice_perpage);
$page=isset($_GET['paged']) ?intval($_GET['paged']) :1;
$offset = $ice_perpage*($page-1);

if($issearch){
	$list = $wpdb->get_results("SELECT * FROM $wpdb->vip where ice_user_id=".$suid." order by ice_time DESC limit $offset,$ice_perpage");
}else{
	$list = $wpdb->get_results("SELECT * FROM $wpdb->vip order by ice_time DESC limit $offset,$ice_perpage");
}

?>
<div class="wrap">
	<h2>VIP订单</h2>
	<p><?php printf(('共有<strong>%s</strong>笔交易，总金额：<strong>%s</strong>'), $total_trade, $total_success); ?></p>
	<form method="post">搜索用户：<input type="text" name="username" placeholder="登录名，例如：admin" value="<?php if($issearch) echo $_POST['username'];?>"><input type="submit" value="查询" class="button"><input type="hidden" name="action" value="2"></form><br>
	<table class="widefat fixed striped posts">
		<thead>
			<tr>
				<th width="15%">用户ID</th>
				<th width="15%">VIP类型</th>
				<th width="5%"><?php echo get_option('ice_name_alipay');?></th>
				<th width="15%">交易时间</th>				
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
				foreach($list as $value)
				{
					if($value->ice_user_type == 6) $typeName = '体验'.$erphp_day_name;
					else {$typeName=$value->ice_user_type==7 ?'包月'.$erphp_month_name :($value->ice_user_type==8 ?'包季'.$erphp_quarter_name : ($value->ice_user_type==10 ?'终身'.$erphp_life_name : '包年'.$erphp_year_name));}
					echo "<tr>\n";
					echo "<td>".get_the_author_meta( 'user_login', $value->ice_user_id )."</td>\n";
					echo "<td>$typeName</td>\n";
					echo "<td>$value->ice_price</td>\n";
					echo "<td>$value->ice_time</td>\n";
					echo "</tr>";
				}
			}
			else
			{
				echo '<tr><td colspan="4" align="center"><strong>没有交易记录</strong></td></tr>';
			}
			?>
		</tbody>
	</table>
	<?php echo erphp_admin_pagenavi($total_trade,$ice_perpage);?>
</div>
