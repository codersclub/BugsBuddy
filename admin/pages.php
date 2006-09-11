<?php

function showAdminPages($permissions) {

  $numberOfLinks = 0;

  if (isset($permissions) && isset($permissions['mayview_admin']) && $permissions['mayview_admin'] == 'true') {
    if (isset($permissions['mayview_admin_bbtags']) && $permissions['mayview_admin_bbtags'] == 'true'){
      echo '&nbsp;&nbsp;<a class="m" href="index.php?js='.((isset($_GET['js'])&&$_GET['js']=='yes')?'yes':'no').'&page=bbtags">' . lang('menu_bbtags') . '</a>';
    }
    if (isset($permissions['mayview_admin_bugpriority']) && $permissions['mayview_admin_bugpriority'] == 'true'){
      echo '&nbsp;&nbsp;<a class="m" href="index.php?js='.((isset($_GET['js'])&&$_GET['js']=='yes')?'yes':'no').'&page=bugpriority">' . lang('menu_priority') . '</a>';
    }
    if (isset($permissions['mayview_admin_bugstatus']) && $permissions['mayview_admin_bugstatus'] == 'true'){
      echo '&nbsp;&nbsp;<a class="m" href="index.php?js='.((isset($_GET['js'])&&$_GET['js']=='yes')?'yes':'no').'&page=bugstatus">' . lang('menu_status') . '</a>';
    }
    if (isset($permissions['mayview_admin_categories']) && $permissions['mayview_admin_categories'] == 'true'){
      echo '&nbsp;&nbsp;<a class="m" href="index.php?js='.((isset($_GET['js'])&&$_GET['js']=='yes')?'yes':'no').'&page=bugcategory">' . lang('menu_category') . '</a>';
    }      
    if (isset($permissions['mayview_admin_editconfig']) && $permissions['mayview_admin_editconfig'] == 'true'){
      echo '&nbsp;&nbsp;<a class="m" href="index.php?js='.((isset($_GET['js'])&&$_GET['js']=='yes')?'yes':'no').'&page=editconfig">' . lang('menu_config') . '</a>';
    }
    if (isset($permissions['mayview_admin_permissions']) && $permissions['mayview_admin_permissions'] == 'true'){
      echo '&nbsp;&nbsp;<a class="m" href="index.php?js='.((isset($_GET['js'])&&$_GET['js']=='yes')?'yes':'no').'&page=permissions">' . lang('menu_permission') . '</a>';
    }
    if (isset($permissions['mayview_admin_project']) && $permissions['mayview_admin_project'] == 'true'){
      echo '&nbsp;&nbsp;<a class="m" href="index.php?js='.((isset($_GET['js'])&&$_GET['js']=='yes')?'yes':'no').'&page=project">'. lang('menu_projects') .'</a>';
    }
    if (isset($permissions['mayview_admin_users']) && $permissions['mayview_admin_users'] == 'true'){
      echo '&nbsp;&nbsp;<a class="m" href="index.php?js='.((isset($_GET['js'])&&$_GET['js']=='yes')?'yes':'no').'&page=users">' . lang('menu_users') . '</a>';
    }
  }
}
