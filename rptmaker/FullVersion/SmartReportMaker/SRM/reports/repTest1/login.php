<?php
session_start();
error_reporting(E_ERROR  | E_PARSE); 
require_once("config.php");
$valid = 1;
// username and password sent from form 


function ValidatUser($myuser,$mypass)
{
  global $host, $user, $pass, $db, $sec_Username,$sec_pass,$sec_table,$sec_Username_Field,$sec_pass_Field;
  
  if(get_magic_quotes_gpc())
    {
    	$myuser = stripslashes($myuser);
		$mypass = stripslashes($mypass);
    }
	
	 
	//Report Admin password
     if(check($sec_Username)&&check($sec_pass))
     {
     	if($myuser==$sec_Username&&$mypass==$sec_pass) 
     		{return True;}
     }

     if(check($sec_table)&&check($sec_Username_Field)&&check($sec_pass_Field)) 

     {  
	 	 
		   //handling Member password
			$passwords = array();		
			$passwords[] = $mypass;
			
			//hashing functions
			//****************************************************//
			// To add any other hashing function , it will be like : 
			// $passwords[] = your_hashing_function($passwords);
			//or if your hashing functions requires  other parameters
			//$passwords[] = your_hashing_function($passwords,parameter(s)); 
			//The following line is hashing by MD5
			//*******************************************************8
			
			$passwords[] = md5($mypass); //hashed by md5		
			

		if(!@mysql_connect($host, $user, $pass))
		{   
			echo("<center><B>Couldn't connect to MySQL</B></center>");
			return false;
		}
		if(!@mysql_select_db($db))
		{ 
		 
			echo("<center><B>Couldn't select database</B></center>");
			return false;
		}
		$query = "select  `$sec_pass_Field` from `$sec_table` where `$sec_Username_Field`="."'".mysql_real_escape_string($myuser)."'";
		
		if(!$result = @mysql_query($query))
		{
					return false;
		}
		elseif(mysql_num_rows($result)>0)
		 {
					while($row = mysql_fetch_array($result))
					{
					   if(in_array($row[0],$passwords)){
					  
					   return true;
					   }
					   else
					   return false;
					
					}
		 }
	 
	}
	   
	return false;
}

function check($var)
{
	if(isset($var))
	{
		if(!empty($var)) return true;
		else
			return false;
	}
    else
    {
    	return false;
    }


}



if(isset($_POST["Submit"]))
  {	

 

		$myusername= $_POST['myusername']; 
		$mypassword=$_POST['mypassword']; 
 
 if(clean($myusername)&&!strstr($mypassword," "))
		if(ValidatUser($myusername,$mypassword))
		{ 
		  $_SESSION[$file_name]	= $myusername;
		  header("location: rep".$file_name.".php"); 
		}
		else
		{   
			$valid = 0;
			$err = "<font color='red'> Incorrect username or password </font>";
		}
	else
	{
	        $valid = 0;
			$err = "<font color='red'> User name or password is not secure, please contact the admin </font>";
	}


 } 
 
 function clean($str){

 $str = strtolower($str);
 $attacks = array("'",'"',"$", "%","drop","insert","update","select","alter"," or "," and "," ",",","*","delete",">","<","&", "|");
	 foreach($attacks as $attack)
	 {
	   if(strstr($str,$attack))
	   return false;
	   
	 }
	 
   return true;
 
 }


?>

  









 


<html>



<head>

<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">

<title>Select table</title>

<link href="medi2.css" rel="stylesheet" type="text/css">

 

</head>



<body>

<center>

<table border="0"  height="477" cellspacing="0" cellpadding="0" width="738">

	<tr>

		<td align="center" width="55" height="20" background="../../wizard/images/topleft.jpg" style="background-repeat: no-repeat" >



            <td align="center" width="629" height="20" background="../../wizard/images/top.jpg" style="background-repeat: x">



            <td align="center" width="54" height="20" background="../../wizard/images/topright.jpg" style="background-repeat: no-repeat">



            <img border="0" src="../../wizard/images/topright.jpg" width="51" height="23"></tr>

	<tr>

		<td align="center" width="55" background="../../wizard/images/leftadd.jpg" style="background-repeat: y" valign="top">



            <img border="0" src="../../wizard/images/left.jpg" width="64" height="403"><td align="center" rowspan="2" >



			<p><img border="0" src="../../wizard/images/01.jpg" width="369" height="71"></p>

			<p>

			&nbsp;&nbsp;&nbsp;



		 
							  <form name="form1" method="post" action="login.php">


				<table border="0" cellpadding="0" cellspacing="0" width="501" id="table1" height="178">

					<tr>

						<td width="27" height="16">

						<img border="0" src="../../wizard/images/ctopleft.jpg" width="38" height="37"></td>

						<td width="425" height="16" background="../../wizard/images/ctop.jpg" style="background-repeat: x"></td>

						<td width="38" height="16">

						<img border="0" src="../../wizard/images/ctopright.jpg" width="38" height="37"></td>

					</tr>

					<tr>

						<td width="27" height="104" background="../../wizard/images/cleft.jpg" style="background-repeat: y">&nbsp;</td>

						<td width="425" valign="top" bgcolor="#F9F9F9">

						<u><b><strong>Member Login </strong></b></u>

						<div align="center">

&nbsp;<table  width="434" id="table3"  height="31" >

							  
 
<tr>
<td width="78">Username</td>
<td width="6">:</td>
<td width="294"><input name="myusername" type="text" id="myusername"></td>
</tr>
<tr>
<td>Password</td>
<td>:</td>
<td><input name="mypassword" type="password" id="mypassword"></td>

</tr>
<?php
if(isset($Forget_password))
{
	if($Forget_password=="enabled")
	{
	echo "<tr> <td colspan='3'><font color='blue'><u><a href='forgetpassword.php'>Forgot password</a></u></font></td> 
	</tr>";
   }
}
?>
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td><input type="submit" name="Submit" value="Login"></td>
</tr> 
<tr>
<td colspan="3">

<?php 
if( $valid==0)
{
  echo $err;
}
?>
</td>
</tr>


						  </table> 

						</div>					  </td>

						<td width="38" background="../../wizard/images/cright.jpg" style="background-repeat: y">&nbsp;</td>

					</tr>

					<tr>

						<td width="27" height="18">

						<img border="0" src="../../wizard/images/cdownleft.jpg" width="38" height="37"></td>

						<td width="425" height="18" background="../../wizard/images/cdown.jpg" style="background-repeat: x"></td>

						<td width="38">

						<img border="0" src="../../wizard/images/cdownright.jpg" width="38" height="37"></td>

					</tr>

			    </table>

				<table border="0" cellpadding="0" cellspacing="0" width="100%" id="table2">

				<tr>

					<td align="center">

					<p align="center">

					</td>

					<td align="center">

					<p align="center">

					</td>

				</tr>

			</table>

			</form>

			<td  align="center" width="54" background="../../wizard/images/rightadd.jpg" style="background-repeat: y" valign="top" height="388">



            <img border="0" src="../../wizard/images/right.jpg"></tr>

	<tr>

		<td align="center" width="55" background="../../wizard/images/leftadd.jpg" style="background-repeat: y">



            <td  align="center" width="54" background="../../wizard/images/rightadd.jpg" style="background-repeat: y" valign="top">



            </tr>

	</tr>

	<tr>

		<td align="center" width="55" height="29" background="../../wizard/images/downleft.jpg" style="background-repeat: no-repeat">



            <img border="0" src="../../wizard/images/downleft.jpg"><td align="center" width="629" height="29" background="../../wizard/images/down.jpg" style="background-repeat: x">



            <td align="center" width="54" height="29" background="downright.jpg" style="background-repeat: no-repeat" >



            <img border="0" src="../../wizard/images/downright.jpg" width="52" height="30"></tr>

	</tr>

</body>



</html>

 