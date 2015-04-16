<?php ob_start(); ?>
<html>
  <head>
	<?php
		$error="";
		$q="SELECT * FROM `t-customer` WHERE `C-CUSTNO`='".mysql_real_escape_string($_POST['c_num'])."'";
		$query=mysql_query($q);
		
		if (!$query || mysql_num_rows($query)<=0) {
			$error="Customer not found.";
		}
		else {
			$customerData=mysql_fetch_assoc($query);
		}
		
		if ($error=="") {
	?>
		<title>Invoice for <?php echo $_POST['c_num']; ?></title>
		<link rel="stylesheet" href="styles.css" type="text/css" />
		<style>
		  * { font-size:10pt; }
			.topHeaders, .topHeaders * { font-size: 14pt; }
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
		<button onclick="window.print()" accesskey='P'><u>P</u>rint</button>
		<br />
		<span class="topHeaders">
		<span style='float:right'><a href="index.php"><img src="logo.png" border=0 /></a><br />
		2898 Long Beach Rd<br />
		Oceanside, NY 11572<br />
		(516)442-2828</span><br clear='both' />
		<?php
			$q="SELECT COUNT(`W-CUSTNO`) AS num FROM `v-a-invoice` WHERE `W-CUSTNO`='".mysql_real_escape_string($_POST['c_num'])."'";
			$query=mysql_query($q);
			$q2="SELECT `TAB-CUSTNO` FROM `t-a-billing` WHERE `TAB-INV-NO`='' AND `TAB-CUSTNO`='".mysql_real_escape_string($_POST['c_num'])."'";
			$adjQuery=mysql_query($q2);
			
			$isRecap=(($query===FALSE || mysql_num_rows($query)<=0) && ($adjQuery===FALSE || mysql_num_rows($adjQuery)<=0));
			
			/* If there's no new items to invoice, show that message and set the number to RECAP instead of generating a new one */
			if ($isRecap) {
				$invNum="RECAP";
			}
			else {
				$q=mysql_fetch_assoc($query);
				if ($q["num"]<=0 && ($adjQuery===FALSE || mysql_num_rows($adjQuery)<=0)) {
					$invNum="RECAP";
				}
				
				/* If there are new items, make a new invoice number */
				else {
					$invNum=GenerateInvoiceNumber();
				}
			}
			
			$now=date("n/j/Y");
			echo "<span style='float:left'>".$customerData["C-NAME"]."</span>"; // Customer name
			echo "<span style='float:right; font-weight:bold'>Invoice ".($invNum=="RECAP"?$invNum:"#".$invNum)."</span><br />"; // Invoice #
			echo "<span style='float:left'>".$customerData["C-ADDR1"]."</span>"; // Customer address line 1
			echo "<span style='float:right; font-weight:bold'>Customer #".$_POST['c_num']."</span><br />"; // Customer number
			if (!empty($customerData["C-ADDR2"])) {
				echo "<span style='float:left'>".$customerData["C-ADDR2"]."</span><br />"; // Customer address line 2
				echo "<span style='float:right; font-weight:bold'>".$now."</span><br />"; // Current date
				echo "<span style='float:left'>".$customerData["C-CITY"].", ".$customerData["C-STATE"]." ".$customerData["C-ZIP"]."</span></span><br /><br />";
			}
			else {
				echo "<span style='float:left'>".$customerData["C-CITY"].", ".$customerData["C-STATE"]." ".$customerData["C-ZIP"]."</span></span>";
				echo "<span style='float:right; font-weight:bold'>".$now."</span><br /><br />"; // Current date
			}
		?>
		<table>
			<?php
				$num=0;
				$subtotal=0;
				$total=0;
				$credits=0;
				
				/* Get opening balance */
				$balance=$customerData["C-BALANCE"];
				
				/* Get previous invoices for the current cycle and, if any exist, show a consolidation of their totals (including adjustment charges and credits) */
				/*$q="SELECT *, SUM(`TAB-TOTAL`) as `TAB-SUM` FROM `t-a-billing` WHERE `TAB-CUSTNO`='".mysql_real_escape_string($_POST['c_num'])."' AND `TAB-INV-NO`!=''  GROUP BY `TAB-INV-NO` ORDER BY `TAB-INV-DT` ASC";
				$query=mysql_query($q);
				if ($query && mysql_num_rows($query)>0) {
					echo "<tr><th colspan='5' style='border-top:2px solid #000000'>Previous Invoices</th></tr>";
				  echo "<tr><th colspan='2'>Invoice Date</th><th colspan='2'>Invoice #</th><th colspan='2'>Invoice Totals</th></tr>";
					while ($row=mysql_fetch_assoc($query)) {
						echo "<tr><td colspan='2'>".$row["TAB-INV-DT"]."</td><td colspan='2'>".$row["TAB-INV-NO"]."</td><td colspan='2' class='right'>$".number_format($row["TAB-SUM"],2)."</td></tr>";
						$total+=$row["TAB-SUM"];
					}
					if ($invNum!="RECAP") { echo "<tr><td colspan='5'>&nbsp;</td></tr>"; }
				}
				echo "<tr><th>Total Previous Charges</th><td colspan='4' class='right'>$".number_format($total,2)."</td></tr>";*/
								
				/* List new items */
				$disc=$customerData["C-DISCNT-PCT"];
				//if (empty($_POST["use_discount"]) || !$_POST["use_discount"]) { $disc=0; }
				
				if ($invNum!="RECAP") {
					$chargeout="<tr><th colspan='4'>New Charges</th></tr>";
					$chargeout.="<tr><th>Transaction #</th><th>Reference</th><th>Use Date</th><th>Amount</th></tr>";
					$q="SELECT * FROM `v-a-invoice` WHERE `W-CUSTNO`='".mysql_real_escape_string($_POST['c_num'])."' ORDER BY `W-TKT`, `W-TKT-SUB` ASC";
					$query=mysql_query($q);
					
					$subtotal=0;
					$charges=0;
					
					while ($row=mysql_fetch_assoc($query)) {
						$chargeout.="<tr style='border-left:1px solid #000000; border-right:1px solid #000000'><td style='border:0px'>".$row["W-TKT"]."-".$row["W-TKT-SUB"]."</td><td style='border:0px'>".$row["W-REF"]."</td><td style='border:0px'>".date("n/j/Y" ,strtotime($row["W-USE-DT"]))."</td><td class='right' style='border:0px'>$".number_format($row["W-AMT"], 2)."</td>";
						$subtotal+=$row["W-AMT"];
					}
					
					/* List new adjusted charges */
					$q="SELECT * FROM `t-a-billing` WHERE `TAB-INV-NO`='' AND `TAB-CUSTNO`='".mysql_real_escape_string($_POST['c_num'])."' AND `TAB-ADJ-TYPE`>9 AND `TAB-ADJ-TYPE` BETWEEN 30 and 39";
					$query=mysql_query($q);
					if ($query && mysql_num_rows($query)>0) {
						while ($row=mysql_fetch_assoc($query)) {
							$chargeout.="<tr style='border-left:1px solid #000000; border-right:1px solid #000000'><td style='border:0px'>".$row["TAB-ADJ-NO"]."</td><td style='border:0px' colspan='2'>".$row["TAB-ADJ-REF"]."</td><td  style='border:0px' class='right'>$".number_format($row["TAB-TOTAL"], 2)."</td></tr>";
							$charges+=$row["TAB-AMT"];
						}
					}

					if ($charges!=0 || $subtotal!=0) { echo $chargeout; }
					
					/* Display subtotals and discounts etc. */
					//echo "<tr><th>New Charges</th><td colspan='4' class='right'>$".number_format($subtotal,2)."</td></tr>";
					
					/* List any credits or payments for the week */
					$q="SELECT * FROM `t-a-billing` WHERE `TAB-CUSTNO`='".mysql_real_escape_string($_POST['c_num'])."' AND `TAB-INV-NO`='' AND `TAB-ADJ-TYPE`>9 AND `TAB-ADJ-TYPE` NOT BETWEEN 30 and 39";
					$query=mysql_query($q);
					
					if ($query && mysql_num_rows($query)>0) {
						//echo "<tr style='border-top:2px solid #000000'><th colspan='5'>Payments and Credits</th></tr>";
						//echo "<tr><th>Transaction #</th><th colspan='3'>Details</th><th class='right'>Amount</th></tr>";
						while ($row=mysql_fetch_assoc($query)) {
							//echo "<tr><td>".$row["TAB-ADJ-NO"]."</td>"; // Adjustment number
							//echo "<td colspan='3'>".$row["TAB-ADJ-REF"]."</td>"; // Adjustment reference info
							//echo "<td class='right'>$".number_format(abs($row["TAB-TOTAL"]), 2)."</td></tr>"; // Amount of credit/payment
							$credits+=abs($row["TAB-TOTAL"]);
						}
					}
					
					/* Calculate charges and balance */
					$total+=$subtotal*(100-$disc)/100;
					$total+=$charges;
				}
				$balance+=$total-$credits;
				
				/* Display totals */
				//echo "<tr><td colspan='5'>&nbsp;</td></tr>";
				if ($total>0) {
					echo "<tr style='border-top:2px solid #000000'><th>Total Charges</th><td colspan='3' class='right'>$".number_format($subtotal+$charges,2)."</td></tr>";
				}
				if ($disc>0) {
					echo "<tr><th>Discount</th><td colspan='3' class='right'>".$disc."%</td></tr>";
					echo "<tr><th>Net Charges</th><td colspan='3' class='right'>$".number_format($total, 2)."</td></tr>";
				}
				echo "<tr><th>Previous Balance</th><td colspan='3' class='right'>$".number_format($customerData["C-BALANCE"], 2)."</td></tr>";
				if ($credits>0) { echo "<tr><th>Total Payments/Credits</th><td colspan='3' class='right'>(-$".number_format($credits, 2).")</td></tr>"; }
				echo "<tr style='font-weight:bold; font-size:12pt'><th style='font-size:12pt'>Current Balance</th><td colspan='3' class='right' style='font-size:12pt'>".($balance<0?"(-$".number_format($balance, 2).")":"$".number_format($balance, 2))."</td></tr>";
				
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
					
					/* Update the customer's balance */
					$q="UPDATE `t-customer` SET `C-BALANCE`=".mysql_real_escape_string($balance)." WHERE `C-CUSTNO`='".mysql_real_escape_string($_POST['c_num'])."'";
					$query=mysql_query($q);
				}
				
				if ($invNum!="RECAP") {
					echo "<script>$(function() { window.print(); });</script>";
				}
			?>
		</table>
		<br />
		<button onclick="window.location='http://127.0.0.1/invoice.php'" accesskey='R'><u>R</u>eturn to invoice form</button>
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