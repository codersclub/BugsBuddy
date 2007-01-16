<?php

/*
  Logout page
*/

function getlogout() {
//DEBUG
//echo 'getlogout started.' "\n";
  return logoutInfo();
}

function logoutInfo() {
  return '<h1>'.lang('logout').'</h1>'.lang('logout_ok');
}

/*
 * This function handles the logout before any other output is generated... used by index.php
 */
function logout() {
  $_SESSION['loggedIn'] = false; // just to be sure
  //unset($_SESSION['username']);
  unset($_SESSION);
  setcookie('AUTOLOGIN', ''); // expire in 30 days

  return '<h1>'.lang('logout').'</h1>'.lang('logout_ok');
}

