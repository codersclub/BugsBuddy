<?php

/*
  Logout page
*/

function getlogout() {
  if (isset($_GET['js']) && $_GET['js'] == "no") {
    require_once(ROOT_DIR.'/pages/home.php');
    return gethome();
  } else {
    require_once(ROOT_DIR.'/pages/home.php');
    require_once(ROOT_DIR.'/pages/login.php');
    // logout executes aditional javascript (is parsed in script.php)
    return Array(gethome(), 'updateLogin("'.addslashes(getLoginHtml()).'");updateLinks("'.str_replace ("\n", "", addslashes(getLinksHtml())).'");');
  }
}

/*
 * This function handles the logout before any other output is generated... used by index.php
 */
function logout() {
  $_SESSION["loggedIn"] = false; // just to be sure
  //unset($_SESSION["username"]);
  unset($_SESSION);
  setcookie('AUTOLOGIN', ''); // expire in 30 days
}

?>