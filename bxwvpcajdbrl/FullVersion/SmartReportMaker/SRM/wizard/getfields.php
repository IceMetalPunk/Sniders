<?php
session_start();
include ("lib.php");
$mydb = $_SESSION["db"];
$result = sql("show tables from `$mydb`");
$tables = array();
 while ($arr = mysql_fetch_array($result))
{
  $tables[] = $arr[0];
}

$table = $_GET["table"];
if(in_array($table,$tables))
{
	
	$result = sql("show columns from `$table`");
	$fields = array();
	while ($f = mysql_fetch_array($result))
	{
		$fields[] = $f[0];
	}
	$json = json_encode($fields);
	echo $json;
}



?>