<?

require("global.php");

require(CWD . "/includes/framework_start.php");
//----------------------------------------------------


$id = intval($id);

$qr = db_query("select * from store_products_data where id='$id'");

if (db_num($qr)) {
    $data = db_fetch($qr);

    compile_hook('product_details_start');

    print_path_links($data['cat'], $data['name']);

    compile_hook('product_details_after_path_links');

    open_table($data['name']);


    //------ fields details -------

    $qrf = db_query("select * from store_fields_data where product_id='$data[id]'");
    if (db_num($qrf)) {

        //--- caching data --//
        while ($pre_dataf_x = db_fetch($qrf)) {
            $pre_dataf[$pre_dataf_x['cat']] = $pre_dataf_x;
            $sets_ids[] = $pre_dataf_x['cat'];
        }
        unset($qrf, $pre_dataf_x);
        //--- caching sets ---//
        $fs_qr = db_query("select id,name,title,type,img from store_fields_sets where id IN (" . implode(",", $sets_ids) . ") and in_details=1 and active=1 order by ord");
        while ($fs_data = db_fetch($fs_qr)) {
            $sets_array[] = $fs_data;
        }
        unset($fs_data, $fs_qr, $sets_ids);
        //--------------------//

        if (count($sets_array)) {
            $fields_content = "<br><table>";
            foreach ($sets_array as $field_name) {


                if (isset($pre_dataf[$field_name['id']])) {

                    $dataf = $pre_dataf[$field_name['id']];

                    $fields_content .= "<tr><td>" . iif($field_name['img'], "<img src=\"$field_name[img]\">&nbsp;") . "<b>" . iif($field_name['title'], $field_name['title'], $field_name['name']) . "</b></td><td>";
                    if ($field_name['type'] == "text") {
                        $fields_content .= "$dataf[value]";
                    } elseif ($field_name['type'] == "select") {
                        $option_name = db_qr_fetch("select value from store_fields_options where id='$dataf[value]'");
                        $fields_content .= iif($option_name['value'], $option_name['value'], "$phrases[not_selected]");
                    } elseif ($field_name['type'] == "checkbox") {
                        $values_arr = unserialize($dataf['value']);
                        if (!is_array($values_arr)) {
                            $values_arr = array();
                        }


                        $qr_options = db_query("select id,value,img from store_fields_options where field_id='$dataf[cat]' order by ord");
                        if (db_num($qr_options)) {
                            $fields_content .= "<table>";
                            while ($data_options = db_fetch($qr_options)) {
                                $fields_content .= "<tr><td>" . iif($data_options['img'], "<img src=\"$data_options[img]\">&nbsp;") . "$data_options[value] </td><td><img src=\"$style[images]/" . iif(in_array($data_options['id'], $values_arr), "true.gif", "false.gif") . "\" border=0></td></tr>";
                            }
                            $fields_content .= "</table>";
                        }
                    }
                    $fields_content .= "</td></tr>
  <tr><td colspan=2><hr class='separate_line' size=1></td></tr>";
                }
            }

            $fields_content .= "</table><br><br>";
        }
    } else {
        $fields_content = "";
    }

    //------------------------------
//compile_template(get_template('product_details'));
//------------ PRODUCT DETAILS TABLE -------------------------------------------

    print "<table width=100%><tr><td valign=top align=center width='" . ($settings['products_img_width'] + 20) . "'>";
    if ($data['thumb']) {
        print "<a href=\"$data[img_full]\" class=\"fancybox\" rel=\"group\">
     <img src=\"$data[img]\" border=0 alt=\"$data[name]\"></a><br><br>";
    }


    //-------- photos -------//
    $qrp = db_query("select * from store_products_photos where product_id='$id' order by ord");
    if (db_num($qrp)) {
        print "<ul id='product_photos'>";
        while ($datap = db_fetch($qrp)) {
            print "<li><a href=\"$datap[img]\" class=\"fancybox\" rel=\"group\"><img src=\"$datap[thumb]\"></a></li>";
        }
        print "</ul>";
    }
    //----------------


    print "</td><td>";



//----- add to cart form -----------
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


    print "<hr class='separate_line' size=1>
 <table width=100%><tr><td align=$global_align>
 <img src='$style[images]/price_details.gif'><span class=price>&nbsp;<b>$phrases[the_price] : </b> $data[price] $settings[currency] </span>
 </td>
 <td align=$global_align_x>";

    if ($data['available']) {
        print "<input type=submit value=\"$phrases[add_to_cart]\" name='cart_button' id='cart_button'>";
    } else {
        print "$phrases[not_available_now]";
    }
    print "</td></table>
 
 </form>
 <hr class='separate_line' size=1>
 <br>";

    //----------

    if ($data['weight']) {
        print "<hr class='separate_line' size=1 width=50% align=$global_align>
 <img src='$style[images]/weight_details.gif'><span class=weight><b> $phrases[the_weight] : </b> " . iif(strchr($data['weight'], "."), $data['weight'], number_format($data['weight'], 2, ".", ",")) . " $phrases[kg]   </span>";
    }


    print "</td></tr></table>";

    print $fields_content;

    print "
 $data[details] 
 <br><br>
 
 
 <div align='$global_align_x'>
 <a href=\"javascript:add2fav($data[id]);\"><img src=\"$style[images]/favorite.gif\" border=0 alt=\"$phrases[add2favorite]\"></a>
 &nbsp;
 <a href='http://www.facebook.com/sharer.php?u=" . urlencode($scripturl . "/" . str_replace('{id}', $data['id'], $links['product_details'])) . "' target=_blank><img src='$style[images]/facebook.gif' alt='Share with Facebook' border=0></a>
 </div>";

//--------------- PRODUCT DETAILS END --------------------------------------

    close_table();

//------ Comments -------------------
    if ($settings['enable_product_comments']) {
        open_table($phrases['members_comments']);
        get_comments_box('product', $id);
        close_table();
    }
    /*
      //-------- photos -------//
      $qrp=db_query("select * from store_products_photos where product_id='$id' order by id");
      if(db_num($qrp)){
      compile_hook('product_details_before_photos_table');

      open_table("$phrases[product_photos]");
      print "<table width=100%><tr>";

      $c=0;

      while($datap=db_fetch($qrp)){

      if($c==$settings['img_cells']){
      print "</tr><tr>";
      $c=0;
      }

      run_template('product_details_photos');

      $c++;
      }
      print "</tr></table>";

      close_table();
      compile_hook('product_details_after_photos_table');
      }
      //---------------- */

    compile_hook('product_details_end');
    ?>
    <script type="text/javascript">
        $(document).ready(function() {
            $(".fancybox").fancybox();
        });
    </script>
    <?

} else {
    open_table();
    print "<center>$phrases[err_wrong_url]</center>";
    close_table();
}

//---------------------------------------------------
require(CWD . "/includes/framework_end.php");
