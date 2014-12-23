<html>
  <head>
    <title>Billing Cycle In Progress</title>

    <!-- Main styles -->
    <link rel="stylesheet" href="styles.css" />

    <!-- Styles for autocomplete elements -->
		<!--
    <link rel="stylesheet" href="jquery-ui.css" />
    <link rel="stylesheet" href="jquery-style.css" />-->

    <!-- jQuery library and its Autocomplete extension -->
    <script type="text/javascript" src="jquery-1.9.1.js"></script>
    <!--<script type="text/javascript" src="jquery-ui.js"></script>-->

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
								$("#FunctionButton").val("Posting Complete");
								$("#FunctionButton").prop("disabled", true);
								GetIsMonthly();
							}
							else {
								numBills=data.total;
								onBill=data.on;
								SetProgress(data.on/data.total, "Processing bill "+data.on+"/"+data.total);
								if (data.on<data.total) { checkTimer=setTimeout(GetBillProgress, 100); }
								else {
									SetProgress(1, "Posting Complete");
									canceled=true;
									document.title="Posting Complete";
									$("#FunctionButton").unbind("click");
									$("#FunctionButton").val("Posting Complete");
									$("#FunctionButton").prop("disabled", true);
									GetIsMonthly();
								}
							}
						},
						"json"
					).fail(function() {
						GetBillProgress();
					});
				}
				
			function GetDate() {
				$.get(
						"getDates.php",
						function (data) {
							if (canceled) { return false; }
							$("#monthlyMessage").hide();
							$("#use_date").val(data.nextCycle);
							$("#FunctionButton").unbind("click");
							$("#FunctionButton").click(SetDate);
							$("#FunctionButton").val("Set Next Cycle End Date");
							$("#FunctionButton").prop("disabled", false);
							$("#dateForm").show();
						},
						"json"
					);
				}
				
			function GetIsMonthly() {
				$.get(
						"isMonthlyTime.php",
						function (data) {
							if (canceled) { return false; }
							if (!data.result) {
								GetDate();
							}
							else {
								$("#monthlyMessage").show();
								$("#FunctionButton").unbind("click");
								$("#FunctionButton").click(function() {
									var win=window.open("monthlyStatement.php", "_blank");
									win.focus();
									GetDate();
								});
								$("#FunctionButton").val("Print Balance Summary");
								$("#FunctionButton").prop("disabled", false);
							}
						},
						"json"
					);
				}
				
				function SetDate() {
					document.entry.submit();
				}
				
				function StartBilling() {
					canceled=false;
					$.get("startBilling.php");
					$("#FunctionButton").unbind("click").click(CancelBilling);
					$("#FunctionButton").val("Cancel Posting");
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
								$("#FunctionButton").val("Begin Posting");
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
									$("#FunctionButton").val("Begin Posting");
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
					if (!confirm("Are you sure you want to cancel the posting process?")) { return false; }
					clearTimeout(checkTimer);
					canceled=true;
					$.ajax({
						type: "POST",
						url: "cancelBilling.php",
						data: {cancel:true},
						success: function(data) {
							SetProgress(onBill/numBills, "Posting Cancelled ("+onBill+"/"+numBills+")");
							document.title="Posting Cancelled";
						},
						async:false,
						dataType: "text"
					});
					return true;
				}
				
				/* Cancel invoicing */
				function CancelInvoice() {
					if (!confirm("Are you sure you want to cancel the posting process?")) { return false; }
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
		<span style="display:none; font-size:14pt" id="monthlyMessage">Your last balance summary was generated over a month ago. Click below to generate a new statement.<br /></span>
		<form id="dateForm" style="display:none" name="entry" action="setCycleDate.php" method="post">
			<input name="use_date" id="use_date" type="text" />
			<input type="hidden" name="full_use_date" />
		</form>
		<input type="button" id="FunctionButton" value="Cancel Invoice" />
	</body>
</html>