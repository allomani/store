<?php
chdir('./../');
define('CWD', (($getcwd = str_replace("\\","/",getcwd())) ? $getcwd : '.'));
define('IS_ADMIN', 1);
$is_admin =1 ;

include_once(CWD . "/global.php") ;
header("Content-Type: text/html;charset=$settings[site_pages_encoding]");

if(!check_admin_login()){die("<center> $phrases[access_denied] </center>");}  

    
//----- Set Blocks Sort ---------//
if($action=="set_blocks_sort"){
 if_admin();

 $blocks = (array) $blocks;
 foreach($blocks as $pos=>$val_txt){
    $vals = array();
     parse_str($val_txt, $vals);
     
     foreach($vals['item'] as $ord=>$id){
          db_query("update store_blocks set ord = '".intval($ord)."',pos='".db_escape($pos)."' WHERE `id` = ".intval($id));
         }
     }
     
 }
 
 
 //-------- Set sort ------------//
 if($action == "set_sort"){
     
   parse_str($_POST['list'], $vals);
   $vals['item'] = (array) $vals['item'];

    switch($op){
        case "products_cats" : if_products_cat_admin($vals['item']); $sort_table = 'store_products_cats';break;
        case "banners" :  if_admin("banners"); $sort_table = 'store_banners';break;
        case "hot_items" :  if_admin("hot_items"); $sort_table = 'store_hot_items';break;
         case "store_fields" :  if_admin("store_fields"); $sort_table = 'store_fields_sets';break;
         case "store_fields_options" :  if_admin("store_fields"); $sort_table = 'store_fields_options';break;
          case "payment_gateways" :  if_admin(); $sort_table = 'store_payment_gateways';break;
        default: die();break;
    }
      
     foreach($vals['item'] as $ord=>$id){
         db_query("update $sort_table set ord = '".intval($ord)."' where id='".intval($id)."'");
       
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

 //---------Product Photos  Sort ------------
if($action=="set_product_photos_sort"){ 
    
if(is_array($sort_list)){
 for ($i = 0; $i < count($sort_list); $i++) {  
   
    db_query("UPDATE store_products_photos SET ord = '$i' WHERE `id` = $sort_list[$i]");
 }
}
}