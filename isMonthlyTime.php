<?php
	$link=mysql_connect("localhost", "root", "tux898");
	$db=mysql_select_db("sniders2013", $link);
	
	$q="SELECT `l-DESC` FROM `t-lookup` WHERE `l-VALUE`=996";
	$query=mysql_query($q);
	$dateRow=mysql_fetch_assoc($query);
	//echo mysql_error();
	$lastDate=$dateRow["l-DESC"];
	$lastDatePHP=strtotime($lastDate);
	
	/* Get the last Sunday of the month */
	$nextMonth=((date("n", $lastDatePHP)+1)%12)+1;
	$whichYear=(date("n", $lastDatePHP)>=11?date("Y", $lastDatePHP)+1:date("Y", $lastDatePHP));
	$nextMonth=mktime(0, 0, 0, $nextMonth, 1, $whichYear);
	$lastSunday=strtotime("previous Sunday", $nextMonth);
	$lastSunday=date("Y-m-d", $lastSunday);

	$today=date("Y-m-d");

	if ($today>=$lastSunday && $lastDate<$today) {
		echo '{"result": true}';
	}
	else {
		echo '{"result": false}';
	}
	
	mysql_close($link);
?>