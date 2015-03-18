<b>Restoring Lost Tables</b><br />
<?php

	$link=mysql_connect("localhost", "root", "tux898");
  
  /* Select the sniders2013 database for use later */
  $db=mysql_select_db("sniders2013", $link);

	$tarCreate = "CREATE TABLE IF NOT EXISTS `t-a-rec` (
  `TAR-CUSTNO` varchar(6) NOT NULL COMMENT 'Customer Number',
  `TAR-POST-DT` date NOT NULL COMMENT 'Date Posted',
  `TAR-INV-NO` varchar(8) NOT NULL COMMENT 'Invoice Number',
  `TAR-ADJ-NUM` varchar(7) NOT NULL COMMENT 'Adjustment Number (If Applicable)',
  `TAR-REF-DESC` varchar(29) NOT NULL COMMENT 'Freeform Adjustment Info (Blank If Not An Adjustment)',
  `TAR-TYPE` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Type (0=Invoice, Other=Adjustment; See Adjustment Types In Adjust.php)',
  `TAR-AMT` float NOT NULL DEFAULT '0' COMMENT 'Total After Discount',
  PRIMARY KEY (`TAR-CUSTNO`,`TAR-INV-NO`,`TAR-ADJ-NUM`),
  KEY `TAR-CUSTNO` (`TAR-CUSTNO`),
  KEY `TAR-CUSTNO_2` (`TAR-CUSTNO`,`TAR-INV-NO`,`TAR-ADJ-NUM`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
	$executeTarCreate = mysql_query($tarCreate);
	echo "TAR Creation Status: ".($executeTarCreate?"Successful":"Failure - ".mysql_error());
	
	$tarInsert = "INSERT INTO `t-a-rec` (`TAR-CUSTNO`, `TAR-POST-DT`, `TAR-INV-NO`, `TAR-ADJ-NUM`, `TAR-REF-DESC`, `TAR-TYPE`, `TAR-AMT`) VALUES
('10104', '2015-03-13', 'I15-0122', '', '', 0, 15),
('10765', '2015-03-13', 'I15-0106', '', '', 0, 65),
('10765', '2015-03-13', 'I15-0113', '', '', 0, 0),
('11113', '2015-03-13', '', 'A18973', 'Cash Payment', 26, -96),
('11133', '2015-03-13', '', 'A18975', 'Cash Payment', 26, -50),
('11133', '2015-03-13', 'I15-0100', '', '', 0, 440),
('11133', '2015-03-13', 'I15-0123', '', '', 0, 85),
('11192', '2015-03-13', 'I15-0117', '', '', 0, 0),
('11242', '2015-03-13', 'I15-0107', '', '', 0, 49.5),
('11249', '2015-03-13', 'I15-0104', '', '', 0, 1176),
('11249', '2015-03-13', 'I15-0110', '', '', 0, 0),
('11249', '2015-03-13', 'I15-0110', 'A18978', 'Cash Payment', 26, -500),
('11250', '2015-03-13', 'I15-0101', '', '', 0, 132),
('11250', '2015-03-13', 'I15-0120', '', '', 0, 132),
('11277', '2015-03-13', 'I15-0115', '', '', 0, 73),
('11277', '2015-03-13', 'I15-0121', '', '', 0, 50),
('11308', '2015-03-13', '', 'A18986', 'Misc. Charge', 32, 50),
('11505', '2015-03-13', 'I15-0119', '', '', 0, 58),
('11542', '2015-03-13', '', 'A18981', 'Cash Payment', 26, -58.5),
('11597', '2015-03-13', '', 'A18974', 'Cash Payment', 26, -73),
('11642', '2015-03-13', '', 'A18982', 'Cash Payment', 26, -480),
('11642', '2015-03-13', 'I15-0103', '', '', 0, 480),
('11642', '2015-03-13', 'I15-0111', '', '', 0, 0),
('11642', '2015-03-13', 'I15-0111', 'A18976', 'Cash Payment', 26, -179),
('11806', '2015-03-13', 'I15-0114', '', '', 0, 306),
('11825', '2015-03-13', '', 'A18984', 'Cash Payment', 26, -192.5),
('11825', '2015-03-13', 'I15-0105', '', '', 0, 562.5),
('11825', '2015-03-13', 'I15-0112', '', '', 0, 0),
('11825', '2015-03-13', 'I15-0112', 'A18979', 'Cash Payment', 26, -370),
('11833', '2015-03-13', 'I15-0102', '', '', 0, 420),
('11833', '2015-03-13', 'I15-0109', '', '', 0, 60),
('22617', '2015-03-13', '', 'A18985', 'Cash Payment', 26, -50),
('30010', '2015-03-13', 'I15-0108', '', '', 0, 417.6),
('30010', '2015-03-13', 'I15-0118', '', '', 0, 0),
('30010', '2015-03-13', 'I15-0118', 'A18980', 'Cash Payment', 26, -417.6),
('30011', '2015-03-13', '', 'A18977', 'Cash Payment', 26, -365),
('30022', '2015-03-13', 'I15-0116', '', '', 0, 0);";
	$executeTarInsert = mysql_query($tarInsert);
	echo "<br />TAR Insertion Status: ".($executeTarInsert?"Successful":"Failure - ".mysql_error());


	$tabCreate = "CREATE TABLE IF NOT EXISTS `t-a-billing` (
	`TAB-CUSTNO` varchar(6) NOT NULL COMMENT 'Customer Number',
	 `TAB-POST-DT` date NOT NULL COMMENT 'Date Posted', `TAB-INV-DT` date NOT NULL COMMENT 'Date Invoiced (If An Invoice)',
	 `TAB-INV-NO` varchar(8) NOT NULL COMMENT 'Invoice Number (If An Invoice)',
	 `TAB-ADJ-NO` varchar(8) NOT NULL DEFAULT '' COMMENT 'Adjustnment Number (Adjustments Only)',
	 `TAB-ADJ-TYPE` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Adjustment Type (0=Bill; 1=Invoice; See Adjust.php For Type Codes)',
	 `TAB-ADJ-REF` varchar(29) NOT NULL COMMENT 'Freeform Adjustment Info',
	 `TAB-AMT` float NOT NULL DEFAULT '0' COMMENT 'Subtotal of non-adjustment charges', `TAB-DISCOUNT` float NOT NULL DEFAULT '0' COMMENT 'Discount Percentage (Optional?)', `TAB-TOTAL` float NOT NULL DEFAULT '0' COMMENT 'Amount After Discount Is Applied',
	 PRIMARY KEY (`TAB-CUSTNO`,`TAB-INV-NO`,`TAB-ADJ-NO`),
	 KEY `TAB-CUSTNO` (`TAB-CUSTNO`), KEY `TAB-CUSTNO_2` (`TAB-CUSTNO`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Stores unbilled invoices and adjustments during a billing cycle';";
	$executeTabCreate = mysql_query($tabCreate);

	echo "<br />Blank TAB Creation Status: ".($executeTabCreate?"Successful":"Failed - ".mysql_error());

	/*
	$invoiceView = "CREATE OR REPLACE VIEW `v-a-invoice` AS SELECT * FROM `t-work` where ((`W-INV-NO` = '0000000' OR `W-INV-NO`='') AND ((to_days(`W-USE-DT`) - to_days((select `l-DESC` from `t-lookup` where (`t-lookup`.`l-VALUE` = 301)))) <= 0))";
	$executeInvoiceView = mysql_query($invoiceView);

	echo "<br />Invoice View Creation Status: ".($executeInvoiceView?"Successful":"Failed - ".mysql_error());*/
	
	mysql_close($link);
?>