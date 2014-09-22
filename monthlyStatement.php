<html>
  <head>
    <title>Monthly Statement</title>

    <!-- Main styles -->
    <link rel="stylesheet" href="styles.css" />
		<link rel="stylesheet" href="jquery-ui.css" />
		<link rel="stylesheet" href="jquery-style.css" />
		<style>
			.restab TD { padding:4px; border: 1px solid #555555; text-align:right; font-size:12pt; }
			.restab TH { padding: 4px; background-color: #aaaaaa; font-weight:bold; border: 1px solid #000000; text-align: center; font-size:12pt;}
			.restab A { font-size:12pt; }
			TABLE.restab { border-collapse: collapse; font-size:12pt; }
		</style>

    <!-- jQuery library and its Autocomplete extension -->
    <script type="text/javascript" src="jquery-1.9.1.js"></script>
		<script type="text/javascript" src="jquery-ui.js"></script>

    <!-- Main Javascript library, including data pulled from the databases, hence why it must be in .php format -->
		<script type="text/javascript" src="invoiceScripts.php?<?php echo time(); ?>"></script>
		
  </head>
	<body>
		<a href="index.php"><span style="float:right"><img src="logo.png" /></span></a><br clear="both" />
<?php
  $link=mysql_connect("localhost", "root", "tux898");
  /* Select the sniders2013 database for use later */
  $db=mysql_select_db("sniders2013", $link);

		$q="SELECT `l-DESC` FROM `t-lookup` WHERE `l-VALUE`=996";
		$query=mysql_query($q);
		$dateRow=mysql_fetch_assoc($query);
		$lastDate=$dateRow["l-DESC"];
		$lastDatePHP=strtotime($lastDate);
		$nextMonth=((date("n", $lastDatePHP)+1)%12)+1;
		if ($nextMonth==1) { $nextMonth=12; }
		else { --$nextMonth; }
		$whichYear=(date("n", $lastDatePHP)>=11?date("Y", $lastDatePHP)+1:date("Y", $lastDatePHP));
		$lastDatePHP=mktime(0,0,0,$nextMonth, $whichYear);

		$q="SELECT * FROM `t-customer` WHERE `C-BALANCE`!=0 ORDER BY `C-NAME`, `C-CUSTNO`";
		$query=mysql_query($q);

		if (mysql_num_rows($query)>0) {
			echo "<h3>Monthly Statement for ".date("F Y", $lastDatePHP)."</h3>";
			echo "<table class='restab'><tr><th>Customer Name</th><th>Customer #</th><th>Opening Balance</th><th>Invoice Total</th><th>Adjustment Total</th><th>Balance</th></tr>";
			
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
				echo "<tr><td>".$custRow["C-NAME"]."</td><td>".$custRow["C-CUSTNO"]."</td>";
				echo "<td>".($custRow['C-OPEN-BALANCE']>=0?"$".number_format($custRow['C-OPEN-BALANCE'], 2):"(-$".number_format($custRow['C-OPEN-BALANCE'], 2).")")."</td>";
				echo "<td>".($invTotal>=0?"$".number_format($invTotal, 2):"(-$".number_format($invTotal, 2).")")."</td>";
				echo "<td>".($adjTotal>=0?"$".number_format($adjTotal, 2):"(-$".number_format($adjTotal, 2).")")."</td>";
				echo "<td>".($custRow['C-BALANCE']>=0?"$".number_format($custRow['C-BALANCE'], 2):"(-$".number_format($custRow['C-BALANCE'], 2).")")."</td>";
				echo "</tr>";
				
				$q="UPDATE `t-customer` SET `C-OPEN-BALANCE`='".mysql_real_escape_string($custRow["C-BALANCE"])."' WHERE `C-CUSTNO`='".mysql_real_escape_string($custRow["C-CUSTNO"])."'";
				$custUpdate=mysql_query($q);
			
			}
			echo "</table>";
		}
		else {
			echo "<h3>Monthly Statement for ".date("F Y", $lastDatePHP)."</h3><br />All customers have 0 balance.";
		}
		
		$q="UPDATE `t-lookup` SET `l-DESC`=NOW() WHERE `l-VALUE`=996";
		$query=mysql_query($q);

	mysql_close($link);
?>
	<script>
		document.body.onload=function() {
			window.print();
		}
	</script>
  </body> 
</html>