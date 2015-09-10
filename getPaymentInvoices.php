<?php
	$link=mysql_connect("localhost", "root", "tux898");
	$db=mysql_select_db("sniders2013", $link);
	
	$remaining=abs($_POST['amt']);
	$invToUpdate=array();
	$checkoff=array();
	
	// From TAR
	if ($remaining>0) {
		$q="SELECT * FROM `t-a-rec` WHERE (`TAR-CUSTNO`='".mysql_real_escape_string($_POST['cust'])."') AND (`TAR-TYPE`=0) AND (`TAR-REMAINING`>0) ORDER BY `TAR-INV-DT` ASC, `TAR-INV-NO` ASC, `TAR-ADJ-NUM` ASC";
		$query=mysql_query($q);
		while ($remaining>0 && ($row=mysql_fetch_assoc($query))) {
			if ($row["TAR-INV-NO"]!=NULL && $row["TAR-INV-NO"]!='') {
				$invToUpdate[$row["TAR-INV-NO"]]=$row["TAR-REMAINING"]-min($remaining, $row["TAR-REMAINING"]);
			}
			else {
				$invToUpdate[$row["TAR-ADJ-NUM"]]=$row["TAR-REMAINING"]-min($remaining, $row["TAR-REMAINING"]);
			}
			$checkoff[]=$row["TAR-INV-NO"];
			$remaining-=min($remaining, $row["TAR-REMAINING"]);							
		}
	}
	
	// From TAB
	if ($remaining>0) {
		unset($invToUpdate);
		$invToUpdate=array();
		$q="SELECT * FROM `t-a-billing` WHERE (`TAB-CUSTNO`='".mysql_real_escape_string($_POST['cust'])."') AND `TAB-ADJ-TYPE`<=1 AND `TAB-REMAINING`>0 ORDER BY `TAB-INV-DT` ASC, `TAB-INV-NO` ASC, `TAB-ADJ-NO` ASC";
		$query=mysql_query($q);
		while ($remaining>0 && ($row=mysql_fetch_assoc($query))) {
			$invToUpdate[$row["TAB-INV-NO"]]=$row["TAB-REMAINING"]-min($remaining, $row["TAB-REMAINING"]);
			$checkoff[]=$row["TAB-INV-NO"];
			$remaining-=min($remaining, $row["TAB-REMAINING"]);							
		}
	}

	if (count($checkoff)<=0) {
		echo "There are no outstanding invoices for this customer.";
	}
	else {
		echo "This payment will apply to the following invoices:<br /><ul>";
		foreach ($checkoff as $ind=>$val) {
			echo "<li><a href='http://127.0.0.1/Sniders/billing/invoices/".$val.".html' target='_blank'>".$val."</a>";
		}
	}
	
	mysql_close($link);
?>