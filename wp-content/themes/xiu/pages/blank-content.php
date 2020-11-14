<?php 
/*
 * Template Name: Blank Content Page
 * http://themebetter.com/theme/xiu
*/
get_header();
?>
<div class="content-wrap">
	<div class="content no-sidebar">
		<?php while (have_posts()) : the_post(); ?>
			<article class="article-content">
				<?php the_content(); ?>
			</article>
			<?php comments_template('', true); ?>
		<?php endwhile;  ?>
	</div>
</div>
<?php get_footer(); ?>