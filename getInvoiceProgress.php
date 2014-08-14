<?php
  if (!file_exists("batchInvoiceProgress.txt")) {
		echo '{"on": 0, "total": -1}';
	}
	else {
		$cont=file("batchInvoiceProgress.txt", FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
		if ($cont[0]>0) {
			echo '{"on": '.$cont[0].', "total": '.$cont[1].'}';
			if ($cont[0]>=$cont[1]) {
			  unlink("batchInvoiceProgress.txt");
			}
		}
		else if ($cont[1]==-1) {
			echo '{"on": 0, "total": 0}';
			unlink("batchInvoiceProgress.txt");
		}
	}
?>