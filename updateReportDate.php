<?php
	$link=mysql_connect("localhost", "root", "tux898");
  $db=mysql_select_db("sniders2013", $link);
	
	$q="UPDATE `t-customer` SET `C-OPEN-BALANCE`=`C-BALANCE`";
	$query=mysql_query($q);
	
	$q="UPDATE `t-lookup` SET `l-DESC`=NOW() WHERE `l-VALUE`=996";
	$query=mysql_query($q);
	echo '{"status": '.mysql_affected_rows().'}';
	mysql_close($link);
?>