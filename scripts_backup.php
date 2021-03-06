/* We'll be using PHP to grab info from the database and convey it to the client side in Javascript variables */
<?php
  header("Content-type: text/javascript");

  /* Connect to the MySQL-running server (on localhost, with username root and no password) */
  $link=mysql_connect("localhost", "root", "tux898");
  
  /* Select the sniders2013 database for use later */
  $db=mysql_select_db("sniders2013", $link);
?>

/* Function to initialize the main data entry page when it loads */
function Initialize() {
  /* Focus the cursor on the Customer Number field (the first one) */
  document.entry.c_num.focus();

  /* When the customer number field blurs (loses focus), call the function to look up the corresponding customer name */
  document.entry.c_num.onblur=function() {
    GetCustomer(document.entry.c_num.value);
  }
  $(".numOnly").keypress(validateNumber); // Make selected fields (those with the numOnly class) only accept numbers
  $("#coat_style").blur(FillComplete); // When the coat style field loses focus, fill the other styles if it's a complete outfit
  
  /* Set up accessories box and complete-outfit box to make them exclusive by unchecking the other when they're checked */
  $("#accBox").click(function() {
    if (this.checked) { document.getElementById("compBox").checked=false; }
    ToggleAccessories(); // Show/hide the appropriate forms.
  });
  $("#compBox").click(function() {
    if (this.checked) { document.getElementById("accBox").checked=false; }
    ToggleAccessories(); // Show/hide the appropriate forms.
  });
  
  /* Make the accessories' quantity fields auto-total when changed */
  $("select[name*='vs_qty']").change(function() {
    Totals("vs"); // VS = Vest and Sash
  });
  $("select[name*='a_tie_qty']").change(function() {
    Totals("a_tie"); // Tie
  });
  $("select[name*='a_glove_qty']").change(function() {
    Totals("a_glove"); // Gloves
  });
  $("select[name*='a_susp_qty']").change(function() {
    Totals("a_susp"); // Suspenders
  });
  
  /* Make the accessories-only vest and sash fields exclusive */
  $("#vest_a_style").change(function() {
    var other=document.getElementById("sash_a_style"); // Shorthand for the other box to look at
    if (this.value=="") { // If the vest field was cleared...
      other.disabled=false; // Enable the sash field
      setTimeout(function() { // Wait 1ms to let the MS qty field be selected, since it'll happen before the sash field is enabled
        if (document.activeElement==document.entry.ms_vs_qty) { other.focus(); } // If the MS field is focused, move back to the sash field
      }, 1);
    } 
    else { // If not...
      setTimeout(function() { // Wait 1ms to give the sash field time to become active if it should
        if (document.activeElement==other) { document.entry.ms_vs_qty.focus(); } // Then skip to the MS dropdown
        other.disabled=true;  // Then disable and clear the sash field.
        other.value="";
      }, 1); 
    } 
  });
  $("#sash_a_style").change(function() {
    var other=document.getElementById("vest_a_style"); // Shorthand for the other box to look at
    if (this.value=="") { other.disabled=false; } // If this was cleared, enable the vest field.
    else { other.disabled=true; other.value=""; } // If not, disable and clear the vest field.
  });
  
  /* Prevent ENTER from submitting the data entry form, because that's too easy to accidentally press */
  $(document.entry).keypress(function(e) {
    return (e.keyCode!=13);
  });
}

/* The function to auto-total accesory quantities from individual size quantities */
function Totals(name) { // Takes a name so it can handle each type of accessory separately.
  var total=0;

  /* Add to the total the value of each selection box (dropdown) named for the qty of the given name */
  $("select[name*='"+name+"_qty']").each(function() {
    total+=parseInt(this.value);
  });
  $("#"+name+"_qty_total").html(total); // Update the contents of the appropraite totals area
}

/* The function to toggle the accessories and main forms' visibilities */
function ToggleAccessories() {
  if (document.entry.accessories.checked) {
    document.getElementById("accForm").style.display="inline"; // Show the accessories form.
    document.getElementById("mainForm").style.display="none"; // Hide the main form.
  }
  else {
    document.getElementById("accForm").style.display="none"; // Hide the accessories form.
    document.getElementById("mainForm").style.display="inline"; // Show the main form.
  }
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
  source: customerList,
  autoFocus: true,

  /* When an autocomplete item is selected, update the customer name field and the customer number field */
  select: function(e, ui) {
    document.entry.c_name.value=ui.item.label; // Just the name, not the city.
    document.entry.c_num.value=ui.item.value; // Customer number auto-fill
    UpdateCustomer(ui.item); // Update the customer's shipping and billing optionsnote
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

    /* While items are being highlighted, the field will show the name and city, but the city will be removed when an item is selected */
    document.entry.c_name.value=ui.item.label+" ("+ui.item.city+")";
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

/* The function that fills the styles for complete outfits when the coat style field blurs */
function FillComplete() {
  if (!document.entry.complete.checked) { return false; } // Do nothing if it's not a complete outfit.
  req=Request(); // Start a new data request.
  req.open("GET", "getStyles.php?style="+this.value); // Point the request to the getStyles.php page and pass it the selected style.
  req.onreadystatechange=HandleXHR; // Tell it what function to call when the request's state (sending, receiving, done, etc.) changes
  req.type="StyleReq"; // Mark this as a style request so the HandleXHR function can deal with it as well as other request types
  req.send(null); // Send the request
}

/* Given a customer number, look through the customerList array to find the matching customer and update the customer name field */
function GetCustomer(c_num) {

  /* Don't continue if the given number is simply empty (i.e. no number was entered when the box left focus) */
  if (c_num=="") { return false; }

  /* Search the customerList array for one matching the fiven customer number and use that item's name */
  for (i in customerList) {
    if (customerList[i].value==c_num) {
      document.entry.c_name.value=customerList[i].label;
      
      /* If found, update the customer's default shipping and billing info */
      UpdateCustomer(customerList[i]);
      
      return true;
    }
  }

  /* If no customer with that ID is found, display that message in the customer name field */
  document.entry.c_name.value="Customer Not Found";
  return false;
}

/* The function to update the customer's default billing and shipping choices */
function UpdateCustomer(cust) {

  /* Select their shipping method */
  var list=document.entry.d_type;
  for (i=0; i<list.options.length; ++i) {
    if (list.options[i].value==cust.shipping) { list.selectedIndex=i; break; }
  }
  
  /* Select their billing method */
  var list=document.entry.b_type;
  for (i=0; i<list.options.length; ++i) {
    if (list.options[i].value==cust.billing) { list.selectedIndex=i; break; }
  }
  
}

/* Load the styles from the database */
<?php

  /* All the different types of items that need styles */
  $types=["coat", "pants", "shirt", "shoe", "tie", "Hankie", "sash", "vest", "Glove", "Cane"];
  /* For each of these types... */

  foreach ($types as $type) {

    /* Get the rows from the t-price table that are for that type and order them by number */
    $q="SELECT * FROM `t-price` WHERE `P-Type`='".$type."' ORDER BY `P-STYLE`";
    $query=mysql_query($q);
    if ($query) {
      
      /* Echo a Javascript array for the given type */
      echo $type."Styles=[";

      /* For each table row, create a Javascript object with a value of the style number and a label that icludes the style number as well
         as its description. These will be used by the autocomplete lists */
      $out="";
      while ($row = mysql_fetch_assoc($query)) {
        $row=array_map("addslashes", $row);
  
        if ($out!="") { $out.=", "; }
        $out.='{value: "'.$row["P-STYLE"].'", label: "'.$row["P-STYLE"].' - '.$row["P-DESC"].'"}';
      }
      echo $out."];"; // Echo the objects and end the array.
    }
?>

    /* Set the appropriate fields to take their autocomplete data from these array */
    $(function() {

      /* The .add() here also makes the accessories form fields autocomplete fields where appropriate */
      $("#<?php echo $type; ?>_style").add("#<?php echo $type; ?>_a_style").autocomplete({
        source: function(req, response) { 

        /* Make a pattern out of whatever the person typed */
        var re = $.ui.autocomplete.escapeRegex(req.term); 

        /* The ^ in a Regular Expression pattern means "at the beginning of the text" */
        var matcher = new RegExp( "^" + re, "i" ); 
    
        /* Respond with only those items in the itemStyles array whose labels match that pattern */
        response($.grep( <?php echo $type; ?>Styles, function(item){ 
            return matcher.test(item.label); }) ); 
       },

    autoFocus: true
      });
    });
    
    /* Make the Date of Use box a calendar picker */
    $(function() {
      $("#use_date").datepicker({
        minDate: new Date(),
        onSelect: function(date, calendar) { this.focus(); } // When a date is picked from the calendar, focus back on the field.
      });
      document.getElementById("use_date").onblur=FormatDate; // When the Date of Use box loses focus, auto-fill the year.
    });

/* The function to auto-fill the year */
function FormatDate() {
  var mm, dd, yy;
  var now=new Date();
  var val=this.value;
  
  if (val=="") { document.entry.full_use_date.value=""; return false; } // If the field is empty, clear the hidden field and do nothing more.
  
  var date=Date.parse(val); // Attempt to parse the date first.
  if (isNaN(date)) { // If it's Not a Number, the parse failed, most likely because the date had no year included.
    val=val.split("/");
    mm=parseInt(val[0]); // Get the month as an int
    dd=parseInt(val[1]); // Get the date as an int
    if (mm<now.getMonth()+1 || (mm==now.getMonth()+1 && dd<now.getDate())) { // If it's a previous month or day, add 1 to the year.
      yy=now.getFullYear()+1;
    }
    else { yy=now.getFullYear(); } // If not, use the current year.
    val=val.join("/")+"/"+yy; // Combine the pieces of the date, including the added year.
  }
  document.entry.full_use_date.value=val; // Put the full date, with year, in the hidden form field.
}    

<?php
  } // End the item styles loop.
  /* Disconnect from the database. This should happen automatically, but just in case, we do it manually */
  mysql_close($link);
?>