<?
require("global.php");
require(CWD . "/includes/framework_start.php");
//-------------------------------------------------
 if(!$action || $action=="addresses" || $action=="del" || $action=="add_ok" || $action=="edit_ok" || $action=="set_billing_default" || $action=="set_shipping_default"){
 if(check_member_login()){  
     
 //------ del -----
 if($action=="del"){
     db_query("delete from store_clients_addresses where id='$id' and client_id='$member_data[id]'");  
 }
 //--- add ----
 if($action=="add_ok"){
     db_query("insert into store_clients_addresses (client_id,address_title,name,country,city,address_1,address_2,tel)
     values ('$member_data[id]','".db_escape($address_title)."','".db_escape($name)."','".db_escape($country)."','".db_escape($city)."','".db_escape($address_1)."','".db_escape($address_2)."','".db_escape($tel)."')");

 }
 //---- edit ---
 if($action=="edit_ok"){
     db_query("update store_clients_addresses set address_title='".db_escape($address_title)."',name='".db_escape($name)."',country='".db_escape($country)."',city='".db_escape($city)."',address_1='".db_escape($address_1)."',address_2='".db_escape($address_2)."',tel='".db_escape($tel)."' where id='$id' and client_id='$member_data[id]'");
 }
 //----- set billing default --- 
 if($action=="set_billing_default"){
 db_query("update store_clients_addresses set default_billing=0");
 db_query("update store_clients_addresses set default_billing=1 where id='$id'");  
 }
 //----- set shipping default --- 
 if($action=="set_shipping_default"){
 db_query("update store_clients_addresses set default_shipping=0");
 db_query("update store_clients_addresses set default_shipping=1 where id='$id'");  
 }
 //---------
 
 
     $qr=db_query("select  * from store_clients_addresses where client_id='$member_data[id]' order by id desc");
     open_table("$phrases[the_addresses]");
     print "<div class='add-new'><a href='my_addresses.php?action=add'>$phrases[add_new_address]</a></div>";
     
     if(db_num($qr)){
         print "<table width=100%>
         
         <tr>
         <th><b>$phrases[address_title]</b></th>
         <th><b>$phrases[billing_name]</b></th>  
         <th><b>$phrases[the_address]</b></th> 
         <th><b>$phrases[billing_address]</b></th> 
         <th><b>$phrases[shipping_address]</b></th>  
         <th><b>$phrases[the_options]</b></th>  
         </tr>
         ";
         while($data = db_fetch($qr)){
         if($c==1){$c=2;}else{$c=1;}
         
          print "<tr class='row_".$c."'>
          <td><b>$data[address_title]</b></td>  
          <td>$data[name]</td>
          <td>$data[address_1] <br> $data[address_2]</td>
          <td class='center'>".iif($data['default_billing'],"<b>[ $phrases[default] ]</b>","<a href='my_addresses.php?action=set_billing_default&id=$data[id]'>$phrases[set_default]</a>")."</td>
          <td class='center'>".iif($data['default_shipping'],"<b>[ $phrases[default] ]</b>","<a href='my_addresses.php?action=set_shipping_default&id=$data[id]'>$phrases[set_default]</a>")."</td>
          <td class='center'><a href='my_addresses.php?action=edit&id=$data[id]'>$phrases[edit]</a> - <a href='my_addresses.php?action=del&id=$data[id]' onClick=\"return confirm('".$phrases['are_you_sure']."');\">$phrases[delete]</a></td></tr>";   
         }
         print "</table>";
     }else{
         print "<center> $phrases[no_saved_addresses] </center> ";
     }
     close_table();
}else{
 login_redirect();

 }
 }
 
 //--------------- Add New Address -----------//
 if($action=="add"){
      if(check_member_login()){  
      print "<ul class='nav-bar'>
          <li><a href='my_addresses.php'>$phrases[the_addresses]</a></li>
          <li>$phrases[add_new_address]</li>
          </ul>";
      
      open_table("$phrases[add_new_address]");
      print "<form action=my_addresses.php method=post>
      <input type=hidden name=action value='add_ok'>
      <table width=100%>
      <tr><td><b> $phrases[address_title] </b></td><td><input type=text name='address_title' size=20></td></tr>
      
      <tr><td></td></tr>
       <tr><td><b> $phrases[billing_name] </b></td><td><input type=text name='name' size=20></td></tr> 
       <tr><td><b>$phrases[country]</b></td><td><select name=\"country\">";
 $qr_c = db_query("select * from store_countries order by name asc");
 while($data_c = db_fetch($qr_c)){
     print "<option value=\"$data_c[code]\">$data_c[name]</option>";
 }
 
 print "</select></td></tr>
   <tr><td><b>$phrases[city]</b></td><td><input type=text name=\"city\" size=30></td></tr> 
  <tr><td><b>$phrases[the_address]</b></td><td><input type=text name=\"address_1\" size=30></td></tr>
 <tr><td></td><td><input type=text name=\"address_2\" size=30></td></tr>
 
 <tr><td><b>$phrases[telephone]</b></td><td><input type=text name=\"tel\" size=30></td></tr>
   
   <tr><td align=center colspan=2><input type=submit value=\"$phrases[add_button]\"></td></tr>
      </table>
      </form>";
      close_table(); 
      }else{
 login_redirect();

 }
 }
 
  //--------------- Edit Address -----------//
 if($action=="edit"){
      if(check_member_login()){ 
 $id=intval($id); 
 $qr = db_query("select * from store_clients_addresses where id='$id' and client_id='$member_data[id]'");
 if(db_num($qr)){
     $data = db_fetch($qr);
     
      print "<ul class='nav-bar'>
          <li><a href='my_addresses.php'>$phrases[the_addresses]</a></li>
          <li>$data[address_title]</li>
      <li>$phrases[edit]</li>
          </ul>";
      
      open_table($data['address_title']);
      print "<form action=my_addresses.php method=post>
      <input type=hidden name=action value='edit_ok'>
      <input type=hidden name=id value='$id'>
      
      <table width=100%>
      <tr><td><b> $phrases[address_title] </b></td><td><input type=text name='address_title' size=20 value=\"$data[address_title]\"></td></tr>
      
      <tr><td></td></tr>
       <tr><td><b>$phrases[billing_name]</b></td><td><input type=text name='name' size=20 value=\"$data[name]\"></td></tr> 
       <tr><td><b>$phrases[country]</b></td><td><select name=\"country\">";
 $qr_c = db_query("select * from store_countries order by name asc");
 while($data_c = db_fetch($qr_c)){
     print "<option value=\"$data_c[code]\"".iif($data['country']==$data_c['code'], "selected").">$data_c[name]</option>";
 }
 
 print "</select></td></tr>
   <tr><td><b>$phrases[city]</b></td><td><input type=text name=\"city\" size=30 value=\"$data[city]\"></td></tr> 
  <tr><td><b>$phrases[the_address]</b></td><td><input type=text name=\"address_1\" size=30 value=\"$data[address_1]\"></td></tr>
 <tr><td></td><td><input type=text name=\"address_2\" size=30 value=\"$data[address_2]\"></td></tr>
 
 <tr><td><b>$phrases[telephone]</b></td><td><input type=text name=\"tel\" size=30 value=\"$data[tel]\"></td></tr>
   
   <tr><td align=center colspan=2><input type=submit value=\"$phrases[edit]\"></td></tr>
      </table>
      </form>";
      close_table();
 }else{
     open_table();
     print "<center>$phrases[err_wrong_url]</center>";
     close_table();
 } 
      }else{
 login_redirect();

 }
 }
 
  //---------------------------------------------
require(CWD . "/includes/framework_end.php");    
?>