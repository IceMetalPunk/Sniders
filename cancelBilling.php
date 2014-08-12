<?php
  if (!empty($_POST['cancel']) && $_POST['cancel']==true) {
	  shell_exec('taskkill /F /IM "php.exe"');
		echo "Cancelled";
	}
?>