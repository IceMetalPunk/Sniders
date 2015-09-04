<?php
	$ticketDir="./tickets/Complete";
	$purgeDir="./purged/Tickets";
	if (!file_exists("./purged")) { mkdir("./purged"); }
	if (!file_exists("./purges/Tickets")) { mkdir("./purged/Tickets"); }
	
	$files=scandir($ticketDir);
	$oldTickets=array();
	foreach ($files as $ind=>$filename) {
		if ($filename=="." || $filename=="..") { continue; }
		$ftime=filectime($ticketDir."/".$filename);
		if ($ftime<mktime(0, 0, 0, 8, 1, 2015)) {
			//echo $filename." is older than 8/1/2015.<br />";
			rename($ticketDir."/".$filename, $purgeDir."/".$filename);
		}
	}

?>