<?php
session_start();
require_once("config.php");
if(isset($security))
	{
		if($security=="enabled"||$members=="enabled")
		{
			if(!isset($_SESSION[$file_name]))
             header("location: login.php"); 
		}
	}
//setting the mode	
if($security=='enabled'||$members=="enabled")
{
	 if($_SESSION[$file_name]=="$sec_Username"&&isset($_GET["e"]))
	 {
	   // only admin can debug
	   $debug = $_GET["e"];
	 }
	 else
	 {
	   $debug = "off";
	 }
   
  
}
else
{
	if(isset($_GET["e"])){
	$debug = $_GET["e"];
	}
	else{
	$debug = "off";
	}
}
	
//Intializing configurations	
	
	if($datasource=='sql'||!isset($table)){
$table = array();
$result = query($sql,"Lib Get Tables");
while ($i < mysql_num_fields($result)) {
    //echo "Information for column $i:<br />\n";
    $meta = mysql_fetch_field($result, $i);
	if(in_array($meta->table,$table)==false) $table[] = $meta->table; 
    $i++;
  }

}



//mysql_free_result($result);
//print_r($table);


if(!isset($tables_filters)||empty($tables_filters)) $tables_filters = array();
if(!isset($relationships)||empty($relationships)) $relationships = array();
if(!isset($chkSearch)) $chkSearch='Yes';
if(empty($chkSearch)||$chkSearch != 'Yes')  $chkSearch='Yes';

          
 //get lables
 
 $labels = unserialize($labels);
 $group_by_source = $group_by;
 $fields_source = $fields;
 $actual_fields_source = array_values(array_diff($fields_source,$group_by_source));
 
 if($debug=="on")
{
  echo "***********************************";
  echo "<br/>DataSource : $datasource<br/>" ;
  echo "Table(s) : ";
  print_r($table);
  echo "<br/>Filter(s) : ";
  print_r($tables_filters);
  echo "<br/> Relations(s) : ";
  print_r($relationships);
  echo "<br/>Fields(s) : ";
  print_r($fields);
  echo "<br/> Search : $chkSearch <br/>";
   
  echo "***********************************<br/>";
 
}

 
 error_reporting(E_ERROR  | E_PARSE);

 
	if (isset($_POST['btnSearch']))  $btnSearch  = $_POST['btnSearch'];
	if (isset($_POST['btnShowAll']))  $btnShowAll  = $_POST['btnShowAll'];
	if (isset($_POST['btnordnary_search']))  $btnordnary_search  = $_POST['btnordnary_search'];
	
          
          if(!empty($btnordnary_search))
          {
              $conditions = array();
              foreach ($fields as $key=>$val)
              {
                  $field = '';
                  if(count($table) == 1)
                      $fildval= "`".$val."`";
                  else
                  {
                      $field = explode('.', $val);
                      $fildval= "`".$field[0]."`."."`".$field[1]."`";
                  }
                  $conditions[] = 'CONCAT('.$fildval.') like \'%'.trim($_POST['txtordnary_search']).'%\'';
              }
              if(count($conditions) > 0)
                  $conditions = '('.implode (' OR ', $conditions).')';
             $_SESSION['searchquery'] =  $conditions;
                  
          }
          elseif(!empty($btnSearch)) 
	{
		if (isset($_POST['HdSearchval']))  $HdSearchval  = $_POST['HdSearchval'];
		//echo $HdSearchval;
		if(!empty($HdSearchval))
		{ 
			$_SESSION['searchquery']=str_replace("\\", "", $HdSearchval);
		} 
	}
	else if(!empty($btnShowAll))
	{
            $_SESSION['searchquery'] =""; 
	}
	 
 
 
  function get_default_value($var)
{ 
	if(!empty($_POST[$var]))
	{
		return $_POST[$var];
	} 
}

  function query($query,$stacktrace = "query")
{
  
    global $host, $user, $pass,$debug, $db;



    if(!@mysql_connect($host, $user, $pass))
    {
        //echo("<center><B>Couldn't connect to MySQL</B></center>");
        return false;
    }
    if(!@mysql_select_db($db))
    {
        //echo("<center><B>Couldn't select database</B></center>");
        return false;
    }
    if(!$result = @mysql_query($query))
    {   if($debug == "on"){
        echo("##<b> $stacktrace : [</b> <font color = 'blue' > $query  </font>] </br> <font color = 'red' >   **Invalid query </font>: Error# " . mysql_errno() . ": " . mysql_error()."</B></center><br/></br>");
		}
        return false;
    }
	
	if($debug == "on")
	{
	   
	   echo "##<b> $stacktrace : [</b> <font color = 'blue' > $query </font> ] </br>";
	   $rows = mysql_num_rows($result);
	   echo "<font color = 'green' >   **valid query : </font>  $rows <br/><br/>";
	
	}
	
	
    return $result;
}

// preparing the datasource
function Prepare_TSql()
{
  global $fields,$table,$sort_by,$group_by,$affected_column ,$groupby_column,$relationships,$tables_filters;
  
   $funcations_arr = array("sum(","avg(","min(","max(","count(");
   
   $sql = "select ";
    $c = 0;
    foreach ($fields as $f)
    {
      if(count($table)!=1)
      {
	  	//check if this is a function field
		$isFunction = 0;
		foreach($funcations_arr as $key=>$val)
		{
		  	if(strstr($f,$val))		
			{
				$isFunction = 1 ;
				break;
			}
		}
	
			$temp = explode(".",$f);
			$t = $temp[0];
			$f = $temp[1];
			if($isFunction ==1)
			{
				$sql .= "$t`.`$f ";		
				$sql .= " as '".substr($f,0,strlen($f)-2)."'";			
			}
			else
			{
				$sql .= "`$t`.`$f` ";		
				$sql .= " as '$f'";			
			}

        }
       else
       {
		$isFunction = 0;
		foreach($funcations_arr as $key=>$val)
		{
		  	if(strstr($f,$val))		
			{
				$isFunction = 1 ;
				break;
			}
		}	   
		if($isFunction ==0) 
	         $sql .= "`$f`";
		else
	         $sql .= "$f";			
       }
     if($c<(count($fields)-1)) $sql .= ",";
     $c++;
    }
	
	//add tables names
	$sql .= " from ";	
  	foreach($table as $key=>$val)	
	 	$sql .= "`$val`,";
	$sql=substr($sql,0,strlen($sql)-1);
	
	//add relations
	if(!empty($relationships) && count($relationships)>0)
	{
		$sql .= " where";
		foreach($relationships as $key=>$val)
		{
				$sql .= " $val" . " and";
		}		
		$sql=substr($sql,0,strlen($sql)-3);		
	}

	

 if(count($tables_filters)>0)
		{
		  if(count($relationships)>0)

			{
			  $sql .= " and";
			}
			else
			{
				$sql .= " where";
			}

		foreach($tables_filters as $key=>$val)
		{

			$newVal=  str_replace("\\", " ", $val);


				$newVal=str_replace("is"," ",$newVal);
	$newVal=str_replace("\\","",$newVal);			$sql .= "( $newVal)" . " and";
		}
		$sql=substr($sql,0,strlen($sql)-3);
	}

 
	if(isset($_SESSION['searchquery']) && !empty($_SESSION['searchquery']) && $_SESSION['searchquery']!="")
	{ 
		if(!empty($relationships) || count($tables_filters)>0)
		{
			$sql .=" and ". $_SESSION['searchquery'];
		}
		else
		{  
			$sql .= " where ".$_SESSION['searchquery'];
		}
	} 
	
	
//group by in case of statistics	
  if (!empty($groupby_column))
  {
  
  		$grp_ar = explode(".",$groupby_column);

		if(count($grp_ar)>1)
		{
	        $sql .= "group by (`".$grp_ar[0]."`.`".$grp_ar[1]."`) ";		
		}
		else
		{
	        $sql .= " group by (`".$grp_ar[0]."`) ";				
		}
  }

  if(count($sort_by)>0 || count($group_by)>0)
		$sql .= " order by ";
		
  $group_by_sort = array();
	foreach($group_by as $g)
	{
		$flag =  0;
		$i = 0;

		foreach($sort_by as $arr)
		{
			if($g == $arr[0])
			{
				$group_by_sort[] =array( $arr[0],$arr[1]);
				$flag =1;
				$sort_by[$i][0] = '~xxx~' ;
				break ;
			}
			$i++;
		}
		if($flag==0)
		{
			$group_by_sort[]= array($g,'0');
		}
	}
	
	foreach($sort_by as $arr_sort)
	{
		if($arr_sort[0] !='~xxx~' )
		{
			$group_by_sort[] = array($arr_sort[0],$arr_sort[1]);
		}
	}
    $i = 0;
	foreach($group_by_sort as $arr)
	{
        if(count($table)!=1)
        {
        $dummy = explode(".",$arr[0]);
		$sql .= "`".$dummy[0] ."`.`".$dummy[1]."`";
        }
        else
        {
         $sql .= "`".$arr[0]."`";
        }

		if($arr[1]=='1') $sql.= "desc";
  	     if($i<(count($group_by_sort)-1))
		{
			$sql .="," ;
		}
		$i++;

	}
	
	$new_fields = array();
	$new_sort_by = array();
	$new_group_by = array();

	//fields	
	foreach($fields as $key=>$val )
	{
		//check if it's function field
		$isFunction = 0;
		foreach($funcations_arr as $key1=>$val1)
		{
		  	if(strstr($val,$val1))		
			{
				$isFunction = 1 ;
				break;
			}
		}
		
		$temp = explode(".",$val);
		$t = $temp[0];
		$f = $temp[1];
		if($isFunction ==1)
		{
			$new_fields[] = substr($f,0,strlen($f)-2);			
		}
		else
		{
			$new_fields[] = $f;
		}
	
	}
	if(count($table)!=1)
	$fields = $new_fields;
	
	//sort_by

	foreach($sort_by as $key=>$arr )
	{
        $temp = explode(".",$arr[0]);
		$t = $temp[0];
		$f = $temp[1];

		$new_sort_by[] =array($f,$arr[1]);
	}
	if(count($table)!=1)
	$sort_by = $new_sort_by;	
	
	
	//group_by
	foreach($group_by as $key=>$val )
	{
		$temp = explode(".",$val);
		$t = $temp[0];
		$f = $temp[1];
	
		$new_group_by[] = $f;
	}
	if(count($table)!=1)
	$group_by = $new_group_by	;
	
	return $sql;
}

/* get_sql_statement() */
function Prepare_QSql()
{
	global $sql,$fields,$group_by,$sort_by,$group_by,$groupby_column;

    if (strstr($sql,"as"))
    {

       $new_sql= $sql;
       //adding the group by clause if any
    }
    else
    {

    $sql = strtolower($sql);
	$pos_select = strpos($sql,"select");
	$pos_from = strpos($sql,"from");
        	//get the second part of the string starting from  'from' to the end
	$string_part = substr($sql,$pos_from,strlen($sql));

        	//create the new sql statement
        	$new_sql = "select " ;
        	//$fun = 1;
        	foreach ($fields as $f)
        	{
                if(strstr($f,"("))
                {
        		$new_sql .= "$f , ";
        		}
                else
                {
               	$new_sql .= "`$f`, ";
                }


        	}

        	$new_sql .= $string_part;
        	$new_sql = str_replace(", from"," from",$new_sql);
    }
	$i = 0;


    if (!empty($groupby_column))
       $new_sql .=  " group by (`".$groupby_column ."`) ";



	if(count($sort_by)>0 || count($group_by)>0 )
   {

	$new_sql .= " order by ";
   }

	$group_by_sort = array();
	
	foreach($group_by as $g) 
	{
		$flag =  0;
		$i = 0;
		
		foreach($sort_by as $arr)
		{
			if($g == $arr[0])
			{
				$group_by_sort[] =array( $arr[0],$arr[1]);
				$flag =1;
				$sort_by[$i][0] = '~xxx~' ;
				break ;
			}
			$i++;
		}
		
		if($flag==0)
		{
			$group_by_sort[]= array($g,'0');
		}
	}
	
	//************* dump ****************
	//foreach($group_by_sort as $arr)
	///{
		//echo ">>>>>>>>" .$arr[0] . "<br>";
	///}
	//**************************************
	
	
	foreach($sort_by as $arr_sort)
	{
		if($arr_sort[0] !='~xxx~' )
		{
			$group_by_sort[] = array($arr_sort[0],$arr_sort[1]);
		}
	}
		
	$i=0;

	foreach($group_by_sort as $arr)
	{


	  $new_sql .= "`$arr[0]` ";

		if($arr[1]=='1') $new_sql.= "desc";

		if($i<(count($group_by_sort)-1))
		{
			$new_sql .="," ;
		}
		$i++;
	}
	
//	echo $new_sql;
	return $new_sql;

}


function grouping_diff_index($arr1 , $arr2)
{
	$i = 0;
		
	foreach ($arr1 as $key=>$val)
	{
		if($val != $arr2[$key])
		{
			//echo "i=".$i."<br>";
			return $i;
		}
		
		$i++;
	}
	
	return -1;
}

function export_csv($sql,$limits,$start,$duration)
{

    if($start<0) $start = 0;
    if($duration<10) $duration = 10;
    if($limits==true) $sql.=" limit $start,$duration";
	//adjust header to send the file
	$html = "";

	 //output CSV HTTP headers ...
	header("Cache-control: private");
	header("Content-type: application/force-download");

	if(strstr($_SERVER["HTTP_USER_AGENT"], "MSIE"))
		header("Content-Disposition: filename=data.csv"); // For IE
	else
		header("Content-Disposition: attachment; filename=data.csv"); // For Other browsers

	//start getting data from the sql statement
	$result = query($sql,"Lib export csv");
	$fields_count = mysql_num_fields($result);
	$fields = '';
	for($i=0;$i<$fields_count;$i++)
	{
		$field = mysql_fetch_field( $result,$i);
		$fields .= str_replace(',',';', $field->name).',';
	}
	$fields = substr($fields,0,strlen($fields)-1);

	// output CSV field names
	$html .= $fields."\r\n";
	echo $html;
	while($row=mysql_fetch_row($result))
	{
		$html = '';
		for($i=0;$i<$fields_count;$i++)
		{
			$field_data = $row[$i] ;
			$field_data = str_replace("\r\n",' ',  $field_data);
			$field_data = str_replace(',',';', $field_data);
			$field_data = str_replace("\n",' ',  $field_data);

			$field_data .= ',';

			$html .= $field_data;
		}

		$html = substr($html,0,strlen($html)-1) ."\r\n";
		echo $html;
	}
}

/*
* Export XML
*/
function export_xml($sql,$limits,$start,$duration)
{
	//adjust header to send the file
	if($start<0) $start = 0;
    if($duration<10) $duration = 10;
    if($limits==true) $sql.=" limit $start,$duration";
	$html = "";
	$fields_arr = array();
	// output CSV HTTP headers ...
	header("Cache-control: private");
	header("Content-type: application/force-download");

	if(strstr($_SERVER["HTTP_USER_AGENT"], "MSIE"))
		header("Content-Disposition: filename=data.xml"); // For IE
	else
		header("Content-Disposition: attachment; filename=data.xml"); // For Other browsers

	//start getting data from the sql statement
	$result = query($sql,"Lib export xml");
	$fields_count = mysql_num_fields($result);

	//add fields names to the array
	for($i=0;$i<$fields_count;$i++)
	{
		$field = mysql_fetch_field( $result,$i);
		$field_name = str_replace(']]>',']>',$field->name);
        //removing invalid characters from field name
        $chars = array("(",")");
        foreach($chars as $v)
        {
         $field_name = str_replace($v,"",$field_name);
        }

		$field_name=str_replace(' ','_', $field_name);
		$fields_arr[]  = array($field_name,$field->numeric);
	}


	//xml header
	echo "<?xml version=\"1.0\"  encoding=\"latin1\" ?>\r\n" ;
	echo "<RECORDS>\r\n";
	//iterate through rows

	while($row=mysql_fetch_row($result))
	{
		$html = '';
		echo "<RECORD>\r\n";
		for($i=0;$i<$fields_count;$i++)
		{
			if($fields_arr[$i][1]==1) //numeric
			{
				$html .= "<" .$fields_arr[$i][0] .">" . $row[$i] . "</" .$fields_arr[$i][0] .">\r\n";
			}
			else
			{
				$html .= "<" .$fields_arr[$i][0] ."><![CDATA[" . $row[$i]. "]]></" .$fields_arr[$i][0] .">\r\n";
			}
		}

		echo $html;
		echo "</RECORD>\r\n";
	}
	echo "</RECORDS>";
}


function get_pdf($sql,$pagesize,$oriantation,$top,$bottom,$left,$right,$width,$max_width,$font,$title_font,$limits,$start,$duration,$debug=0)
{
if($start<0) $start = 0;
if($duration<10) $duration = 10;
set_time_limit(180);
global $datasource,$title;

header("Cache-control: private");
header("Content-type: application/force-download");

if(strstr($_SERVER["HTTP_USER_AGENT"], "MSIE"))
header("Content-Disposition: filename=data.pdf"); // For IE
	else
header("Content-Disposition: attachment; filename=data.pdf");

// For Other browsers

include ('../pdf/class.ezpdf.php');

if($limits) $sql .= " limit $start,$duration" ;
$pdf =& new Cezpdf($pagesize,$oriantation);
$pdf->ezSetMargins($top,$bottom,$left,$right);
$pattern = 'Page {PAGENUM} of {TOTALPAGENUM}';
if($oriantation=="landscape")
$pdf->ezStartPageNumbers($width-20,560,10,'',$pattern,1);
else
$pdf->ezStartPageNumbers($width-20,810,9,'',$pattern,1);


$pdf->selectFont('../pdf/fonts/Helvetica.afm');
$link = query($sql,"Lib export pdf");
$data = array(array());
$i = -1;
$prefrences= array(
'justification' => 'center'
);
$pdf->ezText("<u>$title</u>",15,$prefrences);
$pdf->ezText("",15,$prefrences);
$pdf->ezText("",15,$prefrences);
while($row = mysql_fetch_array($link,MYSQL_ASSOC))
{  $i++;
   foreach($row as $k=>$v)
   {
     if($i==0)
     {
       $cols[$k]['justification']='center' ;
       $col[$k]="<b>$k</b>";


     }
   $data[$i][$k]=$v;
   }
}


 //option array
 $options = array(
'showLines'=> 1,
'showHeadings' => 1,
'shaded'=> 1,
'shadeCol'=> array(0.8,0.8,0.8),
'fontSize' => $font ,
'titleFontSize' => $title_font,
'rowGap' => 2 ,
'colGap' => 2 ,
'xPos' => 'center',
'xOrientation' => 'center',
'width' => $width,
'maxWidth' => $max_width,
'cols'=>$cols);

 $pdf->ezTable($data,$col,"",$options);
$pdf->ezStream();
 /*
 $pdfcode = $pdf->ezOutput();





 $report_name = "pdf".time();
$fp=fopen("temp/$report_name.pdf",'w+');
fwrite($fp,$pdfcode);
fclose($fp);

return "temp/$report_name.pdf";
*/

}





?>
