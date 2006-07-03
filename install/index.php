<?php
/*
 * Author:      Daan Keuper
 * Date:        15 June 2006
 * Version:     1.0
 * Description: installation page of bugsbuddy
 */

chdir('../');

function isValidPassword2($password) {
  if(strlen($password) >= 6) {
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
  if (!empty($_POST['dserver']) && !empty($_POST['dhost']) && !empty($_POST['ddatabase']) && !empty($_POST['dusername']) && !empty($_POST['dpassword'])) {
    if (!empty($_POST['aname']) && !empty($_POST['aemail']) && !empty($_POST['apassword']) && !empty($_POST['apassword2'])) {
        if ($_POST['apassword'] == $_POST['apassword2']) {
          if (eregi('^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$', $_POST['aemail'])) {
            if (isValidPassword2($_POST['apassword'])) {
              if (!file_exists('./config.php')) {
                $data = "<?php\n
                    // Automatic generated configuration file\n
                     define('DATABASE_TYPE',          '$_POST[dserver]');\n
                    define('DATABASE_SERVER',        '$_POST[dhost]');\n
                    define('DATABASE_USER_NAME',     '$_POST[dusername]');\n
                    define('DATABASE_USER_PASSWORD', '$_POST[dpassword]');\n
                    define('DATABASE_DATABASENAME' , '$_POST[ddatabase]');\n
                    ?>\n";  
                $file = './config.php'; 

                if ($file_handle = fopen($file, 'a')) {
                  if (fwrite($file_handle, $data)) {
                    require_once('./includes/Database.php');
                    
                    if (Database::install($_POST['aname'], $_POST['aemail'], passwordHash2($_POST['apassword']))) {
                      $install_complete = true;
                    }
                  } else {
                    $msg = 'Kan niet naar het configuratie bestand worden geschreven.';
                  }
                } else {
                  $msg = 'Configuratie bestand kan niet gemaakt worden.';
                }
                
                fclose($file_handle);
              } else {
                $msg = 'Configuratie bestand bestaat al, gooi config.php weg in de root van BugsBuddy.';
              }
            } else {
              $msg2 = 'Wachtwoord dient uit minimaal 6 tekens te bestaan.';
            }
          } else {
            $msg2 = 'Email adres is niet correct.';
          }
        } else {
          $msg2 = 'Wachtwoorden komen niet overeen.';
        }
    } else {
      $msg2 = 'U heeft niet alles ingevuld';
    }
  } else {
    $msg = 'U heeft niet alles ingevuld.';
  }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>
<head>
  <title>BugsBuddy installatie</title>
  <meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
  <link href="./style.css" rel="stylesheet" type="text/css">
</head>
<body>
  <div id="container">
    <div id="header">
      <img src="../images/logo.gif" alt="Bugsbuddy" />
    </div>

    <div id="balk"></div>

    <div id="content">
      <h1>BugsBuddy installatie</h1>
      
      <?php
      if (!empty($msg)) {
        echo '<p><span class="error">' . $msg . '</span></p>';
      }

      if ($install_complete) {
        echo '<p>Installatie voltooid! Klik <a href="../index.php">hier</a> om naar de begin pagina te gaan.</p>';
      } else {
      ?>

      <form action="./index.php" method="post">
        <p>
          <h3>Database instellingen</h3>

          <div class="registerlabel">
            <label for="dserver">Database type:</label>
          </div>
          <div class="registerinput">
            <select name="dserver" id="dserver">
              <option value="MySQL">MySQL 4.x/5.x</option>
            </select>
          </div>
          
          <div class="registerlabel">
            <label for="dhost">Host:</label>
          </div>
          <div class="registerinput">
            <input  type="text" id="dhost" name="dhost" value="localhost" />
          </div>

          <div class="registerlabel">
            <label for="ddatabase">Database:</label>
          </div>
          <div class="registerinput">
            <input  type="text" id="ddatabase" name="ddatabase" />
          </div>

          <div class="registerlabel">
            <label for="uusername">Gebruikersnaam:</label>
          </div>
          <div class="registerinput">
            <input  type="text" id="uusername" name="dusername" />
          </div>

          <div class="registerlabel">
            <label for="ppassword">Wachtwoord:</label>
          </div>
          <div class="registerinput">
            <input  type="password" id="ppassword" name="dpassword" />
          </div>
          
          <h3>Administrator instellingen</h3>
          
          <?php
          if (!empty($msg2)) {
            echo '<p><span class="error">' . $msg2 . '</span></p>';
          }
          ?>

          <div class="registerlabel">
            <label for="aname">Naam:</label>
          </div>
          <div class="registerinput">
            <input  type="text" id="aname" name="aname" />
          </div>

          <div class="registerlabel">
            <label for="aemail">Email adres:</label>
          </div>
          <div class="registerinput">
            <input  type="text" id="aemail" name="aemail" />
          </div>

          <div class="registerlabel">
            <label for="apassword">Wachtwoord:</label>
          </div>
          <div class="registerinput">
            <input  type="password" id="apassword" name="apassword" />
          </div>

          <div class="registerlabel">
            <label for="apassword2">Wachtwoord (2x):</label>
          </div>
          <div class="registerinput">
            <input  type="password" id="apassword2" name="apassword2" />
          </div>

          <div class="registerlabel">
            <label for="versturen">Versturen:</label>
          </div>
          <div class="registerinput">
            <input  type="submit" id="versturen" name="versturen" value="Installeren" />
          </div>
        </p>
      </form>
      <?php
      }
      ?>
    </div>

    <div id="footer">
      XHTML 1.0 Strict & CSS2 valid.
    </div>
  </div>
</body>
</html>
