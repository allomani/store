<?
// Edited : 07-10-2009

 if(!defined('IS_ADMIN')){die('No Access');} 
 
//----------- Payment Methods --------
if($action=="payment_gateways" || $action=="payment_gateways_edit_ok" || $action=="payment_gateways_del" || 
$action=="payment_gateways_add_ok" || $action=="payment_gateways_enable" || $action=="payment_gateways_disable"){
$id = intval($id);

if_admin();

//------ enable ----
if($action=="payment_gateways_enable"){
db_query("update store_payment_gateways set active=1 where id='$id'");    
}
//------ disable ----
if($action=="payment_gateways_disable"){
db_query("update store_payment_gateways set active=0 where id='$id'");    
}
//----- del ----
if($action=="payment_gateways_del"){
    db_query("delete from  store_payment_gateways where id='$id'");
}
//----- edit -----
if($action=="payment_gateways_edit_ok"){
    db_query("update store_payment_gateways set name='".db_escape($name)."',title='".db_escape($title)."',img='".db_escape($img)."',code='".db_escape($code,false)."' where id='$id'");
}

//---- add ----
if($action=="payment_gateways_add_ok"){
  db_query("insert into store_payment_gateways (name,title,img,code,active) values ('".db_escape($name)."','".db_escape($title)."','".db_escape($img)."','".db_escape($code,false)."','1')");
     
}

  
    print "<p align=center class=title>$phrases[payment_gateways]</p>";
$qr = db_query("select * from store_payment_gateways order by ord asc");

print "<img src='images/add.gif'>&nbsp;<a href='index.php?action=payment_gateways_add'>$phrases[add_button]</a><br><br>";  

if(db_num($qr)){
print "<center><table width=90% class=grid>
<tr><td width=100%>
<div id=\"payment_gateways_data_list\">";
while($data = db_fetch($qr)){
    print "<div id=\"item_$data[id]\" onmouseover=\"this.style.backgroundColor='#EFEFEE'\" onmouseout=\"this.style.backgroundColor='#FFFFFF'\">
<table width=100%>
<tr>
<td width=25>
      <span style=\"cursor: move;\" class=\"handle\"><img src='images/move.gif' alt='$phrases[click_and_drag_to_change_order]'></span> 
      </td>
    <td width=30%>$data[name]</td><td width=30%>$data[title]</td>
    <td>".iif($data['active'],"<a href='index.php?action=payment_gateways_disable&id=$data[id]'>$phrases[disable]</a>",
    "<a href='index.php?action=payment_gateways_enable&id=$data[id]'>$phrases[enable]</a>")." - 
    <a href=\"index.php?action=payment_gateways_edit&id=$data[id]\">$phrases[edit]</a> - 
    <a href='index.php?action=payment_gateways_del&id=$data[id]' onClick=\"return confirm('".$phrases['are_you_sure']."');\">$phrases[delete]</a></td></tr>
    </table></div>";
}
print "</div></td></tr></table></center>";

print "<script type=\"text/javascript\">
        init_sortlist('payment_gateways_data_list','set_payment_gateways_sort');
</script>";


}else{
    print_admin_table("<center>  $phrases[no_data] </center>");  
}
}

//------- Gateway Edit ---------
if($action=="payment_gateways_edit"){
 $id = intval($id);
 
 if_admin();
 
$qr = db_query("select * from store_payment_gateways where id='$id'");
if(db_num($qr)){
$data = db_fetch($qr);

print "<img src='images/arrw.gif'>&nbsp;<a href='index.php?action=payment_gateways'>$phrases[payment_gateways]</a> / $data[name] <br><br>  

<form action=index.php method=post name=sender>
<input type=hidden name=action value='payment_gateways_edit_ok'>
<input type=hidden name=id value='$id'>

<center><table width=95% class=grid>
<tr><td><b>$phrases[the_name]</b></td><td><input type=text name=name size=30 value=\"$data[name]\"></td></tr>
<tr><td><b>$phrases[the_title]</b></td><td><input type=text name=title size=30 value=\"$data[title]\"></td></tr>
  <tr><td>
  <b>$phrases[the_image]</b></td>
  <td> <table><tr><td><input type=text  dir=ltr size=30 name=img value=\"$data[img]\"></td><td><a href=\"javascript:uploader('payment','img');\"><img src='images/file_up.gif' border=0 alt='$phrases[upload_file]'></a></td></tr></table>

   </td></tr>
   
   
<tr><td><b>$phrases[sending_form_code]</b></td><td><textarea name=code cols=60 rows=20 dir=ltr>$data[code]</textarea></td></tr> 

<tr><td colspan=2 align=center><input type=submit value=' $phrases[edit] '></td></tr>
</table></center></form>
";
}else{
    print_admin_table("<center>$phrases[err_wrong_url]</center>");
}   
}

//------- Gateways Add ---------
if($action=="payment_gateways_add"){

    if_admin();
    
print "<img src='images/arrw.gif'>&nbsp;<a href='index.php?action=payment_gateways'>$phrases[payment_gateways]</a> / $phrases[add] <br><br>  

<form action=index.php method=post name=sender>
<input type=hidden name=action value='payment_gateways_add_ok'>


<center><table width=95% class=grid>
<tr><td><b>$phrases[the_name]</b></td><td><input type=text name=name size=30></td></tr>
<tr><td><b>$phrases[the_title]</b></td><td><input type=text name=title size=30></td></tr>
  <tr><td>
  <b>$phrases[the_image]</b></td>
  <td> <table><tr><td><input type=text  dir=ltr size=30 name=img></td><td><a href=\"javascript:uploader('payment','img');\"><img src='images/file_up.gif' border=0 alt='$phrases[upload_file]'></a></td></tr></table>

   </td></tr>
   
   
<tr><td><b>$phrases[sending_form_code]</b></td><td><textarea name=code cols=60 rows=20 dir=ltr></textarea></td></tr> 

<tr><td colspan=2 align=center><input type=submit value=' $phrases[add_button] '></td></tr>
</table></center></form>
";
  
}