<?php
  header("Content-type: text/javascript");

  /* Connect to the MySQL-running server (on localhost, with username root and no password) */
  $link=mysql_connect("localhost", "root", "tux898");
  
  /* Select the sniders2013 database for use later */
  $db=mysql_select_db("sniders2013", $link);
?>

function CustomerInit() {
  $(".numOnly").keypress(validateNumber); // Make selected fields (those with the numOnly class) only accept numbers
  $(".phoneNum").keypress(FormatPhone); // Force phone number format on phoneNum textboxes.
  $(".phoneNum").val("xxx-xxx-xxxx"); // Initialize the phone number field values to all x's.
  
  /* When the customer number entry field blurs (loses focus), call the function to look up the corresponding customer name */
  document.entry.c_num.onblur=function() {
    GetCustomer(document.entry.c_num.value);
  }
  
  /* Create a list of states and their abbreviations, for autocomplete state fields */
  stateList=[
  <?php
    /* Read the list of states from the file */
    $states=file("stateList.txt", FILE_IGNORE_NEW_LINES);
    
    /* Go through it and separate the abbreviations from the names */
    for ($p=0; $p<count($states); ++$p) {
      $state=$states[$p];
      $all=explode(" ~ ", $state);
      if ($p!=0) { echo ", "; }
      /* Build a Javascript autocomplete object for each state */
      echo '{label: "'.$all[0].' - '.$all[1].'", value: "'.$all[0].'"}';
    }
  ?>
  ];

  /* Define all state_choice fields as an autocomplete field for state abbreviations */
  $(".state_choice").autocomplete({

    /* Take the autocomplete options from the customerList array we built in PHP, but only at the beginning of the names */
    source: function(req, response) { 

        /* Make a pattern out of whatever the person typed */
        var re = $.ui.autocomplete.escapeRegex(req.term); 

        /* The ^ in a Regular Expression pattern means "at the beginning of the text" */
        var matcher = new RegExp( "^" + re, "i" ); 
    
        /* Respond with only those items in the stateList array whose labels match that pattern */
        response($.grep(stateList, function(item){ 
            return matcher.test(item.label); }) ); 
       },
    autoFocus: true
  });
}

/* Helper function to set the caret position inside a textbox */
function setCaretPosition(element, caretPos) {
  if(element.createTextRange) { // If the browser supports createTextRange, use that
    var range = element.createTextRange(); // Create a range of text from the field element
      range.move('character', caretPos); // Move the cursor to the specified number of characters.
  }
  else {
    if(element.selectionStart) { // If not, the browser should support selectionStart
      element.focus(); // Focus on it
      element.setSelectionRange(caretPos, caretPos); // Then "select" the single position, moving the cursor there
    }
  }
}

/* Function to force phone number format on phoneNum textboxes */
function FormatPhone(event) {
  var key;
  if (typeof event!="null") { // If a key was pressed (as opposed to an initialization)...
    key=window.event?event.keyCode:event.which; // Get the pressed key
  }
  else { key=-1; } // If it's an initialization, just consider it -1.

  if (key==0 || key==9) { // Allow tabs
    return true;
  }
  if (key>=48 || key<=57 ) { // If a number was pressed...
    this.value+=String.fromCharCode(key); // Add it to the box's value
  }
  var val=this.value.split(/[^0-9]/i).join(""); // Remove nondigits
  
  if (key==8) { // If backspace was pressed...
    val=val.substr(0, val.length-1); // Remove the last digit
  }
  
  var caretPos=val.length; // Record the number of digits to set the caret position later.
  if (caretPos>6) { caretPos+=2; } // Add 2 positions for the dashes
  else if (caretPos>3) { caretPos+=1; } // Or 1 if it's before the second dash
  
  /* Pad the right with x's to get a 10-digit number */
  for (var p=val.length; p<10; ++p) { val+="x"; }
  
  var area=val.substr(0, 3); // First three digits are area code.
  var ex=val.substr(3, 3); // Next three digits are exchange.
  var num=val.substr(6, 4); // Last four digits are the number.
  
  /* Format it */
  this.value=area+"-"+ex+"-"+num;
  
  /* Set the caret position so it's not at the end of the box */
  setCaretPosition(this, caretPos);
  
  return false; // Make sure the pressed key isn't added to the processed value
}

/* Show a section, and optionally focus on specified form field */
function Show(which, focusOn) {
  /* Hide everything */
  $(".hiddenSection").hide();

  /* Show the specified section */
  $("#"+which).show();
  
  /* If a form field to focus on was specified, focus on it */
  if (arguments.length>1) {
    focusOn.focus();
  }
  
  /* Set the hidden action field to mark what action we're taking, as given by the section we're showing.
     Ignore anything with the number 2, as those are multi-page sections and we don't need to change the action
     when moving to a new page of the same form.     */
  if (which.indexOf("2")<0) {
    document.entry.act.value=which;
  }
}

/* Function to set the shortcut keys for things that become visible */
function MoveAccessKey(which, key) {
  $(".access").attr("accessKey", "");
  $(which).attr("accessKey", key);
}

/* Function to parse the data returned from a customer lookup */
function HandleLookups() {
  if (this.readyState==4) { // 4 is the "successfully completed" status code
    if (this.type=="ChangeReq") {
     
      /* The line below fixes a little issue where the PHP escapes single-quotes as well, which JSON doesn't like in double-quote
         strings. So it removes those extra slashes before apostraphes. */
      var rep=this.responseText.replace(/\\'/g, "'");
     
      /* Attempt to convert the returned data into a useable object */
      try {
        resp=JSON.parse(rep); // Parse the response as a Javascript object
      }
      catch (e) {
        // Not very elegant, but if the data returned by the getCustomers script is invalid, it'll pop up an error.
        prompt("Error processing customer information. Please contact the administrator with the following error:", e);
      }
      
      /* Go through the customer's pieces of information and fill out the form */
      for (info in resp) {
        if (typeof document.entry[info]=="undefined") { continue; }
        if (document.entry[info].nodeName.toLowerCase()=="select") { // Handle dropdowns
          var opts=document.entry[info].options;
          for (var i=0; i<opts.length; ++i) {
            if (opts[i].value==resp[info]) {
              document.entry[info].selectedIndex=i;
              break;
            }
          }
        }
        if (document.entry[info].type=="checkbox") { // Handle checkboxes
          document.entry[info].checked=(resp[info]!="0");
        }
        else { // Otherwise, it's just a text field to be filled in
          document.entry[info].value=resp[info];
        }
      }
      
      Show("addSection", document.entry["C-NAME"]); // Show the filled entry form
      MoveAccessKey(document.entry.nextButton, "N"); // Allow the "Next" button to be accessible with ALT+N
      document.entry.act.value="changeSection"; // Set the action type back to "change", as showing the addSection will have altered that
    }
  }
}

function Lookup() {
  req=Request(); // Start a new data request.
  req.open("GET", "getCustomer.php?c_num="+document.entry.c_num.value); // Point the request to the getCustomer.php page and pass it the customer number.
  req.onreadystatechange=HandleLookups; // Tell it what function to call when the request's state (sending, receiving, done, etc.) changes
  req.type="ChangeReq"; // Mark this as a customer change request so the HandleLookups function can deal with it as well as other request types
  req.send(null); // Send the request
}

<?php
  /* Disconnect from database */
  mysql_close($link);
?>