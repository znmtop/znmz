<div class="wrap">
	<?php
	if ( !defined('ABSPATH') ) {exit;}
	date_default_timezone_set("PRC");
	if(isset($_POST['Submit']) && $_POST['Submit']=='确认赠送')
	{
	//check
		$user_info=get_user_by('login', $wpdb->escape($_POST['vipusername']));
		$uid=$user_info->ID;
		$userType=isset($_POST['userType']) && is_numeric($_POST['userType']) ?intval($_POST['userType']) :0;
		if($userType >5 && $userType < 11 && $uid)
		{
			$priceArr=array('6'=>'ciphp_day_price','7'=>'ciphp_month_price','8'=>'ciphp_quarter_price','9'=>'ciphp_year_price','10'=>'ciphp_life_price');
			$priceType=$priceArr[$userType];
			$price=get_option($priceType);
			if(!$price){
				echo '<div class="error settings-error"><p>此类型的会员价格错误，请稍候重试！</p></div>';
			}else{
				addUserMoney($uid,'0'); // 添加记录
				if(userSetMemberSetData($userType,$uid))
				{
					addVipLogByAdmin($price, $userType, $uid);
					echo '<div class="updated settings-error"><p>赠送VIP成功！</p></div>';
				}
				else
				{
					echo '<div class="error settings-error"><p>赠送VIP失败！</p></div>';
				}
			}
			
		}
		else
		{
			echo '<div class="error settings-error"><p>会员类型错误！</p></div>';
		}
}

$ciphp_life_price    = get_option('ciphp_life_price');
$ciphp_year_price    = get_option('ciphp_year_price');
$ciphp_quarter_price = get_option('ciphp_quarter_price');
$ciphp_month_price  = get_option('ciphp_month_price');
$ciphp_day_price  = get_option('ciphp_day_price');
$erphp_life_name    = get_option('erphp_life_name')?'('.get_option('erphp_life_name').')':'';
$erphp_year_name    = get_option('erphp_year_name')?'('.get_option('erphp_year_name').')':'';
$erphp_quarter_name = get_option('erphp_quarter_name')?'('.get_option('erphp_quarter_name').')':'';
$erphp_month_name  = get_option('erphp_month_name')?'('.get_option('erphp_month_name').')':'';
$erphp_day_name  = get_option('erphp_day_name')?'('.get_option('erphp_day_name').')':'';

?>
<form method="post"
action="<?php echo admin_url('admin.php?page='.plugin_basename(__FILE__)); ?>"
style="width: 70%; float: left;">

<h2>后台赠送VIP服务</h2>
<table class="form-table">
	
	<tr>
		<td valign="top" width="30%"><strong>VIP类型</strong><br />
		</td>
		<td>
			<?php if($ciphp_life_price){?><input type="radio" id="userType" name="userType" value="10" checked />终身VIP<?php echo $erphp_life_name;?> --- <?php echo $ciphp_life_price?><?php echo get_option('ice_name_alipay')?><br /><?php }?>
			<?php if($ciphp_year_price){?><input type="radio" id="userType" name="userType" value="9" checked/>包年VIP<?php echo $erphp_year_name;?> --- <?php echo $ciphp_year_price?><?php echo get_option('ice_name_alipay')?><br /> <?php }?>
			<?php if($ciphp_quarter_price){?><input type="radio" id="userType" name="userType" value="8" checked/>包季VIP<?php echo $erphp_quarter_name;?> --- <?php echo $ciphp_quarter_price?><?php echo get_option('ice_name_alipay')?><br /><?php }?>
			<?php if($ciphp_month_price){?><input type="radio" id="userType" name="userType" value="7" checked/>包月VIP<?php echo $erphp_month_name;?> --- <?php echo $ciphp_month_price?><?php echo get_option('ice_name_alipay')?><br /><?php }?>
			<?php if($ciphp_day_price){?><input type="radio" id="userType" name="userType" value="6" checked/>体验VIP<?php echo $erphp_day_name;?> --- <?php echo $ciphp_day_price?><?php echo get_option('ice_name_alipay')?><?php }?>
		</td>
	</tr>
	<tr>
		<td valign="top" width="30%"><strong>被赠送用户登录名</strong><br />
		</td>
		<td><input type="text" name="vipusername">
		</td>
	</tr>
	<tr>
		<td colspan="2"><input type="submit" name="Submit" value="确认赠送"
			onclick="return confirm('确认赠送?')" class="button-primary" />
		</td>
	</tr>
</table>
</form>
</div>