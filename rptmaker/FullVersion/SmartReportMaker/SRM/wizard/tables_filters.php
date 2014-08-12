<?php

ini_set( 'magic_quotes_gpc', 0 );
session_start();
error_reporting(E_ERROR  | E_PARSE);
$next = "step_4.php";
require_once("lib.php");
@$continue= $_POST["continue_x"];
@$left_table = $_POST['left_table'];
@$filters = $_POST['filters'];
@$left_field = $_POST['left_field'];
if(get_magic_quotes_gpc())
    {   
	    $_POST['tables_filters'] = str_replace('\"','"', $_POST['tables_filters']);
    	

    }
	@$tables_filters = $_POST['tables_filters'];

@$arr = array("Equal" => "=","Greater than" => ">","Less than" => "<","Greater than or Equal" => "<=","Less than or Equal" => ">=","Between" => "Between","Like" => "Like" , "NOT Like" => "NOT Like" , "Not Equal" => "!=" , "Begin with" => "Like1" , "End With" => "Like2", "Contain" => "Like3" );
@$filter_filed= "<input type='text' name='filter_value' id='filter_value'>";
@$type=1;
@$btnValue="";
@$ValidatFild="";
if(!empty($left_table))
{
getDataType();
}
else{
	foreach($_SESSION["table"] as $key=>$val)
	{

		if($key==0)
		{
			$left_table = $val;
		}
}
	$result = sql("SHOW COLUMNS FROM `$left_table`");

	$rowC=0;
	while($row=mysql_fetch_row($result))
	{

 	if($rowC==0)
 	{
     $left_field= $row[0];
}
$rowC=1;
	}
	getDataType();
}

if(!empty($continue))
{
  IF(!empty($tables_filters))
  {

	  $_SESSION["tables_filters"]=$tables_filters;
	  header("location: $next");
  }
  else
  {
     $_SESSION["tables_filters"] = $tables_filters;
	 header("location: $next");
  }
}

function getDataType()
{
  	global $left_table ,$arr ,$filter_filed,$left_field,$ValidatFild,$type,$btnValue ;
   $com= 'DESCRIBE `'. $left_table . '`';

 $g= query($com);

 	while($row = mysql_fetch_array($g,MYSQL_ASSOC))

 		{
$count=0;
$bTrue=false;
           foreach ($row as $value) {
             if($count ==0 && $left_field == $value)
             {

$bTrue=true;
             }
             if($count == 1&& $bTrue)
             {
if(strpos(strtolower($value),'char') !== false || strpos(strtolower($value),'text') !== false)
{
 $ValidatFild="HasValue";
 @$arr = array("Equal" => "=" ,"Like" => "Like","NOT Like" => "NOT Like" , "Not Equal" => "!=" , "Begin with" => "Like1" , "End With" => "Like2", "Contain" => "Like3" );
$type=0;

}
else if(strpos(strtolower($value),'int') !== false|| strpos(strtolower($value),'decimal') !== false|| strpos(strtolower($value),'double') !== false|| strpos(strtolower($value),'float') !== false)
{
  $ValidatFild="IsNumeric";
 @$arr = array("Equal" => "=","Greater than" => ">","Less than" => "<","Greater than or Equal" => "<=","Less than or Equal" => ">=","Between" => "Between","Not Equal" => "!=");
 $type=1;
}

else if(strpos(strtolower($value),'date') !== false|| strpos(strtolower($value),'time') !== false||strpos(strtolower($value),'year') !== false)
{
   $ValidatFild="Validate";
 @$arr = array("Equal" => "=","Greater than" => ">","Less than" => "<","Greater than or Equal" => "<=","Less than or Equal" => ">=","Between" => "Between");
$type=2;
}
else if(strpos(strtolower($value),'bit') !== false||strpos(strtolower($value),'bool') !== false)
{
  $type=1;
  $ValidatFild="HasValue";
  @$arr = array("Equal" => "=");
$filter_filed= "<select   name='filter_value' id='filter_value'>
<option value='true' selected>True</option>
<option value='false' selected>False</option>
<\select>";


}
else
{
   $arr = array();
   $filter_filed="Filed with datatype $value  can not be filterd";
$btnValue="disabled='disabled'";
}
             }

     $count = $count + 1;
}
           }
}


function print_tables_names($field_name)
{
	global $filters,$left_table;

	foreach($_SESSION["table"] as $key=>$val)
	{
		if($key==0 && empty($left_table))
		{
			$filters = $val;
			$left_table = $val;
		}

		if($_POST[$field_name] ==$val)
			echo "<option value='$val' selected>$val</option>";
		else
			echo "<option value='$val'>$val</option>";
	}
}


function print_fields_filter($field_name)
{
	global $filters,$left_table;

	foreach($_SESSION["table"] as $key=>$val)
	{
		if($key==0 && empty($left_table))
		{
			$filters = $val;
			$left_table = $val;
		}

		if($_POST[$field_name] ==$val)
			echo "<option value='$val' selected>$val</option>";
		else
			echo "<option value='$val'>$val</option>";
	}
}

function print_tables_filters($field_name)
{
	global $filters ,$arr ,$filter_filed,$type;

	foreach($arr as   $key=>$val)
	{

		if($_POST[$field_name] ==$val)
			echo "<option value='$val' selected>$key</option>";
		else
			echo "<option value='$val'>$key</option>";
	}

	if($_POST[$field_name] == "Between" && $type != 0)
	{
$filter_filed= "<input type='text' name='filter_value' id='filter_value'> <br/> and <br/><input type='text' name='filter_value1' id='filter_value1'>  " ;
      }


}

function print_fields_names($field_name)
{
	global $filters,$left_table;



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
	global $tables_filters;

	if(!isset($_POST['left_table']))
	{
		$tables_filters = $_SESSION['tables_filters'];
	}
	if(!isset($_POST['tables_filters']))
	{

@$_SESSION["tables_filters"]=$tables_filters;
 }

	foreach($tables_filters as $key=>$val)
	{
     $newVal=  str_replace("\\", " ", $val);
		echo "<option value='$newVal'>$newVal</option>";
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
function Validate(strString)
{

	 if (strString.length == 0)
	{
		alert("Please enter the Date..!!");
		  return false;
		}
		if(strString.match(/^[0-9]{4}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])/))
		{
  return true;
		}
		else
		{
			alert("date format is wrong")
			return false;
		}


}

function HasValue(strString)
   {
       if (strString.length == 0)
   {
      alert("Please enter a value.");

     return false;
   }
   else
   {
       return true;
   }
     }
function IsNumeric(strString)
   //  check for valid numeric strings
   {
   var strValidChars = "0123456789.-";
   var strChar;
   var blnResult = true;

   if (strString.length == 0)
   {
      alert("Please enter a value.");

     return false;
     }

   //  test strString consists of valid characters listed above
   for (i = 0; i < strString.length && blnResult == true; i++)
      {
      strChar = strString.charAt(i);
      if (strValidChars.indexOf(strChar) == -1)
         {


      alert("Please check - non numeric value!");

         blnResult = false;
         }
      }
   return blnResult;
   }

       function add_fil() {
         var left_table = document.myform.left_table.value.trim();
         var filters = document.myform.filters.value.trim();
		  var left_field = document.myform.left_field.value.trim();
          var filter_value="";
		  
		  //alert("filter value"+document.myform.filter_value.value);
		  //alert(filters);

          if(!<?php echo " $ValidatFild"?>(document.myform.filter_value.value))
          {
            return false;
          }
         if(<?php echo "$type" ?>!=1)
         { 
		    if(filters == "Like3"){
             filter_value = '"%'+document.myform.filter_value.value+'%"';
			 filters = "Like";
			 document.myform.filters.value = "Like";
			 }
			 else if(filters == "Like1"){
			 filter_value = '"'+document.myform.filter_value.value+'%"';
			 filters = "Like";
			 document.myform.filters.value = "Like";
			 }
			 else if(filters == "Like2"){
			 filter_value = '"%'+document.myform.filter_value.value+'"';
			  filters = "Like";
			 document.myform.filters.value = "Like";
			 }
			 else{
			 filter_value = '"'+document.myform.filter_value.value+'"';
			 }
			 
         }
         else
         {
               filter_value = document.myform.filter_value.value;
         }
		 var filter_value1 = "";
		 if(document.myform.filter_value1)
		 {

           if(!<?php echo " $ValidatFild"?>(document.myform.filter_value1.value))
          {
              return false;
          }
            if(<?php echo "$type" ?>!=1)
         {
		  filter_value1=' and "'+document.myform.filter_value1.value+'"';
		}
  else{
filter_value1=" and "+document.myform.filter_value1.value;

  }
		    if(filter_value=="" || filter_value1==" and ")
		 {
alert("Filter value box can not be empty");
return;

   }
		 }
		  if(filter_value=="")
		 {
alert("Filter value box can not be empty");
return;

   }




		 //check if this relation exists
		   for (i=0;i<myform.tables_filters.length;i++)
		  {
            var opt= "`"+left_table+"`.`"+left_field+"` is "+filters+" "+filter_value+filter_value1;
			 var optf = myform.tables_filters.options[i].text;
    if(opt==optf )
 			 {
 			 	alert('Relation could not be duplicated!');
 				return;
 			 }

		  }


         addOption = new Option( "`"+left_table+"`.`"+left_field+"` is "+filters+" "+filter_value+filter_value1);

         numItems = document.myform.tables_filters.length;
        document.myform.tables_filters.options[numItems] = addOption;
        return true;
      }

	  function remove_rel()
	  {
	  	var i=0;
		var length = myform.tables_filters.options.length;
		for (i=length-1;i>=0;i--)
		  {
			var current = document.myform.tables_filters.options[i];
			if (current.selected)
			{
			  document.myform.tables_filters.options[i] = null;
			}
		  }
		 if(i>0)
		 {
			  document.myform.tables_filters.options[i-1].selected=true;
		 }
	  }
	 /*
	  *select all list items
	  */
	function select_all()
	{
	  for (i=0;i<myform.tables_filters.length;i++)
	  {
		 myform.tables_filters.options[i].selected = true;
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
					<td colspan="2" height="18"><b class="step_title">Tables Filters </b></td>
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
                                      <td width="124" height="24" align="right" valign="top" nowrap> Table                                        </td>
                                      <td width="73" valign="top" nowrap ><select name="left_table" id="left_table" onChange="select_all();myform.submit();">
                                        <?php print_tables_names('left_table'); ?>
                                      </select></td>
                                      <td height="26" align="right" nowrap>
                                         Field                                      </td>
                                      <td height="26" nowrap>
                                        <select name="left_field" id="left_field" onChange="select_all();myform.submit();">
                                          <?php print_fields_names('left_field'); ?>
                                        </select>                                      </td>
                                      <td width="23" valign="top">  </td>
                                    </tr>
                                    <tr>
                                      <td colspan="5" align="right"><hr></td>
                                    </tr>
                                    <tr>

                                      <td width="26" align="right" valign="top" nowrap >Filters</td>
                                      <td width="86" valign="top">

<select name="filters" style="width:110px" id="filters" onChange="select_all();myform.submit();" >
                                        <?php print_tables_filters('filters'); ?>
                                      </select>


                                      </td>
                                      <td height="26" align="right" nowrap>
<?php
if($btnValue=="")
{
  echo "Filter Value";
}

?>


                                      </td>
                                      <td height="26">

									  <?php echo "$filter_filed" ?>
                                                                          </td>
                                      <td>  </td>
                                    </tr>
                                    <?php   if( $type == 2)
      {

 echo " <tr><td  height='23' colspan='5' align='center' style='color:red;' valign='top'>The date formate must be YYYY-MM-DD <td> </tr>";
      }
      ?>
                                    <tr>
                                      <td height="23" colspan="5" align="center" valign="top"> </td>
                                    </tr>
                                    <tr>
                                      <td colspan="5" align="center"><input name="btn_add" type="button" id="btn_add" value="   Add Filter   " onClick="add_fil();" <?php  echo "$btnValue"; ?>>
                                      <a href="" onMouseOver="stm(tables_relations[2],Style);" onClick="return false;" onMouseOut="htm()"></a></td>
                                    </tr>
                                    <tr>
                                      <td colspan="5" align="center"><select name="tables_filters[]" size="3" multiple id="tables_filters" style=" height:70;width:450;" >
									  <?php print_relations();?>
                                      </select></td>
                                    </tr>
                                    <tr>
                                      <td height="26" colspan="5" align="center"><input name="btn_remove" type="button" id="btn_remove" value=" Remove Filter " onClick="remove_rel();">
                                      <a href="" onMouseOver="stm(tables_relations[3],Style);" onClick="return false;" onMouseOut="htm()"></a></td>
                                    </tr>
                                    <tr>
                                      <td height="20" colspan="5" align="center">  </td>
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
                  href="<?php  if(count($_SESSION["table"])==1){ echo  "step_3.php";}else{echo  "tables_relations.php";}?>" style="color: #0029a3; text-decoration: none"><img
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


            <img border="0"   src="images/downright.jpg" width="53" height="30"></tr>
	<td height="2"></tr>
  </table>
</form>
</body>

</html>

