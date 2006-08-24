<?php

/*
  If a user forgot his password (mail him a new one)
*/

require_once(ROOT_DIR.'/includes/Mail.php');

function getforgotpassword() {
  if (isset($_GET) && isset($_GET['mail'])) {
    $mail = strtolower(htmlUnsafe($_GET['mail']));
    return handleForgotPassword($mail);
  } else {
    return getForgotPasswordForm();
  }
  
}

function getForgotPasswordForm() {

  $returnValue = '<h1>'.lang('password_forgot').'</h1>';

  $returnValue .= "<div class='info'>".lang('password_forgot_info').'</div>';

  if (isset($_GET) && isset($_GET['js']) && $_GET['js'] == "no") {
    $returnValue .= '
<form action="index.php" method="get">';
  <div>
    <input type="hidden" name="js" value="no" />
  </div>
  <div>
    <input type="hidden" name="page" value="forgotpassword" />
  </div>
  <div class="forgotpasswordlabel">
    <label style="width: 100;" for="mail">'.lang('email').':</label>
  </div>
  <div class="forgotpasswordinput">
    <input class="forgotpasswordinputcontent" type="text" id="mail" name="mail" value="" />
  </div>
  <div class="registerlabel">
    <label for="submit">'.lang('password_reset').':</label>
  </div>
  <div class="registerinput">
    <input class="registerinputcontent" id="submit" type="submit" value="'.lang('send_mail').'" />
  </div>
</form>
';
    return $returnValue;

  } else {
    $returnValue = '';
    $returnValue .=  '<form method="get" target="submitFrame" onsubmit="getNewContent(\'forgotpassword\&mail=\'+document.getElementById(\'mail\').value); return false;">';
    $returnValue .=   '<div><input type="hidden" name="page" value="forgotpassword" /></div>';
    $returnValue .=    '<div class="forgotpasswordlabel"><label for="mail">'.lang('email').':</label></div><div class="forgotpasswordinput"><input class="forgotpasswordcontent" type="text" name="mail" id="mail" value="" /></div>';
    $returnValue .=    '<div class="registerlabel"><label for="submit">'.lang('password_reset').':</label></div><div class="registerinput"><input class="registerinputcontent" id="submit" type="submit" value="'.lang('send_mail').'" /></div>';
    $returnValue .=  '</form>';
    $returnValue .=  '<br />';
    $returnValue .=  '<div id="forgotPassword">';
    $returnValue .=  '</div>';
    return $returnValue;
  }
}

function handleForgotPassword($mail) {
  $errorMessage = '';
  $error = false;
  $user = Database::getUserByEmail($mail);
  if (!$user || count($user) != 1) {
    $errorMessage .= "\n" . lang('email_not_found');
    $error = true;
  }

  if ($error) {
    return getForgotPasswordForm() . '<br />' . nl2br($errorMessage);
  }

  $newPassword = createRandomPassword();
  $emailMessage = new Mail(lang('password_reset_subject'), '<html><head><title>'.lang('password_reset').'</title></head><body>'.lang('password_reset_body1') . htmlSafe($newPassword) . lang('password_reset_body2') . getConfigurationValue('webmastermail') . lang('password_reset_body3')  .'</body></html>');
  $result = $emailMessage->send($mail);

  if ($result !== false) {
    $registerMessage = lang('password_reset_ok') . htmlSafe($mail).'&nbsp;';
    Database::updateUserPassword($user[0]['id'], $newPassword);
  } else {
    $registerMessage = getForgotPasswordForm() . lang('password_reset_error');
  }
  return $registerMessage;
}


