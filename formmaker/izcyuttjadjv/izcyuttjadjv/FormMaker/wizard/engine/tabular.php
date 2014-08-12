<?php
    require_once 'config.php';
    require_once 'lib.php';
    // --- deserialization general variables
    $message = '';
    $error = false;
    $in_insert = $_REQUEST['p'] == '-1'?true:false;
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
    //------------------------------------------------------------
    //get permissions insert,update and delete
    $insert = $permission[0] == '1'?true:false;
    $update = $permission[1] == '1'?true:false;
    $delete = $permission[2] == '1'?true:false;
    
    $count = mysql_fetch_array(query("SELECT COUNT(*) FROM `$table`")); // get all fields count
    $count = $count[0];
    $pages = ceil($count / $records_per_page);
    
    
      //---- to avoid MYSQL keywords----------------
    $select_fields = implode(array_keys($desc),', ');
    $temp_arr = explode(',', $select_fields);
    $select_fields = array();
    $select_fields_insert = array();
    foreach($temp_arr as $key)
    {
        $select_fields_insert[] = "`".$table."`.".trim($key);
        if(strpos($desc[trim($key)]['Type'], 'bit') === false)  
             $select_fields[] = "`".$table."`.".trim($key);
        else//to handle MYSqL BUG http://bugs.mysql.com/bug.php?id=43670
             $select_fields[] = "`".$table."`.".trim($key).'+0 as '.$key;       
    }
    $select_fields = implode($select_fields,', ');
    $select_fields_insert = implode($select_fields_insert,', ');
    //------------------------------------------
    

    
    if(($insert_only && $_GET['p'] != '-1') || ($count == 0 && $insert && !$in_insert))
       header("Location: index.php?p=-1");
    
    if(!$insert && $count == 0)
       die('<div style="padding:10px; border:1px dotted red; color:red; margin:20px auto; font-family:Tahoma; font-size:12px; text-align:center; width:500px;">Table is empty and you don\'t have insert action permission!</div>'); 
    
   if(((isset ($_POST['insert']) && $insert) || ($count == 0 && $_GET['p'] != '-1')) && $count != 0) // go to insert mode
        header("Location: index.php?p=-1"); 
   
   //security from sql injections
    if((isset ($_POST['first']) || !isset ($_GET['p']) || empty($_GET['p'])) || ((!is_numeric($_GET['p']) || intval($_GET['p']) > $count || intval($_GET['p']) < -1 || intval($_GET['p']) == 0) && $count != 0))   //got to main page
        header("Location: index.php?p=1");
    if(isset ($_POST['next'])) //got to next recore
        header("Location: index.php?p=".(intval($_GET['p'])+1));
    if(isset ($_POST['prev'])) //got to previous recore
        header("Location: index.php?p=".(intval($_GET['p'])-1));
    if(isset ($_POST['last'])) // go to last index
        header("Location: index.php?p=".($pages));
    
    if(isset ($_POST['cancel']) && $in_insert)
        header("Location: index.php?p=1");
    else if(isset ($_POST['cancel'])) // cancel
        header("Location: index.php?p=".(intval($_GET['p'])));
   

    $p = intval($_GET['p']);
    $navigate = ($p - 1) * $records_per_page;
    
    
    //delete
    if(isset($_POST['deletebtn']) && !empty($_POST['deletebtn']))
    {
        $indexes_to_delete = $_POST['delete'];
        $selected_records = count($indexes_to_delete);
        $delete_items_arr = array();
        if($selected_records > 0)
        {
            $data  = query("SELECT $select_fields FROM `$table` LIMIT $navigate,$records_per_page");
            $i = $navigate;
            while($row = mysql_fetch_array($data))
            {
                $i++;
                if(in_array($i, $indexes_to_delete))
                {
                    foreach ($unique as $key)
                    {
                         $delete_items_arr[$key][] = $row[$key];
                    }
                }
            }
            
            $delete_statemet_arr = array();
            foreach ($delete_items_arr as $key=>$val)
            {
                if($desc[$key]['Type'] == 'float' || strpos($desc[$key]['Type'], 'int') > -1 || $desc[$key]['Type'] == 'double' || $desc[$key]['Type'] == 'real')
                    $delete_statemet_arr[] = "CONCAT(`$table`.`$key`) IN('".implode('\', \'', $val) ."')";
                else
                    $delete_statemet_arr[] = "`$table`.`$key` IN('".implode('\', \'', $val) ."')";   
            }
            query("Delete from `$table` where ".implode(' AND ',$delete_statemet_arr)." limit $selected_records");
            //echo "Delete from $table where ".implode(' AND ',$delete_statemet_arr)." limit 1";
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
    
    //inser & update
    if(isset($_POST['save']) && !empty($_POST['save']) && $_POST['save'] == 'Save' )
    {
        if($in_insert) //insert
        {
           $values = array();
           foreach ($desc as $key=>$val)
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
           $values = implode(', ', $values);
           query("INSERT INTO `$table`($select_fields_insert) Values($values)");
           //echo "INSERT INTO $table($select_fields) Values('$values')";
           if(mysql_affected_rows() > 0)
                header("Location: index.php?p=$pages&msg=saved"); //go to last page
            else
            {//error
                $error = true;
                $message = mysql_error();
            }
        }
        else //update
        {
             $i = $navigate;
             $data  = query("SELECT $select_fields FROM `$table` LIMIT $navigate,$records_per_page");
             
             while($row = mysql_fetch_array($data))
             {
                   $i++;
                   $values = array();
                   $conditions = array();
                   
                   foreach(array_keys($row) as $key)
                   {
                       if(in_array($key, $unique) && !is_numeric($key))
                        {
                           // if($desc[$key]['Type'] == 'float' || $desc[$key]['Type'] == 'double' || $desc[$key]['Type'] == 'real')
                                $conditions[] = 'CONCAT(`'.$table.'`.`'.$key.'`) = \''.$row[$key].'\'';
                         //   else
                           //     $conditions[] = "$table.`".$key."` = '".$row[$key]."'";
                        }
                   }
                   
                   foreach ($desc as $key=>$val)
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
                  query("UPDATE `$table` SET ".  implode(', ', $values)." where ".implode(' AND ', $conditions));
                 // echo "UPDATE $table SET ".  implode(', ', $values)." where ".implode(' AND ', $conditions);
             }
             if(mysql_error())
             {
                 $error = true;
                 $message = mysql_error();
             }
             else
             {
                 header("Location: index.php?p=$p&msg=updated");
             }
        }
    }
    
    $data  = query("SELECT $select_fields FROM `$table` LIMIT $navigate,$records_per_page");
    //echo "SELECT $select_fields FROM $table LIMIT $navigate,$records_per_page";
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
    .error_border{border:  2px solid red !important ;}
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
        $('input[data_type=datetime], input[data_type=date], input[data_type=timestamp], input[data_type=time]').focus(function(e){
            e.preventDefault();
        });
        
//        $('.err').click(function(){
//            $(this).slideUp('slow');
//        });
        
        $('input[data_type=datetime], input[data_type=date], input[data_type=timestamp], input[data_type=time]').each(function(){
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
        $('select[relation=yes]').change(remove_error);
        $('input[name*=form_],textarea').keyup(remove_error);
        $('input[name*=form_]').change(remove_error);
        
        
        //save
        $('#btnSave').click(function(e){
            //e.preventDefault();
            
            $('input[name*=form_],textarea[name*=form_]').removeClass('error_border');
            $('input[name*=form_],textarea[name*=form_]').parent().children('span').remove();
            var valid = true;
            
            try {
                 $('input[name*=form_],textarea[name*=form_]').each(function(){
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
            
            
             $('select[relation=yes]').each(function(){
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
    function getParameterByName(name) 
    { 
        var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
        return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
    }
    
    function check_all(obj)
    {
        $('td input[name="delete[]"]').attr('checked',obj.checked);
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
        
        $('td input[name="delete[]"]').click(function(){ $('#checkAll').attr('checked',false); });
    });
</script>
</head>
<body>
    <form method="post">
        <div class="container">
            <div class="form-title">
                <h3><?php echo $title; ?></h3>
                <h6><?php echo $form_desc ?></h6>
            </div>
    <div style="overflow-x: auto;">
<table class="form-grid" style="width: 100%;" border="0" cellspacing="2" cellpadding="1">
  <?php 
        //header
        $str = '<tr>';
        if($delete && !$in_insert)
          $str .= '<td class="red-lbl"><div style="width:37px;"><input id="checkAll" onclick="check_all(this)"   type="checkbox"  />#</div></td>'; 
        foreach($desc as $key=>$val)
        {
            $str .= '<td class="red-lbl">'.$val['Label'].'</td>';  
        }
         
        $str .= '</tr>';
        
        
        //data
        if($in_insert)
        {
             //insert row
            $str .= '<tr>';
            foreach($desc as $key=>$val)
            {
                $attr = 'msg="'.$val['validation']['msg'].'"';
                $attr .= 'from="'.$val['validation']['from'].'"';
                $attr .= 'to="'.$val['validation']['to'].'"';
                $attr .= 'special_char="'.$desc[$val]['validation']['special_char'].'"';
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
             while($row = mysql_fetch_array($data))
             {
                 $i++;
                 $str .= '<tr>';
                 if($delete)
                     $str .= '<td class="red-lbl"><input name="delete[]" type="checkbox" value="'.$i.'" />'.$i.'</td>';  
            
                foreach(array_keys($desc) as $key=>$val)
                {
                    if($update)
                    {
                        $attr = 'msg="'.$desc[$val]['validation']['msg'].'"';
                        $attr .= 'from="'.$desc[$val]['validation']['from'].'"';
                        $attr .= 'to="'.$desc[$val]['validation']['to'].'"';
                        $attr .= 'special_char="'.$desc[$val]['validation']['special_char'].'"';
                        $attr .= 'regx="'.$desc[$val]['validation']['regx'].'"';
                        $attr .= 'data_type="'.$desc[$val]['Type'].'"';
                        $attr .= 'null="'.$desc[$val]['Null'].'"';   
                        $attr .= 'extra="'.$desc[$val]['Extra'].'"';
                        $attr .= 'key="'.$desc[$val]['Key'].'"';
                        $value = htmlspecialchars($row[$key]);
                        if($desc[$val]['Extra'] == 'auto_increment')
                             $str .= '<td class="input-td" style="text-align:center;">'.$value.'</td>'; 
                        else if(array_key_exists('REFERENCED_TABLE_NAME', $desc[$val]))
                        {
                             $tbl_name = $desc[$val]['REFERENCED_TABLE_NAME'];
                             $joinedtabledate = query("select * from $tbl_name");
                             $str_td = '<select relation="yes" '.$attr.' name="'.$i.'_form_'.$val.'">';
                             $str_td .= '<option style="width:156px;" value="0">Select</option>'; 
                             while($row_tbl = mysql_fetch_array($joinedtabledate))
                             {
                                 if($row[$key] == $row_tbl[$desc[$val]['REFERENCED_COLUMN_NAME']] && !$in_insert)
                                    $str_td.= '<option selected value="'.$row_tbl[$desc[$val]['REFERENCED_COLUMN_NAME']].'">'.$row_tbl[$desc[$val]['TextField']].'</option>';
                                 else
                                    $str_td.= '<option  value="'.$row_tbl[$desc[$val]['REFERENCED_COLUMN_NAME']].'">'.$row_tbl[$desc[$val]['TextField']].'</option>';
                             }
                             $str_td .= '</select>';
                            $str .= '<td>'.$str_td.'</td>';
                        }
                        else if(strpos($desc[$val]['Type'], 'set') > -1 || strpos($desc[$val]['Type'], 'enum') > -1)
                        {
                            $items = '';
                            if(strpos($desc[$val]['Type'], 'set') > -1)
                                 $items = substr ($desc[$val]['Type'], 4, count($desc[$val]['Type']) - 2);
                            else
                                $items = substr ($desc[$val]['Type'], 5, count($desc[$val]['Type']) - 2);
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
                        else if($desc[$val]['Type'] == 'bit(1)')
                        {
                            $str_td = '<td>';
                            $checked = ($value == '1')?'checked':'';
                            $str_td .= '<input type="checkbox" name="'.$i.'_form_'.$val.'" '.$checked.' />';
                            $str .= $str_td.'</td>';
                        }
                        else if($desc[$val]['Type'] == 'tinyint(1)')
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
        <div id="controls"><?php  if($insert) {?>
            <input id="btnInsert" type="submit" value="Insert"  class="btn"  name="insert" />
        <?php }?>
        <?php  if($update) {?>
            <input id="btnUpdate" type="button" value="Update" class="btn"  name="update" /> 
        <?php }?>
        <?php  if($delete) {?>
            <input  id="btnDelete" type="submit" value="Delete" onclick="return  confirm('Are you sure that you want to delete the row?');" class="btn"  name="deletebtn" /> 
        <?php }?></div>
        <div id="actions" style="display: none;" >
            
            <input id="btnSave" type="submit" value="Save"  class="btn"  name="save" />  
       <input id="btnCancel"  type="submit" value="Cancel"  class="btn"  name="cancel" /> 
        </div>
    </td>
  </tr>
  <tr <?php if($insert_only) echo 'style="display:none;"'; ?>>
    <td colspan="5" >
            <div class="pager">
                <input type="submit" name="first" class="pager-btn" <?php if($p == 1) echo 'disabled'; ?> value="<<" />
    <input type="submit" name="prev" class="pager-btn" <?php if($p == 1) echo 'disabled'; ?> value="<" />
    <input id="record" type="text" class="pager-records" value="<?php if($in_insert)echo '*'; else echo $p;?>"/>
<input type="submit" name="next" class="pager-btn" <?php if($p == $pages) echo 'disabled'; ?> value=">" />
    <input type="submit" name="last" class="pager-btn" <?php if($p == $pages) echo 'disabled'; ?> value=">>" />   
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

</div>
    </form>
     <script type="text/javascript">
        var count = <?php echo $pages; ?>;
        var index = '<?php if($in_insert) echo '*'; else echo $p; ?>';
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
                       window.location = 'index.php?p='+val;
                }
                
            });
        });
    </script>
</body>
</html>
