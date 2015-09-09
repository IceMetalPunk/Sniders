<html>
  <head>
<?php
  $link=mysql_connect("localhost", "root", "tux898");
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
	
	if (empty($_POST['c_num']) || empty($_POST['amt'])) {
		$error="Please enter all required information (customer and amount).";
	}
	else if (!is_numeric($_POST["amt"]) || $_POST['amt']<0) {
		$error="Invalid amount. Please check and correct it.";
	}
	else {
		$q="SELECT * FROM `t-customer` WHERE `C-CUSTNO`='".mysql_real_escape_string($_POST['c_num'])."'";
		$query=mysql_query($q);
		
		if (!$query || mysql_num_rows($query)<=0) { $error="Customer not found. Please check and correct the customer number."; }
		else {
			$customerData=mysql_fetch_assoc($query);

			$adjNum=GenerateAdjustmentNumber();
			if (($_POST['adjustType']>=20 && $_POST['adjustType']<=29)) {// || ($_POST['adjustType']>39 && $_POST['adjustType']<50)) {
				$_POST['amt']*=-1;
			}

			include "paymentTypes.php";
			
			/* Apply any outstanding payments to this new invoice */
			$remaining=abs($_POST['amt']);
			$invToUpdate=array();
			$checkoff=array();
			
			// From TAR
			if ($remaining>0) {
				$q="SELECT * FROM `t-a-rec` WHERE (`TAR-TYPE`=0) AND (`TAR-REMAINING`>0) ORDER BY `TAR-INV-DT` ASC, `TAR-INV-NO` ASC, `TAR-ADJ-NUM` ASC";
				$query=mysql_query($q);
				while ($remaining>0 && ($row=mysql_fetch_assoc($query))) {
					if ($row["TAR-INV-NO"]!='') {
						$invToUpdate[$row["TAR-INV-NO"]]=$row["TAR-REMAINING"]-min($remaining, $row["TAR-REMAINING"]);
					}
					else {
						$invToUpdate[$row["TAR-ADJ-NUM"]]=$row["TAR-REMAINING"]-min($remaining, $row["TAR-REMAINING"]);
					}
					$checkoff[]=$row["TAR-INV-NO"];
					$remaining-=min($remaining, $row["TAR-REMAINING"]);							
				}
			
				foreach ($invToUpdate as $num=>$amt) {
					$q="SELECT `TAR-CHECKOFF` FROM `t-a-rec` WHERE `TAR-INV-NO`='".$num."'";
					$query=mysql_query($q);
					$row=mysql_fetch_assoc($query);
					$oldCheckoff=unserialize($row["TAR-CHECKOFF"]);
					$oldCheckoff[]=$adjNum;
					
					$q="UPDATE `t-a-rec` SET `TAR-REMAINING`=".$amt.", `TAR-CHECKOFF`='".mysql_real_escape_string(serialize($oldCheckoff))."' WHERE `TAR-INV-NO`='".$num."' AND `TAR-TYPE`<=1";
					$query=mysql_query($q);							
				}
			}
			
			// From TAB
			if ($remaining>0) {
				unset($invToUpdate);
				$invToUpdate=array();
				$q="SELECT * FROM `t-a-billing` WHERE `TAB-ADJ-TYPE`<=1 AND `TAB-REMAINING`>0 ORDER BY `TAB-INV-DT` ASC, `TAB-INV-NO` ASC, `TAB-ADJ-NO` ASC";
				$query=mysql_query($q);
				while ($remaining>0 && ($row=mysql_fetch_assoc($query))) {
					$invToUpdate[$row["TAB-INV-NO"]]=$row["TAB-REMAINING"]-min($remaining, $row["TAB-REMAINING"]);
					$checkoff[]=$row["TAB-INV-NO"];
					$remaining-=min($remaining, $row["TAB-REMAINING"]);							
				}
			
				foreach ($invToUpdate as $num=>$amt) {
					$q="SELECT `TAB-CHECKOFF` FROM `t-a-billing` WHERE `TAB-INV-NO`='".$num."'";
					$query=mysql_query($q);
					$row=mysql_fetch_assoc($query);
					$oldCheckoff=unserialize($row["TAB-CHECKOFF"]);
					$oldCheckoff[]=$adjNum;
					
					$q="UPDATE `t-a-billing` SET `TAB-REMAINING`=".$amt.", `TAB-CHECKOFF`='".mysql_real_escape_string(serialize($oldCheckoff))."' WHERE `TAB-INV-NO`='".$num."' AND `TAB-ADJ-TYPE`<=1";
					$query=mysql_query($q);							
				}
			}
			
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
			$q.=$_POST['amt'].", "; // Total after discount -- is equal to subtotal except for charge adjustments
			$q.="'".serialize($checkoff)."', ".$remaining; // Checkoff and remaining amount
			$q.=")";
			$query=mysql_query($q);
			if (!$query) { $error=mysql_error()."<br />(".$q.")"; }
			
			/* If payment, update customer's last payment date */
			if ($_POST['adjustType']>=20 && $_POST['adjustType']<=29) {
				$query="UPDATE `t-customer` SET `c-lastPayment`=NOW() WHERE `C-CUSTNO`='".mysql_real_escape_string($_POST['c_num'])."'";
				$q=mysql_query($query);
			}
			
		}
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
		<!-- Speed up adjustment added page?
		<script>$(function() { window.location.href="adjustments.php"; });</script>
		-->
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