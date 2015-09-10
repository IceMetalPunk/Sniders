<?php
	$link=mysql_connect("localhost", "root", "tux898");
  $db=mysql_select_db("sniders2013", $link);
	
	echo "Adding lastInvoice column...<br />";
	// Create c-lastInvoice column
	$query="ALTER TABLE `t-customer` ADD COLUMN `c-lastInvoice` DATE NULL DEFAULT NULL";
	$q=mysql_query($query);
	
	echo "Adding lastPayment column...<br />";
	// Create c-lastPayment column
	$query="ALTER TABLE `t-customer` ADD COLUMN `c-lastPayment` DATE NULL DEFAULT NULL";
	$q=mysql_query($query);
	
	echo "Adding invoice date column to accounts receiveable table...<br />";
	// Create TAR-INV-DT column
	$query="ALTER TABLE `t-a-rec` ADD COLUMN `TAR-INV-DT` DATE NULL DEFAULT NULL";
	$q=mysql_query($query);
	
	echo "Initializing lastInvoice values...<br />";
	// Initialize c-lastInvoice column
	$query="SELECT `TAR-CUSTNO`, MAX(`TAR-POST-DT`) AS maxDate FROM `t-a-rec` WHERE `TAR-TYPE`=0 GROUP BY `TAR-CUSTNO`";
	$q=mysql_query($query);
	while ($row=mysql_fetch_assoc($q)) {
		$query="UPDATE `t-customer` SET `c-lastInvoice`='".$row["maxDate"]."' WHERE `C-CUSTNO`='".$row["TAR-CUSTNO"]."'";
		$q2=mysql_query($query);
	}
	
	echo "Initializing lastPayment values...<br />";
	// Initialize c-lastPayment column
	$query="SELECT `TAR-CUSTNO`, MAX(`TAR-POST-DT`) AS maxDate FROM `t-a-rec` WHERE (`TAR-TYPE` BETWEEN 22 AND 26) AND `TAR-TYPE`!=24 GROUP BY `TAR-CUSTNO`";
	$q=mysql_query($query);
	while ($row=mysql_fetch_assoc($q)) {
		$query="UPDATE `t-customer` SET `c-lastPayment`='".$row["maxDate"]."' WHERE `C-CUSTNO`='".$row["TAR-CUSTNO"]."'";
		$q2=mysql_query($query);
	}
	
	echo "Adding check-off columns...<br />";
	// Add TAB-CHECKOFF
	$ar=serialize(array());
	$query="ALTER TABLE `t-a-billing` ADD COLUMN `TAB-CHECKOFF` TEXT";
	$q=mysql_query($query);
	
	// Add TAR-CHECKOFF
	$query="ALTER TABLE `t-a-rec` ADD COLUMN `TAR-CHECKOFF` TEXT";
	$q=mysql_query($query);
	
	echo "Initializing check-off columns...<br />";
	// Initialize check-off columns
	$query="UPDATE `t-a-billing` SET `TAB-CHECKOFF`='".$ar."'";
	$q=mysql_query($query);
	
	$query="UPDATE `t-a-rec` SET `TAR-CHECKOFF`='".$ar."'";
	$q=mysql_query($query);
	
	echo "Adding remaining invoice/charge/payment balance columns...<br />";
	// Add TAB-REMAINING and TAR-REMAINING
	$query="ALTER TABLE `t-a-rec` ADD COLUMN `TAR-REMAINING` FLOAT NOT NULL DEFAULT 0";
	$q=mysql_query($query);
	
	$query="ALTER TABLE `t-a-billing` ADD COLUMN `TAB-REMAINING` FLOAT NOT NULL DEFAULT 0";
	$q=mysql_query($query);
	
	echo "Applying existing positive balances to last invoices...<br />";
	// Apply existing positive balances to last invoices
	$q="SELECT `C-CUSTNO`, `C-BALANCE` FROM `t-customer` WHERE `C-BALANCE`!=0";
	$query=mysql_query($q);
	
	$customers=array();
	while ($row=mysql_fetch_assoc($query)) {
		$customer=$row["C-CUSTNO"];
		$bal=$row["C-BALANCE"];
		$customers[$customer]=$bal;
		
		if ($bal>0) {
			$subq="UPDATE `t-a-billing` SET `TAB-REMAINING`=".$bal." WHERE `TAB-CUSTNO`='".$customer."' AND `TAB-ADJ-TYPE`<=1 ORDER BY `TAB-INV-DT` DESC LIMIT 1";
			$subquery=mysql_query($subq);
			
			if (mysql_affected_rows()<1) {
				$subq="UPDATE `t-a-rec` SET `TAR-REMAINING`=".$bal." WHERE `TAR-CUSTNO`='".$customer."' AND `TAR-TYPE`<=1 ORDER BY `TAR-POST-DT` DESC LIMIT 1";
				$subquery=mysql_query($subq);	
			}
		}
	}
	
	echo "Applying negative balances to last posted payments...<br />";
	// Apply payments to last invoice
  foreach ($customers as $num=>$bal) {
		if ($bal<0) {
			$q="UPDATE `t-a-rec` SET `TAR-REMAINING`='".abs($bal)."' WHERE `TAR-CUSTNO`='".$num."' AND `TAR-TYPE`>=20 AND `TAR-TYPE`<=29 AND `TAR-TYPE`!=24 ORDER BY `TAR-POST-DT` DESC LIMIT 1";
			$query=mysql_query($q);
		}
	}
	
	echo "Done";
	mysql_close($link);	
?>