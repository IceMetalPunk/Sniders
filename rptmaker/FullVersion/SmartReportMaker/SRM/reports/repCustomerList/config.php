<?php
//Customer List,29-Sep-2014 18:39:14
$host='localhost';
$user='root';
$pass='tux898';
$db='sniders2013';
$datasource='table';
$table=array('t-customer');
$tables_filters=array('`t-customer`.`C-ACTIVE` is = 1');
$fields=array('C-CUSTNO','C-NAME','C-ADDR1','C-CITY','C-STATE','C-ZIP','C-PHONE','C-DISCNT-PCT');
$fields2=array('C-CUSTNO','C-NAME','C-ADDR1','C-CITY','C-STATE','C-ZIP','C-PHONE','C-DISCNT-PCT');
$labels=$labels = 'a:8:{s:8:"C-CUSTNO";s:2:"Id";s:6:"C-NAME";s:4:"Name";s:7:"C-ADDR1";s:7:"Address";s:6:"C-CITY";s:4:"city";s:7:"C-STATE";s:5:"state";s:5:"C-ZIP";s:3:"zip";s:7:"C-PHONE";s:5:"phone";s:12:"C-DISCNT-PCT";s:8:"discount";}';;
$group_by=array('C-NAME');
$sort_by=array(array('C-NAME','0'),array('C-CUSTNO','0'));
$layout='Block';
$style_name='GreyScale';
$Forget_password='';
$security='';
$members='';
$sec_Username='';
$sec_pass='';
$sec_table='';
$sec_Username_Field='';
$sec_pass_Field='';
$sec_email='';
$sec_email_field='';
$title='Customer List';
$date_created='29-Sep-2014 18:39:14';
$header='';
$footer='';
$file_name='Customer List';
$records_per_page='10';
$chkSearch='Yes';
$is_mobile='';
?>