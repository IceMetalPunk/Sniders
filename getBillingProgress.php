<?php
  if (!file_exists("billCycleProgress.txt")) {
		echo '{"on": 0, "total": -1}';
	}
	else {
		$cont=file("billCycleProgress.txt", FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
		if ($cont[1]>0) {
			echo '{"on": '.$cont[0].', "total": '.$cont[1].'}';
			if ($cont[0]>=$cont[1]) {
			  @unlink("billCycleProgress.txt");
			}
		}
		else if ($cont[1]==-1) {
			echo '{"on": 0, "total": 0}';
			@unlink("billCycleProgress.txt");
		}
		else if ($cont[1]==0) {
			echo '{"on": -1, "total": 0}';
			@unlink("billCycleProgress.txt");
		}
	}
?>