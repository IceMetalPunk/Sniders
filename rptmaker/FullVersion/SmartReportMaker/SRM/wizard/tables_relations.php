<?php
session_start();
error_reporting(E_ERROR  | E_PARSE);
$next = "tables_filters.php";
require_once("lib.php");

@$continue= $_POST["continue_x"];

@$left_table = $_POST['left_table'];
@$right_table = $_POST['right_table'];
@$left_field = $_POST['left_field'];
@$right_field = $_POST['right_field'];
@$relationships = $_POST['relationships'];

if(!empty($continue))
{
	
  IF(!empty($relationships))
  {	
	  $_SESSION["relationships"]=$relationships;
	  header("location: $next");
  }
  else
  {
	$error .= "* Please specify relations between tables.";
  }
}

function print_tables_names($field_name)
{
	global $right_table,$left_table;
	
	foreach($_SESSION["table"] as $key=>$val)
	{
		if($key==0 && empty($left_table))
		{
			$right_table = $val;
			$left_table = $val;
		}
		
		if($_POST[$field_name] ==$val)
			echo "<option value='$val' selected>$val</option>";		
		else
			echo "<option value='$val'>$val</option>";
	}
}

function print_fields_names($field_name)
{
	global $right_table,$left_table;

	//if right field then get fields of right table	
	if($field_name=='right_field')
	{
		$req_table = $right_table;
	}
	
	//if left field then get fields of left table	
	if($field_name=='left_field')
	{
		$req_table = $left_table;
	}
	
	$result = sql("SHOW COLUMNS FROM `$req_table`");
	while($row=mysql_fetch_row($result))
	{
		if($_POST[$field_name] ==$row[0])	
			echo "<option value=$row[0] selected>$row[0]</option>";
		else
			echo "<option value=$row[0] >$row[0]</option>";		
	}

}

function print_relations()
{
	global $relationships;
	if(!isset($_POST['left_table']))
	{
		$relationships = $_SESSION['relationships'];
	}
	
	foreach($relationships as $key=>$val)
	{
		echo "<option value='$val'>$val</option>";
	}
}


?>

<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Select table</title>
<link href="style.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript1.2" src="main.js" type="text/javascript"></SCRIPT>  
<SCRIPT LANGUAGE="JavaScript"> 
       function add_rel() { 
         var left_table = document.myform.left_table.value; 
         var right_table = document.myform.right_table.value; 		 
         var left_field = document.myform.left_field.value; 		 
         var right_field = document.myform.right_field.value; 		 		 		 
		 
		 //check if this relation exists
		  for (i=0;i<myform.relationships.length;i++)
		  {
			 var val = myform.relationships.options[i].text;
			 if(val.search(left_table)!=-1 && val.search(right_table)!=-1 && val.search(left_field)!=-1 &&val.search(right_field)!=-1 )
			 {
			 	alert('Relation could not be duplicated!');
				return;
			 }
		  }
		 
		 
         addOption = new Option("`"+left_table+"`.`"+left_field+"` = `"+right_table+"`.`"+right_field+"`");
		  
         numItems = document.myform.relationships.length; 
        document.myform.relationships.options[numItems] = addOption; 
        return true; 
      } 
	  
	  function remove_rel()
	  {
	  	var i=0;
		var length = myform.relationships.options.length;
		for (i=length-1;i>=0;i--)
		  {
			var current = document.myform.relationships.options[i];
			if (current.selected)
			{
			  document.myform.relationships.options[i] = null;
			}
		  }	  
		 if(i>0)
		 {
			  document.myform.relationships.options[i-1].selected=true;			  	
		 }		  
	  }
	 /*
	  *select all list items
	  */
	function select_all()
	{
	  for (i=0;i<myform.relationships.length;i++)
	  {
		 myform.relationships.options[i].selected = true;
	  }
	}
	  
 </SCRIPT> 

</head>

<body>
<DIV id="TipLayer" style="visibility:hidden;position:absolute;z-index:1000;top:-100;"></DIV>
<SCRIPT language="JavaScript1.2" src="style.js" type="text/javascript"></SCRIPT>   
<center>
<form action="<?php echo($_SERVER['PHP_SELF']); ?>" method="post" name="myform">
<table width="732"  height="537" border="0" align="center" cellpadding="0" cellspacing="0">
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
					<td colspan="2" height="18"><b class="step_title">Tables Relations </b></td>
				</tr>
				<tr>
					<td colspan="2" height="271" valign="top">
					<div align="center">
		
						<table border="0" cellpadding="0" cellspacing="0" width="501" id="table11" height="248">
						
							<tr>
								<td width="27" height="16">
								<img border="0" src="images/ctopleft.jpg" width="38" height="37"></td>
								<td width="425" height="16" background="images/ctop.jpg" style="background-repeat: x">&nbsp;</td>
								<td width="38" height="16">
								<img border="0" src="images/ctopright.jpg" width="38" height="37"></td>
							</tr>
							<tr>
								<td width="27" background="images/cleft.jpg" style="background-repeat: y">&nbsp;</td>
								<td width="425" align="center" valign="top" bgcolor="#F9F9F9">
								  <table border="0">
                                    <tr >
                                      <td height="20" colspan="5" align="left" valign="top" nowrap class="error"><?php echo $error?></td>
                                    </tr>
                                    <tr >
                                      <td width="124" height="24" align="right" valign="top" nowrap>Left Table                                        </td>
                                      <td width="73" valign="top" nowrap ><select name="left_table" id="left_table" onChange="select_all();myform.submit();">
                                        <?php print_tables_names('left_table'); ?>
                                      </select>   </td>
                                      <td width="128" align="right" valign="top" nowrap >Right Table</td>
                                      <td width="86" valign="top"><select name="right_table" id="right_table" onChange="select_all();myform.submit();">
                                        <?php print_tables_names('right_table'); ?>
                                      </select></td>
                                      <td width="23" valign="top"><a href="" onMouseOver="stm(tables_relations[0],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.gif" width="20" height="15" border="0" align="absmiddle"></a></td>
                                    </tr>
                                    <tr>
                                      <td colspan="5" align="right"><hr></td>
                                    </tr>
                                    <tr>
                                      <td height="26" align="right" nowrap>
                                        Left Field                                      </td>
                                      <td height="26" nowrap>
                                        <select name="left_field" id="left_field">
                                          <?php print_fields_names('left_field'); ?>
                                        </select>                                      </td>
                                      <td height="26" align="right" nowrap>Right Field </td>
                                      <td height="26">
                                        <select name="right_field" id="right_field">
                                          <?php print_fields_names('right_field'); ?>
                                        </select>                                      </td>
                                      <td><a href="" onMouseOver="stm(tables_relations[1],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.gif" width="20" height="15" border="0" align="absmiddle"></a></td>
                                    </tr>
                                    <tr>
                                      <td height="23" colspan="5" align="center" valign="top"><a href="" onMouseOver="stm(tables_relations[2],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.gif" width="20" height="15" border="0" align="absmiddle"></a></td>
                                    </tr>
                                    <tr>
                                      <td colspan="5" align="center"><input name="btn_add" type="button" id="btn_add" value="   Add Relation  " onClick="add_rel();">
                                      <a href="" onMouseOver="stm(tables_relations[2],Style);" onClick="return false;" onMouseOut="htm()"></a></td>
                                    </tr>
                                    <tr>
                                      <td colspan="5" align="center"><select name="relationships[]" size="3" multiple id="relationships" style=" height:70;width:450;" >
									  <?php print_relations();?>
                                      </select></td>
                                    </tr>
                                    <tr>
                                      <td height="26" colspan="5" align="center"><input name="btn_remove" type="button" id="btn_remove" value="Remove Relation " onClick="remove_rel();">
                                      <a href="" onMouseOver="stm(tables_relations[3],Style);" onClick="return false;" onMouseOut="htm()"></a></td>
                                    </tr>
                                    <tr>
                                      <td height="20" colspan="5" align="center"><a href="" onMouseOver="stm(tables_relations[3],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.gif" width="20" height="15" border="0" align="absmiddle"></a></td>
                                    </tr>
                                  </table>
						          <p></td>
								<td width="38" background="images/cright.jpg" style="background-repeat: y">&nbsp;</td>
							</tr>
							<tr>
								<td width="27" height="18">
								<img border="0" src="images/cdownleft.jpg" width="38" height="37"></td>
								<td width="425" height="18" background="images/cdown.jpg" style="background-repeat: x">								</td>
								<td width="38">
								<img border="0" src="images/cdownright.jpg" width="38" height="37"></td>
							</tr>
												
					  </table>
					  </div>	
					  
			      </td>
				</tr>
				<tr>
					<td align="center"><a 
                  href="step_3.php" style="color: #0029a3; text-decoration: none"><img 
                  src="images/03.jpg" border=0 width="170" height="34"></a></td>
					<td align="center">
					<INPUT name=continue type=image id="btn_cont"
                  src="images/04.jpg" width="166" height="34" onClick="select_all();"></td>
				</tr>
			</table>
			<td  align="center" width="48" style="background-repeat: y" valign="top" height="388" background="images/rightadd.jpg">
           
            <img border="0" src="images/right.jpg"></tr>
	<tr>
		<td width="64" height="14" align="center" background="images/leftadd.jpg" style="background-repeat: y">
      <td  align="center" width="48" background="images/rightadd.jpg" style="background-repeat: y" valign="top">
           
    </tr>
	<td height="2"></tr>
	<tr>
		<td align="center" width="64" height="30" style="background-repeat: no-repeat">
           
            <img border="0" src="images/downleft.jpg" width="64" height="30"><td align="center" width="614" height="30" background="images/down.jpg" style="background-repeat: x">
           
            <td align="center" width="48" height="30" background="images/downright.jpg" style="background-repeat: no-repeat" >
           
            <img border="0" src="images/downright.jpg" width="53" height="30"></tr>
	<td height="2"></tr>
  </table>
</form>
</body>

</html>
