<?php
  $link=mysql_connect("localhost", "root", "tux898");
  if (!$link) { die("Couldn't connect to database"); }
  $db=mysql_select_db("sniders2013", $link);
  if (!$db) { mysql_close($link); die("Can't find database"); }

  $q="SELECT * FROM `t-customer` WHERE `C-CUSTNO`='".mysql_real_escape_string($_POST['c_num'])."'";
  $query=mysql_query($q);
  if (!$query) { mysql_close($link); die("Can't find customer"); }

  if (mysql_num_rows($query)<=0) { mysql_close($link); die("Can't find customer"); }
  if (mysql_num_rows($query)>1) { mysql_close($link); die("Multiple Customers"); }

  $row = mysql_fetch_assoc($query);
  echo $row["C-NAME"];
  mysql_close($link);
?>