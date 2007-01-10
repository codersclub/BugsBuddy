<?php
session_start();

//--------------------------------------------------------
// Absolute path from DISK ROOT (With ending slash !)
$dir = (dirname(dirname(__FILE__)));

define('ROOT_DIR', $dir);


require_once(ROOT_DIR.'/includes/helperfunctions.php');

//DEBUG
//echo '<pre>';
//print_r($lang);
//echo '</pre>';
?>
  function updateContent(newContent) {
    document.getElementById('content').innerHTML = newContent;
  }
  function getNewContent(content) {
    var newScript = document.createElement('script');
    newScript.src = "script.php?page="+content;
    document.getElementById('content').appendChild(newScript);
  }
  function updateLogin(content) {
    document.getElementById('login').innerHTML = content;
  }
  function updateLinks(content) {
    document.getElementById('balk').innerHTML = content;
  }
  function updateGoBackLink(page, id) {
    oldpage = currentPage;
    oldid = currentId;
    if (page == null || page == 'null') {
      oldpage = 'home';
    }
    if (currentId != null && currentId != 'null') {
      document.getElementById('goback').innerHTML = '<a href="javascript:getNewContent(oldpage+\'&id=\'+oldid)"><?=lang('go_back')?></a>';
    } else {
      document.getElementById('goback').innerHTML = '<a href="javascript:getNewContent(oldpage)"><?=lang('go_back')?></a>';
    }
    currentPage = page;
    currentId= id;
  }
  function formClear(id, standard) {
    if (document.getElementById(id).value == standard) {
      document.getElementById(id).value = '';
    }
  }
  function loginChecker() {
    // TODO check if email/pass are valid enough to submit, return false if wrong
    document.getElementById('email').value = document.getElementById('input_email').value;
    document.getElementById('pass').value = document.getElementById('input_pass').value;
    document.getElementById('input_email').value = "";
    document.getElementById('input_pass').value = "";
    return true;
  }
  function javascriptSubmit(page, submitit) {
    var string = "";
    var elements1 = document.getElementsByTagName("input");
    var elements2 = document.getElementsByTagName("select");
    var elements3 = document.getElementsByTagName("textarea");
    
    for (var i=0; i<elements1.length; i++) {
      if (elements1[i].className != "login" && elements1[i].type != "submit" && elements1[i].name != "submitit") {
        string += elements1[i].name+"="+elements1[i].value+"&";
      }
       
      if (elements1[i].name == "submitit" && submitit == false) {
        string += elements1[i].name+"=false&";
      }
      
      if (elements1[i].name == "submitit" && submitit == true) {
        string += elements1[i].name+"=true&";
      }
    }
     
    for (var i=0; i<elements2.length; i++) {
      if (elements2[i].className != "login") {
        string += elements2[i].name+"="+elements2[i].value+"&";
      }
    }
    
    for (var i=0; i<elements3.length; i++) {          
      s = new String(elements3[i].value);
      s = s.replace(/\n/g, "%0d%0a");
      string += elements3[i].name+"="+s+"&";
    }          
    
    getNewContent(page + "&"+string);
  }
  
  function checkPassWordStrength(object) {
    var pass   = document.getElementById(object).value;
    var strength = "<?=lang('password_very_very_bad')?>";
    var cUpper    = false;
    var cLower   = false;
    var cNumeric = false;
    var sPoints   = 0;
//    var maxWidth = parseInt(document.getElementById('pwdStrength').style.width);
    var maxWidth = 100;
    
    for (var i = 0; i < pass.length; i++) {
      if(pass.charCodeAt(i) >= 48 && pass.charCodeAt(i) <= 57) {
        cNumeric = true;
      }   
      
      if(pass.charCodeAt(i) >= 65 && pass.charCodeAt(i) <= 90) {
        cUpper = true;
      }  
      
      if(pass.charCodeAt(i) >= 97 && pass.charCodeAt(i) <= 122) {
        cLower = true;
      }
    }
     
    //Check numeric en cases points
    if(cNumeric == true && cUpper == false && cLower == false) {      //Only numeric
      sPoints += 0;
    } else if(cNumeric == false && (cUpper == true || cLower == true)) {  //Only character
      sPoints += 1;
    } else if(cNumeric == true && cUpper == true && cLower == false) {    //Numeric and Upper
      sPoints += 2;
    } else if(cNumeric == true && cUpper == false && cLower == true) {    //Numeric and Lower
      sPoints += 2;
    } else if(cNumeric == true && cUpper == true && cLower == true) {    //Numeric upper and Lower
      sPoints += 3;
    }
    
    //Check password length points
    if(pass.length <= 5) {
      sPoints += 0;
    }
    
    if(pass.length >= 6 && pass.length < 8) {
      sPoints += 1;
    }
    
    if(pass.length >= 8 && pass.length < 10) {
      sPoints += 2;
    }
    
    if(pass.length >= 10) {
      sPoints += 3;
    }  
    
    var command = "document.getElementById('pwdBeamGreen').style.width = '"+ parseInt((maxWidth / 6) * sPoints) +"px';";
    eval(command);      
    
    switch(sPoints) {
      case 0:
        strength = '<?=lang('password_very_bad')?>';
        break;
      case 1:
        strength = '<?=lang('password_very_bad')?>';
        break;
      case 2:
        strength = '<?=lang('password_bad')?>';
        break;
      case 3:
        strength = '<?=lang('password_discouraged')?>';
        break;
      case 4:
        strength = '<?=lang('password_good_enough')?>';
        break;
      case 5:
        strength = '<?=lang('password_good')?>';
        break;
      case 6:
        strength = '<?=lang('password_strong')?>';
        break;
    }
    
    document.getElementById('pwdText').innerHTML = strength;
  }
