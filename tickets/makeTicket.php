<html>
  <head>
    <title>Work Ticket Generation</title>

    <!-- Main styles -->
    <link rel="stylesheet" href="../styles.css" />

    <!-- jQuery library -->
    <script type="text/javascript" src="../jquery-1.9.1.js"></script>

  </head>
  <body>
<?php
  $link=mysql_connect("localhost", "root", "tux898");
  $db=mysql_select_db("sniders2013", $link);

  if (!isset($_POST['outfitPrice']) || $_POST['outfitPrice']==="") {
    echo "<b>Some information is missing. Redirecting to the main menu.</b><meta http-equiv='refresh' content='5; url=../index.php' />";
  }
  else {
    $vals=array();
		$hasChanged=array();
    $vals["W-TKT"]="'".$_POST['ticket']."'"; // Ticket number
    $vals["W-TKT-SUB"]="0"; // Subticket number
    
		$vals["W-INV-NO"]="'0000000'";
    if ($_POST['c_num']!="99999") {
      $vals["W-CUSTNO"]="'".$_POST['c_num']."'"; // Customer number for non-in-house rentals
    }
    // For in-house rentals, we create a temporary "fake" customer with this information, then use that customer number
    else {
      $q="SELECT MAX(`C-CUSTNO`) AS CustNum FROM `t-customer` WHERE CAST(`C-CUSTNO` AS UNSIGNED INTEGER)>=70000 && CAST(`C-CUSTNO` AS UNSIGNED INTEGER)<99999";
      $query=mysql_query($q);
      $row=mysql_fetch_assoc($query);
      if ($query<1 || $row["CustNum"]=="") { $vals["W-CUSTNO"]="70000"; }
      else { $vals["W-CUSTNO"]=$row["CustNum"]+1; }
      
      $q="INSERT INTO `t-customer` (`C-CUSTNO`, `C-NAME`, `C-PHONE`, `C-PHONE2`, `C-CONTACT`) VALUES ";
      $q.="('".$vals["W-CUSTNO"]."', '".mysql_real_escape_string($_POST['c_name'])."', '".mysql_real_escape_string($_POST['homePhone'])."', '".mysql_real_escape_string($_POST['cellPhone'])."', '".mysql_real_escape_string($_POST['ref'])."')";
      $query=mysql_query($q);
      
    }
    $vals["W-ORDER-DT"]="NOW()"; // Order date
    $vals["W-USE-DT"]="STR_TO_DATE('".$_POST['full_use_date']." 00:00:00', '%m/%e/%Y %H:%i:%s')"; // Use date
    $vals["W-SHP-INST"]=$_POST['d_type']; // Shipping instructions
    $vals["W-BILL-INST"]=$_POST['b_type']; // Billing instructions
    $vals["W-COD-INV"]="0";  // COD invoice number
    $vals["W-BILL-INV"]="000000"; // Billing invoice number
    $vals["W-REF"]="'".mysql_real_escape_string($_POST['ref'])."'"; // Reference name
    $vals["W-COMP-OUT"]=(!empty($_POST['complete']))?"1":"0"; // Complete outfit?
		
		$styleCheck=$_POST["c_style"];
		$okay=false;
    $vals["W-COAT"]="'".$_POST['c_style']."'"; // Coat style
    $vals["W-COAT-SZ"]="'".$_POST['c_size']."'"; // Coat size
    $vals["W-COAT-SLV"]="'".$_POST['c_sleeve']."'"; // Coat sleeve
		
		$styleCheck.=$_POST["p_style"];
    $vals["W-PANT-STYLE"]="'".$_POST['p_style']."'"; // Pants style
    $vals["W-WAIST"]="'".$_POST['p_waist']."'"; // Pants waist size
    $vals["W-PANT-LEN"]="'".$_POST['p_length']."'"; // Pants length
    $vals["W-PANTS-SEAT"]="'".$_POST['p_seat']."'"; // Pants seat size
		
		$styleCheck.=$_POST["s_style"];
    $vals["W-SHIRT"]="'".$_POST['s_style']."'"; // Shirt style
    $vals["W-SHIRT-SIZE"]="'".$_POST['s_size']."'"; // Shirt size
    
    /* TODO: Set these based on accessories-only or not */
    
    // Complete outfit only
    $vals["W-VEST"]="''"; // Vest style -- blank for accessories
    $vals["W-SASH"]="''"; // Sash style -- blank for accessories
    $vals["W-TIE"]="''"; // Tie  style -- blank for accessories
    $vals["W-HANKIE"]="''"; // Hankie style -- blank for accessories
    
    if (empty($_POST["accessories"])) {
			$styleCheck.=$_POST["a_vest"].$_POST["a_sash"].$_POST["a_tie"].$_POST["a_hankie"];
      $vals["W-VEST"]="'".$_POST['a_vest']."'"; // Vest style
      $vals["W-SASH"]="'".$_POST['a_sash']."'"; // Sash style
      $vals["W-TIE"]="'".$_POST['a_tie']."'"; // Tie style
      $vals["W-HANKIE"]="'".$_POST['a_hankie']."'"; // Hankie style
    }
    
    // Both Complete Outfit and Accessories-Only (Set value accordingly)
    $vals["W-GLOVE-M"]="0"; // Men's Glove QTY -- exists whether accessories-only or not
    $vals["W-GLOVE-B"]="0"; // Boys' Glove QTY -- exists whether accessories-only or not
    $vals["W-GLOVE-QTY"]="0"; // Glove quantity -- exists whether accessories-only or not
    $vals["W-CANE"]="''"; // Cane style -- exists whether accessories-only or not
    $vals["W-CANE-QTY"]="0"; // Cane quantity -- exists whether accessories-only or not
    $vals["W-SUSP-M"]="0"; // Men's Suspender QTY -- exists whether accessories-only or not
    $vals["W-SUSP-B"]="0"; // Boy's Suspender QTY -- exists whether accessories-only or not
    $vals["W-SUSP-QTY"]="0"; // Suspender quantity -- exists whether accessories-only or not
    
		$shoeOnly=false;
		if ($styleCheck=="" && !empty($_POST["sh_style"])) { $shoeOnly=true; }
    $styleCheck.=$_POST["sh_style"];
    $vals["W-SHOE"]="''"; // Shoe style -- Shoes are blank for non-shoe ticket
    $vals["W-SHOE-COLOR"]="''"; // Shoe color
    $vals["W-SHOE-SIZE"]="''"; // Shoe size
    
    $vals["W-HEIGHT"]="'".mysql_real_escape_string($_POST['height'])."'"; // Height
    $vals["W-WEIGHT"]="'".$_POST['weight']."'"; // Weight
    
    /* Set these based on accessories-only or not */
    if (empty($_POST['accessories'])) {
      $vals["W-TKT-TYPE"]=0;
    
      $vals["W-SASH-ACC"]="''"; // Accessories-only sash style
      $vals["W-SASH-QTY"]="0"; // Accessories-only sash quantity
      
      $vals["W-TIE-ACC"]="''"; // Accessories-only tie style
      $vals["W-TIE-QTY"]="0"; // Accessories-only tie quantity
      $vals["W-TIE-BOYS"]="0"; // Boys' tie quantity
      $vals["W-TIE-MEN"]="0"; // Men's tie quantity
      
      $vals["W-HANKIE-ACC"]="''"; // Accessories-only hankie style
      $vals["W-HANKIE-QTY"]="0"; // Accessories-only hankie quantity
      
      $vals["W-VEST-ACC"]="''"; // Accessories-only vest style
      $vals["W-VEST-QTY"]="0"; // Accessories-only vest total quantity
      $vals["WBS"]="0"; // Accessories-only vest Boys' Small quantity
      $vals["WBM"]="0"; // Accessories-only vest Boy's Medium quantity
      $vals["WBL"]="0"; // Accessories-only vest Boys' Large quantity
      $vals["WMS"]="0"; // Accessories-only vest Men's Small quantity
      $vals["WMM"]="0"; // Accessories-only vest Men's Medium quantity
      $vals["WML"]="0"; // Accessories-only vest Men's Large quantity
      $vals["WMXL"]="0"; // Accessories-only vest Men's Xtra Large total quantity
      $vals["WM2XL"]="0"; // Accessories-only vest Men's Xtra Large total quantity
      $vals["WM3XL"]="0"; // Accessories-only vest Men's Xtra Large total quantity
      $vals["WM4XL"]="0"; // Accessories-only vest Men's Xtra Large total quantity
      $vals["WM5XL"]="0"; // Accessories-only vest Men's Xtra Large total quantity
      $vals["WOTHER"]="0"; // Accessories-only vest Men's Xtra Large total quantity
			
    }
    else {
			$okay=true;
      $vals["W-TKT-TYPE"]=2;
      $vals["W-VEST-ACC"]=(!empty($_POST["vest_a_style"]))?"'".$_POST["vest_a_style"]."'":"''";
      $vals["W-SASH-ACC"]=(!empty($_POST["sash_a_style"]))?"'".$_POST["sash_a_style"]."'":"''";
      $vals["W-VEST"]=$vals["W-VEST-ACC"];
      $vals["W-SASH"]=$vals["W-SASH-ACC"];
      
      $vestSashQty=0;
      $sizes=array("bs", "bm", "bl", "ms", "mm", "ml", "mxl", "m2xl", "m3xl", "m4xl", "m5xl", "other");
      foreach ($sizes as $size) {
        $vestSashQty+=$_POST[$size."_vs_qty"];
        $vals["W".strtoupper($size)]=$_POST[$size."_vs_qty"];
      }
      $vals["W-SASH-QTY"]=(empty($_POST['sash_a_style']))?"0":"".$vestSashQty; // Quantity of sashes if ordered, 0 if not
      
      $vals["W-VEST-ACC"]=(!empty($_POST["vest_a_style"]))?"'".$_POST["vest_a_style"]."'":"''";
      $vals["W-VEST-QTY"]=(empty($_POST['vest_a_style']))?"0":"".$vestSashQty; // Quantity of vests if ordered, 0 if not
      
      $vals["W-TIE-ACC"]=(!empty($_POST["tie_a_style"]))?"'".$_POST["tie_a_style"]."'":"''";
      $vals["W-TIE-BOYS"]=$_POST["boy_a_tie_qty"];
      $vals["W-TIE-MEN"]=$_POST["men_a_tie_qty"];
      $vals["W-TIE-QTY"]=$_POST["boy_a_tie_qty"]+$_POST["men_a_tie_qty"];
      
      $vals["W-HANKIE-ACC"]=(!empty($_POST["hankie_a_style"]))?"'".$_POST["hankie_a_style"]."'":"''";
      $vals["W-HANKIE-QTY"]=$_POST["hankie_a_qty"];
      $vals["W-CANE"]=(!empty($_POST["cane_a_style"]))?"'".$_POST["cane_a_style"]."'":"''";
      $vals["W-CANE-QTY"]=$_POST["cane_a_qty"];
      
      $vals["W-GLOVE-M"]=$_POST["men_a_glove_qty"];
      $vals["W-GLOVE-B"]=$_POST["boy_a_glove_qty"];
      $vals["W-SUSP-M"]=$_POST["men_a_susp_qty"];
      $vals["W-SUSP-B"]=$_POST["men_a_susp_qty"];
    }

    $vals["W-STUDS"]="0"; // Studs
    $vals["W-AMT"]="".$_POST['outfitPrice']; // The outfit price. On the shoe ticket, change this to the shoe price.
    $vals["W-COMMENTS"]="'".mysql_real_escape_string($_POST['comments'])."'"; // Comments
    $vals["W-TKT-PRINTED"]="0"; // Ticket printed?
    $vals["W-TKT-BILLED"]="0"; // Ticket billed?

		$okay=($okay || $styleCheck!=="");

		/* If editing, remember the old ticket for comparison, then remove it from the database */
		$oldTicket=null;
		$error=0;
		if (!empty($_POST['edit']) && $_POST['edit']==1) {
			$hasChanged[0]=false;
			$hasChanged[1]=false;
			/* Save old ticket for later */
			$q="SELECT * FROM `t-work` WHERE `W-TKT`='".mysql_real_escape_string($_POST['ticket'])."' ORDER BY `W-TKT-SUB` ASC";
			$query=mysql_query($q);
			while ($row=mysql_fetch_assoc($query)) {
				if ($oldTicket===null) { $oldTicket=array(); }
				$oldTicket[]=$row;
			}
			
			if ($oldTicket!==null) {
				if ($oldTicket[0]["W-INV-NO"]!="0000000" && $oldTicket[0]["W-INV-NO"]!="") {
					$error=1;
				}
				$vals["W-INV-NO"]="'".$oldTicket[0]["W-INV-NO"]."'";
			}
			
		}
		
    /* Create a SQL query from the data and see if anything's changed from the old ticket if there is one */
		$cols=array();
		$cols[0]="(";
		$values=array();
		$values[0]="(";
		foreach ($vals as $col=>$val) {
			if ($cols[0]!="(") { $cols[0].=", "; }
			if ($values[0]!="(") { $values[0].=", "; }
			if ($col=="b_type") { $col="W-BILL-INST"; }
			else if ($col=="d_type") { $col="W-SHP-INST"; }
			$cols[0].="`".$col."`";
			$values[0].=$val;
			if ($col=="W-TKT-PRINTED") { continue; }
			if ($val[0]=="'" && $val[strlen($val)-1]=="'") { $val=substr($val, 1, strlen($val)-2); }
			if ($oldTicket!==null && !empty($oldTicket[0]) && substr($col, 0, 6)!="W-SHOE" && strpos($col, "-DT")===false && $oldTicket[0][$col]!=$val) {
				$hasChanged[0]=true;
				//echo "Outfit ".$col.": ".$oldTicket[0][$col]." => ".$val."<br />";
			}
		}
		
		$query=true;
				
    /* Create one for the shoe ticket if needed */
    if ($okay && $query && !empty($_POST["sh_style"]) && empty($_POST['accessories'])) {
      $vals["W-TKT-TYPE"]=1;
      $vals["W-TKT-SUB"]="1";
      $vals["W-AMT"]="".$_POST['shoePrice'];
      $vals["W-SHOE"]="'".$_POST['sh_style']."'"; // Shoe style -- Shoes are blank for non-shoe ticket
      $vals["W-SHOE-COLOR"]="'".$_POST['sh_color']."'"; // Shoe color
      $vals["W-SHOE-SIZE"]="'".$_POST['sh_size'].$_POST['sh_wide']."'"; // Shoe size, including wide/boys if applicable

			/* Create a SQL query from the data */
			$cols[1]="(";
			$values[1]="(";
			foreach ($vals as $col=>$val) {
				if ($cols[1]!="(") { $cols[1].=", "; }
				if ($values[1]!="(") { $values[1].=", "; }
				$cols[1].="`".$col."`";
				$values[1].=$val;
				if ($val[0]=="'" && $val[strlen($val)-1]=="'") { $val=substr($val, 1, strlen($val)-2); }
				if ($oldTicket!==null && !empty($oldTicket[1]) && substr($col, 0, 6)=="W-SHOE" && $oldTicket[1][$col]!=$val) {
					$hasChanged[1]=true;
					//echo "Shoe ".$col.": ".$oldTicket[1][$col]." => ".$val."<br/ >";
				}
			}
		}
					
		//echo $hasChanged[0]." , ".$hasChanged[1]." , ".$_POST['print_option'];
		if (isset($hasChanged) && (!$error || (!$hasChanged[0] && !$hasChanged[1] && $_POST['print_option']=="all"))) {
			$q="DELETE FROM `t-work` WHERE `W-TKT`='".mysql_real_escape_string($_POST['ticket'])."'";
			$query=mysql_query($q);
			$error=0;
		}
		if (empty($hasChanged) || ($okay && !$error)) {
			if (!$shoeOnly) {
				$q="INSERT INTO `t-work` ".$cols[0].") VALUES ".$values[0].")";
				$query=mysql_query($q);
			}
			if (!empty($cols[1])) {
				$q="INSERT INTO `t-work` ".$cols[1].") VALUES ".$values[1].")";
				$query=mysql_query($q);
			}
		}

    if ($query && !$error) { ?>
      <b>Ticket added. <?php if ($_POST["print_option"]=="all" || empty($hasChanged) || $hasChanged[0] || $hasChanged[1]) { echo "Please wait while we begin printing."; } ?></b><br />
      <form name="redirect" action="printTicket.php" method="post">
        <input type="hidden" name="ticket" value="<?php echo $_POST['ticket']; ?>" />
        <?php
          foreach ($_POST as $key=>$val) {
            echo "<input type='hidden' name='red_".$key."' value='".addslashes($val)."' />";
          }
					for ($key=0; $key<4; ++$key) {
						echo "<input type='hidden' name='toprint_".$key."' value='".((!isset($hasChanged[$key]) || $hasChanged[$key] || $_POST["print_option"]=="all" || empty($_POST['edit']) || $_POST['edit']==0)?"true":"false")."' />";
					}
        ?>
        <noscript><button type="submit" accesskey="R">Click here if you are not <u>r</u>edirected within 5 seconds.</button></noscript>
      </form>
      <script>
        $(function() { document.redirect.submit(); });
      </script>
     
<?php }
		else if ($error) {
			if (empty($oldTicket[1]["W-AMT"])) { $oldTicket[1]["W-AMT"]=0; }
			echo "<b>You cannot edit a ticket that has already been invoiced. Please issue a credit for the ticket amount ($".number_format($oldTicket[0]["W-AMT"]+$oldTicket[1]["W-AMT"],2).") and create a new ticket to replace it.</b><br /><a href='../index.php'>Click here to return to the main menu</a>";
		}
    else { echo "<b>Ticket creation Failed.</b><br />Please contact the administrator and give them the following error message: ".mysql_error()."<br /><a href='../index.php'>Click here to return to the main menu</a>"; }
  }
  
  mysql_close($link);
?>
	</body>
</html>