<?php

/*
  If somebody wants to submit a bug
*/

function getsubmitbug() {
  $returnValue = '<h1>'. lang('bug_report') . '</h1>';

  if(isLoggedIn()) {
    if (@$_GET['submitit'] == 'true') {
      $returnValue .= handleSubmitBug();
    } else {
      $returnValue .= getSubmitBugForm();
    }
  } else {
    $returnValue .= lang('login_required_for_this');
  }

  return $returnValue;
}

//Get the projects and their versionIds who are visible for logged in user
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
// from the database and put in in a selection box.
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
// from the database and put in in a selection box.
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
        $categorys .= '<option value="'.$row['id'].'" selected="selected">'.$row['category'].'</option>';
      } else {
        $categorys .= '<option value="'.$row['id'].'">'.$row['category'].'</option>';
      }
    }
  }

  return $categorys;
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

//Build the HTML
function getSubmitBugForm() {
  $returnValue = '';

  $title        = '';
  $description  = '';
  $projectId    = 0;
  $versionId    = 0;
  $category1    = 0;
  $category2    = 0;

  if (isset($_POST['title']) && isset($_POST['description'])) {
    $title       = $_POST['title'];
    $description = $_POST['description'];

    if(isset($_POST['projectandversion'])) {
      $aProjVersion = explode(htmlSafe(';'), $_POST['projectandversion']);
      $projectId    = $aProjVersion[0];
      $versionId    = $aProjVersion[1];
    } else if(isset($_POST['projectid']) && isset($_POST['versionid'])) {
      $projectId    = $_POST['projectid'];
      $versionId    = $_POST['versionid'];
    }
  }

  if(!isEmptyCategorys() && isset($_POST['category1']) && isset($_POST['category2'])) {
    $category1 = $_POST['category1'];
    $category2 = $_POST['category2'];
  }

  $thisPage   = 'submitbug';
  $currentUrl = getCurrentRequestUrl();
  $currentUrl = explode('?', $currentUrl);
  $currentUrl = $currentUrl[0];

  //TODO:
  //  registerlabel: vervangen door iets algemeners of een nieuwe
  //  registerinput: vervangen door iets algemeners of een nieuwe
  $returnValue .= '<form action="'.$currentUrl.'" method="POST" id="SubmitBugForm">
            <input type="hidden" name="page" value="'.$thisPage.'"/>
            <input type="hidden" name="submitit" value="true"/>
            <div class="registerinput">
              <label class="registerlabel" for="title">'.lang('title').'</label>
              <input class="" type="text" id="title" name="title" value="'.$title.'"/>
            </div>
            <div class="registerinput">
              <label class="registerlabel" for="description">'.lang('description').':</label>
              <textarea class="" id="description" name="description" cols="40" rows="6">'.$description.'</textarea>
            </div>';
/*
  $returnValue .= '<div class="registerinput">
                       <label class="registerlabel" for="projectandversion">'.lang('project').':</label>
                       <select class="" id="projectandversion" name="projectandversion">'.getProjectsAndVersions($projectId, $versionId).'</select>
                   </div>';
*/
    $returnValue .= '<div class="registerinput">
                       <label class="registerlabel" for="projectid">'.lang('project').':</label>
                       <select class="" id="projectid" name="projectid" onchange="javascriptSubmit(\'submitbug\', false);">'.getProjects($projectId).'</select>
                     </div>
                     <div class="registerinput">
                       <label class="registerlabel" for="versionid">'.lang('version').':</label>
                       <select class="" id="versionid" name="versionid">'.getVersions($projectId, $versionId).'</select>
                     </div>';

  if(!isEmptyCategorys()) {
    $returnValue .= '<div class="registerinput">
                       <label class="registerlabel" for="categorie1">'.lang('category1').':</label>
                       <select class="" id="category1" name="category1">'.getCategorys($category1).'</select>
                     </div>
                     <div class="registerinput">
                       <label class="registerlabel" for="categorie2">'.lang('category2').':</label>
                       <select class="" id="category2" name="category2">'.getCategorys($category2).'</select>
                     </div>';
  }

  $returnValue .=   '<div class="registerinput">
                       <label class="registerlabel" for="verzenden">'. lang('send') .':</label>
                       <input class="" id="verzenden" name="verzenden" type="submit" value="'. lang('send') .'!"/>
                     </div>
                   </form>';

  if(!empty($projectId) && isEmptyProjects()) {
    $returnValue .= lang('bug_no_projects')."\n";
  }

  if(!empty($projectId) && isEmptyVersions($projectId)) {
    $returnValue .= lang('bug_no_versions')."\n";
  }

  return $returnValue;
}

//Submit the informatie to the database
function handleSubmitBug() {
  $title        = '';
  $description  = '';
  $projectId    = 0;
  $versionId    = 0;
  $category1    = 0;
  $category2    = 0;

  $title       = $_POST['title'];
  $description = $_POST['description'];

  $projectId   = $_POST['projectid'];
  $versionId   = $_POST['versionid'];

  if(!isEmptyCategorys() && isset($_POST['category1']) && isset($_POST['category2'])) {
    $category1  = $_POST['category1'];
    $category2  = $_POST['category2'];
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
    return getSubmitBugForm() . nl2br($errorMessage);
  }

  Database::submitBug($title, $description, $versionId, $category1, $category2);
  $result = Database::getProjectUser($projectId, getCurrentUserId());

  if(empty($result)) {
    Database::setProjectUser($projectId, getCurrentUserId());
  }

  return lang('thax_for_bug_report');
}
