<html>
  <head>
    <title>Invoicing</title>

    <!-- Main styles -->
    <link rel="stylesheet" href="styles.css" />

		<!-- Styles for autocomplete elements -->
    <link rel="stylesheet" href="jquery-ui.css" />
    <link rel="stylesheet" href="jquery-style.css" />
		
    <!-- jQuery library and its Autocomplete extension -->
    <script type="text/javascript" src="jquery-1.9.1.js"></script>
		<script type="text/javascript" src="jquery-ui.js"></script>
		
		<!-- Invoice-specific scripts -->
		<script type="text/javascript" src="invoiceScripts.php?<?php echo time(); ?>"></script>
		
  </head>
	<body onload="InitInvoice()">
		<a href="index.php"><span style="float:right"><img src="logo.png" /></span></a><br clear="both" />
		<form name="entry" action="makeInvoice.php" id="entry" method="post">
			<table>
				<?php
					if (!empty($_POST['error'])) {
						echo "<tr><td colspan='3'><span class='error' style='font-size:14pt; display:inline'>".$_POST['error']."</span></td></tr><br />";
					}
				?>
				<tr>
					<td><input id="c_num" name="c_num" placeholder="Cust No" size=6 /></td>
					<td><input id="c_name" name="c_name" size=40 placeholder="Customer Name" /></td>
				</tr>
				<tr>
					<td colspan='2'><input id="c_useDiscount" name="use_discount" type="checkbox" value="true" checked /><label for="c_useDiscount">Use discount if applicable</label></td>
				</tr>
				<tr>
					<td><button type='submit' id='sub' name='sub' accesskey='I'><u>I</u>nvoice</td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</form>
	</body>
</html>