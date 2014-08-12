<?php
session_start();
 error_reporting(E_ERROR  | E_PARSE);
//form vars
if (isset($_POST['btn_generate_report_x']))  $btn_generate_report  = $_POST['btn_generate_report_x'];
if (isset($_POST['btn_back_x']))  $btn_back  = $_POST['btn_back_x'];
if (isset($_POST['txt_report_title']))  $txt_report_title = $_POST['txt_report_title'];
if (isset($_POST['txt_report_header']))  $txt_report_header = $_POST['txt_report_header'];
if (isset($_POST['txt_report_footer']) ) $txt_report_footer = $_POST['txt_report_footer'];
if(isset($_POST['txt_records_per_page']))  $txt_records_per_page  = $_POST['txt_records_per_page'];
if(isset($_POST['txt_report_name']))  $txt_report_name  = $_POST['txt_report_name'];
if(isset($_POST['chkSearch']))  $chkSearch  = $_POST['chkSearch'];


//other vars
$page_errors = array();
$is_form_valid = 1;

if(!empty($btn_generate_report))
{
	if(empty($txt_report_name))
	{
		$page_errors[] = "Please enter report name";
		$is_form_valid = 0;
	}
	if($is_form_valid==1)
	{
		$_SESSION['title'] = $txt_report_title;
		$_SESSION['date_created']=date("d-M-Y H:i:s");
		$_SESSION['header'] = $txt_report_header;
		$_SESSION['footer'] = $txt_report_footer;
		$_SESSION['file_name'] = $txt_report_name;
		$_SESSION['records_per_page'] = $txt_records_per_page;
		$_SESSION['chkSearch'] = $chkSearch;
		 
		
		
		//generate report code goes here
        header("location: engine/common.php");

	}
}
else if(!empty($btn_back))
{
	header("location: step_8.php");
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
		if($var == 'txt_report_title') $s_var = 'title';
		if($var == 'txt_report_header') $s_var = 'header';
		if($var == 'txt_report_footer') $s_var = 'footer';
		if($var == 'txt_report_name') $s_var = 'file_name';
		if($var == 'records_per_page') $s_var = 'records_per_page';
								
		if($var =='user_name') $s_var = 'user';
		if($var=='password') $s_var = 'pass';
		if($var=='host_name') $s_var = 'host'; 
		
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
<title>Report Settings</title>
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
           
			<p><img border="0" src="images/01.jpg" width="369" height="71"></p>
			<table width="100%" height="337" border="0" align="center" id="table8">
				<tr>
					<td colspan="2" height="22"><span class="step_title">Report Settings </span></td>
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
					            <?php print_page_errors();?>				              </tr>
					          <tr>
					            <td width="132" height="23" nowrap class="control_label">Report Title </td>
                                <td width="410"><input name="txt_report_title" type="text" id="txt_report_title" size="40" value="<?php echo get_default_value('txt_report_title')?>" />
                                </td>
                                <td width="410"><a href="" onMouseOver="stm(Step_9[0],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.gif" width="20" height="15" border="0" align="absmiddle"></a></td>
					          </tr>
					          <tr>
					            <td height="21" nowrap class="control_label">Report Footer </td>
                                <td valign="top"><textarea name="txt_report_footer" cols="40" rows="3" id="txt_report_footer"><?php echo get_default_value('txt_report_footer') ?></textarea>
                                </td>
                                <td valign="middle"><a href="" onMouseOver="stm(Step_9[1],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.gif" width="20" height="15" border="0" align="absmiddle"></a></td>
					          </tr>
					          <tr>
					            <td height="21" nowrap class="control_label">Report Header </td>
                                <td valign="top"><textarea name="txt_report_header" cols="40" rows="3" id="txt_report_header"><?php echo get_default_value('txt_report_header')?></textarea>
                                </td>
                                <td valign="middle"><a href="" onMouseOver="stm(Step_9[2],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.gif" width="20" height="15" border="0" align="absmiddle"></a></td>
					          </tr>
					          <tr>
					            <td class="control_label">Report name </td>
                                <td><input name="txt_report_name" type="text" id="txt_report_name" size="40" value="<?php  echo get_default_value('txt_report_name') ?>" />
								</td>
                                <td valign="middle"><a href="" onMouseOver="stm(Step_9[3],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.gif" width="20" height="15" border="0" align="absmiddle"></a></td>
					          </tr>
					          <tr>
					            <td class="control_label">Records per page </td>
                                <td><input name="txt_records_per_page" type="text" id="txt_records_per_page" size="40" value="<?php echo get_default_value('txt_records_per_page')?>"></td>
                                <td valign="middle"><a href="" onMouseOver="stm(Step_9[4],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.gif" width="20" height="15" border="0" align="absmiddle"></a></td>
					          </tr> 
							  <?php if($_SESSION["datasource"]=='table') { ?>
							  
							  <tr style="display: none;">
					            <td class="control_label">Enable report search </td>
								
                                <td> 
								
								<input name="chkSearch" type="checkbox" id="chkSearch" checked  value="Yes"></td>
                                <td valign="middle">  </td>
					          </tr>
							   <?php }?>
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
					<td align="center"><INPUT name=btn_generate_report type=image id="btn_generate_report" 
                  src="images/04.jpg" width="166" height="34"></td>
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
