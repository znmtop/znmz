<?php
if ( !defined('ABSPATH') ) {exit;}
if(!is_user_logged_in())
{
	wp_die('请登录系统');
}
?>
<?php 
/////////////////////////////////////////////////www.mobantu.com   82708210@qq.com
if($_POST)
{
	$paytype=esc_sql(intval($_POST['paytype']));
	$doo = 1;

	if(isset($_POST['paytype']) && $paytype==1)
	{
		$url=constant("erphpdown")."payment/alipay.php?ice_money=".esc_sql($_POST['ice_money']);
	}
	elseif(isset($_POST['paytype']) && $paytype==5)
	{
		$url=constant("erphpdown")."payment/f2fpay.php?ice_money=".esc_sql($_POST['ice_money']);
	}
	elseif(isset($_POST['paytype']) && $paytype==2)
	{
		$url=constant("erphpdown")."payment/paypal.php?ice_money=".esc_sql($_POST['ice_money']);
	}
	elseif(isset($_POST['paytype']) && $paytype==4)
	{
		if(erphpdown_is_weixin() && get_option('ice_weixin_app')){
			$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.get_option('ice_weixin_appid').'&redirect_uri='.urlencode(constant("erphpdown")).'payment%2Fweixin.php%3Fice_money%3D'.esc_sql($_POST['ice_money']).'&response_type=code&scope=snsapi_base&state=STATE&connect_redirect=1#wechat_redirect';
		}else{
			$url=constant("erphpdown")."payment/weixin.php?ice_money=".esc_sql($_POST['ice_money']);
		}
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
		$result = checkDoCardResult(esc_sql($_POST['ice_money']),esc_sql($_POST['password']));
		if($result == '0') echo "此充值卡已被使用，请重新换张！";
		if($result == '4') echo "系统出错，出现问题，请联系管理员！";
		if($result == '1') echo "充值成功！";
	}else{
		
	}
	if($doo)
		echo "<script>location.href='".$url."'</script>";
	exit;
}
?>
<div class="wrap">
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
				alert('请输入金额或卡号');
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
	<form action="" method="post" <?php if(!erphpdown_is_weixin()){?>target="_blank"<?php }?>>
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
					<input type="text" id="ice_money" name="ice_money" maxlength="50" size="50" required="" />
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
						<input type="radio" id="paytype4" class="paytype" checked name="paytype" value="4" checked onclick="checkCard()" />微信&nbsp;
					<?php }?>
					<?php if(get_option('ice_ali_partner') && !erphpdown_is_weixin()){?> 
						<input type="radio" id="paytype1" class="paytype" checked name="paytype" value="1" checked onclick="checkCard()" />支付宝&nbsp;
					<?php }?>
					<?php if(get_option('erphpdown_f2fpay_id') && !erphpdown_is_weixin()){?> 
						<input type="radio" id="paytype5" class="paytype" checked name="paytype" value="5" checked onclick="checkCard()" />支付宝&nbsp;
					<?php }?>
					<?php if(get_option('ice_payapl_api_uid')){?> 
						<input type="radio" id="paytype2" class="paytype" name="paytype" value="2" checked onclick="checkCard()"/>PayPal($美元)汇率：
						(<?php echo get_option('ice_payapl_api_rmb')?>)&nbsp;  
					<?php }?> 
					<?php if(get_option('erphpdown_xhpay_appid31')){?> 
						<input type="radio" id="paytype18" class="paytype" name="paytype" value="18" checked onclick="checkCard()"/>微信&nbsp;      
					<?php }?>
					<?php if(get_option('erphpdown_xhpay_appid32')){?> 
						<input type="radio" id="paytype17" class="paytype" name="paytype" value="17" checked onclick="checkCard()"/>支付宝&nbsp;      
					<?php }?>
					<?php if(get_option('erphpdown_paypy_key')){?> 
						<?php if(!get_option('erphpdown_paypy_wxpay')){?><input type="radio" id="paytype7" class="paytype" name="paytype" value="7" checked onclick="checkCard()"/>微信&nbsp;<?php }?>    
						<?php if(!get_option('erphpdown_paypy_alipay')){?><input type="radio" id="paytype8" class="paytype" name="paytype" value="8" checked onclick="checkCard()"/>支付宝&nbsp;<?php }?>  
					<?php }?>
					<?php if(get_option('erphpdown_codepay_appid')){?> 
						<input type="radio" id="paytype13" class="paytype" name="paytype" value="13" checked onclick="checkCard()"/>支付宝&nbsp;
						<input type="radio" id="paytype14" class="paytype" name="paytype" value="14" onclick="checkCard()"/>微信&nbsp;
						<input type="radio" id="paytype15" class="paytype" name="paytype" value="15" onclick="checkCard()"/>QQ钱包&nbsp;        
					<?php }?>
				</td>
			</tr>
		</table>
		<br /> 
		<table> <tr>
			<td><p class="submit">
				<input type="submit" name="Submit" value="充值" class="button-primary" />
			</p>
		</td>

	</tr> </table>

</form>

</div>
