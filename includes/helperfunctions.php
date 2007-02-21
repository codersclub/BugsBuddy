<?php

/*
  Helper functions
*/

//--------------------------------------------------------
// Absolute path from DISK ROOT (With ending slash !)
$dir = u(dirname(dirname(__FILE__)));
define('ROOT_DIR', $dir);

//--------------------------------------------------------
// Web root directory (start from the SITE ROOT, With ending slash !)
$url = u(dirname($_SERVER['PHP_SELF']));
$url = preg_replace("/\/admin$/",'',$url);
define('ROOT_URL', $url);

//DEBUG
//echo '<pre>';
//echo '__FILE__=', __FILE__, "\n";
//echo 'dir='.$dir."\n";
//echo 'url='.$url."\n";
//print_r($conf);
//print_r($_SERVER);
//echo $_SERVER['PHP_SELF']."\n";
//echo $_SERVER['SCRIPT_NAME']."\n";;
//echo '</pre>';

if(is_file(ROOT_DIR.'/includes/config.php')) {
  require_once(ROOT_DIR.'/includes/config.php');
} else {
  // Auto generated configuration file
  define('DATABASE_TYPE',          'MySQL');
  define('DATABASE_SERVER',        'localhost');
  define('DATABASE_USER_NAME',     'root');
  define('DATABASE_USER_PASSWORD', '');
  define('DATABASE_DATABASENAME' , '');
  define('LANG',                   'en');
}

require_once(ROOT_DIR.'/lang/'.LANG.'.php');
require_once(ROOT_DIR.'/includes/Database.php');
include(ROOT_DIR.'/includes/htmlsafe.php');

//----------------------------------------------
//vot: Unix Style Dir Name
function u($file = '') {
  $file = preg_replace("/^\w:/",'',$file);
  $file = str_replace('\\','/',$file);
  return $file;
}

//----------------------------------------
function lang($key='',$values=array()) {
  global $lang;

  if(count($values)) {
    return (isset($lang[$key])) ? sprintf($lang[$key],$values) : '{'.$key.'}';
  } else {
    return (isset($lang[$key])) ? $lang[$key] : '{'.$key.'}';
  }
}

//------------------------------------
function getLinksHtml() {
  global $pages;
  $returnValue = '';
  if ($pages == null) {
    $returnValue .= pageLink('home', lang('database_connect_error'), 'm');
    return $returnValue;
  }

  $returnValue .= pageLink('home', lang('menu_home'), 'm');

  if (isLoggedIn()) {
    // For debug reasons: show every link to every page:
    $returnValue .= ''
      . '&nbsp;&nbsp;|&nbsp;&nbsp;'
      . pageLink('project', lang('menu_projects'), 'm')
      . '&nbsp;&nbsp;|&nbsp;&nbsp;'
      . pageLink('buglist', lang('menu_buglist'), 'm')
      . '&nbsp;&nbsp;|&nbsp;&nbsp;'
      . pageLink('submitbug', lang('menu_bugreport'), 'm')
//      . pageLink('download', lang('menu_download'), 'm')
      . '';
    $userGroup = getCurrentGroupId();

    $permissionsResults = Database::getPermissions(intval($userGroup));
    $permissions = Array();
    foreach($permissionsResults as $permissionsResult) {
      $permissions[$permissionsResult['setting']] = $permissionsResult['value'];
    }

    if (isset($permissions['mayview_admin']) && $permissions['mayview_admin'] == 'true') {
      $returnValue .= '&nbsp;&nbsp;|&nbsp;&nbsp;'
                    . '<a class="m" href="./admin/">'.lang('menu_admin').'</a>';
    }

  } else {

    $returnValue .= ''
      . '&nbsp;&nbsp;|&nbsp;&nbsp;'
      . pageLink('buglist', lang('menu_buglist'), 'm')
//      . '&nbsp;&nbsp;|&nbsp;&nbsp;'
//      . pageLink('download', lang('menu_download'), 'm')
      . '';
  }

  $returnValue .= "\n";

  return $returnValue;
}

function getConfigurationValue($setting) {
  $returnValue = null;
  global $config;
  foreach($config as $configEntry) {
    if ($configEntry['setting'] == $setting) {
      $returnValue = $configEntry['value'];
    }
  }
  return $returnValue;
}

function isAllowedPage($pageName) {
  global $pages; // $pages is a global variabele defined in index.php AND script.php
  foreach($pages as $value) {
    if ($value['page'] == $pageName) {
      return true;
    }
  }
  return false;
}

function convertHtmlCodeToJavaScriptString($htmlCode) {
  $htmlCode = str_replace("\n", '\n', $htmlCode);
  return addslashes($htmlCode);
}

function getCurrentRequestUrl() {
  $currentUrl = $_SERVER['PHP_SELF'];
  if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] !='') $currentUrl .= '?' . $_SERVER['QUERY_STRING'];
  return htmlspecialchars($currentUrl, ENT_QUOTES);
}

function isLoggedIn() {
  if (isset($_SESSION) && isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']==true) {
    return true;
  } else {
    return false;
  }
}

function getCurrentSafeUsername() {
  if (isLoggedIn() && isset($_SESSION['username'])) {
    return htmlSafe($_SESSION['username']);
  } else {
    return "";
  }
}

function getCurrentUserId() {
  if (!isLoggedIn()) {
    return null;
  } else {
    return $_SESSION['userId'];
  }
}

function getCurrentGroupId() {
  if (!isLoggedIn()) {
    return null;
  } else {
    return $_SESSION['userGroupId'];
  }
}

function passwordHash($password) {
  return md5('passwordprefix' . $password . 'passwordpostfix');
}

function isValidUsername($username) {
  // TODO
  // CHECK IF USERNAME IS VALID:
  // The name may consist of alphanumeric characters with intervening spaces
  return true;
}

function isValidPassword($password) {
  if(strlen($password) >= 5) {
    return true;
  } else {
    return false;
  }
}

function isValidEmailAddress($email) {
  if (preg_match('/^[a-z0-9._-]+@[a-z0-9._-]+\.([a-z]{2,10})$/i', $email)) {
    return true;
  } else {
    return false;
  }
}

/*
 * Create HTML code for an text-link that works for both AJAX as non-AJAX
 */
function pageLink($page, $text, $class=null) {
  $first = substr($page,0,1);
  if($first=='.' || $first=='/') {
    $pg = $page;
    return '<a'.($class!=null?' class="'.$class.'"':' ').' href="'.$pg.'">'.$text.'</a>';
  } else {
    $pg = $page=='home'?'':'page='.$page;
    $admindir = @$_SESSION['admin'] ? '/admin' : '';
    return '<a'.($class!=null?' class="'.$class.'"':' ').' href="'.ROOT_URL.$admindir.'/index.php?'.$pg.'">'.$text.'</a>';
  }
}

function createRandomPassword() {
  $passwordCharacters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  $returnValue = '';
  for ($i=0; $i<8; $i++) {
    $returnValue .= $passwordCharacters{mt_rand(0,(strlen($passwordCharacters)-1))};
  }
  return $returnValue;
}

function timestamp2date($timestamp) {
  if (!is_numeric($timestamp)) {
    return null;
  }
  $day = date('d', $timestamp);
  $year = date('Y', $timestamp);
  $months = lang('months');
  $monthNumber = date('n', $timestamp);
  $month = $months[$monthNumber];
  return $day . ' ' . $month . ' ' . $year;
}

function getUserIp() {
  return (isset($_SERVER['HTTP_X_FORWARDED_FOR']))?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR'];
}

function parseWithBBTags($htmlSafeMessage, $bbTags) {
  $arrayFind = Array();
  $arrayReplace = Array();

//DEBUG
//echo '<pre>';
//$htmlSafeMessage = 'Very [b]Strange[/b] Bug';
//echo 'msg='.$htmlSafeMessage."\n";
//echo 'bbtags=';
//print_r($bbTags);
//echo '</pre>';
//exit;

  foreach($bbTags as $bbTag) {
    //$bbTag = htmlUnsafe($bbTag);
    $bbCode = explode(htmlSafe('*'), $bbTag['bbcode']);
    $htmlCode = explode(htmlSafe('*'), $bbTag['htmlcode']);
    $htmlCode = htmlUnsafe($htmlCode);
    if (count($bbCode) == 2) {
      $continueLoop = true;
      while($continueLoop) {
        $strStart = strpos($htmlSafeMessage, $bbCode[0]);
        if ($strStart !== false) {
          $strStop = strpos($htmlSafeMessage, $bbCode[1], $strStart);
          if ($strStop !== false) {
            $stringBefore = substr($htmlSafeMessage, 0, $strStart);
            $stringMiddle = substr($htmlSafeMessage, $strStart+strlen($bbCode[0]), $strStop-$strStart-strlen($bbCode[0]));
            $stringEnd = substr($htmlSafeMessage, $strStop+strlen($bbCode[1]));
            $htmlSafeMessage = $stringBefore . $htmlCode[0] . $stringMiddle . $htmlCode[1] . $stringEnd;
          } else {
            $continueLoop = false;
          }
        } else {
          $continueLoop = false;
        }
      }
    }
  }

  //$message = str_replace(Array('[b]'), Array('[c]'), $message);

  return str_replace(Array('&#13;&#10;'), Array('<br />'), $htmlSafeMessage);
}


