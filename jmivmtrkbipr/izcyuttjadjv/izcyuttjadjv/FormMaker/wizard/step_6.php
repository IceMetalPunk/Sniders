<?php
/**
    *   Smart Form Maker 
    *   V 1.0.0
    *   All copyrights are preserved to StarSoft
    */
session_start();
require_once '../shared.php';
   
 if(!array_key_exists('form_table',$_SESSION))
     Header("Location: ../index.php");
 
if(isset($_POST['style_name'])) $style_name = $_POST['style_name'];

if(isset($_POST['btn_continue_x'])) $btn_continue = $_POST['btn_continue_x'];

if(isset($_POST['btn_back_x'])) $btn_back = $_POST['btn_back_x'];

if(isset($_POST['css'])) $css = $_POST['css'];

if(isset($_POST['btn_create_new_style_x'])) $btn_create_new_style = $_POST['btn_create_new_style_x'];





//other vars

$page_errors=array();

$is_page_valid = 1;

//continue button

if(!empty($btn_continue))
{
	if(!empty($style_name))
	{
		//save css style name into the session
		$_SESSION['form_style_name'] = $style_name;
		save_css_changes();

		if($is_page_valid)
		{
			$_SESSION['form_style_name'] =$style_name;
			header('location: step_7.php');
		}
	}
	else
	{
		$page_errors[] = 'Please select style name';
	}

}

//create new style

else if(!empty($btn_create_new_style))

{

	//if(!empty($style_name)) $_SESSION['form_style_name'] = $style_name ;
        unset($_SESSION['form_style_name']); 
	header("location: create_new_style.php");

}

//back button

if(!empty($btn_back))

{
         if($_SESSION["form_layout"] == 'Master_Details')
            header("location: details_form_step.php");
         else
            header("location: step_5.php");

}





//function print styles

function print_styles_names()

{

	global $style_name;

	$style_name = get_default_value('style_name');

	

	$d = dir("styles");

	$i=0;

	

	while (false != ($entry = $d->read())) {

		if($entry!="."  & $entry!="..")

		{

			$formatted_css_name = substr( $entry,0,strlen($entry)-4) ;

			if($i==0 &&empty($style_name))

			{

				$style_name  = $formatted_css_name;

			}

		

			if($style_name == $formatted_css_name)

	   				echo "<option selected>" . $formatted_css_name. "</option>";			

			else	

	   				echo "<option>" . $formatted_css_name. "</option>";

					

			$i++;

		}

	}

	$d->close();	

}



function print_css_content()

{

global $style_name;

$handle = fopen('styles/'.$style_name . ".css", "r");

while (!feof($handle)) {

    $buffer = fgets($handle, 4096);

    echo $buffer;

}

fclose($handle);		

}



//save changes to the css file

function save_css_changes()

{

	global $css, $style_name,$is_page_valid;

	if(!@$fp = fopen("styles/".$style_name.'.css', "w"))

	{

			$page_errors = "Unable to update changes to the CSS file. Please make sure you give write permission to 'Syles' folder";

			$is_page_valid = 0;

	}

	else
	{
		$css = stripslashes($css);
		if(!@fwrite($fp,$css))

		{

			$page_errors = "Unable to update changes to the CSS file. Please make sure you give write permission to 'Syles' folder";

			$is_page_valid =0;

		}

		@fclose($fp);
		
	}

}



//print page errors

function print_page_errors()

{

	global $page_errors;

	foreach($page_errors as $key=>$value)

	{

		echo '* ' . $value;

	}

}



//get default value for each form field

function get_default_value($var)

{		

		//echo "var name is" . $var;

		//echo "session is " . $_SESSION[$var];
                    if($var == 'style_name') $s_var = 'form_style_name';
		if(!empty($_POST[$var]))

		{

			return $_POST[$var];

		}

		else if(!empty($_SESSION[$s_var]))

		{

			return $_SESSION[$s_var];

		}
                else if($var == 'style_name')
                    return 'blue';

}

?>

<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Form Style</title>
<link href="style.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
    function refresh()
    {
        document.getElementById('btn_continue').disabled = 'disabled';
        myform.submit();
    }
</script>
</head>
<SCRIPT language="JavaScript1.2" src="main.js" type="text/javascript"></SCRIPT>  
<body>
<DIV id="TipLayer" style="visibility:hidden;position:absolute;z-index:1000;top:-100;"></DIV>
<SCRIPT language="JavaScript1.2" src="style.js" type="text/javascript"></SCRIPT>  
<center>
<form name="myform" action="<?php echo($_SERVER['PHP_SELF']); ?>" method="post">
<table width="732"  height="483" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td align="center" width="64" height="20" background="images/topleft.jpg" style="background-repeat: no-repeat" >
           
      <td align="center" width="614" height="20" background="images/top.jpg" style="background-repeat: x">
           
      <td align="center" width="48" height="20" background="images/topright.jpg" style="background-repeat: no-repeat">
           
    </tr>
	<tr>
		<td align="center" width="64" style="background-repeat: y" valign="top" background="images/leftadd.jpg">
           
            <img border="0" src="images/left.jpg"><td rowspan="2" align="center" valign="top" >
           
			<p><img border="0" src="images/logo.png" width="369" height="71"></p>
			<table width="100%" height="333" border="0" align="center" id="table8">
				<tr>
					<td colspan="2" height="18"><strong>Form Style </strong></td>
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
					      <td width="425" bgcolor="#F9F9F9" align="center"><table width="449" height="126" border="0" align="center">
					        <tr>
						        <?php
										if(!empty($page_errors))
										{
											echo "<td align='left' colspan='2' height='26' valign='top' class='error'>$page_errors</td>";
										}
									?>                                 
				            </tr>
					        <tr>
					          <td width="49" height="31"><strong>Style</strong></td>
                                                            <td><select name="style_name" id="style_name" onChange="refresh()">
                                  <?php print_styles_names(); ?>
                                </select>
                              <a href="" onMouseOver="stm(Step_6[0],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.png" border="0"></a></td>
                              
                              <td align="right"><input name="btn_create_new_style" type="image" id="btn_create_new_style" src="layout/button_create_new_style.gif" width="116" height="23" border="0">
                              <a href="" onMouseOver="stm(Step_6[1],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.png" border="0" align="absmiddle"></a></td>
                            </tr>
					        <tr>
					          <td>&nbsp;</td>
                              <td colspan="2"><textarea name="css" cols="50" rows="10" id="css"><?php print_css_content();?> </textarea></td>
                              <td><a href="" onMouseOver="stm(Step_6[2],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.png" border="0" align="absmiddle"></a></td>
					        </tr>
				          </table>
				          </td>
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
					<td align="center">
                                            
                                              <INPUT name="btn_back" type=image id="btn_back_x" 
                  src="images/03.jpg" width="166" height="34" />
                                            

                                        
                                        
                                        </td>
					<td align="center">
                                            <INPUT name=btn_continue type=image id="btn_continue" 
                  src="images/04.jpg" width="166" height="34"></td>
				</tr>
			</table>
			<td  align="center" width="48" style="background-repeat: y" valign="top" height="388" background="images/rightadd.jpg">
           
            <img border="0" src="images/right.jpg"></tr>
	<tr>
		<td width="64" height="30" align="center" background="images/leftadd.jpg" style="background-repeat: y">
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
