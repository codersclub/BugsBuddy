<?php

/*
	Let a user change his password
*/

require_once('includes/helperfunctions.php');

function getchangepassword() {
	if (defined('PASSWORD_CHANGED')) {
		return 'Je wachtwoord is verandert.';
	} else if (defined('PASSWORD_NOT_CHANGED')) {
		return 'Je wachtwoord is niet verandert, omdat het wachtwoord minstens uit 6 tekens moet bestaan.';
	} else {
		if (isLoggedIn()) {
			$returnValue = '';
			$returnValue .=	'<form action="index.php?js='.(isset($_GET['js'])&&$_GET['js']=='no'?'no':'yes').'" method="post">';
			$returnValue .=		'<div class="changepasswordlabel"><label for="password">wachtwoord:</label></div><div class="forgotpasswordinput"><input class="forgotpasswordcontent" type="password" name="changepassword" id="password" value="" /></div>';
			$returnValue .=		'<div class="changepasswordlabel"><label for="submit">verander wachtwoord:</label></div><div class="registerinput"><input class="registerinputcontent" id="submit" type="submit" value="verander!" /></div>';
			$returnValue .=	'</form>';
			$returnValue .=	'<br />';
			$returnValue .=	'<div id="forgotPassword">';
			$returnValue .=	'</div>';
			return $returnValue;
		} else {
			$returnValue = '';
			$returnValue .=	'Je moet ingelogt zijn om deze pagina te kunnen bekijken.';
			return $returnValue;
		}
	}
}

?>
