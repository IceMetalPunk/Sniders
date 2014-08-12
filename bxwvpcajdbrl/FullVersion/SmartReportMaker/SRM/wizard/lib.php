<?php
  error_reporting(E_ERROR  | E_PARSE);
   $host = $_SESSION["host"];
    $user = $_SESSION["user"];
    $pass = $_SESSION["pass"];
    $db = $_SESSION["db"];

   function sql($query)
{
    global $host, $user, $pass, $db;



    if(!@mysql_connect($host, $user, $pass))
    {
        echo("<center><B>Couldn't connect to MySQL </B></center>");
        return false;
    }
    if(!@mysql_select_db($db))
    {
        echo("<center><B>Couldn't select databasehost </B></center>");
        return false;
    }
    if(!$result = @mysql_query($query))
    {
        echo("<center><B>Error in query: Error# " . mysql_errno() . ": " . mysql_error()."</B></center>");
        return false;
    }
    return $result;
}





 function query($query)
{
    global $host, $user, $pass, $db;



    if(!@mysql_connect($host, $user, $pass))
    {
        echo("<center><B>Couldn't connect to MySQL</B></center>");
        return false;
    }
    if(!@mysql_select_db($db))
    {
        echo("<center><B>Couldn't select database</B></center>");
        return false;
    }
    if(!$result = @mysql_query($query))
    {
        echo("<center><B>Error in query: Error# " . mysql_errno() . ": " . mysql_error()."</B></center>");
        return false;
    }
    return $result;
}






?>
