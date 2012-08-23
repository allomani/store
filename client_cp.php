<?
    if($action=="favorites" || $action=="favorites_del"){
        if(check_member_login()){ 

            //---- del ----
            if($action=="favorites_del"){
                db_query("delete from store_clients_favorites where id='$id' and userid='$member_data[id]'");
            }
            //------------


            print "<p align=center class=title>$phrases[the_favorite]</p>";

            $qr=db_query("select store_clients_favorites.id as fav_id,store_products_data.* from store_products_data,store_clients_favorites where store_products_data.id=store_clients_favorites.product_id and store_clients_favorites.userid='$member_data[id]' order by store_clients_favorites.id desc");

            if(db_num($qr)){

                $products_count  = db_qr_fetch("select count(store_products_data.id) as count from store_products_data,store_clients_favorites where store_products_data.id=store_clients_favorites.product_id and store_clients_favorites.userid='$member_data[id]' order by store_clients_favorites.id desc");


                //----------------- start pages system ----------------------
                $start=intval($start);
                $page_string= "index.php?action=favorites&start={start}";
                $perpage = intval($settings['products_perpage']);
                //--------------------------------------------------------------


                run_template('browse_products_header');  
                $c=0;
                while($data = db_fetch($qr)){

                    $data_cat = db_qr_fetch("select id,name from store_products_cats where id='$data[cat]'");  

                    if ($c==$settings['img_cells']) {
                      run_template('browse_products_spect');  
                        $c = 0 ;
                    }
                  

                    run_template('browse_products');

                $c++;
                }
                run_template('browse_products_footer');  



                //-------------------- pages system ------------------------
                print_pages_links($start,$products_count['count'],$perpage,$page_string); 
                //-----------------------------

            }else{

                open_table();    
                print "<center> $phrases[no_products] </center>";
                close_table();

            }


        }else{
            login_redirect();
        }
    }


    //------------------------------------- Messages ---------------------------------------
    if($action=="msgs" || $action=="msg_del"){
        if(check_member_login()){ 
            if($action == "msg_del"){
                db_query("delete from store_clients_msgs where id='$id' and user='$member_data[id]'");
            }

            open_table($phrases['the_messages']);
            $qr = db_query("select * from store_clients_msgs where user='$member_data[id]' order by id DESC");
            $msgs_count = db_num($qr);
            print "<table width=100%><tr><td align=right><a href='index.php?action=msg_snd'>
            <img src='$style[images]/mail_send.gif' alt=' $phrases[send_new_msg]' border=0> &nbsp; $phrases[send_new_msg]</a></td><td align=left>$msgs_count / $settings[msgs_count_limit] $phrases[used_messages]</td></tr>";

            if($msgs_count >= $settings['msgs_count_limit']){
                print "<tr><td colspan=2 align=center><b><font color=#FF0000> $phrases[pm_box_full_warning] </font></b></td></tr>";
            }

            if(db_num($qr)){
                print "<tr><td width=33%><b>$phrases[the_sender]</b></td><td width=33% align=center><b>$phrases[the_subject]</b></td><td width=33% align=center><b>$phrases[the_date]</b></td><td><b>  $phrases[the_options] </b></td></tr>";
                while($data = db_fetch($qr)){

                    if($tr_class == "row_1"){
                        $tr_class = "row_2"; 
                    }else{
                        $tr_class = "row_1"; 
                    }


                    print "<tr class='$tr_class'><td height=30><a href='index.php?action=msg_view&id=$data[id]'>
                    $data[sender]</a></td>
                    <td align=center><a href='index.php?action=msg_view&id=$data[id]'>".htmlspecialchars($data['title']).iif(!$data['opened'],"&nbsp;<font color=red><b>$phrases[new]<b></font>")."</a></td>
                    <td align=center> $data[date]</td>
                    <td align=center><a href='index.php?action=msg_del&id=$data[id]' onclick=\"return confirm('".$phrases['are_you_sure']."')\" >$phrases[delete]</a></td></tr>";
                }
            }else{
                print "<tr><td colspan=2 align=center>  $phrases[no_messages] </td></tr>" ;
            }
            print "</table>";
            close_table();
        }else{
            login_redirect();
        }
    }
    //-------------- view ----------------
    if($action=="msg_view"){
        if(check_member_login()){ 
            $qr = db_query("select * from store_clients_msgs where id='$id' and user='$member_data[id]'");
            open_table();
            if(db_num($qr)){
                $data = db_fetch($qr);
                db_query("update store_clients_msgs set opened=1 where id='$id'");

                print "<table width=100%>
                <tr><td width=7%><b>  $phrases[the_sender] : </b></td><td>$data[sender]</td></tr>
                <tr><td><b> $phrases[the_date] : </b></td><td>$data[date]</td></tr>
                <tr><td><b> $phrases[the_subject] :</b> </td><td>".htmlspecialchars($data['title'])."</td></tr>
                <tr><td colspan=2 height=25 align=center>

                <table width=300><tr><td align=center>
                <a href='index.php?action=msg_reply&msg_id=$data[id]'><img title='$phrases[reply]' src='$style[images]/mail_reply.gif' border=0><br>$phrases[reply]</a> 
                </td>
                <td align=center>
                <a href='index.php?action=msg_snd'><img src='$style[images]/mail_send.gif' title='$phrases[send_new_msg]' border=0><br>$phrases[send_new_msg]</a> 
                </td>
                <td align=center>
                <a href=\"index.php?action=msg_del&id=$data[id]\" onclick=\"return confirm('$phrases[are_you_sure]');\"><img src='$style[images]/mail_delete.gif' title='$phrases[delete]' border=0><br>$phrases[delete]</a>
                </td></tr></table>

                </td></tr>
                <tr><td colspan=2 align=center>
                <table width=96%><tr><td class='messages'>
                ".nl2br(htmlspecialchars($data['content']))."
                </td></tr></table>
                </td></tr></table>";
            }else{

                print "<center> $phrases[err_wrong_url] </center>";

            }
            close_table();
        }else{
            login_redirect();
        }
    }
    //-------------- snd ------------------
    if($action=="msg_snd" || $action=="msg_reply"){
        if(check_member_login()){ 
            open_table();
            if($msg_snd_ok){
                $qr = db_query("select ".members_fields_replace("id")." from ".members_table_replace("store_clients")." where ".members_fields_replace("username")."='".db_escape($to_username)."'");
                if(db_num($qr)){
                    $data=db_fetch($qr);

                    $data_count = db_qr_fetch("select count(id) as count from store_clients_msgs where user='$data[id]'");
                    $msgs_count = $data_count['count'];
                    if($msgs_count >= $settings['msgs_count_limit']){
                        print "<center>  $phrases[err_sendto_pm_box_full] </center>";

                    }else{

                        db_query("insert into store_clients_msgs (user,sender,title,content,date) values('$data[id]','".db_escape($member_data['username'])."','".db_escape($to_subject)."','".db_escape($to_msg)."',now())");
                        print "<center>  $phrases[pm_sent_successfully] </center>";
                    }
                }else{
                    print "<center>  $phrases[err_sendto_username_invalid]  </center>";
                }
            }else{

                if($action=="msg_reply"){
                    $msg_id = (int)$msg_id;
                    $id = (int) $id;


                    $data = db_qr_fetch("select * from store_clients_msgs where id='$msg_id'");

                    $recevie_user = $data['sender'];
                    $to_subject = " $phrases[reply] : " .$data['title'];
                    $to_msg = "\n\n -------------------------- \n $data[date] \n\n $data[content]";
                }else{


                    if($id){
                        $from_data = db_qr_fetch("select ".members_fields_replace("username")." from ".members_table_replace("store_clients")." where ".members_fields_replace("id")."='$id'");
                        $recevie_user = $from_data['username']  ;
                    }else{
                        $recevie_user = "";
                        $to_subject = "";
                        $to_msg = ""; 
                    }

                }




                print "<form action=index.php method=post>
                <input type=hidden name=msg_snd_ok value=1>
                <input type=hidden name=action value='msg_snd'>
                <table width=100%>
                <tr><td width=100> $phrases[username] : </td><td><input type=text name='to_username' value='$recevie_user' size=25></td></tr>
                <tr><td> $phrases[the_subject] : </td><td><input type=text size=25 name=to_subject value='$to_subject'></td></tr>
                <tr><td> $phrases[the_message] : </td><td>
                <textarea name='to_msg' cols=40 rows=10>$to_msg</textarea>

                </td></tr>
                <tr><td colspan=2 align=center><input type=submit value=' $phrases[send] '></td></tr>
                </table></form>";
            }
            close_table();
        }else{
            login_redirect();
        }
    }
    //------------------- Profile -------------------------------
    if($action=="profile" || $action=="profile_edit"){
        if(check_member_login()){ 
            //--------------------------------------------------------------------------------------
            if($action=="profile_edit"){

                //------------ update profile info ---------------------



                //---------- email change confirmation -----------
                if(check_email_address($email)){ 
                    if($settings['auto_email_activate']){
                        $email_update_query = ", ".members_fields_replace("email")."='".db_escape($email)."'" ;
                    }else{   
                        $data_email = db_qr_fetch("select ".members_fields_replace('email').",".members_fields_replace('username')." from ".members_table_replace("store_clients")." where ".members_fields_replace("id")."='".intval($member_data['id'])."'",MEMBER_SQL);
                        if($email != $data_email['email']){
                            $val_code = md5($email.$data_email['email'].time().rand(0,100));    
                            db_query("insert into store_confirmations (type,old_value,new_value,cat,code) values ('member_email_change','".$data_email['email']."','".db_escape($email)."','".intval($member_data['id'])."','$val_code')");
                            snd_email_chng_conf($data_email['username'],$email,$val_code);
                            open_table();
                            print "<center> $phrases[chng_email_conf_msg_sent] </center>";
                            close_table();
                        }
                        $email_update_query = "";
                    }
                }else{
                    open_table();
                    print "<center>$phrases[err_email_not_valid]</center>";
                    close_table();
                    $email_update_query = "";
                }
                //------------------


                db_query("update ".members_table_replace("store_clients")." set ".members_fields_replace("country")."='".db_escape($country)."',".members_fields_replace("birth")."='".db_escape(connector_get_date("$date_y-$date_m-$date_d",'member_birth_date'))."'
                    $email_update_query where ".members_fields_replace("id")."='".intval($member_data['id'])."'",MEMBER_SQL);


                //-------- if change password --------------
                if ($password){
                    if($password == $re_password){
                        connector_member_pwd($member_data['id'],$password,'update');
                    }else{
                        open_table();
                        print "<center>$phrases[err_passwords_not_match]</center>";
                        close_table();
                    }
                }

                //------------- Custom Fields  ------------------
                if(is_array($custom) && is_array($custom_id)){
                    for($i=0;$i<=count($custom);$i++){
                        if($custom_id[$i] && $custom[$i]){
                            $m_custom_id=intval($custom_id[$i]);
                            $m_custom_name =$custom[$i] ;

                            $qr = db_query("select id from store_clients_fields where cat='$m_custom_id' and member='".intval($member_data['id'])."'");
                            if(db_num($qr)){
                                db_query("update store_clients_fields set value='".db_escape($m_custom_name)."' where cat='$m_custom_id' and member='".intval($member_data['id'])."'");
                            }else{
                                db_query("insert into store_clients_fields (member,cat,value) values('".intval($member_data['id'])."','$m_custom_id','".db_escape($m_custom_name)."')");
                            }

                        }
                    }
                }

                open_table(); 
                print "<center>  $phrases[your_profile_updated_successfully] </center>";

                close_table();
            }


            open_table($phrases['the_profile']);

            $data = db_qr_fetch("select * from ".members_table_replace("store_clients")." where ".members_fields_replace("id")."='".intval($member_data['id'])."'",MEMBER_SQL);


            $birth_data = connector_get_date($data[members_fields_replace('birth')],"member_birth_array");

            print "
            <script type=\"text/javascript\" language=\"javascript\">
            <!--
            function pass_ver(theForm){
            if (theForm.elements['password'].value == theForm.elements['re_password'].value){

            if(theForm.elements['email'].value){
            return true ;
            }else{
            alert (\"$phrases[err_fileds_not_complete]\");
            return false ;
            }
            }else{
            alert (\"$phrases[err_passwords_not_match]\");
            return false ;
            }
            }
            //-->
            </script>


            <form action=index.php method=post onsubmit=\"return pass_ver(this)\">
            <input type=hidden name=action value=profile_edit>


            <fieldset style=\"padding: 2\">
            <table width=100%><tr>
            <td width=20%>
            $phrases[username] :
            </td><td>".$data[members_fields_replace('username')]."</td>  </tr>
            <td width=20%>
            $phrases[email] :
            </td><td ><input type=text name=email value='".$data[members_fields_replace('email')]."' size=30></td>  </tr>
            </tr></table>
            </fieldset>
            <br>
            <fieldset style=\"padding: 2\">
            <table width=100%><tr> 
            <tr>  <td>  $phrases[password] : </td><td><input type=password name=password></td>   </tr>
            <tr>  <td>  $phrases[password_confirm] : </td><td><input type=password name=re_password></td>   </tr>
            <tr><td colspan=2><font color=#D90000>*  $phrases[leave_blank_for_no_change] </font></td></tr>
            </tr></table></fieldset>";

            $cf = 0 ;

            $qrf = db_query("select * from store_clients_sets where required=1 order by ord");
            if(db_num($qrf)){
                print "<br><fieldset style=\"padding: 2\">
                <legend>$phrases[req_addition_info]</legend>
                <br><table width=100%>";

                while($dataf = db_fetch($qrf)){
                    print "
                    <input type=hidden name=\"custom_id[$cf]\" value=\"$dataf[id]\">
                    <tr><td width=25%><b>$dataf[name]</b><br>$dataf[details]</td><td>";
                    print get_member_field("custom[$cf]",$dataf,"edit",$data[members_fields_replace('id')]);
                    print "</td></tr>";
                    $cf++;
                }
                print "</table>
                </fieldset>";
            }

            print "<br><fieldset style=\"padding: 2\">
            <legend>$phrases[not_req_addition_info]</legend>
            <br><table width=100%>
            <tr><td><b> $phrases[birth] </b> </td><td><select name='date_d'>";
            for($i=1;$i<=31;$i++){
                if(strlen($i) < 2){$i="0".$i;}
                if($birth_data['day'] == $i){$chk="selected" ; }else{$chk="";}
                print "<option value=$i $chk>$i</option>";
            }
            print "</select>
            - <select name=date_m>";
            for($i=1;$i<=12;$i++){
                if(strlen($i) < 2){$i="0".$i;}
                if($birth_data['month'] == $i){$chk="selected" ; }else{$chk="";}
                print "<option value=$i $chk>$i</option>";
            }
            print "</select>
            - <input type=text size=3 name='date_y' value='$birth_data[year]'></td></tr>
            <tr>  <td><b>$phrases[country] </b> </td><td><select name=country><option value=''></option>";
            $c_qr = db_query("select * from store_countries order by binary name asc");
            while($c_data = db_fetch($c_qr)){

                if($data['country']==$c_data['name']){$chk="selected";}else{$chk="";}
                print "<option value='$c_data[name]' $chk>$c_data[name]</option>";
            }
            print "</select></td>   </tr>";

            $qrf = db_query("select * from store_clients_sets where required=0 order by ord");
            if(db_num($qrf)){

                while($dataf = db_fetch($qrf)){
                    print "
                    <input type=hidden name=\"custom_id[$cf]\" value=\"$dataf[id]\">
                    <tr><td width=25%><b>$dataf[name]</b><br>$dataf[details]</td><td>";
                    print get_member_field("custom[$cf]",$dataf,"edit",$data[members_fields_replace('id')]);
                    print "</td></tr>";
                    $cf++;
                }
            }

            print "</table>
            </fieldset>";


            print "<br><fieldset style=\"padding: 2\"><table width=100%>
            <tr><td  align=center><input type=submit value=' $phrases[edit] '></td></tr>  </table>
            </fieldset></form> ";

            close_table();
        }else{
            login_redirect();
        }
    }
    //--------------

