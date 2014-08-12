<?php
    /**
    * Smart Form Maker
    * All copyrights are preserved to StarSoft
    */
    define('IN_DEV', TRUE);
    error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
    ini_set('display_errors', FALSE);
     
   function decode($encoded)
   {
      return unserialize(base64_decode($encoded));
   }
?>
