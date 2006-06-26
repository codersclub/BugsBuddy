<?php

/*
	Register Page...
*/

require_once('includes/htmlsafe.php');
require_once('includes/Mail.php');
require_once('includes/Database.php');
require_once('includes/helperfunctions.php');

function getregister() {
	if (isset($_POST) && isset($_POST['name']) && isset($_POST['password']) && isset($_POST['mail'])) {
		$name 		= htmlUnsafe($_POST['name']);
		$password 	= htmlUnsafe($_POST['password']);
		$mail 		= strtolower(htmlUnsafe($_POST['mail']));
		
		return handleRegistry($name, $password, $mail);
	} else {
		return getRegistryForm();
	}
}

function getRegistryForm() {
	if (isset($_GET) && isset($_GET['js']) && $_GET['js'] == "no") {
		if (isset($_POST) && isset($_POST['name']) && isset($_POST['password']) && isset($_POST['mail'])) {
			$name = $_POST['name'];
			$mail = strtolower($_POST['mail']);
		} else {
			$name = '';
			$mail = '';
		}
		return ''.
			'<form action="'.getCurrentRequestUrl().'" method="post">'.
				'<div class="registerlabel"><label for="name">Naam:</label></div><div class="registerinput"><input class="registerinputcontent" type="text" id="name" name="name" value="'.$name.'" /></div>'.
				'<div class="registerlabel"><label for="password">Wachtwoord:</label></div><div class="registerinput"><input class="registerinputcontent" type="password" id="password" name="password" value="" /></div>'.
				'<div class="registerlabel"><label for="mail">E-Mail:</label></div><div class="registerinput"><input class="registerinputcontent" type="text" id="mail" name="mail" value="'.$mail.'" /></div>'.
				'<div class="registerlabel"><label for="submit">Registreer:</label></div><div class="registerinput"><input class="registerinputcontent" id="submit" name="submit" type="submit" value="Registreer" /></div>'.
			'</form>';
	} else {
		return ''.
		    '<form action="script.php?page=register" method="post" target="submitFrame">'.
				'<div class="registerlabel"><label for="registrationName">Naam:</label></div><div class="registerinput"><input class="registerinputcontent" type="text" name="name" id="registrationName" value="" /></div>'.		
				'<div class="registerlabel"><label for="registrationPassword">Wachtwoord:</label></div><div class="registerinput"><input class="registerinputcontent" type="password" name="password" id="registrationPassword" value="" onkeyup="checkPassWordStrength(\'registrationPassword\');"/>'.
				'<br /><br /><div class="pwdStrength" id="pwdStrength"><div class="pwdBeamGreen" id="pwdBeamGreen"></div></div><div class="pwdText" id="pwdText"></div></div><br /><br />' .
				'<div class="registerlabel"><label for="mail">E-Mail:</label></div><div class="registerinput"><input class="registerinputcontent" type="text" id="mail" name="mail" value="" /></div>'.
				'<div class="registerlabel"><label for="submit">Registreer:</label></div><div class="registerinput"><input class="registerinputcontent" id="submit" name="submit" type="submit" value="Registreer" /></div>'.
			'</form>'.
			'<br />'.
			'<div id="registerError">'.
			'</div>';
	}
}

function handleRegistry($name, $password, $mail) {
	
	$errorMessage = '';
	$error = false;
	if (!isValidUsername($name)) {
		$errorMessage .= "\nDe gebruikersnaam is niet geldig. De naam mag bestaan uit alphanumerieke tekens met tussenliggende spaties.";
		$error = true;
	}
	if (!isValidPassword($password)) {
		$errorMessage .= "\nHet wachtwoord is niet geldig. Het wachtwoord moet minstens uit 6 tekens bestaan.";
		$error = true;
	}
	if (!isValidEmailAddress($mail)) {
		$errorMessage .= "\nHet e-mail adres is niet geldig.";
		$error = true;		
	}
	if (count(Database::getUserByName($name)) != 0) {
		$errorMessage .= "\nDe door u gekozen gebruikersnaam is al in gebruik.";
		$error = true;
	}
	if (count(Database::getUserByEmail($mail)) != 0) {
		$errorMessage .= "\nHet door u gekozen e-mail adres is al in gebruik.";
		$error = true;
	}
	if (isset($_GET) && isset($_GET["js"]) && $_GET['js'] == "no") {
		if ($error) {
			return getRegistryForm() . '<br />' . nl2br($errorMessage);
		}
		Database::registerUser($name, $password, $mail);
		$emailMessage = new Mail('Welkom op bugtracker', '<html><head><title>Welkom '.$_POST['name'].'</title></head><body>Welkom op de bucktracker site http://bugsbunny.slapware.eu/. U kunt nu inloggen met uw email adres en wachtwoord.<br /><br />Met vriendelijke groet, Alex de Vries.</body></html>');
		$emailMessage->send($mail);
		$registerMessage = 'Bedank voor het registreren, u kunt nu inloggen op de site';
		return $registerMessage;
	} else {
		if ($error) {
			$returnValue =  ''.
				'<html>'.
					'<head>'.
					'</head>'.
					'<body>'.
						'<script>'.
							'window.parent.document.getElementById("registrationPassword").value="";'.
							'window.parent.document.getElementById("registrationName").focus();'.
							'window.parent.document.getElementById("registrationName").select();'.
							'window.parent.document.getElementById("registerError").innerHTML = "'.safenl2br(htmlsafe($errorMessage)).'";'.
						'</script>'.
					'</body>'.
				'</html>';
			return $returnValue;
		} else {
			Database::registerUser($name, $password, $mail);
			$returnValue =  ''.
				'<html>'.
					'<head>'.
					'</head>'.
					'<body>'.
						'<script>'.
							'window.parent.updateContent("Successvol geregistreerd");'.
						'</script>'.
					'</body>'.
				'</html>';
			return $returnValue;
		}
	}
}
?>