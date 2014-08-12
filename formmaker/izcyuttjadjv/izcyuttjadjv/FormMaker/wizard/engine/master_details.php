<?php 
    require_once 'config.php';
    require_once 'lib.php';
    // --- deserialization general variables
    $message = '';
    $error = false;
    $in_insert = $_REQUEST['index'] == '-1'?true:false;
    $in_insert_details = $_REQUEST['p'] == '-1'?true:false;
    $host = decode($host);
    $user = decode($user);
    $pass = decode($pass);
    $insert_only = decode($permission) == "100"?true:false;
    $permission = str_split(decode($permission));
    $unique = decode($unique);
    $db = decode($db);
    $table = decode($table);
    $fields = decode($fields);
    $desc = decode($desc);
    $layout = decode($layout);
    $style_name = decode($style_name);
    $title = decode($title);
    $form_desc = decode($form_desc);
    $date_created = decode($date_created);
    $file_name = decode($file_name);
    $records_per_page = decode($records_per_page);  
    $sql = decode($sql);
    $data_source = decode($data_source);
    
    //details from pararamters
    $desc_details = decode($desc_details);
    $table_details = decode($details_table);
    $column_details = decode($details_column);
    $unique_details = decode($details_unique);
    
    //------------------------------------------------------------
    //get permissions insert,update and delete
    $insert = $permission[0] == '1'?true:false;
    $update = $permission[1] == '1'?true:false;
    $delete = $permission[2] == '1'?true:false;
  
    

    
             //---- to avoid SQL keywords----------------
    $select_fields = implode(array_keys($desc),', ');
    $temp_arr = explode(',', $select_fields);
    $select_fields = array();
    $select_fields_insert = array();
    foreach($temp_arr as $key)
    {
        $select_fields_insert[] = $table.".".trim($key);
        if(strpos($desc[trim($key)]['Type'], 'bit') === false)
             $select_fields[] = '`'.$table."`.`".trim($key).'`';
        else//to handle MYSqL BUG http://bugs.mysql.com/bug.php?id=43670
             $select_fields[] = '`'.$table."`.".trim($key).'+0 as '.$key;    
    }
    $select_fields = implode($select_fields,', ');
    $select_fields_insert = implode($select_fields_insert,', ');
   //------------------------------------------
    
  
    
        
   //---- to avoid SQL keywords for details ----------------
    $fields_from_desc = array_keys($desc_details);
    $fields_from_desc[] = $column_details;
    $select_fields_details = implode($fields_from_desc,', ');
    $temp_arr_details = explode(',', $select_fields_details);
    $select_fields_details = array();
    $select_fields_insert_details = array();
    foreach($temp_arr_details as $key)
    {
        $select_fields_insert_details[] = $table_details.".".trim($key);
        if(strpos($desc[trim($key)]['Type'], 'bit') === false)
             $select_fields_details[] = '`'.$table_details."`.`".trim($key).'`';
        else//to handle MYSqL BUG http://bugs.mysql.com/bug.php?id=43670
             $select_fields_details[] = '`'.$table_details."`.".trim($key).'+0 as '.$key;    
    }
    $select_fields_details = implode($select_fields_details,', ');
    $select_fields_insert_details = implode($select_fields_insert_details,', ');
   //------------------------------------------
    
    
    $count = mysql_fetch_array(query("SELECT COUNT(*) FROM `$table`")); // get all fields count
    $count = $count[0];
    
    

    
 
    
    
   $index = 0;
   if(isset ($_REQUEST['index']) &&  !$in_insert)
       $index = intval ($_REQUEST['index']) -1;
    $fake_index = intval ($_REQUEST['index']);
    
    $p = intval($_GET['p']);
    $navigate = ($p - 1) * $records_per_page;
    $fake_navigate = ($p) * $records_per_page;
    
    
    
    
     $unique_details_table = array();
     $columns_details_table = mysql_query("SHOW COLUMNS FROM `$table_details`");
     while($field = mysql_fetch_array($columns_details_table))
      {
          if((strtoupper($field['Key']) == "PRI" || strtoupper($field['Key']) == "UNI") && $field['Type'] !== 'bigint(20) unsigned') //get unique keys and avoid serial data type
             $unique_details_table[] = $field['Field'];  
      }
    //echo var_dump($unique_details_table);
    
    
    
   $data = query("SELECT $select_fields FROM `$table` LIMIT $index,1"); //get the current row

   $row = mysql_fetch_assoc($data); // fetch the record
   
   $rel_value = $row[$unique[0]];
   
   
 
    
    
    

   
   $data_details  = query("SELECT * FROM `$table_details` where CONCAT(`$column_details`)='$rel_value' LIMIT $navigate,$records_per_page");
  
    $count_details = query("SELECT COUNT(*) AS COUNT FROM `$table_details` where CONCAT(`$column_details`)='$rel_value'");
    $count_details = mysql_fetch_assoc($count_details);
    $count_details = $count_details['COUNT'];
    $pages = ceil($count_details / $records_per_page);
   
    
    
    
    //    if($insert_only && $_GET['index'] != '-1')
//       header("Location: index.php?p=1&index=-1");
//    
//    if(!$insert && $count == 0)
//       die('<div style="padding:10px; border:1px dotted red; color:red; margin:20px auto; font-family:Tahoma; font-size:12px; text-align:center; width:500px;">Table is empty and you don\'t have insert action permission!</div>');
//    
    
    if((isset ($_POST['insert']) && $insert) || ($count == 0 && $_GET['index'] != '-1')) // go to insert mode
       header("Location: index.php?p=1&index=-1");   
   
        
//         
    //security from sql injections
    if((isset ($_POST['first']) || !isset ($_GET['index']) || empty($_GET['index'])) || ((!is_numeric($_GET['index']) || intval($_GET['index']) > $count || intval($_GET['index']) < -1 || intval($_GET['index']) == 0) && $count != 0))   //got to main page
        header("Location: index.php?index=1&p=1");
    if(isset ($_POST['next'])) //got to next recore
        header("Location: index.php?p=1&index=".(intval($_GET['index'])+1));
    if(isset ($_POST['before'])) //got to previous recore
        header("Location: index.php?p=1&index=".(intval($_GET['index'])-1));
    if(isset ($_POST['last'])) // go to last index
       header("Location: index.php?p=1&index=".($count));
//   
//   
     if(isset ($_POST['cancel']) && $in_insert) // canceled insert mode
        header("Location: index.php?index=1&p=1");
    else if(isset ($_POST['cancel']) && $_GET['index'] != '-1') // to remove messages if usr click cancel
        header("Location: index.php?p=1&index=".$_GET['index']);

    
//   
//   
    //
    //
      // if(($insert_only && $_GET['p'] != '-1') || ($count_details == 0 && $insert && !$in_insert_details))
     //  header("Location: index.php?p=-1");
    
    //if(!$insert && $count_details == 0)
     //  die('<div style="padding:10px; border:1px dotted red; color:red; margin:20px auto; font-family:Tahoma; font-size:12px; text-align:center; width:500px;">Table is empty and you don\'t have insert action permission!</div>'); 
    //|| ($count_details == 0 && $_GET['p'] != '-1')) && $count_details != 0
   if(((isset ($_POST['insert_details']) && $insert) )) // go to insert mode
      header("Location: index.php?p=-1&index=$fake_index"); 
   
   //security from sql injections
    if((isset ($_POST['first_details']) || !isset ($_GET['p']) || empty($_GET['p'])) || ((!is_numeric($_GET['p']) || intval($_GET['p']) > $count_details || intval($_GET['p']) < -1 || intval($_GET['p']) == 0) && $count_details != 0))   //got to main page
        header("Location: index.php?index=$fake_index&p=1");
    if(isset ($_POST['next_details'])) //got to next recore
        header("Location: index.php?index=$fake_index&p=".(intval($_GET['p'])+1));
    if(isset ($_POST['prev_details'])) //got to previous recore
        header("Location: index.php?index=$fake_index&p=".(intval($_GET['p'])-1));
    if(isset ($_POST['last_details'])) // go to last index
        header("Location: index.php?index=$fake_index&p=".($pages));
//    
    if(isset ($_POST['cancel_details']) && $in_insert_details)
        header("Location: index.php?p=1&index=$fake_index");
    else if(isset ($_POST['cancel_details'])) // cancel
       header("Location: index.php?index=$fake_index&p=".(intval($_GET['p'])));
   

    
    //


   
  

   //delete
   if(isset ($_POST['delete']) && $_POST['delete'] == 'Delete')
   {
        
        $data = query("SELECT $select_fields FROM `$table` LIMIT $index,1"); //get the current row
        $row_delete = mysql_fetch_array($data); // fetch the record
        $keys = array_keys($row_delete);
        $conditions = array();
        foreach($unique as $key)
        {
             if($desc[$key]['Type'] == 'float'  || $desc[$key]['Type'] == 'double' || $desc[$key]['Type'] == 'real')
                 $conditions[] = 'CONCAT(`'.$table.'`.`'.$key.'`) = \''.$row_delete[$key].'\'';
             else
                 $conditions[] = '`'.$table.'`.`'.$key.'` = \''.$row_delete[$key].'\'';
        }
        $result = query("delete from `$table` where ".  implode(' and ', $conditions) ." limit 1");
        if($result)
        {
            $index++;
            if($index == $count)
                $index = $count - 1;
            header("Location: index.php?p=1&index=$index&msg=deleted"); 
        }
        else        
        {
            $error = true;
            $mysql_error = mysql_error();
            if($mysql_error)
                $message = $mysql_error;
            else
              $message = 'No rows affected.' ; 
        }
        
        
   }
   
    //delete details
    if(isset($_POST['deletebtn_details']) && !empty($_POST['deletebtn_details']))
    {
        $indexes_to_delete = $_POST['delete_details'];
        $selected_records = count($indexes_to_delete);
        $delete_items_arr = array();
        if($selected_records > 0)
        {
            $data  = query("SELECT * FROM `$table_details` where CONCAT(`$column_details`)='$rel_value' LIMIT $navigate,$records_per_page");

            $i = $navigate;
            while($row_delete_details = mysql_fetch_array($data))
            {
                $i++;
                if(in_array($i, $indexes_to_delete))
                {
                   // $unique_temp = array();
                    ///$unique_temp[] = $unique_details;
                    foreach ($unique_details_table as $key)
                    {
                         $delete_items_arr[$key][] = $row_delete_details[$key];
                    }
                }
            }
            
            $delete_statemet_arr = array();
            foreach ($delete_items_arr as $key=>$val)
            {
                if($desc[$key]['Type'] == 'float' || strpos($desc[$key]['Type'], 'int') > -1 || $desc[$key]['Type'] == 'double' || $desc[$key]['Type'] == 'real')
                    $delete_statemet_arr[] = "CONCAT(`$table_details`.`$key`) IN('".implode('\', \'', $val) ."')";
                else
                    $delete_statemet_arr[] = "`$table_details`.`$key` IN('".implode('\', \'', $val) ."')";   
            }
            query("Delete from `$table_details` where ".implode(' AND ',$delete_statemet_arr)." limit $selected_records");
            //echo "Delete from `$table_details` where ".implode(' AND ',$delete_statemet_arr)." limit $selected_records";
            $affected_rows = mysql_affected_rows();
            if($affected_rows > 0)
            {
                $error = false;
                $case_row = 'row';
                if($affected_rows > 1)
                  $case_row = 'rows';  
                $message = "$affected_rows $case_row Deleted Successfully."; //deleted successfully
            }
            else if(mysql_error())
            {
               $error = true;
               $message = mysql_error();
            }
        }
        else
        {
            $error = true;
            $message = "No rows selected.";
        }
    }
   
   
   
   //insert & update master
   if(isset ($_POST['save'])  && !empty ($_POST['save']) && $_POST['save'] == 'Save')
   {
       if($in_insert)//insert
       {
           $values = array();
           foreach ($desc as $key=>$val)
           {
              if($val['extra'] != 'auto_increment')
              {
                   if($val['Type'] == 'bit(1)')
                   {
                       $value = isset($_POST['form_'.$key])?'1':'0';
                       $values[$key] = "b'".$value."'";
                   }
                   else
                        $values[$key] = "'".addslashes($_POST['form_'.$key])."'";
              }
              
           }
           $values = implode(', ', $values);
           query("INSERT INTO `$table`($select_fields_insert) Values($values)");
           if(mysql_affected_rows() > 0)
           {
               if($insert_only)
                   header("Location: index.php?p=1&index=-1&msg=saved");
               else 
                   header("Location: index.php?p=1&index=".($count+1)."&msg=saved");
           }
            else
            {
                $error = true;
                $message = mysql_error();
            }
       }
       else{ //update
                $values = array();
               foreach ($desc as $key=>$val)
               {
                  if($val['Extra'] != 'auto_increment')
                  {
                        if($val['Type'] == 'bit(1)')
                        {
                            $value = isset($_POST['form_'.$key])?'1':'0';
                            $values[] .= '`'.$table.'`.`'.$key.'` = b\''.$value.'\''; 
                        }
                        else
                            $values[] .= '`'.$table.'`.`'.$key.'` = \''. addslashes($_POST['form_'.$key]).'\''; 
                  }
               }
           
           

            $data = query("SELECT $select_fields FROM `$table` LIMIT $index,1"); //get the current row
            $row = mysql_fetch_array($data); // fetch the record
            $keys = array_keys($row);
            $conditions = array();
            foreach($unique as $key)
            {
                 if($desc[$key]['Type'] == 'float'  || $desc[$key]['Type'] == 'double' || $desc[$key]['Type'] == 'real')
                     $conditions[] = 'CONCAT(`'.$table.'`.`'.$key.'`) = \''.$row[$key].'\'';
                 else
                     $conditions[] = '`'.$table.'`.`'.$key.'` = \''.$row[$key].'\'';
            }
           query("UPDATE `$table` SET ". implode(', ', $values) ." where  ".  implode(' and ', $conditions) ." limit 1");
           if(mysql_error())
           {
               $error = true;
               $message = mysql_error();
           }
           else
           {
                $index++;
                header("Location: index.php?p=1&index=$index&msg=updated");
           }
       }
   }
   
   
   
   //insert & update detail;
    if(isset($_POST['save_details']) && !empty($_POST['save_details']) && $_POST['save_details'] == 'Save' )
    {
        if($in_insert_details) //insert
        {
           $values = array();
           foreach ($desc_details as $key=>$val)
           {
              //get data from submitted form
               if($val['Type'] == 'bit(1)')
               {
                    $value = isset($_POST['form_'.$key])?'1':'0';
                    $values[$key] = "b'".$value."'";
               }
               else
                   $values[$key] = "'".addslashes($_POST['form_'.$key])."'";
              
           }
     
           $values[$column_details] = "'".$rel_value."'";
           
           $values = implode(', ', $values);
          
          query("INSERT INTO `$table_details`($select_fields_insert_details) Values($values)");
          // echo "INSERT INTO `$table_details`($select_fields_insert_details) Values($values)";
           if(mysql_affected_rows() > 0)
                header("Location: index.php?index=$fake_index&p=$pages&msg=saved"); //go to last page
            else
            {//error
                $error = true;
                $message = mysql_error();
            }
        }
        else //update
        {
             $i = $navigate;
             $data_update  = query("SELECT $select_fields_details FROM `$table_details` where CONCAT(`$column_details`)='$rel_value' LIMIT $navigate,$records_per_page");
             
            
              
             while($row_update = mysql_fetch_array($data_update))
             {
                   $i++;
                   $values = array();
                   $conditions = array();
                   foreach(array_keys($row_update) as $key)
                   {
                       
                        
                       if(in_array($key, $unique_details_table) && !is_numeric($key))
                        {
                           // if($desc[$key]['Type'] == 'float' || $desc[$key]['Type'] == 'double' || $desc[$key]['Type'] == 'real')
                                $conditions[] = 'CONCAT(`'.$table_details.'`.`'.$key.'`) = \''.$row_update[$key].'\'';
                         //   else
                           //     $conditions[] = "$table.`".$key."` = '".$row[$key]."'";
                        }
                   }
                   
                   foreach ($desc_details as $key=>$val)
                   {
                      if($val['Extra'] != 'auto_increment')
                      {
                            
                            if($val['Type'] == 'bit(1)')
                            {
                                $value = isset($_POST[$i.'_form_'.$key])?'1':'0';
                                $values[] .= '`'.$key.'` = b\''. $value.'\'';
                            }
                            else
                                $values[] .= '`'.$key.'` = \''. addslashes($_POST[$i.'_form_'.$key]).'\'';
                      } 
                   }
                  query("UPDATE `$table_details` SET ".  implode(', ', $values)." where ".implode(' AND ', $conditions));
                // echo "UPDATE `$table_details` SET ".  implode(', ', $values)." where ".implode(' AND ', $conditions);
             }
             if(mysql_error())
             {
                 $error = true;
                 $message = mysql_error();
             }
             else
             {
                 header("Location: index.php?index=$fake_index&p=$p&msg=updated");
             }
        }
    }
    
   

   //$data = query("SELECT $select_fields FROM `$table` LIMIT $index,1"); //get the current row
   //$row = mysql_fetch_array($data); // fetch the record
   
   $data_details  = query("SELECT * FROM `$table_details` where CONCAT(`$column_details`)='$rel_value'  LIMIT $navigate,$records_per_page");
     $count_details = query("SELECT COUNT(*) AS COUNT FROM `$table_details` where CONCAT(`$column_details`)='$rel_value'");
    $count_details = mysql_fetch_assoc($count_details);
    $count_details = $count_details['COUNT'];
    $pages = ceil($count_details / $records_per_page);
    
   //$data_details  = query("SELECT * FROM `$table_details` LIMIT $navigate,$records_per_page");
  
   ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title; ?></title>
<link href="<?php echo $style_name; ?>.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>

<script type="text/javascript" src="../../js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.core.min.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.datepicker.min.js"></script>
<script type="text/javascript" src="../../js/jquery-ui-timepicker-addon.js"></script>
<link rel="stylesheet" type="text/css" href="../../js/ui-lightness/jquery.ui.all.css" />
<link rel="stylesheet" type="text/css" href="../../js/ui-lightness/jquery.ui.datepicker.css" />

<style>
    .error_border{border:  1px solid red !important ;}
    /* css for timepicker */
    .ui-timepicker-div .ui-widget-header { margin-bottom: 8px; }
    .ui-timepicker-div dl { text-align: left; }
    .ui-timepicker-div dl dt { height: 25px; margin-bottom: -25px; }
    .ui-timepicker-div dl dd { margin: 0 10px 10px 65px; }
    .ui-timepicker-div td { font-size: 90%; }
    .ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }
    .ui-slider { position: relative; text-align: left; }
    .ui-slider .ui-slider-handle { position: absolute; z-index: 2; width: 1.2em; height: 1.2em; cursor: default; }
    .ui-slider .ui-slider-range { position: absolute; z-index: 1; font-size: .7em; display: block; border: 0; background-position: 0 0; }

    .ui-slider-horizontal { height: .8em; }
    .ui-slider-horizontal .ui-slider-handle { top: -.3em; margin-left: -.6em; }
    .ui-slider-horizontal .ui-slider-range { top: 0; height: 100%; }
    .ui-slider-horizontal .ui-slider-range-min { left: 0; }
    .ui-slider-horizontal .ui-slider-range-max { right: 0; }

    .ui-slider-vertical { width: .8em; height: 100px; }
    .ui-slider-vertical .ui-slider-handle { left: -.3em; margin-left: 0; margin-bottom: -.6em; }
    .ui-slider-vertical .ui-slider-range { left: 0; width: 100%; }
    .ui-slider-vertical .ui-slider-range-min { bottom: 0; }
    .ui-slider-vertical .ui-slider-range-max { top: 0; }
</style>

<script type="text/javascript">
    
        $(function(){
        $('.details_form').find('input[data_type=datetime], input[data_type=date], input[data_type=timestamp], input[data_type=time]').focus(function(e){
            e.preventDefault();
        });

        $('.details_form').find('input[data_type=datetime], input[data_type=date], input[data_type=timestamp], input[data_type=time]').each(function(){
            $(this).attr('autocomplete','off');
            var arr_from  = '';
            var arr_to = '';
            if($(this).attr('from') != 'undefined')
                arr_from = $(this).attr('from').split('/'); 
            
            if($(this).attr('to') != 'undefined')
                arr_to = $(this).attr('to').split('/'); 
                arr_from[0]--;
                arr_to[0]--;
                if($(this).attr('data_type') == 'time')
                    $(this).timepicker({showSecond: true,timeFormat:'hh:mm:ss'});
                else
                {
                    if(arr_from != '' && arr_to != '')
                    {
                        if($(this).attr('data_type') == 'date')
                            $(this).datepicker({changeMonth: true,changeYear: true,minDate:new Date(arr_from[2] , arr_from[0], arr_from[1]),maxDate:new Date(arr_to[2] , arr_to[0], arr_to[1]),dateFormat: 'yy-mm-dd'});
                        else
                           $(this).datetimepicker({showSecond: true,timeFormat:'hh:mm:ss',changeMonth: true,changeYear: true,minDate:new Date(arr_from[2] , arr_from[0], arr_from[1]),maxDate:new Date(arr_to[2] , arr_to[0], arr_to[1]),dateFormat: 'yy-mm-dd'}); 
                    }
                    else if(arr_from != '')
                    {
                        if($(this).attr('data_type') == 'date')
                            $(this).datepicker({changeMonth: true,changeYear: true,minDate:new Date(arr_from[2] , arr_from[0], arr_from[1]),dateFormat: 'yy-mm-dd'});
                        else
                            $(this).datetimepicker({showSecond: true,timeFormat:'hh:mm:ss', changeMonth: true,changeYear: true,minDate:new Date(arr_from[2] , arr_from[0], arr_from[1]),dateFormat: 'yy-mm-dd'});
                    }
                    else if(arr_to != '')
                    {
                         if($(this).attr('data_type') == 'date')
                            $(this).datepicker({changeMonth: true,changeYear: true,maxDate:new Date(arr_to[2] , arr_to[0], arr_to[1]),dateFormat: 'yy-mm-dd'});
                        else
                           $(this).datetimepicker({showSecond: true,timeFormat:'hh:mm:ss',changeMonth: true,changeYear: true,maxDate:new Date(arr_to[2] , arr_to[0], arr_to[1]),dateFormat: 'yy-mm-dd'}); 
                    }
                    else
                        {
                            if($(this).attr('data_type') == 'date')
                                $(this).datepicker({changeMonth: true,changeYear: true,dateFormat: 'yy-mm-dd'});    
                            else
                              $(this).datetimepicker({showSecond: true,timeFormat:'hh:mm:ss',changeMonth: true,changeYear: true,dateFormat: 'yy-mm-dd'});      
                        
                        }
                }
               
            
           
        });
        
        
        var remove_error = function(){
            if($.trim($(this).val()) != '')
            {
                $(this).removeClass('error_border');
                $(this).parent().children('span').remove();
            }
        }
        $('.details_form').find('select[relation=yes]').change(remove_error);
        $('.details_form').find('input[name*=form_],textarea').keyup(remove_error);
        $('.details_form').find('input[name*=form_]').change(remove_error);
        
        
        //save
        $('#btnSave_details').click(function(e){
            //e.preventDefault();
            
            $('.details_form').find('input[name*=form_],textarea[name*=form_]').removeClass('error_border');
            $('.details_form').find('input[name*=form_],textarea[name*=form_]').parent().children('span').remove();
            var valid = true;
            
            try {
                 $('.details_form').find('input[name*=form_],textarea[name*=form_]').each(function(){
                var type = new String($(this).attr('data_type')); 
                var value = $(this).val();
                var obj = $(this);
               
                if(($(this).attr('null') == '0' || $(this).attr('null') == '1') && $.trim($(this).val()) == '' && !($(this).attr('extra') == 'auto_increment'))
                {     
                     $(this).addClass('error_border');
                     if(valid)
                        alert('There are some fields required!');
                    valid = false; 
                }
               
               // validation allowed special characters
             if($(this).attr('special_char') && $(this).attr('special_char') != 'none' && $(this).attr('extra') != 'auto_increment' && $(this).attr('type') != 'radio' && $(this).attr('relation') != 'yes')
             {
                 var special_char = $(this).attr('special_char').split(',');
                  $.each(special_char, function(index,val){
               
                      if(value.indexOf(val) != -1 && val != '' && !Contains(obj.attr('special_char'), val))
                       {
                         $(this).addClass('error_border');
                         if(valid)
                            alert(obj.attr('msg'));
                        valid = false; 
                     }
                 });
             }
             //regular expresssion  validation
             if($.trim($(this).val()) != '' && $(this).attr('regx') && $(this).attr('type') != 'radio' && $(this).attr('relation') != 'yes')
             {
             if(!(new RegExp($(this).attr('regx'),'g').test($(this).val())) && $(this).parent().children('span').length == 0)
                 {
                        $(this).addClass('error_border');
                         if(valid)
                            alert(obj.attr('msg'));
                        valid = false; 
                 }
             }
 
                if((parseFloat($(this).val()) < parseFloat($(this).attr('from')) 
                    || parseFloat($(this).val()) > parseFloat($(this).attr('to')))  && $(this).attr('type') != 'radio' && $(this).attr('relation') != 'yes' 
                     )
                {
                     if(!Contains(type,'date'))
                        {
                          $(this).addClass('error_border');
                         if(valid)
                            alert(obj.attr('msg'));
                        valid = false; 
                        }
                }
                   
                if(Contains(type,'varchar') && $(this).attr('extra') != 'auto_increment' &&  $(this).attr('type') != 'radio' && $(this).attr('relation') != 'yes')
                {
                    //alert(type);
                    type = parseInt(type.substr(type.indexOf('(') +1).replace(')', ''));
                    if($(this).val().length > type)
                    {
                          $(this).addClass('error_border');
                         if(valid)
                            alert(obj.attr('msg'));
                        valid = false; 
                    }
                }
            
            if(Contains(type,'year') && $(this).attr('extra') != 'auto_increment' &&  $(this).attr('type') != 'radio' && $(this).attr('relation') != 'yes')
                {
                    var patt = /^[12][0-9][0-9][0-9]$/g;
                    if(!(patt.test($(this).val())))
                    {
                         $(this).addClass('error_border');
                         if(valid)
                            alert(obj.attr('msg'));
                        valid = false; 
                    }
                }
                
                
                    if($(this).attr('extra') != 'auto_increment' && $(this).attr('type') != 'radio' && $(this).attr('relation') != 'yes' && 
                    (
                        Contains(type,'int')
                        || Contains(type,'decimal')
                        || Contains(type,'double')
                        || Contains(type,'real')
                        || Contains(type,'float')
                    )){
                       
                        if(isNaN($(this).val()))
                        {
                             $(this).addClass('error_border');
                            if(valid)
                                alert(obj.attr('msg'));
                            valid = false; 
                        }
                    }
                
                    
            
            });
            
            
             $('.details_form').find('select[relation=yes]').each(function(){
                 if(($(this).attr('null') == '0' || $(this).attr('null') == '1') && $(this).val() == '0') //validate refrenced table relationship
                 {
                    
                     $(this).addClass('error_border');
                     if(valid)
                        alert('There are some fields required!');
                    valid = false; 
                
                 }
           
             });                                
             } catch (exception) { 
                                                  
             }

            if(!valid)
                e.preventDefault();
        });
     
        if(getParameterByName('index') == '-1')
        {
            $('input[extra=auto_increment]').val('auto increment(*)');
            $('input[data_type=datetime]').val('');
        }
    });
    
    function check_all(obj)
    {
        $('td input[name="delete_details[]"]').attr('checked',obj.checked);
    }
</script>
<script type="text/javascript">
    
    
    
    $(function(){
        $('.master_form').find('input[data_type=datetime]').focus(function(e){
            e.preventDefault();
        });
        
//        $('.err').click(function(){
//            $(this).slideUp('slow');
//        });
        
        $('.master_form').find('input[data_type=datetime], input[data_type=date], input[data_type=timestamp], input[data_type=time]').each(function(){
            $(this).attr('autocomplete','off');
            var arr_from  = '';
            var arr_to = '';
            if($(this).attr('from') != 'undefined')
                arr_from = $(this).attr('from').split('/'); 
            
            if($(this).attr('to') != 'undefined')
                arr_to = $(this).attr('to').split('/'); 
           arr_from[0]--;
           arr_to[0]--;
                if($(this).attr('data_type') == 'time')
                    $(this).timepicker({showSecond: true,timeFormat:'hh:mm:ss'});
                else
                {
                    if(arr_from != '' && arr_to != '')
                    {
                        if($(this).attr('data_type') == 'date')
                            $(this).datepicker({changeMonth: true,changeYear: true,minDate:new Date(arr_from[2] , arr_from[0], arr_from[1]),maxDate:new Date(arr_to[2] , arr_to[0], arr_to[1]),dateFormat: 'yy-mm-dd'});
                        else
                           $(this).datetimepicker({showSecond: true,timeFormat:'hh:mm:ss',changeMonth: true,changeYear: true,minDate:new Date(arr_from[2] , arr_from[0], arr_from[1]),maxDate:new Date(arr_to[2] , arr_to[0], arr_to[1]),dateFormat: 'yy-mm-dd'}); 
                    }
                    else if(arr_from != '')
                    {
                        if($(this).attr('data_type') == 'date')
                            $(this).datepicker({changeMonth: true,changeYear: true,minDate:new Date(arr_from[2] , arr_from[0], arr_from[1]),dateFormat: 'yy-mm-dd'});
                        else
                            $(this).datetimepicker({showSecond: true,timeFormat:'hh:mm:ss', changeMonth: true,changeYear: true,minDate:new Date(arr_from[2] , arr_from[0], arr_from[1]),dateFormat: 'yy-mm-dd'});
                    }
                    else if(arr_to != '')
                    {
                         if($(this).attr('data_type') == 'date')
                            $(this).datepicker({changeMonth: true,changeYear: true,maxDate:new Date(arr_to[2] , arr_to[0], arr_to[1]),dateFormat: 'yy-mm-dd'});
                        else
                           $(this).datetimepicker({showSecond: true,timeFormat:'hh:mm:ss',changeMonth: true,changeYear: true,maxDate:new Date(arr_to[2] , arr_to[0], arr_to[1]),dateFormat: 'yy-mm-dd'}); 
                    }
                    else
                        {
                            if($(this).attr('data_type') == 'date')
                                $(this).datepicker({changeMonth: true,changeYear: true,dateFormat: 'yy-mm-dd'});    
                            else
                              $(this).datetimepicker({showSecond: true,timeFormat:'hh:mm:ss',changeMonth: true,changeYear: true,dateFormat: 'yy-mm-dd'});      
                        
                        }
                }
               
            
           
        });
        
        
        var remove_error = function(){
            if($.trim($(this).val()) != '')
            {
                $(this).removeClass('error_border');
                $(this).parent().children('span').remove();
            }
        }
        $('.master_form').find('select[relation=yes]').change(remove_error);
        $('.master_form').find('input[name*=form_],textarea').keyup(remove_error);
        $('.master_form').find('input[name*=form_]').change(remove_error);
        
        
        //save
        $('#btnSave').click(function(e){
            //e.preventDefault();
            
            $('.master_form').find('input[name*=form_],textarea[name*=form_]').removeClass('error_border');
            $('.master_form').find('input[name*=form_],textarea[name*=form_]').parent().children('span').remove();
            var valid = true;
            try {
                  $('.master_form').find('input[name*=form_],textarea[name*=form_]').each(function(){
                var value = $(this).val();
                var obj = $(this);
               var type = new String($(this).attr('data_type'));
                if(($(this).attr('null') == '0' || $(this).attr('null') == '1') && $.trim($(this).val()) == '' && !($(this).attr('extra') == 'auto_increment'))
                {     
                     valid = false;
                     $(this).addClass('error_border');
                     $(this).parent().append('<span style="color:red;">required!</span>');
                }
               
               // validation allowed special characters
             if($(this).attr('special_char') && $(this).attr('extra') != 'auto_increment' && $(this).attr('type') != 'radio' && $(this).attr('relation') != 'yes')
             {
                 if($(this).attr('special_char') != 'none')
                     {
                     var special_char = $(this).attr('special_char').split(',');
                      $.each(special_char, function(index,val){

                        if(value.indexOf(val) != -1 && val != '' && !Contains(obj.attr('special_char'), val))
                         {
                             obj.parent().append('')
                             valid = false;
                             obj.addClass('error_border');
                             obj.parent().append('<span style="color:red;"><br />'+obj.attr('msg')+'</span>');
                         }
                     });
                     }
             }
             //regular expresssion  validation
             if($.trim($(this).val()) != '' && $(this).attr('regx') && $(this).attr('type') != 'radio' && $(this).attr('relation') != 'yes')
             {
             if(!(new RegExp($(this).attr('regx'),'g').test($(this).val())) && $(this).parent().children('span').length == 0)
                 {
                       
                        valid = false;
                         obj.addClass('error_border');
                         obj.parent().append('<span style="color:red;"><br />'+obj.attr('msg')+'</span>');
                 }
             }
                
                
                if( (parseFloat($(this).val()) < parseFloat($(this).attr('from')) 
                    || parseFloat($(this).val()) > parseFloat($(this).attr('to')))  && $(this).attr('type') != 'radio' && $(this).attr('relation') != 'yes' 
                     )
                {
                    if(!Contains(type,'date'))
                        {
                         valid = false;
                         obj.addClass('error_border');
                         if($(this).parent().children('span').length == 0)
                            obj.parent().append('<span style="color:red;"><br />'+obj.attr('msg')+'</span>');
                        }
           }
                
                    
                if(Contains(type,'varchar') && $(this).attr('extra') != 'auto_increment' &&  $(this).attr('type') != 'radio' && $(this).attr('relation') != 'yes')
                {
                    //alert(type);
                    type = parseInt(type.substr(type.indexOf('(') +1).replace(')', ''));
                    if($(this).val().length > type)
                    {
                         valid = false;
                         $(this).addClass('error_border');
                         if($(this).parent().children('span').length == 0)
                             $(this).parent().append('<span style="color:red;"><br />'+obj.attr('msg')+'</span>');
                    }
                }
            
            if(Contains(type,'year') && $(this).attr('extra') != 'auto_increment' &&  $(this).attr('type') != 'radio' && $(this).attr('relation') != 'yes')
                {
                    var patt = /^[12][0-9][0-9][0-9]$/g;
                    if(!(patt.test($(this).val())))
                    {
                         valid = false;
                        $(this).addClass('error_border');
                        if($(this).parent().children('span').length == 0)
                            $(this).parent().append('<span style="color:red;"><br />'+obj.attr('msg')+'</span>');
                    }
                }
                
                
                   if($(this).attr('extra') != 'auto_increment' && $(this).attr('type') != 'radio' && $(this).attr('relation') != 'yes' && 
                    (
                        Contains(type,'int')
                        || Contains(type,'decimal')
                        || Contains(type,'double')
                        || Contains(type,'real')
                        || Contains(type,'float')
                    ))
                    {
                       
                        if(isNaN($(this).val()))
                        {
                            valid = false;
                         $(this).addClass('error_border');
                         if($(this).parent().children('span').length == 0)
                             $(this).parent().append('<span style="color:red;"><br />'+obj.attr('msg')+'</span>');
                        }
                    }
                
                    
            
            });
            
            
             $('.master_form').find('select[relation=yes]').each(function(){
                 if(($(this).attr('null') == '0' || $(this).attr('null') == '1') && $(this).val() == '0') //validate refrenced table relationship
                 {
                    
                         valid = false;
                         $(this).addClass('error_border');
                         if($(this).parent().children('span').length == 0)
                         $(this).parent().append('<span style="color:red;">required!</span>');
                
                 }
           
             });                                 
               } catch (exception) {}

            
            if(!valid)
                e.preventDefault();
        });
        //$(this).datepicker({changeMonth: true,changeYear: true,minDate:new Date($(this).attr('min').split('/')[0] , 1 - 1, 1)});
        //$('input[data_type=datetime]').datepicker({changeMonth: true,changeYear: true});
        if(getParameterByName('index') == '-1')
        {
            $('.master_form').find('input[extra=auto_increment]').val('auto increment(*)');
            $('.master_form').find('input[data_type=datetime]').val('');
        }
    });
    function getParameterByName(name) 
    { 
        var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
        return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
    }
    
    function Contains(main,str)
    {
        if(main != undefined)
        {
            main = main.toString();
            if(main.indexOf(str) == -1)
                return false;
            else
                return true;
        }
        else 
            return false;
    }
    $(function(){
        var show_action = function(){
            $('#actions').show();
            $('#controls').hide();
        }
        var in_insert = <?php $str =  $in_insert == true?'true':'false'; echo $str; ?>;
        if(in_insert)
            show_action();
        $('#btnUpdate').click(show_action);
   
    
    
        var show_action_details = function(){
            $('#actions_details').show();
            $('#controls_details').hide();
        }
        var in_insert = <?php $str =  $in_insert_details == true?'true':'false'; echo $str; ?>;
        if(in_insert)
            show_action_details();
        $('#btnUpdate_details').click(show_action_details);
        
        $('td input[name="delete_details[]"]').click(function(){ $('#checkAll').attr('checked',false); });
    });
</script>
</head>
<body>
    <form method="post">
        <div class="container">
<div class="form-title">
<h3><?php echo $title; ?></h3>
<h6><?php echo $form_desc; ?></h6>
</div>
<table class="form-grid master_form" width="100%" border="0" cellspacing="1" cellpadding="1">
    <tr>
   <?php 
    $i = -1;
    foreach ($desc as $key=>$val)
    {
        $i++;
        if($i == 2)
        {
            $i = 0;
            echo '</tr><tr>';
        }
        $form_name = "form_$key";
        $attr = 'msg="'.$val['validation']['msg'].'"';
        $attr .= 'from="'.$val['validation']['from'].'"';
        $attr .= 'to="'.$val['validation']['to'].'"';
        $attr .= 'special_char="'.$val['validation']['special_char'].'"';
        $attr .= 'regx="'.$val['validation']['regx'].'"';
        $attr .= 'data_type="'.$val['Type'].'"';
        $attr .= 'null="'.$val['Null'].'"';   
       
        $attr .= 'extra="'.$val['Extra'].'"';
        $attr .= 'key="'.$val['Key'].'"';
        //$msg = $val['Val'];
        $value = $in_insert?'':  htmlspecialchars($row[$key]);
        
        if(!empty($_POST['form_'.$key]) && !isset ($_POST['cancel']))
                $value = $_POST['form_'.$key];

        $str_td = '<td class="red-lbl">'.$val['Label'].'</td>';
        $str_td .= '<td style="width:1%;">:</td>';
        $str_td .= '<td class="input-td">';
        if($val['Extra'] == 'auto_increment')
            $str_td .= '<input type="text" '.$attr.' style="background:#e3e3e3;" disabled value="'.$value.'" name="'.$form_name.'" />';
        else if(array_key_exists('REFERENCED_TABLE_NAME', $val))
        {
             $tbl_name = $val['REFERENCED_TABLE_NAME'];
             $joinedtabledate = query("select * from $tbl_name");
             $str = '<select relation="yes" '.$attr.' name="'.$form_name.'">';
             $str .= '<option style="width:156px;" value="0">Select</option>'; 
             while($row_tbl = mysql_fetch_array($joinedtabledate))
             {
                 if($row[$key] == $row_tbl[$val['REFERENCED_COLUMN_NAME']] && !$in_insert)
                    $str.= '<option selected value="'.$row_tbl[$val['REFERENCED_COLUMN_NAME']].'">'.$row_tbl[$val['TextField']].'</option>';
                 else
                    $str.= '<option  value="'.$row_tbl[$val['REFERENCED_COLUMN_NAME']].'">'.$row_tbl[$val['TextField']].'</option>';
             }
             $str .= '</select>';
            $str_td .= $str;
        }
        else if(strpos($val['Type'], 'set') > -1 || strpos($val['Type'], 'enum') > -1)
        {
            $items = '';
            if(strpos($val['Type'], 'set') > -1)
                 $items = substr ($val['Type'], 4, count($val['Type']) - 2);
            else
                $items = substr ($val['Type'], 5, count($val['Type']) - 2);
            $items = explode(',', $items);
            
            $str = '<select '.$attr.' name="'.$form_name.'">';
            foreach ($items as $item)
            {
                $item = str_replace("'", "", $item);
                $selected = ($item == $value)?'selected':'';
                $str .= "<option $selected value=\"$item\">$item</option>";
            }
            $str_td .= $str.'</select>';
        }
        else if($val['Type'] == 'text' || $val['Type'] == 'longtext')
            $str_td .= '<textarea '.$attr.' style="width: 328px;height: 52px;" name="'.$form_name.'">'.$value.'</textarea>';
        else if($val['Type'] == 'bit(1)')
        {
            $checked = ($value == '1')?'checked':'';
            $str_td .= '<input type="checkbox" name="'.$form_name.'" '.$checked.' />';
        }
        else if($val['Type'] == 'tinyint(1)')
        {
            if($value == '1')
                $str_td .= '<label><input  value="1" checked type="radio" name="'.$form_name.'" /> True</label>  <label><input value="0" type="radio" name="'.$form_name.'" /> False</label>';
            else 
                $str_td .= '<label><input value="1" type="radio" name="'.$form_name.'" /> True</label>  <label><input checked value="0" type="radio" name="'.$form_name.'" /> False </label>';
        }
        else 
            $str_td .= '<input '.$attr.' value="'.$value.'" name="'.$form_name.'" />';
       
        $str_td .= '</td>';
        echo $str_td;
    }
   
   ?>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="6" align="center">
        <div id="controls"><?php  if($insert) {?>
            <input id="btnInsert" type="submit" value="Insert"  class="btn"  name="insert" />
        <?php }?>
        <?php  if($update) {?>
            <input id="btnUpdate" type="button" value="Update" class="btn"  name="update" /> 
        <?php }?>
        <?php  if($delete) {?>
            <input  id="btnDelete" type="submit" value="Delete" onclick="return  confirm('Are you sure that you want to delete the row?');" class="btn"  name="delete" /> 
        <?php }?></div>
        <div id="actions" style="display: none;" >
            
            <input id="btnSave" type="submit" value="Save"  class="btn"  name="save" />  
       <input id="btnCancel"  type="submit" value="Cancel"  class="btn"  name="cancel" /> 
        </div>
    </td>
  </tr>
  <tr>
    <td colspan="6">
    <div class="pager" <?php if($insert_only) echo 'style="display:none;"'; ?> >
        <input type="submit" class="pager-btn" name="first" value="<<" <?php if($index == 0) echo 'disabled'; ?> />
    <input type="submit" class="pager-btn" name="before" value="<" <?php if($index == 0) echo 'disabled'; ?> />
    <input type="text" id="record" class="pager-records" name="number" value="<?php if($_REQUEST['index'] == '-1') echo '*'; else  echo $index+1; ?>" />
<input type="submit" class="pager-btn" value=">" name="next" <?php if($index+1 == $count || $in_insert) echo 'disabled'; ?> />
    <input type="submit" class="pager-btn" value=">>"   <?php if($index+1 == $count || $in_insert) echo 'disabled'; ?>  name="last" />
    Of <?php echo $count; ?>
    
    </div>
    
    </td>
  </tr>
  
  </table>
      


    <?php if(!$in_insert) {?>
    
  <div style="overflow-x: auto;">
<table class="form-grid details_form" style="width: 100%;" border="0" cellspacing="2" cellpadding="1">
  <?php 
        //header
        $str = '<tr>';
        if($delete && !$in_insert_details)
          $str .= '<td class="red-lbl"><div style="width:37px;"><input id="checkAll" onclick="check_all(this)"   type="checkbox"  />#</div></td>'; 
        foreach($desc_details as $key=>$val)
        {
            $str .= '<td class="red-lbl">'.$val['Label'].'</td>';  
        }
         
        $str .= '</tr>';
        
        
        //data
        if($in_insert_details)
        {
             //insert row
            $str .= '<tr>';
            foreach($desc_details as $key=>$val)
            {
                $attr = 'msg="'.$val['validation']['msg'].'"';
                $attr .= 'from="'.$val['validation']['from'].'"';
                $attr .= 'to="'.$val['validation']['to'].'"';
                $attr .= 'special_char="'.$desc_details[$val]['validation']['special_char'].'"';
                $attr .= 'regx="'.$val['validation']['regx'].'"';
                $attr .= 'data_type="'.$val['Type'].'"';
                $attr .= 'null="'.$val['Null'].'"';   
                $attr .= 'extra="'.$val['Extra'].'"';
                $attr .= 'key="'.$val['Key'].'"';//&quot;
                $value = htmlspecialchars($_POST['form_'.$key]);
                if($val['Extra'] == 'auto_increment')
                     $str .= '<td class="input-td" style="text-align:center;">(Auto)</td>'; 
                else if(array_key_exists('REFERENCED_TABLE_NAME', $val))
                {
                     $tbl_name = $val['REFERENCED_TABLE_NAME'];
                     $joinedtabledate = query("select * from $tbl_name");
                     $str_td = '<select relation="yes" '.$attr.' name="form_'.$key.'">';
                     $str_td .= '<option style="width:156px;" value="0">Select</option>'; 
                     while($row_tbl = mysql_fetch_array($joinedtabledate))
                     {
                         if($value == $row_tbl[$val['REFERENCED_COLUMN_NAME']])
                            $str_td.= '<option selected value="'.$row_tbl[$val['REFERENCED_COLUMN_NAME']].'">'.$row_tbl[$val['TextField']].'</option>';
                         else
                            $str_td.= '<option  value="'.$row_tbl[$val['REFERENCED_COLUMN_NAME']].'">'.$row_tbl[$val['TextField']].'</option>';
                     }
                     $str_td .= '</select>';
                    $str .= '<td>'.$str_td.'</td>';
                }
                else if(strpos($val['Type'], 'set') > -1 || strpos($val['Type'], 'enum') > -1)
                {
                    $items = '';
                    if(strpos($val['Type'], 'set') > -1)
                        $items = substr ($val['Type'], 4, count($val['Type']) - 2);
                    else
                        $items = substr ($val['Type'], 5, count($val['Type']) - 2);
                    $items = explode(',', $items);

                    $str_select = '<td><select '.$attr.' name="'.$i.'_form_'.$val.'">';
                    foreach ($items as $item)
                    {
                        $item = str_replace("'", "", $item);
                        $selected = ($item == $value)?'selected':'';
                        $str_select .= "<option $selected  value=\"$item\">$item</option>";
                    }
                    $str .= $str_select.'</select></td>';
                }
                else if($val['Type'] == 'bit(1)')
                {
                    $str_td = '<td style="text-align:center;">';
                    $checked = ($value == '1')?'checked':'';
                    $str_td .= '<input type="checkbox" name="form_'.$key.'" '.$checked.' />';
                    $str .= $str_td.'</td>';
                }
                else if($val['Type'] == 'tinyint(1)')
                {
                    $str_td = '<td>';

                    $str_td .= '<select name="form_'.$key.'"><option ';
                    if($value == '0')
                        $str_td .= 'selected ';
                    $str_td .= 'value="0">False</option><option value="1" ';

                    if($value == '1')
                        $str_td .= 'selected ';

                    $str_td .= '>True</option></select></td>';
                    $str .= $str_td;
                }
                else
                {
                        $str .= '<td class="input-td"><input '.$attr.' name="form_'.$key.'" class="txtbox" style=" font-family:Tahoma;" value="'.$value.'" /></td>'; 
                }
            }
            $str .= '</tr>';
        }
        else
        {//update show  (text fields)
            $i = $navigate;
             while($row = mysql_fetch_array($data_details))
             {
                 $i++;
                 $str .= '<tr>';
                 if($delete)
                     $str .= '<td class="red-lbl"><input name="delete_details[]" type="checkbox" value="'.$i.'" />'.$i.'</td>';  
            
                foreach(array_keys($desc_details) as $key=>$val)
                {
                    
                    if($update)
                    {
                        $attr = 'msg="'.$desc_details[$val]['validation']['msg'].'"';
                        $attr .= 'from="'.$desc_details[$val]['validation']['from'].'"';
                        $attr .= 'to="'.$desc_details[$val]['validation']['to'].'"';
                        $attr .= 'special_char="'.$desc_details[$val]['validation']['special_char'].'"';
                        $attr .= 'regx="'.$desc_details[$val]['validation']['regx'].'"';
                        $attr .= 'data_type="'.$desc_details[$val]['Type'].'"';
                        $attr .= 'null="'.$desc_details[$val]['Null'].'"';   
                        $attr .= 'extra="'.$desc_details[$val]['Extra'].'"';
                        $attr .= 'key="'.$desc_details[$val]['Key'].'"';
                        $value = htmlspecialchars($row[$val]);
                        
                        //echo var_dump($value);
                        if($desc_details[$val]['Extra'] == 'auto_increment')
                             $str .= '<td class="input-td" style="text-align:center;">'.$value.'</td>'; 
                        else if(array_key_exists('REFERENCED_TABLE_NAME', $desc_details[$val]))
                        {
                             $tbl_name = $desc_details[$val]['REFERENCED_TABLE_NAME'];
                             $joinedtabledate = query("select * from $tbl_name");
                             $str_td = '<select relation="yes" '.$attr.' name="'.$i.'_form_'.$val.'">';
                             $str_td .= '<option style="width:156px;" value="0">Select</option>'; 
                             while($row_tbl = mysql_fetch_array($joinedtabledate))
                             {
                                 if($row[$val] == $row_tbl[$desc_details[$val]['REFERENCED_COLUMN_NAME']] && !$in_insert)
                                    $str_td.= '<option selected value="'.$row_tbl[$desc_details[$val]['REFERENCED_COLUMN_NAME']].'">'.$row_tbl[$desc_details[$val]['TextField']].'</option>';
                                 else
                                    $str_td.= '<option  value="'.$row_tbl[$desc_details[$val]['REFERENCED_COLUMN_NAME']].'">'.$row_tbl[$desc_details[$val]['TextField']].'</option>';
                             }
                             $str_td .= '</select>';
                            $str .= '<td>'.$str_td.'</td>';
                        }
                        else if(strpos($desc_details[$val]['Type'], 'set') > -1 || strpos($desc_details[$val]['Type'], 'enum') > -1)
                        {
                            $items = '';
                            if(strpos($desc_details[$val]['Type'], 'set') > -1)
                                 $items = substr ($desc_details[$val]['Type'], 4, count($desc_details[$val]['Type']) - 2);
                            else
                                $items = substr ($desc_details[$val]['Type'], 5, count($desc_details[$val]['Type']) - 2);
                            $items = explode(',', $items);

                            $str_select = '<td><select '.$attr.' name="'.$i.'_form_'.$val.'">';
                            foreach ($items as $item)
                            {
                                $item = str_replace("'", "", $item);
                                $selected = ($item == $value)?'selected':'';
                                $str_select .= "<option $selected  value=\"$item\">$item</option>";
                            }
                            $str .= $str_select.'</select></td>';
                        }
                        else if($desc_details[$val]['Type'] == 'bit(1)')
                        {
                            $str_td = '<td>';
                            $checked = ($value == '1')?'checked':'';
                            $str_td .= '<input type="checkbox" name="'.$i.'_form_'.$val.'" '.$checked.' />';
                            $str .= $str_td.'</td>';
                        }
                        else if($desc_details[$val]['Type'] == 'tinyint(1)')
                        {
                            $str_td = '<td>';
                            
                            $str_td .= '<select name="'.$i.'_form_'.$val.'"><option ';
                            if($value == '0')
                                $str_td .= 'selected ';
                            $str_td .= 'value="0">False</option><option value="1" ';
                            
                            if($value == '1')
                                $str_td .= 'selected ';
  
                            $str_td .= '>True</option></select></td>';
                            $str .= $str_td;
                        }
                        else
                        {
                                $str .= '<td class="input-td"><input '.$attr.' name="'.$i.'_form_'.$val.'" class="txtbox" style=" font-family:Tahoma;" value="'.$value.'" /></td>'; 
                        }
                    }
                    else
                    {
                        $text = $row[$key];
                        if(strlen($text) > 50)
                            $text =  substr($text, 0, 50).'...';
                        $str .= '<td class="input-td" title="'.$row[$key].'"><input name="'.$i.'_form_'.$val.'" class="txtbox" readonly style="font-family:Tahoma;" value="'.$row[$key].'" /></td>';  
                    }
                }
            
                $str .= '</tr>';
            }
        }
       
        echo $str;
  ?>
 
    </table>
         </div>

    <table class="form-grid" width="100%" border="0" cellspacing="2" cellpadding="1">    
   <tr>
    <td colspan="6" align="center">
        <div id="controls_details"><?php  if($insert) {?>
            <input id="btnInsert_details" type="submit" value="Insert"  class="btn"  name="insert_details" />
        <?php }?>
        <?php  if($update && $count_details != 0) {?>
            <input id="btnUpdate_details" type="button" value="Update" class="btn"  name="update_details" /> 
        <?php }?>
        <?php  if($delete && $count_details != 0) {?>
            <input  id="btnDelete_details" type="submit" value="Delete" onclick="return  confirm('Are you sure that you want to delete the row?');" class="btn"  name="deletebtn_details" /> 
        <?php }?></div>
        <div id="actions_details" style="display: none;" >
            
            <input id="btnSave_details" type="submit" value="Save"  class="btn"  name="save_details" />  
       <input id="btnCancel_details"  type="submit" value="Cancel"  class="btn"  name="cancel_details" /> 
        </div>
    </td>
  </tr>
  <tr <?php if($insert_only || $count_details == 0) echo 'style="display:none;"'; ?>>
    <td colspan="5" >
            <div class="pager">
                <input type="submit" name="first_details" class="pager-btn" <?php if($p == 1) echo 'disabled'; ?> value="<<" />
    <input type="submit" name="prev_details" class="pager-btn" <?php if($p == 1) echo 'disabled'; ?> value="<" />
    <input id="record_details" type="text" class="pager-records" value="<?php if($in_insert_details)echo '*'; else echo $p;?>"/>
<input type="submit" name="next_details" class="pager-btn" <?php if($p == $pages) echo 'disabled'; ?> value=">" />
    <input type="submit" name="last_details" class="pager-btn" <?php if($p == $pages) echo 'disabled'; ?> value=">>" />   
    Of <?php echo $pages; ?>
    
    </div>
    
    </td>
  </tr>
    </table>
    <?php 

     if(isset ($_GET['msg']) && !$error && $message == '')
     {
         if($_GET['msg'] == 'saved')
            $message = 'Row Inserted Successfully.';
         else if($_GET['msg'] == 'deleted')
            $message = 'Row Deleted Successfully.';
         else if($_GET['msg'] == 'updated')
            $message = 'Data Updated Successfully.';
     }

    if($message != ''){
      
?>
    <div class="err <?php if($error) echo 'error'; else echo 'success'; ?>-message"><?php echo $message; ?></div>
<?php }?>
<?php }?>
 
</div>
    </form>
    <script type="text/javascript">
        var count = <?php echo $count; ?>;
        var index = '<?php if($in_insert) echo '*'; else echo $index+1; ?>';
        $(function(){
            $('html').bind('keypress', function(e)
            {
               if(e.keyCode == 13)
               {
                   $('#record').change();
                  return false;
               }
            });
            $('#record').change(function(){
                if(isNaN($(this).val()))
                    $(this).val(index);
                else
                {
                    var val = parseInt($(this).val());
                    if(val > count || val <= 0)
                       $(this).val(index);
                   else
                       window.location = 'index.php?index='+val;
                }
                
            });
        });
    </script>
    
  <script type="text/javascript">
        var count = <?php echo $pages; ?>;
        var index = '<?php if($in_insert_details) echo '*'; else echo $p; ?>';
        $(function(){
             $('html').bind('keypress', function(e)
            {
               if(e.keyCode == 13)
               {
                   $('#record_details').change();
                  return false;
               }
            });
            $('#record_details').change(function(){
                if(isNaN($(this).val()))
                    $(this).val(index);
                else
                {
                    var val = parseInt($(this).val());
                    if(val > count || val <= 0)
                       $(this).val(index);
                   else
                       window.location = 'index.php?p='+val+'&index='+(getParameterByName('index'));
                }
                
            });
        });
    </script>
</body>
</html>

