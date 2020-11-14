<?php
header("Content-type:text/html;character=utf-8");
require_once('../../../wp-load.php');
date_default_timezone_set('Asia/Shanghai');

add_filter('wp_title', 'assignPageTitle');
function assignPageTitle(){
	return "文件下载";
}

$postid=isset($_GET['postid']) && is_numeric($_GET['postid']) ?intval($_GET['postid']) :false;

if($postid){
	$ppost = get_post($postid);
	if(!$ppost) wp_die('下载信息错误！');

	$start_down2 = get_post_meta($postid, 'start_down2',TRUE);
}

if(!is_user_logged_in() && !$start_down2){
	wp_die('请先登录网站！');
}

$url=isset($_GET['url']) ? $_GET['url'] :false;
$key=isset($_GET['key']) ? $_GET['key'] :false;
$iframe=isset($_GET['iframe']) ? $_GET['iframe'] : 0;
$index=isset($_GET['index']) ? $_GET['index'] : '';

$postid = esc_sql($postid);
$url = esc_sql($url);
$key = esc_sql($key);
$index = esc_sql($index);
$index_name = '';
$index_vip = '';
$hasdown_info = 0;

if($postid==false && $url==false ){
	wp_die("下载信息错误！");
}

if ($postid){
	$ypost = get_post($postid);
	if(!$ypost){
		wp_die("下载信息错误！");
	}
	$isDown=FALSE;

	if($index){
		$urls = get_post_meta($postid, 'down_urls', true);
		if($urls){
			$cnt = count($urls['index']);
			if($cnt){
				for($i=0; $i<$cnt;$i++){
					if($urls['index'][$i] == $index){
    					$data = $urls['url'][$i];
    					$index_name = $urls['name'][$i];
    					$price = $urls['price'][$i];
    					$index_vip = $urls['vip'][$i];
    					break;
    				}
				}
			}
		}
	}else{
		$data=get_post_meta($postid, 'down_url', true);
		$price=get_post_meta($postid, 'down_price', true);
	}

	$memberDown=get_post_meta($postid, 'member_down',TRUE);
	if($index_vip){
		$memberDown = $index_vip;
	}
	$userType=getUsreMemberType();
	$user_info=wp_get_current_user();
	$days=get_post_meta($postid, 'down_days', true);
	$start_down2 = get_post_meta($postid, 'start_down2',TRUE);

	if($start_down2){
		$user_id = is_user_logged_in() ? wp_get_current_user()->ID : 0;
		$wppay = new EPD($postid, $user_id);

		if($wppay->isWppayPaid()){
			$hasdown_info = 1;
		}else{
			$hasdown_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".$postid."' and ice_success=1 and (ice_index is null or ice_index = '') and ice_user_id=".$user_info->ID." order by ice_time desc");
		}
	}else{
		if($index){
			$hasdown_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".$postid."' and ice_index='".$index."' and ice_success=1 and ice_user_id=".$user_info->ID." order by ice_time desc");
		}else{
			$hasdown_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".$postid."' and ice_success=1 and (ice_index is null or ice_index = '') and ice_user_id=".$user_info->ID." order by ice_time desc");
		}

		if($days > 0 && $hasdown_info){
			$lastDownDate = date('Y-m-d H:i:s',strtotime('+'.$days.' day',strtotime($hasdown_info->ice_time)));
			$nowDate = date('Y-m-d H:i:s');
			if(strtotime($nowDate) > strtotime($lastDownDate)){
				$hasdown_info = 0;
			}
		}
	}
	
	if(!$hasdown_info && is_user_logged_in()){
		if(!$price && $memberDown != 4 && $memberDown != 8 && $memberDown != 9){
			$erphp_reg_times  = get_option('erphp_reg_times');
			if(!$userType && $erphp_reg_times > 0){
				if( checkDownLog($user_info->ID,$postid,$erphp_reg_times,erphpGetIP()) ){

				}else{
					wp_die("普通用户每天只能下载".$erphp_reg_times."个免费资源！<a href='".get_option('erphp_url_front_vip')."'>升级VIP下载更多资源</a>","友情提示");
				}
			}

		}else{
			if($memberDown == 3 || $memberDown == 4 || $memberDown == 6 || $memberDown == 7 || $memberDown == 8 || $memberDown == 9){
				
				if($userType){
					
					$erphp_life_times    = get_option('erphp_life_times');
					$erphp_year_times    = get_option('erphp_year_times');
					$erphp_quarter_times = get_option('erphp_quarter_times');
					$erphp_month_times  = get_option('erphp_month_times');
					$erphp_day_times  = get_option('erphp_day_times');

					$erphp_life_name    = get_option('erphp_life_name')?get_option('erphp_life_name'):'终身VIP';
					$erphp_year_name    = get_option('erphp_year_name')?get_option('erphp_year_name'):'包年VIP';
					$erphp_quarter_name = get_option('erphp_quarter_name')?get_option('erphp_quarter_name'):'包季VIP';
					$erphp_month_name  = get_option('erphp_month_name')?get_option('erphp_month_name'):'包月VIP';
					$erphp_day_name  = get_option('erphp_day_name')?get_option('erphp_day_name'):'体验VIP';
					$erphp_vip_name  = get_option('erphp_vip_name')?get_option('erphp_vip_name'):'VIP';

					if($userType == 6 && $erphp_day_times > 0){
						if( checkDownLog($user_info->ID,$postid,$erphp_day_times,erphpGetIP()) ){

						}else{
							wp_die($erphp_day_name."用户每天只能免费下载".$erphp_day_times."个".$erphp_vip_name."资源！<a href='".constant("erphpdown")."buy.php?postid=".$postid."&index=".$index."'>单独购买</a>","友情提示");
						}
					}elseif($userType == 7 && $erphp_month_times > 0){
						if( checkDownLog($user_info->ID,$postid,$erphp_month_times,erphpGetIP()) ){

						}else{
							wp_die($erphp_month_name."用户每天只能免费下载".$erphp_month_times."个".$erphp_vip_name."资源！<a href='".constant("erphpdown")."buy.php?postid=".$postid."&index=".$index."'>单独购买</a>","友情提示");
						}
					}elseif($userType == 8 && $erphp_quarter_times > 0){
						if( checkDownLog($user_info->ID,$postid,$erphp_quarter_times,erphpGetIP()) ){

						}else{
							wp_die($erphp_quarter_name."用户每天只能免费下载".$erphp_quarter_times."个".$erphp_vip_name."资源！<a href='".constant("erphpdown")."buy.php?postid=".$postid."&index=".$index."'>单独购买</a>","友情提示");
						}
					}elseif($userType == 9 && $erphp_year_times > 0){
						if( checkDownLog($user_info->ID,$postid,$erphp_year_times,erphpGetIP()) ){

						}else{
							wp_die($erphp_year_name."用户每天只能免费下载".$erphp_year_times."个".$erphp_vip_name."资源！<a href='".constant("erphpdown")."buy.php?postid=".$postid."&index=".$index."'>单独购买</a>","友情提示");
						}
					}elseif($userType == 10 && $erphp_life_times > 0){
						if( checkDownLog($user_info->ID,$postid,$erphp_life_times,erphpGetIP()) ){

						}else{
							wp_die($erphp_life_name."用户每天只能免费下载".$erphp_life_times."个".$erphp_vip_name."资源！<a href='".constant("erphpdown")."buy.php?postid=".$postid."&index=".$index."'>单独购买</a>","友情提示");
						}
					}
					
				}
			}
		}
	}


	if(strlen($data) > 2)
	{
		$user_info=wp_get_current_user();
		$userType=getUsreMemberType();
		if($hasdown_info){
			$isDown=true;
			$pp = $postid;
		}
		elseif($user_info && $userType && ($memberDown ==3 || $memberDown ==4))
		{
			$isDown=true;
			$pp = $postid;
		}
		elseif($user_info && ($userType == 9 || $userType == 10) && ($memberDown ==6 || $memberDown ==8) )
		{
			$isDown=true;
			$pp = $postid;
		}
		elseif($user_info && $userType == 10 && ($memberDown ==7 || $memberDown ==9) )
		{
			$isDown=true;
			$pp = $postid;
		}
		else 
		{
			if( empty($price) || $price==0 )
			{
				if( ($memberDown ==4 && !$userType) || ($memberDown ==8 && $userType < 9) || ($memberDown ==9 && $userType < 10) ){
					
				}else{
					$isDown=true;
					$pp = $postid;
				}
			}
		}
	}

	if(!$isDown)
	{
		wp_die('下载信息错误！');
	}
}elseif($url){
	$user_info=wp_get_current_user();

	$down_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_url='".esc_sql($url)."' and ice_user_id=".$user_info->ID." order by ice_time desc");
	if($down_info->ice_price > 0){
		$downPostId=$down_info->ice_post;
		$days=get_post_meta($downPostId, 'down_days', true);
		if($days > 0){
			$lastDownDate = date('Y-m-d H:i:s',strtotime('+'.$days.' day',strtotime($down_info->ice_time)));
			$nowDate = date('Y-m-d H:i:s');
			if(strtotime($nowDate) > strtotime($lastDownDate)){
				wp_die('下载权限已过期，请重新购买');
			}
		}
		$pp = $downPostId;
		$postid = $downPostId;
		if($down_info->ice_index){
			$index = $down_info->ice_index;
			$urls = get_post_meta($postid, 'down_urls', true);
			if($urls){
				$cnt = count($urls['index']);
				if($cnt){
					for($i=0; $i<$cnt;$i++){
						if($urls['index'][$i] == $index){
	    					$data = $urls['url'][$i];
	    					$index_name = $urls['name'][$i];
	    					break;
	    				}
					}
				}
			}
		}else{
			$data=get_post_meta($downPostId, 'down_url', true);
		}
	}
	
	if(!$down_info || !$data)
	{
		wp_die('下载信息错误！');
	}
}

$downList=explode("\r\n",trim($data));
$downMsg = '<div class="title"><span>'.($index_name?$index_name:'下载地址').'</span></div>';

if($key){
	if(is_numeric($key)){
		$key=intval($key);
	}else {
		wp_die('下载信息错误！');
	}

	$user_info=wp_get_current_user();
	$file=$downList[$key-1];
	//$file = iconv('UTF-8', 'GBK//TRANSLIT', $file);
	$times=time();
	if(is_user_logged_in()){
		$md5key=md5($user_info->ID.'erphpdown'.$key.$times.get_option('erphpdown_downkey'));
	}else{
		$md5key=md5('erphpdown'.$key.$times.get_option('erphpdown_downkey'));
	}

	$cypher = new ErphpCrypt(ErphpCrypt::CRYPT_MODE_HEXADECIMAL, ErphpCrypt::CRYPT_HASH_SHA1);
	$cypher->Key = get_option('erphpdown_downkey');
	$entemp = $cypher->encrypt($times);

	$file = trim($file);

	header("Location:downloadfile.php?id=".$pp."&filename=".$key."&index=".$index."&md5key=".$md5key."&times=".$times."&session_name=".$entemp);
	exit;
	
}else{
	foreach ($downList as $k=>$v){
		$filepath = trim($downList[$k]);
		if($filepath){

			$erphp_colon_domains = get_option('erphp_colon_domains')?get_option('erphp_colon_domains'):'pan.baidu.com';
			if($erphp_colon_domains){
				$erphp_colon_domains_arr = explode(',', $erphp_colon_domains);
				foreach ($erphp_colon_domains_arr as $erphp_colon_domain) {
					if(strpos($filepath, $erphp_colon_domain)){
						$filepath = str_replace('：', ': ', $filepath);
						break;
					}
				}
			}

			$erphp_blank_domains = get_option('erphp_blank_domains')?get_option('erphp_blank_domains'):'pan.baidu.com';
			$erphp_blank_domain_is = 0;
			if($erphp_blank_domains){
				$erphp_blank_domains_arr = explode(',', $erphp_blank_domains);
				foreach ($erphp_blank_domains_arr as $erphp_blank_domain) {
					if(strpos($filepath, $erphp_blank_domain)){
						$erphp_blank_domain_is = 1;
						break;
					}
				}
			}

			if(strpos($filepath,',')){
				$filearr = explode(',',$filepath);
				$arrlength = count($filearr);
				if($arrlength == 1){
					$downMsg.="<p>文件".($k+1)."地址<a href='download.php?postid=".$postid."&key=".($k+1)."&index=".$index."' target='_blank' class='link'>点击下载</a></p>";
				}elseif($arrlength == 2){
					$downMsg.="<p>".$filearr[0]."<a href='download.php?postid=".$postid."&key=".($k+1)."&index=".$index."' target='_blank' class='link'>点击下载</a></p>";
				}elseif($arrlength == 3){
					$filearr2 = str_replace('：', ': ', $filearr[2]);
					$downMsg.="<p>".$filearr[0]."<a href='download.php?postid=".$postid."&key=".($k+1)."&index=".$index."' target='_blank' class='link erphpdown-down-btn' data-clipboard-text='".str_replace('提取码: ', '', $filearr2)."'>点击下载</a>（".$filearr2."）<a class='erphpdown-copy' data-clipboard-text='".str_replace('提取码: ', '', $filearr2)."' href='javascript:;'>复制</a></p>";
				}
			}elseif(strpos($filepath,'  ') && $erphp_blank_domain_is){
				$filearr = explode('  ',$filepath);
				$arrlength = count($filearr);
				if($arrlength == 1){
					$downMsg.="<p>文件".($k+1)."地址<a href='download.php?postid=".$postid."&key=".($k+1)."&index=".$index."' target='_blank' class='link'>点击下载</a></p>";
				}elseif($arrlength >= 2){
					$filearr2 = explode(':',$filearr[0]);
					$filearr3 = explode(':',$filearr[1]);
					$downMsg.="<p>".$filearr2[0]."<a href='download.php?postid=".$postid."&key=".($k+1)."&index=".$index."' target='_blank' class='link erphpdown-down-btn' data-clipboard-text='".trim($filearr3[1])."'>点击下载</a>（提取码: ".trim($filearr3[1])."）<a class='erphpdown-copy' data-clipboard-text='".trim($filearr3[1])."' href='javascript:;'>复制</a></p>";
				}
			}elseif(strpos($filepath,' ') && $erphp_blank_domain_is){
				$filearr = explode(' ',$filepath);
				$arrlength = count($filearr);
				if($arrlength == 1){
					$downMsg.="<p>文件".($k+1)."地址<a href='download.php?postid=".$postid."&key=".($k+1)."&index=".$index."' target='_blank' class='link'>点击下载</a></p>";
				}elseif($arrlength == 2){
					$downMsg.="<p>".$filearr[0]."<a href='download.php?postid=".$postid."&key=".($k+1)."&index=".$index."' target='_blank' class='link'>点击下载</a></p>";
				}elseif($arrlength >= 3){
					$downMsg.="<p>".str_replace(':', '', $filearr[0])."<a href='download.php?postid=".$postid."&key=".($k+1)."&index=".$index."' target='_blank' class='link erphpdown-down-btn' data-clipboard-text='".$filearr[3]."' >点击下载</a>（".$filearr[2].' '.$filearr[3]."）<a class='erphpdown-copy' data-clipboard-text='".$filearr[3]."' href='javascript:;'>复制</a></p>";
				}
			}else{
				$downMsg.="<p>文件".($k+1)."地址<a href='download.php?postid=".$postid."&key=".($k+1)."&index=".$index."' target='_blank' class='link'>点击下载</a></p>";
			}
		}
	}
	$hiddens = get_post_meta($pp,'hidden_content',true);
	if($hiddens){
		$downMsg .='<div class="title"><span>隐藏信息</span></div><div class="hidden-content" style="border:2px dashed #ff5f33;padding:15px;">'.$hiddens.'<a class="erphpdown-copy" data-clipboard-text="'.$hiddens.'" href="javascript:;" style="margin-left:10px;">复制</a></div>';
	}
	
	if(function_exists('MBThemes_erphpdown_download') && !$iframe){
		MBThemes_erphpdown_download($downMsg,$pp);
	}else{
		epd_download_page($downMsg,$pp);
	}
}