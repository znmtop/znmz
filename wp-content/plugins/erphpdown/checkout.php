<?php
header("Content-type:text/html;character=utf-8");
require_once('../../../wp-load.php');
date_default_timezone_set('Asia/Shanghai');
if(!is_user_logged_in()){
	wp_die('请先登录！','友情提示');
}
$postid=isset($_GET['postid']) && is_numeric($_GET['postid']) ?intval($_GET['postid']) :false;
$postid = $wpdb->escape($postid);
$index=isset($_GET['index']) && is_numeric($_GET['index']) ? intval($_GET['index']) : '';
$index = esc_sql($index);
$index_name = '';
$index_vip = '';
$price = '';

if($postid){
	$days=get_post_meta($postid, 'down_days', true);
	$down_repeat = get_post_meta($postid, 'down_repeat', true);
	$down_only_pay = get_post_meta($postid, 'down_only_pay', true);
	if($down_only_pay){
		wp_die('不支持余额购买，请直接在线支付购买！','友情提示');
	}
	$user_info=wp_get_current_user();
	if($index){
		$hasdown_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".$postid."' and ice_index='".$index."' and ice_success=1 and ice_user_id=".$user_info->ID." order by ice_time desc");
	}else{
		$hasdown_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".$postid."' and ice_success=1 and (ice_index is null or ice_index = '') and ice_user_id=".$user_info->ID." order by ice_time desc");
	}
	if($days > 0){
		$lastDownDate = date('Y-m-d H:i:s',strtotime('+'.$days.' day',strtotime($hasdown_info->ice_time)));
		$nowDate = date('Y-m-d H:i:s');
		if(strtotime($nowDate) > strtotime($lastDownDate)){
			$hasdown_info = null;
		}
	}
	if($hasdown_info && !$down_repeat){
		wp_die('请勿重复购买，请返回原页面刷新页面！','友情提示');
	}
	if($index){
		$urls = get_post_meta($postid, 'down_urls', true);
		if($urls){
			$cnt = count($urls['index']);
			if($cnt){
				for($i=0; $i<$cnt;$i++){
					if($urls['index'][$i] == $index){
    					$index_name = $urls['name'][$i];
    					$price = $urls['price'][$i];
    					$index_vip = $urls['vip'][$i];
    					break;
    				}
				}
			}
		}
	}else{
		$price=get_post_meta($postid, 'down_price', true);
		$start_down2 = get_post_meta($postid, 'start_down2',TRUE);
		if($start_down2){
			$price = $price*get_option('ice_proportion_alipay');
		}
	}

	$memberDown=get_post_meta($postid, 'member_down',TRUE);
	if($index_vip){
		$memberDown = $index_vip;
	}
	$hidden=get_post_meta($postid, 'hidden_content', true);
	$start_down=get_post_meta($postid, 'start_down', true);
	$start_down2 = get_post_meta($postid, 'start_down2',TRUE);
	$start_see=get_post_meta($postid, 'start_see', true);
	$start_see2=get_post_meta($postid, 'start_see2', true);
	$down_activation = get_post_meta($postid, 'down_activation', true);
	if(!$price)
	{
		wp_die('资源价格错误！','友情提示');
	}
	
	$okMoney=erphpGetUserOkMoney();
	$userType=getUsreMemberType();
	if($memberDown==4 || $memberDown==8 || $memberDown==9)
	{
		wp_die('您无权购买此资源！','友情提示');
	}
	if($userType && $memberDown==2)
	{
		$price=sprintf("%.2f",$price*0.5);
	}
	if($userType && $memberDown==5)
	{
		$price=sprintf("%.2f",$price*0.8);
	}
	if(sprintf("%.2f",$okMoney) >= $price && $okMoney > 0 && $price > 0)
	{
		if(erphpSetUserMoneyXiaoFei($price))
		{
			$subject   = get_post($postid)->post_title;
			if($index_name){
				$subject .= ' - '.$index_name;
			}
			$postUserId=get_post($postid)->post_author;
			
			if($start_down || $start_down2 || $start_see || $start_see2)
			{
				$result=erphpAddDownload($subject, $postid, $price, 1, '', $postUserId, $index);
				if($result)
				{
					if($down_activation && function_exists('doErphpAct')){
						$activation_num = doErphpAct($user_info->ID,$postid);
						$wpdb->query("update $wpdb->icealipay set ice_data = '".$activation_num."' where ice_url='".$result."'");
						if($user_info->user_email){
							wp_mail($user_info->user_email, '【'.$subject.'】注册码', '您购买的资源【'.$subject.'】注册码：'.$activation_num);
						}
					}

					$ice_ali_money_author = get_option('ice_ali_money_author');
					if($ice_ali_money_author){
						addUserMoney($postUserId,$price*$ice_ali_money_author/100);
					}elseif($ice_ali_money_author == '0'){

					}else{
						addUserMoney($postUserId,$price);
					}

					$EPD = new EPD();
					$EPD->doAff($price, $user_info->ID);

					if($start_down || $start_down2)
					{
						header("Location:".constant("erphpdown").'download.php?postid=' . $postid.'&index='.$index);
					}
					elseif($start_see || $start_see2)
					{
						header("Location:".get_permalink($postid));
					}
					exit;
				}
				else
				{
					$wpdb->query("update $wpdb->iceinfo set ice_get_money=ice_get_money-".$price ." where ice_user_id=".$user_info->ID);
					wp_die('系统发生错误,请稍候重试！','友情提示');
					exit;
				}
			}
		}
		else 
		{
			wp_die('系统错误！','友情提示');
		}
	}
	else 
	{
		wp_die('余额不足完成此次交易！','友情提示');
	}
}