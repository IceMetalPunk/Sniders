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
	pclose(popen("start php batchInvoice.php /B", "r")); // Start the invoicing process in the background
?>
		<a href="index.php"><span style="float:right"><img src="logo.png" /></span></a><br clear="both" />
		
		<div id="leaveWarning" style="font-weight:bold; font-size:14pt"><span style='color:#ff0000'>Warning:</span> Do not leave this screen while billing is in progress. Either wait for billing to complete or cancel it before leaving.</div>
	  <div class="progBG"><span class="progFG" id="billProg"></span><div id="billProgText" class="progText"></div></div>
		
		<script type="text/javascript">
			$(window).load(function() {
				var onBill=0, numBills=1, checkTimer=null, canceled=false, step=0;
				
				function SetProgress(amt, text) {
					$("#billProg").animate({width: (amt*100)+"%" }, { duration: 500, queue:false});
					$("#billProgText").html(text);
				}
				function GetBillProgress() {
					if (canceled) { return false; }
					$.get(
						"getBillingProgress.php",
						function (data) {
							if (canceled) { return false; }
							if (data.total<0) {
								SetProgress(0, "Connecting to database...");
								checkTimer=setTimeout(GetBillProgress, 100);
							}
							else if (data.total==0) {
								SetProgress(0, "No Bills To Process");
								$("#FunctionButton").unbind("click");
								$("#FunctionButton").val("Billing Complete");
								$("#FunctionButton").prop("disabled", true);
							}
							else {
								numBills=data.total;
								onBill=data.on;
								SetProgress(data.on/data.total, "Processing bill "+data.on+"/"+data.total);
								if (data.on<data.total) { checkTimer=setTimeout(GetBillProgress, 100); }
								else {
									SetProgress(1, "Billing Complete");
									canceled=true;
									document.title="Billing Complete";
									$("#FunctionButton").unbind("click");
									$("#FunctionButton").val("Billing Complete");
									$("#FunctionButton").prop("disabled", true);
								}
							}
						},
						"json"
					).fail(function() {
						GetBillProgress();
					});
				}
				
				function StartBilling() {
					canceled=false;
					$.get("startBilling.php");
					$("#FunctionButton").unbind("click").click(CancelBilling);
					$("#FunctionButton").val("Cancel Billing");
					GetBillProgress();
				}
				
				function GetInvoiceProgress() {
					if (canceled) { return false; }
					$.get(
						"getInvoiceProgress.php",
						function (data) {
							if (canceled) { return false; }
							if (data.total<0) {
								SetProgress(0, "Connecting to database...");
								 checkTimer=setTimeout(GetInvoiceProgress, 100);
							}
							else if (data.total==0) {
								SetProgress(0, "No Invoices To Process");
								$("#FunctionButton").unbind("click").click(StartBilling);
								$("#FunctionButton").val("Begin Billing");
							}
							else {
								numBills=data.total;
								onBill=data.on;
								SetProgress(data.on/data.total, "Invoicing "+data.on+"/"+data.total);
								if (data.on<data.total) { checkTimer=setTimeout(GetInvoiceProgress, 100); }
								else {
									SetProgress(1, "Invoicing Complete");
									canceled=true;
									document.title="Invoicing Complete";
									$("#FunctionButton").unbind("click").click(StartBilling);
									$("#FunctionButton").val("Begin Billing");
								}
							}
						},
						"json"
					).fail(function() {
						$("#debug").html("Failed");
						GetInvoiceProgress();
					});
				}
				GetInvoiceProgress();
				
				/* Cancel billing */
				function CancelBilling() {
					if (!confirm("Are you sure you want to cancel the billing process?")) { return false; }
					clearTimeout(checkTimer);
					canceled=true;
					$.ajax({
						type: "POST",
						url: "cancelBilling.php",
						data: {cancel:true},
						success: function(data) {
							SetProgress(onBill/numBills, "Billing Cancelled ("+onBill+"/"+numBills+")");
							document.title="Billing Cancelled";
						},
						async:false,
						dataType: "text"
					});
					return true;
				}
				
				/* Cancel invoicing */
				function CancelInvoice() {
					if (!confirm("Are you sure you want to cancel the billing process?")) { return false; }
					clearTimeout(checkTimer);
					canceled=true;
					$.ajax({
						type: "POST",
						url: "cancelBilling.php",
						data: {cancel:true},
						success: function(data) {
							SetProgress(onBill/numBills, "Invoicing Cancelled ("+onBill+"/"+numBills+")");
							document.title="Invoicing Cancelled";
						},
						async:false,
						dataType: "text"
					});
					return true;
				}
				
				/* Setup initial button function */
				$("#FunctionButton").unbind("click").click(CancelInvoice);
			});
		</script>
		<input type="button" id="FunctionButton" value="Cancel Invoice" />
	</body>
</html>