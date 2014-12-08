<?php
	$csv = array_map('str_getcsv', file('restorework2.csv'));
	//var_dump($csv);
	$output="";
	foreach ($csv as $ind=>$entry) {
		$orderDate=date("Y-m-d H:i:s", strtotime($entry[5]));
		$useDate=date("Y-m-d H:i:s", strtotime($entry[6]));
		$output.="UPDATE `t-work` SET `W-ORDER-DT`='".$orderDate."', `W-USE-DT`='".$useDate."' WHERE `W-CUSTNO`='".$entry[0]."' AND `W-Number`='".$entry[1]."' AND `W-TKT`='".$entry[2]."' AND `W-TKT-SUB`='".$entry[3]."';\n";
	}
	//echo nl2br($output);
	echo "<span style='font-size:14pt'><b>Follow the steps below to successfully import restorework2.csv:</b>";
	echo "<ol><li>Import the restorework2.csv file into the t-work table from phpMyAdmin.</li>";
	echo "<li>Copy the entire contents of the textarea below into a SQL query on t-work in phpMyAdmin and run it.</li></ol>";
	echo "<b>SQL Query:</b><br /></span><textarea rows=10 cols=50>".$output."</textarea>";
?>