<?

require('./start.php');

//---------------------- Store Fields ---------------------
if (!$action || $action == "edit_ok" || $action == "add_ok" ||
        $action == "del" || $action == "disable" || $action == "enable") {

    if_admin("store_fields");

//------- enable / disale -----------//
    if ($action == "disable") {
        db_query("update store_fields_sets set active=0 where id='$id'");
    }

    if ($action == "enable") {

        db_query("update store_fields_sets set active=1 where id='$id'");
    }
//-------- del -------//
    if ($action == "del") {
        $id = intval($id);
        db_query("delete from store_fields_sets where id='$id'");
        db_query("delete from store_fields_data where cat='$id'");
        db_query("delete from store_fields_options where field_id='$id'");
    }

//----- edit -----//
    if ($action == "edit_ok") {
        $id = intval($id);
        if ($name) {

            db_query("update store_fields_sets set name='" . db_escape($name) . "',title='" . db_escape($title, false) . "',img='" . db_escape($img) . "',in_search='" . intval($in_search) . "',in_details='" . intval($in_details) . "',in_short_details='" . intval($in_short_details) . "',type='" . db_escape($type) . "',value='" . db_escape($value, false) . "',ord='" . intval($ord) . "' where id='$id'");
        }
    }

//------- add -------//
    if ($action == "add_ok") {
        $id = intval($id);
        if ($name) {
            $max_ord = db_qr_fetch("select max(ord)+1 as ord from store_fields_sets limit 1");

            db_query("insert into store_fields_sets  (name,title,img,type,value,in_search,in_details,in_short_details,active,ord) values('" . db_escape($name) . "','" . db_escape($title, false) . "','" . db_escape($img) . "','" . db_escape($type) . "','" . db_escape($value, false) . "','" . intval($in_search) . "','" . intval($in_details) . "','" . intval($in_short_details) . "','1','$max_ord[ord]')");
        }
    }

//------------------------------


    print "<p align=center class=title> $phrases[products_fields] </p>

<p align=$global_align><a href='store_fields.php?action=add' class='add'>$phrases[store_field_add] </a></p>

<center>";

    $qr = db_query("select * from store_fields_sets order by ord asc");
    if (db_num($qr)) {
        print "<table width=100% class=grid>
<tr><td width=100%>

<table width=100%>
<tr>
<th width=30></th>
      
<th width=400><b>$phrases[the_name]</b></th>
    <th width=400><b>$phrases[the_title]</b></th>

<th width=35><b>$phrases[fields_search_menu]</b></th>
<th width=35><b>$phrases[short_description]</b></th>
<th width=35><b>$phrases[product_details]</b></th>


<th width=300><b>$phrases[the_options]</b></th>
    </tr>
    </table>
</td></tr>
<tr><td width=100%>
<div id=\"store_fields_data_list\">";
        while ($data = db_fetch($qr)) {
            toggle_tr_class();

            print "
<div id=\"item_$data[id]\">
<table width=100%>
<tr class='$tr_class'>
<td  class=\"handle\"></td>
      
<td width=400>$data[name]</td><td width=400>$data[title]</td>

<td width=35><img src=\"images/" . iif($data['in_search'], "true.gif", "false.gif") . "\"></td>
<td width=35><img src=\"images/" . iif($data['in_short_details'], "true.gif", "false.gif") . "\"></td>
<td width=35><img src=\"images/" . iif($data['in_details'], "true.gif", "false.gif") . "\"></td>


<td width=300>";

            if ($data['active']) {
                print "<a href='store_fields.php?action=disable&id=$data[id]'>$phrases[disable]</a> - ";
            } else {
                print "<a href='store_fields.php?action=enable&id=$data[id]'>$phrases[enable]</a> - ";
            }

            print "<a href='store_fields.php?action=edit&id=$data[id]'>$phrases[edit]</a> - <a href='store_fields.php?action=del&id=$data[id]' onClick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete]</a></td></tr>
</table></div>";
        }

        print "</div></td></tr></table></center>";


        print "<script type=\"text/javascript\">
        init_sortlist('store_fields_data_list','store_fields');
</script>";
    } else {
        print_admin_table("<center>  $phrases[no_data] </center>");
    }

    print "<br>";
    print_admin_table("<center>$phrases[store_fields_note]</center>");
}

//---------- Add Store Field -------------
if ($action == "add") {
    if_admin("store_fields");

    print "<img src='images/link.gif'> <a href='store_fields.php'>$phrases[products_fields]</a> / $phrases[store_field_add]<br><br>";


    print "<center>
<p align=center class=title>$phrases[store_field_add]</p>
<form action=store_fields.php method=post name=sender>
<input type=hidden name=action value='add_ok'>
<input type=hidden name=id value='$id'>
<table width=80% class=grid>";
    print "<tr>
<td><b>$phrases[the_name]</b> </td><td><input type=text size=20  name=name value=\"$data[name]\"></td></tr>
<td><b>$phrases[the_title]</b> </td><td><input type=text size=20  name=title value=\"$data[title]\"></td></tr> 

<tr>
                <td><b>$phrases[the_image]</b></td>
                <td>

                <table><tr><td>
                                 <input type=\"text\" name=\"img\" size=\"30\" dir=ltr>   </td>

                                <td> <a href=\"javascript:uploader('fields','img');\"><img src='images/file_up.gif' border=0 alt='$phrases[upload_file]'></a>
                                 </td></tr></table>

                                 </td>
        </tr>

<tr><td><b>$phrases[the_type]</b></td><td><select name=type onChange=\"show_hide_fields_divs(this.value);\">
<option value='text'>$phrases[textbox]</option>

<option value='select'>$phrases[select_menu]</option>

<option value='checkbox'>$phrases[checkbox]</option>
</select>
</td></tr>
<tr id='fields_default_value_div'><td><b>$phrases[default_value]</b></td><td>
<textarea name='value' rows=10 cols=30>$data[value]</textarea></td></tr>

 <tr id='fields_options_div' style=\"display:none;\"><td colspan=2>$phrases[fields_options_add_note]</td></tr>
 
 <tr><td><b>$phrases[appearance_places]</b> </td><td>
<input type='checkbox' name=in_search value=\"1\" checked>$phrases[fields_search_menu] <br>
<input type='checkbox' name=in_short_details value=\"1\" checked> $phrases[short_description] <br> 
<input type='checkbox' name=in_details value=\"1\" checked> $phrases[product_details]
</td></tr>   
 
<tr><td colspan=2 align=center><input type=submit value=' $phrases[add_button] '></td></tr>";
    print "</table></center>";
}


//---------- Edit Store Field -------------
if ($action == "edit" || $action == "fields_options_edit_ok") {

    if_admin("store_fields");
    $id = intval($id);

    print "<img src='images/link.gif'> <a href='store_fields.php'>$phrases[products_fields]</a><br><br>";

//---- edit options -----//
    if ($action == "fields_options_edit_ok") {
        for ($i = 0; $i < count($option_id); $i++) {
            db_query("update store_fields_options set value='" . db_escape($option_value[$i], false) . "',img='" . db_escape($option_img[$i]) . "' where id='$option_id[$i]'");
        }
    }

    $qr = db_query("select * from store_fields_sets where id='$id'");

    if (db_num($qr)) {
        $data = db_fetch($qr);
        print "<center><form action=store_fields.php method=post name=sender>
<input type=hidden name=action value='edit_ok'>
<input type=hidden name=id value='$id'>
<table width=80% class=grid>";
        print "
<tr><td><b>$phrases[the_name]</b> </td><td><input type=text size=20  name=name value=\"$data[name]\"></td></tr>
<tr><td><b>$phrases[the_title]</b> </td><td><input type=text size=20  name=title value=\"$data[title]\"></td></tr> 

<tr>
                <td><b>$phrases[the_image]</b></td>
                <td>

                <table><tr><td>
                                 <input type=\"text\" name=\"img\" size=\"30\" dir=ltr value=\"$data[img]\">   </td>

                                <td> <a href=\"javascript:uploader('fields','img');\"><img src='images/file_up.gif' border=0 alt='$phrases[upload_file]'></a>
                                 </td></tr></table>

                                 </td>
        </tr>
        
         
<tr><td><b>$phrases[the_type]</b></td><td><select name=type onChange=\"show_hide_fields_divs(this.value);\">";

        if ($data['type'] == "text") {
            $chk1 = "selected";
            $chk2 = "";
            $chk3 = "";
        } elseif ($data['type'] == "select") {
            $chk1 = "";
            $chk2 = "selected";
            $chk3 = "";
        } elseif ($data['type'] == "checkbox") {
            $chk1 = "";
            $chk2 = "";
            $chk3 = "selected";
        }

        print "<option value='text' $chk1>$phrases[textbox]</option>
<option value='select' $chk2>$phrases[select_menu]</option>
<option value='checkbox' $chk3>$phrases[checkbox]</option>
</select>
</td></tr>
<tr id='fields_default_value_div'><td><b>$phrases[default_value]</b></td><td>
<textarea name='value' rows=10 cols=30>" . htmlspecialchars($data['value']) . "</textarea></td></tr>

<tr id='fields_options_div'><td><b>$phrases[the_options]</b></td><td>";
        $qro = db_query("select * from store_fields_options where field_id='$id' order by ord asc");
        $ix = 0;
        if (db_num($qro)) {
            while ($datao = db_fetch($qro)) {
                print "<li id=\"option_" . $ix . "\">$datao[value]</li>";
            }
        } else {
            print "$phrases[no_options]";
        }


        print "<br><br>
<a href='store_fields.php?action=fields_options_edit&id=$data[id]'>$phrases[options_edit]</a></td></tr>


<tr><td><b>$phrases[appearance_places]</b> </td><td>
<input type='checkbox' name=in_search value=\"1\"" . iif($data['in_search'], " checked") . ">$phrases[fields_search_menu] <br>
<input type='checkbox' name=in_short_details value=\"1\"" . iif($data['in_short_details'], " checked") . "> $phrases[short_description] <br> 
<input type='checkbox' name=in_details value=\"1\"" . iif($data['in_details'], " checked") . "> $phrases[product_details]
</td></tr>   


<tr><td><b>$phrases[the_order]</b> </td><td><input type=text size=3  name=ord value=\"$data[ord]\"></td></tr>

<tr><td colspan=2 align=center><input type=submit value=' $phrases[edit] '></td></tr>";
        print "</table></center></form>

<script>
show_hide_fields_divs($('type').value);
</script>
";
    } else {
        print "<center><table width=70% class=grid>";
        print "<tr><td align=center>$phrases[err_wrong_url]</td></tr>";
        print "</table></center>";
    }
}

//----------- edit options --------------
if ($action == "fields_options_edit" || $action == "fields_options_add_ok" || $action == "fields_options_del") {
    $id = intval($id);
    if_admin("store_fields");
    $data_field = db_qr_fetch("select name from store_fields_sets where id='$id'");
    print "<ul class='nav-bar'>
<li><a href='store_fields.php'>$phrases[products_fields]</a></li>
<li><a href='store_fields.php?action=edit&id=$id'>$data_field[name]</a></li>
</ul>";


//--- add -----//
    if ($action == "fields_options_add_ok") {
        $max_ord = db_qr_fetch("select max(ord)+1 as ord from store_fields_options where field_id='$id'");

        db_query("insert into store_fields_options (value,img,field_id,ord) values('" . db_escape($value, false) . "','" . db_escape($img) . "','$id','$max_ord[ord]')");
    }


//---- del ----
    if ($action == "fields_options_del") {
        $option_id = (int) $option_id;
        db_query("delete from store_fields_options where id='$option_id'");
    }



    $qr = db_query("select * from store_fields_options where field_id='$id' order by ord asc");
    print "<center>
    <form action=store_fields.php method=post name=sender2>
    <input type=hidden name=action value=\"fields_options_add_ok\">
    <input type=hidden name=id value=\"$id\">  
    <table width=100% class=grid>
    <tr><td><b>$phrases[the_value] : </b></td>
    <td><input type=text name=\"value\" size=30></td>  <td rowspan=2><input type=submit value=\"$phrases[add_button]\"></td>
    </tr>
    <tr>
                <td><b>$phrases[the_image]</b></td>
                <td>

                <table><tr><td>
                                 <input type=\"text\" name=\"img\" size=\"30\" dir=ltr>   </td>

                                <td> <a href=\"javascript:uploader2('fields','img','sender2');\"><img src='images/file_up.gif' border=0 alt='$phrases[upload_file]'></a>
                                 </td></tr></table>

                                 </td>
        </tr>
    
    </table>
    </form></center><br>";

    if (db_num($qr)) {
        $i = 0;
        print "<center>
        <form action=store_fields.php method=post name=sender>
        <input type=hidden name=action value=\"fields_options_edit_ok\">
        <input type=hidden name=id value=\"$id\">
        <table width=100% class=grid><tr><td width=100%>
        <div id=\"store_fields_options_data_list\">";
        while ($data = db_fetch($qr)) {
            toggle_tr_class();
            print "
            <div id=\"item_$data[id]\" class='$tr_class'>
<table width=100%>
            <input type=hidden name=\"option_id[$i]\" value=\"$data[id]\">
            
            <tr><td class=\"handle\"></td>
      <td><b>" . ($i + 1) . "</b></td>
            <td>$phrases[the_value] : <br><input name=\"option_value[$i]\" type=text size=20 value=\"$data[value]\"></td>
            <td>
            
            <table><tr><td>
                                $phrases[the_image] : <br>  <input type=\"text\" name=\"option_img[$i]\" size=\"30\" value=\"$data[img]\" dir=ltr>   </td>

                                <td> <a href=\"javascript:uploader('fields','option_img[$i]');\"><img src='images/file_up.gif' border=0 alt='$phrases[upload_file]'></a>
                                 </td></tr></table>

                                 </td>
        </td>
            <td><a href='store_fields.php?action=fields_options_del&id=$id&option_id=$data[id]'>$phrases[delete]</a></td>
            </tr></table></div>";
            $i++;
        }
        print "
        </div></td></tr>
        <tr><td  align=center><input type=submit value=\" $phrases[edit]\"></td></tr>
        </table>
        </form></center>
        
        <script type=\"text/javascript\">
        init_sortlist('store_fields_options_data_list','store_fields_options');
</script>
        ";
    } else {
        print_admin_table("<center>$phrases[no_options]</center>");
    }
}

//-----------end ----------------
require(ADMIN_DIR . '/end.php');