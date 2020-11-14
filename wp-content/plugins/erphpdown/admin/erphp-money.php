<?php
if ( !defined('ABSPATH') ) {exit;}
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
if(isset($_POST['Submit'])) {
	$getinfo=$wpdb->get_row("select * from ".$wpdb->iceget." where ice_user_id=".$user_Info->ID." and ice_success=0");
	$ice_alipay = $wpdb->escape($_POST['ice_alipay']);
	$ice_name   = $wpdb->escape($_POST['ice_name']);
	$ice_money  = isset($_POST['ice_money']) && is_numeric($_POST['ice_money']) ?$wpdb->escape($_POST['ice_money']) :0;
	if($ice_money<get_option('ice_ali_money_limit'))
	{
		echo '<div class="updated settings-error"><p>提现金额至少得满'.get_option('ice_ali_money_limit').get_option('ice_name_alipay').'</p></div>';
	}
	elseif(empty($ice_name) || empty($ice_alipay))
	{
		echo '<div class="updated settings-error"><p>请输入支付宝帐号和姓名！</p></div>';
	}
	elseif($ice_money > $okMoney)
	{
		echo '<div class="updated settings-error"><p>提现金额大于可提现金额'.$okMoney.'</p></div>';
	}
	else
	{

		$sql="insert into ".$wpdb->iceget."(ice_money,ice_user_id,ice_time,ice_success,ice_success_time,ice_note,ice_name,ice_alipay)values
		('".$ice_money."','".$user_Info->ID."','".date("Y-m-d H:i:s")."',0,'".date("Y-m-d H:i:s")."','','$ice_name','$ice_alipay')";
		if($wpdb->query($sql))
		{	
			addUserMoney($user_Info->ID, '-'.$ice_money);
			echo '<div class="updated settings-error"><p>申请成功！等待管理员处理！</p></div>';
		}
		else
		{
			echo '<div class="updated settings-error"><p>系统错误请稍后重试！</p></div>';
		}
	}
}
$userAli=$wpdb->get_row("select * from ".$wpdb->iceget." where ice_user_id=".$user_Info->ID);


?>
<div class="wrap">
	<form method="post" action="<?php echo admin_url('admin.php?page='.plugin_basename(__FILE__)); ?>" style="width:70%;float:left;">

		<h1>提现申请</h1>
		<table class="form-table">
			<tr>
				<th valign="top">支付宝帐号</th>
				<td>
					<?php if(!$userAli){?>
						<input type="text" id="ice_alipay" name="ice_alipay" maxlength="50" size="50" />
					<?php }else{
						echo '<input type="text" id="ice_alipay" name="ice_alipay" value="'.$userAli->ice_alipay.'"/>';
					}?>

				</td>
			</tr>
			<tr>
				<th valign="top">支付宝姓名</th>
				<td>
					<?php if(!$userAli){?>
						<input type="text" id="ice_name" name="ice_name" maxlength="50" size="50" />
					<?php }else{
						echo '<input type="text" id="ice_name" name="ice_name" value="'.$userAli->ice_name.'"/>';
					}?>

				</td>
			</tr>
			<tr>
				<th valign="top">手续费</th>
				<td>
					<?php echo get_option("ice_ali_money_site")?>%
				</td>
			</tr>
			<tr>
				<th valign="top">提现比例</th>
				<td>
					<?php echo get_option('ice_proportion_alipay');?><?php echo get_option('ice_name_alipay');?> = 1元
				</td>
			</tr>
			<tr>
				<th valign="top">提现<?php echo get_option('ice_name_alipay');?></th>
				<td>
					<input type="text" id="ice_money" name="ice_money" maxlength="50" size="50" />(总<?php echo get_option('ice_name_alipay');?>：<?php echo sprintf("%.2f",$okMoney)?>)
				</td>
			</tr>
		</table>
		<br /> <br />
		<table> <tr>
			<td><p class="submit">
				<input type="submit" name="Submit" value="确认申请" class="button-primary"/>
			</p>
		</td>

	</tr> </table>

</form>
</div>