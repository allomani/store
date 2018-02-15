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

define("THIS_PAGE","index"); 
require("global.php");


         
               
templates_cache(array('header','footer','block','table','page_head','js_functions','status_bar','center_banners','blocks_banners'));

//----------------- Disable Browsing ------------------
if($settings['enable_browsing']!="1"){
if(check_login_cookies()){
print "<table width=100% dir=$global_dir><tr><td><font color=red> $phrases[site_closed_for_visitors] </font></td></tr></table>";
}else{
print "<html>
<head>
<META http-equiv=Content-Type content=\"text/html; charset=windows-1256\">
</head>
<body>
<center><table width=50% style=\"border: 1px solid #ccc\"><tr><td> $settings[disable_browsing_msg] </td></tr></table></center>
</body>
</html>";
die();
}
}

//---------------- set vote expire ------------------------
if($action=="vote_add" && $vote_id){
if(!$settings['votes_expire_hours']){$settings['votes_expire_hours'] = 24 ; }
   if(!$_COOKIE['vote_added']){
  setcookie('vote_added', "1" , time() + ($settings['votes_expire_hours'] * 60 * 60),"/");
  }
        }
 



compile_hook('site_before_header');
site_header();
compile_hook('site_after_header');

print "<script>
JSFX_FloatDiv(\"status_bar\",0,0).flt(); 
</script>";

  if(!$blocks_width){
            $blocks_width = "17%" ;
            }
  



print "<table border=\"0\" width=\"100%\"  style=\"border-collapse: collapse\" dir=ltr>

         <tr>" ;
        //------------------------- Block Pages System ---------------------------
        function get_pg_view(){
                global $pg_view ,$action ;
        if($action=="votes" || $action == "vote_add"){
          $pg_view = "votes" ;
          }elseif(!$action){
           $pg_view = "main" ;
        }else{
        $pg_view = $action ;
        }
        if(!$pg_view){$pg_view = "main" ;}
        }
        //-----------------------------Pre Cache Blocks ---------------------------------
        get_pg_view();
        if(!in_array($pg_view,$actions_checks)){$c_pg_view = "none" ;$pg_view = "main" ;}else{ $c_pg_view =  $pg_view;}
        
         
         
         unset($blocks);
        
         $qr=db_query("select * from store_blocks where active=1 and ((pages like '%$pg_view,%' and (pos='l' or pos='r')) or (pages like '%$c_pg_view,%' and pos='c')) order by pos,ord"); 
         while($data = db_fetch($qr)){
         $blocks[$data['pos']][$data['cat']][] = $data;
         }
       //-------------- Pre Cache Banners ----------------// 
        unset($banners);
         $qr=db_query("select * from store_banners where active=1 and ((pages like '%$pg_view,%' and (menu_pos='l' or menu_pos='r')) or (pages like '%$c_pg_view,%' and menu_pos='c')) order by `type`,menu_pos,ord"); 
         while($data = db_fetch($qr)){
             
         $data['menu_pos'] = iif($data['type']=="menu",$data['menu_pos'],"x");
         $data['menu_id'] =iif($data['type']=="menu",intval($data['menu_id']),0);;
         
         $banners[$data['type']][$data['menu_pos']][$data['menu_id']][] = $data;
         }
         
         
        unset($qr,$data);
       //----------------------- Left Content --------------------------------------------
      if(count($blocks['l'])){
        print "<td width='$blocks_width' valign=\"top\" dir=$global_dir>
        <center><table width=100%>" ;

        $adv_c = 1 ;
         foreach($blocks['l'][0] as $xdata){

        print "<tr>
                <td  width=\"100%\" valign=\"top\">";
                
   //     $sub_qr = db_query("select * from store_blocks where active=1 and cat='$xdata[id]' and pages like '%$pg_view,%' order by ord");
          $sub_count = count($blocks['l'][$xdata['id']]);
          
        
            open_block(iif(!$xdata['hide_title'] && !$sub_count,$xdata['title']),$xdata['template']);    
            
           if($sub_count){
              
               $tabs = new tabs("block_".$xdata['id']);
 
        $tabs->start($xdata['title']);
           run_php($xdata['file']);
          $tabs->end();  
          
          foreach($blocks['l'][$xdata['id']] as $sub_data){
           $tabs->start($sub_data['title']);
           run_php($sub_data['file']);
          $tabs->end();     
          }
          
           $tabs->run(); 
         
          
           }else{
               
               run_php($xdata['file']);           
                     
           } 
          close_block($xdata['template']);   
          
                print "</td>
        </tr>";

         //----------------Left block banners--------------------------
   
        if(count($banners['menu']['l'][$adv_c])){
       print_block_banners($banners['menu']['l'][$adv_c]);  
        unset($banners['menu']['l'][$adv_c]);
               }
            ++$adv_c ;
        //----------------------------------------------------
           }
           
//------- print remaining blocks banners --------//
if(count($banners['menu']['l'])){
    foreach($banners['menu']['l'] as $data_array){
         print_block_banners($data_array); 
    }
}

print "</table></center></td>";


//--------------------//

unset($xdata,$data,$adv_c);
}
 

print "<td  valign=\"top\" dir=$global_dir>";


//---------------------  Header Banners ----------------------------
//$qr = db_query("select * from store_banners where type='header' and active=1 and pages like '%$pg_view,%' order by ord");

 if(count($banners['header']['x'][0])){
  
 foreach($banners['header']['x'][0] as $data){
//while($data = db_fetch($qr)){
db_query("update store_banners set views=views+1 where id=$data[id]");
if($data['c_type']=="code"){
compile_template($data['content']);
    }else{
compile_template(get_template("center_banners")); 
}
        }
 print "<br>";
 }

//-------------------------- CENTER CONTENT ---------------------------------------------
 

     //--------- open banners ----------//
  //  $qr= db_query("select * from store_banners where type='open' and active=1 and pages like '%$pg_view,%' order by ord");
    $bnx = 0 ;
   if(count($banners['open']['x'][0])){
 foreach($banners['open']['x'][0] as $data){

    if ($data['url']){
     db_query("update store_banners set views=views+1 where id='$data[id]'");
   print "<script>
   banner_pop_open(\"$data[url]\",\"displaywindow_$bnx\");
       </script>\n";
         $bnx++;
          }

    }
   }
    
    //----------- close banners ----------- //
  
   print "<script>
   function pop_close(){";
    if(count($banners['close']['x'][0])){ 
      db_query("update store_banners set views=views+1 where id='$data[id]'");        
   foreach($banners['close']['x'][0] as $data){   
       print "banner_pop_close(\"$data[url]\",\"displaywindow_close_$data[id]\");";
       
       }
    }
       print " }
        </script>\n";
   

 $adv_c = 1 ;
  if(count($blocks['c'][0])){
         foreach($blocks['c'][0] as $ydata){

    
           $sub_count = count($blocks['c'][$ydata['id']]);    
        

           if($sub_count){
               open_table();
               $tabs = new tabs("block_".$ydata['id']);
 
        $tabs->start($ydata['title']);
           run_php($ydata['file']);
          $tabs->end();  
          
          foreach($blocks['c'][$ydata['id']] as $sub_data){    

           $tabs->start($sub_data['title']);
           run_php($sub_data['file']);
          $tabs->end();     
          }
          
           $tabs->run(); 
           print "<br>";
           close_table();
           }else{
               open_table(iif(!$ydata['hide_title'],$ydata['title']),$ydata['template']);     
               run_php($ydata['file']);           
               close_table($ydata['template']);          
           }   
             


      //----------------- Center Menus Banners-----------------------

       if(count($banners['menu']['c'][$adv_c])){
              print_block_banners($banners['menu']['c'][$adv_c],"center");  
        unset($banners['menu']['c'][$adv_c]);
          
       }
        //----------------------------------------------------
        ++$adv_c ;    
        
                    }
        //------- print remaining blocks banners --------//
if(count($banners['menu']['c'])){
    foreach($banners['menu']['c'] as $data_array){
         print_block_banners($data_array,"center"); 
    }
}
            
                    }
  unset($yqr,$ydata,$data,$adv_c); 
  
  //---------------------------  Browse ---------------------------------------
  if($action=="browse"){
 $cat=intval($cat);
 $hide_subcats = intval($hide_subcats);
   $include_subcats = intval($include_subcats); 
   
 
 compile_hook('browse_products_start');
 
 print_path_links($cat);
 
  compile_hook('browse_products_after_path_links'); 
          
//-------- cats -------
if(!$hide_subcats){
    $qr2 = db_query("select * from store_products_cats where cat='$cat' and active=1 order by ord asc");
if(db_num($qr2)){
   
    compile_hook('browse_products_before_cats_table'); 
    open_table();
     
    
    print "<table width=100%><tr>";
    $c=0;
while($data = db_fetch($qr2)){
    if ($c==$settings['img_cells']) {
print "  </tr><TR>" ;
$c = 0 ;
}
   ++$c ;

   print "<td>";
   compile_template(get_template('browse_products_cats'));
     print " </td>";

}
print "</tr></table>";
close_table();
compile_hook('browse_products_after_cats_table');
  }else{
    
      $no_cats = true;
  }
}else{
     $no_cats = true;
}
 //------------------------
 
 
  
   

   //------------- Getting Required Fields Products Ids ------------//
     $field_option = array_remove_empty_values($field_option);
     $count_fields = count($field_option);
      $fields_ok_array = array(0);
      if(is_array($field_option)){
         
         for($i=0; $i < $count_fields;$i++) { 
            $key = key($field_option);
            $value = current($field_option);
          
          //     print $key ." | ".$value ."--" ;
            if($key && $value){
                
           $qrz = db_query("select product_id from store_fields_data where value='".db_escape($value)."'");  
           while($dataz=db_fetch($qrz)){ 
         
           if($fields_p_array[$dataz['product_id']] > 0){
            $fields_p_array[$dataz['product_id']] = $fields_p_array[$dataz['product_id']]+1 ;
            
           }else{
               $fields_p_array[$dataz['product_id']] = 1;
           }
           //---
           if($fields_p_array[$dataz['product_id']] == $count_fields){
              
                $fields_ok_array[] = $dataz['product_id'];
            }
           //----
           } 
            } 
            next($field_option);
        }
    }
    
     // print_r($fields_ok_array);
   
//---- order by filtering -------//    
if(!$orderby || !$settings['visitors_can_sort_products'] || !in_array($orderby,$orderby_checks)){$orderby=($settings['products_default_orderby'] ? $settings['products_default_orderby'] : "id");}
if(!$sort || !$settings['visitors_can_sort_products'] || !in_array($sort,array('asc','desc'))){$sort=($settings['products_default_sort'] ? $settings['products_default_sort'] : "asc");}



   
     //----------------------
   $start = intval($start);
   $price_from = intval($price_from);
   $price_to = intval($price_to);
 
    
    
   $perpage = $settings['products_perpage'];
 
    if(is_array($field_option) || $orderby != $settings['products_default_orderby'] || $sort !=$settings['products_default_sort'] || $price_from || $price_to){
         $page_string = "index.php?action=browse&hide_subcats=$hide_subcats&include_subcats=$include_subcats&cat=$cat&start={start}&orderby=$orderby&sort=$sort";
         
         if($price_from){$page_string .=  "&price_from=$price_from";}
         if($price_to){$page_string .=  "&price_to=$price_to";} 
         
           if(is_array($field_option)){
         foreach($field_option as $key => $value) { 
            $page_string .= "&field_option[$key]=$value";
         }
           }
     $page_string .= "&start={start}";  
           
      }else{
   $page_string = str_replace('{id}',$cat,$links['links_browse_products_w_pages']);
      }
   //---------------------
   
   

    
    //----------- products Query ------------------------//  
    $sql_query = "select store_products_data.*,store_products_cats.name as cat_name,store_products_cats.id as cat_id from store_products_data,store_products_cats where ";
     
    $sql_where = "store_products_data.cat=store_products_cats.id and store_products_cats.active=1 and store_products_data.active=1"; 
   
   if($include_subcats){ 
   $cats_arr = get_products_cats($cat);
  
    $sql_where .= " and store_products_cats.id IN (".implode(',',$cats_arr).")";   
    
    //----- cache cats data ----//
   // $qr=db_query("select id,name from store_products_cats where id IN (".implode(',',$cats_arr).")
   }else{
       $sql_where .= " and store_products_cats.id='$cat'";
   }
    
    
  
    
    if($price_from){$sql_where .= " and price >= $price_from";}
    
    if($price_to){$sql_where .= " and price <= $price_to";} 
    
        
    
                                  
    if($count_fields){
    $sql_where .= " and  store_products_data.id IN (".implode(',',$fields_ok_array).") ";   
    }
    
    $sql_query .= $sql_where  ;
    $sql_query .= " order by ".iif($orderby=="name" && $global_lang=="arabic","binary")." store_products_data.$orderby ".iif($orderby=="available",iif($sort=="asc","desc","asc"),$sort)." limit $start,$perpage";
         // print   $sql_query;
    $qr = db_query($sql_query);
    //-----------------------------------------------------//
   
       
        
    if(db_num($qr)){
        
        $products_count  = db_qr_fetch("select count(store_products_data.id) as count from store_products_data,store_products_cats where $sql_where");
    //     $data_cat = db_qr_fetch("select name from store_products_cats where id='$cat'");
         
    
       
  compile_template(get_template('browse_products_header'));  
    $c=0;
        while($data = db_fetch($qr)){

    // $data_cat = db_qr_fetch("select name from store_products_cats where id='$cat'");    
    if($include_subcats){
     $data_cat['name'] = $data['cat_name'];
     $data_cat['id'] = $data['cat_id']; 
    }
      
     
if ($c==$settings['img_cells']) {
compile_template(get_template('browse_products_spect'));  
$c = 0 ;
}
    ++$c ;

    compile_template(get_template('browse_products'));


           }
         compile_template(get_template('browse_products_footer'));  
         
           
           
//-------------------- pages system ------------------------
print_pages_links($start,$products_count['count'],$perpage,$page_string); 
//-----------------------------
 
            }else{
                if($no_cats){
                 open_table();    
                    print "<center> $phrases[no_products] </center>";
                    close_table();
                }
                    }
          

 compile_hook('browse_products_end');         
  }
  
 //---------------- Product Details ----------
if($action=="product_details"){
require(CWD . "/product_details.php");
}
 
 //--------------------- Cart ---------------
 if($action=="cart"){

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
</script>

<form action='cart.php' method=post name='cart_form'>
<input type=hidden name='action' value='checkout'>";
 
for($i=0;$i<count($items);$i++){
    
   $items[$i]['id'] = intval($items[$i]['id']);
   $items[$i]['qty'] = intval($items[$i]['qty']);
$data = db_qr_fetch("select id,name,price,thumb from store_products_data where id='".$items[$i]['id']."'");

if($data['id']){   
print "<div id=\"cart_item_".$items[$i]['id']."\">";


$item_price = ($data['price']*$items[$i]['qty']) ;
print "
<input type=hidden name=\"items[$i][id]\" value=\"".$items[$i]['id']."\">
<table width=100%>
<tr>
<td width=120><img src='".get_image($data['thumb'])."' title=\"$data[name]\"></td>
<td><a href=\"".str_replace("{id}",$data['id'],$links['links_product_details'])."\" title=\"$data[name]\">".iif($data['name'],$data['name'],"-")."</a><br>
<br><img src='$style[images]/qty.gif'>&nbsp;<b>$phrases[count] : </b><input type=text name=\"items[$i][qty]\" size=1 value='".$items[$i]['qty']."'>
<br><img src='$style[images]/price.gif'>&nbsp;<b>$phrases[the_price] : </b>".$item_price." $settings[currency]</td>
<td width=10><a href=\"cart.php?action=item_delete&id=".$items[$i]['id']."\"><img src='$style[images]/del_small.gif' border=0 alt='$phrases[delete_from_cart]'></a></td>
</tr></table>";
//print_r(cart_exclude_item($items,$items[$i]['id']));


print "<hr class='separate_line' size=1></div>";


$total_price += $item_price; 
} 
} 

if($total_price){
print "<div align=$global_align_x><b>$phrases[the_total] : </b> $total_price $settings[currency]</div>";
}

print "
<img src='$style[images]/cart_refresh.gif'><a href=\"javascript:;\" onClick=\"cart_update_qty();\">$phrases[cart_update]</a>
<br>
<img src='$style[images]/cart_clear.gif'><a href='cart.php?action=cart_clear' onClick=\"return confirm('$phrases[are_you_sure]');\">$phrases[cart_clear]</a>
<br><br><center>
<input type=submit value='$phrases[checkout]'>
</form>";
}else{
print "<center>$phrases[cart_is_empty]</center>";
} 

close_table(); 
compile_hook('cart_end'); 
 }
 //--------------------- Checkout ----------
 if($action =="checkout"){
  if(check_member_login()){
  
 //---- check informations ------//
 $all_ok=1;
 //------------------------------//
 
 
 if($op=="confirm" && $all_ok){
 $payment_method = intval($payment_method);
 $payment_info  = db_qr_fetch("select name from store_payment_methods where id='$payment_method'");
 
 $shipping_method = intval($shipping_method);
 $shipping_method_info  = db_qr_fetch("select name from store_shipping_methods where id='$shipping_method'");
 
 
 
  if($shipping_info['name'] || $shipping_info['country'] || $shipping_info['address_1']){  
      $data_status = db_qr_fetch("select id,name from store_orders_status where `default_if_shipping`=1");   
  }else{
  $data_status = db_qr_fetch("select id,name from store_orders_status where `default`=1");   
  }
   
 
 db_query("insert into store_orders 
 (userid,payment_method_id,payment_method_name,shipping_method_id,shipping_method_name,date,
 status,status_text,
 shipping_name,shipping_address1,shipping_address2,shipping_telephone,shipping_country,shipping_city,
 billing_name,billing_address1,billing_address2,billing_telephone,billing_country,billing_city
 ) values('$member_data[id]',
 '$payment_method','".db_escape($payment_info['name'])."','$shipping_method','".db_escape($shipping_method_info['name'])."',
 now(),
 '$data_status[id]','".db_escape($data_status['name'])."',
 '".db_escape($shipping_info['name'])."','".db_escape($shipping_info['address_1'])."',
 '".db_escape($shipping_info['address_2'])."','".db_escape($shipping_info['telephone'])."',
 '".db_escape($shipping_info['country'])."','".db_escape($shipping_info['city'])."',
 '".db_escape($billing_info['name'])."','".db_escape($billing_info['address_1'])."',
 '".db_escape($billing_info['address_2'])."','".db_escape($billing_info['telephone'])."',
 '".db_escape($billing_info['country'])."',
 '".db_escape($billing_info['city'])."'
 )");
 $order_id = mysql_insert_id();
 
 //------- items -------
 $total_price = 0;
  for($i=0;$i<count($items);$i++){
      $data_item = db_qr_fetch("select id,name,price from store_products_data where id='".$items[$i]['id']."'");
      db_query("insert into store_orders_items (name,price,qty,order_id) values ('$data_item[name]','$data_item[price]','".intval($items[$i]['qty'])."','$order_id')");
  $total_price += ($data_item['price'] * $items[$i]['qty']);
  }
 
 $invc_url_client = "$scripturl/index.php?action=invoice&id=$order_id";
 $invc_url_admin = "$scripturl/".iif($admin_folder,$admin_folder,"admin")."/index.php?action=orders_edit&id=$order_id";
 
 //---- clean billing and shipping for email ----//
 if(is_array($billing_info)){
 foreach($billing_info as $key=>$value){
 $billing_info_clean[$key] = html_encode_chars($value);
 }
 }
 if(is_array($shipping_info)){
 foreach($shipping_info as $key=>$value){
 $shipping_info_clean[$key] = html_encode_chars($value);
 }
 }
 //-------------------------------//
 
 $msg_srch_arr = array("{sitename}","{siteurl}","{order_number}","{admin_invoice_url}","{invoice_url}","{total_price}",
 "{payment_method_name}",
 "{billing_name}","{billing_telephone}","{billing_country}","{billing_city}","{billing_address1}","{billing_address2}",
 "{shipping_name}","{shipping_telephone}","{shipping_country}","{shipping_city}","{shipping_address1}","{shipping_address2}");
 $msg_rplc_arr = array($sitename,$scripturl,$order_id,$invc_url_admin,$invc_url_client,$total_price,$payment_info['name'],
 $billing_info_clean['name'],$billing_info_clean['telephone'],$billing_info_clean['country'],$billing_info_clean['city'],$billing_info_clean['address_1'],$billing_info_clean['address_2'],
 $shipping_info_clean['name'],$shipping_info_clean['telephone'],$shipping_info_clean['country'],$shipping_info_clean['city'],$shipping_info_clean['address_1'],$shipping_info_clean['shipping_2']);
 
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
  
 compile_template(get_template('checkout_done'));
 

 
 }else{
 
 if(count($checkout_errors)){
     open_table();
     foreach($checkout_errors as $errtxt){
         print "<li>$errtxt</li>";
     }
     close_table();
 }
 
 $items = cart_items_array();
 
 if(count($items)){
 open_table("$phrases[checkout]");
 print "
 <form action='index.php' method=post>
 <input type=hidden name=action value=\"checkout\">
 <input type=hidden name=op value=\"confirm\">
  
 <fieldset><legend> $phrases[the_items] </legend>
 <table width=100%>
 <tr><td>#</td><td><b>$phrases[the_name]</b></td><td><b>$phrases[the_count]</b></td><td><b>$phrases[the_price]</b></td></tr>";
 
 $total_price = 0;
 
 $items_cats = array();
 $shipping_ids = array();
 
 for($i=0;$i<count($items);$i++){

$items[$i]['id'] = intval($items[$i]['id']);
$items[$i]['qty'] = intval($items[$i]['qty']);

    
$data = db_qr_fetch("select id,name,price,thumb,can_shipping,cat from store_products_data where id='".$items[$i]['id']."'");



//----- get items shared shipping methods -----//
if(!in_array($data['cat'],$items_cats)){
$items_cats[] = $data['cat'];

$cat_shipping = get_product_cat_shipping_methods($data['cat'],true);
  
if(count($shipping_ids)){
  
unset($tmp_arr);
  foreach($cat_shipping as $cat_shipping_id){
  
  if(in_array($cat_shipping_id,$shipping_ids)){
$tmp_arr[] = $cat_shipping_id ;  
  }  
  }
$shipping_ids  = $tmp_arr ;   
}else{
    $shipping_ids =  $cat_shipping ;
}
 unset($cat_shipping);
}

//--------------------------------------------//

$item_price = ($data['price']*$items[$i]['qty']) ;
print "<input type=hidden name=\"items[$i][id]\" value=\"".$items[$i]['id']."\">
<input type=hidden name=\"items[$i][qty]\" value=\"".$items[$i]['qty']."\">";

print "<td><b>".($i+1)."</b></td><td>$data[name]</td><td>".$items[$i]['qty']."</td><td>$item_price $settings[currency]</td></tr>";
 $total_price += $item_price;
 
 if($data['can_shipping']){$can_shipping=1;}
 
 }
 
 
 print "<tr><td colspan=4 align=$global_align_x>
 <b>$phrases[the_total] :</b> $total_price $settings[currency]
 </td></tr></table></fieldset><br>
 <fieldset>
 
 <legend>$phrases[billing_address]</legend>
 <b> $phrases[saved_address] : </b> <select name='billing_address_id' onChange=\"get_billing_address_fields_div(this.value);\">
 <option value=0>[ $phrases[not_saved] ]</option>";
 $qr_address = db_query("select * from store_clients_addresses where client_id='$member_data[id]'");
 while($data_address = db_fetch($qr_address)){
 print "<option value='$data_address[id]'".iif($data_address['default_billing']," selected").">$data_address[address_title]</option>";
 if($data_address['default_billing']){$default_billing_address_id=$data_address['id'];}
 }
 print "</select>
 <a href='index.php?action=addresses_add'><img src='$style[images]/add_small.gif' alt=\"$phrases[add_new_address]\" border=0></a>
 <br>
 <div id=billing_address_fields_div>
 <table width=100%>
 <tr><td><b>$phrases[billing_name]</b></td><td><input type=text name=\"billing_info[name]\" size=30></td></tr>
 
 <tr><td><b>$phrases[country]</b></td><td><select name=\"billing_info[country]\">";
 $qr_c = db_query("select * from store_countries order by name asc");
 while($data_c = db_fetch($qr_c)){
     print "<option value=\"$data_c[name]\">$data_c[name]</option>";
 }
 
 print "</select>
 </td></tr>
 
  <tr><td><b>$phrases[city]</b></td><td><input type=text name=\"billing_info[city]\" size=30></td></tr>
 <tr><td><b>$phrases[the_address]</b></td><td><input type=text name=\"billing_info[address_1]\" size=30></td></tr>
 <tr><td></td><td><input type=text name=\"billing_info[address_2]\" size=30></td></tr>
 
 <tr><td><b>$phrases[telephone]</b></td><td><input type=text name=\"billing_info[telephone]\" size=30></td></tr>
   
 </table>
 </div>  
  <div align='$global_align_x' id=billing_address_loading_div style=\"display:none;\"><img src='$style[images]/ajax_loading.gif'></div>   
 </fieldset>";
 if($default_billing_address_id){print "<script>get_billing_address_fields_div($default_billing_address_id);</script>";}
 //--------- shipping Address ------//
 if($can_shipping){
 print "<br>
   <fieldset> 
 <legend>$phrases[shipping_address]</legend>
 <b> $phrases[saved_address] : </b> <select name='shipping_address_id' onChange=\"get_shipping_address_fields_div(this.value);\">
 <option value=0>[ $phrases[not_saved] ]</option>";
 $qr_address = db_query("select * from store_clients_addresses where client_id='$member_data[id]'");
 while($data_address = db_fetch($qr_address)){
 print "<option value='$data_address[id]'".iif($data_address['default_shipping']," selected").">$data_address[address_title]</option>";
 if($data_address['default_shipping']){$default_shipping_address_id=$data_address['id'];}
 }
 print "</select>
 <a href='index.php?action=addresses_add'><img src='$style[images]/add_small.gif' alt=\"$phrases[add_new_address]\" border=0></a>
 <br>
 <div id=shipping_address_fields_div>
 <table width=100%>
 <tr><td><b>$phrases[shipping_name]</b></td><td><input type=text name=\"shipping_info[name]\" size=30></td></tr>
 
 <tr><td><b>$phrases[country]</b></td><td><select name=\"shipping_info[country]\">";
 $qr_c = db_query("select * from store_countries order by name asc");
 while($data_c = db_fetch($qr_c)){
     print "<option value=\"$data_c[name]\">$data_c[name]</option>";
 }
 
 print "</select>
 </td></tr>
 
  <tr><td><b>$phrases[city]</b></td><td><input type=text name=\"shipping_info[city]\" size=30></td></tr>
 <tr><td><b>$phrases[the_address]</b></td><td><input type=text name=\"shipping_info[address_1]\" size=30></td></tr>
 <tr><td></td><td><input type=text name=\"shipping_info[address_2]\" size=30></td></tr>
 
 <tr><td><b>$phrases[telephone]</b></td><td><input type=text name=\"shipping_info[telephone]\" size=30></td></tr>
   
 </table>
 </div>
 <div align='$global_align_x' id=shipping_address_loading_div style=\"display:none;\"><img src='$style[images]/ajax_loading.gif'></div>   
 </fieldset>";
 
    
 //------- shipping method -----//     
 if(count($shipping_ids)){
      
 $qr_sm = db_query("select * from store_shipping_methods where id IN (".implode(",",$shipping_ids).") order by ord asc");
  if(db_num($qr_sm)){
  print "<fieldset>  
 <table width=100%>
  <tr><td><b>$phrases[shipping_method]</b></td><td>
  <select name=shipping_method>";
  while($data_sm = db_fetch($qr_sm)){
      print "<option value=\"$data_sm[id]\">$data_sm[name]</option>";
  }
  print "</select></td></tr>   
 </table>
 </fieldset>
  ";
  }
  }
  
  if($default_shipping_address_id){print "<script>get_shipping_address_fields_div($default_shipping_address_id);</script>";}  
 }
 
 print "<br>
 
 <fieldset>
 <legend>$phrases[payment_method]</legend>";
 $qr_p = db_query("select * from store_payment_methods where active=1 order by ord asc");
 $x=0;
 while($data_p = db_fetch($qr_p)){
 print "<table><tr><td width=5><input type=radio name=payment_method value=\"$data_p[id]\"".iif(!$x," checked")."></td>".iif($data_p['img'],"<td width=10><img src=\"$data_p[img]\"></td>")."<td>$data_p[name]</td></tr></table>"; 
   //  print "<input type=radio name=payment_method value=\"$data_p[id]\"".iif(!$x," checked")."> $data_p[name]<br>";
     $x++;
 }
 print "</fieldset>
 <br>
 <fieldset>
 <center><input type=submit value=' $phrases[order_send] '></center>

 </fieldset>
 
 
 </form>";
 
 close_table();    
 }else{
     open_table();
     print "<center> $phrases[cart_is_empty] </center>";
     close_table();
 }
 }
 
 }else{
    login_redirect();
 }    
 }
 
 //---------------- Invoice ---------------
 if($action=="invoice"){
 if(check_member_login()){
     
 $id=intval($id);
 
 compile_hook('invoice_start');
 
 $qr = db_query("select * from store_orders where id='$id' and userid='$member_data[id]'");
 if(db_num($qr)){
 open_table("$phrases[the_invoice]");
 $data = db_fetch($qr);
 
 print "
 
 <fieldset><legend>$phrases[order_info]</legend>
 <table>
 <tr><td>
 <b>$phrases[order_date]  </b> </td><td>$data[date] </td></tr>";
 
 if($settings['show_paid_option']){
 print "<tr><td><b>$phrases[paid]  </b> </td><td>".iif($data['paid'],"<font color=green>$phrases[yes]</font>","<font color=red>$phrases[no]</font>")."</td></tr>";
 }
 
 print "<tr><td valign=top><b>$phrases[order_status] </b> </td><td> ";
 $qrst=db_query("select id,name,text_color,details,show_payment from store_orders_status where id='$data[status]'"); 
 if(db_num($qrst)){
     $datast=db_fetch($qrst);
     print iif($datast['text_color'],"<b><font color=\"$datast[text_color]\">$datast[name]</font></b>","<b>".$datast['name']."</b>");
     print iif($datast['details'], " <br> $datast[details]");
 }else{
     print iif($data['status_text'],$data['status_text'],"$phrases[not_available]");
 }
 
 print "</td></tr>";
 if($data['shipping_method_name']){
     print "<tr><td valign=top><b>$phrases[shipping_method] </b> </td><td>$data[shipping_method_name]</td></tr> ";  
 }
 
 print "</table></fieldset>
 
 <br>
 <fieldset><legend>$phrases[billing_address]</legend>
 <b>$phrases[billing_name] : </b> $data[billing_name] <br>
 <b>$phrases[country] : </b> $data[billing_country] <br> 
 <b>$phrases[city] : </b> $data[billing_city] <br>     
 <b>$phrases[the_address] : </b> $data[billing_address1] <br> $data[billing_address2] <br>
 <b>$phrases[telephone] : </b> $data[billing_telephone] <br>
 </fieldset>";
 
 if($data['shipping_name'] || $data['shipping_country'] || $data['shipping_address1']){
 
 print "
 <br><fieldset><legend>$phrases[shipping_address]</legend>
 <b>$phrases[shipping_name] : </b> $data[shipping_name] <br>
 <b>$phrases[country] : </b> $data[shipping_country] <br>
  <b>$phrases[city] : </b> $data[shipping_city] <br>   
 <b>$phrases[the_address] : </b> $data[shipping_address1] <br> $data[shipping_address2] <br>
 <b>$phrases[telephone] : </b> $data[shipping_telephone] <br>
 </fieldset>";
 }
 
 print "<br>
 <fieldset><legend> $phrases[the_items] </legend>
 <table width=100%>
 <tr><td>#</td><td><b>$phrases[the_name]</b></td><td><b>$phrases[the_count]</b></td><td><b>$phrases[the_price]</b></td></tr>";
 
 $total_price = 0;
 $qr_items = db_query("select * from store_orders_items where order_id='$data[id]'");
 
 while($data_items = db_fetch($qr_items)){
 
$item_price = ($data_items['price']*$data_items['qty']) ;


print "<td><b>".($i+1)."</b></td><td>$data_items[name]</td><td>".$data_items['qty']."</td><td>$item_price $settings[currency]</td></tr>";
 $total_price += $item_price;
 }
 
 print "<tr><td colspan=4 align=$global_align_x>
 <b>$phrases[the_total] :</b> $total_price $settings[currency]
 </td></tr></table></fieldset><br>";
 
 close_table();
 
 //------ Pay Invoice ------
 if(!$data['paid'] && $datast['show_payment']){
     open_table("$phrases[bill_payment]");
 print "<fieldset>
 <legend>$phrases[payment_method]</legend>";
 $qr_p = db_query("select * from store_payment_methods where active=1 order by ord asc");

 $i=0;
 while($data_p = db_fetch($qr_p)){
     
     if($i==0&&!$data['payment_method_id']){$data['payment_method_id']=$data_p['id'];}
     
     print "<table><tr><td width=5><input type=radio name=payment_method value=\"$data_p[id]\"".iif($data_p['id']==$data['payment_method_id']," checked")." onClick=\"show_payment_method_details(this.value,$id);\"></td>".iif($data_p['img'],"<td width=10><img src=\"$data_p[img]\"></td>")."<td>$data_p[name]</td></tr></table>";
  $i++;
 }
 print "</fieldset><br>
 
 
  
 <div id=\"payment_method_details_loading_div\" style=\"display:none;\"><img src='$style[images]/ajax_loading.gif'></div>
 
 <div id=\"payment_method_details_div\"></div>

 
 <script>
 show_payment_method_details($data[payment_method_id],$id);
 </script>";
 
 
     close_table();
     
 }
 
 
 }else{
 open_table();
 print "<center>$phrases[err_wrong_url]</center>";
 close_table();
 }       
 compile_hook('invoice_end');
 }else{
     login_redirect();
 }    
 }
//------------------------- Statics --------------------------
if($action=="statics"){
      $year = intval($year);
$month = intval($month);
 require(CWD . '/includes/functions_statics.php');


 //-------- browser and os statics ---------
if($settings['count_visitors_info']){
open_table("$phrases[operating_systems]");
get_statics_info("select * from info_os where count > 0 order by count DESC","name","count");
close_table();

open_table("$phrases[the_browsers]");
get_statics_info("select * from info_browser where count > 0 order by count DESC","name","count");
close_table();

$printed  = 1 ;
}

//--------- hits statics ----------
if($settings['count_visitors_hits']){
$printed  = 1 ;

if (!$year){$year = date("Y");}

open_table("$phrases[monthly_statics_for] $year ");

for ($i=1;$i <= 12;$i++){

$dot = $year;

if($i < 10){$x="0$i";}else{$x=$i;}


$sql = "select * from info_hits where date like '%-$x-$dot' order by date" ;
$qr_stat=db_query($sql);

if (db_num($qr_stat)){
$total = 0 ;
while($data_stat=db_fetch($qr_stat)){
$total = $total + $data_stat['hits'];
}

$rx[$i-1]=$total  ;

}else{
        $rx[$i-1]=0 ;
        }

  }

    for ($i=0;$i <= 11;$i++){
    $total_all = $total_all + $rx[$i];
         }

         if ($total_all !==0){

         print "<br>";

  $l_size = @getimagesize("$style[images]/leftbar.gif");
    $m_size = @getimagesize("$style[images]/mainbar.gif");
    $r_size = @getimagesize("$style[images]/rightbar.gif");


 echo "<table cellspacing=\"0\" cellpadding=\"2\" border=\"0\" align=\"center\">";
 for ($i=1;$i <= 12;$i++)  {

    $rs[0] = $rx[$i-1];
    $rs[1] =  substr(100 * $rx[$i-1] / $total_all, 0, 5);
    $title = $i;

    echo "<tr><td>";



   print " $title:</td><td dir=ltr align='$global_align'><img src=\"$style[images]/leftbar.gif\" height=\"$l_size[1]\" width=\"$l_size[0]\">";
    print "<img src=\"$style[images]/mainbar.gif\"  height=\"$m_size[1]\" width=". $rs[1] * 2 ."><img src=\"$style[images]/rightbar.gif\" height=\"$r_size[1]\" width=\"$l_size[0]\">
    </td><td>
    $rs[1] % ($rs[0])</td>
    </tr>\n";

}
print "</table>";
 }else{
        print "<center>$phrases[no_results]</center>";
        }
  print "<br><center>[ $phrases[the_year] : ";
  $yl = date('Y') - 3 ;
  while($yl != date('Y')+1){
      print "<a href='index.php?action=statics&year=$yl'>$yl</a> ";
      $yl++;
      }
  print "]";
close_table();

if (!$month){
        $month =  date("m")."-$year" ;
        }else{
                $month= "$month-$year";
                }

open_table("$phrases[daily_statics_for] $month ");
$dot = $month;
get_statics_info("select * from info_hits where date like '%$dot' order by date","date","hits");

print "<br><center>
          [ $phrases[the_month] :
          <a href='index.php?action=statics&year=$year&month=1'>1</a> -
          <a href='index.php?action=statics&year=$year&month=2'>2</a> -
          <a href='index.php?action=statics&year=$year&month=3'>3</a> -
          <a href='index.php?action=statics&year=$year&month=4'>4</a> -
          <a href='index.php?action=statics&year=$year&month=5'>5</a> -
          <a href='index.php?action=statics&year=$year&month=6'>6</a> -
          <a href='index.php?action=statics&year=$year&month=7'>7</a> -
          <a href='index.php?action=statics&year=$year&month=8'>8</a> -
          <a href='index.php?action=statics&year=$year&month=9'>9</a> -
          <a href='index.php?action=statics&year=$year&month=10'>10</a> -
          <a href='index.php?action=statics&year=$year&month=11'>11</a> -
          <a href='index.php?action=statics&year=$year&month=12'>12</a>
          ]";
          close_table();
}

if(!$printed){
    open_table();
   print "<center>$phrases[no_results]</center>";
    close_table();
    }

        }

 //------------------------------------- News -----------------------------------
  if($action == "news") {
 $id=intval($id);
          
  compile_hook('news_start');

if ($id){
    compile_hook('news_inside_start');
              $qr = db_query("select * from store_news where id='$id'");
              if(db_num($qr)){
              $data = db_fetch($qr);
       print "<img src='$style[images]/arrw.gif'>&nbsp;<a href='".str_replace('{id}',"0",$links['links_browse_news'])."'> $phrases[the_news] </a><br><br>";
      open_table($data['title']);
     compile_template(get_template('browse_news_inside'));
     close_table();
     }else{
     open_table();
     print "<center>$phrases[err_wrong_url]</center>";
     close_table();
             }
   compile_hook('news_inside_end');
        }else{

  compile_hook('news_outside_start');

          $qr = db_query("select left(date,7) as date from store_news group by left(date,7)");
          if(db_num($qr) > 1){
          open_table();
          print "<form action=index.php>
          <input type=hidden name=action value='news'>
           $phrases[the_date] : <select name=date>
           <option value=''> $phrases[all] </option>";
          while($data = db_fetch($qr)){
          if($date == $data['date']){$chk="selected" ;}else{$chk="";}

                  print "<option value='$data[date]' $chk>$data[date]</option>";
                  }
                  print "</select>&nbsp;<input type=submit value=' $phrases[view_do] '></form>";
                  close_table();
                  }
    compile_hook('news_outside_after_date');
           //----------------- start pages system ----------------------
    $start=intval($start);
    if(!$date){$date=0;}
       $page_string= str_replace('{date}',$date,$links['links_browse_news_w_pages']);
         $news_perpage = intval($settings['news_perpage']);
        //--------------------------------------------------------------


  
            open_table("$phrases[the_news_archive]");
            if($date){
            $qr = db_query("select * from store_news where date like '".db_escape($date)."%' order by id DESC limit $start,$news_perpage");
            $page_result = db_qr_fetch("SELECT count(*) as count from store_news where date like '$date%'");
            }else{
             $qr = db_query("select * from store_news order by id DESC limit $start,$news_perpage");
            $page_result = db_qr_fetch("SELECT count(*) as count from store_news");
            }

$numrows=$page_result['count'];


  if(db_num($qr)){
            print "<hr class=separate_line size=\"1\">";
            while ($data = db_fetch($qr)){
  
   compile_template(get_template('browse_news'));
       print "<hr class=separate_line size=\"1\">" ;
                    }
     }else{
             print "<center>$phrases[no_news]</center>" ;
             }
            close_table();
compile_hook('news_outside_before_pages');
//-------------------- pages system ------------------------
print_pages_links($start,$numrows,$news_perpage,$page_string);
//------------ end pages system -------------

compile_hook('news_outside_end');
 }
   compile_hook('news_end');
                  }
  //-------------------------------------------------------------------
  if($action=="contactus"){
      compile_hook('contactus_start');  
          open_table("$phrases[contact_us]");
         print get_template("contactus");
          close_table();
       compile_hook('contactus_end'); 
          }
 // --------------------------- Votes ---------------------------------
  if($action =="votes" || $action == "vote_add"){
      $vote_id = intval($vote_id);
      
      compile_hook('votes_start');  
      
          if ($action=="vote_add")
          {
           
               
            if(!$_COOKIE['vote_added']){
                  db_query("update store_votes set cnt=cnt+1 where id='$vote_id'");
                  }else{
                          open_table();

                          print "<center>".str_replace('{vote_expire_hours}',$settings['votes_expire_hours'],$phrases['err_vote_expire_hours'])."</center>" ;
                      close_table();
                      }

          }

 
 $qr =  db_query("select * from store_votes_cats where ".iif($id,"id='$id'","active=1")); 
 if(db_num($qr)){ 
  $data = db_fetch($qr);   
          open_table("$data[title]");


      
          $qr_stat=db_query("select * from store_votes where cat='$data[id]'");


if (db_num($qr_stat)){
while($data_stat=db_fetch($qr_stat)){
$votes[] = $data_stat;
$total = $total + $data_stat['cnt'];
}

    if($total){
         print "<br>";

  $l_size = @getimagesize("$style[images]/leftbar.gif");
    $m_size = @getimagesize("$style[images]/mainbar.gif");
    $r_size = @getimagesize("$style[images]/rightbar.gif");


 print "<table cellspacing=\"0\" cellpadding=\"2\" border=\"0\" align=\"center\">";
foreach($votes as $data_stat){

    $rs[0] = $data_stat['cnt'];
    $rs[1] =  substr(100 * $data_stat['cnt'] / $total, 0, 5);
    $title = $data_stat['title'];

    print "<tr><td>";


   print " $title:</td><td dir=ltr align='$global_align'><img src=\"$style[images]/leftbar.gif\" height=\"$l_size[1]\" width=\"$l_size[0]\">";
    print "<img src=\"$style[images]/mainbar.gif\"  height=\"$m_size[1]\" width=". $rs[1] * 2 ."><img src=\"$style[images]/rightbar.gif\" height=\"$r_size[1]\" width=\"$l_size[0]\">
    </td><td>
    $rs[1] % ($rs[0])</td>
    </tr>\n";

}
print "</table>";
}else{
        print "<center> $phrases[no_results] </center>";
        }
}else{
    print "<center> $phrases[no_options] </center>";
}
 }else{
     print "<center>$phrases[err_wrong_url]</center>";
 }

close_table();

if($settings['other_votes_show']){
  $qr = db_query("select id,title from store_votes_cats where ".iif($id,"id != '$id'","active != 1")." order by $settings[other_votes_orderby] limit $settings[other_votes_limit]");
if(db_num($qr)){  
open_table("$phrases[prev_votes]");
while($data=db_fetch($qr)){
    print "<li><a href='index.php?action=votes&id=$data[id]'>$data[title]</li>";
}
close_table();
}
}
 compile_hook('votes_end'); 
  }
 //------------------------------- Search -------------------------------------

 if($action=="search"){
     
 if($settings['enable_search']){ 
     
  
         $keyword = trim($keyword);
         
        if(strlen($keyword) >= $settings['search_min_letters']){
          
              $keyword = html_encode_chars($keyword); 
              
                compile_hook('search_start');   
      
       

 if(!$op){
      
   //----------------- start pages system ----------------------
    $start=intval($start);
       $page_string= "index.php?action=search&keyword=".urlencode($keyword)."&start={start}" ;
       $perpage = $settings['products_perpage'];
   //--------------------------------------------------------------
        
        
   
      if($full_text_search){  
         $qr=db_query("select *,match(name) against('".db_escape($keyword)."') as score from store_products_data where active=1 and match(name) against('".db_escape($keyword)."') order by score desc limit $start,$perpage");
         $products_count=db_qr_fetch("select count(*) as count from store_products_data where match(name) against('".db_escape($keyword)."') and active=1");
       
     
      }else{
              $qr=db_query("select * from store_products_data where name like '%".db_escape($keyword)."%' and active=1 order by id desc limit $start,$perpage");
             $products_count = db_qr_fetch("SELECT count(*) as count from store_products_data where name like '%".db_escape($keyword)."%' and active=1");
   
      }
     
       $cnt2 = db_num($qr) ;

         if($cnt2 > 0){
        
         
       
  compile_template(get_template('browse_products_header'));  
    $c=0;
        while($data = db_fetch($qr)){

  $data_cat = db_qr_fetch("select id,name from store_products_cats where id='$data[cat]'"); 

if ($c==$settings['img_cells']) {
compile_template(get_template('browse_products_spect'));  
$c = 0 ;
}
    ++$c ;

    compile_template(get_template('browse_products'));


           }
         compile_template(get_template('browse_products_footer')); 
        
//-------------------- pages system ------------------------
print_pages_links($start,$products_count['count'],$perpage,$page_string); 
//-----------------------------
 
              }else{
                  open_table();
                 print "<center>  $phrases[no_results] </center>";
                 close_table();   
                      }

//-----------------------------------------------------
}elseif($op=="news"){

  open_table("$phrases[search_results]" );   
              //----------------- start pages system ----------------------
    $start=intval($start);
       $page_string= "index.php?action=search&op=news&keyword=".urlencode($keyword)."&start={start}" ;
       $news_perpage = $settings['news_perpage'];
        //--------------------------------------------------------------


    
    if($full_text_search){   
    $qr = db_query("select *,match(`content`,`title`,`details`) against('".db_escape($keyword)."') as score from store_news where match(`content`,`title`,`details`) against('".db_escape($keyword)."') order by score desc limit $start,$news_perpage");
    $page_result = db_qr_fetch("select count(*) as count from store_news where match(`content`,`title`,`details`) against('".db_escape($keyword)."')");
    }else{
   $qr = db_query("select * from store_news where title like '%".db_escape($keyword)."%' or content  like '%".db_escape($keyword)."%' or details  like '%".db_escape($keyword)."%' order by id desc limit $start,$news_perpage");
       $page_result = db_qr_fetch("SELECT count(*) as count from store_news where title like '%".db_escape($keyword)."%' or content  like '%".db_escape($keyword)."%' or details  like '%".db_escape($keyword)."%'");
    }


$numrows=$page_result['count'];



    if(db_num($qr)){

       print "<hr class=separate_line size=\"1\">";
    while($data = db_fetch($qr)){

    $data['content'] = str_replace("$keyword","<font class=\"search_replace\">$keyword</font>",$data['content']);
  
  compile_template(get_template('browse_news'));  
       print "<hr class=separate_line size=\"1\">" ;


             }

//-------------------- pages system ------------------------
print_pages_links($start,$numrows,$settings['news_perpage'],$page_string);
//------------ end pages system -------------

            }else{
               print "<center>  $phrases[no_results] </center>";

        }
 close_table();          
        }
//-----------------------------------------------------


compile_hook('search_end'); 
//----------------
         }else{
         open_table();
         $phrases['type_search_keyword'] = str_replace('{letters}',$settings['search_min_letters'],$phrases['type_search_keyword']);
                 print "<center>  $phrases[type_search_keyword] </center>";
                 close_table();
                 }
                 
                 
}else{
 open_table();
 print "<center> $phrases[sorry_search_disabled]</center>";
 close_table();
     }


         }
 //---------------------------- Pages -------------------------------------
if($action=="pages"){
        $qr = db_query("select * from store_pages where active=1 and id='".intval($id)."'");

         compile_hook('pages_start');

         if(db_num($qr)){
         $data = db_fetch($qr);
          compile_hook('pages_before_data_table');
         open_table("$data[title]");
          compile_hook('pages_before_data_content');
                  run_php($data['content']);
           compile_hook('pages_after_data_content');
                  close_table();
          compile_hook('pages_after_data_table');
                  }else{
                  open_table();
                          print "<center> $phrases[err_no_page] </center>";
                          close_table();
                          }
             compile_hook('pages_end');
             }
//--------------------- Copyrights ----------------------------------
 if($action=="copyrights"){
     global $global_lang;

     open_table();
if($global_lang=="arabic"){
     print "<center>
     مرخص لـ : $_SERVER[HTTP_HOST]   من <a href='http://allomani.com/' target='_blank'>  اللوماني للخدمات البرمجية </a> <br><br>

   جميع حقوق البرمجة محفوظة
                        <a target=\"_blank\" href=\"http://allomani.com/\">
                       للوماني للخدمات البرمجية
                        © 2009";
  }else{
       print "<center>
     Licensed for : $_SERVER[HTTP_HOST]   by <a href='http://allomani.com/' target='_blank'>Allomani&trade; Programming Services </a> <br><br>

   <p align=center>
Programmed By <a target=\"_blank\" href=\"http://allomani.com/\"> Allomani&trade; Programming Services </a> © 2009";
      }
     close_table();
         }
//------------------ Register -------------------------
  if($action == "register" || $action=="register_complete_ok"){


 compile_hook('register_start');

open_table("$phrases[register]");

  if(!check_member_login()){
  if($settings['members_register']){


//---------- filter fields -----------------
$email = html_encode_chars($email);
$email_confirm = html_encode_chars($email_confirm);
$username = html_encode_chars($username);
$password = html_encode_chars($password);
$re_password = html_encode_chars($re_password);



   if($action=="register_complete_ok"){
      $all_ok = 1 ;

    //---------------- check security image ------------------
   if($settings['register_sec_code']){
   if(!$sec_img->verify_string($sec_string)){
   print  "<li>$phrases[err_sec_code_not_valid]</li>";
    $all_ok = 0 ;
    }
    }

if(check_email_address($email)){

$exsists = db_qr_num("select ".members_fields_replace('id')." from ".members_table_replace('store_clients')." where ".members_fields_replace('email')."='".db_escape($email)."'",MEMBER_SQL);
      //------------- check email exists ------------
       if($exsists){
                         print "<li>$phrases[register_email_exists]<br>$phrases[register_email_exists2] <a href='index.php?action=forget_pass'>$phrases[click_here] </a></li>";
              $all_ok = 0 ;
           }
      }else{
       print "<li>$phrases[err_email_not_valid]</li>";
      $all_ok = 0;
      }
       

        //------- username min letters ----------
       if(strlen($username) >= $settings['register_username_min_letters']){
       $exclude_list = explode(",",$settings['register_username_exclude_list']) ;

         if(!in_array($username,$exclude_list)){

     $exsists2 = db_qr_num("select ".members_fields_replace('id')." from ".members_table_replace('store_clients')." where ".members_fields_replace('username')."='".db_escape($username)."'",MEMBER_SQL);

       //-------------- check username exists -------------
            if($exsists2){
                         print(str_replace("{username}",$username,"<li>$phrases[register_user_exists]</li>"));
                $all_ok = 0 ;
           }
           }else{
           print "<li>$phrases[err_username_not_allowed]</li>";
         $all_ok= 0;
               }
          }else{
         print "<li>$phrases[err_username_min_letters]</li>";
         $all_ok= 0;
          }
       //----------------- check required fields ---------------------
        if($email && $email_confirm && $password && $re_password && $username){

        if($password != $re_password){
        print "<li>$phrases[err_passwords_not_match]</li>";
        $all_ok = 0 ;
        }

        if($email != $email_confirm){
        print "<li>$phrases[err_emails_not_match]</li>";
        $all_ok = 0 ;
        }



        }else{
        print  "<li>$phrases[err_fileds_not_complete]</li>";
         $all_ok = 0 ;
            }

//--------------- check required custom fields -------------
if(is_array($custom) && is_array($custom_id)){

   for($i=0;$i<=count($custom);$i++){
   if($custom_id[$i]){
       $m_custom_id=intval($custom_id[$i]);
   $qx = db_qr_fetch("select name,required from store_clients_sets where id='$m_custom_id'");


   if($qx['required']==1 && trim($custom[$i])==""){
   print  "<li>$phrases[err_fileds_not_complete]</li>";
         $all_ok = 0 ;
         break;
       }
   }
   }
   }

//----------------------------------------

 }


 if($all_ok){

if($settings['auto_email_activate']){
    $member_group = $members_connector['allowed_login_groups'][0] ;
    }else{
    $member_group = $members_connector['waiting_conf_login_groups'][0] ;
    }


   db_query("insert into ".members_table_replace('store_clients')." (".members_fields_replace('email').",".members_fields_replace('username').",".members_fields_replace('date').",".members_fields_replace('usr_group').",".members_fields_replace('birth').",".members_fields_replace('country').")
  values('".db_escape($email)."','".db_escape($username)."','".connector_get_date(date("Y-m-d H:i:s"),'member_reg_date')."','$member_group','".connector_get_date("$date_y-$date_m-$date_d",'member_birth_date')."','".db_escape($country)."')",MEMBER_SQL);


    $member_id=mysql_insert_id();


//------------- Custom Fields  ------------------
   if(is_array($custom) && is_array($custom_id)){
   for($i=0;$i<=count($custom);$i++){
   if($custom_id[$i] && $custom[$i]){
   $m_custom_id=intval($custom_id[$i]);
   $m_custom_name =$custom[$i] ;
   db_query("insert into store_clients_fields (member,cat,value) values('$member_id','$m_custom_id','".db_escape($m_custom_name)."')");

       }
   }
   }
//-----------------------------------------------



   connector_member_pwd($member_id,$password,'update');
   connector_after_reg_process();

   if($settings['auto_email_activate']){
       print "<center>  $phrases[reg_complete] </center>";
   }else{
   print "<center>  $phrases[reg_complete_need_activation] </center>";
   snd_email_activation_msg($member_id);
   }

           }else{

 compile_hook('register_before_fields');
print "<script type=\"text/javascript\" language=\"javascript\">
<!--
function pass_ver(theForm){
if ((theForm.elements['email'].value !='') && (theForm.elements['email'].value == theForm.elements['email_confirm'].value)){
if ((theForm.elements['password'].value !='') && (theForm.elements['password'].value == theForm.elements['re_password'].value)){
        if(theForm.elements['username'].value  && theForm.elements['sec_string'].value){
        return true ;
        }else{
       alert (\"$phrases[err_fileds_not_complete]\");
return false ;
}
}else{
alert (\"$phrases[err_passwords_not_match]\");
return false ;
}
}else{
alert (\"$phrases[err_emails_not_match]\");
return false ;
}
}
//-->
</script>

<form action=index.php method=post onsubmit=\"return pass_ver(this)\">
          <input type=hidden name=action value=register_complete_ok>
          <fieldset style=\"padding: 2\">


          <table width=100%><tr>
            <td width=20%> $phrases[username] :</td><td><input type=text name=username value='$username' onblur=\"ajax_check_register_username(this.value);\"></td><td id='register_username_area'></td> </tr>

           <tr><td colspan=2>&nbsp;</td></tr>
          <tr>  <td>  $phrases[password] : </td><td><input type=password name=password></td>   </tr>
          <tr>  <td>  $phrases[password_confirm] : </td><td><input type=password name=re_password></td>   </tr>


   <tr><td colspan=2>&nbsp;</td></tr>

          <td width=20%>$phrases[email] :</td><td><input type=text name=email value=\"$email\" onblur=\"ajax_check_register_email(this.value);\"></td><td id='register_email_area'></td> </tr>
          <td width=20%>$phrases[email_confirm] :</td><td><input type=text name=email_confirm value=\"$email_confirm\"></td> </tr>

         <tr><td colspan=2>&nbsp;</td></tr>
             </table>
            </fieldset>";

$cf = 0 ;

$qr = db_query("select * from store_clients_sets where required=1 order by ord");
   if(db_num($qr)){
    print "<br><fieldset style=\"padding: 2\">
    <legend>$phrases[req_addition_info]</legend>
<br><table width=100%>";

while($data = db_fetch($qr)){
    print "
    <input type=hidden name=\"custom_id[$cf]\" value=\"$data[id]\">
    <tr><td width=25%><b>$data[name]</b><br>$data[details]</td><td>";
    print get_member_field("custom[$cf]",$data);
        print "</td></tr>";
$cf++;
}
print "</table>
</fieldset>";
}

            print "<br><fieldset style=\"padding: 2\">
    <legend>$phrases[not_req_addition_info]</legend>
<br><table>
    <tr><td><b> $phrases[birth] </b> </td><td><select name='date_d'> <option value='00'></option>";
           for($i=1;$i<=31;$i++){
            if(strlen($i) < 2){$i="0".$i;}
           print "<option value=$i>$i</option>";
           }
           print "</select>
           - <select name=date_m> <option value='00'></option>";
            for($i=1;$i<=12;$i++){
             if(strlen($i) < 2){$i="0".$i;}
           print "<option value=$i>$i</option>";
           }
           print "</select>
           - <select name='date_y'>
           <option value='00'></option>";
           for($i=(date('Y')-10);$i>=(date('Y')-70);$i--){

           print "<option value='$i'>$i</option>";
           }
           print"</select></td></tr>
            <tr>  <td><b>$phrases[country] </b> </td><td><select name=country><option value=''> $phrases[select_from_menu] </option> ";


           $c_qr = db_query("select * from store_countries order by binary name asc");
   while($c_data = db_fetch($c_qr)){


        print "<option value='$c_data[name]' $chk>$c_data[name]</option>";
           }
           print "</select></td></tr>";

           $qr = db_query("select * from store_clients_sets where required=0 order by ord");
   if(db_num($qr)){

while($data = db_fetch($qr)){
    print "
    <input type=hidden name=\"custom_id[$cf]\" value=\"$data[id]\">
    <tr><td width=25%><b>$data[name]</b><br>$data[details]</td><td>";
    print get_member_field("custom[$cf]",$data);
        print "</td></tr>";
$cf++;
}
}

           print "</table>
           </fieldset>";


           print " <br><fieldset style=\"padding: 2\"><table width=100%><tr>";

           if($settings['register_sec_code']){
           print "<td><b>$phrases[security_code]</b></td><td>".$sec_img->output_input_box('sec_string','size=7')."</td>
           <td><img src=\"sec_image.php\" alt=\"Verification Image\" /></td>";
           }

           print "<td align=center><input type=submit value=' $phrases[register_do] '></td></tr>
          </table>
          </fieldset></form>";
    compile_hook('register_after_fields');
            }
        }else{
                print "<center>$phrases[register_closed]</center>";
                }
   }else{
           print "<center> $phrases[registered_before] </center>" ;
           }
           close_table();

 compile_hook('register_end');
          }
//---------------------------- Forget Password -------------------------
 if($action == "forget_pass" || $action=="lostpwd" ||  $action=="rest_pwd"){
     if($action == "forget_pass"){$action="lostpwd";}

        connector_members_rest_pwd($action,$useremail);
         }
//-------------------------- Resend Active Message ----------------
if($action=="resend_active_msg"){

   $qr = db_query("select * from ".members_table_replace('store_clients') ." where ".members_fields_replace('email')."='".db_escape($email)."'",MEMBER_SQL);
   if(db_num($qr)){
           $data = db_fetch($qr) ;
           open_table();
   if(in_array($data[members_fields_replace('usr_group')],$members_connector['allowed_login_groups'])){
    print "<center> $phrases[this_account_already_activated] </center>";
    }elseif(in_array($data[members_fields_replace('usr_group')],$members_connector['disallowed_login_groups'])){
            print "<center> $phrases[closed_account_cannot_activate] </center>";
    }elseif(in_array($data[members_fields_replace('usr_group')],$members_connector['waiting_conf_login_groups'])){
   snd_email_activation_msg($data[members_fields_replace('id')]);
   print "<center>  $phrases[activation_msg_sent_successfully] </center>";
   }
   close_table();
   }else{
           open_table();
           print "<center>  $phrases[email_not_exists] </center>";
           close_table();
           }
        }
//-------------------------- Active Account ------------------------
if($action == "activate_email"){
        open_table("$phrases[active_account]");
        $qr = db_query("select * from store_confirmations where code='".db_escape($code)."'");
if(db_num($qr)){
$data = db_fetch($qr);

$qr_member=db_query("select ".members_fields_replace('id')." from ".members_table_replace('store_clients') ." where ".members_fields_replace('id')."='$data[cat]'  and ".members_fields_replace('usr_group')."='".$members_connector['waiting_conf_login_groups'][0]."'",MEMBER_SQL);

 if(db_num($qr_member)){
      db_query("update ".members_table_replace('store_clients') ." set ".members_fields_replace('usr_group')."='".$members_connector['allowed_login_groups'][0]."' where ".members_fields_replace('id')."='$data[cat]'",MEMBER_SQL);
      db_query("delete from store_confirmations where code='".db_escape($code)."'");
    print "<center> $phrases[active_acc_succ] </center>" ;
 }else{
      print "<center> $phrases[active_acc_err] </center>" ;
 }
        }else{
      print "<center> $phrases[active_acc_err] </center>" ;
 }
        close_table();
        }

//-------------------------- Confirmations ------------------------
if($action == "confirmations"){
    //----- email change confirmation ------//
if($op=="member_email_change"){
open_table();
$qr=db_query("select * from store_confirmations where code='".db_escape($code)."' and type='".db_escape($op)."'");

if(db_num($qr)){
$data = db_fetch($qr);

      db_query("update ".members_table_replace('store_clients')." set ".members_fields_replace('email')."='".$data['new_value']."' where ".members_fields_replace('id')."='$data[cat]'",MEMBER_SQL);
      db_query("delete from store_confirmations where code='".db_escape($code)."'");
    print "<center> $phrases[your_email_changed_successfully] </center>" ;
}else{
     print "<center> $phrases[err_wrong_url] </center>" ;
}
 close_table();
}

        }
 //----------- Client CP ------//
 require(CWD . "/client_cp.php");
//------------------------ Members Login ---------------------------
 if($action=="login"){
 if(@file_exists("login_form.php")){
     include "login_form.php";
 }else{
    $re_link = html_encode_chars($re_link) ;

         open_table();
print "<script type=\"text/javascript\" src=\"js/md5.js\"></script>

<form method=\"POST\" action=\"login.php\" onsubmit=\"md5hash(password, md5pwd, md5pwd_utf, 1)\">

<input type=hidden name='md5pwd' value=''>
<input type=hidden name='md5pwd_utf' value=''>


<input type=hidden name=action value=login>
<input type=hidden name=re_link value=\"$re_link\">

<table border=\"0\" width=\"200\">
        <tr>
                <td height=\"15\"><span>$phrases[username] :</span></td>
                <td height=\"15\"><input type=\"text\" name=\"username\" size=\"10\"></td>
        </tr>
        <tr>
                <td height=\"12\"><span>$phrases[password]:</span></td>
                <td height=\"12\" ><input type=\"password\" name=\"password\" size=\"10\"></td>
        </tr>
        <tr>
                <td height=\"23\" colspan=2>
                <p align=\"center\"><input type=\"submit\" value=\"$phrases[login]\"></td>
        </tr>
        <tr>
                <td height=\"38\" colspan=2><span>
                <a href=\"index.php?action=register\">$phrases[newuser]</a><br>
                <a href=\"index.php?action=forget_pass\">$phrases[forgot_pass]</a></span></td>
        </tr>
</table>
</form>\n";
close_table();
 }
         }
 //--------------- Load Index Plugins --------------------------
$pls = load_plugins("index.php");
  if(is_array($pls)){foreach($pls as $pl){include($pl);}}
//---------------------  Footer Banners ------------------------------------------------------
//$qr = db_query("select * from store_banners where type='footer' and active=1 and pages like '%$pg_view,%' order by ord");
if(count($banners['footer']['x'][0])){
 foreach($banners['footer']['x'][0] as $data){
db_query("update store_banners set views=views+1 where id='$data[id]'");

if($data['c_type']=="code"){
    compile_template($data['content']);
    }else{
        compile_template(get_template("center_banners"));     
}
        }
 print "<br>";
}

//---------------------------Right Content--------------------------------------
print "</td>" ;


 //$zqr=db_query("select * from store_blocks where pos='r' and active=1 and cat=0 and pages like '%$pg_view,%' order by ord");

  if(count($blocks['r'][0])){
print "<td width='$blocks_width' valign=\"top\" dir=$global_dir>";

print "<center><table width=100%>";


             $adv_c= 1 ;
       foreach($blocks['r'][0] as $zdata){
        print "<tr>
                <td  width=\"100%\" valign=\"top\">";
                
           //     $sub_qr = db_query("select * from store_blocks where  active=1 and cat='$zdata[id]' and pages like '%$pg_view,%' order by ord");
          $sub_count = count($blocks['r'][$zdata['id']]);      
          
        
            open_block(iif(!$zdata['hide_title'] && !$sub_count,$zdata['title']),$zdata['template']);    
            
           if($sub_count){
              
               $tabs = new tabs("block_".$zdata['id']);
 
        $tabs->start($zdata['title']);
           run_php($zdata['file']);
          $tabs->end();  
          
          foreach($blocks['r'][$zdata['id']] as $sub_data){    

           $tabs->start($sub_data['title']);
           run_php($sub_data['file']);
          $tabs->end();     
          }
          
           $tabs->run(); 
         
          
           }else{
               
               run_php($zdata['file']);           
                     
           } 
          close_block($zdata['template']);  

                print "</td>
        </tr>";

              //---------------------------------------------------

         if(count($banners['menu']['r'][$adv_c])){
           
       print_block_banners($banners['menu']['r'][$adv_c]);  
        unset($banners['menu']['r'][$adv_c]);
               }
            ++$adv_c ;
        //----------------------------------------------------
           }
           
//------- print remaining blocks banners --------//
if(count($banners['menu']['r'])){
    foreach($banners['menu']['r'] as $data_array){
         print_block_banners($data_array); 
    }
}
   
print "</table></center></td>" ;
unset($zdata,$data,$adv_c); 
}
unset($zqr);
print "</tr></table>\n";


print_copyrights();

compile_hook('site_before_footer'); 
site_footer();
compile_hook('site_after_footer');                         
           
if($debug){                                                         
print "<br><div dir=ltr><b>Memory Usage :</b> " .  convert_number_format(memory_get_usage(),2,true,true);
print "<br><div dir=ltr><b>Queries :</b> " .  $queries."<br>"; 
print "</div>";
}
/*
function array_size($arr) {
  ob_start();
  print_r($arr);
  $mem = ob_get_contents();
  ob_end_clean();
  $mem = preg_replace("/\n +/", "", $mem);
  $mem = strlen($mem);
  return $mem;
}
print "<b>Phrases : </b>" .convert_number_format(array_size($phrases),2,true,true);
*/
?>
