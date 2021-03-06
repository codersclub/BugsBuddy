<?php

function getbuglist() {
  $maximum = 16;
  $bugcount = Database::getVisibleBugCount(getCurrentUserId());
  $bugcount = intval($bugcount[0]['count(b.id)'])-1;
  //if ($bugcount <= 0) $bugcount = 1;
  $totalPages = ceil(($bugcount+1) / $maximum);

  $page      = isset($_GET['page']) ? $_GET['page'] : 'buglist';
  $projectid = isset($_GET['id']) ? intval($_GET['id']) : 0;

  if (!isset($_GET['sort'])) {
    $_GET['sort'] = 'date';
  } else {
    if ($_GET['sort'] != 'description' &&
        $_GET['sort'] != 'poster' &&
        $_GET['sort'] != 'date' &&
        $_GET['sort'] != 'status' &&
        $_GET['sort'] != 'projectversion') {
      $_GET['sort'] = 'date';
    }
  }
  if (!isset($_GET['order'])) {
    $_GET['order'] = 'desc';
  } else {
    if ($_GET['order'] != 'asc' && $_GET['order'] != 'desc') {
      $_GET['order'] = 'desc';
    }
  }

  if (!isset($_GET['buglistpage'])) {
    $_GET['buglistpage'] = 1;
  }

  $page = intval($_GET['buglistpage']);

  $result = Database::getBugAll($projectid, getCurrentUserId(), ($page-1)*$maximum, $maximum, $_GET['sort'], $_GET['order']);

  $returnValue = '<h1>' . lang('bugs_total', $bugcount+1) . '</h1>';

  if (empty($result)) {
    $returnValue .= '<p><i>' . lang('bugs_no') . '</i></p>';
  } else {


  $returnValue .= '<div style="text-align: center;">';
  if ($page != 1) {
    $returnValue.= pageLink('buglist&buglistpage='.($page-1).'&sort='.$_GET['sort'].'&order='.$_GET['order'], '<<').'&nbsp;&nbsp;';
  } else {
    $returnValue .= '&lt;&lt;&nbsp;';
  }
  $shownPreviousPageNumber = true;
  $showThisPageNumber = true;
  for ($i=0; $i<$totalPages; $i++) {
    $shownPreviousPageNumber = $showThisPageNumber;
    $showThisPageNumber = false;
    if ($i >= 0 && $i <= 2) $showThisPageNumber = true;
    if ($i >= $totalPages-3 && $i < $totalPages) $showThisPageNumber = true;
    if ($i-($page-1) >= -3 && $i-($page-1) <= 3) $showThisPageNumber = true;
    if ($showThisPageNumber != false) {
      if ($i!=0) {
        $returnValue .= '&nbsp;&nbsp;';
      }
      if (!$shownPreviousPageNumber) {
        $returnValue .= '...&nbsp;&nbsp;';
      }
      if ($page == ($i+1)) {
        $returnValue.= pageLink('buglist&buglistpage='.($i+1).'&sort='.$_GET['sort'].'&order='.$_GET['order'], ''.($i+1), 'red');
      } else {
        $returnValue.= pageLink('buglist&buglistpage='.($i+1).'&sort='.$_GET['sort'].'&order='.$_GET['order'], ''.($i+1));
      }
    }
  }
  if ($page != $totalPages) {
    $returnValue.= '&nbsp;&nbsp;'.pageLink('buglist&buglistpage='.($page+1).'&sort='.$_GET['sort'].'&order='.$_GET['order'], '>>');
  } else {
    $returnValue.= '&nbsp;>>';
  }

  $returnValue .= '</div>';
    $returnValue .= '<table width="100%">' .
              '<tr>' .
                '<th>'.pageLink('buglist&buglistpage=1&sort=description&order=asc', lang('description')).'</th>' .
                '<th style="width: 100px;">'.pageLink('buglist&buglistpage=1&sort=poster&order=asc', lang('poster')).'</th>' .
                '<th style="width: 120px;">'.pageLink('buglist&buglistpage=1&sort=date&order=desc', lang('date')).'</th>' .
                '<th style="width: 100px;">'.pageLink('buglist&buglistpage=1&sort=status&order=asc', lang('status')).'</th>' .
                '<th style="width: 140px;">'.pageLink('buglist&buglistpage=1&sort=projectversion&order=asc', lang('project_version')).'</th>' .
              '</tr>';
    $i = 1;

    foreach ($result as $row) {
      if ($i % 2 == 0) {
        $returnValue .= '<tr class="gray">';
      } else {
        $returnValue .= '<tr>';
      }
      //<a href="index.php?p=bugs&id=' . $row['id'] . '">' . $row['title'] . '</a>
//print_r($row);
//exit;
      $returnValue .= '<td>'.pageLink('viewbug&id='.$row['id'], $row['title']).'</td>' .
              '<td>' . $row['username'] . '</td>' .
              '<td>' . timestamp2date($row['submitdate']) . '</td>' .
              '<td>' . $row['status'] . '</td>' .
              '<td>' . $row['projectname'] . ' ' . $row['projectversion'] . '</td>' .
              '</tr>';
      $i++;
    }

    $returnValue .= '</table>';

  }

  return $returnValue;
}

