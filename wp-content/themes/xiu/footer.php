<?php if( _hui('layout') == 'ui-navtop' ){ ?>
</div>
</section>
<?php } ?>
<footer class="footer">
	<?php if( _hui('flinks_s') && _hui('flinks_cat') && ((_hui('flinks_home_s')&&is_home()&&$paged<=1) || (!_hui('flinks_home_s'))) ){ ?>
		<div class="flinks">
			<?php 
				wp_list_bookmarks(array(
					'category'         => _hui('flinks_cat'),
					'orderby'          => 'rating',
					'order'            => 'DESC',
					'show_description' => false,
					'between'          => '',
					'title_before'     => '<strong>',
					'title_after'      => '</strong>',
					'category_before'  => '',
					'category_after'   => ''
				));
			?>
		</div>
	<?php } ?>
    &copy; <?php echo date('Y'); ?> <a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a> 
    <?php if( !_hui('copyright_s') ){ ?>
    &nbsp; <?php echo __('本站主题由', 'haoui') ?> <a href="http://themebetter.com" target="_blank"><?php echo __('themebetter', 'haoui') ?></a> <?php echo __('提供', 'haoui') ?>
    <?php } ?>
    &nbsp; <?php echo _hui('footer_seo') ?>
    <?php echo _hui('trackcode') ?>
</footer>
<?php if( _hui('layout') !== 'ui-navtop' ){ ?>
</section>
<?php } ?>

<?php if( (is_single() && _hui('post_rewards_s')) && ( _hui('post_rewards_alipay') || _hui('post_rewards_wechat') ) ){ ?>
	<div class="rewards-popover-mask" data-event="rewards-close"></div>
	<div class="rewards-popover">
		<h3><?php echo _hui('post_rewards_title') ?></h3>
		<?php if( _hui('post_rewards_alipay') ){ ?>
		<div class="rewards-popover-item">
			<h4>支付宝扫一扫打赏</h4>
			<img src="<?php echo _hui('post_rewards_alipay') ?>">
		</div>
		<?php } ?>
		<?php if( _hui('post_rewards_wechat') ){ ?>
		<div class="rewards-popover-item">
			<h4>微信扫一扫打赏</h4>
			<img src="<?php echo _hui('post_rewards_wechat') ?>">
		</div>
		<?php } ?>
		<span class="rewards-popover-close" data-event="rewards-close"><i class="glyphicon glyphicon-remove"></i></span>
	</div>
<?php } ?>

<?php  
$roll = '';
if( is_home() && _hui('sideroll_index_s') ){
	$roll = _hui('sideroll_index');
}else if( (is_category() || is_tag() || is_search()) && _hui('sideroll_list_s') ){
	$roll = _hui('sideroll_list');
}else if( is_single() && _hui('sideroll_post_s') ){
	$roll = _hui('sideroll_post');
}else if( is_page() && _hui('sideroll_page_s') ){
	$roll = _hui('sideroll_page');
}

$ajaxpager = '0';
if( ((!wp_is_mobile() &&_hui('ajaxpager_s')) || (wp_is_mobile() && _hui('ajaxpager_s_m'))) && _hui('ajaxpager') ){
	$ajaxpager = _hui('ajaxpager');
}

?>
<script>
window.jui = {
	uri: '<?php echo get_stylesheet_directory_uri() ?>',
	roll: '<?php echo $roll ?>',
	ajaxpager: '<?php echo $ajaxpager ?>'
}
</script>
<?php wp_footer(); ?>
</body>
</html>