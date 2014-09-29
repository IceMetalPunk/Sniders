<html>
  <head>
    <title>Customer Lookup</title>

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
	
	if (!empty($_POST['submitted']) && $_POST['submitted']="Lookup") {
		$whereclause="WHERE ";
		foreach ($_POST as $key=>$val) {
			if (!empty($_POST[$key]) && $key!="submitted") {
				if ($key=="C-CUSTNO") { $oper="="; $end=""; }
				else { $oper=" LIKE "; $end="%"; }
				if ($whereclause!="WHERE ") { $whereclause.=" AND "; }
				$whereclause.="`".mysql_real_escape_string($key)."`".$oper."'".$end.mysql_real_escape_string($val).$end."'";
			}
		}
		$q="SELECT * FROM `t-customer` LEFT JOIN `t-work` ON `C-CUSTNO`=`W-CUSTNO`".$whereclause." GROUP BY `C-CUSTNO`";
		$query=mysql_query($q);
		
		$results=array();
		while ($row=mysql_fetch_assoc($query)) {
			$q="SELECT SUM(CASE WHEN `W-INV-NO`='0000000' THEN 1 ELSE 0 END) as UninvNum, SUM(CASE WHEN `W-INV-NO`!='0000000' THEN 1 ELSE 0 END) as InvNum FROM `t-work`";
			$q.=" WHERE `W-CUSTNO`='".mysql_real_escape_string($row["C-CUSTNO"])."'";
			$q.=" AND ((to_days(`t-work`.`W-USE-DT`) - to_days((select `t-lookup`.`l-DESC` from `t-lookup` where (`t-lookup`.`l-VALUE` = 301)))) <= 0)";
			$q.=" GROUP BY `W-CUSTNO`";
			$countquery=mysql_query($q);
			if (mysql_num_rows($countquery)<=0) {
				$row["NumInv"]=0;
				$row["NumUninv"]=0;
			}
			else {
				$counts=mysql_fetch_assoc($countquery);
				$row["NumInv"]=$counts["InvNum"];
				$row["NumUninv"]=$counts["UninvNum"];
			}
			
			$total=0;
			$credits=0;
			$disc=$row["C-DISCNT-PCT"];
			$balance=$row["C-BALANCE"];
			
			/* INV */
			$q="SELECT *, SUM(`TAB-TOTAL`) as `TAB-SUM` FROM `t-a-billing` WHERE `TAB-CUSTNO`='".mysql_real_escape_string($row['C-CUSTNO'])."' AND `TAB-INV-NO`!='' AND (`TAB-ADJ-TYPE`=0 OR `TAB-ADJ-TYPE` BETWEEN 30 and 39) GROUP BY `TAB-INV-NO` ORDER BY `TAB-INV-DT` ASC";
			$invquery=mysql_query($q);
			
			while ($invrow=mysql_fetch_assoc($invquery)) {
				$total+=$invrow["TAB-SUM"];
			}
			
			/* NEW */
			$q="SELECT * FROM `v-a-invoice` WHERE `W-CUSTNO`='".mysql_real_escape_string($row['C-CUSTNO'])."'";
			$subquery=mysql_query($q);
			
			$subtotal=0;
			
			while ($subrow=mysql_fetch_assoc($subquery)) {
				$subtotal+=$subrow["W-AMT"];
			}
			
			/* ADJ */
			$q="SELECT * FROM `t-a-billing` WHERE `TAB-CUSTNO`='".mysql_real_escape_string($row['C-CUSTNO'])."' AND `TAB-INV-NO`='' AND `TAB-ADJ-TYPE`>9 AND `TAB-ADJ-TYPE` BETWEEN 30 and 39";
			$adjquery=mysql_query($q);
			while ($adjrow=mysql_fetch_assoc($adjquery)) {
				$subtotal+=$adjrow["TAB-AMT"];
			}
			
			/* CRED */
			$q="SELECT * FROM `t-a-billing` WHERE `TAB-CUSTNO`='".mysql_real_escape_string($row['C-CUSTNO'])."' AND `TAB-ADJ-TYPE`>9 AND `TAB-ADJ-TYPE` NOT BETWEEN 30 and 39";
			$credquery=mysql_query($q);
			while ($credrow=mysql_fetch_assoc($credquery)) {
				$credits+=abs($credrow["TAB-TOTAL"]);
			}
			
			$total+=$subtotal*(100-$disc)/100;
			$balance+=$total-$credits;
			$row["Balance"]=$balance;
			
			$n=count($results);
			$results[$n]=$row;
		}
		
		if (count($results)<=0) {
			echo "<b>No users matched your search.</b>";
		}
		else {
			echo "<table class='restab'><tr><th>Customer #</th><th>Name</th><th>Address</th><th>Opening Balance</th><th>Current Balance</th><th>Invoiced Items</th><th>Uninvoiced Items</th><th>Work Tickets</th></tr>";
			foreach ($results as $key=>$cust) {
				echo "<tr><td style='text-align:left'>".$cust["C-CUSTNO"]."</td>";
				echo "<td>".$cust["C-NAME"]."</td>";
				echo "<td>".$cust["C-ADDR1"];
				if (!empty($cust["C-ADDR2"])) { echo ", ".$cust["C-ADDR2"]; }
				echo ", ".$cust["C-CITY"].", ".$cust["C-STATE"]." ".$cust["C-ZIP"]."</td>";
				echo "<td>$".number_format($cust["C-BALANCE"], 2)."</td>";
				echo "<td>$".number_format($cust["Balance"], 2)."</td>";
				echo "<td>".$cust["NumInv"]."</td>";
				echo "<td>".$cust["NumUninv"]."</td>";
				echo "<td><a href='tickets.php?customer=".$cust["C-CUSTNO"]."'>Lookup Tickets</a></td>";
			}
			echo "</table>";
		}
		echo "<br/>";
	}
	
	mysql_close($link);
?>
		<form name="entry" method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
			<table>
				<tr>
					<td>Customer</td>
					<td><input id="c_num" name="C-CUSTNO" placeholder="Cust No" size=6 /></td>
					<td colspan='2'><input id="c_name" name="C-NAME" size=40 placeholder="Customer Name" /></td>
				</tr>
				<tr>
					<td>Address</td>
					<td><input name="C-CITY" placeholder="City"/></td>
					<td><input name="C-STATE" size=5 maxlength=2 style="text-transform: uppercase" placeholder="State" /></td>
					<td><input name="C-ZIP" placeholder="Zip" size=6 maxlength=5/></td>
				</tr>
				<tr>
					<td>Shipping Address</td>
					<td><input name="C-SCITY" placeholder="City"/></td>
					<td><input name="C-SSTATE" size=5 maxlength=2 style="text-transform: uppercase" placeholder="State" /></td>
					<td><input name="C-SZIP" placeholder="Zip" size=6 maxlength=5/></td>
				</tr>
			</table>
			<button type="submit" value="Lookup" name="submitted" accesskey="L"><u>L</u>ookup</button>
		</form>
		
  </body> 
</html>