<?php
require( dirname(__FILE__).'/../../../../../wp-load.php' );
if(is_uploaded_file($_FILES['erphpFile']['tmp_name']) && is_user_logged_in() && current_user_can('publish_posts')){
	$vname = $_FILES['erphpFile']['name'];
	if ($vname != "") {
		$filename = md5(date("YmdHis").mt_rand(100,999)).strrchr($vname,'.');
		//上传路径
		$upfile = '../../../../../wp-content/uploads/erphpdown/';
		if(!file_exists($upfile)){  mkdir($upfile,0777,true);} 
		$file_path = '../../../../../wp-content/uploads/erphpdown/'. $filename;
		if(move_uploaded_file($_FILES['erphpFile']['tmp_name'], $file_path)){
			echo home_url().'/wp-content/uploads/erphpdown/'. $filename;
		}
	}
}