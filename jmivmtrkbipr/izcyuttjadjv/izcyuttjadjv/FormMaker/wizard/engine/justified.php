<?php 
    require_once 'config.php';
    require_once 'lib.php';
    // --- deserialization general variables
    $message = '';
    $error = false;
    $in_insert = $_REQUEST['index'] == '-1'?true:false;
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
    //------------------------------------------------------------
    //get permissions insert,update and delete
    $insert = $permission[0] == '1'?true:false;
    $update = $permission[1] == '1'?true:false;
    $delete = $permission[2] == '1'?true:false;
  
    $count = mysql_fetch_array(query("SELECT COUNT(*) FROM `$table`")); // get all fields count
    $count = $count[0];
    
    if($insert_only && $_GET['index'] != '-1')
       header("Location: index.php?index=-1");
    
    if(!$insert && $count == 0)
       die('<div style="padding:10px; border:1px dotted red; color:red; margin:20px auto; font-family:Tahoma; font-size:12px; text-align:center; width:500px;">Table is empty and you don\'t have insert action permission!</div>');
    
    if((isset ($_POST['insert']) && $insert) || ($count == 0 && $_GET['index'] != '-1')) // go to insert mode
        header("Location: index.php?index=-1");    
    //security from sql injections
    if((isset ($_POST['first']) || !isset ($_GET['index']) || empty($_GET['index'])) || ((!is_numeric($_GET['index']) || intval($_GET['index']) > $count || intval($_GET['index']) < -1 || intval($_GET['index']) == 0) && $count != 0))   //got to main page
        header("Location: index.php?index=1");
    if(isset ($_POST['next'])) //got to next recore
        header("Location: index.php?index=".(intval($_GET['index'])+1));
    if(isset ($_POST['before'])) //got to previous recore
        header("Location: index.php?index=".(intval($_GET['index'])-1));
    if(isset ($_POST['last'])) // go to last index
        header("Location: index.php?index=".($count));
    

    if(isset ($_POST['cancel']) && $in_insert) // canceled insert mode
        header("Location: index.php?index=1");
    else if(isset ($_POST['cancel']) && $_GET['index'] != '-1') // to remove messages if usr click cancel
        header("Location: index.php?index=".$_GET['index']);
   $index = 0;
   
   if(isset ($_REQUEST['index']) &&  !$in_insert)
       $index = intval ($_REQUEST['index']) -1;
   
  
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
   //delete
   if(isset ($_POST['delete']) && $_POST['delete'] == 'Delete')
   {
        
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
        $result = query("delete from `$table` where ".  implode(' and ', $conditions) ." limit 1");
        if($result)
        {
            $index++;
            if($index == $count)
                $index = $count - 1;
            header("Location: index.php?index=$index&msg=deleted"); 
        }
        else{
            $error = true;
            $mysql_error = mysql_error();
            if($mysql_error)
                $message = $mysql_error;
            else
              $message = 'No rows affected.' ; 
        }
        
        
   }
   
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
                   header("Location: index.php?index=-1&msg=saved");
               else 
                   header("Location: index.php?index=".($count+1)."&msg=saved");
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
                header("Location: index.php?index=$index&msg=updated");
           }
       }
   }
   

   $data = query("SELECT $select_fields FROM `$table` LIMIT $index,1"); //get the current row
   $row = mysql_fetch_array($data); // fetch the record
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
        $('input[data_type=datetime]').focus(function(e){
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
                     valid = false;
                     $(this).addClass('error_border');
                     $(this).parent().append('<span style="color:red;">required!</span>');
                }
               
               // validation allowed special characters
             if($(this).attr('special_char') && $(this).attr('special_char') != 'none' && $(this).attr('extra') != 'auto_increment' && $(this).attr('type') != 'radio' && $(this).attr('relation') != 'yes')
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
 
                if((parseFloat($(this).val()) < parseFloat($(this).attr('from')) 
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
            
            
             $('select[relation=yes]').each(function(){
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
            $('input[extra=auto_increment]').val('auto increment(*)');
            $('input[data_type=datetime]').val('');
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
<table class="form-grid" width="100%" border="0" cellspacing="1" cellpadding="1">
  
        <?php
            //get array key indexes
            $keys = array();
            foreach ($desc as $key=>$val)
               $keys[] = $key; 
            $str = '';
            $increament = 3;
            for($i = 1; $i<=count($desc); $i+=$increament)
            {
                
                //echo $i;
                $str .= '<tr>';
                for($j = $i;$j < $i+$increament;$j++)
                {
                    if(array_key_exists($j-1, $keys))
                        $str .= '<td>'.$desc[$keys[$j - 1]]['Label'].'</td>';
                    $val = $desc[$key];
       
                }
                $str .= '</tr><tr>';
                for($j = $i;$j < $i+$increament;$j++)
                {
                    if(array_key_exists($j-1, $keys))
                    {
                        $key = $keys[$j - 1];
                        $val = $desc[$key];
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
                        $value = $in_insert?'':  htmlspecialchars($row[$key]);
                        if(!empty($_POST['form_'.$key]) && !isset ($_POST['cancel']))
                            $value = htmlspecialchars($_POST['form_'.$key]);

                        $str_td = '';
                        if($val['Extra'] == 'auto_increment')
                            $str_td .= '<input class="txtbox" type="text" '.$attr.' style="background:#e3e3e3;" disabled value="'.$value.'" name="'.$form_name.'" />';
                        else if(array_key_exists('REFERENCED_TABLE_NAME', $val))
                        {
                             $tbl_name = $val['REFERENCED_TABLE_NAME'];
                             $joinedtabledate = query("select * from $tbl_name");
                             $str_option = '<select class="txtbox" relation="yes" '.$attr.' name="'.$form_name.'">';
                             $str_option .= '<option style="width:156px;" value="0">Select</option>'; 
                             while($row_tbl = mysql_fetch_array($joinedtabledate))
                             {
                                 if($row[$key] == $row_tbl[$val['REFERENCED_COLUMN_NAME']] && !$in_insert)
                                    $str_option.= '<option selected value="'.$row_tbl[$val['REFERENCED_COLUMN_NAME']].'">'.$row_tbl[$val['TextField']].'</option>';
                                 else
                                    $str_option.= '<option  value="'.$row_tbl[$val['REFERENCED_COLUMN_NAME']].'">'.$row_tbl[$val['TextField']].'</option>';
                             }
                             $str_option .= '</select>';
                            $str_td .= $str_option;
                        }
                         else if(strpos($val['Type'], 'set') > -1 || strpos($val['Type'], 'enum') > -1)
                        {
                            $items = '';
                            if(strpos($val['Type'], 'set') > -1)
                                 $items = substr ($val['Type'], 4, count($val['Type']) - 2);
                            else
                                $items = substr ($val['Type'], 5, count($val['Type']) - 2);
                            $items = explode(',', $items);

                            $str_select = '<select '.$attr.' name="'.$form_name.'">';
                            foreach ($items as $item)
                            {
                                $item = str_replace("'", "", $item);
                                $selected = ($item == $value)?'selected':'';
                                $str_select .= "<option $selected value=\"$item\">$item</option>";
                            }
                            $str_td .= $str_select.'</select>';
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
                            $str_td .= '<input class="txtbox" '.$attr.' value="'.$value.'" name="'.$form_name.'" />';
                        $str .= '<td>';
                        $str .= $str_td;
                        $str .= '</td>';
                    }
                }
                $str .= '</tr>';
            }
            echo $str;
        ?>
        
    
    
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
    <input id="record" type="text" class="pager-records" name="number" value="<?php if($_REQUEST['index'] == '-1') echo '*'; else  echo $index+1; ?>" />
<input type="submit" class="pager-btn" value=">" name="next" <?php if($index+1 == $count || $in_insert) echo 'disabled'; ?> />
    <input type="submit" class="pager-btn" value=">>"   <?php if($index+1 == $count || $in_insert) echo 'disabled'; ?>  name="last" />
    Of <?php echo $count; ?>
    
    </div>
    
    </td>
  </tr>
  
  </table>
<?php 

     if(isset ($_GET['msg']) && !$error)
     {
         if($_GET['msg'] == 'saved')
            $message = 'Row Inserted Successfully.';
         else if($_GET['msg'] == 'deleted')
            $message = 'Row Deleted Successfully.';
         else if($_GET['msg'] == 'updated')
            $message = 'Row Updated Successfully.';
     }

    if($message != ''){
      
?>
    <div class="err <?php if($error) echo 'error'; else echo 'success'; ?>-message"><?php echo $message; ?></div>
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
</body>
</html>

