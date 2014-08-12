<?php 
    include_once 'config.php';
    include_once 'Mobile_Detect.php';
    $detect = new Mobile_Detect();

    if($detect->isMobile() || $detect->isTablet())
        header('Location: rep'.$file_name.'.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title ?> - Mobile View </title>
<style type="text/css">
.emulator {
	background-image: url(emulator.jpg);
	background-repeat: no-repeat;
	height: 912px;
	width: 850px;
	margin-right: auto;
	margin-left: auto;
}
</style>
</head>

<body>
            <div style="text-align: center; position: absolute; width: 200px; margin: 0px auto;"><a  title="Tablet View" href="Tablet.php"><img  border="0" src="view_tablet.png" /></a></div>
<center>
     
<div class="emulator"><iframe src="index.php" frameborder="0" width="313" height="543" style="background-color:#FFF;margin-right: 130px;
margin-top: 163px;"></iframe></div>

</center>
</body>
</html>
