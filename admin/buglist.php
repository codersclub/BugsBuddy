<?php
/*
 * Author:      Jay-P en Erik-P
 * Date:        06 June 2006
 * Version:     1.0
 * Description: buglist
 */

require_once('./includes/Database.php');
require_once('./includes/helperfunctions.php');

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
    $returnValue .= '<p><i>Geen bugs gevonden</i></p>';
  } else {


  $returnValue .= '<div align="center">';
  if ($page != 1) {
    $returnValue.= pageLink('buglist&buglistpage='.($page-1), '<<').'&nbsp;&nbsp;';
  } else {
    $returnValue .= '<<&nbsp;';
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
    $returnValue .= '<table>' .
              '<tr>' .
                '<th style="width: 200px;">Omschrijving</th>' .
                '<th style="width: 100px;">Poster</th>' .
                '<th style="width: 120px;">Datum</th>' .
                '<th style="width: 140px;">Status</th>' .
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
?>
