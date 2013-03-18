<?

require('./start.php');

//----------- Payment Methods --------
if (!$action || $action == "edit_ok" || $action == "del" ||
        $action == "add_ok" || $action == "enable" || $action == "disable") {
    if_admin();
    $id = intval($id);

    //------ enable ----
    if ($action == "enable") {
        db_query("update store_payment_methods set active=1 where id='$id'");
    }
//------ disable ----
    if ($action == "disable") {
        db_query("update store_payment_methods set active=0 where id='$id'");
    }
    //---- del ----
    if ($action == "del") {
        db_query("delete from  store_payment_methods where id='$id'");
    }
    //--- edit ----
    if ($action == "edit_ok") {
        $gateways_str = implode(",", (array) $gateways);
   
        $all_cats = (int) $all_cats;


        if ($geo_zones_all) {
            $geo_zones_txt = '';
        } else {
            $geo_zones = (array) $geo_zones;
            $geo_zones = array_map('intval', $geo_zones);
            $geo_zones_txt = implode(',', $geo_zones);
        }


        db_query("update store_payment_methods set 
             name='" . db_escape($name) . "',img='" . db_escape($img) . "',
             details='" . db_escape($details, false) . "',
              is_gateway='" . intval($is_gateway) . "',
              gateways='" . db_escape($gateways_str) . "',
               geo_zones='" . db_escape($geo_zones_txt) . "',
              min_price='" . db_escape($min_price) . "',max_price='" . db_escape($max_price) . "',
            min_items='" . db_escape($min_items) . "',max_items='" . db_escape($max_items) . "',
            all_cats='{$all_cats}'               
       where id='$id'");


        //--------------
        $cats_arr = (array) explode(",", $cats);
        $qr = db_query("select id,payment_methods from store_products_cats");
        while ($data = db_fetch($qr)) {
            $cat_payment_methods = (array) explode(",", $data['payment_methods']);
            if (($key = array_search($id, $cat_payment_methods)) !== false) {
                unset($cat_payment_methods[$key]);
            }
            if (in_array($data['id'], $cats_arr)) {
                $cat_payment_methods[] = $id;
            }
            db_query("update store_products_cats set payment_methods = '" . implode(",", $cat_payment_methods) . "' where id='$data[id]'");
        }
//---------------
    }

    //--- add ----
    if ($action == "add_ok") {
        $gateways_str = implode(",", (array) $gateways);

        db_query("insert store_payment_methods (name,img,details,is_gateway,gateways,active) values ('" . db_escape($name) . "','" . db_escape($img) . "','" . db_escape($details, false) . "','" . intval($is_gateway) . "','" . db_escape($gateways_str) . "','1')");
    }

    //--------------------------------  
    print "<p align=center class=title>$phrases[payment_methods]</p>";
    $qr = db_query("select * from store_payment_methods order by ord asc");

    print "<a href='payment_methods.php?action=add' class='add'>$phrases[add_button]</a><br><br>";
    if (db_num($qr)) {
        print "<center><table width=100% class=grid>
<tr><td width=100%>
<div id=\"data_list\">";
        while ($data = db_fetch($qr)) {
            toggle_tr_class();
            print "<div id=\"item_$data[id]\" class='$tr_class'>
<table width=100%>
<tr>
<td class=\"handle\" title='$phrases[click_and_drag_to_change_order]'></td>
      <td width=60%>$data[name]</td>
    <td align=$global_align_x>" . iif($data['active'], "<a href='payment_methods.php?action=disable&id=$data[id]'>$phrases[disable]</a>", "<a href='payment_methods.php?action=enable&id=$data[id]'>$phrases[enable]</a>") . " -
    <a href='payment_methods.php?action=edit&id=$data[id]'>$phrases[edit]</a> - 
    <a href='payment_methods.php?action=del&id=$data[id]' onClick=\"return confirm('" . $phrases['are_you_sure'] . "');\">$phrases[delete]</a></td></tr>
    </table></div>";
        }

        print "</div></td></tr></table></center>";

        print "<script type=\"text/javascript\">
        init_sortlist('data_list','payment_methods');
</script>";
    } else {
        print_admin_table("<center>  $phrases[no_data] </center>");
    }
}

///----------- Edit ------------
if ($action == "edit") {
    if_admin();
    $id = intval($id);

    $qr = db_query("select * from store_payment_methods where id='$id'");
    if (db_num($qr)) {
        $data = db_fetch($qr);

        if ($data['geo_zones']) {
            $geo_zones = explode(',', $data['geo_zones']);
        } else {
            $geo_zones = array();
        }


        print "<ul class='nav-bar'>
            <li><a href='payment_methods.php'>$phrases[payment_methods]</a></li>
        <li>$data[name]</li>
            </ul>
        
    <form action=payment_methods.php method=post name=sender>
        <input type=hidden name=id value='$id'>
        <input type=hidden name=action value='edit_ok'>
       
<fieldset>
        
        <table width=100%>
        <tr><td><b>$phrases[the_name]</b></td><td><input type=text name=name value=\"$data[name]\" size=30></td></tr>
            <tr><td>
  <b>$phrases[the_image]</b></td>
  <td> <table><tr><td><input type=text  dir=ltr size=30 name=img value=\"$data[img]\"></td><td><a href=\"javascript:uploader('payment','img');\"><img src='images/file_up.gif' border=0 alt='$phrases[upload_file]'></a></td></tr></table>

   </td></tr>
   

 <tr><td><b>المناطق الجغرافية</b></td><td>
       <input type='radio' id='geo_zones_all_yes' name='geo_zones_all' value=1 onClick=\"\$('#geo_zones_div').css('display','none');\" " . iif(!count($geo_zones), " checked") . ">
     <label for='geo_zones_all_yes'>جميع المناطق</label><br>
           
       <input type='radio' id='geo_zones_all_no' name='geo_zones_all' value=0 onClick=\"\$('#geo_zones_div').css('display','');\"" . iif(count($geo_zones), " checked") . ">
           <label for='geo_zones_all_no'>مناطق محددة</label>
       <br><br>
       <div id='geo_zones_div'" . iif(!count($geo_zones), "style=\"display:none;\"") . ">
       ";
        $qr_geo = db_query("select * from store_geo order by id");
        while ($data_geo = db_fetch($qr_geo)) {
            print "<input type='checkbox' name='geo_zones[]' value='$data_geo[id]'" . iif(in_array($data_geo['id'], $geo_zones), " checked") . "> $data_geo[name] <br>";
        }


        print "
       </div>
       </td></tr>
       
       <tr><td><b>أقل اجمالي الطلب</b></td><td><input type=text name='min_price' value=\"$data[min_price]\" size=30></td></tr> 
       <tr><td><b>اعلى اجمالي الطلب</b></td><td><input type=text name='max_price' value=\"$data[max_price]\" size=30></td></tr> 
       
       <tr><td><b>اقل عدد سلع</b></td><td><input type=text name='min_items' value=\"$data[min_items]\" size=30></td></tr> 
       <tr><td><b>اقصى عدد سلع</b></td><td><input type=text name='max_items' value=\"$data[max_items]\" size=30></td></tr> 
       
         <tr><td><b>$phrases[the_details]</b></td><td><textarea cols=30 rows=7 name=details>$data[details]</textarea></td></tr>
             
</table>
</fieldset>

<fieldset>
<table width=100%>
           <tr><td><b>$phrases[is_gateway]</b></td><td>";
        print_select_row("is_gateway", array($phrases['no'], $phrases['yes']), $data['is_gateway']);
        print "</td></tr>  
           <tr><td><b>$phrases[payment_gateways]</b></td><td>
           <table width=100%><tr>";
        $gateways = (array) explode(",", $data['gateways']);

        $qro = db_query("select * from store_payment_gateways order by ord");
        $c = 0;
        while ($datao = db_fetch($qro)) {
            if ($c == 4) {
                print "</tr><tr>";
                $c = 0;
            }

            print "<td><input type=\"checkbox\" name=\"gateways[]\" value=\"$datao[id]\"" . iif(in_array($datao['id'], $gateways), ' checked') . ">" . iif($datao['title'], $datao['title'], $datao['name']) . "</td>";
            $c++;
        }

        print "</table>
               
</table>
</fieldset>";

        //--------------- categories -------------------
        $categories = array();
        $qr_cats = db_query("select id, name ,cat,payment_methods from store_products_cats order by ord");
        while ($data_cats = db_fetch($qr_cats)) {
            //  $shipping_methods_arr = ;// get_product_cat_shipping_methods($data_cats['id'],true);//

            $categories[] = array(
                "key" => $data_cats['id'],
                "title" => $data_cats['name'],
                "parent" => $data_cats['cat'],
                "select" => iif(in_array($id, explode(',', $data_cats['payment_methods'])), true, false)
            );
        }


        print "<fieldset>
    <legend>$phrases[the_cats]</legend>
        
    <input type='radio' id='all_cats_yes' name='all_cats' value=1 onClick=\"\$('#cats_tree_wrapper').hide();\" " . iif($data['all_cats'], " checked") . ">
    <label for='all_cats_yes'>جميع الأقسام </label><br>
           
    <input type='radio' id='all_cat_no' name='all_cats' value=0 onClick=\"\$('#cats_tree_wrapper').show();\"" . iif(!$data['all_cats'], " checked") . ">
    <label for='all_cat_no'>أقسام محددة </label> 
    ";

        print "<div id='cats_tree_wrapper'>";
        print_dynatree_div($categories, 'cats_tree');
        print "</div>
      <input type='hidden' name='cats' id='cats' value=''>";
        print "</fieldset>";
        print iif($data['all_cats'], "<script>$('#cats_tree_wrapper').hide();</script>");


        print "
    <fieldset style='text-align:center;'>
    <input type=submit value=' $phrases[edit] '></td></tr>
        </fieldset>
        </form>
  ";
        ?>
        <script type="text/javascript">
                                	
                                  
            $(function(){
                init_dynatree('cats_tree','cats');
            });
                                                          
        </script>
        <?

    } else {
        print_admin_table("<center>" . $phrases['err_wrong_url'] . "</center>");
    }
}

///----------- Add ------------
if ($action == "add") {
    if_admin();

    print "
        <ul class='nav-bar'>
        <li><a href='payment_methods.php'>$phrases[payment_methods]</a></li>
<li>$phrases[add]</li>
    </ul>
        
        <center><form action=payment_methods.php method=post name=sender>
        <input type=hidden name=id value='$id'>
        <input type=hidden name=action value='add_ok'>
        <table width=90% class=grid>
        <tr><td><b>$phrases[the_name]</b></td><td><input type=text name=name value=\"$data[name]\" size=30></td></tr>
            <tr><td>
  <b>$phrases[the_image]</b></td>
  <td> <table><tr><td><input type=text  dir=ltr size=30 name=img></td><td><a href=\"javascript:uploader('payment','img');\"><img src='images/file_up.gif' border=0 alt='$phrases[upload_file]'></a></td></tr></table>

   </td></tr>
         <tr><td><b>$phrases[the_details]</b></td><td><textarea cols=30 rows=7 name=details>$data[details]</textarea></td></tr>
           <tr><td><b>$phrases[is_gateway]</b></td><td>";
    print_select_row("is_gateway", array($phrases['no'], $phrases['yes']), $data['is_gateway']);
    print "</td></tr>  
           <tr><td><b>$phrases[payment_gateways]</b></td><td>
           <table width=100%><tr>";
    $gateways = (array) explode(",", $data['gateways']);

    $qro = db_query("select * from store_payment_gateways order by ord");
    $c = 0;
    while ($datao = db_fetch($qro)) {
        if ($c == 4) {
            print "</tr><tr>";
            $c = 0;
        }

        print "<td><input type=\"checkbox\" name=\"gateways[]\" value=\"$datao[id]\"" . iif(in_array($datao['id'], $gateways), ' checked') . ">" . iif($datao['title'], $datao['title'], $datao['name']) . "</td>";
        $c++;
    }

    print "</table></td></tr>
           
         <tr><td colspan=2 align=center><input type=submit value=' $phrases[add_button] '></td></tr>
        </table>
        </form>
        </center>";
}

//-----------end ----------------
require(ADMIN_DIR . '/end.php');