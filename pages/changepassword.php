<?php

/*
  Let a user change his password
*/

function getchangepassword() {
  if (defined('PASSWORD_CHANGED')) {
    return lang('password_changed');
  } else if (defined('PASSWORD_NOT_CHANGED')) {
    return lang('password_not_changed_short');
  } else {

    if (isLoggedIn()) {
      $returnValue = '';

      $js = isset($_GET['js'])?'js=yes':'';

      $returnValue .=  '<h1>' . lang('password_modify') . '</h1>
<form action="index.php?' . $js . '" method="post">
  <div class="changepasswordlabel">
    <label for="password">' . lang('password') . ':</label>
  </div>
  <div class="forgotpasswordinput">
    <input class="forgotpasswordcontent" type="password" name="changepassword" id="password" value="" />
  </div>
  <div class="changepasswordlabel">
    <label for="submit">' . lang('password_change') . ':</label>
  </div>
  <div class="registerinput">
    <input class="registerinputcontent" id="submit" type="submit" value="' . lang('change') . '" />
  </div>
</form>
<br />
<div id="forgotPassword"></div>
';

      return $returnValue;

    } else {
      $returnValue = '';
      $returnValue .= lang('login_required');
      return $returnValue;
    }
  }
}


