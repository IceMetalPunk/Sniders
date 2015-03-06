<html>
	<head>
		<link rel="stylesheet" type="text/css" href="styles.css" />
		
		<style>
			<?php include "styles.css"; ?>
			* { font-size: 10pt; }
			TABLE, TD, TH, TR {
				border: 0px;
				padding: 4px;
			}
			TABLE { width: 100%; }
		</style>
	</head>
	<body>
		<h3>Summary for billing cycle ending on <?php echo date("n/j/Y"); ?></h3>
		<table>
			<tr>
				<th>Customer #</th>
				<th>Customer Name</th>
				<th>Previous Balance</th>
				<th>Charges</th>
				<th>Payments/Credits</th>
				<th>Balance</th>
			</tr>
			<?php
			
				/* Workaround: charges need to be listed above credits and payments, but early in development we arbitrarily set the code range for charges
					to 30-39, payments for 20-29, and credits for 40-49. So rather than changing 20 locations in 6 different files, we're rigging this up by
					false-sorting them before an output. */
				uksort($totals, function($a, $b) {
					if ($a<10 && ($b<30 || $b>39)) { return -1; }
					else if ($b<10 && ($a<30 || $b>39)) { return 1; }
					else if ($a>=30 && $a<=39) { return -1; }
					else if ($b>=30 && $b<=39 && $a>9) { return 1; }
					else { return $b-$a; }
				});
			
				/* Ouput customer info */
				foreach ($customers as $custno=>$data) {
					/* Don't show customers with 0 balance. Will put this in if needed.
					if ($data["balance"]==0) { continue; } */
					echo "<tr><td>".$custno."</td>"; // Customer number
					echo "<td>".$data["name"]."</td>"; // Customer name
					echo "<td class='right'>$".number_format($data["prevBalance"], 2)."</td>"; // Previous balance
					echo "<td class='right'>$".number_format($data["charges"],2)."</td>"; // Charges for this cycle
					echo "<td class='right'>($".number_format($data["credits"], 2).")</td>"; // Credits for this cycle
					echo "<td class='right'>$".number_format($data["balance"], 2)."</td></tr>\r\n"; // Total balance
				}
				
				/* Output totals */
				$allTotal=0;
				foreach ($totals as $type=>$val) {
					echo "<tr style='font-weight: bold'>";
					if ($type==0) { $typeN="Invoice"; }
					else { $typeN=array_search($type, $types); }
					echo "<td>Total ".$typeN."s</td><td class='right'>";
					if ($type>=10 && ($type<30 || $type>=40)) {
						echo "($".number_format($val, 2).")";
						$allTotal-=$val;
					}
					else {
						echo "$".number_format($val, 2);
						$allTotal+=$val;
					}
					echo "</td></tr>";
				}
				
				/* Display the total total...as in, total of all charges - all credits. */
				echo "<tr style='font-weight: bold'><td>Total</td><td class='right'>";
				if ($allTotal<0) {
					echo "($".number_format($allTotal, 2).")";
				}
				else {
					echo "$".number_format($allTotal, 2);
				}
				echo "</td></tr>";
				
			?>
		</table>
	</body>
</html>