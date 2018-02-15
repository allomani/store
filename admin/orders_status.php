<?php
/**
 *  Allomani E-Store v1.0
 * 
 * @package Allomani.E-Store
 * @version 1.0
 * @copyright (c) 2006-2018 Allomani , All rights reserved.
 * @author Ali Allomani <info@allomani.com>
 * @link http://allomani.com
 * @license GNU General Public License version 3.0 (GPLv3)
 * 
 */

 if(!defined('IS_ADMIN')){die('No Access');} 
 
//----------- Orders Statuss --------
if($action=="orders_status" || $action=="orders_status_edit_ok" || $action=="orders_status_del" || 
$action=="orders_status_add_ok" || $action=="orders_status_enable" || $action=="orders_status_disable" ||
$action=="orders_status_set_default" || $action=="orders_status_set_default_if_shipping"){
$id = intval($id);

if_admin('orders_status');

//----- set default  ---//
if($action=="orders_status_set_default"){
db_query("update store_orders_status set `default`=1 where id='$id'");
db_query("update store_orders_status set `default`=0 where id !='$id'");  
}
//----- set default if shipping ---//
if($action=="orders_status_set_default_if_shipping"){
db_query("update store_orders_status set `default_if_shipping`=1 where id='$id'"); 
db_query("update store_orders_status set `default_if_shipping`=0 where id !='$id'");  
}

//------ enable ----
if($action=="orders_status_enable"){
db_query("update store_orders_status set active=1 where id='$id'");    
}
//------ disable ----
if($action=="orders_status_disable"){
$data_st = db_qr_fetch("select `default`,`default_if_shipping` from store_orders_status where id='$id'");
if($data_st['default'] || $data_st['default_if_shipping']){
print_admin_table("<center> $phrases[cannot_disable_default_status] </center>");
}else{    
db_query("update store_orders_status set active=0 where id='$id'");
}    
}
//----- del ----
if($action=="orders_status_del"){
    $data_st = db_qr_fetch("select `default`,`default_if_shipping` from store_orders_status where id='$id'");
if($data_st['default'] || $data_st['default_if_shipping']){
print_admin_table("<center> $phrases[cannot_delete_default_status] </center>");
}else{  
    db_query("delete from  store_orders_status where id='$id'");
}
}
//----- edit -----
if($action=="orders_status_edit_ok"){
    db_query("update store_orders_status set name='".db_escape($name)."',text_color='".db_escape($text_color)."',details='".db_escape($details,false)."',show_payment='".intval($show_payment)."' where id='$id'");
}

//---- add ----
if($action=="orders_status_add_ok"){
  db_query("insert into store_orders_status (name,text_color,active,details,show_payment) values ('".db_escape($name)."','".db_escape($text_color)."','1','".db_escape($details,false)."','".intval($show_payment)."')");
     
}

  
    print "<p align=center class=title>$pherases[orders_status]</p>";
$qr = db_query("select * from store_orders_status order by ord asc");

print "<img src='images/add.gif'>&nbsp;<a href='index.php?action=orders_status_add'>$phrases[add_button]</a><br><br>";  

if(db_num($qr)){
print "<center><table width=100% class=grid>
<tr><td width=100%>
<div>
<table width=100%>
<tr>
<td width=25>
     </span> 
      </td>
    <td width=20%><b>$phrases[the_name]</b></td>
    
    <td width=160 align=center><b>$phrases[default]</b><br><font color=#ACACAC size=1> $phrases[in_orders_without_shipping]</font></td>
     <td width=160 align=center><b>$phrases[default]</b><br><font color=#ACACAC size=1> $phrases[in_orders_with_shipping] </font></td>
   
   
    <td align=center><b>$phrases[the_options]</b></td></tr>
    </table></div>
    
<div id=\"orders_status_data_list\">";
while($data = db_fetch($qr)){
    print "<div id=\"item_$data[id]\" onmouseover=\"this.style.backgroundColor='#EFEFEE'\" onmouseout=\"this.style.backgroundColor='#FFFFFF'\">
<table width=100%>
<tr>
<td width=25>
      <span style=\"cursor: move;\" class=\"handle\"><img src='images/move.gif' alt='$phrases[click_and_drag_to_change_order]'></span> 
      </td>
    <td width=20%><b>".iif($data['text_color'],"<font color=\"$data[text_color]\">$data[name]</font>",$data['name'])."</b></td>
    
    <td width=160 align=center>".iif($data['default'],"<b>$phrases[default]</b>","<a href='index.php?action=orders_status_set_default&id=$data[id]'>$phrases[set_default]</a>")."</td>
     <td width=160 align=center>".iif($data['default_if_shipping'],"<b>$phrases[default]</b>","<a href='index.php?action=orders_status_set_default_if_shipping&id=$data[id]'>$phrases[set_default]</a>")."</td>
   
   
    <td align=center>".iif($data['active'],"<a href='index.php?action=orders_status_disable&id=$data[id]'>$phrases[disable]</a>",
    "<a href='index.php?action=orders_status_enable&id=$data[id]'>$phrases[enable]</a>")." - 
    <a href=\"index.php?action=orders_status_edit&id=$data[id]\">$phrases[edit]</a> - 
    <a href='index.php?action=orders_status_del&id=$data[id]' onClick=\"return confirm('".$phrases['are_you_sure']."');\">$phrases[delete]</a></td></tr>
    </table></div>";
}                         
print "</div></td></tr></table></center>";

print "<script type=\"text/javascript\">
        init_sortlist('orders_status_data_list','set_orders_status_sort');
</script>";


}else{
    print_admin_table("<center>  $phrases[no_data] </center>");  
}
}

//------- Status Edit ---------
if($action=="orders_status_edit"){
 $id = intval($id);
 
 if_admin('orders_status');
 
$qr = db_query("select * from store_orders_status where id='$id'");
if(db_num($qr)){
$data = db_fetch($qr);

print "<img src='images/arrw.gif'>&nbsp;<a href='index.php?action=orders_status'>$phrases[orders_status]</a> / $data[name] <br><br>  

<form action=index.php method=post>
<input type=hidden name=action value='orders_status_edit_ok'>
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
if($action=="orders_status_add"){

    if_admin('orders_status');
    
print "<img src='images/arrw.gif'>&nbsp;<a href='index.php?action=orders_status'>$phrases[orders_status]</a> / $phrases[add] <br><br>  

<form action=index.php method=post>
<input type=hidden name=action value='orders_status_add_ok'>


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
