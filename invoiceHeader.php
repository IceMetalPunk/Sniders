<html>
  <head>
		<title>Invoice for <?php echo $customerData["C-CUSTNO"]; ?></title>
		<link rel="stylesheet" href="styles.css" type="text/css" />
		<style>
		  * { font-size:14pt; }
			TH { font-weight:bold; padding:4px; border:1px solid #000000; background-color:#aaaaaa; }
			TD { border:1px solid #000000; padding:4px; }
			TABLE { border-collapse: collapse; }
			@media print {
			  BUTTON { display:none; }
				.message { display: none; }
			}
		</style>
		<script type="text/javascript" src="jquery-1.9.1.js"></script>
	</head>
	<body>
		<button onclick="window.location='invoice.php'" accesskey='R'><u>R</u>eturn to invoice form</button>
		<br />
		<span style='float:right'><a href="index.php"><img src="logo.png" border=0 /></a><br />
		2882 Long Beach Rd<br />
		Oceanside, NY 11572<br />
		(516)442-2828</span><br clear='both' />
		<?php
		  echo $customerData["C-NAME"]."<br />";
			echo $customerData["C-ADDR1"]."<br />";
			if (!empty($customerData["C-ADDR2"])) {
			  echo $customerData["C-ADDR2"]."<br />";
			}
			echo $customerData["C-CITY"].", ".$customerData["C-STATE"]." ".$customerData["C-ZIP"]."<br />";
		?>
		<table>