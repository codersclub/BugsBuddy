<?php

/*
  View and edit bbTags
*/

function getbbtags() {

  $action = trim(@$_GET['action']);

  $returnValue = '<h1>' .lang('bbtags'). '</h1>';

  if(isLoggedIn()) {
    $results     = Database::getPermissions(intval(getCurrentGroupId()));
    $permissions = Array();

    foreach($results as $result) {
      $permissions[$result['setting']] = $result['value'];
    }

//    if (isset($permissions['mayview_admin_bbtags']) && $permissions['mayview_admin_bbtags'] == 'true') {
    if (@$permissions['mayview_admin_bbtags'] == 'true') {
      if ($action=='delete' || /*$action=='edit' ||*/ $_SERVER['REQUEST_METHOD']=='POST') {
        $returnValue .= handleBbForm();
      } else {
        $returnValue .= getBbForm(true);
      }
    } else {
      $returnValue = lang('permission_not_enough');
    }
  } else {
    $returnValue = lang('login_required_for_this');
  }

  return $returnValue;
}

function getBbForm($recoverData=false) {

  $id     = intval($_GET['id']);
  $action = trim(@$_GET['action']);

  $bbTag   = '';
  $htmlTag = '';
  $returnValue = '';

  if($recoverData) {
//    $id      = intval($_POST['id']);
    $bbTag   = isset($_POST['bbtag']) ? $_POST['bbtag'] : '';
    $htmlTag = isset($_POST['htmltag']) ? $_POST['htmltag'] : '';
  }

  if(isset($_GET['id'])) {
    $result  = Database::getBbTags(true, $id);

    foreach ($result as $row) {
      $bbTag   = $row['bbcode'];
      $htmlTag = $row['htmlcode'];
    }
  }

  $result = Database::getBbTags(false, 0);

  if(!empty($result)) {
    $returnValue .= '<table style="width: 100%;">
              <tr>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>'.lang('bbcode').'</th>
                <th>'.lang('htmlcode').'</th>
              </tr>';

    $i = 1;

    foreach ($result as $row) {
      if ($i % 2 == 0) {
        $returnValue .= '<tr class="gray">';
      } else {
        $returnValue .= '<tr>';
      }

      $returnValue .=   '<td>
                           '.pageLink('bbtags&id='.$row['id'].'&action=edit', lang('edit')).'
                         </td>
                         <td>
                           '.pageLink('bbtags&id='.$row['id'].'&action=delete', lang('delete')).'
                         </td>
                         <td>'.htmlspecialchars($row['bbcode']).'</td>
                         <td>'.htmlspecialchars($row['htmlcode']).'</td>
                       </tr>';

      $i++;
    }

    $returnValue .=   '<tr>
                    <td>&nbsp;</td>
                  </tr>
                </table>';
  }

  $thisPage   = 'bbtags';
  $currentUrl = getCurrentRequestUrl();
  $currentUrl = explode('&amp;id=', $currentUrl);
  $currentUrl = $currentUrl[0];

  //TODO:
  //  registerlabel: replaced by something more general, or a new
  //  registerinput: replaced by something more general, or a new
  $returnValue .= '
                <form action="'.$currentUrl.'" method="POST">
                  <input type="hidden" name="page" value="'.$thisPage.'"/>';

  if($id) {
    $returnValue .= '<input type="hidden" name="id" value="'.$id.'"/>';
  }

  $returnValue .= '<div class="registerinput">
                     <label for="bbtag" class="registerlabel">'.lang('bbcode').':</label>
                     <input type="text" class="" id="bbtag" name="bbtag" value="'.$bbTag.'"/>
                   </div>
                   <div class="registerinput">
                     <label for="htmltag" class="registerlabel">'.lang('htmlcode').':</label>
                     <input type="text" class="" id="htmltag" name="htmltag" value="'.$htmlTag.'"/>
                   </div>
                   <div class="registerinput">
                     <label for="verzenden" class="registerlabel">'. lang('send') .':</label>
                     <input class="" id="verzenden" name="verzenden" type="submit" value="'. lang('send'). '!"/>
                   </div>
                 </form>';

  return $returnValue;
}

function handleBbForm() {

  $id      = intval(@$_GET['id']);
  $action  = trim(@$_GET['action']);

  $returnValue  = '';
  $errorMessage = '';
  $error        = false;

  if($action=='delete') {

    if(!is_numeric($_GET['id'])) {
      $error = true;
    }

    Database::delBbTag($id);

  } else { // Edit

    if($_SERVER['REQUEST_METHOD']=='POST') {
      $id      = intval($_POST['id']);
      $bbTag   = isset($_POST['bbtag']) ? trim($_POST['bbtag']) : '';
      $htmlTag = isset($_POST['htmltag']) ? trim($_POST['htmltag']) : '';
    }

    if(!is_numeric($id) || empty($bbTag) || empty($htmlTag)) {
      $error = true;
    }

    if ($error) {
      return getBbForm(true) . nl2br($errorMessage);
    }

    if(empty($id)) {
      Database::submitBbTag($bbTag, $htmlTag);
    } else {
      Database::updateBbTag($id, $bbTag, $htmlTag);
    }
  }

  return getBbForm(false);
}
