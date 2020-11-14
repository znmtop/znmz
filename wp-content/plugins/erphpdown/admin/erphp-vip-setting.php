<div class="wrap">
	<?php
	if ( !defined('ABSPATH') ) {exit;}
	if(isset($_POST['Submit']) && current_user_can('administrator') && $_POST['Submit']=='保存设置'){
		
		update_option('ciphp_life_price', $_POST['life_price']);
		update_option('ciphp_year_price', $_POST['year_price']);
		update_option('ciphp_quarter_price', $_POST['quarter_price']);
		update_option('ciphp_month_price', $_POST['month_price']);
		update_option('ciphp_day_price', $_POST['day_price']);
		update_option('erphp_life_times', $_POST['life_times']);
		update_option('erphp_year_times', $_POST['year_times']);
		update_option('erphp_quarter_times', $_POST['quarter_times']);
		update_option('erphp_month_times', $_POST['month_times']);
		update_option('erphp_day_times', $_POST['day_times']);
		update_option('erphp_reg_times', $_POST['reg_times']);
		update_option('erphp_life_name', $_POST['life_name']);
		update_option('erphp_year_name', $_POST['year_name']);
		update_option('erphp_quarter_name', $_POST['quarter_name']);
		update_option('erphp_month_name', $_POST['month_name']);
		update_option('erphp_day_name', $_POST['day_name']);
		update_option('erphp_vip_name', $_POST['vip_name']);
		echo'<div class="updated settings-error"><p>更新成功！</p></div>';

	}
/////////////////////////////////////////////////www.mobantu.com   82708210@qq.com
	$erphp_life_price    = get_option('ciphp_life_price');
	$erphp_year_price    = get_option('ciphp_year_price');
	$erphp_quarter_price = get_option('ciphp_quarter_price');
	$erphp_month_price  = get_option('ciphp_month_price');
	$erphp_day_price  = get_option('ciphp_day_price');
	$erphp_life_times    = get_option('erphp_life_times');
	$erphp_year_times    = get_option('erphp_year_times');
	$erphp_quarter_times = get_option('erphp_quarter_times');
	$erphp_month_times  = get_option('erphp_month_times');
	$erphp_day_times  = get_option('erphp_day_times');
	$erphp_reg_times  = get_option('erphp_reg_times');
	$erphp_life_name    = get_option('erphp_life_name');
	$erphp_year_name    = get_option('erphp_year_name');
	$erphp_quarter_name = get_option('erphp_quarter_name');
	$erphp_month_name  = get_option('erphp_month_name');
	$erphp_day_name  = get_option('erphp_day_name');
	$erphp_vip_name  = get_option('erphp_vip_name');
	if(current_user_can('administrator')){
		?>

		<form method="post" action="<?php echo admin_url('admin.php?page='.plugin_basename(__FILE__)); ?>">

			<h2>VIP价格设置</h2>
			<p>如不需要某个VIP类型，可留空价格</p>
			<table class="form-table">
				<tr>
					<th valign="top" width="30%"><strong>终身VIP</strong></th>
					<td><input type="number" step="0.01" id="life_price" name="life_price"
						value="<?php echo $erphp_life_price ; ?>" class="regular-text" /><?php echo get_option('ice_name_alipay');?>
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>包年VIP</strong></th>
					<td><input type="number" step="0.01" id="year_price" name="year_price"
						value="<?php echo $erphp_year_price ; ?>" class="regular-text" /><?php echo get_option('ice_name_alipay');?>
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>包季VIP</strong></th>
					<td><input type="number" step="0.01" id="quarter_price" name="quarter_price"
						value="<?php echo $erphp_quarter_price; ?>" class="regular-text" /><?php echo get_option('ice_name_alipay');?>
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>包月VIP</strong></th>
					<td><input type="number" step="0.01" id="month_price" name="month_price"
						value="<?php echo $erphp_month_price; ?>" class="regular-text" /><?php echo get_option('ice_name_alipay');?>
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>体验VIP</strong></th>
					<td><input type="number" step="0.01" id="day_price" name="day_price"
						value="<?php echo $erphp_day_price; ?>" class="regular-text" /><?php echo get_option('ice_name_alipay');?>
					</td>
				</tr>
			</table>

			<h2>VIP名称设置</h2>
			<p>留空则显示默认名称</p>
			<table class="form-table">
				<tr>
					<th valign="top" width="30%"><strong>终身VIP</strong></th>
					<td><input type="text" id="life_name" name="life_name" value="<?php echo $erphp_life_name ; ?>" class="regular-text" />
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>包年VIP</strong></th>
					<td><input type="text" id="year_name" name="year_name" value="<?php echo $erphp_year_name ; ?>" class="regular-text" />
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>包季VIP</strong></th>
					<td><input type="text" id="quarter_name" name="quarter_name" value="<?php echo $erphp_quarter_name; ?>" class="regular-text" />
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>包月VIP</strong></th>
					<td><input type="text" id="month_name" name="month_name" value="<?php echo $erphp_month_name; ?>" class="regular-text" />
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>体验VIP</strong></th>
					<td><input type="text" id="day_name" name="day_name" value="<?php echo $erphp_day_name; ?>" class="regular-text" />
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>VIP</strong></th>
					<td><input type="text" id="vip_name" name="vip_name" value="<?php echo $erphp_vip_name; ?>" class="regular-text" />
						<p>VIP的统称，用于购买时针对体验、包月、包季权限的显示，建议留空（保持不变）</p>
					</td>
				</tr>
			</table>

			<h2>VIP用户每天下载VIP资源个数限制</h2>
			<p>留空则不限制，这里的下载个数指VIP资源合计每天下载的资源个数，仅对VIP资源有效，单独购买的资源无效</p>
			<table class="form-table">
				<tr>
					<th valign="top" width="30%"><strong>终身VIP</strong></th>
					<td><input type="number" id="life_times" name="life_times"
						value="<?php echo $erphp_life_times ; ?>" class="regular-text" min="0" step="1"/>个
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>包年VIP</strong></th>
					<td><input type="number" id="year_times" name="year_times"
						value="<?php echo $erphp_year_times ; ?>" class="regular-text" min="0" step="1"/>个
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>包季VIP</strong></th>
					<td><input type="number" id="quarter_times" name="quarter_times"
						value="<?php echo $erphp_quarter_times; ?>" class="regular-text" min="0" step="1"/>个
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>包月VIP</strong></th>
					<td><input type="number" id="month_times" name="month_times"
						value="<?php echo $erphp_month_times; ?>" class="regular-text" min="0" step="1"/>个
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>体验VIP</strong></th>
					<td><input type="number" id="day_times" name="day_times"
						value="<?php echo $erphp_day_times; ?>" class="regular-text" min="0" step="1"/>个
					</td>
				</tr>
			</table>

			<h2>普通用户每天下载免费资源个数限制</h2>
			<p>留空则不限制，这里的下载个数指免费资源合计每天下载的资源个数，仅对非VIP用户下载免费资源有效，单独购买的资源无效<br /><span style="color:red">VIP用户对免费资源下载不限制</span></p>
			<table class="form-table">
				<tr>
					<th valign="top" width="30%"><strong>注册用户</strong></th>
					<td><input type="number" id="reg_times" name="reg_times"
						value="<?php echo $erphp_reg_times; ?>" class="regular-text" min="0" step="1"/>个
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<p class="submit">
							<input type="submit" name="Submit" value="保存设置" class="button-primary" />
						</p>
					</td>
				</tr>
			</table>
		</form>
	</div>
	<?php }?>