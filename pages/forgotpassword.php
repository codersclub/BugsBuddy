<?php

/*
	If a user forgot his password (mail him a new one)
*/

require_once('includes/Database.php');
require_once('includes/htmlsafe.php');
require_once('includes/Mail.php');

function getforgotpassword() {
	if (isset($_GET) && isset($_GET['mail'])) {
		$mail = strtolower(htmlUnsafe($_GET['mail']));
		return handleForgotPassword($mail);
	} else {
		return getForgotPasswordForm();
	}
	
}

function getForgotPasswordForm() {
	if (isset($_GET) && isset($_GET['js']) && $_GET['js'] == "no") {
		$returnValue = '';
		$returnValue .= '<form action="index.php" method="get">';
		$returnValue .= '<div><input type="hidden" name="js" value="no" /></div>';
		$returnValue .= '<div><input type="hidden" name="page" value="forgotpassword" /></div>';
		$returnValue .= '<div class="forgotpasswordlabel"><label style="width: 100;" for="mail">Mail:</label></div><div class="forgotpasswordinput"><input class="forgotpasswordinputcontent" type="text" id="mail" name="mail" value="" /></div>';
		$returnValue .= '<div class="registerlabel"><label for="submit">Reset password:</label></div><div class="registerinput"><input class="registerinputcontent" id="submit" type="submit" value="send mail!" /></div>';
		$returnValue .= '</form>';
		return $returnValue;
	} else {
		$returnValue = '';
		$returnValue .=	'<form method="get" target="submitFrame" onsubmit="getNewContent(\'forgotpassword\&mail=\'+document.getElementById(\'mail\').value); return false;">';
		$returnValue .=   '<div><input type="hidden" name="page" value="forgotpassword" /></div>';
		$returnValue .=		'<div class="forgotpasswordlabel"><label for="mail">Mail:</label></div><div class="forgotpasswordinput"><input class="forgotpasswordcontent" type="text" name="mail" id="mail" value="" /></div>';
		$returnValue .=		'<div class="registerlabel"><label for="submit">Reset password:</label></div><div class="registerinput"><input class="registerinputcontent" id="submit" type="submit" value="send mail!" /></div>';
		$returnValue .=	'</form>';
		$returnValue .=	'<br />';
		$returnValue .=	'<div id="forgotPassword">';
		$returnValue .=	'</div>';
		return $returnValue;
	}
}

function handleForgotPassword($mail) {
	$errorMessage = '';
	$error = false;
	$user = Database::getUserByEmail($mail);
	if (!$user || count($user) != 1) {
		$errorMessage .= "\nDit e-mail adres is niet in gebruik.";
		$error = true;
	}

	if ($error) {
		return getForgotPasswordForm() . '<br />' . nl2br($errorMessage);
	}
	$newPassword = createRandomPassword();
	$emailMessage = new Mail('Bugtracker password reset', '<html><head><title>Password reset</title></head><body>U (of iemand anders) heeft het wachtwoord gereset voor het account met dit e-mailadres.<br />Het nieuwe wachtwoord is nu: '.htmlSafe($newPassword).'<br />Heeft u niet u niet om een nieuw wachtwoord gevraagt, gelieve contact op te nemen met '.getConfigurationValue('webmastermail').'<br />Met vriendelijke groet, Alex de Vries.</body></html>');
	$result = $emailMessage->send($mail);
	if ($result !== false) {
		$registerMessage = 'Er is een mail gestuurd met daarin het nieuwe wachtwoord naar &nbsp;'.htmlSafe($mail).'&nbsp;';
		Database::updateUserPassword($user[0]['id'], $newPassword);
	} else {
		$registerMessage = getForgotPasswordForm() . '<br />Het wachtwoord is NIET aangepast, omdat de server niet in staat was om een nieuw wachtwoord via e-mail toe te sturen.';
	}
	return $registerMessage;
}

?>
