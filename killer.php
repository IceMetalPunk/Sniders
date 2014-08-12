<?php
  if (!empty($_POST['killer']) && $_POST['killer']=="Kill It") {
	  shell_exec('taskkill /F /IM "php.exe"');
		echo "Killed!<br />";
	}
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<input type="submit" name="killer" value="Kill It" />
</form>