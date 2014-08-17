<html>
  <head>
<?php
  $link=mysql_connect("localhost", "root", "");
  $db=mysql_select_db("sniders2013", $link);
	
	$error="";
	
	function GenerateAdjustmentNumber() {
	  $q="SELECT `l-DESC` from `t-lookup` WHERE `l-VALUE`=999";
		$query=mysql_query($q);
		
		if (!$query || mysql_num_rows($query)<=0) { return "A00001"; }

		$current=mysql_fetch_assoc($query);
		$current=$current["l-DESC"];
		
		$next=substr($current, 1);
		$next+=1;
		if ($next>99999) { $next=1; }
		while (strlen($next)<5) { $next="0".$next; }
		
		$q="UPDATE `t-lookup` SET `l-DESC`='A".$next."' WHERE `l-VALUE`=999";
		$query=mysql_query($q);
		
		return $current;
		
	}
	
	$q="SELECT * FROM `t-customer` WHERE `C-CUSTNO`='".mysql_real_escape_string($_POST['c_num'])."'";
	$query=mysql_query($q);
	
	if (!$query || mysql_num_rows($query)<=0) { $error="Customer not found."; }
	else {
		$customerData=mysql_fetch_assoc($query);

		$adjNum=GenerateAdjustmentNumber();
		if (($_POST['adjustType']>=20 && $_POST['adjustType']<=29) || ($_POST['adjustType']>39 && $_POST['adjustType']<50)) {
		  $_POST['amt']*=-1;
		}
		
		$types=array(
			"Cash Payment"=>26,
			"Credit Card Payment"=>22,
			"Check Payment"=>23,
			"Write-Off"=>24,
			"Other Payment"=>25,
			"Charge for Lost Item"=>31,
			"Miscellaneous Charge"=>32,
			"Delivery Charge"=>33,
			"Miscellaneous Credit"=>41,
			"Not Used"=>42
		);
		$q="INSERT INTO `t-a-billing` VALUES(";
		$q.="'".mysql_real_escape_string($_POST['c_num'])."', "; // Customer number
		$q.="'', '', "; // Post and invoice dates, blank for now
		$q.="'', '".$adjNum."', "; // Invoice number (blank for now) and adjustment number
		$q.=$_POST['adjustType'].", "; // Adjustment type
		
		$ind=array_search($_POST['adjustType'], $types); // Textual description of adjustment type
		$q.="'".mysql_real_escape_string($ind);
		
		if (!empty($_POST['freeform'])) { $q.=" - ".mysql_real_escape_string($_POST['freeform']); } // Freeform info if any
		$q.="', ";
		$q.=$_POST['amt'].", "; // Subtotal
		$q.=$customerData["C-DISCNT-PCT"].", "; // Discount percentage -- Only charge adjustments are discounted
		if ($_POST['adjustType']>=30 && $_POST['adjustType']<40) {
			$q.=$_POST['amt']*(100-$customerData["C-DISCNT-PCT"])/100; // Total after discount -- charge adjustments have this calculated
		}
		else {
			$q.=$_POST['amt']; // Total after discount -- is equal to subtotal except for charge adjustments
		}
		$q.=")";
		$query=mysql_query($q);
		if (!$query) { $error=mysql_error(); }
	}
	
	if ($error=="") {
?>
    <title>Adjustment Added</title>

    <!-- Main styles -->
    <link rel="stylesheet" href="styles.css" />

    <!-- jQuery library -->
    <script type="text/javascript" src="jquery-1.9.1.js"></script>
		
		<meta http-equiv="refresh" content="1;adjustments.php" />

  </head>
  <body>
		<b>Adjustment added. Redirecting back to the adjustment form.</b>
	</body>
<?php } else { // On insertion failure ?>
		<title>Adjustment Failed</title>
		<link rel="stylesheet" href="styles.css" />
    <script type="text/javascript" src="jquery-1.9.1.js"></script>
	</head>
	<body>
		<b>Adjustment failed.</b><br />
		<form action="adjustments.php" method="post" id="redirect">
		  <input type="hidden" name="error" value="<?php echo $error; ?>" />
			<button id="sub" type="submit" accesskey="B">Go <u>B</u>ack</button>
		</form>
		<script>$("#redirect").submit();</script>
	</body>
</html>
<?php
  }
	mysql_close($link);
?>