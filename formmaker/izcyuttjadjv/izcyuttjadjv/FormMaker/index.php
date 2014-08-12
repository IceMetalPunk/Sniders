<?php
    session_start();
    require 'shared.php';
    /**
    *   Smart Form Maker 
    *   V 1.0.0
    *   All copyrights are preserved to StarSoft
    */
    if(isset ($_GET['delete']))
    {
        recursive_remove_directory($_GET['delete']);
    }
    
    if(isset ($_GET['modify']))
    {
        try
        {
            $form = $_GET['modify'];
            include 'forms/'.$form.'/config.php';
            $in_insert = $_REQUEST['p'] == '-1'?true:false;
            $_SESSION['form_host'] = decode($host);
            $_SESSION['form_user'] = decode($user);
            $_SESSION['form_pass'] = decode($pass);
            $_SESSION['form_permission'] = decode($permission);
            $_SESSION['form_unique'] = decode($unique);
            $_SESSION['form_db'] = decode($db);
            $_SESSION['form_table'] = decode($table);
            $_SESSION['form_fields'] = decode($fields);
            $_SESSION['form_desc'] = decode($desc);
            $_SESSION['form_layout'] = decode($layout);
            $_SESSION['form_style_name'] = decode($style_name);
            $_SESSION['form_title'] = decode($title);
            $_SESSION['form_form_desc'] = decode($form_desc);
            $_SESSION['form_date_created'] = decode($date_created);
            $_SESSION['form_file_name'] = decode($file_name);
            $_SESSION['form_records_per_page'] = decode($records_per_page); 
            $_SESSION['form_modify'] = true;
            $_SESSION['form_form_name'] = $form;
            header("Location: wizard/step_2.php");
        }catch(Exception $e)
        {
            
        }
    }
    
    $index = 0;
    $forms = array();
    $dir = dir('forms') ;
    while (false !== ($entry = $dir->read()))
    {
        
       if($entry != "." && $entry != ".." && !is_file('forms/'.$entry)) 
       {
         
           $info = stat('forms/'.$entry);
           $info = $info['ctime'];
           $forms[$index]["path"] = "forms/$entry/index.php";
           $forms[$index]["folder"] = "forms/$entry";
           $forms[$index]["name"] = $entry;
           $forms[$index]["info"] = $info;
           if(file_exists("forms/$entry/config.php"))
           {
                
                $fp = fopen("forms/$entry/config.php","r+");
                for($i=0;$i<2;$i++)//loop to skip php open tag and read config details
                {
                    $buffer = fgets($fp);
                    if(strstr($buffer,"//"))
                    {
                      $buffer = str_replace("//","",$buffer);
                      $arr = explode(",",$buffer);
                      $forms[$index]["title"]= $arr[0];
                      $forms[$index]["date"]= $arr[1];
                    }
                }
           }
           $index++;
       }
    }
    $dir->close();
    
   // echo var_dump($forms);
    //ksort($forms);
    usort($forms,'mysort');
    $forms = array_reverse($forms);
    //function for deleting folders
    
function mysort($a,$b){
	    return strcmp($a['info'], $b['info']);

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
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Form Maker</title>
    <link href="medi2.css" rel="stylesheet" type="text/css">
    <style type="text/css">
        *{font-family: tahoma !important; font-size: 11px; }
        #table3 tbody tr:first-child td{padding:7px !important;}
    </style>
</head>
<body>
    <center>
        <table border="0" height="477" cellspacing="0" cellpadding="0" width="738">
                  <tbody><tr>
                            <td align="center" width="55" height="20" background="wizard/images/topleft.jpg" style="background-repeat: no-repeat">

                    </td><td align="center" width="629" height="20" background="wizard/images/top.jpg" style="background-repeat: x">

                    </td><td align="center" width="54" height="20" background="wizard/images/topright.jpg" style="background-repeat: no-repeat">

                    <img border="0" src="wizard/images/topright.jpg" width="51" height="23"></td></tr>
                  <tr>
                            <td align="center" width="55" background="wizard/images/leftadd.jpg" style="background-repeat: y" valign="top">

                    <img border="0" src="wizard/images/left.jpg" width="64" height="403"></td><td align="center" rowspan="2">

                                      <p><img border="0" src="wizard/images/logo.png" width="369" height="71"></p>
                                      <p>
                                      <a href="wizard/step_2.php?new=form" style="color: #0029a3; text-decoration: none">
                                                          <img src="wizard/images/btn_create.png" border="0"></a>&nbsp;&nbsp;&nbsp;

                                      </p><form>

                                                <table border="0" cellpadding="0" cellspacing="0" width="501" id="table1" height="178">
                                                          <tbody><tr>
                                                                    <td width="27" height="16">
                                                                    <img border="0" src="wizard/images/ctopleft.jpg" width="38" height="37"></td>
                                                                    <td width="425" height="16" background="wizard/images/ctop.jpg" style="background-repeat: x"></td>
                                                                    <td width="38" height="16">
                                                                    <img border="0" src="wizard/images/ctopright.jpg" width="38" height="37"></td>
                                                          </tr>
                                                          <tr>
                                                                    <td width="27" height="104" background="wizard/images/cleft.jpg" style="background-repeat: y">&nbsp;</td>
                                                                    <td width="425" valign="top" bgcolor="#F9F9F9">
                                                                    <u><b>Existing Forms</b></u>
                                                                    <div align="center">
        &nbsp;<table border="1" cellpadding="2" cellspacing="0" width="434" id="table3" bordercolor="#000000" height="31">
                                                                                <tbody><tr>

                                                                                          <td width="220" bgcolor="#FDC643" height="18" align="center">
                                                                                        <font size="2" color="#000080">	<b><i>Form Name</i></b></font> </td>
                                                                                          <td bgcolor="#FDC643" height="18" align="center">
                                                                                        <font size="2" color="#000080"><i>	<b>Date created</b></i></font></td>
                                                                                          <td bgcolor="#FDC643" style="text-align: center;">&nbsp;</td>
                                                                                          
                                                                                </tr>
                                                                                <?php
                                                                                    if(count($forms) > 0)
                                                                                    {foreach ($forms as $form){?>
                                    <tr><td><font size="2"><a href="<?php echo $form["path"] ?>"><?php echo $form["name"] ?></a></font></td><td><font size="2"><a href="<?php echo $form["path"] ?>"><?php echo $form["date"] ?>
        </a></font></td>
                                        
                                        <td><a title="Delete" href="index.php?delete=<?php echo $form["folder"] ?>" onclick="return confirm('Are you sure that you want to delete the form <?php echo  $form["name"] ?>.');"> <img src="wizard/images/delete.png" border="0" align="left" alt="delete" ></a></td>
                                    
                                    
                                    </tr>
                                                                                  <?php  }}else{?>
                                    <tr><td colspan="3" style="text-align: center; font-weight: bold;"> No Forms Created.</td></tr>
                                                                                <?php }?>

                                                                        </tbody></table>
                                                                    </div>					  </td>
                                                                    <td width="38" background="wizard/images/cright.jpg" style="background-repeat: y">&nbsp;</td>
                                                          </tr>
                                                          <tr>
                                                                    <td width="27" height="18">
                                                                    <img border="0" src="wizard/images/cdownleft.jpg" width="38" height="37"></td>
                                                                    <td width="425" height="18" background="wizard/images/cdown.jpg" style="background-repeat: x"></td>
                                                                    <td width="38">
                                                                    <img border="0" src="wizard/images/cdownright.jpg" width="38" height="37"></td>
                                                          </tr>
                                          </tbody></table>
                                                <table border="0" cellpadding="0" cellspacing="0" width="100%" id="table2">
                                                <tbody><tr>
                                                          <td align="center">
                                                          <p align="center">
                                                          </p></td>
                                                          <td align="center">
                                                          <p align="center">
                                                          </p></td>
                                                </tr>
                                      </tbody></table>
                                      </form>
                                      </td><td align="center" width="54" background="wizard/images/rightadd.jpg" style="background-repeat: y" valign="top" height="388">

                    <img border="0" src="wizard/images/right.jpg"></td></tr>
                  <tr>
                            <td align="center" width="55" background="wizard/images/leftadd.jpg" style="background-repeat: y">

                    </td><td align="center" width="54" background="wizard/images/rightadd.jpg" style="background-repeat: y" valign="top">

                    </td></tr>

                  <tr>
                            <td align="center" width="55" height="29" background="wizard/images/downleft.jpg" style="background-repeat: no-repeat">

                    <img border="0" src="wizard/images/downleft.jpg"></td><td align="center" width="629" height="29" background="wizard/images/down.jpg" style="background-repeat: x">

                    </td><td align="center" width="54" height="29" background="downright.jpg" style="background-repeat: no-repeat">

                    <img border="0" src="wizard/images/downright.jpg" width="52" height="30"></td></tr>




        </tbody></table>
    </center>
</body>
</html>
