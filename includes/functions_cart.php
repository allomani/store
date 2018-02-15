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

//--------- Cart Item Delete ---------
function cart_item_delete($id){
$items = cart_items_array();

$items_arr = cart_exclude_item($items,$id);

cart_set_value($items_arr);
}

//---------- Cart Items Array ---------
function cart_items_array(){
    
$cart_str = get_cookie('cart');
$items = iif($cart_str,unserialize($cart_str),array());
if(!is_array($items)){$items=array();}  

return  $items;  
}
//-----------Cart Exclude Item ---------
function cart_exclude_item($items,$exclude_id){

if(count($items)){
$x=0;
for($i=0;$i<count($items);$i++){
if($items[$i]['id'] != $exclude_id){
$items_arr[$x]['id'] = intval($items[$i]['id']);
$items_arr[$x]['qty'] = intval($items[$i]['qty']);
$x++; 
}
}
}else{
$items_arr = array();
}

return $items_arr;
} 

//--------- Cart Set Value ------
function cart_set_value($value,$is_serialized=0){

if($is_serialized){ 
$c_value = iif(is_serialized($value),$value,serialize(array()));
}else{  
$c_value = iif(is_array($value),serialize($value),serialize(array()));
}

set_cookie('cart',$c_value);   
}

//----------- Order total price- ----------
function get_order_total_price($id){
    $sum=0;
  //  print "id:".$id;
   $qr=db_query("select price,qty from store_orders_items where order_id='$id'"); 
    while($data=db_fetch($qr)){                                         
   //           print ("pr:".$data['price']."  qt : ".$data['qty']."<br>");
        $sum +=  ($data['price']*$data['qty']);
    }
  return $sum;  
}
