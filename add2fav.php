<?php
/**
 *  Allomani E-Store v1.0
 * 
 * @package Allomani.E-Store
 * @version 1.0
 * @copyright (c) 2006-2018 Allomani , All rights reserved.
 * @author Ali Allomani <info@allomani.com>
 * @link http://allomani.com
 * @license GNU General Public License version 3.0 (GPLv3)
 * 
 */

include "global.php" ;
print "<html dir=\"$settings[html_dir]\">
<head>
<META http-equiv=Content-Language content=\"$settings[site_pages_lang]\">
<META http-equiv=Content-Type content=\"text/html; charset=$settings[site_pages_encoding]\">";
print "<title> $phrases[add2favorite] </title>";
print "<LINK href='css.php' type=text/css rel=StyleSheet>
</head>
<body>";

  open_table(); 
 if(check_member_login()){

   db_query("insert into store_clients_favorites (userid,product_id) values('$member_data[id]','$id')");
      print "<center>  $phrases[add2fav_success]  </center>";
   
 }else{
     print "<center> $phrases[please_login_first] </center>";
 }
  close_table();
  
  print "</body></html>";
?>
