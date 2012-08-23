<?
include_once("global.php") ;
header("Content-Type: text/html;charset=$settings[site_pages_encoding]");
//------------------------------------------
if($action=="check_register_username"){
if(strlen($str) >= $settings['register_username_min_letters']){
$exclude_list = explode(",",$settings['register_username_exclude_list']) ;

	 if(!in_array($str,$exclude_list)){
//$num = db_num(member_query("select","id",array("username"=>"='$str'")));
$num = db_qr_num("select ".members_fields_replace("id")." from ".members_table_replace("store_clients")." where ".members_fields_replace("username")." like '".db_escape($str)."'",MEMEBR_SQL);

if(!$num){
print "<img src='$style[images]/true.gif'>";
}else{
print "<img src='$style[images]/false.gif' title=\"".str_replace("{username}",$str,"$phrases[register_user_exists]")."\">";
	}
	}else{
	print "<img src='$style[images]/false.gif' title=\"$phrases[err_username_not_allowed]\">";
		}
	}else{
	print "<img src='$style[images]/false.gif' title=\"$phrases[err_username_min_letters]\">";
		}
}


//------------------------------------------
if($action=="check_register_email"){
if(check_email_address($str)){
$num = db_qr_num("select ".members_fields_replace("id")." from ".members_table_replace("store_clients")." where ".members_fields_replace("email")." like '".db_escape($str)."'",MEMBER_SQL);
if(!$num){
print "<img src='$style[images]/true.gif'>";
}else{
print "<img src='$style[images]/false.gif' title=\"$phrases[register_email_exists]\">";
	}
	}else{
	print "<img src='$style[images]/false.gif' title=\"$phrases[err_email_not_valid]\">";
		}
}
//---------------------------------
if($action=="get_cart_items"){
    
$items = cart_items_array();

       //  print_r($items);
   $total_price = 0;
if(count($items)){
 
for($i=0;$i<count($items);$i++){
    
   $items[$i]['id'] = intval($items[$i]['id']);
   $items[$i]['qty'] = intval($items[$i]['qty']);
   
    print "<div id=\"cart_item_".$items[$i]['id']."\">";

$data = db_qr_fetch("select id,name,price,thumb from store_products_data where id='".$items[$i]['id']."'");

if($data['id']){
$item_price = ($data['price']*$items[$i]['qty']) ;
print "
<input type=hidden name=\"product_id[]\" value=\"$id\">
<table width=100%>
<tr>
<td><img src='".get_image($data['thumb'])."' width=40 height=40></td></tr>
<tr>
<td>".iif($data['name'],$data['name'],"-")."
<br><img src='$style[images]/qty.gif'>&nbsp;<b>$phrases[the_count] : </b>".$items[$i]['qty']."
<br><img src='$style[images]/price.gif'>&nbsp;<b>$phrases[the_price] : </b>".$item_price." $settings[currency]</td>
<td width=10><a href=\"javascript:cart_delete_item(".$items[$i]['id'].");\"><img src='$style[images]/del_small.gif' border=0 alt='$phrases[delete_from_cart]'></a></td>
</tr></table>";


print "<hr class='separate_line' size=1></div>";


$total_price += $item_price;  
}
} 

if($total_price){
print "<b>$phrases[the_total] : </b> $total_price $settings[currency]";
}

print "<br><br>
<center>
<form action='index.php' method=get>
<input type=hidden name=action value='cart'>
<input type=submit value='$phrases[checkout]'>
</form>";
}else{
print "<center>$phrases[cart_is_empty]</center>";
}
}


//----------------------
if($action=="cart_clear"){
cart_set_value(array());   
}
//---------------------------------------
if($action=="cart_add_item"){
   $id = intval($id) ;
   
$items_arr =  cart_items_array();


//----- check if exists ----- //
for($i=0;$i<count($items_arr);$i++){ 
if($items_arr[$i]['id'] == $id){
$item_found=1;
$items_arr[$i]['qty']++;
break;
} 
}

//------
if(!$item_found){
$new_item['id'] = intval($id);
$new_item['qty'] = 1;
array_push($items_arr,$new_item);
}

cart_set_value($items_arr);


}
//---------------------------------------
if($action=="cart_delete_item"){
$id=intval($id);
cart_item_delete($id);
}

//-----------------------------------------


//--------- Payment Method Details -------
if($action=="payment_method_details"){
    $id=intval($id);

$qr = db_query("select * from store_payment_methods where id='$id'");
if(db_num($qr)){
$data = db_fetch($qr);

//---------- method details -----
if($data['details']){
    print "<fieldset><legend>$phrases[the_details]</legend>
    $data[details]
    </fieldset><br>";
}
//-----------------------------


//--------- gateways -------
if($data['gateways']){

print "<fieldset><legend>$phrases[payment_gateways]</legend>";
$x=0;
$g_qr = db_query("select * from store_payment_gateways where active=1 and ID IN (".$data['gateways'].") order by ord asc");
while($g_data = db_fetch($g_qr)){

 print "<table><tr><td width=5><input type=radio name=payment_gateways value=\"$g_data[id]\" onClick=\"show_payment_gateway_details($g_data[id],$order_id);\"".iif(!$x," checked")."></td>".iif($g_data['img'],"<td width=10><img src=\"$g_data[img]\"></td>")."<td>$g_data[title]</td></tr></table>";
 $x++;
 if(!$g_found){$g_found = $g_data['id'];}


}


if(!$g_found){
    print "<center> $phrases[no_payment_gateways_available] </center>";
}else{
    print "<div id=\"payment_gateway_details_loading_div\" style=\"display:none;\"><img src='$style[images]/ajax_loading.gif'></div>
 
 <div id=\"payment_gateway_details_div\">";
payment_gateway_details($g_found,$order_id);
print "</div>";   
}
    

print "</table></fieldset>";  
 
}
//-------------------------

    
}
    
}


//--------- Payment Gateway Details -------
function payment_gateway_details($id,$order_id){
    global $member_data,$data_order ;
    check_member_login();
    
    $id=intval($id);
$order_id = intval($order_id); 
        
$data = db_qr_fetch("select * from store_payment_gateways where id='$id'");
                 
$data_order = db_qr_fetch("select * from store_orders where id='$order_id' and userid='$member_data[id]'");
$data_order['price'] = get_order_total_price($order_id);  
 
//---------- method details -----

    print "<br><fieldset>";
   if($data['details']){
       print "$data[details]<br>";
   }
   
   run_php($data['code']);
    print "</fieldset><br>";
}

if($action=="payment_gateway_details"){
payment_gateway_details($id,$order_id);
}
//-----------------------------

//-------- shippinh address -----//
if($action=="get_address_fields_div"){
    
if($type=="shipping"){$suffix="shipping";}else{$suffix="billing";}

    $data = db_qr_fetch("select * from store_clients_addresses where id='$id'");
    print "<table width=100%>
 <tr><td><b>".$phrases[$suffix."_name"]."</b></td><td><input type=text name=\"".$suffix."_info[name]\" size=30 value=\"$data[name]\"></td></tr>
 
 <tr><td><b>$phrases[country]</b></td><td><select name=\"".$suffix."_info[country]\">";
 $qr_c = db_query("select * from store_countries order by name asc");
 while($data_c = db_fetch($qr_c)){
     print "<option value=\"$data_c[name]\"".iif($data['country']==$data_c['name']," selected").">$data_c[name]</option>";
 }
 
 print "</select></td></tr>
 
  <tr><td><b>$phrases[city]</b></td><td><input type=text name=\"".$suffix."_info[city]\" size=30 value=\"$data[city]\"></td></tr>
 <tr><td><b>$phrases[the_address]</b></td><td><input type=text name=\"".$suffix."_info[address_1]\" size=30 value=\"$data[address_1]\"></td></tr>
 <tr><td></td><td><input type=text name=\"".$suffix."_info[address_2]\" size=30 value=\"$data[address_2]\"></td></tr>
 
 <tr><td><b>$phrases[telephone]</b></td><td><input type=text name=\"".$suffix."_info[telephone]\" size=30 value=\"$data[tel]\"></td></tr>
   
 </table>";
 
}


?>