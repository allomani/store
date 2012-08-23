<?
if(THIS_PAGE != "index"){die();}

 if($action=="product_details"){
 $id=intval($id);
 
 $qr = db_query("select * from store_products_data where id='$id'");
 
 if(db_num($qr)){
 $data = db_fetch($qr);
 
 compile_hook('product_details_start');
 
 print_path_links($data['cat'],$data['name']);
 
 compile_hook('product_details_after_path_links'); 
 
 open_table($data['name']);
 
 
 //------ fields details -------
  
  $qrf = db_query("select * from store_fields_data where product_id='$data[id]'");
  if(db_num($qrf)){
  
   //--- caching data --//
  while($pre_dataf_x = db_fetch($qrf)){
  $pre_dataf[$pre_dataf_x['cat']] =  $pre_dataf_x ;
  $sets_ids[] = $pre_dataf_x['cat'];   
  }
   unset($qrf,$pre_dataf_x);
  //--- caching sets ---//
  $fs_qr = db_query("select id,name,title,type,img from store_fields_sets where id IN (".implode(",",$sets_ids).") and in_details=1 and active=1 order by ord"); 
  while($fs_data = db_fetch($fs_qr)){
  $sets_array[]  =  $fs_data;  
  }
  unset($fs_data,$fs_qr,$sets_ids);
  //--------------------//
         
  if(count($sets_array)){
   $fields_content = "<br><table>";           
  foreach($sets_array as $field_name){
    
 
  if(isset($pre_dataf[$field_name['id']])){    
  
   $dataf = $pre_dataf[$field_name['id']];
  
  $fields_content .= "<tr><td>".iif($field_name['img'],"<img src=\"$field_name[img]\">&nbsp;")."<b>".iif($field_name['title'],$field_name['title'],$field_name['name'])."</b></td><td>";
  if($field_name['type']=="text"){
  $fields_content .= "$dataf[value]";    
  }elseif($field_name['type']=="select"){
       $option_name = db_qr_fetch("select value from store_fields_options where id='$dataf[value]'"); 
       $fields_content .= iif($option_name['value'],$option_name['value'],"$phrases[not_selected]");
  }elseif($field_name['type']=="checkbox"){      
   $values_arr = unserialize($dataf['value']);
  if(!is_array($values_arr)){$values_arr=array();} 
      
      
   $qr_options = db_query("select id,value,img from store_fields_options where field_id='$dataf[cat]' order by ord"); 
   if(db_num($qr_options)){  
   $fields_content .= "<table>"; 
   while($data_options = db_fetch($qr_options)){
       $fields_content .= "<tr><td>".iif($data_options['img'],"<img src=\"$data_options[img]\">&nbsp;")."$data_options[value] </td><td><img src=\"$style[images]/".iif(in_array($data_options['id'],$values_arr),"true.gif","false.gif")."\" border=0></td></tr>";
   } 
   $fields_content .= "</table>";
   }   
  }
  $fields_content .= "</td></tr>
  <tr><td colspan=2><hr class='separate_line' size=1></td></tr>";
  }
  
  }
  
  $fields_content .= "</table><br><br>";
  }
  }else{
      $fields_content  = "";
  }

 //------------------------------

 
compile_template(get_template('product_details'));
 
 close_table();
 
 //-------- photos -------//
 $qrp=db_query("select * from store_products_photos where product_id='$id' order by id");
 if(db_num($qrp)){
     compile_hook('product_details_before_photos_table');
     
     open_table("$phrases[product_photos]");
     print "<table width=100%><tr>";
     
     $c=0;
     
     while($datap=db_fetch($qrp)){
    
    if($c==$settings['img_cells']){
        print "</tr><tr>";
        $c=0;
    }
         
    compile_template(get_template('product_details_photos'));
     
     $c++;   
     }
     print "</tr></table>";
     
     close_table();
     compile_hook('product_details_after_photos_table');
 }
 //----------------
 
  compile_hook('product_details_end');
  
 }else{
 open_table();
 print "<center>$phrases[err_wrong_url]</center>";
 close_table();    
 }
 }