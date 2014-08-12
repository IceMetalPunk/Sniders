<?php
/**
    *   Smart Form Maker 
    *   V 1.0.0
    *   All copyrights are preserved to StarSoft
    */
session_start();
require_once '../shared.php';
require_once("lib.php");
 if(!array_key_exists('form_table',$_SESSION))
     Header("Location: ../index.php");
@$continue= $_POST["continue_x"];
@$back= $_POST["back_x"];

if(!empty($back))
{
    if($_SESSION['form_data_source'] == 'sql')
        header("location: step_2_sql.php");
    else {
        header("location: step_2.php");
    }
}
 if(!empty($continue))
 {
     $SelectedFields = $_POST["SelectedFields"];
     if (empty($SelectedFields))
     {
         $_SESSION["form_fields"] = array();
        $error .= "* At least One field should be selected" ;
      }
     else
     {
         $_SESSION["form_fields"]= $SelectedFields;
         //echo print_r($_SESSION["form_fields"]);
         header("location: step_4.php");
     }
 }
 $table = $_SESSION['form_table'];
 $fields = $_SESSION["form_fields"];
 $insert = substr($_SESSION['form_permission'], 0, 1) == '1'?true:false;
  
 if(empty ($table))
     header("location: step_2.php");
 //$_SESSION["form_fields"]
 $result = query('SHOW COLUMNS FROM `'.$table.'`');
 $DD_Selected = '';
 $DD_Avaliable = '';
 while ($field = mysql_fetch_assoc($result))
 {

     $insert_tmp = $insert == true?'1':'0';
     if(in_array($field['Field'], $fields) || (strtoupper($field['Null']) == 'NO' && $insert))
     {
        $color = (strtoupper($field['Null']) == 'NO' && $insert) ?'red':'black';
        $text =  (strtoupper($field['Null']) == 'NO' && $insert) ?$field['Field'].'(required)':$field['Field'];
        if($field['Extra'] == 'auto_increment')
            $DD_Selected .= '<option value="'.$field['Field'].'"  insert="'.$insert_tmp.'"  null="YES">'.$field['Field'].'(auto_increment)</option>';   
           else  
        $DD_Selected .= '<option value="'.$field['Field'].'" insert="'.$insert_tmp.'" style="color:'.$color.'" null="'.$field['Null'].'">'.$text.'</option>';
     }
     else
        $DD_Avaliable .= '<option value="'.$field['Field'].'" insert="'.$insert_tmp.'"  null="'.$field['Null'].'">'.$field['Field'].'</option>';   
   
 }

?>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Select Fields</title>
<link href="style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
<script type="text/javascript">
     function SelectAll()
     {
          $('#dd_selected').children().each(function(){
             $(this).attr('selected',true);
            });
     }
     function MoveAllLeft()
     {
         $('#dd_selected').children().each(function(){
             if(!($(this).attr('null') == 'NO' && $(this).attr('insert') == '1'))
                 $('#dd_av').append($(this))
         });
     }
     
     function MoveAllRight()
     {
         $('#dd_selected').append($('#dd_av').children());
         
     }
     function movel2R()
     {
         var field = $('#dd_av option:selected');
         var index = $('#dd_av option:selected').index();
         index = (index-1) <= 0 ? 1 : index;
         $('#dd_selected').append(field);
         $('#dd_selected').val(0);
         if($('#dd_av').children().length != 0)
            $('#dd_av option')[index - 1].selected = true;
         $('#dd_av').focus();
     }
     function moveR2l()
     {
         var field = $('#dd_selected option:selected');
         var index = $('#dd_selected option:selected').index();
         index = (index-1) <= 0 ? 1 : index;
         if(field.attr('null') == 'NO' && field.attr('insert') == '1')
         {
             alert('The Field '+field.text()+' is required in insert case.');
             return false;
         }
         $('#dd_av').append($('#dd_selected option:selected'));
         $('#dd_av').val(0);
         if($('#dd_selected').children().length != 0)
            $('#dd_selected option')[index - 1].selected = true;
         $('#dd_selected').focus();
     }
</script>
<SCRIPT language="JavaScript1.2" src="main.js" type="text/javascript"></SCRIPT>  
<body>
<DIV id="TipLayer" style="visibility:hidden;position:absolute;z-index:1000;top:-100;"></DIV>
<SCRIPT language="JavaScript1.2" src="style.js" type="text/javascript"></SCRIPT>           
<center>
<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?> ">
<table width="732"  height="473" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td align="center" width="64" height="20" background="images/topleft.jpg" style="background-repeat: no-repeat" >
      <td align="center" width="614" height="20" background="images/top.jpg" style="background-repeat: x">
      <td align="center" width="48" height="20" background="images/topright.jpg" style="background-repeat: no-repeat">    </tr>
	<tr>
		<td align="center" width="64" style="background-repeat: y" valign="top" background="images/leftadd.jpg">
           
            <img border="0" src="images/left.jpg"><td rowspan="2" align="center" valign="top" bgcolor="#FFFFFF" >
           
			<p><img border="0" src="images/logo.png" width="369" height="71"></p>
			<table border="0" width="100%" id="table8" height="331">
				<tr>
					<td height="18" colspan="2" class="step_title">Select Fields</td>
				</tr>
				<tr>
					<td colspan="2" height="263" valign="top">

		<table cellspacing="0" cellpadding="0" width="100%" border="0" align="center" id="table9">
			<tr>
				<td width="85%" height="261" valign="top" style="font-family: Verdana, Arial, sans-serif; font-size: 11px; line-height: 1.5">
				<div align="left" class='error'>
                <?php
                echo $error;
                ?>
                </center>					  <table width="501" height="242" border="0" align="center" cellpadding="0" cellspacing="0" id="table13">
						<tr>
							<td width="27" height="16">
						    <img border="0" src="images/ctopleft.jpg" width="38" height="37"></td>
							<td width="425" height="16" background="images/ctop.jpg" style="background-repeat: x">&nbsp;</td>
							<td width="38" height="16">
						    <img border="0" src="images/ctopright.jpg" width="38" height="37"></td>
						</tr>
						<tr>
							<td width="27" height="167" background="images/cleft.jpg" style="background-repeat: y">&nbsp;</td>
							<td width="85%" align="center" valign="top" bgcolor="#F9F9F9">
							  <table border="0" width="67%" id="table14">
							    <tr>
							      <td width="108" class="control_label">Available Fields</td>
								    <td width="63" style="font-family: Verdana, Arial, sans-serif; font-size: 11px; line-height: 1.5">&nbsp;</td>
								    <td width="109" class="control_label" >Selected Fields</td>
								  </tr>
							    <tr>
							      <td width="108" style="font-family: Verdana, Arial, sans-serif; font-size: 11px; line-height: 1.5">
							        <select id="dd_av" size="5" name="AllFields" style="width: 200 px ; Height: 100px;">
                                                                                    <?php echo $DD_Avaliable; ?>
                                                                                </select><p></td>
								    <td style="font-family: Verdana, Arial, sans-serif; font-size: 11px; line-height: 1.5" valign="top">
										    
									    <table border="0" width="100%" id="table15" cellspacing="0" cellpadding="0">
									      <tr>
									        <td><input name="B3" type="button" class="arrow_btn" onClick="movel2R();" value="  &gt;  "></td>
										    </tr>
									      <tr>
									        <td>
							                <input name="B4" type="button" class="arrow_btn" onClick="moveR2l()" value="  &lt;  "></td>
										    </tr>
									      <tr>
									        <td>
							                <input name="B5" type="button" class="arrow_btn" onClick="MoveAllRight()" value=" &gt;&gt; "></td>
										    </tr>
									      <tr>
									        <td>
							                <input name="B6" type="button" class="arrow_btn"onclick="MoveAllLeft()" value=" &lt;&lt; "></td>
										    </tr>
									      </table>
  
								    <p></td>
								    <td width="109" valign="top" style="font-family: Verdana, Arial, sans-serif; font-size: 11px; line-height: 1.5">
								    <select id="dd_selected" size="2" name="SelectedFields[]" ID="fields" style="width: 200 px ; Height: 100px;"multiple>
								       <?php echo $DD_Selected; ?>
                                                                                    </select>
                                                                                    </td>
								  </tr>
							    <tr>
							      <td colspan="3" align="right" style="font-family: Verdana, Arial, sans-serif; font-size: 11px; line-height: 1.5"><a href="" onMouseOver="stm(Step_3[0],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.png" border="0"></a></td>
						        </tr>
						        
						        


									<tr>
										<td>&nbsp;
										</td>
									</tr>
						        </table>
                                                            <br>
                                                            <b>Note:</b>: Red fields don't allow null so they must always be selected
                           
							  <p></td><td width="38" background="images/cright.jpg" style="background-repeat: y">&nbsp;</td>
						</tr>
						<tr>
							<td width="27" height="37">
						    <img border="0" src="images/cdownleft.jpg" width="38" height="37"></td>
							<td width="425" height="37" background="images/cdown.jpg" bgcolor="#FFFFFF" style="background-repeat: x">								</td>
							<td width="38">
						    <img border="0" src="images/cdownright.jpg" width="38" height="37"></td>
						</tr>
					    </table></td>
			</tr>
		</table>

				  </td>
				</tr>
				<tr>
					<td align="center">
					<INPUT name=back type=image i 
                  src="images/03.jpg" border=0 width="170" height="34" >
					</td>
					<td align="center">
					<INPUT name=continue type=image id="btn_cont" 
                  src="images/04.jpg" width="166" height="34" onClick="SelectAll()"></td>
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
	<td height="2"></tr>
  </table>
</form>
</body>

</html>
