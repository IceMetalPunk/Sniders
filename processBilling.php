<?php
  set_time_limit(60*60*24*365);
	
  /* Connect to the MySQL-running server (on localhost, with username root and no password) */
	$link=mysql_connect("localhost", "root", "");
	
	/* Select the sniders2013 database for use later */
	$db=mysql_select_db("sniders2013", $link);
	
	$billNums=array();
	$custBills=array();
	
	function GenerateBill() {

	}
	
	function GenerateAdjustmentNumber() {
	  $q="SELECT `l-DESC` FROM `t-lookup` WHERE `l-VALUE`=999";
		$query=mysql_query($q);
		$row=mysql_fetch_assoc($query);
		$num=$row["l-DESC"];
		$newNum=substr($num, 1);
		$newNum+=1;
		if ($newNum>99999) { $newNum=10001; }
		while (strlen($newNum)<5) { $newNum="0".$newNum; }
		$newNum="A".$newNum;
		$q="UPDATE `t-lookup` SET `l-DESC`='".$newNum."' WHERE `l-VALUE`=999";
		mysql_query($q);
		return $num;
	}
	
	function GenerateBillNumber() {
		global $custBills;
		global $billNums;
		
	  if (empty($custBills[0])) { return ""; }
	  $customer=$custBills[0]["C-CUSTNO"];
	  if (!empty($billNums[$customer])) { return $billNums[$customer]; }
		
		$q="SELECT `l-DESC` FROM `t-lookup` WHERE ";

		if ($custBills[0]["C-BILLING-METH"]==0) {
			$q.="`l-VALUE`=998";
			$query=mysql_query($q);
			
			$row=mysql_fetch_assoc($query);
			
			$num=$row["l-DESC"];

			/* Update to new billing number */
			$newNum=$num+1;
			
			if ($newNum>99999) {$newNum=10001; }
			$q="UPDATE `t-lookup` SET `l-DESC`='".$newNum."' WHERE `l-VALUE`=998";
			$query=mysql_query($q);
			
			$billNums[$customer]=$num;
			
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
			
			$billNums[$data["C-CUSTNO"]]=$num;
			
			return $num;
		}
		
	}
	
	function CopyToTAR() {
		global $custBills;
		global $billNums;
		
  	$customer=$custBills[0]["C-CUSTNO"];
	  $billNum=$billNums[$customer];
		
		$workAmt=0;
		$workNum=0;
		foreach ($custBills as $ind=>$data) {
		  if ($data["AB-BILL-TYP"]==10) {
			  $workNum++;
				$workAmt+=$data["AB-AMT"];
			}
			else {
			  $amt=$data["AB-AMT"];
				if ($data["C-DISCNT-PCT"]!=0 && $data["AB-BILL-TYP"]<20 || $data["AB-BILL-TYP"]>29) {
				  $amt*=(100-$data["C-DISCNT-PCT"])/100;
				}
				$q="INSERT INTO `t-a-rec` VALUES (";
				$q.="'".$data["AB-CUSTNO"]."', ";
				$q.="'".$data["AB-USE-DT"]."', ";
				$q.="'".$data["AB-BILL-DT"]."', ";
				if ($data["AB-TKT"]=="UNDEF") {
					$q.="'".GenerateAdjustmentNumber()."', ";
				}
				else {
				  $q.="'".$data["AB-TKT"]."', ";
				}
				$q.=$data["AB-BILL-TYP"].", ";
				$q.=$amt.", ";
				$q.="NOW()";
				$q.=")";
				$query=mysql_query($q);
			}
		}
		
		if ($workNum>0) {
		  if ($data["C-DISCNT-PCT"]!=0) { $workAmt*=(100-$data["C-DISCNT-PCT"])/100; }
			
			$q="INSERT INTO `t-a-rec` VALUES (";
			$q.="'".$customer."', ";
			$q.="NOW(), ";
			$q.="NOW()', ";
			$q.="'".$billNum."', ";
			$q.="10, ";
			$q.=$workAmt.", ";
			$q.="NOW()";
			$q.=")";
			$query=mysql_query($q);
		}
		
	}

	function CopyToTAS() {
		
	}

	function ClearTAB() {
		sleep(1);
	}

	/* TODO: GET CYCLE END DATE FROM LOOKUP TABLE */
	
	/* Track how many bills there are to be processed this cycle */
	/* TODO: INSERT LOOKUP END DATE INTO QUERIES */
	$q="SELECT COUNT(*) AS numBills FROM `t-a-billing` B, `t-customer` C WHERE DATEDIFF(`AB-USE-DT`, NOW())<=0 AND B.`AB-CUSTNO`=C.`C-CUSTNO`";
	$query=mysql_query($q);
	
	if (!$query) {
		file_put_contents("billCycleProgress.txt", "-1\r\nThere are no bills in this cycle.");
	}
	else {
	
		$row=mysql_fetch_assoc($query);
		$numBills=$row["numBills"];

		/* Keep track of the bills in their raw table form */
		$bills=array();
		
		/* Initialize the progress updates */
		$cont=$numBills."\r\n0";
		file_put_contents("billCycleProgress.txt", $cont);
		
		/* Grab the bills to begin processing */
		$q="SELECT * FROM `t-a-billing` B, `t-customer` C WHERE DATEDIFF(`AB-USE-DT`, NOW())<=0 AND B.`AB-CUSTNO`=C.`C-CUSTNO` ORDER BY C.`C-NAME`, B.`AB-USE-DT`, B.`AB-TKT`";
		$query=mysql_query($q);
		
		$onCust=-1;
		/* For each bill... */
		while ($row=mysql_fetch_assoc($query)) {
			/* Update the progress and store the raw bill data */
			$cont=$numBills."\r\n".count($bills);
			file_put_contents("billCycleProgress.txt", $cont);

			if ($onCust<0) { $onCust=$row["C-CUSTNO"]; }

			$bills[]=$row;
			if ($row["C-CUSTNO"]!=$onCust) {
			  $billNumbers[$row["C-CUSTNO"]]=GenerateBillNumber();
				GenerateBill();
				CopyToTAR();
				CopyToTAS();
				$custBills=array();
				$onCust=$row["C-CUSTNO"];
			}				
			$custBills[]=$row;
		}
		
		/* Update the final progress and clear the billing table of all the processed bills */
		$cont=$numBills."\r\n".count($bills);
		file_put_contents("billCycleProgress.txt", $cont);
		ClearTAB();
	}
	
	mysql_close($link);
		
?>