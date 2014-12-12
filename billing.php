<html>
  <head>
    <title>Billing</title>

    <!-- Main styles -->
    <link rel="stylesheet" href="styles.css" />

    <!-- jQuery library and its Autocomplete extension -->
    <script type="text/javascript" src="jquery-1.9.1.js"></script>

    <!-- Main Javascript library, including data pulled from the databases, hence why it must be in .php format -->
    <script type="text/javascript" src="scripts.php?<?php echo time(); ?>"></script>
  </head>
	<body>
		<a href="index.php"><span style="float:right"><img src="logo.png" /></span></a><br clear="both" />
		<button name="cycle" onclick="window.location='billingCycle.php'" accesskey="C">Begin Billing <u>C</u>ycle</button>
		<button name="invoice" onclick="window.location='invoice.php'" accesskey="I">Create <u>I</u>nvoice</button>
		<button name="setdate" onclick="window.location='setDate.php'" accesskey="D">Set Cycle End <u>D</u>ate</button>
		
  </body> 
</html>