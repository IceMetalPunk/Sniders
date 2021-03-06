<html>
  <head>
    <title>Entry Confirmation</title>

    <!-- Main styles -->
    <link rel="stylesheet" href="styles.css" />

    <!-- Styles for autocomplete elements -->
    <link rel="stylesheet" href="jquery-ui.css" />
    <link rel="stylesheet" href="style.css" />

    <!-- jQuery library and its Autocomplete extension -->
    <script type="text/javascript" src="jquery-1.9.1.js"></script>
    <script type="text/javascript" src="jquery-ui.js"></script>
    <script type="text/javascript">
      $(function() {
        document.confirmForm.outfitPrice.focus();
        document.confirmForm.outfitPrice.select();

        $(document.confirmForm.outfitPrice).add(document.confirmForm.shoePrice).blur(function() {
          one=$(document.confirmForm.outfitPrice).val();
          two=$(document.confirmForm.shoePrice).val();
          $("#totalPrice").html((one*1+two*1).toFixed(2));
        });
        
      });
      
      function Validate() {
        var ret=true, error="", num="";
        if (document.confirmForm.outfitPrice.value*1!=document.confirmForm.o_outfitprice.value*1) {
          error+="outfit";
          ret=false;
        }
        if (document.confirmForm.shoePrice.value*1!=document.confirmForm.o_shoeprice.value*1) {
          if (error!="") { error+=" and "; num="s"; }
          error+="shoe";
          ret=false;
        }
        if (ret==false) {
					if (isNaN(document.confirmForm.outfitPrice.value) || document.confirmForm.outfitPrice.value<0) {
						$("#errorbox").html("You've entered an invalid override price for the outfit price. Please check and correct it.");
						document.getElementById("errorbox").style.display="inline";
						window.scrollTo(0,0);
						document.confirmForm.outfitPrice.focus();
						document.confirmForm.outfitPrice.select();
					}
					else if (isNaN(document.confirmForm.shoePrice.value) || document.confirmForm.shoePrice.value<0) {
						$("#errorbox").html("You've entered an invalid override price for the shoe price. Please check and correct it.");
						document.getElementById("errorbox").style.display="inline";
						window.scrollTo(0,0);
						document.confirmForm.shoePrice.focus();
						document.confirmForm.shoePrice.select();
					}
					else {
						$("#errorbox").html("You've overridden the "+error+" price"+num+". Please reconfirm.<br />");
						document.confirmForm.outfitPrice.focus();
						document.confirmForm.outfitPrice.select();
						window.scrollTo(0,0);
						document.confirmForm.o_shoeprice.value=document.confirmForm.shoePrice.value;
						document.confirmForm.o_outfitprice.value=document.confirmForm.outfitPrice.value;
						var one=$(document.confirmForm.outfitPrice).val();
						var two=$(document.confirmForm.shoePrice).val();
						$("#totalPrice").html((one*1+two*1).toFixed(2));
					}
					document.getElementById("errorbox").style.display="inline";
        }
        return ret;
      }
    </script>
  </head>
	
	<?php
		$bigError="";
		if (empty($_POST["c_num"])) {
			$bigError="ERROR: No customer number has been entered. <a href='javascript:history.go(-1)' style='color:#ff0000'>Click here</a> to go back.";
		}
	?>
	
  <body>
    <a href="index.php"><img src="logo.png" /></a>
    <h3>Confirm Data Entry</h3>
    <span id="errorbox" class="smalltext error" <?php if (!empty($bigError)) { echo "style='display:inline'"; } ?>><?php if (!empty($bigError)) { echo $bigError; } ?></span>
    <form action="tickets/makeTicket.php" method="post" onsubmit="return Validate()" name='confirmForm'>
    <?php
    	$link=mysql_connect("localhost", "root", "tux898");
			$db=mysql_select_db("sniders2013", $link);
			
      /* Generate a ticket number based on the master lookup table */
			$isEdit=true;
			if (empty($_POST['ticket'])) {
				$isEdit=false;
				$nextTicket="A000";
		
				$q="SELECT * FROM `t-lookup` WHERE `l-type`='Wrk'";
				$query=mysql_query($q);  
				$row=mysql_fetch_assoc($query);
				$nextnum=1;
				$nextlet="A";
				if (count($row)>0) {
					$letter=$row["l-DESC"]."";
					$num=$row["l-VALUE"];
					
					$nextnum=$num+1;
					$nextlet=$letter;
					if ($nextnum>999) {
						$nextnum=1;
						$nextlet=ord($nextlet)+1;
						if ($nextlet>ord("Z")) { $nextlet="A"; }
						else { $nextlet=chr($nextlet); }
					}
					
					while (strlen($num)<3) { $num="0".$num; }
					$nextTicket=$letter.$num;
					$_POST['ticket']=$nextTicket;
				}
				
				/* Immediately update the lookup table to the next available ticket number to prevent race conditions */
				$q="UPDATE `t-lookup` SET `l-DESC`='".mysql_real_escape_string($nextlet)."', `l-VALUE`=".mysql_real_escape_string($nextnum)." WHERE `l-type`='Wrk'";
				$query=mysql_query($q);  
      }
			
      /* Just make a quick array relating the type codes to their names so we can display the names instead of the codes
         in the confirmation page. Also allows us to check for replacement billing independent of code numbers. */
      $types=array();
      $q="SELECT * FROM `t-lookup` WHERE `l-WRK-TKT`=0";
      $query=mysql_query($q);  
//      echo $q;
//      echo mysql_error();
      while ($row=mysql_fetch_assoc($query)) {
        $types[$row["l-VALUE"]]=$row["l-DESC"];
      }
      
      $price=0;
      $shoeprice=0;
      $total=0;
      /* Calculate the price  if not replacement */
			if ((int)$_POST['c_num']>=70000) { // In-stores default to $0.00, because that's what Barry asked for.
				$price=0;
				$shoeprice=0;
				$total=0;
				$notIn=array();
			}
      else if ($types[$_POST['b_type']]!="Replacement" && $types[$_POST['b_type']]!="Fashion show" && $types[$_POST['b_type']]!="Try On") {
      $items=array(
        "pants"=>!empty($_POST['p_style'])?$_POST['p_style']:"",
        "shirt"=>!empty($_POST['s_style'])?$_POST['s_style']:"",
        "tie"=>!empty($_POST['tie_a_style'])?$_POST['tie_a_style']:"",
        "vest"=>!empty($_POST['vest_a_style'])?$_POST['vest_a_style']:"",
        "sash"=>!empty($_POST['sash_a_style'])?$_POST['sash_a_style']:"",
        "Hankie"=>!empty($_POST['hankie_a_style'])?$_POST['hankie_a_style']:"",
        "Cane"=>!empty($_POST['cane_a_style'])?$_POST['cane_a_style']:"",
        "Glove"=>"Glove"
      );
      
      /* Complete outfit price */
			$notIn=array(); // Keep track of nonexistent styles, if any, for warnings.
      if (empty($_POST['accessories']) && !empty($_POST['complete'])) {
      
        /* Shoes */
        $q="SELECT `P-COMP-PR` FROM `t-price` WHERE `P-Type`='shoe' AND `P-STYLE`='".mysql_real_escape_string($_POST['sh_style'])."'";
        $query=mysql_query($q);
				if (!empty($_POST['sh_style']) && ($query===FALSE || mysql_num_rows($query)<=0)) { $notIn[]="shoe"; }
				else {
					$row=mysql_fetch_assoc($query);
					$shoeprice+=$row['P-COMP-PR'];
				}
      
        /* Coat */
        $q="SELECT `P-COMP-PR` FROM `t-price` WHERE `P-Type`='coat' AND `P-STYLE`='".mysql_real_escape_string($_POST['c_style'])."'";
        $query=mysql_query($q);
				if (!empty($_POST['c_style']) && ($query===FALSE || mysql_num_rows($query)<=0)) { $notIn[]="coat"; }
				else {
					$row=mysql_fetch_assoc($query);
					$price+=$row['P-COMP-PR'];
				}
        
        /* Item upcharges - Take P-Type with it for accessories qty multiplication */
        $items["vest"]=!empty($_POST['a_vest'])?$_POST['a_vest']:"";
        $items["sash"]=!empty($_POST['a_sash'])?$_POST['a_sash']:"";
        $items["tie"]=!empty($_POST['a_tie'])?$_POST['a_tie']:"";
        $items["Hankie"]=!empty($_POST['a_hankie'])?$_POST['a_hankie']:"";
        $items["Cane"]=!empty($_POST['ca_style'])?$_POST['ca_style']:"";
        
        $q="SELECT `P-UPCHARGE-O`,`P-Type` FROM `t-price` WHERE ";
        $itemList="";
        foreach ($items as $item=>$value) {
          if ($itemList!="") { $itemList.=" OR "; }
          $itemList.="(`P-Type`='".mysql_real_escape_string($item)."' AND `P-STYLE`='".mysql_real_escape_string($value)."')";
					if ($value!="" && $item!="Glove") { $notIn[]=$item; }
        }
        $q.=$itemList;
        $query=mysql_query($q);
        
        while ($row=mysql_fetch_assoc($query)) {
          if ($row['P-Type']=="Cane") {
            $price+=$row['P-UPCHARGE-O']*$_POST['ca_qty'];
          }
          else if ($row['P-Type']=="Glove") {
            $price+=$row['P-UPCHARGE-O']*$_POST['gl_qty'];
          }
          else {
            $price+=$row['P-UPCHARGE-O'];
          }
					$ind=array_search($row['P-Type'], $notIn);
					if ($ind!==FALSE) { unset($notIn[$ind]); }
          
        }
      }

      /* Calculate accessories-only and a la carte pricing */
      else {
      
        $items["shoe"]=!empty($_POST['sh_style'])?$_POST['sh_style']:"";      
        
        if (empty($_POST['accessories'])) { // A la carte prices
          $items["coat"]=!empty($_POST['c_style'])?$_POST['c_style']:"";
        }
        else { // Accessories-only prices -- remove other items
          $items["coat"]="";
          $items["pants"]="";
          $items["shirt"]="";
          $items["shoe"]="";
        }
        
        $q="SELECT `P-ITEM`,`P-Type` FROM `t-price` WHERE ";
        $itemList="";
        foreach ($items as $item=>$value) {
          if ($itemList!="") { $itemList.=" OR "; }
          $itemList.="(`P-Type`='".mysql_real_escape_string($item)."' AND `P-STYLE`='".mysql_real_escape_string($value)."')";
					if ($value!="" && $item!="Glove") { $notIn[]=$item; }
        }
        $q.=$itemList;
        //print_r($q);
        $query=mysql_query($q);

        while ($row=mysql_fetch_assoc($query)) {
          if ($row['P-Type']=="shoe") {
            $shoeprice+=$row['P-ITEM'];
          }
          else if ($row['P-Type']=="vest") {
            $price+=$row['P-ITEM']*($_POST['bs_vs_qty']+$_POST['bm_vs_qty']+$_POST['bl_vs_qty']+$_POST['other_vs_qty']+$_POST['ms_vs_qty']+$_POST['mm_vs_qty']+$_POST['ml_vs_qty']+$_POST['mxl_vs_qty']);
          }
          else if ($row['P-Type']=="tie") {
            $price+=$row['P-ITEM']*($_POST['men_a_tie_qty']+$_POST['boy_a_tie_qty']);
          }
          else if ($row['P-Type']=="Hankie") {
            $price+=$row['P-ITEM']*$_POST['hankie_a_qty'];
          }          
          else if ($row['P-Type']=="Cane") {
            $price+=$row['P-ITEM']*$_POST['cane_a_qty'];
          }
          else if ($row['P-Type']=="Glove") {
            $price+=$row['P-ITEM']*($_POST['men_a_glove_qty']+$_POST['boy_a_glove_qty']);
          }
          else {
            $price+=$row['P-ITEM'];
          }
					$ind=array_search($row['P-Type'], $notIn);
					if ($ind!==FALSE) { unset($notIn[$ind]); }
        }
      }
      }
      
			$q="SELECT `C-DISC-OUTFIT`, `C-DISC-SHOE` from `t-customer` WHERE `C-CUSTNO`='".mysql_real_escape_string($_POST['c_num'])."'";
			$query=mysql_query($q);
			$discountInfo=mysql_fetch_assoc($query);
			
      $total=$price+$shoeprice;
			
			/* Output missing style errors if need be */
			if (isset($notin) && count($notIn)>0) {
				echo "<span style='font-size: 12pt; font-weight:bold; color:#aa0000'>The following styles are not in the database and have defaulted to $0.00:<ul>";
				foreach ($notIn as $val) {
					echo "<li style='font-size:12pt'>".ucwords($val)." - ".$items[$val]."</li>";
				}
				echo "</ul></span>";
			}
			
		?>
		<table border=0 class='smalltext' style="border-collapse:collapse">
		<?php
			echo "<tr style='font-weight:bold'><td>Ticket Number: </td><td>".$_POST['ticket'];
			if ($isEdit) {
					echo " (Editing)";
					$oldTotal=array(0, 0);
					$q="SELECT `W-AMT` FROM `t-work` WHERE `W-TKT`='".mysql_real_escape_string($_POST['ticket'])."' AND `W-TKT-SUB`=0";
					$query=mysql_query($q);
					if (mysql_num_rows($query)>0) {
						$row=mysql_fetch_assoc($query);
						$oldTotal[0]=$row["W-AMT"];
					}
					$q="SELECT `W-AMT` FROM `t-work` WHERE `W-TKT`='".mysql_real_escape_string($_POST['ticket'])."' AND `W-TKT-SUB`=1";
					$query=mysql_query($q);
					if (mysql_num_rows($query)>0) {
						$row=mysql_fetch_assoc($query);
						$oldTotal[1]=$row["W-AMT"];
					}
				}
			echo "</td></tr>";
    ?>
    <?php
      /* We want all the data posted to this page to be passed onto the confirmation page. So we build a hidden form with all
         the data filled out and ready to be submitted. */
      foreach ($_POST as $field=>$value) {
        echo "<input type='hidden' name='".$field."' value='".htmlentities($value, ENT_QUOTES)."' />";
      }
			echo "<input type='hidden' name='edit' value='".($isEdit?"1":"0")."' />";
    ?>
    <tr>
      <td>Customer: </td>
      <td><?php echo $_POST['c_name']." (".$_POST['c_num'].")"; ?></td>
    </tr>
    
    <tr>
      <td>Delivery Type: </td>
      <td><?php echo $types[$_POST['d_type']]; ?>
    </tr>
    
    <tr>
      <td>Billing Type: </td>
      <td><?php echo $types[$_POST['b_type']]; ?></td>
    </tr>
    
    <tr>
      <td>Date of Use: </td>
      <td><?php echo $_POST['full_use_date']; ?></td>
    </tr>
    
    <tr>
      <td>Reference: </td>
      <td><?php echo $_POST['ref']; ?></td>
    </tr>

    <tr>
      <td style="border-top:1px solid #aaaaaa">Complete Outfit? </td>
      <td style="border-top:1px solid #aaaaaa"><?php if (!empty($_POST['complete'])) { echo "Yes"; } else { echo "No"; } ?></td>
    </tr>
    
    <?php
      if (empty($_POST['accessories'])) {
    ?>
    
    <tr>
      <td>Coat: </td>
      <td><?php echo $_POST['c_style']." - Size ".$_POST['c_size']." - Sleeve ".$_POST['c_sleeve']; ?></td>
    </tr>
    
    <tr>
      <td>Pants: </td>
      <td><?php echo $_POST['p_style']." - Waist ".$_POST['p_waist']." - Length ".$_POST['p_length']." - Seat ".$_POST['p_seat']; ?></td>
    </tr>
    
    <tr>
      <td>Shirt: </td>
      <td><?php echo $_POST['s_style']." - Size ".$_POST['s_size']; ?></td>
    </tr>
    
    <tr>
      <td>Accessories: </td>
      <td><?php
        $dash=0;
        if (!empty($_POST['a_vest'])) {
          echo "Vest ".$_POST['a_vest'];
          $dash=1;
        }
        if (!empty($_POST['a_sash'])) {
          if ($dash) { echo " - "; }
          echo "Sash ".$_POST['a_sash'];
          $dash=1;
        }
        if (!empty($_POST['a_tie'])) {
          if ($dash) { echo " - "; }
          echo "Tie ".$_POST['a_tie'];
          $dash=1;
        }
        if (!empty($_POST['a_hankie'])) {
          if ($dash) { echo " - "; }
          echo "Hankie ".$_POST['a_hankie'];
          $dash=1;
        }
        
      ?></td>
    </tr>

    <?php if (!empty($_POST['sh_style'])) { ?>
    <tr>
      <td>Shoes: </td>
      <td><?php echo $_POST['sh_style']." - ".$_POST['sh_color']." - Size ".$_POST['sh_size']." ".$_POST['sh_wide']; ?></td>
    </tr>
    <?php } ?>
    
    <tr>
      <td>Extras: </td>
      <td><?php
        if (!empty($_POST['ca_style'])) {
          echo "Cane: ".$_POST['ca_style']." (".$_POST['ca_qty'].") - ";
        }
        else { echo "Cane: (0) - "; }
        echo "Glove: (".$_POST['gl_qty'].") - Suspenders: (".$_POST['sus_qty'].")";
      ?></td>
    </tr>
    
    <?php } else { ?>
    <tr>
      <td>Vest: </td>
      <td><?php
        if (!empty($_POST['vest_a_style'])) {
          echo $_POST['vest_a_style'];
          $which=array(
            "MS"=>$_POST['ms_vs_qty'],
            "MM"=>$_POST['mm_vs_qty'],
            "ML"=>$_POST['ml_vs_qty'],
            "MXL"=>$_POST['mxl_vs_qty'],
						"M2XL"=>$_POST['m2xl_vs_qty'],
						"M3XL"=>$_POST['m3xl_vs_qty'],
						"M4XL"=>$_POST['m4xl_vs_qty'],
						"M5XL"=>$_POST['m5xl_vs_qty'],
            "BS"=>$_POST['bs_vs_qty'],
            "BM"=>$_POST['bm_vs_qty'],
            "BL"=>$_POST['bl_vs_qty'],
            "Other"=>$_POST['other_vs_qty']
          );
          $vestTotal=0;
          foreach ($which as $name=>$amt) {
            if ($amt>0) {
              echo " - ".$amt." ".$name;
              $vestTotal+=$amt;
            }
          }
          echo " - Total: <b>".$vestTotal."</b>";
        }
      ?>
    </tr>
    
    <tr>
      <td>Tie: </td>
      <td><?php
        if (!empty($_POST['tie_a_style'])) {
          echo $_POST['tie_a_style']." - ".$_POST['men_a_tie_qty']." Men's - ".$_POST['boy_a_tie_qty']." Boy's - ";
          echo "Total: <b>".($_POST['men_a_tie_qty']+$_POST['boy_a_tie_qty'])."</b>";
        }
      ?></td>
    </tr>
    
    <tr>
      <td>Hankie: </td>
      <td><?php
        if (!empty($_POST['hankie_a_style'])) {
          echo $_POST['hankie_a_style']." - Total: <b>".$_POST['hankie_a_qty']."</b>";
        }
      ?></td>
      
      <tr>
        <td>Cane: </td>
        <td><?php
          if (!empty($_POST['cane_a_style'])) {
            echo $_POST['cane_a_style']." - Total: <b>".$_POST['cane_a_qty']."</b>";
          }
        ?></td>
      </tr>
      
      <tr>
        <td>Gloves: </td>
        <td><?php
          echo $_POST['men_a_glove_qty']." Men's - ".$_POST['boy_a_glove_qty']." - ";
          echo "Total: <b>".($_POST['men_a_glove_qty']+$_POST['boy_a_glove_qty'])."</b>";
        ?></td>
      </tr>
      
      <tr>
        <td>Suspenders: </td>
        <td><?php
          echo $_POST['men_a_susp_qty']." Men's - ".$_POST['boy_a_susp_qty']." - ";
          echo "Total: <b>".($_POST['men_a_susp_qty']+$_POST['boy_a_susp_qty'])."</b>";
        ?></td>
      </tr>
      
    </tr>
    <?php } ?>
    
    <tr style='font-size:14pt'>
      <td style="border-top:1px solid #aaaaaa; font-size:14pt">Outfit Price: </td>
      <td style="border-top:1px solid #aaaaaa; font-size:14pt"><input type='hidden' name='o_outfitprice' value='<?php echo number_format($isEdit?$oldTotal[0]:$price, 2); ?>' />$<input name='outfitPrice' type='text' value='<?php echo number_format($isEdit?$oldTotal[0]:$price, 2); ?>' />
			<?php
				if ($discountInfo["C-DISC-OUTFIT"]>0) {
					echo '<a href="javascript:document.confirmForm.outfitPrice.value='."'".number_format($discountInfo["C-DISC-OUTFIT"], 2)."'".'; $(document.confirmForm.outfitPrice).blur(); document.confirmForm.outfitPrice.focus(); confirmForm.outfitPrice.select(); void(0)">(Use special price of $'.number_format($discountInfo["C-DISC-OUTFIT"], 2).')</a>';
				}
				if ($isEdit) {
					echo ' <a href="javascript:document.confirmForm.outfitPrice.value='."'".number_format($price, 2)."'".'; $(document.confirmForm.outfitPrice).blur(); document.confirmForm.outfitPrice.focus(); confirmForm.outfitPrice.select(); void(0)"><small>(Use new calculated price of $'.number_format($price, 2).')</small></a>';
					//echo " <small>(Original ticket price: $".number_format($oldTotal[0], 2).")</small>";
				}
			?>
			</td>
    </tr>
    
    <tr style='font-size:14pt'>
      <td style='font-size:14pt'>Shoe Price: </td>
      <td style='font-size:14pt'><input type='hidden' name='o_shoeprice' value='<?php echo number_format(($isEdit && !empty($oldTotal[1]))?$oldTotal[1]:$shoeprice, 2); ?>' />$<input name='shoePrice' type='text' value='<?php echo number_format(($isEdit && !empty($oldTotal[1]))?$oldTotal[1]:$shoeprice, 2); ?>' />
			<?php
				if ($discountInfo["C-DISC-SHOE"]>0) {
					echo '<a href="javascript:document.confirmForm.shoePrice.value='."'".number_format($discountInfo["C-DISC-SHOE"], 2)."'".'; void(0)">(Use special price of $'.number_format($discountInfo["C-DISC-SHOE"], 2).')</a>';
				}
				if ($isEdit && !empty($oldTotal[1])) {
					echo ' <a href="javascript:document.confirmForm.shoePrice.value='."'".number_format($shoeprice, 2)."'".'; void(0)"><small>(Use new calculated price of $'.number_format($shoeprice, 2).')</small></a>';
					//echo " <small>(Original ticket price: $".number_format($oldTotal[1], 2).")</small>";
				}
			?>
			</td>
    </tr>
    
    <tr style='font-size:14pt'>
      <td style='font-size:14pt'>Total Price: </td>
      <td style="font-weight:bold; font-size:14pt">$<span id='totalPrice'><?php echo number_format($total, 2); ?></span></td>
    </tr>
    
    <tr>
      <td style="border-top:1px solid #aaaaaa">Height/Weight: </td>
      <td style="border-top:1px solid #aaaaaa"><?php echo $_POST['height']." / ".$_POST['weight']; ?></td>
    </tr>
    
    <tr>
      <td>Comments:</td>
      <td><?php echo $_POST['comments']; ?></td>
    </tr>
    <?php
			if ($isEdit) {
		?>
		<tr><td colspan='2'>&nbsp;</td></tr>
		<tr>
			<td colspan='2'><label for='print_changed'><input type='radio' name='print_option' value='changed' checked id='print_changed' />Print tickets only where changed.</label><label for='print_all'><input type='radio' name='print_option' value='all' id='print_all' />Print all tickets.</label></td>
		</tr>
		<tr><td colspan='2'>&nbsp;</td></tr>
		<?php }
			else {
				echo "<input type='hidden' name='print_option' value='all' />";
			}
		?>
    <tr>
      <td colspan='2'><?php if (empty($bigError)) { ?><button accesskey="C" type="submit" value="Confirm"><u>C</u>onfirm</button><?php } ?></td>
    </tr>
    </table>
    </form>
  </body> 
</html>
<?php mysql_close($link); ?>