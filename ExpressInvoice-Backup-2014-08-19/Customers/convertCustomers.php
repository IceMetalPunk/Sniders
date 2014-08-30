<?php
	$files=scandir(".");
	$customers=array();
	foreach ($files as $ind=>$fn) {
		if (strrchr($fn, ".dat")!=".dat") { continue; }
		$data=file_get_contents($fn);
		$name=preg_replace('/\\.dat$/', '', $fn);
		$name=urldecode($name);
		$data=explode("&", $data);
		$customers[]=array();
		$n=count($customers)-1;
		$customers[$n]["Name"]=$name;
		foreach ($data as $dataInd=>$term) {
			$variable=explode("=", $term);
			$vari=urldecode($variable[0]);
			$val=urldecode($variable[1]);
			$customers[$n][$vari]=$val;
		}
	}
	
	// Convert to CSV
	
	$csv="";
	foreach ($customers[0] as $field=>$val) {
		if ($csv!="") { $csv.=", "; }
		$csv.='"'.$field.'"';
	}
	
	$csv.="\r\n";
	foreach ($customers as $ind=>$cust) {
		$line="";
		foreach ($cust as $field=>$val) {
			if ($line!="") { $line.=","; }
			$val=preg_replace("/(?:\r)?\n/", " ", $val);
			$line.='"'.$val.'"';
		}
		$csv.=$line;
		$csv.="\r\n";
	}
	
	echo nl2br($csv);
	
	file_put_contents("convertedCustomers.csv", $csv);
?>