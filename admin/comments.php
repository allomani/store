<?
require('./start.php');  


if(!$action || $action=="comments" || $action=="comments_activate"  || $action=="comments_deactivate" || $action=="comments_del" || $action=="comments_edit_ok"){

if_admin("comments");


      
  print "<p align=center class=title>$phrases[the_comments]</p>";  


//---- activate ---
if($action=="comments_activate"){
    $id = (array) $id;
    for($i=0;$i<count($id);$i++){
    db_query("update store_comments set active=1 where id='".$id[$i]."'");
    }
}

//---- deactivate ---
if($action=="comments_deactivate"){
    $id = (array) $id;
    for($i=0;$i<count($id);$i++){
    db_query("update store_comments set active=0 where id='".$id[$i]."'");
    }
}



//---- del -----
if($action=="comments_del"){
     $id = (array) $id;
    for($i=0;$i<count($id);$i++){
    db_query("delete from store_comments where id='".$id[$i]."'");
    }
}

//---- edit ----
if($action=="comments_edit_ok"){
   $id = (array) $id;
    for($i=0;$i<count($id);$i++){
    db_query("update store_comments set content='".db_escape($content[$i])."',active='".intval($active[$i])."' where id='".$id[$i]."'");
    }
}



  
if(!$op){

  
$qr = db_query("select count(*) as count,comment_type from store_comments group by comment_type");


if(db_num($qr)){
print "
<center>

<table width=80% class='grid'><tr><td><b>$phrases[comment_type]</b></td><td><b>$phrases[comments_waiting_admin_review]</b></td><td><b>$phrases[comment_count]</b></td></tr>";
while($data=db_fetch($qr)){

    $new_comments = db_qr_fetch("select count(*) as count from store_comments where comment_type like '$data[comment_type]' and active=0");

    
print "<tr><td><a href='comments.php?op=$data[comment_type]'>".$comments_types_phrases[$data['comment_type']]."</a></td><td>$new_comments[count]</td><td>$data[count]</td></tr>";

} 

print "</table>";
}else{
    print_admin_table("<center>$phrases[no_comments]</center>");
}

}else{

  print "<img src='images/arrw.gif'>&nbsp; <a href='comments.php'>$phrases[the_comments]</a> / ".$comments_types_phrases[$op]." <br><br>";
  
  $start = (int) $start;
  $comments_perpage = 50;
  $page_string = "comments.php?op=".htmlspecialchars($op)."&start={start}";
  
 
  $qr = db_query("select * from store_comments where comment_type like '".db_escape($op)."' order by id desc limit $start,$comments_perpage");
  if(db_num($qr)){
      
  $comments_count = db_qr_fetch("select count(*) as count from store_comments where comment_type like '".db_escape($op)."'");
 
  
  
      print "<form action='comments.php' method='post' name='submit_form'>
      <input type=hidden name='op' value='".htmlspecialchars($op)."'>
      <table width=100% class=grid>";
      $last_active = 0;
      while($data=db_fetch($qr)){
          
          $data_member = db_qr_fetch("select ".members_fields_replace('username')." from ".members_table_replace('store_clients')." where ".members_fields_replace('id')."='$data[uid]'",MEMBER_SQL);
         
$file_info =  get_comment_file_info($op,$data['fid']);
         
 
 if($last_active != $data['active']){
     print "<tr><td colspan=7><hr size=1 class=separate_line></td></tr>";
 }
 
 $last_active = $data['active'];
 
          print "<tr>
          <td width=10><input type='checkbox' name=\"id[]\" value=\"$data[id]\"></td>
           <td width=16>".iif(!$data['active'],"<img src='images/new.gif'>")."</td>       
          <td><a href=\"$scripturl/$file_info[url]\" target=_blank>$file_info[name]</a></a></td>
          <td><a href=\"clients.php?action=client_edit&id=$data[uid]\" target=_blank>$data_member[username]</a></td>
          <td>$data[content]</td>
          <td>".date("d-m-Y h:s:i",$data['time'])."</td>
          <td align='$global_align_x'>
          ".iif($data['active'],"<a href='comments.php?action=comments_deactivate&id=$data[id]&op=$data[comment_type]'>$phrases[deactivate]</a>","<a href='comments.php?action=comments_activate&id=$data[id]&op=$data[comment_type]'>$phrases[activate]</a>")." -
          <a href='comments.php?action=comments_edit&id=$data[id]&op=$data[comment_type]'>$phrases[edit]</a> -
          <a href='comments.php?action=comments_del&id=$data[id]&op=$data[comment_type]' onClick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete]</a>
          </td></tr>";
      }
      print "
      <tr><td colspan=6>
      
       <img src='images/arrow_".$global_dir.".gif'>    
        
          <a href='#' onclick=\"CheckAll(); return false;\"> $phrases[select_all] </a> -
          <a href='#' onclick=\"UncheckAll(); return false;\">$phrases[select_none] </a>
          &nbsp;  &nbsp;
         
          <select name='action'>
           <option value='comments_activate'>$phrases[activate]</option>  
           <option value='comments_deactivate'>$phrases[deactivate]</option>  
           
          <option value='comments_edit'>$phrases[edit]</option>
         <option value='comments_del'>$phrases[delete]</option>
         </select>
        <input type=submit value=\"$phrases[do_button]\" onClick=\"return confirm('$phrases[are_you_sure]');\"> 
        
        </td></tr></table></form>";
        
        
print_pages_links($start,$comments_count['count'],$comments_perpage,$page_string);



  }else{
      print_admin_table("<center>$phrases[no_comments]</center>");  
  }   
    
}


} 
 
 
 //-------- edit --------------
 if($action=="comments_edit"){
     if_admin("comments");  
  
 
    
       $id = (array) $id;
    
    if(count($id)){
    $id = array_map("intval",$id);
    
       $qr=db_query("select * from store_comments where id IN (".implode(",",$id).")");
    if(db_num($qr)){
    
 
    print "<img src='images/arrw.gif'>&nbsp; <a href='comments.php'>$phrases[the_comments]</a> / <a href='comments.php?op=".htmlspecialchars($op)."'>".$comments_types_phrases[$op]."</a> / $phrases[edit] <br><br>";
  
     
       print "<form action='comments.php' method='post'>
       <input type='hidden' name='action' value='comments_edit_ok'> 
       <input type='hidden' name='op' value='".htmlspecialchars($op)."'>
       
       <center>";
       
       $i=0;
       while($data=db_fetch($qr)){
           
             $data_member = db_qr_fetch("select ".members_fields_replace("username")." from ".members_table_replace("store_clients")." where ".members_fields_replace("id")."='$data[uid]'",MEMBER_SQL);
        
           $file_info = get_comment_file_info($data['comment_type'],$data['fid']);
        
           print "<input type='hidden' name='id[$i]' value='$data[id]'>";
           print "<table width=60% class=grid>
           <tr><td><b>$phrases[the_member]</b></td>  <td><a href=\"$scripturl/".str_replace("{id}",$data['uid'],$links['profile'])."\" target=_blank>$data_member[username]</a></td></tr>
           <tr><td><b>$phrases[the_content]</b></td><td><textarea cols=40 rows=6 name='content[$i]'>$data[content]</textarea></td></tr>
        <tr><td><b>$phrases[the_status]</b></td><td>";
        print_select_row("active[$i]",array("0"=>$phrases['disabled'],"1"=>$phrases['enabled']),$data['active']);
        print "</td></tr>
        
        <tr><td><b>$phrases[the_file]</b></td><td>
        <a href=\"$scripturl/$file_info[url]\" target=_blank>$file_info[name]</a>
        </td></tr>  
          
            <tr><td colspan=2 align='$global_align_x'>
       <a href='comments.php?action=comments_del&id=$data[id]&op=$data[comment_type]' onClick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete]</a>
  </td></tr>
  
  </table><br>";
           
       $i++;
       }
       
       print "<input type='submit' value='$phrases[edit]'>
       </form></center>";
       
       
    }else{
          print_admin_table("<center>  $phrases[err_wrong_url] </center>") ;     
    }
    }else{
            print_admin_table("<center>  $phrases[no_files_selected] </center>") ;     
    }
    
    
 } 
 
 
 
 
 //--------------
 function get_comment_file_info($op,$fid){
          global $links,$settings;
     switch($op){
             
             case "news" :
             $df = db_qr_fetch("select title,id from store_news where id='$fid'");
             $file_name = $df['title']; 
             $file_url = str_replace("{id}",$df['id'],$links['news_details']) ;
             break;
             
             
             
             case "singer" :
             $df = db_qr_fetch("select name,page_name,id from store_singers where id='$fid'");
             $file_name = $df['name']; 
             $file_url = singer_url($df['id'],$df['page_name'],$df['name']);
             break;
             
             case "album" :
             $df = db_qr_fetch("select store_albums.*,store_singers.id as singer_id ,store_singers.name as singer_name,store_singers.page_name as singer_page_name from store_singers,store_albums where store_singers.id=store_albums.cat and store_albums.id='$fid'");
             $file_name = $df['singer_name'] . " - ".$df['name']; 
            
             $file_url = album_url($df['id'],$df['page_name'],$df['name'],$df['singer_id'],$df['singer_page_name'],$df['singer_name']);
             break;
             
             
             case "singer_photo" :
             $df = db_qr_fetch("select id from store_singers_photos where id='$fid'");
             $file_name = "$phrases[the_photo] # $df[id]"; 
             $file_url = str_replace("{id}",$df['id'],$links['singer_photo']) ;
             break;
             
             case "video" :
             $df = db_qr_fetch("select id,name from store_videos_data where id='$fid'");
             $file_name = $df['name'];    
             $file_url = str_replace("{id}",$df['id'],$links['video_watch']) ;
             break;
             
             case "song" :
           
             $df = db_qr_fetch("select id,name from store_songs where id='$fid'");
             $file_name = $df['name']; 
             $file_url = str_replace(array("{cat}","{id}"),array($settings['default_url_id'],$df['id']),$links['song_listen']) ;
             break;
              
             default : $file_url="";$file_name=""; break;
         }
 return array("name"=>$file_name,"url"=> $file_url);
          
 }
 
//-----------end ----------------
 require(ADMIN_DIR.'/end.php');