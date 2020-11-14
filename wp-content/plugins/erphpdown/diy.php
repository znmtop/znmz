<?php 
function epd_download_page($msg, $pid=0){
?>
    <html lang="zh-CN">
        <head>
            <meta charset="UTF-8" />
            <link rel="stylesheet" href="<?php echo constant("erphpdown"); ?>static/erphpdown.css" type="text/css" />
            <script src="<?php echo constant("erphpdown"); ?>static/jquery-1.7.min.js"></script>
            <script src="<?php echo constant("erphpdown"); ?>static/erphpdown.js"></script>
            <title>文件下载 - <?php echo get_the_title($pid);?> - <?php bloginfo('name');?></title>
        </head>
        <body class="erphpdown-body">
        	<div id="erphpdown-download">
                <!-- 以下内容不要动 -->
        		<div class="msg"><?php echo $msg;?></div>
                <!-- 以上内容不要动 -->
                <?php do_action('erphpdown_download_ad');?>
            </div>
        </body>
    </html>
<?php 
    exit;
}
