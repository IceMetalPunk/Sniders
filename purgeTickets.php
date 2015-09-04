<?php
	$ticketDir="./tickets/Complete";
	$files=scandir($ticketDir);
	$oldTickets=array();
	foreach ($files as $ind=>$filename) {
		if ($filename=="." || $filename="..") { continue; }
		$ftime=filectime($ticketDir."/".$filename);
		if ($ftime<mktime(0, 0, 0, 8, 1, 2015)) {
			$oldTickets[]=$filename;
		}
	}
	print_r($oldTickets);
?>