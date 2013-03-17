<?

require('./start.php');

//----------- Payment Methods --------
if (!$action || $action == "shipping_methods" || $action == "edit_ok" || $action == "del" ||
        $action == "add_ok" || $action == "enable" || $action == "disable") {
    if_admin();
    $id = intval($id);

    //------ enable ----
    if ($action == "enable") {
        db_query("update store_shipping_methods set active=1 where id='$id'");
    }
//------ disable ----
    if ($action == "disable") {
        db_query("update store_shipping_methods set active=0 where id='$id'");
    }
    //---- del ----
    if ($action == "del") {
        db_query("delete from  store_shipping_methods where id='$id'");
        db_query("delete from  store_shipping_methods_settings where cat='$id'");
    }
    //--- edit ----
    if ($action == "edit_ok") {
        
$all_cats = (int) $all_cats;


        if ($geo_zones_all) {
            $geo_zones_txt = '';
        } else {
            $geo_zones = (array) $geo_zones;
            $geo_zones = array_map('intval', $geo_zones);
            $geo_zones_txt = implode(',', $geo_zones);
        }

        db_query("update store_shipping_methods set class='" . db_escape($class) . "',name='" . db_escape($name) . "',geo_zones='" . db_escape($geo_zones_txt) . "'
  ,min_price='" . db_escape($min_price) . "',max_price='" . db_escape($max_price) . "'
  ,min_items='" . db_escape($min_items) . "',max_items='" . db_escape($max_items) . "'
  ,min_weight='" . db_escape($min_weight) . "',max_weight='" . db_escape($max_weight) . "'
  ,default_status='" . db_escape($default_status) . "',all_cats='{$all_cats}' where id='$id'");


//--------------
$shipping_cats_arr = (array) explode(",",$shipping_cats);
$qr  = db_query("select id,shipping_methods from store_products_cats");
while($data=db_fetch($qr)){
$cat_shipping_methods = (array) explode(",",$data['shipping_methods']);
if(($key = array_search($id, $cat_shipping_methods)) !== false) {
    unset($cat_shipping_methods[$key]);
}
if(in_array($data['id'],$shipping_cats_arr)){
    $cat_shipping_methods[] = $id;
}
db_query("update store_products_cats set shipping_methods = '".implode(",",$cat_shipping_methods)."' where id='$data[id]'");
}
//---------------

//------------------------ 
        $qr = db_query("select name from store_shipping_methods_settings where cat='$id'");
        while ($data = db_fetch($qr)) {
            $availabe_settings[] = $data['name'];
        }
        $availabe_settings = (array) $availabe_settings;

        $shipping_settings = (array) $shipping_settings;
        foreach ($shipping_settings as $key => $value) {
            if (!in_array($key, $availabe_settings)) {
                db_query("insert into store_shipping_methods_settings (cat,name,value) values ('$id','" . db_escape($key) . "','" . db_escape($value) . "')");
            } else {
                db_query("update store_shipping_methods_settings set value='" . db_escape($value) . "' where name like '" . db_escape($key) . "' and cat='$id'");
            }
        }
//-------------------
    }

    //--- add ----
    if ($action == "add_ok") {
        $class= 'shipping_manual';
         $ord = db_fetch_first("select max(ord) from store_shipping_methods") + 1;
        db_query("insert store_shipping_methods (class,name,active,all_cats,ord) values ('" . db_escape($class) . "','" . db_escape($name) . "','1','1','$ord')");

        $new_id = db_inserted_id();
       js_redirect("shipping_methods.php?action=edit&id=$new_id");
      
    }

    //--------------------------------  
    print "<p align=center class=title>$phrases[shipping_methods]</p>";
    $qr = db_query("select * from store_shipping_methods order by ord asc");
?>
<div id="add_form" style="display:none;">
<form action='shipping_methods.php' method=post>
        <input type=hidden name=id value='$id'>
        <input type=hidden name=action value='add_ok'>
        <table width=90% class=grid>
        <tr><td><b><?=$phrases['the_name'];?></b></td><td><input type=text name='name'  size=30></td></tr>
        <tr><td colspan=2 align=center><input type=submit value=' <?=$phrases['add_button'];?> '></td></tr>
        </table>
        </form>
</div>

<script>
    $(document).ready(function(){
        $('#add_method_btn').click(function(e){
            e.preventDefault();
            $('#add_form').dialog({modal: true});
            });
        });
</script>
<?
    print "<a href=\"#\" id='add_method_btn' class='add'>$phrases[add_button]</a><br><br>";
    if (db_num($qr)) {
        print "<table width=100% class=grid>
<tr><td width=100%>
<div id=\"shipping_methods_data_list\">";
        while ($data = db_fetch($qr)) {

            if ($row_class == 'row_1') {
                $row_class = 'row_2';
            } else {
                $row_class = 'row_1';
            }


            print "<div id=\"item_$data[id]\" class='$row_class'>
<table width=100%>
<tr>
<td class=\"handle\"></td>
      <td width=75%>$data[name]</td>
    <td align='$global_align_x'>" . iif($data['active'], "<a href='shipping_methods.php?action=disable&id=$data[id]'>$phrases[disable]</a>", "<a href='shipping_methods.php?action=enable&id=$data[id]'>$phrases[enable]</a>") . " -
    <a href='shipping_methods.php?action=edit&id=$data[id]'>$phrases[edit]</a> - 
    <a href='shipping_methods.php?action=del&id=$data[id]' onClick=\"return confirm('" . $phrases['are_you_sure'] . "');\">$phrases[delete]</a></td></tr>
    </table></div>";
        }

        print "</div></td></tr></table></center>";

        print "<script type=\"text/javascript\">
        init_sortlist('shipping_methods_data_list','shipping_methods');
</script>";
    } else {
        print_admin_table("<center>  $phrases[no_data] </center>");
    }
}

///----------- Edit ------------
if ($action == "edit") {
    if_admin();
    $id = intval($id);

    $qr = db_query("select * from store_shipping_methods where id='$id'");
    if (db_num($qr)) {
        $data = db_fetch($qr);

        if ($data['geo_zones']) {
            $geo_zones = explode(',', $data['geo_zones']);
        } else {
            $geo_zones = array();
        }

        print "<ul class='nav-bar'>
            <li><a href='shipping_methods.php'>$phrases[shipping_methods]</a></li>
        <li>$data[name]</li>
            </ul>
        
        <form action=shipping_methods.php method=post>
        <input type=hidden name=id value='$id'>
        <input type=hidden name=action value='edit_ok'>
        <table width=90% class=grid>
        <tr><td><b>$phrases[the_type]</b></td><td><input type=text name='class' size=30 value=\"$data[class]\"></td></tr>    
        <tr><td><b>$phrases[the_name]</b></td><td><input type=text name=name value=\"$data[name]\" size=30></td></tr>
       
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
       
        <tr><td><b>اقل وزن</b></td><td><input type=text name='min_weight' value=\"$data[min_weight]\" size=30></td></tr> 
       <tr><td><b>اقصى وزن</b></td><td><input type=text name='max_weight' value=\"$data[max_weight]\" size=30></td></tr> 
       
        <tr><td><b>الحالة الافتراضية للطلب</b></td><td>
        <select name='default_status'>";
        $qrs = db_query("select * from store_orders_status where active=1 order by id asc");
        while ($datas = db_fetch($qrs)) {
            print "<option value=\"$datas[id]\"" . iif($data['default_status'] == $datas['id'], " selected") . ">$datas[name]</option>";
        }
        print "</select></td></tr> 
       
        
       
       
         </table>";
//--------------- shipping categories -------------------

          $qr_cats = db_query("select id, name ,cat,shipping_methods from store_products_cats order by ord");
        while ($data_cats = db_fetch($qr_cats)) {
            //  $shipping_methods_arr = ;// get_product_cat_shipping_methods($data_cats['id'],true);//

            $categories[] = array(
                "key" => $data_cats['id'],
                "title" => $data_cats['name'],
                "parent" => $data_cats['cat'],
                "select" => iif(in_array($id, explode(',', $data_cats['shipping_methods'])), true, false)
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
  print_dynatree_div($categories,'cats_tree');
  print "</div>
      <input type='hidden' name='shipping_cats' id='shipping_cats' value=''>";
  print "</fieldset>";
  print iif($data['all_cats'],"<script>$('#cats_tree_wrapper').hide();</script>");
  
//--------- shipping module settings ------------
        $qrs = db_query("select name,value from store_shipping_methods_settings where cat='$id'");
        while ($datas = db_fetch($qrs)) {
            $shipping_settings[$datas['name']] = $datas['value'];
        }


        print "<br>
<fieldset>
<legend><b>$phrases[the_settings]</b></legend>";
//$module_file = CWD."/includes/shipping/{$data['class']}.php"; 
//if(file_exists($module_file)){ 
//require($module_file); 
        if (class_exists($data['class'])) {
            $m = new $data['class'](array(), $shipping_settings);
            print "<table width=100%>";
            foreach ($m->settings as $n => $s) {

                print "<tr><td><b>" . iif($s['title'], $s['title'], $n) . "</b></td><td>";
                if ($s['type'] == "select") {
                    print_select_row("shipping_settings[$n]", $s['options'], $shipping_settings[$n]);
                } else {
                    print "<input type='text' name=\"shipping_settings[$n]\" value=\"{$shipping_settings[$n]}\">";
                }

                print iif($s['ext'], " $s[ext]") . "</td></tr>";
            }
            print "</table>";
//}else{ 
            //  print " Shipping Class Not Exists";
//}
        } else {
            print " Shipping File Not Exists";
        }
        print "</fieldset><br>";

//-----------------------------------------         


        print "<center><input type=submit value=' $phrases[edit] '>

        </form>
        </center>";

      
  
        ?>
       <script type="text/javascript">
                	
                  
            $(function(){
              init_dynatree('cats_tree','shipping_cats');
            });
                                          
        </script>
        <?

    } else {
        print_admin_table("<center>" . $phrases['err_wrong_url'] . "</center>");
    }
}

//-----------end ----------------
require(ADMIN_DIR . '/end.php');