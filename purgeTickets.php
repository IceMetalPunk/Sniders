<?php
	if (!empty($_POST['confirmed']) && $_POST['confirmed']="yes" && !empty($_POST['use_date'])) {
		$ticketDir="./tickets/Complete";
		$purgeDir="./purged/Tickets";
		if (!file_exists("./purged")) { mkdir("./purged"); }
		if (!file_exists("./purged/Tickets")) { mkdir("./purged/Tickets"); }
		
		$files=scandir($ticketDir);
		$oldTickets=array();
		foreach ($files as $ind=>$filename) {
			if ($filename=="." || $filename=="..") { continue; }
			$ftime=filectime($ticketDir."/".$filename);
			if ($ftime<strtotime($_POST['use_date'])) {
				//echo $filename." is older than 8/1/2015.<br />";
				rename($ticketDir."/".$filename, $purgeDir."/".$filename);
			}
		}
	}
?>
<html>
  <head>
    <title>Purge Tickets</title>

    <!-- Main styles -->
    <link rel="stylesheet" href="styles.css" />
		
		<!-- jQuery styles -->
		<link rel="stylesheet" href="jquery-ui.css" />
    <link rel="stylesheet" href="jquery-style.css" />
		
    <!-- jQuery library and its Autocomplete extension -->
    <script type="text/javascript" src="jquery-1.9.1.js"></script>
		<script type="text/javascript" src="jquery-ui.js"></script>

    <!-- Main Javascript library, including data pulled from the databases, hence why it must be in .php format -->
		<script>purging=true;</script>
    <script type="text/javascript" src="scripts.php?<?php echo time(); ?>"></script>
		
		<!-- Purge-specific script -->
		<script>
			function ConfirmPurge() {
				if (document.entry.use_date.value=="") {
					$("#error").html("Please choose a cutoff date for the purge.");
				}
				else if (document.entry.confirmed.value!="yes") {
					document.entry.confirmed.value="yes";
					$("#error").html("Please confirm the purge one more time.");
				}
				else {
					document.entry.submit();
				}
			}
		</script>
  </head>
	<body>
		<a href="index.php"><span style="float:right"><img src="logo.png" /></span></a><br clear="both" />
		<div id="error" style="font-weight:bold;color:#cc0000;font-size:14pt"></div><br />
		<form id="dateForm" name="entry" action="purgeTickets.php" method="post">
			<input type="hidden" name="confirmed" value="no" />
			Purge tickets from before: <input name="use_date" id="use_date" type="text" /><br />
			<button onClick="ConfirmPurge()" type="button" accesskey="C"><u>C</u>onfirm Purge</button>
		</form>