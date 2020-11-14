<?php get_header(); ?>
<div class="content-wrap">
	<div class="content">
		<?php hui_adcode('ads_search_01', 'ssr-content') ?>
		<h1 class="title"><strong><?php echo htmlspecialchars($s); ?> <?php echo __('的搜索结果', 'haoui') ?></strong></h1>
		<?php if ( !have_posts() ) : ?>
			<h3 class="text-muted text-center"><?php echo __('暂无搜索结果', 'haoui') ?></h3>
		<?php else: ?>
			<?php get_template_part( 'excerpt' );  ?>
		<?php endif; ?>
	</div>
</div>
<?php 
get_sidebar(); 

get_footer();
?>