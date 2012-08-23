<?
require("global.php");

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
 cart_item_delete($hash);
 print_redirection($re_link);
 }

 //----------- Cart Update -------
  if($action=="cart_update" || $action=="checkout"){ 
    //  print_r($items);
 /*$items = (array) $items;
 
foreach($items as $key=>$value){ 
$product_options[$key] = (array) $product_options[$key]; 
$items[$key]['options'] = $product_options[$key];
$hash = md5($id.serialize($product_options));
}  */

    
 // cart_set_value($items);
  
  cart_update_qty($items);
  
  if($action=="checkout"){
  print_redirection($scripturl."/checkout.php");
  }else{
 // print_redirection($re_link);
  }
  }
 //-------------------------------
 
 
 //--------------------- Cart ---------------
 
 require(CWD . "/includes/framework_start.php");   
//----------------------------------------------------


 compile_hook('cart_start');
 open_table("$phrases[the_cart]");
 
$items = cart_items_array();

       //  print_r($items);
   $total_price = 0;
if(count($items)){
print "
<script>
function cart_update_qty(){
document.forms['cart_form'].elements['action'].value = 'cart_update';
document.forms['cart_form'].submit();
}

function cart_update_clear(){
if(confirm('$phrases[are_you_sure]')){
document.forms['cart_form'].elements['action'].value = 'cart_clear';
document.forms['cart_form'].submit();
return true;
}else{
return false;
}
}

</script>

<form action='cart.php' method=post name='cart_form'>
<input type=hidden name='action' value='checkout'>";
 
for($i=0;$i<count($items);$i++){
    
   $items[$i]['id'] = intval($items[$i]['id']);
   $items[$i]['qty'] = intval($items[$i]['qty']);
   
$data = cart_item_info($items[$i]); 
          
if($data['id']){  


 
print "<div id=\"cart_item_".$items[$i]['id']."\">";


//$item_price = ($data['price']*$items[$i]['qty']) ;
print "
<input type=hidden name=\"items[$i][id]\" value=\"".$items[$i]['id']."\">
<input type=hidden name=\"items[$i][hash]\" value=\"".$items[$i]['hash']."\">

<table width=100%>
<tr>
<td width=120 valign=top><img src='".get_image($data['thumb'])."' title=\"$data[name]\"></td>
<td>$data[info]";



print "
<br><img src='$style[images]/qty.gif'>&nbsp;<b>$phrases[count] : </b><input type=text name=\"items[$i][qty]\" size=1 value='".$items[$i]['qty']."'>
<br><img src='$style[images]/price.gif'>&nbsp;<b>$phrases[the_price] : </b>".$data['item_price']." $settings[currency]</td>
<td width=10><a href=\"cart.php?action=item_delete&hash=".$items[$i]['hash']."\"><img src='$style[images]/del_small.gif' border=0 title='$phrases[delete_from_cart]'></a></td>
</tr></table>";
//print_r(cart_exclude_item($items,$items[$i]['id']));


print "<hr class='separate_line' size=1></div>";




$total_price += $data['item_price']; 
} 
} 

if($total_price){
print "<div align=$global_align_x><b>$phrases[the_total] : </b> $total_price $settings[currency]</div>";
}

print "
<input type='button' name='cart_update_btn' id='cart_update_btn' onClick=\"cart_update_qty();\" value=\"$phrases[cart_update]\">

<input type='button' name='cart_clear_btn' id='cart_clear_btn' onClick=\"return cart_update_clear();\" value=\"$phrases[cart_clear]\">
<br><br><center>
<input type=submit value='$phrases[checkout]'>
</form>";
}else{
print "<center>$phrases[cart_is_empty]</center>";
} 

close_table(); 
compile_hook('cart_end'); 

 //---------------------------------------------------
require(CWD . "/includes/framework_end.php"); 

 ?>