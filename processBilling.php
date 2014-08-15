<?php
	
  set_time_limit(60*60*24*365);
	
  /* Connect to the MySQL-running server (on localhost, with username root and no password) */
	$link=mysql_connect("localhost", "root", "");
	
	/* Select the sniders2013 database for use later */
	$db=mysql_select_db("sniders2013", $link);
	
	$billNums=array();
	$custBills=array();
	
	/* Generate an individual bill for a customer */
	function MakeBill($custRow) {
		global $cycleDate;
		$customer=$custRow["TAB-CUSTNO"];
		
		/* Get the customer's current balance */
		$q="SELECT `AS-BAL` FROM `t-a-summary` WHERE `AS-CUSTNO`='".mysql_real_escape_string($customer)."' AND `AS-BILL-NO`='CLOSING'";
		$query=mysql_query($q);
		if ($query && mysql_num_rows($query)>0) {
			$openingBalance=mysql_fetch_assoc($query);
			$openingBalance=$openingBalance["AS-BAL"];
		}
		
		/* If there's no closing balance recorded, check for a monthly opening balance */
		else {
			$q="SELECT `AS-BAL` FROM `t-a-summary` WHERE `AS-CUSTNO`='".mysql_real_escape_string($customer)."' AND `AS-BILL-NO`='OPENING'";
			$query=mysql_query($q);
			if ($query && mysql_num_rows($query)>0) {
				$openingBalance=mysql_fetch_assoc($query);
				$openingBalance=$openingBalance["AS-BAL"];
			}
			else {
				$openingBalance=0;
			}
		}
		
		/* Get all unbilled items from TAB */
		$q="SELECT * FROM `t-a-billing`, `t-customer` WHERE `C-CUSTNO`=`TAB-CUSTNO` AND `TAB-CUSTNO`='".$customer."' AND `TAB-BILL-NO`='' AND `TAB-ADJ-TYPE`!=0 ORDER BY `TAB-ADJ-TYPE`, `TAB-INV-DT`";
		$query=mysql_query($q);
		
		if (!$query || mysql_num_rows($query)<=0) {
			return false;
		}
		
		/* Begin bill generation */
		$billNum=GenerateBillNumber($custRow);
		file_put_contents("debug.txt", "Bill ".$billNum);
		ob_start();
		ob_clean();
		
		include "billHeader.php";
		$onInvoices=true;
		$balance=$openingBalance;
		$credits=0;
		$charges=0;
		while ($row=mysql_fetch_assoc($query)) {
		
			/* If we're printing invoice numbers... */
			if ($onInvoices) {
			
				/* Check if we're now on adjustments, and if so, print the header for them and this item itself */
				if ($row["TAB-ADJ-TYPE"]>1) {
					$onInvoices=false;
					echo "<tr><th>Item Date</th><th colspan='3'>Item Number</th><th>Charges</th><th colspan='2'>Credits</th>";
					$charge=($row["TAB-ADJ-TYPE"]>=30 && $row["TAB-ADJ-TYPE"]<40);
					$balance+=$row["TAB-TOTAL"];
					if ($charge) { $charges+=$row["TAB-TOTAL"]; }
					else { $credits+=$row["TAB-TOTAL"]; }
					echo "<tr><td>".$row["TAB-INV-DT"]."</td><td colspan='3'>".$row["TAB-INV-NO"]."</td><td class='right'>".($charge?"$".number_format($row["TAB-TOTAL"],2):"")."</td><td class='right'>".($charge?"":"(-$".number_format(abs($row["TAB-TOTAL"]),2).")")."</td>";
				}
				
				/* If we're still on invoices, output those */
				else {
					$balance+=$row["TAB-TOTAL"];
					$charges+=$row["TAB-TOTAL"];
					echo "<tr><td>".$row["TAB-INV-DT"]."</td><td>".$row["TAB-INV-NO"]."</td><td class='right'>$".number_format($row["TAB-SUBTOTAL"],2)."</td><td class='right'>".$row["TAB-DSCOUNT"]."%</td><td class='right'>$".number_format($row["TAB-TOTAL"],2)."</td><td style='border-left:0px'>&nbsp;</td>\r\n";
				}
			}
			
			/* Else if we're printing adjustments, output those */
			else {
				$charge=($row["TAB-ADJ-TYPE"]>=30 && $row["TAB-ADJ-TYPE"]<40);
				$balance+=$row["TAB-TOTAL"];
				if ($charge) { $charges+=$row["TAB-TOTAL"]; }
				else { $credits+=$row["TAB-TOTAL"]; }
				echo "<tr><td>".$row["TAB-INV-DT"]."</td><td colspan='3'>".$row["TAB-INV-NO"]."</td><td class='right'>".($charge?"$".number_format($row["TAB-TOTAL"],2):"")."</td><td class='right'>".($charge?"":"(-$".number_format(abs($row["TAB-TOTAL"]),2).")")."</td>";
			}
			
					
			/* Copy info to TAR (bill history table) */
			$q="INSERT INTO `t-a-rec` VALUES (";
			$q.="'".mysql_real_escape_string($customer)."', "; // Customer number
			$q.="NOW(), "; // Posted date
			$q.="'".mysql_real_escape_string($cycleDate)."', "; // End-of-billing-cycle date
			$q.="'".mysql_real_escape_string($billNum)."', "; // Bill number
			$q.="'".mysql_real_escape_string($row["TAB-INV-NO"])."', "; // Invoice/Adjustment number
			$q.="'".mysql_real_escape_string($row["TAB-ADJ-REF"])."', "; // Adjustment reference, if any
			$q.=mysql_real_escape_string($row["TAB-ADJ-TYPE"]).", "; // Adjustment type, or 0=bill, 1=invoice
			$q.=mysql_real_escape_string($row["TAB-TOTAL"]).")";
			$TARQuery=mysql_query($q);
		}
		
		/* Output the final balance */
		echo "<tr><td colspan='6'>&nbsp;</td></tr><tr><th>Balance</th><th colspan='5' class='right'>".($balance<0?"-$".abs($balance):"$".$balance)."</th></tr>";
		
		include "billFooter.php";
		file_put_contents("billing/bills/unprinted/bill_".$billNum.".html", ob_get_contents());
		ob_end_clean();
		
		/* Remove this customer's items from TAB (unbilled items table) */
		$q="DELETE FROM `t-a-billing` WHERE `TAB-CUSTNO`='".mysql_real_escape_string($customer)."'";
		$query=mysql_query($q);
		
		/* Insert info into TAS (bill summary table) */
		$q="INSERT INTO `t-a-summary` VALUES (";
		$q.="'".mysql_real_escape_string($customer)."', "; // Customer number
		$q.=mysql_real_escape_string($balance).", "; // Balance
		$q.="NOW(), "; // Bill date
		$q.="'".mysql_real_escape_string($billNum)."')"; // Bill number
		$query=mysql_query($q);
		
		/* Update the customer's closing balance in TAS */
		$q="UPDATE `t-a-summary` SET `AS-BAL`=".mysql_real_escape_string($balance).", `AS-DT`=NOW() WHERE `AS-CUSTNO`='".mysql_real_escape_string($customer)."' AND `AS-BILL-NO`='CLOSING'";
		$query=mysql_query($q);
		
		/* If it wasn't in the table before, then we insert it instead */
		if (mysql_affected_rows($query)<=0) {
			$q="INSERT INTO `t-a-summary` VALUES (";
			$q.="'".mysql_real_escape_string($customer)."', "; // Customer number
			$q.=mysql_real_escape_string($balance).", "; // Balance
			$q.="NOW(), "; // Date of last update
			$q.="'CLOSING')"; // This is the closing balance, so there's no bill number; instead it's marked as CLOSING
			$query=mysql_query($q);
		}
		
	}
	
	/* Process all the unbilled items and generate bills for them */
	function MakeBills() {
		/* Get all the bills that need to be made this cycle */
		$q="SELECT DISTINCT `TAB-CUSTNO`, `t-customer`.* FROM `t-a-billing`, `t-customer`  WHERE `C-CUSTNO`=`TAB-CUSTNO` AND `TAB-BILL-NO`='' ORDER BY `C-NAME`";
		$query=mysql_query($q);
		
		if (!$query || mysql_num_rows($query)<=0) {
			file_put_contents("billCycleProgress.txt", "-1\r\n0");
		}
		else {
			$numBills=mysql_num_rows($query);
			$onBill=0;
			while ($row=mysql_fetch_assoc($query)) {
				MakeBill($row);
				++$onBill;
				file_put_contents("billCycleProgress.txt", $onBill."\r\n".$numBills);
			}
		}
	}
	
	function GenerateBillNumber($custRow) {
		$q="SELECT `l-DESC` FROM `t-lookup` WHERE ";

		if ($custRow["C-BILLING-METH"]==0) {
			$q.="`l-VALUE`=998";
			$query=mysql_query($q);
			
			$row=mysql_fetch_assoc($query);
			
			$num=$row["l-DESC"];

			/* Update to new billing number */
			$newNum=$num+1;
			
			if ($newNum>99999) {$newNum=10001; }
			$q="UPDATE `t-lookup` SET `l-DESC`='".$newNum."' WHERE `l-VALUE`=998";
			$query=mysql_query($q);
			
			return $num;
		}
		else {
			$q.="`l-VALUE`=997";
			$query=mysql_query($q);
			$row=mysql_fetch_assoc($query);
			$num=$row["l-DESC"];
			
			/* Update to new invoice number */
			$newNum=explode("-", $num);
			$numPart=$newNum[1]+1;
			if ($numPart>9999) { $numPart="0001"; }
			while (strlen($numPart)<4) { $numPart="0".$numPart; }
			$newNum="I".date("y")."-".$numPart;
			
			$q="UPDATE `t-lookup` SET `l-DESC`='".$newNum."' WHERE `l-VALUE`=997";
			$query=mysql_query($q);
			
			return $num;
		}
		
	}

	/* GET CYCLE END DATE FROM LOOKUP TABLE */
	$q="SELECT `l-DESC` FROM `t-lookup` WHERE `l-VALUE`=301";
	$query=mysql_query($q);
	$cycleDate=mysql_fetch_assoc($query);
	$cycleDate=$cycleDate["l-DESC"];
	
	/* Make the bills */
	@unlink("billCycleProgress.txt");
	MakeBills();
		
	mysql_close($link);
		
?>