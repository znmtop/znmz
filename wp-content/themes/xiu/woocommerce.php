<?php get_header(); ?>
<div class="content-wrap">
	<div class="content">
		<div class="woomain">
			<?php if( function_exists('is_product') && is_product() && function_exists( 'woocommerce_breadcrumb') ) woocommerce_breadcrumb(); ?>
			<?php woocommerce_content(); ?>
		</div>
	</div>
</div>
<?php get_footer(); ?>