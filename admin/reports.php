<?
require('./start.php');   

if(!$action || $action=="reports" || $action=="del" ){

if_admin("reports");
  
  print "<p align=center class=title>$phrases[the_reports]</p>";  




//---- del -----
if($action=="del"){
     $id = (array) $id;
    for($i=0;$i<count($id);$i++){
    db_query("delete from store_reports where id='".$id[$i]."'");
    }
}


  
if(!$op){

  
$qr = db_query("select count(*) as count,report_type from store_reports group by report_type");


if(db_num($qr)){
print "
<center>

<table width=80% class='grid'><tr><td><b>$phrases[report_type]</b></td><td><b>$phrases[new_reports]</b></td><td><b>$phrases[reports_count]</b></td></tr>";
while($data=db_fetch($qr)){

$new_reports = db_qr_fetch("select count(*) as count from store_reports where report_type like '$data[report_type]' and opened=0");

print "<tr><td><a href='reports.php?op=$data[report_type]'>".$reports_types_phrases[$data['report_type']]."</a></td><td>$new_reports[count]</td><td>$data[count] </td></tr>";

} 

print "</table>";
}else{
    print_admin_table("<center>$phrases[no_reports]</center>");
}

}else{

  print "<ul class='nav-bar'>
      <li><a href='reports.php'>$phrases[the_reports]</a></li>
    <li>".$reports_types_phrases[$op]."</li>
        </ul>";
   
   $start = (int) $start;
  $reports_perpage = 50;
  $page_string = "reports.php?op=".htmlspecialchars($op)."&start={start}";
  
  
                    
  $qr = db_query("select * from store_reports where report_type like '".db_escape($op)."' order by id desc limit $start,$reports_perpage");
  if(db_num($qr)){
      
  $reports_count = db_qr_fetch("select count(*) as count from store_reports where report_type like '".db_escape($op)."'");
 
 
 
      print "<form action='reports.php' method='post' name='submit_form'>
      <input type=hidden name='op' value='".htmlspecialchars($op)."'>
      <table width=100% class=grid>";
      while($data=db_fetch($qr)){
      
       
         
          toggle_tr_class();
        
          print "<tr class='$tr_class'>
         
          <td width=10><input type='checkbox' name=\"id[]\" value=\"$data[id]\"></td>
           <td width=16>".iif(!$data['opened'],"<img src='images/new.gif'>")."</td>  
          <td><a href=\"reports.php?action=view&id=$data[id]\">$phrases[report_number_x] $data[id]</a></td>
        
          <td>$data[content]</td>
          <td>".get_date($data['date'],"d M Y h:s")."</td>
          <td align='$global_align_x'>
   <a href='reports.php?action=del&id=$data[id]&op=$data[report_type]' onClick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete]</a>
          </td></tr>";
      }
      print "
      <tr><td colspan=6>
      
       <img src='images/arrow_".$global_dir.".gif'>    
        
          <a href='#' onclick=\"CheckAll(); return false;\"> $phrases[select_all] </a> -
          <a href='#' onclick=\"UncheckAll(); return false;\">$phrases[select_none] </a>
          &nbsp;  &nbsp;
         
          <select name='action'>
         <option value='reports_del'>$phrases[delete]</option>
         </select>
        <input type=submit value=\"$phrases[do_button]\" onClick=\"return confirm('$phrases[are_you_sure]');\"> 
        
        </td></tr></table></form>";
        
        
        print_pages_links($start,$reports_count['count'],$reports_perpage,$page_string);

  }else{
      print_admin_table("<center>$phrases[no_reports]</center>");  
  }   
    
}


}


//------ report view -------
if($action=="view"){
if_admin("reports");

  print "<p align=center class=title>$phrases[the_reports]</p>";  

  

 $qr = db_query("select * from store_reports where id='$id'");
  if(db_num($qr)){
      
      $data = db_fetch($qr);
    
    db_query("update store_reports set opened=1 where id='$id'");
    
    
     print "<ul class='nav-bar'>
         <li><a href='reports.php'>$phrases[the_reports]</a></li>
         <li><a href='reports.php?op=$data[report_type]'>".$reports_types_phrases[$data['report_type']]."</a></li>
             <li>$data[id]</li>
             </ul>";
   
     
      print "<table width=100% class=grid>
      <tr><td><b>$phrases[from]</b></td><td>";
     if($data['uid']){
     print "<a href=\"$scripturl/".str_replace("{id}",$data['uid'],$links['links_profile'])."\" target=_blank>$data[name]</a>";
     }else{
     print "<a href=\"mailto:$data[email]\" target=_blank>$data[name]</a>";     
     }
     
     print "</td></tr>
     
     <tr><td valign=top><b>$phrases[the_explanation]</b></td><td><textarea cols=40 rows=8>$data[content]</textarea></td><tr>";
    
    //------ comments ------// 
     if($data['report_type']=="comment"){
        print "<tr><td><b>$phrases[the_comment]</b></td>
         <td>";
          
         $data_comment = db_qr_fetch("select id,content,comment_type,fid from store_comments where id='$data[fid]'");
        if($data_comment['id']){
         print "
         <textarea cols=40 rows=8>$data_comment[content]</textarea>";
        
        $file_info =  get_comment_file_info($data_comment['comment_type'],$data_comment['fid']);
         print "<br><br>
          <b> $phrases[the_file] / $phrases[the_products] : </b> <a href=\"$scripturl/$file_info[url]\" target=_blank>$file_info[name]</a>   
        <br><b>$phrases[the_options]  : </b> <a href=\"comments.php?action=comments_edit&op=$data_comment[comment_type]&id=$data[fid]\">$phrases[edit] / $phrases[delete] $phrases[the_comment]</a>";
        }else{
            print $phrases['comment_is_not_exist'];
        }
        
         print "</td><tr>";
      
     //----- product  -----//      
     }elseif($data['report_type']=="product"){
         
       print "<tr><td><b>$phrases[the_product]</b></td>
         <td>";
         
         
      $data_product = db_qr_fetch("select * from store_products_data where id='$data[fid]'");
      
          if($data_song['id']){
  print "<a href=\"products.php?action=edit&id=$data[fid]\">$data_product[name]</a>";
        }else{
            print $phrases['not_available'];
        }  
        
       print "</td><tr>";    
   
     }
     
     
      
      
     print "
     <tr><td colspan=2 align='$global_align_x'>
       <a href='reports.php?action=del&id=$data[id]&op=$data[report_type]' onClick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete]</a>
  </td></tr>
  
     </table>";
      
  }else{
      print_admin_table("<center>$phrases[err_wrong_url]</center>");
  }
  
}
 
 
 

 
 ?>