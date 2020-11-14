<?php
/*
Plugin Name: ErphpDown
Plugin URI: http://www.mobantu.com/1780.html
Description: 会员推广下载专业版：支持在线支付(支付宝、微信支付、贝宝)，用户推广、提现，发布收费下载与收费内容查看，下载加密，VIP会员权限等功能的插件。
Version: 11.4
Author: 模板兔
Author URI: http://www.mobantu.com
*/
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb, $erphpdown_version, $wppay_table_name;
$erphpdown_version = '11.4';
$wpdb->icealipay = $wpdb->prefix.'ice_download';
$wpdb->iceindex = $wpdb->prefix.'ice_download_index';
$wpdb->icemoney  = $wpdb->prefix.'ice_money';
$wpdb->iceinfo  = $wpdb->prefix.'ice_info';
$wpdb->iceget  = $wpdb->prefix.'ice_get_money';
$wpdb->vip  = $wpdb->prefix.'ice_vip';
$wpdb->aff  = $wpdb->prefix.'ice_aff';
$wpdb->down  = $wpdb->prefix.'ice_down';
$wpdb->checkin  = $wpdb->prefix.'checkins';
$wppay_table_name = $wpdb->prefix . 'wppay';
define("erphpdown",plugin_dir_url( __FILE__ ));
define('ERPHPDOWN_URL', plugins_url('', __FILE__));
define('ERPHPDOWN_PATH', dirname( __FILE__ ));

require_once ERPHPDOWN_PATH . '/includes/mobantu.php';
require_once ERPHPDOWN_PATH . '/includes/metabox.php';
require_once ERPHPDOWN_PATH . '/includes/shortcode.php';
require_once ERPHPDOWN_PATH . '/includes/show.php';
require_once ERPHPDOWN_PATH . '/includes/functions.erphp.php';
require_once ERPHPDOWN_PATH . '/includes/class.erphp.php';
require_once ERPHPDOWN_PATH . '/includes/crypt.class.php';
require_once ERPHPDOWN_PATH . '/diy.php';

register_activation_hook(__FILE__, 'erphpdown_install');