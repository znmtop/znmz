<?php get_header(); ?>
<div class="content-wrap">
	<div class="content">
		<?php hui_adcode('ads_index_01', 'ssr-content') ?>
		<?php 		
			if( !$paged && _hui('focusslide_s') ) hui_moloader('hui_focusslide');
		
			if( !$paged && _hui('focus_s') ) hui_moloader('hui_posts_focus');

			if( !$paged && _hui('most_list_s') ) hui_moloader('hui_recent_posts_most');

			if( !$paged && _hui('sticky_s') ) hui_moloader('hui_posts_sticky');
		?>
		<?php hui_adcode('ads_index_02', 'ssr-content') ?>
		<?php 
			if( $paged && $paged > 1 ){
				printf('<h3 class="title"><strong>'.__('所有文章', 'haoui').'</strong> <small>'.__('第', 'haoui').$paged.__('页', 'haoui').'</small></h3>');
			}else{
				printf('<h3 class="title">'.(_hui('recent_posts_number')?'<small class="pull-right">'.__('24小时更新：', 'haoui').hui_get_recent_posts_number().__('篇', 'haoui').' &nbsp; &nbsp; '.__('一周更新：', 'haoui').hui_get_recent_posts_number(7).__('篇', 'haoui').'</small>':'').'<strong>'._hui('index_list_title').'</strong></h3>');
			}

			$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
			$args = array(
			    'ignore_sticky_posts' => 1,
			    'paged' => $paged
			);

			if( _hui('index_list_out_sticky') ){
				$args['post__not_in'] = get_option( 'sticky_posts' );
			}

			if( _hui('home_recent_updatetime') ){
				$args['orderby'] = 'modified';
			}

			$_notinhome = _hui('notinhome');
			if( $_notinhome ){
				$pool = array();
				foreach ($_notinhome as $key => $value) {
					if( $value ) $pool[] = $key;
				}
				$args['cat'] = '-'.implode(',-', $pool);
			}

			query_posts($args);

			get_template_part( 'excerpt' ); 
		?>
		<?php hui_adcode('ads_index_03', 'ssr-content') ?>
	</div>
</div>
<?php get_sidebar(); get_footer(); ?>