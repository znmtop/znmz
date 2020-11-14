<?php
require_once('../../../wp-load.php');
date_default_timezone_set('Asia/Shanghai');

if($_GET['id']){
	$pid = esc_sql($_GET['id']);
	$ppost = get_post($pid);
	if(!$ppost) wp_die('下载信息错误！');

	$start_down2 = get_post_meta($pid, 'start_down2',TRUE);
}

if(!is_user_logged_in() && !$start_down2){
	wp_die('请先登录网站！');
}

$user_info=wp_get_current_user();
$filename=$_GET['filename'];
$md5key=$_GET['md5key'];
$times=$_GET['times'];
$session_name=$_GET['session_name'];
$index=isset($_GET['index']) ? $_GET['index'] : '';
$index = esc_sql($index);
$index_vip = '';
$hasdown_info = 0;

if($_GET['id']){
	$pid = esc_sql($_GET['id']);
	$ppost = get_post($pid);
	if(!$ppost) wp_die('下载信息错误！');
	
	if($index){
		$urls = get_post_meta($pid, 'down_urls', true);
		if($urls){
			$cnt = count($urls['index']);
			if($cnt){
				for($i=0; $i<$cnt;$i++){
					if($urls['index'][$i] == $index){
    					$data = $urls['url'][$i];
    					$price = $urls['price'][$i];
    					$index_vip = $urls['vip'][$i];
    					break;
    				}
				}
			}
		}
	}else{
		$data = get_post_meta($pid, 'down_url', true);
		$price = get_post_meta($pid, 'down_price',true);
	}

	$memberDown=get_post_meta($pid, 'member_down',true);
	if($index_vip){
		$memberDown = $index_vip;
	}
	$userType=getUsreMemberType();

	$days=get_post_meta($pid, 'down_days', true);

	if($start_down2){
		$user_id = is_user_logged_in() ? wp_get_current_user()->ID : 0;
		$wppay = new EPD($pid, $user_id);

		if($wppay->isWppayPaid()){
			$hasdown_info = 1;
		}else{
			$hasdown_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".$pid."' and ice_success=1 and (ice_index is null or ice_index = '') and ice_user_id=".$user_info->ID." order by ice_time desc");
		}
	}else{
		if($index){
			$hasdown_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".$pid."' and ice_index='".$index."' and ice_success=1 and ice_user_id=".$user_info->ID." order by ice_time desc");
		}else{
			$hasdown_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".$pid."' and ice_success=1 and (ice_index is null or ice_index = '') and ice_user_id=".$user_info->ID." order by ice_time desc");
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
				if( checkDownLog($user_info->ID,$pid,$erphp_reg_times,erphpGetIP()) ){

				}else{
					wp_die("普通用户每天只能下载".$erphp_reg_times."个免费资源！<a href='".get_option('erphp_url_front_vip')."'>升级VIP下载更多资源</a>","友情提示");
				}
			}
			if(!$userType) addDownLog($user_info->ID,$pid,erphpGetIP());
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
						if( checkDownLog($user_info->ID,$pid,$erphp_day_times,erphpGetIP()) ){

						}else{
							wp_die($erphp_day_name."用户每天只能免费下载".$erphp_day_times."个".$erphp_vip_name."资源！","友情提示");
						}
					}elseif($userType == 7 && $erphp_month_times > 0){
						if( checkDownLog($user_info->ID,$pid,$erphp_month_times,erphpGetIP()) ){

						}else{
							wp_die($erphp_month_name."用户每天只能免费下载".$erphp_month_times."个".$erphp_vip_name."资源！","友情提示");
						}
					}elseif($userType == 8 && $erphp_quarter_times > 0){
						if( checkDownLog($user_info->ID,$pid,$erphp_quarter_times,erphpGetIP()) ){

						}else{
							wp_die($erphp_quarter_name."用户每天只能免费下载".$erphp_quarter_times."个".$erphp_vip_name."资源！","友情提示");
						}
					}elseif($userType == 9 && $erphp_year_times > 0){
						if( checkDownLog($user_info->ID,$pid,$erphp_year_times,erphpGetIP()) ){

						}else{
							wp_die($erphp_year_name."用户每天只能免费下载".$erphp_year_times."个".$erphp_vip_name."资源！","友情提示");
						}
					}elseif($userType == 10 && $erphp_life_times > 0){
						if( checkDownLog($user_info->ID,$pid,$erphp_life_times,erphpGetIP()) ){

						}else{
							wp_die($erphp_life_name."用户每天只能免费下载".$erphp_life_times."个".$erphp_vip_name."资源！","友情提示");
						}
					}

					addDownLog($user_info->ID,$pid,erphpGetIP());
					
				}
			}
		}
	}

	$g=(int)get_post_meta($pid,'down_times',true);
	if(!$g)$g=0;
	update_post_meta($pid,'down_times',$g+1);

}else{
	wp_die('下载信息错误！');
}

if(abs(time()-$times) < 100){
	if(is_user_logged_in()){
		$md5my=md5($user_info->ID.'erphpdown'.$filename.$times.get_option('erphpdown_downkey'));
	}else{
		$md5my=md5('erphpdown'.$filename.$times.get_option('erphpdown_downkey'));
	}

	$cypher = new ErphpCrypt(ErphpCrypt::CRYPT_MODE_HEXADECIMAL, ErphpCrypt::CRYPT_HASH_SHA1);
	$cypher->Key = get_option('erphpdown_downkey');
	$entemp = $cypher->decrypt($session_name);

	if($md5key==$md5my && $entemp == $times){
		$downList=explode("\r\n",trim($data));
		$file=trim($downList[$filename-1]);

		if(substr($file,0,7) == 'http://' || substr($file,0,8) == 'https://' || substr($file,0,10) == 'thunder://' || substr($file,0,7) == 'magnet:' || substr($file,0,5) == 'ed2k:' || substr($file,0,4) == 'ftp:'){
			$info=erphpdown_download_file($file);
		}else{

			$erphp_colon_domains = get_option('erphp_colon_domains')?get_option('erphp_colon_domains'):'pan.baidu.com';
			if($erphp_colon_domains){
				$erphp_colon_domains_arr = explode(',', $erphp_colon_domains);
				foreach ($erphp_colon_domains_arr as $erphp_colon_domain) {
					if(strpos($file, $erphp_colon_domain)){
						$file = str_replace('：', ': ', $file);
						break;
					}
				}
			}

			$erphp_blank_domains = get_option('erphp_blank_domains')?get_option('erphp_blank_domains'):'pan.baidu.com';
			$erphp_blank_domain_is = 0;
			if($erphp_blank_domains){
				$erphp_blank_domains_arr = explode(',', $erphp_blank_domains);
				foreach ($erphp_blank_domains_arr as $erphp_blank_domain) {
					if(strpos($file, $erphp_blank_domain)){
						$erphp_blank_domain_is = 1;
						break;
					}
				}
			}

			if(strpos($file,',')){
				$filearr = explode(',',$file);
				$arrlength = count($filearr);
				if($arrlength == 1){
					$info=erphpdown_download_file(ABSPATH.'/'.$file);
				}elseif($arrlength >= 2){
					if(substr($filearr[1],0,7) == 'http://' || substr($filearr[1],0,8) == 'https://' || substr($filearr[1],0,10) == 'thunder://' || substr($filearr[1],0,7) == 'magnet:' || substr($filearr[1],0,5) == 'ed2k:' || substr($filearr[1],0,4) == 'ftp:'){
						$info=erphpdown_download_file($filearr[1]);
					}else{
						$info=erphpdown_download_file(ABSPATH.'/'.$filearr[1]);
					}
				}
			}elseif(strpos($file,'  ') && $erphp_blank_domain_is){
				$filearr = explode('  ',$file);
				$arrlength = count($filearr);
				if($arrlength == 1){
					$info=erphpdown_download_file(ABSPATH.'/'.$file);
				}elseif($arrlength >= 2){
					$filearr2 = explode(':',$filearr[0]);
					$file2 = trim($filearr2[1].':'.$filearr2[2]);
					if(substr($file2,0,7) == 'http://' || substr($file2,0,8) == 'https://' || substr($file2,0,10) == 'thunder://' || substr($file2,0,7) == 'magnet:' || substr($file2,0,5) == 'ed2k:' || substr($file2,0,4) == 'ftp:'){
						$info=erphpdown_download_file($file2);
					}else{
						$info=erphpdown_download_file(ABSPATH.'/'.$file2);
					}
				}
			}elseif(strpos($file,' ') && $erphp_blank_domain_is){
				$filearr = explode(' ',$file);
				$arrlength = count($filearr);
				if($arrlength == 1){
					$info=erphpdown_download_file(ABSPATH.'/'.$file);
				}elseif($arrlength >= 2){
					if(substr($filearr[1],0,7) == 'http://' || substr($filearr[1],0,8) == 'https://' || substr($filearr[1],0,10) == 'thunder://' || substr($filearr[1],0,7) == 'magnet:' || substr($filearr[1],0,5) == 'ed2k:' || substr($filearr[1],0,4) == 'ftp:'){
						$info=erphpdown_download_file($filearr[1]);
					}else{
						$info=erphpdown_download_file(ABSPATH.'/'.$filearr[1]);
					}
				}
			}else{
				$info=erphpdown_download_file(ABSPATH.'/'.$file);
			}

		}
		if(!$info)
		{
			wp_die('下载信息错误！');
		}
	}else{
		wp_die('下载信息错误！');
	}
}
