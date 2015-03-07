<?php
	ob_start();
?><html>
  <head>
    <title>Accounts Receivable Summary Report</title>

    <!-- Main styles -->
    <link rel="stylesheet" href="styles.css" />
		<link rel="stylesheet" href="jquery-ui.css" />
		<link rel="stylesheet" href="jquery-style.css" />
		<style>
			.restab TD { padding:4px; border: 1px solid #555555; text-align:right; font-size:12pt; }
			.restab TH { padding: 4px; background-color: #aaaaaa; font-weight:bold; border: 1px solid #000000; text-align: center; font-size:12pt;}
			.restab A { font-size:12pt; }
			TABLE.restab { border-collapse: collapse; font-size:12pt; }
			@media print {
			  BUTTON { display:none; }
				.message { display: none; }
			}
		</style>

    <!-- jQuery library and its Autocomplete extension -->
    <script type="text/javascript" src="jquery-1.9.1.js"></script>
		<script type="text/javascript" src="jquery-ui.js"></script>

</head>
	<body>
		<a href="index.php"><span style="float:right"><img src="logo.png" /></span></a><br clear="both" />
<?php
  $link=mysql_connect("localhost", "root", "tux898");
  /* Select the sniders2013 database for use later */
  $db=mysql_select_db("sniders2013", $link);
	
	function format_amount($amt) {
		return ($amt<0?"(-":"")."$".number_format(abs($amt), 2).($amt<0?")":"");
	}

		$q="SELECT * FROM `t-customer` WHERE `C-BALANCE`!=0 ORDER BY `C-NAME`, `C-CUSTNO`";
		$query=mysql_query($q);

		$grandInv = 0;
		$grandChg = 0;
		$grandPay = 0;
		$grandTotal = 0;
		$grandIndicator = "";
		if (mysql_num_rows($query)>0) {
			//echo "<h3>Monthly Statement for ".date("F Y", $lastDatePHP)."</h3>";
			echo "<h3>Accounts Receivable Summary Report</h3>";
			echo "<table class='restab'>";
			echo "<tr><th>Customer Name</th><th>Opening Balance</th><th>Invoice Total</th><th>Miscellaneous Charges</th><th>Credits/Payments</th><th>Current Balance</th></tr>";
			
			while ($custRow=mysql_fetch_assoc($query)) {
				
				echo "<td>".$custRow["C-NAME"]."</td>";
				echo "<td>".format_amount($custRow["C-OPEN-BALANCE"])."</td>";
				
				$invoiceQ="SELECT SUM(`TAB-TOTAL`) AS InvAMT FROM `t-a-billing` WHERE `TAB-ADJ-TYPE`<2 AND `TAB-CUSTNO`='".$custRow["C-CUSTNO"]."'";
				$invoiceQE=mysql_query($invoiceQ);
				$invoiceRow=mysql_fetch_assoc($invoiceQE);
				if (is_null($invoiceRow["InvAMT"])) { $invoiceRow["InvAMT"]=0; }
				$invoiced=$invoiceRow["InvAMT"];
				echo "<td class='right'>".format_amount($invoiced)."</td>";
				
				$chargeQ="SELECT SUM(`TAB-TOTAL`) AS ChgAMT FROM `t-a-billing` WHERE `TAB-CUSTNO`='".$custRow["C-CUSTNO"]."' AND `TAB-INV-NO`!='' AND `TAB-ADJ-TYPE` BETWEEN 30 AND 39";
				$chargeQE=mysql_query($chargeQ);
				$chargeRow=mysql_fetch_assoc($chargeQE);
				if (is_null($chargeRow["ChgAMT"])) { $chargeRow["ChgAMT"]=0; }
				$charges=$chargeRow["ChgAMT"];
				
				$indicator=""; // "Uninvoiced items included" indicator
				$unchargeQ="SELECT SUM(`TAB-TOTAL`) AS ChgAMT FROM `t-a-billing` WHERE `TAB-CUSTNO`='".$custRow["C-CUSTNO"]."' AND `TAB-INV-NO`='' AND `TAB-ADJ-TYPE` BETWEEN 30 AND 39";
				$unchargeQE=mysql_query($unchargeQ);
				$unchargeRow=mysql_fetch_assoc($unchargeQE);
				if (is_null($unchargeRow["ChgAMT"])) { $unchargeRow["ChgAMT"]=0; }
				else { $indicator="*"; }
				$charges+=$unchargeRow["ChgAMT"];
				echo "<td class='right'>".format_amount($charges)."</td>";
				
				$creditQ="SELECT SUM(`TAB-TOTAL`) AS CredAMT FROM `t-a-billing` WHERE `TAB-CUSTNO`='".$custRow["C-CUSTNO"]."' AND `TAB-INV-NO`!='' AND `TAB-ADJ-TYPE`>9 AND `TAB-ADJ-TYPE` NOT BETWEEN 30 AND 39";
				$creditQE=mysql_query($creditQ);
				$creditRow=mysql_fetch_assoc($creditQE);
				if (is_null($creditRow["CredAMT"])) { $creditRow["CredAMT"]=0; }
				$credits=$creditRow["CredAMT"];
				
				$uncreditQ="SELECT SUM(`TAB-TOTAL`) AS CredAMT FROM `t-a-billing` WHERE `TAB-CUSTNO`='".$custRow["C-CUSTNO"]."' AND `TAB-INV-NO`='' AND `TAB-ADJ-TYPE`>9 AND `TAB-ADJ-TYPE` NOT BETWEEN 30 AND 39";
				$uncreditQE=mysql_query($uncreditQ);
				$uncreditRow=mysql_fetch_assoc($uncreditQE);
				if (is_null($uncreditRow["CredAMT"])) { $uncreditRow["CredAMT"]=0;}
				else { $indicator="*"; }
				$credits+=$uncreditRow["CredAMT"];
				echo "<td class='right'>".format_amount($credits)."</td>";
				
				$bal=$custRow["C-OPEN-BALANCE"]+$charges+$invoiced-abs($credits);
				echo "<td class='right'>".format_amount($bal).$indicator."</td>";
				echo "</tr>";
				
				$grandInv+=$invoiced;
				$grandChg+=$charges;
				$grandPay+=$credits;
				$grandTotal+=$bal;
				if ($indicator=="*") { $grandIndicator="*"; }
			
			}
			
			echo "<tr><td style='font-weight:bold'>Grand Totals</td>";
			echo "<td>&nbsp;</td>";
			echo "<td class='right' style='font-weight:bold'>".format_amount($grandInv)."</td>";
			echo "<td class='right' style='font-weight:bold'>".format_amount($grandChg)."</td>";
			echo "<td class='right' style='font-weight:bold'>".format_amount($grandPay)."</td>";
			echo "<td class='right' style='font-weight:bold'>".format_amount($grandTotal).$grandIndicator."</td>";
			
			echo "</table>";
			echo "<span style='font-size:10pt; font-style:italic'>*Amounts followed by a star include uninvoiced manual adjustments (payments, credits, or miscellaneous charges)</span>";
		}
		else {
			echo "<h3>Accounts Receivable Summary Report</h3><br />All customers have $0.00 balance.";
		}
		
// $q="UPDATE `t-lookup` SET `l-DESC`=NOW() WHERE `l-VALUE`=996";
//		$query=mysql_query($q);

	mysql_close($link);
?>
	<script>
		document.body.onload=function() {
			window.print();
		}
	</script>
  </body> 
</html>
<?php
	if (!file_exists("billing/Accounts Receivable Summary Reports")) {
		mkdir("billing/Accounts Receivable Summary Reports");
	}
	file_put_contents("billing/Accounts Receivable Summary Reports/".date("n-j-Y h-i-s").".html", ob_get_contents());
	ob_end_flush();
?>