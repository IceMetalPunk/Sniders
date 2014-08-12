<html>
  <head>
    <title>Price Maintenance</title>

    <!-- Main styles -->
    <link rel="stylesheet" href="styles.css" />

    <!-- Styles for autocomplete elements -->
    <link rel="stylesheet" href="jquery-ui.css" />
    <link rel="stylesheet" href="jquery-style.css" />

    <!-- jQuery library and its Autocomplete extension -->
    <script type="text/javascript" src="jquery-1.9.1.js"></script>
    <script type="text/javascript" src="jquery-ui.js"></script>

    <!-- Main Javascript library, including data pulled from the databases, hence why it must be in .php format -->
    <!-- The time() is just to give it a unique filename each time to avoid cacheing the data -->
    <script type="text/javascript" src="scripts.php?<?php echo time(); ?>"></script>
    
    <!-- Pricing-specific scripts -->
    <script type="text/javascript" src="pricingScripts.php?<?php echo time(); ?>"></script>
  </head>
  <body onload="PLInit()">
    <span style="float:left">
      <button onclick="PLToggleAdd()" accesskey="A"><u>A</u>dd</button> <button onclick="PLToggleChange()" accesskey="H">C<u>h</u>ange</button>
    </span>
    <span style="float:right">
      <a href="index.php"><img src="logo.png" /></a>
    </span><br clear="both" />

    <div style='display:none' id='addSection'>
      <form name="addForm" action="addPricing.php" method="post">
        <input type='text' name='type' placeholder="Item Type" />
        <input type='text' name='style' placeholder="Item Style" />
        <input type='text' name='description' placeholder="Item Description" /><br />
        <h3>Pricing</h3>
        <table>
          <tr>
            <td>Complete</td>
            <td>$<input type='text' name='compPrice' value="0" /></td>
          </tr>
          <tr>
            <td>Individual</td>
            <td>$<input type='text' name='individualPrice' value="0" /></td>
          </tr>
          <tr>
            <td>Upcharge</td>
            <td>$<input type='text' name='upCharge' value="0" /></td>
          </tr>
        </table>
        
        <h3>Matching Items</h3>
        <table>
          <tr>
            <td>Pants</td>
            <td><input type='text' name='matchPants' /></td>
            <td>Vest</td>
            <td><input type='text' name='matchVest' /></td>
            <td>Sash</td>
            <td><input type='text' name='matchSash' /></td>
          </tr>
          
          <tr>
            <td>Tie</td>
            <td><input type='text' name='matchTie' /></td>
            <td>Shirt</td>
            <td><input type='text' name='matchShirt' /></td>
          </tr>
          <tr><td colSpan='4'>&nbsp;</td></tr>
          <tr>
            <td>Price Listing Options</td>
            <td><select name='PLType'><option value="2" selected>Price List + Validation</option><option value="0">Price List Only</option><option value="1">No Price List, No Validation</option><option value="3">Validation Only</option></select></td>
          </tr>
        </table>
        <button type="submit" name='sub'><u>C</u>onfirm</button>
      </form>
    </div>
    <div style='display:none' id='changeSection'>
      <form name="changeForm" onsubmit="return ConfirmChange()" action="changePricing.php" method="post">
        <input type='text' name='type' placeholder="Item Type" />
        <input type='text' name='style' placeholder="Item Style" />
          <input type='checkbox' name='group' id='group' value='1' onchange="PLToggleGroup()" /><label for='group'>Group Update</label><br />
        <input type='text' name='description' placeholder="Item Description" /><br />
        <h3>Pricing</h3>
        <table>
          <tr>
            <td>Complete</td>
            <td>$<input type='text' name='compPrice' value="0" /></td>
          </tr>
          <tr>
            <td>Individual</td>
            <td>$<input type='text' name='individualPrice' value="0" /></td>
          </tr>
          <tr>
            <td>Upcharge</td>
            <td>$<input type='text' name='upCharge' value="0" /></td>
          </tr>
        </table>
        
        <span id="changeDefaults">
        <h3>Matching Items</h3>
        <table>
          <tr>
            <td>Pants</td>
            <td><input type='text' name='matchPants' /></td>
            <td>Vest</td>
            <td><input type='text' name='matchVest' /></td>
            <td>Sash</td>
            <td><input type='text' name='matchSash' /></td>
          </tr>
          
          <tr>
            <td>Tie</td>
            <td><input type='text' name='matchTie' /></td>
            <td>Shirt</td>
            <td><input type='text' name='matchShirt' /></td>
          </tr>
          <tr><td colSpan='4'>&nbsp;</td></tr>
          <tr>
            <td>Price Listing Options</td>
            <td><select name='PLType'><option value="2" selected>Price List + Validation</option><option value="0">Price List Only</option><option value="1">No Price List, No Validation</option><option value="3">Validation Only</option></select></td>
          </tr>
        </table>
        </span>
        <span id="confirmationMessage"></span><br/>
        <button type="submit" name='sub'><u>U</u>pdate</button>      
      </form>
    </div>
    </form>
  </body> 
</html>