<?php

/*
 * Author:      Jaimy Porter en Erik Plaggenmars
 * Date:        09 June 2006
 * Version:     1.0
 * Description: bugpriority
 *
 *   View and edit possible bug priorities
 */

function getbugpriority() {
  $returnValue = '<h1>Bugpriority toevoegen of verwijderen</h1>';
  
  if (!empty($_GET['remBugPriorityID'])) 
  {
    if (is_numeric($_GET['remBugPriorityID']) && intval($_GET['remBugPriorityID']) != 1) 
    {
      Database::remBugPriority($_GET['remBugPriorityID']);
      $returnValue .= '<p>Bugpriority verwijderd</p><br />';
      $returnValue .= '<a href="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=bugpriority"> << Back </a>';
    }
    else 
    {
      $returnValue .= '<p><i>Geen geldig id</i></p>';
    }
  } 
  else 
  {
    if (!empty($_POST['delBugPriority'])) 
    {
      if (is_numeric($_POST['delBugPriority'])) 
      {
        if (intval($_POST['delBugPriority']) != 1) {
          $bugprioritystatusname = Database::getBugPriorityWithID($_POST['delBugPriority']);
          
          foreach ($bugprioritystatusname as $row) {
            $bugprioritystatusname = $row['name'];
          }
          
          $bugprioritynamecount = Database::countBugPriority($_POST['delBugPriority']);
          $bugprioritynamecount = count($bugprioritynamecount);
          
          if ($bugprioritynamecount > 0) 
          {
            $returnValue .= '<p>Er zijn bugs gevonden met dezelfde bugpriority, deze zullen verwijderd worden.<br />';
            $returnValue .= 'Aantal bugs met dezelfde bugpriority: ' . $bugprioritynamecount . '</p>';
            $returnValue .= '<p>Weet u zeker dat u door wilt gaan.</p>';
            $returnValue .= '<a href="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=bugpriority&remBugPriorityID=' . $_POST['delBugPriority'] . '">Ja</a>&nbsp;';
            $returnValue .= '&nbsp;<a href="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=bugpriority">Nee</a>';
          } 
          else 
          {
            Database::remBugPriority($_POST['delBugPriority']);
            $returnValue .= '<p>Bugpriority ' . $bugprioritystatusname . ' verwijderd</p><br />';
            $returnValue .= '<a href="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=bugpriority"> << Back </a>';
          }
        } else {
          $returnValue .= '<p>Deze bugpriority kan niet worden verwijdert, omdat dit de standaard bug-priority is.</p><br />';
          $returnValue .= '<a href="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=bugpriority"> << Back </a>';
        }
      } 
      else 
      {
        $returnValue .= '<p><i>Geen geldig id</i></p>';
      }
    } 
    elseif (!empty($_POST['addBugPriority'])) 
    {
      Database::insBugPriority($_POST['addBugPriority']);
        
      $returnValue .= '<p>Bugpriority ' . $_POST['addBugPriority'] . ' toegevoegd</p><br />';
      $returnValue .= '<div><a href="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=bugpriority"> << Back </a></div>';
      $returnValue .= '<meta http-equiv="refresh" content="3;URL=index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=bugpriority" />';
    } 
    else 
    {
      $returnValue .= '<form action="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=bugpriority" id="add" method="post">';
      $returnValue .=    '<div>Bugpriority toevoegen:<input type="text" name="addBugPriority" />';
      $returnValue .=    '&nbsp;<input type="submit" value="Toevoegen"/></div>';
      $returnValue .= '</form>';

      $returnValue .= '<form id="delete" method="post" action="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=bugpriority">';
      $returnValue .=    '<div>Bugpriority verwijderen: <input type="hidden" name="page" value="bugpriority" />';
      $returnValue .=    '<select name="delBugPriority">';

      $getpriorities = Database::getBugPriorities();
      if(!empty($getpriorities)) {
        foreach  ($getpriorities as $bugpriority) {
          $returnValue .= '<option value="' . $bugpriority['id'] . '">' . $bugpriority['name'] . '</option>';
        }
      } else {
        $returnValue .= '<option value="0"></option>';
      }  

      $returnValue .=    '</select>';
      $returnValue .=    '&nbsp;<input type="submit" value="Verwijder"/></div>';
      $returnValue .= '</form>';
    }
  }
  return $returnValue;
}

?>