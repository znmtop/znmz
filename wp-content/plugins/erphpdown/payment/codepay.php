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
$subject = 'order['.get_the_author_meta( 'user_login', wp_get_current_user()->ID ).']';  

if($price > 0){
    $user_Info   = wp_get_current_user();
    $sql="INSERT INTO $wpdb->icemoney (ice_money,ice_num,ice_user_id,ice_post_id,ice_post_index,ice_user_type,ice_time,ice_success,ice_note,ice_success_time,ice_alipay)
    VALUES ('$price','$trade_order_id','".$user_Info->ID."','".$post_id."','".$index."','".$user_type."','".date("Y-m-d H:i:s")."',0,'0','".date("Y-m-d H:i:s")."','')";
    $a=$wpdb->query($sql);
    if(!$a){
        wp_die('系统发生错误，请稍后重试!');
    }
}else{
    wp_die('请输入您要充值的金额');
}

$type=1;
if($_GET['type']) $type = $_GET['type'];


$codepay_id=get_option('erphpdown_codepay_appid');
$codepay_key=get_option('erphpdown_codepay_appsecret');

$return_url = get_option('erphp_url_front_success');
if(isset($_COOKIE['erphpdown_return']) && $_COOKIE['erphpdown_return']){
    $return_url = $_COOKIE['erphpdown_return'];
}

$data = array(
    "id" => $codepay_id,//你的码支付ID
    "pay_id" => $trade_order_id, //唯一标识 可以是用户ID,用户名,session_id(),订单ID,ip 付款后返回
    "type" => $type,//1支付宝支付 3微信支付 2QQ钱包
    "price" => $price,//金额100元
    "param" => "erphpdown",//自定义参数
    "notify_url"=>constant("erphpdown").'payment/codepay/notify.php',//通知地址
    "return_url"=>$return_url,//跳转地址
); //构造需要传递的参数

ksort($data); //重新排序$data数组
reset($data); //内部指针指向数组中的第一个元素

$sign = ''; //初始化需要签名的字符为空
$urls = ''; //初始化URL参数为空

foreach ($data AS $key => $val) { //遍历需要传递的参数
    if ($val == ''||$key == 'sign') continue; //跳过这些不参数签名
    if ($sign != '') { //后面追加&拼接URL
        $sign .= "&";
        $urls .= "&";
    }
    $sign .= "$key=$val"; //拼接为url参数形式
    $urls .= "$key=" . urlencode($val); //拼接为url参数形式并URL编码参数值

}
$query = $urls . '&sign=' . md5($sign .$codepay_key); //创建订单所需的参数
$url = "http://api2.fateqq.com:52888/creat_order/?{$query}"; //支付页面

header("Location:{$url}"); //跳转到支付页面