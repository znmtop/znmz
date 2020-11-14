<?php get_header(); ?>
<div class="content-wrap">
	<div class="content">
		<?php hui_adcode('ads_cat_01', 'ssr-content') ?>
		<?php 
		$pagedtext = '';
		if( $paged && $paged > 1 ){
			$pagedtext = ' <small>'.__('第', 'haoui').$paged.__('页', 'haoui').'</small>';
		}

		if( _hui('cat_desc_s') ){
			$description = trim(strip_tags(category_description()));
		    if( _hui('cat_keyworks_s') && $description && strstr($description, '::::::') ){
		        $desc = explode('::::::', $description);
		        $description = trim($desc[2]);
		    }
		    echo '<div class="cat-leader"><h1>', single_cat_title(), $pagedtext.'</h1><div class="cat-leader-desc">'.$description.'</div></div>';
	    }else{
	    	echo '<h1 class="title"><strong><a href="'.get_category_link( get_cat_ID( single_cat_title('',false) ) ).'">', single_cat_title(), '</a></strong>'.$pagedtext.'</h1>';
	    }

		get_template_part( 'excerpt' ); 
		?>
	</div>
</div>
<?php get_sidebar(); get_footer(); ?>