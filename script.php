<?php

session_start();

require_once('config.php');
require_once('includes/helperfunctions.php');
require_once('includes/Database.php');
require_once('includes/htmlsafe.php');

if (!isset($_GET["page"])) {
  $_GET["page"] = "home";
}

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


if (!isset($_GET) || !isset($_GET["page"]) || $_GET["page"]=="") {
  $_GET["page"] == "home";
}
// Handle error page differently, because it might concern the disability to connect to the database
if ($_GET['page'] == 'errorpage') {
  require_once('pages/errorpage.php');
  echo 'updateContent("'.convertHtmlCodeToJavaScriptString(geterrorpage()).'");';
  exit;
}
// Handle login page differently, because of POST values (privacy) this code will be rendered as pure html for the hidden iframe
// (Passwords should never be send throug $GET values!)
if ($_GET['page'] == 'login') {
  require_once('pages/login.php');
  if (isset($_POST) && isset($_POST['email']) && isset($_POST['pass'])) {
    if (!setLoginSession(htmlUnsafe($_POST['email']), htmlUnsafe($_POST['pass']), ((isset($_POST['stay'])&&$_POST['stay']=='on')?true:false), ((isset($_POST['ip'])&&$_POST['ip']=='on')?true:false))) {
      setcookie('AUTOLOGIN', '', time()+60*60*24); // expire in 30 days
    }
  }
  echo getlogin();
  exit;
}
// Logout executes aditional javascrip
if ($_GET['page'] == 'logout') {
  require_once('pages/logout.php');
  logout();
  $logoutScript = getlogout();
  echo 'updateContent("'.convertHtmlCodeToJavaScriptString($logoutScript[0]).'");'.$logoutScript[1];
  exit;
}
// If the register-submit page is requested
if ($_GET['page'] == 'register') {
  if (isset($_POST) && isset($_POST["name"]) && isset($_POST["password"]) && isset($_POST["mail"])) {
    require_once('pages/register.php');
    echo handleRegistry(htmlUnsafe($_POST['name']), htmlUnsafe($_POST['password']), htmlUnsafe($_POST['mail']));
    exit;
  }
}

if (isset($_SERVER['HTTP_REFERER'])) {  
  //die($_SERVER['HTTP_REFERER']);
/*
  $js = '?';
  $page = '';
  $id = '';
  $strpos = strpos($_SERVER['HTTP_REFERER'], '?');
  if ($strpos === false) {
    $arguments = '';
  } else {
    $arguments = substr($_SERVER['HTTP_REFERER'], $strpos+1);
    $argumentArray = explode('&', $arguments);
    $arguments = Array();
    foreach($argumentArray as $argumentValue) {
      if (strpos($argumentValue, '=') !== false) {
        $splittedArgumentValue = explode('=', $argumentValue);
        $arguments[$splittedArgumentValue[0]] = $splittedArgumentValue[1];
      }
    }
    if (isset($arguments['js'])) $js = '?js='.$arguments['js'].'&';
    else $js = '?js=no&';
    if (isset($arguments['page'])) $page = 'page='.$arguments['page'].'&';
    if (isset($arguments['id'])) $id = 'id='.$arguments['id'].'&';
  }
  echo '<a href="index.php'.$js.$page.$id.'">&lt;&lt; Ga een pagina terug</a>';
*/
}

$newPage = $_GET['page'];
$newId = (isset($_GET['id'])?intval($_GET['id']):'null');

// If some other page is requested
if (!isAllowedPage($_GET["page"])) {
  $_GET["page"] = "pagenotfound";
}
// Every variabele is checked before it is parsed into php code!
$phpCode = "require_once('pages/".$_GET["page"].".php'); echo \"updateContent(\\\"\".convertHtmlCodeToJavaScriptString(get".$_GET["page"]."()).\"\\\"); updateGoBackLink(\\\"$newPage\\\", \\\"$newId\\\"); \";";
eval($phpCode);
?>