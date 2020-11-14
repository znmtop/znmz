<?php
if ( !defined('ABSPATH') ) {exit;}
date_default_timezone_set("PRC");
if(!is_user_logged_in())
{
	wp_die('请登录系统');
}
?>
<?php 
if($_POST)
{
	$money=$wpdb->escape($_POST['ice_money']);
	$user_name=$wpdb->escape($_POST['user_id']);
	$user_info=get_user_by('login', $user_name);
	if($user_info)
	{
		$user_id=$user_info->ID;
		if(addUserMoney($user_id, $money))
		{
			$sql="INSERT INTO $wpdb->icemoney (ice_money,ice_num,ice_user_id,ice_time,ice_success,ice_note,ice_success_time,ice_alipay)
			VALUES ('$money','".date("ymdhis").mt_rand(100,999).mt_rand(100,999).mt_rand(100,999)."','".$user_id."','".date("Y-m-d H:i:s")."',1,'1','".date("Y-m-d H:i:s")."','')";
			$wpdb->query($sql);
		}
		if($money > 0){echo "<div class='updated settings-error'><p>充值成功！</p></div>";
	}elseif($money < 0){
		echo "<div class='updated settings-error'><p>扣钱成功！</p></div>";
	}


}
else
{
	echo "<div class='error settings-error'><p>用户不存在！</p></div>";
}
}
?>
<div class="wrap">
	<script type="text/javascript">
		function checkFm()
		{
			if(document.getElementById("ice_money").value=="")
			{
				alert('请输入金额');
				return false;
			}

		}
	</script>
	<form action="" method="post" onsubmit="return checkFm();">

		<h2>给用户充值/扣钱</h2>
		<table class="form-table">
			<tr>
				<td valign="top" width="30%"><strong>充值金额</strong><br />
				</td>
				<td>
					<input type="text" id="ice_money" name="ice_money" maxlength="50" size="50" /><?php echo get_option('ice_name_alipay');?>
					<p>请输入一个整数，负数为扣钱</p>
				</td>
			</tr>
			<tr>
				<td valign="top" width="30%"><strong>用户名</strong><br />
				</td>
				<td>
					<input type="text" id="user_id" name="user_id" maxlength="50" size="50" />
				</td>
			</tr>
		</table>
		<br /> <br />
		<table> <tr>
			<td><p class="submit">
				<input type="submit" name="Submit" value="充值/扣钱" class="button-primary" onclick="return confirm('确认此操作?');"/>
			</p>
		</td>

	</tr> </table>

</form>
</div>