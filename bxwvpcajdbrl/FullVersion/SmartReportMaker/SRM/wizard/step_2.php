<?php
session_start();
error_reporting(E_ERROR  | E_PARSE);
//get form variables

//buttons
if(isset($_POST['btn_cont_x'])) $btn_continue = $_POST['btn_cont_x'];
if(isset($_POST['btn_back_x'])) $btn_back = $_POST['btn_back_x'];
if(isset($_POST['btn_connect_x'])) $btn_connect = $_POST['btn_connect_x'];
//input fields
if(isset($_POST['host_name'])) $host_name = $_POST['host_name'];
if(isset($_POST['user_name'])) $user_name = $_POST['user_name'];
if(isset($_POST['password'])) $password = $_POST['password'];
if(isset($_POST['database_name'])) $database_name = $_POST['database_name'];
if(isset($_POST['data_source'])) $data_source = $_POST['data_source'];
//vars
$is_form_valid = 1;
$page_errors = '';

//check which button was clicked
if(!empty($btn_continue)) //continue
{
	if(empty($host_name))
	{
		$page_errors = "* Please enter host name.";
		$is_form_valid = 0;
	}
	if(empty($user_name))
	{
		if(!empty($page_errors))
			$page_errors .= "<br>";
		$page_errors .= "* Please enter user name." ;
		$is_form_valid = 0;
	}
	if(empty($database_name))
	{
		if(!empty($page_errors))
			$page_errors .= "<br>";
		$page_errors .="* Please select database name.";
		$is_form_valid = 0;

	}

	if($is_form_valid)
	{
		$_SESSION['db'] = $database_name;
		$_SESSION['datasource'] = $data_source; 
		//print_r($_SESSION); // table or query

		if($data_source =='sql') //sql query selected
		{
			header("Location:step_3_sql.php");
		}
		else //table selected
		{
			header("Location:step_3.php");
		}
	}
}
else if(!empty($btn_back)) //back
{
	header("Location:step_1.php");
	exit;
}
else if(!empty($btn_connect)  || !empty($_SESSION['host'])) //connect or back
{
	if(!empty($_SESSION['host']) && empty($btn_connect)) //back
	{
	  	$host_name = $_SESSION['host'];
		$user_name = $_SESSION['user'];
		$password = $_SESSION['pass'];
	}

	if(empty($host_name))
	{
		$form_errors = "* Please enter host name.";
		$is_form_valid = 0;
	}
	if(empty($user_name))
	{
		$page_errors .= "* Please enter user name." ;
		$is_form_valid = 0;
	}
	if($is_form_valid ==1)
	{
		if(@!mysql_connect($host_name, $user_name, $password))
		{
			if(!empty($page_errors))
			{
				$page_errors .="<br>";
			}
			$page_errors .= "* Unable to connect. Please enter valid host name, user name and password";
		}
		else
		{
        	// save data in the sessions
            if(!empty($btn_connect)) // only in case of connect
            {
	        	$_SESSION['host'] = $host_name;
	        	$_SESSION['user'] = $user_name;
	        	$_SESSION['pass'] = $password;
            }

			$query = "show databases;";
			$result = mysql_query($query);
			$database_cmb_names = '';
			//get the default value
			if(isset($database_name))
				$default_db=$database_name;
			else
				$default_db=@$_SESSION['db'];			
			while ($row = mysql_fetch_row($result))
			{
				if($default_db==$row[0])
					$database_cmb_names .= "<option selected>";
				else
					$database_cmb_names .= "<option >";
				$database_cmb_names .= $row[0];
				$database_cmb_names .= "</option>\r\n";
			}
		}
	}

}

//functions section
//get default valu for each form field
function get_default_value($var)
{
		$s_var = $var;
		if($var =='user_name') $s_var = 'user';
		if($var=='password') $s_var = 'pass';
		if($var=='host_name') $s_var = 'host';
		if($var=='data_source') $s_var = 'datasource';		

		if(isset($_POST[$var]))
		{
			return $_POST[$var];
		}
		else if(@isset($_SESSION[$s_var]))
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
<title>Select table</title>
<link href="style.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript1.2" src="main.js" type="text/javascript"></SCRIPT>  
</head>

<body>
<DIV id="TipLayer" style="visibility:hidden;position:absolute;z-index:1000;top:-100;"></DIV>
<SCRIPT language="JavaScript1.2" src="style.js" type="text/javascript"></SCRIPT>           

<center>
<form action="<?php echo($_SERVER['PHP_SELF']); ?>" method="post">
<table border="0"  height="468" cellspacing="0" cellpadding="0" width="732">
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
					<td height="18" colspan="2" class="step_title">Please enter MySQL database 
					parameters</td>
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
								<td width="425" bgcolor="#F9F9F9" align="center">
								<table border="0" width="118%" id="table12" height="136">
									<tr>
									<?php
										if(!empty($page_errors))
										{
											echo "<td align='left' colspan='2' height='26' valign='top' class='error'>$page_errors</td>";
										}
									?>
									</tr>
									<tr>
										<td width="30%" align="right" class="control_label">Host 
										name</td>
									  <td width="68%" valign="middle">
										<input name="host_name" type="text" id="host_name" value="<?php echo get_default_value('host_name')?>" size="21" />
										<a href="" onMouseOver="stm(Step_2[0],Style);" onClick="return false;" onMouseOut="htm()"> <img src="images/Help.gif" width="20" height="15" border="0"></a></td>
									</tr>
									<tr>
										<td width="30%" align="right" class="control_label">
										Username</td>
									  <td width="68%" valign="middle">
										<input name="user_name" type="text" id="user_name" size="21" value="<?php echo get_default_value('user_name') ?>">
										<a href="" onMouseOver="stm(Step_2[1],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.gif" width="20" height="15" border="0"></a></td>
									</tr>
									<tr>
										<td width="30%" align="right" class="control_label">
										Password</td>
									  <td width="68%" valign="middle">
										<input name="password" type="text" id="password" size="21" value="<?php get_default_value('password')?>">
										<a href="" onMouseOver="stm(Step_2[2],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.gif" width="20" height="15" border="0"></a></td>
									</tr>
									<tr>
										<td colspan="2">&nbsp;</td>
									</tr>
									<tr>
										<td colspan="2">
										<p align="center">
										<input name="btn_connect" type="image" id="btn_connect"  src="layout/button_connect.gif" /> 
										</td>
									</tr>
									<tr>
										<td width="30%" align="right" class="control_label">Select 
										Database</td>
									  <td width="68%">
										<select name="database_name" size="1" id="database_name">
										<?php echo($database_cmb_names); ?>;
										
										</select>
										<a href="" onMouseOver="stm(Step_2[3],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.gif" width="20" height="15" border="0"></a></td>
									</tr>
									<tr>
										<td width="30%" align="right" class="control_label">
										Data Source </td>
									  <td width="68%">
										<select name="data_source" size="1" id="cmb_data_source0">
										<option value="table" <?php if(get_default_value('data_source')=='table') echo 'selected'?>>
										Table</option>
										<option value="sql" <?php if(get_default_value('data_source')=='sql') echo 'selected'?>>
										SQL Query</option></select>
										<a href="" onMouseOver="stm(Step_2[4],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.gif" width="20" height="15" border="0"></a></td>
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
						</table></div>				  </td>
				</tr>
				<tr>
					<td align="center"><a href="../index.php"><img 
                  src="images/03.jpg" border=0 width="170" height="34"></a></td>
					<td align="center"><INPUT name=btn_cont type=image id="btn_cont" 
                  src="images/04.jpg" width="166" height="34"></td>
				</tr>
			</table>
			<td  align="center" width="48" style="background-repeat: y" valign="top" height="388" background="images/rightadd.jpg">
           
            <img border="0" src="images/right.jpg"></tr>
	<tr>
		<td width="64" height="13" align="center" background="images/leftadd.jpg" style="background-repeat: y">
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
