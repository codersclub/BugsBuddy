<?php
/*
 * Author:      Daan Keuper
 * Date:        22 May 2006
 * Version:     1.0
 * Description: the home page
 */

function gethome() {
//DEBUG
//echo 'pages/home::gethome started.', "\n";

  $returnValue = '<h1>'.lang('last_10_bugs').'</h1>';
  $result = Database::getBugList(10);
  
  if (empty($result)) {
    $returnValue .= '<p><i>'.lang('bugs_no').'</i></p>';
  } else {
    $returnValue .= '<table width="100%">' .
              '<tr>' .
                '<th>'.lang('description').'</th>' .
                '<th style="width: 100px;">'.lang('poster').'</th>' .
                '<th style="width: 100px;">'.lang('date').'</th>' .
                '<th style="width: 100px;">'.lang('status').'</th>' .
                '<th>'.lang('project_version').'</th>' .
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

