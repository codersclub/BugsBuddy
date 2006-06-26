<?php
	function showAdminPages($permissions) {
		$numberOfLinks = 0;
		if (isset($permissions) && isset($permissions['mayview_admin']) && $permissions['mayview_admin'] == 'true') {
			if (isset($permissions['mayview_admin_bbtags']) && $permissions['mayview_admin_bbtags'] == 'true'){
				echo '&nbsp;&nbsp;<a class="m" href="index.php?js='.((isset($_GET['js'])&&$_GET['js']=='yes')?'yes':'no').'&page=bbtags">BBTAGS</a>';
			}
			if (isset($permissions['mayview_admin_bugpriority']) && $permissions['mayview_admin_bugpriority'] == 'true'){
				echo '&nbsp;&nbsp;<a class="m" href="index.php?js='.((isset($_GET['js'])&&$_GET['js']=='yes')?'yes':'no').'&page=bugpriority">BUGPRIORITY</a>';
			}
			if (isset($permissions['mayview_admin_bugstatus']) && $permissions['mayview_admin_bugstatus'] == 'true'){
				echo '&nbsp;&nbsp;<a class="m" href="index.php?js='.((isset($_GET['js'])&&$_GET['js']=='yes')?'yes':'no').'&page=bugstatus">BUGSTATUS</a>';
			}
			if (isset($permissions['mayview_admin_categories']) && $permissions['mayview_admin_categories'] == 'true'){
				echo '&nbsp;&nbsp;<a class="m" href="index.php?js='.((isset($_GET['js'])&&$_GET['js']=='yes')?'yes':'no').'&page=bugcategory">BUGCATEGORY</a>';
			}			
			if (isset($permissions['mayview_admin_editconfig']) && $permissions['mayview_admin_editconfig'] == 'true'){
				echo '&nbsp;&nbsp;<a class="m" href="index.php?js='.((isset($_GET['js'])&&$_GET['js']=='yes')?'yes':'no').'&page=editconfig">EDIT CONFIG</a>';
			}
			if (isset($permissions['mayview_admin_permissions']) && $permissions['mayview_admin_permissions'] == 'true'){
				echo '&nbsp;&nbsp;<a class="m" href="index.php?js='.((isset($_GET['js'])&&$_GET['js']=='yes')?'yes':'no').'&page=permissions">PERMISSIONS</a>';
			}
			if (isset($permissions['mayview_admin_project']) && $permissions['mayview_admin_project'] == 'true'){
				echo '&nbsp;&nbsp;<a class="m" href="index.php?js='.((isset($_GET['js'])&&$_GET['js']=='yes')?'yes':'no').'&page=project">PROJECT</a>';
			}
			if (isset($permissions['mayview_admin_users']) && $permissions['mayview_admin_users'] == 'true'){
				echo '&nbsp;&nbsp;<a class="m" href="index.php?js='.((isset($_GET['js'])&&$_GET['js']=='yes')?'yes':'no').'&page=users">USERS</a>';
			}
		}
	}
?>