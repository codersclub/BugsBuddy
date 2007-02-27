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
  $returnValue = '<h1>' . lang('priority_add_remove') . '</h1>';

  if (!empty($_GET['remBugPriorityID']))
  {
    if (is_numeric($_GET['remBugPriorityID']) && intval($_GET['remBugPriorityID']) != 1)
    {
      Database::remBugPriority($_GET['remBugPriorityID']);
      $returnValue .= '<p>' . lang('priority_deleted') . '</p><br />';
      $returnValue .= pageLink('bugpriority', lang('back'));
    }
    else
    {
      $returnValue .= '<p><i>' . lang('id_invalid') . '</i></p>';
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
            $returnValue .= '<p>' . lang('priority_has_bugs') . '<br />';
            $returnValue .= lang('priority_bugs_number') . ': ' . $bugprioritynamecount . '</p>';
            $returnValue .= '<p>' . lang('continue_sure') . '</p>';
            $returnValue .= pageLink('bugpriority&remBugPriorityID=' . $_POST['delBugPriority'], lang('yes')) . '&nbsp;';
            $returnValue .= pageLink('bugpriority', lang('no'));
          }
          else
          {
            Database::remBugPriority($_POST['delBugPriority']);
            $returnValue .= '<p>' . lang('priority_x_deleted',$bugprioritystatusname) . '</p><br />';
            $returnValue .= pageLink('bugpriority', lang('back'));
          }
        } else {
          $returnValue .= '<p>' . lang('priority_not_removable') . '</p><br />';
          $returnValue .= pageLink('bugpriority', lang('back'));
        }
      }
      else
      {
        $returnValue .= '<p><i>' . lang('id_invalid') . '</i></p>';
      }
    }
    elseif (!empty($_POST['addBugPriority']))
    {
      Database::insBugPriority($_POST['addBugPriority']);

      $returnValue .= '<p>' . lang('priority_x_added',$_POST['addBugPriority']) . '</p><br />';
      $returnValue .= '<div>'.pageLink('bugpriority', lang('back')) .'</div>';
      $returnValue .= '<meta http-equiv="refresh" content="3;URL=index.php?page=bugpriority" />';
    }
    else
    {
      $returnValue .= '<form action="index.php?page=bugpriority" id="add" method="post">';
      $returnValue .=    '<div>' . lang('priority_add') . ': <input type="text" name="addBugPriority" />';
      $returnValue .=    '&nbsp;<input type="submit" value="' . lang('add') . '"/></div>';
      $returnValue .= '</form>';

      $returnValue .= '<form id="delete" method="post" action="index.php?page=bugpriority">';
      $returnValue .=    '<div>' . lang('priority_delete') . ': <input type="hidden" name="page" value="bugpriority" />';
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
      $returnValue .=    '&nbsp;<input type="submit" value="' . lang('delete') . '"/></div>';
      $returnValue .= '</form>';
    }
  }
  return $returnValue;
}

