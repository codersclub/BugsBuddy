<?php

function showAdminPages($permissions) {

  $numberOfLinks = 0;

  echo '<a class="m" href="../">' . lang('menu_home') . '</a>'."\n";

  if (isset($permissions) && isset($permissions['mayview_admin']) && $permissions['mayview_admin'] == 'true') {

    echo '&nbsp;|&nbsp;<a class="m" href="index.php?">'. lang('menu_admin') .'</a>'."\n";

    if (isset($permissions['mayview_admin_project']) && $permissions['mayview_admin_project'] == 'true') {
      echo '&nbsp;|&nbsp;<a class="m" href="index.php?page=project">'. lang('menu_projects') .'</a>'."\n";
    }
    if (isset($permissions['mayview_admin_bugpriority']) && $permissions['mayview_admin_bugpriority'] == 'true') {
      echo '&nbsp;|&nbsp;<a class="m" href="index.php?page=bugpriority">' . lang('menu_priority') . '</a>'."\n";
    }
    if (isset($permissions['mayview_admin_bugstatus']) && $permissions['mayview_admin_bugstatus'] == 'true') {
      echo '&nbsp;|&nbsp;<a class="m" href="index.php?page=bugstatus">' . lang('menu_status') . '</a>'."\n";
    }
    if (isset($permissions['mayview_admin_categories']) && $permissions['mayview_admin_categories'] == 'true') {
      echo '&nbsp;|&nbsp;<a class="m" href="index.php?page=bugcategory">' . lang('menu_category') . '</a>'."\n";
    }      
    if (isset($permissions['mayview_admin_permissions']) && $permissions['mayview_admin_permissions'] == 'true') {
      echo '&nbsp;|&nbsp;<a class="m" href="index.php?page=permissions">' . lang('menu_permission') . '</a>'."\n";
    }
    if (isset($permissions['mayview_admin_users']) && $permissions['mayview_admin_users'] == 'true') {
      echo '&nbsp;|&nbsp;<a class="m" href="index.php?page=users">' . lang('menu_users') . '</a>'."\n";
    }
    if (isset($permissions['mayview_admin_bbtags']) && $permissions['mayview_admin_bbtags'] == 'true') {
      echo '&nbsp;|&nbsp;<a class="m" href="index.php?page=bbtags">' . lang('menu_bbtags') . '</a>'."\n";
    }
    if (isset($permissions['mayview_admin_editconfig']) && $permissions['mayview_admin_editconfig'] == 'true') {
      echo '&nbsp;|&nbsp;<a class="m" href="index.php?page=editconfig">' . lang('menu_config') . '</a>'."\n";
    }
  }
}
