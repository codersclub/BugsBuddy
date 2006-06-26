<?php

require_once('includes/Database.php');
require_once('includes/htmlsafe.php');
require_once('includes/helperfunctions.php');

function getlogin() {
	if (isset($_POST) && isset($_POST['email']) && isset($_POST['pass'])) {
		if ($_GET['js'] == "no") {
			//don't do anything. It is allready been taken care of by 'setLoginSession()' and index.php
			require_once('pages/home.php');
			return gethome();
		} else {
			$returnValue = '';
			$user = Database::getUserByLogin(htmlUnsafe($_POST['email']), htmlUnsafe($_POST['pass']));
			if ($user && count($user)==1) {
				if (!setLoginSession(htmlUnsafe($_POST['email']), htmlUnsafe($_POST['pass']), ((isset($_POST['stay'])&&$_POST['stay']=='on')?true:false), ((isset($_POST['ip'])&&$_POST['ip']=='on')?true:false))) {
					$returnValue .= '<script>window.parent.updateLogin("OMFG")</script>';
				} else {
					$returnValue .= '<script>';
					$returnValue .= 	'window.parent.updateLogin("'.addslashes(getLogoutHtml()).'");';
					$returnValue .= 	'window.parent.updateLinks("'.str_replace ("\n", "", addslashes(getLinksHtml())).'");';
					$returnValue .= 	'window.parent.getNewContent("home");';
					$returnValue .= '</script>';
				}
			} else {
				$returnValue .= '<script>';
				$returnValue .= 	'window.parent.updateLogin("'.addslashes(getWrongLoginHtml()).'");';
				//$returnValue .= 	'window.parent.updateLinks("'.str_replace ("\n", "", addslashes(getLinksHtml())).'");';
				$returnValue .= '</script>';
			}
			require_once('pages/home.php');
			return gethome() . $returnValue;
		}
	} else {
		// Login page is viewed, but no login data is submitted, just show the homepage
		require_once('pages/home.php');
		return gethome();
	}
}

/*
 * This function handles the login for non-AJAX requests. the 'getLogin()' is not used, because we have
 * to know in an more early stage if the user is logged in or not. used by index.php
 */
function setLoginSession($htmlUnsafeEmail, $htmlUnsafePassword, $stay, $ip) {
	//$user = Database::getUserByLogin(htmlUnsafe($_POST['email']), htmlUnsafe($_POST['pass']));
	$user = Database::getUserByLogin($htmlUnsafeEmail, $htmlUnsafePassword);
	if ($user && count($user)==1) {
		return setLoginSessionByUserArray($user, $stay, $ip);
	}
	return false;
}

function setLoginSessionByUserArray($user, $stay, $ip) {
	$_SESSION["loggedIn"] = true;
	$_SESSION["username"] = $user[0]['name'];
	$_SESSION["userId"] = $user[0]['id'];
	$_SESSION["userGroupId"] = $user[0]['group_id'];
	if ($ip===true) {
		$_SESSION["userIp"] = getUserIp();
	} else {
		$_SESSION["userIp"] = false;
	}
	if ($stay) {
		setcookie('AUTOLOGIN', $_SESSION["username"].'@'.passwordHash($user[0]['name'].$user[0]['id'].getUserIp()), time()+60*60*24); // expire in 30 days
	} else {
		setcookie('AUTOLOGIN', '', time()+60*60*24); // expire in 30 days
	}
	return true;
}

/*
 * Do 'NOT' use newlines ('\n') in the sourcecode below!
 */
function getLoginHtml() {
	$returnValue = '				';
	if (isset($_GET['js']) && $_GET['js'] == "no") {
		$returnValue .= '<a href="index.php?js=no&page=register">Registreer</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="index.php?js=no&page=forgotpassword">Wachtwoord vergeten?</a>';
	} else {
		$returnValue .= '<a href="javascript:getNewContent(\'register\');">Registreer</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:getNewContent(\'forgotpassword\');">Wachtwoord vergeten?</a>';
	}
	if (isset($_GET['js']) && $_GET['js'] == "no") {
		$returnValue .= '<form action="index.php?js=no&page=login" method="post">';
		$returnValue .=   '<p class="nomargin">';
		$returnValue .=   '<input type="text" value="e-mail adres" name="email" id="email" />&nbsp;';
		$returnValue .=   '<input type="password" value="" name="pass" id="pass" />';
	} else {
		$returnValue .= '<form action="script.php?js=yes&page=login" method="post" target="submitFrame" onsubmit="return loginChecker();">';
		$returnValue .=   '<p class="nomargin">';
		$returnValue .=   '<input type="text" value="e-mail adres" id="input_email" onclick="formClear(\'input_email\', \'e-mail adres\');" />&nbsp;';
		$returnValue .=   '<input type="password" value="wachtwoord" id="input_pass" onclick="formClear(\'input_pass\', \'wachtwoord\');" />';
		$returnValue .=   '<input type="hidden" name="email" id="email" value="">';
		$returnValue .=   '<input type="hidden" name="pass" id="pass" value="">';
	}
	$returnValue .=   '<input type="submit" value="Login" /><br />';
	$returnValue .=   '<input type="checkbox" name="stay" class="noborder" /> Blijf ingelogd <input type="checkbox" name="ip" class="noborder" checked="checked" /> Statisch IP';
	$returnValue .=   '</p>';
	$returnValue .= '</form>';
	return $returnValue;
}

/*
 * Do 'NOT' use newlines in the sourcecode below!
 */
function getLogoutHtml() {
		$returnValue = '				'; 
		$returnValue .= 'Welkom terug <strong>'.getCurrentSafeUsername().'</strong><br />';
		$returnValue .= pageLink('logout', 'Uitloggen') . '&nbsp;&nbsp;&nbsp;';
		$returnValue .= pageLink('changepassword', 'Wachtwoord wijzigen');
		
		//pageLink('changepassword', 'VERANDER WACHTWOORD', 'm') . '&nbsp;&nbsp;&nbsp;&nbsp;'.
		return $returnValue;
}

function getWrongLoginHtml() {
	return "Gebruikersnaam en/of wachtwoord verkeerd<br/>Klik <a href=\"index.php?js=".((isset($_GET['js'])&&$_GET['js']=='yes')?'yes':'no')."\">hier</a> om het nog eens te proberen";
}

?>
