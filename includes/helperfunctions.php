<?php

/*
  Helper functions
*/
  require_once("config.php");
  
    function getLinksHtml() {
    global $pages;
    $returnValue = '        ';
    if ($pages == null) {
      $returnValue .= pageLink('home', 'No pages available, unable to connect to database', 'm');
      return;
    }
    if (isLoggedIn()) {
      // For debug reasons: show every link to every page:
      $returnValue .= ''.
        pageLink('home', 'HOME', 'm') . '&nbsp;&nbsp;&nbsp;&nbsp;'.
        pageLink('buglist', 'BUGLIJST', 'm') . '&nbsp;&nbsp;&nbsp;&nbsp;'.
        pageLink('submitbug', 'BUG RAPPORTEREN', 'm') . ''.
//        pageLink('download', 'DOWNLOAD', 'm') . ''.
        '';
      $userGroup = getCurrentGroupId();
      $permissionsResults = Database::getPermissions(intval($userGroup));
      $permissions = Array();
      foreach($permissionsResults as $permissionsResult) {
        $permissions[$permissionsResult['setting']] = $permissionsResult['value'];
      }

      if (isset($permissions['mayview_admin']) && $permissions['mayview_admin'] == 'true') {
        $returnValue .= '&nbsp;&nbsp;&nbsp;&nbsp;<a class="m" href="./admin/?js='.((isset($_GET['js'])&&$_GET['js']=='yes')?'yes':'no').'">ADMIN</a>';
      }

    } else {
      $returnValue .= ''.
        pageLink('home', 'HOME', 'm') . '&nbsp;&nbsp;&nbsp;&nbsp;'.
        pageLink('buglist', 'BUGLIJST', 'm') . ''.
//        pageLink('download', 'DOWNLOAD', 'm') . ''.
        '';
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
    if (isset($_SERVER["QUERY_STRING"]) && $_SERVER["QUERY_STRING"] !="") $currentUrl .= "?" . $_SERVER["QUERY_STRING"];
    return htmlspecialchars($currentUrl, ENT_QUOTES);
  }
  
  function isLoggedIn() {
    if (isset($_SESSION) && isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"]==true) {
      return true;
    } else {
      return false;
    }
  }
  
  function getCurrentSafeUsername() {
    if (isLoggedIn() && isset($_SESSION["username"])) {
      return htmlSafe($_SESSION["username"]);
    } else {
      return "";
    }
  }
  
  function getCurrentUserId() {
    if (!isLoggedIn()) {
      return null;
    } else {
      return $_SESSION["userId"];
    }
  }
  
  function getCurrentGroupId() {
    if (!isLoggedIn()) {
      return null;
    } else {
      return $_SESSION["userGroupId"];
    }
  }
  
  function passwordHash($password) {
    return md5("passwordprefix" . $password . "passwordpostfix");
  }
  
  function isValidUsername($username) {
    // TODO CHECK IF USERNAME IS VALID: De naam mag bestaan uit alphanumerieke tekens met tussenliggende spaties
    return true;
  }
  
  function isValidPassword($password) {
    if(strlen($password) >= 6) {
      return true;
    } else {
      return false;
    }
  }
  
  function isValidEmailAddress($email) {
    if (eregi('^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$', $email)) {
      return true;
    } else {
      return false;
    }
  }
  
  /*
   * Create HTML code for an text-link that works for both AJAX as non-AJAX
   */
  function pageLink($page, $text, $class=null) {
    if (!isset($_GET["js"]) || $_GET["js"] != "no") {
      return '<a'.($class!=null?' class="'.$class.'"':' ').' href="javascript:getNewContent(\''.$page.'\');">'.$text.'</a>';
    } else {
      return '<a'.($class!=null?' class="'.$class.'"':' ').' href="index.php?js=no&page='.$page.'">'.$text.'</a>';
    }
  }
  
  function createRandomPassword() {
    $passwordCharacters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
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
    $months = array(1 => 'januari', 'februari', 'maart', 'april', 'mei', 'juni', 'juli', 'augustus', 'september', 'oktober', 'november', 'december');
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
/*
echo "<pre>";
print_r($bbTags);
echo "</pre>";
exit;
*/
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

?>
