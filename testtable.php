<style>
TABLE { border-collapse:collapse; }
TH { background-color:#eeeeee; }
TH, TD { border:1px solid #000000; padding:4px; }
</style>
<table>
<?php
  /* Connect to the MySQL-running server (on localhost, with username root and no password) */
  $link=mysql_connect("localhost", "root", "tux898");
  
  /* Select the sniders2013 database for use later */
  $db=mysql_select_db("sniders2013", $link);
  if (empty($_GET['table'])) { $tab="t-lookup"; }
  else { $tab=$_GET['table']; }

  $res=mysql_query("SELECT * FROM `".mysql_real_escape_string($tab)."`");

  $heads=false;
  while ($row=mysql_fetch_assoc($res)) {
    echo "<tr>";
    if (!$heads) {
      $heads=true;
      foreach ($row as $key=>$val) { echo "<th>".$key."</th>"; }
      echo "</tr><tr>";
    }
    foreach ($row as $val) { echo "<td>".$val."</td>"; }
    echo "</tr>";
  }
?>
</table>