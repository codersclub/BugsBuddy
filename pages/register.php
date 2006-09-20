<?php

/*
  Register Page...
*/

require_once(ROOT_DIR.'/includes/Mail.php');

function getregister() {
  if (isset($_POST) && isset($_POST['name']) && isset($_POST['password']) && isset($_POST['mail'])) {
    $name     = htmlUnsafe($_POST['name']);
    $password = htmlUnsafe($_POST['password']);
    $mail     = strtolower(htmlUnsafe($_POST['mail']));
    
    return handleRegistry($name, $password, $mail);
  } else {
    return getRegistryForm();
  }
}

function getRegistryForm() {

  if (!isset($_GET['js'])) {

    if (isset($_POST) && isset($_POST['name']) && isset($_POST['password']) && isset($_POST['mail'])) {
      $name = $_POST['name'];
      $mail = strtolower($_POST['mail']);
    } else {
      $name = '';
      $mail = '';
    }
    return '
      <h1>'.lang('register').'</h1>
      <form action="'.getCurrentRequestUrl().'" method="post">
        <div class="registerlabel"><label for="name">'.lang('name').':</label></div><div class="registerinput"><input class="registerinputcontent" type="text" id="name" name="name" value="'.$name.'" /></div>
        <div class="registerlabel"><label for="password">'.lang('password').':</label></div><div class="registerinput"><input class="registerinputcontent" type="password" id="password" name="password" value="" /></div>
        <div class="registerlabel"><label for="mail">'.lang('email').':</label></div><div class="registerinput"><input class="registerinputcontent" type="text" id="mail" name="mail" value="'.$mail.'" /></div>
        <div class="registerlabel"><label for="submit">'.lang('register').':</label></div><div class="registerinput"><input class="registerinputcontent" id="submit" name="submit" type="submit" value="'.lang('register').'" /></div>
      </form>';
  } else {
    return ''.
        '<form action="script.php?page=register" method="post" target="submitFrame">'.
        '<div class="registerlabel"><label for="registrationName">'.lang('name').':</label></div><div class="registerinput"><input class="registerinputcontent" type="text" name="name" id="registrationName" value="" /></div>'.    
        '<div class="registerlabel"><label for="registrationPassword">'.lang('password').':</label></div><div class="registerinput"><input class="registerinputcontent" type="password" name="password" id="registrationPassword" value="" onkeyup="checkPassWordStrength(\'registrationPassword\');"/>'.
        '<br /><br /><div class="pwdStrength" id="pwdStrength"><div class="pwdBeamGreen" id="pwdBeamGreen"></div></div><div class="pwdText" id="pwdText"></div></div><br /><br />' .
        '<div class="registerlabel"><label for="mail">'.lang('email').':</label></div><div class="registerinput"><input class="registerinputcontent" type="text" id="mail" name="mail" value="" /></div>'.
        '<div class="registerlabel"><label for="submit">'.lang('register').':</label></div><div class="registerinput"><input class="registerinputcontent" id="submit" name="submit" type="submit" value="'.lang('register').'" /></div>'.
      '</form>'.
      '<br />'.
      '<div id="registerError">'.
      '</div>';
  }
}

function handleRegistry($name, $password, $mail) {
  
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
  if (!isValidEmailAddress($mail)) {
    $errorMessage .= lang('email_error');
    $error = true;    
  }
  if (count(Database::getUserByName($name)) != 0) {
    $errorMessage .= lang('name_exists');
    $error = true;
  }
  if (count(Database::getUserByEmail($mail)) != 0) {
    $errorMessage .= lang('email_exists');
    $error = true;
  }
  if (!isset($_GET['js'])) {
    if ($error) {
      return getRegistryForm() . '<br />' . nl2br($errorMessage);
    }
    Database::registerUser($name, $password, $mail);
    $emailMessage = new Mail(lang('register_subject'), '<html><head><title>'.lang('welcome').' '.$_POST['name'].'</title></head><body>'.lang('register_body').'</body></html>');
    $emailMessage->send($mail);
    $registerMessage = lang('register_ok');
    return $registerMessage;
  } else {
    if ($error) {
      $returnValue =  ''.
        '<html>'.
          '<head>'.
          '</head>'.
          '<body>'.
            '<script>'.
              'window.parent.document.getElementById("registrationPassword").value="";'.
              'window.parent.document.getElementById("registrationName").focus();'.
              'window.parent.document.getElementById("registrationName").select();'.
              'window.parent.document.getElementById("registerError").innerHTML = "'.safenl2br(htmlsafe($errorMessage)).'";'.
            '</script>'.
          '</body>'.
        '</html>';
      return $returnValue;

    } else {

      Database::registerUser($name, $password, $mail);
      $returnValue =  ''.
        '<html>'.
          '<head>'.
          '</head>'.
          '<body>'.
            '<script>'.
              'window.parent.updateContent('.lang('register_success').');'.
            '</script>'.
          '</body>'.
        '</html>';
      return $returnValue;
    }
  }
}
