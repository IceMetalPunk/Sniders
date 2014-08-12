<?php
error_reporting(E_ERROR  | E_PARSE);
require_once("lib.php"); 
$path =pathinfo($_SERVER["SCRIPT_NAME"]);
$server = $_SERVER["SERVER_NAME"];
$full_path = $server .$path["dirname"];
$full_path = str_replace("http://","",$full_path);
$arr = explode("/",$full_path);

$URL_Back = '';
if($is_mobile)
    $URL_Back = "http://".$full_path ."/index.php" ;
else
    $URL_Back = "http://".$full_path ."/rep".$file_name.".php" ;

$URL = "http://".$full_path ."/rep".$file_name.".php" ;

if(isset($_POST["Submit"]))
{

 @$from = $_POST["from"];

 @$to = $_POST["to"];

 @$subject = $_POST["subject"];

 @$message = $_POST["message"];

 
 // Validation
if (! ereg('[A-Za-z0-9_-]+\@[A-Za-z0-9_-]+\.[A-Za-z0-9_-]+', $from))
{
$err = "please enter a valid  email address <br>";
}

if (! ereg('[A-Za-z0-9_-]+\@[A-Za-z0-9_-]+\.[A-Za-z0-9_-]+', $to))
{
$err = "please enter a valid email address<br>";
}

if(empty($err))
{
    $header = "From: Smart Report Maker<$from>\r\nReply-To:Smart Report Maker<$from>\r\n";

  @mail($to, $subject ,$message ,$header ) ;

  $err = "Message is successfully sent, please hit the back button to get back to the report view" ;
}



}






?>




<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>send email</title>
<link href="file:///C:/Documents%20and%20Settings/karim/Desktop/Colorful.css" rel="stylesheet" type="text/css" />
</head>

<body class="MainPage" style="text-align: center" topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0">
<form name="form1" method="post" action="">
                  <TABLE align="center" id=table3 borderColor=#f3f1ef height=369 cellSpacing=0
                  cellPadding=4 width="561" align=left border=0>
                    <TR>
                      <TD align=left background=bg_table.gif
                      colSpan=2 height=25>
						<p align="right"><font color="#000080"><u>
						<a href="<?php echo $URL_Back;?>"><font color="#000080">Report View</font></a></u></font></TD></TR>
                    <TR bgColor=#f3f1ef>
                      <TD align=left colSpan=2 height=21>
						<p align="center"><font color="#FF0000"><b>
						<?php
					if(!empty($err))
					{
                    echo ($err);

     }



						?>


                        </b></font></TD></TR>
                    <TR bgColor=#f3f1ef>
                      <TD align=right height=21 width="150" ><b>From</b></TD>
                      <TD align=left width="491" ><FONT face="Times New Roman">
						<INPUT
                         size=32 name=from > </FONT></TD></TR>
                    <TR bgColor=#e4ded8>
                      <TD borderColor=#f3f1ef align=right bgColor=#f3f1ef
                      height=21 width="150" ><b>To</b></TD>
                      <TD borderColor=#f3f1ef align=left bgColor=#f3f1ef width="491" ><FONT
                        face="Times New Roman">
						<INPUT id=email size=32
                        name=to> </FONT></TD></TR>
                    <TR bgColor=#e4ded8>
                      <TD borderColor=#f3f1ef align=right bgColor=#f3f1ef
                      height=21 width="150" ><b>Subject</b></TD>
                      <TD borderColor=#f3f1ef align=left bgColor=#f3f1ef width="491" ><FONT
                        face="Times New Roman">
						<INPUT id=subject size=32
                        name=subject > </FONT></TD></TR>
                    <TR bgColor=#e4ded8>
                      <TD borderColor=#f3f1ef align=right bgColor=#f3f1ef
                      height=158 width="150" ><b>Message</b></TD>
                      <TD vAlign=top borderColor=#f3f1ef align=left
                      bgColor=#f3f1ef width="491" ><FONT face="Times New Roman">
						<TEXTAREA id=desc name=message rows=10 cols=54 >Hi,
The following report was created using  Smart Report Maker:
<?php  echo $URL; ?>
</TEXTAREA>
                        </FONT></TD></TR>
                    <TR bgColor=#e4ded8>
                      <TD borderColor=#f3f1ef align=right bgColor=#f3f1ef
                      height=56 colspan="2">

						<p align="center">
						<input type="submit" value="  Send   " name="Submit" >

						</TD>
                      </TR>
                    </TABLE>
</from>

</body>

</html>
