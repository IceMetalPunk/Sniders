<?php
	$link=mysql_connect("localhost", "root", "");
  $db=mysql_select_db("sniders2013", $link);
	
	$q="SELECT DISTINCT `W-CUSTNO` FROM `v-a-invoice`"; // Query to get the list of customers that have uninvoiced items.
	$query=mysql_query($q);
	
	// If there are no uninvoiced items, say that and we're done.
	if ($query===FALSE || mysql_num_rows($query)<=0) {
		file_put_contents("batchInvoiceProgress.txt", "-1\r\nNo items to be invoiced.");
	}
	
	// If there ARE uninvoiced items, it's time to invoice them.
	else {
		$num=mysql_num_rows($query);
		$n=0;
		file_put_contents("batchInvoiceProgress.txt", $n."\r\n".$num); // Initialize the progress to 0%.
		while ($row=mysql_fetch_assoc($query)) { // Get the next customer with uninvoiced items
			$customer=$row["W-CUSTNO"];
			Invoice($customer); // Perform the invoicing for that customer
			file_put_contents("batchInvoiceProgress.txt", ++$n."\r\n".$num); // Update the progress
		}
	}
	
	/* Function to generate new invoice numbers */
	function GenerateInvoiceNumber() {
	
	  // Get current invoice number
		$q="SELECT `l-DESC` FROM `t-lookup` WHERE `l-VALUE`=997";
		$query=mysql_query($q);
		$row=mysql_fetch_assoc($query);
		$num=$row["l-DESC"];

		// Update to new invoice number
		$newNum=explode("-", $num);
		$numPart=$newNum[1]+1;
		if ($numPart>9999) { $numPart="0001"; }
		while (strlen($numPart)<4) { $numPart="0".$numPart; }
		$newNum="I".date("y")."-".$numPart;

		$q="UPDATE `t-lookup` SET `l-DESC`='".$newNum."' WHERE `l-VALUE`=997";
		$query=mysql_query($q);
				
		return $num;
	}
	
	/* Function to get customer data */
	function GetCustomer($customer) {
		$q="SELECT * FROM `t-customer` WHERE `C-CUSTNO`='".mysql_real_escape_string($customer)."'";
		$query=mysql_query($q);
		
		if (!$query || mysql_num_rows($query)<=0) { return FALSE; }
		else {
			return mysql_fetch_assoc($query);
		}
	}
	
	function Invoice($customer) {
		$customerData=GetCustomer($customer); // Grab customer data

		$invNum=GenerateInvoiceNumber(); // Generate a new invoice number
		$now=date("n/j/Y"); // Get the current date in a human-pleasing format
		
		// Using output buffers here to store all the HTML, so that it can be echoed normally, but ultimately put into a file instead of displayed
		ob_start();
		ob_clean();
		
		include "invoiceHeader.php"; // Write the top of the page
		echo "<h3>Invoice #".$invNum." as of ".$now."</h3>"; // Invoice header

		$num=0;
		$subtotal=0;
		$total=0;
		
		/* Get previous invoices for the current cycle and, if any exist, write a consolidation of them */
		$q="SELECT * FROM `t-a-billing` WHERE `TAB-CUSTNO`='".mysql_real_escape_string($customer)."' AND `TAB-ADJ-TYPE`=1 ORDER BY `TAB-INV-NO`, `TAB-INV-DT`";
		$query=mysql_query($q);
		if ($query && mysql_num_rows($query)>0) {
			echo "<tr><th colspan='2'>Invoice Date</th><th colspan='2'>Invoice Number</th><th colspan='2'>Invoice Total</th></tr>";
			while ($row=mysql_fetch_assoc($query)) {
				echo "<tr><td colspan='2'>".$row["TAB-INV-DT"]."</td><td colspan='2'>".$row["TAB-INV-NO"]."</td><td colspan='2'>$".number_format($row["TAB-TOTAL"],2)."</td></tr>";
				$total+=$row["TAB-TOTAL"];
			}
			if ($invNum!="RECAP") { echo "<tr><td colspan='5'>&nbsp;</td></tr>"; }
		}
		
		/* List new items */
		echo "<tr><th>Invoice Date</th><th>Ticket Number</th><th>Use Date</th><th>Reference</th><th>Amount</th></tr>";
		$q="SELECT * FROM `v-a-invoice` WHERE `W-CUSTNO`='".mysql_real_escape_string($customer)."'";
		$query=mysql_query($q);
		
		$subtotal=0;
		
		while ($row=mysql_fetch_assoc($query)) {
			echo "<tr><td>".$now."</td><td>".$row["W-TKT"]."-".$row["W-TKT-SUB"]."</td><td>".date("n/j/Y" ,strtotime($row["W-USE-DT"]))."</td><td>".$row["W-REF"]."</td><td>$".number_format($row["W-AMT"], 2)."</td>";
			$subtotal+=$row["W-AMT"];
		}
		
		/* Display subtotals and discounts etc. */
		$disc=$customerData["C-DISCNT-PCT"];
		echo "<tr style='border-top:2px solid #000000'><th>Subtotal</th><td colspan='4' style='text-align:right'>$".number_format($subtotal,2)."</td></tr>";
		if ($disc>0) {
			echo "<tr><th>Discount</th><td colspan='4' style='text-align:right'>".$disc."%</td></tr>";
		}
		$total+=$subtotal*(100-$disc)/100;
		
		/* Display totals */
		echo "<tr><td colspan='5'>&nbsp;</td></tr>";
		echo "<tr style='border-top:2px solid #000000'><th>Total</th><td colspan='4' style='text-align:right'>$".number_format($total,2)."</td></tr>";
		
		/* Insert invoice into billing table */
		$q="INSERT INTO `t-a-billing` VALUES (";
		$q.="'".mysql_real_escape_string($customer)."', "; // Customer number
		$q.="0, '', "; // Bill date and number -- Initially blank prior to billing
		$q.="NOW(), '".mysql_real_escape_string($invNum)."', "; // Invoice date and number
		$q.="1, '', "; // Adjustment reference -- Blank for invoices (N/A)
		$q.=$subtotal.", ".$disc.", ".($subtotal*(100-$disc)/100).")"; // Subtotal, discount, and total for JUST THIS INVOICE (not counting consolidated previous invoices)
		
		$query=mysql_query($q);
		
		/* And also update the fields in the view (which updates the work table and removes them from the view) */
		$q="UPDATE `v-a-invoice` SET `W-INV-NO`='".mysql_real_escape_string($invNum)."' WHERE `W-CUSTNO`='".mysql_real_escape_string($customer)."'";
		$query=mysql_query($q);
		
		include "invoiceFooter.php"; // Write the bottom of the page

		// Save the invoice
		if (!file_exists("billing")) { mkdir("billing"); }
		if (!file_exists("billing/invoices")) { mkdir("billing/invoices"); }
		file_put_contents("billing/invoices/unprinted/invoice_".$invNum.".html", ob_get_contents());

		ob_end_clean();
		
		// FIXIT: Attempt to print it
		//$cmd="start ./billing/invoices/printhtml.exe file='invoice_".$invNum.".html' header='' footer='' title='' /B";
		//file_put_contents("debug.txt", $cmd);
		//pclose(popen($cmd, "r"));
		//shell_exec('./billing/invoices/printhtml.exe file="invoice_'.$invNum.'.html" header="" footer="" title=""');
	}
	
	mysql_close($link);
?>