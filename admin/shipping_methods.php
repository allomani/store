<?
   if(!defined('IS_ADMIN')){die('No Access');} 
 
//----------- Payment Methods --------
if($action=="shipping_methods" || $action=="shipping_methods_edit_ok" || $action=="shipping_methods_del" || 
$action=="shipping_methods_add_ok" || $action=="shipping_methods_enable" || $action=="shipping_methods_disable"){
if_admin();
$id=intval($id);
 
 //------ enable ----
if($action=="shipping_methods_enable"){
db_query("update store_shipping_methods set active=1 where id='$id'");    
}
//------ disable ----
if($action=="shipping_methods_disable"){
db_query("update store_shipping_methods set active=0 where id='$id'");    
}
 //---- del ----
 if($action=="shipping_methods_del"){
     db_query("delete from  store_shipping_methods where id='$id'");
 }
 //--- edit ----
 if($action=="shipping_methods_edit_ok"){
     
  db_query("update store_shipping_methods set name='".db_escape($name)."' where id='$id'");
     
 } 
 
  //--- add ----
 if($action=="shipping_methods_add_ok"){
  $gateways_str=implode(",",(array) $gateways);
     
  db_query("insert store_shipping_methods (name,active) values ('".db_escape($name)."','1')");
     
 }
 
 //--------------------------------  
    print "<p align=center class=title>$phrases[shipping_methods]</p>";
$qr = db_query("select * from store_shipping_methods order by ord asc");

print "
<img src='images/add.gif'>&nbsp;<a href='index.php?action=shipping_methods_add'>$phrases[add_button]</a><br><br>";
if(db_num($qr)){
print "<center><table width=90% class=grid>
<tr><td width=100%>
<div id=\"shipping_methods_data_list\">";
while($data = db_fetch($qr)){
    print "<div id=\"item_$data[id]\" onmouseover=\"this.style.backgroundColor='#EFEFEE'\" onmouseout=\"this.style.backgroundColor='#FFFFFF'\">
<table width=100%>
<tr>
<td width=25>
      <span style=\"cursor: move;\" class=\"handle\"><img src='images/move.gif' alt='$phrases[click_and_drag_to_change_order]'></span> 
      </td>
      <td width=75%>$data[name]</td>
    <td>".iif($data['active'],"<a href='index.php?action=shipping_methods_disable&id=$data[id]'>$phrases[disable]</a>",
    "<a href='index.php?action=shipping_methods_enable&id=$data[id]'>$phrases[enable]</a>")." -
    <a href='index.php?action=shipping_methods_edit&id=$data[id]'>$phrases[edit]</a> - 
    <a href='index.php?action=shipping_methods_del&id=$data[id]' onClick=\"return confirm('".$phrases['are_you_sure']."');\">$phrases[delete]</a></td></tr>
    </table></div>";
}

print "</div></td></tr></table></center>";

print "<script type=\"text/javascript\">
        init_sortlist('shipping_methods_data_list','set_shipping_methods_sort');
</script>";

}else{
     print_admin_table("<center>  $phrases[no_data] </center>");   
}
}

///----------- Edit ------------
if($action=="shipping_methods_edit"){
if_admin();
 $id=intval($id);
 
    $qr=db_query("select * from store_shipping_methods where id='$id'");
    if(db_num($qr)){
        $data = db_fetch($qr);
        
        print "<img src='images/arrw.gif'>&nbsp;<a href='index.php?action=shipping_methods'>$phrases[shipping_methods]</a> / $data[name] <br><br>   
        
        <center><form action=index.php method=post>
        <input type=hidden name=id value='$id'>
        <input type=hidden name=action value='shipping_methods_edit_ok'>
        <table width=90% class=grid>
        <tr><td><b>$phrases[the_name]</b></td><td><input type=text name=name value=\"$data[name]\" size=30></td></tr>
           
         <tr><td colspan=2 align=center><input type=submit value=' $phrases[edit] '></td></tr>
        </table>
        </form>
        </center>";
    }else{
        print_admin_table("<center>".$phrases['err_wrong_url']."</center>");
    }
    
}

///----------- Add ------------
if($action=="shipping_methods_add"){
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