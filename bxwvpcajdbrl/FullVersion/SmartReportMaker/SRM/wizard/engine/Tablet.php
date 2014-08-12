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
<title><?php echo $title ?> - Tablet View </title>
<style type="text/css">
.ipad {
	background-image: url(ipad-emulator.jpg);
	background-repeat: no-repeat;
	height: 727px;
	width: 800px;
	margin-right: auto;
	margin-bottom: 10px;
	margin-left: auto;
}
</style>
</head>

<body>
        <div style="text-align: center; position: absolute; width: 200px; margin: 0px auto;"><a title="Mobile View" href="Mobile.php"><img border="0" src="view_mobile.png" /></a></div>
<div class="ipad">
<iframe src="index.php" frameborder="0" width="582" height="423" style="background-color:#FFF;margin-left: 108px;
margin-top: 157px;"></iframe>

</div>

</body>
</html>
