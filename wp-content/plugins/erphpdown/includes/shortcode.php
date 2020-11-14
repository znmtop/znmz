<?php
//Powered by mobantu.com 
if ( !defined('ABSPATH') ) {exit;}
add_shortcode( 'ice_purchased_goods','purchased_goods_lists');//已购商品
add_shortcode( 'ice_purchased_tuiguang','purchased_tuiguang_lists');//我的推广
add_shortcode( 'ice_purchased_tuiguangxiazai','purchased_tuiguangxiazai_lists');//推广下载
add_shortcode( 'ice_purchased_tuiguangvip','purchased_tuiguangvip_lists');//推广vip
add_shortcode( 'ice_order_tracking','order_tracking_lists');//订单查询
add_shortcode( 'ice_my_property', 'my_property' );//我的资产
add_shortcode( 'ice_recharge_money','recharge_money');//充值
add_shortcode( 'ice_cash_application','cash_application');//取现申请
add_shortcode( 'ice_cash_application_lists','cash_application_lists');//取现列表
add_shortcode( 'vip_tracking_lists','vip_tracking_lists');//VIP订单查询
add_shortcode( 'ice_vip_member_service','vip_member_service');//VIP会员服务

//已购商品
function purchased_goods_lists() {
	if(!is_user_logged_in()){
		exit;
	}
	global $wpdb;
		//统计数据
	if(current_user_can('level_10')){
		$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->icealipay WHERE ice_success>0");
		$total_money   = $wpdb->get_var("SELECT SUM(ice_price) FROM $wpdb->icealipay WHERE ice_success>0");
	}else{
		$user_info=wp_get_current_user();
		$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->icealipay WHERE ice_success>0 and ice_user_id=".$user_info->ID);
		$total_money   = $wpdb->get_var("SELECT SUM(ice_price) FROM $wpdb->icealipay WHERE ice_success>0 and ice_user_id=".$user_info->ID);
	}
	//分页计算
	/////////////////////////////////////////////////www.mobantu.com   82708210@qq.com
	$ice_perpage = 20;
	$pages = ceil($total_trade / $ice_perpage);
	$page=isset($_GET['p']) ?intval($_GET['p']) :1;
	$offset = $ice_perpage*($page-1);
	if(current_user_can('level_10')){
		$list = $wpdb->get_results("SELECT * FROM $wpdb->icealipay where ice_success=1 order by ice_time DESC limit $offset,$ice_perpage");
	}
	else{
		$list = $wpdb->get_results("SELECT * FROM $wpdb->icealipay where ice_success=1 and ice_user_id=$user_info->ID order by ice_time DESC limit $offset,$ice_perpage");
	}
	?>
	<div class="wrap">
	<p>截止到&nbsp;<i class="icon-time"></i>&nbsp;<?php echo $showtime=date("Y-m-d H:i:s");?>&nbsp;<?php printf(('您在本站共计消费：<strong>%s</strong>.元'), $total_money); ?></p>
		<table class="widefat">
			<thead>
				<tr>
					<?php if(current_user_can('level_10')){ ?>
					<th width="8%">用户ID</th>
					<?php } ?>
					<th width="8%">订单号</th>
					<th width="25%">商品名称</th>
					<th width="5%">价格</th>
					<th width="15%">交易时间</th>	
					<th width="15%">下载</th>		
				</tr>
			</thead>
			<tbody>
		<?php
			if($list) {
				foreach($list as $value)
				{
					echo "<tr>\n";
					if(current_user_can('level_10')){
						echo "<td>".get_the_author_meta( 'user_login', $value->ice_user_id )."</td>";
					}
					echo "<td>$value->ice_num</td>";
					echo "<td><a href='".get_bloginfo('wpurl').'/?p='.$value->ice_post."' target='_blank'>$value->ice_title</a></td>\n";
					echo "<td>$value->ice_price</td>\n";
					echo "<td>$value->ice_time</td>\n";
					if(get_post_meta($value->ice_post, 'start_down', true))
					{
						echo "<td><a href='".constant("erphpdown").'download.php?url='.$value->ice_url."' target='_blank'>进入下载页面</a></td>\n";
					}
					echo "</tr>";
				}
			}
			else
			{
				echo '<tr><td colspan="5" align="center"><strong>您还没有购买记录！</strong></td></tr>';
			}
		?>
		</tbody>
		</table>
	</div>
<?php 
}

//我的推广
function purchased_tuiguang_lists() { 
	global $wpdb;
	$user_Info   = wp_get_current_user();
	if(!is_user_logged_in()){
		exit;
	}
	//统计数据
	$total_user   = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users WHERE father_id=".$user_Info->ID);
	/*$total_money   = $wpdb->get_var("SELECT SUM(ice_price) FROM $wpdb->icealipay WHERE ice_success>0 and ice_user_id in (select ID from $wpdb->users where father_id=".$user_Info->ID.")");*/
	
	//分页计算
	$ice_perpage = 20;
	$pages = ceil($total_user / $ice_perpage);
	$page=isset($_GET['p']) ?intval($_GET['p']) :1;
	$offset = $ice_perpage*($page-1);
	$list = $wpdb->get_results("SELECT ID,user_login,user_registered FROM $wpdb->users where father_id=".$user_Info->ID." limit $offset,$ice_perpage");
	
	?>
	<div class="wrap">
		<p>通过宣传下方的永久推广链接，推广用户购买VIP服务和商品购买下载，即可获得推广分成！</p>
		<p><?php printf(('截至目前，共推广<strong>%s</strong>人'), $total_user); ?>&nbsp;&nbsp;&nbsp;&nbsp;永久推广链接：<textarea id="spreadurl" rows="1" cols="80"><?php echo esc_url( home_url( '/?aff=' ) ).$user_Info->ID; ?></textarea></p>
		<h2>推广注册用户</h2>
		<table class="widefat">
			<thead>
				<tr>
					<th width="30%">用户ID</th>
					<th width="40%">注册时间</th>	    
					<th width="30%">消费额</th>	    
				</tr>
			</thead>
			<tbody>
		<?php
			if($list) {
				foreach($list as $value)
				{
					echo "<tr>\n";
					echo "<td>".$value->user_login."</td>";
					echo "<td>".$value->user_registered."</td>";
					echo "<td>".$wpdb->get_var("SELECT SUM(ice_price) FROM $wpdb->icealipay WHERE ice_success>0 and ice_user_id=".$value->ID)."</td>";
					echo "</tr>";
				}
			}
			else
			{
				echo '<tr><td colspan="3" align="center"><strong>没有推广记录</strong></td></tr>';
			}
		?>
		</tbody>
		</table>
		
	</div>
<?php 
}


//推广下载
function purchased_tuiguangxiazai_lists() { 
	global $wpdb;
	$user_Info   = wp_get_current_user();
	if(!is_user_logged_in())
	{
		exit;
	}
	//统计数据
	$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->icealipay WHERE ice_success>0 and ice_user_id in (select ID from $wpdb->users where father_id=".$user_Info->ID.")");
	$total_money   = $wpdb->get_var("SELECT SUM(ice_price) FROM $wpdb->icealipay WHERE ice_success>0 and ice_user_id in (select ID from $wpdb->users where father_id=".$user_Info->ID.")");
	
	//分页计算
	$ice_perpage = 20;
	$pages = ceil($total_trade / $ice_perpage);
	$page=isset($_GET['p']) ?intval($_GET['p']) :1;
	$offset = $ice_perpage*($page-1);
	$list = $wpdb->get_results("SELECT * FROM $wpdb->icealipay where ice_success=1 and ice_user_id in (select ID from $wpdb->users where father_id=".$user_Info->ID.") order by ice_time DESC limit $offset,$ice_perpage");
	
	?>
	<div class="wrap">
		<h2>推广购买下载订单</h2>
		<p><?php printf(('共<strong>%s</strong>.'), $total_money); ?></p>
		<table class="widefat">
			<thead>
				<tr>
					<th width="10%">用户ID</th>
					<th width="20%">订单号</th>
					<th width="25%">商品名称</th>
					<th width="5%">价格</th>
					<th width="15%">交易时间</th>		
				</tr>
			</thead>
			<tbody>
		<?php
			if($list) {
				foreach($list as $value)
				{
					echo "<tr>\n";
					echo "<td>".get_the_author_meta( 'user_login', $value->ice_user_id )."</td>";
					echo "<td>$value->ice_num</td>";
					echo "<td>$value->ice_title</td>\n";
					echo "<td>$value->ice_price</td>\n";
					echo "<td>$value->ice_time</td>\n";
					echo "</tr>";
				}
			}
			else
			{
				echo '<tr><td colspan="5" align="center"><strong>没有交易记录</strong></td></tr>';
			}
		?>
		</tbody>
		</table>
	
	</div>
<?php 
}




//推广VIP
function purchased_tuiguangvip_lists() { 
	if(!is_user_logged_in()){
			exit;
		}
	global $wpdb;
	$user_Info=wp_get_current_user();
	$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->vip where ice_user_id in (select ID from $wpdb->users where father_id=".$user_Info->ID.")");
	$total_success = $wpdb->get_var("SELECT sum(ice_price) FROM $wpdb->vip where ice_user_id in (select ID from $wpdb->users where father_id=".$user_Info->ID.")");
	
	//分页计算
	$ice_perpage = 20;
	$pages = ceil($total_trade / $ice_perpage);
	$page=isset($_GET['p']) ?intval($_GET['p']) :1;
	$offset = $ice_perpage*($page-1);
	$list = $wpdb->get_results("SELECT * FROM $wpdb->vip where ice_user_id in (select ID from $wpdb->users where father_id=".$user_Info->ID.") order by ice_time DESC limit $offset,$ice_perpage");
	
	?>
	<div class="wrap">
		<h2>推广VIP会员订单</h2>
		<p><?php printf(('共有<strong>%s</strong>笔交易，总金额：<strong>%s</strong>'), $total_trade, $total_success); ?></p>
		<table class="widefat">
			<thead>
				<tr>
					<th width="15%">用户ID</th>
					<th width="15%">VIP类型</th>
					<th width="5%">价格</th>
					<th width="15%">交易时间</th>			
				</tr>
			</thead>
			<tbody>
		<?php
			if($list) {
				foreach($list as $value)
				{
					$typeName=$value->ice_user_type==7 ?'包月' :($value->ice_user_type==8 ?'包季' : ($value->ice_user_type==10 ?'终身' : '包年'));
					echo "<tr>\n";
					echo "<td>".get_the_author_meta( 'user_login', $value->ice_user_id )."</td>\n";
					echo "<td>$typeName</td>\n";
					echo "<td>$value->ice_price</td>\n";
					echo "<td>$value->ice_time</td>\n";
					echo "</tr>";
				}
			}
			else
			{
				echo '<tr><td colspan="4" align="center"><strong>没有交易记录</strong></td></tr>';
			}
		?>
		</tbody>
		</table>
	
	</div>
<?php 
}

//订单查询
function order_tracking_lists() {
	if(!is_user_logged_in()){
		exit;
	}
	global $wpdb;
	if(current_user_can('administrator'))
	{
		//统计数据
		$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->icealipay");
		$total_success = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->icealipay WHERE ice_success>0");
		$total_money   = $wpdb->get_var("SELECT SUM(ice_price) FROM $wpdb->icealipay WHERE ice_success>0");
	}
	else 
	{
		$user_info=wp_get_current_user();
		$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->icealipay where ice_author=".$user_info->ID);
		$total_success = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->icealipay WHERE ice_success>0 and ice_author=".$user_info->ID);
		$total_money   = $wpdb->get_var("SELECT SUM(ice_price) FROM $wpdb->icealipay WHERE ice_success>0 and ice_author=".$user_info->ID);
	}
	//分页计算
	$ice_perpage = 20;
	$pages = ceil($total_trade / $ice_perpage);
	$page=isset($_GET['p']) ?intval($_GET['p']) :1;
	$offset = $ice_perpage*($page-1);
	
	if(current_user_can('administrator'))
	{
		$list = $wpdb->get_results("SELECT * FROM $wpdb->icealipay order by ice_time DESC limit $offset,$ice_perpage");
	}
	else 
	{
		$list = $wpdb->get_results("SELECT * FROM $wpdb->icealipay where ice_author= ".$user_info->ID." order by ice_time DESC limit $offset,$ice_perpage");
	}
?>
    <div class="wrap">
      <p><?php printf(('共有<strong>%s</strong>笔交易，其中<strong>%s</strong>笔交易完成了付款.总金额：<strong>%s</strong>元'), 
        number_format_i18n($total_trade), number_format_i18n($total_success),$total_money); ?></p>
      <table class="widefat">
        <thead>
          <tr>
            <th width="8%">用户ID</th>
            <th width="8%">订单号</th>
            <th width="25%">商品名称</th>
            <th width="5%">价格</th>
            <th width="15%">交易时间</th>
            <th width="8%">交易状态</th>
          </tr>
        </thead>
        <tbody>
          <?php
            if($list) {
                foreach($list as $value)
                {
                    $result=$value->ice_success?'成功':'未完成';
                    echo "<tr>\n";
                    echo "<td>".get_the_author_meta( 'user_login', $value->ice_user_id )."</td>";
                    echo "<td>$value->ice_num</td>\n";
                    echo "<td>$value->ice_title</td>\n";
                    echo "<td>$value->ice_price</td>\n";
                    echo "<td>$value->ice_time</td>\n";
                    echo "<td>$result</td>\n";
                    echo "</tr>";
                }
            }
            else
            {
                echo '<tr><td colspan="6" align="center"><strong>没有交易记录</strong></td></tr>';
            }
        ?>
        </tbody>
      </table>
    
    </div>
<?php
}



//vip订单
function vip_tracking_lists() {
	if(!is_user_logged_in()){
		exit;
	}
	global $wpdb;
	$user_info=wp_get_current_user();
	if(current_user_can('administrator'))
	{
		//统计数据
		$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->vip");
		$total_success = $wpdb->get_var("SELECT sum(ice_price) FROM $wpdb->vip");
	}
	else 
	{
		$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->vip where ice_user_id=".$user_info->ID);
		$total_success = $wpdb->get_var("SELECT sum(ice_price) FROM $wpdb->vip where ice_user_id=".$user_info->ID);
	}
	//分页计算
	$ice_perpage = 20;
	$pages = ceil($total_trade / $ice_perpage);
	$page=isset($_GET['p']) ?intval($_GET['p']) :1;
	$offset = $ice_perpage*($page-1);
	if(current_user_can('administrator'))
	{
		$list = $wpdb->get_results("SELECT * FROM $wpdb->vip order by ice_time DESC limit $offset,$ice_perpage");
	}
	else 
	{
		$list = $wpdb->get_results("SELECT * FROM $wpdb->vip where ice_user_id=".$user_info->ID." order by ice_time DESC limit $offset,$ice_perpage");
	}
	?>
	<div class="wrap">
		<h2>VIP开通记录</h2>
		<p><?php printf(('共有<strong>%s</strong>次开通VIP记录，总金额：<strong>%s</strong>元'), $total_trade, $total_success); ?></p>
		<table class="widefat">
			<thead>
				<tr>
					<th width="15%">用户ID</th>
					<th width="15%">VIP类型</th>
					<th width="5%">价格</th>
					<th width="15%">交易时间</th>			
				</tr>
			</thead>
			<tbody>
		<?php
			if($list) {
				foreach($list as $value)
				{
					$typeName=$value->ice_user_type==7 ?'包月' :($value->ice_user_type==8 ?'包季' : ($value->ice_user_type==10 ?'终身' : '包年'));
					echo "<tr>\n";
					echo "<td>".get_the_author_meta( 'user_login', $value->ice_user_id )."</td>\n";
					echo "<td>$typeName</td>\n";
					echo "<td>$value->ice_price</td>\n";
					echo "<td>$value->ice_time</td>\n";
					echo "</tr>";
				}
			}
			else
			{
				echo '<tr><td colspan="4" align="center"><strong>没有交易记录</strong></td></tr>';
			}
		?>
		</tbody>
		</table>
	
	</div>
<?php
}

//VIP会员服务
function vip_member_service() {
	if(!is_user_logged_in()){
		exit;
	}
	global $wpdb;
	?>
	<div class="wrap">
<?php

	if($_POST['Submit'] && $_POST['Submit']=='确认购买')
	{
		$userType=isset($_POST['userType']) && is_numeric($_POST['userType']) ?intval($_POST['userType']) :0;
		if($userType >6 && $userType < 11)
		{
			$okMoney=erphpGetUserOkMoney();
			$priceArr=array('7'=>'ciphp_month_price','8'=>'ciphp_quarter_price','9'=>'ciphp_year_price','10'=>'ciphp_life_price');
			$priceType=$priceArr[$userType];
			$price=get_option($priceType);
			if(empty($price) || $price<1)
			{
				showMsgNotice("此类型的会员价格错误，请稍候重试!");
			}
			elseif($okMoney < $price)
			{
				showMsgNotice("当前可用余额不足完成此次交易！请充值后重试!");
			}
			elseif($okMoney >=$price)
			{
				if(erphpSetUserMoneyXiaoFei($price))//扣钱
				{
					if(userPayMemberSetData($userType))
					{
						addVipLog($price, $userType);
						//写入提成
						$user_info=wp_get_current_user();
						$RefMoney=$wpdb->get_row("select * from ".$wpdb->users." where ID=".$user_info->ID);
						if($RefMoney->father_id > 0){
							addUserMoney($RefMoney->father_id,$price*get_option('ice_ali_money_ref')*0.01);
						}
						showMsgNotice("购买成功，您即可享受高级会员服务!",TRUE);
					}
					else
					{
						showMsgNotice("系统发生错误，请联系管理员!");
					}
				}
				else
				{
					showMsgNotice("系统发生错误，请稍候重试!");
				}
			}
			else
			{
				showMsgNotice("未定义的操作!");
			}
		}
		else
		{
			showMsgNotice("会员类型错误");
		}
	}
	/////////////////////////////////////////////////www.mobantu.com   82708210@qq.com
	$ciphp_life_price    = get_option('ciphp_life_price');
	$ciphp_year_price    = get_option('ciphp_year_price');
	$ciphp_quarter_price = get_option('ciphp_quarter_price');
	$ciphp_month_price  = get_option('ciphp_month_price');
	
		$okMoney=erphpGetUserOkMoney();//判断余额
		?>
	<form method="post" style="width: 100%; float: left;">
	
		<h2>购买VIP服务</h2>
		<table class="form-table">
			<tr>
				<td valign="top" width="30%"><strong>当前类型</strong><br /></td>
				<td><?php 
				$userTypeId=getUsreMemberType();
				if($userTypeId==7)
				{
					echo "包月会员";
				}
				elseif ($userTypeId==8)
				{
					echo "包季会员";
				}
				elseif ($userTypeId==9)
				{
					echo "包年会员";
				}
				elseif ($userTypeId==10)
				{
					echo "终身会员";
				}
				else 
				{
					echo '未购买任何会员服务';
				}
				?>,&nbsp;&nbsp;&nbsp;<?php if($userTypeId>6 && $userTypeId<10){?>到期时间：<?php echo $userTypeId>0 ?getUsreMemberTypeEndTime() :''?></td><?php }?>
			</tr>
			
			
			<tr>
				<td valign="top" width="30%"><strong>VIP会员类型</strong><br />
				</td>
				<td>
					<?php if($ciphp_life_price){?><input type="radio" id="userType" name="userType" value="10" checked />终身VIP会员 --- <?php echo $ciphp_life_price?><?php echo get_option('ice_name_alipay')?><br /> <?php }?>
					<?php if($ciphp_year_price){?><input type="radio" id="userType" name="userType" value="9" />包年VIP会员 --- <?php echo $ciphp_year_price?><?php echo get_option('ice_name_alipay')?><br /> <?php }?>
					<?php if($ciphp_quarter_price){?><input type="radio" id="userType" name="userType" value="8" />包季VIP会员 --- <?php echo $ciphp_quarter_price?><?php echo get_option('ice_name_alipay')?><br /><?php }?>
					<?php if($ciphp_month_price){?><input type="radio" id="userType" name="userType" value="7" />包月VIP会员 --- <?php echo $ciphp_month_price?><?php echo get_option('ice_name_alipay')?><?php }?>
				</td>
			</tr>
			<tr>
				<td valign="top" width="30%"><strong>可用余额</strong><br />
				</td>
				<td><?php echo sprintf("%.2f",$okMoney)?><?php echo get_option('ice_name_alipay')?>
				</td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" name="Submit" value="确认购买"
					onclick="return confirm('确认购买?')" class="button-primary" />
				</td>
			</tr>
			
			
		</table>
	</form>
	</div>
    <?php 
}


//我的资产
function my_property() {
	if(!is_user_logged_in()){
		exit;
	}
	global $wpdb;
	$user_Info   = wp_get_current_user();
	$userMoney=$wpdb->get_row("select * from ".$wpdb->iceinfo." where ice_user_id=".$user_Info->ID);
	if(!$userMoney)
	{
		$okMoney=0;
	}
	else 
	{
		$okMoney=$userMoney->ice_have_money - $userMoney->ice_get_money;
	}
	/////////////////////////////////////////////////www.mobantu.com   82708210@qq.com
	?>
	<div class="wrap">
	
			<h2>我的资产</h2>
			<table class="form-table">
				<tr>
					<td valign="top" width="30%"><strong>收入+充值+推广：</strong><br />
					</td>
					<td>
					 <?php echo sprintf("%.2f",$userMoney->ice_have_money)?><?php echo get_option('ice_name_alipay')?>
					</td>
				</tr>
				<tr>
					<td valign="top" width="30%"><strong>已消费：</strong><br />
					</td>
					<td>
					 <?php echo sprintf("%.2f",$userMoney->ice_get_money)?><?php echo get_option('ice_name_alipay')?>
					</td>
				</tr>
				<tr>
					<td valign="top" width="30%"><strong>可用金额：</strong><br />
					</td>
					<td>
					 <?php echo sprintf("%.2f",$okMoney)?><?php echo get_option('ice_name_alipay')?>
					</td>
				</tr>
		</table>

	</div>
<?php
}

//充值
function recharge_money() {
	global $wpdb;
	if(!is_user_logged_in())
	{
		exit;
	}

/////////////////////////////////////////////////www.mobantu.com   82708210@qq.com
	if($_POST && $_POST['paytype'])
	{
		$paytype=$wpdb->escape(intval($_POST['paytype']));
		$doo = 1;
		
		if(isset($_POST['paytype']) && $paytype==1)
		{
			$url=constant("erphpdown")."payment/alipay.php?ice_money=".$wpdb->escape($_POST['ice_money']);
		}
		elseif(isset($_POST['paytype']) && $paytype==5)
		{
			$url=constant("erphpdown")."payment/f2fpay.php?ice_money=".esc_sql($_POST['ice_money']);
		}
		elseif(isset($_POST['paytype']) && $paytype==4)
		{
			$url=constant("erphpdown")."payment/weixin.php?ice_money=".$wpdb->escape($_POST['ice_money']);
		}
		elseif(isset($_POST['paytype']) && $paytype==7)
		{
			$url=constant("erphpdown")."payment/paypy.php?ice_money=".esc_sql($_POST['ice_money']);
		}
		elseif(isset($_POST['paytype']) && $paytype==8)
		{
			$url=constant("erphpdown")."payment/paypy.php?ice_money=".esc_sql($_POST['ice_money'])."&type=alipay";
		}
	    elseif(isset($_POST['paytype']) && $paytype==18)
		{
			$url=constant("erphpdown")."payment/xhpay3.php?ice_money=".esc_sql($_POST['ice_money'])."&type=2";
		}
		elseif(isset($_POST['paytype']) && $paytype==17)
		{
			$url=constant("erphpdown")."payment/xhpay3.php?ice_money=".esc_sql($_POST['ice_money'])."&type=1";
		}
	    elseif(isset($_POST['paytype']) && $paytype==13)
	    {
	        $url=constant("erphpdown")."payment/codepay.php?ice_money=".esc_sql($_POST['ice_money'])."&type=1";
	    }elseif(isset($_POST['paytype']) && $paytype==14)
	    {
	        $url=constant("erphpdown")."payment/codepay.php?ice_money=".esc_sql($_POST['ice_money'])."&type=3";
	    }elseif(isset($_POST['paytype']) && $paytype==15)
	    {
	        $url=constant("erphpdown")."payment/codepay.php?ice_money=".esc_sql($_POST['ice_money'])."&type=2";
	    }
		elseif(isset($_POST['paytype']) && $paytype==6)
		{
			$doo = 0;
			$result = checkDoCardResult($wpdb->escape($_POST['ice_money']),$wpdb->escape($_POST['password']));
			if($result == '0') echo "此充值卡已被使用，请重新换张！";
			if($result == '4') echo "系统出错，出现问题，请联系管理员！";
			if($result == '1') echo "充值成功！";
		}
		else
		{
			$url=constant("erphpdown")."payment/paypal.php?ice_money=".$wpdb->escape($_POST['ice_money']);
		}
		if($doo)
			echo "<script>location.href='".$url."'</script>";
		exit;
	}
	?>
	<div class="wrap">
	<script src="http://libs.baidu.com/jquery/1.9.0/jquery.js"></script>
	<script type="text/javascript">
	jQuery(document).ready(function() {
		var c = jQuery("input[name='paytype']:checked").val();
		if(c == 6){jQuery("#cpass").css("display","");jQuery("#cname").html("充值卡号");}
		else{jQuery("#cpass").css("display","none");jQuery("#cname").html("充值金额");}
	});
	
	function checkFm()
	{
		if(document.getElementById("ice_money").value=="")
		{
			alert('请输入金额');
			return false;
		}
	}
	
	function checkCard()
	{
		var c = jQuery("input[name='paytype']:checked").val();
		if(c == 6){jQuery("#cpass").css("display","");jQuery("#cname").html("充值卡号");}
		else{jQuery("#cpass").css("display","none");jQuery("#cname").html("充值金额");}
	}
	</script>
	<form action="" method="post" onsubmit="return checkFm();">
	
			<h2>在线充值</h2>
			<table class="form-table">
				<tr>
					<td valign="top"><strong>充值比例</strong><br />
					</td>
					<td>
						<font color="#006600">1元 = <?php echo get_option('ice_proportion_alipay') ?><?php echo get_option('ice_name_alipay') ?></font>
					</td>
				</tr>
				 <tr>
					<td valign="top"><strong><span id="cname">充值金额</span></strong><br />
					</td>
					<td>
					<input type="text" id="ice_money" name="ice_money" maxlength="50" size="50" />
					</td>
				</tr>
				<tr id="cpass" style="display:none">
					<td valign="top"><strong>充值卡密</strong><br />
					</td>
					<td>
					<input type="text" id="password" name="password" maxlength="50" size="50" placeholder="充值卡密码"/>
					</td>
				</tr>
							<tr>
					<td valign="top"><strong>充值方式</strong><br />
					</td>
					<td>
					<?php if(plugin_check_card()){?>
					 <input type="radio" id="paytype6" class="paytype" name="paytype" value="6" checked onclick="checkCard()"/>充值卡
					 <?php }?>
                     <?php if(get_option('ice_weixin_mchid')){?> 
					<input type="radio" id="paytype4" class="paytype" checked name="paytype" value="4" />微信&nbsp;
					<?php }?>
					<?php if(get_option('ice_ali_partner')){?> 
					<input type="radio" id="paytype1" class="paytype" checked name="paytype" value="1"  />支付宝&nbsp;
					<?php }?>
					<?php if(get_option('erphpdown_f2fpay_id') && !erphpdown_is_weixin()){?> 
					<input type="radio" id="paytype5" class="paytype" checked name="paytype" value="5" onclick="checkCard()" />支付宝&nbsp;
					<?php }?>
					<?php if(get_option('ice_payapl_api_uid')){?> 
					<input type="radio" id="paytype2" class="paytype" name="paytype" value="2" onclick="checkCard()"/>PayPal($美元)汇率：
					 (<?php echo get_option('ice_payapl_api_rmb')?>)&nbsp;  
					 <?php }?> 
	                <?php if(get_option('erphpdown_xhpay_appid31')){?> 
						<input type="radio" id="paytype18" class="paytype" name="paytype" value="18" checked onclick="checkCard()"/>微信&nbsp;      
					<?php }?>
					<?php if(get_option('erphpdown_xhpay_appid32')){?> 
						<input type="radio" id="paytype17" class="paytype" name="paytype" value="17" checked onclick="checkCard()"/>支付宝&nbsp;      
					<?php }?>
					<?php if(get_option('erphpdown_paypy_key')){?> 
						<input type="radio" id="paytype7" class="paytype" name="paytype" value="7" checked onclick="checkCard()"/>微信&nbsp;    
						<input type="radio" id="paytype8" class="paytype" name="paytype" value="8" checked onclick="checkCard()"/>支付宝&nbsp;  
					<?php }?>
	                <?php if(get_option('erphpdown_codepay_appid')){?> 
	                <input type="radio" id="paytype13" class="paytype" name="paytype" value="13" checked onclick="checkCard()"/>支付宝&nbsp;
	                <input type="radio" id="paytype14" class="paytype" name="paytype" value="14" onclick="checkCard()"/>微信&nbsp;
	                <input type="radio" id="paytype15" class="paytype" name="paytype" value="15" onclick="checkCard()"/>QQ钱包&nbsp;        
	                <?php }?>
					</td>
				</tr>
	<tr>
			<td>
				<input type="submit" name="Submit" value="充值" class="button-primary" onclick="return confirm('确认充值?');"/>
				
			</td>
	
			</tr> </table>
	
	</form>

	</div>
<?php
}
//取现申请
function cash_application() {
	if(!is_user_logged_in()){
		exit;
	}
	global $wpdb;
	$fee=get_option("ice_ali_money_site");
	$fee=isset($fee) ?$fee :100;
	$user_Info   = wp_get_current_user();
	$userMoney=$wpdb->get_row("select * from ".$wpdb->iceinfo." where ice_user_id=".$user_Info->ID);
	/////////////////////////////////////////////////www.mobantu.com   82708210@qq.com
	if(!$userMoney)
	{
		$okMoney=0;
	}
	else 
	{
		$okMoney=$userMoney->ice_have_money - $userMoney->ice_get_money;
	
	}
	if($_POST['Submit']) {
		$getinfo=$wpdb->get_row("select * from ".$wpdb->iceget." where ice_user_id=".$user_Info->ID." and ice_success=0");
		if($getinfo)
		{
			wp_die('您已经申请提现，请等待管理员处理!');
		}
		$check7day=$wpdb->get_row("select * from ".$wpdb->iceget." where ice_user_id=".$user_Info->ID."  order by ice_id desc");
		if($check7day && (time()-strtotime($check7day->ice_time) < 7*24*3600))
		{
			wp_die('您好，7天内只能申请一次提现!上次申请提现时间：'.$check7day->ice_time);
		}
		$ice_alipay = $wpdb->escape($_POST['ice_alipay']);
		$ice_name   = $wpdb->escape($_POST['ice_name']);
		$ice_money  = isset($_POST['ice_money']) && is_numeric($_POST['ice_money']) ?$wpdb->escape($_POST['ice_money']) :0;
		if($ice_money<get_option('ice_ali_money_limit'))
		{
			echo "<font color='red'>提现金额必须大于等于".get_option('ice_ali_money_limit').get_option('ice_name_alipay')."</font>";
		}
		elseif(empty($ice_name) || empty($ice_alipay))
		{
			echo "<font color='red'>请输入支付宝帐号和姓名</font>";
		}
		elseif($ice_money > $okMoney)
		{
			echo "<font color='red'>提现金额大于可提现金额".$okMoney."</font>";
		}
		else
		{
	
			$sql="insert into ".$wpdb->iceget."(ice_money,ice_user_id,ice_time,ice_success,ice_success_time,ice_note,ice_name,ice_alipay)values
				('".$ice_money."','".$user_Info->ID."','".date("Y-m-d H:i:s")."',0,'".date("Y-m-d H:i:s")."','','$ice_name','$ice_alipay')";
			if($wpdb->query($sql))
			{
				addUserMoney($user_Info->ID, '-'.$ice_money);
				echo "<font color='red'>申请成功！等待管理员处理!</font>";
			}
			else
			{
				echo "<font color='red'>系统错误请稍后重试</font>";
			}
		}
	}
	$userAli=$wpdb->get_row("select * from ".$wpdb->iceget." where ice_user_id=".$user_Info->ID);
	
	
	?>
	<div class="wrap clearfix">
	<form method="post" action="?action=cash_application" style="width:70%;float:left;">
	
			<h2>提现申请</h2>
		<p style="color: red;">注意提现支付宝设置后不可更改</p>
			<table class="form-table">
				<tr>
					<td valign="top" width="30%">支付宝帐号<br />
					</td>
					<td>
						<?php if(!$userAli){?>
							<input type="text" id="ice_alipay" name="ice_alipay" maxlength="50" size="50" />
						<?php }else{
							echo $userAli->ice_alipay;
							echo '<input type="hidden" id="ice_alipay" name="ice_alipay" value="'.$userAli->ice_alipay.'"/>';
						}?>
	
					</td>
				</tr>
				<tr>
					<td valign="top" width="30%">支付宝姓名<br />
					</td>
					<td>
						<?php if(!$userAli){?>
							<input type="text" id="ice_name" name="ice_name" maxlength="50" size="50" />
						<?php }else{
							echo $userAli->ice_name;
							echo '<input type="hidden" id="ice_name" name="ice_name" value="'.$userAli->ice_name.'"/>';
						}?>
	
					</td>
				</tr>
				 <tr>
					<td valign="top" width="30%">手续费<br />
					</td>
					<td>
					<?php echo get_option("ice_ali_money_site")?>%
					</td>
				</tr>
				<tr>
					<td >提现金额<br />
					</td>
					<td>
					<input type="text" id="ice_money" name="ice_money" maxlength="50" size="50" />
					</td>				
				</tr>
	<tr valign="top" width="30%"><td>总金额:<br /></td>
				<td><?php echo sprintf("%.2f",$okMoney)?><?php echo get_option('ice_name_alipay')?><!--最多可提现：￥<?php echo sprintf("%.2f",$okMoney*(100-$fee)/100)?>--></td>
				</tr>
		</table>
			<br /> <br />
			<table> <tr>
			<td><p class="submit">
				<input type="submit" name="Submit" value="提交申请" class="button-primary"/>
				</p>
			</td>
	
			</tr> </table>
	
	</form>
	</div>
<?php
}

//取现列表
if(erphpdown_lock_url(substr(plugins_url('', __FILE__),'-18','-9'),'cvujz') != 'gxLmUkVVK9I8u3reMFrX8Vc'){
	exit();}
function cash_application_lists() {
	global $wpdb;
	if(!is_user_logged_in()){
		exit;
	}
	$action=isset($_GET['action']) ?$_GET['action'] :false;
	$id=isset($_GET['id']) && is_numeric($_GET['id']) ?intval($_GET['id']) :0;
	if($action=="save" && current_user_can('administrator'))
	{
		$result = isset($_POST['result']) && is_numeric($_POST['result']) ?intval($_POST['result']) :0;
		$note   = isset($_POST['note']) ?$_POST['note'] :'';
		$ok=$wpdb->query("update ".$wpdb->iceget." set ice_success=".$result.",ice_note='".$note."',ice_success_time='".date("Y-m-d H:i:s")."' where ice_id=".$id);
		if(!$ok)
		{
			echo "<font color='red'>系统更错处理失败</font>";
		}
		else 
		{
			$info=$wpdb->get_row("select * from ".$wpdb->iceget." where ice_id=".$id);
			$a=$wpdb->query("update ".$wpdb->iceinfo." set ice_get_money=ice_get_money+".$info->ice_money.' where ice_user_id='.$info->ice_user_id);
			if(!$a)
			{
				wp_die('扣除用户可提现额度失败!');
			}
			else 
			{
				echo "<font color='green'>更新成功!</font>";
			}
		}
		unset($id);
	}
	if($id && current_user_can('administrator'))
	{
		$info=$wpdb->get_row("select * from ".$wpdb->iceget." where ice_id=".$id);
		if(!$info->ice_id)
		{
			echo "<font color='red'>错误的ID</font>";
			exit;
		}
		$userMoney=$wpdb->get_row("select * from ".$wpdb->iceinfo." where ice_user_id=".$info->ice_user_id);
		?>
		<div class="wrap">
	<form method="post" action="<?php echo admin_url('admin.php?page='.plugin_basename(__FILE__).'&action=save&id='.$id); ?>" style="width:70%;float:left;">
	
			<h2>处理提现申请</h2>
			<table class="form-table">
				<tr>
					<td valign="top" width="30%">支付宝帐号<br />
					</td>
					<td><?php echo $info->ice_alipay?></td>
				</tr>
				<tr>
					<td valign="top" width="30%">支付宝姓名<br />
					</td>
					<td><?php echo $info->ice_name?></td>
				</tr>
				<tr>
					<td valign="top" width="30%">提现金额<br />
					</td>
					<td><?php echo $info->ice_money?>(可提现金额:<?php echo $userMoney->ice_have_money - $userMoney->ice_get_money ?><?php echo get_option('ice_name_alipay')?>)
					</td>
				</tr>
				<tr>
					<td valign="top" width="30%">处理结果<br />
					</td>
					<td><input type="radio" name="result" id="res1" value="1" <?php if($info->ice_success==1) echo "checked";?>/>已支付,
					<input type="radio" name="result" id="res1" value="0" <?php if($info->ice_success==0) echo "checked";?>/>未处理
					</td>
				</tr>
				<tr>
					<td valign="top" width="30%">手续费<br />
					</td>
					<td><?php echo  $info->ice_money*(get_option("ice_ali_money_site"))/100?>
					</td>
				</tr>
				<tr>
					<td valign="top" width="30%">实际转账<br />
					</td>
					<td><?php echo  $info->ice_money*(100-get_option("ice_ali_money_site"))/100?>
					</td>
				</tr>
				<tr>
					<td valign="top" width="30%">处理时间<br />
					</td>
					<td><?php echo $info->ice_success_time?>
					</td>
				</tr>
				<tr>
					<td valign="top" width="30%">备注<br />
					</td>
					<td>
					<input type="text" name="note" id="note" value="<?php echo $info->ice_note ?>" />
					</td>
				</tr>
		</table>
			<br /> <br />
			<table> <tr>
			<td><p class="submit">
				<input type="submit" name="Submit" value="确认提交" class="button-primary"/>
				</p>
			</td>
	
			</tr> </table>
	
	</form>
	</div>
	<?php
	exit;
}
//统计数据
$total_money=0;
if(current_user_can('administrator'))
{
	$sql     = "SELECT SUM(ice_money) FROM $wpdb->iceget";
	$listSql = "SELECT * FROM $wpdb->iceget order by ice_time DESC";
}
else 
{
	$user_Info = wp_get_current_user();
	$sql       = "SELECT SUM(ice_money) FROM $wpdb->iceget WHERE ice_user_id=".$user_Info->ID;
	$listSql   = "SELECT * FROM $wpdb->iceget where ice_user_id=".$user_Info->ID." order by ice_time DESC";
}
$total_money = $wpdb->get_var($sql);
$list        = $wpdb->get_results($listSql);

$lv=get_option("ice_ali_money_site");
?>
<div class="wrap">
	<h2>提现列表</h2>
	<p><?php printf(("共申请提现<strong>%.2f</strong>"), $total_money); ?>&nbsp;&nbsp;&nbsp;&nbsp;
	<?php $user_Info   = wp_get_current_user();
$userMoney=$wpdb->get_row("select * from ".$wpdb->iceinfo." where ice_user_id=".$user_Info->ID);

if(!$userMoney)
{
	$okMoney=0;
}
else 
{
	$okMoney=$userMoney->ice_have_money - $userMoney->ice_get_money;

} 
if($okMoney >= get_option('ice_ali_money_limit'))
{
?>
	 
	 <?php } else {?>
	 余额满￥<?php echo get_option('ice_ali_money_limit'); ?>方可提现！
	 <?php } ?>
	 </p>
	<table class="widefat">
		<thead>
			<tr>
				<th width="15%">申请时间</th>
				<th width="15%">申请金额</th>
				<th width="15%">到帐金额</th>
				<th width="17%">支付状态</th>
				<th width="15%">备注</th>
				<?php if(current_user_can('administrator')){?><th>管理</th><?php }?>		
			</tr>
		</thead>
		<tbody>
	<?php
		if($list) {
			foreach($list as $value)
			{
				$result=$value->ice_success==1?'已支付':'--';
				echo "<tr>\n";
				echo "<td>$value->ice_time</td>\n";
				echo "<td>$value->ice_money</td>\n";
				echo "<td>".sprintf("%.2f",(((100-$lv)*$value->ice_money)/100))."</td>\n";
				echo "<td>$result</td>\n";
				echo "<td>$value->ice_note</td>\n";
				if(current_user_can('administrator')){echo "<td><a href='".admin_url('admin.php?page=erphpdown/alipay-money-list.php&id='.$value->ice_id)."'>操作</a></td>";}
				echo "</tr>";
			}
		}
		else
		{
			echo '<tr><td colspan="5" align="center"><strong>没有交易记录</strong></td></tr>';
		}
	?>
	</tbody>
	</table>
</div>
<?php
}