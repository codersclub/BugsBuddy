<?php

function getproject() {
  global $permissions;

  $id     = isset($_GET['id']) ? intval($_GET['id']) : 0;
  $action = isset($_GET['act']) ? $_GET['act'] : '';

  // Check for action
  if($id) {
    return project($id);
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
      $msg = '';
      $msg2 = '';
      $msg3 = '';

      $check = Database::getProject($id);

      if (count($check) == 1) {
        foreach ($check as $row) {
          $name = $row['name'];
          $hidden = $row['projectstatus_id'];
          $checkbox = ($hidden == 2) ? "checked='checked'" : '';
        }

        if (!empty($msg)) {
          $ret .= '<p><span class="error">' . $msg . '</span></p>';
        }

        $ret .= '<form action="index.php?'.(isset($_GET['js'])?'js=yes':'').'&page=project&id=' . $id . '&act=edit" method="post"><p>' .
            '<input type="hidden" name="act" value="project" />' .
            '<div class="clear"><label for="name" class="registerlabel">'. lang('name'). ' *:</label><input class="registerinput" type="text" id="name" name="name" value="' . $name . '" /></div>' .
            '<div class="clear"><label for="visible" class="registerlabel">'. lang('private') .':</label><input class="registerinput"  type="checkbox" id="visible" name="visible" value="2"' . $checkbox . ' /></div>' .
            '<div class="clear"><label for="verzenden" class="registerlabel">'. lang('send') .':</label><input class="registerinput"  id="verzenden" name="verzenden" type="submit" value="' . lang('change') . '" /></div>'.
            '<p><span class="graytext">'. lang('required_fields') .'</span></p>' .
            '</p></form>';
        
        
        $ret .= '<h1>' . lang('versions_delete') . '</h1>';

        if (!empty($msg4)) {
          $ret .= '<p><span class="error">' . $msg4 . '</span></p>';
        }

        $versions = Database::getVersions($id);

        if (count($versions) > 0) {        
          $ret .= '<form action="index.php?'.(isset($_GET['js'])?'js=yes':'').'&page=project&id=' . $id . '&act=versionDelete" method="post"><p>
               <div class="registerlabel"><label for="name">'. lang('version') .':</label></div><div class="registerinput"><select name="version">';

          foreach ($versions as $row) {
            $ret .= '<option value="' . $row['id'] . '">' . $row['version'] . '</option>';
          }
                 
          $ret .= '</select></div>
               <div class="registerlabel"><label for="verzenden">'. lang('send') .':</label></div><div class="registerinput"><input  id="verzenden" name="verzenden" type="submit" value="' . lang('delete') . '" /></div>
               </p></form>';
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
            $ret .= '<form action="index.php?'.(isset($_GET['js'])?'js=yes':'').'&page=project&id=' . $id . '&act=edit" method="post" name="users">
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

              $ret .= '<td>' . $row['name'] . '</td><td>' . $row['email'] . '</td><td class="center"><input type="checkbox" name="' . $row['id'] . '" value="1"' . $checkbox . ' /></td></tr>';

              $i++;
            }
            
            $ret .= '<tr><td colspan="3" class="right"><input type="submit" value="' . lang('edit') . '" /></td></tr></table></form>';
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

              $ret .= '<td>' . $row['name'] . '</td><td>' . $row['email'] . '</td></tr>';

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

    $ret .= "
<table>
  <tr>
    <th style='width: 200px;'>". lang('name'). "</th>
    <th>" . lang('bugs_number') . "</th>
    <th>". lang('status') ."</th>
    <th>&nbsp;</th>
    <th>&nbsp;</th>
  </tr>\n";

    $js = isset($_GET['js'] ? 'js=yes' : '';

    foreach ($projects as $row) {

      $status = ($row['projectstatus_id'] == 1) ? lang('public') : lang('private');

      $bugs = Database::getCountWithProjectID($row['id']);
              
      if (isset($permissions['mayview_admin_project']) && $permissions['mayview_admin_project'] == 'true') {
        $edit_link = "<a href='index.php?" . $js . "&page=project&id=" . $row['id'] . "&act=edit'><img src='../images/edit.png' alt='" . lang('edit') . "' title='" . lang('edit') . "' /></a>";
        $del_link = "<a href='index.php?" . $js . "&page=project&id=" . $row['id'] . "&act=delete'><img src='../images/delete.png' alt='" . lang('delete') . "' title='" . lang('delete') . "' /></a>";
      } else {
        $edit_link = "&nbsp;";
        $del_link = "&nbsp;";
      }

      $ret .= "
  <tr>
    <td>\n";
      if($row['projectstatus_id'] == 1) { // Public Project
        $ret .=  "      <a href='?page=project&id=".$row['id']."'>". $row['name'] . "</a>\n";
      } else {
        $ret .=  "      ".$row['name']."\n";
      }
      $ret .= "    </td>
    <td>" . count($bugs) . "</td>
    <td>" . $status . "</td>
    <td>".$edit_link."</td>
    <td>".$del_link."</td>
  </tr>\n";
    }

    $ret .= "</table>\n";
  }

  return $ret;

/*
  $maximum = 16;
  $bugcount = Database::getVisibleBugCount(getCurrentUserId());
  $bugcount = intval($bugcount[0]['count(b.id)'])-1;
  //if ($bugcount <= 0) $bugcount = 1;
  $totalPages = ceil(($bugcount+1) / $maximum);
  
  if (!isset($_GET['sort'])) {
    $_GET['sort'] = 'date';
  } else {
    if ($_GET['sort'] != 'description' &&
        $_GET['sort'] != 'poster' &&
        $_GET['sort'] != 'date' &&
        $_GET['sort'] != 'status' &&
        $_GET['sort'] != 'projectversion') {
      $_GET['sort'] = 'date';
    }
  }
  if (!isset($_GET['order'])) {
    $_GET['order'] = 'desc';
  } else {
    if ($_GET['order'] != 'asc' && $_GET['order'] != 'desc') {
      $_GET['order'] = 'desc';
    }
  }

  if (!isset($_GET['page'])) {
    $_GET['page'] = 1;
  }
  
  $page = intval($_GET['page']);
  
  $result = Database::getBugAll(getCurrentUserId(), ($page-1)*$maximum, $maximum, $_GET['sort'], $_GET['order']);

  $returnValue = '<h1>'.sprintf(lang('bugs_total', $bugcount+1)). '</h1>';

  if (empty($result)) {
    $returnValue .= '<p><i>'. lang('bugs_no'). '</i></p>';
  } else {


  $returnValue .= '<div style="text-align: center;">';
  if ($page != 1) {
    $returnValue.= pageLink('buglist&page='.($page-1).'&sort='.$_GET['sort'].'&order='.$_GET['order'], '<<').'&nbsp;&nbsp;';
  } else {
    $returnValue .= '&lt;&lt;&nbsp;';
  }

  $shownPreviousPageNumber = true;
  $showThisPageNumber = true;
  for ($i=0; $i<$totalPages; $i++) {
    $shownPreviousPageNumber = $showThisPageNumber;
    $showThisPageNumber = false;
    if ($i >= 0 && $i <= 2) $showThisPageNumber = true;
    if ($i >= $totalPages-3 && $i < $totalPages) $showThisPageNumber = true;
    if ($i-($page-1) >= -3 && $i-($page-1) <= 3) $showThisPageNumber = true;
    if ($showThisPageNumber != false) {
      if ($i!=0) {
        $returnValue .= '&nbsp;&nbsp;';
      }
      if (!$shownPreviousPageNumber) {
        $returnValue .= '...&nbsp;&nbsp;';
      }

      if ($page == ($i+1)) {
        $returnValue.= pageLink('buglist&page='.($i+1).'&sort='.$_GET['sort'].'&order='.$_GET['order'], ''.($i+1), 'red');
      } else {
        $returnValue.= pageLink('buglist&page='.($i+1).'&sort='.$_GET['sort'].'&order='.$_GET['order'], ''.($i+1));
      }
    }
  }
  if ($page != $totalPages) {
    $returnValue.= '&nbsp;&nbsp;'.pageLink('buglist&page='.($page+1).'&sort='.$_GET['sort'].'&order='.$_GET['order'], '>>');
  } else {
    $returnValue.= '&nbsp;&gt;&gt;';
  }

  $returnValue .= '</div>';
    $returnValue .= '<table>' .
      '<tr>' .
        '<th style="width: 200px;">'.pageLink('buglist&page=1&sort=description&order=asc', lang('description')).'</th>' .
        '<th style="width: 100px;">'.pageLink('buglist&page=1&sort=poster&order=asc', lang('poster')).'</th>' .
        '<th style="width: 120px;">'.pageLink('buglist&page=1&sort=date&order=desc', lang('date')).'</th>' .
        '<th style="width: 100px;">'.pageLink('buglist&page=1&sort=status&order=asc', lang('status')).'</th>' .
        '<th style="width: 140px;">'.pageLink('buglist&page=1&sort=projectversion&order=asc', lang('project_version')).'</th>' .
      '</tr>';
    $i = 1;
    
    foreach ($result as $row) {
      if ($i % 2 == 0) {
        $returnValue .= '<tr class="gray">';
      } else {
        $returnValue .= '<tr>';
      }
      //<a href="index.php?p=bugs&id=' . $row['id'] . '">' . $row['title'] . '</a>
//print_r($row);
//exit;
      $returnValue .= '<td>'.pageLink('viewbug&id='.$row['id'], $row['title']).'</td>' .
              '<td>' . $row['username'] . '</td>' .
              '<td>' . timestamp2date($row['submitdate']) . '</td>' .
              '<td>' . $row['status'] . '</td>' .
              '<td>' . $row['projectname'] . ' ' . $row['projectversion'] . '</td>' .
              '</tr>';      
      $i++;
    }
    
    $returnValue .= '</table>';    

  }
  
  return $returnValue;

*/
}

