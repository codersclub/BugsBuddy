<?php

function showAdminPages($permissions) {

  $numberOfLinks = 0;

  echo pageLink('..', lang('menu_home'), 'm') . "\n";

  if (isset($permissions) && isset($permissions['mayview_admin']) && $permissions['mayview_admin'] == 'true') {

    echo '&nbsp;|&nbsp;<a class="m" href="index.php?">'. lang('menu_admin') .'</a>'."\n";

    if (isset($permissions['mayview_admin_project']) && $permissions['mayview_admin_project'] == 'true') {
      echo '&nbsp;|&nbsp;'.pageLink('project', lang('menu_projects'), 'm')."\n";
    }
    if (isset($permissions['mayview_admin_bugpriority']) && $permissions['mayview_admin_bugpriority'] == 'true') {
      echo '&nbsp;|&nbsp;'.pageLink('bugpriority', lang('menu_priority'), 'm') ."\n";
    }
    if (isset($permissions['mayview_admin_bugstatus']) && $permissions['mayview_admin_bugstatus'] == 'true') {
      echo '&nbsp;|&nbsp;'.pageLink('bugstatus', lang('menu_status'), 'm') ."\n";
    }
    if (isset($permissions['mayview_admin_categories']) && $permissions['mayview_admin_categories'] == 'true') {
      echo '&nbsp;|&nbsp;'.pageLink('bugcategory', lang('menu_category'), 'm') ."\n";
    }      
    if (isset($permissions['mayview_admin_permissions']) && $permissions['mayview_admin_permissions'] == 'true') {
      echo '&nbsp;|&nbsp;'.pageLink('permissions', lang('menu_permission'), 'm') ."\n";
    }
    if (isset($permissions['mayview_admin_users']) && $permissions['mayview_admin_users'] == 'true') {
      echo '&nbsp;|&nbsp;'.pageLink('users', lang('menu_users'), 'm') ."\n";
    }
    if (isset($permissions['mayview_admin_bbtags']) && $permissions['mayview_admin_bbtags'] == 'true') {
      echo '&nbsp;|&nbsp;'.pageLink('bbtags', lang('menu_bbtags'), 'm') ."\n";
    }
    if (isset($permissions['mayview_admin_editconfig']) && $permissions['mayview_admin_editconfig'] == 'true') {
      echo '&nbsp;|&nbsp;'.pageLink('editconfig', lang('menu_config'), 'm') ."\n";
    }
  }
}
