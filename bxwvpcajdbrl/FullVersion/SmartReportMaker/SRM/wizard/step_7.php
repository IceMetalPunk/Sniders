<?php
session_start();
error_reporting(E_ERROR  | E_PARSE);
@$continue= $_POST["continue_x"];
 if(!empty($continue))
 {
   $_SESSION["layout"] = $_POST["option"];
  
   header("location: step_8.php");
 }
 
 $image = '';
 if(empty($_SESSION["layout"]))
    $image = 'layout_align_left2.gif';
 else
 {
     $layout = $_SESSION["layout"];
     if($layout == 'AlignLeft1')
         $image = 'layout_align_left1.gif';
     else if($layout == 'Mobile')
         $image = 'mob-layout.png';
     else if($layout == 'AlignLeft2')
         $image = 'layout_align_left2.gif';
     else if($layout == 'Stepped')
         $image = 'layout_stepped.gif';
     else if($layout == 'Block')
         $image = 'layout_block.gif';
     else if($layout == 'Outline1')
         $image = 'layout_outline1.gif';
     else if($layout == 'Outline2')
         $image = 'layout_outline2.gif';

 }
 
?>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>step 7</title>
<link href="style.css" rel="stylesheet" type="text/css">
</head>
<SCRIPT language="JavaScript1.2" src="main.js" type="text/javascript"></SCRIPT>  
<body>
<?php include("menu.html"); ?>
<DIV id="TipLayer" style="visibility:hidden;position:absolute;z-index:1000;top:-100;"></DIV>
<SCRIPT language="JavaScript1.2" src="style.js" type="text/javascript"></SCRIPT>           
<center>
<form action="<?php echo($_SERVER['PHP_SELF']); ?>" method="post">
<table border="0"  height="494" cellspacing="0" cellpadding="0" width="732">
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
					<td height="18" colspan="2" class="step_title">Please select the report layout</td>
				</tr>
				<tr>
					<td colspan="2" height="271" valign="top">					  <table width="501" height="311" border="0" align="center" cellpadding="0" cellspacing="0" id="table11">
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
					        <table width="394" border="0" align="center" id="table12">
					         
                                                              
                                                              
                                                      <tr>
					         <td class="control_label" style="font-family: Verdana, Arial, sans-serif; font-size: 11px; line-height: 1.5">
				                <input name="option" type="radio" value="Mobile"  <?php
                                                        if(@$_SESSION["layout"]=="Mobile") echo " checked='checked'";?>  onclick="document['img_layout'].src= 'images/mob-layout.png';" /> Mobile  <span style="color: red; font-size: 10px;">(new)*</span></td>

					              <td width="263" rowspan="7" style="font-family: Verdana, Arial, sans-serif; font-size: 11px; line-height: 1.5"><img src="images/<?php echo $image; ?>" width="231" height="208" name="img_layout" /></td>
                              </tr>
                                                              
                                                              
                                                              
                                                              <tr>
					         <td class="control_label" style="font-family: Verdana, Arial, sans-serif; font-size: 11px; line-height: 1.5">
				                <input name="option" type="radio" value="AlignLeft1"  <?php
                                if(@$_SESSION["layout"]=="AlignLeft1") echo " checked='checked'";?>  onclick="document['img_layout'].src= 'images/layout_align_left1.gif';" /> Align Left 1</td>

<!--					              <td width="263" rowspan="7" style="font-family: Verdana, Arial, sans-serif; font-size: 11px; line-height: 1.5"><img src="images/layout_align_left2.gif" width="231" height="208" name="img_layout" /></td>-->
                              </tr>
					          <tr>
					          
					          <td height="25" class="control_label" style="font-family: Verdana, Arial, sans-serif; font-size: 11px; line-height: 1.5">
					              <input name="option" type="radio" value="AlignLeft2" <?php
                                if(@$_SESSION["layout"]=="AlignLeft2"||!isset($_SESSION["layout"])) echo " checked='checked'";?> onClick="document['img_layout'].src= 'images/layout_align_left2.gif';" />
				                Align Left 2</td>

                              </tr>
					        
					        
					        
					        
					          <tr>
					            <td width="121" height="25" class="control_label" style="font-family: Verdana, Arial, sans-serif; font-size: 11px; line-height: 1.5">
				                <input name="option" value="Stepped" type="radio" onClick="document['img_layout'].src= 'images/layout_stepped.gif';"
								<?PHP
                                if(isset($_SESSION["layout"]))
                                {

								  if($_SESSION["layout"]=="Stepped")
								   {echo " checked='checked'";}
                                }
                                ?>/> Stepped</td>

                              </tr>
					          <tr>
					            <td class="control_label" style="font-family: Verdana, Arial, sans-serif; font-size: 11px; line-height: 1.5">
					              <input name="option" type="radio" value="Block"  <?php
                                if(@$_SESSION["layout"]=="Block") echo " checked='checked'";?> onClick="document['img_layout'].src= 'images/layout_block.gif';" />
					              Block</td>
                              </tr>

					          <tr>
					          
					          <td class="control_label" style="font-family: Verdana, Arial, sans-serif; font-size: 11px; line-height: 1.5">
					              <input name="option" type="radio" value="Outline1" <?php
                                if(@$_SESSION["layout"]=="Outline1") echo " checked='checked'"; ?> onClick="document['img_layout'].src= 'images/layout_outline1.gif';" />
					              Outline
					              1
                                                            
                                                            
                                                            
                                                             <div class="control_label" style="margin-top: 6px;font-family: Verdana, Arial, sans-serif; font-size: 11px; line-height: 1.5">
					              <input name="option" type="radio" value="Outline2" <?php
                                if(@$_SESSION["layout"]=="Outline2") echo " checked='checked'";?>  onclick="document['img_layout'].src= 'images/layout_outline2.gif';"/>
					              Outline 2</div>
                                                            </td>

                              </tr>
					         
					          <tr>
					            <td height="21" style="font-family: Verdana, Arial, sans-serif; font-size: 11px; line-height: 1.5">&nbsp;</td>
                              </tr>
					          <tr>
					            <td colspan="2" height="21" align="right"><a href="" onMouseOver="stm(Step_7[0],Style);" onClick="return false;" onMouseOut="htm()"><img src="images/Help.gif" width="20" height="15" border="0"></a></td>
						      </tr>
				            </table>			  		      </td>
						    <td width="38" background="images/cright.jpg" style="background-repeat: y">&nbsp;</td>
					    </tr>
					    <tr>
					      <td width="27" height="37">
					        <img border="0" src="images/cdownleft.jpg" width="38" height="37"></td>
						    <td width="425" height="37" background="images/cdown.jpg" style="background-repeat: x">								</td>
						    <td width="38">
					        <img border="0" src="images/cdownright.jpg" width="38" height="37"></td>
					    </tr>
				      </table></td></tr>
				<tr>
					<td align="center">
					<a style="color: #0029a3; text-decoration: none" href="step_6.php"><img 
                  src="images/03.jpg" border=0 width="170" height="34"></a></td>
					<td align="center">
					<INPUT name=continue type=image id="continue" 
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
