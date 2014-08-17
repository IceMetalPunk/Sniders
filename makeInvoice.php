<?php ob_start(); ?>
<html>
  <head>
	<?php
	  $link=mysql_connect("localhost", "root", "");
		$db=mysql_select_db("sniders2013", $link);
		
		$error="";
		$q="SELECT * FROM `t-customer` WHERE `C-CUSTNO`='".mysql_real_escape_string($_POST['c_num'])."'";
		$query=mysql_query($q);
		
		if (!$query || mysql_num_rows($query)<=0) { $error="Customer not found."; }
		else {
			$customerData=mysql_fetch_assoc($query);
		}
		
		if ($error=="") {
	?>
		<title>Invoice for <?php echo $_POST['c_num']; ?></title>
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
			echo $customerData["C-CITY"].", ".$customerData["C-STATE"]." ".$customerData["C-ZIP"]."<br /><br />";
		?>
		<table>
			<?php
			  function GenerateInvoiceNumber() {
					$q="SELECT `l-DESC` FROM `t-lookup` WHERE `l-VALUE`=997";
					$query=mysql_query($q);
					$row=mysql_fetch_assoc($query);
					$num=$row["l-DESC"];

					/* Update to new invoice number */
					$newNum=explode("-", $num);
					$numPart=$newNum[1]+1;
					if ($numPart>99999) { $numPart="0001"; }
					while (strlen($numPart)<4) { $numPart="0".$numPart; }
					$newNum="I".date("y")."-".$numPart;

					$q="UPDATE `t-lookup` SET `l-DESC`='".$newNum."' WHERE `l-VALUE`=997";
					$query=mysql_query($q);
							
					return $num;
				}
				
				$q="SELECT COUNT(`W-CUSTNO`) AS num FROM `v-a-invoice` WHERE `W-CUSTNO`='".mysql_real_escape_string($_POST['c_num'])."'";
				$query=mysql_query($q);
				
				/* If there's no new items to invoice, show that message and set the number to RECAP instead of generating a new one */
				if ($query===FALSE || mysql_num_rows($query)<=0) {
					$invNum="RECAP";
					echo "<span class='message'>There are no new items to be invoiced. Press CTRL+P to print this cycle's invoice recap.<br /></span>";
				}
				else {
					$q=mysql_fetch_assoc($query);
					if ($q["num"]<=0) {
						$invNum="RECAP";
						echo "<span class='message'>There are no new items to be invoiced. Press CTRL+P to print this cycle's invoice recap.<br /></span>";
					}
					
					/* If there are new items, make a new invoice number */
					else {
						$invNum=GenerateInvoiceNumber();
					}
				}
				
				$now=date("n/j/Y");
				echo "<h3>Invoice ".($invNum!="RECAP"?"#":"").$invNum." as of ".$now."</h3>";
				
				$num=0;
				$subtotal=0;
				$total=0;
				
				/* Show opening balance */
				echo "<tr><th>Opening Balance</th><td colspan='4' class='right'>$".number_format($customerData["C-BALANCE"], 2)."</td></tr>";
				$balance=$customerData["C-BALANCE"];
				
				/* Get previous invoices for the current cycle and, if any exist, show a consolidation of their totals (including adjustment charges, but not credits) */
				$q="SELECT *, SUM(`TAB-TOTAL`) as `TAB-SUM` FROM `t-a-billing` WHERE `TAB-INV-NO`!='' AND (`TAB-ADJ-TYPE`=0 OR `TAB-ADJ-TYPE` BETWEEN 30 and 39) GROUP BY `TAB-INV-NO` ORDER BY `TAB-INV-DT` ASC";
				$query=mysql_query($q);
				if ($query && mysql_num_rows($query)>0) {
					echo "<tr><th colspan='5' style='border-top:2px solid #000000'>Previous Invoices</th></tr>";
				  echo "<tr><th colspan='2'>Invoice Date</th><th colspan='2'>Invoice Number</th><th colspan='2'>Invoice Total Charges</th></tr>";
					while ($row=mysql_fetch_assoc($query)) {
						echo "<tr><td colspan='2'>".$row["TAB-INV-DT"]."</td><td colspan='2'>".$row["TAB-INV-NO"]."</td><td colspan='2' class='right'>$".number_format($row["TAB-SUM"],2)."</td></tr>";
						$total+=$row["TAB-SUM"];
					}
					//if ($invNum!="RECAP") { echo "<tr><td colspan='5'>&nbsp;</td></tr>"; }
				}
				
				/* List new items */
				$disc=$customerData["C-DISCNT-PCT"];
				if ($invNum!="RECAP") {
					echo "<tr style='border-top:2px solid #000000'><th colspan='5'>New Charges</th></tr>";
					echo "<tr><th>Invoice Date</th><th>Transaction #</th><th>Reference</th><th>Use Date</th><th>Amount</th></tr>";
					$q="SELECT * FROM `v-a-invoice` WHERE `W-CUSTNO`='".mysql_real_escape_string($_POST['c_num'])."'";
					$query=mysql_query($q);
					
					$subtotal=0;
					
					while ($row=mysql_fetch_assoc($query)) {
						echo "<tr><td>".$now."</td><td>".$row["W-TKT"]."-".$row["W-TKT-SUB"]."</td><td>".$row["W-REF"]."</td><td>".date("n/j/Y" ,strtotime($row["W-USE-DT"]))."</td><td class='right'>$".number_format($row["W-AMT"], 2)."</td>";
						$subtotal+=$row["W-AMT"];
					}
					
					/* List new adjusted charges */
					$q="SELECT * FROM `t-a-billing` WHERE `TAB-INV-NO`='' AND `TAB-ADJ-TYPE`>9 AND `TAB-ADJ-TYPE` BETWEEN 30 and 39";
					$query=mysql_query($q);
					if ($query && mysql_num_rows($query)>0) {
						while ($row=mysql_fetch_assoc($query)) {
							echo "<tr><td>&nbsp;</td><td>".$row["TAB-ADJ-NO"]."</td><td colspan='2'>".$row["TAB-ADJ-REF"]."</td><td class='right'>$".number_format($row["TAB-TOTAL"], 2)."</td></tr>";
							$subtotal+=$row["TAB-AMT"];
						}
					}
					
					/* Display subtotals and discounts etc. */
					echo "<tr><th>New Charges</th><td colspan='4' class='right'>$".number_format($subtotal,2)."</td></tr>";
					if ($disc>0) {
						echo "<tr><th>Discount</th><td colspan='4' class='right'>".$disc."%</td></tr>";
					}
					
					/* List any credits or payments for the week */
					$credits=0;
					$q="SELECT * FROM `t-a-billing` WHERE `TAB-ADJ-TYPE`>9 AND `TAB-ADJ-TYPE` NOT BETWEEN 30 and 39";
					$query=mysql_query($q);
					if ($query && mysql_num_rows($query)>0) {
						echo "<tr style='border-top:2px solid #000000'><th colspan='5'>Payments and Credits</th></tr>";
						echo "<tr><th>Transaction #</th><th colspan='3'>Details</th><th class='right'>Amount</th></tr>";
						while ($row=mysql_fetch_assoc($query)) {
							echo "<tr><td>".$row["TAB-ADJ-NO"]."</td>"; // Adjustment number
							echo "<td colspan='3'>".$row["TAB-ADJ-REF"]."</td>"; // Adjustment reference info
							echo "<td class='right'>$".number_format(abs($row["TAB-TOTAL"]), 2)."</td></tr>"; // Amount of credit/payment
							$credits+=abs($row["TAB-TOTAL"]);
						}
					}
					
					/* Calculate total charges and balance */
					$total+=$subtotal*(100-$disc)/100;
					$balance+=$total-$credits;
				}
				
				/* Display totals */
				echo "<tr><td colspan='5'>&nbsp;</td></tr>";
				echo "<tr style='border-top:2px solid #000000'><th>Total Charges</th><td colspan='4' class='right'>$".number_format($total,2)."</td></tr>";
				echo "<tr><th>Total Payments/Credits</th><td colspan='4' class='right'>$".number_format($credits, 2)."</td></tr>";
				echo "<tr><th>Current Balance</th><td colspan='4' class='right'>$".number_format($balance, 2)."</td></tr>";
				
				/* Insert invoice into billing table */
				if ($invNum!="RECAP") {
					$q="INSERT INTO `t-a-billing` VALUES (";
					$q.="'".mysql_real_escape_string($_POST['c_num'])."', "; // Customer number
					$q.="'', NOW(), '".mysql_real_escape_string($invNum)."', "; // Post date (blank for now), invoice date, and invoice number
					$q.="'', 0, '', "; // Adjustment number (blank for invoices), adjustment type (0=invoice), and adjustment reference (blank for invoices)
					$q.=$subtotal.", ".mysql_real_escape_string($disc).", ".mysql_real_escape_string($subtotal*(100-$disc)/100).")"; // Subtotal, discount, and total
					$query=mysql_query($q);
					
					/* Update the adjustments to have the proper invoice numbers */
					$q="UPDATE `t-a-billing` SET `TAB-INV-NO`='".mysql_real_escape_string($invNum)."' WHERE `TAB-CUSTNO`='".mysql_real_escape_string($_POST['c_num'])."' AND `TAB-INV-NO`=''";
					$query=mysql_query($q);
					
					/* And also update the fields in the view (which updates the work table and removes them from the view) */
					$q="UPDATE `v-a-invoice` SET `W-INV-NO`='".mysql_real_escape_string($invNum)."' WHERE `W-CUSTNO`='".mysql_real_escape_string($_POST['c_num'])."'";
					$query=mysql_query($q);
				}
				
				if ($invNum!="RECAP") {
					echo "<script>$(function() { window.print(); });</script>";				
				}
			?>
		</table>
		<br />
		<button onclick="window.location='invoice.php'" accesskey='R'><u>R</u>eturn to invoice form</button>
	<?php } else { ?>
		<title>Invoice Generation Failed</title>
		<script type="text/javascript" src="jquery-1.9.1.js"></script>
		<link rel="stylesheet" href="styles.css" />
	</head>
	<body>
		An error has occurred.
		<form id='red' action='invoice.php' method='post'>
			<input type="hidden" name='error' value='<?php echo $error; ?>' />
			<button type='submit' id='sub' name='sub' accesskey='R'><u>R</u>eturn to invoice form</button>
		</form>
		<script>$("#red").submit();</script>
		<?php
			}
			mysql_close($link);
		?>
	</body>
</html>
<?php
	if ($error=="") {
	  if (!file_exists("billing")) { mkdir("billing"); }
	  if (!file_exists("billing/invoices")) { mkdir("billing/invoices"); }

		file_put_contents("billing/invoices/".$invNum.".html", ob_get_contents());
	}
  ob_end_flush();
?>