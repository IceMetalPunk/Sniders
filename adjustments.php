<html>
  <head>
    <title>Manual Adjustments</title>

    <!-- Main styles -->
    <link rel="stylesheet" href="styles.css" />
		<style>
			#invoiceOnlyField { display:none; }
		</style>

		<!-- Styles for autocomplete elements -->
    <link rel="stylesheet" href="jquery-ui.css" />
    <link rel="stylesheet" href="jquery-style.css" />
		
    <!-- jQuery library and its Autocomplete extension -->
    <script type="text/javascript" src="jquery-1.9.1.js"></script>
		<script type="text/javascript" src="jquery-ui.js"></script>

		<!-- Adjustment-specific scripts -->
		<script type="text/javascript" src="adjustScripts.php?<?php echo time(); ?>"></script>
		
  </head>
	<body onload="InitAdjust()">
		<a href="index.php"><span style="float:right"><img src="logo.png" /></span></a><br clear="both" />
		<form name="entry" method="post" action="adjust.php">
			<table>
				<?php
					if (!empty($_POST['error'])) {
						echo "<tr><td>&nbsp</td><td colspan='3'><span class='error' style='font-size:14pt; display:inline'>".$_POST['error']."</span></td></tr><br />";
					}
				?>
				<tr>
				  <td>&nbsp;</td>
					<td><input id="c_num" name="c_num" placeholder="Cust No" size=6 /></td>
					<td><input id="c_name" name="c_name" size=40 placeholder="Customer Name" /></td>
				</tr>
				<tr>
					<td style="text-align:right">$</td>
					<td><input class="numOnly" name="amt" id="amt" size=6 maxlength=8 placeholder="0.00" /></td>
					<td><select id="adjustType" name="adjustType" style="width:100%">
						<?php
						  /* Output all the different adjustment types */
							require("paymentTypes.php");
							$started=false;
							foreach ($types as $name=>$val) {
							  echo "<option value='".$val."'".($started?"":" selected").">".$name."</option>\r\n";
								$started=true;
							}
						?>
					</select>
				</tr>
				
				<tr>
					<td>&nbsp;</td>
					<td colSpan='3'><input id="freeform" name="freeform" placeholder="Description" style="width:100%" /></td>
				</tr>
				
				<tr>
					<td>&nbsp;</td>
					<td><button type="submit" name="sub" id="sub" accessKey="C" style="width:100%"><u>C</u>onfirm</button></td>
					<td colSpan='2'>&nbsp;</td>
				</tr>
				
			</table>
			<div id="invoices"></div>
		</form>
		
  </body> 
</html>