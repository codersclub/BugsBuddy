<?php

/*
  If a user forgot his password (mail him a new one)
*/

require_once(ROOT_DIR.'/includes/Mail.php');

function getforgotpassword() {
  if (isset($_POST['email'])) {
    $email = strtolower(htmlUnsafe($_POST['email']));
    return handleForgotPassword($email);
  } else {
    return getForgotPasswordForm();
  }

}

function getForgotPasswordForm() {

  $returnValue = '
    <h1>'.lang('password_forgot').'</h1>
    <div class="info">'.lang('password_forgot_info').'</div>
    <form action="index.php" method="POST" id="ForgotPasswordForm">
      <div>
        <input type="hidden" name="page" value="forgotpassword" />
      </div>
      <div class="forgotpasswordlabel">
        <label style="width: 100;" for="email">'.lang('email').':</label>
      </div>
      <div class="forgotpasswordinput">
        <input class="forgotpasswordinputcontent" type="text" id="email" name="email" value="" />
      </div>
      <div class="registerlabel">
        <label for="submit">'.lang('password_reset').':</label>
      </div>
      <div class="registerinput">
        <input class="registerinputcontent" id="submit" type="submit" value="'.lang('send_mail').'" />
      </div>
    </form>';

  return $returnValue;

}

function handleForgotPassword($email) {
  $errorMessage = '';
  $error = false;
  $user = Database::getUserByEmail($email);
  if (!$user || count($user) != 1) {
    $errorMessage .= "\n" . lang('email_not_found');
    $error = true;
  }

  if ($error) {
    return getForgotPasswordForm() . '<br />' . nl2br($errorMessage);
  }

  $newPassword = createRandomPassword();
  $emailMessage = new Mail(lang('password_reset_subject'), '<html><head><title>'.lang('password_reset').'</title></head><body>'.lang('password_reset_body1') . htmlSafe($newPassword) . lang('password_reset_body2') . getConfigurationValue('webmastermail') . lang('password_reset_body3')  .'</body></html>');
  $result = $emailMessage->send($email);

  if ($result !== false) {
    $registerMessage = lang('password_reset_ok') . htmlSafe($email).'&nbsp;';
    Database::updateUserPassword($user[0]['id'], $newPassword);
  } else {
    $registerMessage = getForgotPasswordForm() . lang('password_reset_error');
  }
  return $registerMessage;
}


