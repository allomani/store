<?

if (!defined('IS_ADMIN')) {
    die('No Access');
}

//----------- Payment Methods --------
if ($action == "shipping_methods" || $action == "shipping_methods_edit_ok" || $action == "shipping_methods_del" ||
        $action == "shipping_methods_add_ok" || $action == "shipping_methods_enable" || $action == "shipping_methods_disable") {
    if_admin();
    $id = intval($id);

    //------ enable ----
    if ($action == "shipping_methods_enable") {
        db_query("update store_shipping_methods set active=1 where id='$id'");
    }
//------ disable ----
    if ($action == "shipping_methods_disable") {
        db_query("update store_shipping_methods set active=0 where id='$id'");
    }
    //---- del ----
    if ($action == "shipping_methods_del") {
        db_query("delete from  store_shipping_methods where id='$id'");
        db_query("delete from  store_shipping_methods_settings where cat='$id'");
    }
    //--- edit ----
    if ($action == "shipping_methods_edit_ok") {

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
  ,default_status='".db_escape($default_status)."' where id='$id'");



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
    if ($action == "shipping_methods_add_ok") {
        db_query("insert store_shipping_methods (class,name,active) values ('" . db_escape($class) . "','" . db_escape($name) . "','1')");

        $new_id = db_inserted_id();
        if ($new_id) {
            $ord = db_qr_first("select max(ord) from store_shipping_methods") + 1;
            db_query("update store_shipping_methods set ord = $ord where id='$new_id'");
            print "<script>window.location = 'index.php?action=shipping_methods_edit&id=$new_id';</script>";
        }
    }

    //--------------------------------  
    print "<p align=center class=title>$phrases[shipping_methods]</p>";
    $qr = db_query("select * from store_shipping_methods order by ord asc");

    print "
<img src='images/add.gif'>&nbsp;<a href='index.php?action=shipping_methods_add'>$phrases[add_button]</a><br><br>";
    if (db_num($qr)) {
        print "<center><table width=100% class=grid>
<tr><td width=100%>
<div id=\"shipping_methods_data_list\">";
        while ($data = db_fetch($qr)) {
            
  if($row_class == 'row_1'){$row_class = 'row_2';}else{  $row_class = 'row_1';}

  
            print "<div id=\"item_$data[id]\" class='$row_class'>
<table width=100%>
<tr>
<td width=25>
      <span style=\"cursor: move;\" class=\"handle\"><img src='images/move.gif' title='$phrases[click_and_drag_to_change_order]'></span> 
      </td>
      <td width=75%>$data[name]</td>
    <td>" . iif($data['active'], "<a href='index.php?action=shipping_methods_disable&id=$data[id]'>$phrases[disable]</a>", "<a href='index.php?action=shipping_methods_enable&id=$data[id]'>$phrases[enable]</a>") . " -
    <a href='index.php?action=shipping_methods_edit&id=$data[id]'>$phrases[edit]</a> - 
    <a href='index.php?action=shipping_methods_del&id=$data[id]' onClick=\"return confirm('" . $phrases['are_you_sure'] . "');\">$phrases[delete]</a></td></tr>
    </table></div>";
        }

        print "</div></td></tr></table></center>";

        print "<script type=\"text/javascript\">
        init_sortlist('shipping_methods_data_list','set_shipping_methods_sort');
</script>";
    } else {
        print_admin_table("<center>  $phrases[no_data] </center>");
    }
}

///----------- Edit ------------
if ($action == "shipping_methods_edit") {
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

        print "<img src='images/arrw.gif'>&nbsp;<a href='index.php?action=shipping_methods'>$phrases[shipping_methods]</a> / $data[name] <br><br>   
        
        <center><form action=index.php method=post>
        <input type=hidden name=id value='$id'>
        <input type=hidden name=action value='shipping_methods_edit_ok'>
        <table width=90% class=grid>
        <tr><td><b>$phrases[the_type]</b></td><td><input type=text name='class' size=30 value=\"$data[class]\"></td></tr>    
        <tr><td><b>$phrases[the_name]</b></td><td><input type=text name=name value=\"$data[name]\" size=30></td></tr>
       
       <tr><td><b>Geo Zones</b></td><td>
       <input type='radio' id='geo_zones_all_yes' name='geo_zones_all' value=1 onClick=\"\$('geo_zones_div').style.display='none';\" " . iif(!count($geo_zones), " checked") . "><label for='geo_zones_all_yes'>جميع المناطق</label><br>
       <input type='radio' id='geo_zones_all_no' name='geo_zones_all' value=0 onClick=\"\$('geo_zones_div').style.display='inline';\"" . iif(count($geo_zones), " checked") . "><label for='geo_zones_all_no'>مناطق محددة</label>
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
       
       <tr><td><b>Min Price</b></td><td><input type=text name='min_price' value=\"$data[min_price]\" size=30></td></tr> 
       <tr><td><b>Max Price</b></td><td><input type=text name='max_price' value=\"$data[max_price]\" size=30></td></tr> 
       
       <tr><td><b>Min Items</b></td><td><input type=text name='min_items' value=\"$data[min_items]\" size=30></td></tr> 
       <tr><td><b>Max Items</b></td><td><input type=text name='max_items' value=\"$data[max_items]\" size=30></td></tr> 
       
        <tr><td><b>Min Weight</b></td><td><input type=text name='min_weight' value=\"$data[min_weight]\" size=30></td></tr> 
       <tr><td><b>Max Weight</b></td><td><input type=text name='max_weight' value=\"$data[max_weight]\" size=30></td></tr> 
       
        <tr><td><b>Default Order Status</b></td><td>
        <select name='default_status'>";
        $qrs = db_query("select * from store_orders_status where active=1 order by id asc");
        while($datas=db_fetch($qrs)){
            print "<option value=\"$datas[id]\"".iif($data['default_status']==$datas['id']," selected").">$datas[name]</option>";
        }
       print "</select></td></tr> 
       
        
       
       
         </table>";

//--------- shipping module settings ------------
        $qrs = db_query("select name,value from store_shipping_methods_settings where cat='$id'");
        while ($datas = db_fetch($qrs)) {
            $shipping_settings[$datas['name']] = $datas['value'];
        }


        print "<br>
<fieldset style=\"width:94%;\">
<legend><b>$phrases[the_settings]</b></legend>";
//$module_file = CWD."/includes/shipping/{$data['class']}.php"; 
//if(file_exists($module_file)){ 
//require($module_file); 
if(class_exists($data['class'])){
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
}else{
            print " Shipping File Not Exists";   
}
        print "</fieldset><br>";

//-----------------------------------------         


        print "<center><input type=submit value=' $phrases[edit] '>

        </form>
        </center>";

$qr_cats = db_query("select id, name ,cat,shipping_methods from store_products_cats");
while($data_cats=db_fetch($qr_cats)){
 //  $shipping_methods_arr = ;// get_product_cat_shipping_methods($data_cats['id'],true);//
 
$categories[] = array(
    "key"=>$data_cats['id'],
    "title"=>$data_cats['name'],
    "parent"=>$data_cats['cat'],
    "isFolder"=>true,
    "select"=> iif(in_array($id,explode(',',$data_cats['shipping_methods'])),true,false)
    );
}

foreach($categories as $catg){
if($catg['select']){
$scr .= "jQuery('#tree').dynatree('getTree').getNodeByKey('$catg[key]').select();\n";
}
}
/*
print "<pre>";
var_dump($categories);
print "</pre>";*/

//

$arr = categoriesToTree($categories);
//$arr = array("children"=>$arr);
       // $arr = array(array("title"=>"عربي 1","key"=>1,"children"=>array(array("title"=>"item1"),array("title"=>"item2"))));
        ?>
<script type="text/javascript">
	/*var treeData = [
		
		{title: "item2: selected on init", select: true },
		{title: "Folder", isFolder: true, select: true, key: "id3",expand: true,
			children: [
				{title: "Sub-item 3.1",key: "3.1"
					
				},
				{title: "Sub-item 3.2",key: "3.2",select: true
					
				}
			]
		}
		
	];*/
    var treeData = <? print json_encode($arr);?>;
	jQuery(function(){jQuery("#tree").dynatree({
			checkbox: true,
			selectMode: 3,
			children: treeData,
			onSelect: function(select, node) {
                                var selRootKeys = jQuery.map(node.tree.getSelectedNodes(true), function(node){
					return node.data.key;
				});
				jQuery("#echoSelectionRootKeys3").text(selRootKeys.join(","));
				
			},
			onDblClick: function(node, event) {
				node.toggleSelect();
			},
			onKeydown: function(node, event) {
				if( event.which == 32 ) {
					node.toggleSelect();
					return false;
				}
                                }
                       
			//,
			// The following options are only required, if we have more than one tree on one page:
//			initId: "treeData",
//			cookieId: "dynatree-Cb3",
//			idPrefix: "dynatree-Cb3-"
		});

<?=$scr;?>
});

                                
                                
</script>

<div id="tree" style="direction:rtl;text-align: right"></div>
	
	<div>Selected root keys: <span id="echoSelectionRootKeys3">-</span></div>
	
<?
    } else {
        print_admin_table("<center>" . $phrases['err_wrong_url'] . "</center>");
    }
}

///----------- Add ------------
if ($action == "shipping_methods_add") {
    if_admin();

    print "
        <img src='images/arrw.gif'>&nbsp;<a href='index.php?action=shipping_methods'>$phrases[shipping_methods]</a> / $phrases[add] <br><br>
        
        <center><form action=index.php method=post>
        <input type=hidden name=id value='$id'>
        <input type=hidden name=action value='shipping_methods_add_ok'>
        <table width=90% class=grid>
        <tr><td><b>$phrases[the_name]</b></td><td><input type=text name=name value=\"$data[name]\" size=30></td></tr>
         
           
         <tr><td colspan=2 align=center><input type=submit value=' $phrases[add_button] '></td></tr>
        </table>
        </form>
        </center>";
}