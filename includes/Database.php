<?php
/*
 * Author:      Daan Keuper
 * Date:        22 May 2006
 * Version:     1.0
 * Description: the database class with all the query's for BugsBuddy
 */

require_once(ROOT_DIR.'/includes/MySQL.php');

class Database {

  static function install($database, $name, $email, $password) {

    MySQL::query('DROP DATABASE IF EXISTS `' . addslashes($database) . '`');
    MySQL::query('CREATE DATABASE `' . addslashes($database) . '` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci');
    MySQL::select_db(addslashes($database));

    $filename = ROOT_DIR.'/install/install.sql';
    $handle = fopen ($filename, 'rb');
    $sql = fread ($handle, filesize ($filename));
    fclose ($handle);

    $splittedSQLNix = preg_split("/;\r\n/", $sql);
    $splittedSQLWindows = preg_split("/;\n/", $sql);

    $splittedSQL = Array();

    if (count($splittedSQLNix) == 1) {
      $splittedSQL = $splittedSQLWindows;
    } else {
      $splittedSQL = $splittedSQLNix;
    }

    foreach($splittedSQL as $query) {
      MySQL::query($query);
    }

    MySQL::query('INSERT INTO users (name, passwordhash, email, group_id) VALUES (\'' . addslashes($name) . '\', \'' .  addslashes($password) . '\', \'' . addslashes($email) . '\', 3)');

    return true;
  }

  static function getPages() {
    return MySQL::query('SELECT page FROM pages WHERE 1');
  }

  static function getConfig() {
    return MySQL::query('SELECT setting, value FROM config WHERE 1');
  }

  static function getAllConfig() {
    return MySQL::query('SELECT * FROM config');
  }

  static function getVisibleProjectsAndVersions($userId) {
    return MySQL::query('SELECT project.id AS projectId, name, projectversion.id AS versionId, version FROM project INNER JOIN projectversion ON project.id = project_id WHERE projectstatus_id = 1');
  }

  static function getProjectsAndVersions() {
    return MySQL::query('SELECT project.id AS projectId, name, projectversion.id AS versionId, version FROM project INNER JOIN projectversion ON project.id = project_id');
  }

  static function getVersionsFromProject($id) {
    return MySQL::query("SELECT * FROM projectversion WHERE project_id = $id");
  }

  static function getVisibleProjects($userId) {
    return MySQL::query('SELECT id, name FROM project WHERE projectstatus_id = 1');
  }

  static function getAllProjects() {
    return MySQL::query('SELECT id, name, projectstatus_id FROM project');
  }

  static function versionWithID($id) {
    return MySQL::query('SELECT id FROM projectversion WHERE id = ' . $id);
  }

  static function getBugForUser($bugId, $user_id) {
    $query = MySQL::query("SELECT b.* FROM bugs b, projectversion pu, project p WHERE b.id = $bugId AND b.productversion_id = pu.id AND pu.project_id = p.id AND p.projectstatus_id = 1");

    if (count($query) == 1) {
      return Database::getBug($bugId);
    } else {
      $query = MySQL::query("SELECT b.id FROM bugs b, projectversion pu, project p, projectusers v WHERE b.id = $bugId AND b.productversion_id = pu.id AND pu.project_id = p.id AND p.projectstatus_id = 2 AND v.project_id = p.id AND v.user_id = $user_id");

      if (count($query) == 1) {
        return Database::getBug($bugId);
      } else {
        return false;
      }
    }
  }

  static function getCountWithVersionID($id) {
    return MySQL::query("SELECT b.id FROM bugs b, projectversion p WHERE b.productversion_id = p.id AND p.id = $id");
  }

  static function deleteVersion($id) {
    return MySQL::query("DELETE FROM projectversion WHERE id = $id");
  }

  static function getVersions($projectId) {
    return MySQL::query('SELECT id, version FROM projectversion WHERE project_id = '. $projectId);
  }

  static function getGroups() {
    return MySQL::query('SELECT id, name FROM usergroups WHERE 1');
  }

  static function getCategorys() {
    return MySQL::query('SELECT id, category FROM bugcategory WHERE 1 ORDER BY category');
  }

  static function submitBug($title, $description, $versionId, $category1, $category2) {
    if (!is_numeric($versionId) || !is_numeric($category1) || !is_numeric($category2)) {
      return null;
    }

    return MySQL::query('INSERT INTO bugs (title, description, user_id, submitdate, productversion_id, priority_id, status_id, category1_id, category2_id) '.
              'VALUES (\'' . addslashes($title) . '\', \'' . addslashes($description) . '\', ' . getCurrentUserId() . ', ' . time() . ', ' . $versionId . ', 1, 1, '. $category1 .', '. $category2 .')');
  }

  static function updateBug($bugId, $title, $description, $versionId, $category1, $category2, $priorityId, $bugStatusId, $fixedInId) {
    if (!is_numeric($bugId) && !is_numeric($versionId) || !is_numeric($category1) || !is_numeric($category2) || !is_numeric($priorityId) || !is_numeric($bugStatusId) || !is_numeric($fixedInId)) {
      return null;
    }

    return MySQL::query('UPDATE bugs SET title           = \''.addslashes($title).'\', '.
              '         description      = \''.addslashes($description).'\', '.
              '         productversion_id    =   '.$versionId.',  '.
              '         category1_id      =   '.$category1.',  '.
              '         category2_id      =   '.$category2.',  '.
              '         priority_id      =   '.$priorityId.', '.
              '         status_id        =   '.$bugStatusId.','.
              '         productversionfixed_id =   '.$fixedInId.'   '.
              ' WHERE id = '.$bugId);
  }

  static function getUsersFromProject($id) {
    return MySQL::query("SELECT u.name, u.email FROM users u, projectusers p WHERE u.id = p.user_id AND p.project_id = $id");
  }

  static function getPermissions($groupId) {
    if (!is_numeric($groupId)) {
      return null;
    }
    return MySQL::query('SELECT setting,value FROM permissions WHERE level_id = \''.$groupId.'\'');
  }

  static function getUserByName($username) {
    return MySQL::query('SELECT * FROM users WHERE name = \'' . addslashes($username) . '\'');
  }

  static function getUserByEmail($email) {
    return MySQL::query('SELECT * FROM users WHERE email = \'' . addslashes($email) . '\'');
  }

  static function getUserByLogin($email, $password) {
    return MySQL::query('SELECT * FROM users WHERE email = \'' . addslashes($email) . '\' AND passwordhash = \'' . addslashes(passwordHash($password)) . '\'');
  }

  static function registerUser($name, $password, $email, $groupid=0) {
//DEBUG
//echo '<pre>';
//echo 'registerUser:', "\n";
//echo 'name=', $name, "\n";
//echo 'password=', $password, "\n";
//echo 'email=', $email, "\n";
//echo 'groupid=', $groupid, "\n";
//echo '</pre>';
//exit;
    return MySQL::query('INSERT INTO users (name, passwordhash, email, group_id) VALUES (\'' . addslashes($name) . '\', \'' .  addslashes(passwordHash($password)) . '\', \'' . addslashes($email) . '\', \'' . intval($groupid) . '\')');
  }

  static function updateConfig($email, $from) {
    MySQL::query("UPDATE config SET value = '" . addslashes($from) . "' WHERE setting = 'mailfrom'");
    MySQL::query("UPDATE config SET value = '" . addslashes($email) . "' WHERE setting = 'webmastermail'");

    return true;
  }

  static function updateUserPassword($userId, $newPassword) {
    if (!is_numeric($userId)) {
      return null;
    }
    return MySQL::query('UPDATE users SET passwordhash=\''.addslashes(passwordHash($newPassword)).'\' WHERE id =\''.$userId.'\' LIMIT 1;');
  }

  static function updateUser($userId, $htmlSafeNewName, $htmlSafeNewEmail, $newGroupId=null) {
    if (!is_numeric($userId)) {
      return null;
    }
    if ($newGroupId === null) {
      return MySQL::query('UPDATE users SET name=\''.addslashes(htmlUnsafe($htmlSafeNewName)).'\', email=\''.addslashes(htmlUnsafe($htmlSafeNewEmail)).'\' WHERE id =\''.$userId.'\' LIMIT 1;');
    } else {
      if (!is_numeric($newGroupId)) {
        return null;
      }
      return MySQL::query('UPDATE users SET name=\''.addslashes(htmlUnsafe($htmlSafeNewName)).'\', email=\''.addslashes(htmlUnsafe($htmlSafeNewEmail)).'\', group_id=\''.$newGroupId.'\' WHERE id =\''.$userId.'\' LIMIT 1;');
    }
  }

  static function getAllUsers() {
    return MySQL::query('SELECT u.id, u.name, u.email, ug.name groupname
			FROM users u
			LEFT JOIN usergroups ug
				ON (u.group_id = ug.id)');
  }

  static function getUserById($id) {
    if (!is_numeric($id)) {
      return null;
    }
    return MySQL::query('SELECT u.id, u.name, u.email, u.group_id, ug.name groupname
			FROM users u
			LEFT JOIN usergroups ug
				ON (u.group_id = ug.id)
			WHERE u.id = \'' . $id . '\'');
  }

  static function deleteUserById($id) {
    if (!is_numeric($id)) {
      return null;
    }
    return MySQL::query('DELETE FROM users WHERE id = \'' . $id . '\' LIMIT 1');
  }

  static function getBug($bugId) {
    if (!is_numeric($bugId)) {
      return null;
    }

    //LEFT JOIN in geval category niet is gevuld of niet is opgegeven.
    return MySQL::query('SELECT bugs.title, '.
              '    bugs.description, '.
              '    bugs.submitdate, '.
              '    bugs.user_id, '.
              '    bugs.priority_id, '.
              '    bugs.status_id, '.
              '    project.name AS projectName, '.
              '    projectversion.project_id, '.
              '    projectversion.id AS version_id, '.
              '    projectversion.version, '.
              '    versionf.id AS versionFixedId, '.
              '    versionf.version AS versionFixed, '.
              '    users.name AS userName, '.
              '    bugstatus.name AS statusName, '.
              '    bugpriority.name AS priorityName, '.
              '    bcat1.id AS category1_id, '.
              '    bcat2.id AS category2_id, '.
              '    bcat1.category AS category1Name, '.
              '    bcat2.category AS category2Name '.
              '  FROM bugs INNER JOIN projectversion         ON bugs.productversion_id    = projectversion.id '.
              '       INNER JOIN project           ON projectversion.project_id  = project.id '.
              '       INNER JOIN users            ON bugs.user_id          = users.id '.
              '       INNER JOIN bugstatus          ON bugs.status_id        = bugstatus.id '.
              '       INNER JOIN bugpriority          ON bugs.priority_id        = bugpriority.id '.
              '       LEFT  JOIN bugcategory AS bcat1    ON bugs.category1_id      = bcat1.id '.
              '       LEFT  JOIN bugcategory AS bcat2    ON bugs.category2_id      = bcat2.id '.
              '       LEFT  JOIN projectversion AS versionf  ON bugs.productversionfixed_id  = versionf.id '.
              ' WHERE bugs.id = '. $bugId);
  }

  static function deleteBug($bugId) {
    if (!is_numeric($bugId)) {
      return null;
    }

    MySQL::query('DELETE FROM bugs WHERE id = '. $bugId);
    MySQL::query('DELETE FROM message WHERE bug_id = '. $bugId);
  }

  static function getBugs($maximum) {
    if (!is_numeric($maximum)) {
      return null;
    }

    return MySQL::query('SELECT b.description, u.name FROM bugs b, users u WHERE b.user_id = u.id ORDER BY b.submitdate DESC, b.id ASC LIMIT ' . $maximum);
  }

  static function getBugList($maximum) {
    if (!is_numeric($maximum)) {
      return null;
    }

    if(getCurrentGroupId() == 1 ||  getCurrentGroupId() == 5) {
      return MySQL::query('SELECT b.id, b.title, u.name username, b.submitdate, s.name status, pv.version projectversion, p.name projectname FROM bugs b, users u, bugstatus s, project p, projectversion pv, projectusers pu WHERE b.user_id = u.id AND b.status_id = s.id AND pv.id = b.productversion_id AND pv.project_id = p.id AND p.id = pu.project_id AND pu.user_id = '.intval(getCurrentUserId()).' AND p.projectstatus_id = 2 UNION '.
                'SELECT b.id, b.title, u.name username, b.submitdate, s.name status, pv.version projectversion, p.name projectname FROM bugs b, users u, bugstatus s, project p, projectversion pv WHERE b.user_id = u.id AND b.status_id = s.id AND pv.id = b.productversion_id AND pv.project_id = p.id AND p.projectstatus_id = 1 ORDER BY submitdate DESC, id ASC LIMIT '. $maximum);
    } else if(!is_numeric(getCurrentGroupId())) {
      return MySQL::query('SELECT b.id, b.title, u.name username, b.submitdate, s.name status, pv.version projectversion, p.name projectname FROM bugs b, users u, bugstatus s, project p, projectversion pv WHERE b.user_id = u.id AND b.status_id = s.id AND pv.id = b.productversion_id AND pv.project_id = p.id AND p.projectstatus_id = 1 ORDER BY b.submitdate DESC, b.id ASC LIMIT ' . $maximum);
    } else {
      return MySQL::query('SELECT b.id, b.title, u.name username, b.submitdate, s.name status, pv.version projectversion, p.name projectname FROM bugs b, users u, bugstatus s, project p, projectversion pv WHERE b.user_id = u.id AND b.status_id = s.id AND pv.id = b.productversion_id AND pv.project_id = p.id ORDER BY b.submitdate DESC, b.id ASC LIMIT ' . $maximum);
    }
  }

  static function getBugAll($projectid=0, $userId, $startfrom, $maximum, $sort='date', $order='desc') {
    if (!is_numeric($maximum)) {
      return null;
    }
    if (!is_numeric($startfrom)) {
      return null;
    }
    if (!is_numeric($userId) && $userId !== null) {
      return null;
    }
    if ($sort != 'description' &&
        $sort != 'poster' &&
        $sort != 'date' &&
        $sort != 'status' &&
        $sort != 'projectversion') {
      return null;
    }
    if ($order != 'asc' && $order != 'desc') {
      return null;
    }
    $orderTable = 'submitdate';
    if ($sort == 'description') {
      $orderTable = 'title';
    } else if ($sort == 'poster') {
      $orderTable = 'username';
    } else if ($sort == 'status') {
      $orderTable = 'status';
    } else if ($sort == 'projectversion') {
      $orderTable = 'status';
    }
    $sqlOrder = 'ORDER BY '.$orderTable.' '.$order.', id ASC';
    $sqlProject = $projectid ? 'AND p.id='.$projectid : '';

    if(getCurrentGroupId() == 1 ||  getCurrentGroupId() == 5) {
      return MySQL::query('SELECT DISTINCT b.id, b.title, u.name username, b.submitdate, \'b.projectstatus_id\', pv.version projectversion, p.name projectname, s.name status FROM bugs b, users u, bugstatus s, project p, projectversion pv, projectusers pu WHERE b.user_id = u.id AND b.status_id = s.id AND pv.id = b.productversion_id AND pv.project_id = p.id AND p.id = pu.project_id AND pu.user_id = '.intval(getCurrentUserId()).' AND p.projectstatus_id = 2 UNION '.
                          'SELECT DISTINCT b.id, b.title, u.name username, b.submitdate, \'b.projectstatus_id\', pv.version projectversion, p.name projectname, s.name status FROM bugs b, users u, bugstatus s, project p, projectversion pv, projectusers pu WHERE b.user_id = u.id AND b.status_id = s.id AND pv.id = b.productversion_id AND pv.project_id = p.id AND p.projectstatus_id = 1 '.$sqlOrder.' LIMIT '.$startfrom.', '.$maximum);
    } else if(!is_numeric(getCurrentGroupId())) {
      return MySQL::query('SELECT DISTINCT b.id, b.title, u.name username, b.submitdate, \'b.projectstatus_id\', pv.version projectversion, p.name projectname, s.name status FROM bugs b, users u, bugstatus s, project p, projectversion pv, projectusers pu WHERE b.user_id = u.id AND b.status_id = s.id AND pv.id = b.productversion_id AND pv.project_id = p.id AND p.projectstatus_id = 1 '.$sqlOrder.' LIMIT '.$startfrom.', '.$maximum);
    } else {
      $sql =	'SELECT DISTINCT
			b.id, b.title,
			u.name username,
			b.submitdate,
			\'b.projectstatus_id\',
			pv.version projectversion,
			p.name projectname,
			s.name status
		FROM bugs b, users u, bugstatus s, project p, projectversion pv, projectusers pu
		WHERE b.user_id = u.id
			AND b.status_id = s.id
			AND pv.id = b.productversion_id
			AND pv.project_id = p.id
			'.$sqlProject.'
			'.$sqlOrder.'
		LIMIT '.$startfrom.', '.$maximum;
      return MySQL::query($sql);
    }
  }

  static function getVisibleBugCount($userId=null) {
    if (!is_numeric($userId) && $userId !== null) {
      return null;
    }
    if(getCurrentGroupId() == 2 ||  getCurrentGroupId() == 3) {
      return MySQL::query('SELECT count(b.id) FROM bugs b');
    }
    if ($userId === null) {
      return MySQL::query('SELECT count(b.id) FROM bugs b, projectversion pv, project p WHERE b.productversion_id = pv.id AND pv.project_id = p.id AND p.projectstatus_id = 1');
    }
    return MySQL::query('SELECT count(b.id) FROM bugs b, projectversion pv, project p, projectusers pu WHERE b.productversion_id = pv.id AND pv.project_id = p.id AND p.id = pu.project_id AND pu.user_id = '.$userId);
  }

  static function getBugComments($bugId) {
    if (!is_numeric($bugId)) {
      return null;
    }

    return MySQL::query('SELECT message.id AS id, bug_id, user_id, users.name AS userName, submitdate, message FROM message INNER JOIN users ON message.user_id = users.id WHERE bug_id = '. $bugId);
  }

  static function getComment($commentId) {
    if (!is_numeric($commentId)) {
      return null;
    }

    return MySQL::query('SELECT bug_id, message FROM message WHERE id = '. $commentId);
  }

  static function updateComment($commentId, $message) {
    if (!is_numeric($commentId)) {
      return null;
    }

    return MySQL::query('UPDATE message SET message = \''.addslashes($message).'\' WHERE id = '. $commentId);
  }

  static function submitBugComment($bugId, $message) {
    if (!is_numeric($bugId)) {
      return null;
    }
    return MySQL::query('INSERT INTO message (bug_id, user_id, submitdate, message) VALUES ('. $bugId .', '. getCurrentUserId() .', '. time() .', \''. $message .'\')');
  }

  static function getProjectID($name) {
    return MySQL::query('SELECT id FROM project WHERE name = \'' . addslashes($name) . '\'');
  }

  static function insertProject($name, $visible) {
    return MySQL::query('INSERT INTO project (name, projectstatus_id) VALUES (\'' . addslashes($name) . '\', ' . addslashes($visible) . ')');
  }

  static function insertVersion($name, $version) {
    $id = Database::getProjectID($name);

    foreach ($id as $row) {
      $id = $row['id'];
    }

    return MySQL::query('INSERT INTO projectversion (project_id, version) VALUES (' . $id[0] . ', \'' . addslashes($version) . '\')');
  }

  static function getProjects() {
    return MySQL::query('SELECT p.id pid, p.name pname, v.id vid, v.version vversion, p.projectstatus_id psid, s.id sid, s.name sname FROM project p, projectversion v, projectstatus s WHERE p.projectstatus_id = s.id AND v.project_id = p.id');
  }

  static function getCountWithProjectID($id) {
    return MySQL::query("SELECT b.id bugs FROM project p, bugs b, projectversion v WHERE b.productversion_id = v.id AND v.project_id = p.id AND p.id = $id");
  }

  static function getProjectList() {
    return MySQL::query('SELECT id, name, projectstatus_id FROM project ORDER BY name');
  }

  static function getNormalUserList() {
    return MySQL::query('SELECT * FROM users WHERE group_id = 1');
  }

  static function hasUserAccess2Project($project_id, $user_id) {
    $query = MySQL::query("SELECT * FROM projectusers WHERE project_id = $project_id AND user_id = $user_id");

    if (count($query) == 0) {
      return false;
    } else {
      return true;
    }
  }

  static function insertVersionWithID($name, $project_id) {
    return MySQL::query("INSERT INTO projectversion (project_id, version) VALUES ($project_id, '" . addslashes($name) . "')");
  }

  static function getProjectUser($project_id, $user_id) {
    return MySQL::query("SELECT project_id, user_id FROM projectusers WHERE project_id = $project_id AND user_id = $user_id");
  }

  static function setProjectUser($project_id, $user_id) {
    return MySQL::query("INSERT INTO projectusers (project_id, user_id) VALUES ($project_id, $user_id)");
  }

  static function deleteProjectUser($project_id, $user_id) {
    return MySQL::query("DELETE FROM projectusers WHERE project_id = $project_id AND user_id = $user_id");
  }

  static function projectWithID($id) {
    return MySQL::query("SELECT id FROM project WHERE id = $id");
  }

  static function getProject($id) {
    return MySQL::query("SELECT * FROM project WHERE id = $id");
  }

  static function updateProject($id, $name, $hidden) {
    return MySQL::query('UPDATE project SET name = \'' . addslashes($name) . '\', projectstatus_id = ' . $hidden . ' WHERE id = ' . $id);
  }

  static function deleteProjectVersions($id) {
    return MySQL::query("DELETE FROM projectversion WHERE project_id = $id");
  }

  static function deleteProject($id) {
    return MySQL::query("DELETE FROM project WHERE id = $id");
  }

  static function deleteBugComment($bugId) {
    if (!is_numeric($bugId)) {
      return null;
    }

    return MySQL::query('DELETE FROM message WHERE id = '. $bugId);
  }

  static function getPermissionWithClause($standard, $id) {
    if (!is_numeric($id)) {
      return null;
    }

    if($standard == true) {
      $clause = '';
    } else {
      $clause = ' AND p.id = '.$id;
    }

    return MySQL::query('SELECT p.id, level_id, g.name AS groupName, setting, value, description FROM permissions p, usergroups g WHERE p.level_id = g.id '. $clause .' ORDER BY setting ASC');
  }

  static function submitPermissions($groupId, $setting, $value, $description) {
    if (!is_numeric($groupId)) {
      return null;
    }

    return MySQL::query('INSERT INTO permissions (level_id, setting, value, description) VALUES ('. $groupId .', \''. addslashes($setting) .'\', \''. addslashes($value) . '\', \''. addslashes($description) .'\')');
  }

  static function updatePermission($id, $groupId, $setting, $value, $description) {
    if (!is_numeric($id) || !is_numeric($groupId)) {
      return null;
    }

    return MySQL::query('UPDATE permissions SET level_id = '.$groupId.', setting = \''.addslashes($setting).'\', value = \''.addslashes($value).'\', description = \''.addslashes($description).'\' WHERE id = '.$id );
  }

  static function delPermission($id) {
    if (!is_numeric($id)) {
      return null;
    }

    return MySQL::query('DELETE FROM permissions WHERE id = '.$id);
  }



  static function getbugstatus() {
    return MySQL::query('SELECT id, name FROM bugstatus');
  }

  static function delbugstatus($bugstatus) {
    return MySQL::query('DELETE FROM bugstatus WHERE id =' . $bugstatus);
  }

  static function insbugstatus($bugstatus) {
    return MySQL::query('INSERT INTO bugstatus (name) '.
              'VALUES (\'' . $bugstatus . '\')');
  }

  static function getbugstatuswithid($id) {
    return MySQL::query('SELECT name FROM bugstatus WHERE id = ' . $id);
  }

  static function countbugstatus($id) {
    return MySQL::query('SELECT id FROM bugs WHERE status_id = ' . $id);
  }

  // Projectstatus.php
  static function getprojectstatus() {
    return MySQL::query('SELECT id, name FROM projectstatus');
  }

  static function delprojectstatus($projectstatus) {
    return MySQL::query('DELETE FROM projectstatus WHERE id =' . $projectstatus);
  }

  static function insprojectstatus($projectstatus) {
    return MySQL::query('INSERT INTO projectstatus (name) '.
              'VALUES (\'' . $projectstatus . '\')');
  }

  static function getprojectstatuswithid($id) {
    return MySQL::query('SELECT name FROM projectstatus WHERE id = ' . $id);
  }

  static function countprojectstatus($id) {
    return MySQL::query('SELECT id FROM project WHERE projectstatus_id = ' . $id);
  }

  // Bugcategory.php
  static function getBugCategory() {
    return MySQL::query('SELECT id, category FROM bugcategory ORDER BY category');
  }

  static function delBugCategory($bugCategory) {
    if(!is_numeric($bugCategory)) {
      return null;
    }

    MySQL::query('DELETE FROM bugs WHERE category1_id = '.$bugCategory.' OR category2_id = '.$bugCategory);

    return MySQL::query('DELETE FROM bugcategory WHERE id =' . $bugCategory);
  }

  static function insBugCategory($bugCategory) {
    return MySQL::query('INSERT INTO bugcategory (category) '.
              'VALUES (\'' . $bugCategory . '\')');
  }

  static function getBugCategoryWithId($id) {
    if(!is_numeric($id)) {
      return null;
    }

    return MySQL::query('SELECT category FROM bugcategory WHERE id = '.$id);
  }

  static function countBugCategory($bugCategory) {
    if(!is_numeric($bugCategory)) {
      return null;
    }

    return MySQL::query('SELECT id FROM bugs WHERE category1_id = '.$bugCategory.' OR category2_id = '.$bugCategory);
  }


  static function getBbTags($withId, $id) {
    $clause = '';
    if($withId == true) {
      if(!is_numeric($id)) {
        return null;
      } else {
        $clause = 'WHERE id = '. $id;
      }
    }

    return MySQL::query('SELECT id, bbcode, htmlcode FROM bbtags '.$clause);
  }

  static function delBbTag($id) {
    if(!is_numeric($id)) {
      return null;
    }

    return MySQL::query('DELETE FROM bbtags WHERE id = '. $id);
  }

  static function submitBbTag($bbTag, $htmlTag) {
    return MySQL::query('INSERT INTO bbtags (bbcode, htmlcode) VALUES (\''.addslashes($bbTag).'\', \''.addslashes($htmlTag).'\')');
  }

  static function updateBbTag($id, $bbTag, $htmlTag) {
    if(!is_numeric($id)) {
      return null;
    }

    return MySQL::query('UPDATE bbtags SET bbcode = \''.addslashes($bbTag).'\', htmlcode = \''.addslashes($htmlTag).'\' WHERE id = '. $id);
  }

  // Bugpriority.php
  static function getbugpriorities() {
    return MySQL::query('SELECT id, name FROM bugpriority');
  }

  static function remBugPriority($bugpriority) {
    return MySQL::query('DELETE FROM bugpriority WHERE id =' . $bugpriority);
  }

  static function insBugPriority($bugpriority) {
    return MySQL::query('INSERT INTO bugpriority (name) '.
              'VALUES (\'' . $bugpriority . '\')');
  }

  static function getBugPriorityWithID($id) {
    return MySQL::query('SELECT name FROM bugpriority WHERE id = ' . $id);
  }

  static function countBugPriority($id) {
    return MySQL::query('SELECT id FROM bugs WHERE priority_id = ' . $id);
  }

}

