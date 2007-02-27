<?php
/*
 * Author:      Daan Keuper
 * Date:        22 May 2006
 * Version:     1.0
 * Description: display an error message
 */

function geterrorpage() {

  if (isset($_GET['message'])) {
    switch ($_GET['message']) {
      case 'database':
        return '<p>' . lang('database_error') . '</p>';
        break;
      default:
        return '<p>' . lang('page_unavailable') . '</p>';
        break;
    }
  } else {
    return '<p>' . lang('page_unavailable') . '</p>';
  }
}

