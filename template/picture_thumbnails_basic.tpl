{if !empty($thumbnails)}{strip}
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
{footer_script}
  var error_icon = "{$ROOT_URL}{$themeconf.icon_dir}/errors_small.png", max_requests = {$maxRequests};
  {literal}
  function centerItVariableWidth(target, outer){
	var out = jQuery(outer);
	var tar = jQuery(target);
	var x = out.width();
	var m = out.find('li');
	var y = tar.outerWidth(true);
	var z = tar.index();
	var q = 0;
	//Just need to add up the width of all the elements before our target. 
	for(var i = 0; i < z; i++){
	  q+= jQuery(m[i]).outerWidth(true);
	}
	out.scrollLeft(Math.max(0, q - (x - y)/2));
  }
  jQuery(document).ready(function() {
	centerItVariableWidth("#currentitem", "#pictureThumbnails");
  });
  {/literal}
{/footer_script}
<div class="pictureThumbnails" id="pictureThumbnails">
<ul class="thumbnails" id="thumbnails">
{foreach from=$thumbnails item=thumbnail}
{assign var=derivative value=$pwg->derivative($derivative_params, $thumbnail.src_image)}
{if !$derivative->is_cached()}
{combine_script id='jquery.ajaxmanager' path='themes/default/js/plugins/jquery.ajaxmanager.js' load='footer'}
{combine_script id='thumbnails.loader' path='themes/default/js/thumbnails.loader.js' require='jquery.ajaxmanager' load='footer'}
{/if}
{if $thumbnail.id eq $current.id}
<li id="currentitem">
{else}
<li>
{/if}
  <span class="wrap1">
	<span class="wrap2">
	{if $thumbnail.id eq $current.id}
	  <a href="{$thumbnail.URL}">
		<img class="currentthumbnail" {if $derivative->is_cached()}src="{$derivative->get_url()}"{else}src="{$ROOT_URL}{$themeconf.icon_dir}/img_small.png" data-src="{$derivative->get_url()}"{/if} alt="{$thumbnail.TN_ALT}" title="{$thumbnail.TN_TITLE}">
	  </a>
	{else}
	  <a href="{$thumbnail.URL}">
		<img class="thumbnail" {if $derivative->is_cached()}src="{$derivative->get_url()}"{else}src="{$ROOT_URL}{$themeconf.icon_dir}/img_small.png" data-src="{$derivative->get_url()}"{/if} alt="{$thumbnail.TN_ALT}" title="{$thumbnail.TN_TITLE}">
	  </a>
	{/if}
	</span>
	{if $SHOW_THUMBNAIL_CAPTION }
	<span class="thumbLegend">
	<span class="thumbName">{$thumbnail.NAME}</span>
	{if !empty($thumbnail.icon_ts)}
	<img title="{$thumbnail.icon_ts.TITLE}" src="{$ROOT_URL}{$themeconf.icon_dir}/recent.png" alt="(!)">
	{/if}
	{if isset($thumbnail.NB_COMMENTS)}
	<span class="{if 0==$thumbnail.NB_COMMENTS}zero {/if}nb-comments">
	<br>
	{$pwg->l10n_dec('%d comment', '%d comments',$thumbnail.NB_COMMENTS)}
	</span>
	{/if}

	{if isset($thumbnail.NB_HITS)}
	<span class="{if 0==$thumbnail.NB_HITS}zero {/if}nb-hits">
	<br>
	{$pwg->l10n_dec('%d hit', '%d hits',$thumbnail.NB_HITS)}
	</span>
	{/if}
	</span>
	{/if}
  </span>
</li>
{/foreach}{/strip}
</ul>
</div>
{/if}
