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

      $returnValue .=  '<h1>' . lang('password_modify') . '</h1>
      <form action="index.php" method="post">
        <div class="registerinput">
          <label class="registerlabel" for="password">' . lang('password') . ':</label>
          <input class="forgotpasswordcontent" type="password" name="changepassword" id="password" value="" />
        </div>
        <div class="registerinput">
          <label class="registerlabel" for="submit">' . lang('password_change') . ':</label>
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


