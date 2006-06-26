<?php

	session_start();

	chdir('../');
	include('./includes/htmlsafe.php');
	require_once('./includes/helperfunctions.php');
	require_once('./includes/Database.php');
	
	
	$userGroup = getCurrentGroupId();
	$permissionsResults = Database::getPermissions(intval($userGroup));
	$permissions = Array();
	foreach($permissionsResults as $permissionsResult) {
		$permissions[$permissionsResult['setting']] = $permissionsResult['value'];
	}
	if (!isset($permissions['mayview_admin']) || $permissions['mayview_admin'] != 'true') {

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>
	<head>
		<noscript>
			<meta http-equiv="refresh" content="0;../index.php?js=no" />
		</noscript>
		<script type="text/javascript">
			document.location.href="../index.php?js=yes";
		</script>
	</head>
	<body>
	</body>
</html>
<?php
		exit;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>
	<head>
		<title>BugsBuddy</title>
		<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
		<link href="../style/default.css" rel="stylesheet" type="text/css" />
	</head>
	<body>

		<div id="container">
			<div id="header">
				<img src="../images/logo.gif" alt="Bugsbuddy" />
			</div>
			<div id="balk">
				<a class="m" href="../?js=<?=((isset($_GET['js'])&&$_GET['js']=='yes')?'yes':'no')?>">HOME</a>
<?
	require_once('./admin/pages.php');
	showAdminPages($permissions);
?>
			</div>
			<div id="content">
<?php
	if (!isset($_GET['page'])) {
		echo "<h1>Welkom op de adminpagina.</h1>";
	} else {
		if ($_GET['page']=='bbtags' && isset($permissions['mayview_admin_bbtags']) && $permissions['mayview_admin_bbtags'] == 'true') {
			require_once('./admin/bbtags.php');
			echo getbbtags();
		} else if ($_GET['page']=='bugpriority' && isset($permissions['mayview_admin_bugpriority']) && $permissions['mayview_admin_bugpriority'] == 'true') {
			require_once('./admin/bugpriority.php');		
			echo getbugpriority();
		} else if ($_GET['page']=='bugstatus' && isset($permissions['mayview_admin_bugstatus']) && $permissions['mayview_admin_bugstatus'] == 'true') {
			require_once('./admin/bugstatus.php');		
			echo getbugstatus();
		} else if ($_GET['page']=='editconfig' && isset($permissions['mayview_admin_editconfig']) && $permissions['mayview_admin_editconfig'] == 'true') {
			require_once('./admin/editconfig.php');		
			echo geteditconfig();
		} else if ($_GET['page']=='permissions' && isset($permissions['mayview_admin_permissions']) && $permissions['mayview_admin_permissions'] == 'true') {
			require_once('./admin/permissions.php');		
			echo getpermissions();
		} else if ($_GET['page']=='project' && isset($permissions['mayview_admin_project']) && $permissions['mayview_admin_project'] == 'true') {
			require_once('./admin/project.php');		
			echo getproject();
		} else if ($_GET['page']=='users' && isset($permissions['mayview_admin_users']) && $permissions['mayview_admin_users'] == 'true') {
			require_once('./admin/users.php');		
			echo getusers();
		} else if ($_GET['page']=='projectstatus' && isset($permissions['mayview_admin_projectstatus']) && $permissions['mayview_admin_projectstatus'] == 'true') {
			require_once('./admin/projectstatus.php');		
			echo getprojectstatus();
		} else if($_GET['page']=='bugcategory' && isset($permissions['mayview_admin_categories']) && $permissions['mayview_admin_categories'] == 'true') {
			require_once('./admin/bugcategory.php');
			echo getbugcategory();
		} else {
			echo "You do not have the right permissions to view this page.";
		}
	}
?>
			</div>
			<div id="footer">
				XHTML 1.0 Strict & CSS2 valid.
			</div>
		</div>
	</body>
</html>
<?php



?>