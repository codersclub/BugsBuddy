<?php

//require_once('includes/helperfunctions.php');

class Mail {

  var $htmlMessage;
  var $subject;

  function Mail($subject, $htmlMessage) {
    $this->subject = $subject;
    $this->htmlMessage = $htmlMessage;
  }
  
  function send($to, $cc=null, $bcc=null) {
    $mailfrom = htmlUnsafe(getConfigurationValue('mailfrom'));
    if ($mailfrom == null) {
      return false;
    }
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= 'To: <'.$to.'>' . "\r\n";
    $headers .= 'From: ' . $mailfrom . "\r\n";
    return @mail('', $this->subject, $this->htmlMessage, $headers);
  }

}

?> 