<?php
/**
    *   Smart Form Maker 
    *   V 1.0.0
    *   All copyrights are preserved to StarSoft
    */
session_start();
require_once '../shared.php';
if(isset ($_GET['new']))
{
    $host_name = $_SESSION['form_host'];
    $user_name = $_SESSION['form_user'];
    $password = $_SESSION['form_pass'];
    foreach($_SESSION as $key=>$val)
        $_SESSION[$key] = NULL;

    session_destroy();
    session_start();
    $_SESSION['form_host'] = $host_name;
    $_SESSION['form_user'] = $user_name;
    $_SESSION['form_pass'] = $password;
}

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

if(isset($_POST['selected_table'])) $selected_table = $_POST['selected_table'];
if(isset($_POST['auto_detect_rel'])) $autodetect = $_POST['auto_detect_rel'];
//vars
$is_form_valid = 1;
$page_errors = '';

$database_cmb_names = '';

$DD_tables = '';
$insert = false;
$update = false;
$delete = false;


if(isset ($_SESSION['form_autodetect']))
   $autodetect =  $_SESSION['form_autodetect'];
else
    $autodetect = "0";


if(isset ($_SESSION['form_data_source']))
   $data_source =  $_SESSION['form_data_source'];
else
   $data_source = "table";


if(!empty($database_name))
    $_SESSION['form_db'] = $database_name;

if(!empty ($_SESSION['form_permission']))
{
  $permission = $_SESSION['form_permission'];
  $insert = substr($permission, 0, 1) == '1'?true:false;
  $update = substr($permission, 1, 1) == '1'?true:false;
  $delete = substr($permission, 2, 1) == '1'?true:false;
}

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
          
          if(empty($selected_table))
	{
		if(!empty($page_errors))
			$page_errors .= "<br>";
		$page_errors .="* Please select Table.";
		$is_form_valid = 0;
	}
          
          if(isset ($_POST['permission']))
          {
              $permission = '';
              $permission .= IsChecked('permission','insert') ? '1' : '0';
              $permission .= IsChecked('permission','update') ? '1' : '0';
              $permission .= IsChecked('permission','delete') ? '1' : '0';
              $_SESSION['form_permission'] = $permission;
          }
          else
          {
                $is_form_valid = 0;
                $page_errors .= "<br>";
                $page_errors .="* One of select form actions required.";
          }
          
          if($is_form_valid && $_SESSION['form_permission'] != '100')
          {
              $host_name = $_SESSION['form_host'];
              $user_name = $_SESSION['form_user'];
	    $password = $_SESSION['form_pass'];
              $unique = array();     
              $connect  = mysql_connect($host_name,$user_name,$password);
              mysql_select_db($_SESSION['form_db']);
              $columns = mysql_query("SHOW COLUMNS FROM `$selected_table`");
              while($field = mysql_fetch_array($columns))
              {
                  if((strtoupper($field['Key']) == "PRI" || strtoupper($field['Key']) == "UNI") && $field['Type'] !== 'bigint(20) unsigned') //get unique keys and avoid serial data type
                     $unique[] = $field['Field'];  
              }
              if(count($unique) == 0)
              {
                   $page_errors .= "* The table must have a primary or unique key in update<br/> or delete actions." ;
                   $is_form_valid = 0;
                   //load_table_dd_data();
              }
              else
                    $_SESSION['form_unique'] = $unique; 
             
          }
          $databases = mysql_query('show databases');
          $information_schema = false;
          while($row = mysql_fetch_array($databases))
          {
              if($row[0] == 'information_schema')
              {  $information_schema = true; break;}
              
          }
          if($_POST['auto_detect_rel'] == "1" && !$information_schema)
          {
               $page_errors .= "<br/>* Your database engine does not allow relationship detection." ;
               $is_form_valid = 0;
               //load_table_dd_data();
          }

	if($is_form_valid)
	{
                $_SESSION['form_db'] = $database_name;
                
                if($selected_table != $_SESSION['form_table']) //handle old relations
                    $_SESSION['form_desc'] = NULL;
                if($_POST['data_source'] == 'table')
                {
                    $_SESSION['form_table'] = $selected_table; 
                    $_SESSION['form_sql'] = '';
                }
                $_SESSION['form_autodetect'] = $_POST['auto_detect_rel'];
                $_SESSION['form_data_source'] = $_POST['data_source'];
                //$_SESSION['form_unique'] = $unique;
                
                
                
                if($_SESSION['form_data_source'] == 'sql')
                    header("Location:step_2_sql.php");
                else
                    header("Location:step_3.php");
	}
          
          
              
}
else if(!empty($btn_back)) //back
{
	header("Location:step_1.php");
	exit;
}
else if(!empty($btn_connect)  || !empty($_SESSION['form_host'])) //connect or back
{
	if(!empty($_SESSION['form_host']) && empty($btn_connect)) //back
	{
	  	$host_name = $_SESSION['form_host'];
		$user_name = $_SESSION['form_user'];
		$password = $_SESSION['form_pass'];
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
                        //save data in the sessions
                        if(!empty($btn_connect)) // only in case of connect
                        {
                                $_SESSION['form_host'] = $host_name;
                                $_SESSION['form_user'] = $user_name;
                                $_SESSION['form_pass'] = $password;
                        }
		}
                    load_table_dd_data();
	}
}
//functions 
function load_table_dd_data()
{
    
                              global $database_cmb_names,$DD_tables;
                              $query = "show databases";
			$result = mysql_query($query);
			
                              $tmp_db = '';
			//get the default value
			if(isset($database_name))
                                 $default_db=$database_name;
			else
                                 $default_db=@$_SESSION['form_db'];	
                              $flag = true;
			while ($row = mysql_fetch_row($result))
			{
                              if(!($row[0] == 'information_schema' || $row[0] == 'performance_schema' || $row[0] == 'mysql'))
                              {
                                    if($flag)
                                    {
                                       $tmp_db = $row[0];
                                    }
                                    $flag = false;
                                    if($default_db==$row[0])
                                            $database_cmb_names .= "<option selected>";
                                    else
                                            $database_cmb_names .= "<option >";

                                    $database_cmb_names .= $row[0];
                                    $database_cmb_names .= "</option>\r\n";
                              }
			}
                           
                              if(!isset($database_name) && !isset ($_SESSION['form_db']))
                                  $default_db = $tmp_db;
                              mysql_select_db($default_db);
                              mysql_free_result($result); 
                              $query = "show tables";
                              $result = mysql_query($query);
                              
                              //get the default value
			if(isset($selected_table))
				$default_tbl=$selected_table;
			else
				$default_tbl=@$_SESSION['form_table'];
                              
                              while ($row = mysql_fetch_row($result))
			{
                                  
                                    if($default_tbl==$row[0])
                                              $DD_tables .= "<option selected>";
                                    else
                                              $DD_tables .= "<option >";

                                    $DD_tables .= $row[0];
                                    $DD_tables .= "</option>\r\n";
                            
			}
}
function IsChecked($chkname,$value)
    {
        if(!empty($_POST[$chkname]))
        {
            foreach($_POST[$chkname] as $chkval)
            {
                if($chkval == $value)
                {
                    return true;
                }
            }
        }
        return false;
    }

//get default valu for each form field
function get_default_value($var)
{
		$s_var = $var;
		if($var =='user_name') $s_var = 'form_user';
		if($var=='password') $s_var = 'form_pass';
		if($var=='host_name') $s_var = 'form_host';
		if($var=='data_source') $s_var = 'form_datasource';		

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
<SCRIPT language="JavaScript1.2" src="../js/jquery-1.7.2.min.js" type="text/javascript"></SCRIPT>  
<SCRIPT language="JavaScript1.2" src="main.js" type="text/javascript"></SCRIPT>  
<script>
    $(function(){
        $('#select_ds').change(function(){
            if($(this).val() == 'table')
                $('#tr_table').show();
            else
                $('#tr_table').hide(); 
        });
        $('#select_ds').change();
    });
</script>
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
           
			<p><img border="0" src="images/logo.png" width="369" height="71"></p>
			<table border="0" width="100%" id="table8" height="333">
				<tr>
                                          <td height="18" colspan="2" class="step_title">Please enter MySQL database parameters</td>
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
										<td width="30%" align="right" class="control_label">Hostname</td>
									  <td width="68%" valign="middle">
										<input name="host_name" type="text" id="host_name" value="<?php echo get_default_value('host_name')?>" size="21" />
										<a href="" onMouseOver="stm(Step_2[0],Style);" onClick="return false;" onMouseOut="htm()"> <img src="images/Help.png" border="0"></a></td>
									</tr>
									<tr>
										<td width="30%" align="right" class="control_label">
										Username</td>
									  <td width="68%" valign="middle">
										<input name="user_name" type="text" id="user_name" size="21" value="<?php echo get_default_value('user_name') ?>">
										<a href="" onMouseOver="stm(Step_2[1],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.png" border="0"></a></td>
									</tr>
									<tr>
										<td width="30%" align="right" class="control_label">
										Password</td>
									  <td width="68%" valign="middle">
										<input name="password" type="text" id="password" size="21" value="<?php get_default_value('password')?>">
										<a href="" onMouseOver="stm(Step_2[2],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.png" border="0"></a></td>
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
                                                                                                <select style="width: 178px;" name="database_name" size="1" id="database_name" onchange="$('form').submit();">
										<?php echo($database_cmb_names); ?>;
										
										</select>
										<a href="" onMouseOver="stm(Step_2[3],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.png" border="0"></a></td>
									</tr>
                                                                                   
                                                                        
                                                         <tr style="display: none;">
										<td width="30%" align="right" class="control_label">Data Source</td>
									  <td width="68%">
                                                                                <select name="data_source" style="width: 178px;" id="select_ds">
                                                                                    <option <?php if($data_source == 'table') echo 'selected'; ?> value="table">Table</option>
                                                                                    <option value="sql" <?php if($data_source == 'sql') echo 'selected'; ?>  >SQL Statement</option>
										</select>
										<a href="" onMouseOver="stm(Step_2[10],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.png" border="0"></a></td>
									</tr>               
                                                                        
                                                                                          <tr id="tr_table">
										<td width="30%" align="right" class="control_label">Select 
										Table</td>
									  <td width="68%">
                                                                                                <select name="selected_table" style="width: 178px;">
										<?php echo $DD_tables; ?>;
										
										</select>
										<a href="" onMouseOver="stm(Step_2[4],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.png" border="0"></a></td>
									</tr>
                                                                                             <tr>
										<td width="30%" align="right" class="control_label">Select Form Actions</td>
									  <td width="68%">
                                                                                                <label><input type="checkbox" name="permission[]"  value="insert" <?php if(!empty ($_SESSION['form_permission'])){if($insert) echo 'checked';} else{ echo 'checked';} ?>  /> Insert</label><a href="" onMouseOver="stm(Step_2[5],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.png" border="0"></a><br/>
                                                                                                <label><input type="checkbox" name="permission[]"  value="update" <?php if($update) echo 'checked'; ?> /> Update</label><a href="" onMouseOver="stm(Step_2[6],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.png" border="0"></a><br/>
                                                                                                <label><input type="checkbox" name="permission[]"  value="delete" <?php if($delete) echo 'checked'; ?> /> Delete</label><a href="" onMouseOver="stm(Step_2[7],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.png" border="0"></a><br/>
                                                                                            </td>
                                                                                             <tr>
									<td width="30%" align="right" class="control_label">Auto Detect Relations</td>
									  <td width="68%">
                                                                                                <label><input <?php if($autodetect == '1') echo 'checked'; ?> name="auto_detect_rel" type="radio" value="1" />Yes</label><label><input <?php if($autodetect == '0') echo 'checked'; ?> name="auto_detect_rel" type="radio" value="0" />No</label>
                                                                                                <a href="" onMouseOver="stm(Step_2[9],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.png" border="0"></a>
                                                                                            </td>
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
