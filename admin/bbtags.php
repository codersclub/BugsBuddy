<?php

/*
  View and edit bbTags
*/

function getbbtags() {
  $returnValue = '';
  $returnValue .= '<h1>' .lang('bbtags'). '</h1>';
  
  if(isLoggedIn()) {
    $results     = Database::getPermissions(intval(getCurrentGroupId()));
    $permissions   = Array();
  
    foreach($results as $result) {
      $permissions[$result['setting']] = $result['value'];
    }
    
    if (isset($permissions['mayview_admin_bbtags']) && $permissions['mayview_admin_bbtags'] == 'true') {    
      if (isset($_GET) && (isset($_GET['delete']) && $_GET['delete'] == 'true') || (isset($_GET['submitit']) && $_GET['submitit'] == 'true')) {
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

function getBbForm($recoverData) {
  $returnValue = '';
  
  $id      = 0;
  $bbTag     = '';
  $htmlTag   = '';

  if ($recoverData && isset($_GET) && isset($_GET['bbtag']) && isset($_GET['htmltag'])) {
    $bbTag    = $_GET['bbtag'];
    $htmlTag   = $_GET['htmltag'];  
  }
  
  if(isset($_GET) && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $result = Database::getBbTags(true, $id);
  
    if(!empty($result)) {
      foreach ($result as $row) {
        $bbTag   = $row['bbcode'];
        $htmlTag = $row['htmlcode'];
      }
    }
  }    
    
  $thisPage   = 'bbtags';
  $currentUrl = getCurrentRequestUrl();
  $currentUrl = explode('?', $currentUrl);
  $currentUrl = $currentUrl[0];
  
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
                           <a href="index.php?'.(isset($_GET['js'])?'js=yes':'').'&page=bbtags&id='.$row['id'].'&edit=true">' . lang('edit') . '</a>
                         </td>
                         <td>
                           <a href="index.php?'.(isset($_GET['js'])?'js=yes':'').'&page=bbtags&id='.$row['id'].'&delete=true">' . lang('delete') . '</a>
                         </td>
                         <td>'.$row['bbcode'].'</td>
                         <td>'.$row['htmlcode'].'</td>
                       </tr>';
              
      $i++;
    }
    
    $returnValue .=   '<tr>
                    <td>&nbsp;</td>
                  </tr>
                </table>';
  }
    
  //TODO:
  //  registerlabel: replaced by something more general, or a new
  //  registerinput: replaced by something more general, or a new
  $returnValue .= '<table>
            <tr>
              <td>
                <form action="'.$currentUrl.'" method="get">
                  <input type="hidden" name="page" value="'.$thisPage.'"/>
                  <input type="hidden" name="submitit" value="true"/>';

  if (isset($_GET['js'])) {
    $returnValue .= '<input type="hidden" name="js" value="yes"/>';
  }      

  if(!empty($id)) {
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
                 </form>
               </td>
             </tr>
           </table>';
  
  return $returnValue;
}

function handleBbForm() {
  $returnValue = '';
  
  $id      = 0;
  $bbTag   = '';
  $htmlTag = '';
  
  if(isset($_GET) && isset($_GET['id'])) {
    $id = $_GET['id'];
  }  

  if(isset($_GET) && isset($_GET['delete']) && $_GET['delete'] == 'true') {    
    if(!is_numeric($id)) {
      $error = true;
    }    
    
    Database::delBbTag($id);  
  } else {
    $errorMessage   = '';
    $error       = false;  

    if (isset($_GET) && isset($_GET['bbtag']) && isset($_GET['htmltag'])) {
      $bbTag    = $_GET['bbtag'];
      $htmlTag  = $_GET['htmltag'];
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



