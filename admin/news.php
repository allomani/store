<?
    if(!defined('IS_ADMIN')){die('No Access');} 
 
// ------------------------------- News ----------------------------------------
 if ($action == "news" || $action=="del_news" || $action=="edit_news_ok" || $action=="add_news"){

 if_admin("news");

if($action=="add_news"){
if($auto_preview_text){
                $content = getPreviewText($details);
}
                
//----- filter XSS Tages -------
include_once(CWD . "/includes/class_inputfilter.php");
$Filter = new InputFilter(array(),array(),1,1);
$details = $Filter->process($details);
//------------------------------

         db_query("insert into store_news(title,writer,content,details,date,img)values('".db_escape($title)."','".db_escape($writer)."','".db_escape($content,false)."','".db_escape($details,false)."',now(),'".db_escape($img)."')");
        }
        //-------------delete-------
    if ($action=="del_news"){
          db_query("delete from store_news where id='$id'");
            }
            //----------edit--------------------
            if ($action=="edit_news_ok"){
            if($auto_preview_text){
                $content = getPreviewText($details);
                }

//----- filter XSS Tages -------
include_once(CWD . "/includes/class_inputfilter.php");
$Filter = new InputFilter(array(),array(),1,1);
$details = $Filter->process($details);
//------------------------------

                db_query("update store_news set title='".db_escape($title)."',writer='".db_escape($writer)."',content='".db_escape($content,false)."',details='".db_escape($details,false)."',img='".db_escape($img)."' where id='$id'");

                    }
                  //-----------------------------


                print "<p align=center class=title>$phrases[the_news]</p>
                <p align=$global_align><a href='index.php?action=news_add'><img src='images/add.gif' border=0>$phrases[news_add]</a></p>";

       $qr=db_query("select * from store_news order by id DESC")   ;

       if (db_num($qr)){
           print "<br><center><table border=0 width=\"90%\"   cellpadding=\"0\" cellspacing=\"0\" class=\"grid\">";


         while($data= db_fetch($qr)){
     print "            <tr>
                <td>$data[title]</td>

                <td  width=\"254\"><a href='index.php?action=edit_news&id=$data[id]'>$phrases[edit] </a> - <a href='index.php?action=del_news&id=$data[id]' onClick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete]</a></td>
        </tr>";

                 }

                print" </table><br>\n";
                }else{
                        print "<center> $phrases[no_news] </center>";
                        }

}

//-------------- Edit News ----------------
if($action == "edit_news"){

    if_admin("news");
   $id=intval($id);
  $data=db_qr_fetch("select * from store_news where id='$id'");

      print " <center>
                <table border=0 width=\"80%\"  style=\"border-collapse: collapse\" class=grid><tr>

                <form method=\"POST\" action=\"index.php\" name='sender'>

                    <input type=hidden name=\"action\" value='edit_news_ok'>
                       <input type=hidden name=\"id\" value='$id'>



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

                                <td> <a href=\"javascript:uploader('news','img');\"><img src='images/file_up.gif' border=0 alt='$phrases[upload_file]'></a>
                                 </td></tr></table>

                                 </td></tr>


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
                 <tr><td colspan=2 align=center>  <input type=\"submit\" value=\"$phrases[edit]\">  </td></tr>




                </table>

</form>    </center>\n";

        }
//------------------ News Add -------------------
if($action=="news_add"){

    if_admin("news");

print "<center>
                <table border=0 width=\"90%\"  style=\"border-collapse: collapse\" class=grid><tr>

                <form name=sender method=\"POST\" action=\"index.php\">

                      <input type=hidden name=\"action\" value='add_news'>



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
                                <input type=\"text\" name=\"img\" size=\"50\" dir=ltr>  </td><td> <a href=\"javascript:uploader('news','img');\"><img src='images/file_up.gif' border=0 alt='$phrases[upload_file]'></a>
                                 </td></tr></table>
                                 </td></tr>
                                          <tr> <td width=\"100\">
                <b>$phrases[the_details]</b></td>
                                <td>";
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