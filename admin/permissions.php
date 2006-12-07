<?php

/*
  View and edit usergroup permissions
*/

function getpermissions() {
  $returnValue = '';
  $returnValue .= '<h1>' . lang('permissions') . '</h1>';
  
  if(isLoggedIn()) {
    $results     = Database::getPermissions(intval(getCurrentGroupId()));
    $permissions   = Array();
  
    foreach($results as $result) {
      $permissions[$result['setting']] = $result['value'];
    }
    
    if (isset($permissions['mayview_admin_permissions']) && $permissions['mayview_admin_permissions'] == 'true') {    
      if (isset($_GET) && (isset($_GET['delete']) && $_GET['delete'] == 'true') || (isset($_GET['submitit']) && $_GET['submitit'] == 'true')) {
        $returnValue .= handlePermissionsForm();
      } else {
        $returnValue .= getPermissionsForm(true); 
      }
    } else {
      $returnValue = lang('permission_not_enough');
    }
  } else {
    $returnValue = lang('login_required_for_this');
  }  
  
  return $returnValue;
}

function getGroups($groupId) {
  $returnValue = '';
  
  $result = Database::getGroups();
        
  if (!empty($result)) {
    foreach ($result as $row) {
      if ($row['id'] == $groupId) {
        $returnValue .= '<option value="'.$row['id'].'" SELECTED>'.htmlSafe($row['name']).'</option>';
      } else {
        $returnValue .= '<option value="'.$row['id'].'">'.htmlSafe($row['name']).'</option>';
      }
    }
  }  
  
  return $returnValue;
}

function getPermissionsForm($recoverData) {
  $returnValue = '';
  
  $id        = 0;
  $groupId    = 0;
  $setting    = '';
  $value      = '';
  $description  = '';
  
  if ($recoverData && isset($_GET) && isset($_GET['groupid']) && isset($_GET['setting']) && isset($_GET['value']) && isset($_GET['description'])) {
    $groupId     = $_GET['groupid'];
    $setting     = $_GET['setting'];
    $value       = $_GET['value'];
    $description = $_GET['description'];  
  }

  if(isset($_GET) && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $result = Database::getPermissionWithClause(false, $id);
    
    if(!empty($result)) {
      foreach ($result as $row) {
        $groupId     = $row['level_id'];
        $setting     = $row['setting'];
        $value       = $row['value'];
        $description = $row['description'];
      }
    }
  }    
  
  $thisPage   = "permissions";
  $currentUrl = getCurrentRequestUrl();
  $currentUrl = explode("?", $currentUrl);
  $currentUrl = $currentUrl[0];
  
  $result = Database::getPermissionWithClause(true, 0);
  
  if(!empty($result)) {
    $returnValue .= '<table style="width: 100%;">'.
              '<tr>'.
                '<th>&nbsp;</th><th>&nbsp;</th><th>' . lang('group') . '</th><th>' . lang('keyword') . '</th><th>' . lang('value') . '</th>'.
              '</tr>';
    
    $i = 1;              
              
    foreach ($result as $row) {
      if ($i % 2 == 0) {
        $returnValue .= '<tr class="gray">';
      } else {
        $returnValue .= '<tr>';
      }      
      
      $returnValue .=   '<td><a href="index.php?'.(isset($_GET['js'])?'js=yes':'').'&page=permissions&id='.$row['id'].'">' . lang('edit') . '</a></td><td><a href="index.php?'.(isset($_GET['js'])?'js=yes':'').'&page=permissions&id='.$row['id'].'&delete=true">' . lang('delete') . '</a></td><td>'.$row['groupName'].'</td><td>'.$row['setting'].'</td><td>'.$row['value'].'</td>'.
              '</tr>';
              
      $i++;
    }
    
    $returnValue .=   '<tr>'.
                '<td>&nbsp;</td>'.
              '</tr>'.
            '</table>';
  }
    
  $returnValue .= '<table>'.
            '<tr>'.
              '<td>'.
                '<h2>'.lang('permission_add').'</h2>'.
                '<form action="'.$currentUrl.'" method="get">'.
                  '<div><input type="hidden" name="page" value="'.$thisPage.'"/></div>'.
                  '<div><input type="hidden" name="submitit" value="true"/></div>';
  if (isset($_GET['js'])) {
    $returnValue .= '<div><input type="hidden" name="js" value="yes"/></div>';
  }                    
  if(!empty($id)) {
    $returnValue .= '<div><input type="hidden" name="id" value="'.$id.'"/></div>';
  }
                
  $returnValue .= '<div class="registerlabel"><label for="groupid">' . lang('group') . ':</label></div><div class="registerinput"><select class="" id="groupid" name="groupid">'.getGroups($groupId).'</select></div>'.
                  '<div class="registerlabel"><label for="setting">' . lang('keyword') . ':</label></div><div class="registerinput"><input type="text" class="" id="setting" name="setting" size="66" value="'.$setting.'"/></div>'.            
                  '<div class="registerlabel"><label for="value">' . lang('value') . ':</label></div><div class="registerinput"><input type="text" class="" id="value" name="value" value="'.$value.'"/></div>'.
                  '<div class="registerlabel"><label for="description">'. lang('description') .':</label></div><div class="registerinput"><textarea class="" id="description" name="description" cols="40" rows="6">'.$description.'</textarea></div>'.
                  '<div class="registerlabel"><label for="verzenden">'. lang('send') .':</label></div><div class="registerinput"><input class="" id="verzenden" name="verzenden" type="submit" value="'. lang('send') .'!"/></div>'.
                '</form>'.
              '</td>'.
            '</tr>'.
          '</table>';
  
  return $returnValue;
}

function handlePermissionsForm() {
  $returnValue = '';
  
  $groupId    = 0;
  $setting    = '';
  $value      = '';
  $description  = '';
  
  if (isset($_GET) && isset($_GET['groupid']) && isset($_GET['setting']) && isset($_GET['value']) && isset($_GET['description'])) {
    $groupId     = $_GET['groupid'];
    $setting     = $_GET['setting'];
    $value       = $_GET['value'];
    $description = $_GET['description'];  
  }  
  
  if(isset($_GET) && isset($_GET['id'])) {
    $id = $_GET['id'];
  }  

  if(isset($_GET) && isset($_GET['delete']) && $_GET['delete'] == 'true') {    
    Database::delPermission($id);  
  } else {
    $errorMessage   = '';
    $error       = false;  
      
    if(!is_numeric($groupId) || empty($setting) || empty($value) || empty($description)) {
      $error = true;
    }
  
    if ($error) {
      return getPermissionsForm(true) . nl2br($errorMessage);
    }  
    
    if(empty($id)) {
      Database::submitPermissions($groupId, htmlUnSafe($setting), htmlUnSafe($value), $description);
    } else {
      Database::updatePermission($id, $groupId, htmlUnSafe($setting), htmlUnSafe($value), $description);  
    }
  }    
  
  return getPermissionsForm(false);
}


