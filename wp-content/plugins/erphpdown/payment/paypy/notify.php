<?php
require_once('../../../../../wp-load.php');
$secretkey = get_option('erphpdown_paypy_key');

$sign = $_POST['sign'];
$total_fee = $_POST['qr_price'];
$extension= $_POST['extension'];
$out_trade_no = $wpdb->escape($_POST['order_id']);

if($sign == md5(md5($_POST['order_id']).$secretkey) && $secretkey){
	global $wpdb, $wppay_table_name;
	if(strstr($out_trade_no,'wppay')){
		$order=$wpdb->get_row("select * from $wppay_table_name where order_num='".$out_trade_no."'");
		if($order){
			if(!$order->order_status){
				$total_fee = $order->post_price;
				$wpdb->query("UPDATE $wppay_table_name SET order_status=1 WHERE order_num = '".$out_trade_no."'");

				$postUserId=get_post($order->post_id)->post_author;
				$ice_ali_money_author = get_option('ice_ali_money_author');
				if($ice_ali_money_author){
					addUserMoney($postUserId,$total_fee*get_option('ice_proportion_alipay')*$ice_ali_money_author/100);
				}elseif($ice_ali_money_author == '0'){

				}else{
					addUserMoney($postUserId,$total_fee*get_option('ice_proportion_alipay'));
				}

				if($order->user_id){
					$data=get_post_meta($order->post_id, 'down_url', true);
					$ppost = get_post($order->post_id);
					erphpAddDownloadByUid($ppost->post_title,$order->post_id,$order->user_id,$total_fee*get_option('ice_proportion_alipay'),1,'',$ppost->post_author);
				}
			}
		}
	}else{
		epd_set_order_success($out_trade_no,$total_fee,'paypy');//这里传的'paypy'值千万不要修改
	}
	echo 'success';
}