<!doctype html>
 
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>jQuery UI Accordion - Default functionality</title>
  <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/sunny/jquery-ui.css" />
  <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
  
  <script>
  $(function() {
    $( "#accordion" ).accordion();
  });
  </script>
</head>
<body>
 
<div id="accordion">
  <h3>Report Style</h3>
  <div>
    <p>
    Mauris mauris ante, blandit et, ultrices a, suscipit eget, quam. Integer
    ut neque. Vivamus nisi metus, molestie vel, gravida in, condimentum sit
    amet, nunc. Nam a nibh. Donec suscipit eros. Nam mi. Proin viverra leo ut
    odio. Curabitur malesuada. Vestibulum a velit eu ante scelerisque vulputate.
    </p>
  </div>
  <h3>Section 2</h3>
  <div>
    <p>
    Sed non urna. Donec et ante. Phasellus eu ligula. Vestibulum sit amet
    purus. Vivamus hendrerit, dolor at aliquet laoreet, mauris turpis porttitor
    velit, faucibus interdum tellus libero ac justo. Vivamus non quam. In
    suscipit faucibus urna.
    </p>
  </div>
  <h3>Section 3</h3>
  <div>
    <p>
    Nam enim risus, molestie et, porta ac, aliquam ac, risus. Quisque lobortis.
    Phasellus pellentesque purus in massa. Aenean in pede. Phasellus ac libero
    ac tellus pellentesque semper. Sed ac felis. Sed commodo, magna quis
    lacinia ornare, quam ante aliquam nisi, eu iaculis leo purus venenatis dui.
    </p>
    <ul>
      <li>List item one</li>
      <li>List item two</li>
      <li>List item three</li>
    </ul>
  </div>
  <h3>Section 4</h3>
  <div>
    <p>
    <Table id="security" width="50%" height="100%" align="left">
      <tr>
        <td>
        <input type="checkbox" id="security" class="security" <?php if($_SESSION["security"]=="enabled") echo "checked"; ?> />
        Password protect generated report
        <td/>
      </tr>
      <tr>
        <td>
        Admin User Name 
      </td>
      <td> : </td>
      <td>
        <input type="text" value="<?php  echo $SESSION["sec_Username"]; ?>" id="sec_Username" ?>
        </td>
      </tr>
      <tr>
        <td>
        Admin Password :
      </td>
      <td>:</td>
      <td>
        <input type="text" value="<?php  echo $SESSION["$sec_pass"]; ?>" id="$sec_pass" ?>
      </td>
        

      </tr>



    </table>
    
    
  </div>
</div>
 
 
</body>
</html>