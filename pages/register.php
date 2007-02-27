<?php

/*
  Register Page...
*/

require_once(ROOT_DIR.'/includes/Mail.php');

function getregister() {
  if (isset($_POST) && isset($_POST['name']) && isset($_POST['password']) && isset($_POST['email'])) {
    $name     = htmlUnsafe($_POST['name']);
    $password = htmlUnsafe($_POST['password']);
    $email    = strtolower(htmlUnsafe($_POST['email']));
    $groupid  = intval(@$_POST['groupid']);

//DEBUG
//echo '<pre>';
//echo 'getRegister', "\n";
//echo '_POST='; print_r($_POST);
//echo 'name=', $name, "\n";
//echo 'password=', $password, "\n";
//echo 'email=', $email, "\n";
//echo 'groupid=', $groupid, "\n";
//echo '</pre>';
//exit;

    return handleRegistry($name, $password, $email, $groupid);
  } else {
    return getRegistryForm();
  }
}

function getRegistryForm() {

  if (isset($_POST) && isset($_POST['name']) && isset($_POST['password']) && isset($_POST['email'])) {
    $name     = $_POST['name'];
    $password = $_POST['password'];
    $email    = strtolower($_POST['email']);
    $groupid  = intval(@$_POST['groupid']);
  } else {
    $name     = '';
    $email    = '';
    $password = '';
    $groupid  = 0;
  }

  $returnValue = '
    <h1>'.lang('register').'</h1>
    <form action="'.getCurrentRequestUrl().'" method="post">
      <div class="registerinput">
        <label for="name" class="registerlabel">'.lang('name').':</label>
        <input class="registerinputcontent" type="text" id="name" name="name" value="'.$name.'" />
      </div>
      <div class="registerinput">
        <label for="password" class="registerlabel">'.lang('password').':</label>
        <input class="registerinputcontent" type="password" id="password" name="password" value="" onkeyup="checkPassWordStrength(\'password\');" />
        <div class="pwdStrength" id="pwdStrength">
          <div class="pwdBeamGreen" id="pwdBeamGreen"></div>
        </div>
        <div class="pwdText" id="pwdText"></div>
      </div>
      <div class="registerinput">
        <label for="email"class="registerlabel">'.lang('email').':</label>
        <input class="registerinputcontent" type="text" id="email" name="email" value="'.$email.'" />
      </div>';

  if (getCurrentGroupId() == 3) {
    $returnValue .= '<div class="registerinput">
                       <label for="groupid" class="registerlabel">' . lang('group') . ':</label>
                       <select class="" id="groupid" name="groupid">'.getGroups($groupId).'</select>
                     </div>';
  }

  $returnValue .= '
      <div id="registerError"></div>
      <div class="registerinput">
        <label for="submit" class="registerlabel">'.lang('register').':</label>
        <input class="registerinputcontent" id="submit" name="submit" type="submit" value="'.lang('register').'" />
      </div>
    </form>';

  return $returnValue;

}

function handleRegistry($name, $password, $email, $groupid=0) {

//DEBUG
//echo '<pre>';
//echo 'handleRegistry', "\n";
//echo '_POST='; print_r($_POST);
//echo 'name=', $name, "\n";
//echo 'password=', $password, "\n";
//echo 'email=', $email, "\n";
//echo 'groupid=', $groupid, "\n";
//echo '</pre>';
//exit;

  $errorMessage = '';
  $error = false;
  if (!isValidUsername($name)) {
    $errorMessage .= lang('name_error');
    $error = true;
  }
  if (!isValidPassword($password)) {
    $errorMessage .= lang('password_error');
    $error = true;
  }
  if (!isValidEmailAddress($email)) {
    $errorMessage .= lang('email_error');
    $error = true;
  }
  if (count(Database::getUserByName($name)) != 0) {
    $errorMessage .= lang('name_exists');
    $error = true;
  }
  if (count(Database::getUserByEmail($email)) != 0) {
    $errorMessage .= lang('email_exists');
    $error = true;
  }

  if ($error) {
    return getRegistryForm() . '<div class="errormessage">' . nl2br($errorMessage) . '</div>';
  }

  Database::registerUser($name, $password, $email, $groupid);

  $emailMessage = new Mail(lang('register_subject'), '<html><head><title>'.lang('welcome').' '.$_POST['name'].'</title></head><body>'.lang('register_body').'</body></html>');
  $emailMessage->send($email);

  $registerMessage = lang('register_ok');
  return $registerMessage;

}
