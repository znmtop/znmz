<div class="wrap">
	<h2>激活插件</h2>
	<script>window._MBT = { plugin: 'ERPHPDOWN', home: '<?php echo get_option('siteurl');?>', uri: '<?php echo ERPHPDOWN_URL; ?>', setting: '<?php echo admin_url('admin.php?page=erphpdown/admin/erphp-settings.php');?>'}</script>
	<table class="form-table">
		<tbody>
			<tr class="form-field form-required">
				<th scope="row"><label for="mbt_epd_username">用户名</label></th>
				<td><input name="mbt_epd_username" type="text" id="mbt_epd_username" value="" aria-required="true" autocapitalize="none" autocorrect="off" required="" style="width: 25em;">
					<p class="description">模板兔网站的用户名（个人中心 - 我的资料）</p>
				</td>
			</tr>
			<tr class="form-field form-required">
				<th scope="row"><label for="mbt_epd_token">激活码</label></th>
				<td><input name="mbt_epd_token" type="text" id="mbt_epd_token" value="" aria-required="true" autocapitalize="none" autocorrect="off" required="" style="width: 25em;">
					<p class="description">模板兔网站的下载标识码（个人中心 - 下载清单）</p>
				</td>
			</tr>
		</tbody>
	</table>
	<p class="submit">
		<input type="button" class="button-primary" name="save" id="mbt-erphpdown-active" value="激活插件"/>
	</p>
	<p class="description">插件无域名限制！<br />请勿将激活码告知他人，否则会因为激活码滥用而被封号！<br />激活遇到问题请联系QQ：82708210</p>
</div>