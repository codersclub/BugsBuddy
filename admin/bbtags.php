<?php

/*
  View and edit bbTags
*/

//require_once('includes/helperfunctions.php');

function getbbtags() {
  $returnValue = '';
  $returnValue .= "<h1>BBTags</h1>";
  
  if(isLoggedIn()) {
    $results     = Database::getPermissions(intval(getCurrentGroupId()));
    $permissions   = Array();
  
    foreach($results as $result) {
      $permissions[$result['setting']] = $result['value'];
    }
    
    if (isset($permissions['mayview_admin_bbtags']) && $permissions['mayview_admin_bbtags'] == 'true') {    
      if (isset($_GET) && (isset($_GET['delete']) && $_GET['delete'] == 'true') || (isset($_GET['submitit']) && $_GET['submitit'] == 'true')) {
        $returnValue .= handleBbForm();
      } else {
        $returnValue .= getBbForm(true); 
      }
    } else {
      $returnValue = 'Onvoldoende rechten.';
    }
  } else {
    $returnValue = 'Voor deze functionaliteit moet u ingelogd zijn.';
  }  
  
  return $returnValue;
}

function getBbForm($recoverData) {
  $returnValue = '';
  
  $id      = 0;
  $bbTag     = '';
  $htmlTag   = '';

  if ($recoverData && isset($_GET) && isset($_GET['bbtag']) && isset($_GET['htmltag'])) {
    $bbTag    = $_GET['bbtag'];
    $htmlTag   = $_GET['htmltag'];  
  }
  
  if(isset($_GET) && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $result = Database::getBbTags(true, $id);
  
    if(!empty($result)) {
      foreach ($result as $row) {
        $bbTag    = $row['bbcode'];
        $htmlTag   = $row['htmlcode'];
      }
    }
  }    
    
  $thisPage   = "bbtags";
  $currentUrl = getCurrentRequestUrl();
  $currentUrl = explode("?", $currentUrl);
  $currentUrl = $currentUrl[0];
  
  $result = Database::getBbTags(false, 0);
  
  if(!empty($result)) {
    $returnValue .= '<table style="width: 700px;">'.
              '<tr>'.
                '<th>&nbsp;</th><th>&nbsp;</th><th>BBCode</th><th>HTMLCode</th>'.
              '</tr>';
    
    $i = 1;              
              
    foreach ($result as $row) {
      if ($i % 2 == 0) {
        $returnValue .= '<tr class="gray">';
      } else {
        $returnValue .= '<tr>';
      }      
      
      $returnValue .=   '<td><a href="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=bbtags&id='.$row['id'].'&edit=true">Wijzigen</a></td><td><a href="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=bbtags&id='.$row['id'].'&delete=true">Verwijderen</a></td><td>'.$row['bbcode'].'</td><td>'.$row['htmlcode'].'</td>'.
              '</tr>';
              
      $i++;
    }
    
    $returnValue .=   '<tr>'.
                '<td>&nbsp;</td>'.
              '</tr>'.
            '</table>';
  }
    
  //TODO:
  //  registerlabel: vervangen door iets algemeners of een nieuwe
  //  registerinput: vervangen door iets algemeners of een nieuwe
  $returnValue .= '<table>'.
            '<tr>'.
              '<td>'.
                '<form action="'.$currentUrl.'" method="get">'.
                  '<div><input type="hidden" name="page" value="'.$thisPage.'"/></div>'.
                  '<div><input type="hidden" name="submitit" value="true"/></div>'.
                  '<div><input type="hidden" name="js" value="'.((isset($_GET['js'])&&$_GET['js']=='yes')?'yes':'no').'"/></div>';
        
  if(!empty($id)) {
    $returnValue .=       '<div><input type="hidden" name="id" value="'.$id.'"/></div>';
  }
                
  $returnValue .=          '<div class="registerlabel"><label for="bbtag">BBCode:</label></div><div class="registerinput"><input type="text" class="" id="bbtag" name="bbtag" value="'.$bbTag.'"/></div>'.
                  '<div class="registerlabel"><label for="htmltag">HTMLCode:</label></div><div class="registerinput"><input type="text" class="" id="htmltag" name="htmltag" value="'.$htmlTag.'"/></div>'.
                  '<div class="registerlabel"><label for="verzenden">Verzenden:</label></div><div class="registerinput"><input class="" id="verzenden" name="verzenden" type="submit" value="Verzenden!"/></div>'.
                '</form>'.
              '</td>'.
            '</tr>'.
          '</table>';
  
  return $returnValue;
}

function handleBbForm() {
  $returnValue = '';
  
  $id      = 0;
  $bbTag    = '';
  $htmlTag  = '';
  
  if(isset($_GET) && isset($_GET['id'])) {
    $id = $_GET['id'];
  }  

  if(isset($_GET) && isset($_GET['delete']) && $_GET['delete'] == 'true') {    
    if(!is_numeric($id)) {
      $error = true;
    }    
    
    Database::delBbTag($id);  
  } else {
    $errorMessage   = '';
    $error       = false;  

    if (isset($_GET) && isset($_GET['bbtag']) && isset($_GET['htmltag'])) {
      $bbTag    = $_GET['bbtag'];
      $htmlTag  = $_GET['htmltag'];
    }      
          
    if(!is_numeric($id) || empty($bbTag) || empty($htmlTag)) {
      $error = true;
    }
  
    if ($error) {
      return getBbForm(true) . nl2br($errorMessage);
    }  
    
    if(empty($id)) {
      Database::submitBbTag($bbTag, $htmlTag);
    } else {
      Database::updateBbTag($id, $bbTag, $htmlTag);
    }
  }    
  
  return getBbForm(false);
}

?>

