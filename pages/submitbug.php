<?php

/*
  If somebody wants to submit a bug
*/

//require_once('includes/helperfunctions.php');

function getsubmitbug() {
  $returnValue = "<h1>Bug rapporteren</h1>";
  
  if(isLoggedIn()) {
    if (isset($_GET) && isset($_GET['submitit']) && $_GET['submitit'] == "true") {
      $returnValue .= handleSubmitBug();
    } else {
      $returnValue .= getSubmitBugForm();
    }
  } else {
    $returnValue .= "Voor deze functionaliteit moet u ingelogd zijn.";
  }
  
  return $returnValue;
}

//Get the projects and their versionIds who are visible for logged in user from
//the database and put in in a selection box. The value is delimited by ;.
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

//Get the projects who are visible for logged in user from
//the database and put in in a selection box.
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

//Get the versions from a project who are visible for logged in user from
//the database and put in in a selection box.
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
  
  $title       = '';
  $description   = '';
  $projectId    = 0;
  $versionId    = 0;
  $category1    = 0;
  $category2    = 0;
  
  if (isset($_GET) && isset($_GET['title']) && isset($_GET['description'])) {
    $title       = $_GET['title'];
    $description   = $_GET['description'];
    
    if($_GET['js'] == 'no' && isset($_GET['projectandversion'])) {
      $aProjVersion  = explode(htmlSafe(';'), $_GET['projectandversion']);
        
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
    
  $thisPage   = "submitbug";
  $currentUrl = getCurrentRequestUrl();
  $currentUrl = explode("?", $currentUrl);
  $currentUrl = $currentUrl[0];
  
  //TODO:
  //  registerlabel: vervangen door iets algemeners of een nieuwe
  //  registerinput: vervangen door iets algemeners of een nieuwe
  $returnValue .= '<form action="'.$currentUrl.'" method="get" '.(strpos(getCurrentRequestUrl(),'script.php')!== false?'onsubmit="javascriptSubmit(\''.$thisPage.'\', true); return false;"':'').'>'.
            '<div><input type="hidden" name="page" value="'.$thisPage.'"/></div>'.
            '<div><input type="hidden" name="js" value="'.(strpos(getCurrentRequestUrl(),'script.php')===false?'no':'yes').'"/></div>'.
            '<div><input type="hidden" name="submitit" value="true"/></div>'.
            '<div class="registerlabel"><label for="title">Titel:</label></div><div class="registerinput"><input class="" type="text" id="title" name="title" value="'.$title.'"/></div>'.
            '<div class="registerlabel"><label for="description">Omschrijving:</label></div><div class="registerinput"><textarea class="" id="description" name="description" cols="40" rows="6">'.$description.'</textarea></div>';
      
  if(isset($_GET['js']) && $_GET['js'] == "no") {
    $returnValue .= '<div class="registerlabel"><label for="projectandversion">Project:</label></div><div class="registerinput"><select class="" id="projectandversion" name="projectandversion">'.getProjectsAndVersions($projectId, $versionId).'</select></div>';
  } else {
    $returnValue .=  '<div class="registerlabel"><label for="projectid">Project:</label></div><div class="registerinput"><select class="" id="projectid" name="projectid" onchange="javascriptSubmit(\'submitbug\', false);">'.getProjects($projectId).'</select></div>'.
            '<div class="registerlabel"><label for="versionid">Versie:</label></div><div class="registerinput"><select class="" id="versionid" name="versionid">'.getVersions($projectId, $versionId).'</select></div>';
  }
  
  if(!isEmptyCategorys()) {
    $returnValue .=  '<div class="registerlabel"><label for="categorie1">Categorie 1:</label></div><div class="registerinput"><select class="" id="category1" name="category1">'.getCategorys($category1).'</select></div>'.
            '<div class="registerlabel"><label for="categorie2">Categorie 2:</label></div><div class="registerinput"><select class="" id="category2" name="category2">'.getCategorys($category2).'</select></div>';
  }
            
  $returnValue .=   '<div class="registerlabel"><label for="verzenden">Verzenden:</label></div><div class="registerinput"><input class="" id="verzenden" name="verzenden" type="submit" value="Verzenden!"/></div>'.
          '</form>';

  if(!empty($projectId) && isEmptyProjects()) {
    $returnValue .= 'Er zijn geen projecten gevonden, er kunnen geen bugs worden geplaatst.';
  }
  
  if(!empty($projectId) && isEmptyVersions($projectId)) {
    $returnValue .= 'Er zijn geen versies gevonden voor dit project, er kan geen bug voor dit project worden geplaatst.';
  }          
              
  return $returnValue;
}

//Submit the informatie to the database
function handleSubmitBug() {
  $title       = '';
  $description   = '';
  $projectId    = 0;
  $versionId    = 0;  
  $category1    = 0;
  $category2    = 0;
  
  if (isset($_GET) && isset($_GET['title']) && isset($_GET['description'])) {
    $title       = $_GET['title'];
    $description   = $_GET['description'];
    
    if($_GET['js'] == 'no' && isset($_GET['projectandversion'])) {
      $aProjVersion  = explode(htmlSafe(';'), $_GET['projectandversion']);
        
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
    $errorMessage   .= 'De titel moet gevuld zijn.';
  }
  
  if(empty($description) && $error == false) {
    $error        = true;
    $errorMessage   .= 'De beschrijving moet gevuld zijn.';
  }  
  
  if(empty($projectId) && $error == false) {
    $error        = true;
    $errorMessage   .= 'Er moet een project worden gekozen.';    
  }
  
  if(empty($versionId) && $error == false) {
    $error        = true;
    $errorMessage   .= 'Er moet een versie worden gekozen.';
  }    

  if ($error) {
    return getSubmitBugForm() . nl2br($errorMessage);
  }  
  
  Database::submitBug($title, $description, $versionId, $category1, $category2);
  $result = Database::getProjectUser($projectId, getCurrentUserId());
    
  if(empty($result)) {
    Database::setProjectUser($projectId, getCurrentUserId());  
  }
    
  return 'Bedankt voor het plaatsen van deze bug.';  
}
?>