<?php 
/*
 * Template Name: Tags Page
 * http://themebetter.com/theme/xiu
*/
get_header();

$pagetags = _hui('tagspagenumber', 40);

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$purl = get_page_link();

if( strstr($purl, '?') ){
	$purl .= '?paged=';
}else{
	$purl .= '/page/';
}

$tagsoffset = $pagetags*($paged-1);


?>
<div class="content-wrap">
    <div class="content page-tags no-sidebar">
        <h1 class="title"><strong><?php echo get_the_title(); ?></strong></h1>
		<ul class="tagslist">
			<?php 
			
			$tags_list = get_tags('orderby=count&order=DESC&number='.$pagetags.'&offset='.$tagsoffset);
			if ($tags_list) { 
				foreach($tags_list as $tag) {
					echo '<li><a class="tagname" href="'.get_tag_link($tag).'">'. $tag->name .'</a><strong>x '. $tag->count .'</strong><br>'; 
					$posts = get_posts( "tag_id=". $tag->term_id ."&numberposts=1" );
					if( $posts ){
						foreach( $posts as $post ) {
							setup_postdata( $post );
							echo '<a href="'.get_permalink().'">'.get_the_title().'</a><br><span class="muted">'.get_the_time('Y-m-d').'</span class="muted">';
						}
					}
					echo '</li>';
				} 
			} 
			?>
		</ul>
		<?php _tags_paging() ?>
	</div>
</div>
<?php get_footer(); 


function _tags_paging() {
   	global $purl;
    global $pagetags;
    global $paged;
    $max_page = ceil(wp_count_terms('post_tag', array('hide_empty' => true))/$pagetags);

    if ( $max_page == 1 ) return; 
    echo '<div class="pagination pagination-multi"><ul>';

    for( $i = 1; $i <= $max_page; $i++ ) { 
        if ( $i > 0 && $i <= $max_page ) $i == $paged ? print "<li class=\"active\"><span>{$i}</span></li>" : print "<li><a href='".$purl.$i."'>{$i}</a></li>";
    }

    echo '<li><span>'.__('共', 'haoui').' '.$max_page.' '.__('页', 'haoui').'</span></li>';
    
    echo '</ul></div>';

}