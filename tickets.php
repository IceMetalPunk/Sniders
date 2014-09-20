<html>
  <head>
    <title>Ticket Lookup</title>

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
		<?php
  $link=mysql_connect("localhost", "root", "tux898");
  /* Select the sniders2013 database for use later */
  $db=mysql_select_db("sniders2013", $link);
	
	if (!empty($_GET['customer']) || !empty($_GET['reference']) || !empty($_GET['ticket'])) {
		$whereclause="";
		if (!empty($_GET['customer'])) { $whereclause.=" AND `C-CUSTNO`='".$_GET['customer']."'"; }
		if (!empty($_GET['reference'])) { $whereclause.=" AND `W-REF` LIKE '%".$_GET['reference']."%'"; }
		if (!empty($_GET['ticket'])) { $whereclause.=" AND `W-TKT` LIKE '%".$_GET['ticket']."%'"; }
		$q="SELECT * FROM `t-customer`, `t-work` WHERE `C-CUSTNO`=`W-CUSTNO`";
		$q.=$whereclause." ORDER BY `C-CUSTNO`, `C-NAME`";
		$query=mysql_query($q);
		
		$results=array();
		while ($row=mysql_fetch_assoc($query)) {
			$n=count($results);
			$results[$n]=$row;
		}
		
		if (count($results)<=0) {
			echo "<b>No tickets matched your search.</b>";
		}
		else {
			echo "<table class='restab'><tr><th>Customer Name</th>";
			echo "<th>Customer #</th>";
			echo "<th>Ticket #</th>";
			echo "<th>Reference</th>";
			echo "<th>Order Date</th>";
			echo "<th>Use Date</th>";
			echo "<th>Invoiced?</th>";
			echo "<th>Ticket</th>";
			echo "<th>&nbsp;</th></tr>";
			
			foreach ($results as $key=>$cust) {
				echo "<tr><td style='text-align:left'>".$cust["C-NAME"]."</td>";
				if ($cust["C-CUSTNO"]<70000) { echo "<td>".$cust["C-CUSTNO"]."</td>"; }
				else { echo "<td>99999 (In-House)</td>"; }
				echo "<td>".$cust["W-TKT"]."-".$cust["W-TKT-SUB"]."</td>";
				echo "<td>".$cust["W-REF"]."</td>";
				echo "<td>".date("j/n/Y", strtotime($cust["W-ORDER-DT"]))."</td>";
				echo "<td>".date("j/n/Y", strtotime($cust["W-USE-DT"]))."</td>";
				$inv=!($cust["W-INV-NO"]=="000000" || $cust["W-INV-NO"]=="");
				echo "<td>".($inv?"Yes":"No")."</td>";
				echo "<td><img src='tickets/Complete/ticket-".$cust["W-TKT"]."-".$cust["W-TKT-SUB"].".png' class='ticketThumb' /></td>";
				echo "<td><a href='entry.php?edit=".$cust["W-TKT"]."'>Edit</a></td>";
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
					<td>Ticket</td>
					<td><input name="reference" placeholder="Reference" /></td>
					<td><input name="ticket" placeholder="Ticket #" /></td>
				</tr>
			</table>
			<button type="submit" value="Lookup" name="submitted" accesskey="L"><u>L</u>ookup</button>
		</form>
		
  </body> 
</html>