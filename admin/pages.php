<?
require('./start.php'); 

// ------------------------------- pages ----------------------------------------
 if (!$action || $action == "pages" || $action=="pages_del" || $action=="pages_edit_ok" || $action=="pages_add_ok" || $action=="page_enable" || $action=="page_disable"){

 if_admin("pages");

if($action=="page_enable"){
        db_query("update store_pages set active=1 where id='$id'");
        }

if($action=="page_disable"){
        db_query("update store_pages set active=0 where id='$id'");
        }

if($action=="pages_add_ok"){
         db_query("insert into store_pages(title,content,active)values('".db_escape($title)."','".db_escape($content,false)."','1')");
}
        //==========================================
    if ($action=="pages_del"){
          db_query("delete from store_pages where id='$id'");
            }
            //==============================================
            if ($action=="pages_edit_ok"){
                db_query("update store_pages set title='".db_escape($title)."',content='".db_escape($content,false)."' where id='$id'");

                    }
                    //================================================
  print "<p align=center class=title>$phrases[the_pages]</p>
                <p align=$global_align><a href='pages.php?action=pages_add'><img src='images/add.gif' border=0>$phrases[pages_add]</a></p>";


       $qr=db_query("select * from store_pages order by id DESC")   ;
          print "<br><center><table border=0 width=\"90%\"   cellpadding=\"0\" cellspacing=\"0\" class=\"grid\">";
       if (db_num($qr)){



         while($data= db_fetch($qr)){
     print "            <tr>
                <td >$data[title]</td>
                <td align=center> <a target=_blank href='../".str_replace('{id}',$data['id'],$links['pages'])."'>$phrases[view_page]</a> </td>
                <td align=left>" ;

                if($data['active']){
                        print "<a href='pages.php?action=page_disable&id=$data[id]'>$phrases[disable] </a>" ;
                        }else{
                        print "<a href='pages.php?action=page_enable&id=$data[id]'>$phrases[enable] </a>" ;
                        }

                print " - <a href='pages.php?action=pages_edit&id=$data[id]'>$phrases[edit] </a> - <a href='pages.php?action=pages_del&id=$data[id]' onClick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete]</a></td>
        </tr>";

                 }


                }else{
                        print "<tr><td width=100%><center> $phrases[no_pages] </center></td></tr>";
                        }
                      print" </table>\n";
}
//--------- Edit Pages ----------------
if($action == "pages_edit"){
  
if_admin("pages"); 
       
$id=intval($id);
    
$qr  = db_query("select * from store_pages where id='$id'");

if(db_num($qr)){
  $data=db_fetch($qr);
  print "<img src='images/arrw.gif'> &nbsp; <a href='pages.php?action=pages'>$phrases[pages]</a> / $data[title] <br><br>";
  
   
    form_start("pages.php");
    form_hidden("action","pages_edit_ok");
    form_hidden("id",$id);
    
    print " <center><table  width=\"90%\"  class=grid> 
          <tr>  <td>
                <b>$phrases[the_title]</b></td><td>";  
    form_text($phrases['the_title'],"title",$data['title']);
    
    print "<tr> <td>
                <b>$phrases[the_content]</b>
                </td>
               <td>";
      if($use_editor_for_pages){ 
          form_editor($phrases['the_content'],'content',$data['content']);
      }else{
          form_textarea($phrases['the_content'],'content',$data['content'],60,10,'ltr');
      }
      
      print "</td></tr>
                 <tr>
                 <td colspan=2 align=center>";
     form_submit($phrases['edit']);
     print "</td></tr></table></center>";
    form_end();
    
    
    /*
      print " <center><table  width=\"90%\"  style=\"border-collapse: collapse\"  class=grid>

                <form method=\"POST\" action=\"pages.php\">

                    <input type=hidden name=\"action\" value='pages_edit_ok'>
                       <input type=hidden name=\"id\" value='$id'>



                        <tr>
                                <td width=\"70\">
                <b>$phrases[the_title]</b></td><td>
                <input type=\"text\" name=\"title\" size=\"29\" value='$data[title]'></td>
                        </tr>


                             <tr> <td width=\"50\">
                <b>$phrases[the_content]</b></td>
                                <td>";
                if($use_editor_for_pages){
                               editor_print_form("content",600,300,"$data[content]");
                }else{
                print "<textarea cols=60 rows=10 name='content' dir=ltr>".htmlspecialchars($data['content'])."</textarea>"; 
                }
                 print "</td></tr>
                 <tr>
                 <td colspan=2 align=center>
                 <input type=\"submit\" value=\"$phrases[edit]\">
                        </td>
                        </tr>






</table>
</form>    </center>\n";   */

}else{
print_admin_table("<center>$phrases[err_wrong_url]</center>");
}
        }
        
//-------------- Pages Add ------------
if($action=="pages_add"){
if_admin("pages"); 


  print "<img src='images/arrw.gif'> &nbsp; <a href='pages.php?action=pages'>$phrases[pages]</a> / $phrases[pages_add] <br><br>";
  
  
  
print "<center><table border=\"0\" width=\"90%\" class=\"grid\">

                <form method=\"POST\" action=\"pages.php\">

                      <input type=hidden name=\"action\" value='pages_add_ok'>



                        <tr>
                                <td width=\"70\">
                <b>$phrases[the_title]</b></td><td >
                <input type=\"text\" name=\"title\" size=\"50\"></td>
                        </tr>



                             <tr> <td width=\"50\">
                <b>$phrases[the_content]</b></td>
                                <td>";
                                
                                if($use_editor_for_pages){
                               editor_print_form("content",600,300,"");
                }else{
                print "<textarea cols=60 rows=10 name='content' dir=ltr></textarea>"; 
                }

                 print "</td></tr>
                 <tr>
                 <td colspan=2 align=center>
                 <input type=\"submit\" value=\"$phrases[add_button]\">
                        </td>
                        </tr>

                </table>

</form>    </center>";
}        

//-----------end ----------------
 require(ADMIN_DIR.'/end.php');