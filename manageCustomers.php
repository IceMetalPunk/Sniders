<html>
  <head>
    <title>Customer Management Confirmation</title>

    <!-- Main styles -->
    <link rel="stylesheet" href="styles.css" />

    <!-- Styles for autocomplete elements -->
    <link rel="stylesheet" href="jquery-ui.css" />
    <link rel="stylesheet" href="style.css" />

    <!-- jQuery library and its Autocomplete extension -->
    <script type="text/javascript" src="jquery-1.9.1.js"></script>
    <script type="text/javascript" src="jquery-ui.js"></script>
	</head>
	<body>
    <a href="index.php"><img src="logo.png" /></a>
    <h3>Confirm Customer Management</h3>
		<form name="confirmForm" method="post" action="manageCustomers.php">
		<?php
		  $link=mysql_connect("localhost", "root", "tux898");
			$db=mysql_select_db("sniders2013", $link);
		
			$validActions=array("addSection", "changeSection", "deleteSection");
			if (empty($_POST['act']) || !in_array($_POST['act'], $validActions)){
				echo "<span class='smalltext error' style='display:block'>Something's gone wrong and we couldn't process your submission. 'Invalid action requested.'</span>";
			}
			else if (empty($_POST['confirmed']) || $_POST['confirmed']==false || $_POST['confirmed']=="false") {
				$act=str_ireplace("Section", "", $_POST['act']);
				echo "<b>Please confirm that you would like to ".strtoupper($act)." the following customer:</b><br />";
				echo "<input type='hidden' name='confirmed' value='true' />";
				echo "<input type='hidden' name='act' value='".$_POST['act']."' />";
				
				echo "<table border=0 class='smalltext' style='border-collapse:collapse'>";
				if ($act=="change" || $act=="add") {
				
					if ($act=="add") {
						$query="SELECT MAX(CAST(`C-CUSTNO` AS UNSIGNED INT)) as MaxNum FROM `t-customer` WHERE CAST(`C-CUSTNO` AS UNSIGNED INT)<70000";
						$q=mysql_query($query);
						
						if (!$q || mysql_num_rows($q)<=0) { $_POST['c_num']="00001"; }
						else {
							$row=mysql_fetch_assoc($q);
							$num=$row["MaxNum"]*1+1;
							$num=str_pad($num, 5, "0", STR_PAD_LEFT);
							$_POST['c_num']=$num;
						}
						
					}
				
					echo "<input type='hidden' name='c_num' value='".$_POST['c_num']."' />";
					echo "<tr><td>CUSTNO: </td><td>".$_POST['c_num']."</td></tr>";					
					foreach ($_POST as $name=>$val) {
						if (strtoupper(substr($name, 0, 2))=="C-") {
							echo "<input type='hidden' name='".$name."' value='".htmlspecialchars(htmlspecialchars_decode($val), ENT_QUOTES)."' />";
							echo "<tr><td>".substr($name, 2).": </td><td>".htmlspecialchars_decode($val)."</td></tr>";
						}
					}
				}
				echo "<tr><td colspan='2'><button name='sub' accesskey='C'><u>C</u>onfirm</button></tr></table>";
			}
			else {
				$act=str_ireplace("Section", "", $_POST['act']);
				if ($act=="add") {
					$str="INSERT INTO `t-customer` (`C-CUSTNO`, ";
					$vals="VALUES ('".$_POST['c_num']."', ";
					foreach ($_POST as $name=>$val) {
						if (strtoupper(substr($name, 0, 2))=="C-") {
							$str.="`".$name."`, ";
							$vals.="'".$val."', ";
						}
					}
					$vals=substr($vals, 0, -2).")";
					$str=substr($str, 0, -2).") ".$vals;
					$q=mysql_query($str);
					
					if (!$q || mysql_affected_rows()<=0) {
						echo "<span class='smalltext error' style='display:block'>Could not add new customer (".mysql_error().")</span>";
					}
					else {
						echo "Added customer. Returning to customer management page. <meta http-equiv='refresh' content='0;customers.php' />";
					}
					
				}
				else if ($act=="change") {
					$str="UPDATE `t-customer` SET ";
					$where="WHERE `C-CUSTNO`='".$_POST['c_num']."'";
					foreach ($_POST as $name=>$val) {
						if (strtoupper(substr($name, 0, 2))=="C-") {
							$str.="`".mysql_real_escape_string(str_replace("`", "", $name))."`='".mysql_real_escape_string($val)."', ";
						}
					}
					$str=substr($str, 0, -2)." ".$where;
					$q=mysql_query($str);
					
					if (!$q || mysql_affected_rows()<=0) {
						echo "<span class='smalltext error' style='display:block'>Could not modify customer (".mysql_error().")</span>";
					}
					else {
						echo "Updated customer. Returning to customer management page. <meta http-equiv='refresh' content='0;customers.php' />";
					}
					
				}
			}
			
			mysql_close($link);
		?>
		</form>
	</body>
</html>