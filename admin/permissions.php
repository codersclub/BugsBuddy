<?php

/*
  View and edit usergroup permissions
*/

function getpermissions() {
  $returnValue = '<h1>' . lang('permissions') . '</h1>';

  if(isLoggedIn()) {
    $results     = Database::getPermissions(intval(getCurrentGroupId()));
    $permissions = Array();

    foreach($results as $result) {
      $permissions[$result['setting']] = $result['value'];
    }

    if (isset($permissions['mayview_admin_permissions']) && $permissions['mayview_admin_permissions'] == 'true') {
      if (@$_GET['delete'] == 'true' || @$_POST['submitit'] == 'true') {
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

  $id          = 0;
  $groupId     = 0;
  $setting     = '';
  $value       = '';
  $description = '';
  $returnValue = '';

  if ($recoverData && isset($_POST['groupid']) && isset($_POST['setting']) && isset($_POST['value']) && isset($_POST['description'])) {
    $groupId     = intval($_POST['groupid']);
    $setting     = $_POST['setting'];
    $value       = $_POST['value'];
    $description = $_POST['description'];
  }

  if(isset($_GET['id'])) {
    $id = intval($_GET['id']);

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

  $thisPage   = 'permissions';
  $currentUrl = getCurrentRequestUrl();
  $currentUrl = explode('&amp;id=', $currentUrl);
  $currentUrl = $currentUrl[0];

  $result = Database::getPermissionWithClause(true, 0);

  if(!empty($result)) {
    $returnValue .= '<table width="100%">
              <tr>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>' . lang('group') . '</th>
                <th>' . lang('keyword') . '</th>
                <th>' . lang('value') . '</th>
              </tr>';

    $i = 1;

    foreach ($result as $row) {
      if ($i % 2 == 0) {
        $returnValue .= '<tr class="gray">';
      } else {
        $returnValue .= '<tr>';
      }

      $returnValue .= '
                <td>'.pageLink('permissions&id='.$row['id'], lang('edit')) . '</td>
                <td>'.pageLink('permissions&id='.$row['id'].'&delete=true', lang('delete') ). '</td>
                <td>'.$row['groupName'].'</td>
                <td>'.$row['setting'].'</td>
                <td>'.$row['value'].'</td>
              </tr>';

      $i++;
    }

    $returnValue .=   '
              <tr>
                <td>&nbsp;</td>
              </tr>
            </table>';
  }

  $subTitle = $id ? lang('permission_edit') : lang('permission_add');

  $returnValue .= '
                <h2>'.$subTitle.'</h2>
                <form action="'.$currentUrl.'" method="POST">
                  <input type="hidden" name="page" value="'.$thisPage.'"/>
                  <input type="hidden" name="submitit" value="true"/>';
  if(!empty($id)) {
    $returnValue .= '<input type="hidden" name="id" value="'.$id.'"/>';
  }

  $returnValue .= '<div class="registerinput">
                     <label class="registerlabel" for="groupid">' . lang('group') . ':</label>
                     <select class="" id="groupid" name="groupid">'.getGroups($groupId).'</select>
                   </div>
                   <div class="registerinput">
                     <label class="registerlabel" for="setting">' . lang('keyword') . ':</label>
                     <input type="text" class="" id="setting" name="setting" size="66" value="'.$setting.'"/>
                   </div>
                   <div class="registerinput">
                     <label class="registerlabel" for="value">' . lang('value') . ':</label>
                     <input type="text" class="" id="value" name="value" value="'.$value.'"/>
                   </div>
                   <div class="registerinput">
                     <label class="registerlabel" for="description">'. lang('description') .':</label>
                     <textarea class="" id="description" name="description" cols="40" rows="6">'.$description.'</textarea>
                   </div>
                   <div class="registerinput">
                     <label class="registerlabel" for="verzenden">'. lang('send') .':</label>
                     <input class="" id="verzenden" name="verzenden" type="submit" value="'. lang('send') .'!"/>
                   </div>
                 </form>
               </td>
             </tr>
           </table>';

  return $returnValue;
}

function handlePermissionsForm() {
  $returnValue = '';

  $groupId    = 0;
  $setting    = '';
  $value      = '';
  $description  = '';

  if (isset($_POST['groupid']) && isset($_POST['setting']) && isset($_POST['value']) && isset($_POST['description'])) {
    $groupId     = intval($_POST['groupid']);
    $setting     = $_POST['setting'];
    $value       = $_POST['value'];
    $description = $_POST['description'];
    $pId         = intval($_POST['id']);
  }

  if(isset($_GET['id'])) {
    $id = intval($_GET['id']);
  }

  if(@$_GET['delete'] == 'true') {
    Database::delPermission($id);
  } else {
    $errorMessage = '';
    $error        = false;

    if(!is_numeric($groupId) || empty($setting) || empty($value) || empty($description)) {
      $error = true;
    }

    if ($error) {
      return getPermissionsForm(true) . nl2br($errorMessage);
    }

    if(empty($pId)) {
      Database::submitPermissions($groupId, htmlUnSafe($setting), htmlUnSafe($value), $description);
    } else {
      Database::updatePermission($pId, $groupId, htmlUnSafe($setting), htmlUnSafe($value), $description);
    }
  }

  return getPermissionsForm(false);
}


