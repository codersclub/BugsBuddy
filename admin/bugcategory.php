<?php

/*
 * Author:      Jay-P en Erik-P
 * Date:        06 June 2006
 * Version:     1.0
 * Description: buglist
 * View and edit possible bug statusvalues
 */

function getbugcategory() {
  $returnValue = '<h1>' . lang('category_add') . '</h1>';
  if (!empty($_GET['id'])) {
    if (is_numeric($_GET['id'])) {
      Database::delBugCategory($_GET['id']);
      $returnValue .= '<p>' . lang('category_removed') . '</p><br />';
      $returnValue .= pageLink('bugcategory', lang('back'));
    } else {
      $returnValue .= '<p><i>' . lang('id_invalid') . '</i></p>';
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
          $returnValue .= '<p>' . lang('category_has_bugs') . '<br />';
          $returnValue .= lang('category_bugs_number') . ': ' . $bugnamecount . '</p>';
          $returnValue .= '<p>' . lang('continue_sure') . '</p>';
          $returnValue .= pageLink('bugcategory&id=' . $_POST['delthis'], lang('yes')) . '&nbsp;';
          $returnValue .= pageLink('bugcategory', lang('no'));
        } else {
          Database::delBugCategory($_POST['delthis']);
          $returnValue .= '<p>' . lang('category_x_removed',$bugstatusname) . '</p><br />';
          $returnValue .= pageLink('bugcategory', lang('back'));
        }
      } else {
        $returnValue .= '<p><i>' . lang('id_invalid') . '</i></p>';
      }
    } elseif (!empty($_POST['bugaddname'])) {
        Database::insBugCategory($_POST['bugaddname']);
        $returnValue .= '<p>' . lang('category_x_added',$_POST['bugaddname']) . '</p><br />';
        $returnValue .= '<div>'.pageLink('bugcategory', lang('back')) .'</div>';
        $returnValue .= '<meta http-equiv="refresh" content="3;URL=index.php?page=bugcategory" />';
    } else {
      //return lang('admin_statuses');

      
      
      $returnValue .= '<form action="index.php?page=bugcategory" id="add" method="post">';
      $returnValue .= '<div>' . lang('category_add') . ': <input type="text" name="bugaddname" />';
      $returnValue .= '&nbsp;<input type="submit" value="' . lang('add') . '" /></div>';
      $returnValue .= '</form>';
      
      $returnValue .= '<form id="delete" method="post" action="index.php?&page=bugcategory">';
      $returnValue .= '<div>' . lang('category_delete') . ': <input type="hidden" name="page" value="bugcategory" />';
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
      $returnValue .= '&nbsp;<input type="submit" value="' . lang('delete') . '" /></div>';
      $returnValue .= '</form>';
    }
  }
  return $returnValue;
}

