<?
require('./start.php');

if(!$action || $action=="edit_ok"){
    
    

 $qr=db_query("select * from store_products_data where id='$id'"); 
 
 if(db_num($qr)){
     

  $data= db_fetch($qr);   
     if_products_cat_admin($data['cat']);
     
     
         
    print_admin_path_links($data['cat'],"<a href='products.php?action=product_edit&id=$id'>$data[name]</a> / $phrases[features]");
    
 //----------- edit ---------------
   if($action=="edit_ok"){
    
    db_query("delete from store_fields_data where  product_id='$id'");  
 for($i=0;$i<count($field_id);$i++){
 
 unset($cur_field_value);    
 if(is_array($field_value[$i])){$cur_field_value = serialize($field_value[$i]);}else{$cur_field_value = $field_value[$i] ;}
 
   db_query("insert into store_fields_data (cat,value,product_id) values('".$field_id[$i]."','".$cur_field_value."','$id')");

 }
   }
   
     
            //------- fields ------//
                      
                      $fields_array = get_product_cat_fields($data['cat'],true);
                      if(count($fields_array)){  
                      print " <center>
             <form action='products_fields.php' method=post>
             <input type='hidden' name='action' value='edit_ok'>
             <input type='hidden' name='id' value='$id'>
                
 <table border=0 width=\"90%\"  class=grid>";
                        
                        $qro = db_query("select * from store_fields_sets where id IN (".implode(",",$fields_array).") and active=1 order by ord");  
                       $i=0;
                       while($datao=db_fetch($qro)){
                print "<tr><td><b>".iif($datao['title'],$datao['title'],$datao['name'])."</b></td><td>
   <input type=hidden name=\"field_id[$i]\" value=\"$datao[id]\">";
   if($datao['type']=="text"){
      
       $option_value = db_qr_fetch("select value from store_fields_data where product_id='$id' and cat='$datao[id]'"); 
       print "<textarea cols=30 rows=5 name=\"field_value[$i]\">".iif($option_value['value'],$option_value['value'],$datao['value'])."</textarea>";
   }else{
   
   if($datao['type']=="select"){    
   print "<select name=\"field_value[$i]\">
   <option value=''>$phrases[not_selected]</option>";
   }
   
    $qr_options = db_query("select * from store_fields_options where field_id='$datao[id]' order by ord");
    while($data_options = db_fetch($qr_options)){
        $option_value = db_qr_fetch("select * from store_fields_data where product_id='$id' and cat='$datao[id]'");
        
          if($datao['type']=="select"){     
        print "<option value=\"$data_options[id]\"".iif($option_value['value']==$data_options['id']," selected").">$data_options[value]</option>";
          }else{
              $option_value_arr = unserialize($option_value['value']);
              if(!is_array($option_value_arr)){$option_value_arr=array();}
              
              print "<input type=checkbox name=\"field_value[$i][]\" value=\"$data_options[id]\"".iif(in_array($data_options['id'],$option_value_arr)," checked")."> $data_options[value] <br>";
          }
          }
   if($datao['type']=="select"){     
    print "</select>";
   }
   }
   print "</td></tr>"; 
      $i++;         
                       }
      print "</table><br>";
                     
   //---------------------// 
 
                print "<table border=0 width=\"90%\"  style=\"border-collapse: collapse\" class=grid>                              
      <tr><td  align=center>  <input type=\"submit\" value=\"$phrases[edit]\">  </td></tr>
</table>

</form>    </center>";
 }else{
     print_admin_table("<center> $phrases[no_features]</center>");
 }
  
 }else{
     print_admin_table("<center> $phrases[err_wrong_url] </center>");
 }
 
}

//-----------end ----------------
 require(ADMIN_DIR.'/end.php');
 ?>