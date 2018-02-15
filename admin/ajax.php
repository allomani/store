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

chdir('./../');
define('CWD', (($getcwd = str_replace("\\","/",getcwd())) ? $getcwd : '.'));
define('IS_ADMIN', 1);
$is_admin =1 ;

include_once(CWD . "/global.php") ;
header("Content-Type: text/html;charset=$settings[site_pages_encoding]");

if(!check_login_cookies()){die("<center> $phrases[access_denied] </center>");}  


//----- Set Blocks Sort ---------//
if($action=="set_blocks_sort"){
 //   file_put_contents("x.txt","d".$data[0]); 
 if_admin();
if(is_array($blocks_list_r)){
$sort_list = $blocks_list_r ;
$pos="r";
}elseif(is_array($blocks_list_c)){
$sort_list = $blocks_list_c ;
$pos="c";
}else{
$sort_list = $blocks_list_l ;
$pos="l";
}
 
if(is_array($sort_list)){
 for ($i = 0; $i < count($sort_list); $i++) {  
    db_query("UPDATE store_blocks SET ord = '$i',pos='$pos' WHERE `id` = $sort_list[$i]");
 }
}
 }
 
 //------------ Set Banners Sort ---------------
if($action=="set_banners_sort"){
    if_admin("adv");
if(is_array($sort_list)){
 for ($i = 0; $i < count($sort_list); $i++) {  
    db_query("UPDATE store_banners SET ord = '$i' WHERE `id` = $sort_list[$i]");
 }
}
}


 
 
 //--------- Hot items Sort ------------
if($action=="set_hot_items_sort"){
   if_admin("hot_items");
if(is_array($sort_list)){
 for ($i = 0; $i < count($sort_list); $i++) {  
   
    db_query("UPDATE store_hot_items SET ord = '$i' WHERE `id` = $sort_list[$i]");
 }
}
}



 //--------- Products Cats  Sort ------------
if($action=="set_products_cats_sort"){
if(is_array($sort_list)){
 for ($i = 0; $i < count($sort_list); $i++) { 
 if_products_cat_admin($sort_list[$i]);  
 
    db_query("UPDATE store_products_cats SET ord = '$i' WHERE `id` = $sort_list[$i]");
 }
}
}
 //--------- Store Fields  Sort ------------
if($action=="set_store_fields_sort"){
    if_admin("store_fields");  
    
if(is_array($sort_list)){
 for ($i = 0; $i < count($sort_list); $i++) {  
   
    db_query("UPDATE store_fields_sets SET ord = '$i' WHERE `id` = $sort_list[$i]");
 }
}
}

 //---------Store Fields Options Sort ------------
if($action=="set_store_fields_options_sort"){
    if_admin("store_fields"); 
    
if(is_array($sort_list)){
 for ($i = 0; $i < count($sort_list); $i++) {  
   
    db_query("UPDATE store_fields_options SET ord = '$i' WHERE `id` = $sort_list[$i]");
 }
}
}

 //--------- Payments gateways  Sort ------------
if($action=="set_payment_gateways_sort"){
    if_admin();  
    
if(is_array($sort_list)){
 for ($i = 0; $i < count($sort_list); $i++) {  
   
    db_query("UPDATE store_payment_gateways SET ord = '$i' WHERE `id` = $sort_list[$i]");
 }
}
}
 
 
 //--------- Payments Methods  Sort ------------
if($action=="set_payment_methods_sort"){
    if_admin();  
    
if(is_array($sort_list)){
 for ($i = 0; $i < count($sort_list); $i++) {  
   
    db_query("UPDATE store_payment_methods SET ord = '$i' WHERE `id` = $sort_list[$i]");
 }
}
}

 //--------- Payments Methods  Sort ------------
if($action=="set_orders_status_sort"){
    if_admin('orders_status');  
    
if(is_array($sort_list)){
 for ($i = 0; $i < count($sort_list); $i++) {  
   
    db_query("UPDATE store_orders_status SET ord = '$i' WHERE `id` = $sort_list[$i]");
 }
}
}


 //---------Shipping Methods  Sort ------------
if($action=="set_shipping_methods_sort"){
    if_admin();  
    
if(is_array($sort_list)){
 for ($i = 0; $i < count($sort_list); $i++) {  
   
    db_query("UPDATE store_shipping_methods SET ord = '$i' WHERE `id` = $sort_list[$i]");
 }
}
}
