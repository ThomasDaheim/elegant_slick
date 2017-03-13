<?php

// Need upgrade?
global $conf;
include(PHPWG_THEMES_PATH.'elegant_slick/admin/upgrade.inc.php');

load_language('theme.lang', PHPWG_THEMES_PATH.'elegant_slick/');

$config_send= array();

if(isset($_POST['submit_elegant_slick']))
{
  $config_send['p_no_cat_page']=(isset($_POST['p_no_cat_page']) and !empty($_POST['p_no_cat_page'])) ? $_POST['p_no_cat_page'] : 'on';
  $config_send['p_max_pano']=(isset($_POST['p_max_pano']) and !empty($_POST['p_max_pano'])) ? $_POST['p_max_pano'] : 'on';
  
  conf_update_param('elegant_slick', $config_send, true);

  array_push($page['infos'], l10n('Information data registered in database'));
}

$template->set_filenames(array(
    'theme_admin_content' => dirname(__FILE__) . '/admin.tpl'));

$template->assign('options', safe_unserialize($conf['elegant_slick']));

$template->assign_var_from_handle('ADMIN_CONTENT', 'theme_admin_content');
  
?>