<html>
  <head>
    <title>Billing</title>

    <!-- Main styles -->
    <link rel="stylesheet" href="styles.css" />
		
		<!-- jQuery styles -->
		<link rel="stylesheet" href="jquery-ui.css" />
    <link rel="stylesheet" href="jquery-style.css" />
		
    <!-- jQuery library and its Autocomplete extension -->
    <script type="text/javascript" src="jquery-1.9.1.js"></script>
		<script type="text/javascript" src="jquery-ui.js"></script>

    <!-- Main Javascript library, including data pulled from the databases, hence why it must be in .php format -->
    <script type="text/javascript" src="scripts.php?<?php echo time(); ?>"></script>
  </head>
	<body>
		<a href="index.php"><span style="float:right"><img src="logo.png" /></span></a><br clear="both" />
		<h3>Set Cycle End Date</h3>
		<form id="dateForm" name="entry" action="setCycleDate.php" method="post">
			<input name="use_date" id="use_date" type="text" />
			<input type="hidden" name="full_use_date" />
			
			<script type="text/javascript">
				$(function() {
					document.entry.use_date.value="<?php
						$nextCycle=strtotime("next Sunday");
						if (date("w")!=0) {
							$nextCycle=strtotime("+7 days", $nextCycle);
						}
						echo date("n/j/Y", $nextCycle);
						?>";
					$(document.entry.use_date).blur();
					$(document.entry.use_date).select();
					$(document.entry.use_date).focus();
				});
			</script>
			<button type="submit" accesskey="C"><u>C</u>onfirm New Cycle End Date</button>
		</form>
		
  </body> 
</html>