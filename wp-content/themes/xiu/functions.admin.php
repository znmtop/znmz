<?php

/* 
 * options
 * ====================================================
*/
define( 'OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/options/assets/' );
require_once get_template_directory() . '/options/options-ui.php';

/* 
 * admin login
 * ====================================================
*/
/*if( _hui('admin_login_limit') ){
add_action('login_enqueue_scripts','login_protection');  
function login_protection(){  
    if($_GET['admin'] != _hui('admin_login_limit')) header('Location: '.home_url());  
}
}*/


function add_editor_buttons($buttons) {
    $buttons[] = 'fontselect';
    $buttons[] = 'fontsizeselect';
    $buttons[] = 'cleanup';
    $buttons[] = 'styleselect';
    $buttons[] = 'del';
    $buttons[] = 'sub';
    $buttons[] = 'sup';
    $buttons[] = 'copy';
    $buttons[] = 'paste';
    $buttons[] = 'cut';
    $buttons[] = 'image';
    $buttons[] = 'anchor';
    $buttons[] = 'backcolor';
    $buttons[] = 'wp_page';
    $buttons[] = 'charmap';
    return $buttons;
}
add_filter("mce_buttons_2", "add_editor_buttons");






add_filter('manage_posts_columns', 'postlike_admin_add_column');
function postlike_admin_add_column($columns){
    $columns['like'] = __('点赞数', 'haoui');
    return $columns;
}
add_action('manage_posts_custom_column','postlike_admin_show',10,2);
function postlike_admin_show($column_name,$id){
    if ($column_name != 'like')
        return;   
    $post_like = get_post_meta($id, "like",true);
    echo $post_like;
}




add_filter('manage_posts_columns', 'postviews_admin_add_column');
function postviews_admin_add_column($columns){
    $columns['views'] = __('阅读数', 'haoui');
    return $columns;
}
add_action('manage_posts_custom_column','postviews_admin_show',10,2);
function postviews_admin_show($column_name,$id){
    if ($column_name != 'views')
        return;   
    $post_views = get_post_meta($id, "views",true);
    echo $post_views;
}




/* 
 * post meta from
 * ====================================================
*/
$postmeta_from = array(
    array(
        "name" => "fromname",
        "std" => "",
        "title" => __('来源网站名', 'haoui').'：'
    ),
    array(
        "name" => "fromurl",
        "std" => "",
        "title" => __('来源地址', 'haoui').'：'
    )
);
add_action('admin_menu', 'hui_create_meta_box');
add_action('save_post', 'hui_save_postdata');

function hui_postmeta_from() {
    global $post, $postmeta_from;
    foreach($postmeta_from as $meta_box) {
        $meta_box_value = get_post_meta($post->ID, $meta_box['name'].'_value', true);
        if($meta_box_value == "")
            $meta_box_value = $meta_box['std'];
        echo'<p>'.$meta_box['title'].'</p>';
        echo '<p><input type="text" style="width:98%" value="'.$meta_box_value.'" name="'.$meta_box['name'].'_value"></p>';
    }
   
    echo '<input type="hidden" name="post_newmetaboxes_noncename" id="post_newmetaboxes_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
}

function hui_create_meta_box() {
    global $theme_name;
    if ( function_exists('add_meta_box') ) {
        add_meta_box( 'new-meta-boxes', __('来源', 'haoui'), 'hui_postmeta_from', 'post', 'side', 'high' );
    }
}

function hui_save_postdata( $post_id ) {
    global $postmeta_from;
   
    if ( !wp_verify_nonce( isset($_POST['post_newmetaboxes_noncename'])?$_POST['post_newmetaboxes_noncename']:'', plugin_basename(__FILE__) ))
        return;
   
    if ( !current_user_can( 'edit_posts', $post_id ))
        return;
                   
    foreach($postmeta_from as $meta_box) {
        $data = isset($_POST[$meta_box['name'].'_value']) ? $_POST[$meta_box['name'].'_value'] : '';
        if(get_post_meta($post_id, $meta_box['name'].'_value') == "")
            add_post_meta($post_id, $meta_box['name'].'_value', $data, true);
        elseif($data != get_post_meta($post_id, $meta_box['name'].'_value', true))
            update_post_meta($post_id, $meta_box['name'].'_value', $data);
        elseif($data == "")
            delete_post_meta($post_id, $meta_box['name'].'_value', get_post_meta($post_id, $meta_box['name'].'_value', true));
    }
}



/* 
 * post meta keywords
 * ====================================================
*/
$postmeta_keywords_description = array(
    array(
        "name" => "keywords",
        "std" => "",
        "title" => __('关键字', 'haoui').'：'
    ),
    array(
        "name" => "description",
        "std" => "",
        "title" => __('描述', 'haoui').'：'
        )
);
if( _hui('post_keywords_description_s') ){
    add_action('admin_menu', 'hui_postmeta_keywords_description_create');
    add_action('save_post', 'hui_postmeta_keywords_description_save');
}

function hui_postmeta_keywords_description() {
    global $post, $postmeta_keywords_description;
    foreach($postmeta_keywords_description as $meta_box) {
        $meta_box_value = get_post_meta($post->ID, $meta_box['name'], true);
        if($meta_box_value == "")
            $meta_box_value = $meta_box['std'];
        echo'<p>'.$meta_box['title'].'</p>';
        if( $meta_box['name'] == 'keywords' ){
            echo '<p><input type="text" style="width:98%" value="'.$meta_box_value.'" name="'.$meta_box['name'].'"></p>';
        }else{
            echo '<p><textarea style="width:98%" name="'.$meta_box['name'].'">'.$meta_box_value.'</textarea></p>';
        }
    }
   
    echo '<input type="hidden" name="post_newmetaboxes_noncename" id="post_newmetaboxes_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
}

function hui_postmeta_keywords_description_create() {
    global $theme_name;
    if ( function_exists('add_meta_box') ) {
        add_meta_box( 'postmeta_keywords_description_boxes', __('SEO关键字和描述', 'haoui'), 'hui_postmeta_keywords_description', 'post', 'side', 'high' );
        add_meta_box( 'postmeta_keywords_description_boxes', __('SEO关键字和描述', 'haoui'), 'hui_postmeta_keywords_description', 'page', 'side', 'high' );
    }
}

function hui_postmeta_keywords_description_save( $post_id ) {
    global $postmeta_keywords_description;
   
    if ( !wp_verify_nonce( isset($_POST['post_newmetaboxes_noncename'])?$_POST['post_newmetaboxes_noncename']:'', plugin_basename(__FILE__) ))
        return;
   
    if ( !current_user_can( 'edit_posts', $post_id ))
        return;
                   
    foreach($postmeta_keywords_description as $meta_box) {
        $data = isset($_POST[$meta_box['name']]) ? $_POST[$meta_box['name']] : '';
        if(get_post_meta($post_id, $meta_box['name']) == "")
            add_post_meta($post_id, $meta_box['name'], $data, true);
        elseif($data != get_post_meta($post_id, $meta_box['name'], true))
            update_post_meta($post_id, $meta_box['name'], $data);
        elseif($data == "")
            delete_post_meta($post_id, $meta_box['name'], get_post_meta($post_id, $meta_box['name'], true));
    }
}



/* 
 * post meta link
 * ====================================================
*/
$postmeta_link = array(
    array(
        "name" => "link",
        "std" => ""/*,
        "title" => __('直达链接', 'haoui').'：'*/
    )
);
if( _hui('post_link_excerpt_s') || _hui('post_link_single_s') ){
    add_action('admin_menu', 'hui_postmeta_link_create');
    add_action('save_post', 'hui_postmeta_link_save');
}

function hui_postmeta_link() {
    global $post, $postmeta_link;
    foreach($postmeta_link as $meta_box) {
        $meta_box_value = get_post_meta($post->ID, $meta_box['name'], true);
        if($meta_box_value == "")
            $meta_box_value = $meta_box['std'];
        if( isset($meta_box['title']) ) echo'<p>'.$meta_box['title'].'</p>';
        echo '<p><input type="text" style="width:98%" value="'.$meta_box_value.'" name="'.$meta_box['name'].'"></p>';
    }
   
    echo '<input type="hidden" name="post_newmetaboxes_noncename" id="post_newmetaboxes_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
}

function hui_postmeta_link_create() {
    global $theme_name;
    if ( function_exists('add_meta_box') ) {
        add_meta_box( 'postmeta_link_boxes', __('直达链接', 'haoui'), 'hui_postmeta_link', 'post', 'side', 'high' );
    }
}

function hui_postmeta_link_save( $post_id ) {
    global $postmeta_link;
   
    if ( !wp_verify_nonce( isset($_POST['post_newmetaboxes_noncename'])?$_POST['post_newmetaboxes_noncename']:'', plugin_basename(__FILE__) ))
        return;
   
    if ( !current_user_can( 'edit_posts', $post_id ))
        return;
                   
    foreach($postmeta_link as $meta_box) {
        $data = isset($_POST[$meta_box['name']]) ? $_POST[$meta_box['name']] : '';
        if(get_post_meta($post_id, $meta_box['name']) == "")
            add_post_meta($post_id, $meta_box['name'], $data, true);
        elseif($data != get_post_meta($post_id, $meta_box['name'], true))
            update_post_meta($post_id, $meta_box['name'], $data);
        elseif($data == "")
            delete_post_meta($post_id, $meta_box['name'], get_post_meta($post_id, $meta_box['name'], true));
    }
}






/* 
 * post meta subtitle
 * ====================================================
*/
$postmeta_subtitle = array(
    array(
        "name" => "subtitle",
        "std" => ""/*,
        "title" => __('直达链接', 'haoui').'：'*/
    )
);

add_action('admin_menu', 'hui_postmeta_subtitle_create');
add_action('save_post', 'hui_postmeta_subtitle_save');


function hui_postmeta_subtitle() {
    global $post, $postmeta_subtitle;
    foreach($postmeta_subtitle as $meta_box) {
        $meta_box_value = get_post_meta($post->ID, $meta_box['name'], true);
        if($meta_box_value == "")
            $meta_box_value = $meta_box['std'];
        if( isset($meta_box['title']) ) echo'<p>'.$meta_box['title'].'</p>';
        echo '<p><input type="text" style="width:98%" value="'.$meta_box_value.'" name="'.$meta_box['name'].'"></p>';
    }
   
    echo '<input type="hidden" name="post_newmetaboxes_noncename" id="post_newmetaboxes_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
}

function hui_postmeta_subtitle_create() {
    global $theme_name;
    if ( function_exists('add_meta_box') ) {
        add_meta_box( 'postmeta_subtitle_boxes', __('副标题', 'haoui'), 'hui_postmeta_subtitle', 'post', 'side', 'high' );
    }
}

function hui_postmeta_subtitle_save( $post_id ) {
    global $postmeta_subtitle;
   
    if ( !wp_verify_nonce( isset($_POST['post_newmetaboxes_noncename'])?$_POST['post_newmetaboxes_noncename']:'', plugin_basename(__FILE__) ))
        return;
   
    if ( !current_user_can( 'edit_posts', $post_id ))
        return;
                   
    foreach($postmeta_subtitle as $meta_box) {
        $data = isset($_POST[$meta_box['name']]) ? $_POST[$meta_box['name']] : '';
        if(get_post_meta($post_id, $meta_box['name']) == "")
            add_post_meta($post_id, $meta_box['name'], $data, true);
        elseif($data != get_post_meta($post_id, $meta_box['name'], true))
            update_post_meta($post_id, $meta_box['name'], $data);
        elseif($data == "")
            delete_post_meta($post_id, $meta_box['name'], get_post_meta($post_id, $meta_box['name'], true));
    }
}









/*$postmeta_xzh = array(
    array(
        "title" => "原创文章",
        "name" => "is_original",
        "std" => ""
    )
);

if( _hui('xzh_on') ){
    add_action('admin_menu', 'hui_postmeta_xzh_create');
    add_action('save_post', 'hui_postmeta_xzh_save');
}

function hui_postmeta_xzh() {
    global $post, $postmeta_xzh;
    foreach($postmeta_xzh as $meta_box) {
        $meta_box_value = get_post_meta($post->ID, $meta_box['name'], true);
        if($meta_box_value == "")
            $meta_box_value = $meta_box['std'];
        echo '<p><label><input '.($meta_box_value?'checked':'').' type="checkbox" value="1" name="'.$meta_box['name'].'"> '.(isset($meta_box['title']) ? $meta_box['title'] : '').'</label></p>';
    }
    $tui = get_post_meta($post->ID, 'xzh_tui_back', true);
    if( $tui ) echo '<p>实时推送结果：'.$tui.'</p>';
   
    echo '<input type="hidden" name="post_newmetaboxes_noncename" id="post_newmetaboxes_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
}

function hui_postmeta_xzh_create() {
    global $theme_name;
    if ( function_exists('add_meta_box') ) {
        add_meta_box( 'postmeta_xzh_boxes', __('百度熊掌号设置', 'haoui'), 'hui_postmeta_xzh', 'post', 'normal', 'high' );
    }
}

function hui_postmeta_xzh_save( $post_id ) {
    global $postmeta_xzh;
   
    if ( !wp_verify_nonce( isset($_POST['post_newmetaboxes_noncename'])?$_POST['post_newmetaboxes_noncename']:'', plugin_basename(__FILE__) ))
        return;
   
    if ( !current_user_can( 'edit_posts', $post_id ))
        return;
                   
    foreach($postmeta_xzh as $meta_box) {
        $data = isset($_POST[$meta_box['name']]) ? $_POST[$meta_box['name']] : '';
        if(get_post_meta($post_id, $meta_box['name']) == "")
            add_post_meta($post_id, $meta_box['name'], $data, true);
        elseif($data != get_post_meta($post_id, $meta_box['name'], true))
            update_post_meta($post_id, $meta_box['name'], $data);
        elseif($data == "")
            delete_post_meta($post_id, $meta_box['name'], get_post_meta($post_id, $meta_box['name'], true));
    }
}*/




// baidu tui
////////////////////////////////////////////////////////////////////////////////////////////////////

if( _hui('bdtui_on') ) add_action('publish_post', 'tb_post_to_baidu_tui');
function tb_post_to_baidu_tui() {
    global $post;
    $plink = get_permalink($post->ID);
    if( $plink ){

        if( _hui('bdtui_kuai_api') && isset($_POST['baidutui_kuai_on']) && $_POST['baidutui_kuai_on'] && !get_post_meta($post->ID, 'baidutui_kuai', true) ){
            $ch = curl_init();
            $options =  array(
                CURLOPT_URL            => _hui('bdtui_kuai_api'),
                CURLOPT_POST           => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS     => $plink,
                CURLOPT_HTTPHEADER     => array('Content-Type: text/plain')
            );
            curl_setopt_array($ch, $options);
            $result = curl_exec($ch);
            update_post_meta($post->ID, 'baidutui_kuai', $result);
        }

        if( _hui('bdtui_api') && !get_post_meta($post->ID, 'baidutui', true) ){
            $ch = curl_init();
            $options =  array(
                CURLOPT_URL            => _hui('bdtui_api'),
                CURLOPT_POST           => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS     => $plink,
                CURLOPT_HTTPHEADER     => array('Content-Type: text/plain')
            );
            curl_setopt_array($ch, $options);
            $result = curl_exec($ch);
            update_post_meta($post->ID, 'baidutui', $result);
        }

    }
}




if( _hui('bdtui_on') ) add_action( 'add_meta_boxes', 'tbcm_meta_boxs2' );
function tbcm_meta_boxs2() {
    add_meta_box( 'tb_baidu_tui', '百度收录', 'tb_baidu_tui_init', 'post', 'side', 'low' );
}

function tb_baidu_tui_init() {
    global $post;
    $tui = get_post_meta($post->ID, 'baidutui', true);
    $kuai = get_post_meta($post->ID, 'baidutui_kuai', true);
    echo '<br>';
    echo '<label><input type="checkbox" name="baidutui_kuai_on" id="">快速收录</label>';
    echo '<br>';
    echo '<br>';

    if( $kuai ){
        $kuaiObj = json_decode( $kuai );
        echo '<p><strong>快速收录：'.(isset($kuaiObj->success_daily)&&$kuaiObj->success_daily>0?'<span style="color:#46B450">推送成功</span>':'<span style="color:#FF5E52">推送失败</span>').'</strong></p>';
        echo '<p>推送结果：<code style="word-break:break-all">'.($kuai?$kuai:'').'</code></p>';
        echo '<br>';
    }

    if( $tui ){
        $tuiObj = json_decode( $tui );
        echo '<p><strong>普通收录：'.(isset($tuiObj->success)&&$tuiObj->success>0?'<span style="color:#46B450">推送成功</span>':'<span style="color:#FF5E52">推送失败</span>').'</strong></p>';
        echo '<p>推送结果：<code style="word-break:break-all">'.($tui?$tui:'').'</code></p>';
    }else{
        echo '<p><strong>普通收录：</strong>将在发布或更新文章时推送</p>';
    }

    echo '<input type="hidden" name="tb_baidu_tui_noncename" id="tb_baidu_tui_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
}