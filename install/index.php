<?php
/*
 * Author:      Daan Keuper
 * Date:        15 June 2006
 * Version:     1.0
 * Description: installation page of bugsbuddy
 */

error_reporting(E_ALL);
require_once('../includes/helperfunctions.php');

//chdir('../');

function isValidPassword2($password) {
  if(strlen($password) >= 5) {
    return true;
  } else {
    return false;
  }
}

function passwordHash2($password) {
  return md5("passwordprefix" . $password . "passwordpostfix");
}

$msg  = '';
$msg2 = '';
$install_complete = false;

if (!empty($_POST)) {
  $file = ROOT_DIR.'/includes/config.php'; 
  $_POST['email']=html_entity_decode($_POST['email']);

//DEBUG
//echo '<pre>';
//echo '{1}';
//echo 'file=', $file, "\n";
//print_r($_POST);
//echo '</pre>';

  if (!empty($_POST['dserver']) && !empty($_POST['dhost']) && !empty($_POST['ddatabase']) && !empty($_POST['dusername']) /*&& !empty($_POST['dpassword'])*/) {
    if (!empty($_POST['aname']) && !empty($_POST['email']) && !empty($_POST['apassword']) && !empty($_POST['apassword2'])) {
      if ($_POST['apassword'] == $_POST['apassword2']) {
        if (preg_match('/^[a-z0-9\._-]+@[a-z0-9\.-]+\.([a-z]{2,})$/i', $_POST['email'])) {
          if (isValidPassword2($_POST['apassword'])) {
            if (!file_exists($file)) {
              $data = "<?php\n
                    // Auto generated configuration file\n
                    define('DATABASE_TYPE',          '$_POST[dserver]');\n
                    define('DATABASE_SERVER',        '$_POST[dhost]');\n
                    define('DATABASE_USER_NAME',     '$_POST[dusername]');\n
                    define('DATABASE_USER_PASSWORD', '$_POST[dpassword]');\n
                    define('DATABASE_DATABASENAME' , '$_POST[ddatabase]');\n
                    define('LANG',                   'en');\n
                    define('CHARSET',                'UTF-8');\n
                    define('MAIL_CHARSET',           'UTF-8');\n
                    define('DATABASE_CHARSET',       'utf8');\n
                    \n";  

              if ($file_handle = fopen($file, 'a')) {
                if (fwrite($file_handle, $data)) {
                    
                  if (Database::install($_POST['ddatabase'], $_POST['aname'], $_POST['email'], passwordHash2($_POST['apassword']))) {
                    $install_complete = true;
                  }
                } else {
                  $msg = lang('config_write_error');
                }
              } else {
                $msg = lang('config_open_error');
              }
              
              fclose($file_handle);
            } else {
              $msg = lang('config_exists');
            }
          } else {
            $msg2 = lang('password_less_5');
          }
        } else {
          $msg2 = lang('email_invalid');
        }
      } else {
        $msg2 = lang('passwords_not_equal');
      }
    } else {
      $msg2 = lang('fill_all_fields');
    }
  } else {
    $msg = lang('fill_all_fields');
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
  <title><?=lang('bugsbuddy_install')?></title>
  <meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
  <link href="./style.css" rel="stylesheet" type="text/css">
</head>
<body>
  <div id="container">
    <div id="header">
        <a href="<?=ROOT_URL?>/"><img src="<?=ROOT_URL?>/images/logo.gif" alt="Bugsbuddy" /></a>
    </div>

    <div id="balk"></div>

    <div id="content">
      <h1><?=lang('bugsbuddy_install')?></h1>
      
<?php
      if (!empty($msg)) {
        echo '<p><span class="error">' . $msg . '</span></p>';
      }

      if ($install_complete) {
        echo lang('install_completed');
      } else {
?>

      <form action="./index.php" method="post">
        <p>
          <h3><?=lang('database_settings')?></h3>

          <div class="registerlabel">
            <label for="dserver"><?=lang('database_type')?>:</label>
          </div>
          <div class="registerinput">
            <select name="dserver" id="dserver">
              <option value="MySQL">MySQL 4.x/5.x</option>
            </select>
          </div>
          
          <div class="registerlabel">
            <label for="dhost"><?=lang('database_host')?>:</label>
          </div>
          <div class="registerinput">
            <input  type="text" id="dhost" name="dhost" value="localhost" />
          </div>

          <div class="registerlabel">
            <label for="ddatabase"><?=lang('database_name')?>:</label>
          </div>
          <div class="registerinput">
            <input  type="text" id="ddatabase" name="ddatabase" />
          </div>

          <div class="registerlabel">
            <label for="uusername"><?=lang('user_name')?>:</label>
          </div>
          <div class="registerinput">
            <input  type="text" id="uusername" name="dusername" />
          </div>

          <div class="registerlabel">
            <label for="ppassword"><?=lang('password')?>:</label>
          </div>
          <div class="registerinput">
            <input  type="password" id="ppassword" name="dpassword" />
          </div>
          
          <h3><?=lang('admin_settings')?></h3>
          
<?php
          if (!empty($msg2)) {
            echo '<p><span class="error">' . $msg2 . '</span></p>';
          }
?>

          <div class="registerlabel">
            <label for="aname"><?=lang('name')?>:</label>
          </div>
          <div class="registerinput">
            <input  type="text" id="aname" name="aname" />
          </div>

          <div class="registerlabel">
            <label for="email"><?=lang('email')?>:</label>
          </div>
          <div class="registerinput">
            <input  type="text" id="email" name="email" />
          </div>

          <div class="registerlabel">
            <label for="apassword"><?=lang('password')?>:</label>
          </div>
          <div class="registerinput">
            <input  type="password" id="apassword" name="apassword" />
          </div>

          <div class="registerlabel">
            <label for="apassword2"><?=lang('password_again')?>:</label>
          </div>
          <div class="registerinput">
            <input  type="password" id="apassword2" name="apassword2" />
          </div>

          <div class="registerlabel">
            <label for="versturen"><?=lang('submit')?>:</label>
          </div>
          <div class="registerinput">
            <input  type="submit" id="versturen" name="versturen" value="<?=lang('install')?>" />
          </div>
        </p>
      </form>
<?php
      }
?>
    </div>

    <div id="footer">
      <?=lang('xhtml1_css2_valid')?>
    </div>
  </div>
</body>
</html>
