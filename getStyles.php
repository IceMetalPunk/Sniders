<?php
  $link=mysql_connect("localhost", "root", "");
  
  /* Select the sniders2013 database */
  $db=mysql_select_db("sniders2013", $link);
  
  /* Find the style in the database and its associated pieces */
  $q="SELECT * FROM `t-price` WHERE `P-STYLE`='".mysql_real_escape_string($_GET['style'])."' AND `P-Type`='coat'";
  $query=mysql_query($q);  
  $row=mysql_fetch_assoc($query);
  $row=array_map("addslashes", $row); // Needed so special characters like quotes, etc. are escaped before putting them in the Javascript

  /* Build a Javascript object with the different style data */
  $out="{";  
  $out.='"sash": "'.$row['P-SAH-DEF'].'", ';
  $out.='"vest": "'.$row['P-VEST-DEF'].'", ';
  $out.='"tie": "'.$row['P-TIE-DEF'].'", ';
  $out.='"shirt": "'.$row['P-SHIRT-DEF'].'", ';
  $out.='"pants": "'.$row['P-PANT-DEF'].'"';
  $out.="}";
  
  /* Output it */
  echo $out;
  
  mysql_close($link); // Disconnect from the database
?>