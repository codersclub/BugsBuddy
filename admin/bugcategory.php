<?php

/*
 * Author:      Jay-P en Erik-P
 * Date:        06 June 2006
 * Version:     1.0
 * Description: buglist
 * View and edit possible bug statusvalues
 */

function getbugcategory() {
  $returnValue = '<h1>Bugcategory toevoegen of verwijderen</h1>';
  if (!empty($_GET['id'])) {
    if (is_numeric($_GET['id'])) {
      Database::delBugCategory($_GET['id']);
      $returnValue .= '<p>Categorie verwijderd</p><br />';
      $returnValue .= '<a href="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=bugcategory"> << Back </a>';
    } else {
      $returnValue .= '<p><i>Geen geldig id</i></p>';
    }
  } else {

    if (!empty($_POST['delthis'])) {
      if (is_numeric($_POST['delthis'])) {
        $bugstatusname = Database::getBugCategoryWithId($_POST['delthis']);
        foreach  ($bugstatusname as $row) {
          $bugstatusname = $row['category'];
        }
        $bugnamecount = Database::countBugCategory($_POST['delthis']);
        $bugnamecount = count($bugnamecount);
        if ($bugnamecount > 0) {
          $returnValue .= '<p>Er zijn bugs gevonden met dezelfde categorie, deze zullen verwijderd worden.<br />';
          $returnValue .= 'Aantal bugs met dezelfde categorie: ' . $bugnamecount . '</p>';
          $returnValue .= '<p>Weet u zeker dat u door wilt gaan.</p>';
          $returnValue .= '<a href="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=bugcategory&id=' . $_POST['delthis'] . '">Ja</a>&nbsp;';
          $returnValue .= '&nbsp;<a href="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=bugcategory">Nee</a>';
        } else {
          Database::delBugCategory($_POST['delthis']);
          $returnValue .= '<p>Bugcategory ' . $bugstatusname . ' verwijderd</p><br />';
          $returnValue .= '<a href="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=bugcategory"> << Back </a>';
        }
      } else {
        $returnValue .= '<p><i>Geen geldig id</i></p>';
      }
    } elseif (!empty($_POST['bugaddname'])) {
        Database::insBugCategory($_POST['bugaddname']);
        $returnValue .= '<p>Bugcategory ' . $_POST['bugaddname'] . ' toegevoegd</p><br />';
        $returnValue .= '<div><a href="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=bugcategory"> << Back </a></div>';
        $returnValue .= '<meta http-equiv="refresh" content="3;URL=index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=bugcategory" />';
    } else {
      //return "Admin Bug Status Page";

      
      
      $returnValue .= '<form action="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=bugcategory" id="add" method="post">';
        $returnValue .= '<div>Bugcategory toevoegen: <input type="text" name="bugaddname" />';
        $returnValue .= '&nbsp;<input type="submit" value="Toevoegen" /></div>';
      $returnValue .= '</form>';
      
      $returnValue .= '<form id="delete" method="post" action="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=bugcategory">';
        $returnValue .= '<div>Bugcategory verwijderen: <input type="hidden" name="page" value="bugcategory" />';
          $returnValue .= '<select name="delthis">';
      
      $getbugs = Database::getBugCategory();    
      if(!empty($getbugs)) {
        foreach  ($getbugs as $bugstatus) {
          $returnValue .= '<option value="' . $bugstatus['id'] . '">' . $bugstatus['category'] . '</option>';
        }
      } else {
        $returnValue .= '<option value="0"></option>';
      }          

          $returnValue .= '</select>';
        $returnValue .= '&nbsp;<input type="submit" value="Verwijder" /></div>';
      $returnValue .= '</form>';
    }
  }
  return $returnValue;
}

?>