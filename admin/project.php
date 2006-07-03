<?php
/*
 * Author:      Daan Keuper
 * Date:        12 June 2006
 * Version:     1.0
 * Description: add/remove and edit projects, and add users to a project
 */

function getproject() {
  $sec = isset($_GET['sec'])?$_GET['sec']:null;
  $sec = ($sec == 'delete' || $sec == 'edit' || $sec == 'deleteConfirm' || $sec == 'versionDelete') ? $sec : 'view';

  if ($sec == 'view' || $sec == 'deleteConfirm') {
    $msg = '';
    $msg2 = '';
    $msg3 = '';
    $msg4 = '';

    if ($sec == 'deleteConfirm' && isset($_GET['id'])) {
      $id = $_GET['id'];

      if (is_numeric($id)) {
        $check = Database::projectWithID($id);
        
        if (count($check) == 1) {
          Database::deleteProjectVersions($id);
          Database::deleteProject($id);
          $msg2 = 'Project is succesvol verwijderd.';
        } else {
          $msg2 = 'Project is onbekend.';
        }
      } else {
        $msg2 = 'Project is onbekend.';
      }
    }

    if (!empty($_POST)) {
      if (!isset($_POST['name']) || !isset($_POST['version'])) {
        $msg = 'U heeft niet alle verplichte velden ingevuld.';
      } else {
        if (count(Database::getProjectID($_POST['name'])) != 0) {
          $msg = 'Er bestaat al een project met deze naam.';
        } else {
          $visible = (isset($_POST['visible'])) ? 2 : 1;

          Database::insertProject($_POST['name'], $visible);
          Database::insertVersion($_POST['name'], $_POST['version']);

          $msg = 'Project ' . $_POST['name'] . ' aangemaakt.';
        }
      }
    }

    $ret = '<h1>Nieuw project aanmaken</h1>';

    if (!empty($msg)) {
      $ret .= '<span class="error">' . $msg . '</span>';
    }

    $ret .= '<form action="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=project" method="post">' .
        '<div class="registerlabel"><label for="name">Naam *:</label></div><div class="registerinput"><input  type="text" id="name" name="name" /></div>' .
        '<div class="registerlabel"><label for="version">Versie *:</label></div><div class="registerinput"><input  type="text" id="version" name="version" /></div>' .
        '<div class="registerlabel"><label for="visible">Onzichtbaar:</label></div><div class="registerinput"><input  type="checkbox" id="visible" name="visible" value="2" /></div>' .
        '<div class="registerlabel"><label for="verzenden">Verzenden:</label></div><div class="registerinput"><input  id="verzenden" name="verzenden" type="submit" value="Aanmaken" /></div>'.
        '</form>' .
        '<p><span class="graytext">* = Verplichte velden</span></p>';

    $ret .= '<h1>Bestaande projecten wijzigen</h1>';

    if (!empty($msg2)) {
      $ret .= '<span class="error">' . $msg2 . '</span>';
    }

    $projects = Database::getProjectList();

    if (count($projects) == 0) {
      $ret .= '<p><i>Er zijn op dit moment geen projecten.</i></p>';
    } else {
      $ret .= '<table><tr>' .
          '<th style="width: 200px;">Naam</th><th style="width: 100px;">Aantal bugs</th><th style="width: 100px;">Status</th><th style="width: 16px;"></th><th style="width: 16px;"></th></tr>';

      foreach ($projects as $row) {
        $status = ($row['projectstatus_id'] == 1) ? 'Publiek' : 'Onzichtbaar';
        $bugs = Database::getCountWithProjectID($row['id']);
                
        $ret .= '<tr><td>' . $row['name'] . '</td><td>' . count($bugs) . '</td><td>' . $status . '</td><td><a href="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=project&sec=edit&id=' . $row['id'] . '"><img src="../images/edit.png" alt="Wijzigen" title="Wijzigen" /></a></td><td><a href="index.php?js=' . $_GET['js'] . '&page=project&sec=delete&id=' . $row['id'] . '"><img src="../images/delete.png" alt="Verwijderen" title="Verwijderen" /></a></td></tr>';  
      }

      $ret .= '</table>';
    }
  } elseif ($sec == 'delete') {
    $id = $_GET['id'];

    if (is_numeric($id)) {
      $check = Database::projectWithID($id);
      if (count($check) == 1) {
        $ret = '<h1>Project verwijderen</h1>';
        
        $bugs = Database::getCountWithProjectID($id);

        if (count($bugs) > 0) {
          $ret .= '<p>Weet u het zeker? Hiermee verwijderd u ook ' . count($bugs) . ' bugs!</p>';
        } else {
          $ret .= '<p>Weet u het zeker?</p>';
        }
        
        $ret .= '<p><a href="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=project">Nee</a> <a href="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=project&sec=deleteConfirm&id=' . $id . '">Ja</a></p>';
      } else {
        $ret .= '<p><i>Project is onbekend.</i></p>';
      }
    } else {
      $ret .= '<p><i>Project is onbekend.</i></p>';
    }
  } elseif ($sec == 'edit') {
    $ret = '<h1>Bewerk project</h1>';

    $id = $_GET['id'];

    if (is_numeric($id)) {
      $msg = '';
      $msg2 = '';
      $msg3 = '';
      
      if (!empty($_POST)) {
        if ($_POST['sec'] == 'project') {
          echo 'test';
          if (empty($_POST['name'])) {
            $msg = 'U heeft niet alle verplichte velden ingevuld.';
          } else {
            $visible = (isset($_POST['visible'])) ? 2 : 1;
            Database::updateProject($id, $_POST['name'], $visible);

            $msg = 'Project ' . $_POST['name'] . ' is aangepast.';
          }
        } elseif ($_POST['sec'] == 'users') {
          $users = Database::getNormalUserList();

          foreach ($users as $row) {
            $user_id = $row['id'];

            if (!empty($_POST[$user_id]) && !Database::hasUserAccess2Project($id, $user_id)) {
              Database::setProjectUser($id, $user_id);
            } elseif (empty($_POST[$user_id]) && Database::hasUserAccess2Project($id, $user_id)) {
              Database::deleteProjectUser($id, $user_id);
            }
          }

          $msg2 = 'Gebruikers toegang gewijzigd.';
        } if ($_POST['sec'] == 'versionnew') {
          if (!empty($_POST['name'])) {
            Database::insertVersionWithID($_POST['name'], $id);

            $msg3 = 'Versie ' . $_POST['name'] . ' is aangemaakt.';
          } else {
            $msg3 = 'U heeft geen naam opgegeven.';
          }
        }
      }

      if (!empty($_GET['version'])) {
        $version = $_GET['version'];

        if (is_numeric($version)) {
          $count = Database::versionWithID($version);

          if (count($count) == 1) {
            Database::deleteVersion($version);

            $msg4 = 'Versie verwijderd.';
          } else {
            $msg4 = 'Versie niet gevonden.';
          }
        }
      }

      $check = Database::getProject($id);
      if (count($check) == 1) {
        foreach ($check as $row) {
          $name = $row['name'];
          $hidden = $row['projectstatus_id'];
          $checkbox = ($hidden == 2) ? 'checked=\'checked\'' : '';
        }

        if (!empty($msg)) {
          $ret .= '<p><span class="error">' . $msg . '</span></p>';
        }

        $ret .= '<form action="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=project&sec=edit&id=' . $id . '" method="post"><p>' .
            '<input type="hidden" name="sec" value="project" />' .
            '<div class="registerlabel"><label for="name">Naam *:</label></div><div class="registerinput"><input  type="text" id="name" name="name" value="' . $name . '" /></div>' .
            '<div class="registerlabel"><label for="visible">Onzichtbaar:</label></div><div class="registerinput"><input  type="checkbox" id="visible" name="visible" value="2"' . $checkbox . ' /></div>' .
            '<div class="registerlabel"><label for="verzenden">Verzenden:</label></div><div class="registerinput"><input  id="verzenden" name="verzenden" type="submit" value="Aanpassen" /></div>'.
            '</p></form>' .
            '<p><span class="graytext">* = Verplichte velden</span></p>';
        
        
        $ret .= '<h1>Nieuwe versie</h1>';
                
        if (!empty($msg3)) {
          $ret .= '<p><span class="error">' . $msg3 . '</span></p>';
        }
      
        $ret .= '<form action="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=project&sec=edit&id=' . $id . '" method="post"><p>
             <input type="hidden" name="sec" value="versionnew" />
             <div class="registerlabel"><label for="name">Naam:</label></div><div class="registerinput"><input  type="text" id="name" name="name" /></div>
             <form action="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=project&sec=edit&id=' . $id . '" method="post"><p>
             <div class="registerlabel"><label for="verzenden">Verzenden:</label></div><div class="registerinput"><input  id="verzenden" name="verzenden" type="submit" value="Aanmaken" /></div>
             </p></form>';

        $ret .= '<h1>Versies verwijderen</h1>';

        if (!empty($msg4)) {
          $ret .= '<p><span class="error">' . $msg4 . '</span></p>';
        }

        $versions = Database::getVersions($id);

        if (count($versions) > 0) {        
          $ret .= '<form action="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=project&sec=versionDelete&id=' . $id . '" method="post"><p>
               <div class="registerlabel"><label for="name">Versie:</label></div><div class="registerinput"><select name="version">';

          foreach ($versions as $row) {
            $ret .= '<option value="' . $row['id'] . '">' . $row['version'] . '</option>';
          }
                 
          $ret .= '</select></div>
               <div class="registerlabel"><label for="verzenden">Verzenden:</label></div><div class="registerinput"><input  id="verzenden" name="verzenden" type="submit" value="Verwijderen" /></div>
               </p></form>';
        } else {
          $ret .= '<p><i>Geen versies gevonden.</i></p>';
        }

        if ($hidden == 2) {
          $ret .= '<h1>Gebruikers toegang</h1>';

          if (!empty($msg2)) {
            $ret .= '<p><span class="error">' .  $msg2 . '</span></p>';
          }

          $users = Database::getNormalUserList();
          
          if (count($users) > 0) {
            $ret .= '<form action="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=project&sec=edit&id=' . $id . '" method="post" name="users">
                <input type="hidden" name="sec" value="users" />
                  <table>
                  <tr>
                    <th style="width: 200px;">Naam</th>
                    <th style="width: 200px;">Email adres</th>
                    <th style="width: 50px;">Toegang</th>
                  </tr>';
            
            $i = 0;
            foreach ($users as $row) {
              if ($i % 2 != 0) {
                $ret .= '<tr class="gray">';
              } else {
                $ret .= '<tr>';
              }

              $checkbox = (Database::hasUserAccess2Project($id, $row['id'])) ? ' checked="checked"' : '';

              $ret .= '<td>' . $row['name'] . '</td><td>' . $row['email'] . '</td><td class="center"><input type="checkbox" name="' . $row['id'] . '" value="1"' . $checkbox . ' /></td></tr>';

              $i++;
            }
            
            $ret .= '<tr><td colspan="3" class="right"><input type="submit" value="Wijzig" /></td></tr></table></form>';
          } else {
            $ret .= '<p><i>Geen gebruikers gevonden</p></i>';
          }
        } elseif ($hidden = 1) {
          $ret .= '<h1>Gekoppelde gebruikers</h1>';

          $users = Database::getUsersFromProject($id);
          
          if (count($users) > 0) {
            $ret .= '<table>
                  <tr>
                    <th style="width: 200px;">Naam</th>
                    <th style="width: 200px;">Email adres</th>
                  </tr>';
            
            $i = 0;
            foreach ($users as $row) {
              if ($i % 2 != 0) {
                $ret .= '<tr class="gray">';
              } else {
                $ret .= '<tr>';
              }

              $ret .= '<td>' . $row['name'] . '</td><td>' . $row['email'] . '</td></tr>';

              $i++;
            }
            
            $ret .= '</table>';
          } else {
            $ret .= '<p><i>Geen gebruikers zijn gekoppeld aan deze bug</p></i>';
          }
        }
      } else {
        $ret .= '<p><i>Project niet gevonden.</i></p>';
      }
    } else {
      $ret .= '<p><i>Project niet gevonden.</i></p>';
    }
  } elseif ($sec == 'versionDelete') {
    $id = $_POST['version'];
    $ret = '';

    if (is_numeric($id)) {
      $check = Database::versionWithID($id);
      if (count($check) == 1) {
        $ret .= '<h1>Versie verwijderen</h1>';
        
        $bugs = Database::getCountWithVersionID($id);

        if (count($bugs) > 0) {
          $ret .= '<p>Weet u het zeker? Hiermee verwijderd u ook ' . count($bugs) . ' bugs!</p>';
        } else {
          $ret .= '<p>Weet u het zeker?</p>';
        }
        
        $ret .= '<p><a href="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=project&sec=edit&id=' . $_GET['id'] . '">Nee</a> <a href="index.php?js=' . $_GET['js'] . '&page=project&sec=edit&id=' . $_GET['id'] . '&version=' . $id . '">Ja</a></p>';
      } else {
        $ret .= '<p><i>Versie is onbekend.</i></p>';
      }
    } else {
      $ret .= '<p><i>Versie is onbekend.</i></p>';
    }
  }

  return $ret;
}
?>
