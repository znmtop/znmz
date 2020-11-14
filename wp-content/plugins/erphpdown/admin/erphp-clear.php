<?php
if ( !defined('ABSPATH') ) {exit;}
if(!is_user_logged_in()){
	wp_die('请登录系统');
}
?>
<?php 
if($_POST['action']){
	if($_POST['action'] == 1){
		$wpdb->query("delete from $wpdb->icemoney WHERE ice_success = 0 and ice_time < DATE_SUB(CURDATE(), INTERVAL 3 WEEK)");
		echo'<div class="updated settings-error"><p>清理成功！</p></div>';
	}elseif($_POST['action'] == 2){
		$wpdb->query("delete from $wpdb->down WHERE ice_time < DATE_SUB(CURDATE(), INTERVAL 1 WEEK)");
		echo'<div class="updated settings-error"><p>清理成功！</p></div>';
	}
}
?>
<div class="wrap">
	<h1>清理数据表</h1>
	<p>清理数据表冗余数据可有效减轻数据库负担，对网站已知统计数据无影响。为确保万无一失，请在清理前备份下数据库。</p>
	<form action="" method="post">
		<input type="hidden" name="action" value="1"  />
		<input type="submit" value="清理 3 周之前所有未完成的充值订单" class="button-primary">（随着时间推移网站未完成的充值订单记录会越来越多，可清理）
	</form>
	<br><br>
	<form action="" method="post">
		<input type="hidden" name="action" value="2"  />
		<input type="submit" value="清理 1 周之前所有下载次数数据" class="button-primary">（统计会员每天下载资源个数的数据，不是用户购买订单，可清理）
	</form>
</div>