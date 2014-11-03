<?php
require_once("lib.php"); 
?>
<style>
    .error_border{border:  1px solid red !important ;}
    .error_span{font-size: 12px; color: red; font-size: tahoma; cursor:default;}
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

<script type="text/javascript" src="../../Js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../../Js/jquery.ui.core.min.js"></script>
<script type="text/javascript" src="../../Js/jquery.ui.datepicker.min.js"></script>
<script type="text/javascript" src="../../Js/jquery-ui-timepicker-addon.js"></script>
<link rel="stylesheet" type="text/css" href="../../Js/ui-lightness/jquery.ui.all.css" />
<link rel="stylesheet" type="text/css" href="../../Js/ui-lightness/jquery.ui.datepicker.css" />
<style type="text/css">
        .ui-datepicker{ font-size: 13px;}
    
</style>


 <?php 
    $display_search = 'none';
    if(isset($_POST['btnSearch']))
        $display_search = 'block';
 ?>
<div style="width: 100%; position: fixed; text-align: center;">
    <div class="search" style="display: <?php echo $display_search; ?>; margin: 68px auto 0px auto;">
    <img src="close.png" style="cursor: pointer; float: right;" id="close_search" />
<form action="<?php echo($link_home); ?>" method="post">
<table border="0" style="width:600px; margin:auto;" cellpadding="0" cellspacing="0">

        <tr>
		    <td class="small-lbl">
			   Field
            </td>
			<td class="small-lbl"  style="width:5px;"  > 
				  :   
			</td>
			<td>
		
                                  <select id="fields" name="fields" class="search-txtbox" style="width: 120px;padding-right: 5px;">
				<?php printOption(); ?>
				</select>   
			</td> 
			<td  style="width:5px;"  >&nbsp;  
				
			</td>
			 <td class="small-lbl">
			   KeyWord
            </td>
			<td class="small-lbl"  style="width:5px;" >  
		         :   
			</td>
			<td>
                                  <div id="search_feild_div"></div>
			</td>
			
			<td style="width:5px;" >&nbsp;  
				
			</td>
			 <td>
                                 <input type="submit" class="srch-btn" value="Search" id="btnSearch" name="btnSearch" />
           
            </td> 
			<td  style="width:5px;"  >&nbsp;  
				
			</td>
			 <td>
<input type="submit" class="srch-btn" value="Show All" id="btnShowAll2" name="btnShowAll" />
           
            </td>
        </tr>
		 
    </table>
	<input type="hidden"   id="HdSearchval" name="HdSearchval"  />
 </form>
 </div>
</div>
 
<script language="JavaScript" type="text/javascript">
var dataStr=new Array('varchar','char','text');
var dataInt=new Array('int','decimal','double','smallint','float');
var dataDate=new Array('datetime','date','time','year','timestamp');
var dataBool=new Array('bit','bool','tinyint');
var run = false;


if (!Array.prototype.indexOf)
{
  Array.prototype.indexOf = function(elt /*, from*/)
  {
    var len = this.length >>> 0;

    var from = Number(arguments[1]) || 0;
    from = (from < 0)
         ? Math.ceil(from)
         : Math.floor(from);
    if (from < 0)
      from += len;

    for (; from < len; from++)
    {
      if (from in this &&
          this[from] === elt)
        return from;
    }
    return -1;
  };
}

$(function(){
            var def_value= '<?php echo get_default_value('keyWord')?>';
            var def_value2 = '<?php echo get_default_value('keyWord2')?>';
            
           // var show_advanced = <?php if(!empty($btnSearch)) echo 'true'; else echo 'false'; ?>;
            $('#search_advanced').click(function(){
                    $('.search').show();                
            });
            
            $('#close_search').click(function(){
                    $('.search').hide();         
            });
            
 	$("#fields").change(function(){ 
              
              $('#search_feild_div').empty();
              var dataty=  $("#fields option:selected").attr("dat"); 
              //$('#keyWord').timepicker('disable');
               if(dataBool.indexOf(dataty)>-1)
               { 
                   var selected  = '';              
                   if(def_value == '0' && !run)    
                        selected = 'selected';
                   $('#search_feild_div').append('<select class="search-txtbox" name="keyWord" typ="'+dataty+'" id="keyWord"><option value="1">True</option><option value="0" '+selected+'>False</option></select>');
               }
               else if(dataInt.indexOf(dataty)>-1)
               { 
                  var val = '';
                  var val2 = '';
                  if(!run)
                  {
                    val =  def_value;
                    val2 = def_value2;
                  }
                  $('#search_feild_div').append('<input style="width:80px; margin:2px;" placeholder="From" name="keyWord" value="'+val+'" type="text" typ="'+dataty+'" id="keyWord" class="search-txtbox" /><input  style="width:80px; margin:2px;" placeholder="To"  name="keyWord2" value="'+val2+'" type="text" typ="'+dataty+'" id="keyWord2" class="search-txtbox" />');
                  $(':text').blur();
         }
              else if(dataDate.indexOf(dataty)>-1)
               { 
                  var val = '';
                  var val2 = '';
                  if(!run)
                  {
                    val =  def_value;
                    val2 = def_value2;
                  }
                  $('#search_feild_div').append('<input class="search-txtbox" style="width:123px;" placeholder="From"  name="keyWord" value="'+val+'" type="text" typ="'+dataty+'" id="keyWord" /><input style="width:123px;" placeholder="To"  name="keyWord2" value="'+val2+'" type="text" typ="'+dataty+'" id="keyWord2" class="search-txtbox" />');
                  $(':text').blur();
                if(dataty == 'time')
                {
                    $('#keyWord').timepicker({showSecond: true,timeFormat:'hh:mm:ss'});
                    $('#keyWord2').timepicker({showSecond: true,timeFormat:'hh:mm:ss'});
                }
                else if(dataty == 'date')
                {
                    $('#keyWord').datepicker({changeMonth: true,changeYear: true,dateFormat: 'yy-mm-dd'});    
                    $('#keyWord2').datepicker({changeMonth: true,changeYear: true,dateFormat: 'yy-mm-dd'});    
                }
                else
                    {
                        $('#keyWord').datetimepicker({showSecond: true,timeFormat:'hh:mm:ss',changeMonth: true,changeYear: true,dateFormat: 'yy-mm-dd'});  
                        $('#keyWord2').datetimepicker({showSecond: true,timeFormat:'hh:mm:ss',changeMonth: true,changeYear: true,dateFormat: 'yy-mm-dd'}); 
                        
                    }
                      
          }
              else
              { 
                  var val = '';
                  if(!run)
                    val =  def_value;
                   $('#search_feild_div').append('<input class="search-txtbox" name="keyWord" value="'+val+'" type="text" typ="'+dataty+'" id="keyWord" />');
               }
               if(dataty == 'time')
                $('#keyWord').timepicker({showSecond: true,timeFormat:'hh:mm:ss'});
               else if(dataty == 'date')
                $('#keyWord').datepicker({changeMonth: true,changeYear: true,dateFormat: 'yy-mm-dd'});    
               else if(dataty == 'datetime')
                $(this).datetimepicker({showSecond: true,timeFormat:'hh:mm:ss',changeMonth: true,changeYear: true,dateFormat: 'yy-mm-dd'});      
                        
                run = true;
	});
	
	$("#btnShowAll,#btnShowAll2").click(function(){ 
	 $("#keyWord").val("");
           $("#keyWord2").val("");
           //alrt($("#keyWord2").val());
           $('#txtordnary_search').val('');
	 $("#fields").val(0);
	});
	$("#btnSearch").click(function(){ 
	if(dataInt.indexOf($("#fields option:selected").attr("dat"))>-1)
	{
                    var Return = true;
		if(!TryParseInt($("#keyWord").val()) && $("#keyWord").val() != '')
		{
                        if(!($("#keyWord").next().is('span')))
                            $("#keyWord").after($('<span class="error_span" title="Please enter a valid numeric value !!">*</span>'));
		  Return =  false;
		} 
                    if(!TryParseInt($("#keyWord2").val()) && $("#keyWord2").val() != '')
		{
                        if(!($("#keyWord2").next().is('span')))
                            $("#keyWord2").after($('<span class="error_span" title="Please enter a valid numeric value !!">*</span>'));
		  Return =  false;
		} 
                    if(!Return)
                        return false;
                    var cond = '';
                    if($.trim($("#keyWord").val()) != '')
                        cond = $("#fields").val() +" >= "+$("#keyWord").val();
                    
                    if($.trim($("#keyWord2").val()) != '')
                        {
                            if(cond != '')
                                cond += ' AND '
                            cond += $("#fields").val() +" <= "+$("#keyWord2").val();
                        }
                 
                    //$("#fields").val() +" = "+$("#keyWord").val()
            	$("#HdSearchval").val(cond); 

	}
    else if(dataBool.indexOf($("#fields option:selected").attr("dat"))>-1 )
	{
//		if($("#keyWord").val()!="true"||$("#keyWord").val()!="false")
//		{ 
//			alert("Please enter a valid boolian value !!");
//			return false;
//		}
//		else
//		{ 
			$("#HdSearchval").val($("#fields").val() +" = "+$("#keyWord").val());
//		}
              }
              else if(dataDate.indexOf($("#fields option:selected").attr("dat"))>-1 )
              {
                  if($.trim($("#keyWord").val()) != '')
                        cond = $("#fields").val() +" >= '"+$("#keyWord").val()+"'";
                    
                    if($.trim($("#keyWord2").val()) != '')
                        {
                            if(cond != '')
                                cond += ' AND '
                            cond += $("#fields").val() +" <= '"+$("#keyWord2").val()+"'";
                        }
                 
                    //$("#fields").val() +" = "+$("#keyWord").val()
            	$("#HdSearchval").val(cond);
                  
                        //$("#HdSearchval").val($("#fields").val() +" = '"+$("#keyWord").val()+"'"); 
              }
              else 
              {
                        $("#HdSearchval").val($("#fields").val() +" like '%"+$.trim($("#keyWord").val())+"%'"); 
              }
	});
	
	$("#fields").change();
});

function TryParseInt(str)
{   
	var retValue = false; 
	if(str!=null)
	{     
		if(str.length>0)
		{        
			if (!isNaN(str))
			{         
				str = parseInt(str);
				retValue=true;
				if (isNaN(str))
				{ 
				  retValue=false;
				}
			}     
		}   
	}  
	return retValue;
}

 $(function() {
	if(!$.support.placeholder) { 
		var active = document.activeElement;
                    
                    
                    $(':text').live('focus',function () {
                             
			if ($(this).attr('placeholder') != '' && $(this).val() == $(this).attr('placeholder')) {
				$(this).val('').removeClass('hasPlaceholder');
			}
		});
                    
                    $(':text').live('blur',function () {
			if ($(this).attr('placeholder') != '' && ($(this).val() == '' || $(this).val() == $(this).attr('placeholder'))) {
				$(this).val($(this).attr('placeholder')).addClass('hasPlaceholder');
			}
		});
		
		$(':text').blur();
		$(active).focus();
		//$('form').submit(function () {
		//	$(this).find('.hasPlaceholder').each(function() { $(this).val(''); });
		//});
	}
});
</script>
<div class="clear" ></div>
