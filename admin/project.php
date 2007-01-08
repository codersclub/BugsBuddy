<?php
/*
 * Author:      Daan Keuper
 * Date:        12 June 2006
 * Version:     1.0
 * Description: add/remove and edit projects, and add users to a project
 */

function getproject() {
  $act = isset($_GET['act'])?$_GET['act']:null;
  $act = ($act == 'delete' || $act == 'edit' || $act == 'deleteConfirm' || $act == 'versionDelete') ? $act : 'view';

  if ($act == 'view' || $act == 'deleteConfirm') {
    $msg = '';
    $msg2 = '';
    $msg3 = '';
    $msg4 = '';

    if ($act == 'deleteConfirm' && isset($_GET['id'])) {
      $id = $_GET['id'];

      if (is_numeric($id)) {
        $check = Database::projectWithID($id);
        
        if (count($check) == 1) {
          Database::deleteProjectVersions($id);
          Database::deleteProject($id);
          $msg2 = lang('project_deleted_ok');
        } else {
          $msg2 = lang('project_unknown');
        }
      } else {
        $msg2 = lang('project_unknown');
      }
    }

    if (!empty($_POST)) {
      if (!isset($_POST['name']) || !isset($_POST['version'])) {
        $msg = lang('required_fields_fill');
      } else {
        if (count(Database::getProjectID($_POST['name'])) != 0) {
          $msg = lang('project_name_exists');
        } else {
          $visible = (isset($_POST['visible'])) ? 2 : 1;

          Database::insertProject($_POST['name'], $visible);
          Database::insertVersion($_POST['name'], $_POST['version']);

          $msg = lang('project_created', $_POST['name']);
        }
      }
    }

    $ret = '<h1>'. lang('project_add') .'</h1>';

    if (!empty($msg)) {
      $ret .= '<span class="error">' . $msg . '</span>';
    }

    $ret .= '<form action="index.php?'.(isset($_GET['js'])?'js=yes':'').'&page=project" method="post">' .
               <div class="registerinput">
                 <label class="registerlabel" for="name">'. lang('name'). ' *:</label>
                 <input  type="text" id="name" name="name" />
               </div>
               <div class="registerinput">
                 <label class="registerlabel" for="version">'. lang('version') .' *:</label>
                 <input  type="text" id="version" name="version" />
               </div>
               <div class="registerinput">
                 <label class="registerlabel" for="visible">'. lang('private') .':</label>
                 <input  type="checkbox" id="visible" name="visible" value="2" />
               </div>
               <div>
                 <label class="registerlabel" for="verzenden">'. lang('send') .':</label>
                 <input  id="verzenden" name="verzenden" type="submit" value="'.lang('send').'" />
               </div>
             </form>' .
             <p><span class="graytext">'. lang('required_fields') .'</span></p>';

    $ret .= '<h1>'. lang('projects_modify') .'</h1>';

    if (!empty($msg2)) {
      $ret .= '<span class="error">' . $msg2 . '</span>';
    }

    $projects = Database::getProjectList();

    if (count($projects) == 0) {
      $ret .= '<p><i>'. lang('projects_no') .'</i></p>';
    } else {
      $ret .= '<table>
                 <tr>
                   <th style="width: 200px;">'. lang('name'). '</th>
                   <th>' . lang('bugs_number') . '</th>
                   <th>'. lang('status') .'</th>
                   <th>&nbsp;</th>
                   <th>&nbsp;</th>
                 </tr>';

      foreach ($projects as $row) {
        $status = ($row['projectstatus_id'] == 1) ? lang('public') : lang('private');
        $bugs = Database::getCountWithProjectID($row['id']);
                
        $ret .= '<tr>
                   <td>' . $row['name'] . '</td>
                   <td>' . count($bugs) . '</td>
                   <td>' . $status . '</td>
                   <td><a href="index.php?'.(isset($_GET['js'])?'js=yes':'').'&page=project&act=edit&id=' . $row['id'] . '"><img src="../images/edit.png" alt="' . lang('edit') . '" title="' . lang('edit') . '" /></a></td>
                   <td><a href="index.php?' . (isset($_GET['js'])?'js=yes':'') . '&page=project&act=delete&id=' . $row['id'] . '"><img src="../images/delete.png" alt="' . lang('delete') . '" title="' . lang('delete') . '" /></a></td>
                 </tr>';
      }

      $ret .= '</table>';
    }
  } elseif ($act == 'delete') {
    $id = $_GET['id'];

    if (is_numeric($id)) {
      $check = Database::projectWithID($id);
      if (count($check) == 1) {
        $ret = '<h1>' . lang('project_delete') . '</h1>';
        
        $bugs = Database::getCountWithProjectID($id);

        if (count($bugs) > 0) {
          $ret .= '<p>' . lang('are_you_sure') . ' ' . lang('bugs_x_number',count($bugs)) . '</p>';
        } else {
          $ret .= '<p>' . lang('are_you_sure') . '</p>';
        }
        
        $ret .= '<p><a href="index.php?'.(isset($_GET['js'])?'js=yes':'').'&page=project">' . lang('no') . '</a> <a href="index.php?'.(isset($_GET['js'])?'js=yes':'').'&page=project&act=deleteConfirm&id=' . $id . '">' . lang('yes') . '</a></p>';
      } else {
        $ret .= '<p><i>' . lang('project_unknown') . '</i></p>';
      }
    } else {
      $ret .= '<p><i>' . lang('project_unknown') . '</i></p>';
    }
  } elseif ($act == 'edit') {
    $ret = '<h1>' . lang('project_edit') . '</h1>';

    $id = $_GET['id'];

    if (is_numeric($id)) {
      $msg = '';
      $msg2 = '';
      $msg3 = '';
      
      if (!empty($_POST)) {
        if ($_POST['act'] == 'project') {
          echo 'test';
          if (empty($_POST['name'])) {
            $msg = lang('required_fields_fill');
          } else {
            $visible = (isset($_POST['visible'])) ? 2 : 1;
            Database::updateProject($id, $_POST['name'], $visible);

            $msg = lang('project_updated', $_POST['name']);
          }
        } elseif ($_POST['act'] == 'users') {
          $users = Database::getNormalUserList();

          foreach ($users as $row) {
            $user_id = $row['id'];

            if (!empty($_POST[$user_id]) && !Database::hasUserAccess2Project($id, $user_id)) {
              Database::setProjectUser($id, $user_id);
            } elseif (empty($_POST[$user_id]) && Database::hasUserAccess2Project($id, $user_id)) {
              Database::deleteProjectUser($id, $user_id);
            }
          }

          $msg2 = lang('user_access_changed');
        } if ($_POST['act'] == 'versionnew') {
          if (!empty($_POST['name'])) {
            Database::insertVersionWithID($_POST['name'], $id);

            $msg3 = lang('version_created', $_POST['name']);
          } else {
            $msg3 = lang('name_empty');
          }
        }
      }

      if (!empty($_GET['version'])) {
        $version = $_GET['version'];

        if (is_numeric($version)) {
          $count = Database::versionWithID($version);

          if (count($count) == 1) {
            Database::deleteVersion($version);

            $msg4 = lang('version_removed');
          } else {
            $msg4 = lang('version_not_found');
          }
        }
      }

      $check = Database::getProject($id);

      if (count($check) == 1) {
        foreach ($check as $row) {
          $name = $row['name'];
          $hidden = $row['projectstatus_id'];
          $checkbox = ($hidden == 2) ? 'checked=\'checked\'' : '';
        }

        if (!empty($msg)) {
          $ret .= '<p><span class="error">' . $msg . '</span></p>';
        }

        $ret .= '<form action="index.php?'.(isset($_GET['js'])?'js=yes':'').'&page=project&act=edit&id=' . $id . '" method="post">
                   <input type="hidden" name="act" value="project" />
                   <div class="registerinput">
                     <label class="registerlabel" for="name">'. lang('name'). ' *:</label>
                     <input  type="text" id="name" name="name" value="' . $name . '" />
                   </div>
                   <div class="registerinput">
                     <label class="registerlabel" for="visible">'. lang('private') .':</label>
                     <input  type="checkbox" id="visible" name="visible" value="2"' . $checkbox . ' />
                   </div>
                   <div class="registerinput">
                     <label class="registerlabel" for="verzenden">'. lang('send') .':</label>
                     <input  id="verzenden" name="verzenden" type="submit" value="' . lang('change') . '" />
                   </div>
                </form>
                <p><span class="graytext">'. lang('required_fields') .'</span></p>';
        
        
        $ret .= '<h1>' . lang('version_new') . '</h1>';
                
        if (!empty($msg3)) {
          $ret .= '<p><span class="error">' . $msg3 . '</span></p>';
        }
      
        $ret .= '<form action="index.php?'.(isset($_GET['js'])?'js=yes':'').'&page=project&act=edit&id=' . $id . '" method="post">
                   <input type="hidden" name="act" value="versionnew" />
                   <div class="registerinput">
                     <label class="registerlabel" for="name">'. lang('name'). ':</label>
                     <input  type="text" id="name" name="name" />
                   </div>
                   <div class="registerinput">
                     <label class="registerlabel" for="verzenden">'. lang('send') .':</label>
                     <input  id="verzenden" name="verzenden" type="submit" value="'.lang('create').'" />
                   </div>
                 </form>';

        $ret .= '<h1>' . lang('versions_delete') . '</h1>';

        if (!empty($msg4)) {
          $ret .= '<p><span class="error">' . $msg4 . '</span></p>';
        }

        $versions = Database::getVersions($id);

        if (count($versions) > 0) {        
          $ret .= '<form action="index.php?'.(isset($_GET['js'])?'js=yes':'').'&page=project&act=versionDelete&id=' . $id . '" method="post">
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
            $ret .= '<form action="index.php?'.(isset($_GET['js'])?'js=yes':'').'&page=project&act=edit&id=' . $id . '" method="post" name="users">
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

              $ret .= '<td>' . $row['name'] . '</td>
                       <td>' . $row['email'] . '</td>
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
  } elseif ($act == 'versionDelete') {
    $id = $_POST['version'];
    $ret = '';

    if (is_numeric($id)) {
      $check = Database::versionWithID($id);
      if (count($check) == 1) {
        $ret .= '<h1>' . lang('version_delete') . '</h1>';
        
        $bugs = Database::getCountWithVersionID($id);

        if (count($bugs) > 0) {
          $ret .= '<p>' . lang('are_you_sure') . ' ' . lang('bugs_x_number',count($bugs)) . '</p>';
        } else {
          $ret .= '<p>' . lang('are_you_sure') . '</p>';
        }
        
        $ret .= '<p><a href="index.php?'.(isset($_GET['js'])?'js=yes':'').'&page=project&act=edit&id=' . $_GET['id'] . '">' . lang('no') . '</a> <a href="index.php?' . (isset($_GET['js'])?'js=yes':'') . '&page=project&act=edit&id=' . $_GET['id'] . '&version=' . $id . '">' . lang('yes') . '</a></p>';
      } else {
        $ret .= '<p><i>' . lang('version_unknown') . '</i></p>';
      }
    } else {
      $ret .= '<p><i>' . lang('version_unknown') . '</i></p>';
    }
  }

  return $ret;
}

