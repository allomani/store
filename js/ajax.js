function ajax_check_register_username(str)
{
var url="ajax.php";
url=url+"?action=check_register_username&str="+str;
url=url+"&sid="+Math.random();

new Ajax.Request(url, {   
method: 'get',   
onSuccess: function(t){ $('register_username_area').innerHTML=t.responseText;}
 }); 

}

function ajax_check_register_email(str)
{
var url="ajax.php";
url=url+"?action=check_register_email&str="+str;
url=url+"&sid="+Math.random();

new Ajax.Request(url, {   
method: 'get',   
onSuccess: function(t){$('register_email_area').innerHTML=t.responseText;}
 }); 

}


function get_shipping_address_fields_div(id)
{

$('shipping_address_loading_div').style.display = "inline";


var url="ajax.php";
url=url+"?action=get_address_fields_div&id="+id;
url=url+"&type=shipping&sid="+Math.random();

new Ajax.Request(url, {   
method: 'get',   
onSuccess: function(t){
$('shipping_address_fields_div').innerHTML=t.responseText;
$('shipping_address_loading_div').style.display = "none";
}
 }); 

}

function get_billing_address_fields_div(id)
{

$('billing_address_loading_div').style.display = "inline";


var url="ajax.php";
url=url+"?action=get_address_fields_div&id="+id;
url=url+"&type=billing&sid="+Math.random();

new Ajax.Request(url, {   
method: 'get',   
onSuccess: function(t){
$('billing_address_fields_div').innerHTML=t.responseText;
$('billing_address_loading_div').style.display = "none";
}
 }); 

}





function cart_add_item(id){

$('cart_loading_div').style.display = "inline";

$('status_bar').style.display = "inline";
$('stauts_bar_text').innerHTML = "Adding Item to Cart..."; 

                                             


var url="ajax.php";
url=url+"?action=cart_add_item&id="+id;
url=url+"&sid="+Math.random();

//$('status_bar').style.display = "none";

new Ajax.Request(url, {   
method: 'get',   
onSuccess: function(t){
//alert(t.responseText);
get_cart_items();
}
 }); 
}

function cart_delete_item(id){

var url="ajax.php";
url=url+"?action=cart_delete_item&id="+id;
url=url+"&sid="+Math.random();

new Ajax.Request(url, {   
method: 'get',   
onSuccess: function(t){
get_cart_items();

//var x=$('cart_item_'+id).parentNode.childNodes.length;
//$('cart_item_'+id).parentNode.removeChild($('cart_item_'+id));
//alert(x);
//if(x <=4){
//$('cart_div').innerHTML = '---';
//}

}
 }); 
}

function cart_clear(){

var url="ajax.php";
url=url+"?action=cart_clear";
url=url+"&sid="+Math.random();

new Ajax.Request(url, {   
method: 'get',   
onSuccess: function(t){
get_cart_items();
}
 }); 
}

function get_cart_items(){


$('cart_loading_div').style.display = "inline";

var url="ajax.php";
url=url+"?action=get_cart_items";
url=url+"&sid="+Math.random();

new Ajax.Request(url, {   
method: 'get',   
onSuccess: function(t){
$('cart_div').innerHTML =  t.responseText;
$('cart_loading_div').style.display = "none";
$('status_bar').style.display = "none"; 
}

 }); 
}

//------------- Payment Method Details --------//
function show_payment_method_details(id,order_id){


$('payment_method_details_loading_div').style.display = "inline";
$('payment_method_details_div').innerHTML = "";


var url="ajax.php";
url=url+"?action=payment_method_details";
url=url+"&id="+id+"&order_id="+order_id+"&sid="+Math.random();

new Ajax.Request(url, {   
method: 'get',   
onSuccess: function(t){
$('payment_method_details_div').innerHTML =  t.responseText;
$('payment_method_details_loading_div').style.display = "none";
}

 }); 
}


//------------- Payment Gateway Details --------//
function show_payment_gateway_details(id,order_id){

$('payment_gateway_details_loading_div').style.display = "inline";
$('payment_gateway_details_div').innerHTML = "";


var url="ajax.php";
url=url+"?action=payment_gateway_details";
url=url+"&id="+id+"&order_id="+order_id+"&sid="+Math.random();

new Ajax.Request(url, {   
method: 'get',   
onSuccess: function(t){
$('payment_gateway_details_div').innerHTML =  t.responseText;
$('payment_gateway_details_loading_div').style.display = "none";
}

 }); 
}