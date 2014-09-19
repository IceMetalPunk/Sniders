<?php
  $link=mysql_connect("localhost", "root", "tux898");
  $db=mysql_select_db("sniders2013", $link);
  
  $_POST=array_map("mysql_real_escape_string", $_POST);
  
  $group=(!empty($_POST['group']) && $_POST['group']==1);
  
  $q="UPDATE `t-price` SET ";
  $like=" LIKE ";
  $wildcard="%";
  if (!$group) {
    $q.="`P-Type`='".$_POST['type']."',";
    $q.="`P-STYLE`='".$_POST['style']."',";
    $q.="`P-DESC`='".$_POST['description']."',";
    $q.="`P-SASH-DEF`='".$_POST['matchSash']."',";
    $q.="`P-VEST-DEF`='".$_POST['matchVest']."',";
    $q.="`P-TIE-DEF`='".$_POST['matchTie']."',";
    $q.="`P-SHIRT-DEF`='".$_POST['matchShirt']."',";
    $q.="`P-PANT-DEF`='".$_POST['matchPants']."',";
    $q.="`P-PLIST`=".$_POST['PLType'].",";
    $like="=";
    $wildcard="";
  }
  $q.="`P-COMP-PR`=".$_POST['compPrice'].",";
  $q.="`P-ITEM`=".$_POST['individualPrice'].",";
  $q.="`P-UPCHARGE-O`=".$_POST['upCharge'];
  
  $q.=" WHERE `P-TYPE`='".$_POST['type']."' AND `P-STYLE`".$like."'".$_POST['style'].$wildcard."'";
  $query=mysql_query($q);
  mysql_close($link);
?><html>
  <head>
    <title>Updating Price List Item</title>
    <link rel="stylesheet" type="text/css" href="styles.css" />
    <meta http-equiv="refresh" content="2; prices.php" />
  </head>
  <body>
    <h3>Price list has been updated. Returning to price maintenance page.</h3>
  </body>
</html>