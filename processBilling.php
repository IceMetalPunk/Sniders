<?php
	
  set_time_limit(60*60*24*365);
	
  /* Connect to the MySQL-running server (on localhost, with username root and no password) */
	$link=mysql_connect("localhost", "root", "tux898");
	
	/* Select the sniders2013 database for use later */
	$db=mysql_select_db("sniders2013", $link);

	file_put_contents("billCycleProgress.txt", "0\r\n0"); // Initialize progress indicator
	require("paymentTypes.php");
	
	
	/* Get all unprocessed items from TAB, sorted by customer name */
	$q="SELECT `t-a-billing`.*, `C-BALANCE`, `C-NAME` FROM `t-a-billing`, `t-customer` WHERE `TAB-CUSTNO`=`C-CUSTNO` ORDER BY `C-NAME`, `C-CUSTNO`";
	$query=mysql_query($q);

	if (!$query || mysql_num_rows($query)<=0) {
		file_put_contents("billCycleProgress.txt", "-1\r\n0");
	}
	else {
		$n=mysql_num_rows($query);
		$index=0;
		file_put_contents("billCycleProgress.txt", "0\r\n".$n);
		
		sleep(2);
		
		/* Initialize the arrays to keep customer information and totals */
		$customers=array();
		$totals=array();
		
		/* Copy the items into TAR, keeping track of charges, credits, etc. */
		while ($row=mysql_fetch_assoc($query)) {
		
			/* If the customer hasn't been encountered yet, initialize their array for charges, credits, etc. */
			if (empty($customers[$row["TAB-CUSTNO"]])) {
				$customers[$row["TAB-CUSTNO"]]=array(
					"charges"=>0,
					"credits"=>0,
					"balance"=>$row["C-BALANCE"],
					"prevBalance"=>$row["C-BALANCE"],
					"name"=>$row["C-NAME"]
				);
			}
			
			/* Update the customer information */
			if ($row["TAB-TOTAL"]>0) {
				$customers[$row["TAB-CUSTNO"]]["charges"]+=$row["TAB-TOTAL"];
			}
			else {
				$customers[$row["TAB-CUSTNO"]]["credits"]+=abs($row["TAB-TOTAL"]);
			}
			$customers[$row["TAB-CUSTNO"]]["balance"]+=$row["TAB-TOTAL"];
			
			/* Update the totals information */
			if (empty($totals[$row["TAB-ADJ-TYPE"]])) {
				$totals[$row["TAB-ADJ-TYPE"]]=0;
			}
			$totals[$row["TAB-ADJ-TYPE"]]+=abs($row["TAB-TOTAL"]);
			
			/* Copy items into TAR */
			$q="INSERT INTO `t-a-rec` VALUES (";
			$q.="'".mysql_real_escape_string($row["TAB-CUSTNO"])."', "; // Customer number
			$q.="NOW(), '".mysql_real_escape_string($row["TAB-INV-NO"])."', "; // Posting date and invoice number
			$q.="'".mysql_real_escape_string($row["TAB-ADJ-NO"])."', "; // Adjustment number, if any
			$q.="'".mysql_real_escape_string($row["TAB-ADJ-REF"])."', "; // Adjustment text description, if any
			$q.=mysql_real_escape_string($row["TAB-ADJ-TYPE"]).", "; // Adjustment type (0=invoice, 20-29=payments, 30-39=charges, 40-49=credits/misc.)
			$q.=mysql_real_escape_string($row["TAB-TOTAL"]).")"; // Total amount of item
			$copyQuery=mysql_query($q);
			++$index;
			file_put_contents("billCycleProgress.txt", $index."\r\n".$n);
		}
		
		/* Update customer balances */
		foreach ($customers as $custno=>$data) {
			$q="UPDATE `t-customer` SET `C-BALANCE`=".mysql_real_escape_string($data["balance"])." WHERE `C-CUSTNO`='".mysql_real_escape_string($custno)."'";
			$query=mysql_query($q);
		}
		
		/* Clear TAB table */
		$q="DELETE FROM `t-a-billing`";
		$query=mysql_query($q);
		
		/* Create summary report */
		ob_start();
		ob_clean();
		
		include "cycleSummary.php";
		file_put_contents("billing/weeklySummaries/unprinted/summary_".date("n-j-Y").".html", ob_get_contents());
		
		ob_end_clean();
		
	}
		
	mysql_close($link);
		
?>