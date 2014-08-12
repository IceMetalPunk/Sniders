<?php
  if (!file_exists("billCycleProgress.txt")) {
		echo '{"on": 0, "total": -1}';
	}
	else {
		$cont=file("billCycleProgress.txt", FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
		if ($cont[0]>0) {
			echo '{"on": '.$cont[1].', "total": '.$cont[0].'}';
			if ($cont[1]>=$cont[0]) {
			  unlink("billCycleProgress.txt");
			}
		}
		else if ($cont[0]==-1) {
			echo '{"on": 0, "total": 0}';
			unlink("billCycleProgress.txt");
		}
	}
?>