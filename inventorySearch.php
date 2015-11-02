<html>
  <head>
    <title>Inventory Search</title>

    <!-- Main styles -->
    <link rel="stylesheet" href="styles.css" />

    <!-- Styles for autocomplete elements -->
    <link rel="stylesheet" href="jquery-ui.css" />
    <link rel="stylesheet" href="jquery-style.css" />
		<style>
			.restab TD { padding:4px; border: 1px solid #555555; text-align:right; font-size:12pt; }
			.restab TH { padding: 4px; background-color: #aaaaaa; font-weight:bold; border: 1px solid #000000; text-align: center; font-size:12pt;}
			.restab A { font-size:12pt; }
			TABLE.restab { border-collapse: collapse; font-size:12pt; }
		</style>

    <!-- jQuery library and its Autocomplete extension -->
    <script type="text/javascript" src="jquery-1.9.1.js"></script>
    <script type="text/javascript" src="jquery-ui.js"></script>

    <!-- Main Javascript library, including data pulled from the databases, hence why it must be in .php format -->
    <script type="text/javascript" src="scripts.php?<?php echo time(); ?>"></script>
  </head>
  <body onLoad="Initialize(false)">
		<!-- Logo that links to the main menu -->
    <a href="index.php"><span style="float:right"><img src="logo.png" /></span></a><br clear="both" />
			
    <!-- Main data entry form -->
    <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" name="entry">
			<input type="checkbox" name="complete" value="true" id="compBox" checked=true /><label for="compBox">Complete Outfit</label> <input type="checkbox" name="accessories" value="true" id="accBox" /><label for="accBox">Accessories Only</label>

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
					<td>Seat</td>
          <td><input name="p_seat" type="text" size=5 /></td>
          <td>Length</td>
          <td><input name="p_length" type="text" size=5 /></td>
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
          <td><select name="sh_color"><option value=""></option><?php
						$shoeColors=array(
							"black"=>"Black",
							"white"=>"White",
							"silver"=>"Silver",
							"ivory"=>"Ivory"
						);
						foreach ($shoeColors as $val=>$txt) {
							echo "<option value='".$val."'>".$txt."</option>";
						}
					?>
					</select></td>
          <td>Size</td>
          <td><input name="sh_size" type="text" size=5 /></td>
          <td colSpan='3'><input type="radio" name="sh_wide" value="" id="shNormal" checked /><label for="shNormal">Normal</label> <input type="radio" name="sh_wide" value="W" id="shWide" /><label for="shWide">Wide</label> <input type="radio" name="sh_wide" value="B" id="shBoys" /><label for="shBoys">Boys</label></td>
        </tr>
      </table>

       <!-- Accessories row -->
     <table border=0 class="bottomRow">
       <tr>
          <td>Cane Color</td>
          <td><input id="Cane_style" name="ca_style" type="text" size=5 /></td>
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
          <td>Tie</td>
          <td colSpan='4'><input id="tie_a_style" name="tie_a_style" type="text" size=5 /></td>
        </tr>
        <tr>
          <td>Hankie</td>
          <td colSpan='4'><input id="hankie_a_style" name="hankie_a_style" type="text" size=5 /></td>
        </tr>
        <tr>
          <td>Cane</td>
          <td colSpan='4'><input id="Cane_a_style" name="cane_a_style" type="text" size=5 /></td>
        </tr>
      </table>
      </div>
			<input type="hidden" name="ticket" />
      <button type="submit" value="Submit" name="submit" accesskey="S"><u>S</u>earch</button>
    </form>
		<br />
		<!-- Search results -->
		<?php
			if (!empty($_POST["submit"]) && $_POST["submit"]=="Submit") {
				$link=mysql_connect("localhost","root","tux898");
				$db=mysql_select_db("sniders2013");
				
				$q="SELECT `W-CUSTNO`, `C-NAME`, `W-TKT`, `W-TKT-SUB`, `W-ORDER-DT`, `W-USE-DT`, `W-REF`, `W-INV-NO` FROM `t-work`, `t-customer` WHERE `W-CUSTNO`=`C-CUSTNO`";
				
				// Go through the options... one by one...
				if (!empty($_POST['c_style'])) { $q.=" AND `W-COAT`='".$_POST['c_style']."'"; }
				if (!empty($_POST['c_size'])) { $q.=" AND `W-COAT-SZ`='".$_POST['c_size']."'"; }
				if (!empty($_POST['c_sleeve'])) { $q.=" AND `W-COAT-SLV`='".$_POST['c_sleeve']."'"; }
				if (!empty($_POST['p_style'])) { $q.=" AND `W-PANT-STYLE`='".$_POST['p_style']."'"; }
				if (!empty($_POST['p_waist'])) { $q.=" AND `W-WAIST`='".$_POST['p_waist']."'"; }
				if (!empty($_POST['p_seat'])) { $q.=" AND `W-PANTS-SEAT`='".$_POST['p_seat']."'"; }
				if (!empty($_POST['p_length'])) { $q.=" AND `W-PANT-LEN`='".$_POST['p_length']."'"; }
				if (!empty($_POST['s_style'])) { $q.=" AND `W-SHIRT`='".$_POST['s_style']."'"; }
				if (!empty($_POST['s_size'])) { $q.=" AND `W-SHIRT-SIZE`='".$_POST['s_size']."'"; }
				if (!empty($_POST['a_vest'])) { $q.=" AND `W-VEST`='".$_POST['a_vest']."'"; }
				if (!empty($_POST['a_sash'])) { $q.=" AND `W-SASH`='".$_POST['a_sash']."'"; }
				if (!empty($_POST['a_tie'])) { $q.=" AND `W-TIE`='".$_POST['a_tie']."'"; }
				if (!empty($_POST['a_hankie'])) { $q.=" AND `W-HANKIE`='".$_POST['a_hankie']."'"; }
				if (!empty($_POST['sh_style'])) { $q.=" AND `W-SHOE`='".$_POST['sh_style']."'"; }
				if (!empty($_POST['sh_color'])) { $q.=" AND `W-SHOE-COLOR`='".$_POST['sh_color']."'"; }
				if (!empty($_POST['sh_size']) && !empty($_POST['sh_wide'])) { $q.=" AND `W-SHOE-SIZE`='".$_POST['sh_size'].$_POST['sh_wide']."'"; }
				if (!empty($_POST['ca_style'])) { $q.=" AND `W-CANE`='".$_POST['ca_style']."'"; }
				if (!empty($_POST['vest_a_style'])) { $q.=" AND `W-VEST-ACC`='".$_POST['vest_a_style']."'"; }
				if (!empty($_POST['sash_a_style'])) { $q.=" AND `W-SASH-ACC`='".$_POST['sash_a_style']."'"; }
				if (!empty($_POST['tie_a_style'])) { $q.=" AND `W-TIE-ACC`='".$_POST['tie_a_style']."'"; }
				if (!empty($_POST['hankie_a_style'])) { $q.=" AND `W-HANKIE-ACC`='".$_POST['hankie_a_style']."'"; }
				if (!empty($_POST['cane_a_style'])) { $q.=" AND `W-CANE`='".$_POST['cane_a_style']."'"; }
				
				$q.=" ORDER BY `W-USE-DT` DESC, `C-NAME` ASC, `W-ORDER-DT` DESC";
				$query=mysql_query($q);
				
				if (!$query) {
					echo 'An error occurrec during the search ("'.mysql_error().'").';
				}
				else {
					echo "<table class='restab'>";
					echo "<tr><th>Cust #</th>";
					echo "<th>Customer</th>";
					echo "<th>Ticket #</th>";
					echo "<th>Reference</th>";
					echo "<th>Ordered</th>";
					echo "<th>Use Date</th>";
					echo "<th>Invoice #</th></tr>";
					
					if (mysql_num_rows($query)<=0) {
						echo "<tr><td colSpan='7' style='text-align:center;font-style:italic;font-size:14pt'>No tickets found.</td></tr>";
					}
					else {
						while ($row=mysql_fetch_assoc($query)) {
							echo "<tr><td>".$row["W-CUSTNO"]."</td>";
							echo "<td>".$row["C-NAME"]."</td>";
							$tktLink="./tickets/Complete/ticket-".$row["W-TKT"]."-".$row["W-TKT-SUB"].".png";
							if (!file_exists($tktLink)) {
								$tktLink="./purged/Tickets/ticket-".$row["W-TKT"]."-".$row["W-TKT-SUB"].".png";
							}
							if (file_exists($tktLink)) {
								echo "<td><a href='".$tktLink."' target='_blank'>".$row["W-TKT"]."-".$row["W-TKT-SUB"]."</a></td>";
							}
							else {
								echo "<td>".$row["W-TKT"]."-".$row["W-TKT-SUB"]."</td>";
							}
							echo "<td>".$row["W-REF"]."</td>";
							echo "<td>".date("n/j/Y", strtotime($row["W-ORDER-DT"]))."</td>";
							echo "<td>".date("n/j/Y", strtotime($row["W-USE-DT"]))."</td>";
							if (!empty($row["W-INV-NO"]) && $row["W-INV-NO"]!="00000") { echo "<td>".$row["W-INV-NO"]."</td>"; }
							else { echo "<td>(Not Invoiced)</td>"; }
							echo "</tr>";
						}
					}
					
					echo "</table>";
				}
				
				mysql_close($link);
			}
		?>
  </body> 
</html>