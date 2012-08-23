<?
// Edited : 07-10-2009


   if(!defined('IS_ADMIN')){die('No Access');} 
 
//----------- Payment Methods --------
if($action=="payment_methods" || $action=="payment_methods_edit_ok" || $action=="payment_methods_del" || 
$action=="payment_methods_add_ok" || $action=="payment_methods_enable" || $action=="payment_methods_disable"){
if_admin();
$id=intval($id);
 
 //------ enable ----
if($action=="payment_methods_enable"){
db_query("update store_payment_methods set active=1 where id='$id'");    
}
//------ disable ----
if($action=="payment_methods_disable"){
db_query("update store_payment_methods set active=0 where id='$id'");    
}
 //---- del ----
 if($action=="payment_methods_del"){
     db_query("delete from  store_payment_methods where id='$id'");
 }
 //--- edit ----
 if($action=="payment_methods_edit_ok"){
  $gateways_str=implode(",",(array) $gateways);
     
  db_query("update store_payment_methods set name='".db_escape($name)."',img='".db_escape($img)."',details='".db_escape($details,false)."',is_gateway='".intval($is_gateway)."',gateways='".db_escape($gateways_str)."' where id='$id'");
     
 } 
 
  //--- add ----
 if($action=="payment_methods_add_ok"){
  $gateways_str=implode(",",(array) $gateways);
     
  db_query("insert store_payment_methods (name,img,details,is_gateway,gateways,active) values ('".db_escape($name)."','".db_escape($img)."','".db_escape($details,false)."','".intval($is_gateway)."','".db_escape($gateways_str)."','1')");
     
 }
 
 //--------------------------------  
    print "<p align=center class=title>$phrases[payment_methods]</p>";
$qr = db_query("select * from store_payment_methods order by ord asc");

print "
<img src='images/add.gif'>&nbsp;<a href='index.php?action=payment_methods_add'>$phrases[add_button]</a><br><br>";
if(db_num($qr)){
print "<center><table width=90% class=grid>
<tr><td width=100%>
<div id=\"payment_methods_data_list\">";
while($data = db_fetch($qr)){
    print "<div id=\"item_$data[id]\" onmouseover=\"this.style.backgroundColor='#EFEFEE'\" onmouseout=\"this.style.backgroundColor='#FFFFFF'\">
<table width=100%>
<tr>
<td width=25>
      <span style=\"cursor: move;\" class=\"handle\"><img src='images/move.gif' alt='$phrases[click_and_drag_to_change_order]'></span> 
      </td>
      <td width=60%>$data[name]</td>
    <td>".iif($data['active'],"<a href='index.php?action=payment_methods_disable&id=$data[id]'>$phrases[disable]</a>",
    "<a href='index.php?action=payment_methods_enable&id=$data[id]'>$phrases[enable]</a>")." -
    <a href='index.php?action=payment_methods_edit&id=$data[id]'>$phrases[edit]</a> - 
    <a href='index.php?action=payment_methods_del&id=$data[id]' onClick=\"return confirm('".$phrases['are_you_sure']."');\">$phrases[delete]</a></td></tr>
    </table></div>";
}

print "</div></td></tr></table></center>";

print "<script type=\"text/javascript\">
        init_sortlist('payment_methods_data_list','set_payment_methods_sort');
</script>";

}else{
     print_admin_table("<center>  $phrases[no_data] </center>");   
}
}

///----------- Edit ------------
if($action=="payment_methods_edit"){
if_admin();
 $id=intval($id);
 
    $qr=db_query("select * from store_payment_methods where id='$id'");
    if(db_num($qr)){
        $data = db_fetch($qr);
        
        print "<img src='images/arrw.gif'>&nbsp;<a href='index.php?action=payment_methods'>$phrases[payment_methods]</a> / $data[name] <br><br>   
        
        <center><form action=index.php method=post name=sender>
        <input type=hidden name=id value='$id'>
        <input type=hidden name=action value='payment_methods_edit_ok'>
        <table width=90% class=grid>
        <tr><td><b>$phrases[the_name]</b></td><td><input type=text name=name value=\"$data[name]\" size=30></td></tr>
            <tr><td>
  <b>$phrases[the_image]</b></td>
  <td> <table><tr><td><input type=text  dir=ltr size=30 name=img value=\"$data[img]\"></td><td><a href=\"javascript:uploader('payment','img');\"><img src='images/file_up.gif' border=0 alt='$phrases[upload_file]'></a></td></tr></table>

   </td></tr>
         <tr><td><b>$phrases[the_details]</b></td><td><textarea cols=30 rows=7 name=details>$data[details]</textarea></td></tr>
           <tr><td><b>$phrases[is_gateway]</b></td><td>";
           print_select_row("is_gateway",array($phrases['no'],$phrases['yes']),$data['is_gateway']);
           print "</td></tr>  
           <tr><td><b>$phrases[payment_gateways]</b></td><td>
           <table width=100%><tr>";
         $gateways = (array) explode(",",$data['gateways']);
         
        $qro=db_query("select * from store_payment_gateways order by ord");
        $c=0;
    while($datao=db_fetch($qro)){
   if($c==4){
    print "</tr><tr>" ;
    $c=0;
    }
    
    print "<td><input type=\"checkbox\" name=\"gateways[]\" value=\"$datao[id]\"".iif(in_array($datao['id'],$gateways),' checked').">".iif($datao['title'],$datao['title'],$datao['name'])."</td>";
    $c++;
    }
                       
           print "</table></td></tr>
           
         <tr><td colspan=2 align=center><input type=submit value=' $phrases[edit] '></td></tr>
        </table>
        </form>
        </center>";
    }else{
        print_admin_table("<center>".$phrases['err_wrong_url']."</center>");
    }
    
}

///----------- Add ------------
if($action=="payment_methods_add"){
if_admin();
 
        print "
        <img src='images/arrw.gif'>&nbsp;<a href='index.php?action=payment_methods'>$phrases[payment_methods]</a> / $phrases[add] <br><br>
        
        <center><form action=index.php method=post name=sender>
        <input type=hidden name=id value='$id'>
        <input type=hidden name=action value='payment_methods_add_ok'>
        <table width=90% class=grid>
        <tr><td><b>$phrases[the_name]</b></td><td><input type=text name=name value=\"$data[name]\" size=30></td></tr>
            <tr><td>
  <b>$phrases[the_image]</b></td>
  <td> <table><tr><td><input type=text  dir=ltr size=30 name=img></td><td><a href=\"javascript:uploader('payment','img');\"><img src='images/file_up.gif' border=0 alt='$phrases[upload_file]'></a></td></tr></table>

   </td></tr>
         <tr><td><b>$phrases[the_details]</b></td><td><textarea cols=30 rows=7 name=details>$data[details]</textarea></td></tr>
           <tr><td><b>$phrases[is_gateway]</b></td><td>";
           print_select_row("is_gateway",array($phrases['no'],$phrases['yes']),$data['is_gateway']);
           print "</td></tr>  
           <tr><td><b>$phrases[payment_gateways]</b></td><td>
           <table width=100%><tr>";
         $gateways = (array) explode(",",$data['gateways']);
         
        $qro=db_query("select * from store_payment_gateways order by ord");
        $c=0;
    while($datao=db_fetch($qro)){
   if($c==4){
    print "</tr><tr>" ;
    $c=0;
    }
    
    print "<td><input type=\"checkbox\" name=\"gateways[]\" value=\"$datao[id]\"".iif(in_array($datao['id'],$gateways),' checked').">".iif($datao['title'],$datao['title'],$datao['name'])."</td>";
    $c++;
    }
                       
           print "</table></td></tr>
           
         <tr><td colspan=2 align=center><input type=submit value=' $phrases[add_button] '></td></tr>
        </table>
        </form>
        </center>";
  
}