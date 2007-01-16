<?php

function getlogin() {

  // Check Login Parameters
  if (isset($_POST) && isset($_POST['email']) && isset($_POST['pass'])) {

    //don't do anything.
    // It is allready been taken care of by 'setLoginSession()' and index.php
    require_once('pages/home.php');
    return gethome();

  } else {

    // Login page is viewed, but no login data is submitted,
    // just show the login form
//    require_once('pages/home.php');
//    return gethome();
    return getLoginForm();
  }
}

/*
 * This function handles the login for non-AJAX requests.
 * the 'getLogin()' is not used, because we have to know in a
 * more early stage if the user is logged in or not. used by index.php
 */
function setLoginSession($htmlUnsafeEmail, $htmlUnsafePassword, $stay, $ip) {
  //$user = Database::getUserByLogin(htmlUnsafe($_POST['email']), htmlUnsafe($_POST['pass']));
  $user = Database::getUserByLogin($htmlUnsafeEmail, $htmlUnsafePassword);
  if ($user && count($user)==1) {
    return setLoginSessionByUserArray($user, $stay, $ip);
  }
  return false;
}

function setLoginSessionByUserArray($user, $stay, $ip) {
  $_SESSION['loggedIn'] = true;
  $_SESSION['username'] = $user[0]['name'];
  $_SESSION['userId'] = $user[0]['id'];
  $_SESSION['userGroupId'] = $user[0]['group_id'];
  if ($ip===true) {
    $_SESSION['userIp'] = getUserIp();
  } else {
    $_SESSION['userIp'] = false;
  }
  if ($stay) {
    setcookie('AUTOLOGIN', $_SESSION['username'].'@'.passwordHash($user[0]['name'].$user[0]['id'].getUserIp()), time()+60*60*24); // expire in 30 days
  } else {
    setcookie('AUTOLOGIN', '', time()+60*60*24); // expire in 1 day
  }
  return true;
}

/*
 * Do 'NOT' use newlines ('\n') in the sourcecode below!
 */
function getLoginForm() {

  $returnValue = '
    <h1>'.lang('login').'</h1>
    <p class="nomargin">
      <form action="'.getCurrentRequestUrl().'" method="post">
        <div class="registerinput">
          <label class="registerlabel" for="email">'.lang('email').':</label>
          <input class="registerinputcontent" type="text" id="email" name="email" value="'.$_POST['email'].'" />
        </div>
        <div class="registerinput">
          <label class="registerlabel" for="pass">'.lang('password').':</label>
          <input class="registerinputcontent" type="password" id="pass" name="pass" value="" />
        </div>
        <div class="registerinput">
          <label for="stay" class="registerlabel">'.lang('remember_me').'</label>
          <input type="checkbox" name="stay" id="stay" />
        </div>
        <div class="registerinput">
          <label for="ip" class="registerlabel">'.lang('static_ip').'</label>
          <input type="checkbox" name="ip" id="ip" />
        </div>
        <div class="registerinput">
          <div class="registerlabel"></div>
          <input type="submit" value="'.lang('login').'" />
        </div>
      </form>
    </p>
';

  return $returnValue;
}

/*
 * Do 'NOT' use newlines ('\n') in the sourcecode below!
 */
function getLoginHtml() {

  $returnValue =  '
  <p class="nomargin">
    '. lang('welcome') . ' <strong>' . lang('guest') . '</strong>
    <br />
    <a href="index.php?page=login">' . lang('login') . '</a>
    |
    <a href="index.php?page=register">' . lang('register') . '</a>
    |
    <a href="index.php?page=forgotpassword">' . lang('password_forgot') . '</a>
  </p>'."\n";

  return $returnValue;
}

/*
 * Do 'NOT' use newlines in the sourcecode below!
 */
function getLogoutHtml() {
    $returnValue = ''; 
    $returnValue .= lang('welcome').' <strong>'.getCurrentSafeUsername().'</strong><br />';
    $returnValue .= pageLink('logout', lang('logout')) . '&nbsp;|&nbsp;';
    $returnValue .= pageLink('changepassword', lang('password_modify'));
    
    //pageLink('changepassword', lang('menu_password_change'), 'm') . '&nbsp;&nbsp;|&nbsp;&nbsp;'.
    return $returnValue;
}

function getWrongLoginHtml() {
  return lang('login_invalid') . '<br/>' . lang('click') . ' <a href="index.php">' . lang('here') . '</a> ' . lang('to_try_again');
}


