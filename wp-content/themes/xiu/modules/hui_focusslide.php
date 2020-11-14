<?php 
/* 
 * post focus
 * ====================================================
*/
function hui_focusslide(){
    $indicators = '';
    $inner = '';
    $sort = _hui('focusslide_sort') ? _hui('focusslide_sort') : '1 2 3 4 5';
    $sort = array_unique(explode(' ', trim($sort)));
    $i = 0;
    foreach ($sort as $key => $value) {
        if( _hui('focusslide_src_'.$value) && _hui('focusslide_href_'.$value) && _hui('focusslide_title_'.$value) ){
            $inner .= '<div class="swiper-slide"><a'.( _hui('focusslide_blank_'.$value) ? ' target="_blank"' : '' ).' href="'._hui('focusslide_href_'.$value).'"><img src="'._hui('focusslide_src_'.$value).'"><span class="carousel-caption">'._hui('focusslide_title_'.$value).'</span><span class="carousel-bg"></span></a></div>';
            $i++;
        }
    }

    echo '<div id="focusslide" class="swiper-container">
        <div class="swiper-wrapper">'.$inner.'</div>
        <div class="swiper-pagination"></div>
        <div class="swiper-button-next swiper-button-white"><i class="glyphicon glyphicon-chevron-right"></i></div>
        <div class="swiper-button-prev swiper-button-white"><i class="glyphicon glyphicon-chevron-left"></i></div>
    </div>';
}