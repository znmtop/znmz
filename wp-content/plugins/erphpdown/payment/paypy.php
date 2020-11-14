<?php 
if(isset($_GET['redirect_url'])){
    $_COOKIE['erphpdown_return'] = urldecode($_GET['redirect_url']);
    setcookie('erphpdown_return',urldecode($_GET['redirect_url']),0,'/');
}else{
    $_COOKIE['erphpdown_return'] = '';
    setcookie('erphpdown_return','',0,'/');
}
require_once('../../../../wp-load.php');
$secretkey = get_option('erphpdown_paypy_key');
$api = get_option('erphpdown_paypy_api').'api/order/';

header("Content-Type: text/html;charset=utf-8");
date_default_timezone_set('Asia/Shanghai');

if(!is_user_logged_in()){wp_die('请先登录！','提示');}

$post_id   = isset($_GET['ice_post']) && is_numeric($_GET['ice_post']) ?$_GET['ice_post'] :0;
$user_type   = isset($_GET['ice_type']) && is_numeric($_GET['ice_type']) ?$_GET['ice_type'] :'';
$index   = isset($_GET['index']) && is_numeric($_GET['index']) ?$_GET['index'] :'';
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

$trade_order_id = date("ymdhis").mt_rand(100,999).mt_rand(100,999).mt_rand(100,999);
$subject = get_bloginfo('name').'订单['.get_the_author_meta( 'user_login', wp_get_current_user()->ID ).']';

if($price > 0){
    $user_Info   = wp_get_current_user();
    $sql="INSERT INTO $wpdb->icemoney (ice_money,ice_num,ice_user_id,ice_user_type,ice_post_id,ice_post_index,ice_time,ice_success,ice_note,ice_success_time,ice_alipay)
    VALUES ('$price','$trade_order_id','".$user_Info->ID."','".$user_type."','".$post_id."','".$index."','".date("Y-m-d H:i:s")."',0,'0','".date("Y-m-d H:i:s")."','')";
    $a=$wpdb->query($sql);
    if(!$a){
        wp_die('系统发生错误，请稍后重试!');
    }else{
		$money_info=$wpdb->get_row("select * from ".$wpdb->icemoney." where ice_num='".$trade_order_id."'");
	}
}else{
    wp_die('请输入您要充值的金额');
}

$order_type = 'wechat';
if($_GET['type'] == 'alipay') $order_type = 'alipay';

$sign = md5(md5($trade_order_id.$price).$secretkey);
$logged_ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? esc_sql($_SERVER['HTTP_X_FORWARDED_FOR']) : esc_sql($_SERVER['REMOTE_ADDR']);

/*$body = array("order_id"=>$trade_order_id, "order_type"=>$order_type, "order_price"=>$price, "order_ip"=>$logged_ip, "order_name"=>$subject, "sign"=>$sign, "redirect_url"=>constant("erphpdown")."payment/paypy/notify.php", "extension"=>"erphpdown");
$result = wp_remote_request($api, array("method" => "POST", "body"=>$body));
var_dump($result);exit;*/

$result = erphpdown_curl_post($api,"order_id=".$trade_order_id."&order_type=".$order_type."&order_price=".$price."&order_ip=".$logged_ip."&order_name=".$subject."&sign=".$sign."&redirect_url=".constant("erphpdown")."payment/paypy/notify.php"."&extension=erphpdown");
$result = trim($result, "\xEF\xBB\xBF");
$resultArray = json_decode($result,true);
if($resultArray['code'] != '1'){
	echo '获取支付失败：'.$resultArray['msg'];
}else{
?>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" /> 
    <title><?php echo ($order_type=='alipay')?'支付宝':'微信';?>支付</title>
    <link rel='stylesheet'  href='../static/erphpdown.css' type='text/css' media='all' />
</head>
<body<?php if(!isset($_GET['iframe'])){echo ' class="erphpdown-page-pay"';}?>>

	<div class="wppay-custom-modal-box mobantu-wppay erphpdown-custom-modal-box">
		<section class="wppay-modal">
            <section class="erphp-wppay-qrcode mobantu-wppay">
                <section class="tab">
                    <a href="javascript:;" class="active"><div class="payment"><img src="<?php echo constant("erphpdown");?>static/images/<?php echo ($order_type=='alipay')?'payment-alipay':'payment-weixin';?>.png"></div>￥<?php echo sprintf("%.2f",$resultArray['qr_price']);?></a>
                    <?php if($resultArray['qr_price']<$price) echo '<div class="discount">原价<del>￥'.sprintf("%.2f",$price).'</del>，随机立减￥'.($price - $resultArray['qr_price']).'</div><div class="warning">请务必支付金额￥'.$resultArray['qr_price'].'</div>';?>
                </section>
                <section class="tab-list" style="background-color: <?php echo ($order_type=='alipay')?'#00a3ee':'#21ab36';?>;">
                    <section class="item">
                        <section class="qr-code">
                            <img src="<?php echo constant("erphpdown").'includes/qrcode.php?data='.$resultArray['qr_url'];?>" class="img" alt="">
                        </section>
                        <p class="account">支付完成后请等待5秒左右，期间请勿刷新</p>
                        <p id="time" class="desc"></p>
                        <?php if(wp_is_mobile()){
                            if($order_type=='alipay'){
                                echo '<p class="wap"><a href="'.urldecode($resultArray['qr_url']).'" target="_blank">启动支付宝APP支付</a></p>';
                            }else{
                                echo '<p class="wap">请截屏后，打开微信扫一扫，从相册选择二维码图片</p>';
                            }
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

        var m = <?php echo $resultArray['qr_minute'];?>, s = 0;  
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
<?php
}