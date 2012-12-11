<?

function init_members_connector(){
  global $member_table_tofind,$member_table_toreplace,$members_connector,$member_fields_tofind,$member_fields_toreplace,$search_fields,$required_database_fields_names,$required_database_fields_types;     
if($members_connector['enable']){
$full_connector_path = CWD."/members_connectors/".$members_connector['connector_file'];
}else{
$full_connector_path =  CWD. "/members_connectors/default.php";
}


if(file_exists($full_connector_path)){
    require($full_connector_path);
}else{
 trigger_error("Cannot Open Members Connector File");   
}

}

/*
function members_fields_replace($a,$type="value"){
	global $member_fields_tofind,$member_fields_toreplace ;
//$strfind = ($req_rep_tablename ? $db_table1.".id":"id");
//$strreplace = ($req_rep_tablename ? $db_table.".userid" : "userid");

$nr = array_replace($member_fields_tofind,$member_fields_toreplace,$a);
return $nr;
unset($nr);
}
 */

//----------- check if same connection ---------------
if($db_host ==$members_connector['db_host'] && $members_connector['db_username'] ==$db_username){
$members_connector['same_connection'] =true ;
}else{
$members_connector['same_connection'] =false ;
}
//---------- check if same db ----------------------
if($members_connector['db_name'] == $db_name && $members_connector['same_connection']){
    $members_connector['same_db'] = true;
}else{
   $members_connector['same_db'] = false;   
}




function members_table_replace($value){
	global $member_table_tofind,$member_table_toreplace,$members_connector;
	if($members_connector['enable']){
      
return str_replace($member_table_tofind,$member_table_toreplace,$value);
}else{
return $value;
	}
	}


function members_fields_replace($value){
	global $member_fields_tofind,$member_fields_toreplace,$members_connector ;
if($members_connector['enable']){
return str_replace($member_fields_tofind,$member_fields_toreplace,$value);
}else{
return $value;
	}
}

function member_time_replace($time){
global $members_connector;
if($members_connector['time_type']=="timestamp"){
	return date($members_connector['time_format'],$time);
	}else{
		return $time;
		}
		}


//--------------- remote db connect ------------------
function members_remote_db_connect(){
global $members_connector,$db_host,$db_name,$db_username;
if($members_connector['enable'] && !$members_connector['same_db']){


//----- connect -----
if($members_connector['same_connection']){
db_select($members_connector['db_name'],$members_connector['db_charset']); 
}else{
db_connect($members_connector['db_host'],$members_connector['db_username'],$members_connector['db_password'],$members_connector['db_name'],$members_connector['db_charset']);
}
//-------

}
}

function members_local_db_connect(){
global $db_name,$members_connector,$db_host,$db_username,$db_password,$db_charset;

if($members_connector['enable'] && !$members_connector['same_db']){

//----- connect -----
if($members_connector['same_connection']){
db_select($db_name,$db_charset); 
}else{
db_connect($db_host,$db_username,$db_password,$db_name,$db_charset);
}
//-------

}

}


//----------------------------- Members -----------------
$member_data = array();
function check_member_login(){
      global $member_data,$members_connector,$session;

 $member_data['id'] = $session->get('member_data_id');
 $member_data['password'] = $session->get('member_data_password');

   if($member_data['id']){

   $qr = db_query("select * from ".members_table_replace("store_clients")." where ".members_fields_replace("id")."='$member_data[id]'",MEMBER_SQL);

         if(db_num($qr)){
           $data = db_fetch($qr);
           if($data[members_fields_replace('password')] == $member_data['password']){

            if(in_array($data[members_fields_replace('usr_group')],$members_connector['allowed_login_groups'])){

               db_query("update ".members_table_replace('store_clients')." set ".members_fields_replace('last_login')."='".connector_get_date(date("Y-m-d H:i:s"),'member_last_login')."' where ".members_fields_replace('id')."='".$member_data['id']."'",MEMBER_SQL);
            $member_data['username'] = $data[members_fields_replace('username')];
            $member_data['email'] = $data[members_fields_replace('email')];
            $member_data['usr_group'] = $data[members_fields_replace('usr_group')];
                   return true ;

              }else{
                      return false ;
                      }
                   }else{
                           return false ;
                           }

                 }else{
                         return false ;
                         }

           }else{

                   return false ;
                   }

        }

//----------- members custom fields ----------

function get_member_field($name,$data,$action="add",$memberid=0){
      global $phrases;

    $cntx = "" ;

//----------- text ---------------
if($data['type']=="text"){

if($action=="edit"){
    $dtsx  = db_qr_fetch("select value from store_clients_fields where member='$memberid' and cat='$data[id]'");

 $cntx .= "<input type=text name=\"$name\" value=\"$dtsx[value]\" $data[style]>";
        }elseif($action=="add"){
        $cntx .= "<input type=text name=\"$name\" value=\"$data[value]\" $data[style]>";
            }else{
            $cntx .= "<input type=text name=\"$name\" value=\"\" $data[style]>";
                }

//---------- text area -------------
}elseif($data['type']=="textarea"){

if($action=="edit"){
    $dtsx  = db_qr_fetch("select value from store_clients_fields where member='$memberid' and cat='$data[id]'");

$cntx .= "<textarea name=\"$name\" $data[style]>$dtsx[value]</textarea>";
   }elseif($action=="add"){
$cntx .= "<textarea name=\"$name\" $data[style]>$data[value]</textarea>";
   }else{
$cntx .= "<textarea name=\"$name\" $data[style]></textarea>";
    }

//-------- select -----------------
}elseif($data['type']=="select"){

        if($action=="edit"){
        $dtsx  = db_qr_fetch("select value from store_clients_fields where member='$memberid' and cat='$data[id]'");
        }

        $cntx .= "<select name=\"$name\" $data[style]>";
        if($action=="search"){ $cntx .= "<option value=\"\">$phrases[without_selection]</option>";}

        $vx  = explode("\n",$data['value']);
        foreach($vx as $value){

        if($action=="edit" && $value==$dtsx['value']){$chk="selected";}else{$chk="";}

        $cntx .= "<option value=\"$value\" $chk>$value</option>";
            }
        $cntx .= "</select>";

//--------- radio ------------
}elseif($data['type']=="radio"){

        if($action=="search"){ $cntx .= "<input type=\"radio\" name=\"$name\" value=\"\" $data[style] checked>$phrases[without_selection]<br>";}

        if($action=="edit"){
        $dtsx  = db_qr_fetch("select value from store_clients_fields where member='$memberid' and cat='$data[id]'");
        }

        $vx  = explode("\n",$data['value']);
        foreach($vx as $value){
        if($action=="edit" && $value==$dtsx['value']){$chk="checked";}else{$chk="";}
        $cntx .= "<input type=\"radio\" name=\"$name\" value=\"$value\" $data[style] $chk> $value<br>";
            }

//-------- checkbox -------------
}elseif($data['type']=="checkbox"){

if($action=="edit"){
        $dtsx  = db_qr_fetch("select value from store_clients_fields where member='$memberid' and cat='$data[id]'");
        }

        $vx  = explode("\n",$data['value']);
        foreach($vx as $value){
        if($action=="edit" && $value==$dtsx['value']){$chk="checked";}else{$chk="";}
        $cntx .= "<input type=\"checkbox\" name=\"$name\" value=\"$value\"  $chk> $value<br>";
            }
        }
return $cntx;
}

//-------- Members Custom Fields Value ----------
function get_member_field_value($cat,$memberid){
$dtsx  = db_qr_fetch("select value from store_clients_fields where member='$memberid' and cat='$cat'");
return "$dtsx[value]";
}


//--------------- Account Activation Email --------------------
function snd_email_activation_msg($id){
               global $sitename,$mailing_email,$script_path,$settings,$siteurl,$scripturl,$phrases,$settings;

  $qr = db_query("select * from ".members_table_replace('store_clients')." where ".members_fields_replace('id')."='$id'",MEMBER_SQL);
  if(db_num($qr)){
  $data = db_fetch($qr);

  $active_code = md5(rand(0,999).time().$data[members_fields_replace('email')].rand().$id) ;

   db_query("delete from store_confirmations where type='validate_email' and cat='".$data[members_fields_replace('id')]."'");
     db_query("insert into store_confirmations (type,cat,code) values('validate_email','".$data[members_fields_replace('id')]."','$active_code')");

     $url = $scripturl."/index.php?action=activate_email&code=$active_code" ;

     $msg = get_template('email_activation_msg',array('{name}','{url}','{code}','{siteurl}','{sitename}'),
     array($data[members_fields_replace('username')],$url,$active_code,$siteurl,$sitename));

    send_email($sitename,$mailing_email,$data[members_fields_replace('email')],$phrases['email_activation_msg_subject'],$msg,$settings['mailing_default_use_html'],$settings['mailing_default_encoding']);
  }
  }
  
//--------------- Change Email Confirmation --------------------
function snd_email_chng_conf($username,$email,$active_code){
               global $sitename,$mailing_email,$script_path,$settings,$phrases,$sitename,$siteurl,$scripturl;

    $active_link = $scripturl."/index.php?action=confirmations&op=member_email_change&code=$active_code" ;


   $msg =  get_template("email_change_confirmation_msg",array('{username}','{active_link}','{sitename}','{siteurl}'),array($username,$active_link,$sitename,$siteurl));


    $mailResult = send_email($sitename,$mailing_email,$email,$phrases['chng_email_msg_subject'],$msg,$settings['mailing_default_use_html'],$settings['mailing_default_encoding']);
}

//--------------- Forgot Password Message ---------------------
function snd_usr_info($email){
  global $sitename,$mailing_email,$sitename,$siteurl,$phrases;
   $msg =  get_template("forgot_pwd_msg");

   $qr=db_query("select ".members_fields_replace('username').",".members_fields_replace('password').",".members_fields_replace('last_login')." from  ".members_table_replace('store_clients')." where ".members_fields_replace('email')."='$email'",MEMBER_SQL);
       if(db_num($qr)){
     $data = db_fetch($qr);

   $msg = str_replace("{username}",$data['username'],$msg);
   $msg = str_replace("{password}",$data['password'],$msg);
   $msg = str_replace("{last_login}",$data['last_login'],$msg);
  $msg = str_replace("{sitename}",$sitename,$msg);
  $msg = str_replace("{siteurl}",$siteurl,$msg);


     $mailResult = send_email($sitename,$mailing_email,$email,$phrases['forgot_pwd_msg_subject'],$msg,$settings['mailing_default_use_html'],$settings['mailing_default_encoding']);

    return true ;
    }else{
            return false ;
            }
          }  