{combine_css path="themes/default/js/ui/theme/jquery.ui.button.css"}
{combine_css path="themes/elegant/admin/jquery.ui.button.css"}
{footer_script require='jquery.ui.button'}
{literal}
jQuery(document).ready(function(){
  jQuery( ".radio" ).buttonset();
});
{/literal}
{/footer_script}

<div class="titrePage">
  <h2>{'Elegant Slick, Configuration Page'|@translate}</h2>
</div>
<form method="post" class="properties" action="" ENCTYPE="multipart/form-data" name="form" class="properties">
<div id="configContent">
  <fieldset>
    <legend>{'Options'|@translate}</legend>
    <ul>
      <li class="radio" >
        <label for="p_no_cat_page"><span class="property">{'Suppress category page'|@translate}</span>&nbsp;</label>
        <input type="radio" id="p_no_cat_page_on" name="p_no_cat_page" value="on" {if $options.p_no_cat_page=="on"}checked="checked"{/if}><label for="p_no_cat_page_on">{'Yes'|@translate}</label>
        <input type="radio" id="p_no_cat_page_off" name="p_no_cat_page" value="off" {if $options.p_no_cat_page=="off"}checked="checked"{/if}><label for="p_no_cat_page_off">{'No'|@translate}</label>
      </li>
      <li class="radio" >
        <label for="p_max_pano"><span class="property">{'Maximize panorama height'|@translate}</span>&nbsp;</label>
        <input type="radio" id="p_max_pano_on" name="p_max_pano" value="on" {if $options.p_max_pano=="on"}checked="checked"{/if}><label for="p_max_pano_on">{'Yes'|@translate}</label>
        <input type="radio" id="p_max_pano_off" name="p_max_pano" value="off" {if $options.p_max_pano=="off"}checked="checked"{/if}><label for="p_max_pano_off">{'No'|@translate}</label>
      </li>
    </ul>
  </fieldset>
</div>
<p>
  <input class="submit" type="submit" value="{'Submit'|@translate}" name="submit_elegant_slick" />
</p>
</form>
