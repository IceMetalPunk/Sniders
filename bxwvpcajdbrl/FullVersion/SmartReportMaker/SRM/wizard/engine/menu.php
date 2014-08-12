<?php
require_once("lib.php"); 

$dataty=array('varchar','char','text','int','decimal','double','smallint','float','datetime','date','time','year','bit','bool');
$dataStr=array('varchar','char','text');
$dataInt=array('int','decimal','double','smallint','float');
$dataDate=array('datetime','date','time','year','timestamp');
$dataBool=array('bit','bool','tinyint');

$cond="";
foreach ($table as $value) 
{
  $cond .= "table_name = '".$value."' or ";
}
$cond .=")";
$cond=str_replace("or )"," ",$cond); 
$resultcon = query( "SELECT table_name,COLUMN_NAME ,DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS"
			." WHERE $cond","Menu: Get Data Types");
			
 function lower(&$string){
    $string = strtolower($string);
 }

 /*** apply the lower function to the array ***/
 
 array_walk($fields2,'lower'); 
 
$data=array();
while($row = mysql_fetch_array($resultcon,MYSQL_ASSOC))
{
	$fild=array();
	
	//if(in_array($row['DATA_TYPE'],$dataty) && in_array(((count($table)==1)?"":strtolower($row['table_name']).".").strtolower($row['COLUMN_NAME']),$fields2) )
	//{ 
	
		foreach ($row as $k => $v) 
		{
		  $fild[]=$v;
		}
		if(!in_array($fild,$data))
		{
			$data[]=$fild;
		}
	//}
}  

//echo var_dump($labels);
function printOption()
{ 
	global $data,$table,$labels;
      
          
	foreach($data as $val)
	{
                  
                $fild=((count($table)==1)?"":$val[0].".").$val[1];
                $fildval=((count($table)==1)?"":"`".$val[0]."`.")."`".$val[1]."`";
                
                if(!array_key_exists($fild, $labels))
                  continue;
		if($_POST["fields"] == $fildval)
			echo " <option value='".$fildval."' dat='".$val[2]."' selected>".$labels[$fild]."</option>\n ";
		else
			echo " <option value='".$fildval."' dat='".$val[2]."' >".$labels[$fild]."</option>\n ";
	}
}

?>
<?php
    if(!($start+$records_per_page<$nRecords))
        $link_next = '#';
    if($start <= 0)
        $link_prev = '#';
        
?>
<?php 

if(!isset($_GET['print']))
{
    ?>

<div class="menu" style="z-index: 1; text-align: center;">
    <div style="position:relative;width:960px;margin-right:auto;margin-left:auto;">
        
    <div class="nav-holder">
            <ul class="nav-menu">
            <li><a href="<?php echo $link_prev?>"><img src="prev.png"style="vertical-align: middle;
            margin-right: 5px;">Prev</a></li>
            <li><a href="<?php echo $link_next ?>">Next <img src="next.png" style="vertical-align: middle;
            margin-left: 5px;"></a></li>
            <li><a href="#" class="menu_hvr" target_class=".sub1">Print<img src="arrow.png" style="vertical-align: middle;
            margin-left: 5px;"></a></li>
            <li><a href="#" class="menu_hvr" target_class=".sub2">Export<img src="arrow.png" style="vertical-align: middle;
            margin-left: 5px;"></a></li>
        
            <?php 
                if(!empty($chkSearch))

              {
            ?>
            <li>
                <form action="<?php

                if(isset($debug)&&$debug=="on")
				echo $_SERVER['PHP_SELF']. "?e=on"; 
				else
				echo $_SERVER['PHP_SELF']; ?>" method="post">
            <input typ="text" class="search-txtbox" name="txtordnary_search" value="<?php echo get_default_value('txtordnary_search'); ?>" id="txtordnary_search" />
            <input type="submit" class="srch-btn" name="btnordnary_search" value="Search" id="txtordnary_search" />

            <input type="submit" class="srch-btn"  value="Show All" id="btnShowAll" name="btnShowAll" />
            <a href="#" id="search_advanced" class="advanced-link">Advanced Search</a>
            <?php

             if(isset($security))
                  {
                    if($security == "enabled")
                    {
                      echo " <a href='logout.php' id='LogOut' class='advanced-link'>    Sign out</a>";
                    }
                
                  }
      ?>

                </form>

            </li>
            <?php }?>
            </ul>

            <ul class="sub-menu sub1" style="right: 574px; display: none;">
            <li><a href="<?php echo $link_print2; ?>">All Pages</a></li>
            <li><a href="<?php echo $link_print1;?>">Current Page</a></li>
            </ul>

                <ul class="sub-menu sub2" style="right: 513px; display: none;">
            <li><a class="menu_hvr_sub" target_class=".sub3" href="#" style="background-image: url(sub-arrow.png);
                      background-position: right center; background-repeat:no-repeat;
                      display: inline-block;
                      width:90%;">PDF</a></li>
            <li><a class="menu_hvr_sub" target_class=".sub4" href="#" style="background-image: url(sub-arrow.png);
                      background-position: right center; background-repeat:no-repeat;
                      display: inline-block;
                      width:90%;">CSV</a></li>
            <li><a class="menu_hvr_sub" target_class=".sub5" href="#" style="background-image: url(sub-arrow.png);
                      background-position: right center; background-repeat:no-repeat;
                      display: inline-block;
                      width:90%;">XML</a></li>
            </ul>
            <!----2 level sub--->
                <ul class="sub-menu sub3 sub_sub" style="right: 367px; display: none;">
            <li><a href="<?php echo $link_pdf_all; ?>">All Pages</a></li>
            <li><a href="<?php echo $link_pdf_current;?>">Current Page</a></li>
            </ul>
                <ul class="sub-menu sub4 sub_sub" style="right: 367px; display: none; top:40px;">
            <li><a href="<?php echo $link_csv_all; ?>">All Pages</a></li>
            <li><a href="<?php echo $link_csv_current;?>">Current Page</a></li>
            </ul>
                <ul class="sub-menu sub5 sub_sub" style="right: 367px; display: none; top:70px;">
            <li><a href="<?php echo $link_xml_all; ?>">All Pages</a></li>
            <li><a href="<?php echo $link_xml_current;?>">Current Page</a></li>
            </ul>
            </div>
        
    
    </div>
    
</div>

 <?php
      if(!empty($chkSearch))
      {
        require_once("search.php"); 
      }

     
?>  

<br/>
<script type="text/javascript">
    //fix ie menu
    if($.browser.msie)
    {
        $('.search').css('margin-top','0px');  
        $('.search').css('z-index','-1');  
    }
    
    var close = false;
    $(function(){
        $('.menu_hvr').mouseover(function(){
            $('.sub-menu').not($($(this).attr('target_class'))).hide();
            $($(this).attr('target_class')).slideDown();
            close = false;
        });
        
        $('.menu_hvr').mouseleave(function(){
            close = true;
            setTimeout(function(){
                if(close)
                    {$('.sub-menu').not($($(this).attr('target_class'))).hide();
                }
                    
            }, 500);
        });
        $('.sub-menu').mouseover(function(){
            close = false;
        });
        $('.sub-menu').mouseleave(function(){
            $('.menu_hvr').mouseleave();
        });
        
        $('.menu_hvr_sub').mouseover(function(){
            $('.sub_sub').not($($(this).attr('target_class'))).hide();
            $($(this).attr('target_class')).slideDown();
        });
    });
</script>

<?php } ?>