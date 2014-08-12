<?php
    session_start();
  //  session_destroy();
    error_reporting(E_ERROR  | E_PARSE);
    $next = "step_5.php";
    $error_msg = '';
    @$continue= $_POST["continue_x"];
    $SelectedFields = $_SESSION["fields"];
    $labels = $_SESSION["labels"];
    $table = $_SESSION["table"];
    

    if(empty($labels))
    {
        $temp = array();
        foreach($SelectedFields as $feild)
        {
          $feild_splitted = explode('.', $feild);
          $temp[$feild] = (count($table) == 1)? $feild : $feild_splitted[1];
        }
        $labels = $temp;
        $_SESSION["labels"] = $labels;
    }
    else
    {
    
        $temp = array();
        foreach ($SelectedFields as $feild)
        {
            if(array_key_exists($feild, $labels))
                $temp[$feild] =  $labels[$feild];
            else
            {
                $feild_splitted = explode('.', $feild);
                $temp[$feild] = (count($table) == 1)? $feild : $feild_splitted[1];
            }
            
        }
    
        $labels = $temp;
        $_SESSION["labels"] = $labels;
    }
    //echo var_dump($labels);
    if(!empty($continue))
    {
        $error = false;
        foreach ($SelectedFields as $field)
        {
          
            
            if(trim($_POST['lbl_'. str_replace('.', '0x', $field)]) == '')
            {
                $error = true;
                $error_msg .= '<br/>The Label of column '.$field.' is required.';
            }
            //echo $_POST['lbl_'.str_replace('.', '0x', $field)];
            $labels[$field] = $_POST['lbl_'.str_replace('.', '0x', $field)];
        }  
        
        $_SESSION['labels'] = $labels;
        //echo print_r($_SESSION['labels']);
        
        if(!$error)
            header ('Location: '.$next);
            
    }
    
    //echo var_dump($labels);
?>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Labeling</title>
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
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">

<link href="style.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript1.2" src="main.js" type="text/javascript"></SCRIPT>  

</head>
<body style="overflow-x:hidden;">

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
           
			<p><img border="0" src="images/01.jpg" width="369" height="71"></p>
			<table border="0" width="100%" id="table8" height="331">
				<tr>
					<td height="18" colspan="2" class="step_title">Labeling </td>
				</tr>
				<tr>
					<td colspan="2" height="263" valign="top">

		<table cellspacing="0" cellpadding="0" width="100%" border="0" align="center" id="table9">
			<tr>
				<td width="85%" height="261" valign="top" style="font-family: Verdana, Arial, sans-serif; font-size: 11px; line-height: 1.5">
				<div align="left" class='error'>
                <?php
                echo $error_msg;
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
                                              <th>Field</th>
                                              <th>Label</th>
                                          </tr>
                                          </thead>
                                          <?php foreach($labels as $key=>$val) {?>
                                            <tr>
                                                <td><?php echo $key; ?></td>
                                                <td><input style="width:120px;" type="text" value="<?php echo $val; ?>" name="lbl_<?php echo str_replace('.', '0x', $key); ?>" /></td>
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
					
					<a href='step_4.php'><img src="images/03.jpg" border=0 width="170" height="34"></a></td>
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
