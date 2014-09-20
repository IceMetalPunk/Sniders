<html>
  <head>
    <title>Quarterly Recap</title>

    <!-- Main styles -->
    <link rel="stylesheet" href="styles.css" />
		<link rel="stylesheet" href="jquery-ui.css" />
		<link rel="stylesheet" href="jquery-style.css" />
		<style>
			.restab TD { padding:4px; border: 1px solid #555555; text-align:right; font-size:12pt; }
			.restab TH { padding: 4px; border: 1px solid #000000; tet-align: center; font-size:12pt;}
			.restab A { font-size:12pt; }
			TABLE.restab { border-collapse: collapse; font-size:12pt; }
		</style>

    <!-- jQuery library and its Autocomplete extension -->
    <script type="text/javascript" src="jquery-1.9.1.js"></script>
		<script type="text/javascript" src="jquery-ui.js"></script>

    <!-- Main Javascript library, including data pulled from the databases, hence why it must be in .php format -->
		<script type="text/javascript" src="invoiceScripts.php?<?php echo time(); ?>"></script>
		
  </head>
	<body onload="InitInvoice()">
		<a href="index.php"><span style="float:right"><img src="logo.png" /></span></a><br clear="both" />
<?php
  $link=mysql_connect("localhost", "root", "tux898");
  /* Select the sniders2013 database for use later */
  $db=mysql_select_db("sniders2013", $link);
	
	$error="";
	if (!empty($_POST['submitted']) && $_POST['submitted']="Recap") {
		if (empty($_POST['date1']) || empty($_POST['date2'])) {
			$error="Please enter both starting and ending dates.";
		}
		else {
			$dt1=date("Y-n-j", strtotime($_POST['date1']));
			$dt2=date("Y-n-j", strtotime($_POST['date2']));
			$q="SELECT DISTINCT `C-CUSTNO` FROM `t-customer` WHERE `C-BALANCE`>0";
			$query=mysql_query($q);
			
			$results=array();
			while ($row=mysql_fetch_assoc($query)) {
				$q="SELECT SUM(`TAR-AMT`) as `TAR-TOTAL` FROM `t-a-rec` WHERE `TAR-CUSTNO`='".mysql_real_escape_string($row["C-CUSTNO"])."' AND `TAR-POST-DT` BETWEEN '".mysql_real_escape_string($dt1)."' AND '".mysql_real_escape_string($dt2)."' GROUP BY `TAR-CUSTNO`";
				$tarQuery=mysql_query($q);
				
				if (mysql_num_rows($tarQuery)<=0) {
					$row["C-INV-TOTAL"]=0;
				}
				else {
					$tarRow=mysql_fetch_assoc($tarQuery);
					$row["C-INV-TOTAL"]=$tarRow["TAR-TOTAL"];
				}
				
				$results[]=$row;
			}
			if (count($results)<=0) {
				$error="No customers have nonzero balances.";
			}
		}
	}
	mysql_close($link);
?>
		<form name="entry" method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
			<table>
				<tr>
					<td>Customer</td>
					<td><input id="c_num" name="C-CUSTNO" placeholder="Cust No" size=6 /></td>
					<td colspan='2'><input id="c_name" name="C-NAME" size=40 placeholder="Customer Name" /></td>
				</tr>
				<tr>
					<td>Address</td>
					<td><input name="C-CITY" placeholder="City"/></td>
					<td><input name="C-STATE" size=5 maxlength=2 style="text-transform: uppercase" placeholder="State" /></td>
					<td><input name="C-ZIP" placeholder="Zip" size=6 maxlength=5/></td>
				</tr>
				<tr>
					<td>Shipping Address</td>
					<td><input name="C-SCITY" placeholder="City"/></td>
					<td><input name="C-SSTATE" size=5 maxlength=2 style="text-transform: uppercase" placeholder="State" /></td>
					<td><input name="C-SZIP" placeholder="Zip" size=6 maxlength=5/></td>
				</tr>
			</table>
			<button type="submit" value="Lookup" name="submitted" accesskey="L"><u>L</u>ookup</button>
		</form>
		
  </body> 
</html>