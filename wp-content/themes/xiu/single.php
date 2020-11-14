<?php get_header(); ?>
<?php $smeta = _hui('single_plugin'); ?>
<div class="content-wrap">
	<div class="content">
		<?php hui_adcode('ads_post_00', 'ssr-content ssr-posthd') ?>
		<?php while (have_posts()) : the_post(); ?>
		<header class="article-header">
			<?php if( _hui('breadcrumbs_single_s') ){ ?>
			<div class="breadcrumbs"><?php echo hui_breadcrumbs() ?></div>
			<?php } ?>
			<h1 class="article-title"><a href="<?php the_permalink() ?>"><?php the_title(); echo get_the_subtitle(); ?></a></h1>
			<ul class="article-meta">
				<?php $author = get_the_author();
			    if( _hui('author_link') ){
			        $author = '<a href="'.get_author_posts_url( get_the_author_meta( 'ID' ) ).'">'.$author.'</a>';
			    } ?>
				<li>
					<?php if( $smeta && isset($smeta['author']) && $smeta['author'] ) echo $author ?> 
					<?php if( $smeta && isset($smeta['time']) && $smeta['time'] ) echo __('发布于 ', 'haoui').hui_get_post_date( get_the_time('Y-m-d H:i:s') ); ?>
				</li>
				<?php if( $smeta && isset($smeta['catname']) && $smeta['catname'] ){ ?><li><?php echo __('分类：', 'haoui');the_category(' / '); ?></li><?php } ?>
				<?php echo _hui('post_from_s') && hui_get_post_from() ? '<li>'.hui_get_post_from().'</li>' : '' ?>
				<?php if( $smeta && isset($smeta['view']) && $smeta['view'] ){ ?><li><?php echo hui_get_views() ?></li><?php } ?>
				<?php if( $smeta && isset($smeta['comm']) && $smeta['comm'] && !_hui('commentkill') ){ ?><li><?php echo hui_get_comment_number() ?></li><?php } ?>
				<?php if( $smeta && isset($smeta['edit']) && $smeta['edit'] ){ ?><li><?php edit_post_link('['.__('编辑', 'haoui').']'); ?></li><?php } ?>
			</ul>
		</header>
		<?php hui_adcode('ads_post_01', 'ssr-content ssr-post') ?>
		<article class="article-content">
			<?php the_content(); ?>
			<?php wp_link_pages('link_before=<span>&link_after=</span>&before=<div class="article-paging">&after=</div>&next_or_number=number'); ?>
			<?php hui_adcode('ads_post_06', 'ssr-content-bom ssr-post') ?>
        	<?php if( _hui('post_copyright_s') ) echo '<p class="post-copyright">'._hui('post_copyright').'：<a href="'.get_bloginfo('url').'">'.get_bloginfo('name').'</a> &raquo; <a href="'.get_permalink().'">'.get_the_title().get_the_subtitle().'</a></p>'; ?>
		</article>
		<?php endwhile;  ?>
		<div class="article-social">
			<?php if( $smeta && isset($smeta['like']) && $smeta['like'] ) echo hui_get_post_like($class='action action-like'); ?>
			<?php if( _hui('post_rewards_s') ){ ?>
	            <a href="javascript:;" class="action action-rewards" data-event="rewards"><i class="glyphicon glyphicon-usd"></i><?php echo _hui('post_rewards_text', '打赏') ?></a>
	        <?php } ?>
			<?php if( _hui('post_link_single_s') ) hui_post_link(); ?>
		</div>

		<?php echo _hui('share_single_s')&&_hui('share_single_code') ? '<div class="share-single">'. _hui('share_single_code') .'</div>' : '' ?>
		
		<div class="article-tags">
			<?php the_tags(__('标签：', 'haoui'),'',''); ?>
		</div>

		<?php if( _hui('post_authordesc_s') ){ ?>
		<div class="article-author">
			<?php echo hui_get_avatar(get_the_author_meta('ID'), get_the_author_meta('email')); ?>
			<h4>作者：<a title="查看更多文章" href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><?php echo get_the_author_meta('nickname'); ?></a></h4>
			<div class="article-author-desc"><?php echo get_the_author_meta('description'); ?></div>
		</div>
		<?php } ?>

		<nav class="article-nav">
			<span class="article-nav-prev"><?php previous_post_link(__('<span>上一篇</span>', 'haoui').'%link'); ?></span>
			<span class="article-nav-next"><?php next_post_link(__('<span>下一篇</span>', 'haoui').'%link'); ?></span>
		</nav>

		<?php hui_adcode('ads_post_02', 'ssr-content ssr-related') ?>
		<?php if( _hui('post_related_s') ) hui_posts_related( _hui('related_title'), _hui('post_related_n'), (_hui('post_related_model') ? _hui('post_related_model') : 'thumb') ) ?>
		<?php 
			if( !$paged && _hui('sticky_post_s') ) {
				hui_moloader('hui_posts_sticky');
			}
		?>
		<?php hui_adcode('ads_post_03', 'ssr-content ssr-comment') ?>
		<?php comments_template('', true); ?>
	</div>
</div>
<?php get_sidebar(); get_footer(); ?>