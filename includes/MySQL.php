<?php

/*
  This class is able to connect to a MySQL database and query it.
  All results must be parsed to return an Array! This class is a singleton!
  No file but 'Database.php' should have an 'require' to this file
*/

$MySQLInstance = null;

class MySQL {

  var $databaseConnection = null;
  var $connected = false;
  var $test = "test";

  /*
   * The constructor is private and should not be called outside this script!
   */
  function MySQL()  {
    global $MySQLInstance;
    if ($MySQLInstance != null) {
      return $MySQLInstance;
    }
    $this->databaseConnection = @mysql_connect(DATABASE_SERVER, DATABASE_USER_NAME, DATABASE_USER_PASSWORD);
    if ($this->databaseConnection === false) {
      $this->connected = false;
      return;
    }
    if(defined('DATABASE_DATABASENAME')) {
    if (!mysql_select_db(DATABASE_DATABASENAME, $this->databaseConnection)) {
      @mysql_close($this->databaseConnection);
      $this->connected = false;
      return;
    }
    }
    $this->connected = true;
  }
  
  /*
   * Returns the only instance of the MySQL object. If the object does not yet exists, a new one will
   * be created
   */
  function select_db($database) {
//DEBUG
//echo '<pre>';
//echo 'select_db started. $database=', $database, "\n";
//var_dump(debug_backtrace());
//echo '</pre>';
//exit;
    $mySQLInstance = MySQL::instance();
    if ($mySQLInstance==null) {
      return null;
    }
    if (!mysql_select_db($database, $mySQLInstance->databaseConnection)) {
      @mysql_close($mySQLInstance->databaseConnection);
      $mySQLInstance->connected = false;
      return;
    }
  }
  
  /*
   * Returns the only instance of the MySQL object. If the object does not yet exists, a new one will
   * be created
   */
  function instance() {
    global $MySQLInstance;
    if ($MySQLInstance == null) {
      $MySQLInstance = new MySQL();
      if ($MySQLInstance->connected) {
        return $MySQLInstance;
      }
      $MySQLInstance = null;
    }
    return $MySQLInstance;
  }
  
  /*
   * Executes a query and returns an Array with the result. If there was an error while executing the
   * query, the value 'null' is returned
   */
  function query($queryString) {
//DEBUG
//echo '<pre>';
//echo 'query=', htmlspecialchars($queryString), "\n";
//echo '</pre>';

    $mySQLInstance = MySQL::instance();
    if ($mySQLInstance==null) {
      return null;
    }
    $result = @mysql_query($queryString, $mySQLInstance->databaseConnection) or die(mysql_error());
    if ($result === false) {
      return null;
    } else {
      $returnValue = Array();
      while ($row = @mysql_fetch_array($result, MYSQL_ASSOC)) {
        array_push($returnValue, $row);
      }
      @mysql_free_result($result);
      return $returnValue;
    }
  }
  
  /*
   * Static function that closes the mySQL connection if is is still open
   */
  function cleanUp() {
    global $MySQLInstance;
    if ($MySQLInstance != null) {
      @mysql_close($MySQLInstance->databaseConnection);
      $MySQLInstance->databaseConnection = null;
      $MySQLInstance->connected = false;
      
    }
  }

}


