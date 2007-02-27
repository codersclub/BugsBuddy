<?php
/*
 * Author:      Daan Keuper
 * Date:        22 May 2006
 * Version:     1.0
 * Description: if a page is not found
 */

function getpagenotfound() {
  $content = '<p>' . lang('page_not_exists') . '</p>';
  return $content;
}
