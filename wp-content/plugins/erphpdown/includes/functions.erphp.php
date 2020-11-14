<?php
/**
author: www.mobantu.com
QQ: 82708210
email: 82708210@qq.com
*/
if ( !defined('ABSPATH') ) {exit;}

add_action('admin_menu', 'mobantu_erphp_menu');
function mobantu_erphp_menu() {
	global $wpdb;
	$tx_count = $wpdb->get_var("SELECT count(ice_id) FROM $wpdb->iceget where ice_success != 1");
	$token = get_option('MBT_ERPHPDOWN_token');
	if($token){
		if (function_exists('add_menu_page')) {
			add_menu_page('erphpdown', 'ErphpDown'.($tx_count?'<span class="awaiting-mod">'.$tx_count.'</span>':''), 'activate_plugins', 'erphpdown/admin/erphp-settings.php', '','dashicons-admin-network');
			add_menu_page('erphpdown2', '会员推广下载', 'read', 'erphpdown/admin/erphp-my-money.php', '','dashicons-shield');
		}
		if (function_exists('add_submenu_page')) {
			add_submenu_page('erphpdown/admin/erphp-settings.php', '基础设置','基础设置', 'activate_plugins', 'erphpdown/admin/erphp-settings.php');
			add_submenu_page('erphpdown/admin/erphp-settings.php', '支付设置', '支付设置', 'activate_plugins', 'erphpdown/admin/erphp-payment.php');
			add_submenu_page('erphpdown/admin/erphp-settings.php', '显示设置', '显示设置', 'activate_plugins', 'erphpdown/admin/erphp-front.php');
			if(plugin_check_video()){
				add_submenu_page('erphpdown/admin/erphp-settings.php', 'VOD设置', 'VOD设置', 'activate_plugins', 'erphpdown-addon-video/settings.php');
			}
			if(plugin_check_card()){
				add_submenu_page('erphpdown/admin/erphp-settings.php', '所有充值卡','所有充值卡', 'activate_plugins', 'erphpdown-add-on-card/card-list.php');
				add_submenu_page('erphpdown/admin/erphp-settings.php', '添加充值卡','添加充值卡', 'activate_plugins', 'erphpdown-add-on-card/card-add.php');
			}
			if(plugin_check_activation()){
				add_submenu_page('erphpdown/admin/erphp-settings.php', '所有激活码','所有激活码', 'activate_plugins', 'erphpdown-add-on-activation/activation-list.php');
				add_submenu_page('erphpdown/admin/erphp-settings.php', '添加激活码','添加激活码', 'activate_plugins', 'erphpdown-add-on-activation/activation-add.php');
			}
			add_submenu_page('erphpdown/admin/erphp-settings.php', 'VIP设置','VIP设置','activate_plugins', 'erphpdown/admin/erphp-vip-setting.php');
			add_submenu_page('erphpdown/admin/erphp-settings.php', 'VIP订单','VIP订单','activate_plugins', 'erphpdown/admin/erphp-vip-items.php');
			add_submenu_page('erphpdown/admin/erphp-settings.php', 'VIP用户','VIP用户','activate_plugins', 'erphpdown/admin/erphp-vip-users.php');
			add_submenu_page('erphpdown/admin/erphp-settings.php', '赠送VIP', '赠送VIP', 'activate_plugins', 'erphpdown/admin/erphp-add-vip.php');
	        add_submenu_page('erphpdown/admin/erphp-settings.php', '充值/扣钱', '充值/扣钱', 'activate_plugins', 'erphpdown/admin/erphp-add-money.php');
	        add_submenu_page('erphpdown/admin/erphp-settings.php', '充值统计', '充值统计', 'activate_plugins', 'erphpdown/admin/erphp-chong-list.php');
	        add_submenu_page('erphpdown/admin/erphp-settings.php', '销售排行', '销售排行', 'activate_plugins', 'erphpdown/admin/erphp-items-list.php');
			add_submenu_page('erphpdown/admin/erphp-settings.php', '购买统计', '购买统计', 'activate_plugins', 'erphpdown/admin/erphp-orders-list.php');
			//add_submenu_page('erphpdown/admin/erphp-settings.php', '附加购买统计', '附加购买统计', 'activate_plugins', 'erphpdown/admin/erphp-indexs-list.php');
			add_submenu_page('erphpdown/admin/erphp-settings.php', '免登录购买统计', '免登录购买统计', 'activate_plugins', 'erphpdown/admin/erphp-wppays-list.php');
			add_submenu_page('erphpdown/admin/erphp-settings.php', '提现统计', '提现统计'.($tx_count?'<span class="awaiting-mod">'.$tx_count.'</span>':''), 'activate_plugins', 'erphpdown/admin/erphp-tixian-list.php');
	        add_submenu_page('erphpdown/admin/erphp-settings.php', '推广统计', '推广统计', 'activate_plugins', 'erphpdown/admin/erphp-reference-all.php');
	        add_submenu_page('erphpdown/admin/erphp-settings.php', '免费下载/查看统计', '免费下载/查看统计', 'activate_plugins', 'erphpdown/admin/erphp-vipdown-list.php');
	        add_submenu_page('erphpdown/admin/erphp-settings.php', '签到统计', '签到统计', 'activate_plugins', 'erphpdown/admin/erphp-checkin-list.php');
	        add_submenu_page('erphpdown/admin/erphp-settings.php', '查询用户', '查询用户', 'activate_plugins', 'erphpdown/admin/erphp-check-users.php');
	        add_submenu_page('erphpdown/admin/erphp-settings.php', '批量处理', '批量处理', 'activate_plugins', 'erphpdown/admin/erphp-shop-list.php');
	        add_submenu_page('erphpdown/admin/erphp-settings.php', '清理数据表', '清理数据表', 'activate_plugins', 'erphpdown/admin/erphp-clear.php');
			add_submenu_page('erphpdown/admin/erphp-settings.php', '检查更新', '检查更新', 'activate_plugins', 'erphpdown/admin/update.php');
			
			add_submenu_page('erphpdown/admin/erphp-my-money.php', '我的资产', '我的资产', 'read', 'erphpdown/admin/erphp-my-money.php');
			add_submenu_page('erphpdown/admin/erphp-my-money.php', '在线充值', '在线充值', 'read', 'erphpdown/admin/erphp-add-money-online.php');
			if(plugin_check_cred() && get_option('erphp_mycred') == 'yes'){
				add_submenu_page('erphpdown/admin/erphp-my-money.php', '积分兑换','积分兑换', 'read', 'erphpdown-add-on-mycred/erphp-to-mycred.php');
			}
			add_submenu_page('erphpdown/admin/erphp-my-money.php', '充值记录', '充值记录', 'read', 'erphpdown/admin/erphp-add-money-list.php');
			add_submenu_page('erphpdown/admin/erphp-my-money.php', '升级VIP', '升级VIP', 'read', 'erphpdown/admin/erphp-update-vip.php');
			add_submenu_page('erphpdown/admin/erphp-my-money.php', 'VIP记录', 'VIP记录', 'read', 'erphpdown/admin/erphp-update-vip-list.php');
			add_submenu_page('erphpdown/admin/erphp-my-money.php', '消费清单', '消费清单', 'read', 'erphpdown/admin/erphp-get-items.php');
			add_submenu_page('erphpdown/admin/erphp-my-money.php', '销售订单', '销售订单', 'edit_posts', 'erphpdown/admin/erphp-items.php');
			add_submenu_page('erphpdown/admin/erphp-my-money.php', '提现列表', '提现列表', 'read', 'erphpdown/admin/erphp-money-list.php');
			add_submenu_page('erphpdown/admin/erphp-my-money.php', '申请提现', '申请提现', 'read', 'erphpdown/admin/erphp-money.php');
			add_submenu_page('erphpdown/admin/erphp-my-money.php', '推广注册', '推广注册', 'read', 'erphpdown/admin/erphp-reference.php');
			add_submenu_page('erphpdown/admin/erphp-my-money.php', '推广下载', '推广下载', 'read', 'erphpdown/admin/erphp-reference-list.php');
			add_submenu_page('erphpdown/admin/erphp-my-money.php', '推广VIP', '推广VIP', 'read', 'erphpdown/admin/erphp-reference-vip-list.php');
			add_submenu_page('erphpdown/admin/erphp-my-money.php', '免费下载记录', '免费下载记录', 'read', 'erphpdown/admin/erphp-vipdown-list-my.php');
	    }
	}else{
		if (function_exists('add_menu_page')) {
			add_menu_page('erphpdown', 'ErphpDown', 'activate_plugins', 'erphpdown/admin/erphp-active.php', '','dashicons-admin-network');
		}
	}
    
}

function epd_wppay_callback(){
	global $wpdb, $wppay_table_name;
	$post_id = $_POST['post_id'];
	$user_id = is_user_logged_in() ? wp_get_current_user()->ID : 0;
	$price = get_post_meta($post_id,'down_price',true);
	$price2 = '';$code='';$code2='';$link='';$msg='';$num='';$status=400;$minute=0;$aliurl='';$gift=0;$gift2=0;
	$out_trade_no = date("ymdhis").mt_rand(100,999).mt_rand(100,999).mt_rand(100,999).'wppay';

	if($price){

		$wppay = new EPD($post_id, $user_id);

		if(is_user_logged_in()){
			$okMoney = erphpGetUserOkMoney();
			if($okMoney >= $price*get_option('ice_proportion_alipay')){
				if($wppay->addWppay($out_trade_no, $price)){

					addUserMoney($user_id, '-'.($price*get_option('ice_proportion_alipay')));
					$wpdb->query("UPDATE $wppay_table_name SET order_status=1 WHERE order_num = '".$out_trade_no."'");
				
					$data=get_post_meta($post_id, 'down_url', true);
					$ppost = get_post($post_id);
					$result = erphpAddDownloadByUid($ppost->post_title,$post_id,$user_id,$price*get_option('ice_proportion_alipay'),1,'',$ppost->post_author);
					if($result){
						$ice_ali_money_author = get_option('ice_ali_money_author');
						if($ice_ali_money_author){
							addUserMoney($ppost->post_author,$price*get_option('ice_proportion_alipay')*$ice_ali_money_author/100);
						}elseif($ice_ali_money_author == '0'){

						}else{
							addUserMoney($ppost->post_author,$price*get_option('ice_proportion_alipay'));
						}
                        $RefMoney=$wpdb->get_row("select * from ".$wpdb->users." where ID=".$user_id);
                        if($RefMoney->father_id > 0){
                            addUserMoney($RefMoney->father_id,$price*get_option('ice_proportion_alipay')*get_option('ice_ali_money_ref')*0.01);
                        }
					}

					$num = $out_trade_no;
					$status=202;
					
				}else{
					$status=201;
					$msg = '支付失败，请稍后重试！';
				}

				$result = array(
					'status' => $status,
					'num' => $num,
					'msg' => $msg
				);

				header('Content-type: application/json');
				echo json_encode($result);
				exit;
			}
		}

		if(get_option('erphp_wppay_payment') == 'f2fpay'){
			$qrPayResult = $wppay->f2fpayWppayQr($out_trade_no, $price);
			if($qrPayResult->getTradeStatus() == 'SUCCESS'){
				if($wppay->addWppay($out_trade_no, $price)){
					$response = $qrPayResult->getResponse();
					$code = ERPHPDOWN_URL.'/includes/qrcode.php?data='.urlencode($response->qr_code);
					$aliurl = $response->qr_code;
					$num = $out_trade_no;
					$status=200;
				}
			}else{
				$status=201;
				$msg = '获取支付信息失败！';
			}
		}elseif(get_option('erphp_wppay_payment') == 'weixin'){
			$qrPayResult = $wppay->weixinWppayQr($out_trade_no, $price);
			if($qrPayResult['result_code'] == 'SUCCESS'){
				if($wppay->addWppay($out_trade_no, $price)){
					$code = ERPHPDOWN_URL.'/includes/qrcode.php?data='.urlencode($qrPayResult['code_url']);
					$link = '';
					$num = $out_trade_no;
					$status=200;
				}
			}else{
				$status=201;
				$msg = '获取支付信息失败！';
			}
		}elseif(get_option('erphp_wppay_payment') == 'f2fpay_weixin'){
			$qrF2fpayPayResult = $wppay->f2fpayWppayQr($out_trade_no, $price);
			$qrWeixinPayResult = $wppay->weixinWppayQr($out_trade_no, $price);
			if($qrWeixinPayResult['result_code'] == 'SUCCESS' && $qrF2fpayPayResult->getTradeStatus() == 'SUCCESS'){
				if($wppay->addWppay($out_trade_no, $price)){
					$response = $qrF2fpayPayResult->getResponse();
					$code = ERPHPDOWN_URL.'/includes/qrcode.php?data='.urlencode($response->qr_code);
					$code2 = ERPHPDOWN_URL.'/includes/qrcode.php?data='.urlencode($qrWeixinPayResult['code_url']);
					$aliurl = $response->qr_code;
					$num = $out_trade_no;
					$status=200;
				}
			}else{
				$status=201;
				$msg = '获取支付信息失败！';
			}
		}elseif(get_option('erphp_wppay_payment') == 'f2fpay_hupiv3'){
			$qrF2fpayPayResult = $wppay->f2fpayWppayQr($out_trade_no, $price);
			$qrWeixinPayResult = $wppay->hupiWxWppayQr($out_trade_no, $price);
			if($qrWeixinPayResult['errcode'] == 0 && $qrF2fpayPayResult->getTradeStatus() == 'SUCCESS'){
				if($wppay->addWppay($out_trade_no, $price)){
					$response = $qrF2fpayPayResult->getResponse();
					$code = ERPHPDOWN_URL.'/includes/qrcode.php?data='.urlencode($response->qr_code);
					$code2 = $qrWeixinPayResult['url_qrcode'];
					$aliurl = $response->qr_code;
					$num = $out_trade_no;
					$status=200;
				}
			}else{
				$status=201;
				$msg = '获取支付信息失败！';
			}
		}elseif(get_option('erphp_wppay_payment') == 'hupiv3'){ 
			$appid_ali = get_option('erphpdown_xhpay_appid32');
			$appid_wx = get_option('erphpdown_xhpay_appid31');

			if($appid_ali) $qrAlipayPayResult = $wppay->hupiAliWppayQr($out_trade_no, $price);
			if($appid_wx) $qrWeixinPayResult = $wppay->hupiWxWppayQr($out_trade_no, $price);
			if((isset($qrWeixinPayResult) && $qrWeixinPayResult['errcode'] == 0) || (isset($qrAlipayPayResult) && $qrAlipayPayResult['errcode'] == 0)){
				if($wppay->addWppay($out_trade_no, $price)){
					if($appid_ali){
						if($qrAlipayPayResult['errcode'] == 0){
							$code = $qrAlipayPayResult['url_qrcode'];
						}
					}
					if($appid_wx){
						if($qrWeixinPayResult['errcode'] == 0){
							$code2 = $qrWeixinPayResult['url_qrcode'];
						}
					}

					$link = '';
					$num = $out_trade_no;
					$status=200;
				}
			}else{
				$status=201;
				$msg = '获取支付信息失败！';
			}
		}elseif(get_option('erphp_wppay_payment') == 'paypy'){
			$old_price = $price;
			$qrResult = $wppay->paypyQr($out_trade_no, $price);
			if($qrResult['status'] == '1'){
				if($wppay->addWppay($out_trade_no, $price)){
					if($qrResult['ali_url']){
						$code = ERPHPDOWN_URL.'/includes/qrcode.php?data='.$qrResult['ali_url'];
						$price = $qrResult['ali_qr_price'];
						if($price < $old_price){
							$gift = 1;
						}
						$aliurl = urldecode($qrResult['ali_url']);
					}
					if($qrResult['wx_url']){
						$code2 = ERPHPDOWN_URL.'/includes/qrcode.php?data='.$qrResult['wx_url'];
						$price2 = $qrResult['wx_qr_price'];
						if($price2 < $old_price){
							$gift2 = 1;
						}
					}
					$minute = $qrResult['minute'];
					$num = $out_trade_no;
					$status=200;
				}
			}else{
				$status=201;
				$msg = '获取支付信息失败！';
			}
		}
	}

	$result = array(
		'status' => $status,
		'price' =>$price,
		'price2' =>$price2,
		'code' => $code,
		'code2' => $code2,
		'gift' => $gift,
		'gift2' => $gift2,
		'link' => $link,
		'aliurl' => $aliurl,
		'minute' => $minute,
		'num' => $num,
		'msg' => $msg
	);

	header('Content-type: application/json');
	echo json_encode($result);
	exit;
}
add_action( 'wp_ajax_epd_wppay', 'epd_wppay_callback');
add_action( 'wp_ajax_nopriv_epd_wppay', 'epd_wppay_callback');

function epd_wppay_pay_callback(){
	$post_id = $_POST['post_id'];
	$order_num = $_POST['order_num'];
	$status = 0;
	$user_id = is_user_logged_in() ? wp_get_current_user()->ID : 0;
	$wppay = new EPD($post_id, $user_id);
	if($wppay->checkWppayPaid($order_num)){
		$days = get_option('erphp_wppay_cookie');
		$expire = time() + $days*24*60*60;
	    setcookie('wppay_'.$post_id, $wppay->setWppayKey($order_num), $expire, '/', $_SERVER['HTTP_HOST'], false);
	    $status = 1;
	}

	$result = array(
		'status' => $status
	);

	header('Content-type: application/json');
	echo json_encode($result);
	exit;
}
add_action( 'wp_ajax_epd_wppay_pay', 'epd_wppay_pay_callback');
add_action( 'wp_ajax_nopriv_epd_wppay_pay', 'epd_wppay_pay_callback');


function epd_index_callback(){
	global $wpdb;
	$post_id = $_POST['post_id'];
	$index_id = $_POST['index_id'];
	$price = $_POST['price'];
	$user_id = wp_get_current_user()->ID;
	$okMoney=erphpGetUserOkMoney();

	$erphp_url_front_recharge = get_bloginfo('wpurl').'/wp-admin/admin.php?page=erphpdown/admin/erphp-add-money-online.php';
	if(get_option('erphp_url_front_recharge')){
		$erphp_url_front_recharge = get_option('erphp_url_front_recharge');
	}
							
	$status = 201;

	if(sprintf("%.2f",$okMoney) >= $price && $okMoney > 0 && $price > 0){
		if(erphpSetUserMoneyXiaoFei($price)){
			$postUserId=get_post($post_id)->post_author;
			$result = erphpAddDownloadIndex($post_id,$price,$index_id);
			if($result){
				$ice_ali_money_author = get_option('ice_ali_money_author');
				if($ice_ali_money_author){
					addUserMoney($postUserId,$price*$ice_ali_money_author/100);
				}elseif($ice_ali_money_author == '0'){

				}else{
					addUserMoney($postUserId,$price);
				}
				$RefMoney=$wpdb->get_row("select * from ".$wpdb->users." where ID=".$user_id);
				if($RefMoney->father_id > 0 && erphpdod() > 0){
					addUserMoney($RefMoney->father_id,$price*get_option('ice_ali_money_ref')*0.01);
				}
				$status = 200;
			}else{
				$msg = '购买失败，请稍后重试！';
			}
		}else{
			$msg = '购买失败，请稍后重试！';
		}
	}else{
		$status = 202;
		$msg = '余额不足，请先充值！';
	}

	$result = array(
		'status' => $status,
		'msg' => $msg,
		'recharge' => $erphp_url_front_recharge
	);

	header('Content-type: application/json');
	echo json_encode($result);
	exit;
}
add_action( 'wp_ajax_epd_index', 'epd_index_callback');

function epd_see_callback(){
	date_default_timezone_set('Asia/Shanghai'); 
	global $wpdb;
	$post_id = $_POST['post_id'];
	$user_info=wp_get_current_user();
	$userType=getUsreMemberType();
	$status = 0;

	$erphp_life_times    = get_option('erphp_life_times');
	$erphp_year_times    = get_option('erphp_year_times');
	$erphp_quarter_times = get_option('erphp_quarter_times');
	$erphp_month_times  = get_option('erphp_month_times');
	$erphp_day_times  = get_option('erphp_day_times');

	if($userType == 6 && $erphp_day_times > 0){
		if( checkSeeLog($user_info->ID,$post_id,$erphp_day_times,erphpGetIP()) ){
			addDownLog($user_info->ID,$post_id,erphpGetIP());
			$status = 200;
		}else{
			$status = 201;
		}
	}elseif($userType == 7 && $erphp_month_times > 0){
		if( checkSeeLog($user_info->ID,$post_id,$erphp_month_times,erphpGetIP()) ){
			addDownLog($user_info->ID,$post_id,erphpGetIP());
			$status = 200;
		}else{
			$status = 201;
		}
	}elseif($userType == 8 && $erphp_quarter_times > 0){
		if( checkSeeLog($user_info->ID,$post_id,$erphp_quarter_times,erphpGetIP()) ){
			addDownLog($user_info->ID,$post_id,erphpGetIP());
			$status = 200;
		}else{
			$status = 201;
		}
	}elseif($userType == 9 && $erphp_year_times > 0){
		if( checkSeeLog($user_info->ID,$post_id,$erphp_year_times,erphpGetIP()) ){
			addDownLog($user_info->ID,$post_id,erphpGetIP());
			$status = 200;
		}else{
			$status = 201;
		}
	}elseif($userType == 10 && $erphp_life_times > 0){
		if( checkSeeLog($user_info->ID,$post_id,$erphp_life_times,erphpGetIP()) ){
			addDownLog($user_info->ID,$post_id,erphpGetIP());
			$status = 200;
		}else{
			$status = 201;
		}
	}

	$result = array(
		'status' => $status
	);

	header('Content-type: application/json');
	echo json_encode($result);
	exit;
}
add_action( 'wp_ajax_epd_see', 'epd_see_callback');

function epd_checkin_callback(){
	global $wpdb,$current_user;
	date_default_timezone_set('Asia/Shanghai'); 
	$gift = get_option('ice_ali_money_checkin');
	if($gift){
		if(erphpdown_check_checkin($current_user->ID)){
			$status = 201;
			$msg = '您今天已经签过到了，明儿再来哦～';
		}else{
			$result = $wpdb->query("insert into $wpdb->checkin (user_id,create_time) values(".$current_user->ID.",'".date("Y-m-d H:i:s")."')");
			if($result){
				if(function_exists('addUserMoney')){
					$status = 200;
					addUserMoney($current_user->ID, $gift);
				}
			}else{
				$status = 201;
				$msg = '签到失败，请稍后重试！';
			}
		}
	}else{
		$status = 201;
		$msg = '抱歉，签到功能已关闭！';
	}

	$result = array(
		'status' => $status,
		'msg' => $msg
	);

	header('Content-type: application/json');
	echo json_encode($result);
	exit;
}
add_action( 'wp_ajax_epd_checkin', 'epd_checkin_callback');

function epd_download_html($content){
	echo $content;
	exit;
}

function erphpdown_install() {
	global $wpdb, $erphpdown_version, $wppay_table_name;
	$charset_collate = $wpdb->get_charset_collate();
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	if( $wpdb->get_var("show tables like '{$wppay_table_name}'") != $wppay_table_name ) {
		$wpdb->query("CREATE TABLE {$wppay_table_name} (
			id      BIGINT(20) NOT NULL AUTO_INCREMENT,
			order_num VARCHAR(50) NOT NULL,
			post_id BIGINT(20) NOT NULL,
			post_price double(10,2) NOT NULL,
			user_id BIGINT(20) NOT NULL DEFAULT 0,
			order_pay_num VARCHAR(100),
			order_time datetime NOT NULL,
			order_status int(1) NOT NULL DEFAULT 0,
			ip_address VARCHAR(25) NOT NULL,
			UNIQUE KEY id (id)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");
	}

	$create_ice_alipay_sql = "CREATE TABLE $wpdb->icealipay (".
			"ice_id int(11) NOT NULL auto_increment,".
			"ice_num varchar(50) NOT NULL,".
			"ice_title varchar(100) NOT NULL,".
			"ice_post int(11) NOT NULL,".
			"ice_price double(10,2) NOT NULL,".
			"ice_url varchar(32) NOT NULL,".
			"ice_user_id int(11) NOT NULL,".
			"ice_time datetime NOT NULL,".
			"ice_data text,".
			"ice_index int(11),".
			"ice_success int(11) NOT NULL,".
			"ice_author int(11) NOT NULL,".
			"PRIMARY KEY (ice_id)) $charset_collate;";
	dbDelta( $create_ice_alipay_sql );

	$create_ice_index_sql = "CREATE TABLE $wpdb->iceindex (".
			"ice_id int(11) NOT NULL auto_increment,".
			"ice_num varchar(50) NOT NULL,".
			"ice_post int(11) NOT NULL,".
			"ice_price double(10,2) NOT NULL,".
			"ice_url varchar(32) NOT NULL,".
			"ice_user_id int(11) NOT NULL,".
			"ice_time datetime NOT NULL,".
			"ice_index int(11) NOT NULL,".
			"PRIMARY KEY (ice_id)) $charset_collate;";
	dbDelta( $create_ice_index_sql );
	
	$create_ice_money_sql="CREATE TABLE $wpdb->icemoney (".
			"ice_id int(11) NOT NULL auto_increment,".
			"ice_num varchar(50) NOT NULL,".
			"ice_money double(10,2) NOT NULL,".
			"ice_user_id int(11) NOT NULL,".
			"ice_post_id int(11),".
			"ice_post_index int(11),".
			"ice_user_type int(2),".
			"ice_time datetime NOT NULL,".
			"ice_success int(10) NOT NULL,".
			"ice_note varchar(50) NOT NULL,".
			"ice_success_time datetime NOT NULL,".
			"ice_alipay varchar(200) NOT NULL,".
			"PRIMARY KEY (ice_id)) $charset_collate;";
	dbDelta( $create_ice_money_sql );
	
	$create_money_info_sql="CREATE TABLE $wpdb->iceinfo (".
			"ice_id int(11) NOT NULL auto_increment,".
			"ice_have_money double(10,2) NOT NULL,".
			"ice_user_id int(11) NOT NULL,".
			"ice_get_money double(10,2) NOT NULL,".
			"userType TINYINT(4) NOT NULL DEFAULT 0,".
			"endTime DATE NOT NULL DEFAULT '1000-01-01',".
			"PRIMARY KEY (ice_id)) $charset_collate;";
	dbDelta( $create_money_info_sql );
	
	$create_get_money_sql="CREATE TABLE $wpdb->iceget (".
			"ice_id int(11) NOT NULL auto_increment,".
			"ice_alipay varchar(100) NOT NULL,".
			"ice_name varchar(30) NOT NULL,".
			"ice_user_id int(11) NOT NULL,".
			"ice_money double(10,2) NOT NULL,".
			"ice_time datetime NOT NULL,".
			"ice_success int(10) NOT NULL,".
			"ice_note varchar(50) NOT NULL,".
			"ice_success_time datetime NOT NULL,".
			"PRIMARY KEY (ice_id)) $charset_collate;";
	dbDelta( $create_get_money_sql );
	
	$create_ice_vip_sql = "CREATE TABLE $wpdb->vip (".
			"ice_id int(11) NOT NULL auto_increment,".
			"ice_price double(10,2) NOT NULL,".
			"ice_user_id int(11) NOT NULL,".
			"ice_user_type tinyint(4) NOT NULL default 0,".
			"ice_time datetime NOT NULL,".
			"PRIMARY KEY (ice_id)) $charset_collate;";
	dbDelta( $create_ice_vip_sql );
	
	$create_ice_aff_sql = "CREATE TABLE $wpdb->aff (".
			"ice_id int(11) NOT NULL auto_increment,".
			"ice_price double(10,2) NOT NULL,".
			"ice_user_id int(11) NOT NULL,".
			"ice_user_id_visit int(11),".
			"ice_ip varchar(50),".
			"ice_time datetime NOT NULL,".
			"PRIMARY KEY (ice_id)) $charset_collate;";
	dbDelta( $create_ice_aff_sql );

	$create_ice_down_sql = "CREATE TABLE $wpdb->down (".
			"ice_id int(11) NOT NULL auto_increment,".
			"ice_user_id int(11) NOT NULL,".
			"ice_post_id int(11),".
			"ice_ip varchar(50),".
			"ice_time datetime NOT NULL,".
			"PRIMARY KEY (ice_id)) $charset_collate;";
	dbDelta( $create_ice_down_sql );

	$create_ice_checkin_sql = "CREATE TABLE $wpdb->checkin (
	   ID int(11) NOT NULL auto_increment,
	   user_id int(11) NOT NULL,
	   create_time datetime NOT NULL,
	   PRIMARY KEY (ID)
	  );";
	dbDelta($create_ice_checkin_sql);
	
	$update1="ALTER TABLE `".$wpdb->users."` ADD  `father_id` INT( 10 ) NOT NULL DEFAULT  '0'";
	$wpdb->query($update1);

	$update2="ALTER TABLE `".$wpdb->users."` ADD  `reg_ip` varchar( 60 ) DEFAULT  ''";
	$wpdb->query($update2);
	
	$update3="ALTER TABLE `".$wpdb->icemoney."` modify column ice_num varchar(50)";
	$wpdb->query($update3);

	$update4="ALTER TABLE `".$wpdb->icealipay."` modify column ice_num varchar(50)";
	$wpdb->query($update4);

	$update5="ALTER TABLE `".$wpdb->icemoney."` ADD `ice_post_id` int(11), add `ice_user_type` int(2)";
	$wpdb->query($update5);

	$update6="ALTER TABLE `".$wpdb->icealipay."` ADD `ice_index` int(11)";
	$wpdb->query($update6);

	$update7="ALTER TABLE `".$wpdb->icemoney."` ADD `ice_post_index` int(11)";
	$wpdb->query($update7);

	if(get_option('erphpdown_version') < 9.00){
		update_option('erphp_post_types',array('post'));
	}

	update_option( 'erphpdown_version', $erphpdown_version );
}

add_action('admin_enqueue_scripts', 'erphpdown_setting_scripts');
function erphpdown_setting_scripts(){
	if( isset($_GET['page']) && $_GET['page'] == "erphpdown/admin/erphp-active.php" ){
		wp_enqueue_script( 'erphpdown_setting', ERPHPDOWN_URL.'/static/setting.js', array(), false, true );	
	}
}