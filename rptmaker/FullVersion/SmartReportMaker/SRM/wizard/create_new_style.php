<?php
	session_start();
	 error_reporting(E_ERROR  | E_PARSE);
	//get form vars
	if(isset($_POST['style_name'])) $style_name = $_POST['style_name'];
	if(isset($_POST['css_code'])) $css_code = $_POST['css_code'];
	if(isset($_POST['btn_save_x'])) $btn_save = $_POST['btn_save_x'];
	if(isset($_POST['btn_cancel_x'])) $btn_cancel = $_POST['btn_cancel_x'];	
	
	//other vars
	$is_page_valid = 1;
	$page_errors = array();
	
	if(!empty($btn_save))
	{
		if(empty($style_name))
		{
			$page_errors[] = "Please enter style name";
			$is_page_valid = 0;
		}
		else if (empty($css_code))
		{
			$page_errors[] = "Please enter CSS tags";
			$is_page_valid = 0;
		}
		else
		{
			save_css();
			if($is_page_valid)
			{
				header("location: step_8.php");
			}
		}
	}
	
	else if(!empty($btn_cancel))
	{
		header("location: step_8.php");		
	}
	
	function print_errors()
	{
		global $page_errors;
		if(count($page_errors)>0)
			echo "<td colspan=2>";
		foreach($page_errors as $key=>$value)
		{
			echo '* ' . $value . "<br>";
		}
		if(count($page_errors)>0)
			echo "</td>";	
	}
	
	function save_css()
	{
		global $css_code, $style_name,$is_page_valid;
		
		if(!$fp = fopen("styles/".str_replace(' ' ,'_',trim( $style_name)).'.css', "w"))
		{
				$page_errors = "Unable to  create CSS file. Please make sure you give write permission to 'Syles' folder";
				$is_page_valid = 0;
		}
		else
			if(!fwrite($fp,$css_code))
			{
				$page_errors = "Unable to write to CSS file. Please make sure you give write permission to 'Syles' folder";
				$is_page_valid =0;
			}
			fclose($fp);
	}
	
	function get_default_css()
	{
		global $css_code;
		if(empty($css_code ))
		{
			return "/* Controls the page background colors and margings */
.MainPage
{
			
}
			
/* main table */
.MainTable
{
			 
}
			
			
/* Report Title */
.Title
{
			
}
			
/* Separate between sections  */
.Separator 
{
			
}
			
/* The first grouping levels */
.MainGroup
{
			
}
		
/* Any subgrouping levels */
.SubGroup
{
			
}
			
/* records Table Header */
.TableHeader
{
			
}
			
			
/* columns header */
.ColumnHeader
{
	
}
			
			
/* Table Cell */
.TableCell
{
		 
}
			
/* Alternate table’s cell (Toggling) */
.AlternateTableCell
{
			
}
			
/* navigation menu */
.menu {
font-family: Verdana, Arial, Helvetica, sans-serif;
	color: #000000;
	background-color:#FFAC33;
	border-color:#00AC33;		
}

/* Navigation Menu when mouse hover */
.menu_hover
{
	color: #000000;
	background-color:#FFCC33;	
}
			
/* visited links style */
a:visited
{
			
}
			
/* default links style
a:link
{
			
}	
				
				
/* page Footer */
.TableFooter
{
			
}";
		}
		else
		{
			return $css_code;
		}
	}
	
?>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Select table</title>
<link href="style.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript1.2" src="main.js" type="text/javascript"></SCRIPT>  
</head>
<body>
<DIV id="TipLayer" style="visibility:hidden;position:absolute;z-index:1000;top:-100;"></DIV>
<SCRIPT language="JavaScript1.2" src="style.js" type="text/javascript"></SCRIPT>

<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
<table width="732"  height="498" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td align="center" width="64" height="20" background="images/topleft.jpg" style="background-repeat: no-repeat" >
           
      <td align="center" width="614" height="20" background="images/top.jpg" style="background-repeat: x">
           
      <td align="center" width="48" height="20" background="images/topright.jpg" style="background-repeat: no-repeat">
           
    </tr>
	<tr>
		<td align="center" width="64" style="background-repeat: y" valign="top" background="images/leftadd.jpg">
           
            <img border="0" src="images/left.jpg"><td rowspan="2" align="center" valign="top" >
           
			<p><img border="0" src="images/01.jpg" width="369" height="71"></p>
			<table border="0" id="table8" height="333">
				<tr>
					<td height="18" colspan="2" class="step_title">Create New Style </td>
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
					      <td width="425" bgcolor="#F9F9F9" align="center"><table width="446" height="197" border="0" align="center">
					        <tr>
					          <?php print_errors();?>				            </tr>
					        <tr>
					          <td width="91" height="23" nowrap>Style Name </td>
                              <td width="451"><input name="style_name" type="text" id="style_name" size="40" maxlength="20" />
                              <a href="" onMouseOver="stm(New_Style[0],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.gif" width="20" height="15" border="0"></a></td>
                            </tr>
					        <tr>
					          <td height="21" class="control_label">CSS Code </td>
                              <td valign="top"><textarea name="css_code" cols="40" rows="10" id="css_code"><?php echo get_default_css();?></textarea>
                              <a href="" onMouseOver="stm(New_Style[1],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.gif" width="20" height="15" border="0"></a></td>
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
					<td align="center"><a 
                  href="step_8.php" style="color: #0029a3; text-decoration: none"><img 
                  src="images/03.jpg" border=0 width="170" height="34"></a></td>
					<td align="center"><INPUT name=btn_save type=image id="btn_save" 
                  src="images/04.jpg" width="166" height="34"></td>
				</tr>
			</table>
			<td  align="center" width="48" style="background-repeat: y" valign="top" height="388" background="images/rightadd.jpg">
           
            <img border="0" src="images/right.jpg"></tr>
	<tr>
		<td width="64" height="45" align="center" background="images/leftadd.jpg" style="background-repeat: y">
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
