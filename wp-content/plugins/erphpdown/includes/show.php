<?php
/**
author: www.mobantu.com
QQ: 82708210
email: 82708210@qq.com
*/
if ( !defined('ABSPATH') ) {exit;}

add_action('the_content','erphpdown_content_show');
function erphpdown_content_show($content){
	global $wpdb;
	$original_content = $content;
	
	$erphp_see2_style = get_option('erphp_see2_style');
	$erphp_life_name    = get_option('erphp_life_name')?get_option('erphp_life_name'):'终身VIP';
	$erphp_year_name    = get_option('erphp_year_name')?get_option('erphp_year_name'):'包年VIP';
	$erphp_quarter_name = get_option('erphp_quarter_name')?get_option('erphp_quarter_name'):'包季VIP';
	$erphp_month_name  = get_option('erphp_month_name')?get_option('erphp_month_name'):'包月VIP';
	$erphp_day_name  = get_option('erphp_day_name')?get_option('erphp_day_name'):'体验VIP';
	$erphp_vip_name  = get_option('erphp_vip_name')?get_option('erphp_vip_name'):'VIP';

	$down_box_hide = get_post_meta(get_the_ID(), 'down_box_hide', true);
	if(!$down_box_hide){
		$content2 = $content;
		$start_down=get_post_meta(get_the_ID(), 'start_down', true);
		$start_down2=get_post_meta(get_the_ID(), 'start_down2', true);
		$start_see=get_post_meta(get_the_ID(), 'start_see', true);
		$start_see2=get_post_meta(get_the_ID(), 'start_see2', true);

		if(is_singular()){
			$days=get_post_meta(get_the_ID(), 'down_days', true);
			$price=get_post_meta(get_the_ID(), 'down_price', true);
			$price_type=get_post_meta(get_the_ID(), 'down_price_type', true);
			$url=get_post_meta(get_the_ID(), 'down_url', true);
			$urls=get_post_meta(get_the_ID(), 'down_urls', true);
			$url_free=get_post_meta(get_the_ID(), 'down_url_free', true);
			$memberDown=get_post_meta(get_the_ID(), 'member_down',TRUE);
			$hidden=get_post_meta(get_the_ID(), 'hidden_content', true);
			$userType=getUsreMemberType();
			$down_info = null;$downMsgFree = '';$down_checkpan = '';
			
			$erphp_url_front_vip = get_bloginfo('wpurl').'/wp-admin/admin.php?page=erphpdown/admin/erphp-update-vip.php';
			if(get_option('erphp_url_front_vip')){
				$erphp_url_front_vip = get_option('erphp_url_front_vip');
			}
			$erphp_url_front_login = wp_login_url();
			if(get_option('erphp_url_front_login')){
				$erphp_url_front_login = get_option('erphp_url_front_login');
			}

			if(is_user_logged_in()){
				$erphp_url_front_vip2 = $erphp_url_front_vip;
			}else{
				$erphp_url_front_vip2 = $erphp_url_front_login;
			}

			$erphp_blank_domains = get_option('erphp_blank_domains')?get_option('erphp_blank_domains'):'pan.baidu.com';
			$erphp_colon_domains = get_option('erphp_colon_domains')?get_option('erphp_colon_domains'):'pan.baidu.com';

			if($url_free){
				$downMsgFree .= '<div class="erphpdown-title">免费资源</div><div class="erphpdown-free">';
				$downList=explode("\r\n",$url_free);
				foreach ($downList as $k=>$v){
					$filepath = $downList[$k];
					if($filepath){

						if($erphp_colon_domains){
							$erphp_colon_domains_arr = explode(',', $erphp_colon_domains);
							foreach ($erphp_colon_domains_arr as $erphp_colon_domain) {
								if(strpos($filepath, $erphp_colon_domain)){
									$filepath = str_replace('：', ': ', $filepath);
									break;
								}
							}
						}

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
								$downMsgFree.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".$filepath."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
							}elseif($arrlength == 2){
								$downMsgFree.="<div class='erphpdown-item'>".$filearr[0]."<a href='".$filearr[1]."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
							}elseif($arrlength == 3){
								$filearr2 = str_replace('：', ': ', $filearr[2]);
								$downMsgFree.="<div class='erphpdown-item'>".$filearr[0]."<a href='".$filearr[1]."' target='_blank' class='erphpdown-down'>点击下载</a>（".$filearr2."）<a class='erphpdown-copy' data-clipboard-text='".str_replace('提取码: ', '', $filearr2)."' href='javascript:;'>复制</a></div>";
							}
						}elseif(strpos($filepath,'  ') && $erphp_blank_domain_is){
							$filearr = explode('  ',$filepath);
							$arrlength = count($filearr);
							if($arrlength == 1){
								$downMsgFree.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".$filepath."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
							}elseif($arrlength >= 2){
								$filearr2 = explode(':',$filearr[0]);
								$filearr3 = explode(':',$filearr[1]);
								$downMsgFree.="<div class='erphpdown-item'>".$filearr2[0]."<a href='".trim($filearr2[1].':'.$filearr2[2])."' target='_blank' class='erphpdown-down'>点击下载</a>（提取码: ".trim($filearr3[1])."）<a class='erphpdown-copy' data-clipboard-text='".trim($filearr3[1])."' href='javascript:;'>复制</a></div>";
							}
						}elseif(strpos($filepath,' ') && $erphp_blank_domain_is){
							$filearr = explode(' ',$filepath);
							$arrlength = count($filearr);
							if($arrlength == 1){
								$downMsgFree.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".$filepath."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
							}elseif($arrlength == 2){
								$downMsgFree.="<div class='erphpdown-item'>".$filearr[0]."<a href='".$filearr[1]."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
							}elseif($arrlength >= 3){
								$downMsgFree.="<div class='erphpdown-item'>".str_replace(':', '', $filearr[0])."<a href='".$filearr[1]."' target='_blank' class='erphpdown-down'>点击下载</a>（".$filearr[2].' '.$filearr[3]."）<a class='erphpdown-copy' data-clipboard-text='".$filearr[3]."' href='javascript:;'>复制</a></div>";
							}
						}else{
							$downMsgFree.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".$filepath."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
						}
					}
				}

				$downMsgFree .= '</div>';
				if(get_option('ice_tips_free')) $downMsgFree.='<div class="erphpdown-tips erphpdown-tips-free">'.get_option('ice_tips_free').'</div>';
				if($start_down2 || $start_down){
					$downMsgFree .= '<div class="erphpdown-title">付费资源</div>';
				}
			}
			
			if($start_down2){
				if($url){
					$content.='<fieldset class="erphpdown" id="erphpdown"><legend>资源下载</legend>'.$downMsgFree;
					
					$user_id = is_user_logged_in() ? wp_get_current_user()->ID : 0;
					$wppay = new EPD(get_the_ID(), $user_id);

					if($wppay->isWppayPaid() || !$price || ($memberDown == 3 && $userType)){
						$downList=explode("\r\n",trim($url));
						foreach ($downList as $k=>$v){
							$filepath = trim($downList[$k]);
							if($filepath){

								if($erphp_colon_domains){
									$erphp_colon_domains_arr = explode(',', $erphp_colon_domains);
									foreach ($erphp_colon_domains_arr as $erphp_colon_domain) {
										if(strpos($filepath, $erphp_colon_domain)){
											$filepath = str_replace('：', ': ', $filepath);
											break;
										}
									}
								}

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
										$downMsg.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".ERPHPDOWN_URL."/download.php?postid=".get_the_ID()."&key=".($k+1)."&nologin=1' target='_blank' class='erphpdown-down'>点击下载</a></div>";
									}elseif($arrlength == 2){
										$downMsg.="<div class='erphpdown-item'>".$filearr[0]."<a href='".ERPHPDOWN_URL."/download.php?postid=".get_the_ID()."&key=".($k+1)."&nologin=1' target='_blank' class='erphpdown-down'>点击下载</a></div>";
									}elseif($arrlength == 3){
										$filearr2 = str_replace('：', ': ', $filearr[2]);
										$downMsg.="<div class='erphpdown-item'>".$filearr[0]."<a href='".ERPHPDOWN_URL."/download.php?postid=".get_the_ID()."&key=".($k+1)."&nologin=1' target='_blank' class='erphpdown-down'>点击下载</a>（".$filearr2."）<a class='erphpdown-copy' data-clipboard-text='".str_replace('提取码: ', '', $filearr2)."' href='javascript:;'>复制</a></div>";
									}
								}elseif(strpos($filepath,'  ') && $erphp_blank_domain_is){
									$filearr = explode('  ',$filepath);
									$arrlength = count($filearr);
									if($arrlength == 1){
										$downMsg.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".ERPHPDOWN_URL."/download.php?postid=".get_the_ID()."&key=".($k+1)."&nologin=1' target='_blank' class='erphpdown-down'>点击下载</a></div>";
									}elseif($arrlength >= 2){
										$filearr2 = explode(':',$filearr[0]);
										$filearr3 = explode(':',$filearr[1]);
										$downMsg.="<div class='erphpdown-item'>".$filearr2[0]."<a href='".ERPHPDOWN_URL."/download.php?postid=".get_the_ID()."&key=".($k+1)."&nologin=1' target='_blank' class='erphpdown-down'>点击下载</a>（提取码: ".trim($filearr3[1])."）<a class='erphpdown-copy' data-clipboard-text='".trim($filearr3[1])."' href='javascript:;'>复制</a></div>";
									}
								}elseif(strpos($filepath,' ') && $erphp_blank_domain_is){
									$filearr = explode(' ',$filepath);
									$arrlength = count($filearr);
									if($arrlength == 1){
										$downMsg.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".ERPHPDOWN_URL."/download.php?postid=".get_the_ID()."&key=".($k+1)."&nologin=1' target='_blank' class='erphpdown-down'>点击下载</a></div>";
									}elseif($arrlength == 2){
										$downMsg.="<div class='erphpdown-item'>".$filearr[0]."<a href='".ERPHPDOWN_URL."/download.php?postid=".get_the_ID()."&key=".($k+1)."&nologin=1' target='_blank' class='erphpdown-down'>点击下载</a></div>";
									}elseif($arrlength >= 3){
										$downMsg.="<div class='erphpdown-item'>".str_replace(':', '', $filearr[0])."<a href='".ERPHPDOWN_URL."/download.php?postid=".get_the_ID()."&key=".($k+1)."&nologin=1' target='_blank' class='erphpdown-down'>点击下载</a>（".$filearr[2].' '.$filearr[3]."）<a class='erphpdown-copy' data-clipboard-text='".$filearr[3]."' href='javascript:;'>复制</a></div>";
									}
								}else{
									$downMsg.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".ERPHPDOWN_URL."/download.php?postid=".get_the_ID()."&key=".($k+1)."&nologin=1' target='_blank' class='erphpdown-down'>点击下载</a></div>";
								}
							}
						}
						$content .= $downMsg;	
						if($hidden){
							$content .= '<div class="erphpdown-item">提取码：'.$hidden.' <a class="erphpdown-copy" data-clipboard-text="'.$hidden.'" href="javascript:;">复制</a></div>';
						}
					}else{
						if($url){
							$tname = '资源下载';
						}else{
							$tname = '内容查看';
						}
						if($memberDown == 3){
							$content .= $tname.'价格<span class="erphpdown-price">'.$price.'</span>元<a href="javascript:;" class="erphp-wppay-loader erphpdown-buy" data-post="'.get_the_ID().'">立即支付</a>&nbsp;&nbsp;<b>或</b>&nbsp;&nbsp;升级'.$erphp_vip_name.'后免费<a href="'.$erphp_url_front_vip2.'" target="_blank" class="erphpdown-vip'.(is_user_logged_in()?'':' erphp-login-must').'">升级'.$erphp_vip_name.'</a>';
						}else{
							$content .= $tname.'价格<span class="erphpdown-price">'.$price.'</span>元<a href="javascript:;" class="erphp-wppay-loader erphpdown-buy" data-post="'.get_the_ID().'">立即支付</a>';	
						}
					}
					
					if(get_option('ice_tips')) $content.='<div class="erphpdown-tips">'.get_option('ice_tips').'</div>';
					$content.='</fieldset>';
				}

			}elseif($start_down){
				$content.='<fieldset class="erphpdown" id="erphpdown"><legend>资源下载</legend>'.$downMsgFree;
				if($price_type){
					if($urls){
						$cnt = count($urls['index']);
            			if($cnt){
            				for($i=0; $i<$cnt;$i++){
            					$index = $urls['index'][$i];
            					$index_name = $urls['name'][$i];
            					$price = $urls['price'][$i];
            					$index_url = $urls['url'][$i];
            					$index_vip = $urls['vip'][$i];

            					$indexMemberDown = $memberDown;
            					if($index_vip){
            						$indexMemberDown = $index_vip;
            					}

            					$content .= '<fieldset class="erphpdown-child"><legend>'.$index_name.'</legend>';

            					if(function_exists('epd_check_pan_callback')){
									if(strpos($index_url,'pan.baidu.com') !== false){
										$down_checkpan = '<a class="erphpdown-buy erphpdown-checkpan" href="javascript:;" data-id="'.get_the_ID().'" data-index="'.$index.'" data-buy="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&index='.$index.'">网盘链接，点击检测有效后购买</a>';
									}
								}

            					if(is_user_logged_in()){
									if($price){
										if($indexMemberDown != 4 && $indexMemberDown != 8 && $indexMemberDown != 9)
											$content.='此资源下载价格为<span class="erphpdown-price">'.$price.'</span>'.get_option("ice_name_alipay");
									}else{
										if($indexMemberDown != 4 && $indexMemberDown != 8 && $indexMemberDown != 9)
											$content.='此资源为免费资源';
									}

									if($price || $indexMemberDown == 4 || $indexMemberDown == 8 || $indexMemberDown == 9){
										$user_info=wp_get_current_user();
										$down_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".get_the_ID()."' and ice_index='".$index."' and ice_success=1 and ice_user_id=".$user_info->ID." order by ice_time desc");
										if($days > 0){
											$lastDownDate = date('Y-m-d H:i:s',strtotime('+'.$days.' day',strtotime($down_info->ice_time)));
											$nowDate = date('Y-m-d H:i:s');
											if(strtotime($nowDate) > strtotime($lastDownDate)){
												$down_info = null;
											}
										}

										if($indexMemberDown > 1){
											$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
											if($userType){
												$vipText = '';
											}
											if($indexMemberDown==3 && $down_info==null){
												$content.='（'.$erphp_vip_name.'免费'.$vipText.'）';
											}elseif ($indexMemberDown==2 && $down_info==null){
												$content.='（'.$erphp_vip_name.' 5折'.$vipText.'）';
											}elseif ($indexMemberDown==5 && $down_info==null){
												$content.='（'.$erphp_vip_name.' 8折'.$vipText.'）';
											}elseif ($indexMemberDown==6 && $down_info==null){
												if($userType < 9){
													$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_year_name.'</a>';
												}
												$content.='（'.$erphp_year_name.'免费'.$vipText.'）';
											}elseif ($indexMemberDown==7 && $down_info==null){
												if($userType < 10){
													$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_life_name.'</a>';
												}
												$content.='（'.$erphp_life_name.'免费'.$vipText.'）';
											}elseif ($indexMemberDown==4){
												if($userType){
													$content.='此资源为'.$erphp_vip_name.'专享资源';
												}
											}elseif ($indexMemberDown==8){
												if($userType >= 9){
													$content.='此资源为'.$erphp_year_name.'专享资源';
												}
											}elseif ($indexMemberDown==9){
												if($userType >= 10){
													$content.='此资源为'.$erphp_life_name.'专享资源';
												}
											}
										}

										if($indexMemberDown==4 && $userType==FALSE){
											$content.='此资源仅限'.$erphp_vip_name.'下载<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
										}elseif($indexMemberDown==8 && $userType < 9){
											$content.='此资源仅限'.$erphp_year_name.'下载<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_year_name.'</a>';
										}elseif($indexMemberDown==9 && $userType < 10){
											$content.='此资源仅限'.$erphp_life_name.'下载<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_life_name.'</a>';
										}else{
											
											if($userType && $indexMemberDown > 1){
												if($indexMemberDown==3 || $indexMemberDown==4){
													if(get_option('erphp_popdown')){
														$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."&iframe=1' class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
													}else{
														$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."' class='erphpdown-down' target='_blank'>立即下载</a>";
													}
												}elseif ($indexMemberDown==2 && $down_info==null){
													if($down_checkpan) $content .= $down_checkpan;
													else $content.='<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&index='.$index.'" target="_blank">立即购买</a>';
												}elseif ($indexMemberDown==5 && $down_info==null){
													if($down_checkpan) $content .= $down_checkpan;
													else $content.='<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&index='.$index.'" target="_blank">立即购买</a>';
												}elseif ($indexMemberDown==6 && $down_info==null){
													if($userType == 9){
														if(get_option('erphp_popdown')){
															$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."&iframe=1' class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
														}else{
															$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."' class='erphpdown-down' target='_blank'>立即下载</a>";
														}	
													}elseif($userType == 10){
														if(get_option('erphp_popdown')){
															$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."&iframe=1' class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
														}else{
															$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."' class='erphpdown-down' target='_blank'>立即下载</a>";
														}	
													}else{
														if($down_checkpan) $content .= $down_checkpan;
														else $content.='<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&index='.$index.'" target="_blank">立即购买</a>';
													}
												}elseif ($indexMemberDown==7 && $down_info==null){
													if($userType == 10){
														if(get_option('erphp_popdown')){
															$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."&iframe=1' class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
														}else{
															$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."' class='erphpdown-down' target='_blank'>立即下载</a>";
														}	
													}else{
														if($down_checkpan) $content .= $down_checkpan;
														else $content.='<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&index='.$index.'" target="_blank">立即购买</a>';
													}
												}elseif ($indexMemberDown==8 && $down_info==null){
													if($userType >= 9){
														if(get_option('erphp_popdown')){
															$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."&iframe=1' class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
														}else{
															$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."' class='erphpdown-down' target='_blank'>立即下载</a>";
														}	
													}
												}elseif ($indexMemberDown==9 && $down_info==null){
													if($userType >= 10){
														if(get_option('erphp_popdown')){
															$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."&iframe=1' class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
														}else{
															$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."' class='erphpdown-down' target='_blank'>立即下载</a>";
														}	
													}
												}elseif($down_info){
													if(get_option('erphp_popdown')){
														$content.='<a href="'.constant("erphpdown").'download.php?postid='.get_the_ID().'&index='.$index.'&iframe=1" class="erphpdown-down erphpdown-down-layui">立即下载</a>';
													}else{
														$content.='<a href="'.constant("erphpdown").'download.php?postid='.get_the_ID().'&index='.$index.'" class="erphpdown-down" target="_blank">立即下载</a>';
													}
												}
											}else{
												if($down_info && $down_info->ice_price > 0){
													if(get_option('erphp_popdown')){
														$content.='<a href="'.constant("erphpdown").'download.php?postid='.get_the_ID().'&index='.$index.'&iframe=1" class="erphpdown-down erphpdown-down-layui">已购买，立即下载</a>';
													}else{
														$content.='<a href="'.constant("erphpdown").'download.php?postid='.get_the_ID().'&index='.$index.'" class="erphpdown-down" target="_blank">已购买，立即下载</a>';
													}
												}else{
													if($down_checkpan) $content .= $down_checkpan;
													else $content.='<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&index='.$index.'" target="_blank">立即购买</a>';
												}
											}
										}
										
									}else{
										if(get_option('erphp_popdown')){
											$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."&iframe=1' class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
										}else{
											$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."' class='erphpdown-down' target='_blank'>立即下载</a>";
										}
									}
									
								}else{
									if($indexMemberDown == 4 || $indexMemberDown == 8 || $indexMemberDown == 9){
										$content.='此资源仅限'.$erphp_vip_name.'下载，请先<a href="'.$erphp_url_front_login.'" target="_blank" class="erphp-login-must">登录</a>';
									}else{
										if($price){
											$content.='此资源下载价格为<span class="erphpdown-price">'.$price.'</span>'.get_option('ice_name_alipay').'，请先<a href="'.$erphp_url_front_login.'" target="_blank" class="erphp-login-must">登录</a>';
										}else{
											$content.='此资源为免费资源，请先<a href="'.$erphp_url_front_login.'" target="_blank" class="erphp-login-must">登录</a>';
										}
									}
								}
            					$content .= '</fieldset>';
            				}
            			}
					}
				}else{
					if(function_exists('epd_check_pan_callback')){
						if(strpos($url,'pan.baidu.com') !== false){
							$down_checkpan = '<a class="erphpdown-buy erphpdown-checkpan" href="javascript:;" data-id="'.get_the_ID().'" data-index="0" data-buy="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'">网盘链接，点击检测有效后购买</a>';
						}
					}
					if(is_user_logged_in()){
						if($price){
							if($memberDown != 4 && $memberDown != 8 && $memberDown != 9)
								$content.='此资源下载价格为<span class="erphpdown-price">'.$price.'</span>'.get_option("ice_name_alipay");
						}else{
							if($memberDown != 4 && $memberDown != 8 && $memberDown != 9)
								$content.='此资源仅限注册用户下载';
						}

						if($price || $memberDown == 4 || $memberDown == 8 || $memberDown == 9){
							$user_info=wp_get_current_user();
							$down_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".get_the_ID()."' and ice_success=1 and (ice_index is null or ice_index = '') and ice_user_id=".$user_info->ID." order by ice_time desc");
							if($days > 0 && $down_info){
								$lastDownDate = date('Y-m-d H:i:s',strtotime('+'.$days.' day',strtotime($down_info->ice_time)));
								$nowDate = date('Y-m-d H:i:s');
								if(strtotime($nowDate) > strtotime($lastDownDate)){
									$down_info = null;
								}
							}

							if($memberDown > 1){
								$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
								if($userType){
									$vipText = '';
								}
								if($memberDown==3 && $down_info==null){
									$content.='（'.$erphp_vip_name.'免费'.$vipText.'）';
								}elseif ($memberDown==2 && $down_info==null){
									$content.='（'.$erphp_vip_name.' 5折'.$vipText.'）';
								}elseif ($memberDown==5 && $down_info==null){
									$content.='（'.$erphp_vip_name.' 8折'.$vipText.'）';
								}elseif ($memberDown==6 && $down_info==null){
									if($userType < 9){
										$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_year_name.'</a>';
									}
									$content.='（'.$erphp_year_name.'免费'.$vipText.'）';
								}elseif ($memberDown==7 && $down_info==null){
									if($userType < 10){
										$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_life_name.'</a>';
									}
									$content.='（'.$erphp_life_name.'免费'.$vipText.'）';
								}elseif ($memberDown==4){
									if($userType){
										$content.='此资源为'.$erphp_vip_name.'专享资源';
									}
								}elseif ($memberDown==8){
									if($userType >= 9){
										$content.='此资源为'.$erphp_year_name.'专享资源';
									}
								}elseif ($memberDown==9){
									if($userType >= 10){
										$content.='此资源为'.$erphp_life_name.'专享资源';
									}
								}
							}

							if($memberDown==4 && $userType==FALSE){
								$content.='此资源仅限'.$erphp_vip_name.'下载<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
							}elseif($memberDown==8 && $userType < 9){
								$content.='此资源仅限'.$erphp_year_name.'下载<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_year_name.'</a>';
							}elseif($memberDown==9 && $userType < 10){
								$content.='此资源仅限'.$erphp_life_name.'下载<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_life_name.'</a>';
							}else{
								
								if($userType && $memberDown > 1){
									if($memberDown==3 || $memberDown==4){
										if(get_option('erphp_popdown')){
											$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&iframe=1' class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
										}else{
											$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID()." class='erphpdown-down' target='_blank'>立即下载</a>";
										}
									}elseif ($memberDown==2 && $down_info==null){
										if($down_checkpan) $content .= $down_checkpan;
										else $content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank">立即购买</a>';
									}elseif ($memberDown==5 && $down_info==null){
										if($down_checkpan) $content .= $down_checkpan;
										else $content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank">立即购买</a>';
									}elseif ($memberDown==6 && $down_info==null){
										if($userType == 9){
											if(get_option('erphp_popdown')){
												$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&iframe=1' class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
											}else{
												$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID()." class='erphpdown-down' target='_blank'>立即下载</a>";
											}	
										}elseif($userType == 10){
											if(get_option('erphp_popdown')){
												$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&iframe=1' class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
											}else{
												$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID()." class='erphpdown-down' target='_blank'>立即下载</a>";
											}	
										}else{
											if($down_checkpan) $content .= $down_checkpan;
											else $content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank">立即购买</a>';
										}
									}elseif ($memberDown==7 && $down_info==null){
										if($userType == 10){
											if(get_option('erphp_popdown')){
												$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&iframe=1' class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
											}else{
												$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID()." class='erphpdown-down' target='_blank'>立即下载</a>";
											}	
										}else{
											if($down_checkpan) $content .= $down_checkpan;
											else $content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank">立即购买</a>';
										}
									}elseif ($memberDown==8 && $down_info==null){
										if($userType >= 9){
											if(get_option('erphp_popdown')){
												$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&iframe=1' class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
											}else{
												$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID()." class='erphpdown-down' target='_blank'>立即下载</a>";
											}	
										}
									}elseif ($memberDown==9 && $down_info==null){
										if($userType >= 10){
											if(get_option('erphp_popdown')){
												$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&iframe=1' class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
											}else{
												$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID()." class='erphpdown-down' target='_blank'>立即下载</a>";
											}	
										}
									}elseif($down_info){
										if(get_option('erphp_popdown')){
											$content.='<a href="'.constant("erphpdown").'download.php?url='.$down_info->ice_url.'&iframe=1" class="erphpdown-down erphpdown-down-layui">立即下载</a>';
										}else{
											$content.='<a href='.constant("erphpdown").'download.php?url='.$down_info->ice_url.' class="erphpdown-down" target="_blank">立即下载</a>';
										}
									}
								}else{
									if($down_info && $down_info->ice_price > 0){
										if(get_option('erphp_popdown')){
											$content.='<a href="'.constant("erphpdown").'download.php?url='.$down_info->ice_url.'&iframe=1" class="erphpdown-down erphpdown-down-layui">已购买，立即下载</a>';
										}else{
											$content.='<a href='.constant("erphpdown").'download.php?url='.$down_info->ice_url.' class="erphpdown-down" target="_blank">已购买，立即下载</a>';
										}
									}else{
										if($down_checkpan) $content .= $down_checkpan;
										else $content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank">立即购买</a>';
									}
								}
							}
							
						}else{
							if(get_option('erphp_popdown')){
								$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&iframe=1' class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
							}else{
								$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID()." class='erphpdown-down' target='_blank'>立即下载</a>";
							}
						}
						
					}else{
						if($memberDown == 4 || $memberDown == 8 || $memberDown == 9){
							$content.='此资源仅限'.$erphp_vip_name.'下载，请先<a href="'.$erphp_url_front_login.'" target="_blank" class="erphp-login-must">登录</a>';
						}else{
							if($price){
								$content.='此资源下载价格为<span class="erphpdown-price">'.$price.'</span>'.get_option('ice_name_alipay').'，请先<a href="'.$erphp_url_front_login.'" target="_blank" class="erphp-login-must">登录</a>';
							}else{
								$content.='此资源仅限注册用户下载，请先<a href="'.$erphp_url_front_login.'" target="_blank" class="erphp-login-must">登录</a>';
							}
						}
					}
					
				}
				if(get_option('ice_tips')) $content.='<div class="erphpdown-tips">'.get_option('ice_tips').'</div>';
				$content.='</fieldset>';
			}elseif($start_see){
				
				if(is_user_logged_in()){
					$user_info=wp_get_current_user();
					$down_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".get_the_ID()."' and ice_success=1 and (ice_index is null or ice_index = '') and ice_user_id=".$user_info->ID." order by ice_time desc");
					if($days > 0){
						$lastDownDate = date('Y-m-d H:i:s',strtotime('+'.$days.' day',strtotime($down_info->ice_time)));
						$nowDate = date('Y-m-d H:i:s');
						if(strtotime($nowDate) > strtotime($lastDownDate)){
							$down_info = null;
						}
					}
					if( ($userType && ($memberDown==3 || $memberDown==4)) || ($down_info && $down_info->ice_price > 0) || (($memberDown==6 || $memberDown==8) && $userType >= 9) || (($memberDown==7 || $memberDown==9) && $userType == 10) || (!$price && $memberDown!=4 && $memberDown!=8 && $memberDown!=9)){
						return $content;
					}else{
					
						$content2='<fieldset class="erphpdown erphpdown-see" id="erphpdown" style="display:block"><legend>内容查看</legend>';
						if($price){
							if($memberDown != 4 && $memberDown != 8 && $memberDown != 9)
								$content2.='此内容查看价格为<span class="erphpdown-price">'.$price.'</span>'.get_option('ice_name_alipay');
						}
						
						
						if($memberDown > 1)
						{
							$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
							if($userType){
								$vipText = '';
							}
							if($memberDown==3 && $down_info==null){
								$content2.='（'.$erphp_vip_name.'免费'.$vipText.'）';
							}elseif ($memberDown==2 && $down_info==null){
								$content2.='（'.$erphp_vip_name.' 5折'.$vipText.'）';
							}elseif ($memberDown==5 && $down_info==null){
								$content2.='（'.$erphp_vip_name.' 8折'.$vipText.'）';
							}elseif ($memberDown==6 && $down_info==null){
								if($userType < 9){
									$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_year_name.'</a>';
								}
								$content2.='（'.$erphp_year_name.'免费'.$vipText.'）';
							}elseif ($memberDown==7 && $down_info==null){
								if($userType < 10){
									$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_life_name.'</a>';
								}
								$content2.='（'.$erphp_life_name.'免费'.$vipText.'）';
							}elseif ($memberDown==4){
								if($userType){
									
								}
							}
						}
						
						if($memberDown==4 && $userType==FALSE)
						{
							$content2.='此内容仅限'.$erphp_vip_name.'查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
						}elseif($memberDown==8 && $userType<9)
						{
							$content2.='此内容仅限'.$erphp_year_name.'查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_year_name.'</a>';
						}elseif($memberDown==9 && $userType<10)
						{
							$content2.='此内容仅限'.$erphp_life_name.'查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_life_name.'</a>';
						}
						else 
						{
							if($userType && $memberDown > 1)
							{
								if ($memberDown==2 && $down_info==null)
								{
									$content2.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank" >立即购买</a>';
								}
								elseif ($memberDown==5 && $down_info==null)
								{
									$content2.='<a class="erphpdown-iframe erphpdown-buy"  href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank" >立即购买</a>';
								}
								elseif ($memberDown==6 && $down_info==null)
								{
									if($userType < 9){
										$content2.='<a class="erphpdown-iframe erphpdown-buy"  href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank" >立即购买</a>';
									}
								}
								elseif ($memberDown==7 && $down_info==null)
								{
									if($userType < 10){
										$content2.='<a class="erphpdown-iframe erphpdown-buy"  href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank" >立即购买</a>';
									}
								}
							}
							else 
							{
								if($down_info  && $down_info->ice_price > 0){
									
								}else {
									$content2.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().'>立即购买</a>';
								}
							}
						}
					}

				}else{
					$content2='<fieldset class="erphpdown erphpdown-see" id="erphpdown" style="display:block"><legend>内容查看</legend>';
					
					if($memberDown == 4 || $memberDown == 8 || $memberDown == 9){
						$content2.='此内容仅限'.$erphp_vip_name.'查看，请先<a href="'.$erphp_url_front_login.'" target="_blank" class="erphp-login-must">登录</a>';
					}else{
						if($price){
							$content2.='此内容查看价格为<span class="erphpdown-price">'.$price.'</span>'.get_option('ice_name_alipay').'，请先<a href="'.$erphp_url_front_login.'" target="_blank" class="erphp-login-must">登录</a>';
						}else{
							$content2.='此内容仅限注册用户查看，请先<a href="'.$erphp_url_front_login.'" target="_blank" class="erphp-login-must">登录</a>';
						}
					}
					
				}
				if(get_option('ice_tips')) $content2.='<div class="erphpdown-tips">'.get_option('ice_tips').'</div>';
				$content2.='</fieldset>';
				return $content2;
				
			}elseif($start_see2 && $erphp_see2_style){
				
				if(is_user_logged_in()){
					$user_info=wp_get_current_user();
					$down_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".get_the_ID()."' and ice_success=1 and (ice_index is null or ice_index = '') and ice_user_id=".$user_info->ID." order by ice_time desc");
					if($days > 0){
						$lastDownDate = date('Y-m-d H:i:s',strtotime('+'.$days.' day',strtotime($down_info->ice_time)));
						$nowDate = date('Y-m-d H:i:s');
						if(strtotime($nowDate) > strtotime($lastDownDate)){
							$down_info = null;
						}
					}
					if( ($userType && ($memberDown==3 || $memberDown==4)) || ($down_info && $down_info->ice_price > 0) || (($memberDown==6 || $memberDown==8) && $userType >= 9) || (($memberDown==7 || $memberDown==9) && $userType == 10) || (!$price && $memberDown!=4 && $memberDown!=8 && $memberDown!=9)){
						
					}else{
					
						$content.='<fieldset class="erphpdown erphpdown-see" id="erphpdown" style="display:block"><legend>内容查看</legend>';
						if($price){
							if($memberDown != 4 && $memberDown != 8 && $memberDown != 9)
								$content.='本文隐藏内容查看价格为<span class="erphpdown-price">'.$price.'</span>'.get_option('ice_name_alipay');
						}
						
						
						if($memberDown > 1)
						{
							$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
							if($userType){
								$vipText = '';
							}
							if($memberDown==3 && $down_info==null){
								$content.='（'.$erphp_vip_name.'免费'.$vipText.'）';
							}elseif ($memberDown==2 && $down_info==null){
								$content.='（'.$erphp_vip_name.' 5折'.$vipText.'）';
							}elseif ($memberDown==5 && $down_info==null){
								$content.='（'.$erphp_vip_name.' 8折'.$vipText.'）';
							}elseif ($memberDown==6 && $down_info==null){
								if($userType < 9){
									$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_year_name.'</a>';
								}
								$content.='（'.$erphp_year_name.'免费'.$vipText.'）';
							}elseif ($memberDown==7 && $down_info==null){
								if($userType < 10){
									$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_life_name.'</a>';
								}
								$content.='（'.$erphp_life_name.'免费'.$vipText.'）';
							}elseif ($memberDown==4){
								if($userType){
									
								}
							}
						}
						
						if($memberDown==4 && $userType==FALSE)
						{
							$content.='此内容仅限'.$erphp_vip_name.'查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
						}elseif($memberDown==8 && $userType<9)
						{
							$content.='此内容仅限'.$erphp_year_name.'查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_year_name.'</a>';
						}elseif($memberDown==9 && $userType<10)
						{
							$content.='此内容仅限'.$erphp_life_name.'查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_life_name.'</a>';
						}
						else 
						{
							if($userType && $memberDown > 1)
							{
								if ($memberDown==2 && $down_info==null)
								{
									$content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank" >立即购买</a>';
								}
								elseif ($memberDown==5 && $down_info==null)
								{
									$content.='<a class="erphpdown-iframe erphpdown-buy"  href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank" >立即购买</a>';
								}
								elseif ($memberDown==6 && $down_info==null)
								{
									if($userType < 9){
										$content.='<a class="erphpdown-iframe erphpdown-buy"  href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank" >立即购买</a>';
									}
								}
								elseif ($memberDown==7 && $down_info==null)
								{
									if($userType < 10){
										$content.='<a class="erphpdown-iframe erphpdown-buy"  href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank" >立即购买</a>';
									}
								}
							}
							else 
							{
								if($down_info  && $down_info->ice_price > 0){
									
								}else {
									$content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().'>立即购买</a>';
								}
							}
						}
					}

				}else{
					$content.='<fieldset class="erphpdown erphpdown-see" id="erphpdown" style="display:block"><legend>内容查看</legend>';
					
					if($memberDown == 4 || $memberDown == 8 || $memberDown == 9){
						$content.='本文隐藏内容仅限'.$erphp_vip_name.'查看，请先<a href="'.$erphp_url_front_login.'" target="_blank" class="erphp-login-must">登录</a>';
					}else{
						if($price){
							$content.='本文隐藏内容查看价格为<span class="erphpdown-price">'.$price.'</span>'.get_option('ice_name_alipay').'，请先<a href="'.$erphp_url_front_login.'" target="_blank" class="erphp-login-must">登录</a>';
						}else{
							$content.='此内容仅限注册用户查看，请先<a href="'.$erphp_url_front_login.'" target="_blank" class="erphp-login-must">登录</a>';
						}
					}
					
				}
				if(get_option('ice_tips')) $content.='<div class="erphpdown-tips">'.get_option('ice_tips').'</div>';
				$content.='</fieldset>';
				return $content;
				
			}else{
				if($downMsgFree) $content.='<fieldset class="erphpdown" id="erphpdown"><legend>资源下载</legend>'.$downMsgFree.'</fieldset>';
			}
			
		}else{
			if($start_see){
				return '';
			}
		}
	}
	
	return apply_filters('erphpdown_content_show', $content);
}

