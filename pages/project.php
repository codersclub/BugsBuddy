<?php

function getproject() {
  global $permissions;

  $id     = isset($_GET['id']) ? intval($_GET['id']) : 0;
  $action = isset($_GET['act']) ? $_GET['act'] : '';

  // Check for action
  if($id) {
    if($action=='edit') {
      return projectEdit($id);
    } else {
      return project($id);
    }
  } else {
    return projectList();
  }
}

//------------------------------------------------
function project($id=0) {

//  $ret = 'project('.$id.') started.';

    $ret = '<h1>' . lang('project_details') . '</h1>';

//htmlsafe($project['name'])
//    $id = $_GET['id'];

    if (is_numeric($id)) {
      $msg  = '';
      $msg2 = '';
      $msg3 = '';

      $check = Database::getProject($id);

      if (count($check) == 1) {
        foreach ($check as $row) {
          $name     = $row['name'];
          $hidden   = $row['projectstatus_id'];
          $checkbox = ($hidden == 2) ? "checked='checked'" : '';
        }

        if (!empty($msg)) {
          $ret .= '<p><span class="error">' . $msg . '</span></p>';
        }

        $ret .= '<p>
                   <div class="registerinput">
                     <label class="registerlabel">'. lang('name'). ':</label>
                     ' . $name . '
                   </div>
                   <div class="registerinput">
                     <label class="registerlabel">'. lang('private') .':</label>
                     <input type="checkbox" id="visible" name="visible" value="2"' . $checkbox . ' />
                   </div>';

        $versions = Database::getVersions($id);

        if (count($versions) > 0) {        
          $ret .= '<div class="registerinput">
                     <label class="registerlabel">'. lang('version') .':</label>
                     ';

          foreach ($versions as $row) {
            $ret .= '<span class="version">' . $row['version'] . '</span>';
          }
                 
          $ret .= '</div>';
        } else {
          $ret .= '<p><i>' . lang('versions_not_found') . '</i></p>';
        }



        if ($hidden == 2) {
          $ret .= '<h1>' . lang('user_access') . '</h1>';

          if (!empty($msg2)) {
            $ret .= '<p><span class="error">' .  $msg2 . '</span></p>';
          }

          $users = Database::getNormalUserList();
          
          if (count($users) > 0) {
            $ret .= '
                <input type="hidden" name="act" value="users" />
                  <table width="100%">
                  <tr>
                    <th>'. lang('name'). '</th>
                    <th>'. lang('email'). '</th>
                    <th>' . lang('access') . '</th>
                  </tr>';
            
            $i = 0;
            foreach ($users as $row) {
              if ($i % 2 != 0) {
                $ret .= '<tr class="gray">';
              } else {
                $ret .= '<tr>';
              }

              $checkbox = (Database::hasUserAccess2Project($id, $row['id'])) ? ' checked="checked"' : '';

              $ret .= '<td>' . $row['name'] . '</td>
                       <td>' . $row['email'] . '</td>
                       <td class="center"><input type="checkbox" name="' . $row['id'] . '" value="1"' . $checkbox . ' /></td>
                     </tr>';

              $i++;
            }
            
            $ret .= '<tr>
                       <td colspan="3" class="right"><input type="submit" value="' . lang('edit') . '" /></td>
                     </tr>
                   </table>';
          } else {
            $ret .= '<p><i>' . lang('users_not_found') . '</p></i>';
          }
        } elseif ($hidden = 1) {
          $ret .= '<h1>' . lang('users_linked') . '</h1>';

          $users = Database::getUsersFromProject($id);
          
          if (count($users) > 0) {
            $ret .= '<table>
                  <tr>
                    <th>'. lang('name'). '</th>
                    <th>'. lang('email'). '</th>
                  </tr>';
            
            $i = 0;
            foreach ($users as $row) {
              if ($i % 2 != 0) {
                $ret .= '<tr class="gray">';
              } else {
                $ret .= '<tr>';
              }

              $ret .= '<td>' . $row['name'] . '</td>
                       <td>' . $row['email'] . '</td>
                     </tr>';

              $i++;
            }
            
            $ret .= '</table>';
          } else {
            $ret .= '<p><i>' . lang('users_no_linked') . '</i></p>';
          }
        }
        require_once(ROOT_DIR.'/pages/buglist.php');
        $ret .= getbuglist();
      } else {
        $ret .= '<p><i>' . lang('project_not_found') . '</i></p>';
      }
    } else {
      $ret .= '<p><i>' . lang('project_not_found') . '</i></p>';
    }

  return $ret;
}

//------------------------------------------------
function projectEdit($id=0) {

//  $ret = 'project('.$id.') started.';

    $ret = '<h1>' . lang('project_details') . '</h1>';

//htmlsafe($project['name'])
//    $id = $_GET['id'];

    if (is_numeric($id)) {
      $msg  = '';
      $msg2 = '';
      $msg3 = '';

      $check = Database::getProject($id);

      if (count($check) == 1) {
        foreach ($check as $row) {
          $name     = $row['name'];
          $hidden   = $row['projectstatus_id'];
          $checkbox = ($hidden == 2) ? "checked='checked'" : '';
        }

        if (!empty($msg)) {
          $ret .= '<p><span class="error">' . $msg . '</span></p>';
        }

        $ret .= '<form action="index.php?page=project&id=' . $id . '&act=edit" method="post">
                   <input type="hidden" name="act" value="project" />
                   <div class="registerinput">
                     <label for="name" class="registerlabel">'. lang('name'). ' *:</label>
                     <input type="text" id="name" name="name" value="' . $name . '" />
                   </div>
                   <div class="registerinput">
                     <label for="visible" class="registerlabel">'. lang('private') .':</label>
                     <input class="registerinput"  type="checkbox" id="visible" name="visible" value="2"' . $checkbox . ' />
                   </div>
                   <div class="registerinput">
                     <label for="verzenden" class="registerlabel">'. lang('send') .':</label>
                     <input class="registerinput"  id="verzenden" name="verzenden" type="submit" value="' . lang('change') . '" />
                   </div>
                   <p><span class="graytext">'. lang('required_fields') .'</span></p>
                 </form>';
        
        $ret .= '<h1>' . lang('versions_delete') . '</h1>';

        if (!empty($msg4)) {
          $ret .= '<p><span class="error">' . $msg4 . '</span></p>';
        }

        $versions = Database::getVersions($id);

        if (count($versions) > 0) {        
          $ret .= '<form action="index.php?page=project&id=' . $id . '&act=versionDelete" method="post">
                     <div class="registerinput">
                       <label class="registerlabel" for="name">'. lang('version') .':</label>
                       <select name="version">';

          foreach ($versions as $row) {
            $ret .= '<option value="' . $row['id'] . '">' . $row['version'] . '</option>';
          }
                 
          $ret .= '</select>
                     </div>
                     <div class="registerinput">
                       <label class="registerlabel" for="verzenden">'. lang('send') .':</label>
                       <input  id="verzenden" name="verzenden" type="submit" value="' . lang('delete') . '" />
                     </div>
                   </form>';
        } else {
          $ret .= '<p><i>' . lang('versions_not_found') . '</i></p>';
        }

        if ($hidden == 2) {
          $ret .= '<h1>' . lang('user_access') . '</h1>';

          if (!empty($msg2)) {
            $ret .= '<p><span class="error">' .  $msg2 . '</span></p>';
          }

          $users = Database::getNormalUserList();
          
          if (count($users) > 0) {
            $ret .= '<form action="index.php?page=project&id=' . $id . '&act=edit" method="post" name="users">
                <input type="hidden" name="act" value="users" />
                  <table>
                    <tr>
                      <th style="width: 200px;">'. lang('name'). '</th>
                      <th style="width: 200px;">'. lang('email'). '</th>
                      <th style="width: 50px;">' . lang('access') . '</th>
                    </tr>';
            
            $i = 0;
            foreach ($users as $row) {
              if ($i % 2 != 0) {
                $ret .= '<tr class="gray">';
              } else {
                $ret .= '<tr>';
              }

              $checkbox = (Database::hasUserAccess2Project($id, $row['id'])) ? ' checked="checked"' : '';

              $ret .= '<td>' . $row['name'] . '</td><td>' . $row['email'] . '</td>
                       <td class="center"><input type="checkbox" name="' . $row['id'] . '" value="1"' . $checkbox . ' /></td>
                     </tr>';

              $i++;
            }
            
            $ret .= '<tr>
                       <td colspan="3" class="right"><input type="submit" value="' . lang('edit') . '" /></td>
                     </tr>
                   </table>
                 </form>';
          } else {
            $ret .= '<p><i>' . lang('users_not_found') . '</p></i>';
          }
        } elseif ($hidden = 1) {
          $ret .= '<h1>' . lang('users_linked') . '</h1>';

          $users = Database::getUsersFromProject($id);
          
          if (count($users) > 0) {
            $ret .= '<table>
                  <tr>
                    <th style="width: 200px;">'. lang('name'). '</th>
                    <th style="width: 200px;">'. lang('email'). '</th>
                  </tr>';
            
            $i = 0;
            foreach ($users as $row) {
              if ($i % 2 != 0) {
                $ret .= '<tr class="gray">';
              } else {
                $ret .= '<tr>';
              }

              $ret .= '<td>' . $row['name'] . '</td>
                       <td>' . $row['email'] . '</td>
                     </tr>';

              $i++;
            }
            
            $ret .= '</table>';
          } else {
            $ret .= '<p><i>' . lang('users_no_linked') . '</p></i>';
          }
        }
      } else {
        $ret .= '<p><i>' . lang('project_not_found') . '</i></p>';
      }
    } else {
      $ret .= '<p><i>' . lang('project_not_found') . '</i></p>';
    }

  return $ret;
}

//------------------------------------------------
function projectList() {

  $projects = Database::getProjectList();

  $ret = '<h1>'.lang('projects').'</h1>';

  if (count($projects) == 0) {
    $ret .= '<p><i>'. lang('projects_no') .'</i></p>';

  } else {

    $ret .= '
        <table width="100%">
          <tr>
            <th style="width: 200px;">'. lang('name'). '</th>
            <th>' . lang('bugs_number') . '</th>
            <th>'. lang('status') .'</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>';

    $i = 1;
    foreach ($projects as $row) {
      $status = ($row['projectstatus_id'] == 1) ? lang('public') : lang('private');

      $bugs = Database::getCountWithProjectID($row['id']);
              
      if (isset($permissions['mayview_admin_project']) && $permissions['mayview_admin_project'] == 'true') {
        $edit_link = pageLink('project&id=' . $row['id'] . '&act=edit', '<img src="../images/edit.png" alt="' . lang('edit') . '" title="' . lang('edit') . '" />');
        $del_link  = pageLink('project&id=' . $row['id'] . '&act=delete', '<img src="../images/delete.png" alt="' . lang('delete') . '" title="' . lang('delete') . '" />');
      } else {
        $edit_link = '&nbsp;';
        $del_link = '&nbsp;';
      }

      if ($i++ % 2 == 0) {
        $ret .= '<tr class="gray">';
      } else {
        $ret .= '<tr>';
      }

      $ret .= '
            <td>
              ';
      if($row['projectstatus_id'] == 1) { // Public Project
        $ret .= '<a href="?page=project&id='.$row['id'].'">'. $row['name'] . '</a>';
      } else {
        $ret .= $row['name'];
      }
      $ret .= '
            </td>
            <td>' . count($bugs) . '</td>
            <td>' . $status . '</td>
            <td>' . $edit_link . '</td>
            <td>' . $del_link . '</td>
          </tr>';
    }

    $ret .= '
          </table>';
  }

  return $ret;

}

