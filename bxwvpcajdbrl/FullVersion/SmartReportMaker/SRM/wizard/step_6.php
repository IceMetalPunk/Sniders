<?php
//start session
session_start();
error_reporting(E_ERROR  | E_PARSE);

//get form data
//input images
if(isset($_POST['btn_continue_x'])) $btn_continue = $_POST['btn_continue_x'];
if(isset($_POST['btn_back_x'])) $btn_back = $_POST['btn_back_x'];
//select
$form_fields = array();
if(isset($_POST['fields1'])) $form_fields[] = $_POST['fields1'];
if(isset($_POST['fields2'])) $form_fields[] = $_POST['fields2'];
if(isset($_POST['fields3'])) $form_fields[] = $_POST['fields3'];
if(isset($_POST['fields4'])) $form_fields[] = $_POST['fields4'];
if(isset($_POST['fields5'])) $form_fields[] = $_POST['fields5'];
//check boxes
$desc = array();
for($i=0;$i<5;$i++)
{
	$field_name = 'desc'.($i+1);
	if(empty($_POST[$field_name]))
	{
		$desc[] = 0;
	}
	else
	{
		$desc[] = 1;
	}
}


//errors array
$page_errors = array( );
$table_fields = $_SESSION['fields'];


if(!empty($btn_continue)) //continue
{
	$sort_by = array();
	$i=0;
	foreach($form_fields as $key => $value)
	{
		if($value!='None'&& !strstr($value,"("))
		{
			$sort_by[$i][0] = $value;
			$sort_by[$i][1] = $desc[$key];
			$i++;
		}
	}
	$_SESSION['sort_by'] = $sort_by;
	//foreach($_SESSION['sort_by'] as $key => $arr)
	//{
	//	echo "Field = $arr[0] and Desc = $arr[1]";
	//	echo "<br/>";
	//}
	//exit;
	
	header("Location:step_7.php");
	exit;
}
else if(!empty($btn_back)) //back
{
	header("Location:step_5.php");
	exit;
}

function print_table_fields()
{
	global $table_fields;
	static	$i =0;

	$text ='<option>None</option>';

	if (count($table_fields)>0)
	{
		foreach($table_fields as $key => $value)
		{
			if($value == $_SESSION['sort_by'][$i][0])
				$text .= "<option selected>$value</option>";
			else
				$text .= "<option>$value</option>";			
		}
		$i++;
		echo $text;
	}
	else
	{
		$page_errors[] = "Unexpected error. There is no fields selected!";
	}
}

//function get_status that gets check box status from the session
function get_status($n)
{
	if(@$_SESSION['sort_by'][$n][1]==1)
	{
		return 'checked';
	}
	else
	{
		return '';
	}
}

?>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Select table</title>
<link href="style.css" rel="stylesheet" type="text/css">

</head>
<SCRIPT language="JavaScript1.2" src="main.js" type="text/javascript"></SCRIPT>  
<body>
<DIV id="TipLayer" style="visibility:hidden;position:absolute;z-index:1000;top:-100;"></DIV>
<SCRIPT language="JavaScript1.2" src="style.js" type="text/javascript"></SCRIPT>           
<center>
<form action="<?php echo($_SERVER['PHP_SELF']); ?>" method="post">
<table width="732"  height="455" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td align="center" width="64" height="20" background="images/topleft.jpg" style="background-repeat: no-repeat" >
           
      <td align="center" width="614" height="20" background="images/top.jpg" style="background-repeat: x">
           
      <td align="center" width="48" height="20" background="images/topright.jpg" style="background-repeat: no-repeat">
           
    </tr>
	<tr>
		<td align="center" width="64" style="background-repeat: y" valign="top" background="images/leftadd.jpg">
           
            <img border="0" src="images/left.jpg"><td rowspan="2" align="center" valign="top" >
           
			<p><img border="0" src="images/01.jpg" width="369" height="71"></p>
			<table border="0" width="100%" id="table8" height="333">
				<tr>
					<td height="18" colspan="2" class="step_title">Select Sort Fields </td>
				</tr>
				<tr>
					<td colspan="2" height="271" valign="top">					  <table width="501" height="248" border="0" align="center" cellpadding="0" cellspacing="0" id="table11">
					    <tr>
					      <td width="27" height="16">
					        <img border="0" src="images/ctopleft.jpg" width="38" height="37"></td>
						    <td width="425" height="16" background="images/ctop.jpg" style="background-repeat: x">&nbsp;</td>
						    <td width="38" height="16">
					        <img border="0" src="images/ctopright.jpg" width="38" height="37"></td>
					    </tr>
					    <tr>
					      <td width="27" background="images/cleft.jpg" style="background-repeat: y">&nbsp;</td>
					      <td width="425" bgcolor="#F9F9F9" align="center"><table width="95%" height="169" border="0" align="center" cellpadding="2" class="formPage" id="table4">
					        <tr>
					          <td width="30%" valign="top">						          <table width="83%" border="0" align="center" id="table7">
					            <tr>
					              <td width="35" class="control_label">1</td>
                                  <td width="177"><select name="fields1" size="1" id="fields1">
                                      <?php print_table_fields();?>
                                  </select></td>
                                  <td width="138" nowrap class="control_label"> Descending
                                  <input name="desc1" type="checkbox" id="desc1" value="ON"  <?php echo get_status(0); ?>/></td>
                                </tr>
					            <tr>
					              <td width="35" height="25" class="control_label">2</td>
                                  <td height="25" width="177"><select name="fields2" size="1" id="fields2">
                                      <?php print_table_fields(); ?>
                                  </select></td>
                                  <td height="25" nowrap class="control_label"> Descending
                                  <input name="desc2" type="checkbox" id="desc2" value="ON" <?php echo get_status(1); ?>></td>
                                </tr>
					            <tr>
					              <td width="35" class="control_label">3</td>
                                  <td width="177"><select name="fields3" size="1" id="fields3">
                                      <?php print_table_fields();?>
                                  </select></td>
                                  <td nowrap class="control_label"> Descending
                                  <input name="desc3" type="checkbox" id="desc3" value="ON" <?php echo get_status(2); ?>></td>
                                </tr>
					            <tr>
					              <td width="35" class="control_label">4</td>
                                  <td width="177"><select name="fields4" size="1" id="fields4">
                                      <?php print_table_fields();?>
                                  </select></td>
                                  <td nowrap class="control_label"> Descending
                                  <input name="desc4" type="checkbox" id="desc4" value="ON" <?php echo get_status(3); ?>></td>
                                </tr>
					            <tr>
					              <td width="35" class="control_label">5</td>
                                  <td width="177"><select name="fields5" size="1" id="fields5">
                                      <?php print_table_fields();?>
                                  </select></td>
                                  <td nowrap class="control_label">Descending
                                  <input name="desc5" type="checkbox" id="desc5" value="ON" <?php echo get_status(4); ?>></td>
                                </tr>
					            <tr>
					              <td colspan="3" align="right"><a href="" onMouseOver="stm(Step_6[0],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.gif" width="20" height="15" border="0"></a></td>
				                </tr>
			                  </table></td>
                            </tr>
				          </table></td>
						    <td width="38" background="images/cright.jpg" style="background-repeat: y">&nbsp;</td>
					    </tr>
					    <tr>
					      <td width="27" height="18">
					        <img border="0" src="images/cdownleft.jpg" width="38" height="37"></td>
						    <td width="425" height="18" background="images/cdown.jpg" style="background-repeat: x">								</td>
						    <td width="38">
					        <img border="0" src="images/cdownright.jpg" width="38" height="37"></td>
					    </tr>
				      </table></td></tr>
				<tr>
					<td height="36" align="center"><a 
                  href="step_5.php" style="color: #0029a3; text-decoration: none"><img 
                  src="images/03.jpg" name="btn_back" width="170" height="34" border=0 id="btn_back"></a></td>
					<td align="center"><INPUT name=btn_continue type=image id="btn_continue" 
                  src="images/04.jpg" width="166" height="34"></td>
				</tr>
			</table>
			<td  align="center" width="48" style="background-repeat: y" valign="top" height="388" background="images/rightadd.jpg">
           
            <img border="0" src="images/right.jpg"></tr>
	<tr>
		<td width="64" height="2" align="center" background="images/leftadd.jpg" style="background-repeat: y">
      <td  align="center" width="48" background="images/rightadd.jpg" style="background-repeat: y" valign="top">
           
    </tr>
	</tr>
	<tr>
		<td align="center" width="64" height="30" style="background-repeat: no-repeat">
           
            <img border="0" src="images/downleft.jpg" width="64" height="30"><td align="center" width="614" height="30" background="images/down.jpg" style="background-repeat: x">
           
            <td align="center" width="48" height="30" background="images/downright.jpg" style="background-repeat: no-repeat" >
           
            <img border="0" src="images/downright.jpg" width="53" height="30"></tr>
	</tr>
  </table>
</form>
</body>

</html>
