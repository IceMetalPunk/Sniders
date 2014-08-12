<?php
    session_start();
    require 'lib.php';
    $desc = $_SESSION['form_desc'];
    $column;
    if(empty($_POST['column']))
    {
        echo 'false';
//        //session_destroy();
//        foreach($_SESSION['form_desc'] as $key=>$val)
//        {
//            echo $key.'--'.$val['REFERENCED_TABLE_NAME'].'--'.$val['REFERENCED_COLUMN_NAME'].'--'.$val['TextField'].'<br/>';
//        }
        exit();
    }
    else
        $column = $_POST['column'];
   
   if(isset($_POST['remove_relation']) && $_POST['remove_relation'] == 'true') 
   {
       unset ($desc[$column]['REFERENCED_TABLE_NAME']);
       unset ($desc[$column]['REFERENCED_COLUMN_NAME']);
       unset ($desc[$column]['TextField']);
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
   else if(array_key_exists($column, $desc))
   {
        if(isset ($_POST['msg']))
        $desc[$column]['validation']['msg'] = $_POST['msg'];
        if(isset ($_POST['regx']))
            $desc[$column]['validation']['regx'] = $_POST['regx'];
        if(isset ($_POST['special_char']))
            $desc[$column]['validation']['special_char'] = $_POST['special_char'];

        if(isset ($_POST['from']))
            $desc[$column]['validation']['from'] = $_POST['from'];
        if(isset ($_POST['to']))
            $desc[$column]['validation']['to'] = $_POST['to'];
        
         if(isset ($_POST['regx_type']))
            $desc[$column]['validation']['regx_type'] = $_POST['regx_type'];
        
        if(isset ($_POST['REFERENCED_TABLE_NAME']))
            $desc[$column]['REFERENCED_TABLE_NAME'] = $_POST['REFERENCED_TABLE_NAME'];
        if(isset ($_POST['REFERENCED_COLUMN_NAME']))
            $desc[$column]['REFERENCED_COLUMN_NAME'] = $_POST['REFERENCED_COLUMN_NAME'];
        if(isset ($_POST['TextField']))
            $desc[$column]['TextField'] = $_POST['TextField'];
        
        
        
        echo 'true';
   }
    //save settings into  session
    $_SESSION['form_desc'] = $desc;
?>
