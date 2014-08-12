  var obj_btn;
    function open_popup(obj)
    {
        $('.tbl_settings').children('tbody').children().remove();
        obj_btn = obj;
        $('#txt_validation_msg').val($(obj).attr('msg'));
        var regx = $(obj_btn).attr('regx');
        var from  = $(obj_btn).attr('from');
        var to  = $(obj_btn).attr('to');
     
        var str = '';
        if(($(obj_btn).attr('data_type').toString().indexOf('varchar') != -1  || $(obj_btn).attr('data_type').toString().indexOf('text') != -1))
        {
            str += '<tr>';
            str += '<td style="font-size: 10px;">Regx:</td>';
            str += '<td><select id="select_regx"><option value="0">Select</option><option value="email">Email</option><option value="url">URL</option><option value="credit" style="display:none;">Credit Card Number</option><option value="degits">Degits</option><option value="ip">IP-Address</option><option value="other">Regex</option></select><input style="width: 220px;" type="text" id="txt_regx" value="'+regx+'" /></td>';
            str +=  '<td> <a href="" onMouseOver="stm(Step_4[10],Style);" onClick="return false;" onMouseOut="htm()"> <img src="images/Help.png" border="0"></a></td>';
            str += '</tr>';
            $('.tbl_settings').children('tbody').append(str);
            str = '<tr>';
            str += '<td style="font-size: 10px;">Special Characters:</td>';
            if($.trim($(obj_btn).attr('special_char')) == 'none')
                str += '<td><input  type="checkbox" id="check_sc"  /></td>';
            else
                str += '<td><input  type="checkbox" id="check_sc" checked /></td>';
            
                
            str +=  '<td> <a href="" onMouseOver="stm(Step_4[11],Style);" onClick="return false;" onMouseOut="htm()"> <img src="images/Help.png" border="0"></a></td>';
            str += '<tr>';
            $('.tbl_settings').children('tbody').append(str);
            $('#select_regx').val($(obj_btn).attr('regx_type'));
       
        }
        else if(($(obj_btn).attr('data_type').toString().indexOf('int') != -1 || $(obj_btn).attr('data_type').toString().indexOf('float') != -1) && $(obj_btn).attr('data_type').toString().indexOf('tinyint') == -1)
        {
           
           str += '<tr>';
           str += '<td style="font-size: 10px;">Number Range:</td>';
           str += '<td>from <input style="width:50px;" type="text" id="range_from" value="'+from+'" column_type="number" />  to <input style="width:50px;" type="text" id="range_to" value="'+to+'" /></td>';
           str +=  '<td> <a href="" onMouseOver="stm(Step_4[12],Style);" onClick="return false;" onMouseOut="htm()"> <img src="images/Help.png" border="0"></a></td>';
           str += '</tr>';
           $('.tbl_settings').children('tbody').append(str);
         }
         else if($(obj_btn).attr('data_type').toString().indexOf('datetime') != -1)
        {
           str += '<tr>';
           str += '<td style="font-size: 10px;">Date Range:</td>';
           str += '<td>from <input style="width:80px;" readonly type="text" id="range_from" value="'+from+'" column_type="date" />  to <input readonly style="width:80px;" type="text" id="range_to" value="'+to+'" /><input style="font-size:9px;" type="button" value="Clear" onclick="$(\'#range_from\').val(\'\'); $(\'#range_to\').val(\'\');" /></td>';
           str +=  '<td> <a href="" onMouseOver="stm(Step_4[13],Style);" onClick="return false;" onMouseOut="htm()"> <img src="images/Help.png" border="0"></a></td>';
           str += '</tr>';
           $('.tbl_settings').children('tbody').append(str);
           $('#range_from').datepicker({changeMonth: true,changeYear: true});
           $('#range_to').datepicker({changeMonth: true,changeYear: true});
         }
         
        $('#pop_layout').show();
        $('#pop_div').css('margin-top','-200px');
        $('#pop_container').fadeIn('medium', function(){
            $('#pop_div').animate({'margin-top':'150px'}, '300');
        });
    }
    function close_popup()
    {
        //$('#pop_div,#pop_div2').animate({'margin-top':'-250px'}, '300',function(){
            $('#pop_container').fadeOut();
            $('#pop_layout,#pop_layout2').fadeOut();
       // });
    }
    $(function(){
        $('#txt_regx').live('keyup',function(){
            $('#select_regx').val('other');
        });
        $('#select_regx').live('change',function(){
            if($(this).val() == 'other')
                $('#txt_regx').val('');
            else if($(this).val() == 'email')
                $('#txt_regx').val('^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$');
            else if($(this).val() == 'url')
                $('#txt_regx').val("^(http:\/\/www.|https:\/\/www.|ftp:\/\/www.|www.|http:\/\/){1}([0-9A-Za-z]+\.)");
            else if($(this).val() == 'credit')
                $('#txt_regx').val('(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6011[0-9]{12}|3(?:0[0-5]|[68][0-9])[0-9]{11}|3[47][0-9]{13})$');
            else if($(this).val() == 'degits')
                $('#txt_regx').val('^[0-9]+$');
            else if($(this).val() == 'ip')
                $('#txt_regx').val('^((?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?))*$');
   
    });
        
        $('#pop_close,#btnCancel,#btnCancel_Rel,#pop_close2').click(function(){
            close_popup();
        });
        
        
        $('#select_table').change(function(){
                $('#select_value').children().remove();
                $('#select_text').children().remove();


                var table = $(this).val();
                var column = $(obj_btn).attr('column');
                var REFERENCED_TABLE_NAME = $(obj_btn).attr('REFERENCED_TABLE_NAME');
                var REFERENCED_COLUMN_NAME = $(obj_btn).attr('REFERENCED_COLUMN_NAME');
                var TextField = $(obj_btn).attr('TextField');
                
                $.post('step_4_ajax.php', {'column':column,'get_columns':table}, function(data){
                    //alert(data);
                    data = eval(data);
                    $.each(data, function(index,val){
                         if(val == REFERENCED_COLUMN_NAME)
                         $('#select_value').append('<option selected value="'+val+'">'+val+'</option>');
                        else
                         $('#select_value').append('<option  value="'+val+'">'+val+'</option>');
                     
                     if(val == TextField)
                         $('#select_text').append('<option selected value="'+val+'">'+val+'</option>');
                        else
                         $('#select_text').append('<option  value="'+val+'">'+val+'</option>');
                    });
                });
                
        });
        
        $('.relation').click(function(){
            var column = $(this).attr('column');
            obj_btn = $(this);
            $('#btnSave_Rel').removeAttr('disabled');
            $('#select_table').val($(this).attr('referenced_table_name'));
            $('#select_table').change();
            $('#pop_layout2').show();
           // $('#pop_div2').css('margin-top','-200px');
            $('#pop_container').fadeIn('medium', function(){
           // $('#pop_div2').animate({'margin-top':'150px'}, '300');
        });
            
            
            
           $('#btnSave_Rel').click(function(e){
               $(this).attr('disabled',true);
               if($('#select_table').val() == '0')
               {
                   var column = $(obj_btn).attr('column');
                   $.post('step_4_ajax.php', {'column':column,'remove_relation':'true'},function(){
                       close_popup();
                   });
                    //alert('Please select displayed text value to continue.');
                    //return false;
               }
               else
               {
                    var REFERENCED_TABLE_NAME = $('#select_table').val();
                    var REFERENCED_COLUMN_NAME = $('#select_table [value="'+REFERENCED_TABLE_NAME+'"]').attr('pri');
                    var column = $(obj_btn).attr('column');
                    var TextField = $('#select_text').val();
                    $.post('step_4_ajax.php', {'column':column,'TextField':TextField,'REFERENCED_COLUMN_NAME':REFERENCED_COLUMN_NAME,'REFERENCED_TABLE_NAME':REFERENCED_TABLE_NAME}, function(data){
                    if(data == 'true')
                    {
                        $(obj_btn).attr('TextField',TextField);
                        $(obj_btn).attr('REFERENCED_COLUMN_NAME',REFERENCED_COLUMN_NAME);
                        $(obj_btn).attr('REFERENCED_TABLE_NAME', REFERENCED_TABLE_NAME);
                        close_popup();
                    }
                    else
                        alert('Error ocuured! try again...!')
                });
               }
           });
            
        });
        
        $('#btnSave').click(function(){
            
            var valid = true;
            var chars = ['!','@','#','$','%','^','&','*','(',')','|'];
            var column = $(obj_btn).attr('column');
            var msg = $('#txt_validation_msg').val();
            var regx = $('#txt_regx').val();
            var from = $('#range_from').val();
            var to = $('#range_to').val();
            var special_char = '';
            var regx_type = $('#select_regx').val();
            if($('#check_sc').attr('checked'))
            {
                special_char = chars.join(',');
//                $.each(chars, function(index,value){
//                    if(regx.indexOf(value) != -1)
//                        chars.splice(index,1);
//                });
//                special_char = chars;
//                
            }
            if($.trim(special_char) == '')
                special_char = 'none';

            if($.trim(msg) == '')
            {
                valid = false;
                alert('Validation message is required.');
            }
            else if($('#range_from').attr('column_type') == 'number')
            {
                if(!isNumber($('#range_from').val()) && $('#range_from').val() != '')
                {
                    valid = false;
                    $('#range_from').css('border','1px solid red');
                }
                if(!isNumber($('#range_to').val()) && $('#range_to').val() != '')
                {
                    valid = false;
                    $('#range_to').css('border','1px solid red');
                }
                if(!valid)
                    alert('Please enter valid number.');
            }

            if(valid)
            {
               $.post('step_4_ajax.php', {'column':column,'msg':msg,'regx':regx,'special_char':special_char,'from':from,'to':to,'regx_type':regx_type}, function(data){
                    if(data == 'true')
                    {
                        $(obj_btn).attr('msg',msg);
                        $(obj_btn).attr('regx',regx);
                        $(obj_btn).attr('special_char', special_char);
                        $(obj_btn).attr('from', from);
                        $(obj_btn).attr('to', to);  
                        $(obj_btn).attr('regx_type', regx_type);
                        //alert(msg);
                        close_popup();
                    }
                    else
                        alert('Error ocuured! try again...!')
                });
            }
        });
    });
    function isNumber(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}