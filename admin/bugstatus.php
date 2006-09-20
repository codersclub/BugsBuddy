<?php

/*
 * Author:      Jay-P en Erik-P
 * Date:        06 June 2006
 * Version:     1.0
 * Description: buglist
 * View and edit possible bug statusvalues
 */

function getbugstatus() {
  $returnValue = '<h1>' . lang('status_add_remove') . '</h1>';
  if (!empty($_GET['id'])) {
    if (is_numeric($_GET['id']) && intval($_GET['id']) != 1) {
      Database::delbugstatus($_GET['id']);
      $returnValue .= '<p>' . lang('status_removed') . '</p><br />';
      $returnValue .= '<a href="index.php?'.(isset($_GET['js'])?'js=yes':'').'&page=bugstatus">'. lang('back') .'</a>';
    } else {
      $returnValue .= '<p><i>' . lang('id_invalid') . '</i></p>';
    }
  } else {

    if (!empty($_POST['delthis'])) {
      if (is_numeric($_POST['delthis'])) {
        if (intval($_POST['delthis']) != 1) {
          $bugstatusname = Database::getbugstatuswithid($_POST['delthis']);
          foreach  ($bugstatusname as $row) {
            $bugstatusname = $row['name'];
          }
          $bugnamecount = Database::countbugstatus($_POST['delthis']);
          $bugnamecount = count($bugnamecount);
          if ($bugnamecount > 0) {
            $returnValue .= '<p>' . lang('status_has_bugs') . '.<br />';
            $returnValue .= lang('status_bugs_number') . ': ' . $bugnamecount . '</p>';
            $returnValue .= '<p>' . lang('continue_sure') . '</p>';
            $returnValue .= '<a href="index.php?'.(isset($_GET['js'])?'js=yes':'').'&page=bugstatus&id=' . $_POST['delthis'] . '">' . lang('yes') . '</a>&nbsp;';
            $returnValue .= '&nbsp;<a href="index.php?'.(isset($_GET['js'])?'js=yes':'js=no').'&page=bugstatus">' . lang('no') . '</a>';
          } else {
            Database::delbugstatus($_POST['delthis']);
            $returnValue .= '<p>' . lang('status_x_removed',$bugstatusname) . '</p><br />';
            $returnValue .= '<a href="index.php?'.(isset($_GET['js'])?'js=yes':'').'&page=bugstatus">'. lang('back') .'</a>';
          }
        } else {
          $returnValue .= '<p>' . lang('status_not_removable') . '</p><br />';
          $returnValue .= '<a href="index.php?'.(isset($_GET['js'])?'js=yes':'').'&page=bugstatus">'. lang('back') .'</a>';
        }
      } else {
        $returnValue .= '<p><i>' . lang('id_invalid') . '</i></p>';
      }
    } elseif (!empty($_POST['bugaddname'])) {
        Database::insbugstatus($_POST['bugaddname']);
        $returnValue .= '<p>' . lang('status_x_added',$_POST['bugaddname']) . '</p><br />';
        $returnValue .= '<div><a href="index.php?'.(isset($_GET['js'])?'js=yes':'').'&page=bugstatus">'. lang('back') .'</a></div>';
        $returnValue .= '<meta http-equiv="refresh" content="3;URL=index.php?'.(isset($_GET['js'])?'js=yes':'').'&page=bugstatus" />';
    } else {
      //return "Admin Bug Status Page";

      
      
      $returnValue .= '<form action="index.php?'.(isset($_GET['js'])?'js=yes':'').'&page=bugstatus" id="add" method="post">';
      $returnValue .= '<div>' . lang('status_add') . ': <input type="text" name="bugaddname" />';
      $returnValue .= '&nbsp;<input type="submit" value="' . lang('add') . '"/></div>';
      $returnValue .= '</form>';
      
      $returnValue .= '<form id="delete" method="post" action="index.php?'.(isset($_GET['js'])?'js=yes':'js=no').'&page=bugstatus">';
      $returnValue .= '<div>' . lang('status_delete') . ': <input type="hidden" name="page" value="bugstatus" />';
      $returnValue .= '<select name="delthis">';
          
      $getbugs = Database::getbugstatus();
      if(!empty($getbugs)) {
        foreach  ($getbugs as $bugstatus) {
          $returnValue .= '<option value="' . $bugstatus['id'] . '">' . $bugstatus['name'] . '</option>';
        }
      } else {
        $returnValue .= '<option value="0"></option>';
      }

      $returnValue .= '</select>';
      $returnValue .= '&nbsp;<input type="submit" value="' . lang('delete') . '"/></div>';
      $returnValue .= '</form>';
    }
  }
  return $returnValue;
}

