<?php

/*
 * Author:      Jay-P en Erik-P
 * Date:        06 June 2006
 * Version:     1.0
 * Description: buglist
 * View and edit possible bug statusvalues
 */

function getbugstatus() {
	$returnValue = '<h1>Bugstatus toevoegen of verwijderen</h1>';
	if (!empty($_GET['id'])) {
		if (is_numeric($_GET['id']) && intval($_GET['id']) != 1) {
			Database::delbugstatus($_GET['id']);
			$returnValue .= '<p>Bugstatus verwijderd</p><br />';
			$returnValue .= '<a href="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=bugstatus"> << Back </a>';
		} else {
			$returnValue .= '<p><i>Geen geldig id</i></p>';
		}
	} else {

		if (!empty($_POST['delthis'])) {
			if (is_numeric($_POST['delthis'])) {
				if (intval($_POST['delthis']) != 1) {
					$bugstatusname = Database::getbugstatuswithid($_POST['delthis']);
					foreach  ($bugstatusname as $row) {
						$bugstatusname = $row['name'];
					}
					$bugnamecount = Database::countbugstatus($_POST['delthis']);
					$bugnamecount = count($bugnamecount);
					if ($bugnamecount > 0) {
						$returnValue .= '<p>Er zijn bugs gevonden met dezelfde status, deze zullen verwijderd worden.<br />';
						$returnValue .= 'Aantal bugs met dezelfde status: ' . $bugnamecount . '</p>';
						$returnValue .= '<p>Weet u zeker dat u door wilt gaan.</p>';
						$returnValue .= '<a href="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=bugstatus&id=' . $_POST['delthis'] . '">Ja</a>&nbsp;';
						$returnValue .= '&nbsp;<a href="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=bugstatus">Nee</a>';
					} else {
						Database::delbugstatus($_POST['delthis']);
						$returnValue .= '<p>Bugstatus ' . $bugstatusname . ' verwijderd</p><br />';
						$returnValue .= '<a href="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=bugstatus"> << Back </a>';
					}
				} else {
					$returnValue .= '<p>Deze bugstatus kan niet worden verwijdert, omdat dit de standaard bug-status is.</p><br />';
					$returnValue .= '<a href="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=bugstatus"> << Back </a>';
				}
			} else {
				$returnValue .= '<p><i>Geen geldig id</i></p>';
			}
		} elseif (!empty($_POST['bugaddname'])) {
				Database::insbugstatus($_POST['bugaddname']);
				$returnValue .= '<p>Bugstatus ' . $_POST['bugaddname'] . ' toegevoegd</p><br />';
				$returnValue .= '<div><a href="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=bugstatus"> << Back </a></div>';
				$returnValue .= '<meta http-equiv="refresh" content="3;URL=index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=bugstatus" />';
		} else {
			//return "Admin Bug Status Page";

			
			
			$returnValue .= '<form action="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=bugstatus" id="add" method="post">';
				$returnValue .= '<div>Bugstatus toevoegen: <input type="text" name="bugaddname" />';
				$returnValue .= '&nbsp;<input type="submit" value="Toevoegen"/></div>';
			$returnValue .= '</form>';
			
			$returnValue .= '<form id="delete" method="post" action="index.php?'.((isset($_GET['js'])&&$_GET['js']=='yes')?'js=yes':'js=no').'&page=bugstatus">';
				$returnValue .= '<div>Bugstatus verwijderen: <input type="hidden" name="page" value="bugstatus" />';
					$returnValue .= '<select name="delthis">';
					
			$getbugs = Database::getbugstatus();
			if(!empty($getbugs)) {
				foreach  ($getbugs as $bugstatus) {
					$returnValue .= '<option value="' . $bugstatus['id'] . '">' . $bugstatus['name'] . '</option>';
				}
			} else {
				$returnValue .= '<option value="0"></option>';
			}

					$returnValue .= '</select>';
				$returnValue .= '&nbsp;<input type="submit" value="Verwijder"/></div>';
			$returnValue .= '</form>';
		}
	}
	return $returnValue;
}

?>