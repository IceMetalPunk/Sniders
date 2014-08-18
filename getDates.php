<?php
	$link=mysql_connect("localhost", "root", "");
  $db=mysql_select_db("sniders2013", $link);
	
	/* Get current cycle end date */
	$q="SELECT `l-DESC` FROM `t-lookup` WHERE `l-VALUE`=301";
	$query=mysql_query($q);
	
	$cycleDate=mysql_fetch_assoc($query);
	$cycleDate=strtotime($cycleDate["l-DESC"]);
	$nextCycle=strtotime("next Sunday");
	echo '{"cycleDate": "'.date("n/j/Y", $cycleDate).'", "nextCycle": "'.date("n/j/Y", $nextCycle).'"}';
	
	mysql_close($link);
?>