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
				<th>Total Invoiced This Cycle</th>
				<th>Total Adjusted Charges This Cycle</th>
				<th>Total Payments/Credits This Cycle</th>
				<th>Net Transactions This Cycle</th>
				<th>Current Balance</th>
				<th>Last Invoice</th>
				<th>Last Payment</th>
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
					if ($data["balance"]==0) { continue; }
					echo "<tr><td>".$custno."</td>"; // Customer number
					echo "<td>".$data["name"]."</td>"; // Customer name
					echo "<td class='right'>$".number_format($data["invoiced"], 2)."</td>"; // Previous balance
					echo "<td class='right'>$".number_format($data["charges"],2)."</td>"; // Charges for this cycle
					echo "<td class='right'>($".number_format(abs($data["credits"]), 2).")</td>"; // Credits for this cycle
					$net=$data["invoiced"]+$data["charges"]-abs($data["credits"]);
					echo "<td class='right'>".($net<0?"(-":"")."$".number_format(abs($net), 2).($net<0?")":"")."</td>";
					echo "<td class='right'>".($data["balance"]<0?"(-":"")."$".number_format(abs($data["balance"]), 2).($data["balance"]<0?")":"")."</td>";
					echo "<td class='right'>".date("n/j/Y", $data["lastInvoice"])."</td>";
					echo "<td class='right'>".date("n/j/Y", $data["lastPayment"])."</td>";
				}

				/* Output totals */				
				echo "<tr style='font-weight:bold'><td>&nbsp;</td><td>Totals</td>";
				echo "<td class='right'>$".number_format($totals["Invoiced"], 2)."</td>"; // Previous balance
				echo "<td class='right'>$".number_format($totals["Charges"],2)."</td>"; // Charges for this cycle
				echo "<td class='right'>($".number_format(abs($totals["Credits"]), 2).")</td>"; // Credits for this cycle
				$net=$totals["Invoiced"]+$totals["Charges"]-abs($totals["Credits"]);
				echo "<td class='right'>".($net<0?"(-":"")."$".number_format(abs($net), 2).($net<0?")":"")."</td>";
				echo "<td colSpan='3'>&nbsp;</td></tr>";
				
			?>
		</table>
	</body>
</html>