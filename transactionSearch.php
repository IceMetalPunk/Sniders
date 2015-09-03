<html>
  <head>
    <title>Transaction Search</title>

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

		<!-- Ticket-lookup-specific code -->
		<script>
			$(document).ready(function() {
				$(".ticketThumb").click(function() {
					window.location=this.src;
				});
			});
		</script>
		
    <!-- Main Javascript library, including data pulled from the databases, hence why it must be in .php format -->
		<script type="text/javascript" src="invoiceScripts.php?<?php echo time(); ?>"></script>
		
  </head>
	<body onload="InitInvoice()">
		<a href="index.php"><span style="float:right"><img src="logo.png" /></span></a><br clear="both" />
				<form name="entry" method="get" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
			<table>
				<tr>
					<td>Customer</td>
					<td><input id="c_num" name="customer" placeholder="Cust No" size=6 /></td>
					<td colSpan='2'><input id="c_name" name="name" size=40 placeholder="Customer Name" /></td>
				</tr>
				<tr>
					<td>Show: <input type="radio" name="types" id="type_all" value="all" checked /><label for="type_all">All</label></td>
					<td><input type="radio" name="types" id="type_pay" value="pay" /><label for="type_pay">Payments/Credits</label></td>
					<td><input type="radio" name="types" id="type_charge" value="charge" /><label for="type_charge">Charges</label></td>
					<td><input type="radio" name="types" id="type_invoice" value="invoice" /><label for="type_invoice">Invoices</label></td>
				</tr>
				<tr>
					<td colSpan='4'><input type="radio" name="range" id="range_current" value="current" checked /> <label for="range_current">Current week (unposted transactions)</label></td>
				</tr>
				<tr>
					<td><input type="radio" name="range" id="range_custom" value="custom" /><label for="range_custom">
					Date Between <input class="date" id="fromPicker" data-linked="from" /><input type="hidden" name="from" id="from" /> &amp; </td>
					<td colSpan='3'><input class="date" id="toPicker" data-linked="to" /><input type="hidden" name="to" id="to" /></td></label>
				</tr>
			</table>
			<button type="submit" value="Lookup" name="submitted" accesskey="L"><u>L</u>ookup</button>
		</form>
		<?php
  $link=mysql_connect("localhost", "root", "tux898");
  /* Select the sniders2013 database for use later */
  $db=mysql_select_db("sniders2013", $link);
	
	$error=0;
	$errorText="";
	if (!empty($_GET['customer']) || !empty($_GET['range']) || !empty($_GET['from']) || !empty($_GET['to'])) {
		if (empty($_GET['customer'])) {
			$error|=1;
			$errorText.="You need to enter a customer.<br />";
		}
		else {
			if (empty($_GET['types'])) { $types="all"; }
			else { $types=$_GET['types']; }
			if (isset($_GET["range"]) && $_GET["range"]=="current") {
				$from=date("Y-m-d", strtotime("last Sunday"));
				$to=date("Y-m-d");
				$fromDisplay=date("n/j/Y", strtotime($from));
				$toDisplay=date("n/j/Y", strtotime($to));
			}
			else {
				if (empty($_GET['from'])) { $fromDisplay="the beginning"; $from="2014-10-01"; }
				else { $from=$_GET['from']; $fromDisplay=date("n/j/Y", strtotime($from)); }
				if (empty($_GET['to'])) { $toDisplay="all time"; $to="2099-12-31"; }
				else { $to=$_GET['to']; $toDisplay=date("n/j/Y", strtotime($to)); }
			}
			echo "<h3>Transaction history from ".$fromDisplay." to ".$toDisplay."</h3>";
			$custQ="SELECT * FROM `t-customer` WHERE `C-CUSTNO`='".$_GET['customer']."'";
			$custQuery=mysql_query($custQ);
			$custInfo=mysql_fetch_assoc($custQuery);
			
			/* TAR results, if needed */
			if (!isset($_GET["range"]) || $_GET["range"]=="custom") {
				$whereTAR="`TAR-CUSTNO`='".$_GET['customer']."'";
				
				if (!empty($_GET['from']) || !empty($_GET['to'])) { $whereTAR.=" AND (`TAR-POST-DT` BETWEEN '".mysql_real_escape_string($from)."' AND '".mysql_real_escape_string($to)."')"; }
				
				$q="SELECT * FROM `t-a-rec` WHERE ";
				$q.=$whereTAR." ORDER BY `TAR-CUSTNO`, `TAR-POST-DT`, `TAR-TYPE`";
				$query=mysql_query($q);
				
				$TARresults=array();
				while ($row=mysql_fetch_assoc($query)) {
					$n=count($TARresults);
					$TARresults[$n]=$row;
				}
				
				echo "<h10><i>Posted Transactions</i></h10>";
				echo "<table class='restab'>";
				echo "<tr><th colSpan='5'>".$custInfo["C-NAME"]." (".$custInfo["C-CUSTNO"].")</th></tr>";
				echo "<tr><th>Transaction #</th>";
				echo "<th>Date</th>";
				echo "<th>Type</th>";
				echo "<th>Description</th>";
				echo "<th>Amount</th></tr>";
				$offset=0;
					
				if (count($TARresults)>0) {
					foreach ($TARresults as $key=>$item) {
						if ($types=="all" || ($types=="pay" && $item["TAR-TYPE"]>=20 && $item["TAR-TYPE"]<=29) || ($types=="invoice" && $item["TAR-TYPE"]<=1) || ($types=="charge" && $item["TAR-TYPE"]>=10 && ($item["TAR-TYPE"]<20 || $item["TAR-TYPE"]>29))) {
							$isInvoiced=(!empty($item["TAR-INV-NO"]));
							echo "<tr>";
							echo "<td>".(!empty($item["TAR-ADJ-NUM"])?$item["TAR-ADJ-NUM"]:($item["TAR-TYPE"]<=1?$item["TAR-INV-NO"]:"&nbsp;"))."</td>";
							echo "<td>".date("n/j/Y", strtotime($item["TAR-POST-DT"]))."</td>";
							echo "<td>".($item["TAR-TYPE"]<=1?"Invoice":(($item["TAR-TYPE"]>=20 && $item["TAR-TYPE"]<=29)?"Payment":"Charge"))."</td>";
							echo "<td>".(!empty($item["TAR-ADJ-NUM"])?$item["TAR-REF-DESC"]:"&nbsp;")."</td>";
							echo "<td>".($item["TAR-AMT"]>=0?"$".number_format($item["TAR-AMT"],2):"(-$".number_format(abs($item["TAR-AMT"]),2).")");
							if (!$isInvoiced) {
								echo "*";
								$offset+=$item["TAR-AMT"];
							}
							echo "</td>";
							echo "</tr>";
						}
					}
				}
				else {
					echo "<tr><td colSPan='5'><i>No posted transactions match your search.</td></tr>";
				}
				echo "</table>";
				echo "<br/>";
			}
			
			/* TAB results */
			$whereTAB="`TAB-CUSTNO`='".$_GET['customer']."'";
			
			//if (!empty($_GET['from']) || !empty($_GET['to'])) { $whereTAB.=" AND (`TAB-INV-DT` BETWEEN '".mysql_real_escape_string($from)."' AND '".mysql_real_escape_string($to)."')"; }
			
			$custQ="SELECT * FROM `t-customer` WHERE `C-CUSTNO`='".$_GET['customer']."'";
			$custQuery=mysql_query($custQ);
			$custInfo=mysql_fetch_assoc($custQuery);
			
			$q="SELECT * FROM `t-a-billing` WHERE ";
			$q.=$whereTAB." ORDER BY `TAB-CUSTNO`, `TAB-INV-DT`, `TAB-ADJ-TYPE`";
			$query=mysql_query($q);
			
			$results=array();
			while ($row=mysql_fetch_assoc($query)) {
				$n=count($results);
				$results[$n]=$row;
			}
			
			echo "<h10><i>Unposted Transactions</i></h10>";
			echo "<table class='restab'>";
			echo "<tr><th colSpan='5'>".$custInfo["C-NAME"]." (".$custInfo["C-CUSTNO"].")</th></tr>";
			echo "<tr><td colSpan='3' style='text-align:center'>Opening Balance</td><td colSpan='2' style='text-align:center'>$".number_format($custInfo["C-OPEN-BALANCE"],2)."</td></tr>";
			echo "<tr><th>Transaction #</th>";
			echo "<th>Date</th>";
			echo "<th>Type</th>";
			echo "<th>Description</th>";
			echo "<th>Amount</th></tr>";
			$offset=0;
				
			if (count($results)>0) {
				foreach ($results as $key=>$item) {
					$isInvoiced=(!empty($item["TAB-INV-NO"]));
					echo "<tr>";
					echo "<td>".(!empty($item["TAB-ADJ-NO"])?$item["TAB-ADJ-NO"]:($item["TAB-ADJ-TYPE"]<=1?$item["TAB-INV-NO"]:"&nbsp;"))."</td>";
					if ($item["TAB-INV-DT"]=="0000-00-00") { echo "<td>*</td>"; }
					else { echo "<td>".date("n/j/Y", strtotime($item["TAB-INV-DT"]))."</td>"; }
					echo "<td>".($item["TAB-ADJ-TYPE"]<=1?"Invoice":(($item["TAB-ADJ-TYPE"]>=20 && $item["TAB-ADJ-TYPE"]<=29)?"Payment":"Charge"))."</td>";
					echo "<td>".(!empty($item["TAB-ADJ-NO"])?$item["TAB-ADJ-REF"]:"&nbsp;")."</td>";
					echo "<td>".($item["TAB-TOTAL"]>=0?"$".number_format($item["TAB-TOTAL"],2):"(-$".number_format(abs($item["TAB-TOTAL"]),2).")");
					if (!$isInvoiced) {
						echo "*";
						$offset+=$item["TAB-TOTAL"];
					}
					echo "</td>";
					echo "</tr>";
				}
			}
			else {
				echo "<tr><td colSPan='5'><i>No unposted transactions match your search.</td></tr>";
			}
			echo "<tr><th colSpan='3'>Balance</th><th colSpan='2'>".(($custInfo["C-BALANCE"]+$offset)>=0?"$".number_format($custInfo["C-BALANCE"]+$offset,2):"(-$".number_format(abs($offset+$custInfo["C-BALANCE"]),2).")")."</th></tr>";
			echo "</table>";
			echo "<br/>";
		}
	}
	
	mysql_close($link);
	
	if ($error>0) {
		echo "<b class='error' style='display:inline'>".$errorText."</b><br />";
	}
?>
		
		<small><i>Amounts marked with a * have not yet been invoiced.</i></small>
		
  </body> 
</html>