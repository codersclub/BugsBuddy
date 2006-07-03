<?php
/*
 * Author:      Daan Keuper
 * Date:        22 May 2006
 * Version:     1.0
 * Description: display an error message
 */
 
function geterrorpage() {
  if (isset($_GET['message'])) {
    switch ($_GET['message']) {
      case 'database':
        return '<p>Sorry, er is iets mis met de database, probeer het later nog eens</p>';
        break;
      default:
        return '<p>Deze pagina is op dit moment niet beschikbaar</p>';
        break;
    }
  } else {
    return '<p>Deze pagina is op dit moment niet beschikbaar</p>';
  }
}
?>
