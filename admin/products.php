<?
require('./start.php'); 

 //----------------- Products Move -------
if($action == "products_move"){ 

$cat = intval($cat);

if_products_cat_admin($cat,false);  

 if(is_array($id)){

 print "<form action='products.php' method=post name=sender>
 <input type=hidden name=action value='products_move_ok'>
 <input type=hidden name=from_cat value='$cat'>
 <center><table width=60% class=grid><tr><td colspan=2><b>$phrases[move_from] : </b>";

//-----------------------------------------
$data_from['cat'] = $cat ;
while($data_from['cat']>0){
   $data_from = db_qr_fetch("select name,id,cat from store_products_cats where id='$data_from[cat]'");

        $data_from_txt = "$data_from[name] / ". $data_from_txt  ;

        }
   print "$data_from_txt";
//------------------------------------------

 print "</td></tr>";
 $c = 1 ;
foreach($id as $idx){
$data=db_qr_fetch("select name from store_products_data where id='$idx'");
  print "<input type=hidden name=id[] value='$idx'>";
        print "<tr><td width=2><b>$c</b></td><td>$data[name]</td></tr>"  ;
        ++$c;
        }
 print "<tr><td colspan=2><b>$phrases[move_to] : </b><select name=cat>";
       $qr = db_query("select * from store_products_cats order by cat,ord,binary name asc");
   
    while($data=db_fetch($qr)){
        //-------------------------------
        $dir_content = "";
        $dir_data['cat'] = $data['cat'] ;
while($dir_data['cat']!=0){
   $dir_data = db_qr_fetch("select name,id,cat from store_products_cats where id=$dir_data[cat]");

        $dir_content = "$dir_data[name] -> ". $dir_content  ;
        }
      $data['full_name'] = $dir_content .$data['name'];      
     //---------------------------------------
        
       print "<option value='$data[id]'>$data[full_name]</option>";   
       
    }
   
    
  print "</select>
  </td></tr>
 <tr><td colspan=2 align=center><input type=submit value=' $phrases[move_the_products] '></td></tr>
 </table>";
 }else{
                print "<center>  $phrases[please_select_products_first] </center>";
                }
        }
  
  
   //----------------- Products Move -------
if($action == "products_cat_move"){ 

$cat = intval($cat);

if_products_cat_admin($cat,false);  

 if(is_array($id)){

 print "<form action='products.php' method=post name=sender>
 <input type=hidden name=action value='products_cat_move_ok'>
 <input type=hidden name=from_cat value='$cat'>
 <center><table width=60% class=grid><tr><td colspan=2><b> $phrases[move_from] : </b>";

//-----------------------------------------
$data_from['cat'] = $cat ;
while($data_from['cat']>0){
   $data_from = db_qr_fetch("select name,id,cat from store_products_cats where id='$data_from[cat]'");

  
        $data_from_txt = "$data_from[name] / ". $data_from_txt  ;
 
        }
   print "$data_from_txt";
//------------------------------------------

 print "</td></tr>";
 $c = 1 ;
foreach($id as $idx){
$data=db_qr_fetch("select name from store_products_cats where id='$idx'");
  print "<input type=hidden name=id[] value='$idx'>";
        print "<tr><td width=2><b>$c</b></td><td>$data[name]</td></tr>"  ;
        ++$c;
        $sql_ids[] = intval($idx);
        }
 print "<tr><td colspan=2><b>$phrases[move_to] : </b><select name=cat>
 <option value='0'>$phrases[without_main_cat]</option>";
       $qr = db_query("select * from store_products_cats where id !='$cat' and id not IN(".implode($sql_ids).") order by cat,ord,binary name asc");
   
    while($data=db_fetch($qr)){
        //-------------------------------
        $dir_content = "";
        $dir_data['cat'] = $data['cat'] ;
while($dir_data['cat']!=0){
   $dir_data = db_qr_fetch("select name,id,cat from store_products_cats where id=$dir_data[cat]");

        $dir_content = "$dir_data[name] -> ". $dir_content  ;
        }
      $data['full_name'] = $dir_content .$data['name'];      
     //---------------------------------------
        
       print "<option value='$data[id]'>$data[full_name]</option>";   
       
    }
   
    
  print "</select>
  </td></tr>
 <tr><td colspan=2 align=center><input type=submit value=' $phrases[move_the_cats] '></td></tr>
 </table>";
 }else{
                print "<center>  $phrases[please_select_cats_first] </center>";
                }
        }      
        
//---------------------------------- products Cats -----------------------------
if(!$action || $action=="products_cats" ||  $action=="products_cat_del" || $action=="products_cat_edit_ok" || 
$action=="products_cat_add_ok" || $action=="products" || $action=="product_add_ok" || $action=="product_edit_ok" || 
$action=="products_del" || $action=="products_cats_enable" || $action=="products_cats_disable" || $action=="products_move_ok" || $action=="products_cat_move_ok" ||
$action=="products_enable" || $action=="products_disable"){

 
    
 $cat = intval($cat);  
//if_products_cat_admin($cat);

print_admin_path_links($cat);
            

//------------------ move products------------------------
if($action=="products_move_ok"){
 if_products_cat_admin($cat,false);
$qr_to =  db_qr_num("select id from store_products_cats where id='$cat'");

 if($qr_to > 0){
     if(is_array($id)){
     
    
    foreach($id as $idx){
            db_query("update store_products_data set cat='$cat' where id='$idx'");
    }
     }else{
          print_admin_table("$phrases[err_products_not_selected]");   
     }
      
         }else{
       print_admin_table("$phrases[err_invalid_cat_id]");
        }
    }
    
    //------------------ move cats------------------------
if($action=="products_cat_move_ok"){
 if_products_cat_admin($cat,false);
 
$qr_to =  db_qr_num("select id from store_products_cats where id='$cat'");
if($cat==0){$qr_to=1;}

 if($qr_to > 0){
     if(is_array($id)){
     
      
    foreach($id as $idx){
            db_query("update store_products_cats set cat='$cat' where id='$idx'");
            $path = get_cat_path_str($idx);
            db_query("update store_products_cats set path='$path' where id='$idx'");  
    }  
           
     }else{
          print_admin_table("$phrases[err_cats_not_selected]");   
     }
      
         }else{
       print_admin_table("$phrases[err_invalid_cat_id]");
        }
    }
 //--------- enable / disable cat ------------
 if($action=="products_cats_disable"){
     if_products_cat_admin($id); 
 
        db_query("update store_products_cats set active=0 where id='$id'");
        }

if($action=="products_cats_enable"){
        if_products_cat_admin($id); 
       db_query("update store_products_cats set active=1 where id='$id'");
        }  
//--------------------Cat Add--------------------------------
if($action =="products_cat_add_ok"){
    if_products_cat_admin($cat,false);
    
    $fields = @implode(',',(array) $field_id);    
    $shipping_methods = @implode(',',(array) $shipping_id);  
      
     if(if_admin("",true)){
 $users_str = @implode(',',(array) $user_id);  
 }else{
     $users_str= "";
 }
 
 
  $max_ord = db_qr_fetch("select max(ord)+1 as ord from store_products_cats where cat='$cat' limit 1"); 
    
  db_query("insert into store_products_cats (name,cat,img,`fields`,shipping_methods,`active`,users,page_title,page_description,page_keywords,ord) values('".db_escape($name)."','$cat','".db_escape($img)."','".db_escape($fields)."','".db_escape($shipping_methods)."','1','".db_escape($users_str)."','".db_escape($page_title)."','".db_escape($page_description)."','".db_escape($page_keywords)."','$max_ord[ord]')");
 
  $new_id = db_inserted_id();
  $path = get_cat_path_str($new_id);  
  db_query("update store_products_cats set path='$path' where id='$new_id'");   
}
//-----------------Cat Del----------------------------------
 if($action=="products_cat_del"){
     $id=(array) $id ;
     
foreach($id as $idx){
    $idx = intval($idx);
if($idx > 0){
      if_products_cat_admin($idx,false);     
                  $delete_array = get_products_cats($idx);
  foreach($delete_array as $id_del){
    
     product_del($id_del,"cat");
       
     db_query("delete from store_products_cats where id='$id_del'");

     

     }

}
         }
 
 }
//--------------------Cat Edit--------------------------------
 if($action=="products_cat_edit_ok"){
 if_products_cat_admin($id);
 
 $fields = @implode(',',(array) $field_id);
 
 $shipping_methods = @implode(',',(array) $shipping_id); 
 
 
 if(if_admin("",true)){
 $users_str = @implode(',',(array) $user_id);
 $update_cat_users=true;  
 }else{
     $users_str= "";
     $update_cat_users=false; 
 }
 
 db_query("update store_products_cats set name='".db_escape($name)."',img='".db_escape($img)."',`fields`='".db_escape($fields)."',shipping_methods='".db_escape($shipping_methods)."'".iif($update_cat_users,",users='".db_escape($users_str)."'").",page_title='".db_escape($page_title)."',page_description='".db_escape($page_description)."',page_keywords='".db_escape($page_keywords)."' where id='$id'");
 
  }
  
   //--------- enable / disable product ------------
 if($action=="products_disable"){
     if_products_cat_admin($cat); 
 
        db_query("update store_products_data set active=0 where id='$id'");
        }

if($action=="products_enable"){
        if_products_cat_admin($cat); 
       db_query("update store_products_data set active=1 where id='$id'");
        } 
        
        
 //-------------------------product Add------------------------------
if($action=="product_add_ok"){
    
    if_products_cat_admin($cat);
    
if($auto_preview_text){
                $content = getPreviewText($details);
}
                
//----- filter XSS Tags -------
/*
$Filter = new InputFilter(array(),array(),1,1);
$details = $Filter->process($details);    */
//------------------------------



//-------- Save New Picture ---------- 
if($_FILES['imgfile']['name']){
if($settings['uploader']){
$imtype = strtolower(file_extension($_FILES['imgfile']['name']));

if(in_array($imtype,$upload_types)){

$upload_folder = $settings['uploader_path']."/products" ;    
$fl = new save_file($_FILES['imgfile']['tmp_name'],$upload_folder,$_FILES['imgfile']['name']);

if($fl->status){
$img_full =  $fl->saved_filename;

//----------- Thumb Create ----------
if($img_full){

$img=  create_thumb($img_full,$settings['products_img_width'],$settings['products_img_height'],$settings['products_img_fixed'],'details',true);

$thumb =  create_thumb($img_full,$settings['products_thumb_width'],$settings['products_thumb_height'],$settings['products_thumb_fixed'],'thumb',true);


}
//------------------------
   
}else{
print_admin_table("<center>".$fl->last_error_description."</center>");   
}

      
}else{
print_admin_table("<center>$phrases[this_filetype_not_allowed]</center>");
}
}else{
      print_admin_table("<center>  $settings[uploader_msg] </center> ","90%") ;  
}
}

//--------------End Save New Picture----------------


   

 db_query("insert into store_products_data (name,img_full,img,thumb,content,details,can_shipping,price,weight,cat,date,page_title,page_description,page_keywords,active,available) 
 values ('".db_escape($name)."','".db_escape($img_full,false)."','".db_escape($img,false)."','".db_escape($thumb,false)."','".db_escape($content,false)."','".db_escape($details,false)."','".intval($can_shipping)."','".db_escape($price)."','".db_escape($weight)."','$cat','".time()."','".db_escape($page_title)."','".db_escape($page_description)."','".db_escape($page_keywords)."','1','".intval($available)."')");

 $new_id = db_inserted_id();
 //------ fields ---------------//
 for($i=0;$i<count($field_id);$i++){
 
 unset($cur_field_value);    
 if(is_array($field_value[$i])){$cur_field_value = serialize($field_value[$i]);}else{$cur_field_value = $field_value[$i] ;}
   db_query("insert into store_fields_data (cat,value,product_id) values('".$field_id[$i]."','".$cur_field_value."','$new_id')");
 
 }
 //----------------------------//

}
//------------------------product Del------------------------------
if($action=="products_del"){
    
 if_products_cat_admin($cat);
 
$id = (array) $id;

foreach($id as $del_id){
  $del_id = (int) $del_id;
    product_del($del_id);    
}
 }
//-----------------------product Edit-------------------------
if($action=="product_edit_ok"){

 if_products_cat_admin($cat);
 
 if($auto_preview_text){
                $content = getPreviewText($details);
}
//----- filter XSS Tages -------
/*
$Filter = new InputFilter(array(),array(),1,1);
$details = $Filter->process($details);  */
//------------------------------


//-------- Save New Picture ----------
if($_FILES['imgfile']['name']){
 if($settings['uploader']){     
//----- delete old picture files first -------
$old_img = db_qr_fetch("select img,thumb,img_full from store_products_data where id='$id'");
delete_file($old_img['img']);
delete_file($old_img['img_full']);
delete_file($old_img['thumb']);
//--------------------------------------------


$imtype = strtolower(file_extension($_FILES['imgfile']['name']));

if(in_array($imtype,$upload_types)){

$upload_folder = $settings['uploader_path']."/products" ;    
$fl = new save_file($_FILES['imgfile']['tmp_name'],$upload_folder,$_FILES['imgfile']['name']);

if($fl->status){
$img_full =  $fl->saved_filename;

//----------- Thumb Create ----------
if($img_full){

$img=  create_thumb($img_full,$settings['products_img_width'],$settings['products_img_height'],$settings['products_img_fixed'],'details',true);

$thumb =  create_thumb($img_full,$settings['products_thumb_width'],$settings['products_thumb_height'],$settings['products_thumb_fixed'],'thumb',true);


}
//------------------------
   
}else{
print_admin_table("<center>".$fl->last_error_description."</center>");   
}
      
}else{
print_admin_table("<center>$phrases[this_filetype_not_allowed]</center>");
}
 }else{
     print_admin_table("<center>  $settings[uploader_msg] </center> ","90%") ;  
 }
}
//--------------End Save New Picture----------------

 db_query("update store_products_data set name='".db_escape($name)."'"
 .iif($img_full,",img_full='".db_escape($img_full,false)."',img='".db_escape($img,false)."',thumb='".db_escape($thumb,false)."'").",
 content='".db_escape($content,false)."',details='".db_escape($details,false)."',
 can_shipping='".intval($can_shipping)."',available='".intval($available)."',
 price='".db_escape($price)."',weight='".db_escape($weight)."',page_title='".db_escape($page_title)."',
 page_description='".db_escape($page_description)."',
 page_keywords='".db_escape($page_keywords)."' 
 where id='$id'");

 
  }          
//-----------------------------------------------------------


//-------- List Cats ---------//
 print "<p align=$global_align><a href='products.php?action=products_cat_add&cat=$cat'><img src='images/add.gif' border=0> $phrases[add_cat]</a></p>";   



       $qr = db_query("select * from store_products_cats where cat='$cat' order by ord asc");
     
      

 if(db_num($qr)){
 print "<center>
 <p class=title>$phrases[the_cats]</p>
 <form action='products.php' name=cats_form method=post>
 <input type=hidden name=cat value='$cat'>
 <input type=hidden name='start' value='$start'>
     
 
<table width=100% class=grid><tr><td>
<div id=\"products_cats_list\" >";
 while($data = db_fetch($qr)){
     toggle_tr_class();
     
      print "<div id=\"item_$data[id]\" class='$tr_class'>
      <table width=100%><tr>
      <td width=2>
      <input type=checkbox name=id[] value='$data[id]'>
      </td>
      <td class=\"handle\"></td>
      
      <td>
      
      <a href='products.php?cat=$data[id]'>$data[name]</a></td>
      <td width=200>";
      if($data['active']){
                        print "<a href='products.php?action=products_cats_disable&id=$data[id]&cat=$cat'>$phrases[disable]</a> - " ;
                        }else{
                        print "<a href='products.php?action=products_cats_enable&id=$data[id]&cat=$cat'>$phrases[enable]</a> - " ;
                        }
      print "<a href='products.php?action=products_cat_edit&id=$data[id]&cat=$cat'>$phrases[edit] </a> - <a href=\"products.php?action=products_cat_del&id=$data[id]&cat=$cat&start=$start\" onClick=\"return confirm('$phrases[del_product_cat_warning]');\">$phrases[delete]</a></td>
      </tr></table></div>";
         }
       print "</div>
       
       
       <table width=100%><tr>
          <td width=2><img src='images/arrow_".$global_dir.".gif'></td>   
          <td>

          <a href='#' onclick=\"CheckAll('cats_form'); return false;\"> $phrases[select_all] </a> -
          <a href='#' onclick=\"UncheckAll('cats_form'); return false;\">$phrases[select_none] </a> 
          &nbsp;&nbsp; 
          <select name=action>
         
          <option value='products_cat_move'>$phrases[move]</option>
           <option value='products_cat_del'>$phrases[delete]</option>  
          </select>
           &nbsp;&nbsp;
           <input type=submit value=' $phrases[do_button] ' onClick=\"return confirm('".$phrases['are_you_sure']."');\">
          </td></tr></table>
          
          </td></tr>
          
          </table>
          
          
      
       
        
          </center><br>
          </form>
       
       <script type=\"text/javascript\">
        init_sortlist('products_cats_list','products_cats');
</script>";
       
       
 }else{
     $no_cats = true;
 }
//------------------------//


 if($cat > 0){

 print "<p class=title align=center>$phrases[the_products]</p>      
<p><a href='products.php?action=product_add&cat=$cat' class='add'>$phrases[add_product]</a></p>";

    }

    //------------ show products ------------------//
    $start = (int) $start;
  $perpage = $settings['admin_products_perpage'];
    $page_string = "products.php?cat=$cat&start={start}";
      $qr=db_query("select * from store_products_data where cat='$cat' order by id desc limit $start,$perpage");
     
      if(db_num($qr)){
          
        $items_count = valueof(db_qr_fetch("select count(*) as count from store_products_data where cat='$cat'"),'count');
        
            print "<center>
            <form action='products.php' method=post name='products_form'>
            <input type=hidden name=cat value='$cat'>
            <table class=grid width=90%><tr><td>
            <table width=100%>" ; 
           while($data = db_fetch($qr)){
               

toggle_tr_class();

                print "<tr class='$tr_class'>
                <td width=2><input type=checkbox name='id[]' value='$data[id]'></td>
                <td width=35><img src=\"".iif(strchr($data['thumb'],"http://"),$data['thumb'],get_image($data['thumb'],'','../'))."\" class='products_list_img'></td>
                <td><a href='products.php?action=product_edit&id=$data[id]&start=$start'>$data[name]</a></td>
                <td align=$global_align_x>";
                 if($data['active']){
                        print "<a href='products.php?action=products_disable&id=$data[id]&cat=$cat'>$phrases[disable]</a> - " ;
                        }else{
                        print "<a href='products.php?action=products_enable&id=$data[id]&cat=$cat'>$phrases[enable]</a> - " ;
                        }
                        print "<a href='products.php?action=product_edit&id=$data[id]&start=$start'>$phrases[edit] </a> - 
                <a href='products.php?action=products_del&id=$data[id]&cat=$cat' onClick=\"return confirm('$phrases[are_you_sure]');\"> $phrases[delete] </a></td></tr>";

                   }
            print "</table>
          <table width=100%><tr>
          <td width=2><img src='images/arrow_".$global_dir.".gif'></td>   
          <td>

          <a href='#' onclick=\"CheckAll('products_form'); return false;\"> $phrases[select_all] </a> -
          <a href='#' onclick=\"UncheckAll('products_form'); return false;\">$phrases[select_none] </a> 
          &nbsp;&nbsp; 
          <select name=action>
          <option value='hot_items_add'>$phrases[add_to_hot_items]</option>
          
          <option value='products_move'>$phrases[move]</option>
          <option value='products_del'>$phrases[delete]</option>  
          </select>
           &nbsp;&nbsp;
           <input type=submit value=' $phrases[do_button] ' onClick=\"return confirm('".$phrases['are_you_sure']."');\">
          </td></tr></table>
          
          </td></tr>
          
          </table>";  
            
            print_pages_links($start, $items_count, $perpage, $page_string);
              }else{
                if($cat > 0 || $no_cats){
                      print_admin_table("<center>$phrases[no_products]</center>");
                }
                      }

        }
//-------------- Cat Add -----------
if($action=="products_cat_add"){
    $cat = intval($cat);
  
  if_products_cat_admin($cat,false); 
  print_admin_path_links($cat);  
  
  
print "<center><p class=title>$phrases[add_cat] </p>
   <form method=\"POST\" action=\"products.php\" name='sender'>
   <input type=hidden name='action' value='products_cat_add_ok'>
      <input type=hidden name='cat' value='$cat'>
      
      
   <table width=100% class=grid><tr>
   <td> <b>$phrases[the_name] </b></td><td>
    
   <input type=text name=name size=30>
    </td></tr>
       <tr><td>
  <b>$phrases[the_image]</b></td>
  <td> <table><tr><td><input type=text  dir=ltr size=30 name=img></td><td><a href=\"javascript:uploader('products','img');\"><img src='images/file_up.gif' border=0 alt='$phrases[upload_file]'></a></td></tr></table>

   </td></tr>
   </table> <br>";
   //------------ Fields ----------//
    print "<table width=100% class=grid>
    <tr><td><b>$phrases[products_fields]</b></td>
                       <td>
                       <table width=100%><tr>";
                       $fields_array = get_product_cat_fields($cat);
                     
                    
                       $qro=db_query("select * from store_fields_sets order by ord");
                       $c=0;
                       while($datao=db_fetch($qro)){
   if($c==4){
    print "</tr><tr>" ;
    $c=0;
    }
    
                           print "<td><input type=\"checkbox\" name=\"field_id[]\" value=\"$datao[id]\"".iif($fields_array[$datao['id']],' checked').iif($fields_array[$datao['id']] && $fields_array[$datao['id']] !=$id,' disabled').">$datao[name]</td>";
                           $c++;
                       }
                       print "</table></td></tr>
                       
                                       </table><br>";
                                       
    //-------------------- Shipping Methods --------------//
    print "<table border=0 width=\"100%\"  class=grid>
    
                       <tr><td><b>$phrases[shipping_methods]</b></td>
                       <td>
                       <table width=100%><tr>";
                       $shipping_array = get_product_cat_shipping_methods($cat);
                     
                    
                       $qro=db_query("select * from store_shipping_methods order by ord");
                       $c=0;
                       while($datao=db_fetch($qro)){
   if($c==4){
    print "</tr><tr>" ;
    $c=0;
    }
    
                           print "<td><input type=\"checkbox\" name=\"shipping_id[]\" value=\"$datao[id]\"".iif($shipping_array[$datao['id']],' checked').iif($shipping_array[$datao['id']] && $shipping_array[$datao['id']] !=$id,' disabled').">$datao[name]</td>";
                           $c++;
                       }
                       print "</table></td></tr>
                       </table><br>";                                        
 //------------------ Modetators ----------------//                      
                       if(if_admin("",true)){
                       
                       print "
                        <table border=0 width=\"100%\"   class=grid>
                        <tr><td><b>$phrases[the_moderators]</b></td>
                       <td>";
                       
                     
                       $users_array = get_product_cat_users($cat);
                           // print_r($users_array);
                    
                       $qro=db_query("select * from store_user where group_id=2 order by id");
                       if(db_num($qro)){
                       print "<table width=100%><tr>";     
                       $c=0;
                       while($datao=db_fetch($qro)){
   if($c==4){
    print "</tr><tr>" ;
    $c=0;
    }
    
                           print "<td><input type=\"checkbox\" name=\"user_id[]\" value=\"$datao[id]\"".iif($users_array[$datao['id']],' checked').iif($users_array[$datao['id']] && $users_array[$datao['id']] !=$id,' disabled').">$datao[username]</td>";
                           $c++;
                       }
                       print "</tr></table>";
                       }else{
                           print "$phrases[no_moderators]";
                       }
                       print "</td></tr>
                       </table><br>";
                       }
  //-------------- Tags --------------//                     
                              print "
                              <fieldset>
                              <legend><b>$phrases[page_custom_info]</b></legend>
                              <table width=100%>
                              
                              <tr><td><b>$phrases[the_title] : </td><td>
                              <input type=text size=30 name=page_title value=\"$data[page_title]\"></td></tr>
                              
                              <tr><td><b>$phrases[the_description] : </td><td>
                              <input type=text size=30 name=page_description value=\"$data[page_description]\"></td></tr>
                              
                              <tr><td><b>$phrases[the_page_keywords] : </td><td>
                              <input type=text size=30 name=page_keywords value=\"$data[page_keywords]\"></td></tr>
                              
                              </table>
                              </fieldset><br><br>
                               <table border=0 width=\"100%\"   class=grid>
                              
   <tr>
    <td align=center><input type=submit value='$phrases[add_button]'></td>
    </tr></table>

    </form>

   </center>";
   
   }
 //------------------------- Cat Edit------------------------
        if($action == "products_cat_edit"){
   if_products_cat_admin($id);
   print_admin_path_links($id);  
   
        $qr =db_query("select * from store_products_cats where id='$id'");
        if(db_num($qr)){
          if_products_cat_admin($id);
           
           $data=db_fetch($qr); 
               print "<center>

                <table border=0 width=\"100%\"   class=grid><tr>

                <form method=\"POST\" action=\"products.php\" name='sender'>

                      <input type=hidden name=\"id\" value='$id'>
                      <input type=hidden name=\"cat\" value='$cat'>

                      <input type=hidden name=\"action\" value='products_cat_edit_ok'> ";


                  print "  <tr>
                                <td width=\"50\">
                <b>$phrases[the_name]</b></td><td width=\"223\">
                <input type=\"text\" name=\"name\" value=\"$data[name]\" size=\"29\"></td>
                        </tr>
                  


                             <tr><td>
  <b>$phrases[the_image]</b></td>
  <td> <table><tr><td><input type=text  dir=ltr size=30 name=img value='$data[img]'></td><td><a href=\"javascript:uploader('products','img');\"><img src='images/file_up.gif' border=0 alt='$phrases[upload_file]'></a></td></tr></table>

   </td></tr>
   </table>
   <br>";
   //-------------------- Fields --------------//
    print "<table border=0 width=\"100%\"   class=grid>
    
                       <tr><td><b>$phrases[products_fields]</b></td>
                       <td>
                       <table width=100%><tr>";
                       $fields_array = get_product_cat_fields($id);
                    ///          print_r($fields_array) ;
                    
                       $qro=db_query("select * from store_fields_sets order by ord");
                       $c=0;
                       while($datao=db_fetch($qro)){
   if($c==4){
    print "</tr><tr>" ;
    $c=0;
    }
    
                           print "<td><input type=\"checkbox\" name=\"field_id[]\" value=\"$datao[id]\"".iif($fields_array[$datao['id']],' checked').iif($fields_array[$datao['id']] && $fields_array[$datao['id']] !=$id,' disabled').">$datao[name]</td>";
                           $c++;
                       }
                       print "</table></td></tr>
                       </table><br>";
   //-------------------- Shipping Methods --------------//
    print "<table border=0 width=\"100%\"   class=grid>
    
                       <tr><td><b>$phrases[shipping_methods]</b></td>
                       <td>
                       <table width=100%><tr>";
                       $shipping_array = get_product_cat_shipping_methods($id);
                     
                    
                       $qro=db_query("select * from store_shipping_methods order by ord");
                       $c=0;
                       while($datao=db_fetch($qro)){
   if($c==4){
    print "</tr><tr>" ;
    $c=0;
    }
    
                           print "<td><input type=\"checkbox\" name=\"shipping_id[]\" value=\"$datao[id]\"".iif($shipping_array[$datao['id']],' checked').iif($shipping_array[$datao['id']] && $shipping_array[$datao['id']] !=$id,' disabled').">$datao[name]</td>";
                           $c++;
                       }
                       print "</table></td></tr>
                       </table><br>";                     
 //-------------- Moderators --------------//                      
                       if(if_admin("",true)){
                       
                       print "
                        <table border=0 width=\"100%\"   class=grid>
                        <tr><td><b>$phrases[the_moderators]</b></td>
                       <td>";
                       $users_array = get_product_cat_users($id);
                           // print_r($users_array);
                    
                       $qro=db_query("select * from store_user where group_id=2 order by id");
                        if(db_num($qro)){
                       print "<table width=100%><tr>";
                       $c=0;
                       while($datao=db_fetch($qro)){
   if($c==4){
    print "</tr><tr>" ;
    $c=0;
    }
    
                           print "<td><input type=\"checkbox\" name=\"user_id[]\" value=\"$datao[id]\"".iif($users_array[$datao['id']],' checked').iif($users_array[$datao['id']] && $users_array[$datao['id']] !=$id,' disabled').">$datao[username]</td>";
                           $c++;
                       }
                       print "</tr></table>";
                        }else{
                              print " $phrases[no_moderators]";
                        }
                       print "</td></tr>
                       </table><br>";
                       }
    //-------------- Tags ------------//                 
                              print "
                              <fieldset>
                              <legend><b>$phrases[page_custom_info]</b></legend>
                              <table width=100%>
                              
                              <tr><td><b>$phrases[the_title] : </td><td>
                              <input type=text size=30 name=page_title value=\"$data[page_title]\"></td></tr>
                              
                              <tr><td><b>$phrases[the_description] : </td><td>
                              <input type=text size=30 name=page_description value=\"$data[page_description]\"></td></tr>
                              
                              <tr><td><b>$phrases[the_page_keywords] : </td><td>
                              <input type=text size=30 name=page_keywords value=\"$data[page_keywords]\"></td></tr>
                              
                              </table>
                              </fieldset><br><br>
                               <table border=0 width=\"100%\"   class=grid>
                               <tr>
                                <td>
                <center><input type=\"submit\" value=\"$phrases[edit]\">
                        </td>
                        </tr>
                        </table>

                  

</form>    </center>\n";
}else{
     print_admin_table("<center> $phrases[err_wrong_url]</center>");
     
 }
                      }

//------------------------ product Edit --------------------------------------
if($action == "product_edit"){

  $start = (int) $start;
  
 $qr=db_query("select * from store_products_data where id='$id'"); 
 
 if(db_num($qr)){
     $data= db_fetch($qr);
     if_products_cat_admin($data['cat']);
     
     
         
    print_admin_path_links($data['cat'],$data['name']);
   
   
 
  
   
   print " <center>
   
   <table class=grid><tr>
   <td align=center><a href='products_fields.php?id=$id'>Fields</a></td>
   <td align=center><a href='products_options.php?id=$id'>Order Options</a></td>
   <td align=center><a href='products_photos.php?id=$id'> $phrases[manage_product_photos] </a></td>
   </tr></table><br>
   
   
   <form name=sender action='products.php' method=post enctype=\"multipart/form-data\">
       <input type=hidden name='action' value='product_edit_ok'>
       <input type=hidden name='cat' value='$data[cat]'>
       <input type=hidden name='id' value='$id'>
       <input type=hidden name='start' value='$start'>
    
                <table border=0 width=\"100%\" class=grid><tr>

             
                        <tr>
                        
       <td colspan=2 rowspan=6 align=center><img src=\"".get_image($data['img'],'','../')."\"></td>
       
       
                 <td width=\"100\">
                <b>$phrases[the_name]</b></td><td >
                <input type=\"text\" name=\"name\" size=\"50\" value=\"$data[name]\"></td>
                        </tr>
                  

                              <tr> <td width=\"100\">
                <b>$phrases[the_image]</b></td>
                                <td>
                                <input type=file name='imgfile'> &nbsp;* $phrases[leave_blank_for_no_change]
                                 </td></tr>
                                 
                           <tr><td width=\"100\">
                <b>$phrases[the_price]</b></td><td >
                <input type=\"text\" name=\"price\" size=\"5\" value='$data[price]'> $settings[currency]</td>
                        </tr>
                 
                   <tr><td width=\"100\">                                 
                <b>$phrases[the_weight]</b></td><td >
                <input type=\"text\" name=\"weight\" size=\"5\" value='".iif(strchr($data['weight'],"."),$data['weight'],number_format($data['weight'],2,".",","))."'> $phrases[kg]</td>
                        </tr>
                               
                               
                        <tr><td><b>$phrases[available]</b></td><td>";
                print_select_row("available",array("1"=>$phrases['yes'],"0"=>"$phrases[not_available_now]"),$data['available'],''); 
                print "</td></tr>
                                 
                          <tr><td><b>$phrases[can_shipping]</b></td><td>";
                print_select_row("can_shipping",array($phrases['no'],$phrases['yes']),$data['can_shipping'],''); 
                print "</td></tr>
                </table><br>
               <table border=0 width=\"100%\" class=grid><tr>  


                                    <tr> <td width=\"50\">
                <b>$phrases[the_details]</b></td>
                                <td>";
                                 editor_print_form("details",600,300,"$data[details]");

                                print "
                                <tr><td colspan=2><input name=\"auto_preview_text\" type=\"checkbox\" value=1 onClick=\"show_hide_preview_text(this);\"> $phrases[auto_short_content_create]
                                </td></tr>
                      <tr id=preview_text_tr> <td width=\"100\">
                <b>$phrases[news_short_content]</b></td>
                            <td >
                                <textarea cols=50 rows=5 name='content'>$data[content]</textarea>
                                </td></tr>


                        </td>
                        </tr>
                        
                 </table><br>";       
          
         
         print " 
                              <fieldset>
                              <legend><b>$phrases[page_custom_info]</b></legend>
                              <table width=100%>
                              
                              <tr><td><b>$phrases[the_title] : </td><td>
                              <input type=text size=30 name=page_title value=\"$data[page_title]\"></td></tr>
                              
                              <tr><td><b>$phrases[the_description] : </td><td>
                              <input type=text size=30 name=page_description value=\"$data[page_description]\"></td></tr>
                              
                              <tr><td><b>$phrases[the_page_keywords] : </td><td>
                              <input type=text size=30 name=page_keywords value=\"$data[page_keywords]\"></td></tr>
                              
                              </table>
                              </fieldset><br><br> ";
            //------------ Photos -----------//
           /* $count_photos = db_qr_fetch("select count(id) as count from store_products_photos where product_id='$id'");
            
            print "<fieldset>
                              <legend><b>$phrases[product_photos]</b></legend>
                              <center><b> $phrases[photos_count] : </b> ".intval($count_photos['count'])." [<a href='index.php?action=products_photos&id=$id'> $phrases[manage_product_photos] </a>]</center>
                              <br>
                              </fieldset><br><br>";*/
                                              
            //-------------------------------------                  
               print "<table border=0 width=\"100%\"   class=grid>                              
      <tr><td  align=center>  <input type=\"submit\" value=\"$phrases[edit]\">  </td></tr>
</table>

</form>    </center>";


 }else{
     print_admin_table("<center> $phrases[err_wrong_url]</center>");
     
 }
        }
        
 //----------- Product Add ---------
if($action=="product_add"){
    $cat = intval($cat);
    if_products_cat_admin($cat,false);
    print_admin_path_links($cat);
    
   print "<center>
               
                   
                <form name=sender method=\"POST\" action=\"products.php\" enctype=\"multipart/form-data\">

                      <input type=hidden name=\"action\" value='product_add_ok'>
                      <input type=hidden name=\"cat\" value=\"$cat\">



                                       <table border=0 width=\"100%\"   class=grid><tr>

              


                        <tr>
                                <td width=\"100\">
                <b>$phrases[the_name]</b></td><td >
                <input type=\"text\" name=\"name\" size=\"50\" value=\"$data[name]\"></td>
                        </tr>
                  

                              <tr> <td width=\"100\">
                <b>$phrases[the_image]</b></td>
                                <td>
                                <input type=file name='imgfile'> 
                                 </td></tr>
                  <tr>
                                <td width=\"100\">
                <b>$phrases[the_price]</b></td><td >
                <input type=\"text\" name=\"price\" size=\"4\" value='$data[price]'> $settings[currency]</td>
                        </tr> 
                          <tr><td width=\"100\">                                 
                <b>$phrases[the_weight]</b></td><td >
                <input type=\"text\" name=\"weight\" size=\"5\" value='0.00'> $phrases[kg]</td>
                        </tr>
                        
                          <tr><td><b>$phrases[available]</b></td><td>";
                print_select_row("available",array("1"=>$phrases['yes'],"0"=>"$phrases[not_available_now]"),1); 
                print "</td></tr>
                
                <tr><td><b>$phrases[can_shipping]</b></td><td>";
                print_select_row("can_shipping",array($phrases['no'],$phrases['yes']),$data['can_shipping'],''); 
                print "</td></tr>               
                  </table><br>
                  <table border=0 width=\"100%\"   class=grid>  
                                    <tr> <td width=\"50\">
                <b>$phrases[the_details]</b></td>
                                <td>";
                                 editor_print_form("details",600,300,"$data[details]");

                                print "
                                <tr><td colspan=2><input name=\"auto_preview_text\" type=\"checkbox\" value=1 onClick=\"show_hide_preview_text(this);\"> $phrases[auto_short_content_create]
                                </td></tr>
                      <tr id=preview_text_tr> <td width=\"100\">
                <b>$phrases[news_short_content]</b></td>
                            <td >
                                <textarea cols=50 rows=5 name='content'>$data[content]</textarea>
                                </td></tr>


                        </td>
                        </tr>
                        
                         
                </table><br>";       
                      //------- fields ------//
                      $fields_array = get_product_cat_fields($cat,true);
                      if(count($fields_array)){ 
                      print "  <table border=0 width=\"100%\"   class=grid>  ";
                         
                        $qro = db_query("select * from store_fields_sets where id IN (".implode(",",$fields_array).") and active=1 order by ord");  
                       $i=0;
                       while($datao=db_fetch($qro)){
                print "<tr><td><b>".iif($datao['title'],$datao['title'],$datao['name'])."</b></td><td>
   <input type=hidden name=\"field_id[$i]\" value=\"$datao[id]\">";
   if($datao['type']=="text"){
      
   
       print "<textarea cols=30 rows=5 name=\"field_value[$i]\">".$datao['value']."</textarea>";
   }else{
   
   if($datao['type']=="select"){    
   print "<select name=\"field_value[$i]\">
   <option value=''>$phrases[not_selected]</option>";
   }
   
    $qr_options = db_query("select * from store_fields_options where field_id='$datao[id]' order by ord");
    while($data_options = db_fetch($qr_options)){
        
        
          if($datao['type']=="select"){     
        print "<option value=\"$data_options[id]\">$data_options[value]</option>";
          }else{
              
              print "<input type=checkbox name=\"field_value[$i][]\" value=\"$data_options[id]\"> $data_options[value] <br>";
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
                      }
                      //---------------------// 
                print " 
                              <fieldset>
                              <legend><b>$phrases[page_custom_info]</b></legend>
                              <table width=100%>
                              
                              <tr><td><b>$phrases[the_title] : </td><td>
                              <input type=text size=30 name=page_title value=\"$data[page_title]\"></td></tr>
                              
                              <tr><td><b>$phrases[the_description] : </td><td>
                              <input type=text size=30 name=page_description value=\"$data[page_description]\"></td></tr>
                              
                              <tr><td><b>$phrases[the_page_keywords] : </td><td>
                              <input type=text size=30 name=page_keywords value=\"$data[page_keywords]\"></td></tr>
                              
                              </table>
                              </fieldset><br><br>
                 <fieldset>
                              <legend><b>$phrases[product_photos]</b></legend>
                              <center>$phrases[product_add_photos_note]</center>
                              <br>
                              </fieldset><br><br>
                                     
               <table border=0 width=\"100%\"   class=grid>           
             <tr><td align=center>  <input type=\"submit\" value=\"$phrases[add_button]\">  </td></tr>




                </table>

</form>    </center>\n";

}
//-----------end ----------------
 require(ADMIN_DIR.'/end.php');