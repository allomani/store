<?
require('./start.php'); 
    
// ------------------------------- News ----------------------------------------
 if (!$action ||$action == "news" || $action=="news_del" || $action=="news_edit_ok" || $action=="news_add_ok" || $action=="news_cats_add_ok" || $action=="news_cats_edit_ok" || $action=="news_cats_del" || $action=="news_move_ok"){

 if_admin("news");

 $cat = (int) $cat;
 
//----- cat add ----
if($action=="news_cats_add_ok"){
    db_query("insert into store_news_cats (name,img) values('".db_escape($name)."','".db_escape($img)."')");
}

//---- cat edit -----
if($action=="news_cats_edit_ok"){  
db_query("update store_news_cats set name='".db_escape($name)."',img='".db_escape($img)."' where id='$id'");
}

//----- cat del ------
if($action=="news_cats_del"){  
db_query("delete from store_news_cats where id='$id'");
db_query("delete from store_news where cat='$id'");
 
}




//---- news add -----
if($action=="news_add_ok"){
if($auto_preview_text){
                $content = getPreviewText($details);
}
                
//----- filter XSS Tages -------
/*
include_once(CWD . "/includes/class_inputfilter.php");
$Filter = new InputFilter(array(),array(),1,1);
$details = $Filter->process($details);*/
//------------------------------

         db_query("insert into store_news(title,writer,content,details,date,img,cat)values('".db_escape($title)."','".db_escape($writer)."','".db_escape($content,false)."','".db_escape($details,false)."','".time()."','".db_escape($img)."','$cat')");
 }
 
 
//--------news edit--------------------
if ($action=="news_edit_ok"){
            if($auto_preview_text){
                $content = getPreviewText($details);
                }

//----- filter XSS Tages -------
/*
include_once(CWD . "/includes/class_inputfilter.php");
$Filter = new InputFilter(array(),array(),1,1);
$details = $Filter->process($details);*/
//------------------------------

                db_query("update store_news set title='".db_escape($title)."',writer='".db_escape($writer)."',content='".db_escape($content,false)."',details='".db_escape($details,false)."',img='".db_escape($img)."' where id='$id'");

}
 
//-------------delete-------------
    if ($action=="news_del"){
    $id = (array) $id;
    foreach($id as $iid){
          db_query("delete from store_news where id='$iid'");
    }
            }
            
//----- move -------
if($action=="news_move_ok"){
    $id = (array) $id;
    foreach($id as $iid){
        db_query("update store_news set cat='$cat' where id='$iid'");
    }
    
}
//-----------------------------

//----------------------------------------------------------------------------------------//

  print "<p align=center class=title>$phrases[the_news]</p>";
   
  

if($cat){  
$qr = db_query("select id,name from store_news_cats where id='$cat'");
if(db_num($qr)){
    $data=db_fetch($qr);
print "<ul class='nav-bar'>
    <li><a href='news.php'>$phrases[the_news]</a></li>
    <li>$data[name]</li>
    </ul>";
  $continue = true;  
}else{
   print_admin_table("<center>$phrases[err_wrong_url]</center>"); 
    $continue = false;
}
}else{
    print "<ul class='nav-bar'>
        <li><a href='news.php'>$phrases[the_news]</a></li>
    </ul>"; 
    $continue = true;
}


    
     
if($continue){        

//-------------- cats ----------
$no_cats = false;
if(!$cat){
print "<p align='$global_align'><a href='news.php?action=news_cats_add' class='add'>$phrases[add_cat]</a></p>";
 $qr = db_query("select * from  store_news_cats order by ord asc");
 if(db_num($qr)){
 print "<center>
 <table width=100% class=grid><tr><td>
 <div id=\"news_cats_list\" >";
 
 while($data = db_fetch($qr)){
      print "<div id=\"item_$data[id]\" onmouseover=\"this.style.backgroundColor='#EFEFEE'\"
     onmouseout=\"this.style.backgroundColor='#FFFFFF'\">
      <table width=100%><tr>
      
      <td width=25>
      <span style=\"cursor: move;\" class=\"handle\"><img alt='$phrases[click_and_drag_to_change_order]' src='images/move.gif'></span> 
      </td>
      
      <td width=75%><a href='news.php?cat=$data[id]'>$data[name]</a></td>
     
      <td align='$global_align_x'><a href='news.php?action=news_cats_edit&id=$data[id]'>$phrases[edit]</a> - 
      <a href=\"news.php?action=news_cats_del&id=$data[id]\" onClick=\"return confirm('$phrases[news_cat_del_warn]');\">$phrases[delete]</a></td>
     </table></div> ";
         }
       print "</div></td></tr></table><br>
       
       
  <script type=\"text/javascript\">
        init_sortlist('news_cats_list','set_news_cats_sort');
</script>";

 }else{
     $no_cats = true;
 }
}else{
     $no_cats = true; 
}
//-------------------------------


 print "<p align='$global_align'><a href='news.php?action=news_add&cat=$cat' class='add'>$phrases[news_add]</a></p>";
 
 
 //----------------- start pages system ----------------------
 $start=(int) $start;  
   $page_string= "news.php?cat=$cat&start={start}";
   $news_perpage = intval($settings['news_perpage']);
   //--------------------------------------
   
 $qr=db_query("select * from store_news where cat='$cat' order by id DESC limit $start,$news_perpage")   ;

       if (db_num($qr)){
           
    //-------------------------------
   $news_count = valueof(db_qr_fetch("select count(*) as count from store_news where cat='$cat'"),"count");  
   //--------------------------------------------------------------
   
   
           print "<br><center>
           <form action='news.php' method=post name='submit_form'>
        <input type='hidden' name='cat' value='$cat'>
   <input type='hidden' name='start' value='$start'>
        
        <table border=0 width=\"100%\" class=\"grid\">";


         while($data= db_fetch($qr)){
             
             toggle_tr_class();
    
     print "<tr class='$tr_class'>
     <td width=2>
      <input type=checkbox name=id[] value='$data[id]'>
      </td>
                <td>$data[title]</td>
                 <td align=center>".get_date($data['date'])."</td>  
                <td align='$global_align_x'><a href='news.php?action=news_edit&id=$data[id]&start=$start'>$phrases[edit] </a> - <a href='news.php?action=news_del&id=$data[id]&cat=$cat&start=$start' onClick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete]</a></td>
        </tr>";

                 }

                print" 
               <tr><td colspan=3> 
                       <table width=100%><tr>
          <td width=2><img src='images/arrow_".$global_dir.".gif'></td>   
          <td>

          <a href='#' onclick=\"CheckAll(); return false;\"> $phrases[select_all] </a> -
          <a href='#' onclick=\"UncheckAll(); return false;\">$phrases[select_none] </a> 
          &nbsp;&nbsp; 
          <select name=action>
         
          <option value='news_move'>$phrases[move]</option>
           <option value='news_del'>$phrases[delete]</option>  
          </select>
           &nbsp;&nbsp;
           <input type=submit value=' $phrases[do_button] ' onClick=\"return confirm('".$phrases['are_you_sure']."');\">
          </td></tr></table>
          
          </td></tr>
          
          
          </table></form><br>\n";
                
     //-------------------- pages system ------------------------
print_pages_links($start,$news_count,$news_perpage,$page_string);
//-----------------------------------------------------------------



                }else{
                
     if($no_cats){
                        print_admin_table("<center> $phrases[no_news] </center>");
     }
                
   }
}
}

//-------------- Edit News ----------------
if($action == "news_edit"){

    if_admin("news");

   $start = (int) $start;
   
  $qr=db_query("select * from store_news where id='$id'");

  if(db_num($qr)){
 
 $data=db_fetch($qr);   
 
 
 if($data['cat']){
    $data_cat = db_qr_fetch("select id,name from store_news_cats where id='$data[cat]'");
}


  
      print "
<ul class='nav-bar'>
<li><a href='news.php'>$phrases[the_news]</a></li>
".iif($data['cat'],"<li><a href='news.php?cat=$data[cat]'>$data_cat[name]</a></li>")."
              <li>$data[title]</li>
              </ul>";


      print " <center>
  
          <form method=\"POST\" action=\"news.php\" name=\"sender\"> 
           <input type=hidden name=\"action\" value='news_edit_ok'>
                       <input type=hidden name=\"id\" value='$id'>
                       <input type=hidden name=\"cat\" value='$data[cat]'>  
                       <input type=hidden name=\"start\" value='$start'> 
      
                       
                <table border=0 width=\"100%\" class=grid><tr>

                      <tr>
                                <td width=\"100\">
                <b>$phrases[the_title]</b></td><td >
                <input type=\"text\" name=\"title\" size=\"50\" value='$data[title]'></td>
                        </tr>
                       <tr>
                                <td width=\"100\">
                <b>$phrases[the_writer]</b></td><td width=\"223\">
                <input type=\"text\" name=\"writer\" size=\"50\" value='$data[writer]'></td>
                        </tr>

                               <tr> <td width=\"100\">
                <b>$phrases[the_image]</b></td>
                                <td>


                            <table><tr><td>
                                 <input type=\"text\" name=\"img\" size=\"50\" dir=ltr value=\"$data[img]\">   </td>

                                <td> <a href=\"javascript:uploader('news','img');\"><img src='images/file_up.gif' border=0 title='$phrases[upload_file]'></a>
                                 </td></tr></table>

                                 </td></tr>


                   <tr> <td width=\"50\" colspan=2>
                <b>$phrases[the_details]</b></td> </tr>
                            <tr>    <td colspan=2>";
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
                 <tr><td colspan=2 align=center>  <input type=\"submit\" value=\"$phrases[edit]\">  </td></tr>




                </table>

</form>    </center>\n";
  }else{
      print_admin_table("<center>$phrases[err_wrong_url]</center>");
  }
        }
//------------------ News Add -------------------
if($action=="news_add"){

    if_admin("news");

   
if($cat){
    $data_cat = db_qr_fetch("select id,name from store_news_cats where id='$cat'");
}else{
    $data_cat['id'] = 0 ;
}

 
print "<ul class='nav-bar'>
    <li><a href='news.php'>$phrases[the_news]</a></li>
".iif($data_cat['id'],"<li><a href='news.php?cat=$data_cat[id]'>$data_cat[name]</a></li>")."
        <li>$phrases[news_add]</li>
        </ul>";

print "<center>
                <table border=0 width=\"100%\"  class=grid><tr>

                <form name=sender method=\"POST\" action=\"news.php\" name=\"sender\">

                      <input type=hidden name=\"action\" value='news_add_ok'>
                      <input type=hidden name=\"cat\" value='".intval($data_cat['id'])."'>  



                        <tr>
                                <td width=\"100\">
                <b>$phrases[the_title]</b></td><td >
                <input type=\"text\" name=\"title\" size=\"50\"></td>
                        </tr>
                       <tr>
                                <td width=\"100\">
                <b>$phrases[the_writer]</b></td><td width=\"223\">
                <input type=\"text\" name=\"writer\" size=\"50\" value=\"$user_info[username]\"></td>
                        </tr>

                               <tr> <td width=\"100\">
                <b>$phrases[the_image]</b></td>
                                <td>
                                <table><tr><td>
                                <input type=\"text\" name=\"img\" size=\"50\" dir=ltr>  </td><td> <a href=\"javascript:uploader('news','img');\"><img src='images/file_up.gif' border=0 title='$phrases[upload_file]'></a>
                                 </td></tr></table>
                                 </td></tr>
                                          <tr> <td width=\"50\" colspan=2>
                <b>$phrases[the_details]</b></td></tr>
                                <tr><td colspan=2>";
                                editor_print_form("details",600,300,"");

                                print "
                                <tr><td colspan=2><input name=\"auto_preview_text\" type=\"checkbox\" value=1 onClick=\"show_hide_preview_text(this);\"> $phrases[auto_short_content_create]
                                </td></tr>
                      <tr id=preview_text_tr> <td width=\"100\">
                <b>$phrases[news_short_content]</b></td>
                                <td>
                                <textarea cols=60 rows=5 name='content'></textarea>


                                </td></tr>
                  <tr><td align=center colspan=2>
                 <input type=\"submit\" value=\"$phrases[add_button]\">
                        </td>
                        </tr>
</table>

</form>    </center>\n";
}



// --------------------- News Cats Edit ------------------------------
 if($action == "news_cats_edit"){
     
 if_admin("news");
 
$qr = db_query("select * from store_news_cats where id='$id'");     

if(db_num($qr)){
    $data = db_fetch($qr);
    
    print "<ul class='nav-bar'>
        <li><a href='news.php'>$phrases[the_news]</a></li>
        <li>$data[name]</li>
    </ul>";
    
    
               print "<center>
                    <form method=\"POST\" action=\"news.php\" name='sender'>

                      <input type=hidden name=\"id\" value='$id'>

                      <input type=hidden name=\"action\" value='news_cats_edit_ok'> 
                      
                <table border=0 width=\"100%\"   class=grid><tr>


                      
                        <tr>
                                <td width=\"50\">
                <b>$phrases[the_name]</b></td><td width=\"223\">
                <input type=\"text\" name=\"name\" value=\"$data[name]\" size=\"29\"></td>
                        </tr> 
                        
                                                 <tr> <td width=\"100\">
                <b>$phrases[the_image]</b></td>
                                <td>                 
                                <table><tr><td>
                                <input type=\"text\" name=\"img\" size=\"50\" value=\"$data[img]\" dir=ltr>  </td><td> <a href=\"javascript:uploader('cats','img');\"><img src='images/file_up.gif' border=0 title='$phrases[upload_file]'></a>
                                 </td></tr></table>
                                 </td></tr>
                                 
                                 <tr>
                                <td colspan=2>
                <center><input type=\"submit\" value=\"$phrases[edit]\">
                        </td>
                        </tr>

      
                </table>

</form>    </center>\n";
}else{
    print_admin_table("<center> $phrases[err_wrong_url] </center>");
}
                      }
                      
// --------------------- News Cats Add ------------------------------
 if($action == "news_cats_add"){
   
   if_admin("news");
    
         print "<ul class='nav-bar'>
             <li><a href='news.php'>$phrases[the_news]</a></li>
   <li>$phrases[add_cat]</li>
   </ul>";
    
    
    
               print "<center>

                               <form method=\"POST\" action=\"news.php\" name='sender'>

                      <input type=hidden name=\"action\" value='news_cats_add_ok'> 
                      
                <table border=0 width=\"100%\" class=grid><tr>



                 <tr>
                                <td width=\"50\">
                <b>$phrases[the_name]</b></td><td width=\"223\">
                <input type=\"text\" name=\"name\" size=\"29\"></td>
                        </tr> 
                                                 <tr> <td width=\"100\">
                <b>$phrases[the_image]</b></td>
                                <td>                 
                                <table><tr><td>
                                <input type=\"text\" name=\"img\" size=\"50\" dir=ltr>  </td><td> <a href=\"javascript:uploader('cats','img');\"><img src='images/file_up.gif' border=0 title='$phrases[upload_file]'></a>
                                 </td></tr></table>
                                 </td></tr>
                                 
                                 <tr>
                                <td colspan=2>
                <center><input type=\"submit\" value=\"$phrases[add]\">
                        </td>
                        </tr>

      
                </table>

</form>    </center>\n";
}

//------------- news move --------------
if($action=="news_move"){
    if_admin("news");
    
    
    $id = (array) $id;
if(count($id)){
    $ids = implode(",",$id);                       
$qr = db_query("select id,title from store_news where id IN ($ids)");
if(db_num($qr)){
    print "<form action='news.php' method=post>
    <input type='hidden' name='action' value='news_move_ok'>
    
    <table width='100%' class='grid'>";
    $c=0;
    while($data=db_fetch($qr)){
        print "<input type='hidden' name='id[$c]' value='$data[id]'>";
        print "<tr><td><b>".($c+1).". </b> $data[title] </td></tr>";
        $c++;
    }
  print "<tr><td>";
  
  $qrc = db_query("select id,name from store_news_cats order by ord");
  if(db_num($qrc)){
       print "<b> $phrases[move_to] : </b> <select name='cat'>
       <option value=0>$phrases[without_main_cat]</option>";
       while($datac=db_fetch($qrc)){
           print "<option value='$datac[id]'>$datac[name]</option>";
       }
       
       print "</select> <input type='submit' value='$phrases[move]'>";
  }else{
      print $phrases['no_cats'];
  }
     
    print "</td></tr></table>
    </form>";
}else{
    print_admin_table("<center>$phrases[err_wrong_url]</center>");
}

}else{
     print_admin_table("<center>$phrases[please_select_news_first]</center>");    
}

}

//-----------end ----------------
 require(ADMIN_DIR.'/end.php');