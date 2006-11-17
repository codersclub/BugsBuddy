<?php

/*
  Admin Register Page...
*/
//DEBUG
echo '<pre>';
echo 'admin/register.php:', "\n";
//echo 'getRegister', "\n";
echo '_POST='; print_r($_POST);
echo 'name=', $name, "\n";
echo 'password=', $password, "\n";
echo 'email=', $email, "\n";
echo 'groupid=', $groupid, "\n";
echo '</pre>';
//exit;


//require_once(ROOT_DIR.'/includes/Mail.php');
require_once(ROOT_DIR.'/admin/users.php');
require_once(ROOT_DIR.'/pages/register.php');

