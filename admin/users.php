<?php

/*
  View and edit users
*/
function getusers() {
  if (@$_GET['delete'] == 'true' || @$_GET['submitit'] == 'true') {
    $returnValue = handleForm();
  } else {
    $returnValue = outputForm(true); 
  }
  return $returnValue;
}

function outputForm($recoverData) {
  $returnValue = '';
  $returnValue .= '<h1>'.lang('users').'</h1>';
  $returnValue .= '<div class="right">
                     '.pageLink('page=register', lang('user_add')) .'
                   </div>';

  $result = Database::getAllUsers();
/*
echo '<pre>';
print_r($result);
echo '</pre>';
*/
  if(!empty($result)) {
    $returnValue .= '
            <table style="width: 100%;">
              <tr>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>'. lang('name'). '</th>
                <th>'. lang('email'). '</th>
                <th>' . lang('group') . '</th>
              </tr>';
    $i = 1;              
    foreach ($result as $row) {
      if ($i % 2 == 0) {
        $returnValue .= '<tr class="gray">';
      } else {
        $returnValue .= '<tr>';
      }      
      $returnValue .=   '<td>'.pageLink('users&id='.$row['id'], lang('edit')) . '</td>
                <td>'.pageLink('users&id='.$row['id'].'&delete=true', lang('delete')) . '</td>
                <td>'.htmlSafe($row['name']).'</td><td>'.htmlSafe($row['email']).'</td>
                <td>'.htmlSafe($row['groupname']).'</td>
              </tr>';
      $i++;
    }
    $returnValue .=   '<tr>
                <td>&nbsp;</td>
              </tr>
            </table>';
  }
  
  $id      = 0;
  $groupId = 0;
  $name    = '';
  $email   = '';
  $description  = '';
  if ($recoverData && isset($_GET['groupid']) && isset($_GET['name']) && isset($_GET['email']) && isset($_GET['description'])) {
    $groupId = intval($_GET['groupid']);
    $name    = $_GET['name'];
    $email   = $_GET['email'];
  }
  
  if(isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $result = Database::getUserById($id);
    
    if(!empty($result)) {
      foreach ($result as $row) {
        $groupId = $row['group_id'];
        $name    = $row['name'];
        $email   = $row['email'];
      }
    }
  }

  if (isset($_GET['id']) && !isset($_GET['submitit'])) {
    $thisPage   = 'users';
    $currentUrl = getCurrentRequestUrl();
    $currentUrl = explode('?', $currentUrl);
    $currentUrl = $currentUrl[0];
    $returnValue .= '
                  <form action="'.$currentUrl.'" method="get">
                    <input type="hidden" name="submitit" value="true"/>
                    <input type="hidden" name="page" value="'.$thisPage.'"/>
                    <input type="hidden" name="submitit" value="true"/>';
    if(!empty($id)) {
      $returnValue .= '<input type="hidden" name="id" value="'.$id.'"/>';
    }
                  
    $returnValue .=  '<div class="registerinput">
                       <label for="name" class="registerlabel">'. lang('name'). ':</label>
                       <input type="text" class="" id="name" name="name" size="40" value="'.$name.'"/>
                     </div>
                     <div class="registerinput">
                       <label for="email" class="registerlabel">'. lang('email'). ':</label>
                       <input type="text" class="" id="email" name="email" size="40" value="'.$email.'"/>
                     </div>';
    if (getCurrentGroupId() == 3) {
      $returnValue .= '<div class="registerinput">
                         <label for="groupid" class="registerlabel">' . lang('group') . ':</label>
                         <select class="" id="groupid" name="groupid">'.getGroups($groupId).'</select>
                       </div>';
    }
    $returnValue .=  '<div class="registerinput">
                        <label for="verzenden" class="registerlabel">'. lang('send') .':</label>
                        <input class="" id="verzenden" name="verzenden" type="submit" value="'. lang('send') .'!"/>
                      </div>
                    </form>';
  }
  
  return $returnValue;
}

function handleForm() {
  // Delete
  if (isset($_GET['delete']) && $_GET['delete']=='true' && isset($_GET['id'])) {
    $id=intval($_GET['id']);
    if (getCurrentGroupId() == 3) {
      Database::deleteUserById(intval($_GET['id']));
    }

  // Edit
  } elseif (isset($_GET['name']) && isset($_GET['email']) && isset($_GET['groupid']) && isset($_GET['id'])) {
    $name    = $_GET['name'];
    $email   = $_GET['email'];
    $groupid = intval($_GET['groupid']);
    $id      = intval($_GET['id']);

    Database::updateUser($id, $name, $email, ((getCurrentGroupId()!=3)?null:$groupid));
  
  } else {
  }
  return outputForm(false);
  
}

function getGroups($groupId) {
  $returnValue = '';
  
  $result = Database::getGroups();
        
  if (!empty($result)) {
    foreach ($result as $row) {
      if ($row['id'] == $groupId) {
        $returnValue .= '<option value="'.$row['id'].'" selected="selected">'.htmlSafe($row['name']).'</option>';
      } else {
        $returnValue .= '<option value="'.$row['id'].'">'.htmlSafe($row['name']).'</option>';
      }
    }
  }  
  
  return $returnValue;
}

