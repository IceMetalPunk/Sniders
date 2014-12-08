<?php
	set_time_limit(60*60*24*365);
	$link=mysql_connect("localhost", "root", "tux898");
  $db=mysql_select_db("sniders2013", $link);
	
	function GenerateInvoiceNumber() {
		$q="SELECT `l-DESC` FROM `t-lookup` WHERE `l-VALUE`=997";
		$query=mysql_query($q);
		$row=mysql_fetch_assoc($query);
		$num=$row["l-DESC"];

		/* Update to new invoice number */
		$newNum=explode("-", $num);
		$numPart=$newNum[1]+1;
		if ($numPart>99999) { $numPart="0001"; }
		while (strlen($numPart)<4) { $numPart="0".$numPart; }
		$newNum="I".date("y")."-".$numPart;

		$q="UPDATE `t-lookup` SET `l-DESC`='".$newNum."' WHERE `l-VALUE`=997";
		$query=mysql_query($q);
				
		return $num;
	}	

	$q="SELECT DISTINCT `W-CUSTNO` FROM `v-a-invoice` WHERE CAST(`W-CUSTNO` AS UNSIGNED INTEGER)<70000"; // Query to get the list of customers that have uninvoiced items.
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
	
	function Invoice($customer) {
		$_POST['c_num']=$customer;
		
		// Using output buffers here to store all the HTML, so that it can be echoed normally, but ultimately put into a file instead of displayed
		ob_start();
		ob_clean();
		
		include "batchInvoiceTemplate.php"; // Generate the invoice
		
		ob_end_clean();

	}
	
	mysql_close($link);
?>