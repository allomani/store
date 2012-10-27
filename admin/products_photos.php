<?
require('./start.php');

//------------ Products Photos ---------//
 if(!$action || $action=="products_photos" || $action=="add_ok" || $action=="edit_ok" || $action=="del"){
 
     
        $qr=db_query("select id,cat,name from store_products_data where id='$id'");
    
    if(db_num($qr)){
    
        $data=db_fetch($qr);
        
    if_products_cat_admin($data['cat']);
    print_admin_path_links($data['cat'],"<a href='products.php?action=product_edit&id=$data[id]&cat=$data[cat]'>$data[name]</a> / $phrases[product_photos]");
    
 //----------- edit ---------//
 if($action=="edit_ok"){
      $pic_id = (int) $pic_id;
   
     db_query("update store_products_photos set name='".db_escape($name)."' where id='$pic_id'");
    
 }
 //----------- del ----------//
 if($action=="del"){
     $pic_id = (array) $pic_id;
   foreach($pic_id as $iid){ 
   $iid = (int) $iid;
    
   $data_pic  = db_qr_fetch("select img,thumb from store_products_photos where id='$iid'");
   delete_file($data_pic['img']);
   delete_file($data_pic['thumb']);
   db_query("delete from store_products_photos where id='$iid'"); 
   }
 }
  //------------ photos add -----------//
  if($action=="add_ok"){  
 if($settings['uploader']){   
    require_once(CWD. "/includes/class_save_file.php");                          
   $upload_folder = "$settings[uploader_path]/products_photos";
   $upload_types = array("gif","jpg","png","bmp");
   


                        
 for($i=0;$i<count($_FILES['img']);$i++){
 
 
         if($_FILES['img']['name'][$i]){
     $imtype = strtolower(file_extension($_FILES['img']['name'][$i]));

if(in_array($imtype,$upload_types)){

    
$fl = new save_file($_FILES['img']['tmp_name'][$i],$upload_folder,$_FILES['img']['name'][$i]);

if($fl->status){
$img_saved =  $fl->saved_filename;

//------- thumb --------
$thumb_saved =  create_thumb($img_saved,$settings['products_photos_thumb_width'],$settings['products_photos_thumb_height'],$settings['products_photos_thumb_fixed'],'thumb');
   
  db_query("insert into store_products_photos (name,img,thumb,product_id) values 
             ('".db_escape($name[$i])."','".db_escape($img_saved,false)."','".db_escape($thumb_saved,false)."','$id')");
             
             
              
}else{
print_admin_table("<center>".$fl->last_error_description."</center>");  
}



}else{
print_admin_table("<center>$phrases[this_filetype_not_allowed]</center>");
}
  
           
                 
         }  
          
      }
 }else{
       print_admin_table("<center>  $settings[uploader_msg] </center> ","90%") ;  
 }    
  }
  //------------------------------------
  
     
    
          print "<p align=$global_align><a href='products_photos.php?action=add&cat=$id' class='add'>$phrases[add_photos] </a></p>
                              
                              ";
                       $qr=db_query("select * from store_products_photos where product_id='$id' order by ord asc");
                       if(db_num($qr)){
                     //      print "<center><table width=90% class=grid><tr>";
    $photos_main_div_width = ((($settings['thumb_width']+60)*4)+60);
                     print " <center>
        <form action='products_photos.php' method='post' name='submit_form'>
        <input type=hidden name='id' value='$id'>    
        
         <div id=\"product_photos_list\" style=\"width:100%;\" >";
                       
                       while($data=db_fetch($qr)){
                       
                        print "<div id=\"item_$data[id]\" style=\"float: $global_align;padding:10px;border: #CCC 1px dashed; margin:10px;\" onmouseover=\"this.style.backgroundColor='#EFEFEE'\" onmouseout=\"this.style.backgroundColor='#FFFFFF'\">
  
    <div style=\"cursor: move;text-align:right;\" class=\"handle\"></div>  
  
    <br>
   <img src=\"$scripturl/".get_image($data['thumb'])."\" title=\"$data[name]\"><br>
   <br>
   <input type='checkbox' name='pic_id[]' value='$data[id]'>
   <a href='products_photos.php?action=edit&pic_id=$data[id]&id=$id'>$phrases[edit]</a> - 
   <a href='products_photos.php?action=del&pic_id=$data[id]&id=$id' onclick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete]</a>
   
   </div> ";
 
                       }    
                     
                     
                      print "  </div>
                      

         <div style=\"width:100%;padding-top:20px;\">
         <table width=\"100%\" class=grid>
         <tr><td>
         <img src='images/arrow_".$global_dir.".gif'>
         
          <a href='#' onclick=\"CheckAll(); return false;\"> $phrases[select_all] </a> -
          <a href='#' onclick=\"UncheckAll(); return false;\">$phrases[select_none] </a>
          &nbsp;  &nbsp;
          
         <select name='action'>
         <option value='del'>$phrases[delete]</option>
         </select>
        <input type=submit value=\"$phrases[do_button]\" onClick=\"return confirm('$phrases[are_you_sure]');\">
         </td></tr></table></div>
         
         </center>
         </form> ";    
          
           print "<script type=\"text/javascript\">
                    init_sortlist('product_photos_list','product_photos');
                  </script>";


                          
                       }else{
                       print_admin_table("<center>  $phrases[no_photos] </center>");
                       } 
     }else{
        print_admin_table("<center>$phrases[err_wrong_url]</center>");
    }                     
}
//------------ Products Photos Add ---------
if($action=="add"){
    $cat=intval($cat);
    
    
    
    $qr=db_query("select id,cat,name from store_products_data where id='$cat'");
    
    if(db_num($qr)){
    
        $data=db_fetch($qr);
        
    if_products_cat_admin($data['cat']);
    print_admin_path_links($data['cat'],"<a href='products.php?action=product_edit&id=$data[id]&cat=$data[cat]'>$data[name]</a> / $phrases[product_photos]");
     
   print "<form action=products_photos.php method=post enctype=\"multipart/form-data\">
   <input type=hidden name=action value='add_ok'>
   <input type=hidden name=id value='$cat'>";
         
    for($i=0;$i<10;$i++){
        print "<fieldset><legend>#".($i+1)."</legend>
        <table width=100%>
         <tr><td>$phrases[the_name] : </td><td><input type=text name=\"name[$i]\" size=30></td></tr>
        <tr><td>$phrases[the_image] : </td><td><input type=file name=\"img[$i]\" size=30></td></tr>
        </table>
        </fieldset>
    ";
    } 
    print "<br><br><center><input type=submit value=' $phrases[add_button] '></center></form>";
      
    }else{
        print_admin_table("<center>$phrases[err_wrong_url]</center>");
    }
}


//------------ Products Photos Add ---------
if($action=="edit"){
    $pic_id=intval($pic_id);
  
    
    
    $qrp=db_query("select id,name,product_id,img,thumb from store_products_photos where id='$pic_id'");
    
    if(db_num($qrp)){
    
        $datap=db_fetch($qrp);
        $data=db_qr_fetch("select * from store_products_data where id='$datap[product_id]'");
        
        
    if_products_cat_admin($data['cat']);
    print_admin_path_links($data['cat'],"<a href='products.php?action=product_edit&id=$data[id]&cat=$data[cat]'>$data[name]</a> / <a href='products_photos.php?action=products_photos&id=$data[id]'>$phrases[product_photos]</a> / $datap[name]");
     
   print "<form action=products_photos.php method=post >
   <input type=hidden name=action value='edit_ok'>
   <input type=hidden name=id value='$data[id]'>
    <input type=hidden name=pic_id value='$datap[id]'> 
          
  
        <center><table width=50% class=grid>
        <tr><td colspan=2 align=center><a href=\"$scripturl/".$datap['img']."\" target=_blank><img src=\"$scripturl/".get_image($datap['thumb'])."\" border=0></a></td></tr>
         <tr><td>$phrases[the_name] : </td><td><input type=text name=\"name\" value=\"$datap[name]\" size=30></td></tr>
         <tr><td colspan=2 align=center><input type=submit value=' $phrases[edit] '></td></tr>
        </table>
      
   
 </form>";
      
    }else{
        print_admin_table("<center>$phrases[err_wrong_url]</center>");
    }
}

//-----------end ----------------
require(ADMIN_DIR . '/end.php');