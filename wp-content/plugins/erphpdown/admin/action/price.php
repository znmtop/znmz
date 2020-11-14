<?php
require( dirname(__FILE__) . '/../../../../../wp-load.php' );
if(current_user_can('administrator')){
	global $wpdb;
	if($_POST['do']=='editprice'){
		$pid=$_POST['postid'];
		$pprice = $_POST['new_price'];
		$pid = $wpdb->escape($pid);
		$pprice = $wpdb->escape($pprice);
		update_post_meta($pid,"down_price",$pprice);
		echo "success";	
	}
}

