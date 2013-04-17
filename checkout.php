<?
require("global.php");

require(CWD . "/includes/framework_start.php");   
//----------------------------------------------------

 /*
 if(count($checkout_errors)){
     open_table();
     foreach($checkout_errors as $errtxt){
         print "<li>$errtxt</li>";
     }
     close_table();
 }  */


//----------- prepair items --------------------// 
 $items = cart_items_array();
 
 
  $items_cats = array();
 $shipping_ids = array();

 
 $total_price = 0;
 $total_weight = 0;
 
 for($i=0;$i<count($items);$i++){

$items[$i]['id'] = (int) $items[$i]['id'];
$items[$i]['qty'] = (int) $items[$i]['qty'];


 $data =  cart_item_info($items[$i]);  
 $items[$i]['data'] = $data;
     
//$data = db_qr_fetch("select weight,can_shipping,cat from store_products_data where id='".$items[$i]['id']."'");

 $total_price += $data['item_price']; 
 $total_weight += $data['weight'];

 if($data['can_shipping']){$can_shipping=1;}
 
 }
 
 $payment_ids = items_available_payment_methods($items);
 $shipping_ids = items_available_shipping_methods($items);

//--------------------------------------------//
$total_items = count($items);
 
 if($total_items){
 
 
 if($can_shipping){
 $steps = array(1=>"login",2=>"billing",3=>"shipping",4=>"shipping_method",5=>"billing_method",6=>"review",7=>"confirm");
 }else{
 $steps = array(1=>"login",2=>"billing",3=>"billing_method",4=>"review",5=>"confirm");  
 }
 
 $step = (int) $step;
 if(!$step){$step=1;}
 if(!$op){$op=$steps[$step];}
 // check_member_login();
 
//-------- step nav -----------//  
 $cur_step = array_get_key($steps,$op);
 $prev_step = $cur_step-1;
 $next_step = $cur_step+1;
 
 
 //---------- step action -------------------//
 if($sop=="billing"){
 $billing_info = (array)$billing_info; 
  if($billing_info['name'] && $billing_info['country']){
 $billing_info = array_map('htmlspecialchars',$billing_info);
 $session->set("checkout_billing",$billing_info);
 }else{
    if($sop == $steps[$prev_step]){
     print "<div class='alert error'>$phrases[please_fill_all_fields]</div>";
     $op = $sop;
    }
    }
 }
 
 
 if($sop=="shipping"){
    $shipping_info = (array) $shipping_info;
    if($shipping_info['name'] && $shipping_info['country']){
    $shipping_info = array_map('htmlspecialchars',$shipping_info);
    $session->set("checkout_shipping",$shipping_info);
    }else{
    if($sop == $steps[$prev_step]){
     print "<div class='alert error'>$phrases[please_fill_all_fields]</div>";
     $op = $sop;
    }
    }
 }
 
 
 if($sop=="shipping_method"){
    if($shipping_method){
    $obj = get_shipping_method($shipping_method);
    if($obj['status']){
    $session->set("checkout_shipping_method",$shipping_method);
    $session->set("checkout_shipping_price",  $obj['price']);
    }else{
    $session->set("checkout_shipping_method",'');
    $session->set("checkout_shipping_price", '');
    
    if($sop == $steps[$prev_step]){
     print "<div class='alert error'>$phrases[cannot_get_shipping_price]</div>";
     $op = $sop;
    }
    
        
    }
    
    }else{
    if($sop == $steps[$prev_step]){
     print "<div class='alert error'>$phrases[no_shipping_method_selected]</div>";
     $op = $sop;
    }
    }
 }
 
 
  if($sop=="billing_method"){
    if($payment_method){
    $session->set("checkout_billing_method",$payment_method);
    }else{
    if($sop == $steps[$prev_step]){
     print "<div class='alert error'>$phrases[no_billing_method_selected]</div>";
     $op = $sop;
    }
    }
 }
 
 
 
//--------- step redirection ---------------//
   if(!check_member_login()){  
  $op = "login";   
   }else{
       if($op=="login"){$op = "billing";}
   }

 
 /* if($op=="shipping" && !$can_shipping){
      $op = "payment";    
  }  */
  

  //-------- step nav -----------//  
 $cur_step = array_get_key($steps,$op);
 $prev_step = $cur_step-1;
 $next_step = $cur_step+1;
 
 
open_table("$phrases[checkout]");
  //------------ steps header -------------
  print "<div class='checkout_steps'>";
  foreach($steps as $s){
 print "<div class='".iif($s==$op,"step_btn_selected","step_btn")."'>{$phrases["checkout_{$s}"]}</div>";
 }
 print "</div>
 <div class='clear'></div>";
  
 if($op != "login" && $op != "confirm"){
 print "
 <script>
 function checkout_prev(){
 document.forms['checkout_form'].elements['op'].value = '".$steps[$prev_step]."';
 document.forms['checkout_form'].submit();
 }
 function checkout_next(){
 document.forms['checkout_form'].elements['op'].value = '".$steps[$next_step]."';
 document.forms['checkout_form'].submit();
 }
 </script>
  
 <form action='checkout.php' method='post' id='checkout_form' name='checkout_form'>
 <input type='hidden' name='sop' value=\"".htmlspecialchars($op)."\">
 <input type='hidden' name='op' value=\"\">";
 }
 //---------------- Register / Login ------------------------
  if($op=="login"){
      
/*  if(check_member_login()){
   print_redirection("checkout.php?op=billing");   
  } */
  
   print "<table width=100%><tr>
   <td width=50%>
   <input type='radio' name='op' id='op_1' value='register'><label for='op_1'> New User </lable><br>
   <input type='radio' name='op' id='op_2' value='guest'><label for='op_2'> Continue as Guest</lable><br>   
   
   </td>
   <td width=50%>";
   require(CWD."/login_form.php");
   print "
   </td>
   </td></tr></table>";   
  }
   
 //-------------- billing --------------
 if($op=="billing"){
     
 $billing_session = (array) $session->get("checkout_billing");
 
 print "<br>

 <fieldset style=\"width:100%;\">
 <legend>$phrases[billing_address]</legend>
 <select name='billing_address_id' onChange=\"get_saved_address(this.value,'billing');\">
 <option value=0>-- $phrases[saved_address] --</option>";
 $qr_address = db_query("select * from store_clients_addresses where client_id='$member_data[id]'");
 while($data_address = db_fetch($qr_address)){
 print "<option value='$data_address[id]'".iif($data_address['default_billing']," selected").">$data_address[address_title]</option>";
 if($data_address['default_billing']){$default_billing_address_id=$data_address['id'];}
 }
 print "</select>
 <a href='index.php?action=addresses_add'><img src='$style[images]/add_small.gif' title=\"$phrases[add_new_address]\" border=0></a>
 <br>
 <div id=billing_address_fields_div>
 <table width=100%>
 <tr><td><b>$phrases[billing_name]</b></td><td><input type=text id='info_name' name=\"billing_info[name]\" size=30 value=\"".($billing_session['name'])."\"></td></tr>
 
 <tr><td><b>$phrases[country]</b></td><td><select id='info_country' name=\"billing_info[country]\">
 <option value=''>-- $phrases[select_from_menu] --</option>";
 $qr_c = db_query("select * from store_countries order by name asc");
 while($data_c = db_fetch($qr_c)){
     print "<option value=\"$data_c[code]\" ".iif($billing_session['country']==$data_c['code']," selected").">$data_c[name]</option>";
 }
 
 print "</select>
 </td></tr>
 
  <tr><td><b>$phrases[city]</b></td><td><input type=text id='info_city' name=\"billing_info[city]\" size=30 value=\"".($billing_session['city'])."\"></td></tr>
 <tr><td><b>$phrases[the_address]</b></td><td><input type=text id='info_address_1' name=\"billing_info[address_1]\" size=30 value=\"".($billing_session['address_1'])."\"></td></tr>
 <tr><td></td><td><input type=text id='info_address_2' name=\"billing_info[address_2]\" size=30 value=\"".($billing_session['address_2'])."\"></td></tr>
 
 <tr><td><b>$phrases[telephone]</b></td><td><input type=text id='info_telephone' name=\"billing_info[telephone]\" size=30 value=\"".($billing_session['telephone'])."\"></td></tr>
   
 </table>
 </div>  
  <div align='$global_align_x' id='address_loading_div' style=\"display:none;\"><img src='$style[images]/ajax_loading.gif'></div>   
 </fieldset>";
 if($default_billing_address_id){print "<script>get_saved_address($default_billing_address_id,'billing');</script>";}
 
 }

 //--------- shipping Address ------//
  if($op=="shipping"){
     
 $shipping_session = (array) $session->get("checkout_shipping");
 
 print "<br>

 <fieldset style=\"width:100%;\">
 <legend>$phrases[shipping_address]</legend>
 <select name='shipping_address_id' onChange=\"get_saved_address(this.value,'shipping');\">
 <option value=0>-- $phrases[saved_address] --</option>";
 $qr_address = db_query("select * from store_clients_addresses where client_id='$member_data[id]'");
 while($data_address = db_fetch($qr_address)){
 print "<option value='$data_address[id]'".iif($data_address['default_shipping']," selected").">$data_address[address_title]</option>";
 if($data_address['default_shipping']){$default_shipping_address_id=$data_address['id'];}
 }
 print "</select>
 <a href='index.php?action=addresses_add'><img src='$style[images]/add_small.gif' title=\"$phrases[add_new_address]\" border=0></a>
 <br>
 <div id=shipping_address_fields_div>
 <table width=100%>
 <tr><td><b>$phrases[shipping_name]</b></td><td><input type=text  id='info_name' name=\"shipping_info[name]\" size=30 value=\"".($shipping_session['name'])."\"></td></tr>
 
 <tr><td><b>$phrases[country]</b></td><td><select id='info_country' name=\"shipping_info[country]\">";
 $qr_c = db_query("select * from store_countries order by name asc");
 while($data_c = db_fetch($qr_c)){
     print "<option value=\"$data_c[code]\" ".iif($shipping_session['country']==$data_c['code']," selected").">$data_c[name]</option>";
 }
 
 print "</select>
 </td></tr>
 
  <tr><td><b>$phrases[city]</b></td><td><input type=text id='info_city' name=\"shipping_info[city]\" size=30 value=\"".($shipping_session['city'])."\"></td></tr>
 <tr><td><b>$phrases[the_address]</b></td><td><input type=text id='info_address_1' name=\"shipping_info[address_1]\" size=30 value=\"".($shipping_session['address_1'])."\"></td></tr>
 <tr><td></td><td><input type=text id='info_address_2' name=\"shipping_info[address_2]\" size=30 value=\"".($shipping_session['address_2'])."\"></td></tr>
 
 <tr><td><b>$phrases[telephone]</b></td><td><input type=text id='info_telephone' name=\"shipping_info[telephone]\" size=30 value=\"".($shipping_session['telephone'])."\"></td></tr>
   
 </table>
 </div>  
  <div align='$global_align_x' id='address_loading_div' style=\"display:none;\"><img src='$style[images]/ajax_loading.gif'></div>   
 </fieldset>";
 if($default_shipping_address_id){print "<script>get_saved_address($default_shipping_address_id,'shipping');</script>";}
 
 } 
 //------- shipping method -----//   
 if($op=="shipping_method"){  
 if(count($shipping_ids)){
  $shipping_method_session = $session->get("checkout_shipping_method");    
 $qr_sm = db_query("select * from store_shipping_methods where (id IN (".implode(",",$shipping_ids).") or all_cats=1) and (min_price <= $total_price or min_price=0) and (max_price >= $total_price or max_price=0) and (min_weight <= $total_weight or min_weight=0) and (max_weight >= $total_weight or max_weight=0) and (min_items <= $total_items or min_items=0) and (max_items >= $total_items or max_items=0) order by ord asc");
  if(db_num($qr_sm)){
  print "<fieldset style=\"width:100%;\">  
  <legend>$phrases[shipping_method]</legend>";
  $c=0;
  $first_id = 0;
  while($data_sm = db_fetch($qr_sm)){
    if(!$first_id){$first_id =  $data_sm['id'];}  
      print "
            <input type='radio' name='shipping_method' id='shipping_method_{$c}' onClick=\"get_shipping_method_price($data_sm[id]);\" value=\"$data_sm[id]\"".iif(!$shipping_method_session && $c==0," checked",iif($shipping_method_session==$data_sm['id']," checked")).">
            <label for='shipping_method_{$c}'><span>$data_sm[name]</span></lable><br>";
   $c++;
  }
  print "
 </fieldset>
  <br />
 <fieldset>
  <legend>Shipping Price </legend>
<div id='shipping_method_price'></div>
   <div id='loading_div' style=\"display:none;\"><img src='images/ajax_loading.gif'></div>
  </fieldset>
 

<script>
get_shipping_method_price($first_id);
</script>
  ";
  }
  
  
 // if($default_shipping_address_id){print "<script>get_shipping_address_fields_div($default_shipping_address_id);</script>";}  
 //}
 
 }else{
     print "no shipping methods";
 }
 }
 //----------- billing method ----------------
 if($op=="billing_method"){
     $payment_method = $session->get('checkout_billing_method');
    
 print "<br>
 
 <fieldset style=\"width:100%;\">
 <legend>$phrases[payment_method]</legend>";
 $qr_p = db_query("select * from store_payment_methods where active=1 and (id IN (".implode(",",$payment_ids).") or all_cats=1) and (min_price <= $total_price or min_price=0) and (max_price >= $total_price or max_price=0) and (min_items <= $total_items or min_items=0) and (max_items >= $total_items or max_items=0) order by ord asc");
 $x=0;
 while($data_p = db_fetch($qr_p)){
 print "<table><tr><td width=5><input type=radio name='payment_method' value=\"$data_p[id]\"".iif($payment_method==$data_p['id'],"checked",iif(!$x," checked"))."></td>".iif($data_p['img'],"<td width=10><img src=\"$data_p[img]\"></td>")."<td>$data_p[name]</td></tr></table>"; 
   //  print "<input type=radio name=payment_method value=\"$data_p[id]\"".iif(!$x," checked")."> $data_p[name]<br>";
     $x++;
 }
 print "</fieldset>
 <br>  ";  
 }
 
 
 //--------------- review ------------------
 if($op=="review"){
     
     print "
  
 <fieldset style=\"width:100%;\">
 <legend> $phrases[the_items] </legend>
 <table width=100%>
 <tr><td>#</td><td><b>$phrases[the_name]</b></td><td><b>$phrases[the_count]</b></td><td><b>$phrases[the_price]</b></td></tr>
 <tr><td colspan=4><hr class='separate_line'>";
 
 
 $items_cats = array();
 $shipping_ids = array();
 
 for($i=0;$i<count($items);$i++){

$items[$i]['id'] = intval($items[$i]['id']);
$items[$i]['qty'] = intval($items[$i]['qty']);

    
$data = $items[$i]['data'];




//--------------------------------------------//

/*
print "<input type=hidden name=\"items[$i][id]\" value=\"".$items[$i]['id']."\">
<input type=hidden name=\"items[$i][qty]\" value=\"".$items[$i]['qty']."\">
<input type=hidden name=\"items[$i][hash]\" value=\"".$items[$i]['hash']."\">"; */

print "<tr>
<td><b>".($i+1)."</b></td><td>$data[info]";



print "</td><td>".$items[$i]['qty']."</td><td>$data[item_price] $settings[currency]</td></tr>

<tr><td colspan=4><hr class='separate_line' size=1></td></tr>";

 
 //if($data['can_shipping']){$can_shipping=1;}
 
 }
 
 $shipping_price = (float) $session->get('checkout_shipping_price');
 $shipping_method = (int) $session->get('checkout_shipping_method');
 
 
 print "<tr><td colspan=4 align='$global_align_x'>";
 
 if($shipping_method){
 print "<b>اجمالي السلع :</b> $total_price $settings[currency] <br />
 <b>طريقة التوصيل :</b> ".iif($shipping_price,$shipping_price." ".$settings['currency'],$phrases['free'])." <br />";
 }
 
 print "<b>$phrases[the_total] :</b> ".($total_price+$shipping_price)." $settings[currency] <br />";
 print "</td></tr></table></fieldset><br>";
 
 }
     
     
//-------------- done ---------------------------
 if($op=="confirm"){
     
 $payment_method = (int) $session->get('checkout_billing_method');
 $payment_info  = db_qr_fetch("select name from store_payment_methods where id='$payment_method'");
 
 $shipping_method = (int) $session->get('checkout_shipping_method'); 
 if($shipping_method){  
 $shipping_method_info  = db_qr_fetch("select name,default_status from store_shipping_methods where id='$shipping_method'");
 }
 
 $billing_info = (array) $session->get("checkout_billing");
 $shipping_info = (array) $session->get("checkout_shipping");  
 
 $shipping_price = (float) $session->get("checkout_shipping_price");  
 
 
 
 //----- default order status ------------
  if($shipping_method){  
  $data_status = db_qr_fetch("select id,name from store_orders_status where id='".$shipping_method_info['default_status']."'");   
  }else{
  $data_status = db_qr_fetch("select id,name from store_orders_status where `default`=1");   
  }
  //--------------------------------------
  
   
 
 db_query("insert into store_orders 
            (userid,payment_method_id,payment_method_name,shipping_method_id,shipping_method_name,date,
                status,status_text,
                shipping_name,shipping_address1,shipping_address2,shipping_telephone,shipping_country,shipping_city,
                billing_name,billing_address1,billing_address2,billing_telephone,billing_country,billing_city,shipping_price
 ) values (
            '$member_data[id]',
            '$payment_method',
            '".db_escape($payment_info['name'])."',
            '$shipping_method',
            '".db_escape($shipping_method_info['name'])."',
            '".time()."',
            '$data_status[id]',
            '".db_escape($data_status['name'])."',
            '".db_escape($shipping_info['name'])."',
            '".db_escape($shipping_info['address_1'])."',
            '".db_escape($shipping_info['address_2'])."',
            '".db_escape($shipping_info['telephone'])."',
            '".db_escape($shipping_info['country'])."',
            '".db_escape($shipping_info['city'])."',
            '".db_escape($billing_info['name'])."',
            '".db_escape($billing_info['address_1'])."',
            '".db_escape($billing_info['address_2'])."',
            '".db_escape($billing_info['telephone'])."',
            '".db_escape($billing_info['country'])."',
            '".db_escape($billing_info['city'])."',
            '".$shipping_price."'
 )");
 $order_id = db_inserted_id();
 
 //------- items -------
 
  for($i=0;$i<count($items);$i++){
      $data_item = $items[$i]['data'];
      
  db_query("insert into store_orders_items (name,price,qty,order_id,product_id) values ('".db_escape(strip_tags(str_replace("<br>","\n",$data_item['info'])))."','$data_item[item_price_single]','".intval($items[$i]['qty'])."','$order_id','$data[id]')");
  }
 
 $invc_url_client = "$scripturl/invoice.php?id=$order_id";
 $invc_url_admin = "$scripturl/".iif($admin_folder,$admin_folder,"admin")."/index.php?action=orders_edit&id=$order_id";
 
 
 $msg_srch_arr = array("{sitename}","{siteurl}","{order_number}","{admin_invoice_url}","{invoice_url}","{total_price}",
 "{payment_method_name}",
 "{billing_name}","{billing_telephone}","{billing_country}","{billing_city}","{billing_address1}","{billing_address2}",
 "{shipping_name}","{shipping_telephone}","{shipping_country}","{shipping_city}","{shipping_address1}","{shipping_address2}");
 $msg_rplc_arr = array($sitename,$scripturl,$order_id,$invc_url_admin,$invc_url_client,$total_price,$payment_info['name'],
 $billing_info['name'],$billing_info['telephone'],$billing_info['country'],$billing_info['city'],$billing_info['address_1'],$billing_info['address_2'],
 $shipping_info['name'],$shipping_info['telephone'],$shipping_info['country'],$shipping_info['city'],$shipping_info['address_1'],$shipping_info['shipping_2']);
 
 //--- pre-cache templates --//
 templates_cache(array('msg_new_order_client','msg_new_order_admin','msg_new_order_client_subject','msg_new_order_admin_subject')); 
 //---- replace vars ----//
 $msg_new_order_client = get_template('msg_new_order_client',$msg_srch_arr,$msg_rplc_arr) ;
 $msg_new_order_admin = get_template('msg_new_order_admin',$msg_srch_arr,$msg_rplc_arr) ;
 $msg_new_order_client_sbjct = get_template('msg_new_order_client_subject',$msg_srch_arr,$msg_rplc_arr) ;
 $msg_new_order_admin_sbjct = get_template('msg_new_order_admin_subject',$msg_srch_arr,$msg_rplc_arr) ; 
  
 //---------- send new order email ----------
 
 send_email($sitename,$settings['mailing_email'],$member_data['email'],$msg_new_order_client_sbjct,$msg_new_order_client,$settings['mailing_default_use_html'],$settings['mailing_default_encoding']);
 send_email($sitename,$settings['mailing_email'],$settings['admin_email'],$msg_new_order_admin_sbjct,$msg_new_order_admin,$settings['mailing_default_use_html'],$settings['mailing_default_encoding']);

 //------------------------------------------
 
   $session->set("checkout_shipping",'');
   $session->set("checkout_billing",'');
   $session->set("checkout_shipping_price",'');
   $session->set("checkout_shipping_method",'');
   $session->set("checkout_billing_method",'');
   
 run_template('checkout_done');
 
 } 
 
 
if($op != "login" && $op != "confirm"){ 
 print "</form>
 ";
 
if($steps[$prev_step]){ 
 print "
 <input type='button' value='$phrases[prev]' onClick=\"checkout_prev();\">";
}
 
if($steps[$next_step]){
print "
               
  <input style=\"float:$global_align_x; position:relative;\" type='button' value='$phrases[next]' onClick=\"checkout_next();\"> ";
}
}

 close_table();    
 }else{
     open_table();
     print "<center> $phrases[cart_is_empty] </center>";
     close_table();
 }
 
 
 //---------------------------------------------------
require(CWD . "/includes/framework_end.php"); 