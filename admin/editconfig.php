<?php
/*
 * Author:      Daan Keuper
 * Date:        12 June 2006
 * Version:     1.0
 * Description: edit configuration
 */

function geteditconfig() {
  $msg = '';

  if (!empty($_POST)) {
    Database::updateConfig($_POST['email'], $_POST['from']);

    $msg = lang('config_changed');
  }

  $ret = '<h1>' . lang('config_edit') . '</h1>';

  if (!empty($msg)) {
    $ret .= '<p><span class="error">' . $msg . '</span></p>';
  }

  $config = Database::getAllConfig();

  foreach ($config as $row) {
    if ($row['setting'] == 'webmastermail') {
      $email = $row['value'];
    } elseif ($row['setting'] == 'mailfrom') {
      $from = $row['value'];
    }
  }

  $ret .= '<form action="index.php?page=editconfig" method="post">
             <div class="registerinput">
               <label for="email" class="registerlabel">' . lang('email_site') . ':</label>
               <input  type="text" id="email" name="email" value="' . $email . '" />
             </div>
             <div>
             <div class="registerinput">
               <label for="from" class="registerlabel">' . lang('email_sender') . ':</label>
               <input  type="text" id="from" name="from" value="' . $from . '" />
             </div>
             <div class="registerinput">
               <label for="verzenden" class="registerlabel">'. lang('send') .':</label>
               <input  id="verzenden" name="verzenden" type="submit" value="' . lang('edit') . '" />
             </div>
           </form>';

  return $ret;
}


