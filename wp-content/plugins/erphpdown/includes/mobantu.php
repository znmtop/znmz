<?php
/*
mobantu.com
erphpdown.com
qq 82708210
*/
if ( !defined('ABSPATH') ) {exit;}

if(isset($_REQUEST['aff']) && !isset($_COOKIE["erphprefid"])){
	setcookie("erphprefid", $_REQUEST['aff'], time()+2592000, '/');
}

function erphpdown_style() {
	global $erphpdown_version;
	if(is_singular()){
		wp_enqueue_style( 'erphpdown', constant("erphpdown")."static/erphpdown.css", array(), $erphpdown_version,'screen' );
		wp_enqueue_script('jquery');
		wp_enqueue_script( 'erphpdown', constant("erphpdown")."static/erphpdown.js", false, $erphpdown_version, true);
		wp_localize_script( 'erphpdown', 'erphpdown_ajax_url', admin_url("admin-ajax.php"));
	}
}
add_action('wp_enqueue_scripts', 'erphpdown_style',20,1);

add_action( 'wp_head', 'erphpdown_head_style' );
function erphpdown_head_style(){
?>
	<style id="erphpdown-custom"><?php echo get_option('erphp_custom_css');?></style>
	<script>window._ERPHPDOWN = {"uri":"<?php echo ERPHPDOWN_URL;?>", "payment": "<?php if(get_option('erphp_wppay_payment') == 'f2fpay') echo "1";elseif(get_option('erphp_wppay_payment') == 'f2fpay_weixin') echo "4";elseif(get_option('erphp_wppay_payment') == 'f2fpay_hupiv3') echo "4";elseif(get_option('erphp_wppay_payment') == 'weixin') echo "3";elseif(get_option('erphp_wppay_payment') == 'paypy') echo "6";elseif(get_option('erphp_wppay_payment') == 'hupiv3') echo "5"; else echo "2";?>", "author": "mobantu"}</script>
<?php 
}

add_action('user_register', 'erphp_register_extra_fields');
function erphp_register_extra_fields($user_id, $password="", $meta=array()) {
	global $wpdb;
	$hasRe = $wpdb->get_row("select ID,father_id from $wpdb->users where reg_ip = '".erphpGetIP()."'");
	if($hasRe->ID){
		$sql = "update $wpdb->users set reg_ip = '".erphpGetIP()."' where ID=".$user_id;
		$wpdb->query($sql);

		if(!$hasRe->father_id){
			if(isset($_COOKIE["erphprefid"]) && is_numeric($_COOKIE["erphprefid"])){
				$sql = "update $wpdb->users set father_id='".esc_sql($_COOKIE["erphprefid"])."',reg_ip = '".erphpGetIP()."' where ID=".$user_id;
				$wpdb->query($sql);
				addUserMoney($_COOKIE["erphprefid"], get_option('ice_ali_money_reg'));
			}
		}
	}else{
		$ice_ali_money_new = get_option('ice_ali_money_new');
		if($ice_ali_money_new){
			addUserMoney($user_id,$ice_ali_money_new);
		}

		if(isset($_COOKIE["erphprefid"]) && is_numeric($_COOKIE["erphprefid"])){
			$sql = "update $wpdb->users set father_id='".esc_sql($_COOKIE["erphprefid"])."',reg_ip = '".erphpGetIP()."' where ID=".$user_id;
			$wpdb->query($sql);
			addUserMoney($_COOKIE["erphprefid"],get_option('ice_ali_money_reg'));
		}
	}
}

add_action("init","erphp_noadmin_redirect");
function erphp_noadmin_redirect(){
	global $wpdb;
	if ( is_admin() && ( !defined( 'DOING_AJAX' ) || !DOING_AJAX ) && get_option('erphp_url_front_noadmin')=='yes') {
	  	$current_user = wp_get_current_user();
	  	if($current_user->roles[0] == get_option('default_role')) {
			$userpage = get_bloginfo('url');
			if(get_option('erphp_url_front_userpage')){
				$userpage = get_option('erphp_url_front_userpage');
			}
			wp_redirect( $userpage );
	  	}
	}
}

function addDownLog($uid,$pid,$ip){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	$sql="insert into $wpdb->down(ice_user_id,ice_post_id,ice_ip,ice_time)values('".$uid."','".$pid."','".$ip."','".date("Y-m-d H:i:s")."')";
	$wpdb->query($sql);
}

function checkDownLog($uid,$pid,$times,$ip){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	$result = $wpdb->get_var("select count(distinct ice_post_id) from $wpdb->down where ice_user_id=".$uid." and DATEDIFF(ice_time,NOW())=0");
	if($result > $times) 
		return false;
	elseif($result == $times){
		$exist = $wpdb->get_var("select ice_id from $wpdb->down where ice_user_id=".$uid." and DATEDIFF(ice_time,NOW())=0 and ice_post_id = $pid");
		if($exist) 
			return true;
		else 
			return false;
	}
	else 
		return true;
	
}

function getSeeCount($uid){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	$result = $wpdb->get_var("select count(distinct ice_post_id) from $wpdb->down where ice_user_id=".$uid." and DATEDIFF(ice_time,NOW())=0");
	return $result;
}

function checkSeeLog($uid,$pid,$times,$ip){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	$result = $wpdb->get_var("select count(distinct ice_post_id) from $wpdb->down where ice_user_id=".$uid." and DATEDIFF(ice_time,NOW())=0");
	if($result >= $times) 
		return false;
	else 
		return true;
}

function checkDownHas($uid,$pid){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	$exist = $wpdb->get_var("select ice_id from $wpdb->down where ice_user_id=".$uid." and DATEDIFF(ice_time,NOW())=0 and ice_post_id = $pid");
	if($exist) 
		return true;
	else 
		return false;
}

function addVipLog($price,$userType){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	$user_info = wp_get_current_user();
	$sql="insert into $wpdb->vip(ice_price,ice_user_id,ice_user_type,ice_time)values('".$price."','".$user_info->ID."','".$userType."','".date("Y-m-d H:i:s")."')";
	$wpdb->query($sql);
}

function addVipLogByAdmin($price,$userType,$uid){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	$sql="insert into $wpdb->vip(ice_price,ice_user_id,ice_user_type,ice_time)values('".$price."','".$uid."','".$userType."','".date("Y-m-d H:i:s")."')";
	$wpdb->query($sql);
}

function addAffLog($price,$uid,$ip){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	$sql="insert into $wpdb->aff(ice_price,ice_user_id,ice_ip,ice_time)values('".$price."','".$uid."','".$ip."','".date("Y-m-d H:i:s")."')";
	$wpdb->query($sql);
	addUserMoney($uid,$price);
}

function checkAffLog($uid,$ip){
	global $wpdb;
	$result = $wpdb->get_var("select ice_id from $wpdb->aff where ice_user_id=".$uid." and ice_ip='".$ip."'");
	if($result) return false;
	else return true;
}


function getUsreMemberType(){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	$user_info = wp_get_current_user();
	$userTypeInfo=$wpdb->get_row("select * from  ".$wpdb->iceinfo." where ice_user_id=".$user_info->ID);
	if($userTypeInfo)
	{
		if(time() > strtotime($userTypeInfo->endTime) +24*3600)
		{
			$wpdb->query("update $wpdb->iceinfo set userType=0,endTime='1000-01-01' where ice_user_id=".$user_info->ID);
			return 0;
		}
		return $userTypeInfo->userType;
	}
	return FALSE;
}

function getUsreMemberTypeById($uid){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	$userTypeInfo=$wpdb->get_row("select * from  ".$wpdb->iceinfo." where ice_user_id=".$uid);
	if($userTypeInfo)
	{
		if(time() > strtotime($userTypeInfo->endTime) +24*3600)
		{
			$wpdb->query("update $wpdb->iceinfo set userType=0,endTime='1000-01-01' where ice_user_id=".$uid);
			return 0;
		}
		return $userTypeInfo->userType;
	}
	return FALSE;
}

function getUsreMemberTypeEndTime(){
	global $wpdb;
	$user_info = wp_get_current_user();
	$userTypeInfo=$wpdb->get_row("select * from  ".$wpdb->iceinfo." where ice_user_id=".$user_info->ID);
	if($userTypeInfo)
	{
		return $userTypeInfo->endTime;
	}
	return FALSE;
}

function getUsreMemberTypeEndTimeById($uid){
	global $wpdb;
	$userTypeInfo=$wpdb->get_row("select * from  ".$wpdb->iceinfo." where ice_user_id=".$uid);
	if($userTypeInfo)
	{
		return $userTypeInfo->endTime;
	}
	return FALSE;
}

function versioncheck(){
	$url='http://api.mobantu.com/erphpdown/update.php';  
	$result=file_get_contents($url);  
	return $result;
}

function plugin_check_card(){
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if(!is_plugin_active( 'erphpdown-add-on-card/erphpdown-add-on-card.php' )){
		return 0;
	}
	else{
		return 1;
	}
}

function plugin_check_cred(){
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if(!is_plugin_active( 'erphpdown-add-on-mycred/erphpdown-add-on-mycred.php' )){
		return 0;
	}
	else{
		return 1;
	}
}

function plugin_check_video(){
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if(!is_plugin_active( 'erphpdown-addon-video/erphpdown-addon-video.php' )){
		return 0;
	}
	else{
		return 1;
	}
}

function plugin_check_activation(){
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if(!is_plugin_active( 'erphpdown-add-on-activation/erphpdown-add-on-activation.php' )){
		return 0;
	}
	else{
		return 1;
	}
}


function userPayMemberSetData($userType){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	$user_info = wp_get_current_user();
	$oldUserType = getUsreMemberType();
	if($oldUserType){
		$oldEndTime = getUsreMemberTypeEndTime();
		if($userType==6)
		{
			$endTime=date("Y-m-d",strtotime("+1 day",strtotime($oldEndTime)));
		}
		elseif($userType==7)
		{
			$endTime=date("Y-m-d",strtotime("+1 month",strtotime($oldEndTime)));
		}
		elseif ($userType==8)
		{
			$endTime=date("Y-m-d",strtotime("+3 month",strtotime($oldEndTime)));
		}
		elseif ($userType==9)
		{
			$endTime=date("Y-m-d",strtotime("+1 year",strtotime($oldEndTime)));
		}
		elseif ($userType==10)
		{
			$endTime=date("Y-m-d",strtotime("2038-01-01"));
		}
	}else{
		$endTime=date("Y-m-d");
		if($userType==6)
		{
			$endTime=date("Y-m-d",strtotime("+1 day"));
		}
		elseif($userType==7)
		{
			$endTime=date("Y-m-d",strtotime("+1 month"));
		}
		elseif ($userType==8)
		{
			$endTime=date("Y-m-d",strtotime("+3 month"));
		}
		elseif ($userType==9)
		{
			$endTime=date("Y-m-d",strtotime("+1 year"));
		}
		elseif ($userType==10)
		{
			$endTime=date("Y-m-d",strtotime("2038-01-01"));
		}
	}

	if($oldUserType){
		if($oldUserType > $userType){
			$userType = $oldUserType;
		}
	}

	$sql="update ".$wpdb->iceinfo." set userType=".$userType.", endTime='".$endTime."' where ice_user_id=".$user_info->ID;

	$wpdb->query($sql);
	return true;

}

function userSetMemberSetData($userType,$uid)
{
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	$oldUserType = getUsreMemberTypeById($uid);
	if($oldUserType){
		$oldEndTime = getUsreMemberTypeEndTimeById($uid);
		if($userType==6)
		{
			$endTime=date("Y-m-d",strtotime("+1 day",strtotime($oldEndTime)));
		}
		elseif($userType==7)
		{
			$endTime=date("Y-m-d",strtotime("+1 month",strtotime($oldEndTime)));
		}
		elseif ($userType==8)
		{
			$endTime=date("Y-m-d",strtotime("+3 month",strtotime($oldEndTime)));
		}
		elseif ($userType==9)
		{
			$endTime=date("Y-m-d",strtotime("+1 year",strtotime($oldEndTime)));
		}
		elseif ($userType==10)
		{
			$endTime=date("Y-m-d",strtotime("2038-01-01"));
		}
	}else{
		$endTime=date("Y-m-d");
		if($userType==6)
		{
			$endTime=date("Y-m-d",strtotime("+1 day"));
		}
		elseif($userType==7)
		{
			$endTime=date("Y-m-d",strtotime("+1 month"));
		}
		elseif ($userType==8)
		{
			$endTime=date("Y-m-d",strtotime("+3 month"));
		}
		elseif ($userType==9)
		{
			$endTime=date("Y-m-d",strtotime("+1 year"));
		}
		elseif ($userType==10)
		{
			$endTime=date("Y-m-d",strtotime("2038-01-01"));
		}
	}

	if($oldUserType){
		if($oldUserType > $userType){
			$userType = $oldUserType;
		}
	}

	$sql="update ".$wpdb->iceinfo." set userType=".$userType.",endTime='".$endTime."' where ice_user_id=".$uid;
	$wpdb->query($sql);
	return true;
}

function erphp_admin_pagenavi($total_count, $number_per_page=15){

	$current_page = isset($_GET['paged'])?$_GET['paged']:1;

	if(isset($_GET['paged'])){
		unset($_GET['paged']);
	}

	$base_url = add_query_arg($_GET,admin_url('admin.php'));

	$total_pages	= ceil($total_count/$number_per_page);

	$first_page_url	= $base_url.'&amp;paged=1';
	$last_page_url	= $base_url.'&amp;paged='.$total_pages;
	
	if($current_page > 1 && $current_page < $total_pages){
		$prev_page		= $current_page-1;
		$prev_page_url	= $base_url.'&amp;paged='.$prev_page;

		$next_page		= $current_page+1;
		$next_page_url	= $base_url.'&amp;paged='.$next_page;
	}elseif($current_page == 1){
		$prev_page_url	= '#';
		$first_page_url	= '#';
		if($total_pages > 1){
			$next_page		= $current_page+1;
			$next_page_url	= $base_url.'&amp;paged='.$next_page;
		}else{
			$next_page_url	= '#';
		}
	}elseif($current_page == $total_pages){
		$prev_page		= $current_page-1;
		$prev_page_url	= $base_url.'&amp;paged='.$prev_page;
		$next_page_url	= '#';
		$last_page_url	= '#';
	}
	?>
	<div class="tablenav bottom">
		<div class="tablenav-pages">
			<span class="displaying-num">每页 <?php echo $number_per_page;?> 共 <?php echo $total_count;?></span>
			<span class="pagination-links">
				<a class="first-page button <?php if($current_page==1) echo 'disabled'; ?>" title="前往第一页" href="<?php echo $first_page_url;?>">«</a>
				<a class="prev-page button <?php if($current_page==1) echo 'disabled'; ?>" title="前往上一页" href="<?php echo $prev_page_url;?>">‹</a>
				<span class="paging-input">第 <?php echo $current_page;?> 页，共 <span class="total-pages"><?php echo $total_pages; ?></span> 页</span>
				<a class="next-page button <?php if($current_page==$total_pages) echo 'disabled'; ?>" title="前往下一页" href="<?php echo $next_page_url;?>">›</a>
				<a class="last-page button <?php if($current_page==$total_pages) echo 'disabled'; ?>" title="前往最后一页" href="<?php echo $last_page_url;?>">»</a>
			</span>
		</div>
		<br class="clear">
	</div>
	<?php
}

add_filter('admin_footer_text', 'erphp_left_admin_footer_text'); 
function erphp_left_admin_footer_text($text) {
	$text = '<span id="footer-thankyou">感谢使用<a href=http://cn.wordpress.org/ >WordPress</a>进行创作，使用<a href="http://www.erphpdown.com">Erphpdown</a>进行网站VIP支付下载功能。</span>'; 
	return $text;
}

function epd_set_order_success($order_num,$total_fee,$pay_method=''){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	$money_info=$wpdb->get_row("select * from ".$wpdb->icemoney." where ice_num='".$wpdb->escape($order_num)."'");
	if($money_info){
		if(!$money_info->ice_success){
			if($pay_method == 'paypy'){
				$total_fee = $money_info->ice_money;
			}

			if(!$money_info->ice_post_id && !$money_info->ice_user_type){
				$epd_game_price  = get_option('epd_game_price');
		        if($epd_game_price){
		          	$cnt = count($epd_game_price['buy']);
		          	for($i=0; $i<$cnt;$i++){
			            if($total_fee == $epd_game_price['buy'][$i]){
			              	$total_fee = $epd_game_price['get'][$i];
			              	break;
			            }
		          	}
		        }
		    }

			$updatOrder=$wpdb->query("update $wpdb->icemoney set ice_success=1, ice_money = '".$total_fee*get_option('ice_proportion_alipay')."', ice_alipay = '".$pay_method."', ice_success_time = '".date("Y-m-d H:i:s")."' where ice_num='".$wpdb->escape($order_num)."'");
			if($updatOrder){
				addUserMoney($money_info->ice_user_id,$total_fee*get_option('ice_proportion_alipay'));
			}

			if($money_info->ice_post_id){
				$okMoney=erphpGetUserOkMoneyById($money_info->ice_user_id);
                $postid = $money_info->ice_post_id;
                $index = '';$index_name = '';
                $price = $total_fee*get_option('ice_proportion_alipay');
                if($okMoney >= $price){
                    if(erphpSetUserMoneyXiaoFeiByUid($price,$money_info->ice_user_id))
                    {
                    	if($money_info->ice_post_index){
                    		$index = $money_info->ice_post_index;
                    		$urls = get_post_meta($postid, 'down_urls', true);
							if($urls){
								$cnt = count($urls['index']);
								if($cnt){
									for($i=0; $i<$cnt;$i++){
										if($urls['index'][$i] == $index){
					    					$index_name = $urls['name'][$i];
					    					break;
					    				}
									}
								}
							}
                    	}

                        $subject   = get_post($postid)->post_title;
                        if($index_name){
							$subject .= ' - '.$index_name;
						}
                        $postUserId=get_post($postid)->post_author;
                        
                        $result=erphpAddDownloadByUid($subject, $postid, $money_info->ice_user_id,$price,1, '', $postUserId, $index);
                        if($result)
                        {
                        	$down_activation = get_post_meta($postid, 'down_activation', true);
                        	if($down_activation && function_exists('doErphpAct')){
								$activation_num = doErphpAct($money_info->ice_user_id,$postid);
								$wpdb->query("update $wpdb->icealipay set ice_data = '".$activation_num."' where ice_url='".$result."'");
								$cuser = get_user_by('id',$money_info->ice_user_id);
								if($cuser && $cuser->user_email){
									wp_mail($cuser->user_email, '【'.$subject.'】注册码', '您购买的资源【'.$subject.'】注册码：'.$activation_num);
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
							$EPD->doAff($price, $money_info->ice_user_id);
                        } 
                    }
                }
			}elseif($money_info->ice_user_type){
				addUserMoney($money_info->ice_user_id, '-'.$total_fee*get_option('ice_proportion_alipay'));
				userSetMemberSetData($money_info->ice_user_type,$money_info->ice_user_id);
				addVipLogByAdmin($total_fee*get_option('ice_proportion_alipay'), $money_info->ice_user_type, $money_info->ice_user_id);
				$EPD = new EPD();
				$EPD->doAff($total_fee*get_option('ice_proportion_alipay'), $money_info->ice_user_id);	
			}
		}
	}
}

function erphpCheckAlipayReturnNum($orderNum,$money)
{
	global $wpdb;
	$row=$wpdb->get_row("select * from ".$wpdb->icemoney." where ice_num='".$orderNum."'");
	if($row)
	{
		if($row->ice_money == $money)
		{
			return true;
		}
	}
	return false;
}
function erphpAddDownloadByUid($subject,$postid,$userid,$price,$success,$data,$postUserId,$index='')
{
	date_default_timezone_set('Asia/Shanghai');
	if($price > 0){
		global $wpdb;
		$subject = str_replace("'","",$subject);
		$subject = str_replace("‘","",$subject);
		$url       = md5(date("YmdHis").$postid.mt_rand(1000000, 9999999));
		$orderNum  = date("d").mt_rand(10000, 99999).mt_rand(10,99);
		$sql       = "INSERT INTO $wpdb->icealipay (ice_num,ice_title,ice_post,ice_price,ice_success,ice_url,ice_user_id,ice_time,ice_data,ice_index,
		ice_author)VALUES ('$orderNum','$subject','$postid','$price','$success','$url','".$userid."','".date("Y-m-d H:i:s")."','".$data."','".$index."','$postUserId')";
		if($wpdb->query($sql))
		{
			return $url;
		}
	}
	return false;
}
function erphpAddDownload($subject,$postid,$price,$success,$data,$postUserId,$index='')
{
	date_default_timezone_set('Asia/Shanghai');
	if($price > 0){
		global $wpdb;
		$subject = str_replace("'","",$subject);
		$subject = str_replace("‘","",$subject);
		$user_info = wp_get_current_user();
		$url       = md5(date("YmdHis").$postid.mt_rand(1000000, 9999999));
		$orderNum  = date("d").mt_rand(10000, 99999).mt_rand(10,99);
		$sql       = "INSERT INTO $wpdb->icealipay (ice_num,ice_title,ice_post,ice_price,ice_success,ice_url,ice_user_id,ice_time,ice_data,ice_index,
		ice_author)VALUES ('$orderNum','$subject','$postid','$price','$success','$url','".$user_info->ID."','".date("Y-m-d H:i:s")."','".$data."','".$index."','$postUserId')";
		if($wpdb->query($sql))
		{
			return $url;
		}
	}
	return false;
}
function erphpAddDownloadIndex($postid,$price,$index)
{
	date_default_timezone_set('Asia/Shanghai');
	if($price > 0){
		global $wpdb;
		$user_info = wp_get_current_user();
		$url       = md5(date("YmdHis").$postid.mt_rand(1000000, 9999999));
		$orderNum  = date("YmdHis").mt_rand(10000, 99999);
		$sql       = "INSERT INTO $wpdb->iceindex (ice_num,ice_post,ice_price,ice_url,ice_user_id,ice_time,ice_index)VALUES ('$orderNum','$postid','$price','$url','".$user_info->ID."','".date("Y-m-d H:i:s")."','".$index."')";
		if($wpdb->query($sql))
		{
			return $url;
		}
	}
	return false;
}
function erphpSetUserMoneyXiaoFei($num)
{
	if($num > 0){
		global $wpdb;
		$user_info=wp_get_current_user();
		return $wpdb->query("update $wpdb->iceinfo set ice_get_money=ice_get_money+".$num." where ice_user_id=".$user_info->ID);
	}else{
		return false;
	}
}
function erphpSetUserMoneyXiaoFeiByUid($num,$uid)
{
	if($num > 0){
		global $wpdb;
		return $wpdb->query("update $wpdb->iceinfo set ice_get_money=ice_get_money+".$num." where ice_user_id=".$uid);
	}else{
		return false;
	}
}
function erphpGetUserAllXiaofei($uid){
	global $wpdb;
	$money = $wpdb->get_var("SELECT SUM(ice_price) FROM $wpdb->icealipay WHERE ice_success>0 and ice_user_id=".$uid);
	$money2 = $wpdb->get_var("SELECT sum(ice_price) FROM $wpdb->vip where ice_user_id=".$uid);
	$money += $money2;
	return $money ? $money :'0';
}
function erphpGetUserOkMoney()
{
	global $wpdb;
	$user_info=wp_get_current_user();
	if($user_info)
	{
		$userMoney=$wpdb->get_row("select * from ".$wpdb->iceinfo." where ice_user_id=".$user_info->ID);
		return $userMoney==false ?0:($userMoney->ice_have_money - $userMoney->ice_get_money);
	}
	return 0;
}
function erphpGetUserOkMoneyById($uid)
{
	global $wpdb;
	if($uid){
		$userMoney=$wpdb->get_row("select * from ".$wpdb->iceinfo." where ice_user_id=".$uid);
		return $userMoney==false ?0:($userMoney->ice_have_money - $userMoney->ice_get_money);
	}
	return 0;
}
function getProductSales($pid){
	global $wpdb;
	$total_trade  = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->icealipay WHERE ice_success>0 and ice_post=".$pid);
	return $total_trade;
}

function getProductMember($pid){
	$type = get_post_meta($pid,"member_down",true);
	if($type == "1"){
		return "无";
	}elseif($type == "2"){
		return "VIP5折";
	}elseif($type == "3"){
		return "VIP免费";
	}elseif($type == "4"){
		return "VIP专享";
	}elseif($type == "5"){
		return "VIP八折";
	}elseif($type == "6"){
		return "年费VIP免费";
	}elseif($type == "7"){
		return "终身VIP免费";
	}elseif($type == "8"){
		return "年费VIP专享";
	}elseif($type == "9"){
		return "终身VIP专享";
	}else{
		return "未知";
	}
}

function getProductDownType($pid){
	$start_down = get_post_meta($pid,"start_down",true);
	$start_down2 = get_post_meta($pid,"start_down2",true);
	$start_see = get_post_meta($pid,"start_see",true);
	$start_see2 = get_post_meta($pid,"start_see2",true);
	if($start_down == "yes"){
		return "下载";
	}elseif($start_down2 == "yes"){
		return "免登录";
	}elseif($start_see == "yes"){
		return "查看";
	}elseif($start_see2 == "yes"){
		return "部分查看";
	}else{
		return "无";
	}
}

add_action('wp_dashboard_setup', 'erphp_modify_dashboard_widgets' );
function erphp_modify_dashboard_widgets() {
	global $wp_meta_boxes;
	add_meta_box( 'erphpdown_dashboard_widget', 'Erphpdown', 'erphpdown_dashboard_widget_function','dashboard', 'normal', 'core' );
}
function erphpdown_dashboard_widget_function() {
	global $wpdb, $wppay_table_name;
	if(current_user_can('administrator')){
		$today_chong_money = $wpdb->get_var("SELECT SUM(ice_money) FROM $wpdb->icemoney WHERE ice_success>0 and TO_DAYS(NOW())- TO_DAYS(ice_time) = 0");
		$today_order_money   = $wpdb->get_var("SELECT SUM(ice_price) FROM $wpdb->icealipay WHERE ice_success>0 and TO_DAYS(NOW())- TO_DAYS(ice_time) = 0");
		$today_order2_money   = $wpdb->get_var("SELECT SUM(post_price) FROM $wppay_table_name WHERE order_status=1 and TO_DAYS(NOW())- TO_DAYS(order_time) = 0");

		$yestoday_chong_money = $wpdb->get_var("SELECT SUM(ice_money) FROM $wpdb->icemoney WHERE ice_success>0 and TO_DAYS(NOW())- TO_DAYS(ice_time) = 1");
		$yestoday_order_money   = $wpdb->get_var("SELECT SUM(ice_price) FROM $wpdb->icealipay WHERE ice_success>0 and TO_DAYS(NOW())- TO_DAYS(ice_time) = 1");
		$yestoday_order2_money   = $wpdb->get_var("SELECT SUM(post_price) FROM $wppay_table_name WHERE order_status=1 and TO_DAYS(NOW())- TO_DAYS(order_time) = 1");

		echo '<div class="main"><ul>';
		echo '<li style="margin-bottom:10px"><span style="font-size:16px;">今日充值</span>：<a href="'.admin_url('admin.php?page=erphpdown/admin/erphp-chong-list.php').'">'.($today_chong_money?$today_chong_money:'0').' '.get_option('ice_name_alipay').'</a></li>';
		echo '<li style="margin-bottom:10px"><span style="font-size:16px;">今日销售</span>：<a href="'.admin_url('admin.php?page=erphpdown/admin/erphp-orders-list.php').'">'.($today_order_money?$today_order_money:'0').' '.get_option('ice_name_alipay').'</a>（登录），<a href="'.admin_url('admin.php?page=erphpdown/admin/erphp-wppays-list.php').'">'.($today_order2_money?$today_order2_money:'0').' 元</a>（免登录）</li>';
		echo '<li style="margin-bottom:10px"><span style="font-size:16px;">昨日充值</span>：<a href="'.admin_url('admin.php?page=erphpdown/admin/erphp-chong-list.php').'">'.($yestoday_chong_money?$yestoday_chong_money:'0').' '.get_option('ice_name_alipay').'</a></li>';
		echo '<li style="margin-bottom:10px"><span style="font-size:16px;">昨日销售</span>：<a href="'.admin_url('admin.php?page=erphpdown/admin/erphp-orders-list.php').'">'.($yestoday_order_money?$yestoday_order_money:'0').' '.get_option('ice_name_alipay').'</a>（登录），<a href="'.admin_url('admin.php?page=erphpdown/admin/erphp-wppays-list.php').'">'.($yestoday_order2_money?$yestoday_order2_money:'0').' 元</a>（免登录）</li>';
		echo '</ul><p style="opacity:.7;font-size:12px;">收入 = 充值额 + 免登录销售额</p></div><div style="border-top:1px solid #eee;padding: 8px 12px 4px;margin:0 -12px"><i class="dashicons dashicons-external"></i> 强烈推荐完美兼容插件的<a href="https://www.mobantu.com/7191.html" target="_blank">Modown</a>主题。</div>';
	}else{
		$user_info=wp_get_current_user();
		$userMoney=$wpdb->get_row("select * from ".$wpdb->iceinfo." where ice_user_id=".$user_info->ID);
		if(!$userMoney){
			$okMoney=0;
		}else {
			$okMoney=$userMoney->ice_have_money - $userMoney->ice_get_money;
		}
		$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->icealipay WHERE ice_success>0 and ice_user_id=".$user_info->ID);
		$total_money   = $wpdb->get_var("SELECT SUM(ice_price) FROM $wpdb->icealipay WHERE ice_success>0 and ice_user_id=".$user_info->ID);
		$lists = $wpdb->get_results("SELECT * FROM $wpdb->icealipay where ice_success=1 and ice_user_id=$user_info->ID order by ice_time DESC limit 0,5");
		echo '下载/查看：'.$total_trade.'个&nbsp;&nbsp;&nbsp;&nbsp;消费：'.sprintf("%.2f",$userMoney->ice_get_money).get_option('ice_name_alipay').'&nbsp;&nbsp;&nbsp;&nbsp;剩余：'.sprintf("%.2f",$okMoney).get_option('ice_name_alipay').'<br />';
		echo '<ul>';
		foreach ($lists as $list){
			echo '<li><a target="_blank" href="'.get_permalink($list->ice_post).'">'.$list->ice_title.'</a></li>';
		}
		echo '</ul><div style="border-top:1px solid #eee;padding: 8px 12px 4px;margin:0 -12px"><i class="dashicons dashicons-external"></i> 强烈推荐完美兼容插件的<a href="https://www.mobantu.com/7191.html" target="_blank">Modown</a>主题。</div>';
	}
}

function addUserMoney($userId,$money){
	global $wpdb;
	$myinfo=$wpdb->get_row("select * from ".$wpdb->iceinfo." where ice_user_id=".$userId);
	if(!$myinfo){
		return $wpdb->query("insert into $wpdb->iceinfo(ice_have_money,ice_user_id,ice_get_money)values('$money','$userId',0)");
	}else{
		return $wpdb->query("update $wpdb->iceinfo set ice_have_money=ice_have_money+".$money." where ice_user_id=".$userId);
	}
}

/*  www.erphp.com  */
function erphpmeta(){
	return true;
}

function mbtcheck(){
	return "1";
}

function erphpdod(){
	return "1";
}

function erphpdown_check_xiaofei($uid){
	global $wpdb;
	$down_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_success=1 and ice_user_id=".$uid);
	if($down_info)
		return true;
	return false;
}


add_shortcode('buy','erphpdown_shortcode_buy');
function erphpdown_shortcode_buy($atts){
	$atts = shortcode_atts( array(
        'id' => '',
        'buy' => '立即购买',
        'down' => '立即下载'
    ), $atts, 'buy' );

    date_default_timezone_set('Asia/Shanghai'); 
	global $post,$wpdb;

	if($atts['id']) {
		$post_id = $atts['id'];
	}else{
		$post_id = $post->ID;
	}

	$memberDown=get_post_meta($post_id, 'member_down',TRUE);
	$start_down=get_post_meta($post_id, 'start_down', true);
	$days=get_post_meta($post_id, 'down_days', true);
	$userType=getUsreMemberType();
	$user_info=wp_get_current_user();
	$down_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".$post_id."' and ice_success=1 and (ice_index is null or ice_index = '') and ice_user_id=".$user_info->ID." order by ice_time desc");
	$user_id = is_user_logged_in() ? wp_get_current_user()->ID : 0;
	$wppay = new EPD($post_id, $user_id);

	if($days > 0){
		$lastDownDate = date('Y-m-d H:i:s',strtotime('+'.$days.' day',strtotime($down_info->ice_time)));
		$nowDate = date('Y-m-d H:i:s');
		if(strtotime($nowDate) > strtotime($lastDownDate)){
			$down_info = null;
		}
	}
	
	if( (($memberDown==3 || $memberDown==4) && $userType) || $wppay->isWppayPaid() || $down_info || (($memberDown==6 || $memberDown==8) && $userType >= 9) || (($memberDown==7 || $memberDown==9) && $userType == 10) ){
		if($start_down){
			return "<a href=".constant("erphpdown").'download.php?postid='.$post_id." class='erphpdown-down' target='_blank'>".$atts['down']."</a>";
		}else{
			return '';
		}
	}else{
		return '<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.$post_id.' target="_blank" >'.$atts['buy'].'</a>';
	}
	
}

add_shortcode('box','erphpdown_shortcode_box');
function erphpdown_shortcode_box(){
	date_default_timezone_set('Asia/Shanghai'); 
	global $post, $wpdb;
	$start_down=get_post_meta(get_the_ID(), 'start_down', true);
	$start_down2=get_post_meta(get_the_ID(), 'start_down2', true);
	$days=get_post_meta(get_the_ID(), 'down_days', true);
	$price=get_post_meta(get_the_ID(), 'down_price', true);
	$price_type=get_post_meta(get_the_ID(), 'down_price_type', true);
	$url=get_post_meta(get_the_ID(), 'down_url', true);
	$urls=get_post_meta(get_the_ID(), 'down_urls', true);
	$url_free=get_post_meta(get_the_ID(), 'down_url_free', true);
	$memberDown=get_post_meta(get_the_ID(), 'member_down',TRUE);
	$hidden=get_post_meta(get_the_ID(), 'hidden_content', true);
	$userType=getUsreMemberType();
	$down_info = null;$downMsgFree = '';

	$erphp_life_name    = get_option('erphp_life_name')?get_option('erphp_life_name'):'终身VIP';
	$erphp_year_name    = get_option('erphp_year_name')?get_option('erphp_year_name'):'包年VIP';
	$erphp_vip_name  = get_option('erphp_vip_name')?get_option('erphp_vip_name'):'VIP';

	$erphp_popdown = get_option('erphp_popdown');
	if($erphp_popdown){
		$iframe = '&iframe=1';
	}
	
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

	$content = '';

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
			$content.='<fieldset class="erphpdown" id="erphpdown" style="display:block"><legend>资源下载</legend>'.$downMsgFree;
			
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
							$downMsg.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".$filepath."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
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
		$content.='<fieldset class="erphpdown" id="erphpdown" style="display:block"><legend>资源下载</legend>'.$downMsgFree;
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
    					if(is_user_logged_in()){
							if($price){
								if($indexMemberDown != 4 && $indexMemberDown != 8 && $indexMemberDown != 9)
									$content.='此资源下载价格为<span class="erphpdown-price">'.$price.'</span>'.get_option("ice_name_alipay");
							}else{
								if($indexMemberDown != 4 && $indexMemberDown != 8 && $indexMemberDown != 9)
									$content.='此资源仅限注册用户下载';
							}

							if($price || $indexMemberDown == 4 || $indexMemberDown == 8 || $indexMemberDown == 9){
								global $wpdb;
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
												$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index.$iframe."' class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
											}else{
												$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."' class='erphpdown-down' target='_blank'>立即下载</a>";
											}
										}elseif ($indexMemberDown==2 && $down_info==null){
											$content.='<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&index='.$index.'" target="_blank">立即购买</a>';
										}elseif ($indexMemberDown==5 && $down_info==null){
											$content.='<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&index='.$index.'" target="_blank">立即购买</a>';
										}elseif ($indexMemberDown==6 && $down_info==null){
											if($userType == 9){
												if(get_option('erphp_popdown')){
													$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index.$iframe."' class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
												}else{
													$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."' class='erphpdown-down' target='_blank'>立即下载</a>";
												}	
											}elseif($userType == 10){
												if(get_option('erphp_popdown')){
													$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index.$iframe."' class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
												}else{
													$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."' class='erphpdown-down' target='_blank'>立即下载</a>";
												}	
											}else{
												$content.='<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&index='.$index.'" target="_blank">立即购买</a>';
											}
										}elseif ($indexMemberDown==7 && $down_info==null){
											if($userType == 10){
												if(get_option('erphp_popdown')){
													$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index.$iframe."' class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
												}else{
													$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."' class='erphpdown-down' target='_blank'>立即下载</a>";
												}	
											}else{
												$content.='<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&index='.$index.'" target="_blank">立即购买</a>';
											}
										}elseif ($indexMemberDown==8 && $down_info==null){
											if($userType >= 9){
												if(get_option('erphp_popdown')){
													$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index.$iframe."' class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
												}else{
													$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."' class='erphpdown-down' target='_blank'>立即下载</a>";
												}	
											}
										}elseif ($indexMemberDown==9 && $down_info==null){
											if($userType >= 10){
												if(get_option('erphp_popdown')){
													$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index.$iframe."' class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
												}else{
													$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."' class='erphpdown-down' target='_blank'>立即下载</a>";
												}	
											}
										}elseif($down_info){
											if(get_option('erphp_popdown')){
												$content.='<a href="'.constant("erphpdown").'download.php?postid='.get_the_ID().'&index='.$index.$iframe.'" class="erphpdown-down erphpdown-down-layui">立即下载</a>';
											}else{
												$content.='<a href="'.constant("erphpdown").'download.php?postid='.get_the_ID().'&index='.$index.'" class="erphpdown-down" target="_blank">立即下载</a>';
											}
										}
									}else{
										if($down_info && $down_info->ice_price > 0){
											if(get_option('erphp_popdown')){
												$content.='<a href="'.constant("erphpdown").'download.php?postid='.get_the_ID().'&index='.$index.$iframe.'" class="erphpdown-down erphpdown-down-layui">已购买，立即下载</a>';
											}else{
												$content.='<a href="'.constant("erphpdown").'download.php?postid='.get_the_ID().'&index='.$index.'" class="erphpdown-down" target="_blank">已购买，立即下载</a>';
											}
										}else{
											$content.='<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&index='.$index.'" target="_blank">立即购买</a>';
										}
									}
								}
								
							}else{
								if(get_option('erphp_popdown')){
									$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index.$iframe."' class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
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
									$content.='此资源仅限注册用户下载，请先<a href="'.$erphp_url_front_login.'" target="_blank" class="erphp-login-must">登录</a>';
								}
							}
						}
    					$content .= '</fieldset>';
    				}
    			}
			}
		}else{
			if(is_user_logged_in()){
				if($price){
					if($memberDown != 4 && $memberDown != 8 && $memberDown != 9)
						$content.='此资源下载价格为<span class="erphpdown-price">'.$price.'</span>'.get_option("ice_name_alipay");
				}else{
					if($memberDown != 4 && $memberDown != 8 && $memberDown != 9)
						$content.='此资源仅限注册用户下载';
				}

				if($price || $memberDown == 4 || $memberDown == 8 || $memberDown == 9){
					global $wpdb;
					$user_info=wp_get_current_user();
					$down_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".get_the_ID()."' and ice_success=1 and (ice_index is null or ice_index = '') and ice_user_id=".$user_info->ID." order by ice_time desc");
					if($days > 0){
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
									$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID().$iframe." class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
								}else{
									$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID()." class='erphpdown-down' target='_blank'>立即下载</a>";
								}
							}elseif ($memberDown==2 && $down_info==null){
								$content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank">立即购买</a>';
							}elseif ($memberDown==5 && $down_info==null){
								$content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank">立即购买</a>';
							}elseif ($memberDown==6 && $down_info==null){
								if($userType == 9){
									if(get_option('erphp_popdown')){
										$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID().$iframe." class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
									}else{
										$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID()." class='erphpdown-down' target='_blank'>立即下载</a>";
									}	
								}elseif($userType == 10){
									if(get_option('erphp_popdown')){
										$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID().$iframe." class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
									}else{
										$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID()." class='erphpdown-down' target='_blank'>立即下载</a>";
									}	
								}else{
									$content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank">立即购买</a>';
								}
							}elseif ($memberDown==7 && $down_info==null){
								if($userType == 10){
									if(get_option('erphp_popdown')){
										$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID().$iframe." class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
									}else{
										$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID()." class='erphpdown-down' target='_blank'>立即下载</a>";
									}
								}else{
									$content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank">立即购买</a>';
								}
							}elseif ($memberDown==8 && $down_info==null){
								if($userType >= 9){
									if(get_option('erphp_popdown')){
										$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID().$iframe." class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
									}else{
										$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID()." class='erphpdown-down' target='_blank'>立即下载</a>";
									}
										
								}
							}elseif ($memberDown==9 && $down_info==null){
								if($userType >= 10){
									if(get_option('erphp_popdown')){
										$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID().$iframe." class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
									}else{
										$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID()." class='erphpdown-down' target='_blank'>立即下载</a>";
									}
										
								}
							}elseif($down_info){
								if(get_option('erphp_popdown')){
									$content.='<a href='.constant("erphpdown").'download.php?postid='.get_the_ID().$iframe.' class="erphpdown-down erphpdown-down-layui">立即下载</a>';
								}else{
									$content.='<a href='.constant("erphpdown").'download.php?postid='.get_the_ID().' class="erphpdown-down" target="_blank">立即下载</a>';
								}
							}
						}else {
							if($down_info && $down_info->ice_price > 0){
								if(get_option('erphp_popdown')){
									$content.='<a href='.constant("erphpdown").'download.php?postid='.get_the_ID().$iframe.' class="erphpdown-down erphpdown-down-layui">已购买，立即下载</a>';
								}else{
									$content.='<a href='.constant("erphpdown").'download.php?postid='.get_the_ID().' class="erphpdown-down" target="_blank">已购买，立即下载</a>';
								}
							}else{
								$content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank">立即购买</a>';
							}
						}
					}
					
				}else{
					if(get_option('erphp_popdown')){
						$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID().$iframe." class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
					}else{
						$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID()." class='erphpdown-down' target='_blank'>立即下载</a>";
					}
				}
				
			}else {
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
		
	}else{
		if($downMsgFree) $content.='<fieldset class="erphpdown" id="erphpdown" style="display:block"><legend>资源下载</legend>'.$downMsgFree.'</fieldset>';
	}
	
	return $content;
}

function erphpdown_shortcode_see($atts, $content=null){
	$atts = shortcode_atts( array(
        'index' => '',
        'price' => ''
    ), $atts, 'erphpdown' );
	date_default_timezone_set('Asia/Shanghai'); 
	global $post,$wpdb;

	$erphp_life_name    = get_option('erphp_life_name')?get_option('erphp_life_name'):'终身VIP';
	$erphp_year_name    = get_option('erphp_year_name')?get_option('erphp_year_name'):'包年VIP';
	$erphp_vip_name  = get_option('erphp_vip_name')?get_option('erphp_vip_name'):'VIP';

	$original_content = $content;

	$erphp_see2_style = get_option('erphp_see2_style');

	$days=get_post_meta(get_the_ID(), 'down_days', true);
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

	if($atts['index'] > 0 && is_numeric($atts['index'])){
		if($atts['price'] > 0 && is_numeric($atts['price'])){
			$price_index = $atts['price'];
		}else{
			$price_index = get_post_meta($post->ID, 'down_price', true);
		}

		if($price_index > 0){
			$html='<fieldset class="erphpdown erphpdown-see erphpdown-content-vip" style="display:block"><legend>内容查看</legend>';
			if(is_user_logged_in()){
				$user_info=wp_get_current_user();
				$down_info=$wpdb->get_row("select * from ".$wpdb->iceindex." where ice_post='".$post->ID."' and ice_index=".$atts['index']." and ice_user_id=".$user_info->ID." and ice_price='".$price_index."' order by ice_time desc");
				if($days > 0){
					$lastDownDate = date('Y-m-d H:i:s',strtotime('+'.$days.' day',strtotime($down_info->ice_time)));
					$nowDate = date('Y-m-d H:i:s');
					if(strtotime($nowDate) > strtotime($lastDownDate)){
						$down_info = null;
					}
				}
				if($down_info){
					return '<fieldset class="erphpdown erphpdown-see erphpdown-content-vip" style="display:block"><legend>内容查看</legend>'.do_shortcode($content).'</fieldset>';
				}else{
					$html.='此隐藏内容查看价格为<span class="erphpdown-price">'.$price_index.'</span>'.get_option('ice_name_alipay');
					$html.='<a class="erphpdown-buy erphpdown-buy-index" href="javascript:;" data-post="'.$post->ID.'" data-index="'.$atts['index'].'" data-price="'.$price_index.'">立即购买</a></fieldset>';
				}
			}else{
				$html.='此隐藏内容查看价格为<span class="erphpdown-price">'.$price_index.'</span>'.get_option('ice_name_alipay').'，请先<a href="'.$erphp_url_front_login.'" target="_blank" class="erphp-login-must">登录</a></fieldset>';
			}
			return $html;
		}else{
			return '';
		}
	}else{
		$userType=getUsreMemberType();
		$memberDown=get_post_meta($post->ID, 'member_down',TRUE);
		$start_down2=get_post_meta($post->ID, 'start_down2', true);
		$start_down=get_post_meta($post->ID, 'start_down', true);
		$start_see2=get_post_meta($post->ID, 'start_see2', true);
		$start_see=get_post_meta($post->ID, 'start_see', true);
		$price=get_post_meta($post->ID, 'down_price', true);

		$user_info=wp_get_current_user();
		$down_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".$post->ID."' and ice_success=1 and (ice_index is null or ice_index = '') and ice_user_id=".$user_info->ID." order by ice_time desc");
		$user_id = is_user_logged_in() ? wp_get_current_user()->ID : 0;
		$wppay = new EPD($post->ID, $user_id);

		if($start_down2){
			if( $wppay->isWppayPaid() || ($memberDown == 3 && $userType) || !$price){
				return '<fieldset class="erphpdown erphpdown-content-vip erphpdown-see-pay" style="display:block"><legend>内容查看</legend>'.do_shortcode($content).'</fieldset>';
			}else{
				if($memberDown == 3){
					$content = '<fieldset class="erphpdown erphpdown-content-vip erphpdown-see-pay" style="display:block"><legend>内容查看</legend>此隐藏内容查看价格<span class="erphpdown-price">'.$price.'</span>元<a href="javascript:;" class="erphp-wppay-loader erphpdown-buy" data-post="'.$post->ID.'">立即支付</a>&nbsp;&nbsp;<b>或</b>&nbsp;&nbsp;升级'.$erphp_vip_name.'后免费<a href="'.$erphp_url_front_vip2.'" target="_blank" class="erphpdown-vip'.(is_user_logged_in()?'':' erphp-login-must').'">升级'.$erphp_vip_name.'</a>';
				}else{
					$content = '<fieldset class="erphpdown erphpdown-content-vip erphpdown-see-pay" style="display:block"><legend>内容查看</legend>此隐藏内容查看价格<span class="erphpdown-price">'.$price.'</span>元<a href="javascript:;" class="erphp-wppay-loader erphpdown-buy" data-post="'.get_the_ID().'">立即支付</a>';	
				}

				if(get_option('ice_tips_see')) $content.='<div class="erphpdown-tips">'.get_option('ice_tips_see').'</div>';

				$content .= '</fieldset>'; 
				return $content;
			}
		}elseif($start_down || $start_see2 || $start_see){
			if(is_user_logged_in()){
				if($days > 0){
					$lastDownDate = date('Y-m-d H:i:s',strtotime('+'.$days.' day',strtotime($down_info->ice_time)));
					$nowDate = date('Y-m-d H:i:s');
					if(strtotime($nowDate) > strtotime($lastDownDate)){
						$down_info = null;
					}
				}
				if( (($memberDown==3 || $memberDown==4) && $userType) || $wppay->isWppayPaid() || $down_info || (($memberDown==6 || $memberDown==8) && $userType >= 9) || (($memberDown==7 || $memberDown==9) && $userType == 10) ){
					if(!$wppay->isWppayPaid() && !$down_info){
						$erphp_life_times    = get_option('erphp_life_times');
						$erphp_year_times    = get_option('erphp_year_times');
						$erphp_quarter_times = get_option('erphp_quarter_times');
						$erphp_month_times  = get_option('erphp_month_times');
						$erphp_day_times  = get_option('erphp_day_times');

						if(checkDownHas($user_info->ID,$post->ID)){
							return '<fieldset class="erphpdown erphpdown-see erphpdown-content-vip" style="display:block"><legend>内容查看</legend>'.do_shortcode($content).'</fieldset>';
						}else{
							if($userType == 6 && $erphp_day_times > 0){
								if( checkSeeLog($user_info->ID,$post->ID,$erphp_day_times,erphpGetIP()) ){
									return '<p class="erphpdown-content-vip erphpdown-content-vip-see">您可免费查看本文隐藏内容！<a href="javascript:;" class="erphpdown-see-btn" data-post="'.$post->ID.'">立即查看</a>（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_day_times-getSeeCount($user_info->ID)).'个）</p>';
								}else{
									return '<p class="erphpdown-content-vip">您暂时无权查看本文隐藏内容，请明天再来！（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_day_times-getSeeCount($user_info->ID)).'个）</p>';
								}
							}elseif($userType == 7 && $erphp_month_times > 0){
								if( checkSeeLog($user_info->ID,$post->ID,$erphp_month_times,erphpGetIP()) ){
									return '<p class="erphpdown-content-vip erphpdown-content-vip-see">您可免费查看本文隐藏内容！<a href="javascript:;" class="erphpdown-see-btn" data-post="'.$post->ID.'">立即查看</a>（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_month_times-getSeeCount($user_info->ID)).'个）</p>';
								}else{
									return '<p class="erphpdown-content-vip">您暂时无权查看本文隐藏内容，请明天再来！（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_month_times-getSeeCount($user_info->ID)).'个）</p>';
								}
							}elseif($userType == 8 && $erphp_quarter_times > 0){
								if( checkSeeLog($user_info->ID,$post->ID,$erphp_quarter_times,erphpGetIP()) ){
									return '<p class="erphpdown-content-vip erphpdown-content-vip-see">您可免费查看本文隐藏内容！<a href="javascript:;" class="erphpdown-see-btn" data-post="'.$post->ID.'">立即查看</a>（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_quarter_times-getSeeCount($user_info->ID)).'个）</p>';
								}else{
									return '<p class="erphpdown-content-vip">您暂时无权查看本文隐藏内容，请明天再来！（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_quarter_times-getSeeCount($user_info->ID)).'个）</p>';
								}
							}elseif($userType == 9 && $erphp_year_times > 0){
								if( checkSeeLog($user_info->ID,$post->ID,$erphp_year_times,erphpGetIP()) ){
									return '<p class="erphpdown-content-vip erphpdown-content-vip-see">您可免费查看本文隐藏内容！<a href="javascript:;" class="erphpdown-see-btn" data-post="'.$post->ID.'">立即查看</a>（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_year_times-getSeeCount($user_info->ID)).'个）</p>';
								}else{
									return '<p class="erphpdown-content-vip">您暂时无权查看本文隐藏内容，请明天再来！（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_year_times-getSeeCount($user_info->ID)).'个）</p>';
								}
							}elseif($userType == 10 && $erphp_life_times > 0){
								if( checkSeeLog($user_info->ID,$post->ID,$erphp_life_times,erphpGetIP()) ){
									return '<p class="erphpdown-content-vip erphpdown-content-vip-see">您可免费查看本文隐藏内容！<a href="javascript:;" class="erphpdown-see-btn" data-post="'.$post->ID.'">立即查看</a>（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_life_times-getSeeCount($user_info->ID)).'个）</p>';
								}else{
									return '<p class="erphpdown-content-vip">您暂时无权查看本文隐藏内容，请明天再来！（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_life_times-getSeeCount($user_info->ID)).'个）</p>';
								}
							}else{
								return '<fieldset class="erphpdown erphpdown-see erphpdown-content-vip" style="display:block"><legend>内容查看</legend>'.do_shortcode($content).'</fieldset>';
							}
						}
					}else{
						return '<fieldset class="erphpdown erphpdown-see erphpdown-content-vip" style="display:block"><legend>内容查看</legend>'.do_shortcode($content).'</fieldset>';
					}
				}else{
					if($start_see2 && $erphp_see2_style){
						$content = '<div class="erphpdown-content-vip erphpdown-content-vip2">您暂时无权查看此隐藏内容！</div>';
					}else{
						$content = '<fieldset class="erphpdown erphpdown-see erphpdown-see-pay erphpdown-content-vip" id="erphpdown" style="display:block"><legend>内容查看</legend>';
						if($price){
							if($memberDown != 4 && $memberDown != 8 && $memberDown != 9){
								$content.='此隐藏内容查看价格为<span class="erphpdown-price">'.$price.'</span>'.get_option('ice_name_alipay');
							}
						}else{
							if($memberDown != 4 && $memberDown != 8 && $memberDown != 9){
								return '<fieldset class="erphpdown erphpdown-see erphpdown-content-vip" style="display:block"><legend>内容查看</legend>'.do_shortcode($original_content).'</fieldset>';
							}
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

						if($memberDown==4 && $userType==FALSE){
							$content.='此隐藏内容仅限'.$erphp_vip_name.'查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
						}elseif($memberDown==8 && $userType<9)
						{
							$content.='此隐藏内容仅限'.$erphp_year_name.'查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_year_name.'</a>';
						}elseif($memberDown==9 && $userType<10)
						{
							$content.='此隐藏内容仅限'.$erphp_life_name.'查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_life_name.'</a>';
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
								$content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank">立即购买</a>';
							}
						}

						if(get_option('ice_tips_see')) $content.='<div class="erphpdown-tips">'.get_option('ice_tips_see').'</div>';
						$content.='</fieldset>';
					}
					return $content;
				}
			}else{
				$content='<fieldset class="erphpdown erphpdown-see erphpdown-see-pay erphpdown-content-vip" id="erphpdown" style="display:block"><legend>内容查看</legend>';
				if($memberDown == 4 || $memberDown == 8 || $memberDown == 9){
					$content.='此隐藏内容仅限'.$erphp_vip_name.'查看，请先<a href="'.$erphp_url_front_login.'" target="_blank" class="erphp-login-must">登录</a>';
				}else{
					if($price){
						$content.='此隐藏内容查看价格为<span class="erphpdown-price">'.$price.'</span>'.get_option('ice_name_alipay').'，请先<a href="'.$erphp_url_front_login.'" target="_blank" class="erphp-login-must erphp-see-must">登录</a>';
					}else{
						$content.='此隐藏内容仅限注册用户查看，请先<a href="'.$erphp_url_front_login.'" target="_blank" class="erphp-login-must">登录</a>';
					}
				}
				
				if(get_option('ice_tips_see')) $content.='<div class="erphpdown-tips">'.get_option('ice_tips_see').'</div>';
				$content.='</fieldset>';
				return $content;
			}
		}
	}
}  
add_shortcode('erphpdown','erphpdown_shortcode_see');

function erphpdown_shortcode_vip($atts, $content=null){
	$atts = shortcode_atts( array(
        'type' => '',
    ), $atts, 'vip' );

    $erphp_life_name    = get_option('erphp_life_name')?get_option('erphp_life_name'):'终身VIP';
	$erphp_year_name    = get_option('erphp_year_name')?get_option('erphp_year_name'):'包年VIP';
	$erphp_quarter_name = get_option('erphp_quarter_name')?get_option('erphp_quarter_name'):'包季VIP';
	$erphp_month_name  = get_option('erphp_month_name')?get_option('erphp_month_name'):'包月VIP';
	$erphp_day_name  = get_option('erphp_day_name')?get_option('erphp_day_name'):'体验VIP';
	$erphp_vip_name  = get_option('erphp_vip_name')?get_option('erphp_vip_name'):'VIP';

    $erphp_url_front_vip = get_bloginfo('wpurl').'/wp-admin/admin.php?page=erphpdown/admin/erphp-update-vip.php';
	if(get_option('erphp_url_front_vip')){
		$erphp_url_front_vip = get_option('erphp_url_front_vip');
	}
	$erphp_url_front_login = wp_login_url();
	if(get_option('erphp_url_front_login')){
		$erphp_url_front_login = get_option('erphp_url_front_login');
	}

	$vip = '<fieldset id="erphpdown" class="erphpdown erphpdown-see erphpdown-content-vip" style="display:block"><legend>内容查看</legend>此隐藏内容仅限'.$erphp_vip_name.'查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_vip_name.'</a></fieldset>';

	if(is_user_logged_in()){
		$userType=getUsreMemberType();
		if(!$atts['type']){
			if($userType){
				return do_shortcode($content);
			}else{
				return $vip;
			}
		}else{
			if($atts['type'] == '6' && $userType < 6){
				return '<fieldset id="erphpdown" class="erphpdown erphpdown-see erphpdown-content-vip" style="display:block"><legend>内容查看</legend>此隐藏内容仅限'.$erphp_vip_name.'查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_vip_name.'</a></fieldset>';
			}elseif($atts['type'] == '7' && $userType < 7){
				return '<fieldset id="erphpdown" class="erphpdown erphpdown-see erphpdown-content-vip" style="display:block"><legend>内容查看</legend>此隐藏内容仅限'.$erphp_month_name.'查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_month_name.'</a></fieldset>';
			}elseif($atts['type'] == '8' && $userType < 8){
				return '<fieldset id="erphpdown" class="erphpdown erphpdown-see erphpdown-content-vip" style="display:block"><legend>内容查看</legend>此隐藏内容仅限'.$erphp_quarter_name.'查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_quarter_name.'</a></fieldset>';
			}elseif($atts['type'] == '9' && $userType < 9){
				return '<fieldset id="erphpdown" class="erphpdown erphpdown-see erphpdown-content-vip" style="display:block"><legend>内容查看</legend>此隐藏内容仅限'.$erphp_year_name.'查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_year_name.'</a></fieldset>';
			}elseif($atts['type'] == '10' && $userType < 10){
				return '<fieldset id="erphpdown" class="erphpdown erphpdown-see erphpdown-content-vip" style="display:block"><legend>内容查看</legend>此隐藏内容仅限'.$erphp_life_name.'查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_life_name.'</a></fieldset>';
			}else{
				return do_shortcode($content);
			}
		}
	}else{
		return $vip;
	}			
}  
add_shortcode('vip','erphpdown_shortcode_vip');

function erphpdown_admin_notice() {
	$token = get_option('MBT_ERPHPDOWN_token');
	if($token){
		$ll = admin_url().'admin.php?page=erphpdown/admin/erphp-settings.php';
	}else{
		$ll = admin_url().'admin.php?page=erphpdown/admin/erphp-active.php';
	}
    ?>
    <br>
    <div id="message" class="error updated notice is-dismissible">
        <p>Erphpdown插件需要先设置下哦！<a href="<?php echo $ll;?>">去设置</a></p>
        <button type="button" class="notice-dismiss"><span class="screen-reader-text">忽略此通知。</span></button>
    </div>
    <?php
}
$ice_proportion_alipay = get_option('ice_proportion_alipay');
if(!$ice_proportion_alipay){
	add_action( 'admin_notices', 'erphpdown_admin_notice' );
}

function erphpdown_check_checkin($uid){
	date_default_timezone_set('Asia/Shanghai');
    global $wpdb;
    $result = $wpdb->get_var("select count(ID) from $wpdb->checkin where date(create_time)=curdate() and user_id=".$uid);
    if($result){
        return 1;
    }
    return 0;
}

function erphpGetIP(){
	if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $ip = getenv('REMOTE_ADDR');
    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
}

function erphpdown_encrypt($string,$operation,$key=''){ 
    $key=md5($key); 
    $key_length=strlen($key); 
      $string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string; 
    $string_length=strlen($string); 
    $rndkey=$box=array(); 
    $result=''; 
    for($i=0;$i<=255;$i++){ 
           $rndkey[$i]=ord($key[$i%$key_length]); 
        $box[$i]=$i; 
    } 
    for($j=$i=0;$i<256;$i++){ 
        $j=($j+$box[$i]+$rndkey[$i])%256; 
        $tmp=$box[$i]; 
        $box[$i]=$box[$j]; 
        $box[$j]=$tmp; 
    } 
    for($a=$j=$i=0;$i<$string_length;$i++){ 
        $a=($a+1)%256; 
        $j=($j+$box[$a])%256; 
        $tmp=$box[$a]; 
        $box[$a]=$box[$j]; 
        $box[$j]=$tmp; 
        $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256])); 
    } 
    if($operation=='D'){ 
        if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8)){ 
            return substr($result,8); 
        }else{ 
            return''; 
        } 
    }else{ 
        return str_replace('=','',base64_encode($result)); 
    } 
}

function erphpdown_lock_url($str,$key){
	return erphpdown_encrypt($str,'E',$key);
}

function erphpdown_unlock_url($str,$key){
	return erphpdown_encrypt($str,'D',$key);
}

function erphpdown_file_post($url = '', $postData = ''){
	$data = http_build_query($postData);
	$opts = array(
	   'http'=>array(
	     'method'=>"POST",
	     'header'=>"Content-type: application/x-www-form-urlencoded\r\n".
	               "Content-length:".strlen($data)."\r\n" .
	               "Cookie: foo=bar\r\n" .
	               "\r\n",
	     'content' => $data,
	   )
	);
	$cxContext = stream_context_create($opts);
	$result = file_get_contents($url, false, $cxContext);
	return $result;
}


function erphpdown_curl_post($url = '', $postData = ''){
	if(function_exists('curl_init')){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}else{
		wp_die("网站未开启curl组件，正常情况下该组件必须开启，请开启curl组件解决该问题");
	}
} 

function erphpdown_http_post($url, $data) {  
  $ch = curl_init();  
  curl_setopt($ch, CURLOPT_URL,$url);  
  curl_setopt($ch, CURLOPT_HEADER,0);  
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
  curl_setopt($ch, CURLOPT_POST, 1);  
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  //不验证 SSL 证书
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不验证 SSL 证书域名
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  
  $res = curl_exec($ch);  
  curl_close($ch);  
  return $res;  
}

function erphpdown_download_file($file_dir){
	//$file_dir = iconv('UTF-8', 'GBK//TRANSLIT', $file_dir);
	if(substr($file_dir,0,7) == 'http://' || substr($file_dir,0,8) == 'https://' || substr($file_dir,0,10) == 'thunder://' || substr($file_dir,0,7) == 'magnet:' || substr($file_dir,0,5) == 'ed2k:' || substr($file_dir,0,4) == 'ftp:')
	{
		$file_path = chop($file_dir);
		$allow_type = "jpg,gif,png,jpeg,bmp,mp3,wav,pdf";
		$allow = explode(",",$allow_type); 
		if (erphpdown_file_suffix($file_path,$allow)){
            ob_clean();
            ob_start();
            if(strpos(strtolower($file_path),'.pdf')){
            	header('Content-type: application/pdf');
            }
            header('Content-Disposition: attachment; filename="'.basename($file_path).'"');
			readfile($file_path);


		}else{
			echo "<script type='text/javascript'>window.location='$file_path';</script>";
		}
		exit;
	}
	$file_dir=chop($file_dir);
	if(!file_exists($file_dir))
	{
		return false;
	}
	$temp=explode("/",$file_dir);

	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: public");
	header("Content-Description: File Transfer");
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=\"".end($temp)."\"");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".filesize($file_dir));
	ob_end_flush();
	@readfile($file_dir);
}

function erphpdown_file_suffix($file_name, $allow_type = array()){
  $fnarray=explode('.', $file_name);
    $file_suffix = strtolower(array_pop($fnarray));
  if (empty($allow_type))
  {
    return $file_suffix;
  }
  else
  {
    if (in_array($file_suffix, $allow_type))
    {
      return true;
    }
    else
    {
      return false;
    }
  }
}


function erphpdown_modify_user_table( $column ) {
    $column['vip'] = 'VIP';
    $column['money'] = '余额';
    $column['cart'] = '消费记录';
    $column['reg'] = '注册时间';
    return $column;
}
add_filter( 'manage_users_columns', 'erphpdown_modify_user_table' );

function erphpdown_modify_user_table_row( $val, $column_name, $user_id ) {
    switch ($column_name) {
        case 'vip' :
        	$userType = getUsreMemberTypeById($user_id);
            if($userType == 6){
            	return '体验';
            }elseif($userType == 7){
            	return '包月';
            }elseif($userType == 8){
            	return '包季';
            }elseif($userType == 9){
            	return '包年';
            }elseif($userType == 10){
            	return '终身';
            }else{
            	return '—';
            }
            break;
        case 'money':
            return erphpGetUserOkMoneyById($user_id);
        	break;
        default:
        case 'cart':
            return erphpdown_check_xiaofei($user_id)?'有':'—';
        	break;
        case 'reg':
        	$user = get_user_by("ID",$user_id);
            return get_date_from_gmt($user->user_registered);
        	break;
    }
    return $val;
}
add_filter( 'manage_users_custom_column', 'erphpdown_modify_user_table_row', 10, 3 );

add_filter( 'manage_users_sortable_columns', 'erphpdown_modify_user_table_row_sortable' );
function erphpdown_modify_user_table_row_sortable( $columns ) {
	return wp_parse_args( array( 'reg' => 'registered' ), $columns );
}

function erphpdown_column_width() {
    echo '<style type="text/css">';
    echo '.column-vip , .column-money , .column-cart{ text-align: center !important; width:74px;}.column-reg{ text-align: center !important; width:90px;}';
    echo '</style>';
}
add_action('admin_head', 'erphpdown_column_width');

function erphpdown_is_mobile(){  
    $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';  
    $mobile_browser = '0';  
  	if (isset($_SERVER['HTTP_USER_AGENT'])) {
      $clientkeywords = array('iphone', 'android', 'phone', 'mobile', 'wap', 'netfront', 'java', 'opera mobi', 'opera mini','ucweb', 'windows ce', 'symbian', 'series', 'webos', 'sony', 'blackberry', 'dopod', 'nokia', 'samsung','palmsource', 'xda', 'pieplus', 'meizu', 'midp', 'cldc', 'motorola', 'foma', 'docomo', 'up.browser','up.link', 'blazer', 'helio', 'hosin', 'huawei', 'xiaomi', 'novarra', 'coolpad', 'webos', 'techfaith', 'palmsource','alcatel', 'amoi', 'ktouch', 'nexian', 'ericsson', 'philips', 'sagem', 'wellcom', 'bunjalloo', 'maui', 'smartphone','iemobile', 'spice', 'bird', 'zte-', 'longcos', 'pantech', 'gionee', 'portalmmm', 'jig browser', 'hiptop','benq', 'haier', '^lct', '320x320', '240x320', '176x220', 'windows phone');
      if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
      	$mobile_browser++;  
      }
    }
    if(preg_match('/(up.browser|up.link|ucweb|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))  
        $mobile_browser++;  
    if((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') !== false))  
        $mobile_browser++;  
    if(isset($_SERVER['HTTP_X_WAP_PROFILE']))  
        $mobile_browser++;  
    if(isset($_SERVER['HTTP_PROFILE']))  
        $mobile_browser++;  
    $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));  
    $mobile_agents = array(  
        'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',  
        'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',  
        'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',  
        'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',  
        'newt','noki','oper','palm','pana','pant','phil','play','port','prox',  
        'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',  
        'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',  
        'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',  
        'wapr','webc','winw','winw','xda','xda-' 
        );  
    if(in_array($mobile_ua, $mobile_agents))  
        $mobile_browser++;  
    if(strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)  
        $mobile_browser++;  
    // Pre-final check to reset everything if the user is on Windows  
    if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)  
        $mobile_browser=0;  
    // But WP7 is also Windows, with a slightly different characteristic  
    if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false)  
        $mobile_browser++;  
    if($mobile_browser>0)  
        return true;  
    else
        return false;  
}

function erphpdown_is_weixin(){ 
	if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
        return true;
    }  
    return false;
}

function showMsgNotice($msg,$color=FALSE){
	echo '<div class="updated settings-error"><p>'.$msg.'</p></div>';
}