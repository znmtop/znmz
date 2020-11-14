<?php 
if ( !defined('ABSPATH') ) {exit;}
class EPD{

	private $ip;
	public $user_id;
	public $post_id;
	public $is_logged = 0;

	public function __construct($post_id = 0, $user_id = 0){

		$this->ip = erphpGetIP();
		$this->post_id = $post_id;
		$this->user_id = $user_id?$user_id:0;

		if(is_user_logged_in()){
			$this->is_logged = 1;
		}
	
	}

	public function doAff($money, $user_id){
		global $wpdb;
		$ref = get_option('ice_ali_money_ref');
		$ref2 = get_option('ice_ali_money_ref2');
		if($ref){
			$RefMoney=$wpdb->get_row("select father_id from ".$wpdb->users." where ID=".$user_id);
			if($RefMoney->father_id){
				self::addUserMoney($RefMoney->father_id, $money*$ref*0.01);
				if($ref2){
					$RefMoney2=$wpdb->get_row("select father_id from ".$wpdb->users." where ID=".$RefMoney->father_id);
					if($RefMoney2->father_id){
						self::addUserMoney($RefMoney2->father_id, $money*$ref2*0.01);
					}
				}
			}
		}
	}

	public static function addUserMoney($user_id, $money){
		if(!$user_id)
			return false;
		global $wpdb;
		$myinfo=$wpdb->get_row("select ice_id from ".$wpdb->iceinfo." where ice_user_id=".$user_id);
		if(!$myinfo){
			return $wpdb->query("insert into $wpdb->iceinfo(ice_have_money,ice_user_id,ice_get_money)values('$money','$user_id',0)");
		}else{
			return $wpdb->query("update $wpdb->iceinfo set ice_have_money=ice_have_money+".$money." where ice_user_id=".$user_id);
		}
	}

	public static function getPostHidden($post_id){
		if(!$post_id)
			return false;

		$hidden_content = get_post_meta($post_id,'hidden_content',true);
		return $hidden_content;
	}

	public static function getPostVipType($post_id){
		if(!$post_id)
			return false;

		$member_down = get_post_meta($post_id,'member_down',true);
		return $member_down;
	}

	public static function getUserVipType($user_id = null){
		if($user_id){
			$ice_user_id = $user_id;
		}else{
			if(!is_user_logged_in())
				return false;
			$ice_user_id = get_current_user_id();
		}

		global $wpdb;
		$userTypeInfo=$wpdb->get_row("select endTime, userType from ".$wpdb->iceinfo." where ice_user_id=".$ice_user_id);
		if($userTypeInfo){
			if(time() > strtotime($userTypeInfo->endTime) + 24*3600){
				$wpdb->query("update $wpdb->iceinfo set userType=0, endTime='1000-01-01' where ice_user_id=".$ice_user_id);
				return false;
			}
			return $userTypeInfo->userType;
		}
		return false;
	}

	public static function getUserMoney($user_id = null){
		if($user_id){
			$ice_user_id = $user_id;
		}else{
			if(!is_user_logged_in())
				return false;
			$ice_user_id = get_current_user_id();
		}

		global $wpdb;
		$userMoney=$wpdb->get_row("select * from ".$wpdb->iceinfo." where ice_user_id=".$ice_user_id);
		return $userMoney == false ? 0 : ($userMoney->ice_have_money - $userMoney->ice_get_money);
	}

	public function curl_post($url = '', $postData = ''){
		if(function_exists('curl_init')){
			$ch = curl_init();								//初始化curl
			curl_setopt($ch, CURLOPT_URL, $url);			//设置抓取的url	
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	//要求结果为字符串且输出到屏幕上
			curl_setopt($ch, CURLOPT_POST, true);			//设置post方式提交
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);//设置post数据
			curl_setopt($ch, CURLOPT_TIMEOUT, 30); 			//设置cURL允许执行的最长秒数
			//https请求 不验证证书和host
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			$data = curl_exec($ch);
			curl_close($ch);
			return $data;
		}else{
			wp_die("网站未开启curl组件，正常情况下该组件必须开启，请开启curl组件解决该问题");
		}
	}

	public function checkWppayPaid($order_num){
		global $wpdb, $wppay_table_name;
		$wppay_check = $wpdb->get_var($wpdb->prepare("SELECT id FROM $wppay_table_name
										WHERE	post_id = %d
										AND     order_status = 1
										AND		order_num = %s", $this->post_id, $order_num));
			
		$wppay_check = intval($wppay_check);
		return $wppay_check && $wppay_check > 0;
	}

	public function isWppayPaid(){
		global $wpdb, $wppay_table_name;

		if( isset($_COOKIE['wppay_'.$this->post_id]) ){
			$order_num = $this->getWppayKey($_COOKIE['wppay_'.$this->post_id]);
			$wppay_check = $wpdb->get_var($wpdb->prepare("SELECT id FROM $wppay_table_name
										WHERE	post_id = %d
										AND     order_status = 1
										AND		order_num = %s", $this->post_id, $order_num));
			$wppay_check = intval($wppay_check);
			return $wppay_check && $wppay_check > 0;
		}
		
		if($this->user_id){
			// user is logged in	
			$wppay_check = $wpdb->get_var($wpdb->prepare("SELECT id FROM $wppay_table_name
											WHERE   post_id = %d
											AND     order_status = 1
											AND		user_id = %d", $this->post_id, $this->user_id));
			if(!$wppay_check){
				if(get_option('erphp_wppay_ip')){
					$wppay_check = $wpdb->get_var($wpdb->prepare("SELECT id FROM $wppay_table_name
													WHERE	post_id = %d
													AND     order_status = 1
													AND		ip_address = %s
													AND		user_id = %d", $this->post_id, $this->ip, 0));
				}else{
					$wppay_check = 0;
				}

				if(!$wppay_check){
					$wppay_check=$wpdb->get_var("select ice_id from ".$wpdb->icealipay." where ice_post='".$this->post_id."' and ice_success=1 and (ice_index is null or ice_index = '') and ice_user_id=".$this->user_id." order by ice_time desc");
				}
			}
		} else{
			// user not logged in, check by ip address
			if(get_option('erphp_wppay_ip')){
				$wppay_check = $wpdb->get_var($wpdb->prepare("SELECT id FROM $wppay_table_name
												WHERE	post_id = %d
												AND     order_status = 1
												AND		ip_address = %s
												AND		user_id = %d", $this->post_id, $this->ip, 0));
			}else{
				$wppay_check = 0;
			}
		}

		$wppay_check = intval($wppay_check);

		return $wppay_check && $wppay_check > 0;
	}

	public function addWppay($order_num,$post_price){
		global $wpdb, $wppay_table_name;
		date_default_timezone_set('Asia/Shanghai');
		$result = $wpdb->insert($wppay_table_name, array(
			'order_num' => $order_num,
			'post_id' => $this->post_id,
			'post_price' => $post_price,
			'user_id' => $this->user_id,
			'order_time' => date("Y-m-d H:i:s"),
			'ip_address' => $this->ip), array('%s', '%d', '%s', '%d', '%s', '%s'));

		if($result){
	    	return true;
	    }
	    return false;
	}

	public function paypyQr($out_trade_no,$price){
		$secretkey = get_option('erphpdown_paypy_key');
		$api = get_option('erphpdown_paypy_api').'api/order/';
		$sign = md5(md5($out_trade_no.$price).$secretkey);
		$subject = get_bloginfo('name').'充值订单';
		$wx_qr_url = ''; $ali_qr_url = ''; $status = 0;$minute = 0;
		$logged_ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? esc_sql($_SERVER['HTTP_X_FORWARDED_FOR']) : esc_sql($_SERVER['REMOTE_ADDR']);
		$wx_result = $this->curl_post($api,"order_id=".$out_trade_no."&order_type=wechat&order_price=".$price."&order_ip=".$logged_ip."&order_name=".$subject."&sign=".$sign."&redirect_url=".ERPHPDOWN_URL."/payment/paypy/notify.php"."&extension=erphpdown");
		$wxResultArray = json_decode($wx_result,true);
		$ali_result = $this->curl_post($api,"order_id=".$out_trade_no."&order_type=alipay&order_price=".$price."&order_ip=".$logged_ip."&order_name=".$subject."&sign=".$sign."&redirect_url=".ERPHPDOWN_URL."/payment/paypy/notify.php"."&extension=erphpdown");
		$aliResultArray = json_decode($ali_result,true);
		if($wxResultArray['code'] == '1'){
			$wx_qr_url = $wxResultArray['qr_url'];
			$wx_qr_price = $wxResultArray['qr_price'];
			$status = 1;
			$minute = $wxResultArray['qr_minute'];
		}
		if($aliResultArray['code'] == '1'){
			$ali_qr_url = $aliResultArray['qr_url'];
			$ali_qr_price = $aliResultArray['qr_price'];
			$status = 1;
			$minute = $aliResultArray['qr_minute'];
		}
		return array("status"=>$status,"minute"=>$minute,"wx_url"=>$wx_qr_url,"ali_url"=>$ali_qr_url,"wx_qr_price"=>$wx_qr_price,"ali_qr_price"=>$ali_qr_price);
	}

	public function hupiAliWppayQr($out_trade_no,$price){
		require_once ERPHPDOWN_PATH."/payment/xhpay/api3.php";

	    $appid              = get_option('erphpdown_xhpay_appid32');
		$appsecret          = get_option('erphpdown_xhpay_appsecret32');
		$url                = get_option('erphpdown_xhpay_api32')?get_option('erphpdown_xhpay_api32'):"https://api.xunhupay.com/payment/do.html";
		$notify = constant("erphpdown").'payment/xhpay/notify32.php'; 
		$payment = 'alipay';
	    $subject = 'order['.get_the_author_meta( 'user_login', wp_get_current_user()->ID ).']';

		$mob = 'N';
		if(wp_is_mobile()){
		    $mob = 'Y';
		}

		$data=array(
		    'version'   => '1.1',//固定值，api 版本，目前暂时是1.1
		    'lang'       => 'zh-cn', //必须的，zh-cn或en-us 或其他，根据语言显示页面
		    'plugins'   => 'erphpdown-xhpay3',//必须的，根据自己需要自定义插件ID，唯一的，匹配[a-zA-Z\d\-_]+ 
		    'appid'     => $appid, //必须的，APPID
		    'trade_order_id'=> $out_trade_no, //必须的，网站订单ID，唯一的，匹配[a-zA-Z\d\-_]+ 
		    'payment'   => $payment,//必须的，支付接口标识：wechat(微信接口)|alipay(支付宝接口)
		    'is_app'    => $mob, //必须的，Y|N 是否是移动端
		    'total_fee' => $price,//人民币，单位精确到分(测试账户只支持0.1元内付款)
		    'title'     => $subject, //必须的，订单标题，长度32或以内
		    'description'=> '',//可选，订单描述，长度5000或以内
		    'time'      => time(),//必须的，当前时间戳，根据此字段判断订单请求是否已超时，防止第三方攻击服务器
		    'notify_url'=>  $notify, //必须的，支付成功异步回调接口
		    'return_url'=> get_option('erphp_url_front_success'),//必须的，支付成功后的跳转地址
		    'callback_url'=>get_option('erphp_url_front_success'),//必须的，支付发起地址（未支付或支付失败，系统会会跳到这个地址让用户修改支付信息）
		    'nonce_str' => str_shuffle(time())//必须的，随机字符串，作用：1.避免服务器缓存，2.防止安全密钥被猜测出来
		);

		$hashkey =$appsecret;
		$data['hash']     = XH_Payment_Api::generate_xh_hash($data,$hashkey);

		try {
		    $response     = XH_Payment_Api::http_post($url, json_encode($data));
		    $result       = $response?json_decode($response,true):null;
		    if(!$result){
		        throw new Exception('Internal server error',500);
		    }
		     
		    $hash         = XH_Payment_Api::generate_xh_hash($result,$hashkey);
		    if(!isset( $result['hash'])|| $hash!=$result['hash']){
		        throw new Exception(__('Invalid sign!',XH_Wechat_Payment),40029);
		    }

		    if($result['errcode']!=0){
		        throw new Exception($result['errmsg'],$result['errcode']);
		    }

		    return $result;
		} catch (Exception $e) {
		    //echo "errcode:{$e->getCode()},errmsg:{$e->getMessage()}";
		    //TODO:处理支付调用异常的情况
		}
	}

	public function hupiWxWppayQr($out_trade_no,$price){
		require_once ERPHPDOWN_PATH."/payment/xhpay/api3.php";

	    $appid              = get_option('erphpdown_xhpay_appid31');
	    $appsecret          = get_option('erphpdown_xhpay_appsecret31'); 
	    $url              = get_option('erphpdown_xhpay_api31')?get_option('erphpdown_xhpay_api31'):"https://api.xunhupay.com/payment/do.html";
	    $notify = constant("erphpdown").'payment/xhpay/notify31.php';
	    $payment = 'wechat';
	    $subject = 'order['.get_the_author_meta( 'user_login', wp_get_current_user()->ID ).']';

		$mob = 'N';
		if(wp_is_mobile()){
		    $mob = 'Y';
		}

		$data=array(
		    'version'   => '1.1',//固定值，api 版本，目前暂时是1.1
		    'lang'       => 'zh-cn', //必须的，zh-cn或en-us 或其他，根据语言显示页面
		    'plugins'   => 'erphpdown-xhpay3',//必须的，根据自己需要自定义插件ID，唯一的，匹配[a-zA-Z\d\-_]+ 
		    'appid'     => $appid, //必须的，APPID
		    'trade_order_id'=> $out_trade_no, //必须的，网站订单ID，唯一的，匹配[a-zA-Z\d\-_]+ 
		    'payment'   => $payment,//必须的，支付接口标识：wechat(微信接口)|alipay(支付宝接口)
		    'is_app'    => $mob, //必须的，Y|N 是否是移动端
		    'total_fee' => $price,//人民币，单位精确到分(测试账户只支持0.1元内付款)
		    'title'     => $subject, //必须的，订单标题，长度32或以内
		    'description'=> '',//可选，订单描述，长度5000或以内
		    'time'      => time(),//必须的，当前时间戳，根据此字段判断订单请求是否已超时，防止第三方攻击服务器
		    'notify_url'=>  $notify, //必须的，支付成功异步回调接口
		    'return_url'=> get_option('erphp_url_front_success'),//必须的，支付成功后的跳转地址
		    'callback_url'=>get_option('erphp_url_front_success'),//必须的，支付发起地址（未支付或支付失败，系统会会跳到这个地址让用户修改支付信息）
		    'nonce_str' => str_shuffle(time())//必须的，随机字符串，作用：1.避免服务器缓存，2.防止安全密钥被猜测出来
		);

		$hashkey =$appsecret;
		$data['hash']     = XH_Payment_Api::generate_xh_hash($data,$hashkey);

		try {
		    $response     = XH_Payment_Api::http_post($url, json_encode($data));
		    $result       = $response?json_decode($response,true):null;
		    if(!$result){
		        throw new Exception('Internal server error',500);
		    }
		     
		    $hash         = XH_Payment_Api::generate_xh_hash($result,$hashkey);
		    if(!isset( $result['hash'])|| $hash!=$result['hash']){
		        throw new Exception(__('Invalid sign!',XH_Wechat_Payment),40029);
		    }

		    if($result['errcode']!=0){
		        throw new Exception($result['errmsg'],$result['errcode']);
		    }

		    return $result;
		} catch (Exception $e) {
		    //echo "errcode:{$e->getCode()},errmsg:{$e->getMessage()}";
		    //TODO:处理支付调用异常的情况
		}
	}

	public function weixinWppayQr($out_trade_no,$price){
		require_once ERPHPDOWN_PATH."/payment/weixin/lib/WxPay.Api.php";
		require_once ERPHPDOWN_PATH."/payment/weixin/lib/WxPay.NativePay.php";

		$subject = get_bloginfo('name').'充值订单';

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
		//$url2 = $result["code_url"];
		return $result;
	}

	public function f2fpayWppayQr($out_trade_no,$price){
		require_once ERPHPDOWN_PATH.'/payment/f2fpay/f2fpay/model/builder/AlipayTradePrecreateContentBuilder.php';
		require_once ERPHPDOWN_PATH.'/payment/f2fpay/f2fpay/service/AlipayTradeService.php';
		$outTradeNo = $out_trade_no;
		$totalAmount = $price;
		$subject = get_bloginfo('name').'充值订单';
		$body = $subject;
		$operatorId = "erphpdown";

		$providerId = ""; //系统商pid,作为系统商返佣数据提取的依据
		$extendParams = new ExtendParams();
		$extendParams->setSysServiceProviderId($providerId);
		$extendParamsArr = $extendParams->getExtendParams();

		$timeExpress = "5m";
		$goodsDetailList = array();

		$goods1 = new GoodsDetail();
		$goods1->setGoodsId($out_trade_no);
		$goods1->setGoodsName($subject);
		$goods1->setPrice($price*100);
		$goods1->setQuantity(1);
		$goods1Arr = $goods1->getGoodsDetail();

		$goodsDetailList = array($goods1Arr);

		$appAuthToken = "";//根据真实值填写

		$qrPayRequestBuilder = new AlipayTradePrecreateContentBuilder();
		$qrPayRequestBuilder->setOutTradeNo($outTradeNo);
		$qrPayRequestBuilder->setTotalAmount($totalAmount);
		$qrPayRequestBuilder->setTimeExpress($timeExpress);
		$qrPayRequestBuilder->setSubject($subject);
		$qrPayRequestBuilder->setBody($body);
		$qrPayRequestBuilder->setUndiscountableAmount($undiscountableAmount);
		$qrPayRequestBuilder->setExtendParams($extendParamsArr);
		$qrPayRequestBuilder->setGoodsDetailList($goodsDetailList);
		$qrPayRequestBuilder->setStoreId($storeId);
		$qrPayRequestBuilder->setOperatorId($operatorId);
		$qrPayRequestBuilder->setAlipayStoreId($alipayStoreId);

		$qrPayRequestBuilder->setAppAuthToken($appAuthToken);

		$qrPay = new AlipayTradeService($config);
		$qrPayResult = $qrPay->qrPay($qrPayRequestBuilder);
		return $qrPayResult;
	}

	public function getWppayKey($key){
		return str_replace( md5(get_option('erphpdown_downkey')), '', base64_decode($key) );
	}

	public function setWppayKey($order_num){
		return base64_encode($order_num.md5(get_option('erphpdown_downkey')));
	}

	public static function send_request($body, $method='POST'){
	    $url = 'http://api.mobantu.com/auth/erphpdown.php';
	    $result = wp_remote_request($url, array('method' => $method, 'body'=>$body));
	    if(is_array($result)){
	        return $result['body'];
	    }
	}

}