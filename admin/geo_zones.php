<?
require('./start.php');

if_admin();
 
 if($action=="del"){
     db_query("delete from store_geo where id='".$id."'");
     db_query("delete from store_geo_index where geo_id='".$id."'");
      
    js_redirect("geo_zones.php");
 }
 
 if($action=="edit_ok"){
     
     $payment_methods = (array) $payment_methods;
     $shipping_methods = (array) $shipping_methods;
     
     
     db_query("update store_geo set name='".db_escape($name)."',payment_methods='".implode(",", $payment_methods)."',shipping_methods='".implode(",", $shipping_methods)."' where id='".$id."'");
     
     //---- update countris -------//
     $data_countries = db_fetch_all("select country_code from store_geo_index where geo_id='".$id."'");
     $countries_list= array();
     foreach($data_countries as $d){
         $countries_list[] = $d['country_code'];
         }
     
   
    $countries = (array) $countries ;
    
    foreach($countries_list as $country){
        if(!in_array($country, $countries)){
            db_query("delete from store_geo_index where country_code='".db_escape($country)."' and geo_id='".$id."'");
            }
        }
    
   foreach($countries as $country){
          if(!in_array($country, $countries_list)){
              db_query("insert into store_geo_index (geo_id,country_code) values ('$id','".db_escape($country)."')");
              }
       }
        
     //--------------------------//
     
  js_redirect("geo_zones.php?action=edit&id=$id");
 }
     
if($action == "add_ok"){
   db_query("insert into store_geo (name) values ('".db_escape($name)."')");
   $id = db_inserted_id();
    js_redirect("geo_zones.php?action=edit&id=$id");
    }
    
    
 if(!$action){
?>

<div id="add_form" style="display:none;">
      <form action="geo_zones.php" method=post name=sender>
        <input type=hidden name=action value='add_ok'>
        <table width=90% class=grid>
        <tr><td><b><?=$phrases['the_name'];?></b></td><td><input type=text name="name" size=30 required></td></tr>
        <tr><td colspan=2 align=center><input type=submit value="<?= $phrases['add_button'];?> "></td></tr>
        </table>
      </form>
</div>


<script>
$(document).ready(function(){
    $('#geo_add').click(function(e){
          e.preventDefault();
       $('#add_form').dialog({modal: true});
        });
});
</script>
<?
     print "
         
<ul class='nav-bar'>
<li><a href='settings.php'>$phrases[the_settings]</a></li>
<li>$phrases[geo_zones]</li>
</ul>
    


         <p class='title' align='center'>$phrases[geo_zones]</p>
         
<p><a href=\"#\" id='geo_add' class='add'>$phrases[add]</a></p>";
     
     $qr = db_query("select * from store_geo order by id");
     if(db_num($qr)){
         print "<table width=100% class='grid'>";
         while($data=db_fetch($qr)){
              if($tr_class == "row_1"){$tr_class = "row_2";}else{$tr_class = "row_1";} 
              
             print "<tr class='$tr_class'><td><a href='geo_zones.php?action=edit&id=$data[id]'>$data[name]</a></td>
                 <td align='center'><a href='geo_zones.php?action=del&id=$data[id]' onClick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete]</a></td>
                     </tr>";
         }
         print "</table>";
     }else{
         print_admin_table("<center>   لا توجد مناطق جغرافية  </center>");
     }
 }
 
 
 if($action=="edit"){

 $qr = db_query("select * from store_geo where id='$id'");
 if(db_num($qr)){
     
     $data = db_fetch($qr);
     
?>
<script>
$(document).ready(function() {
 
    $('#add_btn').click(function(){
        $('#countries_all option:selected').each( function() {
                $('#countries').append("<option value='"+$(this).val()+"'>"+$(this).text()+"</option>");
            $(this).remove();
        });
    });
    $('#del_btn').click(function(){
        $('#countries option:selected').each( function() {
            $('#countries_all').append("<option value='"+$(this).val()+"'>"+$(this).text()+"</option>");
            $(this).remove();
        });
    });
 
            $('#geo_edit_form').submit(function(e){
               $('#countries option').each(function(i) {  
            $(this).attr("selected", "selected");  
            });  
            });
});
</script>
<?
print "
    
<ul class='nav-bar'>
<li><a href='settings.php'>$phrases[the_settings]</a></li>
<li><a href='geo_zones.php'>$phrases[geo_zones]</a></li>
<li>$data[name]</li>
    </ul>
    


<form action='geo_zones.php' method=post id='geo_edit_form'>
<input type='hidden' name='action' value='edit_ok'>
<input type='hidden' name='id' value='$id'>
    
<fieldset>
<input type='text' name='name' value=\"$data[name]\" size=30>
</fieldset>


<fieldset>
<legend>الدول</legend>

<table width='100%'><tr><td align=center width=45%>
الدول المتوفرة  
<br><br>
<select name='countries_all' id='countries_all' multiple size=\"20\" style=\"width:100%;\">";
$qrc = db_query("select * from store_countries order by name asc");
while($datac=db_fetch($qrc)){
    print "<option value='$datac[code]'>$datac[name]</option>";
}
print "</select>
  
</td><td align=center valign=center width=100>

<input type='button' value='$phrases[add_button]' id='add_btn'>
<br />
<input type='button' value='$phrases[delete]' id='del_btn'>
  
</td><td align=center width=45%>
الدول المختارة 
<br><br>
<select name='countries[]' id='countries' multiple size=\"20\" style=\"width:100%;\">";
$qrcs = db_query("select store_countries.name,store_geo_index.country_code as code from store_countries,store_geo_index where store_countries.code =  store_geo_index.country_code and store_geo_index.geo_id = '$id'");
while($datacs=db_fetch($qrcs)){
    print "<option value='$datacs[code]'>$datacs[name]</option>";
}
print "</select>
   
</td></tr>
</table>
</fieldset>

<fieldset>
<legend>$phrases[shipping_methods]</legend>";
   $datas = db_fetch_all("select * from store_shipping_methods where active=1 order by ord");
   $availabe_shipping = (array) explode(",", $data['shipping_methods']);
   foreach($datas as $datass){
       print "<input type='checkbox' name=\"shipping_methods[]\" id=\"$datass[id]\"".iif(in_array($datass['id'],$availabe_shipping) || $datass['all_geo_zones']," checked").iif($datass['all_geo_zones']," disabled").">$datass[name]</br>";   
       }
print "</fieldset>


<fieldset>
<legend>$phrases[payment_methods]</legend>";
   $datap = db_fetch_all("select * from store_payment_methods where active=1 order by ord");
   $available_payment = (array) explode(",", $data['payment_methods']);
   foreach($datap as $datapp){
       print "<input type='checkbox' name=\"payment_methods[]\" id=\"$datapp[id]\"".iif(in_array($datapp['id'],$available_payment) || $datapp['all_geo_zones']," checked").iif($datapp['all_geo_zones']," disabled").">$datapp[name]</br>";   
       }
print "</fieldset>
    

<fieldset style='text-align:center;'>
<input type='submit' value=\"$phrases[edit]\">
</fieldset>

</form>";


 }else{
     print_admin_table("<center> $phrases[err_wrong_url] </center>");
 }
 }
 
 //-----------end ----------------
 require(ADMIN_DIR.'/end.php');