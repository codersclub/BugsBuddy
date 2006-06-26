<?php
/*
 * Author:      Daan Keuper
 * Date:        12 June 2006
 * Version:     1.0
 * Description: edit configuration
 */

function geteditconfig() {
	$msg = '';
	
	if (!empty($_POST)) {
		Database::updateConfig($_POST['email'], $_POST['from']);

		$msg = 'Configuratie gewijzigd.';
	}

	$ret = '<h1>Wijzig configuratie</h1>';
	
	if (!empty($msg)) {
		$ret .= '<p><span class="error">' . $msg . '</span></p>';
	}
	
	$config = Database::getAllConfig();

	foreach ($config as $row) {
		if ($row['setting'] == 'webmastermail') {
			$email = $row['value'];
		} elseif ($row['setting'] == 'mailfrom') {
			$from = $row['value'];
		}
	}

	$ret .= '<form action="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=editconfig" method="post">' .
				'<div class="registerlabel"><label for="email">Webmaster mail:</label></div><div class="registerinput"><input  type="text" id="email" name="email" value="' . $email . '" /></div>' .
				'<div class="registerlabel"><label for="from">E-mail from:</label></div><div class="registerinput"><input  type="text" id="from" name="from" value="' . $from . '" /></div>' .
				'<div class="registerlabel"><label for="verzenden">Verzenden:</label></div><div class="registerinput"><input  id="verzenden" name="verzenden" type="submit" value="Wijzig" /></div>'.
				'</form>';

	return $ret;	
}

?>
