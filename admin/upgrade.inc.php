<?php

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

global $prefixeTable, $conf;

if (!isset($conf['elegant_slick']))
{
  $config = array(
    'p_no_cat_page' => 'on', //on - off - disabled
    'p_max_pano' => 'on', //on - off - disabled
  );
  
  conf_update_param('elegant_slick', $config, true);
}
elseif (count(safe_unserialize( $conf['elegant_slick'] ))!=3)
{
  $conff = safe_unserialize($conf['elegant_slick']);
  $config = array(
    'p_no_cat_page' => (isset($conff['p_no_cat_page'])) ? $conff['p_no_cat_page'] :'on',
    'p_max_pano' => (isset($conff['p_max_pano'])) ? $conff['p_max_pano'] :'on',
  );
  
  conf_update_param('elegant_slick', $config, true);
}
?>