<?php
/**
* Smart Report Maker
*All copyrights are preserved to StarSoft
*http://mysqlreports.com/
*
*/
//echo"hi";
 session_start();
 //print_r($_SESSION);
 error_reporting(E_ERROR  | E_PARSE);
 $demo =1;
 //creating the report folder
 function RecursiveMkdir($path)
 {
   if (!file_exists($path))
   {
      RecursiveMkdir(dirname($path));
      mkdir($path, 0777);
    }
  }

  function Remove_special_charcters($str)
  {
     $specials = array("!","@","#","$","%","^","&","*","(",")","-","+","=","/","'",".","<",">","|"," ");
     foreach($specials as $special)
     {
       $str =  str_replace($special, "", $str);
     }

      return $str;


  }


 $file_name = "rep".$_SESSION['file_name'];
 $file_name = str_replace(" ","",$file_name);
 $folder_name = str_replace(".php","",$file_name);
 $report_path = "../../reports/$folder_name";

 
 $_SESSION['is_mobile'] = false;
 if($_SESSION['layout'] == 'Mobile')
     $_SESSION['is_mobile'] = true;
 

 RecursiveMkdir($report_path);
 
 //records per page

 $_SESSION['records_per_page']= intval($_SESSION['records_per_page']);

 if($_SESSION['records_per_page']<1 ||!is_int($_SESSION['records_per_page']))
 $_SESSION['records_per_page']=10;

//regarding the statestical fields
 if (isset($_SESSION["affected_column"])&& !empty($_SESSION["affected_column"]))
 {
    //CHANGING THE FIELDS ARRAY
    $flds = $_SESSION["fields"];
    $_SESSION["fields"]=array();
    $new_flds = array();
    foreach($flds as $f)
    {
      if($f == $_SESSION["affected_column"])
       $new_flds[]= $_SESSION["function"]."(`$f`)";
      else
      $new_flds[]=$f;
    }
   $_SESSION["fields"] = $new_flds;

   //remove the affected column from group by array and sort array

   $group = $_SESSION["group_by"];
   $_SESSION["group_by"] = array();
   $new_group = array();
   foreach($group as $g)
   {
      if($g != $_SESSION["affected_column"])
      $new_group[]= $g ;
   }
  $_SESSION["group_by"]=  $new_group;

  $sort = $_SESSION["sort_by"];
  $_SESSION["sort_by"] = array();
  $new_sort = array();

  foreach($sort as $arr)
  {
    if($arr[0]!=$_SESSION["affected_column"])
    $new_sort[]=$arr ;
  }
  $_SESSION["sort_by"] = $new_sort;

 }


  //creating config.php
$date_create = $_SESSION['date_created'];
$title = $_SESSION['title'];
$fp=fopen($report_path."/config.php","w+");
fwrite($fp,'<?php'."\n");
fwrite($fp,"//$title,$date_create\n");
// echo "$report_path";

unset($_SESSION['searchquery']);
foreach($_SESSION as $k=>$val)
{

  $temp = "$";
  //remove any special characters from the key
  $temp .= Remove_special_charcters("$k");
   $temp .= ("=");
  //if($k == 'searchquery')
    //  continue;
  if($k == 'labels')
  {
      $temp .= '$labels = \''.  serialize($val).'\';';
  }

 elseif($k == 'sql')
 {
    $val =  str_replace("'", '"' , $val);
     $temp .= "'".$val."'" ;
   
 }


  elseif(empty($val))
  {
    if($k== "group_by") $temp .= "array()";
    elseif($k=="sort_by") $temp .= "array()";
    else $temp .= "''";

  }
  elseif(is_array($val))
  {
     $temp .= "array(";
       foreach($val as $v)
       {

            if(is_array($v))
            {
              $temp .= "array(";
              foreach($v as $v1)
              {
                $temp .= "'$v1',";
              }
              $temp .= "),";
              //removin last comma
              $temp = str_replace(",)",")",$temp);
            }
            else
            {
            $temp .= "'$v',";
            }
       }
      $temp .= ")" ;
      //removing last comma
      $temp = str_replace(",)" ,")",$temp);


  }
  else
  {
    $temp .= "'$val'" ;
  }

  $temp .= ";\n";
  fwrite($fp,$temp);
}
fwrite($fp,'?>');
fclose($fp);

//moving the CSS
$css = "../styles/".$_SESSION["style_name"].".css" ;
copy($css,$report_path."/".$_SESSION["style_name"].".css");
if($_SESSION['layout']=="Stepped" || $_SESSION['layout']=="Block")
{
    if($_SESSION['layout'] != 'Mobile')
        copy("print1.css",$report_path."/print.css");
}
else
{
copy("print.css",$report_path."/print.css");
}
//moving the functions lib
copy("lib.php",$report_path."/lib.php");
copy("login.php",$report_path."/login.php");
copy("logout.php",$report_path."/logout.php");
copy("forgetpassword.php",$report_path."/forgetpassword.php");


if($_SESSION['layout'] != 'Mobile')
{
//move menu file
copy("menu.php",$report_path."/menu.php");
}
if($_SESSION['layout'] == 'Mobile')
{
    copy("diamond_upholstery.png",$report_path."/diamond_upholstery.png");
    copy("down.png",$report_path."/down.png");
    copy("downArrow.png",$report_path."/downArrow.png");
    copy("fabric_plaid.png",$report_path."/fabric_plaid.png");
    copy("minus.png",$report_path."/minus.png");
    copy("mobile-nav-icon.png",$report_path."/mobile-nav-icon.png");
    copy("plus.png",$report_path."/plus.png");
    copy("sorting_sprite.png",$report_path."/sorting_sprite.png");
    
    copy("sorting_sprite.png",$report_path."/sorting_sprite.png");
    copy("srch-bg.png",$report_path."/srch-bg.png");
    copy("up.png",$report_path."/up.png");
    copy("upArrow.png",$report_path."/upArrow.png");
    
    copy("ipad-emulator.jpg",$report_path."/ipad-emulator.jpg");
    copy("emulator.jpg",$report_path."/emulator.jpg");
    
    copy("Mobile_Detect.php",$report_path."/Mobile_Detect.php");
    copy("Mobile.php",$report_path."/Mobile.php");
    copy("Tablet.php",$report_path."/Tablet.php");
    
    copy("view_tablet.png",$report_path."/view_tablet.png");
    copy("view_mobile.png",$report_path."/view_mobile.png");
}
else
{
    //move images
    copy("arrow.png",$report_path."/arrow.png");
    copy("btn-bg.png",$report_path."/btn-bg.png");
    copy("search.php",$report_path."/search.php");
    copy("cell-bg1.jpg",$report_path."/cell-bg1.jpg");
    copy("coffe.png",$report_path."/coffe.png");
    copy("glass-cell.png",$report_path."/glass-cell.png");
    copy("next.png",$report_path."/next.png");
    copy("prev.png",$report_path."/prev.png");
    copy("seach-icon.png",$report_path."/seach-icon.png");
    copy("sub-arrow.png",$report_path."/sub-arrow.png");
    copy("tri.gif",$report_path."/tri.gif");
    copy("tridown.gif",$report_path."/tridown.gif");
    copy("trileft.gif",$report_path."/trileft.gif");
    copy("zoom.png",$report_path."/zoom.png");
    copy("close.png",$report_path."/close.png");
    @copy("bg_table.gif",$report_path."/bg_table.gif");
}

@copy("email_report.php",$report_path."/email_report.php");
@copy("PIE.htc",$report_path."/PIE.htc");

if($_SESSION['layout'] == 'Mobile')
{
    $layout = "layouts/".$_SESSION["layout"].".php";
    copy($layout,"$report_path/index.php");
    copy('mobile_index.php',"$report_path/$folder_name.php");
}
else
{
    //direct the user to the layout...
    $layout = "layouts/".$_SESSION["layout"].".php";
    copy($layout,"$report_path/$folder_name.php");
}
//echo "$report_path/$folder_name.php";
header("location: $report_path/$folder_name.php");












?>
