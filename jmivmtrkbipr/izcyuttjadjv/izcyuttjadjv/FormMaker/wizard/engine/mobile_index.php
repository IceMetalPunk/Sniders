<?php
  include_once 'Mobile_Detect.php';
  $detect = new Mobile_Detect();
  if(!$detect->isMobile() && !$detect->isTablet())
      header('Location: Mobile.php');
  else
      include 'index.php';
      //include 'rep'.$file_name.'.php';

?>