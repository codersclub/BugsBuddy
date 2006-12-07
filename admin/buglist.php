<?php
/*
 * Author:      Jay-P en Erik-P
 * Date:        06 June 2006
 * Version:     1.0
 * Description: buglist
 */

function getbuglist() {
  $maximum = 15;
  $bugcount = Database::getVisibleBugCount();
  $bugcount = intval($bugcount[0]['count(id)'])-1;
  if ($bugcount <= 0) $bugcount = 1;
  $totalPages = ceil($bugcount / $maximum);
  //print_r($bugcount);
  //exit;

  if (!isset($_GET['buglistpage'])) {
    $_GET['buglistpage'] = 1;
  }
  
  $page = intval($_GET['buglistpage']);
  
  $result = Database::getBugAll(($page-1)*$maximum, $maximum);



  $returnValue = '<h1>Laatste ' . $maximum . ' bugs</h1>';
  if (empty($result)) {
    $returnValue .= '<p><i>'. lang('bugs_no') .'</i></p>';
  } else {


  $returnValue .= '<div align="center">';
  if ($page != 1) {
    $returnValue.= pageLink('buglist&buglistpage='.($page-1), '&lt;&lt;').'&nbsp;&nbsp;';
  } else {
    $returnValue .= '&lt;&lt;&nbsp;';
  }
  for ($i=0; $i<$totalPages; $i++) {
    if ($i!=0) {
      $returnValue .= '&nbsp;&nbsp;';
    }
    if ($page == ($i+1)) {
      $returnValue.= pageLink('buglist&buglistpage='.($i+1), ''.($i+1), 'red');
    } else {
      $returnValue.= pageLink('buglist&buglistpage='.($i+1), ''.($i+1));
    }

  }
  if ($page != $totalPages) {
    $returnValue.= '&nbsp;&nbsp;'.pageLink('buglist&buglistpage='.($page+1), '>>');
  } else {
    $returnValue.= '&nbsp;>>';
  }

  $returnValue .= '</div>';
    $returnValue .= '<table width="100%">' .
              '<tr>' .
                '<th style="width: 200px;">'. lang('description') .'</th>' .
                '<th style="width: 100px;">'. lang('poster') .'</th>' .
                '<th style="width: 120px;">'. lang('date') .'</th>' .
                '<th style="width: 140px;">'. lang('status') .'</th>' .
              '</tr>';
    $i = 1;
    
    foreach ($result as $row) {
      if ($i % 2 == 0) {
        $returnValue .= '<tr class="gray">';
      } else {
        $returnValue .= '<tr>';
      }
      
      $returnValue .= '<td><a href="index.php?p=bugs&id=' . $row['id'] . '">' . $row['title'] . '</a></td>' .
              '<td>' . $row['username'] . '</td>' .
              '<td>' . timestamp2date($row['submitdate']) . '</td>' .
              '<td>' . $row['status'] . '</td></tr>';      
      $i++;
    }
    
    $returnValue .= '</table>';    

  }
  
  return $returnValue;
}

