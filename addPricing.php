<?php
  $link=mysql_connect("localhost", "root", "tux898");
  $db=mysql_select_db("sniders2013", $link);
  
  $_POST=array_map("mysql_real_escape_string", $_POST);
  
  $q="INSERT INTO `t-price` ";
  $q.="(`P-Type`, `P-STYLE`, `P-DESC`, `P-COMP-PR`, `P-ITEM`, `P-UPCHARGE-O`, `P-SASH-DEF`, `P-VEST-DEF`, `P-TIE-DEF`, `P-SHIRT-DEF`, `P-PANT-DEF`, `P-SPECIAL-2`, `P-SPECIAL-3`, `P-PLIST`, `P-PRIORITY`)";
  $q.=" VALUES ";
  $q.="('".$_POST['type']."', '".$_POST['style']."', '".$_POST['description']."', ".$_POST['compPrice'].", ".$_POST['individualPrice'].", ".$_POST['upCharge'].", '".$_POST['matchSash']."', '".$_POST['matchVest']."', '".$_POST['matchTie']."', '".$_POST['matchShirt']."', '".$_POST['matchPants']."', '', '', '".$_POST['PLType']."', '')";
  mysql_query($q);
  mysql_close($link);
?><html>
  <head>
    <title>Adding Price List Item</title>
    <link rel="stylesheet" type="text/css" href="styles.css" />
    <meta http-equiv="refresh" content="2; prices.php" />
  </head>
  <body>
    <h3>Price list has been updated. Returning to price maintenance page.</h3>
  </body>
</html>