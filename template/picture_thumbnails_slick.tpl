{if !empty($thumbnails)}
{strip}
{*define_derivative name='derivative_params' width=160 height=90 crop=true*}
{html_style}
{*Set some sizes according to maximum thumbnail width and height*}
{* TF, 20160403: but don't add any extra *}
.thumbnails SPAN,
.thumbnails .wrap2 A,
.thumbnails LABEL{ldelim}
  width: {$derivative_params->max_width()}px;
}

.thumbnails .wrap2{ldelim}
  height: {$derivative_params->max_height()}px;
}
{if $derivative_params->max_width() > 600}
.thumbLegend {ldelim}font-size: 130%}
{else}
{if $derivative_params->max_width() > 400}
.thumbLegend {ldelim}font-size: 110%}
{else}
.thumbLegend {ldelim}font-size: 90%}
{/if}
{/if}
#theImage, .wrapper {ldelim}
  height: {$max_image_size[1]}px;
}
#imageInfos {ldelim}
  max-height: {$max_image_size[1]-50}px;
}
p.imageComment {ldelim}
  width: {$real_image_size[0]}px;
  bottom: {$real_image_size[1]+3}px;
}
{/html_style}

{combine_css path="themes/elegant_slick/js/slick/slick.css"}
{combine_css path="themes/elegant_slick/js/slick/slick-theme.css"}
{combine_script id="slick.carousel" require="jquery" path="themes/elegant_slick/js/slick/slick.min.js"}
{footer_script require='jquery'}
$('.slick-carousel').slick({
 infinite: false,
 centerMode: false,
 swipeToSlide: false,
 slidesToShow: 10,
 slidesToScroll: 6,
 variableWidth: true,
 lazyLoad: 'progressive',
 responsive: [
  {
   breakpoint: 1024,
   settings: {
    slidesToShow: 10,
    slidesToScroll: 3
   }
  },
  {
   breakpoint: 600,
   settings: {
    slidesToShow: 8,
    slidesToScroll: 2
   }
  },
  {
   breakpoint: 420,
   settings: {
    slidesToShow: 4,
    slidesToScroll: 2
   }
  }]
});

var currentThumbnailIndex = $('.slick-carousel').find('[data-thumbnail-active="1"]').data('slick-index');
$(".slick-slider").slick('goTo', currentThumbnailIndex, true);

{/footer_script}
{/strip}

<div class="container">
 <div class="col-lg-10 col-md-offset-1">
  <div id="thumbnailCarousel" class="slick-carousel">
{foreach from=$thumbnails item=thumbnail}
{assign var=derivative value=$pwg->derivative($derivative_params, $thumbnail.src_image)}
{if !$derivative->is_cached()}
{combine_script id='jquery.ajaxmanager' path='themes/default/js/plugins/jquery.ajaxmanager.js' load='footer'}
{combine_script id='thumbnails.loader' path='themes/default/js/thumbnails.loader.js' require='jquery.ajaxmanager' load='footer'}
{/if}
{if $thumbnail.id eq $current.id}
        <div class="text-center thumbnail-active" data-thumbnail-active="1"><a href="{$thumbnail.URL}"><img {if $derivative->is_cached()}data-lazy="{$derivative->get_url()}"{else}data-lazy="{$ROOT_URL}{$themeconf.icon_dir}/img_small.png" data-src="{$derivative->get_url()}"{/if} alt="{$thumbnail.TN_ALT}" title="{$thumbnail.TN_TITLE}" class="img-responsive"></a></div>
{else}
        <div class="text-center"><a href="{$thumbnail.URL}"><img {if $derivative->is_cached()}data-lazy="{$derivative->get_url()}"{else}data-lazy="{$ROOT_URL}{$themeconf.icon_dir}/img_small.png" data-src="{$derivative->get_url()}"{/if} alt="{$thumbnail.TN_ALT}" title="{$thumbnail.TN_TITLE}" class="img-responsive"></a></div>
{/if}
{/foreach}
  </div>
 </div>
</div>
{/if}

