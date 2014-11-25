<html>
  <head>
    <title>Customer Management</title>

    <!-- Main styles -->
    <link rel="stylesheet" href="styles.css" />

    <!-- Styles for autocomplete elements -->
    <link rel="stylesheet" href="jquery-ui.css" />
    <link rel="stylesheet" href="jquery-style.css" />

    <!-- jQuery library and its Autocomplete extension -->
    <script type="text/javascript" src="jquery-1.9.1.js"></script>
    <script type="text/javascript" src="jquery-ui.js"></script>

    <!-- Customer maintainance Javascript library, including data pulled from the databases, hence why it must be in .php format -->
    <script type="text/javascript" src="customerScripts.php?<?php echo time(); ?>"></script>
    
  </head>
  <body onLoad="CustomerInit()">
    <span style="float:left">
    <!-- The buttons -->
    <button type="button" accesskey="A" onClick="Show('addSection', document.entry['C-NAME']); MoveAccessKey(document.entry.nextButton, 'N')"><u>A</u>dd</button> <button type="button" accesskey="C" onClick="Show('changeSection', document.entry.c_num); MoveAccessKey(document.entry.lookup, 'L')"><u>C</u>hange</button> <button type="button" accesskey="D" onClick="Show('deleteSection', document.entry['c_delnum']); MoveAccessKey(document.entry.remove, 'R')"><u>D</u>elete</button>
    </span>
    <!-- Logo that links to the main menu -->
    <a href="index.php"><span style="float:right"><img src="logo.png" /></span></a><br clear="both" />
    
    <form name="entry" method="post" action="manageCustomers.php">
    <!-- The hidden field that will change its value according to the action being performed (add, change, delete). This gets
         passed to the processing script so it can determine how to handle the data it's given. -->
    <input type="hidden" name="act" value="" />
    
		<!-- The "Delete Customer" section -->
		<div id="deleteSection" class="hiddenSection">
        <input type="text" name="c_delnum" class="numOnly" placeholder="Customer Number" maxlength=5 size=15 /> <input type="text" id="c_delname" name="c_delname" size=40 maxlength=40 placeholder="Customer Name" />
        <button type="submit" name="remove"><u>R</u>emove</button>			
		</div>
		
    <!-- The "Change Customer" section, first only showing the box to enter the customer name or ID -->
    <div id="changeSection" class="hiddenSection">
        <input type="text" name="c_num" class="numOnly" placeholder="Customer Number" maxlength=5 size=15 /> <input type="text" id="c_name" name="c_name" size=40 maxlength=40 placeholder="Customer Name" />
        <button type="button" onClick="Lookup()" name="lookup"><u>L</u>ookup</button>
    </div>

    <!-- The "Add Customer" and generic data entry section -->
      <div id="addSection" class="hiddenSection">
        <h3>Add Customer</h3>
        <table class="entryTable">
          <tr>
            <td>Name</td>
            <td colSpan='5'><input type="text" maxlength=40 size=30 name="C-NAME" /></td>
          </tr>
          <tr>
            <td>Address Line 1</td>
            <td colSpan='5'><input type="text" name="C-ADDR1" maxlength=60 size=30 /></td>
          </tr>
          <tr>
            <td>Address Line 2</td>
            <td colSpan='5'><input type="text" name="C-ADDR2" maxlength=60 size=30 /></td>
          </tr>
          <tr>
            <td>City</td>
            <td><input name="C-CITY" type="text" maxlength=25 size=30 /></td>
            <td>State</td>
            <td><input name="C-STATE" class="state_choice" type="text" maxlength=2 size=2 /></td>
            <td>Zip</td>
            <td><input name="C-ZIP" type="text" class="numOnly" maxlength=5 size=5 /></td>
          </tr>
          <tr>
            <td colSpan='6' style="color:#555555; border-bottom:1px solid #555555">Shipping Address (If Different)</td>
          </tr>
          <tr>
            <td>Address Line 1</td>
            <td colSpan='5'><input type="text" name="C-SADDR1" maxlength=60 size=30 /></td>
          </tr>
          <tr>
            <td>Address Line 2</td>
            <td colSpan='5'><input type="text" name="C-SADDR2" maxlength=60 size=30 /></td>
          </tr>
          <tr>
            <td>City</td>
            <td><input name="C-SCITY" type="text" maxlength=25 size=30 /></td>
            <td>State</td>
            <td><input name="C-SSTATE" class="state_choice" type="text" maxlength=2 size=2 /></td>
            <td>Zip</td>
            <td><input name="C-SZIP" type="text" class="numOnly" maxlength=5 size=5 /></td>
          </tr>
          <tr>
            <td>Contact Name</td>
            <td><input name="C-CONTACT" type="text" maxlength=40 size=30 /></td>
            <td>Title</td>
            <td><input name="C-TITLE" type="text" size=30 /></td>
          </tr>
          <tr>
            <td>Phone</td>
            <td><input type="text" maxlength=12 size=12 class="phoneNum" name="C-PHONE" /></td>
            <td>Cell Phone</td>
            <td><input type="text" maxlength=12 size=12 class="phoneNum" name="C-PHONE2" /></td>
          </tr>
        </table>
        <button name="nextButton" type="button" class="access" onClick="Show('addSection2', document.entry.shipping); MoveAccessKey(document.entry.sub, 'R')"><u>N</u>ext &gt;</button>
      </div>
      <div id="addSection2" class="hiddenSection">
        <h3>Add Customer</h3>
        <table class="entryTable">
          <tr>
            <td>Shipping Method</td>
            <td colSpan='3'>
              <select name="c-SHIP-METHOD">
                <option value="101">Delivery</option>
                <option value="102">Pickup</option>
                <option value="103">UPS</option>
                <option value="104">UPS Priority</option>
                <option value="105">UPS Air</option>
                <option value="106">FedEx</option>
                <option value="107">Delivery Today</option>
                <option value="108">Pickup Today</option>
                <option value="109">Other</option>
              </select>
            </td>
          </tr>
          <tr>
            <td>Delivery Route</td>
            <td><input name="C-ROUTE" type="text" size=30 /></td>
            <td colSpan='2'><input type="checkbox" name="C-DEL-SURCHARGE" value="yes" /> Delivery Surcharge?</td>
          </tr>
          <tr>
            <td>Billing</td>
            <td colSpan='3'><select name="C-BILLING">
                <option value="203">COD</option>
                <option value="204">COD-Cash</option>
                <option value="201">Standard</option>
                <option value="202">Standard-Discount</option>
                <option value="208">Standard-Discount 2</option>
                <option value="207">Fashion Show</option>
                <option value="209">Replacement</option>
                <option value="205">Try-On</option>
              </select>
            </td>
          </tr>
          <tr>
            <td>Discount %</td>
            <td><input type="text" name="C-DISCNT-PCT" class="numOnly" value="0.00" maxlength=6 size=5 /></td>
            <td>Shoe Discount %</td>
            <td><input type="text" name="C-DISC-SHOE" class="numOnly" value="0.00" maxlength=6 size=5 /></td>
          </tr>
          <tr>
            <td><input name="C-ACTIVE" type="checkbox" value="1" checked /> Active</td>
            <!--<td colSpan='3'><input name="C-DISCNT" type="checkbox" value="yes" /> Discount</td>-->
          </tr>
        </table>
        <button type="submit" name="sub" class="access"><u>R</u>eview</button>
      </div>
    </form>
  </body>
</html>