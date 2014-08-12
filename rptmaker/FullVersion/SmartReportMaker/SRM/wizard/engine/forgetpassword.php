<?php
session_start();
error_reporting(E_ERROR  | E_PARSE); 
require_once("config.php");
$err = "";
// username and password sent from form 



Function ForgetPassword($email)
{

   global $host, $user, $pass, $db, $sec_email,$sec_email_field,$sec_pass,$sec_table,$sec_pass_Field;
   
   //case static data
   
    	if($email==$sec_email)
    	{
    		
    		return $sec_pass;
    	}
		
	// Member case
		
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
    
	//case D
	 if(check($sec_table)&&check($sec_email_field)&&check($sec_pass_Field))
     {
	   $query = "select `$sec_pass_Field` from `$sec_table` where `$sec_email_field`="."'".mysql_real_escape_string($email)."'";
	   
     	if(!$result=@mysql_query($query))
     		{     			
     			return false;
     		}
     		elseif(mysql_num_rows($result)>0)
     		{
     			
     			$arr =  mysql_fetch_array($result);     			
     			//
				
     			return $arr[0];
     		}
			else
			{
			  return false;
			}

     }  
     return false;
}



if(isset($_POST["Submit"]))
  {	
  
   //$version = explode('.', PHP_VERSION);
  
   
   
		if(!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)||!clean($_POST['email']))
		{
		  $err = "<font color='red'>Please enter a valid email address</font>";
		  
		}
		else
		{
		  retrievePassword($_POST['email']);
		
		}
	}

 

	
	
function retrievePassword($email)
{
  global $err;
  $pass = ForgetPassword($email);

	if($pass != false)
		{ 
             
			$header = "From: Smart Report Maker<$email>\r\nReply-To:Smart Report Maker<$email>\r\n";
			$Mess = "Dear Sir or Madam,\r\nWe have received your request for password retrival\r\n
	               Your  password is: $pass \r\n Yours sincerely,\r\n Smart Report Maker";

	       @mail($email, "password retrival for Smart Report Maker" ,$Mess ,$header ) ; 
	       $err = "<font color='green'>Password has been sent to your email address</font>";

		}
	else
		{  		
			$err = "<font color='red'>'$email' does not match any existing accounts</font>";
		}


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

function clean($str){

 $str = strtolower($str);
 $attacks = array("'",'"',"$", "%","drop","insert","update","select","alter"," or "," and "," ",",","*","delete","truncate");
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



		 
							  <form name="form1" method="post" action="<?PHP echo $_SERVER["PHP_SELF"] ?>">


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

						<u><b><strong>Please enter your email address:   </b></u>

						<div align="center">

&nbsp;<table  width="434" id="table3"  height="31" >

							  
 

<tr>
<td>Email</td>
<td>:</td>
<td><input name="email" type="text" id="email"></td>

</tr>

<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td><input type="submit" name="Submit" value="Send"></td>
</tr> 
<tr>
<td colspan="3">

<?php 

  echo "<center>$err</center>" ;

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

 