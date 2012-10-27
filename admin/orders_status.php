<?
require('./start.php');  
 
//----------- Orders Statuss --------
if(!$action || $action=="orders_status" || $action=="edit_ok" || $action=="del" || 
$action=="add_ok" || $action=="enable" || $action=="disable" ||
$action=="set_default" || $action=="set_default_if_shipping"){
$id = intval($id);

if_admin('orders_status');

//----- set default  ---//
if($action=="set_default"){
db_query("update store_orders_status set `default`=1 where id='$id'");
db_query("update store_orders_status set `default`=0 where id !='$id'");  
}
/*//----- set default if shipping ---//
if($action=="set_default_if_shipping"){
db_query("update store_orders_status set `default_if_shipping`=1 where id='$id'"); 
db_query("update store_orders_status set `default_if_shipping`=0 where id !='$id'");  
}  */

//------ enable ----
if($action=="enable"){
db_query("update store_orders_status set active=1 where id='$id'");    
}
//------ disable ----
if($action=="disable"){
$data_st = db_qr_fetch("select `default`,`default_if_shipping` from store_orders_status where id='$id'");
if($data_st['default'] || $data_st['default_if_shipping']){
print_admin_table("<center> $phrases[cannot_disable_default_status] </center>");
}else{    
db_query("update store_orders_status set active=0 where id='$id'");
}    
}
//----- del ----
if($action=="del"){
    $data_st = db_qr_fetch("select `default`,`default_if_shipping` from store_orders_status where id='$id'");
if($data_st['default'] || $data_st['default_if_shipping']){
print_admin_table("<center> $phrases[cannot_delete_default_status] </center>");
}else{  
    db_query("delete from  store_orders_status where id='$id'");
}
}
//----- edit -----
if($action=="edit_ok"){
    db_query("update store_orders_status set name='".db_escape($name)."',text_color='".db_escape($text_color)."',details='".db_escape($details,false)."',show_payment='".intval($show_payment)."' where id='$id'");
}

//---- add ----
if($action=="add_ok"){
  db_query("insert into store_orders_status (name,text_color,active,details,show_payment) values ('".db_escape($name)."','".db_escape($text_color)."','1','".db_escape($details,false)."','".intval($show_payment)."')");
     
}

  
    print "<p align=center class=title>$pherases[orders_status]</p>";
$qr = db_query("select * from store_orders_status order by ord asc");

print "<a href='orders_status.php?action=add' class='add'>$phrases[add_button]</a><br><br>";  

if(db_num($qr)){
print "<center><table width=100% class=grid>
<tr><td width=100%>
<div>
<table width=100%>
<tr>
<td width=25></td>
    <td width=20%><b>$phrases[the_name]</b></td>
    <td width=160 align=center><b>$phrases[default]</b><br><font color=#ACACAC size=1> $phrases[in_orders_without_shipping]</font></td>";
// <td width=160 align=center><b>$phrases[default]</b><br><font color=#ACACAC size=1> $phrases[in_orders_with_shipping] </font></td>
print "<td></td></tr>
    </table></div>
    
<div id=\"data_list\">";
while($data = db_fetch($qr)){

    if($row_class == 'row_1'){$row_class = 'row_2';}else{  $row_class = 'row_1';}

    print "<div id=\"item_$data[id]\" class='$row_class'>
<table width=100%>
<tr>
<td class=\"handle\"></td>
    <td width=20%><b>".iif($data['text_color'],"<font color=\"$data[text_color]\">$data[name]</font>",$data['name'])."</b></td>
    
    <td width=160 align=center>".iif($data['default'],"<b>$phrases[default]</b>","<a href='orders_status.php?action=set_default&id=$data[id]'>$phrases[set_default]</a>")."</td>";
 //    <td width=160 align=center>".iif($data['default_if_shipping'],"<b>$phrases[default]</b>","<a href='orders_status.php?action=set_default_if_shipping&id=$data[id]'>$phrases[set_default]</a>")."</td>
   
   
print "  <td align='$global_align_x'>".iif($data['active'],"<a href='orders_status.php?action=disable&id=$data[id]'>$phrases[disable]</a>",
    "<a href='orders_status.php?action=enable&id=$data[id]'>$phrases[enable]</a>")." - 
    <a href=\"orders_status.php?action=edit&id=$data[id]\">$phrases[edit]</a> - 
    <a href='orders_status.php?action=del&id=$data[id]' onClick=\"return confirm('".$phrases['are_you_sure']."');\">$phrases[delete]</a></td></tr>
    </table></div>";
}                         
print "</div></td></tr></table></center>";

print "<script type=\"text/javascript\">
        init_sortlist('data_list','orders_status');
</script>";


}else{
    print_admin_table("<center>  $phrases[no_data] </center>");  
}
}

//------- Status Edit ---------
if($action=="edit"){
 $id = intval($id);
 
 if_admin('orders_status');
 
$qr = db_query("select * from store_orders_status where id='$id'");
if(db_num($qr)){
$data = db_fetch($qr);

print "<ul class='nav-bar'>
    <li><a href='orders_status.php?action=orders_status'>$phrases[orders_status]</a></li>
    <li>$data[name]</li>
        </ul>

<form action='orders_status.php' method=post>
<input type=hidden name=action value='edit_ok'>
<input type=hidden name=id value='$id'>

<center><table width=95% class=grid>
<tr><td>$phrases[the_name]</td><td><input type=text name=name size=30 value=\"$data[name]\"></td></tr>
<tr><td>$phrases[text_color]</td><td><input type=text name=text_color size=30 value=\"$data[text_color]\" dir=ltr></td></tr>

<tr><td>$phrases[the_description]</td><td><textarea name=details cols=40 rows=5>$data[details]</textarea></td></tr> 

 <tr><td> $phrases[show_payment_options] : </td><td>" ;
 print_select_row("show_payment",array($phrases['no'],$phrases['yes']),$data['show_payment']);
 print "</td></tr>
 
<tr><td colspan=2 align=center><input type=submit value=' $phrases[edit] '></td></tr>
</table></center></form>
";
}else{
    print_admin_table("<center>$phrases[err_wrong_url]</center>");
}   
}

//------- Stauts Add ---------
if($action=="add"){

    if_admin('orders_status');
    
print "<ul class='nav-bar'>
    <li><a href='orders_status.php?action=orders_status'>$phrases[orders_status]</a></li>
    <li>$phrases[add]</li>
        </ul>

<form action='orders_status.php' method=post>
<input type=hidden name=action value='add_ok'>


<center><table width=95% class=grid>
<tr><td>$phrases[the_name]</td><td><input type=text name=name size=30></td></tr>
<tr><td>$phrases[text_color]</td><td><input type=text name=title size=30  dir=ltr></td></tr>
<tr><td>$phrases[the_description]</td><td><textarea name=details cols=40 rows=5></textarea></td></tr>   

 <tr><td> $phrases[show_payment_options] : </td><td>" ;
 print_select_row("show_payment",array($phrases['no'],$phrases['yes']),"0");
 print "</td></tr>
 
 
<tr><td colspan=2 align=center><input type=submit value=' $phrases[add_button] '></td></tr>
</table></center></form>
";
  
}

//-----------end ----------------
 require(ADMIN_DIR.'/end.php');