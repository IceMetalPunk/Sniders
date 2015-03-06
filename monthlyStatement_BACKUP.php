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

    <!-- Main Javascript library, including data pulled from the databases, hence why it must be in .php format -->
		<script type="text/javascript" src="invoiceScripts.php?<?php echo time(); ?>"></script>
		
		<!-- Code to set accounts receivable summary report date for "opening balance" and cutoff for invoice/adjustment totals -->
		<!-- No longer needed!
		<script type="text/javascript">
			function SetDate() {
				var req=new XMLHttpRequest();
				req.open("GET", "updateReportDate.php");
				req.onreadystatechange=function() {
					if (this.readyState==4) {
						if (this.status<200 || this.status>=300) {
							$("#updateResponse").html("<b style='color:#aa0000'>A server error has occurred. Please contact the administrator and give him code "+this.status+".</b>");
						}
						else {
							try {
								var json=JSON.parse(this.responseText);
								if (typeof json.status=="undefined") {
									$("#updateResponse").html("<b style='color:#aa0000'>A server object error has occurred. Please contact the administrator.</b>");
								}
								else if (json.status<=0) {
									$("#updateResponse").html("<b style='color:#aa0000'>A server update error has occurred. Please contact the administrator.</b>");
								}
								else {
									$("#updateResponse").html("<b>Accounts receiveable date has been reset to today.</b>");
								}
							}
							catch (e) {
								$("#updateResponse").html("<b style='color:#aa0000'>A server response error has occurred. Please contact the administrator.</b>");
							}
						}
					}
				}
				req.send(null);
			}
		</script>-->
		
  </head>
	<body>
		<a href="index.php"><span style="float:right"><img src="logo.png" /></span></a><br clear="both" />
		<div id="updateResponse" class="message" style="font-size:10pt"></div>
		<!--<button onclick="SetDate()" accesskey="D">Reset Accounts Receivable <u>D</u>ate</button>-->
<?php
  $link=mysql_connect("localhost", "root", "tux898");
  /* Select the sniders2013 database for use later */
  $db=mysql_select_db("sniders2013", $link);

		$q="SELECT `l-DESC` FROM `t-lookup` WHERE `l-VALUE`=996";
		$query=mysql_query($q);
		$dateRow=mysql_fetch_assoc($query);
		$lastDate=$dateRow["l-DESC"];
		$lastDatePHP=strtotime($lastDate);

		$q="SELECT * FROM `t-customer` WHERE `C-BALANCE`!=0 ORDER BY `C-NAME`, `C-CUSTNO`";
		$query=mysql_query($q);

		$grandInv = 0;
		$grandChg = 0;
		$grandPay = 0;
		$grandTotal = 0;
		if (mysql_num_rows($query)>0) {
			//echo "<h3>Monthly Statement for ".date("F Y", $lastDatePHP)."</h3>";
			echo "<h3>Accounts Receivable Summary Report to Date</h3>";
			echo "<table class='restab'><tr><th>Customer Name</th><th>Invoice Total</th><th>Miscellaneous Charges</th><th>Credits/Payments</th><th>Net Balance</th></tr>";
			
			while ($custRow=mysql_fetch_assoc($query)) {
				$q="SELECT SUM(`TAR-AMT`) AS amt, `TAR-TYPE` FROM `t-a-rec` WHERE `TAR-CUSTNO`='".mysql_real_escape_string($custRow["C-CUSTNO"])."' GROUP BY `TAR-TYPE`";
				$invTotal=0;
				$chgTotal=0;
				$creditTotal=0;
				$subquery=mysql_query($q);
				if (mysql_num_rows($subquery)>0) {
					while ($info=mysql_fetch_assoc($subquery)) {
						if ($info["TAR-TYPE"]==0) { $invTotal+=$info["amt"]; }
						else if (($info["TAR-TYPE"]>=20 && $info["TAR-TYPE"]<=29) || $info["TAR-TYPE"]==41) { $creditTotal+=$info["amt"]; }
						else { $chgTotal+=$info["amt"]; $invTotal-=$info["amt"]; }
					}	
				}
				
				$grandChg += $chgTotal;
				$grandPay += $creditTotal;
				$grandInv += $invTotal;
				$grandTotal += $custRow["C-BALANCE"];
				
				/* Prevous TAB (uninvoiced items) summation--no longer needed. Kept for insurance.
				$q="SELECT SUM(`TAB-TOTAL`) AS amt, `TAB-ADJ-TYPE` FROM `t-a-billing` WHERE `TAB-CUSTNO`='".mysql_real_escape_string($custRow["C-CUSTNO"])."' AND `TAB-INV-NO`!='' GROUP BY `TAB-ADJ-TYPE`";
				$subquery=mysql_query($q);
				if (mysql_num_rows($subquery)>0) {
					while ($info=mysql_fetch_assoc($subquery)) {
						if ($info["TAB-ADJ-TYPE"]==0) { $invTotal+=$info["amt"]; }
						else { $adjTotal+=$info["amt"]; }
					}	
				}
				*/
				echo "<tr><td>".$custRow["C-NAME"]."</td>";//<td>".$custRow["C-CUSTNO"]."</td>";
				echo "<td>".($invTotal>=0?"$".number_format($invTotal, 2):"(-$".number_format(abs($invTotal), 2).")")."</td>";
				echo "<td>".($chgTotal>=0?"$".number_format($chgTotal, 2):"(-$".number_format(abs($chgTotal), 2).")")."</td>";
				echo "<td>".($creditTotal>=0?"$".number_format($creditTotal, 2):"(-$".number_format(abs($creditTotal), 2).")")."</td>";
				echo "<td>".($custRow['C-BALANCE']>=0?"$".number_format($custRow['C-BALANCE'], 2):"(-$".number_format(abs($custRow['C-BALANCE']), 2).")")."</td>";
				echo "</tr>";
				
				//$q="UPDATE `t-customer` SET `C-OPEN-BALANCE`='".mysql_real_escape_string($custRow["C-BALANCE"])."' WHERE `C-CUSTNO`='".mysql_real_escape_string($custRow["C-CUSTNO"])."'";
				//$custUpdate=mysql_query($q);
			
			}
			
			echo "<tr><td style='font-weight:bold'>Grand Totals</td>";
			echo "<td style='font-weight:bold'>".($grandInv>=0?"$".number_format($grandInv, 2):"(-$".number_format(abs($grandInv), 2).")")."</td>";
			echo "<td style='font-weight:bold'>".($grandChg>=0?"$".number_format($grandChg, 2):"(-$".number_format(abs($grandChgl), 2).")")."</td>";
			echo "<td style='font-weight:bold'>".($grandPay>=0?"$".number_format($grandPay, 2):"(-$".number_format(abs($grandPay), 2).")")."</td>";
			echo "<td style='font-weight:bold'>".($grandTotal>=0?"$".number_format($grandTotal, 2):"(-$".number_format(abs($grandTotal), 2).")")."</td>";
			
			echo "</table>";
		}
		else {
			echo "<h3>Accounts Receivable Summary Report</h3><br />All customers have 0 balance.";
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
	file_put_contents("billing/Accounts Receivable Summary Reports/".date("n-j-Y", $lastDatePHP)." to ".date("n-j-Y").".html", ob_get_contents());
	ob_end_flush();
?>