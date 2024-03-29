{if !$current.selected_derivative->is_cached()}
{combine_script id='jquery.ajaxmanager' path='themes/default/js/plugins/jquery.ajaxmanager.js' load='footer'}
{combine_script id='thumbnails.loader' path='themes/default/js/thumbnails.loader.js' require='jquery.ajaxmanager' load='footer'}
{footer_script}var error_icon = "{$ROOT_URL}{$themeconf.icon_dir}/errors_small.png"{/footer_script}
{/if}
{* https://github.com/justin-john/jquery.dynamic.imagemap, to use add "data-class" where required *}
{combine_script id='dynamic.imagemap' path='themes/elegant_slick/js/jquery.dynamic.imagemap.min.js' require='jquery' load='footer'}
{combine_script id='my.imagemap' path='themes/elegant_slick/js/my.imagemap.js' require='dynamic.imagemap' load='footer'}

<img {if $current.selected_derivative->is_cached()}src="{$current.selected_derivative->get_url()}" {$current.selected_derivative->get_size_htm()}{else}src="{$ROOT_URL}{$themeconf.img_dir}/ajax_loader.gif" data-src="{$current.selected_derivative->get_url()}"{/if} alt="{$ALT_IMG}" id="theMainImage" usemap="#map{$current.selected_derivative->get_type()}" title="{if isset($COMMENT_IMG)}{$COMMENT_IMG|@strip_tags:false|@replace:'"':' '}{else}{$current.TITLE_ESC} - {$ALT_IMG}{/if}">

{foreach from=$current.unique_derivatives item=derivative key=derivative_type}{strip}
<map name="map{$derivative->get_type()}">
{assign var='size' value=$derivative->get_size()}
{if isset($previous)}
<area {if $current.selected_derivative->get_type()==$derivative->get_type()}data-class="prevImage"{/if} shape=rect coords="0,0,{($size[0]/8)|@intval},{$size[1]}" href="{$previous.U_IMG}" title="{'Previous'|@translate} : {$previous.TITLE_ESC}" alt="{$previous.TITLE_ESC}">
{/if}
{* TF, 20160408: no more up arrow since we don't have a category page anymore...
<area {if $current.selected_derivative->get_type()==$derivative->get_type()}data-class="upImage"{/if} shape=rect coords="{($size[0]/8)|@intval},0,{($size[0]*7/8)|@intval},{($size[1]/8)|@intval}" href="{$U_UP}" title="{'Thumbnails'|@translate}" alt="{'Thumbnails'|@translate}"> *}
{if isset($next)}
<area {if $current.selected_derivative->get_type()==$derivative->get_type()}data-class="nextImage"{/if} shape=rect coords="{($size[0]*7/8)|@intval},0,{$size[0]},{$size[1]}" href="{$next.U_IMG}" title="{'Next'|@translate} : {$next.TITLE_ESC}" alt="{$next.TITLE_ESC}">
{/if}
</map>
{/strip}{/foreach}
