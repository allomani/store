<?
require("global.php");

if($settings['register_sec_code']){  
require(CWD . '/includes/class_captcha.php');
$sec_img = new captcha('register');
}


require(CWD . "/includes/framework_start.php");   
//---------------------------------------------------------

 compile_hook('register_start');

open_table("$phrases[register]");

  if(!check_member_login()){
  if($settings['members_register']){


//---------- filter fields -----------------
$email = htmlspecialchars($email);
$email_confirm = htmlspecialchars($email_confirm);
$username = htmlspecialchars($username);
$password = htmlspecialchars($password);
$re_password = htmlspecialchars($re_password);

/*
//--------- filter custom_id fields --------------
if(is_array($custom_id)){
 for($i=0;$i<=count($custom_id);$i++){
 $custom_id[$i] = htmlentities($custom_id[$i]);
 }
 }
//--------- filter custom fields --------------
if(is_array($custom)){
 for($i=0;$i<=count($custom);$i++){
 $custom[$i] = htmlentities($custom[$i]);
 }
 }
    */

   if($action=="register_complete_ok"){
      $all_ok = 1 ;

    //---------------- check security image ------------------
   if($settings['register_sec_code']){
   if(!$sec_img->verify_string($sec_string)){
   print  "<li>$phrases[err_sec_code_not_valid]</li>";
    $all_ok = 0 ;
    }
    }

if(check_email_address($email)){
//$email = db_escape($email);

$exsists = db_qr_num("select ".members_fields_replace('id')." from ".members_table_replace('store_clients')." where ".members_fields_replace('email')." like '".db_escape($email)."'",MEMBER_SQL);
      //------------- check email exists ------------
       if($exsists){
                         print "<li>$phrases[register_email_exists]<br>$phrases[register_email_exists2] <a href='index.php?action=forget_pass'>$phrases[click_here] </a></li>";
              $all_ok = 0 ;
           }
      }else{
       print "<li>$phrases[err_email_not_valid]</li>";
      $all_ok = 0;
      }
 //      $username = db_escape($username);

        //------- username min letters ----------
       if(strlen($username) >= $settings['register_username_min_letters']){
              if(strlen($username) <= $settings['username_max_letters']){    
       $exclude_list = explode(",",$settings['register_username_exclude_list']) ;

         if(!in_array($username,$exclude_list)){

     $exsists2 = db_qr_num("select ".members_fields_replace('id')." from ".members_table_replace('store_clients')." where ".members_fields_replace('username')." like '".db_escape($username)."'",MEMBER_SQL);

       //-------------- check username exists -------------
            if($exsists2){
                         print(str_replace("{username}",$username,"<li>$phrases[register_user_exists]</li>"));
                $all_ok = 0 ;
           }
           }else{
           print "<li>$phrases[err_username_not_allowed]</li>";
         $all_ok= 0;
               }
               }else{
         print "<li>$phrases[err_username_max_letters]</li>";
         $all_ok= 0;
          }
          }else{
         print "<li>$phrases[err_username_min_letters]</li>";
         $all_ok= 0;
          }
       //----------------- check required fields ---------------------
        if($email && $email_confirm && $password && $re_password && $username){

        if($password != $re_password){
        print "<li>$phrases[err_passwords_not_match]</li>";
        $all_ok = 0 ;
        }

        if($email != $email_confirm){
        print "<li>$phrases[err_emails_not_match]</li>";
        $all_ok = 0 ;
        }



        }else{
        print  "<li>$phrases[err_fileds_not_complete]</li>";
         $all_ok = 0 ;
            }

//--------------- check required custom fields -------------
if(is_array($custom) && is_array($custom_id)){

   for($i=0;$i<=count($custom);$i++){
   if($custom_id[$i]){
       $m_custom_id=intval($custom_id[$i]);
   $qx = db_qr_fetch("select name,required from store_clients_sets where id='$m_custom_id'");


   if($qx['required']==1 && trim($custom[$i])==""){
   print  "<li>$phrases[err_fileds_not_complete]</li>";
         $all_ok = 0 ;
         break;
       }
   }
   }
   }

//----------------------------------------

 }


 if($all_ok){

if($settings['auto_email_activate']){
    $member_group = $members_connector['allowed_login_groups'][0] ;
    }else{
    $member_group = $members_connector['waiting_conf_login_groups'][0] ;
    }


   db_query("insert into ".members_table_replace('store_clients')." (".members_fields_replace('email').",".members_fields_replace('username').",".members_fields_replace('date').",".members_fields_replace('usr_group').",".members_fields_replace('birth').",".members_fields_replace('country').",".members_fields_replace('gender').",pm_email_notify,privacy_settings,members_list)
  values('".db_escape($email)."','".db_escape($username)."','".connector_get_date(time(),'member_reg_date')."','$member_group','".connector_get_date("$date_y-$date_m-$date_d",'member_birth_date')."','".db_escape($country)."','".db_escape($gender)."','1','$settings[defualt_privacy_settings]','1')",MEMBER_SQL);


    $member_id=db_inserted_id();


//------------- Custom Fields  ------------------
   if(is_array($custom) && is_array($custom_id)){
   for($i=0;$i<=count($custom);$i++){
   if($custom_id[$i] && $custom[$i]){
   $m_custom_id=intval($custom_id[$i]);
   $m_custom_name =$custom[$i] ;
    db_query("update ".members_table_replace('store_clients')." set field_".$m_custom_id."='".db_escape($m_custom_name)."' where ".members_fields_replace('id')."='$member_id'",MEMBER_SQL);
  
       }
   }
   }
//-----------------------------------------------



   connector_member_pwd($member_id,$password,'update');
   connector_after_reg_process();

   if($settings['auto_email_activate']){
       print "<center>  $phrases[reg_complete] </center>";
   }else{
   print "<center>  $phrases[reg_complete_need_activation] </center>";
   snd_email_activation_msg($member_id);
   }

           }else{

 compile_hook('register_before_fields');
print "<script type=\"text/javascript\" language=\"javascript\">
<!--
function pass_ver(theForm){
if ((theForm.elements['email'].value !='') && (theForm.elements['email'].value == theForm.elements['email_confirm'].value)){
if ((theForm.elements['password'].value !='') && (theForm.elements['password'].value == theForm.elements['re_password'].value)){
        if(theForm.elements['username'].value  && theForm.elements['sec_string'].value){
        return true ;
        }else{
       alert (\"$phrases[err_fileds_not_complete]\");
return false ;
}
}else{
alert (\"$phrases[err_passwords_not_match]\");
return false ;
}
}else{
alert (\"$phrases[err_emails_not_match]\");
return false ;
}
}
//-->
</script>

<form action='register.php' method=post onsubmit=\"return pass_ver(this)\">
          <input type=hidden name=action value=register_complete_ok>
          <fieldset style=\"padding: 2\">


          <table width=100%><tr>
            <td width=20%> $phrases[username] :</td><td><input type=text name=username value=\"$username\" onblur=\"ajax_check_register_username(this.value);\"></td><td id='register_username_area'></td> </tr>

           <tr><td colspan=2>&nbsp;</td></tr>
          <tr>  <td>  $phrases[password] : </td><td><input type=password name=password></td>   </tr>
          <tr>  <td>  $phrases[password_confirm] : </td><td><input type=password name=re_password></td>   </tr>


   <tr><td colspan=2>&nbsp;</td></tr>

          <td width=20%>$phrases[email] :</td><td><input type=text name=email value=\"$email\" onblur=\"ajax_check_register_email(this.value);\"></td><td id='register_email_area'></td> </tr>
          <td width=20%>$phrases[email_confirm] :</td><td><input type=text name=email_confirm value=\"$email_confirm\"></td> </tr>

         <tr><td colspan=2>&nbsp;</td></tr>
             </table>
            </fieldset>";

$cf = 0 ;

$qr = db_query("select * from store_clients_sets where required=1 order by ord");
   if(db_num($qr)){
    print "<br><fieldset style=\"padding: 2\">
    <legend>$phrases[req_addition_info]</legend>
<br><table width=100%>";

while($data = db_fetch($qr)){
    print "
    <input type=hidden name=\"custom_id[$cf]\" value=\"$data[id]\">
    <tr><td width=25%><b>$data[name]</b><br>$data[details]</td><td>";
    print get_member_field("custom[$cf]",$data);
        print "</td></tr>";
$cf++;
}
print "</table>
</fieldset>";
}

            print "<br><fieldset style=\"padding: 2\">
    <legend>$phrases[not_req_addition_info]</legend>
<br><table>
  <tr>  <td><b>$phrases[gender] </b> </td><td><select name='gender'>
  <option value=''> $phrases[select_from_menu] </option>
  <option value='male'> $phrases[male] </option>
  <option value='female'> $phrases[female] </option>
  </select>
  </td></tr>

  
    <tr><td><b> $phrases[birth] </b> </td><td><select name='date_d'> <option value='00'></option>";
           for($i=1;$i<=31;$i++){
            if(strlen($i) < 2){$i="0".$i;}
           print "<option value=$i>$i</option>";
           }
           print "</select>
           - <select name=date_m> <option value='00'></option>";
            for($i=1;$i<=12;$i++){
             if(strlen($i) < 2){$i="0".$i;}
           print "<option value=$i>$i</option>";
           }
           print "</select>
           - <select name='date_y'>
           <option value='00'></option>";
           for($i=(date('Y')-10);$i>=(date('Y')-70);$i--){

           print "<option value='$i'>$i</option>";
           }
           print"</select></td></tr>
            <tr>  <td><b>$phrases[country] </b> </td><td><select name=country><option value=''> $phrases[select_from_menu] </option> ";


           $c_qr = db_query("select * from store_countries order by binary name asc");
   while($c_data = db_fetch($c_qr)){


        print "<option value='$c_data[name]' $chk>$c_data[name]</option>";
           }
           print "</select></td></tr>";

           $qr = db_query("select * from store_clients_sets where required=0 order by ord");
   if(db_num($qr)){

while($data = db_fetch($qr)){
    print "
    <input type=hidden name=\"custom_id[$cf]\" value=\"$data[id]\">
    <tr><td width=25%><b>$data[name]</b><br>$data[details]</td><td>";
    print get_member_field("custom[$cf]",$data,"",true);
        print "</td></tr>";
$cf++;
}
}

           print "</table>
           </fieldset>";


           print " <br><fieldset style=\"padding: 2\"><table width=100%><tr>";

           if($settings['register_sec_code']){
           print "<td><b>$phrases[security_code]</b></td><td>".$sec_img->output_input_box('sec_string','size=7')
                   .$sec_img->output_img_box()."
           </td>";
           }

           print "<td align=center><input type=submit value=' $phrases[register_do] '></td></tr>
          </table>
          </fieldset></form>";
    compile_hook('register_after_fields');
            }
        }else{
                print "<center>$phrases[register_closed]</center>";
                }
   }else{
           print "<center> $phrases[registered_before] </center>" ;
           }
           close_table();

 compile_hook('register_end');
           
//---------------------------------------------
require(CWD . "/includes/framework_end.php");  