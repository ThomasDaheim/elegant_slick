<?php
/*
Theme Name: Elegant Slick
Version: 1.0.0
Description: Various upates: No category page, thumbnails on picture page, openstreetmap from category on picture page, maximize panoramas
Theme URI: 
Author: Thomas Feuster
Author URI: http://bilder.feuster.com/
*/
$themeconf = array(
  'name'  => 'elegant_slick',
  'parent' => 'elegant',
  'load_parent_css' => true,
  'load_parent_local_head' => true,
  'url' => 'http://bilder.feuster.com/'
);
// Need upgrade?
global $conf;
include(PHPWG_THEMES_PATH.'elegant_slick/admin/upgrade.inc.php');

add_event_handler('init', 'elegant_slick_set_config_values');
function elegant_slick_set_config_values()
{
  global $conf, $template;
  $config = safe_unserialize( $conf['elegant_slick'] );
  $template->assign( 'elegant_slick', $config );
}

/*
Panorama in original size
*/
add_event_handler('render_element_content', 'elegant_slick_pano_max_size', EVENT_HANDLER_PRIORITY_NEUTRAL+5);
function elegant_slick_pano_max_size($content, $element_info)
{
  global $conf, $page, $template;
  
  if ( !empty($content) )
  {// we have the image - now lets check & replace if required
    //print_r($element_info);
    
    $width = $element_info['width'];
    $height = $element_info['height'];
    
    // store values for later use...
    if (isset($template->get_template_vars('current')['selected_derivative']))
    {
      $real_image_size = $template->get_template_vars('current')['selected_derivative']->get_size();
    }
    else
    {
      // the hard way... no picture set, e.g. a video
      if (isset($_COOKIE['picture_deriv']))
      {
        if ( array_key_exists($_COOKIE['picture_deriv'], ImageStdParams::get_defined_type_map()) )
        {
          pwg_set_session_var('picture_deriv', $_COOKIE['picture_deriv']);
        }
      }
      $derivative_type = pwg_get_session_var('picture_deriv', $conf['derivative_default_size']);

      // set real size from the dimensions of this derivative - can't think of anything better to do
      $real_image_size = ImageStdParams::get_by_type($derivative_type)->sizing->ideal_size;
    }
  
    // store real width for further use
    $template->assign('real_image_size', $real_image_size);

	// check, if we should do anything
	if ( !isset($template->get_template_vars('elegant_slick')['p_max_pano']) or
		$template->get_template_vars('elegant_slick')['p_max_pano'] == 'off')
	{
		return $content;
	}

	// TFE, 20180902: a pano is also everything with width > 1280...
    if ((($height > 0 && $width/$height > 3.5) || ($width > 1280)) && isset($template->get_template_vars('current')['selected_derivative']))
    {
      //print_r('panorama detected!');
      //print_r($element_info);
      
      // i) find out what derivative type we have and what max height value it has
      $derivative_type = $template->get_template_vars('current')['selected_derivative']->get_type();
      $ideal_height = ImageStdParams::get_by_type($derivative_type)->sizing->ideal_size[1];
      //print_r(' ideal height: ');
      //print_r($ideal_height);
            
      // ii) find largest derivative with height <= $ideal_height
      foreach($element_info['derivatives'] as $type => $derivative)
      {
        if ($type==IMG_SQUARE || $type==IMG_THUMB)
          continue;
        if (!array_key_exists($type, ImageStdParams::get_defined_type_map()))
          continue;
		  //print_r(' checking derivative: ');
		  //print_r($derivative->get_size()[1]);
        // check height against $ideal_height
        if ($derivative->get_size()[1] > $ideal_height)
          continue;
        
        if (!isset($ideal_derivative) || $derivative->get_size()[1] > $ideal_derivative->get_size()[1])
          $ideal_derivative = $derivative;
      }
	  //print_r(' ideal derivative: ');
      //print_r($ideal_derivative->get_size()[1]);
          
      // now replace the link, the width & height and the usemap from the ideal derivative
      // src="_data/i/galleries/Lissabon 2016/img_5945_dpp_stitch-me.jpg" width="792" height="98" usemap="#mapmedium" 
      
      // 1: image url
      $replacement = '<img src="'.$ideal_derivative->get_url().'"';
      $search = '/<img src=\".*?\"/';
      $content = preg_replace($search, $replacement, $content);
      
      // 2: width
      $replacement = 'width="'.$ideal_derivative->get_size()[0].'"';
      $search = '/width=\".*?\"/';
      $content = preg_replace($search, $replacement, $content);
      $real_image_width = $ideal_derivative->get_size()[0];
      
      // 3: height
      $replacement = 'height="'.$ideal_derivative->get_size()[1].'"';
      $search = '/height=\".*?\"/';
      $content = preg_replace($search, $replacement, $content);
      
      // 4: usemap
      $replacement = 'usemap="#map'.$ideal_derivative->get_type().'"';
      $search = '/usemap=\".*?\"/';
      $content = preg_replace($search, $replacement, $content);
	  
	  // 5: switch "data-class" from current derivative to new one
	  // 5.1: get current and new "map" definition
	  $current_search = '/<map name=\"map'.$derivative_type.'\">.*?map>/';
      //print_r($current_search);
	  if (preg_match($current_search, $content, $current_match_array))
	  {
		  $current_match = $current_match_array[0];
		  //print_r($current_match);
		  $ideal_search = '/<map name=\"map'.$ideal_derivative->get_type().'\">.*?map>/';
		  //print_r($ideal_search);
		  if (preg_match($ideal_search, $content, $ideal_match_array))
		  {
			  $ideal_match = $ideal_match_array[0];
			  //print_r($ideal_match);
			  // 5.2 check for prevImage, upImage, nextImage data-class entries and shift them each from curent to new "map" definition
			  // 5.2.a replace 'area data-class="prevImage"' with 'area' and so on AND check if something was replaced
			  $replacement = 'area';
			  $search = 'area data-class="prevImage"';
			  $current_match = str_replace($search, $replacement, $current_match, $prevCount);
			  $replacement = 'area';
			  $search = 'area data-class="upImage"';
			  $current_match = str_replace($search, $replacement, $current_match, $upCount);
			  $replacement = 'area';
			  $search = 'area data-class="nextImage"';
			  $current_match = str_replace($search, $replacement, $current_match, $nextCount);

			  // 5.2.b now add 'data-class="prevImage"' and so on ONLY if they have been replaced previously
			  if ($prevCount == 1)
			  {
				  $replacement = 'area data-class="prevImage" shape';
				  // always find the next "area" without a data-class after it :-)
				  $search = 'area  shape';
				  $startPos = strpos($ideal_match, $search);
				  $ideal_match = substr_replace($ideal_match, $replacement, $startPos, strlen($search));
			  }
			  if ($upCount == 1)
			  {
				  $replacement = 'area data-class="upImage" shape';
				  $search = 'area  shape';
				  $startPos = strpos($ideal_match, $search);
				  $ideal_match = substr_replace($ideal_match, $replacement, $startPos, strlen($search));
			  }
			  if ($nextCount == 1)
			  {
				  $replacement = 'area data-class="nextImage" shape';
				  $search = 'area  shape';
				  $startPos = strpos($ideal_match, $search);
				  $ideal_match = substr_replace($ideal_match, $replacement, $startPos, strlen($search));
			  }
			  
			  // 5.3: replace curent and new definitions
			  $content = preg_replace($current_search, $current_match, $content);
			  $content = preg_replace($ideal_search, $ideal_match, $content);
		  }
	  }
      
      //print_r($content);
      $real_image_size = $ideal_derivative->get_size();
    }
  
    // store real width for further use
    $template->assign('real_image_size', $real_image_size);
      
    return $content;
  }
}

/*
No category page
*/
add_event_handler('loc_end_index_thumbnails', 'elegant_slick_skip_cat', 55);
function elegant_slick_skip_cat($tpl_thumbnails_var)
{
//	print_r('skip category');
	global $page, $template;

	// check, if we should do anything
	if (// TFE, 20190804: check if $page or $template are set; if not we're e.g. showing recent pics...
		empty($page) or empty($template) or 
		// is it something else than the category page
		!isset($page['category']) or
		// is the template flag set to not suppress the category page?
		!isset($template->get_template_vars('elegant_slick')['p_no_cat_page']) or
		$template->get_template_vars('elegant_slick')['p_no_cat_page'] == 'off')
	{
		// lets get out of here - nothing for us to do
//		print_r('nothing todo');
		return $tpl_thumbnails_var;
	}

	redirect($tpl_thumbnails_var[0]['URL']);
}

/*
Picture Thumbnails
*/
add_event_handler('loc_end_picture', 'elegant_slick_add_thumbs_to_pic');
function elegant_slick_add_thumbs_to_pic()
{
  // stuff borrowed from category_default.inc.php
  // to retrieve template data required for thumbnails
  
  global $template, $conf, $user, $page;
  
  // determine ideal height and set div for image content & image info to it
  if (isset($template->get_template_vars('current')['selected_derivative']))
  {
    $derivative_type = $template->get_template_vars('current')['selected_derivative']->get_type();
  }
  else
  {
    // the hard way... no picture set, e.g. a video
    if (isset($_COOKIE['picture_deriv']))
    {
      if ( array_key_exists($_COOKIE['picture_deriv'], ImageStdParams::get_defined_type_map()) )
      {
        pwg_set_session_var('picture_deriv', $_COOKIE['picture_deriv']);
      }
    }
    $derivative_type = pwg_get_session_var('picture_deriv', $conf['derivative_default_size']);
  }
      
  if (strcmp($derivative_type, 'Original') == 0) {
    // no derivative_type we can use
    if (isset($template->get_template_vars('current')['selected_derivative']))
    {
      // we have at least the size of the image
      $max_image_size = $template->get_template_vars('current')['selected_derivative']->get_size();
    }
    else
    {
      // out of ideas - 'Original' and no selected derivative...
      $max_image_size = ImageStdParams::get_by_type('normal')->sizing->ideal_size;
    }
  }
  else
  {
    $max_image_size = ImageStdParams::get_by_type($derivative_type)->sizing->ideal_size;
  }
  $template->assign('max_image_size', $max_image_size);
  
  // select all pictures for this category
  $query = '
  SELECT *
    FROM '.IMAGES_TABLE.'
    WHERE id IN ('.implode(',', $page['items']).')
    ORDER BY FIELD(id, '.implode(',', $page['items']).')
  ;';

  $result = pwg_query($query);
  
  $pictures = array();
  while ($row = pwg_db_fetch_assoc($result))
  {
    $pictures[] = $row;
  }
  
  trigger_notify('loc_begin_index_thumbnails', $pictures);
  $tpl_thumbnails_var = array();

  foreach ($pictures as $row)
  {    
    $url = duplicate_picture_url(
      array(
        'image_id' => $row['id'],
        'image_file' => $row['file'],
        ),
      array('start')
      );

    $name = render_element_name($row);
    $desc = render_element_description($row, 'main_page_element_description');
    
    $tpl_var = array_merge( $row, array(
      'NAME' => $name,
      'TN_ALT' => htmlspecialchars(strip_tags($name)),
      'TN_TITLE' => get_thumbnail_title($row, $name, $desc),
      'URL' => $url,
      'DESCRIPTION' => $desc,
      'src_image' => new SrcImage($row),
    ) );
    
    $tpl_thumbnails_var[] = $tpl_var;
  }
  //print_r($tpl_thumbnails_var);
  
  $template->assign( array(
    'derivative_params' => trigger_change('get_index_derivative_params', ImageStdParams::get_by_type( pwg_get_session_var('index_deriv', IMG_THUMB) ) ),
    'maxRequests' =>$conf['max_requests'],
    'SHOW_THUMBNAIL_CAPTION' =>$conf['show_thumbnail_caption'],
      ) );
  $template->assign('thumbnails', $tpl_thumbnails_var);

  unset($tpl_thumbnails_var, $pictures);
  //print_r($template);
}

/*
Openstreetmap on picture page
*/
add_event_handler('loc_end_picture', 'elegant_slick_add_map_to_pic');
function elegant_slick_add_map_to_pic()
{
  global $template, $conf, $page;
  
  // check if openstreetmap is installed & enabled for category page
  if (isset($conf['osm_conf']) && isset($conf['osm_conf']['category_description']) && $conf['osm_conf']['category_description']['enabled'])
  {
    include_once(OSM_PATH.'category.inc.php');
    
    // trick openstreetmap into thinking its a category page...
    $cur_image_id = $page['image_id'];
    unset($page['image_id']);
    osm_render_category();
    $page['image_id'] = $cur_image_id;
    
    // add button to show/hide map
    $content = '<a id="mapSwitcher" title="Show map" ';
    $content .= 'class="pwg-state-default pwg-button" rel="nofollow">';
    $content .= '<span class="pwg-icon pwg-icon-globe"></span>';
    $content .= '<span class="pwg-button-text">Show map</span>';
    $content .= '</a>';
    $template->add_picture_button($content);
    // code for this is included in picture.tpl

    // TF, 20161026: in case we show a gpx or kml we now have included leaflet.js twice :-(
	// once from osm-gpx.tpl and once from osm-category.tpl
	// so we need to find that and remove one of the includes (along with leaflet.css)
	elegant_slick_cleanup_html_head();

    //print_r('rendered');
  }
}

function elegant_slick_cleanup_html_head()
{
    global $template;

    // the variable in $template we're looking for is "html_head_elements"
    // and its an array with one entry for each *.tpl that had a {html_head} section
    if ( count($template->html_head_elements) )
    {
        // combine array elements into one AND split @ newline AND create a unique array
        //$unique_html_head_elements = array_unique( explode( "\n", implode( "\n", $template->html_head_elements ) ) );
        //print_r($unique_html_head_elements);

        $template->html_head_elements = array_unique( explode( "\n", implode( "\n", $template->html_head_elements ) ) );
    }
}

?>
