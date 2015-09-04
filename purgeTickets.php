<?php
	$ticketDir="./tickets/Complete";
	$files=scandir($ticketDir);
	$oldTickets=array();
	foreach ($files as $ind=>$filename) {
		if ($filename=="." || $filename=="..") { continue; }
		$ftime=filectime($ticketDir."/".$filename);
		if ($ftime<mktime(0, 0, 0, 8, 1, 2015)) {
			echo $filename." is older than 8/1/2015.<br />";
			$oldTickets[]=$filename;
		}
		else {
			echo $filename." is newer than 8/1/2015.<br />";
		}
	}
	print_r($oldTickets);

?>