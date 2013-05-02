<?
require("global.php");

require(CWD . "/includes/framework_start.php");   
//---------------------------------------------------------

if(check_member_login()){
 
 open_table();
   print "
        <center>
        <table width=70%><tr><td align=center><a href='messages.php?action=new'>
        <img src='images/msg_send.gif' title=\"$phrases[send_new_msg]\" border=0><br>$phrases[send_new_msg]</a></td>
        
        <td align=center><a href='messages.php?action=inbox'>
        <img src='images/received_msgs.gif' title=\"$phrases[received_msgs]\" border=0><br>$phrases[received_msgs]</a></td>
        
        <td align=center><a href='messages.php?action=sent'>
        <img src='images/sent_msgs.gif' title=\"$phrases[sent_messages]\" border=0><br>$phrases[sent_messages]</a></td>
        </tr></table></center>";
        
close_table();
   
 //------------------------------------- Messages ---------------------------------------
if(!$action || $action=="inbox" || $action=="sent"){

if($op == "del"){
db_query("delete from store_clients_msgs where id='$id' and owner='$member_data[id]'");
}



open_table(iif($action=="sent",$phrases['sent_messages'],$phrases['received_msgs']));

$qr = db_query("select * from store_clients_msgs where owner='$member_data[id]' and sent=".iif($action=="sent","1","0")." order by id DESC");
$msgs_count = intval(db_num($qr));
     
     
     //-----------------   
     if($action != "sent"){  
     print "<div align=$global_align_x>$msgs_count / $settings[msgs_count_limit] $phrases[used_messages]";
        if($msgs_count >= $settings['msgs_count_limit']){
                print "<b><font color=#FF0000> $phrases[pm_box_full_warning] </font></b>";
                }
      print  "</div><br><br>"; 
     } 
      //------------------
      
      
      if(db_num($qr)){
          print "<table width=100%>";
          
                 print "<tr><td></td><td width=33%><b>".iif($action == "sent",$phrases['to'],$phrases['the_sender'])."</b></td><td width=33% align=center><b>$phrases[the_subject]</b></td><td width=33% align=center><b>$phrases[the_date]</b></td><td><b>  $phrases[the_options] </b></td></tr>";
                while($data = db_fetch($qr)){
          
          
      if($tr_class=="row_1"){
       $tr_class = "row_2";      
      }else{
       $tr_class = "row_1";   
      }
          


         print "<tr class='$tr_class'>
         <td align=center><img src=\"images/".iif(!$data['opened'] && $action !="sent","new_msg.gif","old_msg.gif")."\"></td>
         <td><a href='messages.php?action=view&id=$data[id]'>$data[username]</a></td>    
         <td align=center><a href='messages.php?action=view&id=$data[id]'>$data[title]</a></td>
        
         <td align=center>".date("d-m-Y h:i",$data['date'])."</td>
         <td align=center><a href='messages.php?action=".htmlspecialchars($action)."&op=del&id=$data[id]' onclick=\"return confirm('".$phrases['are_you_sure']."')\" ><img src='images/delete.gif' title=\"$phrases[delete]\"></a></td></tr>";
              }
              print "</table>";
                }else{
                        print "<center>  $phrases[no_messages] </center>" ;
                        }

        close_table();

        }
        //-------------- view ----------------
if($action=="view"){
  $qr = db_query("select * from store_clients_msgs where id='$id' and owner='$member_data[id]'");
    open_table();
    if(db_num($qr)){
    $data = db_fetch($qr);
    db_query("update store_clients_msgs set opened=1 where id='$id'");

   print "<table width=100%>
   <tr><td width=7%><b>  ".iif($data['sent'],$phrases['to'],$phrases['the_sender'])." : </b></td><td><a href=\"".str_replace("{id}","$data[uid]",$links['links_profile'])."\">$data[username]</a></td></tr>
   <tr><td><b> $phrases[the_date] : </b></td><td>".date("d-m-Y h:i",$data['date'])."</td></tr>
   <tr><td><b> $phrases[the_subject] :</b> </td><td>$data[title]</td></tr>
   <tr><td colspan=2 height=25 align=center>
   ".iif(!$data['sent'],"<a href='messages.php?action=reply&msg_id=$data[id]' title='$phrases[reply]'><img alt='$phrases[reply]' src='images/msg_reply.png' border=0></a> &nbsp;&nbsp;")."
   <a href=\"messages.php?action=".iif($data['sent'],"sent","inbox")."&op=del&id=$data[id]\" onclick=\"return confirm('$phrases[are_you_sure]');\" title='$phrases[delete]'><img src='images/del.gif' alt='$phrases[delete]' border=0></a>

   </td></tr>
   <tr><td colspan=2 align=center>
   <table width=96%><tr><td class=messages>
  ".nl2br($data['content'])."
   </td></tr></table>
   </td></tr></table>";
          }else{

                  print "<center> $phrases[err_wrong_url] </center>";

                  }
                   close_table();
}

//-------------- snd ------------------
 if($action=="new" || $action=="reply"){
       $id = (int) $id;
       
if($msg_snd_ok){
                    
$qr = members::db_query("select ::id,::username,::email,::pm_email_notify from {{store_clients}} where ::username=':username'",
        array('username'=>db_escape($to_username)));
if(db_num($qr)){
$data=members::db_fetch($qr);
 
                         $data_count = db_qr_fetch("select count(id) as count from store_clients_msgs where owner='$data[id]'");
                          $msgs_count = $data_count['count'];
                        if($msgs_count >= $settings['msgs_count_limit']){
                        open_table();   
                        print "<center>  $phrases[err_sendto_pm_box_full] </center>";
                        close_table();

                        }else{

                        db_query("insert into store_clients_msgs (owner,uid,username,title,content,date,sent) values('$data[id]','{$member_data['id']}','{$member_data['username']}','".db_escape($to_subject)."','".db_escape($to_msg)."','".time()."','0')");
                        db_query("insert into store_clients_msgs (owner,uid,username,title,content,date,sent) values('{$member_data['id']}','$data[id]','$data[username]','".db_escape($to_subject)."','".db_escape($to_msg)."','".time()."','1')");
                    
                    
                    //---------- send email to receiver ------------
                    if($data['pm_email_notify']){
                      $msg_url = "$scripturl/messages.php";
                      $msg = get_template("pm_email_notify_msg",array("{name_from}","{url}"),array($member_data['username'],$msg_url));
                      $email_result = send_email($sitname,$mailing_email,$data['email'],"$phrases[pm_email_notify_subject]",$msg,$settings['mailing_default_use_html'],$settings['mailing_default_encoding']);
                    }
                      //----------------------------------------------
                      
                      
                        open_table();   
                        print "<center>  $phrases[pm_sent_successfully] </center>";
                        close_table();    
                        }
                        
                   
                        }else{
                            open_table();   
                                print "<center>  $phrases[err_sendto_username_invalid]  </center>";
                                close_table();   
                                }
                        }else{
                   open_table();
                   
                  unset($recevie_user,$to_subject,$to_msg);
                   
                   if($action=="reply"){
                   $msg_id = (int) $msg_id;
                     $data = db_qr_fetch("select * from store_clients_msgs where id='$msg_id'");
                     $recevie_user = $data['username'];
                     $to_subject = " $phrases[reply] : " .$data['title'];
                     $to_msg = "\n\n\n\n--------------------------\n".date("d-m-Y h:i",$data['date'])."\n\n$data[content]";
                     }else{
                     if($id){
                     $from_data = members::db_qr_fetch("select ::username from {{store_clients}} where ::id=':id'",array('id'=>$id));
                     $recevie_user = $from_data['username']  ;
                     }
                     }

           


           print "<form action='messages.php' method=post>
           <input type=hidden name='msg_snd_ok' value=1>
           <input type=hidden name=action value='new'>
           <table width=100%>
           <tr><td width=100> $phrases[username] : </td><td><input type=text name=to_username value='$recevie_user' size=25></td></tr>
                 <tr><td> $phrases[the_subject] : </td><td><input type=text size=25 name=to_subject value='$to_subject'></td></tr>
                       <tr><td> $phrases[the_message] : </td><td>
      <textarea name='to_msg' cols=40 rows=10>$to_msg</textarea>

                     </td></tr>
                       <tr><td colspan=2 align=center><input type=submit value=' $phrases[send] '></td></tr>
                 </table></form>"; 
                  close_table();  
                                }
         
 }
 
//---------------------------------------------
 }else{
  login_redirect();
 }
 
 //---------------------------------------------
require(CWD . "/includes/framework_end.php"); 