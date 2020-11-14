<?php 
/**
 * 支付成功异步回调接口
 *
 * 当用户支付成功后，支付平台会把订单支付信息异步请求到本接口(最多5次)
 * 
 * @date 2017年3月13日
 * @copyright 重庆迅虎网络有限公司
 */
require_once('../../../../../wp-load.php');
require_once 'api3.php';

/**
 * 回调数据
 * @var array
 *   array(
 *       'trade_order_id'，商户网站订单ID
         'total_fee',订单支付金额
         'transacton_id',//支付平台订单ID
         'order_date',//支付时间
         'plugins',//自定义插件ID,与支付请求时一致
          'status'=>'OD'//订单状态，OD已支付，WP未支付
 *   )
 */
$data = $_POST;
if(!isset($data['hash'])
  ||!isset($data['trade_order_id'])){
        return;
}
//自定义插件ID,请与支付请求时一致
if(isset($data['plugins'])&&$data['plugins']!='erphpdown-xhpay3'){
    return;
}

$erphpdown_xhpay_appsecret    = get_option('erphpdown_xhpay_appsecret31');

$hash =XH_Payment_Api::generate_xh_hash($data,$erphpdown_xhpay_appsecret);
if($data['hash']!=$hash){
    //签名验证失败
    return;
}

//商户订单ID
$trade_order_id =$data['trade_order_id'];

if($data['status']=='OD'){
/************商户业务处理******************/
//TODO:此处处理订单业务逻辑,支付平台会多次调用本接口(防止网络异常导致回调失败等情况)
//     请避免订单被二次更新而导致业务异常！！！
//     if(订单未处理){
//         处理订单....
//      }
    global $wpdb, $wppay_table_name;
    $total_fee=$data['total_fee'];

    if(strstr($trade_order_id,'wppay')){
      $order=$wpdb->get_row("select * from $wppay_table_name where order_num='".$trade_order_id."'");
      if($order){
        if(!$order->order_status){
          $wpdb->query("UPDATE $wppay_table_name SET order_status=1 WHERE order_num = '".$trade_order_id."'");

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
      epd_set_order_success($trade_order_id,$total_fee,'wxpay');
    }


//....
//...
/*************商户业务处理 END*****************/
}else if($data['status']=='WP'){
    //处理未支付的情况    
}

//以下是处理成功后输出，当支付平台接收到此消息后，将不再重复回调当前接口
echo 'success';
exit;
?>