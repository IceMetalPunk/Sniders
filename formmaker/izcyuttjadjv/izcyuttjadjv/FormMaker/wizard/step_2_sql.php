<?php
  session_start();
  error_reporting(E_ERROR  | E_PARSE);
  
  //get form data
  //if(isset($_POST['btn_validate_x']))	$btn_validate = $_POST['btn_validate_x'];
  if(isset($_POST['btn_continue_x'])) $btn_continue = $_POST['btn_continue_x'];
  if(isset($_POST['btn_validate_x'])) $btn_validate = $_POST['btn_validate_x'];
  if(isset($_POST['sql'])) $sql = $_POST['sql'];
  else $sql = '';

if(get_magic_quotes_gpc())
{
  $sql = stripslashes($sql);
}

  $page_errors = '';
  $is_form_valid = 1;

  if(!empty($btn_validate)) //execute command
  {
            if(empty($sql))
            {
                      $page_errors = "Please enter SQL statement";
                      $is_form_valid = 0;
            }
          if(substr(strtolower($sql), 0, 6) != 'select')
            {
                      $page_errors = "Please enter select sql statment only.";
                      $is_form_valid = 0;
            }
            if($is_form_valid)							
            {
                      if(!@mysql_connect($_SESSION['form_host'], $_SESSION['form_user'], $_SESSION['form_pass']))
                      {
                                append_status("* Unable to connect! Please try again.");
                      }
                      else
                      {	
                                if(!@mysql_select_db($_SESSION['form_db']))
                                {
                                          append_status("* Unable to select database.");
                                }
                                else
                                {
                                          $result = @mysql_query($sql );
                                          if(!$result)
                                          {
                                                    append_status("Invalid SQL statement");
                                          }
                                          else
                                          {
                                                    append_status("Valid SQL statement");					
                                          }

                                }		
                      }
            }
  }
  else if(!empty($btn_continue )) //continue command
  {
            if(empty($sql))
            {
                      append_status( "Please enter SQL statement");
                      $is_form_valid = 0;
            }
            else if(substr(strtolower(trim($sql)), 0, 6) != 'select')
            {
                      $page_errors = "Please enter select sql statment only.";
                      $is_form_valid = 0;
            }
            else
            {
                      if(!@mysql_connect($_SESSION['form_host'], $_SESSION['form_user'], $_SESSION['form_pass']))
                      {
                                append_status("* Unable to connect! Please try again.");
                      }
                      else
                      {	
                                if(!@mysql_select_db($_SESSION['form_db']))
                                {
                                          append_status("* Unable to select database.");
                                }
                                else
                                {
                                          $result = @mysql_query($sql );
                                          if(!$result)
                                          {
                                                    append_status("Invalid SQL statement");
                                          }
                                          else
                                          {
                                                    $pos_from = strpos(strtoupper($sql),'FROM')+4;
                                                    $pos_length = 2;
                                                    while(true)
                                                    {

                                                        if(substr($sql, $pos_from+$pos_length, 1) == ' ' || substr($sql, $pos_from+$pos_length, 1) == '')
                                                            break;
                                                        else
                                                            $pos_length++;
                                                    }
                                                     $table = trim(substr($sql,$pos_from,$pos_length));
                                                     $select = trim(substr($sql,  strpos(strtoupper($sql), 'SELECT')+6,strpos(strtoupper($sql), 'FROM') - strpos(strtoupper($sql), 'SELECT')-6));
                                                     if($select != '*')
                                                        $columns_from_query = array_map('trim',explode(',',$select ));
                                                     $columns_from_db = array();
                                                     $temp_columns = array();
                                                     $sql_get_columns = mysql_query("SHOW COLUMNS FROM `$table`");
                                                    while($row = mysql_fetch_array($sql_get_columns))
                                                            $columns_from_db[] = trim($row[0]);
                                                    if($select == '*')
                                                        $temp_columns =   $columns_from_db;
                                                    else
                                                    {
                                                        foreach ($columns_from_query as $key)
                                                        {
                                                            if(strpos($key,'.') != false)
                                                               $key = trim(substr($key,strpos($key,'.')+1));
                                                            if(in_array($key, $columns_from_db))
                                                                    $temp_columns[] = $key;
                                                        }
                                                    }
                                                    $_SESSION['form_table'] = $table;
                                                    $_SESSION['form_fields'] = $temp_columns;
                                                   //echo print_r($temp_columns);
                                                    header("location:step_3.php");
                                                }
                                }		
                      }
            }
  }

  //functions section
  function append_status($status)
  {
             global $page_errors;
            if(!empty($page_errors))
            {
                      $page_errors .="<br>";
            }
            $page_errors .= $status;
  }

  function validate_sql($sql)
  {
  }

  //get default value of a form field
  function get_default_value($var)
  {
                      if(!empty($_POST[$var]))
                      {
      if(get_magic_quotes_gpc())
                                return stripslashes($_POST[$var]);
      else
                                return $_POST[$var];
                      }
                      else if(!empty($_SESSION[$var]))
                      {
      if(get_magic_quotes_gpc())
      {
                                return stripslashes($_SESSION[$var]);
            }
            else
                                return $_SESSION[$var];
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
<form action="<?php echo($_SERVER['PHP_SELF']);?>" method="post">
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
					<td height="18" colspan="2" class="step_title">Please enter SQL query</td>
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
								  <a href="" onMouseOver="stm(Step_3_sql[0],Style);" onClick="return false;" onMouseOut="htm()"></a>
								  <table border="0">
                                  <tr>
								  <?php
								  if(!empty($page_errors))
								  {
                                    echo "<td class='error'>* $page_errors</td>";
								  }
								  ?>
                                  </tr>
                                  <tr>
                                    <td><textarea name="sql" cols="50" rows="7" id="sql"><?php echo get_default_value("sql") ?></textarea></td>
                                  </tr>
                                  <tr>
                                    <td align="right"><a href="" onMouseOver="stm(Step_2[8],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.png" border="0"></a></td>
                                  </tr>
                                  <tr>
                                    <td><div align="center">
                                      <input name="btn_validate" type="image" id="btn_validate" src="layout/button_execute.gif" />
                                    </div></td>
                                  </tr>
                                </table>
                              <p></td>
								<td width="38" background="images/cright.jpg" style="background-repeat: y">&nbsp;</td>
							</tr>
							
							<tr>
								<td width="27" height="18">
								<img border="0" src="images/cdownleft.jpg" width="38" height="37"></td>
								<td width="425" height="18" background="images/cdown.jpg" style="background-repeat: x">								<div align="center"></div></td>
								<td width="38">
								<img border="0" src="images/cdownright.jpg" width="38" height="37"></td>
							</tr>
						</table>
					</div>				  </td>
				</tr>
				<tr>
					<td align="center"><a 
                  href="step_2.php" style="color: #0029a3; text-decoration: none"><img 
                  src="images/03.jpg" border=0 width="170" height="34"></a></td>
					<td align="center">
					<INPUT name=btn_continue type=image id="btn_cont" 
                  src="images/04.jpg" width="166" height="34"></td>
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
	</tr>
  </table>
</form>
</body>

</html>
