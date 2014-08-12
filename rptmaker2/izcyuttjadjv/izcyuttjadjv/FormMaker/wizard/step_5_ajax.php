<?php
    session_start();
    require 'lib.php';
   if(isset($_POST['remove_details']) && $_POST['remove_details'] == 'true') 
   {
       unset ($_SESSION['form_details_table']);
       unset ($_SESSION['form_details_column']);
       unset ($_SESSION['form_details_unique']);
       echo "true";
   }
   else if(isset($_POST['get_columns']))
   {
      $table = $_POST['get_columns'];

      $result = query("show columns from $table");
      $arr = array();
      while($row = mysql_fetch_array($result))
      {
          $arr[] = $row['Field'];
      }
      echo json_encode($arr);
   }
   else if(isset($_POST['table']) && isset($_POST['field'])&& isset($_POST['pri']))
   {
       $_SESSION['form_details_table'] = $_POST['table'];
       $_SESSION['form_details_column'] = $_POST['field'];
       $_SESSION['form_details_unique'] = $_POST['pri'];
       echo "true";
   }
   else 
   {
       echo "false";
   }
   

  
?>
