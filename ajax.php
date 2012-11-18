<?

require("global.php");
header("Content-Type: text/html;charset=$settings[site_pages_encoding]");
//------------------------------------------
if ($action == "check_register_username") {
    if (strlen($str) >= $settings['register_username_min_letters']) {
        $exclude_list = explode(",", $settings['register_username_exclude_list']);

        if (!in_array($str, $exclude_list)) {
//$num = db_num(member_query("select","id",array("username"=>"='$str'")));
            $num = db_qr_num("select " . members_fields_replace("id") . " from " . members_table_replace("store_clients") . " where " . members_fields_replace("username") . " like '" . db_escape($str) . "'", MEMEBR_SQL);

            if (!$num) {
                print "<img src='$style[images]/true.gif'>";
            } else {
                print "<img src='$style[images]/false.gif' title=\"" . str_replace("{username}", $str, "$phrases[register_user_exists]") . "\">";
            }
        } else {
            print "<img src='$style[images]/false.gif' title=\"$phrases[err_username_not_allowed]\">";
        }
    } else {
        print "<img src='$style[images]/false.gif' title=\"$phrases[err_username_min_letters]\">";
    }
}


//------------------------------------------
if ($action == "check_register_email") {
    if (check_email_address($str)) {
        $num = db_qr_num("select " . members_fields_replace("id") . " from " . members_table_replace("store_clients") . " where " . members_fields_replace("email") . " like '" . db_escape($str) . "'", MEMBER_SQL);
        if (!$num) {
            print "<img src='$style[images]/true.gif'>";
        } else {
            print "<img src='$style[images]/false.gif' title=\"$phrases[register_email_exists]\">";
        }
    } else {
        print "<img src='$style[images]/false.gif' title=\"$phrases[err_email_not_valid]\">";
    }
}
//---------------------------------
if ($action == "get_cart_items") {

    $items = cart_items_array();

    //  print_r($items);
    $total_price = 0;
    if (count($items)) {

        for ($i = 0; $i < count($items); $i++) {

            $items[$i]['id'] = intval($items[$i]['id']);
            $items[$i]['qty'] = intval($items[$i]['qty']);

            print "<div id=\"cart_item_" . $items[$i]['id'] . "\">";

            $data = cart_item_info($items[$i]);

            if ($data['id']) {
                print "
<input type=hidden name=\"product_id[]\" value=\"$id\">
<table width=100%>
<tr>
<td><img src='" . get_image($data['thumb']) . "' width=40 height=40></td></tr>
<tr>
<td>$data[info]

<br><img src='$style[images]/qty.gif'>&nbsp;<b>$phrases[the_count] : </b>" . $items[$i]['qty'] . "
<br><img src='$style[images]/price.gif'>&nbsp;<b>$phrases[the_price] : </b>" . $data['item_price'] . " $settings[currency]</td>
<td width=10><a href=\"javascript:cart_delete_item('" . $items[$i]['hash'] . "');\"><img src='$style[images]/del_small.gif' border=0 title='$phrases[delete_from_cart]'></a></td>
</tr></table>";


                print "<hr class='separate_line' size=1></div>";



                $total_price += $data['item_price'];
            }
        }

        if ($total_price) {
            print "<b>$phrases[the_total] : </b> $total_price $settings[currency]";
        }

        print "<br><br>
<center>
<form action='cart.php' method=post>

<input type=submit value='$phrases[checkout]'>
</form>";
    } else {
        print "<center>$phrases[cart_is_empty]</center>";
    }
}


//----------------------
if ($action == "cart_clear") {
    cart_set_value(array());
}
//---------------------------------------
if ($action == "cart_add_item") {
    $id = intval($id);

    $items_arr = cart_items_array();

    $id = (int) $id;
    $product_options = (array) $product_options;
    $hash = md5($id . serialize($product_options));

//----- check if exists ----- //
    for ($i = 0; $i < count($items_arr); $i++) {
        if ($items_arr[$i]['id'] == $id) {
            if ($items_arr[$i]['hash'] == $hash) {
                $item_found = 1;
                $items_arr[$i]['qty']++;
                break;
            }
        }
    }

//------
    if (!$item_found) {
        $new_item['id'] = $id;
        $new_item['qty'] = 1;
        $new_item['options'] = $product_options;
        $new_item['hash'] = $hash;
        array_push($items_arr, $new_item);
    }

    cart_set_value($items_arr);
}
//---------------------------------------
if ($action == "cart_delete_item") {
//$id=intval($id);
    cart_item_delete($hash);
}


if ($action == "cart_item_options") {

    print "<form action='ajax.php' method=post id='add_to_cart_form' onSubmit=\"cart_add_item('add_to_cart_form');return false;\">
<input type='hidden' name='action' value='cart_add_item'>
<input type='hidden' name='id' value='$id'>
";
    $qro = db_query("select * from store_products_options where product_id='$id'");
    $o = 0;
    while ($datao = db_fetch($qro)) {
        print "<input type='hidden' name=\"product_options[$o][type]\" value='$datao[type]'>
     <input type='hidden' name=\"product_options[$o][id]\" value='$datao[id]'>";
        print "<fieldset>
     <legend><b>$datao[name]</b></legend>";


        if ($datao['type'] == "select" || $datao['type'] == "checkbox") {
            $qr_values = db_query("select * from store_products_options_data where cat='$datao[id]'");
            unset($option_values_arr);
            while ($data_values = db_fetch($qr_values)) {
                $option_values_arr[$data_values['id']] = $data_values['name'] . iif($data_values['price'], " (" . $data_values['price_prefix'] . $data_values['price'] . " $settings[currency])");
            }

            if ($datao['type'] == "select") {
                print_select_row("product_options[$o][value]", $option_values_arr);
            } else {
                $oo = 0;
                foreach ($option_values_arr as $key => $value) {
                    print "<input type=checkbox name=\"product_options[$o][value][$oo]\" value=\"$key\" id=\"option_{$key}\"><label for=\"option_{$key}\" class='pointer'>$value</label><br>";
                    $oo++;
                }
            }
        } elseif ($datao['type'] == "text") {
            print "<input type=\"text\" name=\"product_options[$o][value]\" size=20>";
        } elseif ($datao['type'] == "textarea") {
            print "<textarea cols=20 rows=5 name=\"product_options[$o][value]\"></textarea>";
        }


        print "</fieldset><br>";
        $o++;
    }

    print "<p align=\"$global_align_x\"><input type=submit value=\"$phrases[add_to_cart]\" name='cart_button' id='cart_button' class='cart_button'></p>
 </form>";
}


//-----------------------------------------
//--------- Payment Method Details -------
if ($action == "payment_method_details") {
    $id = intval($id);

    $qr = db_query("select * from store_payment_methods where id='$id'");
    if (db_num($qr)) {
        $data = db_fetch($qr);

//---------- method details -----
        if ($data['details']) {
            print "<fieldset><legend>$phrases[the_details]</legend>
    $data[details]
    </fieldset><br>";
        }
//-----------------------------
//--------- gateways -------
        if ($data['gateways']) {

            print "<fieldset><legend>$phrases[payment_gateways]</legend>";
            $x = 0;
            $g_qr = db_query("select * from store_payment_gateways where active=1 and ID IN (" . $data['gateways'] . ") order by ord asc");
            while ($g_data = db_fetch($g_qr)) {

                print "<table><tr><td width=5><input type=radio name=payment_gateways value=\"$g_data[id]\" onClick=\"show_payment_gateway_details($g_data[id],$order_id);\"" . iif(!$x, " checked") . "></td>" . iif($g_data['img'], "<td width=10><img src=\"$g_data[img]\"></td>") . "<td>$g_data[title]</td></tr></table>";
                $x++;
                if (!$g_found) {
                    $g_found = $g_data['id'];
                }
            }


            if (!$g_found) {
                print "<center> $phrases[no_payment_gateways_available] </center>";
            } else {
                print "<div id=\"payment_gateway_details_loading_div\" style=\"display:none;\"><img src='$style[images]/ajax_loading.gif'></div>
 
 <div id=\"payment_gateway_details_div\">";
                payment_gateway_details($g_found, $order_id);
                print "</div>";
            }


            print "</table></fieldset>";
        }
//-------------------------
    }
}

//--------- Payment Gateway Details -------
function payment_gateway_details($id, $order_id) {
    global $member_data, $data_order, $gateway_settings;
    check_member_login();

    $id = intval($id);
    $order_id = intval($order_id);

    $data = db_qr_fetch("select * from store_payment_gateways where id='$id'");

//----------------
    $gateway_settings = array();
    $qrg = db_query("select * from store_payment_gateways_settings where cat='$id'");
    while ($datag = db_fetch($qrg)) {
        $gateway_settings[$datag['name']] = $datag['value'];
    }
//--------------

    $data_order = db_qr_fetch("select * from store_orders where id='$order_id' and userid='$member_data[id]'");
    $data_order['price'] = get_order_total_price($order_id);

//---------- method details -----

    print "<br><fieldset>";
    if ($data['details']) {
        print "$data[details]<br>";
    }

    $gateway_file = CWD . "/includes/gateways/{$data['name']}.php";
    if (file_exists($gateway_file)) {
        require($gateway_file);
        if (class_exists($data['name'])) {
            $m = new $data['name'];
            $m->print_form();
        } else {
            print " Gateway Class Not Exists";
        }
    } else {
        print " Gateway File Not Exists";
    }
    //  run_php($data['code']);
    print "</fieldset><br>";
}

//---------- payment gateway details --------------
if ($action == "payment_gateway_details") {
    payment_gateway_details($id, $order_id);
}

//-------- shipping / billing saved address -----//
if ($action == "get_saved_address") {

    if ($type == "shipping") {
        $suffix = "shipping";
    } else {
        $suffix = "billing";
    }

    $data = db_qr_fetch("select * from store_clients_addresses where id='$id'");
    print json_encode($data);
    /*  print "<table width=100%>
      <tr><td><b>" . $phrases[$suffix . "_name"] . "</b></td><td><input type=text name=\"" . $suffix . "_info[name]\" size=30 value=\"$data[name]\"></td></tr>

      <tr><td><b>$phrases[country]</b></td><td><select name=\"" . $suffix . "_info[country]\">";
      $qr_c = db_query("select * from store_countries order by name asc");
      while ($data_c = db_fetch($qr_c)) {
      print "<option value=\"$data_c[name]\"" . iif($data['country'] == $data_c['name'], " selected") . ">$data_c[name]</option>";
      }

      print "</select></td></tr>

      <tr><td><b>$phrases[city]</b></td><td><input type=text name=\"" . $suffix . "_info[city]\" size=30 value=\"$data[city]\"></td></tr>
      <tr><td><b>$phrases[the_address]</b></td><td><input type=text name=\"" . $suffix . "_info[address_1]\" size=30 value=\"$data[address_1]\"></td></tr>
      <tr><td></td><td><input type=text name=\"" . $suffix . "_info[address_2]\" size=30 value=\"$data[address_2]\"></td></tr>

      <tr><td><b>$phrases[telephone]</b></td><td><input type=text name=\"" . $suffix . "_info[telephone]\" size=30 value=\"$data[tel]\"></td></tr>

      </table>"; */
}

if ($action == "shipping_method_price") {

    $id = (int) $id;
    $obj = get_shipping_method($id);
    if ($obj['status']) {
        print "<div id='shipping_method_price'>" . iif($obj['price'], $obj['price'] . ' ' . $settings['currency'], $phrases['free']) . "</div>";
    } else {

        print "<div id='shipping_method_price'>Cannot Get Price !</div>";
    }
}



//-------- Rating ---------------
if ($action == "rating_send") {

    $id = (int) $id;
    $score = (int) $score;



    if (in_array($type, $rating_types)) {

        if ($score > 0) {
            $session_name = 'rating_' . $type . '_' . $id;

            $settings['rating_expire_hours'] = intval($settings['rating_expire_hours']);
            $settings['rating_expire_hours'] = iif($settings['rating_expire_hours'], $settings['rating_expire_hours'], 1);

            if (get_session($session_name) > time() - (60 * 60 * $settings['rating_expire_hours'])) {
                print "<center>" . str_replace('{hours}', $settings['rating_expire_hours'], $phrases['rating_expire_msg']) . "</center>";
            } else {

                if ($type == 'news') {
                    db_query("update store_news set votes=votes+$score , votes_total=votes_total+1 where id='$id'");
                    db_query("update store_news set rate = (votes/votes_total) where id='$id'");
                }

                set_session($session_name,time());
                print "$phrases[rating_done]";
            }
        } else {
            print "Wrong Rating Value !";
        }
    } else {
        print "Wrong Reference !";
    }
}




//---------------------  Comments ---------------------------

if ($action == "comments_add") {
    if (check_member_login()) {

        if (in_array($type, $comments_types)) {

            $content = trim($content);

            if ($content) {

                db_query("insert into store_comments (uid,fid,comment_type,content,time,active) values ('" . intval($member_data['id']) . "','" . intval($id) . "','" . db_escape($type) . "','" . db_escape($content) . "','" . time() . "','" . iif($settings['comments_auto_activate'], 1, 0) . "')");

                $new_id = mysql_insert_id();

                if ($settings['comments_auto_activate']) {
                    //  print $content;   
                    $data_member = db_qr_fetch("select " . members_fields_replace('id') . " as uid," . members_fields_replace('username') . " as username from " . members_table_replace('store_clients') . " where " . members_fields_replace('id') . "='" . intval($member_data['id']) . "'", MEMBER_SQL);

                    $data = $data_member;
                    $data['id'] = $new_id;
                    $data['time'] = time() - 1;
                    $data['content'] = htmlspecialchars($content);


                    $rcontent = get_comment($data);

                    print json_encode(array("status" => 1, "content" => $rcontent));
                } else {
                    print json_encode(array("status" => 1, "content" => "", "msg" => "$phrases[comment_is_waiting_admin_review]"));
                }
            } else {
                print json_encode(array("status" => 0, "msg" => "$phrases[err_empty_comment]"));
            }
        } else {
            print json_encode(array("status" => 0, "msg" => "$phrases[err_wrong_url]"));
        }
    } else {
        print json_encode(array("status" => 0, "msg" => "$phrases[please_login_first]"));
    }
}

//--------------------------
if ($action == "comments_delete") {

    check_member_login();
    db_query("delete from store_comments where id='" . intval($id) . "'" . iif(!check_admin_login(), " and uid='" . $member_data['id'] . "'"));
}

//------------------------------


if ($action == "comments_get") {

    $offset = (int) $offset;
    if (!$offset) {
        $offset = 1;
    }
    $perpage = intval($settings['commets_per_request']);
    if (!$perpage) {
        $perpage = 10;
    }
    $start = (($offset - 1) * $perpage);


    $check_admin_login = check_admin_login();
    $check_member_login = check_member_login();

    $members_cache = array();


    $qr = db_query("select * from store_comments where fid='" . db_escape($id) . "' and comment_type like '" . db_escape($type) . "' and active=1 order by id desc limit $start,$perpage");

    if (db_num($qr)) {
        if ($offset = 1) {
            print "<div id='no_comments'></div>";
        }



        $c = 0;
        while ($data = db_fetch($qr)) {
            $data_arr[$c] = $data;

            if ($members_cache[$data['uid']]['username']) {
                $udata = $members_cache[$data['uid']];
            } else {
                $udata = db_qr_fetch("select " . members_fields_replace('username') . " as username from " . members_table_replace('store_clients') . " where " . members_fields_replace('id') . "='$data[uid]'", MEMBER_SQL);
                $members_cache[$data['uid']] = $udata;
            }

            $data_arr[$c]['username'] = $udata['username'];



            $c++;
        }



        //--- first row id ----
        $first_index = count($data_arr) - 1;
        $data_first_row = db_qr_fetch("select id from store_comments where fid='" . db_escape($id) . "' and comment_type like '" . db_escape($type) . "' and active=1 order by id limit 1");
        if ($data_arr[$first_index]['id'] != $data_first_row['id']) {
            print " <div id='comments_older_div' class='older_comments_div'><a href='javascript:;' onClick=\"comments_get('" . $type . "','" . $id . "');\"><img src=\"$style[images]/older_comments.gif\">&nbsp; $phrases[older_comments]</a></div> ";
        }
        //---------------------



        unset($data);
        for ($i = count($data_arr) - 1; $i >= 0; $i--) {
            //    print $i;
            $data = $data_arr[$i];

            if ($tr_class == "row_2") {
                $tr_class = "row_1";
            } else {
                $tr_class = "row_2";
            }

            print get_comment($data);
        }
    } else {
        if ($offset == 1) {
            print "<div id='no_comments'>$phrases[no_comments]</div>";
        }
    }
}
?>