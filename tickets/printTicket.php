<?php
  set_time_limit(60);

  function GenerateTickets($query) {

  $lookup=array();
  $q="SELECT * FROM `t-lookup` WHERE `l-WRK-TKT`=0";
  $look=mysql_query($q);
  while ($row=mysql_fetch_assoc($look)) {
    $lookup[$row["l-VALUE"]]=$row["l-DESC"];
  }

  $desc=array(
    array("pipe", "rw"),
    array("pipe", "rw"),
    array("file", "errorlog.txt", "a")
  );
  $path=dirname($_SERVER['PHP_SELF']);
  $path="C:/wamp/www".$path;

  while ($row=mysql_fetch_assoc($query)) {
  $prefix="";
  if ($row["W-TKT-TYPE"]==1) { $prefix="shoe-"; }
  else if ($row["W-TKT-TYPE"]==2) { $prefix="acc-"; }
  $row["W-VS-QTY"]=$row["W-VEST-QTY"]+$row["W-SASH-QTY"];
  $all=file_get_contents($prefix."template.html");
  $all=explode("$$", $all);
  
  for ($p=1; $p<count($all); $p+=2) {
    if ($all[$p]=="W-USE-DT" || $all[$p]=="W-ORDER-DT") {
      $all[$p]=strtotime($row[$all[$p]]);
      $all[$p]=date("n/j", $all[$p]);
    }
    else if (isset($row[$all[$p]])) {
      $all[$p]=$row[$all[$p]];
    }
    else if (substr($all[$p], 0, 1)=="=") {
      $field=substr($all[$p], 1);
      $all[$p]=$lookup[$row[$field]];
    }
    else if ($all[$p]=="TYPE") {
      $all[$p]="$$"."TYPE$$";
    }
    else {
      $all[$p]="&nbsp;";
    }

    if ($all[$p]=="") { $all[$p]="&nbsp;"; }

  }
  
  $all=implode("", $all);
  
  if ($row["W-TKT-TYPE"]==1) {
    $ticks=array("CUSTOMER"=>"CUSTOMER", "SHOE"=>"SHOE");
  }
  else if ($row["W-TKT-TYPE"]==2) {
    $ticks=array("CUSTOMER"=>"CUSTOMER", "VEST"=>"ACCESSORIES");
  }
  else {
    $ticks=array("CUSTOMER"=>"CUSTOMER", "COAT"=>"COAT", "PANT-STYLE"=>"PANTS", "VEST"=>"VEST", "SHIRT"=>"SHIRT");
  }
  
  $numberPrinted=0;
  foreach ($ticks as $field=>$type) {
    $temp=explode("$$"."TYPE$$", $all);
    $temp=implode($type, $temp);
    
    if ($row["W-TKT-TYPE"]==0 && $type!="CUSTOMER" && empty($row["W-".$field])) { continue; }
    if ($type=="SHIRT" && $numberPrinted>1) { continue; }
    
    ++$numberPrinted;
    file_put_contents($_POST['ticket']."-".$row["W-TKT-SUB"]."-".$type.".html", $temp);
  
    $page=$_POST['ticket']."-".$row["W-TKT-SUB"]."-".$type.".html";

    $html2img="wkhtmltoimage";

    $p=proc_open($html2img.' --crop-w 400 --load-error-handling ignore '.$page.' '.$_POST['ticket'].'-'.$row["W-TKT-SUB"].'-'.$type.'.png', $desc, $pipes, $path, NULL);
    //echo "Running command: ".'"'.$html2img.'" --crop-w 400 "'.$page.'" "'.$_POST['ticket'].'-'.$row["W-TKT-SUB"].'-'.$type.'.png"';
    //echo "<br />From path: ".$path;

    if (is_resource($p)) {
      $ret=proc_close($p);
      if ($ret!=0) {
        die("<b>Error:</b><br /><pre>".htmlentities(file_get_contents("errorlog.txt"))."</pre>");
      }
    }
    else { die("ERROR starting ticket conversion process."); }
  }

  $page2="ticket-".$_POST['ticket']."-".$row["W-TKT-SUB"].".html";
  $body="<html><head><style>BODY { float:left; width:1600px; }</style></head><body>";
  foreach ($ticks as $type) {
    if (file_exists($_POST['ticket'])."-".$row["W-TKT-SUB"]."-".$type.".png") {
      $body.="<img style='float:left' src='".$_POST['ticket']."-".$row["W-TKT-SUB"]."-".$type.".png' />";  
    }
  }
  $body.="<br clear='both' /></body></html>";
  file_put_contents($page2, $body);
  
  $p=proc_open($html2img.' --load-error-handling ignore '.$page2.' ticket-'.$_POST['ticket']."-".$row["W-TKT-SUB"].'.png', $desc, $pipes, $path, NULL);

  if (is_resource($p)) {
    $ret=proc_close($p);
    if ($ret!==0) {
      die("<b>Error:</b><br /><pre>Error in compilation.\n".htmlentities(file_get_contents("errorlog.txt"))."</pre>");
    }
  }
  else { die("ERROR finishing ticket conversion process."); }

  @unlink("errorlog.txt");
  @unlink($page2);
  foreach ($ticks as $type) {
    @unlink($_POST['ticket'].'-'.$row["W-TKT-SUB"].'-'.$type.'.png');
    @unlink($_POST['ticket'].'-'.$row["W-TKT-SUB"].'-'.$type.'.html');
  }
  }
  }
  
  $link=mysql_connect("localhost", "root", "tux898");
  $db=mysql_select_db("sniders2013", $link);
  
  $q="SELECT * FROM `t-work` INNER JOIN `t-customer` ON `t-work`.`W-CUSTNO`=`t-customer`.`C-CUSTNO` WHERE `t-work`.`W-TKT`='".$_POST['ticket']."' AND `t-work`.`W-TKT-PRINTED`=0";
  $query=mysql_query($q);
  GenerateTickets($query);
  
  $q="UPDATE `t-work` SET `W-TKT-PRINTED`=1 WHERE `W-TKT`='".$_POST['ticket']."'";
  $query=mysql_query($q);
  
  mysql_close($link);
  
?><html>
      <body onload='document.entry.submit()'>
        <form name='entry' action='../entry.php' method='post'>
          <input type='hidden' name='redirected' value='1' />
          <input type='hidden' name='red_custno' value='<?php echo $_POST['red_c_num']; ?>' />
          <input type='hidden' name='red_usedate' value='<?php echo $_POST['red_date_use']; ?>' />
        </form>
      </body>
    </html>