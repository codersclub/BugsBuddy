<?php

/*
  If somebody wants to view a bug
*/

function getviewbug() {
  $returnValue = '<h1>' . lang('bug_info') . '</h1>';
  
  if (isset($_GET) && isset($_GET['submitit']) && $_GET['submitit'] == 'true') {
    $returnValue .= handleSubmit();
  } else {
    if(isset($_GET) && isset($_GET['action']) && isset($_GET['actionId'])) {
      if($_GET['action'] == 'delBug') {
        Database::deleteBug($_GET['actionId']);
        $returnValue .= lang('bug_removed');
      }
      
      if($_GET['action'] == 'delComment') {
        Database::deleteBugComment($_GET['actionId']);
        $returnValue .= getViewBugForm();
        $returnValue .= getCommentSubmitForm(false);
      }
      
      if($_GET['action'] == 'editBug') {
        $returnValue .= getEditBugForm($_GET['actionId']);
      }
      
      if($_GET['action'] == 'editComment') {
        $returnValue .= getEditCommentForm($_GET['actionId']);
      }
    } else {
      $returnValue .= getViewBugForm();
      $returnValue .= getCommentSubmitForm(true);
    }
  }
  
  return $returnValue;
}

//Get the projects and their versionIds
// who are visible for logged in user
// from the database and put in in a selection box.
// The value is delimited by ;.
function getProjectsAndVersions($projectId, $versionId) {
  $versions = '<option value="0">&nbsp;</option>';

  $group = getCurrentGroupId();

  if ($group == 1) {
    $result = Database::getAllProjects();

    foreach ($result as $row) {
      if ($row['projectstatus_id'] == 1 || Database::hasUserAccess2Project($row['id'], getCurrentUserId())) {
        $result2 = Database::getVersionsFromProject($row['id']);

        foreach ($result2 as $row2) {
          if ($row['id'] == $projectId && $row2['id'] == $versionId) {
            $versions .= '<option value="'.$row['id'].';'.$row2['id'].'" selected="selected">'.($row['name']).' '.($row2['version']).'</option>';
          } else {
            $versions .= '<option value="'.$row['id'].';'.$row2['id'].'">'.($row['name']).' '.($row2['version']).'</option>';
          }
        }
      }
    }
  } elseif ($group == 2 || $group == 3) {
    $result = Database::getProjectsAndVersions();
        
    if (!empty($result)) {
      foreach ($result as $row) {
        if ($row['projectId'] == $projectId && $row['versionId'] == $versionId) {
          $versions .= '<option value="'.$row['projectId'].';'.$row['versionId'].'" selected="selected">'.($row['name']).' '.($row['version']).'</option>';
        } else {
          $versions .= '<option value="'.$row['projectId'].';'.$row['versionId'].'">'.($row['name']).' '.($row['version']).'</option>';
        }
      }
    }
  }

  return $versions;
}

//Get the projects who are visible for logged in user
// fromthe database and put in in a selection box.
function getProjects($projectId) {
  $projects = '<option value="0">&nbsp;</option>';

  $group = getCurrentGroupId();

  if ($group == 1) {
    $result = Database::getAllProjects();

    foreach ($result as $row) {
      if ($row['projectstatus_id'] == 1 || Database::hasUserAccess2Project($row['id'], getCurrentUserId())) {
        if ($row['id'] == $projectId) {
          $projects .= '<option value="'.$row['id'].'" selected="selected">'.$row['name'].'</option>';
        } else {
          $projects .= '<option value="'.$row['id'].'">'.$row['name'].'</option>';
        }
      }
    }
  } elseif ($group == 2 || $group == 3) {
    $result = Database::getAllProjects();

    foreach ($result as $row) {
      if ($row['id'] == $projectId) {
        $projects .= '<option value="'.$row['id'].'" selected="selected">'.$row['name'].'</option>';
      } else {
        $projects .= '<option value="'.$row['id'].'">'.$row['name'].'</option>';
      }
    }
  }
  
  return $projects;
}

//Get the versions from a project who are visible for logged in user
// fromthe database and put in in a selection box.
function getVersions($projectId, $versionId) {
  $versions = '<option value="0">&nbsp;</option>';

  $result = Database::getVersions($projectId);
        
  if (!empty($result)) {
    foreach ($result as $row) {
      if ($row['id'] == $versionId) {
        $versions .= '<option value="'.$row['id'].'" selected="selected">'.$row['version'].'</option>';
      } else {
        $versions .= '<option value="'.$row['id'].'">'.$row['version'].'</option>';
      }
    }
  }
  
  return $versions;
}

function getCategorys($category) {
  $categorys = '<option value="0">&nbsp;</option>';

  $result = Database::getCategorys();
      
  if (!empty($result)) {
    foreach ($result as $row) {
      if ($row['id'] == $category) {
        $categorys .= '<option value="'.$row['id'].'" selected="selected">'.($row['category']).'</option>';
      } else {
        $categorys .= '<option value="'.$row['id'].'">'.($row['category']).'</option>';
      }
    }
  }

  return $categorys;  
}

function getPrioritys($priorityId) {
  $prioritys = '<option value="0">&nbsp;</option>';

  $result = Database::getbugpriorities();
      
  if (!empty($result)) {
    foreach ($result as $row) {
      if ($row['id'] == $priorityId) {
        $prioritys .= '<option value="'.$row['id'].'" selected="selected">'.($row['name']).'</option>';
      } else {
        $prioritys .= '<option value="'.$row['id'].'">'.($row['name']).'</option>';
      }
    }
  }

  return $prioritys;    
}

function getBugstatus($bugStatusId) {
  $bugStatuses = '<option value="0">&nbsp;</option>';

  $result = Database::getbugstatus();
      
  if (!empty($result)) {
    foreach ($result as $row) {
      if ($row['id'] == $bugStatusId) {
        $bugStatuses .= '<option value="'.$row['id'].'" selected="selected">'.($row['name']).'</option>';
      } else {
        $bugStatuses .= '<option value="'.$row['id'].'">'.($row['name']).'</option>';
      }
    }
  }

  return $bugStatuses;  
}

//Check if there are category's
function isEmptyCategorys() {
  $result = Database::getCategorys();
  
  if (empty($result)) {
    return true;
  } else {
    return false;
  }  
}

//Check if there are projects
function isEmptyProjects() {
  $result = Database::getVisibleProjects(getCurrentUserId());
  
  if (empty($result)) {
    return true;
  } else {
    return false;
  }
}

//Check if there are any related versions to a project
function isEmptyVersions($projectId) {
  $result = Database::getVersions($projectId);
  
  if (empty($result)) {
    return true;
  } else {
    return false;
  }  
}

//Check if there are any priority's in the database
function isEmptyPriority() {
  $result = Database::getbugpriorities();
  
  if (empty($result)) {
    return true;
  } else {
    return false;
  }  
}

//Check if there are any Bugstatus'es in the database.
function isEmptyBugstatus() {
  $result = Database::getbugstatus();
  
  if (empty($result)) {
    return true;
  } else {
    return false;
  }  
}

function getEditBugForm($bugId) {
  $returnValue = '';
  
  $title       = '';
  $description   = '';
  $projectId    = 0;
  $versionId    = 0;
  $category1    = 0;
  $category2    = 0;
  $fixedInId    = 0;

  if(!isEmptyCategorys() && isset($_GET) && isset($_GET['category1']) && isset($_GET['category2'])) {
    $category1  = $_GET['category1'];
    $category2  = $_GET['category2'];    
  }    
      
  if (isset($_GET) && isset($_GET['title']) && isset($_GET['description'])) {
    $title       = $_GET['title'];
    $description = $_GET['description'];
    $fixedInId   = $_GET['fixedin'];

    $priorityId  = $_GET['priorityid'];
    $bugStatusId = $_GET['bugstatusid'];
    $fixedInId   = $_GET['fixedin'];  
        
    if(!isset($_GET['js']) && isset($_GET['projectandversion'])) {
      $aProjVersion  = explode(htmlSafe(';'), $_GET['projectandversion']);
        
      $projectId    = $aProjVersion[0];
      $versionId    = $aProjVersion[1];
    } else if(isset($_GET['projectid']) && isset($_GET['versionid'])) {
      $projectId    = $_GET['projectid'];
      $versionId    = $_GET['versionid'];      
    }
  } else {
    $result = Database::getBug($bugId);

    if(!empty($result)) {
      foreach ($result as $row) {
        $title        = $row['title'];
        $description  = $row['description'];
        $projectId    = $row['project_id'];
        $versionId    = $row['version_id'];
        $category1    = $row['category1_id'];
        $category2    = $row['category2_id'];
        $priorityId   = $row['priority_id'];
        $bugStatusId  = $row['status_id'];
        $fixedInId    = $row['versionFixedId'];
      }
    }
  }

  $thisPage   = 'viewbug';
  $currentUrl = getCurrentRequestUrl();
  $currentUrl = explode('?', $currentUrl);
  $currentUrl = $currentUrl[0];
  
  //TODO:
  //  registerlabel: vervangen door iets algemeners of een nieuwe
  //  registerinput: vervangen door iets algemeners of een nieuwe
  $returnValue .= '<form action="'.$currentUrl.'" method="get" '.(strpos(getCurrentRequestUrl(),'script.php')!== false?'onsubmit="javascriptSubmit(\''.$thisPage.'\', true); return false;"':'').'>
                     <input type="hidden" name="id" value="'.$bugId.'"/>
                     <input type="hidden" name="page" value="'.$thisPage.'"/>
                     <input type="hidden" name="js" value="'.(strpos(getCurrentRequestUrl(),'script.php')===false?'no':'yes').'"/>
                     <input type="hidden" name="submitit" value="true"/>
                     <input type="hidden" name="action" value="editBug"/>
                     <input type="hidden" name="actionId" value="'.$bugId.'"/>
                     <div class="registerinput">
                       <label class="registerlabel" for="title">'.lang('title').':</label>
                       <input class="" type="text" id="title" name="title" value="'.$title.'"/>
                     </div>
                     <div class="registerinput">
                       <label class="registerlabel" for="description">'.lang('description').':</label>
                       <textarea class="" id="description" name="description" cols="40" rows="6">'.$description.'</textarea>
                     </div>';
      
  if(!isset($_GET['js'])) {
    $returnValue .= '<div class="registerinput">
                       <label class="registerlabel" for="projectandversion">'.lang('project').':</label>
                       <select class="" id="projectandversion" name="projectandversion">'.getProjectsAndVersions($projectId, $versionId).'</select>
                     </div>';
  } else {
    $returnValue .=  '<div class="registerinput">
                        <label class="registerlabel" for="projectid">'.lang('project').':</label>
                        <select class="" id="projectid" name="projectid" onchange="javascriptSubmit(\'submitbug\', false);">'.getProjects($projectId).'</select>
                      </div>
                      <div class="registerinput">
                        <label class="registerlabel" for="versionid">'.lang('version').':</label>
                        <select class="" id="versionid" name="versionid">'.getVersions($projectId, $versionId).'</select>
                      </div>';
  }
  
  if(!isEmptyCategorys()) {
    $returnValue .=  '<div class="registerinput">
                        <label class="registerlabel" for="categorie1">'.lang('category1').':</label>
                        <select class="" id="category1" name="category1">'.getCategorys($category1).'</select>
                      </div>
                      <div>
                        <label class="registerlabel" for="categorie2">'.lang('category2').':</label>
                      <div class="registerinput">
                        <select class="" id="category2" name="category2">'.getCategorys($category2).'</select>
                      </div>';
  }

  if (getCurrentGroupId() == 2 || getCurrentGroupId() == 3) {
    $returnValue .=   '<div class="registerinput">
                         <label class="registerlabel" for="priorityid">'.lang('priority').':</label>
                         <select class="" id="priorityid" name="priorityid">'.getPrioritys($priorityId).'</select>
                       </div>
                       <div class="registerinput">
                         <label class="registerlabel" for="bugstatusid">'.lang('status').':</label>
                       <select class="" id="bugstatusid" name="bugstatusid">'.getBugstatus($bugStatusId).'</select>
                       '.lang('in').'
                       <select class="" id="fixedin" name="fixedin">'.getVersions($projectId, $fixedInId).'</select>
                     </div>';
  } else {
    $returnValue .= '<input type="hidden" name="priorityid" value="'.$priorityId.'" />
                     <input type="hidden" name="bugstatusid" value="'.$bugStatusId.'" />
                     <input type="hidden" name="fixedin" value="'.$fixedInId.'" />';
  }

  $returnValue .=    '<div class="registerinput">
                        <label class="registerlabel" for="verzenden">'. lang('send') .':</label>
                        <input class="" id="verzenden" name="verzenden" type="submit" value="'. lang('send') .'!"/>
                      </div>
                    </form>';

  if(!empty($projectId) && isEmptyProjects()) {
    $returnValue .= lang('bug_no_projects');
  }
  
  if(!empty($projectId) && isEmptyVersions($projectId)) {
    $returnValue .= lang('bug_no_versions');
  }          

  if(isEmptyPriority()) {
    $returnValue .= lang('bug_no_priorities');
  }
  
  if(isEmptyBugstatus()) {
    $returnValue .= lang('bug_no_statuses');
  }    
  
  return $returnValue;  
}

function getEditCommentForm($commentId) {
  $message  = '';

  if (isset($_GET) && isset($_GET['message'])) {
    $message   = $_GET['message'];
    $bugId     = $_GET['bugId'];
    $commentId = $_GET['actionId'];
  } else {
    $result = Database::getComment($commentId);
    
    if(!empty($result)) {
      foreach($result as $row) {
        $message = $row['message'];
        $bugId   = $row['bug_id'];
      }
    }
  }
    
  $thisPage   = 'viewbug';
  $currentUrl = getCurrentRequestUrl();
  $currentUrl = explode('?', $currentUrl);
  $currentUrl = $currentUrl[0];    
  
  $returnValue = '<form action="'.$currentUrl.'" method="get" '.(strpos(getCurrentRequestUrl(),'script.php')!== false?'onsubmit="javascriptSubmit(\''.$thisPage.'\', true); return false;"':'').'>
                    <input type="hidden" name="page" value="'.$thisPage.'"/>
                    <input type="hidden" name="js" value="'.(strpos(getCurrentRequestUrl(),'script.php')===false?'no':'yes').'"/>
                    <input type="hidden" name="submitit" value="true"/>
                    <input type="hidden" name="id" value="'.$bugId.'"/>
                    <input type="hidden" name="actionId" value="'.$commentId.'"/>
                    <input type="hidden" name="action" value="editComment"/>
                    <table style="width: 100%;">
                    <tr>
                      <td class="registerlabel"><label for="message">' . lang('message') . ':</label></td>
                      <td class="registerinput"><textarea class="" id="message" name="message" cols="40" rows="6">'.$message.'</textarea></td>
                    </tr>
                    <tr>
                      <td class="registerlabel"><label for="verzenden">'. lang('send') .':</label></td>
                      <td class="registerinput"><input class="" id="verzenden" name="verzenden" type="submit" value="'. lang('send') .'!"/></td>
                    </tr>
                  </table>
                </form>';
  
  return $returnValue;
}

//Build the HTML
function getViewBugForm() {
  $returnValue = '';
  
  if(isset($_GET) && isset($_GET['id'])) {
    $bugId = intval($_GET['id']);

    $userGroup = getCurrentGroupId();
    $results   = Database::getPermissions(intval($userGroup));
  
    $permissions = Array();
    foreach($results as $result) {
      $permissions[$result['setting']] = $result['value'];
    }    
          
    $result = Database::getBugForUser($bugId, getCurrentUserId());
    
    if(!empty($result)) {
      foreach ($result as $row) {
        $returnValue .= '<table style="width: 100%; margin: 0 auto;">';
        
        if (getCurrentUserId() == $row['user_id'] || (isset($permissions['mayadd_viewbug_comment']) && $permissions['mayadd_viewbug_comment'] == 'true')) {
          $returnValue .=  '<tr>
                    <td colspan="2">'.pageLink('viewbug&id='.$bugId.'&action=editBug&actionId='.$bugId, lang('edit')).' '.pageLink('viewbug&id='.$bugId.'&action=delBug&actionId='.$bugId, lang('delete')).'</td>
                  </tr>';
        }
        
        $bbTags = Database::getBBTags(false, 0);
        
        $returnValue .= '<tr>
                    <td class="lightgray" width="100">' . lang('reported_by') . ':</td>
                    <td>'.htmlSafe($row['userName']) . ', ' . htmlSafe(timestamp2date($row['submitdate'])).'</td>
                  </tr>
                  <tr>
                    <td class="lightgray" width="100">&nbsp;</td><td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td class="lightgray" width="100">'.lang('title').':</td>
                    <td>'.$row['title'].'</td>
                  </tr>
                  <tr>
                    <td class="lightgray" width="100">'.lang('description').':</td>
                    <td>'.parseWithBBTags($row['description'], $bbTags).'</td>
                  </tr>
                  <tr>
                    <td class="lightgray" width="100">'.lang('project').':</td>
                    <td>'.($row['projectName']).'</td>
                  </tr>
                  <tr>
                    <td class="lightgray" width="100">'.lang('version').':</td>
                    <td>'.($row['version']).'</td>
                  </tr>
                  <tr>
                    <td class="lightgray" width="100">'.lang('category1').':</td>
                    <td>'.($row['category1Name']).'</td>
                  </tr>
                  <tr>
                    <td class="lightgray" width="100">'.lang('category2').':</td>
                    <td>'.($row['category2Name']).'</td>
                  </tr>
                  <tr>
                    <td class="lightgray" width="100">'.lang('priority').':</td>
                    <td>'.($row['priorityName']).'</td>
                  </tr>
                  <tr>
                    <td class="lightgray" width="100">'.lang('status').':</td>
                    <td>'.($row['statusName']);
                    
        if(!empty($row['versionFixed'])) {                  
          $returnValue .=  lang('in_version') . $row['versionFixed'];  
        }
        
        $returnValue .=  '</td>
                  </tr>
                </table>';
      }
      
      $result = Database::getBugComments($bugId);
      if(!empty($result)) {
        $returnValue .= '<table style="width: 100%;">
                  <tr>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <th>' . lang('messages') . '</th>
                  </tr>';                  
        
        foreach($result as $row) {
          if (getCurrentUserId() == $row['user_id'] || (isset($permissions['mayadd_viewbug_comment']) && $permissions['mayadd_viewbug_comment'] == 'true')) {
            $returnValue .=  '<tr>
                    <td>'.pageLink('viewbug&id='.$bugId.'&action=editComment&actionId='.$row['id'], lang('edit')).' '.pageLink('viewbug&id='.$bugId.'&action=delComment&actionId='.$row['id'], lang('delete') ).'</td>
                  </tr>';
            }  
                    
          $returnValue .= '<tr>
                    <td>'.htmlSafe($row['userName']). lang('at') . htmlSafe(timestamp2date($row['submitdate'])).'</td>
                  </tr>
                  <tr>
                    <td>'.parseWithBBTags($row['message'], $bbTags).'</td>
                  </tr>
                  <tr>
                    <td><hr /></td>
                  </tr>';
        }
        
        $returnValue .= '</table>';
      }
    } else {
      $returnValue = lang('bug_id_not_found');
    }
  } else {
    $returnValue = lang('bug_id_not_found');
  }    
  
  return $returnValue;
}

function getCommentSubmitForm($recoverData) {
  $message  = '';
  
  if(isset($_GET) && isset($_GET['id']) && isLoggedIn()) {
    $bugId = $_GET['id'];
  } else {
    return '';
  }

  if ($recoverData && isset($_GET) && isset($_GET['message'])) {
    $message = $_GET['message'];
  }
    
  $result = Database::getBug($bugId);
    
  if(!empty($result)) {
    foreach ($result as $row) {  
      $postedById = $row['user_id'];
    }
  } else {
    return '';
  }
  
  $userGroup   = getCurrentGroupId();
  $results   = Database::getPermissions(intval($userGroup));
  
  $permissions = Array();
  foreach($results as $result) {
    $permissions[$result['setting']] = $result['value'];
  }
  $returnValue = '';
  if (getCurrentUserId() == $postedById || (isset($permissions['mayadd_viewbug_comment']) && $permissions['mayadd_viewbug_comment'] == 'true')) {
    $thisPage   = 'viewbug';
    $currentUrl = getCurrentRequestUrl();
    $currentUrl = explode('?', $currentUrl);
    $currentUrl = $currentUrl[0];    
    
    $returnValue .= '<form action="'.$currentUrl.'" method="get" '.(strpos(getCurrentRequestUrl(),'script.php')!== false?'onsubmit="javascriptSubmit(\''.$thisPage.'\', true); return false;"':'').'>
              <input type="hidden" name="page" value="'.$thisPage.'"/>
              <input type="hidden" name="js" value="'.(strpos(getCurrentRequestUrl(),'script.php')===false?'no':'yes').'"/>
              <input type="hidden" name="submitit" value="true"/>
              <input type="hidden" name="id" value="'.$bugId.'"/>
              <table style="width: 100%;">
                <tr>
                  <td style="width: 100px;">&nbsp;</td>
                </tr>
                <tr>
                  <th colspan="2">' . lang('message_post') . '</th>
                </tr>
                <tr>
                  <td class="registerlabel"><label for="message">' . lang('message') . ':</label></td>
                  <td class="registerinput"><textarea class="" id="message" name="message" cols="40" rows="6">'.$message.'</textarea></td>
                </tr>
                <tr>
                  <td class="registerlabel"><label for="verzenden">'. lang('send') .':</label></td>
                  <td class="registerinput"><input class="" id="verzenden" name="verzenden" type="submit" value="'. lang('send') .'!"/></td>
                </tr>
              </table>
            </form>';
  } else {
    $returnValue .= '<h1>' . lang('message_post_no_rights') . '</h1>';
  }
  
  return $returnValue;
}

function handleSubmit() {
  if(isset($_GET)) {
    if (isset($_GET) && isset($_GET['message']) && isset($_GET['id']) && !isset($_GET['action'])) {
      $message = $_GET['message'];
      $bugId   = $_GET['id'];
  
      $errorMessage = '';
      $error = false;  
      
      if(empty($message)) {
        $error = true;
      }
            
      if ($error) {
        $returnValue  = getViewBugForm();
        $returnValue .= getCommentSubmitForm(true) . nl2br($errorMessage);
        
        return $returnValue;
      }  
      
      Database::submitBugComment($bugId, $message);
        
      $returnValue  = getViewBugForm();
      $returnValue .= getCommentSubmitForm(false);
      
      return $returnValue;
    }
    
    if(isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] == 'editBug') {
      $title        = '';
      $description  = '';
      $bugId        = 0;
      $projectId    = 0;
      $versionId    = 0;  
      $category1    = 0;
      $category2    = 0;
      $priorityId   = 0;
      $bugStatusId  = 0;
      $fixedInId    = 0;
      
      if (isset($_GET) && isset($_GET['id']) && isset($_GET['title']) && isset($_GET['description']) && isset($_GET['priorityid']) && isset($_GET['bugstatusid'])) {
        $bugId        = $_GET['id'];
        $title        = $_GET['title'];
        $description  = $_GET['description'];
        $priorityId   = $_GET['priorityid'];
        $bugStatusId  = $_GET['bugstatusid'];
        $fixedInId    = $_GET['fixedin'];
                
        if(!isset($_GET['js']) && isset($_GET['projectandversion'])) {
          $aProjVersion = explode(htmlSafe(';'), $_GET['projectandversion']);
            
          $projectId    = $aProjVersion[0];
          $versionId    = $aProjVersion[1];
        } else if(isset($_GET['projectid']) && isset($_GET['versionid'])) {
          $projectId    = $_GET['projectid'];
          $versionId    = $_GET['versionid'];      
        }
      }  
      
      if(!isEmptyCategorys() && isset($_GET) && isset($_GET['category1']) && isset($_GET['category2'])) {
        $category1  = $_GET['category1'];
        $category2  = $_GET['category2'];    
      }
      
      $errorMessage = '';
      $error = false;  
      
      if(empty($title) && $error == false) {
        $error        = true;
        $errorMessage .= lang('title_empty');
      }
      
      if(empty($description) && $error == false) {
        $error        = true;
        $errorMessage .= lang('description_empty');
      }  
      
      if(empty($projectId) && $error == false) {
        $error        = true;
        $errorMessage .= lang('project_not_selected');    
      }
      
      if(empty($versionId) && $error == false) {
        $error        = true;
        $errorMessage .= lang('version_empty');
      }    
    
      if ($error) {
        return getEditBugForm($bugId) . nl2br($errorMessage);
      }  
      
      Database::updateBug($bugId, $title, $description, $versionId, $category1, $category2, $priorityId, $bugStatusId, $fixedInId);
        
      $returnValue  = getViewBugForm();
      $returnValue .= getCommentSubmitForm(false);
      
      return $returnValue;
    }
    
    if(isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] == 'editComment') {
      $message   = '';  
      $CommentId   = 0;
        
      if (isset($_GET) && isset($_GET['message']) && isset($_GET['actionId'])) {
        $message   = $_GET['message'];
        $CommentId = $_GET['actionId'];
      }  
      
      $errorMessage = '';
      $error = false;  
      
      if(empty($message)) {
        $error = true;
      }
            
      if ($error) {
        return editCommentForm();
      }  
      
      Database::updateComment($CommentId, $message);
        
      $returnValue  = getViewBugForm();
      $returnValue .= getCommentSubmitForm(false);
      
      return $returnValue;      
    }
  }
}
