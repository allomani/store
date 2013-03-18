<?
 require('./start.php'); 
 //---------------------------------

if(!$action || $action=="edit_ok" || $action=="add_ok" || $action=="del"){

 if_admin("members");
 
if($action=="del"){
$id=intval($id);
db_query("delete from store_clients_sets where id='$id'");
members_db_query("ALTER TABLE  `{{store_clients}}` DROP  `field_".$id."`");
}

if($action=="edit_ok"){
$id=intval($id);
if($name){
      $value=trim($value); 
db_query("update store_clients_sets set name='".db_escape($name)."',details='".db_escape($details)."',required='$required',type='$type',value='".db_escape($value,false)."',style='$style_v',ord='".intval($ord)."' where id='$id'");
    }
}

if($action=="add_ok"){
$id=intval($id);
if($name){
    $value=trim($value);
db_query("insert into store_clients_sets  (name,details,required,type,value,style,ord) values('".db_escape($name)."','".db_escape($details)."','$required','$type','".db_escape($value,false)."','$style_v','$ord')");

$field_id = db_inserted_id();

members_db_query("ALTER TABLE  `{{store_clients}}` ADD  `field_".$field_id."` VARCHAR( 255 ) NOT NULL , ADD INDEX (  `field_".$field_id."` )");  
}
}

print "
<ul class='nav-bar'>
<li><a href='clients_fields.php'>$phrases[members_custom_fields]</a></li>
</ul>";



print "<p align=center class=title> $phrases[members_custom_fields]</p>

<p><a href='clients_fields.php?action=add' class='add'>$phrases[add_member_custom_field] </a></p>";




$qr= db_query("select * from store_clients_sets order by required desc,ord asc");
if(db_num($qr)){
print "<table class=grid><tr><td>
<div id=\"clients_fields_list\">";

while($data=db_fetch($qr)){
    toggle_tr_class();
    
print "<div id=\"item_$data[id]\" class='$tr_class'>
       
       <table width=100%>
<tr>
 <td width=25 class=\"handle\" title='$phrases[click_and_drag_to_change_order]'>
      </td>
      
      <td width=75%>";
if($data['required']){
    print "<b>$data[name]</b>";
    }else{
    print "$data[name]";
        }
        print "</td>
      
<td><a href='clients_fields.php?action=edit&id=$data[id]'>$phrases[edit]</a> - <a href='clients_fields.php?action=del&id=$data[id]' onClick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete]</a></td></tr>
</table></div>";

}
print "</div>
</td></table></center>";

 print "<script type=\"text/javascript\">
        init_sortlist('clients_fields_list','clients_fields');
</script>";



}else{
print_admin_table("<center>$phrases[no_clients_custom_fields] </center>");
    }


}

//---------- Add Member Field -------------
if($action=="add"){
 if_admin("clients");
print "
<ul class='nav-bar'>
<li><a href='clients_fields.php'>$phrases[members_custom_fields]</a></li>
<li>$phrases[add]</li>
</ul>


<p align=center class=title>$phrases[add_member_custom_field]</p>
<form action='clients_fields.php' method=post>
<input type=hidden name=action value='add_ok'>
<input type=hidden name=id value='$id'>
<table width=80% class=grid>";
print "<tr><td><b> $phrases[the_name]</b> </td><td><input type=text size=20  name=name></td></tr>
<tr><td><b> $phrases[the_description] </b></b></td><td><input type=text size=30  name=details></td></tr>
<tr><td><b>$phrases[the_type]</b></td><td><select name=type>
<option value='text'>$phrases[textbox]</option>
<option value='textarea'>$phrases[textarea]</option>
<option value='select'>$phrases[select_menu]</option>
<option value='radio'>$phrases[radio_button]</option>
<option value='checkbox'>$phrases[checkbox]</option>
</select>
</td></tr>
<tr><td><b>$phrases[default_value_or_options]</b><br><br>$phrases[put_every_option_in_sep_line]</td><td>
<textarea name='value' rows=10 cols=30>$data[value]</textarea></td></tr>

<tr><td><b>$phrases[addition_style]</b> </td><td><input type=text size=30  name='style_v' value=\"$data[style]\" dir=ltr></td></tr>


<tr><td><b>$phrases[required]</b></td><td><select name=required>";
print "
<option value=0>$phrases[no]</option>
    <option value=1>$phrases[yes]</option>
</select></td></tr>
                                                                         

<tr><td colspan=2 align=center><input type=submit value=' $phrases[add_button] '></td></tr>";
print "</table></center>";

}


//---------- Edit Member Field -------------
if($action=="edit"){

    if_admin("members");

print "<ul class='nav-bar'>
<li><a href='clients_fields.php'>$phrases[members_custom_fields]</a></li>
<li>$phrases[edit]</li>
</ul>";
    
    
$qr = db_query("select * from store_clients_sets where id='$id'");

if(db_num($qr)){
$data = db_fetch($qr);
print "
    
<form action='clients_fields.php' method=post>
<input type=hidden name=action value='edit_ok'>
<input type=hidden name=id value='$id'>
<table width=80% class=grid>";
print "<tr><td><b> $phrases[the_name]</b> </td><td><input type=text size=20  name=name value=\"$data[name]\"></td></tr>
<tr><td><b> $phrases[the_description] </b></b></td><td><input type=text size=30  name=details value=\"$data[details]\"></td></tr>
<tr><td><b>$phrases[the_type]</b></td><td><select name=type>";


print "<option value='text'".iif($data['type']=="text", "selected").">$phrases[textbox]</option>
<option value='textarea'".iif($data['type']=="textarea"," selected").">$phrases[textarea]</option>
<option value='select'".iif($data['type']=="select"," selected").">$phrases[select_menu]</option>
<option value='radio'".iif($data['type']=="radio"," selected").">$phrases[radio_button]</option>
<option value='checkbox'".iif($data['type']=="checkbox"," selected").">$phrases[checkbox]</option>
</select>
</td></tr>
<tr><td><b>$phrases[default_value_or_options]</b><br><br>$phrases[put_every_option_in_sep_line]</td><td>
<textarea name='value' rows=10 cols=30>$data[value]</textarea></td></tr>

<tr><td><b>$phrases[addition_style]</b> </td><td><input type=text size=30  name='style_v' value=\"$data[style]\" dir=ltr></td></tr>


<tr><td><b>$phrases[required]</b></td><td><select name=required>";
if($data['required']){$chk1="selected";$chk2="";}else{$chk1="";$chk2="selected";}
print "<option value=1 $chk1>$phrases[yes]</option>
<option value=0 $chk2>$phrases[no]</option>
</select></td></tr>
                                                                                                       

<tr><td colspan=2 align=center><input type=submit value=' $phrases[edit] '></td></tr>";
print "</table></center>";
}else{
print "<center><table width=70% class=grid>";
print "<tr><td align=center>$phrases[err_wrong_url]</td></tr>";
print "</table></center>";
}

}
 //-----------end ----------------
 require(ADMIN_DIR.'/end.php');
?>
