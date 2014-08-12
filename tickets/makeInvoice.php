<html>
  <head>
	<?php
		if ($error=="") {
	?>
		<title>Invoice for <?php echo $_POST['c_num']; ?></title>
		<link rel="stylesheet" href="styles.css" type="text/css" />
	</head>
	<body>
		<h3>Invoice as of <?php echo date("n/j/Y"); ?></h3>
		<table>
		
		</table>
		<b>Note: This is an invoice, <i>not</i> an official bill. Your official bill will be sent at the end of the current billing period.</b>
	</body>
	<?php } else { ?>
	An error has occurred.
	<form id='red' action='invoice.php' method='post'>
		<input name='error' value='' />
		<button type='submit' id='sub' name='sub' accesskey='R'><u>R</u>eturn to invoice form</button>
	</form>
	<script>$("#red").submit();</script>
	<?php } ?>
</html>