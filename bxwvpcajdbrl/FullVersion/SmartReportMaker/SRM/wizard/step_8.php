<?php
session_start();
//print_r($_SESSION);

//print_r($_POST);
 error_reporting(E_ERROR  | E_PARSE);
 require_once("lib.php");

 //Get Post data

if(isset($_POST['style_name'])) $style_name = $_POST['style_name'];
if(isset($_POST['btn_continue_x'])) $btn_continue = $_POST['btn_continue_x'];

if(isset($_POST['btn_back_x'])) $btn_back = $_POST['btn_back_x'];

if(isset($_POST['css'])) $css = $_POST['css'];

if(isset($_POST['btn_create_new_style_x'])) $btn_create_new_style = $_POST['btn_create_new_style_x'];
require_once("lib.php");

//functions

 



//other vars

$page_errors=array();

//post back code

if(!empty($btn_continue))

{	
	SaveState();
    validate();

   if(empty($page_errors))
   {
   	 
   	 header('location: step_9.php');
   } 
}
else if(!empty($btn_create_new_style))
{

	if(!empty($style_name)) $_SESSION['style_name'] = $style_name ;
	header("location: create_new_style.php");

}

//back button

else if(!empty($btn_back))

{

	header("location: step_7.php");

}



//saving and redirect

function validate()
{
	global $_POST,$style_name,$page_errors;

	if(empty($style_name))

	{

		$page_errors[] = 'Please select style name';
		

	}
	if($_SESSION["layout"]=='Mobile'){
	  if(adjust($_POST["security"])=="enabled"||adjust($_POST["Forget_password"])=="enabled"||adjust($_POST["members"])=="enabled")
	  $page_errors[] = "Security Options, Forget password and Members login are not supported for the mobile layout ";
	}
	else
	{

		//security enabled and user name and password is empty
		if(adjust($_POST["security"])=="enabled")
		{
			if(!CheckVar($_POST["sec_Username"])|| !CheckVar($_POST["sec_pass"]))
			{
				$page_errors[] = "Admin Username or password is empty";
			}
			// username  shouldn't have  attackers
			if(!clean($_POST["sec_Username"]))
			{
			  $page_errors[] = "Admin Username should not include any special characters or sql commands for security reasons";
			
			}
			if(strstr($_POST["sec_pass"]," "))
			{
			
			//password shouldn't include empty
			$page_errors[] = "Admin Password should not include any spaces  for security reasons";
			}			
		}
		
		
		// email must be in valid email formats
		
		
		

		

		//forget password is enabled and email is empty 
		if(adjust($_POST["Forget_password"])=="enabled")

		{
			if(!CheckVar($_POST["sec_email"]))
			{
			  $page_errors[] = "Admin Email is empty";
			}
			
			if(!clean($_POST["sec_email"])||!strstr($_POST["sec_email"],"@")||!strstr($_POST["sec_email"],"."))
			{
			  $page_errors[] = "Admin email address is not valid";
			
			}

			if(adjust($_POST["security"])!="enabled") 
				{$page_errors[] = "To enable the Forget Password, you must enable the security options first";}

		}

		//forget password is enabled and members is enabled and member admin is missing
		if(adjust($_POST["Forget_password"])=="enabled" && adjust($_POST["members"])=="enabled")
		{
			if(!CheckVar($_POST["sec_email_field"])){

			$page_errors[] = "Members Email Field is empty";

			}

		}

		//members is enabled yet table, user name , password is missing 
		if(adjust($_POST["members"])=="enabled")


		{
			if(adjust($_POST["security"])!="enabled"){

				$page_errors[] = "To enable the members login, you must enable the security options first";
			}


			if(!CheckVar($_POST["sec_table"]) || !CheckVar($_POST["sec_Username_Field"]) || !CheckVar($_POST["sec_pass_Field"])){
			  $page_errors[] = "Members Table, Username field or password field is empty";
			}
		}
	}
}

function CheckVar($str)
{
	if(isset($str))
	{
		if(empty($str)||$str=="NoValue"||$str=="Please select a value")
		{
			
			return false;
		}
		else
		{
			
			return true;
		}

	}
	else 
	{
		
		return false;
	}
}

function SaveState()
{
	//save css style name into the session
        global $_SESSION, $_POST,$style_name;

		$_SESSION['style_name'] = $style_name;
		save_css_changes();

        $_SESSION["Forget_password"] = adjust($_POST["Forget_password"]);
		$_SESSION["security"] = adjust($_POST["security"]);
		$_SESSION["members"] = adjust($_POST["members"]);     
         
         if($_SESSION["security"] == "enabled")
         {
		$_SESSION["sec_Username"] = $_POST["sec_Username"];
        $_SESSION["sec_pass"] = $_POST["sec_pass"];
        }
        else
        {
        	$_SESSION["sec_Username"] = "";
            $_SESSION["sec_pass"] = "";
        }

        if($_SESSION["members"] == "enabled")
        {
        $_SESSION["sec_table"] = $_POST["sec_table"];
        $_SESSION["sec_Username_Field"] = $_POST["sec_Username_Field"];
        $_SESSION["sec_pass_Field"] = $_POST["sec_pass_Field"];
        }
        else
        {
        	$_SESSION["sec_table"] = "";
            $_SESSION["sec_Username_Field"] = "";
            $_SESSION["sec_pass_Field"] = "";
        }

        if($_SESSION["Forget_password"]== "enabled")
        {
        	 $_SESSION["sec_email"] = $_POST["sec_email"];        
		     
        }
        else
        	 {
        	 $_SESSION["sec_email"] = "";        
		     
        }		

        if($_SESSION["Forget_password"]== "enabled"&&$_SESSION["members"] == "enabled")
        {
        	$_SESSION["sec_email_field"] = $_POST["sec_email_field"];
        }
        else
        {
        	$_SESSION["sec_email_field"] = "";
        }
		

			//redirect

			

		
}

function adjust($string)
{   
	if(isset($string))
	{
		if(empty($string)){

			return"";
		}
		elseif($string=="1"||$string=="checked"||$string="on"||$string="On"){
			return "enabled";
		}
		else
		{
			return "";
		}
	}
    else
    {
    	return "";
    }

	
}

//create new style
function display_options($string)
{
	global $_SESSION ;
	echo "<option value='NoValue'> Please select a value </option>";

	if($string=="sec_table")
	{
		$mydb = $_SESSION["db"];
        $result = sql("show tables from `$mydb`");
            
			while($raw = mysql_fetch_array($result) )
			{  
				if ($raw[0] == $_SESSION["sec_table"]) {$selected = "selected";}
			    echo "<option $selected value='".$raw[0]."'>". $raw[0] . "</option>" ;
			    $selected = "" ;
			}
	}

	elseif(CheckVar($_SESSION["sec_table"]) && $string=="sec_Username_Field" && CheckVar($_SESSION["sec_Username_Field"]))
	 {
		LoadFields($_SESSION["sec_table"],$_SESSION["sec_Username_Field"]);
	}
	elseif(CheckVar($_SESSION["sec_table"]) && $string=="sec_pass_Field" && CheckVar($_SESSION["sec_pass_Field"]))
	{
		LoadFields($_SESSION["sec_table"],$_SESSION["sec_pass_Field"]);
	} 
	elseif(CheckVar($_SESSION["sec_table"]) && $string=="sec_email_field" && CheckVar($_SESSION["sec_email_field"]))
	{ 
				LoadFields($_SESSION["sec_table"],$_SESSION["sec_email_field"]);

     }
	
	
}

function LoadFields($table,$selectedField)
{
	$result = sql("show columns from `$table`");
	
	while ($f = mysql_fetch_array($result))
	{
		if($f[0]==$selectedField) {$selected = "selected";}
		echo "<option $selected value='".$f[0]."'>". $f[0] . "</option>" ;
		$selected = "" ;
		
	}
	
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
                        
                          if(($_SESSION['layout'] == 'Mobile' && $entry != 'mobile.css' )
                                  || ($_SESSION['layout'] != 'Mobile' && $entry == 'mobile.css')
                                  )
                            {
                                 continue;
                            }
        

			$formatted_css_name = substr( $entry,0,strlen($entry)-4) ;

			if($i==0 &&empty($style_name))

			{

				$style_name  = $formatted_css_name;

			}

		

			if($style_name == $formatted_css_name || ($style_name == ''))

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

if($_SESSION['layout'] == 'Mobile')
    $style_name = 'mobile';

if($_SESSION['layout'] != 'Mobile' && $style_name == 'mobile')
    $style_name = 'GreyScale';

$handle = fopen('styles/'.$style_name . ".css", "r");
if($handle)
{
while (!feof($handle)) {

    $buffer = fgets($handle, 4096);

    echo $buffer;

}

fclose($handle);		
}
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

		if(!empty($_POST[$var]))

		{

			return $_POST[$var];

		}

		else if(!empty($_SESSION[$var]))

		{

			return $_SESSION[$var];

		}
                    else
                        return 'GreyScale';
                    

}


function clean($str){

 $str = strtolower($str);
 $attacks = array("'",'"',"$", "%","drop","insert","update","select","alter"," or "," and "," ",",","*","delete");
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
	<meta charset="utf-8" />
  <title>Step8 General Options</title>
  <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/sunny/jquery-ui.css" />
  <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
  
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Select table</title>
<link href="style.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
$(function() {
    $( "#accordion" ).accordion();
  });

    function refresh()
    {
        document.getElementById('btn_continue').disabled = 'disabled';
        myform.submit();
    }
</script>
</head>
<SCRIPT language="JavaScript1.2" src="main.js" type="text/javascript"></SCRIPT>  


<body>

	<form name="myform" action="<?php echo($_SERVER['PHP_SELF']); ?>" method="post">

    
<DIV id="TipLayer" style="visibility:hidden;position:absolute;z-index:1000;top:-100;"></DIV>
<SCRIPT language="JavaScript1.2" src="style.js" type="text/javascript"></SCRIPT>           
<center>

<table width="732"  height="483" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td align="center" width="64" height="20" background="images/topleft.jpg" style="background-repeat: no-repeat" >
           
      <td align="center" width="614" height="20" background="images/top.jpg" style="background-repeat: x">
           
      <td align="center" width="48" height="20" background="images/topright.jpg" style="background-repeat: no-repeat">
           
    </tr>
	<tr>
		<td align="center" width="64" style="background-repeat: y" valign="top" background="images/leftadd.jpg">
           
            <img border="0" src="images/left.jpg"><td rowspan="2" align="center" valign="top" >
           
			<p><img border="0" src="images/01.jpg" width="369" height="71"></p>



			<table width="100%" height="333" border="0" align="center" id="table8">
				<tr>
					<td colspan="2" height="18"><strong>General Options     </strong></td>
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
					      <td width="425" bgcolor="#F9F9F9" align="center">


                 <div id="accordion">

                               <h3>Report Style</h3>

                        <div>


					      	<table width="449" height="126" border="0" align="center">
					        
						        <?php
										if(!empty($page_errors))
										{ 
											foreach($page_errors as $err)
											echo "<tr><td align='left' colspan='3' height='26' valign='top' class='error'>**$err</td></tr>";
										}
									?>                                 
				            
					        <tr>
					          <td width="49" height="31"><strong>Style</strong></td>
                              <td><select name="style_name" id="style_name" onChange="refresh()">
                                  <?php print_styles_names(); ?>
                                </select>
                                <a href="" onMouseOver="stm(Step_7[0],Style);" onClick="return false;" onMouseOut="htm()"></a> <a href="" onMouseOver="stm(Step_8[0],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.gif" width="20" height="15" border="0"></a></td>
                              
                              <td align="right"><input name="btn_create_new_style" type="image" id="btn_create_new_style" src="layout/button_create_new_style.gif" width="116" height="23" border="0">
                              <a href="" onMouseOver="stm(Step_8[1],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.gif" width="20" height="15" border="0" align="absmiddle"></a></td>
                            </tr>
                            <tr>
					          <td>&nbsp;</td>
                              <td colspan="2"><textarea name="css" cols="50" rows="10" id="css"><?php print_css_content();?> </textarea></td>
                              <td><a href="" onMouseOver="stm(Step_8[2],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.gif" width="20" height="15" border="0" align="absmiddle"></a></td>
					        </tr>
					        
				          </table>


                            </div>
                             <h3>Security Options</h3>
  <div>
    
    <Table id="securityTable" width="100%" height="50%" align="left">
      <tr>
	        <td colspan="3">
	        <input type="checkbox"  id="security" name ="security" class="security" <?php if($_SESSION["security"]=="enabled") echo "checked"; ?> />
	        Password protect generated report
	        <td/>
      </tr>
      <tr>
	        <td>
	        Admin User Name 
	      </td>
	      <td> : </td>
	      <td>
	        <input type="text" data-disable-controller="security" value="<?php  echo $_SESSION["sec_Username"]; ?>" name="sec_Username" id="sec_Username" ?>
	        </td>
      </tr>
      <tr>

	        <td>
	        Admin Password 
	      </td>
	      <td>:</td>
	      <td>
	        <input type="password" data-disable-controller="security" value="<?php  echo $_SESSION["sec_pass"]; ?>" name="sec_pass" id="sec_pass" ?>
	      </td>
        

      </tr>



    </table>    
  </div>

  <h3>Members Login</h3>
  <div>
    <Table id="MembersTable" width="100%" height="50%" align="left">
      <tr>
	        <td colspan="3">
	        <input type="checkbox" id="members" name="members" data-disable-controller="security" class="security" <?php if($_SESSION["members"]=="enabled") echo "checked"; ?> />
	        Allow members to login to the generated report  
	        <td/>
      </tr>
      <tr>
		        <td>
		          Members Table
		      </td>
		      <td> : </td>
		      <td>
		       <select data-disable-controller="members" id="sec_table" name="sec_table" >
		          <?php display_options("sec_table"); ?>
		       </select>
		      </td>
      </tr>
      <tr>
	        <td>
	         UserName Field
	      </td>
	      <td>:</td>
	      <td>
	       <select data-disable-controller="members"  id="sec_Username_Field"  name="sec_Username_Field">
	          <?php display_options("sec_Username_Field"); ?>
	       </select>
	      </td>
        

      </tr>
      <tr>
		        <td>
		         Password Field
		      </td>
		      <td>:</td>
		      <td>
		       <select id="sec_pass_Field" name="sec_pass_Field" data-disable-controller="members">
		          <?php display_options("sec_pass_Field"); ?>
		       </select>
		      </td>
		        

      </tr>
	  <tr>
		        <td colspan=3>
		         <span id="note"><font color="red"><center>** To Enable Members login please enable the security options first .   </center> </font>  <span>
		      </td>
		      
		        

      </tr>

      
    </table>
  </div>

  <h3>Forget Password</h3>
  <div>
    <Table id="ForgetPasswordTable" width="100%" height="50%" align="left">
      <tr>
	        <td colspan="3">
	        <input type="checkbox" id="Forget_password"  data-disable-controller="security"  name="Forget_password" class="security" <?php if($_SESSION["Forget_password"]=="enabled") echo "checked"; ?> />
	        Allow password retrival via email
	        <td/>
      </tr>
      <tr>
	        <td>
	        Admin Email 
	      </td>
	      <td> : </td>
	      <td>
	        <input type="text" data-disable-controller="Forget_password" value="<?php  echo $_SESSION["sec_email"]; ?>" id="sec_email" name ="sec_email"  ?>
	        </td>
      </tr>
      <tr>
	        <td>
	        Members Email Field
	      </td>
	      <td>:</td>
	      <td>
	       <select id="sec_email_field" name="sec_email_field" >
	          <?php display_options("sec_email_field"); ?>
	       </select>
	      </td>
	        

      </tr>

    </table>
  </div>
</div>




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
                  href="step_7.php" style="color: #0029a3; text-decoration: none"><img 
                  src="images/03.jpg" border=0 width="170" height="34"></a></td>
					<td align="center"><INPUT name=btn_continue type=image id="btn_continue" 
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

<script type="text/javascript">

function Set_Members_ForgetPassword (){
           	 if($("#members:checkbox").get(0).checked && $("#Forget_password:checkbox").get(0).checked) {
           	 	$("#sec_email_field").attr("disabled", false);
           	 }
           	 else {
           	 	$("#sec_email_field").attr("disabled", true);
           	 }
           }
	$(document).ready(function() {

	   
		// $("#security:checkbox").bind("click", function(){
		// 	$("#sec_Username").attr("disabled", !($(this).get(0).checked));
		// });

	   Set_Members_ForgetPassword ();
	   if($("#security:checkbox").get(0).checked )
	    {
				    $("#note").hide();				   
					
	    }

       $("#sec_table").bind("change",function(){
            var  selectedIndex = $(this).get(0).selectedIndex;
        	var tableName = $(this).children(":eq(" + selectedIndex + ")").text();
	        $.getJSON("getfields.php?table="+  tableName,function(json)
	        {
	        	var obj = eval(json);
	        	$("#sec_Username_Field").empty();
	        	$("#sec_pass_Field").empty();
	        	$("#sec_email_field").empty();
	        	$("#sec_Username_Field").append("<option selected  value='NoValue'> Please select a value </option>");
	        		$("#sec_pass_Field").append("<option selected  value='NoValue'> Please select a value </option>");
	        		$("#sec_email_field").append("<option selected value='NoValue'> Please select a value </option>");
	        	for (var i = 0; i < obj.length; i++) {
	        		$("#sec_Username_Field").append("<option value='"+obj[i]+"''>"+ obj[i] +"</option>");
	        		$("#sec_pass_Field").append("<option value='"+obj[i]+"''>"+ obj[i] +"</option>");
	        		$("#sec_email_field").append("<option value='"+obj[i]+"''>"+ obj[i] +"</option>");
	        	};
	        });

       });



		$('[data-disable-controller]').each(function(){
				var controllerId = $(this).data("disableController");
				var element = $(this);
                //intial value
				$(this).attr("disabled", !($("#" + controllerId).get(0).checked));
				//disable and clear text
				
 
				$("#" + controllerId).click(function(){
					element.attr("disabled", !($(this).get(0).checked));
					if(!$(this).get(0).checked)
					{element.val("");}
				if(controllerId=="members" || controllerId== "Forget_password")
				{
						if($("#members:checkbox").get(0).checked && $("#Forget_password:checkbox").get(0).checked)
						 {
		           	     	$("#sec_email_field").attr("disabled", false);
		           	     }
		           	 else
		           	    {
		           	 	    $("#sec_email_field").attr("disabled", true);
		           	     }


				}
				
				if(controllerId =="security")
				{
				  if($("#security:checkbox").get(0).checked )
				  {
				    $("#note").hide();				   
					
				  }
				  else
				  {
				      
				     $("#sec_table").val('NoValue');
					 $("#sec_Username_Field").val('NoValue');
					 $("#sec_pass_Field").val('NoValue');
					 $("#sec_email_field").val('NoValue');
					 
					 $("#sec_Username_Field").attr("disabled", true);
					$("#sec_table").attr("disabled", true);
					$("#sec_pass_Field").attr("disabled", true);
				    $("#sec_email_field").attr("disabled", true);
					$("#sec_email").attr("disabled", true);
				     $("#note").show();
				  }
				
				
				}
				});
		});

           

          
	   

  });
</script>
</body>
</html>
