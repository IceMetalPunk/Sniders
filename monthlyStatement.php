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
		</script>
		
  </head>
	<body>
		<a href="index.php"><span style="float:right"><img src="logo.png" /></span></a><br clear="both" />
		<div id="updateResponse" class="message" style="font-size:10pt"></div>
		<button onclick="SetDate()" accesskey="D">Reset Accounts Receivable <u>D</u>ate</button>
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

		if (mysql_num_rows($query)>0) {
			//echo "<h3>Monthly Statement for ".date("F Y", $lastDatePHP)."</h3>";
			echo "<h3>Accounts Receivable Summary Report For ".date("n/j/Y", $lastDatePHP)." - ".date("n/j/Y")."</h3>";
			echo "<table class='restab'><tr><th>Customer Name</th><th>Previous Balance</th><th>Invoice Total</th><th>Adjustment Total</th><th>Balance</th></tr>";
			
			while ($custRow=mysql_fetch_assoc($query)) {
				$q="SELECT SUM(`TAR-AMT`) AS amt, `TAR-TYPE` FROM `t-a-rec` WHERE `TAR-CUSTNO`='".mysql_real_escape_string($custRow["C-CUSTNO"])."' AND `TAR-POST-DT` BETWEEN '".mysql_real_escape_string($lastDate)."' AND NOW() GROUP BY `TAR-TYPE`";
				$invTotal=0;
				$adjTotal=0;
				$subquery=mysql_query($q);
				if (mysql_num_rows($subquery)>0) {
					while ($info=mysql_fetch_assoc($subquery)) {
						if ($info["TAR-TYPE"]==0) { $invTotal+=$info["amt"]; }
						else { $adjTotal+=$info["amt"]; }
					}	
				}
				echo "<tr><td>".$custRow["C-NAME"]."</td>";//<td>".$custRow["C-CUSTNO"]."</td>";
				echo "<td>".($custRow['C-OPEN-BALANCE']>=0?"$".number_format($custRow['C-OPEN-BALANCE'], 2):"(-$".number_format($custRow['C-OPEN-BALANCE'], 2).")")."</td>";
				echo "<td>".($invTotal>=0?"$".number_format($invTotal, 2):"(-$".number_format($invTotal, 2).")")."</td>";
				echo "<td>".($adjTotal>=0?"$".number_format($adjTotal, 2):"(-$".number_format($adjTotal, 2).")")."</td>";
				echo "<td>".($custRow['C-BALANCE']>=0?"$".number_format($custRow['C-BALANCE'], 2):"(-$".number_format($custRow['C-BALANCE'], 2).")")."</td>";
				echo "</tr>";
				
				//$q="UPDATE `t-customer` SET `C-OPEN-BALANCE`='".mysql_real_escape_string($custRow["C-BALANCE"])."' WHERE `C-CUSTNO`='".mysql_real_escape_string($custRow["C-CUSTNO"])."'";
				//$custUpdate=mysql_query($q);
			
			}
			echo "</table>";
		}
		else {
			echo "<h3>Accounts Receivable Summary Report For ".date("n/j/Y", $lastDatePHP)." - ".date("n/j/Y")."</h3><br />All customers have 0 balance.";
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