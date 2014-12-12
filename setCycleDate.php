<?php
	$link=mysql_connect("localhost", "root", "tux898");
  $db=mysql_select_db("sniders2013", $link);
	
	if (!empty($_POST["use_date"])) {
		$newDate=strtotime($_POST["use_date"]);
		if ($newDate!==FALSE) {
			$newDate=date("Y-m-d", $newDate);
		
			/* Update the cycle's end date */
			$q="UPDATE `t-lookup` SET `l-DESC`='".$newDate."' WHERE `l-VALUE`=301";
			$query=mysql_query($q);
		}
	}
	
	mysql_close($link);
?>
<html>
	<head>
		<title>End Date Set</title>
		<link rel="stylesheet" href="styles.css" type="text/css" />
		<meta http-equiv="refresh" content="1;index.php" />
		<script>
			window.onload=function() {
				window.location.href="index.php";
			}
		</script>
	</head>
	<body>
		<h3>Set the cycle's end date. Returning you to the main form.</h3><br />
		<small>If you are not redirected in 3 seconds, please <a href="index.php">click here</a>.</small>
	</body>
</html>