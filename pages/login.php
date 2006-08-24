<?php

//require_once('includes/helperfunctions.php');

function getlogin() {

  // Check Login Parameters
  if (isset($_POST) && isset($_POST['email']) && isset($_POST['pass'])) {

    if (!isset($_GET['js']) || $_GET['js'] == "no") {
      //don't do anything.
      // It is allready been taken care of by 'setLoginSession()' and index.php
      require_once('pages/home.php');
      return gethome();
    } else {

      $returnValue = '';

      $user = Database::getUserByLogin(htmlUnsafe($_POST['email']), htmlUnsafe($_POST['pass']));

      if ($user && count($user)==1) {
        if (!setLoginSession(htmlUnsafe($_POST['email']), htmlUnsafe($_POST['pass']), ((isset($_POST['stay'])&&$_POST['stay']=='on')?true:false), ((isset($_POST['ip'])&&$_POST['ip']=='on')?true:false))) {
          $returnValue .= '<script>window.parent.updateLogin("OMFG")</script>';
        } else {
          $returnValue .= '<script>';
          $returnValue .=   'window.parent.updateLogin("'.addslashes(getLogoutHtml()).'");';
          $returnValue .=   'window.parent.updateLinks("'.str_replace ("\n", "", addslashes(getLinksHtml())).'");';
          $returnValue .=   'window.parent.getNewContent("home");';
          $returnValue .= '</script>';
        }
      } else {
        $returnValue .= '<script>';
        $returnValue .=   'window.parent.updateLogin("'.addslashes(getWrongLoginHtml()).'");';
        //$returnValue .=   'window.parent.updateLinks("'.str_replace ("\n", "", addslashes(getLinksHtml())).'");';
        $returnValue .= '</script>';
      }
      require_once('pages/home.php');
      return gethome() . $returnValue;
    }

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
  $_SESSION["loggedIn"] = true;
  $_SESSION["username"] = $user[0]['name'];
  $_SESSION["userId"] = $user[0]['id'];
  $_SESSION["userGroupId"] = $user[0]['group_id'];
  if ($ip===true) {
    $_SESSION["userIp"] = getUserIp();
  } else {
    $_SESSION["userIp"] = false;
  }
  if ($stay) {
    setcookie('AUTOLOGIN', $_SESSION["username"].'@'.passwordHash($user[0]['name'].$user[0]['id'].getUserIp()), time()+60*60*24); // expire in 30 days
  } else {
    setcookie('AUTOLOGIN', '', time()+60*60*24); // expire in 1 day
  }
  return true;
}

/*
 * Do 'NOT' use newlines ('\n') in the sourcecode below!
 */
function getLoginForm() {

  $returnValue = '<h1>'.lang('login').'</h1>'."\n";

  $returnValue .=   "<p class='nomargin'>\n";

  if (!isset($_GET['js']) || $_GET['js'] == 'no') {

    $returnValue .= '
    <form action="" method="post">
      <input type="hidden" name="mod" value="login">
      <div class="clear">
        <label for="email" class="registerlabel">'.lang('email').'</label>
        <input class="registerinput" type="text" value="'.$_POST['email'].'" name="email" id="email" />
      </div>
      <div class="clear">
        <label for="pass" class="registerlabel">'.lang('password').'</label>
        <input class="registerinput" type="password" value="" name="pass" id="pass" />
      </div>'."\n";

  } else {

    $returnValue .= '
    <form action="script.php?js=yes&page=login" method="post" target="submitFrame" onsubmit="return loginChecker();">
      <label for="input_email">'.lang('email').'</label> <input type="text" value="'.$_POST['email'].'" id="input_email" onclick="formClear(\'input_email\', \''.lang('email').'\');" />
      <br />
      <label for="input_pass">'.lang('password').'</label> <input type="password" value="" id="input_pass" onclick="formClear(\'input_pass\', \''.lang('password').'\');" />
      <input type="hidden" name="email" id="email" value="">
      <input type="hidden" name="pass" id="pass" value="">
    ';
  }

  $returnValue .= "
      <div class='clear'>
        <label for='stay' class='registerlabel'>".lang('remember_me')."</label>
        <div class='registerinput'><input type='checkbox' name='stay' id='stay' /></div>
      </div>
      <div class='clear'>
        <label for='ip' class='registerlabel'>".lang('static_ip')."</label>
        <div class='registerinput'><input type='checkbox' name='ip' id='ip' /></div>
      </div>
      <div class='clear'>
        <div class='registerlabel'></div><input type='submit' value='".lang('login')."' />
      </div>\n";

/*
  if (!isset($_GET['js']) || $_GET['js'] == "no") {
    $returnValue .= "
      <div class='clear'>
        <a href='index.php?page=register'>" . lang('register') . "</a>
        |
        <a href='index.php?page=forgotpassword'>" . lang('password_forgot') . "</a>
      </div>\n";
  } else {
    $returnValue .= '
      <br />
      <a href="javascript:getNewContent(\'register\');">' . lang('register') . '</a>
      <br />
      <a href="javascript:getNewContent(\'forgotpassword\');">' . lang('password_forgot') . '</a>'."\n";
  }
*/
  $returnValue .= "    </form>\n";
  $returnValue .=   "</p>\n";

  return $returnValue;
}

/*
 * Do 'NOT' use newlines ('\n') in the sourcecode below!
 */
function getLoginHtml() {

  $returnValue .=   '<p class="nomargin">';

  $returnValue .= lang('welcome').' <strong>'.lang('guest').'</strong><br />';

  if (isset($_GET['js']) && $_GET['js'] == 'yes') {
    $returnValue .= '
      <a href="javascript:getNewContent(\'login\');">' . lang('login') . '</a>
      |
      <a href="javascript:getNewContent(\'register\');">' . lang('register') . '</a>
      |
      <a href='javascript:getNewContent(\'forgotpassword\');">' . lang('password_forgot') . "</a>\n";
  } else {
    $returnValue .= '
      <a href="index.php?page=login">' . lang('login') . '</a>
      |
      <a href="index.php?page=register">' . lang('register') . '</a>
      |
      <a href="index.php?page=forgotpassword">' . lang('password_forgot') . "</a>\n";
  }

  $returnValue .=   "</p>\n";

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
  $js = (isset($_GET['js']) && $_GET['js']=='yes') ? '?js=yes' : '';
  return lang('login_invalid') . '<br/>' . lang('click') . ' <a href="index.php'.$js.'">' . lang('here') . '</a> ' . lang('to_try_again');
}


