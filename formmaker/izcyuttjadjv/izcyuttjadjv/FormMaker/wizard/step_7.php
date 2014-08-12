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
//form vars
if (isset($_POST['btn_generate_form_x']))  $btn_generate_form  = $_POST['btn_generate_form_x'];
if (isset($_POST['btn_back_x']))  $btn_back  = $_POST['btn_back_x'];
if (isset($_POST['txt_form_title']))  $txt_form_title = $_POST['txt_form_title'];
if (isset($_POST['form_desc']))  $txt_form_desc = $_POST['form_desc'];

if(isset($_POST['txt_records_per_page']))  $txt_records_per_page  = $_POST['txt_records_per_page'];
if(isset($_POST['txt_form_name']))  $txt_form_name  = $_POST['txt_form_name'];


//other vars
$page_errors = array();
$is_form_valid = 1;

@$continue= $_POST["continue_x"];
 if(!empty($continue))
 {

      if(empty($txt_form_title))
	{
		$page_errors[] = "Please enter form title.";
		$is_form_valid = 0;
	}
	if(empty($txt_form_name))
	{
		$page_errors[] = "Please enter form name.";
		$is_form_valid = 0;
	}
          if(empty($txt_form_desc))
	{
		$page_errors[] = "Please enter form description";
		$is_form_valid = 0;
                   
	}
          
          
	if($is_form_valid==1)
	{
		$_SESSION['form_title'] = $txt_form_title;
		$_SESSION['form_date_created']=date("d-M-Y H:i:s");
                    $_SESSION['form_form_desc'] = $txt_form_desc;
		$_SESSION['form_file_name'] = $txt_form_name;
                    if(empty($txt_records_per_page))
                        $_SESSION['form_records_per_page'] = '';
                        else
		$_SESSION['form_records_per_page'] = $txt_records_per_page;	
		

                        
		//generate form code goes here
        header("location: engine/common.php");

	}
}
else if(!empty($btn_back))
{
	header("location: step_6.php");
}

function print_page_errors()
{
	global $page_errors;
	if(count($page_errors)>0)
		echo "<td colspan=2 class='error'>";
	foreach($page_errors as $key=>$value)
	{
		echo '* ' . $value . "<br>";
	}
	if(count($page_errors)>0)
		echo "</td>";	
}


//get default valu for each form field
function get_default_value($var)
{	
		if($var == 'txt_form_title') $s_var = 'form_title';
		if($var == 'txt_form_name') $s_var = 'form_file_name';
		if($var == 'records_per_page') $s_var = 'form_records_per_page';
                    if($var == 'form_desc') $s_var = 'form_form_desc';
								
		if($var =='user_name') $s_var = 'form_user';
		if($var=='password') $s_var = 'form_pass';
		if($var=='host_name') $s_var = 'form_host'; 
		
		if(!empty($_POST[$var]))
		{
			return $_POST[$var];
		}
		else if(@!empty($_SESSION[$s_var]))
		{
			return @$_SESSION[$s_var];
		}
		else
		{
			if ($var=='host_name')
			{
				return 'localhost';
			}
		}
		 
}
?>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Form Global Settings</title>
<link href="style.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript1.2" src="main.js" type="text/javascript"></SCRIPT>  
</head>
<body>
<DIV id="TipLayer" style="visibility:hidden;position:absolute;z-index:1000;top:-100;"></DIV>
<SCRIPT language="JavaScript1.2" src="style.js" type="text/javascript"></SCRIPT>           
<center>
<form action="<?php echo($_SERVER['PHP_SELF']); ?>" method="post">
<table width="732"  height="467" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td align="center" width="64" height="20" background="images/topleft.jpg" style="background-repeat: no-repeat" >
           
      <td align="center" width="614" height="20" background="images/top.jpg" style="background-repeat: x">
           
      <td align="center" width="48" height="20" background="images/topright.jpg" style="background-repeat: no-repeat">
           
    </tr>
	<tr>
		<td align="center" width="64" style="background-repeat: y" valign="top" background="images/leftadd.jpg">
           
            <img border="0" src="images/left.jpg"><td rowspan="2" align="center" valign="top" >
           
			<p><img border="0" src="images/logo.png" width="369" height="71"></p>
			<table width="100%" height="337" border="0" align="center" id="table8">
				<tr>
					<td colspan="2" height="22"><span class="step_title">Form Global Settings </span></td>
				</tr>
				<tr>
					<td colspan="2" height="266" valign="top">					  <table width="501" height="248" border="0" align="center" cellpadding="0" cellspacing="0" id="table11">
					    <tr>
					      <td width="27" height="16">
					        <img border="0" src="images/ctopleft.jpg" width="38" height="37"></td>
						    <td width="425" height="16" background="images/ctop.jpg" style="background-repeat: x">&nbsp;</td>
						    <td width="38" height="16">
					        <img border="0" src="images/ctopright.jpg" width="38" height="37"></td>
					    </tr>
					    <tr>
					      <td width="27" background="images/cleft.jpg" style="background-repeat: y">&nbsp;</td>
					      <td width="425" bgcolor="#F9F9F9" align="center">
					           
                                                            <table width="466" height="192" border="0" align="center">
                                                                <tr>
                                                            
                                                                        <?php echo  print_page_errors();?>
                                                                        
                                                                  
                                                                </tr>
					          <tr>
					            <td width="132" height="23" nowrap class="control_label">Form Title </td>
                                <td width="410"><input name="txt_form_title" type="text" id="txt_form_title" size="40" value="<?php echo get_default_value('txt_form_title')?>" />
                                </td>
                                <td width="410"><a href="" onMouseOver="stm(Step_7[0],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.png" border="0" align="absmiddle"></a></td>
					          </tr>
	
					          <tr>
					            <td class="control_label">Form Name </td>
                                <td><input name="txt_form_name" type="text" id="txt_form_name" size="40" value="<?php  echo get_default_value('txt_form_name') ?>" />
								</td>
                                <td valign="middle"><a href="" onMouseOver="stm(Step_7[3],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.png" border="0" align="absmiddle"></a></td>
					          </tr>
                                                            
                                                            
                                                                      <tr>
					            <td class="control_label">Form Description </td>
                                <td><textarea style="margin: 2px;height: 108px;width: 305px;" name="form_desc" type="text" id="txt_form_desc" ><?php  echo get_default_value('form_desc') ?></textarea>
								</td>
                                <td valign="middle"><a href="" onMouseOver="stm(Step_7[3],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.png" border="0" align="absmiddle"></a></td>
					          </tr>
                                                            <?php if($_SESSION["form_layout"] == 'Tabular') {?>
					          <tr>
					            <td class="control_label">Records per page </td>
                                <td><input name="txt_records_per_page" type="text" id="txt_records_per_page" size="40" value="<?php echo get_default_value('txt_records_per_page')?>"></td>
                                <td valign="middle"><a href="" onMouseOver="stm(Step_7[4],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.png" border="0" align="absmiddle"></a></td>
					          </tr> 
                                                            <?php } ?>
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
					<td align="center"><a 
                  href="step_6.php" style="color: #0029a3; text-decoration: none"><img 
                  src="images/03.jpg" border=0 width="170" height="34"></a></td>
					<td align="center"><INPUT name=continue type=image id="btn_cont" 
                  src="images/04.jpg" width="166" height="34" ></td>
				</tr>
			</table>
			<td  align="center" width="48" style="background-repeat: y" valign="top" height="388" background="images/rightadd.jpg">
           
            <img border="0" src="images/right.jpg"></tr>
	<tr>
		<td width="64" height="12" align="center" background="images/leftadd.jpg" style="background-repeat: y">
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
