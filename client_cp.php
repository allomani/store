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


   
    //------------------- Profile -------------------------------
    if($action=="profile" || $action=="profile_edit"){
        if(check_member_login()){ 
            //--------------------------------------------------------------------------------------
            if($action=="profile_edit"){

                //------------ update profile info ---------------------



                //---------- email change confirmation -----------
                if(check_email_address($email)){ 
                    if($settings['auto_email_activate']){
                        $email_update_query = ", ::email=':email'" ;
                    }else{   
                        $data_email = members_db_qr_fetch("select ::email,::username from {{store_clients}} where ::id=':id'",array('id'=>intval($member_data['id'])));
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


                members_db_query("update {{store_clients}} set ::country=':country',::birth=':birth' $email_update_query where ::id=':id'",
                        array(
                            'country'=>db_escape($country),
                            'birth'=>db_escape(connector_get_date("$date_y-$date_m-$date_d",'member_birth_date')),
                            'id'=>intval($member_data['id'])      
                            )
                        );


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

             $data = members_db_qr_fetch("select * from {{store_clients}} where ::id=':id'",
                     array(
                         'id'=>intval($member_data['id'])
                         )
                     );

            $birth_data = connector_get_date($data['birth'],"member_birth_array");

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
            </td><td>".$data['username']."</td>  </tr>
            <td width=20%>
            $phrases[email] :
            </td><td ><input type=text name=email value='".$data['email']."' size=30></td>  </tr>
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
                    print get_member_field("custom[$cf]",$dataf,"edit",$data['id']);
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
                    print get_member_field("custom[$cf]",$dataf,"edit",$data['id']);
                    print "</td></tr>";
                    $cf++;
                }
            }

            print "</table>
            </fieldset><br>
            
        
           <fieldset style=\"padding: 2\">
           <input type='checkbox' name='pm_email_notify' value='1' ".iif($data['pm_email_notify'],"checked")."> $phrases[new_pm_email_notify] <br>
           </fieldset>    <br>";


            print "<br><fieldset style=\"padding: 2\"><table width=100%>
            <tr><td  align=center><input type=submit value=' $phrases[edit] '></td></tr>  </table>
            </fieldset></form> ";

            close_table();
        }else{
            login_redirect();
        }
    }
    //--------------


