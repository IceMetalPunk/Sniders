<?php
/*
    * Smart Form Maker 
    * V 1.0.0
    * All copyrights are preserved to StarSoft
*/
session_start();
require_once '../shared.php';
require_once("lib.php");
 if(!array_key_exists('form_table',$_SESSION))
     Header("Location: ../index.php");

$table = $_SESSION['form_details_table'];
$column = $_SESSION['form_details_column'];
$autodetect = $_SESSION['form_autodetect'];
//$SQL_IN_FORMAT_FIELDS = '"'.str_replace(',', '","', implode($fields, ',')).'"';

$desc = $_SESSION['form_desc_details'];

$temp_desc = $desc;
$desc = array();

@$result = query('SHOW COLUMNS FROM `'.$table.'` WHERE Field not IN("'.$column.'")');// WHERE Field IN('.$SQL_IN_FORMAT_FIELDS.')');

while($row = mysql_fetch_assoc($result))
{
    
    $desc[$row['Field']]['Type'] = $row['Type'];
    
    //Null
    if(strtoupper($row['Null']) == 'NO')
    {
        $desc[$row['Field']]['Null'] = 0;
    }
    else
    {
        if(array_key_exists($row['Field'], $temp_desc))
            $desc[$row['Field']]['Null'] = $temp_desc[$row['Field']]['Null'];
        else
            $desc[$row['Field']]['Null'] = 2;
    }
    $desc[$row['Field']]['Key'] = $row['Key'];
    $desc[$row['Field']]['Default'] = $row['Default'];
    $desc[$row['Field']]['Extra'] = $row['Extra'];
    
    //label
    if(empty($temp_desc))
    {
        
        $desc[$row['Field']]['Label'] = $row['Field'];
    }
    else
    {

        if(array_key_exists($row['Field'], $temp_desc))
             $desc[$row['Field']]['Label'] = $temp_desc[$row['Field']]['Label'];
        else
            $desc[$row['Field']]['Label'] = $row['Field'];
    }
    
   
    //load default validation
    if(array_key_exists('validation', $temp_desc[$row['Field']]))
    {
        $desc[$row['Field']]['validation']['msg'] = $temp_desc[$row['Field']]['validation']['msg'];
        $desc[$row['Field']]['validation']['regx'] = $temp_desc[$row['Field']]['validation']['regx'];
        $desc[$row['Field']]['validation']['special_char'] = $temp_desc[$row['Field']]['validation']['special_char'];
        $desc[$row['Field']]['validation']['from'] = $temp_desc[$row['Field']]['validation']['from'];
        $desc[$row['Field']]['validation']['to'] = $temp_desc[$row['Field']]['validation']['to'];
        $desc[$row['Field']]['validation']['regx_type'] = $temp_desc[$row['Field']]['validation']['regx_type'];
    }
    else
    {
        $desc[$row['Field']]['validation']['msg'] = 'Invalid data entered in column '.$row['Field'];
        $desc[$row['Field']]['validation']['regx'] = '';
        $desc[$row['Field']]['validation']['special_char'] = '';
        $desc[$row['Field']]['validation']['from'] = '';
        $desc[$row['Field']]['validation']['to'] = '';
        $desc[$row['Field']]['validation']['regx_type'] = '';
    }
      
    
   
    
    //refence
    if(!$autodetect)
    {
        if(array_key_exists('REFERENCED_TABLE_NAME', $temp_desc[$row['Field']]))
            $desc[$row['Field']]['REFERENCED_TABLE_NAME'] = $temp_desc[$row['Field']]['REFERENCED_TABLE_NAME'];

        
        if(array_key_exists('REFERENCED_COLUMN_NAME', $temp_desc[$row['Field']]))
            $desc[$row['Field']]['REFERENCED_COLUMN_NAME'] = $temp_desc[$row['Field']]['REFERENCED_COLUMN_NAME'];

        
        if(array_key_exists('TextField', $temp_desc[$row['Field']]))
            $desc[$row['Field']]['TextField'] = $temp_desc[$row['Field']]['TextField'];

    }
    
    
    //     //text field
//    if(!$autodetect)
//    {
//        if(array_key_exists($row['Field'], $temp_desc))
//                 $desc[$row['Field']]['TextField'] = $temp_desc[$row['Field']]['TextField'];
//        else
//                $desc[$row['Field']]['TextField'] = NULL;
//    }
// 
}

if($autodetect)
{
    @$result = query('select CONSTRAINT_NAME,TABLE_NAME,COLUMN_NAME,REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME
                    from
                        information_schema.key_column_usage
                    where
                        referenced_table_name is not null and COLUMN_NAME IN('.$SQL_IN_FORMAT_FIELDS.') and TABLE_NAME = "'.$table.'"');
    while($row = mysql_fetch_assoc($result))
    {

        $desc[$row['COLUMN_NAME']]['REFERENCED_TABLE_NAME'] = $row['REFERENCED_TABLE_NAME'];
        $desc[$row['COLUMN_NAME']]['REFERENCED_COLUMN_NAME'] = $row['REFERENCED_COLUMN_NAME'];
    }
}
$_SESSION['form_desc_details'] = $desc;
//echo print_r($desc);



@$continue= $_POST["continue_x"];
 if(!empty($continue))
 {
    $valid = true;
    foreach($desc as $key=>$val)
    {
       
     if(!isset ($_POST['lbl_'.str_replace(' ', '_', $key)]) || trim($_POST['lbl_'.str_replace(' ', '_', $key)]) == '')
     {
          $error .= "<br />* Label name for field <strong>$key</strong> is empty.";
          $valid = false;
     }
     
       if($val['Null'] != 0)
       {
           
           if(isset($_POST['null_'.str_replace(' ', '_', $key)]))
            $desc[$key]['Null'] = 2;
           else
            $desc[$key]['Null'] = 1;

       }
       
       $desc[$key]['Label'] = $_POST['lbl_'.  str_replace(' ', '_', $key)];
       
       //save text field
       if(array_key_exists('REFERENCED_TABLE_NAME', $val) && $autodetect)
       {
           if($_POST['txt_field_'.str_replace(' ', '_', $key)] == '0')
           {
                $error .= "<br />* Please select text field for column <strong>$key</strong>.";
                $valid = false;
           }
          $desc[$key]['TextField'] = $_POST['txt_field_'.str_replace(' ', '_', $key)];
       }
    }
    $_SESSION['form_desc_details'] = $desc;
  
    if($valid)
    {
        header("location: step_6.php");
    }
 }
?>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Settings</title>
<link href="style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>

<SCRIPT language="JavaScript1.2" src="main.js" type="text/javascript"></SCRIPT>
<style type="text/css">
    .tbl_alias
    {
        font-size: 11px;
        border:1px solid #e3e3e3;
        border-collapse: collapse;
        text-align: center;
    }
    .tbl_alias tbody tr  td{border: 1px solid #e3e3e3;}
    .tbl_alias tbody tr th,.tbl_alias tbody tr td{padding: 3px;}
    .tbl_alias thead tr.header{background: #FDC643; height: 31px;}
    .tbl_alias tbody tr:hover{
        background: #e3e3e3;
    }
    #pop_close,#pop_close2
    {
        display: block;
        width: 25px;
        height: 25px;
        background: url(images/popup-close.png) no-repeat 0px -28px;
        position: relative;
        top: -14px;
        left: 365px;
    }
    #pop_close,#pop_close2:hover
    {
        background: url(images/popup-close.png) no-repeat;
    }
    #pop_container{display: none;margin:0px;top:0px; left: 0px; right: 0px; bottom: 0px; 
                   position:  absolute;height: 100%; 
                   background: url(images/bg_opacity.png);
                   width: 105%; height: 105%;}
 
</style>
<script type="text/javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
<script type="text/javascript" src="../js/jquery.ui.core.min.js"></script>
<script type="text/javascript" src="../js/jquery.ui.datepicker.min.js"></script>
<script type="text/javascript" src="../js/validation_detail.js"></script>
<link rel="stylesheet" type="text/css" href="../js/ui-lightness/jquery.ui.all.css" />
<link rel="stylesheet" type="text/css" href="../js/ui-lightness/jquery.ui.datepicker.css" />
</head>
<body style="overflow-x:hidden;">
 <div id="pop_container"></div>
 <div id="pop_layout" style="display: none; position: absolute; width: 100%; height: 100%; margin: -9px; text-align: center;">
     
     <div id="pop_div" style="width: 400px; background: #e3e3e3; margin: 150px auto; border-radius:5px;">
         <a  id="pop_close"></a>
         <div style=" padding: 10px;">
             <table class="tbl_settings" style="width:100%;">
                 <thead>
                      <tr>
                     <td style="font-size: 10px;">Message:</td>
                     <td><input style="width: 220px;" type="text" id="txt_validation_msg" /></td>
                     <td> <a href="" onMouseOver="stm(Step_4[9],Style);" onClick="return false;" onMouseOut="htm()"> <img src="images/Help.png" border="0"></a></td>
                 </tr>
                 </thead>
                 <tbody>
                     
                 </tbody>
             </table>
             <hr/>
             <div style="text-align: right;">
                 <input type="button" id="btnCancel" value="Cancel" />
                 <input type="button" id="btnSave" value="Save" />
             </div>
         </div>
     </div>
 </div>
 
  <div id="pop_layout2" style="display: none; position: absolute; width: 100%; height: 100%; margin: -9px; text-align: center;">
     
     <div id="pop_div2" style="width: 400px; background: #e3e3e3; margin: 150px auto; border-radius:5px;">
         <a  id="pop_close2"></a>
         <div style=" padding: 10px;">
             <table style="width:100%;" class="tbl_alias">
                 <thead>
                      <tr class="header"> 
                         <th>Table<a href="" onMouseOver="stm(Step_4[2],Style);" onClick="return false;" onMouseOut="htm()"> <img src="images/Help.png" border="0"></a></th>
                         <th>Displayed Value Field<a href="" onMouseOver="stm(Step_4[3],Style);" onClick="return false;" onMouseOut="htm()"> <img src="images/Help.png" border="0"></a></th>
                     </tr>
                 </thead>
                 <tbody>
                     <tr>
                         <td>
                            <select style="width: 100px;" id="select_table">
                                <option value="0">Select Table</option>
                                <?php 
                                    $tables = mysql_query('show tables');
                                    while($table =  mysql_fetch_array($tables))
                                    {
                                        $table = $table[0];
                                        $columns = mysql_query("show columns from `$table`");
                                        $has_pri = FALSE;
                                        $pri = '';
                                        while($column = mysql_fetch_array($columns))
                                        {
                                            if($column['Key'] == 'PRI')
                                            {
                                                $has_pri = TRUE; 
                                                $pri =  $column['Field'];
                                                break;
                                                
                                           }
                                        }
                                        IF($has_pri)
                                            echo '<option pri="'.$pri.'" value="'.$table.'">'.$table.'</option>';
                                    }
                                ?>
                            </select>
                             
                         </td>
                         <td><select style="width: 100px;" id="select_text"></select></td>
                     </tr>
                 </tbody>
             </table>
             <hr/>
             <div style="text-align: right;">
                 <input type="button" id="btnCancel_Rel" value="Cancel" />
                 <input type="button" id="btnSave_Rel" value="Save" />
             </div>
         </div>
     </div>
 </div>
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
					<td height="18" colspan="2" class="step_title">Details form fields <a href="" onMouseOver="stm(Step_4[0],Style);" onClick="return false;" onMouseOut="htm()"> <img src="images/Help.png" border="0"></a></td>
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
                                            </center>					  <table style="width: 100%;" height="242" border="0" align="center" cellpadding="0" cellspacing="0" id="table13">
						<tr>
							<td width="27" height="16">
						    <img border="0" src="images/ctopleft.jpg" width="38" height="37"></td>
							<td style="width: 88%;"  height="16" background="images/ctop.jpg" style="background-repeat: x">&nbsp;</td>
							<td width="38" height="16">
						    <img border="0" src="images/ctopright.jpg" width="38" height="37"></td>
						</tr>
						<tr>
							<td width="27" height="167" background="images/cleft.jpg" style="background-repeat: repeat-y">&nbsp;</td>
							<td width="85%" align="center" valign="top" bgcolor="#F9F9F9">
                                  <div style="overflow-y: auto;max-height: 278px;">
                                      
                                       <table style="width: 100%;" class="tbl_alias">
                                           <thead>
                                          <tr class="header">
                                              <th>Field<a href="" onMouseOver="stm(Step_4[4],Style);" onClick="return false;" onMouseOut="htm()"> <img src="images/Help.png" border="0"></a></th>
                                              <th>Label<a href="" onMouseOver="stm(Step_4[5],Style);" onClick="return false;" onMouseOut="htm()"> <img src="images/Help.png" border="0"></a></th>
                                              <?php
                                                if($autodetect)
                                                    echo '<th>Display Value<a href="" onMouseOver="stm(Step_4[3],Style);" onClick="return false;" onMouseOut="htm()"> <img src="images/Help.png" border="0"></a></th>' ;
                                                else
                                                   echo '<th>Relations<a href="" onMouseOver="stm(Step_4[7],Style);" onClick="return false;" onMouseOut="htm()"> <img src="images/Help.png" border="0"></a></th>';
                                              ?>
                                              
                                              <th>Null<a href="" onMouseOver="stm(Step_4[6],Style);" onClick="return false;" onMouseOut="htm()"> <img src="images/Help.png" border="0"></a></th>
                                              <th>Validation<a href="" onMouseOver="stm(Step_4[8],Style);" onClick="return false;" onMouseOut="htm()"> <img src="images/Help.png" border="0"></a></th>
                                          </tr>
                                          </thead>
                                          <?php foreach($desc as $key=>$val) {?>
                                            <tr>
                                                <td><?php echo $key; ?></td>
                                                <td><input style="width:120px;" type="text" value="<?php echo $val['Label']; ?>" name="lbl_<?php echo str_replace(' ', '_', $key); ?>" /></td>
                                                <td>
                                                    <?php if($autodetect && array_key_exists('REFERENCED_TABLE_NAME', $val)) {?>
                                                    <select style="width:110px;" name="txt_field_<?php echo str_replace(' ', '_', $key); ?>">
                                                        <option value="0"> - Select -</option>
                                                        <?php
                                                        $columns = query('SHOW COLUMNS FROM `'.$val['REFERENCED_TABLE_NAME'].'`');
                                                        while($field = mysql_fetch_assoc($columns))
                                                        {
                                                            if($field['Field'] != $val['TextField'])
                                                                echo '<option>'.$field['Field'].'</option>';
                                                            else
                                                                echo '<option selected>'.$field['Field'].'</option>';
                                                        }
                                                      ?>
                                                    </select>
                                                    <a href="" onMouseOver="stm(Step_4[1],Style);" onClick="return false;" onMouseOut="htm()"> <img src="images/Help.png" border="0"></a>
                                                    <?php } else if($autodetect){
                                                         echo    'No relation';
                                                    }else{ ?>
                                                        <input <?php //if($val['Key'] == 'PRI') echo 'disabled title="Primary Key"' ?> type="button" value="Foreign key" class="relation" column="<?php echo $key; ?>" REFERENCED_TABLE_NAME="<?php echo $val['REFERENCED_TABLE_NAME']; ?>" REFERENCED_COLUMN_NAME="<?php echo $val['REFERENCED_COLUMN_NAME']; ?>" TextField="<?php echo $val['TextField']; ?>" />
                                                    <?php }?>
                                                </td>
                                                <td>
                                                    <?php
                                                    
                                                        if($val['Null'] == 0)
                                                            echo '<input type="checkbox" name="null_'.str_replace(' ', '_', $key).'" disabled title="This field is not null." />';
                                                        else if($val['Null'] == 1)
                                                             echo '<input type="checkbox" name="null_'.str_replace(' ', '_', $key).'"  />';
                                                       
                                                        else
                                                            echo '<input type="checkbox" checked name="null_'.str_replace(' ', '_', $key).'" />';
                                                    ?>
                                                </td>
                                                <td>
                                                    
                                                    <input <?php if($val['Key'] == 'PRI') echo 'disabled title="Primary Key"' ?>  column="<?php echo $key; ?>" regx_type="<?php echo $val['validation']['regx_type']; ?>" from="<?php echo $val['validation']['from']; ?>" to="<?php echo $val['validation']['to']; ?>" type="button" special_char="<?php echo $val['validation']['special_char']; ?>" regx="<?php echo $val['validation']['regx']; ?>" data_type="<?php echo $val['Type'] ?>" msg="<?php echo $val['validation']['msg']; ?>"  onclick="open_popup(this)" value="Validation" title="Custom validation for column <?php echo $key ?>"     />
                                                </td>
                                            </tr>
                                          <?php } ?>
                                          
                                      </table>
                               </div>
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
					
					<a href='step_5.php'><img src="images/03.jpg" border=0 width="170" height="34"></a></td>
					<td align="center">
					<INPUT name=continue type=image id="btn_cont" 
                  src="images/04.jpg" width="166" height="34" ></td>
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
