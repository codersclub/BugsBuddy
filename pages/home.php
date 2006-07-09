<?php
/*
 * Author:      Daan Keuper
 * Date:        22 May 2006
 * Version:     1.0
 * Description: the home page
 */

//require_once('./includes/helperfunctions.php');

function gethome() {
  $returnValue = '<h1>Laatste 10 bugs</h1>';
  $result = Database::getBugList(10);
  
  if (empty($result)) {
    $returnValue .= '<p><i>Geen bugs gevonden</i></p>';
  } else {
    $returnValue .= '<table>' .
              '<tr>' .
                '<th style="width: 200px;">Omschrijving</th>' .
                '<th style="width: 100px;">Poster</th>' .
                '<th style="width: 120px;">Datum</th>' .
                '<th style="width: 140px;">Status</th>' .
                '<th style="width: 140px;">Projectversie</th>' .
              '</tr>';
    $i = 1;
    
    foreach ($result as $row) {
      if ($i % 2 == 0) {
        $returnValue .= '<tr class="gray">';
      } else {
        $returnValue .= '<tr>';
      }
      
      $returnValue .= '<td>'.pageLink('viewbug&id='.$row['id'], $row['title']).'</td>' .
                '<td>' . $row['username'] . '</td>' .
                '<td>' . timestamp2date($row['submitdate']) . '</td>' .
                '<td>' . $row['status'] . '</td>'.
                '<td>' . $row['projectname'] . ' ' . $row['projectversion'] . '</td>'.
              '</tr>';      
      $i++;
    }
    
    $returnValue .= '</table>';    
  }
  
  return $returnValue;
}
?>
