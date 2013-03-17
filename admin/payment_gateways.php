<?
require('./start.php'); 
 
//----------- Payment Methods --------
if(!$action || $action=="payment_gateways" || $action=="edit_ok" || $action=="del" || 
$action=="add_ok" || $action=="enable" || $action=="disable"){

if_admin();

//------ enable ----
if($action=="enable"){
db_query("update store_payment_gateways set active=1 where id='$id'");    
}
//------ disable ----
if($action=="disable"){
db_query("update store_payment_gateways set active=0 where id='$id'");    
}
//----- del ----
if($action=="del"){
    db_query("delete from  store_payment_gateways where id='$id'");
    db_query("delete from store_payment_gateways_settings where cat='$id'");
}
//----- edit -----
if($action=="edit_ok"){
    db_query("update store_payment_gateways set class='".db_escape($class)."',name='".db_escape($name)."',img='".db_escape($img)."' where id='$id'");

$qr  = db_query("select name from store_payment_gateways_settings where cat='$id'");
while($data=db_fetch($qr)){   
$availabe_settings[] = $data['name'];
}
$availabe_settings = (array) $availabe_settings;


foreach($gateway_settings as $key=>$value){
   if(!in_array($key,$availabe_settings)){
       db_query("insert into store_payment_gateways_settings (cat,name,value) values ('$id','".db_escape($key)."','".db_escape($value)."')");
   }else{
       db_query("update store_payment_gateways_settings set value='".db_escape($value)."' where name like '".db_escape($key)."' and cat='$id'");
   }
     
}

}

//---- add ----
if($action=="add_ok"){
  db_query("insert into store_payment_gateways (class,name,img,active) values ('".db_escape($class)."','".db_escape($name)."','".db_escape($img)."','1')");
 $new_id = db_inserted_id();
 if($new_id){
     $ord = db_fetch_first("select max(ord) from store_payment_gateways")+1;
     db_query("update store_payment_gateways set ord = $ord where id='$new_id'");
    print "<script>window.location = 'payment_gateways.php?action=edit&id=$new_id';</script>";
 }    
}

  
    print "<p align=center class=title>$phrases[payment_gateways]</p>";
$qr = db_query("select * from store_payment_gateways order by ord asc");

print "<a href='payment_gateways.php?action=add' class='add'>$phrases[add_button]</a><br><br>";  

if(db_num($qr)){
print "<table width=100% class=grid>
<tr><td width=100%>
<div id=\"payment_gateways_data_list\">";
while($data = db_fetch($qr)){
    toggle_tr_class();
    print "<div id=\"item_$data[id]\" class='$tr_class'>
<table width=100%>
<tr>
<td class=\"handle\"></td>
    <td width=30%>$data[class]</td><td width=30%>$data[name]</td>
    <td align='$global_align_x'>".iif($data['active'],"<a href='payment_gateways.php?action=disable&id=$data[id]'>$phrases[disable]</a>",
    "<a href='payment_gateways.php?action=enable&id=$data[id]'>$phrases[enable]</a>")." - 
    <a href=\"payment_gateways.php?action=edit&id=$data[id]\">$phrases[edit]</a> - 
    <a href='payment_gateways.php?action=del&id=$data[id]' onClick=\"return confirm('".$phrases['are_you_sure']."');\">$phrases[delete]</a></td></tr>
    </table></div>";
}
print "</div></td></tr></table></center>";

print "<script type=\"text/javascript\">
        init_sortlist('payment_gateways_data_list','payment_gateways');
</script>";


}else{
    print_admin_table("<center>  $phrases[no_data] </center>");  
}
}

//------- Gateway Edit ---------
if($action=="edit"){
 $id = intval($id);
 
 if_admin();
 
$qr = db_query("select * from store_payment_gateways where id='$id'");
if(db_num($qr)){
$data = db_fetch($qr);

print "<ul class='nav-bar'>
<li><a href='payment_gateways.php'>$phrases[payment_gateways]</a></li>
<li>$data[name]</li>
</ul>


<form action=payment_gateways.php method=post name=sender>
<input type=hidden name=action value='edit_ok'>
<input type=hidden name=id value='$id'>

<table width=100% class=grid>
<tr><td><b>$phrases[the_type]</b></td><td><input type=text name=class size=30 value=\"$data[class]\"></td></tr>
<tr><td><b>$phrases[the_name]</b></td><td><input type=text name='name' size=30 value=\"$data[name]\"></td></tr>
  <tr><td>
  <b>$phrases[the_image]</b></td>
  <td> <table><tr><td><input type=text  dir=ltr size=30 name=img value=\"$data[img]\"></td><td><a href=\"javascript:uploader('payment','img');\"><img src='images/file_up.gif' border=0 alt='$phrases[upload_file]'></a></td></tr></table>

   </td></tr> ";
  
print "
</table>";

//--------- gateway settings ------------
$qrs  = db_query("select name,value from store_payment_gateways_settings where cat='$id'");
while($datas=db_fetch($qrs)){   
$gateway_settings[$datas['name']] = $datas['value'];
}

  
print "<br>
<fieldset>
<legend><b>$phrases[the_settings]</b></legend>";
$gateway_file = CWD."/includes/gateways/{$data['class']}.php"; 
if(file_exists($gateway_file)){ 
require($gateway_file); 
if(class_exists($data['class'])){
$m = new $data['class'];
print "<table width=100%>";
foreach($m->settings as $n=>$s){
    print "<tr><td><b>$n</b></td><td>";
    if($s['type']=="select"){
    print_select_row("gateway_settings[$n]",$s['options'],$gateway_settings[$n]);
    }else{
    print "<input type='text' name=\"gateway_settings[$n]\" value=\"{$gateway_settings[$n]}\">";
    }
    
    print "</td></tr>";
} 
print "</table>";
}else{ 
    print " Gateway Class Not Exists";
}
}else{
     print " Gateway File Not Exists";   
}
print "</fieldset>";

//-----------------------------------------


print "<center><br><input type=submit value=' $phrases[edit] '></center></form>
";
//$m->print_form();
}else{
    print_admin_table("<center>$phrases[err_wrong_url]</center>");
}   
}

//------- Gateways Add ---------
if($action=="add"){

    if_admin();

$gateways = get_files(CWD."/includes/gateways/","php");
foreach($gateways as $g){
    $name = str_replace(".php","",basename($g));
    $available_gateways[$name] =$name;
}
    
print "<ul class='nav-bar'>
<li><a href='payment_gateways.php'>$phrases[payment_gateways]</a></li>
<li>$phrases[add]</li>
</ul>

<form action=payment_gateways.php method=post name=sender>
<input type=hidden name=action value='add_ok'>


<table width=100% class=grid>
<tr><td><b>$phrases[the_type]</b></td><td>";
print_select_row("class",$available_gateways);
print "</td></tr>
<tr><td><b>$phrases[the_name]</b></td><td><input type=text name=name size=30></td></tr>
  <tr><td>
  <b>$phrases[the_image]</b></td>
  <td> <table><tr><td><input type=text  dir=ltr size=30 name=img></td><td><a href=\"javascript:uploader('payment','img');\"><img src='images/file_up.gif' border=0 title='$phrases[upload_file]'></a></td></tr></table>

   </td></tr>
<tr><td colspan=2 align=center><input type=submit value=' $phrases[add_button] '></td></tr>
</table></center></form>
";
  
}


//-----------end ----------------
 require(ADMIN_DIR.'/end.php');