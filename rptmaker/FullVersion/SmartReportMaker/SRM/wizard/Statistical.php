<?php
session_start();
//error_reporting(E_ERROR  | E_PARSE);
require_once("lib.php");
$temp = file("functions.txt");
$continue= $_POST["continue_x"];
//print_r($_POST);
 if(!empty($continue))
 {
   //recieving var
    $function = $_POST["functions"];
    $affected_column=$_POST["affected_column"];
    $groupby_column=$_POST["groupby_column"];
    
   //validation work
     if(empty($function))
     {
        $page_errors .= "please select a function ";
        $error = true ;
      }
      
      if(empty($affected_column))
      {
         $page_errors .= "please select an affected column  ";
         $error = true;
      }
        
      if(empty($groupby_column))
      {
        $page_errors .= "please select a group by column  ";
         $error = true;

      }
      
      if($groupby_column == $affected_column)
      {
         $page_errors .= "Affected column and group by column cant be the same, please pick another affected or group by column";
         $error = true;

      }
//echo "fuck $error + $page_errors  ";

    if(!$error)
    {
     //session register
     $_SESSION["statestical"] = 1 ;
     $_SESSION["function"] = $function;
     $_SESSION["affected_column"]= $affected_column;
     $_SESSION["groupby_column"]=$groupby_column;
    
    
    //move forward
     header("location: step_5.php");
     }

 }




$functions = array();
foreach ($temp as $v)
{
    if(!empty($v))
    $functions[] = trim($v);
}

$Selected_fields=$_SESSION["fields"];


?>
<html>

<head>

<style>

<!--

body { font-family: Verdana, Arial, sans-serif; font-size: 11px; line-height: 1.5; }



p { font-family: Verdana, Arial, sans-serif; font-size: 11px; line-height: 1.5; }



td { font-family: Verdana, Arial, sans-serif; font-size: 11px; line-height: 1.5; }



TD.leftColumn { background-color: #DEFFCF; }



.pageTitle { font-family: Verdana, Arial, sans-serif; font-size: 11px; line-height: 1.5; font-weight: bold; text-decoration: underline; }

TABLE.formPage { border-right: #eeeeee 1px solid; border-top: #eeeeee 1px solid; border-left: #eeeeee 1px solid; border-bottom: #eeeeee 1px solid; background-color: #ffffee; }

.smallDesc { font-family: Verdana, Arial, sans-serif; font-size: 9px; line-height: 1.5; color: #4D68A1; }

input { border-right: #eeeeee 1px solid; border-top: #eeeeee 1px solid; font: 8pt verdana, arial, helvetica; border-left: #cccccc 1px solid; color: #000000; border-bottom: #cccccc 1px solid; background-color: #ffffff }

.longDescription { visibility: hidden; display: none; }



a:link { color: #0029A3; text-decoration: none; }

TD.rightColumn { background-color: #CFF5FF; }

-->

</style>
<title>Smart Report Maker</title>
<link href="style.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript1.2" src="main.js" type="text/javascript"></SCRIPT>
</head>
<body>
<DIV id="TipLayer" style="visibility:hidden;position:absolute;z-index:1000;top:-100;"></DIV>
<SCRIPT language="JavaScript1.2" src="style.js" type="text/javascript"></SCRIPT>
<div align="center">
<form method="post" action="Statistical.php">
<table width="600" height="100" border="0" cellpadding="0" cellspacing="0" id="table1">
	<tr>
		<td width="1%">
		<img src="layout/topleft.gif" width="15" height="15" border="0"></td>
		<td width="98%" background="layout/topmid.gif">
		<img src="layout/pixel_trans.gif" width="1" height="1" border="0"></td>
		<td width="2%">
		<img src="layout/topright.gif" width="15" height="15" border="0"></td>
	</tr>
	<tr>
		<td width="1%" background="layout/midleft.gif">
		<img src="layout/pixel_trans.gif" width="1" height="1" border="0"></td>
		<td width="98%">

		<div align="center">
		<table cellspacing="0" cellpadding="0" width="100%" border="0" id="table3">
			<tr>
				<td width="5%" class="" valign="top"  >
				 </td>
				<td width="95%" valign="top">
				  <p class="pageTitle">
					<span style="font-size: 12.0pt; font-family: Times New Roman">
					Statistical options</span></p>
					<p><?php echo $page_errors;?>
				    </p>
					<div align="center">
					<table width="85%" border="0" cellpadding="2" class="formPage" style="background-color:#f9f9f9;"  id="table4" height="156">
					
						<tr>
							<td width="100%" valign="top">
							<div align="center">
								<table border="0" width="100%" id="table7" height="136">
									<tr>
										<td align="right" width="40%"><b>
										Statistical Function</b></td>
										<td >
										<select size="1" name="functions">


<?php
// *********************the functions
foreach ($functions as $func)
{
    if(isset($_SESSION["function"])&&$_SESSION["function"]==$func)
    {
        echo "<option selected>$func</option>";
    }
    else
    {
        echo "<option>$func</option>";
    }
}
?>





										</select></td>
										<td  align="left" style="font-family: Verdana, Arial, sans-serif; font-size: 11px; line-height: 1.5"><a href="" onMouseOver="stm(Step_s[0],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.gif" width="20" height="15" border="0"></a></td>
									</tr>
									
									
									<tr>
										<td align="right" width="30%"><b>
										Affected column</b></td>
										<td >
										<select size="1" name="affected_column">
<?php
//************
foreach($Selected_fields as $f)
{
  if(isset($_SESSION["affected_column"])&&$_SESSION["affected_column"]==$f)
    {
    echo "<option selected>$f</option>";
   }
  else
  {

    echo "<option>$f</option>";
  }
}
?>
										

										</select></td>
										
										<td  align="left" style="font-family: Verdana, Arial, sans-serif; font-size: 11px; line-height: 1.5"><a href="" onMouseOver="stm(Step_s[1],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.gif" width="20" height="15" border="0"></a></td>
									</tr>
									<tr>
										<td align="right" width="30%"><b>Group
										by column</b></td>
										<td >
										&nbsp;<select size="1" name="groupby_column">

										
<?php
if(count($_SESSION["fields"])==1)
{
    if(!isset($_SESSION["groupby_column"]))
    echo "<option selected value='None'>None										</option>";
    else
    echo "<option  value='None'>None										</option>";
}


 foreach($Selected_fields as $f)
{
    if(isset($_SESSION["groupby_column"])&&$f==$_SESSION["groupby_column"])
    {
     echo "<option selected>$f</option>";
    }
    else
    {
     echo "<option>$f</option>";
    }
 }

?>
										
										
										</select></td>
										<td  align="left" style="font-family: Verdana, Arial, sans-serif; font-size: 11px; line-height: 1.5"><a href="" onMouseOver="stm(Step_s[2],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.gif" width="20" height="15" border="0"></a></td>
									</tr>
									<tr>
										<td colspan="2">&nbsp;</td>
									</tr>
									<tr>
										<td colspan="2">
										  <p align="center">
										    &nbsp;</td>
									</tr>
									<tr>
										<td colspan="2">&nbsp;</td>
									</tr>
									</table>
							</div>
							</td>
						</tr>
					</table>
					</div>
					<p>&nbsp;</p>
					<table border="0" width="100%" cellspacing="0" cellpadding="0" id="table5">
						<tr>
						  <td align="center"><a href="step_4.php"><img src="layout/button_back.gif" width="77" height="23" border="0"  /></a></td>
							<td align="center">

       <input type ="image" name="continue"  src="layout/button_continue.gif">
							</td>
						</tr>
					</table>
				</td>
				<td width="5%"  valign="top">
				 </td>
			</tr>
		</table>
		</div>
<br/>
		</td>
		<td width="2%" background="layout/midright.gif">
		<img src="layout/pixel_trans.gif" width="1" height="1" border="0" alt=""></td>
	</tr>
	<tr>
		<td width="1%">
		<img src="layout/botleft.gif" width="15" height="15" border="0" alt=""></td>
		<td width="98%" background="layout/botmid.gif">
		<img src="layout/pixel_trans.gif" width="1" height="1" border="0" alt=""></td>
		<td width="2%">
		<img src="layout/botright.gif" width="15" height="15" border="0" alt=""></td>
	</tr>
</table>
</form>
</div>
</body>
