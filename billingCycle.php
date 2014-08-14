<html>
  <head>
    <title>Billing Cycle In Progress</title>

    <!-- Main styles -->
    <link rel="stylesheet" href="styles.css" />

    <!-- jQuery library and its Autocomplete extension -->
    <script type="text/javascript" src="jquery-1.9.1.js"></script>

    <!-- Main Javascript library, including data pulled from the databases, hence why it must be in .php format -->
    <script type="text/javascript" src="scripts.php?<?php echo time(); ?>"></script>
  </head>
	<body>
<?php
  //pclose(popen("start php processBilling.php /B", "r")); // Start the billing process in the background.
	pclose(popen("start php batchInvoice.php /B", "r"));
?>
		<a href="index.php"><span style="float:right"><img src="logo.png" /></span></a><br clear="both" />
		
		<div id="leaveWarning" style="font-weight:bold; font-size:14pt"><span style='color:#ff0000'>Warning:</span> Do not leave this screen while billing is in progress. Either wait for billing to complete or cancel it before leaving.</div>
	  <div class="progBG"><span class="progFG" id="billProg"></span><div id="billProgText" class="progText"></div></div>
		
		<script type="text/javascript">
		  var onBill=0, numBills=1, checkTimer=null, canceled=false;
			
			function SetProgress(amt, text) {
				onBill=amt;
				$("#billProg").animate({width: ((onBill/numBills)*100)+"%" }, { duration: 500, queue:false});
				$("#billProgText").html(text);
			}
			function GetProgress() {
			  if (canceled) { return false; }
			  $.get(
					"getBillingProgress.php",
					function (data) {
					  if (canceled) { return false; }
					  if (data.total<0) {
						  SetProgress(0, "Connecting to database...");
							 checkTimer=setTimeout(GetProgress, 100);
						}
						else if (data.total==0) {
						  SetProgress(0, "No Bills To Process");
						}
						else {
						  numBills=data.total;
							SetProgress(data.on, "Processing bill "+data.on+"/"+data.total);
							if (data.on<data.total) { checkTimer=setTimeout(GetProgress, 100); }
							else { SetProgress(data.total, "Billing Complete"); canceled=true; document.title="Billing Complete"; }
						}
					},
					"json"
				).fail(function() {
				  GetProgress();
				});
			}
			GetProgress();
			
			function CancelProgress() {
			  if (!confirm("Are you sure you want to cancel the billing process?")) { return false; }
			  clearTimeout(checkTimer);
				canceled=true;
				$.ajax({
					type: "POST",
					url: "cancelBilling.php",
					data: {cancel:true},
					success: function(data) {
						SetProgress(onBill, "Billing Cancelled ("+onBill+"/"+numBills+")");
						document.title="Billing Cancelled";
					},
					async:false,
					dataType: "text"
				});
				return true;
			}
		</script>
		<input type="button" onclick="CancelProgress()" value="Cancel Billing" />
	</body>
</html>