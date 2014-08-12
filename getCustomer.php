<?php
  $link=mysql_connect("localhost", "root", "");
  
  /* Select the sniders2013 database */
  $db=mysql_select_db("sniders2013", $link);
  
  /* Find the style in the database and its associated pieces */
  $q="SELECT * FROM `t-customer` WHERE `C-CUSTNO`='".mysql_real_escape_string($_GET['c_num'])."'";
  $query=mysql_query($q);  
  $row=mysql_fetch_assoc($query);
  $row=array_map("addslashes", $row); // Needed so special characters like quotes, etc. are escaped before putting them in the Javascript
  
  /* Build a Javascript object with all the customer data */
  $out="{";  
  foreach ($row as $prop=>$val) {
    if ($out!="{") { $out.=", "; }
    $out.='"'.$prop.'": "'.$val.'"';
  }
  $out.="}";
  
  /* Output it */
  echo $out;
  
  mysql_close($link); // Disconnect from the database
?>