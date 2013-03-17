<?
require("global.php");
require(CWD . "/includes/framework_start.php");
//-------------------------------------------------

if (check_member_login()) {

    $id = intval($id);

    compile_hook('invoice_start');

    $qr = db_query("select * from store_orders where id='$id' and userid='$member_data[id]'");
    if (db_num($qr)) {
        open_table("$phrases[the_invoice]");
        $data = db_fetch($qr);

        print "
 
 <fieldset><legend>$phrases[order_info]</legend>
 <table>
 <tr><td>
 <b>$phrases[order_date]  </b> </td><td>".get_date($data['date'],"d M Y H:i")." </td></tr>";

        if ($settings['show_paid_option']) {
            print "<tr><td><b>$phrases[paid]  </b> </td><td>" . iif($data['paid'], "<font color=green>$phrases[yes]</font>", "<font color=red>$phrases[no]</font>") . "</td></tr>";
        }

        print "<tr><td valign=top><b>$phrases[order_status] </b> </td><td> ";
        $qrst = db_query("select id,name,text_color,details,show_payment from store_orders_status where id='$data[status]'");
        if (db_num($qrst)) {
            $datast = db_fetch($qrst);
            print iif($datast['text_color'], "<b><font color=\"$datast[text_color]\">$datast[name]</font></b>", "<b>" . $datast['name'] . "</b>");
            print iif($datast['details'], " <br> $datast[details]");
        } else {
            print iif($data['status_text'], $data['status_text'], "$phrases[not_available]");
        }

        print "</td></tr>";
        if ($data['shipping_method_name']) {
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

        if ($data['shipping_name'] || $data['shipping_country'] || $data['shipping_address1']) {

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
 <tr><td>#</td><td><b>$phrases[the_name]</b></td><td><b>$phrases[the_count]</b></td><td><b>$phrases[the_price]</b></td></tr>
        <tr><td colspan=4><hr class='separate_line' size=1></td></tr>";

        $total_price = 0;
        $qr_items = db_query("select a.*,b.cat from store_orders_items a left join store_products_data b on b.id=a.product_id where a.order_id='$data[id]'");
$i=0;
$items = array();
        while ($data_items = db_fetch($qr_items)) {

            $item_price = ($data_items['price'] * $data_items['qty']);
            $items[$i]['data']['cat'] = $data_items['cat'];

            print "<tr><td><b>" . ($i + 1) . "</b></td><td>" . nl2br($data_items['name']) . "</td><td>" . $data_items['qty'] . "</td><td>$item_price $settings[currency]</td></tr>
                    <tr><td colspan=4><hr class='separate_line' size=1></td></tr>";
            $total_price += $item_price;
            $i++;
        }
$total_items = count($items);

 print "</table></fieldset><br>";

 
 //--------- Price Div -------------
 print "<div id='invoice_price_div' align='$global_align_x'>
     <table>";
 if($data['shipping_price']){
 print "<tr><td><b>Items :</b></td><td>$total_price $settings[currency]</td></tr>
        <tr><td><b>Shipphing :</b></td><td>$data[shipping_price] $settings[currency] </td></tr>";
 }
 
 print "<tr><td><b>$phrases[the_total] :</b></td><td>".($total_price +$data['shipping_price'])." $settings[currency]</td></tr>
     </table></div>";
 //--------------------------------
 
 
        close_table();

//------ Pay Invoice ------
        if (!$data['paid'] && $datast['show_payment']) {
            
            $payment_ids = items_available_payment_methods($items);
          
            open_table("$phrases[bill_payment]");
            print "<fieldset>
 <legend>$phrases[payment_method]</legend>";
            $qr_p = db_query("select * from store_payment_methods where active=1 and (id IN (".implode(",",$payment_ids).") or all_cats=1) and (min_price <= $total_price or min_price=0) and (max_price >= $total_price or max_price=0) and (min_items <= $total_items or min_items=0) and (max_items >= $total_items or max_items=0) order by ord asc");

            $i = 0;
            while ($data_p = db_fetch($qr_p)) {

                if ($i == 0 && !$data['payment_method_id']) {
                    $data['payment_method_id'] = $data_p['id'];
                }

                print "<table><tr><td width=5><input type=radio name=payment_method value=\"$data_p[id]\"" . iif($data_p['id'] == $data['payment_method_id'], " checked") . " onClick=\"show_payment_method_details(this.value,$id);\"></td>" . iif($data_p['img'], "<td width=10><img src=\"$data_p[img]\"></td>") . "<td>$data_p[name]</td></tr></table>";
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
    } else {
        open_table();
        print "<center>$phrases[err_wrong_url]</center>";
        close_table();
    }
    compile_hook('invoice_end');
} else {
    login_redirect();
}
//---------------------------------------------------
require(CWD . "/includes/framework_end.php");
?>