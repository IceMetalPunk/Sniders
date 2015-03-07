<?php
	$link=mysql_connect("localhost", "root", "tux898");
	$db=mysql_select_db("sniders2013");
	
	$isEdit=false;
	$editData=array();
	if (!empty($_GET['edit'])) {
		$q="SELECT * FROM `t-work` WHERE `W-TKT`='".mysql_real_escape_string($_GET['edit'])."' ORDER BY `W-TKT-SUB` ASC";
		$query=mysql_query($q);
		if (mysql_num_rows($query)>0) {
			$isEdit=true;
			while ($row=mysql_fetch_assoc($query)) {
				$editData[]=$row;
			}
			if (count($editData)<2) { $editData[]=$editData[0]; }
			$editDate=$editData[0]["W-USE-DT"];
			$editData[0]["C-PHONE"]="";
			$editData[0]["C-PHONE2"]="";
			$editDate=strtotime($editDate);
			$editDate=date("n/j/Y", $editDate);
			
			$q="SELECT `C-PHONE`, `C-PHONE2` FROM `t-customer` WHERE `C-CUSTNO`='".$editData[0]["W-CUSTNO"]."'";
			$query=mysql_query($q);
			if (mysql_num_rows($query)>0) {
				$row=mysql_fetch_assoc($query);
				$editData[0]["C-PHONE"]=$row["C-PHONE"];
				$editData[0]["C-PHONE2"]=$row["C-PHONE2"];
			}
			
		}
	}
	
	mysql_close($link);
?>

<html>
  <head>
    <title>Data Entry</title>

    <!-- Main styles -->
    <link rel="stylesheet" href="styles.css" />

    <!-- Styles for autocomplete elements -->
    <link rel="stylesheet" href="jquery-ui.css" />
    <link rel="stylesheet" href="jquery-style.css" />

    <!-- jQuery library and its Autocomplete extension -->
    <script type="text/javascript" src="jquery-1.9.1.js"></script>
    <script type="text/javascript" src="jquery-ui.js"></script>

    <!-- Main Javascript library, including data pulled from the databases, hence why it must be in .php format -->
    <script type="text/javascript" src="scripts.php?<?php echo time(); ?>"></script>
  </head>
  <body onLoad="Initialize(<?php if (!empty($_POST['redirected']) && $_POST['redirected']==1) { echo "false"; } else { echo "true"; } ?>)">

    <!-- Main data entry form -->
    <form action="confirmEntry.php" method="post" name="entry">
      <span style="float:left">
      <!-- Today's date, followed by fields for customer number and customer name -->
      <?php echo date("n/j/Y"); ?> - <input type="text" name="c_num" class="numOnly" placeholder="Customer Number" maxlength=5 size=15 <?php
        if (!empty($_POST['redirected']) && $_POST['redirected']=="1") {
          echo "value='".$_POST['red_custno']."' ";
        }
				else if ($isEdit) {
					echo "value='".($editData[0]["W-CUSTNO"])."' ";
				}
      ?>/> <input type="text" id="c_name" name="c_name" size=40 maxlength=40 placeholder="Customer Name" />
      <br/>
      
      <?php if ((!empty($_POST['redirected']) && $_POST['redirected']=="1") || $isEdit) { ?>
      <script>
        document.body.onload=function() {
					if (document.entry.c_num.value=="99999" || document.entry.c_num.value*1>=70000) {
						ShowInHouse();
						if (document.entry.c_num.value!="99999") { GetCustomer(document.entry.c_num.value, true); }
					}
					else {
						GetCustomer(document.entry.c_num.value, true);
						document.entry.date_use.focus();
						document.entry.date_use.select();
					}
        };
      </script>
      <?php  } else if ($isEdit) { ?>
			<script>
        $(function() {
					if (parseInt(document.entry.c_num.value)>=70000) {
						ShowInHouse();
					}
					else { GetCustomer(document.entry.c_num.value, true); }
        });
      </script>
			<?php } ?>
      
      <!-- Dropdowns for delivery type and billing/payment type -->
      <select name="d_type">
				<?php
					$Types[201]="Delivery";
					$Types[202]="Pickup";
					$Types[203]="UPS";
					$Types[204]="UPS Priority";
					$Types[205]="UPS Air";
					$Types[206]="FedEx";
					$Types[207]="Delivery Today";
					$Types[208]="Pickup Today";
					$Types[209]="Other";
					foreach ($Types as $i=>$name) {
						echo "<option value='".$i."'";
						if ($isEdit && $editData[0]["W-SHP-INST"]==$i) { echo " selected='selected'"; }
						echo ">".$name."</option>";
					}
				?>
      </select>
      <select name="b_type">
				<?php
					$billTypes[201]="Standard";
					$billTypes[202]="Standard-Discount";
					$billTypes[203]="COD";
					$billTypes[204]="COD-Cash";
					$billTypes[205]="Try-On";
					$billTypes[206]="Replacement";
					$billTypes[207]="Fashion Show";
					$billTypes[208]="Standard-Discount 2";
					foreach ($billTypes as $i=>$name) {
						echo "<option value='".$i."'";
						if ($isEdit && $editData[0]["W-BILL-INST"]==$i) { echo " selected='selected'"; }
						echo ">".$name."</option>";
					}
				?>
      </select>
      <br />
      <div id="inHouseSpot" style="display:none">Cell Phone&nbsp;&nbsp;&nbsp; <input type='text' name='cellPhone' value='<?php
				if ($isEdit) { echo $editData[0]["C-PHONE2"]; }
			?>' /><br/>
      Home Phone <input type='text' name='homePhone' value='<?php
				if ($isEdit) { echo $editData[0]["C-PHONE"]; }
			?>' /></div>
      </span>
      <!-- Logo that links to the main menu -->
      <a href="index.php"><span style="float:right"><img src="logo.png" /></span></a><br clear="both" />
      <!-- Main section -->
      <h3>Main</h3>

      <!-- Field for date of use and reference name, and checkbox for complete outfit or accessories only -->
      <input name="date_use" id="use_date" placeholder="Date of Use (MM/DD)" type="text"<?php
        if (!empty($_POST['redirected']) && $_POST['redirected']=="1") { 
          echo ' value="'.addslashes($_POST['red_usedate']).'"';
        }
				else if ($isEdit) { echo "value='".htmlentities($editDate, ENT_QUOTES)."'"; } ?>/><input type="hidden" name="full_use_date"<?php
        if (!empty($_POST['redirected']) && $_POST['redirected']=="1") { 
          echo ' value="'.addslashes($_POST['red_usedate']).'"';
        }
				else if ($isEdit) { echo "value='".htmlentities($editDate, ENT_QUOTES)."'"; } ?>/> <input name="ref" type="text" size=40 maxlength=50 placeholder="Reference" <?php if ($isEdit) { echo "value='".htmlentities($editData[0]["W-REF"], ENT_QUOTES)."'"; } ?>/> <input type="checkbox" name="complete" value="true" id="compBox" checked=true /><label for="compBox">Complete Outfit</label> <input type="checkbox" name="accessories" value="true" id="accBox" /><label for="accBox">Accessories Only</label>

      <!-- Main table with item information fields -->
      <div id="mainForm">
      <table border=0 class='entryTable'>

        <!-- Coat row -->
        <tr>
          <td>Coat</td>
          <td><input id="coat_style" name="c_style" type="text" size=5 <?php if ($isEdit) { echo "value='".htmlentities($editData[0]["W-COAT"], ENT_QUOTES)."' "; } ?>/></td>
          <td>Size</td>
          <td><input name="c_size" type="text" size=5 <?php if ($isEdit) { echo "value='".htmlentities($editData[0]["W-COAT-SZ"], ENT_QUOTES)."' "; } ?>/></td>
          <td>Sleeve</td>
          <td colSpan='3'><input name="c_sleeve" type="text" size=5 <?php if ($isEdit) { echo "value='".htmlentities($editData[0]["W-COAT-SLV"], ENT_QUOTES)."' "; } ?>/></td>
        </tr>

        <!-- Pants row -->
        <tr>
          <td>Pants</td>
          <td><input id="pants_style" name="p_style" type="text" size=5 <?php if ($isEdit) { echo "value='".htmlentities($editData[0]["W-PANT-STYLE"], ENT_QUOTES)."' "; } ?>/></td>
          <td>Waist</td>
          <td><input name="p_waist" type="text" size=5 <?php if ($isEdit) { echo "value='".htmlentities($editData[0]["W-WAIST"], ENT_QUOTES)."' "; } ?>/></td>
					<td>Seat</td>
          <td><input name="p_seat" type="text" size=5 <?php if ($isEdit) { echo "value='".htmlentities($editData[0]["W-PANTS-SEAT"], ENT_QUOTES)."' "; } ?>/></td>
          <td>Length</td>
          <td><input name="p_length" type="text" size=5 <?php if ($isEdit) { echo "value='".htmlentities($editData[0]["W-PANT-LEN"], ENT_QUOTES)."' "; } ?>/></td>
        </tr>

        <!-- Shirt row -->
        <tr>
          <td>Shirt</td>
          <td><input id="shirt_style" name="s_style" type="text" size=5 <?php if ($isEdit) { echo "value='".htmlentities($editData[0]["W-SHIRT"], ENT_QUOTES)."' "; } ?>/></td>
          <td>Size</td>
          <td><input name="s_size" type="text" size=5 <?php if ($isEdit) { echo "value='".htmlentities($editData[0]["W-SHIRT-SIZE"], ENT_QUOTES)."' "; } ?>/></td>
        </tr>

        <!-- Vest, sash, tie and hankie row -->
        <tr>
          <td>Vest</td>
          <td><input id="vest_style" name="a_vest" type="text" size=5 <?php if ($isEdit) { echo "value='".htmlentities($editData[0]["W-VEST"], ENT_QUOTES)."' "; } ?>/></td>
          <td>Sash</td>
          <td><input id="sash_style" name="a_sash" type="text" size=5 <?php if ($isEdit) { echo "value='".htmlentities($editData[0]["W-SASH"], ENT_QUOTES)."' "; } ?>/></td>
          <td>Tie</td>
          <td><input id="tie_style" name="a_tie" type="text" size=5 <?php if ($isEdit) { echo "value='".htmlentities($editData[0]["W-TIE"], ENT_QUOTES)."' "; } ?>/></td>
          <td>Hankie</td>
          <td><input id="hankie_style" name="a_hankie" type="text" size=5 <?php if ($isEdit) { echo "value='".htmlentities($editData[0]["W-HANKIE"], ENT_QUOTES)."' "; } ?>/></td>
        </tr>

        <!-- Shoes row -->
        <tr>
          <td>Shoe</td>
          <td><input id="shoe_style" name="sh_style" type="text" size=5 <?php if ($isEdit) { echo "value='".htmlentities($editData[1]["W-SHOE"], ENT_QUOTES)."' "; } ?>/></td>
          <td>Color</td>
          <td><select name="sh_color"><option value=""></option><?php
						$shoeColors=array(
							"black"=>"Black",
							"white"=>"White",
							"silver"=>"Silver",
							"ivory"=>"Ivory"
						);
						foreach ($shoeColors as $val=>$txt) {
							echo "<option value='".$val."' ";
							if ($isEdit && $val==$editData[1]["W-SHOE-COLOR"]) { echo "selected "; }
							echo ">".$txt."</option>";
						}
					?>
					</select></td>
          <td>Size</td>
          <td><input name="sh_size" type="text" size=5 <?php if ($isEdit) { echo "value='".htmlentities($editData[1]["W-SHOE-SIZE"], ENT_QUOTES)."' "; } ?>/></td>
          <td colSpan='3'><input type="radio" name="sh_wide" value="" id="shNormal" checked /><label for="shNormal">Normal</label> <input type="radio" name="sh_wide" value="W" id="shWide" /><label for="shWide">Wide</label> <input type="radio" name="sh_wide" value="B" id="shBoys" /><label for="shBoys">Boys</label></td>
        </tr>
      </table>

       <!-- Accessories row -->
     <table border=0 class="bottomRow">
       <tr>
          <td>Cane Color</td>
          <td><input id="Cane_style" name="ca_style" type="text" size=5 <?php if ($isEdit) { echo "value='".htmlentities($editData[0]["W-CANE"], ENT_QUOTES)."' "; } ?>/></td>
          <td>Qty</td>
          <td><select name="ca_qty">
					<?php
						for ($i=0; $i<=9; ++$i) {
							echo "<option value='".$i."' ";
							if ($isEdit && $i==$editData[0]["W-CANE-QTY"]) { echo "selected "; }
							echo ">".$i."</option>";
						}
					?>					
					</select></td>
          <td>Glove Qty
          <td><select name="gl_qty">
					<?php
						for ($i=0; $i<=9; ++$i) {
							echo "<option value='".$i."' ";
							if ($isEdit && $i==$editData[0]["W-GLOVE-QTY"]) { echo "selected "; }
							echo ">".$i."</option>";
						}
					?>	
					</select></td>
          <td>Susp</td>
          <td><select name="sus_qty">
					<?php
						for ($i=0; $i<=9; ++$i) {
							echo "<option value='".$i."' ";
							if ($isEdit && $i==$editData[0]["W-SUSP-QTY"]) { echo "selected "; }
							echo ">".$i."</option>";
						}
					?>	
					</select></td>
        </tr>

      </table>
      </div>
      
      <!-- Accessories table with item information fields -->
      <div id="accForm">
      <table border=0 class='entryTable'>

        <!-- Coat row -->
        <tr>
          <td>Vest</td>
          <td><input id="vest_a_style" name="vest_a_style" type="text" size=5 <?php if ($isEdit) { echo "value='".htmlentities($editData[0]["W-VEST-ACC"], ENT_QUOTES)."' "; } ?>/></td>
          <td>Sash</td>
          <td colSpan='5'><input id="sash_a_style" name="sash_a_style" type="text" size=5 <?php if ($isEdit) { echo "value='".htmlentities($editData[0]["W-SASH-ACC"], ENT_QUOTES)."' "; } ?>/></td>
        </tr>
        <tr>
          <td>MS</td>
          <td><select name="ms_vs_qty">
					<?php
						for ($i=0; $i<=9; ++$i) {
							echo "<option value='".$i."' ";
							if ($isEdit && $i==$editData[0]["WMS"]) { echo "selected "; }
							echo ">".$i."</option>";
						}
					?>	
					</td>
          <td>MM</td>
          <td><select name="mm_vs_qty">
					<?php
						for ($i=0; $i<=9; ++$i) {
							echo "<option value='".$i."' ";
							if ($isEdit && $i==$editData[0]["WMM"]) { echo "selected "; }
							echo ">".$i."</option>";
						}
					?>	
					</td>
          <td>ML</td>
          <td><select name="ml_vs_qty">
					<?php
						for ($i=0; $i<=9; ++$i) {
							echo "<option value='".$i."' ";
							if ($isEdit && $i==$editData[0]["WML"]) { echo "selected "; }
							echo ">".$i."</option>";
						}
					?>	
					</select></td>
          <td>MXL</td>
          <td><select name="mxl_vs_qty">
					<?php
						for ($i=0; $i<=9; ++$i) {
							echo "<option value='".$i."' ";
							if ($isEdit && $i==$editData[0]["WMXL"]) { echo "selected "; }
							echo ">".$i."</option>";
						}
					?>	
					</select></td>
        </tr>
        <tr>
          <td>M2XL</td>
          <td><select name="m2xl_vs_qty">
					<?php
						for ($i=0; $i<=9; ++$i) {
							echo "<option value='".$i."' ";
							if ($isEdit && $i==$editData[0]["WM2XL"]) { echo "selected "; }
							echo ">".$i."</option>";
						}
					?>	
					</td>
          <td>M3XL</td>
          <td><select name="m3xl_vs_qty">
					<?php
						for ($i=0; $i<=9; ++$i) {
							echo "<option value='".$i."' ";
							if ($isEdit && $i==$editData[0]["WM3XL"]) { echo "selected "; }
							echo ">".$i."</option>";
						}
					?>	
					</td>
          <td>M4XL</td>
          <td><select name="m4xl_vs_qty">
					<?php
						for ($i=0; $i<=9; ++$i) {
							echo "<option value='".$i."' ";
							if ($isEdit && $i==$editData[0]["WM4XL"]) { echo "selected "; }
							echo ">".$i."</option>";
						}
					?>	
					</select></td>
          <td>M5XL</td>
          <td><select name="m5xl_vs_qty">
					<?php
						for ($i=0; $i<=9; ++$i) {
							echo "<option value='".$i."' ";
							if ($isEdit && $i==$editData[0]["WM5XL"]) { echo "selected "; }
							echo ">".$i."</option>";
						}
					?>	
					</select></td>
        </tr>
        <tr>
          <td>BS</td>
          <td><select name="bs_vs_qty">
					<?php
						for ($i=0; $i<=9; ++$i) {
							echo "<option value='".$i."' ";
							if ($isEdit && $i==$editData[0]["WBS"]) { echo "selected "; }
							echo ">".$i."</option>";
						}
					?>	
					</select></td>
          <td>BM</td>
          <td><select name="bm_vs_qty">
					<?php
						for ($i=0; $i<=9; ++$i) {
							echo "<option value='".$i."' ";
							if ($isEdit && $i==$editData[0]["WBM"]) { echo "selected "; }
							echo ">".$i."</option>";
						}
					?>	
					</select></td>
          <td>BL</td>
          <td><select name="bl_vs_qty">
					<?php
						for ($i=0; $i<=9; ++$i) {
							echo "<option value='".$i."' ";
							if ($isEdit && $i==$editData[0]["WBL"]) { echo "selected "; }
							echo ">".$i."</option>";
						}
					?>	
					</select></td>
          <td>Other</td>
          <td><select name="other_vs_qty">
					<?php
						for ($i=0; $i<=9; ++$i) {
							echo "<option value='".$i."' ";
							if ($isEdit && $i==$editData[0]["WOTHER"]) { echo "selected "; }
							echo ">".$i."</option>";
						}
					?>	
					</select></td>
        </tr>
        <tr>
          <td colSpan='8'>Total: <span id='vs_qty_total'>0</span></td>
        </tr>
        <tr>
          <td>Tie</td>
          <td><input id="tie_a_style" name="tie_a_style" type="text" size=5 <?php if ($isEdit) { echo "value='".htmlentities($editData[0]["W-TIE-ACC"], ENT_QUOTES)."' "; } ?>/></td>
          <td>Men's</td>
          <td><select name="men_a_tie_qty">
					<?php
						for ($i=0; $i<=9; ++$i) {
							echo "<option value='".$i."' ";
							if ($isEdit && $i==$editData[0]["W-TIE-MEN"]) { echo "selected "; }
							echo ">".$i."</option>";
						}
					?>	
					</select></td>
          <td>Boy's</td>
          <td><select name="boy_a_tie_qty">
					<?php
						for ($i=0; $i<=9; ++$i) {
							echo "<option value='".$i."' ";
							if ($isEdit && $i==$editData[0]["W-TIE-BOYS"]) { echo "selected "; }
							echo ">".$i."</option>";
						}
					?>	
					</select></td>
          <td colSpan='2'>Total: <span id="a_tie_qty_total">0</span></td>
        </tr>
        <tr>
          <td>Hankie</td>
          <td><input id="hankie_a_style" name="hankie_a_style" type="text" size=5 <?php if ($isEdit) { echo "value='".htmlentities($editData[0]["W-HANKIE-ACC"], ENT_QUOTES)."' "; } ?>/></td>
          <td>Qty</td>
          <td colSpan='5'><select name="hankie_a_qty">
					<?php
						for ($i=0; $i<=9; ++$i) {
							echo "<option value='".$i."' ";
							if ($isEdit && $i==$editData[0]["W-HANKIE-QTY"]) { echo "selected "; }
							echo ">".$i."</option>";
						}
					?>	
					</select></td>
        </tr>
        <tr>
          <td>Cane</td>
          <td><input id="Cane_a_style" name="cane_a_style" type="text" size=5 <?php if ($isEdit) { echo "value='".htmlentities($editData[0]["W-CANE"], ENT_QUOTES)."' "; } ?>/></td>
          <td>Qty</td>
          <td colSpan='5'><select name="cane_a_qty">
					<?php
						for ($i=0; $i<=9; ++$i) {
							echo "<option value='".$i."' ";
							if ($isEdit && $i==$editData[0]["W-CANE-QTY"]) { echo "selected "; }
							echo ">".$i."</option>";
						}
					?>	
					</select></td>
        </tr>
        <tr>
          <td>Gloves</td>
          <td>Men's</td>
          <td><select name="men_a_glove_qty">
					<?php
						for ($i=0; $i<=9; ++$i) {
							echo "<option value='".$i."' ";
							if ($isEdit && $i==$editData[0]["W-GLOVE-M"]) { echo "selected "; }
							echo ">".$i."</option>";
						}
					?>	
					</select></td>
          <td>Boy's</td>
          <td><select name="boy_a_glove_qty">
					<?php
						for ($i=0; $i<=9; ++$i) {
							echo "<option value='".$i."' ";
							if ($isEdit && $i==$editData[0]["W-GLOVE-B"]) { echo "selected "; }
							echo ">".$i."</option>";
						}
					?>	
					</select></td>
          <td colSpan='3'>Total: <span id="a_glove_qty_total">0</span></td>
        </tr>
        <tr>
          <td>Susp</td>
          <td>Men's</td>
          <td><select name="men_a_susp_qty">
					<?php
						for ($i=0; $i<=9; ++$i) {
							echo "<option value='".$i."' ";
							if ($isEdit && $i==$editData[0]["W-SUSP-M"]) { echo "selected "; }
							echo ">".$i."</option>";
						}
					?>	
					</select></td>
          <td>Boy's</td>
          <td><select name="boy_a_susp_qty">
					<?php
						for ($i=0; $i<=9; ++$i) {
							echo "<option value='".$i."' ";
							if ($isEdit && $i==$editData[0]["W-SUSP-B"]) { echo "selected "; }
							echo ">".$i."</option>";
						}
					?>	
					</select></td>
          <td colSpan='3'>Total: <span id="a_susp_qty_total">0</span></td>
        </tr>
      </table>
      </div>
      
      <!-- Totals section, for the comments, height, and weight fields -->
      <h3>Totals</h3>
      <input type="text" maxlength=50 size=40 name="comments" placeholder="Comments" <?php if ($isEdit) { echo "value='".htmlentities($editData[0]["W-COMMENTS"], ENT_QUOTES)."' "; } ?>/><input type="text" maxlength=6 size=6 name="height" placeholder="Height" <?php if ($isEdit) { echo "value='".htmlentities($editData[0]["W-HEIGHT"], ENT_QUOTES)."' "; } ?>/><input type="text" maxlength=4 size=6 name="weight" placeholder="Weight" <?php if ($isEdit) { echo "value='".htmlentities($editData[0]["W-WEIGHT"], ENT_QUOTES)."' "; } ?>/>
			<input type="hidden" name="ticket" value="<?php if ($isEdit) { echo $_GET['edit']; } ?>" />
			<?php
				if ($isEdit && $editData[0]["W-TKT-TYPE"]==2) {
			?>
			<script>
				$(function() {
					$("#accBox").prop("checked", true);
					$("#compBox").prop("checked", false);
					ToggleAccessories();
				});
			</script>
			<?php
				}
			?>
      <button type="submit" value="Submit" accesskey="R"><u>R</u>eview</button>
    </form>
  </body> 
</html>