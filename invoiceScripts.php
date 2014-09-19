/* We'll be using PHP to grab info from the database and convey it to the client side in Javascript variables */
<?php
  header("Content-type: text/javascript");

  /* Connect to the MySQL-running server (on localhost, with username root and no password) */
  $link=mysql_connect("localhost", "root", "tux898");
  
  /* Select the sniders2013 database for use later */
  $db=mysql_select_db("sniders2013", $link);
?>

/* Function to initialize the main data entry page when it loads */
function InitInvoice() {
  /* Focus the cursor on the Customer Number field (the first one) */
  document.entry.c_num.focus();

  /* When the customer number field blurs (loses focus), call the function to look up the corresponding customer name
     or the one to handle in-house rentals (customer pseudo-number 99999) */
  document.entry.c_num.onblur=function() {
    GetCustomer(document.entry.c_num.value);
  }
  $(".numOnly").keypress(validateNumber); // Make selected fields (those with the numOnly class) only accept numbers
  
  /* Prevent ENTER from submitting the data entry form, because that's too easy to accidentally press */
  $(document.entry).keypress(function(e) {
    return (e.keyCode!=13);
  });
}

/* This function doesn't allow anything but numbers in any field that triggers it on key press (i.e. customer number field) */
function validateNumber(event) {

    /* Get which key was pressed */
    key=window.event?event.keyCode:event.which;

    /* Key Code 8 is backspace, code 9 is tab, code 46 is a decimal point, and code 0 is a key-triggered blur event. Allow those. Codes 48 to 57 are numbers; don't allow anything else */
    if (key==8 || key==9 || key==0 || key==46) { return true; }
    else if (key<48 || key>57 ) { return false; }
    return true;
};

/* Use jQuery's Autocomplete library to make the Customer Name field an autocomplete box */
 $(function() {
customerList = [

/* Use PHP to dynamically create the customer-name autocomplete options from the database information */
<?php

  /* Get all the information from the t-customer table */
  $q="SELECT * FROM `t-customer`";
  $query=mysql_query($q);
  if ($query) {
    
    /* For each row in the table, make a Javascript object with the properties "label" (for the customer name) and "value" (for the 
       customer number). Add that object to the autocomplete array by echoing it into the script */
    $out="";
    while ($row = mysql_fetch_assoc($query)) {
      $row=array_map("addslashes", $row); // Needed so special characters like quotes, etc. are escaped before putting them in the script

      if ($out!="") { $out.=", "; }
      $out.='{label: "'.$row["C-NAME"].'", value: "'.$row["C-CUSTNO"].'", city: "'.$row["C-CITY"].'", shipping: '.$row["c-SHIP-METHOD"].', billing: '.$row["C-BILLING"].'}';
    }
    echo $out;
  }

?>
];

/* Define the c_name input field as an autocomplete field */
$( "#c_name" ).autocomplete({

  /* Take the autocomplete options from the customerList array we built in PHP */
  source: function(req, response) { 

        /* Make a pattern out of whatever the person typed */
        var re = $.ui.autocomplete.escapeRegex(req.term); 

        /* The ^ in a Regular Expression pattern means "at the beginning of the text" */
        var matcher = new RegExp( "^" + re, "i" ); 
    
        /* Respond with only those items in the itemStyles array whose labels match that pattern */
        response($.grep( customerList, function(item){ 
            return matcher.test(item.label); }) ); 
       },
  autoFocus: true,

  /* When an autocomplete item is selected, update the customer name field and the customer number field */
  select: function(e, ui) {
    document.entry.c_name.value=ui.item.label; // Just the name, not the city.
    document.entry.c_num.value=ui.item.value; // Customer number auto-fill
    return false;
  },

  /* When an autocomplete item is highlighted... */
  focus: function(e, ui) {

    /* Reset all items' texts to just their customer name, if that's been stored */
    items=$("a.ui-corner-all");
    items.each(function() {
      if ($(this).data("last-text")!="undefined") {
        $(this).html($(this).data("last-text"));
      }
    });

    /* Store the highlighted item's text, then change it to include the label (customer name) and the city as well */
    it=$("a.ui-state-focus");
    it.data("last-text", it.html());
    it.html(ui.item.label+" ("+ui.item.city+")");

    return false;
  }

});

});


/* The groundwork for creating a request to the server without loading a new page (known as AJAX or XHR) */
function Request() {
  if (window.XMLHttpRequest ) { return new XMLHttpRequest(); }
  else if (window.ActiveXObject) {
    try {
      return new ActiveXObject("Msxml2.XMLHTTP");
    }
    catch (e) {
      try {
        return new ActiveXObject("Microsoft.XMLHTTP");
      }
      catch (e) {
        return false;
      }
    }
  }
}

/* The function that triggers when a dynamic request has received a response */
function HandleXHR() {
  if (this.readyState==4) { // 4 is the "successfully completed" status code
    if (this.type=="StyleReq") {
      //alert(this.responseText);
      resp=JSON.parse(this.responseText); // Parse the response as a Javascript object
      document.getElementById("pants_style").value=FindVal(pantsStyles, resp.pants);
      document.getElementById("shirt_style").value=FindVal(shirtStyles, resp.shirt);
      document.getElementById("vest_style").value=FindVal(vestStyles, resp.vest);
      document.getElementById("sash_style").value=FindVal(sashStyles, resp.sash);
      document.getElementById("tie_style").value=FindVal(tieStyles, resp.tie);
    }
  }
}

/* Helper function to search through autocomplete object arrays to find one with a given value. */
function FindVal(where, what) {
  for (item in where) { // For each item in the given array...
    if (where[item].value.toLowerCase()==what.toLowerCase()) { // Find one that matches the search term, case-insensitive
      return where[item].value; // If found, return its value
    }
  }
  return ""; // If none are found, return an empty string.
}

/* Given a customer number, look through the customerList array to find the matching customer and update the customer name field */
function GetCustomer(c_num) {

  /* Don't continue if the given number is simply empty (i.e. no number was entered when the box left focus) 
     Also stop if the in-house rental number 99999 was entered, but something went wrong and we got here anyway */
  if (c_num=="99999") { return false; }

  $("#c_name").autocomplete("enable");
  if (c_num=="") { return false; }
  
  /* Search the customerList array for one matching the fiven customer number and use that item's name */
  for (i in customerList) {
    if (customerList[i].value==c_num) {
      document.entry.c_name.value=customerList[i].label;
      
      return true;
    }
  }

  /* If no customer with that ID is found, display that message in the customer name field */
  document.entry.c_name.value="Customer Not Found";
  return false;
}

<?php
  /* Disconnect from the database. This should happen automatically, but just in case, we do it manually */
  mysql_close($link);
?>