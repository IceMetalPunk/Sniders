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
			
			$q="SELECT * FROM `t-lookup` WHERE `l-VALUE`=301";
			$query=mysql_query($q);
			if (!$query || mysql_num_rows($query)<=0) {
				$error="Billing cycle date not found.";
			}
			else {
				$nextCycle=mysql_fetch_assoc($query);
				$nextCycle=$nextCycle["l-DESC"];
			}
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
			}
		</style>
		<script type="text/javascript" src="jquery-1.9.1.js"></script>
	</head>
	<body>
		<button onclick="window.location='invoice.php'" accesskey='R'><u>R</u>eturn to invoice form</button>
		<br />
		<span style='float:right'><a href="index.php"><img src="logo.png" /></a><br />
		3435 Lawson Blvd<br />
		Oceanside, NY 11572<br />
		<br />
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
			<?php
			  function GenerateInvoiceNumber() {
					$q="SELECT `l-DESC` FROM `t-lookup` WHERE `l-VALUE`=997";
					$query=mysql_query($q);
					$row=mysql_fetch_assoc($query);
					$num=$row["l-DESC"];

					/* Update to new invoice number */
					$newNum=explode("-", $num);
					$numPart=$newNum[1]+1;
					if ($numPart>9999) { $numPart="0001"; }
					while (strlen($numPart)<4) { $numPart="0".$numPart; }
					$newNum="I".date("y")."-".$numPart;

					$q="UPDATE `t-lookup` SET `l-DESC`='".$newNum."' WHERE `l-VALUE`=997";
					$query=mysql_query($q);
							
					return $num;
				}
				$q="SELECT `AB-INV-DT`, `AB-BILL-NO` FROM `t-a-billing` WHERE (`AB-CUSTNO`,`AB-INV-DT`) IN (SELECT `AB-CUSTNO`, MAX(`AB-INV-DT`) FROM `t-a-billing` WHERE `AB-CUSTNO`='".mysql_real_escape_string($_POST['c_num'])."' AND DATEDIFF(`AB-USE-DT`, '".$nextCycle."')<=0 AND `AB-BILL-NO` IS NOT NULL AND `AB-BILL-NO`!='' GROUP BY `AB-CUSTNO`)";
				$query=mysql_query($q);
				if (!$query || mysql_num_rows($query)<=0) {
				  $invNum=GenerateInvoiceNumber();
				}
				else {
					$invNum=mysql_fetch_assoc($query);
					$invNum=$invNum["AB-BILL-NO"]; 
				}
				
				echo "<h3>Invoice #".$invNum." as of ".date("n/j/Y")."</h3>";
				
			  $q="SELECT * FROM `t-a-billing` WHERE `AB-CUSTNO`='".mysql_real_escape_string($_POST['c_num'])."' AND DATEDIFF(`AB-USE-DT`, '".$nextCycle."')<=0 ORDER BY `AB-BILL-TYP`, `AB-INV-DT`";
				$query=mysql_query($q);
				if (!$query || mysql_num_rows($query)<=0) {
				  echo "<tr><td>You have no items to invoice for this billing cycle.</td></tr>";
				}
				else {
				  $num=0;
					$subtotal=0;
					$credit=0;
					$payment=0;
					$total=0;
					$lastDate="";
					$now=date("Y-n-j");
					$worked=false;
					
				  echo "<tr><th>Invoice Date</th><th>Ticket Number</th><th>Use Date</th><th>Reference</th><th>Credit</th><th>Charge</th></tr>";
				  while ($row=mysql_fetch_assoc($query)) {
					  $num++;
					  $dt=$row["AB-INV-DT"];
						if ($dt=="" || $dt==NULL) {
							$dt=$now;
						}
					  echo "<tr";
						if (($row["AB-BILL-TYP"]!=10 || $num==1) && !$worked) { 
							if ($num>1) { $worked=true; }
							echo " style='border-top:2px solid #000000'";
						}
						echo "><td>";
						if ($lastDate!=$dt) { echo date("n/j/Y", strtotime($dt)); }
						echo "&nbsp;</td>";
						if ($row["AB-BILL-TYP"]==10) { echo "<td>".$row["AB-TKT"]."-".$row["AB-TKT-SUB"]."&nbsp;</td>"; }
						else { echo "<td>&nbsp;</td>"; }
						echo "<td>".date("n/j/Y", strtotime($row["AB-USE-DT"]))."&nbsp;</td><td>".$row["AB-REF"]."&nbsp;</td>";
						if ($row["AB-BILL-TYP"]<20 || ($row["AB-BILL-TYP"]>29 && $row["AB-BILL-TYP"]<40)) {
							$subtotal+=$row["AB-AMT"];
							echo "<td>&nbsp;</td><td>$".number_format(abs($row["AB-AMT"]), 2)."</td>";
						}
						else if ($row["AB-BILL-TYP"]>=40) {
							$credit+=abs($row["AB-AMT"]);
							echo "<td>$".number_format(abs($row["AB-AMT"]), 2)."</td><td>&nbsp;</td>";
						}
						else {
							$payment+=abs($row["AB-AMT"]);
							echo "<td>$".number_format(abs($row["AB-AMT"]), 2)."</td><td>&nbsp;</td>";
						}
						
						$lastDate=$dt;
					}
					$disc=$customerData["C-DISCNT-PCT"];
					if ($disc>0) {
						echo "<tr style='border-top:2px solid #000000'><th>Subtotal</th><td colspan='5' style='text-align:right'>$".number_format($subtotal,2)."</td></tr>";
						echo "<tr><th>Discount</th><td colspan='5' style='text-align:right'>".$disc."%</td></tr>";
					}
						$total=$subtotal*(100-$disc)/100;
					echo "<tr><th>Total</th><td colspan='5' style='text-align:right'>$".number_format($total,2)."</td></tr>";
					echo "<tr><th>Payments</th><td colspan='5' style='text-align:right'>$".number_format($payment,2)."</td></tr>";
					echo "<tr><th>Credit</th><td colspan='5' style='text-align:right'>$".number_format($credit, 2)."</td></tr>";
					echo "<tr><td colspan='6'>&nbsp;</td></tr>";
					echo "<tr style='border-top:2px solid #000000'><th>Remaining Balance</th><td colspan='5' style='text-align:right; background-color:#cccccc'>$".number_format($total-$credit-$payment, 2)."</td></tr>";
				}
				
				$q="UPDATE `t-a-billing` SET `AB-INV-DT`='".mysql_real_escape_string($now)."', `AB-BILL-NO`='".mysql_real_escape_string($invNum)."' WHERE `AB-CUSTNO`='".mysql_real_escape_string($_POST['c_num'])."' AND DATEDIFF(`AB-USE-DT`, '".$nextCycle."')<=0 AND `AB-INV-DT` IS NULL";
				$query=mysql_query($q);
				
				echo "<script>$(function() { window.print(); });</script>";				
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