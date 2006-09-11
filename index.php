<?php
session_start();

require_once('includes/helperfunctions.php');

if (file_exists('./install/')) {
  echo( lang('install_remove') );
}

//DEBUG
//echo "<pre>";
//print_r($lang);
//echo "<pre>";

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
//  echo '        ';
  if (!isset($_GET["page"]) || $_GET["page"] == "") {
    $_GET["page"] = "home";
  }
  if ($_GET['js'] == "no") {
    // Handle error page differently, because it might concern the disability to connect to the database
    if ($_GET['page'] == 'errorpage') {
      require_once('pages/errorpage.php');
      echo geterrorpage();
    } else {
      if (!isAllowedPage($_GET['page'])) {
        $_GET["page"] = "pagenotfound";
      }
      // Every variabele is checked before it is parsed into php code!
//      $phpCode = "require_once('pages/".$_GET["page"].".php');  echo get".$_GET["page"]."();";
//      eval($phpCode);
      require_once(ROOT_DIR.'/pages/'.$_GET['page'].'.php');
      $f = "get".$_GET['page'];
      if(function_exists($f)) {
//echo "function ".$f." exists.<br>";
        echo $f();
      } else {
//echo "function ".$f." NOT exists.<br>";
      }
    }
  } else {	// Show by Ajax
    if (isset($_GET['page']) && $_GET['page'] == 'errorpage') {
      require_once(ROOT_DIR.'/pages/errorpage.php');
      echo geterrorpage() . "\n";
    } else {
      if (!isset($_POST['page'])) {
        require_once(ROOT_DIR.'/pages/home.php');
        echo gethome() . "\n";
      } else {
        if (!isAllowedPage($_POST['page'])) {
          $_POST["page"] = "pagenotfound";
        }
        // Every variabele is checked before it is parsed into php code!
//        $phpCode = "require_once('pages/".$_POST["page"].".php');  echo get".$_POST["page"]."();";
//        eval($phpCode);
        require_once(ROOT_DIR. '/pages/".$_POST["page"].".php');
        $f = "get".$_POST["page"];
        echo $f();
      }
    }
  }
  echo "\n";
}

/*
 * If the $_GET['js'] value is not set, then the browser has to decide to use AJAX or not. This depends on JavaScript availability
 */
if (!isset($_GET["js"])) {
  //vot  noajaxcall();
}
  
/*
 * If user is logged in, check if IP matches with session
 */
if (isLoggedIn() && isset($_SESSION["userIp"]) && $_SESSION["userIp"] !== false) {
  if ($_SESSION["userIp"] != getUserIp()) {
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
//vot if ($_GET['js'] == "no" && isset($_POST) && isset($_POST['email']) && isset($_POST['pass'])) {
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
//if (isset($_GET['js']) && $_GET['js'] == "no" && isset($_GET['page']) && $_GET['page'] == "logout") {
if (isset($_GET['page']) && $_GET['page'] == "logout") {
//  $_GET["page"] = "changepassword";
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
    <meta http-equiv="Content-Type" content="application/xhtml+xml; charset=<?=CHARSET;?>" />
    <link href="style/default.css" rel="stylesheet" type="text/css"/>
<?php
//if ($_GET["js"] == "yes") {
?>
<script type="text/javascript">
  var currentPage = "home";
  var currentId = null;
  function updateContent(newContent) {
    document.getElementById('content').innerHTML = newContent;
  }
  function getNewContent(content) {
    var newScript = document.createElement('script');
    newScript.src = "script.php?page="+content;
    document.getElementById('content').appendChild(newScript);
  }
  function updateLogin(content) {
    document.getElementById('login').innerHTML = content;
  }
  function updateLinks(content) {
    document.getElementById('balk').innerHTML = content;
  }
  function updateGoBackLink(page, id) {
    oldpage = currentPage;
    oldid = currentId;
    if (page == null || page == 'null') {
      oldpage = 'home';
    }
    if (currentId != null && currentId != 'null') {
      document.getElementById('goback').innerHTML = '<a href="javascript:getNewContent(oldpage+\'&id=\'+oldid)"><?=$lang['go_back']?></a>'
    } else {
      document.getElementById('goback').innerHTML = '<a href="javascript:getNewContent(oldpage)"><?=$lang['go_back']?></a>'
    }
    currentPage = page;
    currentId= id;
  }
  function formClear(id, standard) {
    if (document.getElementById(id).value == standard) {
      document.getElementById(id).value = '';
    }
  }
  function loginChecker() {
    // TODO check if email/pass are valid enough to submit, return false if wrong
    document.getElementById('email').value = document.getElementById('input_email').value;
    document.getElementById('pass').value = document.getElementById('input_pass').value;
    document.getElementById('input_email').value = "";
    document.getElementById('input_pass').value = "";
    return true;
  }
  function javascriptSubmit(page, submitit) {
    var string = "";
    var elements1 = document.getElementsByTagName("input");
    var elements2 = document.getElementsByTagName("select");
    var elements3 = document.getElementsByTagName("textarea");
    
    for (var i=0; i<elements1.length; i++) {
      if (elements1[i].className != "login" && elements1[i].type != "submit" && elements1[i].name != "submitit") {
        string += elements1[i].name+"="+elements1[i].value+"&";
      }
       
      if (elements1[i].name == "submitit" && submitit == false) {
        string += elements1[i].name+"=false&";
      }
      
      if (elements1[i].name == "submitit" && submitit == true) {
        string += elements1[i].name+"=true&";
      }
    }
     
    for (var i=0; i<elements2.length; i++) {
      if (elements2[i].className != "login") {
        string += elements2[i].name+"="+elements2[i].value+"&";
      }
    }
    
    for (var i=0; i<elements3.length; i++) {          
      s = new String(elements3[i].value);
      s = s.replace(/\n/g, "%0d%0a");
      string += elements3[i].name+"="+s+"&";
    }          
    
    getNewContent(page + "&"+string);
  }
  
  function checkPassWordStrength(object){
    var pass   = document.getElementById(object).value;
    var strength = '<?=lang('password_very_very_bad')?>';
    var cUpper    = false;
    var cLower   = false;
    var cNumeric = false;
    var sPoints   = 0;
//    var maxWidth = parseInt(document.getElementById('pwdStrength').style.width);
    var maxWidth = 100;
    
    for (var i = 0; i < pass.length; i++) {
      if(pass.charCodeAt(i) >= 48 && pass.charCodeAt(i) <= 57) {
        cNumeric = true;
      }   
      
      if(pass.charCodeAt(i) >= 65 && pass.charCodeAt(i) <= 90) {
        cUpper = true;
      }  
      
      if(pass.charCodeAt(i) >= 97 && pass.charCodeAt(i) <= 122) {
        cLower = true;
      }
    }
     
    //Check numeric en cases points
    if(cNumeric == true && cUpper == false && cLower == false) {      //Only numeric
      sPoints += 0;
    } else if(cNumeric == false && (cUpper == true || cLower == true)) {  //Only character
      sPoints += 1;
    } else if(cNumeric == true && cUpper == true && cLower == false) {    //Numeric and Upper
      sPoints += 2;
    } else if(cNumeric == true && cUpper == false && cLower == true) {    //Numeric and Lower
      sPoints += 2;
    } else if(cNumeric == true && cUpper == true && cLower == true) {    //Numeric upper and Lower
      sPoints += 3;
    }
    
    //Check password length points
    if(pass.length <= 5) {
      sPoints += 0;
    }
    
    if(pass.length >= 6 && pass.length < 8) {
      sPoints += 1;
    }
    
    if(pass.length >= 8 && pass.length < 10) {
      sPoints += 2;
    }
    
    if(pass.length >= 10) {
      sPoints += 3;
    }  
    
    var command = "document.getElementById('pwdBeamGreen').style.width = '"+ parseInt((maxWidth / 6) * sPoints) +"px';";
    eval(command);      
    
    switch(sPoints) {
      case 0:
        strength = '<?=lang('password_very_bad')?>';
        break;
      case 1:
        strength = '<?=lang('password_very_bad')?>';
        break;
      case 2:
        strength = '<?=lang('password_bad')?>';
        break;
      case 3:
        strength = '<?=lang('password_discouraged')?>';
        break;
      case 4:
        strength = '<?=lang('password_good_enough')?>';
        break;
      case 5:
        strength = '<?=lang('password_good')?>';
        break;
      case 6:
        strength = '<?=lang('password_strong')?>';
        break;
    }
    
    document.getElementById('pwdText').innerHTML = strength;
  }
</script>
<?php
//}
?>
  </head>
  <body>
    <div id="container">
      <div id="header">
        <img src="./images/logo.gif" alt="Bugsbuddy" />
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

  <div id="menubar">
    <?php echo getLinksHtml(); ?>
  </div>

<?php
/*
  <div id="goback">

if (isset($_GET['js']) && $_GET['js']!='yes' && isset($_SERVER['HTTP_REFERER'])) {
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
    else $js = '?';

    if (isset($arguments['page'])) $page = 'page='.$arguments['page'].'&';
    if (isset($arguments['id'])) $id = 'id='.$arguments['id'].'&';
              
  }
  echo '<a href="index.php?'.$page.$id.'">'. $lang['go_back'] .'</a>';
}
  </div>
*/
?>


  <div id="content">
    <?php showPage(); ?>
  </div>


  <div id="footer">
    <? echo lang('xhtml1_css2_valid');?>
  </div>

</div>


<?php if ($_GET['js'] == "yes") { ?>
    <iframe name="submitFrame" id="loginFrame" marginwidth="0" marginheight="0" height="0" width="0" name="0" style="width:0px; height:0px; border:0px"></iframe>
<?php } ?>
  </body>
</html>

<?
exit;








//----------------------------
function noajaxcall() {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>

<noscript>
  <meta http-equiv="refresh" content="0;index.php">
</noscript>

<script type="text/javascript">
  document.location.href="index.php";
</script>

</head>

<body>
</body>
</html>
<?php
  exit;
}
