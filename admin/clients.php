<?
 require('./start.php'); 
 

//------------------------------- Email Members -----------------------------------
if($action=="mailing"){
if_admin("clients");
$username = htmlspecialchars($username) ; 
print "<p align=center class=title> $phrases[members_mailing] </p><br>" ;

 print "<center><iframe src='mailing.php?username=$username' width=95% height=900  border=0 frameborder=0></iframe></center>";
        }


//---------------- Members Search  ------------------------------
 if($action == "search"){

if_admin("clients");

$limit = intval($limit);
$start  = intval($start);

//-------- check remote and local db connection ------
if($members_connector['enable']){
$srch_remote_db = $members_connector['db_name'];
$srch_local_db = $db_name ;
}else{
$srch_remote_db = $db_name ;
$srch_local_db = $db_name ;
}


 print "<p align=center class=title> $phrases[the_clients] </p>
             ";

if($date_y || $date_m || $date_d){

   $birth_struct =  iif($date_y,$date_y."-","0000-").iif($date_m,$date_m."-","01-").iif($date_d,$date_d,"01");
  // print $birth_struct;

$birth = connector_get_date($birth_struct,'member_birth_date');
//print $birth;
    }else{
$birth = "";
}

$cond = "::username like '%".db_escape($username)."%' and ::email like '%".db_escape($email)."%' ";

$cond .= "and ::birth like '%".db_escape($birth)."%' and ::country like '%".db_escape($country)."%'";

$c_custom = 0 ;

//------------- Custom Fields  ------------------
   if(is_array($custom) && is_array($custom_id)){

   for($i=0;$i<=count($custom_id);$i++){
   if($custom_id[$i] & $custom[$i] ){
   $m_custom_id=intval($custom_id[$i]);
   $m_custom_name =$custom[$i] ;
if(trim($m_custom_id) && trim($m_custom_name)){
    $c_custom++;
$cond .= " and field_".$m_custom_id." like '%".db_escape($m_custom_name)."%'";
}

       }
       }
  $cond .= " ";
   }



$sql= "select * from {{store_clients}} where ".$cond ." limit $start,$limit";
$page_result_sql = "select count(*) as count from {{store_clients}} where ".$cond;

$qr = members_db_query($sql);


 if(db_num($qr)){

$page_result = members_db_qr_fetch($page_result_sql);
 print "<b> $phrases[view]  </b>".($start+1)." - ".($start+$limit) . "<b> $phrases[from] </b> $page_result[count]<br><br>";


$numrows=$page_result['count'];
$previous_page=$start - $m_perpage;
$next_page=$start + $m_perpage;
$m_perpage = $limit ;

$query_string = $_SERVER['QUERY_STRING'];
$query_string = iif(strchr($query_string,"&start="),$query_string,$query_string."&start=0");
$page_string = htmlspecialchars("clients.php?".substr($query_string,0,strpos($query_string,"&start="))."&start={start}"); 




 print " 


      <table width=100% class=grid><tr>
      <td><b>$phrases[username]</b></td><td><b>$phrases[email]</b></td>
 <td><b>$phrases[birth]</b></td>
 <td><b>$phrases[register_date]</b></td><td><b>$phrases[last_login]</b></td></tr>";
 while($data = db_fetch($qr)){
 print "<tr><td><a href='clients.php?action=edit&id=".$data['id']."'>$data[username]</td>
 </td><td>".$data['email']."</td>
 <td>".$data['birth']."</td>
 <td>".member_time_replace($data['date'])."</td>
 <td>".member_time_replace($data['last_login'])."</td>
 </tr>";

         }
         print "</table>";

         
 //-------------------- pages system ------------------------
print_pages_links($start,$numrows,$limit,$page_string); 


         }else{

                print_admin_table("<center>$phrases[no_results] </center>");
                 }



        }

//------------------------- Memebers Operations ---------------------------------
if(!$action || $action=="clients" || $action=="add_ok" || $action=="edit_ok" || $action=="del"){
if_admin("clients");

if($action=="add_ok"){

    $all_ok = 1;
 if(check_email_address($email)){

$exsists = members_db_qr_fetch("select count(*) as count from {{store_clients}} where ::email=':email'",array('email'=>db_escape($email)));
      //------------- check email exists ------------
       if($exsists['count']){
                         print "<li>$phrases[register_email_exists]<br>$phrases[register_email_exists2] <a href='index.php?action=forget_pass'>$phrases[click_here] </a></li>";
              $all_ok = 0 ;
           }
      }else{
       show_alert("$phrases[err_email_not_valid]","error");
      $all_ok = 0;
      }
    

        //------- username min letters ----------
       if(mb_strlen($username,'utf-8') >= $settings['register_username_min_letters']){
       $exclude_list = explode(",",$settings['register_username_exclude_list']) ;

         if(!in_array($username,$exclude_list)){

     $exsist2 = members_db_qr_fetch("select count(*) as count from {{store_clients}} where ::username=':username'",array('username'=>db_escape($username)));

       //-------------- check username exists -------------
            if($exsist2['count']){
                         print(str_replace("{username}",$username,"<li>$phrases[register_user_exists]</li>"));
                $all_ok = 0 ;
           }
           }else{
           show_alert("$phrases[err_username_not_allowed]","error");
         $all_ok= 0;
               }
          }else{
         show_alert("$phrases[err_username_min_letters]","error");
         $all_ok= 0;
          }
if($all_ok){
if($username && $email && $password){


 members_db_query("insert into {{store_clients}} (::username,::email,::country,::birth,::usr_group,::date)
 values(':username',':email',':country',':birth',':usr_group',':date')",
         array(
             'username'=>db_escape($username),
             'email'=>db_escape($email),
             'country'=>db_escape($country),
             'birth'=>connector_get_date("$date_y-$date_m-$date_d",'member_birth_date'),
             'usr_group'=>$usr_group,
             'date'=>connector_get_date(date("Y-m-d H:i:s"),'member_reg_date')
             )
         );


 $member_id=db_inserted_id();

//------------- Custom Fields  ------------------
   if(is_array($custom) && is_array($custom_id)){
   for($i=0;$i<=count($custom);$i++){
   if($custom_id[$i]){
   $m_custom_id=intval($custom_id[$i]);
   $m_custom_name =$custom[$i] ;
   members_db_query("update {{store_clients}} set field_".$m_custom_id."='".db_escape($m_custom_name,false)."' where ::id=':id'",
          array('id'=>$member_id)
        );
       }
   }
   }
//-----------------------------------------------


connector_member_pwd($member_id,$password,'update');

 show_alert("$phrases[member_added_successfully]","success");

}else{
 show_alert("$phrases[please_fill_all_fields]","error");
}
}
        }

//------ delete memeber query --------
if($action == "del"){
members_db_query("delete from {{store_clients}} where ::id=':id'",array('id'=>$id));

show_alert( "$phrases[client_deleted_successfully]","success");
        }


 if($action == "edit_ok"){

members_db_query("update {{store_clients}} set ::username=':username',::email=':email',::country=':country',
    ::birth=':birth',::usr_group=':usr_group'  where ::id=':id'",
        array(
            'username'=>db_escape($username),
            'email'=>db_escape($email),
            'country'=>db_escape($country),
            'birth'=>connector_get_date("$date_y-$date_m-$date_d",'member_birth_date'),
            'usr_group'=>$usr_group,
            'id'=>$id
            ));

 //-------- if change password --------------
          if ($password){
              if($password == $re_password){
               connector_member_pwd($id,$password,'update');
              }else{

              show_alert("$phrases[err_passwords_not_match]","error");

              }
           }

//------------- Custom Fields  ------------------
   if(is_array($custom) && is_array($custom_id)){
   for($i=0;$i<=count($custom);$i++){
   if($custom_id[$i]){
   $m_custom_id=intval($custom_id[$i]);
   $m_custom_name =$custom[$i] ;
  members_db_query("update {{store_clients}} set field_".$m_custom_id."='".db_escape($m_custom_name,false)."' where ::id=':id'",
          array('id'=>$id)
        );
       }
   }
   }

   show_alert("$phrases[member_edited_successfully]","success");
         }

//---------- show members search form ---------
print "<p align=center class=title> $phrases[the_members] </p>
        <p align=$global_align><a href='clients.php?action=add' class='add'>$phrases[add_member] </a></p>
           
     <form action='clients.php' method=get>
      <fieldset>
      <table width=100%>
   <input type=hidden name='action' value='search'>

   <tr><td> $phrases[username] : </td><td><input type=text name=username size=30></td></tr>
   <tr><td> $phrases[email]  : </td><td><input type=text name=email size=30></td></tr>";
    print "</table>
</fieldset>";

      print "<br><br><fieldset>
<table width=100%>
    <tr><td><b> $phrases[birth] </b> </td><td>
    <input type=text size=1 name='date_d'> - <input type=text size=1 name='date_m'> - <input type=text size=4 name='date_y'></td></tr>

            <tr>  <td><b>$phrases[country] </b> </td><td><select name=country><option value=''></option>";
            $c_qr = db_query("select * from store_countries order by name asc");
   while($c_data = db_fetch($c_qr)){


        print "<option value='$c_data[code]'>$c_data[name]</option>";
           }
           print "</select></td>   </tr></table></fieldset>";

   $cf = 0 ;

   //------------ custom fields -----
   if(!$members_connector['enable'] || $members_connector['same_connection']){
$qr = db_query("select * from store_clients_sets order by required,ord");
   if(db_num($qr)){
    print "<br><br><fieldset>
    <legend>$phrases[addition_fields] </legend>
<br><table width=100%>";

while($data = db_fetch($qr)){
    print "
    <input type=hidden name=\"custom_id[$cf]\" value=\"$data[id]\">
    <tr><td width=25%><b>$data[name]</b><br>$data[details]</td><td>";
    print get_member_field("custom[$cf]",$data,"",true);
        print "</td></tr>";
$cf++;
}
print "</table>
</fieldset>";
}
   }

   print "<br><br><fieldset>
      <table width=100%>

      <tr><td width=30%>$phrases[records_perpage]</td><td><input type=text name=limit size=3 value='30'></td><td align=center><input type='submit' value=' $phrases[search_do] '></td></tr>
  </table></fieldset>
   <input type=hidden name=start value=\"0\">
   </form>" ;
        }
 //-----------------------------------------------------
if($action=="edit"){
   if_admin("members");

           $qr = members_db_query("select * from {{store_clients}} where ::id=':id'",array('id'=>$id));

    if(db_num($qr)){
                   $data = members_db_fetch($qr);
          $birth_data = connector_get_date($data['birth'],"member_birth_array");
           print "
                   <script type=\"text/javascript\" language=\"javascript\">
<!--
function pass_ver(theForm){
 if (theForm.elements['password'].value == theForm.elements['re_password'].value){

        if(theForm.elements['email'].value && theForm.elements['username'].value){
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

<ul class='nav-bar'>
<li><a href='clients.php'>$phrases[the_clients]</a></li>
<li>$phrases[edit]</li>
</ul>

          <p class=title align=center>  $phrases[member_edit] </p>

           <form action='clients.php' method=post onsubmit=\"return pass_ver(this)\">
          <input type=hidden name=action value='edit_ok'>
          <input type=hidden name=id value='".intval($id)."'>

          <table class=grid><tr><td>
          <a href='clients.php?action=mailing&username=".$data['username']."'>$phrases[send_msg_to_client] </a> -
          <a href='orders.php?op=search&username=".$data['username']."'>$phrases[find_client_orders] </a> 
          
          </td></tr></table><br>
          
          <fieldset><table width=100%>

     <tr>
          <td width=20%>
         $phrases[username] :
          </td><td ><input type=text name=username value='".$data['username']."'></td>  </tr>
           <td width=20%>
          $phrases[email] :
          </td><td ><input type=text name=email value='".$data['email']."' size=30></td>  </tr>
          <tr>  <td>  $phrases[password] : </td><td><input type=password name=password></td>   </tr>
          <tr>  <td>  $phrases[password_confirm] : </td><td><input type=password name=re_password></td>   </tr>
         <tr><td colspan=2><font color=#D90000>*  $phrases[leave_blank_for_no_change] </font></td></tr>
             <tr><td colspan=2>&nbsp;</td></tr>




 <tr>   <td>$phrases[member_acc_type] : </td><td>";
 $usrs_groups = get_members_groups_array();
 
                print_select_row("usr_group",$usrs_groups,$data['usr_group']);
                   
            print "</td>     </tr>
</table></fieldset>";

 $cf = 0 ;

$qrf = db_query("select * from store_clients_sets where required=1 order by ord");
   if(db_num($qrf)){
    print "<br><fieldset>
    <legend>$phrases[req_addition_info]</legend>
<br><table width=100%>";

while($dataf = db_fetch($qrf)){
    print "
    <input type=hidden name=\"custom_id[$cf]\" value=\"$dataf[id]\">
    <tr><td width=25%><b>$dataf[name]</b><br>$dataf[details]</td><td>";
    print get_member_field("custom[$cf]",$dataf,$data["field_".$dataf['id']]);
        print "</td></tr>";
$cf++;
}
print "</table>
</fieldset>";
}

            print "<br><fieldset>
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
            $c_qr = db_query("select * from store_countries order by name asc");
   while($c_data = db_fetch($c_qr)){

       print "<option value='$c_data[code]'".iif($data['country']==$c_data['code']," selected").">$c_data[name]</option>";
           }
           print "</select></td>   </tr>";

           $qrf = db_query("select * from store_clients_sets where required=0 order by ord");
   if(db_num($qrf)){

while($dataf = db_fetch($qrf)){
    print "
    <input type=hidden name=\"custom_id[$cf]\" value=\"$dataf[id]\">
    <tr><td width=25%><b>$dataf[name]</b><br>$dataf[details]</td><td>";
    print get_member_field("custom[$cf]",$dataf,$data["field_".$dataf['id']]);
        print "</td></tr>";
$cf++;
}
}

           print "</table>
           </fieldset>";


          print "<br><br><fieldset><table width=100%>

           <tr><td align=center><input type=submit value=' $phrases[edit] '></td></tr>
                     <tr><td align=left> <a href='clients.php?action=del&id=$id' onclick=\"return confirm('".$phrases['are_you_sure']."');\">$phrases[delete]</a></td></tr>
          </tr></table></fieldset>
         </form> ";
         }else{
                 print "<center>  $phrases[this_member_not_exists] </center>";
                 }
        }
 //------------------------- add member --------
 if($action=="add"){
   if_admin("clients");

           print "
<script type=\"text/javascript\" language=\"javascript\">

function pass_ver(theForm){
 if (theForm.elements['password'].value == theForm.elements['re_password'].value){

        if(theForm.elements['email'].value && theForm.elements['username'].value){
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

<ul class='nav-bar'>
<li><a href='clients.php'>$phrases[the_clients]</a></li>
<li>$phrases[add]</li>
</ul>
          <p class=title align=center>  $phrases[add_member] </p> 
              

<table class=grid>

           <form action='clients.php' method=post onsubmit=\"return pass_ver(this)\">
          <input type=hidden name=action value='add_ok'>

     <tr>
          <td width=20%>
         $phrases[username] :
          </td><td ><input type=text name=username></td>  </tr>
           <td width=20%>
          $phrases[email] :
          </td><td ><input type=text name=email size=30></td>  </tr>
          <tr>  <td>  $phrases[password] : </td><td><input type=password name=password></td>   </tr>
          <tr>  <td>  $phrases[password_confirm] : </td><td><input type=password name=re_password></td>   </tr>

             <tr><td colspan=2>&nbsp;</td></tr>

             <tr>   <td>$phrases[member_acc_type] : </td><td>";
              print_select_row("usr_group",get_members_groups_array());


            print "
            </td>     </tr>
            </table>";

   $cf = 0 ;

$qrf = db_query("select * from store_clients_sets where required=1 order by ord");
   if(db_num($qrf)){
    print "<br><fieldset>
    <legend>$phrases[req_addition_info]</legend>
<br><table width=100%>";

while($dataf = db_fetch($qrf)){
    print "
    <input type=hidden name=\"custom_id[$cf]\" value=\"$dataf[id]\">
    <tr><td width=25%><b>$dataf[name]</b><br>$dataf[details]</td><td>";
    print get_member_field("custom[$cf]",$dataf);
        print "</td></tr>";
$cf++;
}
print "</table>
</fieldset>";
}

            print "<br><fieldset>
    <legend>$phrases[not_req_addition_info]</legend>
<br><table width=100%>
    <tr><td><b> $phrases[birth] </b> </td><td><select name='date_d'>";
    for($i=1;$i<=31;$i++){
             if(strlen($i) < 2){$i="0".$i;}

           print "<option value=$i>$i</option>";
           }
           print "</select>
           - <select name=date_m>";
            for($i=1;$i<=12;$i++){
                    if(strlen($i) < 2){$i="0".$i;}

           print "<option value=$i>$i</option>";
           }
           print "</select>
           - <input type=text size=3 name='date_y' value='0000'></td></tr>
            <tr>  <td><b>$phrases[country] </b> </td><td><select name=country><option value=''></option>";
            $c_qr = db_query("select * from store_countries order by name asc");
   while($c_data = db_fetch($c_qr)){


        print "<option value='$c_data[code]'>$c_data[name]</option>";
           }
           print "</select></td>   </tr>";

           $qrf = db_query("select * from store_clients_sets where required=0 order by ord");
   if(db_num($qrf)){

while($dataf = db_fetch($qrf)){
    print "
    <input type=hidden name=\"custom_id[$cf]\" value=\"$dataf[id]\">
    <tr><td width=25%><b>$dataf[name]</b><br>$dataf[details]</td><td>";
    print get_member_field("custom[$cf]",$dataf);
        print "</td></tr>";
$cf++;
}
}

           print "</table>
           </fieldset>";


          print "<br><br><fieldset><table width=100%>


           <tr><td align=center><input type=submit value=' $phrases[add_button] '></td></tr>
                </table></fieldset>
         </form> ";
        }
//-----------end ----------------
 require(ADMIN_DIR.'/end.php');
?>
