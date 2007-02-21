<?php
session_start();

$_SESSION['admin'] = 0;

require_once('includes/helperfunctions.php');

//DEBUG
//echo '<pre>';
//print_r($lang);
//echo '<pre>';

/*
 * Get the allowed pages and config
 */

$pages = Database::getPages();

$config = Database::getConfig();

if ($pages == null || $config == null) {
  // We where unable to get the pages from the database, something is seriously wrong with the database
  $_GET['page'] = 'errorpage';
  $_GET['message'] = 'database';
}

/*
 * getLinksHtml() Subroutine to print all links in the top DIV
 * function moved to helperfunctions., bacause script.php schould also be able to call the function for
 * dynamic links-adjustment
 */

/*
 *  Subroutine to show the content
 */
function showPage() {

  if (!isset($_GET['page']) || $_GET['page'] == '') {
    $_GET['page'] = 'home';
  }
  // Handle error page differently, because it might concern the disability to connect to the database
  if ($_GET['page'] == 'errorpage') {
    require_once('pages/errorpage.php');
    echo geterrorpage();
  } else {
    if (!isAllowedPage($_GET['page'])) {
      $_GET['page'] = 'pagenotfound';
    }

    require_once(ROOT_DIR.'/pages/'.$_GET['page'].'.php');
    $f = 'get' . $_GET['page'];

    if(function_exists($f)) {
//DEBUG
//echo 'function '.$f.' exists.<br>';
      echo $f();
    } else {
//DEBUG
//echo 'function '.$f.' NOT exists.<br>';
    }
  }
  echo "\n";
}

/*
 * If user is logged in, check if IP matches with session
 */
if (isLoggedIn() && isset($_SESSION['userIp']) && $_SESSION['userIp'] !== false) {
  if ($_SESSION['userIp'] != getUserIp()) {
    require_once('pages/logout.php');
    logout();
  }
}

/*
 * If autologin is enabled, check if autologinstring is correct and log the user in
 */
if (isset($_COOKIE['AUTOLOGIN'])) {
  $autoLogin = explode('@', htmlUnsafe($_COOKIE['AUTOLOGIN']));
  if (count($autoLogin) == 2) {
    if (isValidUsername($autoLogin[0])) {
      $user = Database::getUserByName($autoLogin[0]);
      if (count($user) == 1) {
        if (passwordHash($user[0]['name'].$user[0]['id'].getUserIp()) == $autoLogin[1]) {
          require_once('pages/login.php');
          setLoginSessionByUserArray($user, true, true);
        }
      }
    }
  }
}

/*
 * If the user tries to login on the non-AJAX,
 * set the session before initial page output
 */
if (isset($_POST) && isset($_POST['email']) && isset($_POST['pass'])) {
  require_once('pages/login.php');

  if (!setLoginSession(htmlUnsafe($_POST['email']), htmlUnsafe($_POST['pass']), ((isset($_POST['stay'])&&$_POST['stay']=='on')?true:false), ((isset($_POST['ip'])&&$_POST['ip']=='on')?true:false))) {
    define('LOGIN_FAILED', true);
    setcookie('AUTOLOGIN', '', time()+60*60*24); // expire in 1 day
  }
}

/*
 * Handle logout before any other output is generated on non-AJAX pages
 */
if (isset($_GET['page']) && $_GET['page'] == 'logout') {
//  $_GET['page'] = 'changepassword';
  require_once('pages/logout.php');
  logout();
}

/*
 * If user wants to change his password
 */
if (isLoggedIn() && isset($_POST['changepassword']) && isValidPassword(htmlUnsafe($_POST['changepassword']))) {
  require_once('pages/logout.php');
  Database::updateUserPassword(getCurrentUserId(), htmlUnsafe($_POST['changepassword']));

  define('PASSWORD_CHANGED', true);

  $_GET['page'] = 'changepassword';
  $_POST['page'] = 'changepassword';
  logout();
} else if (isLoggedIn() && isset($_POST['changepassword'])) {
  define('PASSWORD_NOT_CHANGED', true);
  $_GET['page'] = 'changepassword';
  $_POST['page'] = 'changepassword';
}

/*
 * Start outputting the normal page
 */
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
  <head>
    <title>BugsBuddy</title>
    <meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8" />
    <link href="style/default.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="<?=ROOT_URL?>/js/main.php"></script>

    <script type="text/javascript">
      var currentPage = "home";
      var currentId = null;
    </script>
  </head>
  <body>
    <div id="container">
      <div id="header">
        <a href="<?=ROOT_URL?>/"><img src="<?=ROOT_URL?>/images/logo.gif" alt="Bugsbuddy" /></a>
        <div id="login">
<?php

require_once('pages/login.php');

if (defined('LOGIN_FAILED') && LOGIN_FAILED) {
  echo getWrongLoginHtml();
} else {
  if (!isLoggedIn()) {
    echo getLoginHtml(); 
  } else {
    echo getLogoutHtml(); 
  }
}
?>
    </div>

  </div>

  <div id="balk">
    <?=getLinksHtml()?>
  </div>

  <div id="content">
<?php
  if (file_exists('./install/')) {
    echo( lang('install_remove') );
  }
 showPage();
?>
  </div>

  <div id="footer">
    <?=lang('xhtml1_css2_valid')?>
  </div>

</div>


</body>
</html>
