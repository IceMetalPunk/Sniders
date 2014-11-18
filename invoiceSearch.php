<html>
  <head>
    <title>Invoice Lookup</title>

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
	<body onload="InitInvoice()">
		<a href="index.php"><span style="float:right"><img src="logo.png" /></span></a><br clear="both" />
		<?php
  $link=mysql_connect("localhost", "root", "tux898");
  /* Select the sniders2013 database for use later */
  $db=mysql_select_db("sniders2013", $link);
	
	if (!empty($_GET['customer']) || !empty($_GET['invoice']) || !empty($_GET['from']) || !empty($_GET['to'])) {
		$where1="";
		$where2="";
		if (!empty($_GET['customer'])) {
			$where1.=" AND `C-CUSTNO`='".$_GET['customer']."'";
			$where2.=" AND `C-CUSTNO`='".$_GET['customer']."'";
		}
		if (!empty($_GET['invoice'])) {
			$where1.=" AND `TAB-INV-NO` LIKE '%".$_GET['invoice']."%'";
			$where2.=" AND `TAR-INV-NO` LIKE '%".$_GET['invoice']."%'";
		}
		$from=$_GET['from'];
		if (empty($from)) { $from="2014-10-01"; }
		$to=$_GET['to'];
		if (empty($to)) { $to="2099-12-31"; }
		
		if (!empty($_GET['from']) || !empty($_GET['to'])) {
			$where1.=" AND (`TAB-INV-DT` BETWEEN '".mysql_real_escape_string($from)."' AND '".mysql_real_escape_string($to)."')";
			$where2.=" AND (`TAR-POST-DT` BETWEEN '".mysql_real_escape_string($from)."' AND '".mysql_real_escape_string($to)."')";
		}
		
		$results=array();
		
		$q="SELECT * FROM `t-customer`, `t-a-billing` WHERE `TAB-CUSTNO`=`C-CUSTNO` AND `TAB-ADJ-TYPE`<=9";
		$q.=$where1." ORDER BY `C-CUSTNO`, `C-NAME`";
		$query=mysql_query($q);
		while ($row=mysql_fetch_assoc($query)) {
			$n=count($results);
			$results[$n]=$row;
		}
		
		$q="SELECT * FROM `t-customer`, `t-a-rec` WHERE `TAR-CUSTNO`=`C-CUSTNO` AND `TAR-TYPE`<=9";
		$q.=$where2." ORDER BY `C-CUSTNO`, `C-NAME`";
		$query=mysql_query($q);
		while ($row=mysql_fetch_assoc($query)) {
			$n=count($results);
			$results[$n]=$row;
		}
		
		if (count($results)<=0) {
			echo "<b>No invoices matched your search.</b>";
		}
		else {
			echo "<table class='restab'><tr><th>Customer Name</th>";
			echo "<th>Customer #</th>";
			echo "<th>Invoice #</th>";
			echo "<th>Date</th>";
			echo "<th>Posted?</th>";
			echo "<th>Total</th>";
			echo "<th>&nbsp;</th></tr>";
			
			foreach ($results as $key=>$cust) {
				echo "<tr><td style='text-align:left'>".$cust["C-NAME"]."</td>";
				if ($cust["C-CUSTNO"]<70000) { echo "<td>".$cust["C-CUSTNO"]."</td>"; }
				else { echo "<td>99999 (In-House)</td>"; }
				if (!empty($cust["TAR-INV-NO"])) { $num=$cust["TAR-INV-NO"]; }
				else { $num=$cust["TAB-INV-NO"]; }				
				echo "<td>".$num."</td>";
				
				if (!empty($cust["TAR-POST-DT"])) {
					echo "<td>".date("n/j/Y", strtotime($cust["TAR-POST-DT"]))."</td>";
					echo "<td>Yes</td>";
				}
				else {
					echo "<td>".date("n/j/Y", strtotime($cust["TAB-INV-DT"]))."</td>";
					echo "<td>No</td>";
				}
				if (!empty($cust["TAR-AMT"])) { echo "<td>$".number_format($cust["TAR-AMT"], 2)."</td>"; }
				else { echo "<td>$".number_format($cust["TAB-TOTAL"], 2)."</td>"; }
				echo "<td><a href='billing/invoices/".$num.".html'>View/Print</a></td>";
				
				echo "</tr>";
			}
			echo "</table>";
		}
		echo "<br/>";
	}
	
	mysql_close($link);
?>
		<form name="entry" method="get" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
			<table>
				<tr>
					<td>Customer</td>
					<td><input id="c_num" name="customer" placeholder="Cust No" size=6 /></td>
					<td colspan='2'><input id="c_name" name="name" size=40 placeholder="Customer Name" /></td>
				</tr>
				<tr>
					<td>Invoice #</td>
					<td colspan='2'><input name="invoice" /></td>
				</tr>
				<tr>
					<td>Post/Invoice Date Between</td>
					<td><input class="date" id="fromPicker" data-linked="from" /><input type="hidden" name="from" id="from" /> &amp; </td>
					<td><input class="date" id="toPicker" data-linked="to" /><input type="hidden" name="to" id="to" /></td>
				</tr>
			</table>
			<button type="submit" value="Lookup" name="submitted" accesskey="L"><u>L</u>ookup</button>
		</form>
		
  </body> 
</html>