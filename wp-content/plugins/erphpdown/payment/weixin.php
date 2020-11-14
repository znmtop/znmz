<?php
if(isset($_GET['redirect_url'])){
    $_COOKIE['erphpdown_return'] = urldecode($_GET['redirect_url']);
    setcookie('erphpdown_return',urldecode($_GET['redirect_url']),0,'/');
}else{
    $_COOKIE['erphpdown_return'] = '';
    setcookie('erphpdown_return','',0,'/');
}
require_once('../../../../wp-load.php');
header("Content-Type: text/html;charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
if(!is_user_logged_in()){wp_die('请先登录！','提示');}
$post_id   = isset($_GET['ice_post']) && is_numeric($_GET['ice_post']) ?$_GET['ice_post'] :0;
$user_type   = isset($_GET['ice_type']) && is_numeric($_GET['ice_type']) ?$_GET['ice_type'] :'';
$index   = isset($_GET['index']) && is_numeric($_GET['index']) ?$_GET['index'] :'';
$ice_weixin_app  = get_option('ice_weixin_app');
if($post_id){
    if($index){
        $urls = get_post_meta($post_id, 'down_urls', true);
        if($urls){
            $cnt = count($urls['index']);
            if($cnt){
                for($i=0; $i<$cnt;$i++){
                    if($urls['index'][$i] == $index){
                        $index_name = $urls['name'][$i];
                        $price = $urls['price'][$i];
                        break;
                    }
                }
            }
        }
    }else{
        $price=get_post_meta($post_id, 'down_price', true);
    }
    $start_down2 = get_post_meta($post_id, 'start_down2',TRUE);
    if(!$start_down2){
        $price = $price / get_option("ice_proportion_alipay");
    }
    $memberDown=get_post_meta($post_id, 'member_down',TRUE);
    $userType=getUsreMemberType();
    if($userType && $memberDown==2){
        $price=sprintf("%.2f",$price*0.5);
    }elseif($userType && $memberDown==5){
        $price=sprintf("%.2f",$price*0.8);
    }
}elseif($user_type){
    $ciphp_life_price    = get_option('ciphp_life_price');
    $ciphp_year_price    = get_option('ciphp_year_price');
    $ciphp_quarter_price = get_option('ciphp_quarter_price');
    $ciphp_month_price  = get_option('ciphp_month_price');
    $ciphp_day_price  = get_option('ciphp_day_price');
    if($user_type == 6){
        $price = $ciphp_day_price/get_option('ice_proportion_alipay');
    }elseif($user_type == 7){
        $price = $ciphp_month_price/get_option('ice_proportion_alipay');
    }elseif($user_type == 8){
        $price = $ciphp_quarter_price/get_option('ice_proportion_alipay');
    }elseif($user_type == 9){
        $price = $ciphp_year_price/get_option('ice_proportion_alipay');
    }elseif($user_type == 10){
        $price = $ciphp_life_price/get_option('ice_proportion_alipay');
    }
}else{
	$price   = isset($_GET['ice_money']) && is_numeric($_GET['ice_money']) ?$_GET['ice_money'] :0;
	$price = $wpdb->escape($price);
	$erphpdown_min_price    = get_option('erphpdown_min_price');
	if($erphpdown_min_price > 0){
		if($price < $erphpdown_min_price){
			wp_die('您最低需充值'.$erphpdown_min_price.'元','提示');
		}
	}
}


if($price){

	global $wpdb;
	$subject = get_bloginfo('name').'订单['.get_the_author_meta( 'user_login', wp_get_current_user()->ID ).']';  
	$out_trade_no = date("ymdhis").mt_rand(100,999).mt_rand(100,999).mt_rand(100,999);	
	$time = date('Y-m-d H:i:s');
	$user_Info   = wp_get_current_user();
	$sql="INSERT INTO $wpdb->icemoney (ice_money,ice_num,ice_user_id,ice_user_type,ice_post_id,ice_post_index,ice_time,ice_success,ice_note,ice_success_time,ice_alipay)
	VALUES ('$price','$out_trade_no','".$user_Info->ID."','".$user_type."','".$post_id."','".$index."','".date("Y-m-d H:i:s")."',0,'0','".date("Y-m-d H:i:s")."','')";
	$a=$wpdb->query($sql);
	if(!$a){
		wp_die('系统发生错误，请稍后重试!','提示');
	}else{
		$money_info=$wpdb->get_row("select * from ".$wpdb->icemoney." where ice_num='".$out_trade_no."'");
	}

    if($ice_weixin_app && erphpdown_is_mobile() && !erphpdown_is_weixin()){
        $appid = get_option('ice_weixin_appid');
        $mch_id = get_option('ice_weixin_mchid');
        $key = get_option('ice_weixin_key');
        $nonce_str=MD5($out_trade_no);
        $body = $subject;
        $total_fee = $price*100; 
        $spbill_create_ip = erphpGetIP(); 
        $notify_url = constant("erphpdown").'payment/weixin/notify.php'; 
        $trade_type = 'MWEB';
        $scene_info ='{"h5_info":{"type":"Wap","wap_url":"'.home_url().'","wap_name":"支付"}}';
        $signA ="appid=$appid&body=$body&mch_id=$mch_id&nonce_str=$nonce_str&notify_url=$notify_url&out_trade_no=$out_trade_no&scene_info=$scene_info&spbill_create_ip=$spbill_create_ip&total_fee=$total_fee&trade_type=$trade_type";
        $strSignTmp = $signA."&key=$key";
        $sign = strtoupper(MD5($strSignTmp)); 
        $post_data = "<xml>
                        <appid>$appid</appid>
                        <body>$body</body>
                        <mch_id>$mch_id</mch_id>
                        <nonce_str>$nonce_str</nonce_str>
                        <notify_url>$notify_url</notify_url>
                        <out_trade_no>$out_trade_no</out_trade_no>
                        <scene_info>$scene_info</scene_info>
                        <spbill_create_ip>$spbill_create_ip</spbill_create_ip>
                        <total_fee>$total_fee</total_fee>
                        <trade_type>$trade_type</trade_type>
                        <sign>$sign</sign>
                    </xml>";
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        $dataxml = erphpdown_http_post($url,$post_data);
        $objectxml = (array)simplexml_load_string($dataxml, 'SimpleXMLElement', LIBXML_NOCDATA);
        wp_redirect($objectxml['mweb_url']);
    }elseif($ice_weixin_app && erphpdown_is_mobile() && erphpdown_is_weixin()){
        require_once "weixin/lib/WxPay.Api.php";
        require_once "weixin/lib/WxPay.JsApiPay.php";

        $tools = new JsApiPay();
        $openId = $tools->GetOpenid();

        $input = new WxPayUnifiedOrder();
        $input->SetBody($subject);
        $input->SetAttach("ERPHP");
        $input->SetOut_trade_no($out_trade_no);
        $input->SetTotal_fee($price*100);
        $input->SetTime_start(date("YmdHis"));
        //$input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("MBT");
        $input->SetNotify_url(constant("erphpdown").'payment/weixin/notify.php');
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = WxPayApi::unifiedOrder($input);
        //var_dump($order);exit;
        if($order['result_code'] == 'FAIL') wp_die($order['err_code_des'],'提示');
        $jsApiParameters = $tools->GetJsApiParameters($order);
        $editAddress = $tools->GetEditAddressParameters();
        ?>

        <html>
        <head>
            <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
            <meta name="viewport" content="width=device-width, initial-scale=1"/> 
            <title>微信支付</title>
            <script type="text/javascript">
            function jsApiCall()
            {
                WeixinJSBridge.invoke(
                    'getBrandWCPayRequest',
                    <?php echo $jsApiParameters; ?>,
                    function(res){
                        WeixinJSBridge.log(res.err_msg);
                        /*alert(res.err_code+res.err_desc+res.err_msg);*/
                        if(res.err_msg == "get_brand_wcpay_request:ok" ){
                            alert("支付成功！");
                            <?php if(isset($_COOKIE['erphpdown_return']) && $_COOKIE['erphpdown_return']){?>
                            location.href="<?php echo $_COOKIE['erphpdown_return'];?>";
                            <?php }elseif(get_option('erphp_url_front_success')){?>
                            location.href="<?php echo get_option('erphp_url_front_success');?>";
                            <?php }else{?>
                            window.close();
                            <?php }?>
                        }
                    }
                );
            }

            function callpay()
            {
                if (typeof(WeixinJSBridge) == "undefined"){
                    if( document.addEventListener ){
                        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                    }else if (document.attachEvent){
                        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
                        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                    }
                }else{
                    jsApiCall();
                }
            }
            </script>
            <script type="text/javascript">
            function editAddress()
            {
                WeixinJSBridge.invoke(
                    'editAddress',
                    <?php echo $editAddress; ?>,
                    function(res){
                        var value1 = res.proviceFirstStageName;
                        var value2 = res.addressCitySecondStageName;
                        var value3 = res.addressCountiesThirdStageName;
                        var value4 = res.addressDetailInfo;
                        var tel = res.telNumber;
                        
                        //alert(value1 + value2 + value3 + value4 + ":" + tel);
                    }
                );
            }
            
            window.onload = function(){
                if (typeof(WeixinJSBridge) == "undefined"){
                    if( document.addEventListener ){
                        document.addEventListener('WeixinJSBridgeReady', editAddress, false);
                    }else if (document.attachEvent){
                        document.attachEvent('WeixinJSBridgeReady', editAddress); 
                        document.attachEvent('onWeixinJSBridgeReady', editAddress);
                    }
                }else{
                    editAddress();
                }
            };
            
            </script>
        </head>
        <body>
            <br/>
            <div align="center"><?php bloginfo('name');?><br/><span style="font-size:36px">￥<?php echo $price;?></span></div><br/><br/>
            <div style="background: #fff;border-top:1px solid #eaeaea;border-bottom:1px solid #eaeaea;padding:15px;color:#aaa">收款方<span style="float:right;color:#222"><?php bloginfo('name');?></span></div>
            <div align="center" style="margin-top: 20px;">
                <button style="width:90%; height:40px; border-radius: 2px;background-color:#43be15; border:none; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="javascript:callpay();return false;" >立即支付</button>
            </div>
        </body>
        </html>
    <?php }else{

    require_once "weixin/lib/WxPay.Api.php";
    require_once "weixin/lib/WxPay.NativePay.php";

	$notify = new NativePay();
	$input = new WxPayUnifiedOrder();
	$input->SetBody($subject);
	$input->SetAttach("ERPHPDOWN");
	$input->SetOut_trade_no($out_trade_no);
	$input->SetTotal_fee($price*100);
	$input->SetTime_start(date("YmdHis"));
	//$input->SetTime_expire(date("YmdHis", time() + 600));
	$input->SetGoods_tag("MBT");
	$input->SetNotify_url(constant("erphpdown").'payment/weixin/notify.php');
	$input->SetTrade_type("NATIVE");
	$input->SetProduct_id($out_trade_no);
	$result = $notify->GetPayUrl($input);
	//var_dump($result);
	if($result["return_code"] == 'FAIL'){
		wp_die($result["return_msg"],'提示');
	}else{
	$url2 = $result["code_url"];
?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" /> 
    <title>微信支付</title>
    <link rel='stylesheet'  href='../static/erphpdown.css' type='text/css' media='all' />
</head>
<body<?php if(!isset($_GET['iframe'])){echo ' class="erphpdown-page-pay"';}?>>

	<div class="wppay-custom-modal-box mobantu-wppay erphpdown-custom-modal-box">
		<section class="wppay-modal">
                    
            <section class="erphp-wppay-qrcode mobantu-wppay wppay-net">
                <section class="tab">
                    <a href="javascript:;" class="active"><div class="payment"><img src="<?php echo constant("erphpdown");?>static/images/payment-weixin.png"></div>￥<?php echo sprintf("%.2f",$price);?></a>
                           </section>
                <section class="tab-list" style="background-color: #21ab36;">
                    <section class="item">
                        <section class="qr-code">
                            <img src="<?php echo constant("erphpdown");?>includes/qrcode.php?data=<?php echo urlencode($url2);?>" class="img" alt="">
                        </section>
                        <p class="account">支付完成后请等待5秒左右</p>
                        <p id="time" class="desc"></p>
                        <?php if(wp_is_mobile()){
                            echo '<p class="wap">请截屏后，打开微信扫一扫，从相册选择二维码图片</p>';
                        }?>
                    </section>
                </section>
            </section>
        
    	</section>
    </div>

    <script src="<?php echo ERPHPDOWN_URL;?>/static/jquery-1.7.min.js"></script>
	<script>
		erphpOrder = setInterval(function() {
			$.ajax({  
	            type: 'POST',  
	            url: '<?php echo ERPHPDOWN_URL;?>/admin/action/order.php',  
	            data: {
	            	do: 'checkOrder',
	            	order: '<?php echo $money_info->ice_id;?>'
	            },  
	            dataType: 'text',
	            success: function(data){  
	                if( $.trim(data) == '1' ){
	                    clearInterval(erphpOrder);
                        <?php if(isset($_GET['iframe'])){?>
                            var mylayer= parent.layer.getFrameIndex(window.name);
                            parent.layer.close(mylayer);
                            parent.layer.msg('充值成功！');
                            parent.location.reload();  
                        <?php }else{?>
    	                    alert('支付成功！');
    	                    <?php if(isset($_COOKIE['erphpdown_return']) && $_COOKIE['erphpdown_return']){?>
                            location.href="<?php echo $_COOKIE['erphpdown_return'];?>";
    	                    <?php }elseif(get_option('erphp_url_front_success')){?>
    	                    location.href="<?php echo get_option('erphp_url_front_success');?>";
    	                    <?php }else{?>
    	                    window.close();
    	                	<?php }?>
                        <?php }?>
	                }  
	            },
	            error: function(XMLHttpRequest, textStatus, errorThrown){
	            	//alert(errorThrown);
	            }
	        });

		}, 5000);

		var m = 5, s = 0;  
        var Timer = document.getElementById("time");
        wppayCountdown();
        erphpTimer = setInterval(function(){ wppayCountdown() },1000);
        function wppayCountdown (){
            Timer.innerHTML = "支付倒计时：<span>0"+m+"分"+s+"秒</span>";
            if( m == 0 && s == 0 ){
                clearInterval(erphpOrder);
                clearInterval(erphpTimer);
                $(".qr-code").append('<div class="expired"></div>');
                m = 4;
                s = 59;
            }else if( m >= 0 ){
                if( s > 0 ){
                    s--;
                }else if( s == 0 ){
                    m--;
                    s = 59;
                }
            }
        }
	</script>
</body>
</html>

<?php } }}?>