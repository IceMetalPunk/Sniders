<?php
  $link=mysql_connect("localhost", "root", "tux898");
  $db=mysql_select_db("sniders2013", $link);
	
	$q="SELECT `C-CUSTNO` FROM `t-customer` WHERE `C-CUSTNO` IS NOT NULL";
	$query=mysql_query($q);
	
	if (!$query) {
	  exit("<b>Error:</b> No customers found. ".mysql_error());
	}
	$customers=array();
	while ($row=mysql_fetch_assoc($query)) {
	  $customers[]=$row["C-CUSTNO"];
	}
	
	function randWord($n) {
	  $un=uniqid(mt_rand(0, 999), true);
		return substr($un, 0, $n);
	}
	
	for ($i=0; $i<300; ++$i) {
		$q="INSERT INTO `t-a-billing` VALUES (";
		$q.="'".$customers[array_rand($customers)]."', ";
		$q.="NOW(), ";
		$q.="'".randWord(5)."', ";
		$q.=mt_rand(0, 9).", ";
		$q.="'".randWord(7)."', ";
		$q.="NOW(), 0, 0, NOW(), ";
		$q.="'".randWord(10)."', 11)";
		mysql_query($q);
	}
	
	mysql_close($link);
?>
