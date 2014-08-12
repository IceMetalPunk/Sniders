<?php
/**
    *   Smart Form Maker 
    *   V 1.0.0
    *   All copyrights are preserved to StarSoft
    */

 session_start();
 
 if($_SESSION['form_form_host'] == null)
     Header("Location: ../../../index.php");
 //require_once '../../shared.php';
 
 //creating the form folder
 function RecursiveMkdir($path)
 {
   if (!file_exists($path))
   {
      RecursiveMkdir(dirname($path));
      mkdir($path, 0777);
    }
  }

 $file_name = $_SESSION['form_file_name'];
 $file_name = str_replace(" ","",$file_name);
 $folder_name = str_replace(".php","",$file_name);
 $form_path = "../../forms/$folder_name";
 
 if(isset ($_SESSION['form_form_name']) && !empty($_SESSION['form_form_name']) && $_SESSION['form_form_name'] != NULL)
    recursive_remove_directory("../../forms/".$_SESSION['form_form_name']);

 RecursiveMkdir($form_path);
 
 //records per page

 $_SESSION['form_records_per_page']= intval($_SESSION['form_records_per_page']);

 if($_SESSION['form_records_per_page']<1 ||!is_int($_SESSION['form_records_per_page']))
    $_SESSION['form_records_per_page']=10;



  //creating config.php
$date_create = $_SESSION['form_date_created'];
$title = $_SESSION['form_title'];
$fp=fopen($form_path."/config.php","w+");
fwrite($fp,'<?php'."\n");
fwrite($fp,"//$title,$date_create\n");

foreach($_SESSION as $key=>$val)
{
    if($key != 'form_name' && $key != 'modify')
    {
        $key = substr($key, 5);
        $temp  = "\n$$key = ";
        $temp .= '\''. base64_encode(serialize($val)).'\';';
        fwrite($fp,$temp);
    }
}
fwrite($fp,'?>');
fclose($fp);

//create help file
$fp=fopen($form_path."/help.php","w+");
fwrite($fp,'<?php'."\n");
fwrite($fp,"//$title,$date_create\n");

foreach($_SESSION as $key=>$val)
{
    if($key != 'form_name' && $key != 'modify')
    {
        $key = substr($key, 5);
        $temp  = "\n//$$key = ";
        $temp .= '\''. serialize($val).'\';';
        fwrite($fp,$temp);
    }
}
fwrite($fp,'?>');
fclose($fp);

//moving the CSS


//moving the index file
if($_SESSION["form_layout"] == "Mobile")
{
 $index = "responsive";
 $Is_mobile = true;
 copy('mobile_index.php',"$form_path/mobile_index.php");
}
else
{
$index = strtolower($_SESSION["form_layout"]);
$css = "../styles/".$_SESSION["form_style_name"].".css" ;
copy($css,$form_path."/".$_SESSION["form_style_name"].".css");
$Is_mobile = false;
copy('mobile_index.php',"$form_path/mobile_view.php");    
}
copy("$index.php",$form_path."/index.php");

//moving the functions lib
copy("lib.php",$form_path."/lib.php");


//create dir images
$form_image_path = $form_path.'/images/';
if(!file_exists($form_path.'/images/'))
        mkdir($form_path.'/images/');
//move images
copy("ball.png",$form_image_path."ball.png");
copy("bg-texture1.png",$form_image_path."bg-texture1.png");
copy("black-texture.png",$form_image_path."black-texture.png");
copy("body_bg.jpg",$form_image_path."body_bg.jpg");
copy("btn-bg.png",$form_image_path."btn-bg.png");

copy("error.gif",$form_image_path."error.gif");
copy("glass-bg.png",$form_image_path."glass-bg.png");
copy("glass-bg2.png",$form_image_path."glass-bg2.png");
copy("ind-bg.png",$form_image_path."ind-bg.png");
copy("success.gif",$form_image_path."success.gif");


//////////////////////////////////
///Mobile Layout


    copy("mobile-nav-icon.png",$form_path."/mobile-nav-icon.png");
    copy("ipad-emulator.jpg",$form_path."/ipad-emulator.jpg");
    copy("emulator.jpg",$form_path."/emulator.jpg");    
    copy("Mobile_Detect.php",$form_path."/Mobile_Detect.php");
    copy("Mobile.php",$form_path."/Mobile.php");
    copy("Tablet.php",$form_path."/Tablet.php");    
    copy("view_tablet.png",$form_path."/view_tablet.png");
    copy("view_mobile.png",$form_path."/view_mobile.png");
    copy("mobile.css",$form_path."/mobile.css");






/////////////////////////////////


$host_name = $_SESSION['form_host'];
$user_name = $_SESSION['form_user'];
$password = $_SESSION['form_pass'];
$modify = $_SESSION['form_modify'];
//foreach($_SESSION as $key=>$val)
//{
//    $_SESSION[$key] = NULL;
//}
//
//session_destroy();
//session_start();
$_SESSION['form_host'] = $host_name;
$_SESSION['form_user'] = $user_name;
$_SESSION['form_pass'] = $password;

if($Is_mobile==true)
{
header("location: $form_path/
");
}
else
{
header("location: $form_path/index.php");
}









  function recursive_remove_directory($directory, $empty=FALSE)
     {
        
         // if the path has a slash at the end we remove it here
         if(substr($directory,-1) == '/')
         {
          
             $directory = substr($directory,0,-1);
         }

         // if the path is not valid or is not a directory ...
         if(!file_exists($directory) || !is_dir($directory))
         {
             // ... we return false and exit the function
            return FALSE;

         // ... if the path is not readable
         }elseif(!is_readable($directory))
         {
             // ... we return false and exit the function
             return FALSE;

         // ... else if the path is readable
         }else{

             // we open the directory
             $handle = opendir($directory);

             // and scan through the items inside
             while (FALSE !== ($item = readdir($handle)))
             {
                 // if the filepointer is not the current directory
                 // or the parent directory
                 if($item != '.' && $item != '..')
                 {
                     // we build the new path to delete
                     $path = $directory.'/'.$item;

                     // if the new path is a directory
                     if(is_dir($path)) 
                     {
                         // we call this function with the new path
                         recursive_remove_directory($path);

                     // if the new path is a file
                     }else{
                         // we remove the file
                         unlink($path);
                     }
                 }
             }
             // close the directory
             closedir($handle);

             // if the option to empty is not set to true
             if($empty == FALSE)
             {
                 // try to delete the now empty directory
                 if(!rmdir($directory))
                 {
                     // return false if not possible
                     return FALSE;
                 }
             }
             // return success
             return TRUE;
         }
     }

?>
