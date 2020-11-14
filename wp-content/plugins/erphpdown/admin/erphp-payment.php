<?php
/**
 * setting
 www.mobantu.com
 E-mail:82708210@qq.com
 */
 if ( !defined('ABSPATH') ) {exit;}

 if(isset($_POST['Submit'])) {

 	update_option('erphpdown_alipay_type', trim($_POST['erphpdown_alipay_type']));
 	update_option('ice_ali_partner', trim($_POST['ice_ali_partner']));
 	update_option('ice_ali_security_code', trim($_POST['ice_ali_security_code']));
 	update_option('ice_ali_seller_email', trim($_POST['ice_ali_seller_email']));
 	update_option('ice_ali_seller_name', trim($_POST['ice_ali_seller_name']));
    update_option('ice_ali_app', trim($_POST['ice_ali_app']));
    update_option('ice_ali_app_id', trim($_POST['ice_ali_app_id']));
    update_option('ice_ali_private_key', trim($_POST['ice_ali_private_key']));
    update_option('ice_ali_public_key', trim($_POST['ice_ali_public_key']));
 	update_option('ice_payapl_api_uid', trim($_POST['ice_payapl_api_uid']));
 	update_option('ice_payapl_api_pwd', trim($_POST['ice_payapl_api_pwd']));
 	update_option('ice_payapl_api_md5', trim($_POST['ice_payapl_api_md5']));
 	update_option('ice_payapl_api_rmb', trim($_POST['ice_payapl_api_rmb']));   
 	update_option('erphpdown_xhpay_appid31', trim($_POST['erphpdown_xhpay_appid31']));
 	update_option('erphpdown_xhpay_appsecret31', trim($_POST['erphpdown_xhpay_appsecret31']));
 	update_option('erphpdown_xhpay_api31', trim($_POST['erphpdown_xhpay_api31']));
 	update_option('erphpdown_xhpay_appid32', trim($_POST['erphpdown_xhpay_appid32']));
 	update_option('erphpdown_xhpay_appsecret32', trim($_POST['erphpdown_xhpay_appsecret32']));
 	update_option('erphpdown_xhpay_api32', trim($_POST['erphpdown_xhpay_api32']));
 	update_option('ice_weixin_mchid', trim($_POST['ice_weixin_mchid']));
 	update_option('ice_weixin_appid', trim($_POST['ice_weixin_appid']));
 	update_option('ice_weixin_key', trim($_POST['ice_weixin_key']));
 	update_option('ice_weixin_secret', trim($_POST['ice_weixin_secret']));
    update_option('ice_weixin_app', trim($_POST['ice_weixin_app']));
 	update_option('erphpdown_paypy_key', trim($_POST['erphpdown_paypy_key']));
 	update_option('erphpdown_paypy_api', trim($_POST['erphpdown_paypy_api']));
    update_option('erphpdown_paypy_alipay', trim($_POST['erphpdown_paypy_alipay']));
    update_option('erphpdown_paypy_wxpay', trim($_POST['erphpdown_paypy_wxpay']));
 	update_option('erphpdown_codepay_appid', trim($_POST['erphpdown_codepay_appid']));
 	update_option('erphpdown_codepay_appsecret', trim($_POST['erphpdown_codepay_appsecret']));
    update_option('erphpdown_codepay_qqpay', trim($_POST['erphpdown_codepay_qqpay']));
 	update_option('erphpdown_f2fpay_id', trim($_POST['erphpdown_f2fpay_id']));
 	update_option('erphpdown_f2fpay_public_key', trim($_POST['erphpdown_f2fpay_public_key']));
 	update_option('erphpdown_f2fpay_private_key', trim($_POST['erphpdown_f2fpay_private_key']));
 	update_option('erphpdown_f2fpay_alipay', trim($_POST['erphpdown_f2fpay_alipay']));

 	echo'<div class="updated settings-error"><p>更新成功！</p></div>';

 }

 $erphpdown_alipay_type = get_option('erphpdown_alipay_type');
 $ice_ali_partner       = get_option('ice_ali_partner');
 $ice_ali_security_code = get_option('ice_ali_security_code');
 $ice_ali_seller_email  = get_option('ice_ali_seller_email');
 $ice_ali_seller_name   = get_option('ice_ali_seller_name');
 $ice_ali_app   = get_option('ice_ali_app');
 $ice_ali_app_id   = get_option('ice_ali_app_id');
 $ice_ali_private_key   = get_option('ice_ali_private_key');
 $ice_ali_public_key   = get_option('ice_ali_public_key');
 $ice_payapl_api_uid    = get_option('ice_payapl_api_uid');
 $ice_payapl_api_pwd    = get_option('ice_payapl_api_pwd');
 $ice_payapl_api_md5    = get_option('ice_payapl_api_md5');
 $ice_payapl_api_rmb    = get_option('ice_payapl_api_rmb');
 $erphpdown_xhpay_appid31    = get_option('erphpdown_xhpay_appid31');
 $erphpdown_xhpay_appsecret31    = get_option('erphpdown_xhpay_appsecret31');
 $erphpdown_xhpay_api31    = get_option('erphpdown_xhpay_api31');
 $erphpdown_xhpay_appid32    = get_option('erphpdown_xhpay_appid32');
 $erphpdown_xhpay_appsecret32    = get_option('erphpdown_xhpay_appsecret32');
 $erphpdown_xhpay_api32    = get_option('erphpdown_xhpay_api32');
 $ice_weixin_mchid  = get_option('ice_weixin_mchid');
 $ice_weixin_appid  = get_option('ice_weixin_appid');
 $ice_weixin_key  = get_option('ice_weixin_key');
 $ice_weixin_secret  = get_option('ice_weixin_secret');
 $ice_weixin_app  = get_option('ice_weixin_app');
 $erphpdown_paypy_key    = get_option('erphpdown_paypy_key');
 $erphpdown_paypy_api    = get_option('erphpdown_paypy_api');
 $erphpdown_paypy_alipay = get_option('erphpdown_paypy_alipay');
 $erphpdown_paypy_wxpay = get_option('erphpdown_paypy_wxpay');
 $erphpdown_codepay_appid    = get_option('erphpdown_codepay_appid');
 $erphpdown_codepay_appsecret    = get_option('erphpdown_codepay_appsecret');
 $erphpdown_codepay_qqpay = get_option('erphpdown_codepay_qqpay');
 $erphpdown_f2fpay_id       = get_option('erphpdown_f2fpay_id');
 $erphpdown_f2fpay_public_key       = get_option('erphpdown_f2fpay_public_key');
 $erphpdown_f2fpay_private_key       = get_option('erphpdown_f2fpay_private_key');
 $erphpdown_f2fpay_alipay = get_option('erphpdown_f2fpay_alipay');
 ?>
 <style>.form-table th{font-weight: 400}</style>
 <div class="wrap">
 	<h1>支付设置</h1>
 	<form method="post" action="<?php echo admin_url('admin.php?page='.plugin_basename(__FILE__)); ?>">
 		<h3>1、支付宝（官方接口）</h3>
 		PC电脑网站支付申请地址： https://mrchportalweb.alipay.com/user/home.htm#/ 页面下面开通电脑网站支付。
 		<table class="form-table">
 			<tr>
 				<th valign="top">接口类型</th>
 				<td>
 					<select name="erphpdown_alipay_type">
 						<option value="create_direct_pay_by_user" <?php if($erphpdown_alipay_type == 'create_direct_pay_by_user') echo 'selected="selected"';?>>即时到账</option>
 						<option value ="create_partner_trade_by_buyer" <?php if($erphpdown_alipay_type == 'create_partner_trade_by_buyer') echo 'selected="selected"';?>>担保交易（官方已下架）</option>
 						<option value ="trade_create_by_buyer" <?php if($erphpdown_alipay_type == 'trade_create_by_buyer') echo 'selected="selected"';?>>双接口（官方已下架）</option>
 					</select>
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">合作者身份(Partner ID)</th>
 				<td>
 					<input type="text" id="ice_ali_partner" name="ice_ali_partner" value="<?php echo $ice_ali_partner ; ?>" class="regular-text"/>
                    <p>查看地址：https://openhome.alipay.com/platform/keyManage.htm?keyType=partner</p>
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">安全校验码(Key)</th>
 				<td>
 					<input type="text" id="ice_ali_security_code" name="ice_ali_security_code" value="<?php echo $ice_ali_security_code; ?>" class="regular-text"/>
 					<p>密钥管理 - mapi网关产品密钥，MD5密钥</p>
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">支付宝收款账号</th>
 				<td>
 					<input type="text" id="ice_ali_seller_email" name="ice_ali_seller_email" value="<?php echo $ice_ali_seller_email; ?>" class="regular-text"/>
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">收款方名称</th>
 				<td>
 					<input type="text" id="ice_ali_seller_name" name="ice_ali_seller_name" value="<?php echo $ice_ali_seller_name; ?>" class="regular-text"/>
 				</td>
 			</tr>
            <tr>
                <th valign="top">启用唤醒APP支付</th>
                <td>
                    <input type="checkbox" id="ice_ali_app" name="ice_ali_app" value="yes" <?php if($ice_ali_app == 'yes') echo 'checked'; ?> /> 
                    <p>唤醒APP支付接口现在已免费集成，但模板兔不提供免费的调试接口辅助。<br>开放平台申请接口：https://openhome.alipay.com/platform/developerIndex.htm<br>网页&移动应用，能力名称为 手机网站支付</p>
                </td>
            </tr>
            <tr>
                <th valign="top">APP支付APPID</th>
                <td>
                <input type="text" id="ice_ali_app_id" name="ice_ali_app_id" value="<?php echo $ice_ali_app_id; ?>" class="regular-text"/>
                </td>
            </tr>
            <tr>
                <th valign="top">APP支付商户应用私钥</th>
                <td>
                <textarea id="ice_ali_private_key" name="ice_ali_private_key" class="regular-text" style="height: 200px;"><?php echo $ice_ali_private_key; ?></textarea>
                </td>
            </tr>
            <tr>
                <th valign="top">APP支付支付宝公钥</th>
                <td>
                <textarea id="ice_ali_public_key" name="ice_ali_public_key" class="regular-text" style="height: 200px;"><?php echo $ice_ali_public_key; ?></textarea>
                </td>
            </tr>
 		</table>
 		<br />
		<h3>2、支付宝当面付（官方接口）</h3>
		<table class="form-table">
				<tr>
 				<th valign="top">应用ID</th>
 				<td>
 					<input type="text" id="erphpdown_f2fpay_id" name="erphpdown_f2fpay_id" value="<?php echo $erphpdown_f2fpay_id ; ?>" class="regular-text"/>
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">商户应用私钥</th>
 				<td>
 					<textarea id="erphpdown_f2fpay_private_key" name="erphpdown_f2fpay_private_key" class="regular-text" style="height: 200px;"><?php echo $erphpdown_f2fpay_private_key; ?></textarea>
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">支付宝公钥</th>
 				<td>
 					<textarea id="erphpdown_f2fpay_public_key" name="erphpdown_f2fpay_public_key" class="regular-text" style="height: 200px;"><?php echo $erphpdown_f2fpay_public_key; ?></textarea>
 				</td>
 			</tr>
 			<tr>
                <th valign="top">隐藏</th>
                <td>
                    <input type="checkbox" id="erphpdown_f2fpay_alipay" name="erphpdown_f2fpay_alipay" value="yes" <?php if($erphpdown_f2fpay_alipay == 'yes') echo 'checked'; ?> /> 
                </td>
            </tr>
		</table>
		<br />
 		<h3>3、微信支付（官方接口）</h3>
 		微信支付-->开发配置，设置支付授权目录：<?php echo home_url();?>/wp-content/plugins/erphpdown/payment/
		<table class="form-table">
			<tr>
				<th valign="top">商户号(MCHID)</th>
				<td>
					<input type="text" id="ice_weixin_mchid" name="ice_weixin_mchid" value="<?php echo $ice_weixin_mchid ; ?>" class="regular-text"/>
				</td>
			</tr>
			<tr>
				<th valign="top">APPID</th>
				<td>
					<input type="text" id="ice_weixin_appid" name="ice_weixin_appid" value="<?php echo $ice_weixin_appid; ?>" class="regular-text"/>
				</td>
			</tr>
			<tr>
				<th valign="top">商户支付密钥(KEY)</th>
				<td>
					<input type="text" id="ice_weixin_key" name="ice_weixin_key" value="<?php echo $ice_weixin_key; ?>" class="regular-text"/><br>
					设置地址：<a href="https://pay.weixin.qq.com/index.php/account/api_cert" target="_blank">https://pay.weixin.qq.com/index.php/account/api_cert </a>，建议为32位字符串<br>设置教程：<a href="https://www.mobantu.com/7919.html" target="_blank">https://www.mobantu.com/7919.html</a>
				</td>
			</tr>
			<tr>
				<th valign="top">公众帐号Secret</th>
				<td>
					<input type="text" id="ice_weixin_secret" name="ice_weixin_secret" value="<?php echo $ice_weixin_secret; ?>" class="regular-text"/>
				</td>
			</tr>
        <tr>
            <th valign="top">启用唤醒APP支付</th>
            <td>
                <input type="checkbox" id="ice_weixin_app" name="ice_weixin_app" value="yes" <?php if($ice_weixin_app == 'yes') echo 'checked'; ?> /> 
                <p>唤醒APP支付接口现在已免费集成，但模板兔不提供免费的调试接口辅助。<br>1、微信公众平台-->公众号设置-->功能设置，设置业务域名、JS接口安全域名、网页授权域名<br>2、商户平台-->产品中心-->开发配置，设置支付授权目录、H5支付域名</p>
            </td>
        </tr>
		</table>

		<br />
		<h3>4、PayPal（官方接口）</h3>
		<table class="form-table">
			<tr>
				<th valign="top">API帐号</th>
				<td>
					<input type="text" id="ice_payapl_api_uid" name="ice_payapl_api_uid" value="<?php echo $ice_payapl_api_uid ; ?>" class="regular-text"/>
				</td>
			</tr>
			<tr>
				<th valign="top">API密码</th>
				<td>
					<input type="text" id="ice_payapl_api_pwd" name="ice_payapl_api_pwd" value="<?php echo $ice_payapl_api_pwd; ?>" class="regular-text"/>
				</td>
			</tr>
			<tr>
				<th valign="top">API签名</th>
				<td>
					<input type="text" id="ice_payapl_api_md5" name="ice_payapl_api_md5" value="<?php echo $ice_payapl_api_md5; ?>" class="regular-text"/>
				</td>
			</tr>
			<tr>
				<th valign="top">汇率</th>
				<td>
					<input type="text" id="ice_payapl_api_rmb" name="ice_payapl_api_rmb" value="<?php echo $ice_payapl_api_rmb; ?>" class="regular-text"/>
				</td>
			</tr>
		</table>

		<br />
		<h3>5、Paypy-微信/支付宝个人免签</h3>
		<div>详情请看：<a href="http://www.mobantu.com/8080.html" target="_blank" rel="nofollow">http://www.mobantu.com/8080.html</a></div>
		<table class="form-table">
			<tr>
				<th valign="top">签名密钥</th>
				<td>
					<input type="text" id="erphpdown_paypy_key" name="erphpdown_paypy_key" value="<?php echo $erphpdown_paypy_key ; ?>" class="regular-text"/>
				</td>
			</tr>
			<tr>
				<th valign="top">Api地址</th>
				<td>
					<input type="text" id="erphpdown_paypy_api" name="erphpdown_paypy_api" value="<?php echo $erphpdown_paypy_api; ?>" class="regular-text"/>
				</td>
			</tr>
            <tr>
                <th valign="top">隐藏支付宝</th>
                <td>
                    <input type="checkbox" id="erphpdown_paypy_alipay" name="erphpdown_paypy_alipay" value="yes" <?php if($erphpdown_paypy_alipay == 'yes') echo 'checked'; ?> /> 
                </td>
            </tr>
            <tr>
                <th valign="top">隐藏微信</th>
                <td>
                    <input type="checkbox" id="erphpdown_paypy_wxpay" name="erphpdown_paypy_wxpay" value="yes" <?php if($erphpdown_paypy_wxpay == 'yes') echo 'checked'; ?> /> 
                </td>
            </tr>
		</table>
		<br />
		<h3>6.1、虎皮椒V3-微信支付</h3>
		<div>关于此接口的安全稳定性，请使用者自行把握，我们只提供集成服务，接口申请地址：<a href="https://admin.xunhupay.com/sign-up/451.html" target="_blank" rel="nofollow">点击查看</a></div>
		<table class="form-table">
			<tr>
				<th valign="top">appid</th>
				<td>
					<input type="text" id="erphpdown_xhpay_appid31" name="erphpdown_xhpay_appid31" value="<?php echo $erphpdown_xhpay_appid31 ; ?>" class="regular-text"/>
				</td>
			</tr>
			<tr>
				<th valign="top">appsecret</th>
				<td>
					<input type="text" id="erphpdown_xhpay_appsecret31" name="erphpdown_xhpay_appsecret31" value="<?php echo $erphpdown_xhpay_appsecret31; ?>" class="regular-text"/>
				</td>
			</tr>
			<tr>
				<th valign="top">网关</th>
				<td>
					<input type="text" id="erphpdown_xhpay_api31" name="erphpdown_xhpay_api31" value="<?php echo $erphpdown_xhpay_api31; ?>" class="regular-text"/>
					<p>留空则默认网关，无特别升级提示，请留空即可</p>
				</td>
			</tr>
		</table>
		<br />
		<h3>6.2、虎皮椒V3-支付宝支付</h3>
		<div>关于此接口的安全稳定性，请使用者自行把握，我们只提供集成服务，接口申请地址：<a href="https://admin.xunhupay.com/sign-up/451.html" target="_blank" rel="nofollow">点击查看</a></div>
		<table class="form-table">
			<tr>
				<th valign="top">appid</th>
				<td>
					<input type="text" id="erphpdown_xhpay_appid32" name="erphpdown_xhpay_appid32" value="<?php echo $erphpdown_xhpay_appid32 ; ?>" class="regular-text"/>
				</td>
			</tr>
			<tr>
				<th valign="top">appsecret</th>
				<td>
					<input type="text" id="erphpdown_xhpay_appsecret32" name="erphpdown_xhpay_appsecret32" value="<?php echo $erphpdown_xhpay_appsecret32; ?>" class="regular-text"/>
				</td>
			</tr>
			<tr>
				<th valign="top">网关</th>
				<td>
					<input type="text" id="erphpdown_xhpay_api32" name="erphpdown_xhpay_api32" value="<?php echo $erphpdown_xhpay_api32; ?>" class="regular-text"/>
					<p>留空则默认网关，无特别升级提示，请留空即可</p>
				</td>
			</tr>
		</table>
		<br />
		<h3>7、码支付（支付宝/微信/QQ钱包）</h3>
		<div>关于此接口的安全稳定性，请使用者自行把握，我们只提供集成服务，接口申请地址：<a href="https://codepay.fateqq.com/i/520753" target="_blank" rel="nofollow">点击查看</a></div>
		<table class="form-table">
			<tr>
				<th valign="top">码支付ID</th>
				<td>
					<input type="text" id="erphpdown_codepay_appid" name="erphpdown_codepay_appid" value="<?php echo $erphpdown_codepay_appid ; ?>" class="regular-text"/>
				</td>
			</tr>
			<tr>
				<th valign="top">通讯密钥</th>
				<td>
					<input type="text" id="erphpdown_codepay_appsecret" name="erphpdown_codepay_appsecret" value="<?php echo $erphpdown_codepay_appsecret; ?>" class="regular-text"/>
				</td>
			</tr>
            <tr>
                <th valign="top">隐藏QQ钱包</th>
                <td>
                    <input type="checkbox" id="erphpdown_codepay_qqpay" name="erphpdown_codepay_qqpay" value="yes" <?php if($erphpdown_codepay_qqpay == 'yes') echo 'checked'; ?> /> 
                </td>
            </tr>
		</table>

		<p class="submit">
			<input type="submit" name="Submit" value="保存设置" class="button-primary"/>
			<div >技术支持：mobantu.com <a href="http://www.mobantu.com/6658.html" target="_blank">使用教程>></a></div>
		</p>      
	</form>
</div>