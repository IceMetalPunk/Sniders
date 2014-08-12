<?php
session_start();
error_reporting(E_ERROR  | E_PARSE);
require_once("lib.php");
$datasource =  $_SESSION["datasource"];
@$Statistical = $_POST["Statistical_x"];
//print_r($_SESSION);
@$continue= $_POST["continue_x"];



  if(!empty($continue))
 {
     $SelectedFields = $_POST["SelectedFields"];
     if (empty($SelectedFields))
     {
     $error .= "* At least One field should be selected" ;
      }
     else
     {
         $_SESSION["fields"]= $SelectedFields;
		 $_SESSION["fields2"]= $SelectedFields;
		 //print_r($_SESSION);
         header("location: labeling.php");
     }

      
 }


 //statestical option

  if(!empty($Statistical))
 {

     $SelectedFields = $_POST["SelectedFields"];
     if (empty($SelectedFields))
     {
     $error .= "* At least One field should be selected" ;
      }
     else
     {
         $_SESSION["fields"]= $SelectedFields;		 
		 $_SESSION["fields2"]= $SelectedFields;
         header("location: Statistical.php");
     }


 }

if($datasource=="table")
{
	$table = $_SESSION["table"];
	foreach($table as $key=>$val)
	{

		$result = sql("show columns from `$val`");
		while($raw = mysql_fetch_array($result) )
		{   if (count($table) == 1 )
            {
            $All_fields[]= $raw[0];
            }
          else
          {
           $All_fields[]= $val.".".$raw[0];
          }


		}
	}		
}
else
{

$sql =   $_SESSION["sql"];
$result = sql($sql);
    $raw = mysql_fetch_assoc($result);
   $rows = mysql_num_rows($result);
   //echo $rows;
   if($rows!=0)
   {

        foreach($raw as $key=>$val)
        {

            $All_fields[]= $key;
        }
   }
    else
   {
         $error .= "<br>*Records = 0, please click back and enter another query" ;
   }
}

?>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Select table</title>
<link href="style.css" rel="stylesheet" type="text/css">

<script language ="javascript">
function movel2R()
{
 var index = document.form1.AllFields.selectedIndex;
 if(index != -1)
 {
 var choice = document.form1.AllFields.options[index].text;

 var len = document.form1.fields.length;
 document.form1.fields.options[len] = new Option(choice,choice,false,false);
 document.form1.AllFields.remove(index);
 document.form1.AllFields.options[0].selected = true;
 }
 else
 {
  document.form1.fields.options[0].selected = true;
 }
}


function moveR2l()
{
 var index = document.form1.fields.selectedIndex;
 if(index != -1)
 {
 var choice = document.form1.fields.options[index].text;

 var len = document.form1.AllFields.length;
 document.form1.AllFields.options[len] = new Option(choice,choice,false,false);
 document.form1.fields.remove(index);
 document.form1.fields.options[0].selected = true;
    }
  else
  {
    document.form1.AllFields.options[0].selected = true;
  }

}


function MoveAllRight()
{


var len1 = document.form1.AllFields.length;
for(i=0;i<len1;i++)
{
 var element = document.form1.AllFields.options[i].text;
 var len2 = document.form1.fields.length;
 document.form1.fields.options[len2] =  new Option(element,element,false,false);
}
document.form1.AllFields.length= 0;
}

function MoveAllLeft()
{


var len1 = document.form1.fields.length;
for(i=0;i<len1;i++)
{
 var element = document.form1.fields.options[i].text;
 var len2 = document.form1.AllFields.length;
 document.form1.AllFields.options[len2] =  new Option(element,element,false,false);
}
document.form1.fields.length= 0;
}

function SelectAll ()
{
 var len = document.form1.fields.length;
 for(i=0;i<len;i++)
 {
   document.form1.fields.options[i].selected = true;



 }


}
</script>
<SCRIPT language="JavaScript1.2" src="main.js" type="text/javascript"></SCRIPT>  
<body>
<DIV id="TipLayer" style="visibility:hidden;position:absolute;z-index:1000;top:-100;"></DIV>
<SCRIPT language="JavaScript1.2" src="style.js" type="text/javascript"></SCRIPT>           
<center>
<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?> ">
<table width="732"  height="473" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td align="center" width="64" height="20" background="images/topleft.jpg" style="background-repeat: no-repeat" >
      <td align="center" width="614" height="20" background="images/top.jpg" style="background-repeat: x">
      <td align="center" width="48" height="20" background="images/topright.jpg" style="background-repeat: no-repeat">    </tr>
	<tr>
		<td align="center" width="64" style="background-repeat: y" valign="top" background="images/leftadd.jpg">
           
            <img border="0" src="images/left.jpg"><td rowspan="2" align="center" valign="top" bgcolor="#FFFFFF" >
           
			<p><img border="0" src="images/01.jpg" width="369" height="71"></p>
			<table border="0" width="100%" id="table8" height="331">
				<tr>
					<td height="18" colspan="2" class="step_title">Select fields</td>
				</tr>
				<tr>
					<td colspan="2" height="263" valign="top">

		<table cellspacing="0" cellpadding="0" width="100%" border="0" align="center" id="table9">
			<tr>
				<td width="85%" height="261" valign="top" style="font-family: Verdana, Arial, sans-serif; font-size: 11px; line-height: 1.5">
				<div align="left" class='error'>
                <?php
                echo $error;
                ?>
                </center>					  <table width="501" height="242" border="0" align="center" cellpadding="0" cellspacing="0" id="table13">
						<tr>
							<td width="27" height="16">
						    <img border="0" src="images/ctopleft.jpg" width="38" height="37"></td>
							<td width="425" height="16" background="images/ctop.jpg" style="background-repeat: x">&nbsp;</td>
							<td width="38" height="16">
						    <img border="0" src="images/ctopright.jpg" width="38" height="37"></td>
						</tr>
						<tr>
							<td width="27" height="167" background="images/cleft.jpg" style="background-repeat: y">&nbsp;</td>
							<td width="85%" align="center" valign="top" bgcolor="#F9F9F9">
							  <table border="0" width="67%" id="table14">
							    <tr>
							      <td width="108" class="control_label" >Available Fields</td>
								    <td width="63" style="font-family: Verdana, Arial, sans-serif; font-size: 11px; line-height: 1.5">&nbsp;</td>
								    <td width="109" class="control_label" >Selected Fields</td>
								  </tr>
							    <tr>
							      <td width="108" style="font-family: Verdana, Arial, sans-serif; font-size: 11px; line-height: 1.5">
							        <select size="5" name="AllFields"style="width: 200 px ; Height: 100px;">
								          
								          
							          <?php


      foreach($All_fields as $val)
         {
           if(isset($_SESSION["fields"]))
           {
                   if ( !in_array($val,$_SESSION["fields"]))

                   {
                   echo "<option>$val</option>";
                 }

           }
           else
           {
                echo "<option>$val</option>";
           }

         }

      ?>
					                </select><p></td>
								    <td style="font-family: Verdana, Arial, sans-serif; font-size: 11px; line-height: 1.5" valign="top">
										    
									    <table border="0" width="100%" id="table15" cellspacing="0" cellpadding="0">
									      <tr>
									        <td><input name="B3" type="button" class="arrow_btn" onClick="movel2R();" value="  &gt;  "></td>
										    </tr>
									      <tr>
									        <td>
							                <input name="B4" type="button" class="arrow_btn" onClick="moveR2l()" value="  &lt;  "></td>
										    </tr>
									      <tr>
									        <td>
							                <input name="B5" type="button" class="arrow_btn" onClick="MoveAllRight()" value=" &gt;&gt; "></td>
										    </tr>
									      <tr>
									        <td>
							                <input name="B6" type="button" class="arrow_btn"onclick="MoveAllLeft()" value=" &lt;&lt; "></td>
										    </tr>
									      </table>
  
								    <p></td>
								    <td width="109" valign="top" style="font-family: Verdana, Arial, sans-serif; font-size: 11px; line-height: 1.5">
								      <select size="2" name="SelectedFields[]" ID="fields" style="width: 200 px ; Height: 100px;"multiple>
								        <?php
										
    if(isset($_SESSION["fields"]))
    {
        foreach($_SESSION["fields"] as $val)
        {
          if(in_array($val,$All_fields))
          {
          echo "<option>$val</option>";
          }
        }

    }
										
										
										
										
										
										
										?>
						          </select></td>
								  </tr>
							    <tr>
							      <td colspan="3" align="right" style="font-family: Verdana, Arial, sans-serif; font-size: 11px; line-height: 1.5"><a href="" onMouseOver="stm(Step_4[0],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.gif" width="20" height="15" border="0"></a></td>
						        </tr>
						        
						        
						        	<tr>
										<td>&nbsp;
										</td>
										<td colspan="2">&nbsp;
										</td>
										<td colspan="2">&nbsp;
										</td>
										<td>

										<td align="center">

										    <input type ="image" name="Statistical"  src="images/stat1.jpg"onclick="SelectAll()">
										</td>
										<td>&nbsp;
										</td>
									</tr>
						        	<tr>
										<td>&nbsp;
										</td>
										<td colspan="2">&nbsp;
										</td>
										<td colspan="2">&nbsp;
										</td>
										<td>

										<td align="center">

										    <input type ="image" name="Statistical"  src="layout/statestical.gif"onclick="SelectAll()">
										</td>
										<td>&nbsp;
										</td>
									</tr>
									<tr>
										<td>&nbsp;
										</td>
									</tr>
						        </table>
							  <p></td><td width="38" background="images/cright.jpg" style="background-repeat: y">&nbsp;</td>
						</tr>
						<tr>
							<td width="27" height="37">
						    <img border="0" src="images/cdownleft.jpg" width="38" height="37"></td>
							<td width="425" height="37" background="images/cdown.jpg" bgcolor="#FFFFFF" style="background-repeat: x">								</td>
							<td width="38">
						    <img border="0" src="images/cdownright.jpg" width="38" height="37"></td>
						</tr>
					    </table></td>
			</tr>
		</table>

				  </td>
				</tr>
				<tr>
					<td align="center">
					<?php
				  if($datasource=="table")
					{
						if(count($_SESSION['table'])==1)
						{
						  	echo "<a href='tables_filters.php'>";
						}
						else
						{
						  	echo "<a href='tables_filters.php'>";
						}
					}
					else
					{
					  echo "<a href='step_3_sql.php'>";
					}?>
					<img src="images/03.jpg" border=0 width="170" height="34"></a></td>
					<td align="center">
					<INPUT name=continue type=image id="btn_cont" 
                  src="images/04.jpg" width="166" height="34" onClick="SelectAll()"></td>
				</tr>
			</table>
			<td  align="center" width="48" style="background-repeat: y" valign="top" height="388" background="images/rightadd.jpg">
           
            <img border="0" src="images/right.jpg"></tr>
	<tr>
		<td width="64" height="15" align="center" background="images/leftadd.jpg" style="background-repeat: y">
      <td  align="center" width="48" background="images/rightadd.jpg" style="background-repeat: y" valign="top">
           
    </tr>
	</tr>
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
