<?php
require( dirname(__FILE__).'/../../../../../wp-load.php' );
if(isset($_POST) && current_user_can('administrator')){
	$status = 0;$message = '激活失败';
	$username = $_POST['username'];
	$token = $_POST['token'];
	$home = $_POST['home'];
	$plugin = $_POST['plugin'];
	$body = array('username'=>$username, 'token'=>$token, 'plugin'=>$plugin, 'domain'=>$_SERVER['SERVER_NAME'], 'action'=>'active');
	$result_body = json_decode(EPD::send_request($body));
	if( isset($result_body->status) && $result_body->status=='1' ){
		update_option('MBT_'.$plugin.'_user',$username);
		update_option('MBT_'.$plugin.'_token',$result_body->token);
		update_option('MBT_'.$plugin.'_key',$token);
		$status = 1;
		$message = $result_body->message;
	}
	$arr=array(
		"status"=>$status,
		"message"=>$message
	); 
	$jarr=json_encode($arr); 
	echo $jarr;
}