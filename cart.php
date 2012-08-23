<?
include "global.php";

$re_link = iif($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_REFERER'],'index.php');

 //----------Cart Clear------------
 if($action=="cart_clear"){
     cart_set_value(array());
     print_redirection($re_link); 
 }
 
 
 //--------------Item Delete------------
 if($action=="item_delete"){
 //set_cookie('cart',base64_url_decode($cart_hash));
 //print base64_url_decode($cart_hash);
 cart_item_delete($id);
 print_redirection($re_link);
 }

 //----------- Cart Update -------
  if($action=="cart_update" || $action=="checkout"){ 
    //  print_r($items);
      
  cart_set_value($items);
  
  if($action=="checkout"){
  print_redirection($scripturl."/index.php?action=checkout");
  }else{
  print_redirection($re_link);
  }
  }
 //-------------------------------
 
 ?>