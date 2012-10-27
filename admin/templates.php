<?
require('./start.php'); 

//--------------------- Templates ----------------------------------

  if(!$action || $action =="templates" || $action =="edit_ok" || $action=="del" ||
  $action =="add_ok" || $action=="cat_edit_ok" || $action=="cat_add_ok" ||
  $action=="cat_del" || $action=="set_default"){

 if_admin("templates");

 $id=intval($id);
 $cat =intval($cat);

 //-----  set default style ----
 if($action=="set_default"){
     db_query("update store_settings set value='$id' where name like 'default_styleid'");
     load_settings(); 
 }
 //------- template cat edit ---------
 if($action=="cat_edit_ok"){
 if(trim($name)){
 db_query("update store_templates_cats set name='".db_escape($name)."',selectable='".intval($selectable)."',images='".db_escape($images)."' where id='$id'");
     }
 }
//------ template cat add ----------
if($action=="cat_add_ok"){
db_query("insert into store_templates_cats (name,selectable,images) values('".db_escape($name)."','".intval($selectable)."','".db_escape($images)."')");
$catid = db_inserted_id();

$qr = db_query("select * from store_templates where cat='1' order by id");
while($data = db_fetch($qr)){
db_query("insert into store_templates (name,title,content,cat,protected,group_id) values (
'".db_escape($data['name'])."',
'".db_escape($data['title'])."',
'".db_escape($data['content'],false)."',
'$catid',
'".$data['protected']."'
'".$data['group_id']."')");
    }

}
//--------- template cat del --------
if($action=="cat_del"){
if($id !="1"){
db_query("delete from store_templates where cat='$id'");
db_query("delete from store_templates_cats where id='$id'");
     }
    }
//-------- template edit -----------
if($action =="edit_ok"){
$non_safe_content =  check_safe_functions($content);
if(!$non_safe_content){
db_query("update store_templates set title='".db_escape($title)."',content='".db_escape($content,false)."',group_id='".intval($group_id)."' where id='$id'");
cache_del("template:$cat:$name");

}else{
    print_admin_table("<center> $non_safe_content </center>");
}
}
//--------- template add ------------
if($action =="add_ok"){
$non_safe_content =  check_safe_functions($content);
if(!$non_safe_content){
    
db_query("insert into  store_templates (name,title,content,cat,group_id) values(
'".db_escape($name)."',
'".db_escape($title)."',
'".db_escape($content,false)."',
'".intval($cat)."','".intval($group_id)."')");

}else{
    print_admin_table("<center> $non_safe_content </center>");
}
}
//---------- template del ---------
if($action=="del"){
      db_query("delete from store_templates where id='$id' and protected=0");
      db_query("update store_blocks set template=0 where template='$id'");
}

print "
  <p class=title align=center>  $phrases[the_templates] </p> ";


  if($cat){

$cat_data = db_qr_fetch("select name from store_templates_cats where id='$cat'");
print "<ul class='nav-bar'>
    <li><a href='templates.php'>$phrases[the_templates]</a></li>
<li>$cat_data[name]</li>
</ul>";


         $qr = db_query("select a.*,b.name as group_name from store_templates a,store_templates_groups b where a.cat = '$cat' and b.id = a.group_id order by b.ord , a.id");
        if (db_num($qr)){
      print "<p align='$global_align'><a href='templates.php?action=add&cat=$cat' class='add'>$phrases[cp_add_new_template] </a></p>
      <br>" ;

   $trx = 1;
   $last_group_id = 0;
    while($data=db_fetch($qr)){
        
    if($last_group_id != $data['group_id']){
        
        print iif($last_group_id,"</table></center>");
        $last_group_id = $data['group_id'];
        print "<h3>$data[group_name]</h3><hr class='separate_line'>";
        print "<center><table width=99% class=grid>";
        $tr_class='';
        }
        
    toggle_tr_class();
        
        
    print "<tr class='$tr_class'><td><b>$data[name]</b><br><span class=small>$data[title]</span></td>
   <td align=center> <a href='templates.php?action=edit&id=$data[id]'> $phrases[edit] </a>"
    .iif(!$data['protected']," - <a href='templates.php?action=del&id=$data[id]&cat=$cat' onclick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete]</a>")
    ."</td></tr>";

     }
      print "</table>";

                }else{
                    print_admin_table($phrases['cp_no_templates']);
                     }

}else{
    $qr = db_query("select * from store_templates_cats order by id asc");
     print "<p align='$global_align'><a href='templates.php?action=cat_add' class='add'>$phrases[add_style] </a></p>
      <br>
    <center><table width=90% class=grid>";
    while($data =db_fetch($qr)){
    print "<tr><td><a href='templates.php?cat=$data[id]'>$data[name]</a></td>
    <td align=center>".iif($data['id']==$settings['default_styleid'],"[$phrases[default]]","<a href='templates.php?action=set_default&id=$data[id]'>$phrases[set_default]</a>")." - 
    <a href='templates.php?cat=$data[id]'>$phrases[edit_templates]</a> -   
    
     <a href='templates.php?action=cat_edit&id=$data[id]'> $phrases[style_settings] </a>";
    if($data['id']!=1){
            print " - <a href='templates.php?action=cat_del&id=$data[id]' onclick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete]</a>";
            }
            print "</td></tr>";
    }
    print "</table></center>";
}



          }
  //--------template cat edit --------
  if($action=="cat_edit"){
    if_admin("templates");

      $id= intval($id);
$qr= db_query("select * from store_templates_cats where id='$id'");
 print  "<p class=title align=center>  $phrases[the_templates] </p> ";
if(db_num($qr)){
$data = db_fetch($qr);
 print "<center>
 <form action='templates.php' method=post>
 <input type=hidden name=action value='cat_edit_ok'>
 <input type=hidden name=id value='$id'>
 <table width=70% class=grid>
 <tr><td><b>$phrases[the_name]</b></td>
 <td>";
 print_text_row("name",$data['name']);
 print "</td></tr>
 <tr><td><b>$phrases[images_folder]</b></td>
 <td>";
 print_text_row("images",$data['images']);
 print "</td></tr>
 <tr><td><b>$phrases[style_selectable]</b></td><td>";
 print_select_row("selectable",array("$phrases[no]","$phrases[yes]"),$data['selectable']);
 print "</td></tr>
 <tr><td align=center colspan=2><input type=submit value=' $phrases[edit] '></td></tr>
 </table>";
}else{
    print_admin_table($phrases['err_wrong_url']);
    }
  }
  //--------template cat add --------
  if($action=="cat_add"){
    if_admin("templates");



print  "<p class=title align=center>  $phrases[the_templates] </p> ";

print "<center>
 <form action='templates.php' method=post>
 <input type=hidden name=action value='cat_add_ok'>
 <table width=70% class=grid>
 <tr><td><b>$phrases[the_name]</b></td>
 <td>";
 print_text_row("name");
 print "</td></tr>
  <tr><td><b>$phrases[images_folder]</b></td>
 <td>";
 print_text_row("images");
 print "</td></tr>
 <tr><td><b>$phrases[style_selectable]</b></td><td>";
 print_select_row("selectable",array("$phrases[no]","$phrases[yes]"));
 print "</td></tr>
 <tr><td align=center colspan=2><input type=submit value=' $phrases[add_button] '></td></tr>
 </table>";

  }
 //-------- template edit ------------
          if($action=="edit"){
    if_admin("templates");
   $id=intval($id);
$qr = db_query("select * from store_templates where id='$id'");
      if(db_num($qr)){

      $data = db_fetch($qr);
      


    $data['content'] = htmlspecialchars($data['content']);
    
     
 $cat_data = db_qr_fetch("select name from store_templates_cats where id='$data[cat]'");
print "<ul class='nav-bar'>
    <li><a href='templates.php'>$phrases[the_templates]</a></li>
        <li><a href='templates.php?cat=$data[cat]'>$cat_data[name]</a></li>
 <li>$data[name]</li>
 </ul>";



print "
  <center>
          <span class=title>$data[name]</span>  <br><br>
  <form method=\"POST\" action=\"templates.php\">
  <input type='hidden' name='action' value='edit_ok'>
  <input type='hidden' name='id' value='$data[id]'>
   <input type='hidden' name='cat' value='$data[cat]'>
   <input type='hidden' name='name' value='".strtolower($data['name'])."'>
   

  <table width=99% class=grid>
        <tr><td> <b> $phrases[template_name] : </b></td><td>$data[name]</td></tr>
  <tr> <td> <b> $phrases[template_description] : </b></td><td><input type=text size=30 name=title value='$data[title]'></td></tr>
  <tr><td><b>$phrases[the_group] : </b></td>
     <td><select name='group_id'>";
$qrg = db_query("select * from store_templates_groups order by ord");
while($datag=db_fetch($qrg)){
    print "<option value='$datag[id]'".iif($datag['id']==$data['group_id']," selected").">$datag[name]</option>";
}
print "</select></td></tr>
   </table> <br>
   
        <textarea id='content' name=\"content\">$data[content]</textarea>
        <center>
        <input type=\"submit\" value=\" $phrases[edit] \" name=\"B1\"></td></tr>
        </center>
</form></center>\n";

code_editor_init("content");

}else{
print_admin_table($phrases['err_wrong_url']);
        }
 }
//------------ template add ------------
  if($action=="add"){
if_admin("templates");

   $cat=intval($cat);
 $cat_data = db_qr_fetch("select name from store_templates_cats where id='$cat'");
print "<ul class='nav-bar'>
    <li><a href='templates.php'>$phrases[the_templates]</a></li>
        <li><a href='templates.php?cat=$cat'>$cat_data[name]</a></li>
 <li>$phrases[add_new_template]</li>
 </ul>";


print "
  <center>
          <span class=title>$phrases[add_new_template] </span>  <br><br>
  <form method=\"POST\" action=\"templates.php\">
  <input type='hidden' name='action' value='add_ok'>
  <input type='hidden' name='cat' value='".intval($cat)."'>
  <table width=99% class=grid><tr>
  <td> <b> $phrases[template_name] : </b></td><td><input type=text size=30 name=name></td></tr>
  <tr>
  <td> <b> $phrases[template_description] : </b></td><td><input type=text size=30 name=title></td></tr>
          <tr><td><b>$phrases[the_group] : </b></td>
     <td><select name='group_id'>";
$qrg = db_query("select * from store_templates_groups order by ord");
while($datag=db_fetch($qrg)){
    print "<option value='$datag[id]'>$datag[name]</option>";
}
print "</select></td></tr>
  </table>
        <br>
        <textarea id=\"content\" name=\"content\"></textarea>
       
        <center>
        <input type=\"submit\" value=\"$phrases[add_button]\" name=\"B1\">
        </center>
        
</form>\n";

code_editor_init("content");
 }

 //-----------end ----------------
 require(ADMIN_DIR.'/end.php');