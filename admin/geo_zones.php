<?
 if(!defined('IS_ADMIN')){die('No Access');}  
 
 if($action=="geo_zones"){
     if_admin();
     
     $qr = db_query("select * from store_geo order by id");
     if(db_num($qr)){
         print "<table width=100% class='grid'>";
         while($data=db_fetch($qr)){
              if($tr_class == "row_1"){$tr_class = "row_2";}else{$tr_class = "row_1";} 
              
             print "<tr class='$tr_class'><td><a href='index.php?action=geo_zones_edit&id=$data[id]'>$data[name]</a></td></tr>";
         }
         print "</table>";
     }else{
         print_admin_table("<center> No Geo Zones</center>");
     }
 }
 
 
 if($action=="geo_zones_edit" || $action=="geo_zone_country_add"){

 if_admin();
  $country_id = (int) $country_id;
  
 if($action=="geo_zone_country_add"){
     $count = db_fetch_first("select count(*) as count from store_geo_index where geo_id='$id' and country_id='$country_id'");
     if(!$count){
         db_query("insert into store_geo_index (geo_id,country_id) values ('$id','$country_id');");
     }
     
 }
 
 $qr = db_query("select * from store_geo where id='$id'");
 if(db_num($qr)){
     
print "
<form action='index.php' method=post>
<input type='hidden' name='action' value='geo_zone_country_add'>
<input type='hidden' name='id' value='$id'>
<fieldset>
<select name='country_id'>";
$qrc = db_query("select * from store_countries order by name asc");
while($datac=db_fetch($qrc)){
    print "<option value='$datac[id]'>$datac[name]</option>";
}
print "</select>
<input type='submit' value='$phrases[add_button]'>
</fieldset><br>
</form>";


$qr = db_query("select store_countries.name,store_geo_index.id from store_countries,store_geo_index where store_countries.id =  store_geo_index.country_id and store_geo_index.geo_id = '$id'");
if(db_num($qr)){
    print "<table width=100% class='grid'>";
    while($data=db_fetch($qr)){
          if($tr_class == "row_1"){$tr_class = "row_2";}else{$tr_class = "row_1";}
          
        print "<tr class='$tr_class'><td>$data[name]</td></tr>";
    }
    print "</table>";
}else{
    print_admin_table("<center> no geo records </center>");
}

 }else{
     print_admin_table("<center> $phrases[err_wrong_url] </center>");
 }
 }