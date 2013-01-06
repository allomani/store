<?

//---------- Cart Item Info -----------
function cart_item_info($item) {
    global $settings, $phrases, $links;

    $data = db_qr_fetch("select id,name,price,thumb,weight,can_shipping,cat from store_products_data where id='" . $item['id'] . "'");
    $info = "<span class='cart_item_title'><a href=\"" . str_replace("{id}", $data['id'], $links['product_details']) . "\" title=\"$data[name]\" target=_blank><b>" . iif($data['name'], $data['name'], "-") . "</b></a></span><span class='cart_item_single_price'>  ($data[price] $settings[currency]) </span>";

    if ($data['id']) {
        $item_price_single = $data['price'];
//------------ options ----------------
        if (count($item['options'])) {
            $info .= "<br>";
            foreach ($item['options'] as $option) {

                if ($option['type'] == "select") {
                    if ($option['value']) {
                        $data_option_value = db_qr_fetch("select store_products_options_data.*,store_products_options.name as option_name from store_products_options_data,store_products_options where store_products_options.id=store_products_options_data.cat and store_products_options_data.id='" . intval($option['value']) . "'");
                        $info .= "<br>- <b>$data_option_value[option_name] : </b> $data_option_value[name] " . iif($data_option_value['price'], "($data_option_value[price_prefix]$data_option_value[price] $settings[currency])");

                        if ($data_option_value['price_prefix'] == "+") {
                            $item_price_single += (float) $data_option_value['price'];
                        } else {
                            $item_price_single -= (float) $data_option_value['price'];
                        }
                    }
                } elseif ($option['type'] == "checkbox") {
                    if (is_array($option['value'])) {
                        $data_option_value = db_qr_fetch("select name from store_products_options where id='" . intval($option['id']) . "'");
                        $data_option_data = db_fetch_all("select * from store_products_options_data where id IN (" . implode(",", $option['value']) . ")");

                        $info .= "<br>- <b>$data_option_value[name] : </b> <br> ";
                        foreach ($data_option_data as $d) {
                            $info .= "-- " . $d['name'] . " " . iif($d['price'], "($d[price_prefix]$d[price] $settings[currency])") . "<br>";

                            if ($d['price_prefix'] == "+") {
                                $item_price_single += (float) $d['price'];
                            } else {
                                $item_price_single -= (float) $d['price'];
                            }
                        }
                    }
                } else {
                    if ($option['value']) {
                        $data_option_value = db_qr_fetch("select name from store_products_options where id='" . intval($option['id']) . "'");
                        $info .= "<br>- <b>$data_option_value[name] : </b> $option[value] ";
                    }
                }
            }
            $info .= "<br>";
        }
//-----------------

        $data['info'] = $info;
        $data['item_price_single'] = (int) $item_price_single;
        $data['item_price'] = (int) ($item_price_single * $item['qty']);
    }



    return (array) $data;
}

//--------- Cart Item Delete ---------
function cart_item_delete($hash) {
    $items = cart_items_array();

    $items_arr = cart_exclude_item($items, $hash);

    cart_set_value($items_arr);
}

//---------- Cart Items Array ---------
function cart_items_array() {
global $session;
 

    return (array) $session->get('cart');
}

//-----------Cart Exclude Item ---------
function cart_exclude_item($items, $exclude_hash) {

    if (count($items)) {
        $x = 0;
        for ($i = 0; $i < count($items); $i++) {
            if ($items[$i]['hash'] != $exclude_hash) {
//$items_arr[$x]['id'] = intval($items[$i]['id']);
                $items_arr[$x] = $items[$i];
                $x++;
            }
        }
    } else {
        $items_arr = array();
    }

    return $items_arr;
}

//------------- cart update qty -----------
function cart_update_qty($items) {

    $items_saved = cart_items_array();

    foreach ($items as $item) {
        for ($i = 0; $i < count($items_saved); $i++) {
            if ($item['hash'] == $items_saved[$i]['hash']) {
                $items_saved[$i]['qty'] = $item['qty'];
                break;
            }
        }
    }


    cart_set_value($items_saved);
}

//--------- Cart Set Value ------
function cart_set_value($value) {
    global $session;
 
    $session->set('cart', (array) $value);
}

//----------- Order total price- ----------
function get_order_total_price($id) {
    $sum = 0;
    //  print "id:".$id;
    $data_order = db_qr_fetch("select shipping_price from store_orders where id='$id'");
    
    $qr = db_query("select price,qty from store_orders_items where order_id='$id'");
    while ($data = db_fetch($qr)) {
        //           print ("pr:".$data['price']."  qt : ".$data['qty']."<br>");
        $sum += ($data['price'] * $data['qty']);
    }
    return $sum + $data_order['shipping_price'];
}


//------ Shipping Method Price --------------
function get_shipping_method($id){
    
    $data = db_qr_fetch("select * from store_shipping_methods where id='$id'");
     //--------- shipping module settings ------------
      $shipping_settings = array();
      $qrs  = db_query("select name,value from store_shipping_methods_settings where cat='$id'");
      while($datas=db_fetch($qrs)){   
        $shipping_settings[$datas['name']] = $datas['value'];
      }
      $shipping_method = new $data['class'](array(),$shipping_settings);
      return $shipping_method->get_price();
      //---------------------------------------
}