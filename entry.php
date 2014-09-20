<?php
	$link=mysql_connect("localhost", "root", "tux898");
	$db=mysql_select_db("sniders2013");
	
	$isEdit=false;
	$editData=array();
	if (!empty($_GET['edit'])) {
		$q="SELECT * FROM `t-work` WHERE `W-TKT`='".mysql_real_escape_string($_GET['edit'])."'";
		$query=mysql_query($q);
		if (mysql_num_rows($query)>0) {
			$isEdit=true;
			while ($row=mysql_fetch_assoc($query)) {
				$editData[]=$row;
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
      
      <?php if (!empty($_POST['redirected']) && $_POST['redirected']=="1") { ?>
      <script>
        $(function() {
          GetCustomer(document.entry.c_num.value, true);
					document.entry.date_use.focus();
          document.entry.date_use.select();
        });
      </script>
      <?php  } else if ($isEdit) { ?>
			<script>
        $(function() {
          GetCustomer(document.entry.c_num.value, true);
        });
      </script>
			<?php } ?>
      
      <!-- Dropdowns for delivery type and billing/payment type -->
      <select name="d_type">
        <option value="101">Delivery</option>
        <option value="102">Pickup</option>
        <option value="103">UPS</option>
        <option value="104">UPS Priority</option>
        <option value="105">UPS Air</option>
        <option value="106">FedEx</option>
        <option value="107">Delivery Today</option>
        <option value="108">Pickup Today</option>
        <option value="109">Other</option>
      </select>
      <select name="b_type">
        <option value="203">COD</option>
        <option value="204">COD-Cash</option>
        <option value="201">Standard</option>
        <option value="202">Standard-Discount</option>
        <option value="208">Standard-Discount 2</option>
        <option value="207">Fashion Show</option>
        <option value="206">Replacement</option> 
        <option value="205">Try-On</option>
      </select>
      <br />
      <div id="inHouseSpot" style="display:none">Cell Phone&nbsp;&nbsp;&nbsp; <input type='text' name='cellPhone' /><br/>
      Home Phone <input type='text' name='homePhone' /></div>
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
      ?> /><input type="hidden" name="full_use_date" /> <input name="ref" type="text" size=40 maxlength=50 placeholder="Reference" /> <input type="checkbox" name="complete" value="true" id="compBox" /><label for="compBox">Complete Outfit</label> <input type="checkbox" name="accessories" value="true" id="accBox" /><label for="accBox">Accessories Only</label>

      <!-- Main table with item information fields -->
      <div id="mainForm">
      <table border=0 class='entryTable'>

        <!-- Coat row -->
        <tr>
          <td>Coat</td>
          <td><input id="coat_style" name="c_style" type="text" size=5 /></td>
          <td>Size</td>
          <td><input name="c_size" type="text" size=5 /></td>
          <td>Sleeve</td>
          <td colSpan='3'><input name="c_sleeve" type="text" size=5 /></td>
        </tr>

        <!-- Pants row -->
        <tr>
          <td>Pants</td>
          <td><input id="pants_style" name="p_style" type="text" size=5 /></td>
          <td>Waist</td>
          <td><input name="p_waist" type="text" size=5 /></td>
          <td>Length</td>
          <td><input name="p_length" type="text" size=5 /></td>
          <td>Seat</td>
          <td><input name="p_seat" type="text" size=5 /></td>
        </tr>

        <!-- Shirt row -->
        <tr>
          <td>Shirt</td>
          <td><input id="shirt_style" name="s_style" type="text" size=5 /></td>
          <td>Size</td>
          <td><input name="s_size" type="text" size=5 /></td>
        </tr>

        <!-- Vest, sash, tie and hankie row -->
        <tr>
          <td>Vest</td>
          <td><input id="vest_style" name="a_vest" type="text" size=5 /></td>
          <td>Sash</td>
          <td><input id="sash_style" name="a_sash" type="text" size=5 /></td>
          <td>Tie</td>
          <td><input id="tie_style" name="a_tie" type="text" size=5 /></td>
          <td>Hankie</td>
          <td><input id="hankie_style" name="a_hankie" type="text" size=5 /></td>
        </tr>

        <!-- Shoes row -->
        <tr>
          <td>Shoe</td>
          <td><input id="shoe_style" name="sh_style" type="text" size=5 /></td>
          <td>Color</td>
          <td><select name="sh_color"><option value=""></option><option value="black">Black</option><option value="white">White</option><option value="silver">Silver</option><option value="ivory">Ivory</option></select></td>
          <td>Size</td>
          <td><input name="sh_size" type="text" size=5 /></td>
          <td colSpan='3'><input type="radio" name="sh_wide" value="Normal" id="shNormal" checked /><label for="shNormal">Normal</label> <input type="radio" name="sh_wide" value="Wide" id="shWide" /><label for="shWide">Wide</label> <input type="radio" name="sh_wide" value="Boys" id="shBoys" /><label for="shBoys">Boys</label></td>
        </tr>
      </table>

       <!-- Accessories row -->
     <table border=0 class="bottomRow">
       <tr>
          <td>Cane Color</td>
          <td><input id="Cane_style" name="ca_style" type="text" size=5 /></td>
          <td>Qty</td>
          <td><select name="ca_qty"><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option></select></td>
          <td>Glove Qty
          <td><select name="gl_qty"><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option></select></td>
          <td>Susp</td>
          <td><select name="sus_qty"><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option></select></td>
        </tr>

      </table>
      </div>
      
      <!-- Accessories table with item information fields -->
      <div id="accForm">
      <table border=0 class='entryTable'>

        <!-- Coat row -->
        <tr>
          <td>Vest</td>
          <td><input id="vest_a_style" name="vest_a_style" type="text" size=5 /></td>
          <td>Sash</td>
          <td colSpan='5'><input id="sash_a_style" name="sash_a_style" type="text" size=5 /></td>
        </tr>
        <tr>
          <td>MS</td>
          <td><select name="ms_vs_qty"><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option></select></td>
          <td>MM</td>
          <td><select name="mm_vs_qty"><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option></select></td>
          <td>ML</td>
          <td><select name="ml_vs_qty"><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option></select></td>
          <td>MXL</td>
          <td><select name="mxl_vs_qty"><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option></select></td>
        </tr>
        <tr>
          <td>BS</td>
          <td><select name="bs_vs_qty"><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option></select></td>
          <td>BM</td>
          <td><select name="bm_vs_qty"><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option></select></td>
          <td>BL</td>
          <td><select name="bl_vs_qty"><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option></select></td>
          <td>Other</td>
          <td><select name="other_vs_qty"><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option></select></td>
        </tr>
        <tr>
          <td colSpan='8'>Total: <span id='vs_qty_total'>0</span></td>
        </tr>
        <tr>
          <td>Tie</td>
          <td><input id="tie_a_style" name="tie_a_style" type="text" size=5 /></td>
          <td>Men's</td>
          <td><select name="men_a_tie_qty"><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option></select></td>
          <td>Boy's</td>
          <td><select name="boy_a_tie_qty"><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option></select></td>
          <td colSpan='2'>Total: <span id="a_tie_qty_total">0</span></td>
        </tr>
        <tr>
          <td>Hankie</td>
          <td><input id="hankie_a_style" name="hankie_a_style" type="text" size=5 /></td>
          <td>Qty</td>
          <td colSpan='5'><select name="hankie_a_qty"><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option></select></td>
        </tr>
        <tr>
          <td>Cane</td>
          <td><input id="Cane_a_style" name="cane_a_style" type="text" size=5 /></td>
          <td>Qty</td>
          <td colSpan='5'><select name="cane_a_qty"><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option></select></td>
        </tr>
        <tr>
          <td>Gloves</td>
          <td>Men's</td>
          <td><select name="men_a_glove_qty"><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option></select></td>
          <td>Boy's</td>
          <td><select name="boy_a_glove_qty"><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option></select></td>
          <td colSpan='3'>Total: <span id="a_glove_qty_total">0</span></td>
        </tr>
        <tr>
          <td>Susp</td>
          <td>Men's</td>
          <td><select name="men_a_susp_qty"><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option></select></td>
          <td>Boy's</td>
          <td><select name="boy_a_susp_qty"><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option></select></td>
          <td colSpan='3'>Total: <span id="a_susp_qty_total">0</span></td>
        </tr>
      </table>
      </div>
      
      <!-- Totals section, for the comments, height, and weight fields -->
      <h3>Totals</h3>
      <input type="text" maxlength=50 size=40 name="comments" placeholder="Comments" /><input type="text" maxlength=6 size=6 name="height" placeholder="Height" /><input type="text" maxlength=4 size=6 name="weight" placeholder="Weight" />
      <button type="submit" value="Submit" accesskey="R"><u>R</u>eview</button>
    </form>
  </body> 
</html>