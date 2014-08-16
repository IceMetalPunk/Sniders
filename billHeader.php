<html>
  <head>
		<title>Bill for <?php echo $custRow["C-CUSTNO"]; ?></title>
		<link rel="stylesheet" href="styles.css" type="text/css" />
		<style>
		  * { font-size:14pt; }
			TH { font-weight:bold; padding:4px; border:1px solid #000000; background-color:#aaaaaa; }
			TD { border:1px solid #000000; padding:4px; }
			TABLE { border-collapse: collapse; }
		</style>
		<script type="text/javascript" src="jquery-1.9.1.js"></script>
	</head>
	<body>
		<span style='float:right'><a href="index.php"><img src="logo.png" border=0 /></a><br />
		2882 Long Beach Rd<br />
		Oceanside, NY 11572<br />
		(516)442-2828</span><br clear='both' />
		<?php
		  echo $custRow["C-NAME"]."<br />";
			echo $custRow["C-ADDR1"]."<br />";
			if (!empty($custRow["C-ADDR2"])) {
			  echo $custRow["C-ADDR2"]."<br />";
			}
			echo $custRow["C-CITY"].", ".$custRow["C-STATE"]." ".$custRow["C-ZIP"]."<br />";
		?>
		<table>
			<h3>Bill #<?php echo $billNum; ?> as of <?php echo date("n/j/Y"); ?></h3>
			<tr>
				<th>Invoice Date</th>
				<th>Invoice Number</th>
				<th>Subtotal</th>
				<th>Discount</th>
				<th>Total</th>
				<th style='border-left:0px'>&nbsp;</th>
			</tr>