<?php
	@unlink("billingCycleProgress.txt");
	pclose(popen("start php processBilling.php /B", "r")); // Start the billing process in the background.
?>