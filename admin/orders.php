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

// Edited : 07-10-2009

  if(!defined('IS_ADMIN')){die('No Access');} 
 
//------------- Orders --------------//
if($action=="orders" ||  $action=="orders_del"){

    if_admin("orders");
    
//----- del ----
if($action=="orders_del"){
    db_query("delete from store_orders where id='$id'");
    db_query("delete from store_orders_items where order_id='$id'");
}    


print "<p align=center class=title>$phrases[the_orders]</p>

<a href='index.php?action=orders_search'><img src='images/search.gif' border=0>&nbsp; $phrases[search]</a><br><br>";

//--------------------------
$start = intval($start);
$perpage = $settings['orders_perpage'];
 
//-------------------------

if($op=="search"){
$query_string = $_SERVER['QUERY_STRING'];
$query_string = iif(strchr($query_string,"&start="),$query_string,$query_string."&start=0");
$page_string = html_encode_chars("index.php?".substr($query_string,0,strpos($query_string,"&start="))."&start={start}"); 

 

 
 //------------
    
 $sql_where = "where 
id like '%".iif($order_id,intval($order_id),"")."%' and 
date like '%".db_escape($order_date)."%' and paid like '%".db_escape($paid)."%'
".iif($status !="all" && isset($status),iif($status," and status='".intval($status)."'",iif($status_text," and status_text like '%".db_escape($status_text)."%'"," and status=0")))."
".iif($payment_method_id !="all" && isset($payment_method_id),iif($payment_method_id," and payment_method_id='".intval($payment_method_id)."'",iif($payment_method_name," and payment_method_name like '%".db_escape($payment_method_name)."%'"," and payment_method_id=0")))."
".iif($shipping_method_id !="all" && isset($shipping_method_id),iif($shipping_method_id," and shipping_method_id='".intval($shipping_method_id)."'",iif($shipping_method_name," and shipping_method_name like '%".db_escape($shipping_method_name)."%'"," and shipping_method_id=0")))."
 and  
billing_name like '%".db_escape($billing_name)."%' and
billing_country like '%".db_escape($billing_country)."%' and
billing_city like '%".db_escape($billing_city)."%' and
billing_address1 like '%".db_escape($billing_address1)."%' and  
billing_address2 like '%".db_escape($billing_address2)."%' and
billing_telephone like '%".db_escape($billing_telephone)."%' and 

shipping_name like '%".db_escape($shipping_name)."%' and
shipping_country like '%".db_escape($shipping_country)."%' and
shipping_city like '%".db_escape($shipping_city)."%' and
shipping_address1 like '%".db_escape($shipping_address1)."%' and  
shipping_address2 like '%".db_escape($shipping_address2)."%' and
shipping_telephone like '%".db_escape($shipping_telephone)."%'";


 //-------------   
 if($username){
     $qru = db_query("select id from store_clients where username like '%".db_escape($username)."%'");
     if(db_num($qru)){
        
     while($datau=db_fetch($qru)){
         $orders_users[] = $datau['id'];
     }
     $sql_where .= "and userid IN (".implode(",",$orders_users).") ";
     unset($orders_users);
     }else{
      $sql_where .= " and userid=0 ";   
     }
 }
   
    
    
}else{
    $page_string = "index.php?action=orders&start={start}";
  $sql_where = "";  
}


$qr = db_query("select * from store_orders $sql_where order by id desc limit $start,$perpage"); 
 
if(db_num($qr)){
    $orders_count = db_qr_fetch("select count(id) as count from store_orders $sql_where");
 
 //---- sync statuses -----
 $qrst=db_query("select id,name,text_color from store_orders_status");
 while($datast=db_fetch($qrst)){
 $status_texts[$datast['id']]['name'] = $datast['name'];
 $status_texts[$datast['id']]['text_color'] = $datast['text_color'];  
 }
 //-------------------------
   
    print "
    <b> $phrases[the_orders_count] : </b> $orders_count[count]<br><br>
    <center><table width=99% class=grid>
    <tr>
    <td><b>$phrases[the_order_number]</b></td>
    <td><b>$phrases[order_status]</b></td>
    <td><b>$phrases[order_date]</b></td> 
    <td><b>$phrases[client_account]</b></td>
    <td><b>$phrases[billing_name]</b></td>
    <td><b>$phrases[the_options]</b></td>
    </tr>
    ";
   
 while($data = db_fetch($qr)){
     
if($tr_class==="row_1"){
$tr_class = "row_2";
}else{
$tr_class="row_1";
}


     $data_user = db_qr_fetch("select id,username from store_clients where id='$data[userid]'");
     print "<tr class='$tr_class'><td><a href='index.php?action=orders_edit&id=$data[id]'>$data[id]</a></td>
     <td><b>";
    
    if($status_texts[$data['status']]['name']){
     print iif($status_texts[$data['status']]['text_color'],"<font color=\"".$status_texts[$data['status']]['text_color']."\">".$status_texts[$data['status']]['name']."</font>",$status_texts[$data['status']]['name']);
 }else{
     print iif($data['status_text'],$data['status_text'],"$phrases[not_available]");
 }
 
    print "</b></td>
     <td>$data[date]</td> 
     <td><a href='index.php?action=client_edit&id=$data_user[id]'>$data_user[username]</a></td>
     <td>$data[billing_name]</td>
     <td><a href='index.php?action=orders_edit&id=$data[id]'>$phrases[edit]</a> - <a href='index.php?action=orders_del&id=$data[id]' onClick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete]</a></td>";
     
 }
 print "</table></center>";
 
 //-------------------- pages system ------------------------
print_pages_links($start,$orders_count['count'],$perpage,$page_string); 


}else{
    print_admin_table("<center> $phrases[no_orders] </center>");
}    
}

//------------ Orders Search --------
if($action=="orders_search"){

      if_admin("orders");  
      
      
print "<p align=center class=title>$phrases[orders_seach]</p> 


  <center>
  <form action=index.php method=get>
  <input type=hidden name=action value='orders'>
  <input type=hidden name=op value='search'>
  
 <fieldset>
 <legend>$phrases[order_info]</legend> 
  <table width=100%>
  <tr><td><b>$phrases[the_order_number]</b></td><td><input type=text name=order_id size=10 dir=ltr></td></tr> 
  
  <tr><td><b>$phrases[username]</b></td><td>
  <input type=text name=username size=20>
  </td></tr>
 
 <tr><td><b>$phrases[order_date]</b></td><td><input type=text name=\"order_date\"  dir=ltr size=30></td></tr>
 <tr><td><b>$phrases[paid]</b></td><td>
 <select name='paid'>
 <option value=''>$phrases[all]</option>
 <option value='0'>$phrases[no]</option>
 <option value='1'>$phrases[yes]</option>
 </select>
 </td></tr>  

 <tr><td><b>$phrases[payment_method]</b></td><td><select name='payment_method_id' onChange=\"set_payment_method_text_display(this.value);\">";
 $qrpm=db_query("select id,name from store_payment_methods where active=1 order by ord");
  print "<option value='all'>$phrases[all]</option> "; 
 while($datapm=db_fetch($qrpm)){
 print "<option value='$datapm[id]'>$datapm[name]</option>";
 }
 print "<option value=0>$phrases[other]</option>
 </select>
<br>
 <input type=text name=payment_method_name size=30 style=\"display:none;\"></td></tr>

 <tr><td><b>$phrases[shipping_method]</b></td><td><select name='shipping_method_id' onChange=\"set_shipping_method_text_display(this.value);\">";
 $qrpm=db_query("select id,name from store_shipping_methods where active=1 order by ord");
  print "<option value='all'>$phrases[all]</option> "; 
 while($datapm=db_fetch($qrpm)){
 print "<option value='$datapm[id]'>$datapm[name]</option>";
 }
 print "<option value=0>$phrases[other]</option>
 </select>
<br>
 <input type=text name=shipping_method_name size=30 style=\"display:none;\"></td></tr>
 
   
 <tr><td><b>$phrases[order_status]</b></td><td><select name='status' onChange=\"set_status_text_display(this.value);\">";
 $qrst=db_query("select id,name from store_orders_status where active=1 order by ord");
 print "<option value='all'>$phrases[all]</option> "; 
 while($datast=db_fetch($qrst)){
 print "<option value='$datast[id]'>$datast[name]</option>";
 }
print "<option value=0>$phrases[other]</option> ";

 print "
 </select>
 <br>
 <input type=text name=status_text size=30 style=\"display:none;\"></td></tr> 
 

 </table>
 </fieldset><br>
 
 <fieldset>
 <legend>$phrases[billing_address]</legend> 
 
 
 <table width=100%>
 <tr><td><b>$phrases[billing_name]</b></td><td><input type=text name=\"billing_name\" value=\"$data[billing_name]\" size=30></td></tr>
 
 <tr><td><b>$phrases[country]</b></td><td><select name=\"billing_country\">";
 $qr_c = db_query("select * from store_countries order by name asc");
 print "<option value=''></option>";
 while($data_c = db_fetch($qr_c)){
     print "<option value=\"$data_c[name]\">$data_c[name]</option>";
 }
 
 print "</select></td></tr>
 
 <tr><td><b>$phrases[city]</b></td><td><input type=text name=\"billing_city\" size=30></td></tr> 
 <tr><td><b>$phrases[the_address]</b></td><td><input type=text name=\"billing_address1\" size=30></td></tr>
 <tr><td></td><td><input type=text name=\"billing_address2\" size=30></td></tr>
 
 <tr><td><b>$phrases[telephone]</b></td><td><input type=text name=\"billing_telephone\" size=30></td></tr>
   
 </table>   
 </fieldset><br>
 
 
 <fieldset>
 <legend>$phrases[shipping_address]</legend> 
 
 
 <table width=100%>
 <tr><td><b>$phrases[shipping_name]</b></td><td><input type=text name=\"shipping_name\" value=\"$data[shipping_name]\" size=30></td></tr>
 
 <tr><td><b>$phrases[country]</b></td><td><select name=\"shipping_country\">";
 $qr_c = db_query("select * from store_countries order by name asc");
  print "<option value=''></option>";
 while($data_c = db_fetch($qr_c)){
     print "<option value=\"$data_c[name]\">$data_c[name]</option>";
 }
 
 print "</select></td></tr>
 
 <tr><td><b>$phrases[city]</b></td><td><input type=text name=\"shipping_city\" value=\"$data[shipping_city]\" size=30></td></tr> 
 <tr><td><b>$phrases[the_address]</b></td><td><input type=text name=\"shipping_address1\" value=\"$data[shipping_address1]\" size=30></td></tr>
 <tr><td></td><td><input type=text name=\"shipping_address2\" size=30 value=\"$data[shipping_address2]\"></td></tr>
 
 <tr><td><b>$phrases[telephone]</b></td><td><input type=text name=\"shipping_telephone\" size=30 value=\"$data[shipping_telephone]\"></td></tr>
   
 </table>   
 </fieldset><br>
 
 <input type=hidden name=start value='0'>
 
 <input type=submit value=' $phrases[search] '>
 </center>
 </form>";
 
 

    
}

//------------- Order Edit -------------
if($action=="orders_edit" || $action=="orders_edit_ok" || $action=="orders_item_del" || $action=="orders_item_add"){
    
      if_admin("orders");  
      
    $id = intval($id);
    
print "<img src='images/arrw.gif'>&nbsp;<a href='index.php?action=orders'>$phrases[the_orders]</a> / <a href='index.php?action=orders_edit&id=$id'>$phrases[order_number_x] $id</a><br><br>";


//----------------- update order  ---------------------------
//-----------------------------------------------------------
if($action=="orders_edit_ok"){
    
    //---- update items values ---
    for($i=0;$i<count($items);$i++){
     db_query("update store_orders_items set name='".db_escape($items[$i]['name'])."',qty='".intval($items[$i]['qty'])."',price='".db_escape($items[$i]['price'])."' where id='".intval($items[$i]['id'])."'"); 
    }
    
    //---- add new item ----
    if(trim($items_new_name)){
       db_query("insert into  store_orders_items (order_id,name,qty,price) values('$id','".db_escape($items_new_name)."','".intval($items_new_qty)."','".db_escape($items_new_price)."')");   
    }
    
    
    //----- get status text ---//
    if($status){
        $data_status = db_qr_fetch("select name from store_orders_status where id='".intval($status)."'");
        $status_text = $data_status['name'];
    }

    //----- notify customer if status changed ----//
    if($status_change_notify){
        $old_status = db_qr_fetch("select status,status_text,userid,billing_name from store_orders where id='$id'");
        if(($old_status['status'] != $status && $status!=0)|| ($status==0 &&  $old_status['status_text'] != $status_text)){
            //----send notification ---//
                
$invc_url = "$scripturl/index.php?action=invoice&id=$id"; 
                
$msg_srch_arr = array("{sitename}","{siteurl}","{order_number}","{invoice_url}","{old_status}","{new_status}","{billing_name}");
$msg_rplc_arr = array($sitename,$scripturl,$id,$invc_url,"\"".addslashes($old_status['status_text'])."\"","\"".addslashes($status_text)."\"",$old_status['billing_name']);
 
 //--- pre-cache templates --//
 templates_cache(array('msg_order_status_changed','msg_order_status_changed_subject')); 
 //---- replace vars ----//
 $msg_order_status_changed = get_template('msg_order_status_changed',$msg_srch_arr,$msg_rplc_arr) ;
 $msg_order_status_changed_sbjct = get_template('msg_order_status_changed_subject',$msg_srch_arr,$msg_rplc_arr) ;
 //----- get client email ----//
 $client_email = db_qr_fetch("select email from store_clients where id='$old_status[userid]'"); 
 //---------- send new order email ----------
 
 $snd = send_email($sitename,$settings['mailing_email'],$client_email['email'],$msg_order_status_changed_sbjct,$msg_order_status_changed,$settings['mailing_default_use_html'],$settings['mailing_default_encoding']);
            //-------------------------//
            if($snd){
            print_admin_table("<center><img src='images/done.gif'> &nbsp; $client_email[email] <br>$phrases[status_change_notify_sent]  </center>");
            }else{
                print_admin_table("<center><img src='images/send_faild.gif'> &nbsp; $client_email[email] <br> $phrases[status_change_notify_failed] </center>"); 
            }
            print "<br>";
         
        }
    }
    
      //----- get payment method text ---//
    if($payment_method_id){
        $payment_method_id = intval($payment_method_id);
        $data_payment_method = db_qr_fetch("select name from store_payment_methods where id='$payment_method_id'");
        $payment_method_name = $data_payment_method['name'];
        unset($data_payment_method);
    }
    
         //----- get shipping method text ---//
    if($shipping_method_id){
        $shipping_method_id = intval($shipping_method_id);
        $data_shipping_method = db_qr_fetch("select name from store_shipping_methods where id='$shipping_method_id'");
        $shipping_method_name = $data_shipping_method['name'];
        unset($data_shipping_method);
    }
    
    
//------ update order details -----
 
 db_query("update store_orders set payment_method_id='$payment_method_id',payment_method_name='".db_escape($payment_method_name)."',
 shipping_method_id='$shipping_method_id',shipping_method_name='".db_escape($shipping_method_name)."',
 shipping_name='".db_escape($shipping_name)."',
 shipping_address1='".db_escape($shipping_address1)."',shipping_address2='".db_escape($shipping_address2)."',
 shipping_telephone='".db_escape($shipping_telephone)."',shipping_country='".db_escape($shipping_country)."',
 shipping_city='".db_escape($shipping_city)."',
 billing_name='".db_escape($billing_name)."',billing_address1='".db_escape($billing_address1)."',
 billing_address2='".db_escape($billing_address2)."',
 billing_telephone='".db_escape($billing_telephone)."',
 billing_country='".db_escape($billing_country)."',billing_city='".db_escape($billing_city)."',
 paid='".intval($paid)."',date='".db_escape($date)."',status='".intval($status)."',status_text='".db_escape($status_text)."' where id='$id'");
    
}
//-----------------------------------------------------------
//-----------------------------------------------------------


//----- del item ----------
if($action=="orders_item_del"){
    db_query("delete from store_orders_items where id='".intval($item_id)."'");
}
//------- add item --------
if($action=="orders_item_add"){
    db_query("insert into  store_orders_items (order_id,name,qty,price) values('$id','".db_escape($new_item_name)."','".intval($new_item_qty)."','".db_escape($new_item_price)."')");
}
//-----------------------


    $qr = db_query("select * from store_orders where id='$id'");
    if(db_num($qr)){
        $data = db_fetch($qr);
//------- new Item Form and function ---------
print "<script>
function item_add(){                                              
document.forms['new_item_form'].elements['new_item_name'].value = document.forms['sender'].elements['items_new_name'].value
document.forms['new_item_form'].elements['new_item_qty'].value = document.forms['sender'].elements['items_new_qty'].value
document.forms['new_item_form'].elements['new_item_price'].value = document.forms['sender'].elements['items_new_price'].value
document.forms['new_item_form'].submit();
}

</script>
<form action='index.php' method=post name='new_item_form'>
<input type=hidden name=action value='orders_item_add'>
<input type=hidden name=id value='$id'>

<input type=hidden name='new_item_name' value=''>
<input type=hidden name='new_item_qty' value=''> 
<input type=hidden name='new_item_price' value=''>
</form>";
 


print "
<div align='$global_align_x'>
<table class=grid><tr><td>
<center>
<a href='index.php?action=orders_del&id=$id' onClick=\"return confirm('".$phrases['are_you_sure']."');\"><img src='images/del.gif' border=0><br>
$phrases[order_delete]</a>
</center>
</td></tr></table>
</div>
<br>

<form action='index.php' method=post name=sender>
 <input type=hidden name=action value=\"orders_edit_ok\">
 <input type=hidden name=id value='$id'>
 
  
 <fieldset><legend> $phrases[the_items] </legend>
 <table width=100%>
 <tr><td>#</td><td><b>$phrases[the_name]</b></td><td><b>$phrases[the_count]</b></td><td><b>$phrases[the_price]</b></td><td><b>$phrases[the_total_price]</b></td></tr>";
 
 $total_price = 0;
 $qr_items = db_query("select * from store_orders_items where order_id='$id' order by id asc");
 $i=0;
 while($data_items = db_fetch($qr_items)){
    

$item_price = ($data_items['price']*$data_items['qty']) ;


if($tr_color=="#F0F0F0"){
$tr_color = "#FFFFFF";
}else{
$tr_color="#F0F0F0";
}

                    
print "<tr bgcolor='$tr_color'>
<td><b>".($i+1)."</b></td>
<input type=hidden name=\"items[$i][id]\" value=\"$data_items[id]\">
<td><input type=text name=\"items[$i][name]\" value=\"$data_items[name]\" size=30></td>
<td><input type=text name=\"items[$i][qty]\" value=\"$data_items[qty]\" size=4></td>
<td><input type=text name=\"items[$i][price]\" value=\"$data_items[price]\" size=5></td>

<td>$item_price $settings[currency]</td>
<td width=10><a href='index.php?action=orders_item_del&id=$id&item_id=$data_items[id]' onClick=\"return confirm('".$phrases['are_you_sure']."');\"><img src='images/del.gif' border=0 alt='$phrases[delete]'></a></td>
</tr>";
 $i++;
 $total_price += $item_price;
 }
 
 
 //--------- new item field ------------
 if($tr_color=="#F0F0F0"){
$tr_color = "#FFFFFF";
}else{
$tr_color="#F0F0F0";
}

 print "<tr bgcolor='$tr_color'>
<td><b>".($i+1)."</b></td>
<td><input type=text name=\"items_new_name\" value=\"\" size=30></td>
<td><input type=text name=\"items_new_qty\" value=\"1\" size=4></td>
<td><input type=text name=\"items_new_price\" value=\"0\" size=5></td>

<td>---</td>
<td width=10><a href='javascript:;' onClick=\"item_add();\"><img src='images/add.gif' border=0 alt='$phrases[add]'></a></td>
</tr>";
//----------------------------------------

 print "<tr><td colspan=5 align=$global_align_x>
 <br>
 <b>$phrases[the_total] :</b> $total_price $settings[currency]
 </td></tr>
 

 
 
 </table>
 
 <br>
  <div align=$global_align_x>
 <input type=submit value=' $phrases[update_data] '>
 </div>
 </fieldset><br>
 
 <br>
 <fieldset>
 <legend>$phrases[order_info]</legend> 
  <table width=100%>
  <tr><td><b>$phrases[the_client]</b></td><td>";
  $data_client = db_qr_fetch("select username,id from store_clients where id='$data[userid]'");
  print "<a href=\"index.php?action=client_edit&id=$data_client[id]\">$data_client[username]</a>";
  print "</td></tr>
 
 <tr><td><b>$phrases[order_date]</b></td><td><input type=text name=\"date\" value=\"$data[date]\" dir=ltr size=30></td></tr>";
  if($settings['show_paid_option']){   
 print "<tr><td><b>$phrases[paid]</b></td><td>";
 print_select_row('paid',array("$phrases[no]","$phrases[yes]"),$data['paid']) ;
 print "</td></tr>";  
  }
 
 print "<tr><td><b>$phrases[payment_method]</b></td><td><select name='payment_method_id' onChange=\"set_payment_method_text_display(this.value);\">";
 $qrpm=db_query("select id,name from store_payment_methods where active=1 order by ord");
 while($datapm=db_fetch($qrpm)){
 print "<option value='$datapm[id]'";
 if($data['payment_method_id']==$datapm['id']){print "selected";$found_st=true;}
 print ">$datapm[name]</option>";
 }

 print "<option value=0";
 if(!$found_st){print " selected";$data['payment_method_id']=0;}
 print ">$phrases[other]</option>
 </select>
 <br>
 <input type=text name=payment_method_name size=30 value=\"$data[payment_method_name]\"></td></tr>";
 
  unset($found_st,$datapm,$qrpm);

 print "<tr><td><b>$phrases[shipping_method]</b></td><td><select name='shipping_method_id' onChange=\"set_shipping_method_text_display(this.value);\">";
 $qrpm=db_query("select id,name from store_shipping_methods where active=1 order by ord");
 while($datapm=db_fetch($qrpm)){
 print "<option value='$datapm[id]'";
 if($data['shipping_method_id']==$datapm['id']){print "selected";$found_st=true;}
 print ">$datapm[name]</option>";
 }

 print "<option value=0";
 if(!$found_st){print " selected";$data['shipping_method_id']=0;}
 print ">$phrases[other]</option>
 </select>
 <br>
 <input type=text name=shipping_method_name size=30 value=\"$data[shipping_method_name]\"></td></tr>";
 
  unset($found_st,$datapm,$qrpm); 
  
   
 print "<tr><td><b>$phrases[order_status]</b></td><td><select name='status' onChange=\"set_status_text_display(this.value);\">";
 $qrst=db_query("select id,name from store_orders_status where active=1 order by ord");
 while($datast=db_fetch($qrst)){
 print "<option value='$datast[id]'";
 if($data['status']==$datast['id']){print "selected";$found_st=true;}
 print ">$datast[name]</option>";
 }

 
 print "<option value=0";
 if(!$found_st){print " selected";$data['status']=0;}
 print ">$phrases[other]</option>
 </select>
 <br>
 <input type=text name=status_text size=30 value=\"$data[status_text]\">
 <input type=checkbox name=status_change_notify value=1 ".iif($settings['default_status_change_notify'],"checked")."> $phrases[notify_client_when_order_status_change]
 </td></tr> 
 <tr><td colspan=2> </td></tr>
 
 </table>
  <div align=$global_align_x>
 
 <input type=submit value=' $phrases[update_data] '>
 </div>
 </fieldset><br>

<script>
set_status_text_display(".$data['status'].");
set_payment_method_text_display(".$data['payment_method_id'].");
set_shipping_method_text_display(".$data['shipping_method_id']."); 
</script>

 <fieldset>
 <legend>$phrases[billing_address]</legend> 
 
 
 <table width=100%>
 <tr><td><b>$phrases[billing_name]</b></td><td><input type=text name=\"billing_name\" value=\"$data[billing_name]\" size=30></td></tr>
 
 <tr><td><b>$phrases[country]</b></td><td><select name=\"billing_country\">";
 $qr_c = db_query("select * from store_countries order by name asc");
 while($data_c = db_fetch($qr_c)){
     print "<option value=\"$data_c[name]\"".iif($data['billing_country']==$data_c['name']," selected").">$data_c[name]</option>";
 }
 
 print "</select></td></tr>
 
 <tr><td><b>$phrases[city]</b></td><td><input type=text name=\"billing_city\" value=\"$data[billing_city]\" size=30></td></tr> 
 <tr><td><b>$phrases[the_address]</b></td><td><input type=text name=\"billing_address1\" value=\"$data[billing_address1]\" size=30></td></tr>
 <tr><td></td><td><input type=text name=\"billing_address2\" size=30 value=\"$data[billing_address2]\"></td></tr>
 
 <tr><td><b>$phrases[telephone]</b></td><td><input type=text name=\"billing_telephone\" size=30 value=\"$data[billing_telephone]\"></td></tr>
   
 </table>   
 </fieldset><br>
 
 
 <fieldset>
 <legend>$phrases[shipping_address]</legend> 
 
 
 <table width=100%>
 <tr><td><b>$phrases[shipping_name]</b></td><td><input type=text name=\"shipping_name\" value=\"$data[shipping_name]\" size=30></td></tr>
 
 <tr><td><b>$phrases[country]</b></td><td><select name=\"shipping_country\">";
 $qr_c = db_query("select * from store_countries order by name asc");
 while($data_c = db_fetch($qr_c)){
     print "<option value=\"$data_c[name]\"".iif($data['shipping_country']==$data_c['name']," selected").">$data_c[name]</option>";
 }
 
 print "</select></td></tr>
 
 <tr><td><b>$phrases[city]</b></td><td><input type=text name=\"shipping_city\" value=\"$data[shipping_city]\" size=30></td></tr> 
 <tr><td><b>$phrases[the_address]</b></td><td><input type=text name=\"shipping_address1\" value=\"$data[shipping_address1]\" size=30></td></tr>
 <tr><td></td><td><input type=text name=\"shipping_address2\" size=30 value=\"$data[shipping_address2]\"></td></tr>
 
 <tr><td><b>$phrases[telephone]</b></td><td><input type=text name=\"shipping_telephone\" size=30 value=\"$data[shipping_telephone]\"></td></tr>
   
 </table>   
 </fieldset><br>";

print_admin_table("<center>
 <input type=submit value=' $phrases[update_data] '>    
 </center>");      
        
    }else{
        print_admin_table("<center>$phrases[err_wrong_url]</center>");
    }
}
