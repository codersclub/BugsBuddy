<?
/*
 * Make every 'Post', 'Get' and 'Cookie' value html safe.
 * Be warned: $_SESSION does not becomes htmlsafe
 */
if (get_magic_quotes_gpc()) {
  $_POST = stripTheSlashes($_POST);
  $_GET = stripTheSlashes($_GET);
  $_COOKIE = stripTheSlashes($_COOKIE);
}
$_POST = htmlSafe($_POST);
$_GET = htmlSafe($_GET);
$_COOKIE = htmlSafe($_COOKIE);

/*
 * Converts every charater in the string to a html-safe representation
 * except for the characters: a-z, A-Z and 0-9
 * If it is an array, loop throut the array and make every key and value htmlsafe.
 */
function htmlSafe($string) {
  if (is_array($string)) {
    $returnValue = array();
    foreach($string as $key => $value) {
      $returnValue[htmlSafe($key)] = htmlSafe($value);
    }
    return $returnValue;
  } else {
    $chars = array();
    $chars = preg_split("//", $string, -1, PREG_SPLIT_NO_EMPTY);
    $returnArray = array();
    for ($i=0; $i<count($chars); $i++) {
      $theDecimalCharacterValue = ord($chars[$i]);
      if (($theDecimalCharacterValue >= 97 && $theDecimalCharacterValue <= 122) ||
          ($theDecimalCharacterValue >= 65 && $theDecimalCharacterValue <= 90) ||
          ($theDecimalCharacterValue >= 48 && $theDecimalCharacterValue <= 57)) {
        $returnArray[$i] = $chars[$i];
      } else {
        $returnArray[$i] = "&#" . $theDecimalCharacterValue . ";";
      }
    }
    return implode("", $returnArray);
  }
}

function htmlUnsafe($string) {
  if (is_array($string)) {
    $returnValue = array();
    foreach($string as $key => $value) {
      $returnValue[htmlUnsafe($key)] = htmlUnsafe($value);
    }
    return $returnValue;
  } else {
    $result = 0;
    $result = strpos($string, '&', $result);
    while($result !== false) {
      if ($string{$result+1} != '#') {
        showErrorPage("An error occured while making an htmlsafe string back unsafe.", __FILE__, __LINE__, "The '&amp;' is not followed by the '#' character at position " . $result . " in string: '".htmlSafe($string)."'");
      }
      $nextSemicolonPosition = strpos($string, ';', $result+2);
      if ($nextSemicolonPosition === false) {
        showErrorPage("An error occured while making an htmlsafe string back unsafe.", __FILE__, __LINE__, "After the '&amp;#' characters there was never found an ';' in string: '".htmlSafe($string)."' at position: " . $result);
      }
      if (!is_numeric(substr($string, $result+2, $nextSemicolonPosition-($result+2)))) {
        showErrorPage("An error occured while making an htmlsafe string back unsafe.", __FILE__, __LINE__, "The text between '&amp;#' and ';' is not numeric. It occures in string '".htmlSafe($string)."' at position: " . $result);
      }
      $representingCharacter = chr(intval(substr($string, $result+2, $nextSemicolonPosition-($result+2))));
      $string = str_replace(substr($string, $result, ($nextSemicolonPosition-$result)+1), $representingCharacter, $string);
      $result = strpos($string, '&', $result+1);
    }
    return $string;
  }
}

function safenl2br($string) {
  if (is_array($string)) {
    $returnValue = array();
    foreach($string as $key => $value) {
      $returnValue[stripTheSlashes($key)] = stripTheSlashes($value);
    }
    return $returnValue;
  } else {
    return str_replace("&#10;", "<br>", $string);
  }
}

/*
 * Strips slashes from a string
 * example: "hello I\'m Alex" will become "hello I'm Alex"
 * and "test\\this" becomes "test\this"
 */
function stripTheSlashes($string) {
  if (is_array($string)) {
    $returnValue = array();
    foreach($string as $key => $value) {
      $returnValue[stripTheSlashes($key)] = stripTheSlashes($value);
    }
    return $returnValue;
  } else {
    return stripslashes($string);
  }
}

