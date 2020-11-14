<?php 
require_once '../../../wp-load.php';
date_default_timezone_set('Asia/Shanghai');
if(!is_user_logged_in()){
	wp_die('请先登录系统');
}?>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8" />
	<link rel="stylesheet" href="<?php echo constant("erphpdown"); ?>static/erphpdown.css?v=<?php echo $erphpdown_version;?>" type="text/css" />
	<script type="text/javascript" src="<?php echo ERPHPDOWN_URL;?>/static/jquery-1.7.min.js"></script>
</head>
<body style="margin:15px;padding: 0">
	<div id="erphpdown-paybox">
	<?php
	$erphp_ajaxbuy = get_option('erphp_ajaxbuy');
	$erphp_justbuy = get_option('erphp_justbuy');
	$postid=isset($_GET['postid']) && is_numeric($_GET['postid']) ?intval($_GET['postid']) :false;
	$index=isset($_GET['index']) && is_numeric($_GET['index']) ?intval($_GET['index']) : '';
	$postid = $wpdb->escape($postid);
	$index = esc_sql($index);
	if($postid){
		$user_info=wp_get_current_user();
		$days=get_post_meta($postid, 'down_days', true);
		$down_repeat = get_post_meta($postid, 'down_repeat', true);
		$down_only_pay = get_post_meta($postid, 'down_only_pay', true);

		if($user_info->ID){

			if($index){
				$downInfo=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".$postid."' and ice_index='".$index."' and ice_success=1 and ice_user_id=".$user_info->ID." order by ice_time desc");
			}else{
				$downInfo=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_user_id=".$user_info->ID ." and ice_post=".$postid." and ice_success=1 and (ice_index is null or ice_index = '') order by ice_time desc");
			}

			if($days > 0){
				$lastDownDate = date('Y-m-d H:i:s',strtotime('+'.$days.' day',strtotime($downInfo->ice_time)));
				$nowDate = date('Y-m-d H:i:s');
				if(strtotime($nowDate) > strtotime($lastDownDate)){
					$downInfo = null;
				}
			}

			if($downInfo && !$down_repeat){
				?>
				<a class="erphpdown-btn" href="<?php echo constant("erphpdown");?>download.php?postid=<?php echo $postid?>&index=<?php echo $index;?>">您已经购买过，点击直接下载</a>	
				<?php
			}else{

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
            				$price_old = $price;
							$hidden=get_post_meta($postid, 'hidden_content', true);
							if($price){
								$okMoney=erphpGetUserOkMoney();
								$vip=false;
								$memberDown=get_post_meta($postid, 'member_down',TRUE);
								$indexMemberDown = $memberDown;
            					if($index_vip){
            						$indexMemberDown = $index_vip;
            					}
								$userType=getUsreMemberType();
								if( $indexMemberDown==4 || $indexMemberDown==8 || $indexMemberDown==9)
								{
									echo "您无权购买此资源！";exit;
								}
								if($userType && $indexMemberDown==2)
								{
									$vip=TRUE;
									$price=$price*0.5;
								}
								if($userType && $indexMemberDown==5)
								{
									$vip=TRUE;
									$price=$price*0.8;
								}

								$erphp_url_front_recharge = get_bloginfo('wpurl').'/wp-admin/admin.php?page=erphpdown/admin/erphp-add-money-online.php';
								if(get_option('erphp_url_front_recharge')){
									$erphp_url_front_recharge = get_option('erphp_url_front_recharge');
								}
								?>

								<table class="erphpdown-table" width="100%" align="center">
									<tr>
										<td><div style="display:inline-block;max-height:60px;overflow:hidden;"><span>名称：</span><?php echo get_post($postid)->post_title;?> - <?php echo $index_name;?></div></td>
									</tr>
									<tr>
										<td><span>价格：</span><?php echo sprintf("%.2f",$price);?><?php echo  $vip==TRUE?' <del>(原价:'.sprintf("%.2f",$price_old).')</del>' :'';?> <?php echo get_option('ice_name_alipay');?></td>
									</tr>
									<?php if(!$down_only_pay){?>
									<tr>
										<td><span>余额：</span><?php echo sprintf("%.2f",$okMoney);?> <?php echo get_option('ice_name_alipay');?></td>
									</tr>
									<?php }?>
									<tr>
										<td style="padding-top:10px;">
										<?php if(sprintf("%.2f",$okMoney) >= sprintf("%.2f",$price) && !$down_only_pay) {?>
											<?php if($erphp_ajaxbuy){?>
											<button class="erphpdown-btn do-erphpdown-pay" data-href="<?php echo constant("erphpdown").'checkout-ajax.php?postid='.$postid;?>&index=<?php echo $index;?>" style="border:none;cursor: pointer;">使用余额支付</button>
											<?php }else{?>
											<a class="ss-button erphpdown-btn" href="<?php echo constant("erphpdown").'checkout.php?postid='.$postid; ?>&index=<?php echo $index;?>" target="_blank">使用余额支付</a>
											<?php }?>
										<?php }else{

											if($erphp_justbuy){
												echo '<div class="erphp-justbuy">';
										?>
											<?php if(get_option('ice_weixin_mchid')){?> 
												<a href="<?php echo constant("erphpdown")."payment/weixin.php?ice_post=".$postid."&index=".$index."&redirect_url=".urlencode(get_permalink($postid));?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> 微信支付</a>
											<?php }?>
											<?php if(get_option('ice_ali_partner') && !erphpdown_is_weixin()){?> 
												<a href="<?php echo constant("erphpdown")."payment/alipay.php?ice_post=".$postid."&index=".$index."&redirect_url=".urlencode(get_permalink($postid));?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> 支付宝</a>
											<?php }?>
											<?php if(get_option('erphpdown_f2fpay_id') && !erphpdown_is_weixin()){?> 
												<a href="<?php echo constant("erphpdown")."payment/f2fpay.php?ice_post=".$postid."&index=".$index."&redirect_url=".urlencode(get_permalink($postid));?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> 支付宝</a>
											<?php }?>
											<?php if(get_option('ice_payapl_api_uid')){?> 
												<a href="<?php echo constant("erphpdown")."payment/paypal.php?ice_post=".$postid."&index=".$index."&redirect_url=".urlencode(get_permalink($postid));?>" class="pmt-pp erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-paypay"></i> Paypal</a>
											<?php }?> 
											<?php if(get_option('erphpdown_xhpay_appid31')){?> 
												<a href="<?php echo constant("erphpdown")."payment/xhpay3.php?ice_post=".$postid."&index=".$index."&type=2"."&redirect_url=".urlencode(get_permalink($postid));?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> 微信支付</a>   
											<?php }?>
											<?php if(get_option('erphpdown_xhpay_appid32')){?> 
												<a href="<?php echo constant("erphpdown")."payment/xhpay3.php?ice_post=".$postid."&index=".$index."&redirect_url=".urlencode(get_permalink($postid));?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> 支付宝</a>  
											<?php }?>
											<?php if(get_option('erphpdown_paypy_key')){?> 
												<?php if(!get_option('erphpdown_paypy_wxpay')){?><a href="<?php echo constant("erphpdown")."payment/paypy.php?ice_post=".$postid."&index=".$index."&redirect_url=".urlencode(get_permalink($postid));?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> 微信支付</a><?php }?>  
												<?php if(!get_option('erphpdown_paypy_alipay')){?><a href="<?php echo constant("erphpdown")."payment/paypy.php?ice_post=".$postid."&index=".$index."&type=alipay"."&redirect_url=".urlencode(get_permalink($postid));?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> 支付宝</a><?php }?>
											<?php }?>
											<?php if(get_option('erphpdown_codepay_appid')){?> 
												<a href="<?php echo constant("erphpdown")."payment/codepay.php?ice_post=".$postid."&index=".$index."&type=1"."&redirect_url=".urlencode(get_permalink($postid));?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> 支付宝</a>
												<a href="<?php echo constant("erphpdown")."payment/codepay.php?ice_post=".$postid."&index=".$index."&type=3"."&redirect_url=".urlencode(get_permalink($postid));?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> 微信支付</a>
												<?php if(!get_option('erphpdown_codepay_qqpay')){?><a href="<?php echo constant("erphpdown")."payment/codepay.php?ice_post=".$postid."&index=".$index."&type=2"."&redirect_url=".urlencode(get_permalink($postid));?>" class="pmt-qq erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-qqpay"></i> QQ钱包</a><?php }?>    
											<?php }?>
										<?php	
												echo '</div>';
											}
											if(!$down_only_pay){
												echo "<div class='erphp-creditbuy'><a target=_blank class='erphpdown-btn erphpdown-jump' href='".$erphp_url_front_recharge."'>充值后余额支付</a></div>";
											}
										}?>
										</td>
									</tr>
								</table>
								<?php
							}else{
								echo "获取文章价格出错!";
							}
            			}
            		}
				}else{
					$price=get_post_meta($postid, 'down_price', true);
					$start_down2 = get_post_meta($postid, 'start_down2',TRUE);
					if($start_down2){
						$price = $price*get_option('ice_proportion_alipay');
					}
					$price_old = $price;
					$hidden=get_post_meta($postid, 'hidden_content', true);
					if($price){
						$okMoney=erphpGetUserOkMoney();
						$vip=false;
						$memberDown=get_post_meta($postid, 'member_down',TRUE);
						$userType=getUsreMemberType();
						if( $memberDown==4 || $memberDown==8 || $memberDown==9)
						{
							echo "您无权购买此资源！";exit;
						}
						if($userType && $memberDown==2)
						{
							$vip=TRUE;
							$price=$price*0.5;
						}
						if($userType && $memberDown==5)
						{
							$vip=TRUE;
							$price=$price*0.8;
						}

						$erphp_url_front_recharge = get_bloginfo('wpurl').'/wp-admin/admin.php?page=erphpdown/admin/erphp-add-money-online.php';
						if(get_option('erphp_url_front_recharge')){
							$erphp_url_front_recharge = get_option('erphp_url_front_recharge');
						}
						?>

						<table class="erphpdown-table" width="100%" align="center">
							<tr>
								<td><div style="display:inline-block;max-height:60px;overflow:hidden;"><span>名称：</span><?php echo get_post($postid)->post_title;?></div></td>
							</tr>
							<tr>
								<td><span>价格：</span><?php echo sprintf("%.2f",$price);?><?php echo  $vip==TRUE?' <del>(原价:'.sprintf("%.2f",$price_old).')</del>' :'';?> <?php echo get_option('ice_name_alipay');?></td>
							</tr>
							<?php if(!$down_only_pay){?>
							<tr>
								<td><span>余额：</span><?php echo sprintf("%.2f",$okMoney);?> <?php echo get_option('ice_name_alipay');?></td>
							</tr>
							<?php }?>
							<tr>
								<td style="padding-top:10px;">
								<?php if(sprintf("%.2f",$okMoney) >= sprintf("%.2f",$price) && !$down_only_pay) {?>
									<?php if($erphp_ajaxbuy){?>
									<button class="erphpdown-btn do-erphpdown-pay" data-href="<?php echo constant("erphpdown").'checkout-ajax.php?postid='.$postid;?>" style="border:none;cursor: pointer;">使用余额支付</button>
									<?php }else{?>
									<a class="ss-button erphpdown-btn" href="<?php echo constant("erphpdown").'checkout.php?postid='.$postid; ?>"
										target="_blank">使用余额支付</a>
									<?php }?>
								<?php }else{

									if($erphp_justbuy){
										echo '<div class="erphp-justbuy">';
								?>
									<?php if(get_option('ice_weixin_mchid')){?> 
										<a href="<?php echo constant("erphpdown")."payment/weixin.php?ice_post=".$postid."&redirect_url=".urlencode(get_permalink($postid));?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> 微信支付</a>
									<?php }?>
									<?php if(get_option('ice_ali_partner') && !erphpdown_is_weixin()){?> 
										<a href="<?php echo constant("erphpdown")."payment/alipay.php?ice_post=".$postid."&redirect_url=".urlencode(get_permalink($postid));?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> 支付宝</a>
									<?php }?>
									<?php if(get_option('erphpdown_f2fpay_id') && !erphpdown_is_weixin()){?> 
										<a href="<?php echo constant("erphpdown")."payment/f2fpay.php?ice_post=".$postid."&redirect_url=".urlencode(get_permalink($postid));?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> 支付宝</a>
									<?php }?>
									<?php if(get_option('ice_payapl_api_uid')){?> 
										<a href="<?php echo constant("erphpdown")."payment/paypal.php?ice_post=".$postid."&redirect_url=".urlencode(get_permalink($postid));?>" class="pmt-pp erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-paypay"></i> Paypal</a>
									<?php }?> 
									<?php if(get_option('erphpdown_xhpay_appid31')){?> 
										<a href="<?php echo constant("erphpdown")."payment/xhpay3.php?ice_post=".$postid."&type=2"."&redirect_url=".urlencode(get_permalink($postid));?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> 微信支付</a>   
									<?php }?>
									<?php if(get_option('erphpdown_xhpay_appid32')){?> 
										<a href="<?php echo constant("erphpdown")."payment/xhpay3.php?ice_post=".$postid."&redirect_url=".urlencode(get_permalink($postid));?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> 支付宝</a>  
									<?php }?>
									<?php if(get_option('erphpdown_paypy_key')){?> 
										<?php if(!get_option('erphpdown_paypy_wxpay')){?><a href="<?php echo constant("erphpdown")."payment/paypy.php?ice_post=".$postid."&redirect_url=".urlencode(get_permalink($postid));?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> 微信支付</a><?php }?>  
										<?php if(!get_option('erphpdown_paypy_alipay')){?><a href="<?php echo constant("erphpdown")."payment/paypy.php?ice_post=".$postid."&type=alipay"."&redirect_url=".urlencode(get_permalink($postid));?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> 支付宝</a><?php }?>
									<?php }?>
									<?php if(get_option('erphpdown_codepay_appid')){?> 
										<a href="<?php echo constant("erphpdown")."payment/codepay.php?ice_post=".$postid."&type=1"."&redirect_url=".urlencode(get_permalink($postid));?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> 支付宝</a>
										<a href="<?php echo constant("erphpdown")."payment/codepay.php?ice_post=".$postid."&type=3"."&redirect_url=".urlencode(get_permalink($postid));?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> 微信支付</a>
										<?php if(!get_option('erphpdown_codepay_qqpay')){?><a href="<?php echo constant("erphpdown")."payment/codepay.php?ice_post=".$postid."&type=2"."&redirect_url=".urlencode(get_permalink($postid));?>" class="pmt-qq erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-qqpay"></i> QQ钱包</a><?php }?>    
									<?php }?>
								<?php	
										echo '</div>';
									}
									if(!$down_only_pay){
										echo "<div class='erphp-creditbuy'><a target=_blank class='erphpdown-btn erphpdown-jump' href='".$erphp_url_front_recharge."'>充值后余额支付</a></div>";
									}
								}?>
								</td>
							</tr>
						</table>
							<?php
					}else{
						echo "获取文章价格出错!";
					}
				}
			}
		}else{
			echo "用户信息获取失败";
		}
	}else{
		echo "文章ID错误";
	}
	?>

	</div>
	<?php if($erphp_ajaxbuy){?>
	<script>
		$(".erphpdown-jump").click(function(){
			parent.layer.closeAll();
		});

		$(".do-erphpdown-pay").click(function(){
			var that = $(this);
			that.text("处理中...").attr("disabled","disabled");
			$.ajax({  
	            type: 'GET',  
	            url:  $(this).data("href"),  
	            dataType: 'json',
				data: {

				},
	            success: function(data){
	            	that.text("使用余额支付").removeAttr("disabled");  
	                if( data.error ){
	                    if( data.msg ){
	                        alert(data.msg)
	                    }
	                    return
	                }else{
	                	if(data.jump == '2'){
	                		parent.location.reload();
	                	}else if(data.jump == '1'){
	                		parent.location.href=data.link;
	                	}else{
	                		parent.location.reload();
	                	}
	                }

	            }  

	        });
	        return false;
		});
	</script>
	<?php }?>
</body>
</html>
