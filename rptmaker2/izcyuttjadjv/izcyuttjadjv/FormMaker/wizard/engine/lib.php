<?php
/**
    *   Smart Form Maker 
    *   V 1.0.0
    *   All copyrights are preserved to StarSoft
    */
  error_reporting(E_ERROR  | E_PARSE);
  function decode($encoded)
  {
      return unserialize(base64_decode($encoded));
  }
 function query($query)
{
    global $host, $user, $pass, $db;



    if(!@mysql_connect($host, $user, $pass))
    {
        die("<center><B>Couldn't connect to MySQL</B></center>");
        return false;
    }
    if(!@mysql_select_db($db))
    {
        die("<center><B>Couldn't select database</B></center>");
        return false;
    }
    mysql_query("set character_set_server='utf8'");
    mysql_query("set names 'utf8'");
    if(function_exists('mysql_set_charset'))
         mysql_set_charset('utf8'); 
    $result = @mysql_query($query);
    return $result;
}
?>
