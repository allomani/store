<?

define('ADMIN_DIR', (($getcwd = str_replace("\\","/",getcwd())) ? $getcwd : '.'));  
chdir('./../');
define('CWD', (($getcwd = str_replace("\\","/",getcwd())) ? $getcwd : '.'));
define('IS_ADMIN', 1);
define('THIS_PAGE', "admin"); 


include_once(CWD . "/global.php") ;



//----------- Login Script ----------------------------------------------------------
if ($action == "login" && $username && $password ){

     $result=db_query("select * from store_user where username='".db_escape($username,false)."'");
     if(db_num($result)){
     $login_data=db_fetch($result);

 
       if($login_data['password']==$password){
 access_log_record($login_data['username'],"Login Done");
 
set_cookie('admin_id', $login_data['id']);
set_cookie('admin_username', $login_data['username']);
set_cookie('admin_password', md5($login_data['password']));

     print "<SCRIPT>window.location=\"index.php\";</script>";
      exit();
       }else{
            access_log_record($login_data['username'],"Login Invalid Password"); 
              print "<link href=\"smiletag-admin.css\" type=text/css rel=stylesheet>\n";
              print "<br><center><table width=60% class=grid><tr><td align=center> $phrases[cp_invalid_pwd] </td></tr></table></center>";

              }
            }else{
                  access_log_record($username,"Login Invalid Username");        
                 print " <link href=\"smiletag-admin.css\" type=text/css rel=stylesheet>    \n";
                    print "<br><center><table width=60% class=grid><tr><td align=center>   $phrases[cp_invalid_username] </td></tr></table></center>";

                    }
              }elseif($action == "logout"){
                  
                
                    set_cookie('admin_id');
                    set_cookie('admin_username');
                    set_cookie('admin_password');
                    


                  print "<SCRIPT>window.location=\"index.php\";</script>";

                      }
//-------------------------------------------------------------------------------------------
//--------- add Main user --------//
if($op=="add_main_user"){
$users_num = db_qr_fetch("select count(id) as count from store_user");
if($users_num['count'] == 0 && trim($cp_username) && trim($cp_password)){
db_query("insert into store_user (username,password,email,group_id) values('".db_escape($cp_username,false)."','".db_escape($cp_password,false)."','".db_escape($cp_email)."','1')");
}
}
//-------- First time setup ----------//
$users_num = db_qr_fetch("select count(id) as count from store_user");
if($users_num['count'] == 0){

if($global_lang=="arabic"){
$global_dir = "rtl" ;
print "<html dir=$global_dir>
<title>$sitename - ·ÊÕ… «· Õﬂ„ </title>" ;
}else{
$global_dir = "ltr" ;
print "<html dir=$global_dir>
<title>$sitename - Control Panel </title>" ;
}
print "<META http-equiv=Content-Language content=\"$settings[site_pages_lang]\">
<META http-equiv=Content-Type content=\"text/html; charset=$settings[site_pages_encoding]\">
<link href=\"images/style.css\" type=text/css rel=stylesheet>
<script src='$scripturl/js/StrongPassword.js'></script>";

print "<center>
<form action='index.php' method=post>
<input type=hidden name=op value='add_main_user'>
<br><br><table width=50% class=grid>
<tr><td colspan=2><h2>$phrases[create_main_user]</h2></td></tr>
<tr><td>$phrases[username]</td><td><input type=text name=cp_username dir=ltr></td></tr>
<tr><td>$phrases[password]</td><td><input type=text id='cp_password'  name=cp_password dir=ltr onChange=\"passwordStrength(this.value);\" onkeyup=\"passwordStrength(this.value);\"> &nbsp; <input type=button value=\"Generate\" onClick=\"document.getElementById('cp_password').value=GenerateAndValidate(12,1);passwordStrength(document.getElementById('cp_password').value);\"></td></tr>
<tr><td>$phrases[email]</td><td><input type=text name=cp_email dir=ltr></td></tr>

<tr><td></td><td>
<div id=\"passwordDescription\">-</div>
<div id=\"passwordStrength\" class=\"strength0\"></div>
</td></tr>
<tr><td colspan=2 align=center><input type=submit value=' $phrases[add_button] '></td></tr>
</table>
</form></center>";
die();   
}
if (check_login_cookies()) {
//--------------------------- Backup Job ------------------------------
if($action=="backup_db_do"){
if(!$disable_backup){
if_admin();
require(CWD. '/includes/class_mysql_db_backup.php');
$backup_obj = new MySQL_DB_Backup();
$backup_obj->server = $db_host ;
$backup_obj->port = 3306;
$backup_obj->username = $db_username;
$backup_obj->password = $db_password;
$backup_obj->database = $db_name;
$backup_obj->drop_tables = true;
$backup_obj->create_tables = true;
$backup_obj->struct_only = false;
$backup_obj->locks = true;
$backup_obj->comments = true;
$backup_obj->fname_format = 'm-d-Y-h-i-s';
$backup_obj->null_values = array( '0000-00-00', '00:00:00', '0000-00-00 00:00:00');
$backup_obj->backup_dir = CWD . '/'.$settings['uploader_path'].'/backups/'; 

if($op=="local"){
$task = MSX_DOWNLOAD;
$filename = "store_".date('m-d-Y_h-i-s').".sql.gz";
}elseif($op=="server"){
$task = MSX_SAVE ;
$filename = basename($filename);
}
$use_gzip = true;
$result_bk = $backup_obj->Execute($task, $filename, $use_gzip);
    if (!$result_bk)
        {
                 $output = $backup_obj->error;
        }
        else
        {
                $output = $phrases['backup_done_successfully'];

        }
        }else{
        $output =  $disable_backup ;
                }
}


//---------- load editor --------//
require (CWD."/".$editor_path."/editor_init_functions.php") ;
editor_init();


//----- load headers ------//
if($global_lang=="arabic"){
$global_dir = "rtl" ;
print "<html dir=$global_dir>
<title>$sitename - ·ÊÕ… «· Õﬂ„ </title>" ;
}else{
$global_dir = "ltr" ;
print "<html dir=$global_dir>
<title>$sitename - Control Panel </title>" ;
}
print "<META http-equiv=Content-Language content=\"$settings[site_pages_lang]\">
<META http-equiv=Content-Type content=\"text/html; charset=$settings[site_pages_encoding]\">

<link href=\"images/style.css\" type=text/css rel=stylesheet />
<script src='js.js' type=\"text/javascript\" language=\"javascript\"></script>

<script src='$scripturl/js/prototype.js'></script>
<script src='ajax.js'></script> 
<script src='$scripturl/js/scriptaculous/scriptaculous.js'></script>
<script src='$scripturl/js/StrongPassword.js'></script>";
editor_html_init();

if(file_exists(CWD . "/install/")){
print "<div style=\"border:1px solid;color: #D8000C;background-color: #FFBABA;padding:3px;text-align:center;margin:0;\">Installation folder exists at /install , Please delete it</div>";
}
        
if($license_properties['expire']['value'] && $license_properties['expire']['value'] != "0000-00-00"){
    $remaining_days = floor((strtotime($license_properties['expire']['value']) - time()) / (24*60*60));
    print "<div style=\"border:1px solid;color: #9F6000;background-color: #F9F0B5;padding:3px;text-align:center;margin:0;direction:ltr;\">The license will expire on : {$license_properties['expire']['value']} ($remaining_days days)</div>";
}

?>  

<table width=100% height=100%><tr><td width=20% valign=top>

<?
print str_replace("{username}",$user_info['username'],$phrases['cp_welcome_msg']); 
print " <br><br>";

 require("admin_menu.php") ;
?>

</td>
 <td width=1 background='images/dot.gif'></td>
<td valign=top> <br>
<?
//----------------------Start -------------------------------------------------------
if(!$action){
  $count_products = db_qr_fetch("select count(id) as count from store_products_data");

   $data4 = db_qr_fetch("select count(id) as count from store_user");
   
   $count_members = db_qr_fetch("select count(".members_fields_replace("id").") as count from ".members_table_replace("store_clients"),MEMBER_SQL);

print "<center><table width=50% class=grid><tr><td align=center><b>$phrases[welcome_to_cp] <br><br>";


   if($global_lang=="arabic"){
  print "„—Œ’ ·‹ : $_SERVER[HTTP_HOST]" ;
  if(COPYRIGHTS_TXT_ADMIN){
  	print "   „‰ <a href='http://allomani.com/' target='_blank'>  «··Ê„«‰Ì ··Œœ„«  «·»—„ÃÌ… </a> " ;
  	}

  	print "<br><br>

   ≈’œ«— : $version_number <br><br>";
  }else{
  print "Licensed For : $_SERVER[SERVER_NAME]" ;
  if(COPYRIGHTS_TXT_ADMIN){
  	print "   By  <a href='http://allomani.com/' target='_blank'>Allomani&trade;</a> " ;
  	}

  	print "<br><br>

   Version : $version_number <br><br>";
  	}

  print "$phrases[cp_statics] : </b><br> $phrases[products_count] : $count_products[count] <br>  
  $phrases[clients_count] : $count_members[count] <br>
   $phrases[users_count] : $data4[count] </font></td></tr></table></center>";

 print "<br><center><table width=50% class=grid><td align=center>";
    print "<b><span dir=$global_dir>$phrases[php_version] : </span></b> <span dir=ltr>".@phpversion()." </span><br> ";

      print "<b><span dir=$global_dir>$phrases[mysql_version] :</span> </b><span dir=ltr>" .@mysql_get_server_info() ."</span><br>";
    if(@function_exists('zend_loader_version')){
   print "<b><span dir=$global_dir>$phrases[zend_version] :</span> </b><span dir=ltr>" . @zend_loader_version() ."</span><br><br>";
    }

   if(@function_exists("gd_info")){
   $gd_info = @gd_info();
   print "<b>  $phrases[gd_library] : </b> <font color=green> $phrases[cp_available] </font><br>
  <b>$phrases[the_version] : </b> <span dir=ltr>".$gd_info['GD Version'] ."</span>";
  }else{
  print "<b>  $phrases[gd_library] : </b> <font color=red> $phrases[cp_not_available] </font><br>
  $phrases[gd_install_required] ";
          }
          
   print "<br><br><b>Safe Mode : </b> ".iif(@ini_get('safe_mode'),
   "<font color=green>ON</font>",
   "<font color=red>OFF</font><br>".iif($global_lang=="arabic"," ÌÊ’Ï » ›⁄Ì· Safe Mode ·÷„«‰ „” ÊÏ Õ„«Ì… «›÷·","it's recommended to enable SafeMode for better Security Level"));
   
   print "</td></tr></table>";

  print "<br><center><table width=50% class=grid><td align=center>
  <p><b> $phrases[cp_addons] </b></p>";

   //--------------- Check Installed Plugins --------------------------
$dhx = @opendir(CWD ."/plugins");
  $plgcnt = 0 ;
while ($rdx = @readdir($dhx)){
         if($rdx != "." && $rdx != "..") {
                 $cur_fl = CWD ."/plugins/" . $rdx . "/index.php" ;
        if(@file_exists($cur_fl)){
                print $rdx ."<br>" ;
                $plgcnt = 1 ;
                }
          }

    }
@closedir($dhx);
if(!$plgcnt){
	print "<center> $phrases[no_addons] </center>";
	}
 print "</td></tr></table>";

if($global_lang=="arabic"){
    print "<br><center><table width=50% class=grid><td align=center>
     Ì ’›Õ «·„Êﬁ⁄ Õ«·Ì« $counter[online_users] “«∆—
                                               <br><br>
   √ﬂ»—  Ê«Ãœ ﬂ«‰  $counter[best_visit] ›Ì : <br> $counter[best_visit_time] <br></td></tr></table>";
 }else{
 	    print "<br><center><table width=50% class=grid><td align=center>
     Now Browsing : $counter[online_users] Visitor
                                               <br><br>
   Best Visitors Count : $counter[best_visit] in : <br> $counter[best_visit_time] <br></td></tr></table>";

 	}
   }




//------------- Products ----------
require(ADMIN_DIR ."/products.php");
//------------- Hot Items ----------
require(ADMIN_DIR ."/hot_items.php");
//------------- Orders ----------
require(ADMIN_DIR . "/orders.php");
//------------- Orders Status----------
require(ADMIN_DIR . "/orders_status.php");
//-------------- Blocks  ---------------
require(ADMIN_DIR ."/blocks.php");
//------------ Votes ---------
require(ADMIN_DIR ."/votes.php");
 //------------- News --------
 require(ADMIN_DIR ."/news.php");
//---------- Members --------
require(ADMIN_DIR ."/clients.php");
//-------- Pages ----------
require(ADMIN_DIR ."/pages.php");
//-------- Store Fields --------
require(ADMIN_DIR ."/store_fields.php");
//-------- Payments Methods --------
require(ADMIN_DIR ."/payment_methods.php");
//-------- Payments Gateways --------
require(ADMIN_DIR ."/payment_gateways.php");
//-------- Shipping Methods --------
require(ADMIN_DIR ."/shipping_methods.php");    



//-------------------- Permisions------------------------
if($action=="permisions"){

    if_admin();
    $data =db_qr_fetch("select * from store_user where id='$id'");     
    
    print "<img src='images/arrw.gif'>&nbsp;<a href='index.php?action=users'>$phrases[the_users]</a> / $phrases[permissions_manage]  / $data[username] <br><br>
    <form method=post action=index.php>
           <input type=hidden value='$id' name='user_id'>
               <input type=hidden value='permisions_edit' name='action'>";


         print "<center><span class=title>$phrases[permissions_manage]</span><br><br>
           <table cellpadding=\"0\" border=0 cellspacing=\"0\" width=\"80%\" class=\"grid\">

        <tr><td>
        <center><span class=title>$phrases[cats_permissions]</span> <br><br>
$phrases[cats_permissions_note]";
          
           print " </center></td></tr>
           </table><br>";

     //------------------------------------------------------------------------------

    

    


      print "<table cellpadding=\"0\" border=0 cellspacing=\"0\" width=\"80%\" class=\"grid\">
     <tr> <td colspan=5 align=center><span class=title>$phrases[cp_sections_permissions]</span></td></tr>
            <tr><td><table width=100%><tr>";

            $prms = explode(",",$data['cp_permisions']);
                      

  if(is_array($permissions_checks)){

  $c=0;
 for($i=0; $i < count($permissions_checks);$i++) {

        $keyvalue = current($permissions_checks);

if($c==4){
    print "</tr><tr>" ;
    $c=0;
    }

if(in_array($keyvalue,$prms)){$chk = "checked" ;}else{$chk = "" ;}

print "<td width=25%><input  name=\"cp_permisions[$i]\" type=\"checkbox\" value=\"$keyvalue\" $chk>".key($permissions_checks)."</td>";


$c++ ;

 next($permissions_checks);
}
  }
print "</tr></table></td>

            </tr></table>";

          print "<center> <br><input type=submit value='$phrases[edit]'></form>" ;

        }
//---------------------------- Users ------------------------------------------
if ($action == "users" or $action=="edituserok" or $action=="adduserok" or $action=="deluser" || $action=="permisions_edit"){


if($action=="permisions_edit"){

        if_admin();

$user_id = intval($user_id);

if($cp_permisions){
foreach ($cp_permisions as $value) {
       $perms .=  "$value," ;
     }
       }else{
               $perms = '' ;
               }

 db_query("update store_user set cp_permisions='$perms' where id='$user_id'");
 

           }

        //---------------------------------------------
        if ($action=="deluser" && $id){
        if($user_info['groupid']==1 ){
db_query("delete from store_user where id='$id'");
}else{
        print_admin_table("<center>$phrases[access_denied]</center>");
                          die();
        }
        }
        //---------------------------------------------
        if ($action == "adduserok"){
        if($user_info['groupid']==1){
                if(trim($username) && trim($password)){
                if(db_qr_num("select username from store_user where username='".db_escape($username,false)."'")){
                        print "<center> $phrases[cp_err_username_exists] </center>";
                        }else{
        db_query("insert into store_user (username,password,email,group_id) values ('".db_escape($username,false)."','".db_escape($password,false)."','".db_escape($email)."','".intval($group_id)."')");
        }
        }else{
                print "<center>  $phrases[cp_plz_enter_usr_pwd] </center>";
                }
                }else{
                          print_admin_table("<center>$phrases[access_denied]</center>");
                          die();
        }
        }
        //------------------------------------------------------------------------------
        if ($action == "edituserok"){
                if ($password){
                $ifeditpassword = ", password='".db_escape($password,false)."'" ;
                }

        if ($user_info['groupid'] == 1){
        db_query("update store_user set username='".db_escape($username,false)."'  , email='".db_escape($email)."' ,group_id='".intval($group_id)."' $ifeditpassword where id='$id'");
        }else{
         if($user_info['id'] == $id){
        db_query("update store_user set username='".db_escape($username,false)."'  , email='".db_escape($email)."'  $ifeditpassword where id='$id'");

                 }else{
                   print_admin_table("<center>$phrases[access_denied]</center>");
                   die(); 
                         }
                }
     
                print "<center>  $phrases[cp_edit_user_success]  </center>";
       
        }

if ($user_info['groupid'] == 1){
print "<img src='images/add.gif'><a href='index.php?action=useradd'>$phrases[cp_add_user]</a>";

//----------------------------------------------------
     print "<p align=center class=title>$phrases[the_users]</p>";
       $result=db_query("select * from store_user order by id asc");


  print " <center> <table cellpadding=\"0\" border=0 cellspacing=\"0\" width=\"80%\" class=\"grid\">

        <tr>
             <td height=\"18\" width=\"134\" valign=\"top\" align=\"center\">$phrases[cp_username]</td>
                <td height=\"18\" width=\"240\" valign=\"top\">
                <p align=\"center\">$phrases[cp_email]</td>
                <td height=\"18\" width=\"105\" valign=\"top\">
                <p align=\"center\">$phrases[cp_user_group]</td>
                <td height=\"18\" width=\"193\" valign=\"top\" colspan=2>
                <p align=\"center\">$phrases[the_options]</td>
        </tr>";

      while($data = db_fetch($result)){


        if ($data['group_id']==1){$groupname="$phrases[cp_user_admin]";
             $permision_link="";
      }elseif($data['group_id']==2){$groupname="$phrases[cp_user_mod]";
       $permision_link="<a href='index.php?action=permisions&id=$data[id]'>$phrases[permissions_manage]</a>";

      }


        print "<tr>
                <td  width=\"134\" >
                <p align=\"center\">$data[username]</p></td>
                <td  width=\"240\" >
                <p align=\"center\">$data[email]</p></td>
                <td  width=\"105\"><p align=\"center\">$groupname</p></td>
                 <td  width=\"105\"><p align=\"center\">$permision_link</p></td>
                <td  width=\"193\"><p align=\"center\">
                 <a href='index.php?action=edituser&id=$data[id]'> $phrases[edit] </a> ";
        if ($data['id'] !="1"){
                print "- <a href='index.php?action=deluser&id=$data[id]' onClick=\"return confirm('".$phrases['are_you_sure']."');\"> $phrases[delete] </a>";
        }
                print " </p>
                </td>
        </tr>";
          }

print "</table></center>\n";




        }else{

                print "<br><center><table width=70% class=grid><tr><td align=center>
                $phrases[edit_personal_acc_only] <br>
                <a href='index.php?action=edituser'> $phrases[click_here_to_edit_ur_account] </a>
                </td></tr></table></center>";
        }
        }
//-------------------------Edit User------------------------------------------

if ($action=="edituser"){
       $id = intval($id);

if($user_info['groupid']!=1){
        $id=$user_info['id'];
}

$qr=db_query("select * from store_user where id='$id'") ;
if (db_num($qr)){

$data = db_fetch($qr) ;

print "<img src='images/arrw.gif'>&nbsp;<a href='index.php?action=users'>$phrases[the_users]</a> / $data[username] <br><br>


<center>
<FORM METHOD=\"post\" ACTION=\"index.php\">

 <TABLE width=70% class=grid>
    <TR>

    <INPUT TYPE=\"hidden\" NAME=\"id\" \" value=\"$data[id]\" >
<INPUT TYPE=\"hidden\" NAME=\"action\"  value=\"edituserok\" >

   <TD width=\"100\"><font color=\"#006699\"><b>$phrases[cp_username] : </b></font> </TD>
   <TD width=\"614\"><INPUT TYPE=\"text\" NAME=\"username\" size=\"32\" value=\"$data[username]\" > </TD>
  </TR>
    <TR>
   <TD width=\"100\"><font color=\"#006699\"><b>$phrases[cp_password] : </b></font> </TD>
   <TD width=\"614\"><INPUT TYPE=\"text\" NAME=\"password\" size=\"32\" onChange=\"passwordStrength(this.value);\" onkeyup=\"passwordStrength(this.value);\"> &nbsp; <input type=button value=\"Generate\" onClick=\"document.getElementById('password').value=GenerateAndValidate(12,1);passwordStrength(document.getElementById('password').value);\">
    <br>* $phrases[leave_blank_for_no_change] </TD>
  </TR>
  <tr><td></td><td>
<div id=\"passwordDescription\">-</div>
<div id=\"passwordStrength\" class=\"strength0\"></div>
</td></tr>
   <TR>
   <TD width=\"100\"><font color=\"#006699\"><b>$phrases[cp_email] : </b></font> </TD>
   <TD width=\"614\"><INPUT TYPE=\"text\" NAME=\"email\" size=\"32\" value=\"$data[email]\" > </TD>
  </TR>\n";

  if($user_info['groupid'] != 1){
          print "<input type='hidden' name='group_id' value='2'>";
  }else {
   print "<TR>
   <TD width=\"100\"><font color=\"#006699\"><b>$phrases[cp_user_group]: </b></font> </TD>
   <TD width=\"614\">\n";


if ($data['group_id'] == 1){$ifselected1 = "selected" ; }else{$ifselected2 = "selected";}

print "  <p><select size=\"1\" name=group_id>\n
        <option value='1' $ifselected1> $phrases[cp_user_admin] </option>
  <option value='2' $ifselected2>$phrases[cp_user_mod] </option>" ;


 print "  </select>";
  }

   print "</TD>
  </TR>


  <TR>
   <TD COLSPAN=\"2\" width=\"685\">
   <p align=\"center\"><INPUT TYPE=\"submit\" name=\"usereditbutton\" VALUE=\"$phrases[edit]\"></TD>
  </TR>
 </TABLE>
</FORM>
</center>\n";


}else{
    print "<center> $phrases[err_wrong_url]</center>" ;
    }
}
//--------------------- Add User Form -------------------------------------------------------
if($action=="useradd"){
print "   <img src='images/arrw.gif'>&nbsp;<a href='index.php?action=users'>$phrases[the_users]</a> / $phrases[add_button] <br><br>

   <center>

<FORM METHOD=\"post\" ACTION=\"index.php\">

 <TABLE width=\"70%\" class=grid>
    <TR>
   <td colspan=2 align=center><span class=title> $phrases[cp_add_user] </span></td></tr>
   <tr>
<INPUT TYPE=\"hidden\" NAME=\"action\"  value=\"adduserok\" >

   <TD width=\"150\"><font color=\"#006699\"><b>$phrases[cp_username]: </b></font> </TD>
   <TD ><INPUT TYPE=\"text\" NAME=\"username\" size=\"32\"  </TD>
  </TR>
    <TR>
   <TD width=\"150\"><font color=\"#006699\"><b>$phrases[cp_password] : </b></font> </TD>
   <TD ><INPUT TYPE=\"text\" NAME=\"password\" size=\"32\" onChange=\"passwordStrength(this.value);\" onkeyup=\"passwordStrength(this.value);\"> &nbsp; <input type=button value=\"Generate\" onClick=\"document.getElementById('password').value=GenerateAndValidate(12,1);passwordStrength(document.getElementById('password').value);\"> </TD>
  </TR>
  <tr><td></td><td>
<div id=\"passwordDescription\">-</div>
<div id=\"passwordStrength\" class=\"strength0\"></div>
</td></tr>
   <TR>
   <TD width=\"150\"><font color=\"#006699\"><b>$phrases[cp_email] : </b></font> </TD>
   <TD ><INPUT TYPE=\"text\" NAME=\"email\" size=\"32\" > </TD>
  </TR>

   <TR>
   <TD width=\"150\"><font color=\"#006699\"><b>$phrases[cp_user_group]: </b></font> </TD>
   <TD >\n";


print "  <p><select size=\"1\" name=group_id>\n
        <option value='1' > $phrases[cp_user_admin] </option>
  <option value='2' > $phrases[cp_user_mod]</option>" ;


 print "  </select>";


  print " </TD>
  </TR>


  <TR>
   <TD COLSPAN=\"2\" >
   <p align=\"center\"><INPUT TYPE=\"submit\" name=\"useraddbutton\" VALUE=\"$phrases[add_button]\"></TD>
  </TR>
 </TABLE>
</FORM>
</center><br><br>\n";
}

//---------- Banners -----------
require("banners.php");
 //----------------------hooks ----------------------------
if($action=="hooks" || $action=="hook_disable" || $action=="hook_enable" || $action=="hook_add_ok" || $action=="hook_edit_ok" || $action=="hook_del" || $action=="hooks_fix_order"){


    if_admin();
//--------- hook add ---------------
if($action=="hook_add_ok"){
db_query("insert into store_hooks (name,hookid,code,ord,active) values (
'".db_escape($name)."',
'".db_escape($hookid)."',
'".db_escape($code,false)."',
'".intval($ord)."','1')");
}
//------- hook edit ------------
if($action=="hook_edit_ok"){
db_query("update store_hooks set
name='".db_escape($name)."',
hookid='".db_escape($hookid)."',
code='".db_escape($code,false)."',
ord='".intval($ord)."' where id='".intval($id)."'");
}
//--------- hook del --------
if($action=="hook_del"){
    db_query("delete from store_hooks where id='".intval($id)."'");
    }
//--------- enable / disable -----------------
if($action=="hook_disable"){
        db_query("update store_hooks set active=0 where id='".intval($id)."'");
        }

if($action=="hook_enable"){

       db_query("update store_hooks set active=1 where id='".intval($id)."'");
        }
//-------- fix order -----------
if($action=="hooks_fix_order"){

   $qr=db_query("select hookid,id from store_hooks order by hookid,ord ASC");
    if(db_num($qr)){
    $hook_c = 1 ;
    while($data = db_fetch($qr)){

    if($last_hookid !=$data['hookid']){$hook_c=1;}

    db_query("update store_hooks set ord='$hook_c' where id='$data[id]'");
     $last_hookid = $data['hookid'];
    ++$hook_c;
    }
     }
     unset($last_hookid);
     }
//---------------------------------------------


$qr =db_query("select * from store_hooks order by hookid,ord,active");

print "<center><p class=title> $phrases[cp_hooks] </p>

<p align=$global_align><a href='index.php?action=hook_add'><img src='images/add.gif' border=0> $phrases[add] </a></p>";

if(db_num($qr)){
              print "<table width=80% class=grid><tr>";

print "<tr><td><b>$phrases[the_name]</b></td><td><b>$phrases[the_order]</b></td><td><b>$phrases[the_place]</b></td><td><b>$phrases[the_options]</b></td></tr>";
while($data = db_fetch($qr)){

     if($last_hookid !=$data['hookid']){print "<tr><td colspan=4><hr class=separate_line></td></tr>";}

print "<tr><td>$data[name]</td><td><b>$data[ord]</b></td><td>$data[hookid]</td><td>";
 if($data['active']){
                        print "<a href='index.php?action=hook_disable&id=$data[id]'>$phrases[disable]</a>" ;
                        }else{
                        print "<a href='index.php?action=hook_enable&id=$data[id]'>$phrases[enable]</a>" ;
                        }

print "- <a href='index.php?action=hook_edit&id=$data[id]'>$phrases[edit] </a>
- <a href='index.php?action=hook_del&id=$data[id]' onClick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete] </a>
</td></tr>";


    $last_hookid = $data['hookid'];
    }

          print "</table>
 <br><form action='index.php' method=post>
                <input type=hidden name=action value='hooks_fix_order'>
                <input type=submit value=' $phrases[cp_hooks_fix_order] '>
                </form></center>";

}else{
print "<table width=80% class=grid><tr>
    <tr><td align=center>  $phrases[no_hooks] </td></tr>
    </table></center>";
    }

}

//-------- add hook -------
if($action=="hook_add"){

    if_admin();

print "<center>
<form action='index.php' method=post>
<input type=hidden name=action value='hook_add_ok'>
<table width=80% class=grid>
<tr><td><b>$phrases[the_name]</b></td><td><input type=text size=20 name=name></td></tr>
<tr><td><b>$phrases[the_place]</b></td><td>";
$hooklocations = get_plugins_hooks();
print_select_row("hookid",$hooklocations,"","dir=ltr");
print "</td></tr>
  <tr>
              <td width=\"70\">
                <b>$phrases[the_code]</b></td><td width=\"223\">
                  <textarea name='code' rows=20 cols=45 dir=ltr ></textarea></td>
                        </tr>
<tr><td><b>$phrases[the_order]</b></td><td><input type=text size=3 name=ord value='0'></td></tr>
<tr><td colspan=2 align=center><input type=submit value=' $phrases[add_button] '></td></tr>
</table>
</form></center>";
}

//-------- edit hook -------
if($action=="hook_edit"){

    if_admin();
$id=intval($id);

$qr = db_query("select * from store_hooks where id='$id'");

if(db_num($qr)){
    $data = db_fetch($qr);
print "<center>
<form action='index.php' method=post>
<input type=hidden name=action value='hook_edit_ok'>
<input type=hidden name=id value='$id'>
<table width=80% class=grid>
<tr><td><b>$phrases[the_name]</b></td><td><input type=text size=20 name=name value=\"$data[name]\"></td></tr>
<tr><td><b>$phrases[the_place]</b></td><td>";
$hooklocations = get_plugins_hooks();
print_select_row("hookid",$hooklocations,"$data[hookid]","dir=ltr");
print "</td></tr>
  <tr>
              <td width=\"70\">
                <b>$phrases[the_code]</b></td><td width=\"223\">
                  <textarea name='code' rows=20 cols=45 dir=ltr >".htmlspecialchars($data['code'])."</textarea></td>
                        </tr>
<tr><td><b>$phrases[the_order]</b></td><td><input type=text size=3 name=ord value=\"$data[ord]\"></td></tr>
<tr><td colspan=2 align=center><input type=submit value=' $phrases[edit] '></td></tr>
</table>
</form></center>";
}else{
print "<center><table width=50% class=grid><tr><td align=center>$phrases[err_wrong_url]</td></tr></table></center>";
}
}         
//------------------- DATABASE BACKUP --------------------------
if($action=="backup_db_do"){
    $output = htmlspecialchars($output) ;
print "<br><center> <table width=50% class=grid><tr><td align=center>  $output </td></tr></table>";
}

  if($action=="backup_db"){

   if_admin();
      print "<br><center>
      <p align=center class=title> $phrases[cp_db_backup] </p>

      <form action=index.php method=post>
      <input type=hidden name=action value='backup_db_do'>
      <table width=50% class=grid><tr><td>
      <input type=\"radio\" name=op value='local' checked onclick=\"document.getElementById('backup_server').style.display = 'none';\"> $phrases[db_backup_saveto_pc]
      <br><input type=\"radio\" name=op value='server' onclick=\"document.getElementById('backup_server').style.display = 'inline';\" > $phrases[db_backup_saveto_server]
      </td></tr>
      <tr><td>
      <div id=backup_server style=\"display: none; text-decoration: none\">
      <b> $phrases[the_file_path] : &nbsp; </b> <div dir=ltr>data/backups/<input type=text name=filename dir=ltr size=40 value='store_".date("d-m-Y-h-i-s").".sql.gz'></div>
      </div>
     </td></tr><tr> <td align=center>
      <input type=submit value=' $phrases[cp_db_backup_do] '>
      </form></td></tr></table></center>";

          }
// ----------------- Repair Database -----------------------

if($action=="db_info"){

    if_admin();

if(!$disable_repair){
print "<script language=\"JavaScript\">\n";
print "function checkAll(form){\n";
print "  for (var i = 0; i < form.elements.length; i++){\n";
print "    eval(\"form.elements[\" + i + \"].checked = form.elements[0].checked\");\n";
print "  }\n";
print "}\n";
print "</script>\n";

        $tables = db_query("SHOW TABLE STATUS");
        print "<form name=\"form1\" method=\"post\" action=\"index.php\"/>
        <input type=hidden name=action value='repair_db_ok'>
        <center><table width=\"96%\"  class=grid>";
        print "<tr><td colspan=\"5\"> <font size=4><b>$phrases[the_database]</b></font> </td></tr>
        <tr><td>
        <input type=\"checkbox\" name=\"check_all\" checked=\"checked\" onClick=\"checkAll(this.form)\"/></td>
        ";
        print "<td><b>$phrases[the_table]</b></td><td><b>$phrases[the_size]</b></td>
        <td><b>$phrases[the_status]</b></td>
            </tr>";
        while($table = db_fetch($tables))
        {
            $size = round($table['Data_length']/1024, 2);
            $status = db_qr_fetch("ANALYZE TABLE `$table[Name]`");
            print "<tr>
            <td  width=\"5%\"><input type=\"checkbox\" name=\"check[]\" value=\"$table[Name]\" checked=\"checked\" /></td>
            <td width=\"50%\">$table[Name]</td>
            <td width=\"10%\" align=left dir=ltr>$size KB</td>
            <td>$status[Msg_text]</td>
            </tr>";
        }

        print "</table><br> <center><input type=\"submit\" name=\"submit\" value=\"$phrases[db_repair_tables_do]\" /></center> <br>
        </form>";
        }else{
              print_admin_table("<center> $disable_repair </center>") ;
            }
    }
//------------------------------------------------
    if($action=="repair_db_ok"){
       if_admin();

    if(!$disable_repair){
        if(!$check){
            print "<center><table width=50% class=grid><tr><td align=center> $phrases[please_select_tables_to_rapair] </td></tr></table></center>";
    }else{
        $tables = $_POST['check'];
        print "<center><table width=\"60%\"  class=grid>";

        foreach($tables as $table)
        {
            $query = db_query("REPAIR TABLE `". $table . "`");
            $que = db_fetch($query);
            print "<tr><td width=\"20%\">";
            print "$phrases[cp_repairing_table] " . $que['Table'] . " , <font color=green><b>$phrases[done]</b></font>";
            print "</td></tr>";
        }

        print "</table></center>";

        }

        }else{
              print_admin_table("<center> $disable_repair </center>") ;
            }
    }

//--------------------- Templates ----------------------------------

  if($action =="templates" || $action =="template_edit_ok" || $action=="template_del" ||
  $action =="template_add_ok" || $action=="template_cat_edit_ok" || $action=="template_cat_add_ok" ||
  $action=="template_cat_del"){

 if_admin("templates");
 $id=intval($id);
 $cat =intval($cat);

 //------- template cat edit ---------
 if($action=="template_cat_edit_ok"){
 if(trim($name)){
 db_query("update store_templates_cats set name='".db_escape($name)."',selectable='".intval($selectable)."',images='".db_escape($images)."' where id='$id'");
     }
 }
//------ template cat add ----------
if($action=="template_cat_add_ok"){
db_query("insert into store_templates_cats (name,selectable,images) values('".db_escape($name)."','".intval($selectable)."','".db_escape($images)."')");
$catid = mysql_insert_id();

$qr = db_query("select * from store_templates where cat='1' order by id");
while($data = db_fetch($qr)){
db_query("insert into store_templates (name,title,content,cat,protected) values (
'".db_escape($data['name'])."',
'".db_escape($data['title'])."',
'".db_escape($data['content'],false)."',
'$catid','".intval($data['protected'])."')");
    }

}
//--------- template cat del --------
if($action=="template_cat_del"){
if($id !="1"){
db_query("delete from store_templates where cat='$id'");
db_query("delete from store_templates_cats where id='$id'");
     }
    }
//-------- template edit -----------
if($action =="template_edit_ok"){
db_query("update store_templates set title='".db_escape($title)."',content='".db_escape($content,false)."' where id='$id'");
}
//--------- template add ------------
if($action =="template_add_ok"){
db_query("insert into  store_templates (name,title,content,cat) values(
'".db_escape($name)."',
'".db_escape($title)."',
'".db_escape($content,false)."',
'".intval($cat)."')");
}
//---------- template del ---------
if($action=="template_del"){
      db_query("delete from store_templates where id='$id' and protected=0");
      db_query("update store_blocks set template=0 where template='$id'");
}

print "<center>
  <p class=title>  $phrases[the_templates] </p> ";


  if($cat){

$cat_data = db_qr_fetch("select name from store_templates_cats where id='$cat'");
print "<p align=$global_align><img src='images/link.gif'><a href='index.php?action=templates'>$phrases[the_templates] </a> / $cat_data[name]</p>";


         $qr = db_query("select * from store_templates where cat='$cat' order by id");
        if (db_num($qr)){
      print "<p align='$global_align'><img src='images/add.gif'> <a href='index.php?action=template_add&cat=$cat'> $phrases[cp_add_new_template] </a></p>
      <br>
      <center>
  <table width=80% class=grid>" ;

   $trx = 1;
    while($data=db_fetch($qr)){
    if($trx == 1){
        $tr_color = "#FFFFFF";
        $trx=2;
        }else{
        $tr_color = "#F2F2F2";
        $trx=1;
        }
    print "<tr bgcolor=$tr_color><td><b>$data[name]</b><br><span class=small>$data[title]</span></td>
   <td align=center> <a href='index.php?action=template_edit&id=$data[id]'> $phrases[edit] </a>";
    if($data['protected']==0){
            print " - <a href='index.php?action=template_del&id=$data[id]&cat=$cat' onclick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete]</a>";
            }
            print "</td></tr>";

     }
      print "</table>";

                }else{
                    print_admin_table($phrases['cp_no_templates']);
                     }

}else{
    $qr = db_query("select * from store_templates_cats order by id asc");
     print "<p align='$global_align'><img src='images/add.gif'> <a href='index.php?action=template_cat_add'> $phrases[add_style] </a></p>
      <br>
    <center><table width=90% class=grid>";
    while($data =db_fetch($qr)){
    print "<tr><td><a href='index.php?action=templates&cat=$data[id]'>$data[name]</a></td>
    <td align=center>
    <a href='index.php?action=templates&cat=$data[id]'>$phrases[edit_templates]</a> -   
    
     <a href='index.php?action=template_cat_edit&id=$data[id]'> $phrases[style_settings] </a>";
    if($data['id']!=1){
            print " - <a href='index.php?action=template_cat_del&id=$data[id]' onclick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete]</a>";
            }
            print "</td></tr>";
    }
    print "</table></center>";
}



          }
  //--------template cat edit --------
  if($action=="template_cat_edit"){
    if_admin("templates");

      $id= intval($id);
$qr= db_query("select * from store_templates_cats where id='$id'");
 print  "<p class=title align=center>  $phrases[the_templates] </p> ";
if(db_num($qr)){
$data = db_fetch($qr);
 print "<center>
 <form action=index.php method=post>
 <input type=hidden name=action value='template_cat_edit_ok'>
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
  if($action=="template_cat_add"){
    if_admin("templates");



print  "<p class=title align=center>  $phrases[the_templates] </p> ";

print "<center>
 <form action=index.php method=post>
 <input type=hidden name=action value='template_cat_add_ok'>
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
          if($action=="template_edit"){
    if_admin("templates");
   $id=intval($id);
$qr = db_query("select * from store_templates where id='$id'");
      if(db_num($qr)){
      $data = db_fetch($qr);
    $data['content'] = htmlspecialchars($data['content']);
    
     
 $cat_data = db_qr_fetch("select name from store_templates_cats where id='$data[cat]'");
print "<p align=$global_align><img src='images/link.gif'><a href='index.php?action=templates'>$phrases[the_templates] </a> / <a href='index.php?action=templates&cat=$data[cat]'>$cat_data[name]</a> / $data[name]</p>";



print "
  <center>
          <span class=title>$data[name]</span>  <br><br>
  <form method=\"POST\" action=\"index.php\">
  <input type='hidden' name='action' value='template_edit_ok'>
  <input type='hidden' name='id' value='$data[id]'>
   <input type='hidden' name='cat' value='$data[cat]'>

  <table width=80% class=grid><tr>
  <td> <b> $phrases[template_name] : </b></td><td>$data[name]</td></tr>
  <tr>
  <td> <b> $phrases[template_description] : </b></td><td><input type=text size=30 name=title value='$data[title]'></td></tr>
   <tr><td colspan=2 align=center>
        <textarea dir=ltr rows=\"20\" name=\"content\" cols=\"70\">$data[content]</textarea></td></tr>
        <tr><td colspan=2 align=center>
        <input type=\"submit\" value=\" $phrases[edit] \" name=\"B1\"></td></tr>
        </table>
</form></center>\n";
}else{
print_admin_table($phrases['err_wrong_url']);
        }
 }
//------------ template add ------------
  if($action=="template_add"){
if_admin("templates");

   $cat=intval($cat);
 $cat_data = db_qr_fetch("select name from store_templates_cats where id='$cat'");
print "<p align=$global_align><img src='images/link.gif'><a href='index.php?action=templates'>$phrases[the_templates] </a> / <a href='index.php?action=templates&cat=$cat'>$cat_data[name]</a> / $phrases[add_new_template]</p>";


print "
  <center>
          <span class=title>$phrases[add_new_template] </span>  <br><br>
  <form method=\"POST\" action=\"index.php\">
  <input type='hidden' name='action' value='template_add_ok'>
  <input type='hidden' name='cat' value='".intval($cat)."'>
  <table width=80% class=grid><tr>
  <td> <b> $phrases[template_name] : </b></td><td><input type=text size=30 name=name></td></tr>
  <tr>
  <td> <b> $phrases[template_description] : </b></td><td><input type=text size=30 name=title></td></tr>
   <tr><td colspan=2 align=center>
        <textarea dir=ltr rows=\"20\" name=\"content\" cols=\"70\"></textarea></td></tr>
        <tr><td colspan=2 align=center>
        <input type=\"submit\" value=\"$phrases[add_button]\" name=\"B1\"></td></tr>
        </table>
</form></center>\n";

 }


 //----------------------- Settings --------------------------------
 if($action == "settings" || $action=="settings_edit"){
 
     if_admin();


 if($action=="settings_edit"){
     
 
 
  if(is_array($stng)){
 for($i=0;$i<count($stng);$i++) {

        $keyvalue = current($stng);

       db_query("update store_settings set value='".db_escape($keyvalue,false)."' where name='".db_escape(key($stng))."'");


 next($stng);
}
}

         }


 load_settings();
 

 print "<center>
 <p align=center class=title>  $phrases[the_settings] </p>
 <form action=index.php method=post>
 <input type=hidden name=action value='settings_edit'>
 <table width=70% class=grid>
  
 <tr><td>  $phrases[site_name] : </td><td><input type=text name=stng[sitename] size=30 value='$settings[sitename]'> &nbsp; </td></tr>
  <tr><td> $phrases[show_sitename_in_subpages] </td><td>";
  print_select_row("stng[sitename_in_subpages]",array($phrases['no'],$phrases['yes']),$settings['sitename_in_subpages']);
  print "</td></tr>
 
 
 <tr><td>  $phrases[section_name] : </td><td><input type=text name=stng[section_name] size=30 value='$settings[section_name]'></td></tr>
 <tr><td> $phrases[show_section_name_in_subpages] </td><td>";
  print_select_row("stng[section_name_in_subpages]",array($phrases['no'],$phrases['yes']),$settings['section_name_in_subpages']);
  print "</td></tr>
 
  <tr><td>  $phrases[copyrights_sitename] : </td><td><input type=text name=stng[copyrights_sitename] size=30 value='$settings[copyrights_sitename]'></td></tr>
 
   <tr><td>  $phrases[mailing_email] : </td><td><input type=text dir=ltr name=stng[mailing_email] size=30 value='$settings[mailing_email]'></td></tr>
   <tr><td>  $phrases[admin_email] : </td><td><input type=text dir=ltr name=stng[admin_email] size=30 value='$settings[admin_email]'></td></tr>

 <tr><td> $phrases[page_dir] : </td><td><select name=stng[html_dir]>" ;
 if($settings['html_dir'] == "rtl"){$chk1 = "selected" ; $chk2=""; }else{ $chk2 = "selected" ; $chk1="";}
 print "<option value='rtl' $chk1>$phrases[right_to_left]</option>
 <option value='ltr' $chk2>$phrases[left_to_right]</option>
 </select>
 </td></tr>
  <tr><td>  $phrases[pages_lang] : </td><td><input type=text name=stng[site_pages_lang] size=30 value='$settings[site_pages_lang]'></td></tr>
    <tr><td>  $phrases[pages_encoding] : </td><td><input type=text name=stng[site_pages_encoding] size=30 value='$settings[site_pages_encoding]'></td></tr>
  <tr><td> $phrases[page_keywords] : </td><td><input type=text name=stng[header_keywords] size=30 value='$settings[header_keywords]'></td></tr>
   <tr><td> $phrases[page_description] : </td><td><input type=text name=stng[header_description] size=30 value='$settings[header_description]'></td></tr>

   
  </table>
   <br>
   <table width=70% class=grid>
  <tr><td>  $phrases[cp_enable_browsing]</td><td><select name=stng[enable_browsing]>";
  if($settings['enable_browsing']=="1"){$chk1="selected";$chk2="";}else{$chk1="";$chk2="selected";}
  print "<option value='1' $chk1>$phrases[cp_opened]</option>
  <option value='0' $chk2>$phrases[cp_closed]</option>
  </select></td></tr>
  <tr><td>$phrases[cp_browsing_closing_msg]</td><td><textarea cols=30 rows=5 name=stng[disable_browsing_msg]>$settings[disable_browsing_msg]</textarea>
  </td></tr>
   </table>
   <br>
   <table width=70% class=grid>
 <tr><td>  $phrases[products_perpage] : </td><td><input type=text name=stng[products_perpage] size=5 value='$settings[products_perpage]'></td></tr>
  <tr><td>  $phrases[orders_perpage] : </td><td><input type=text name=stng[orders_perpage] size=5 value='$settings[orders_perpage]'></td></tr>
  
 
  <tr><td>  $phrases[news_perpage] : </td><td><input type=text name=stng[news_perpage] size=5 value='$settings[news_perpage]'></td></tr>

 
 <tr><td>  $phrases[images_cells_count] : </td><td><input type=text name=stng[img_cells] size=5 value='$settings[img_cells]'></td></tr>
<tr><td>  $phrases[votes_expire_time] : </td><td><input type=text name=stng[votes_expire_hours] size=5 value='$settings[votes_expire_hours]'> $phrases[hour] </td></tr>

<tr><td>  $phrases[currency_mark] : </td><td><input type=text name=stng[currency] size=5 value='$settings[currency]'></td></tr>



    </table>
     <br>
   <table width=70% class=grid>
   <tr><td> $phrases[visitors_can_sort_products] : </td><td>" ;
 print_select_row("stng[visitors_can_sort_products]",array($phrases['no'],$phrases['yes']),$settings['visitors_can_sort_products']);
 print "</td></tr>
   <tr><td> $phrases[show_paid_option] : </td><td>" ;
 print_select_row("stng[show_paid_option]",array($phrases['no'],$phrases['yes']),$settings['show_paid_option']);
 print "</td></tr>

  <tr><td> $phrases[notify_client_when_order_status_change] : </td><td>" ;
 print_select_row("stng[default_status_change_notify]",array($phrases['no'],$phrases['yes']),$settings['default_status_change_notify']);
 print "</td></tr>
  
 
 
 <tr><td>$phrases[products_default_orderby] : </td><td>
<select size=\"1\" name=\"stng[products_default_orderby]\">";
for($i=0; $i < count($orderby_checks);$i++) {

$keyvalue = current($orderby_checks);
if($keyvalue==$settings['products_default_orderby']){$chk="selected";}else{$chk="";}

print "<option value=\"$keyvalue\" $chk>".key($orderby_checks)."</option>";;

 next($orderby_checks);
}
print "</select>&nbsp;&nbsp; <select name=stng[products_default_sort]> ";
if($settings['products_default_sort']=="asc"){$chk1="selected";$chk2="";}else{$chk1="";$chk2="selected";}
print "<option value='asc' $chk1>$phrases[asc]</option>
<option value='desc' $chk2>$phrases[desc]</option>
</select>
</td></tr>
   </table>

   
 <br>        
   <table width=70% class=grid>
 
 <tr><td>  $phrases[products_thumb_width] : </td><td><input type=text name=stng[products_thumb_width] size=5 value='$settings[products_thumb_width]'></td></tr>
  <tr><td>  $phrases[products_thumb_height] : </td><td><input type=text name=stng[products_thumb_hieght] size=5 value='$settings[products_thumb_hieght]'></td></tr>
    <tr><td>  $phrases[products_thumb_fixed] : </td><td>";
    print_select_row('stng[products_thumb_fixed]',array("$phrases[no]","$phrases[yes]"),$settings['products_thumb_fixed']);
    print "</td></tr>

  </table>
                      <br>
 <table width=70% class=grid>

 <tr><td>$phrases[the_search] : </td><td><select name=stng[enable_search]>" ;
 if($settings['enable_search']){$chk1 = "selected" ; $chk2 ="" ;}else{ $chk2 = "selected" ; $chk1 ="" ;}
 print "<option value=1 $chk1>$phrases[enabled]</option>
 <option value=0 $chk2>$phrases[disabled]</option>
 </select>
 </td></tr>

<tr><td>  $phrases[search_min_letters] : </td><td><input type=text name=stng[search_min_letters] size=5 value='$settings[search_min_letters]'>  </td></tr>

   </table>
   <br>
 <table width=70% class=grid>
  <tr><td>$phrases[default_style]</td><td><select name=stng[default_styleid]>";
  $qrt=db_query("select * from store_templates_cats order by id asc");
while($datat =db_fetch($qrt)){
print "<option value=\"$datat[id]\"".iif($settings['default_styleid']==$datat['id']," selected").">$datat[name]</option>";
}
  print "</select>
  </td>
 </table>
                     <br>
 <table width=70% class=grid>


 <tr><td>$phrases[os_and_browsers_statics] : </td><td><select name=stng[count_visitors_info]>" ;
 if($settings['count_visitors_info']){$chk1 = "selected" ; $chk2 ="" ;}else{ $chk2 = "selected" ; $chk1 ="" ;}
 print "<option value=1 $chk1>$phrases[enabled]</option>
 <option value=0 $chk2>$phrases[disabled]</option>
 </select>
 </td></tr>

  <tr><td>$phrases[visitors_hits_statics] : </td><td><select name=stng[count_visitors_hits]>" ;
 if($settings['count_visitors_hits']){$chk1 = "selected" ; $chk2 ="" ;}else{ $chk2 = "selected" ; $chk1 ="" ;}
 print "<option value=1 $chk1>$phrases[enabled]</option>
 <option value=0 $chk2>$phrases[disabled]</option>
 </select>
 </td></tr>

  <tr><td>$phrases[online_visitors_statics] : </td><td><select name=stng[count_online_visitors]>" ;
 if($settings['count_online_visitors']){$chk1 = "selected" ; $chk2 ="" ;}else{ $chk2 = "selected" ; $chk1 ="" ;}
 print "<option value=1 $chk1>$phrases[enabled]</option>
 <option value=0 $chk2>$phrases[disabled]</option>
 </select>
 </td></tr>


    </table>
                     
                     <br>
 <table width=70% class=grid>
    <tr><td>$phrases[registration] : </td><td><select name=stng[members_register]>" ;
 if($settings['members_register']){$chk1 = "selected" ; $chk2 ="" ;}else{ $chk2 = "selected" ; $chk1 ="" ;}
 print "<option value=1 $chk1>$phrases[cp_opened]</option>
 <option value=0 $chk2>$phrases[cp_closed]</option>
 </select>
 </td></tr>


  <tr><td>$phrases[security_code_in_registration] : </td><td><select name=stng[register_sec_code]>" ;
 if($settings['register_sec_code']){$chk1 = "selected" ; $chk2 ="" ;}else{ $chk2 = "selected" ; $chk1 ="" ;}
 print "<option value=1 $chk1>$phrases[enabled]</option>
 <option value=0 $chk2>$phrases[disabled]</option>
 </select>
 </td></tr>

 <tr><td>$phrases[auto_email_activate]: </td><td><select name=stng[auto_email_activate]>" ;
 if($settings['auto_email_activate']){$chk1 = "selected" ; $chk2 ="" ;}else{ $chk2 = "selected" ; $chk1 ="" ;}
 print "<option value=1 $chk1>$phrases[enabled]</option>
 <option value=0 $chk2>$phrases[disabled]</option>
 </select>
 </td></tr>

 <tr><td>  $phrases[msgs_count_limit] : </td><td><input type=text name=stng[msgs_count_limit] size=5 value='$settings[msgs_count_limit]'>  $phrases[message] </td></tr>

<tr><td>  $phrases[username_min_letters] : </td><td><input type=text name=stng[register_username_min_letters] size=5 value='$settings[register_username_min_letters]'> </td></tr>

<tr><td> $phrases[username_exludes] : </td><td><input type=text name=stng[register_username_exclude_list] dir=ltr size=20 value='$settings[register_username_exclude_list]'> </td></tr>


  </table>
                     <br>
    
 <table width=70% class=grid>                
                   
 <tr><td>$phrases[show_prev_votes] : </td><td><select name=stng[other_votes_show]>" ;
 if($settings['other_votes_show']){$chk1 = "selected" ; $chk2 ="" ;}else{ $chk2 = "selected" ; $chk1 ="" ;}
 print "<option value=1 $chk1>$phrases[enabled]</option>
 <option value=0 $chk2>$phrases[disabled]</option>
 </select>
 </td></tr>
 <tr><td> $phrases[max_count] : </td><td><input type=text name=stng[other_votes_limit] dir=ltr size=4 value='$settings[other_votes_limit]'> </td></tr>

  <tr><td>$phrases[orderby] : </td><td> ";
  print_select_row("stng[other_votes_orderby]",array("rand()"=>"$phrases[random]","id asc"=>"$phrases[the_date] $phrases[asc]","id desc"=>"$phrases[the_date] $phrases[desc]"),$settings['other_votes_orderby']);
  print "</td></tr>
 </table><br>
 
 
 <table width=70% class=grid>
 <tr><td>$phrases[emails_msgs_default_type] : </td><td><select name=stng[mailing_default_use_html]>" ;
 if($settings['mailing_default_use_html']){$chk1 = "selected" ; $chk2 ="" ;}else{ $chk2 = "selected" ; $chk1 ="" ;}
 print "<option value=1 $chk1>HTML</option>
 <option value=0 $chk2>TEXT</option>
 </select>
 </td></tr>
 <tr><td> $phrases[emails_msgs_default_encoding] : </td><td><input type=text name=stng[mailing_default_encoding] size=20 value='$settings[mailing_default_encoding]'> <br> * $phrases[leave_blank_to_use_site_encoding]</td></tr>
</table>";


   //--------------- Load Settings Plugins --------------------------
$pls = load_plugins("settings.php");
  if(is_array($pls)){foreach($pls as $pl){include($pl);}}
//----------------------------------------------------------------

  print "
  <br>
                    <table width=70% class=grid>
  <tr><td>  $phrases[uploader_system] : </td><td><select name=stng[uploader]>" ;
 if($settings['uploader']){$chk1 = "selected" ; $chk2=""; }else{ $chk2 = "selected" ; $chk1="";}
 print "<option value=1 $chk1>$phrases[enabled]</option>
 <option value=0 $chk2>$phrases[disabled]</option>
 </select>
 </td></tr>
 <tr><td> $phrases[disable_uploader_msg]  : </td><td><input type=text name=stng[uploader_msg] size=30 value='$settings[uploader_msg]'></td></tr>
 <tr><td>  $phrases[uploader_path] : </td><td><input dir=ltr type=text name=stng[uploader_path] size=30 value='$settings[uploader_path]'></td></tr>
 <tr><td>  $phrases[uploader_allowed_types] : </td><td><input dir=ltr type=text name=stng[uploader_types] size=30 value='$settings[uploader_types]' style=\"font-family:Arial, Helvetica, sans-serif;font-weight:bold;\"></td></tr>

<tr><td> $phrases[uploader_thumb_width] : </td><td><input type=text name=stng[uploader_thumb_width] size=5 value='$settings[uploader_thumb_width]'> $phrases[pixel] </td></tr>
<tr><td>  $phrases[uploader_thumb_hieght]  : </td><td><input type=text name=stng[uploader_thumb_hieght] size=5 value='$settings[uploader_thumb_hieght]'> $phrases[pixel] </td></tr>


 <tr><td colspan=2 align=center><input type=submit value=' $phrases[edit] '></td></tr>
 </table></center>" ;

         }
//---------------------------------- Statics ---------------------
if($action=="statics"){
        if_admin();


                if($op){
     print "<center><table width=50% class=grid>
<tr><td><ul>";
  foreach($op as $op){
 //---------------------
 if($op=="statics_rest"){
        db_query("delete from info_hits");
        db_query("update info_browser set count=0");
        db_query("update info_os set count=0");
        db_query("update info_best_visitors  set v_count=0");
        print "<li>$phrases[visitors_statics_rest_done]</li>" ;
                }

 //---------------------
          }
          print "</ul></td></tr></table>";
          }
$data_frstdate = db_qr_fetch("select * from info_hits order by date asc limit 1");
 if(!$data_frstdate['date']){$data_frstdate['date']= "$phrases[cp_not_available]"; }
 $qr_total=db_query("select hits from info_hits");
 $total_hits = 0 ;
 while($data_total = db_fetch($qr_total)){
 $total_hits += $data_total['hits'];
         }

print "<center><p class=title> $phrases[cp_visitors_statics] </p>
<table width=50% class=grid>
<tr><td><b> $phrases[cp_counters_start_date] </b></td><td>$data_frstdate[date]
</td></tr>
<tr><td><b> $phrases[cp_total_visits] </b></td><td>$total_hits
</td></tr>
</table>
<br>
 <p class=title>  $phrases[cp_rest_counters] </p>
<form action='index.php' method=post onSubmit=\"return confirm('$phrases[are_you_sure]');\">
<input type=hidden name=action value='statics'>
<table width=50% class=grid><tr><td>
<input type='checkbox' value='statics_rest'  name='op[]' >$phrases[cp_visitors_statics]<br><br>



</td></tr><tr><td align=center>
<input type=submit value=' $phrases[cp_rest_counters_do] '>
</table></center>
</form>";
        }
        

      

 //-------------------- Access Log -------------
 if($action=="access_log"){
     if_admin();
     
     $qr=db_query("select * from store_access_log order by id desc");
     print "<center>
     <p class=title>$phrases[access_log]</p>
     <table width=90% class=grid>";
       print "<tr><td><b>$phrases[username]</b></td><td><b>$phrases[the_date]</b></td><td><b>$phrases[the_status]</b></td></tr>";   
     while($data = db_fetch($qr)){
         print "<tr><td>$data[username]</td><td>$data[date]</td><td>$data[status]</td></tr>";
     }
     print "</table></center>";
 }
//------------------------------ Phrases -------------------------------------
if($action=="phrases" || $action=="phrases_update"){

if_admin("phrases");

$cat = intval($cat);

if($action=="phrases_update"){
        $i = 0;
        foreach($phrases_ids  as $id){
        db_query("update store_phrases set value='".db_escape($phrases_values[$i],false)."' where id='$phrases_ids[$i]'");

        ++$i;
                }
                }

if($group){
  $group = htmlspecialchars($group);
$cat_data = db_qr_fetch("select name from store_phrases_cats where id='".db_escape($group)."'");

print "<p align=$global_align><img src='images/link.gif'><a href='index.php?action=phrases'>$phrases[the_phrases] </a> / $cat_data[name]</p>";


         $qr = db_query("select * from store_phrases where cat='".db_escape($group)."'");
        if (db_num($qr)){

        print "<form action=index.php method=post>
        <input type=hidden name=action value='phrases_update'>
        <input type=hidden name=group value='$group'>
        <center><table width=60% class=grid>";

        $i = 0;
        while($data=db_fetch($qr)){
         print "<tr onmouseover=\"set_tr_color(this,'#EFEFEE');\" onmouseout=\"set_tr_color(this,'#FFFFFF');\"><td>$data[name]</td><td>
         <input type=hidden name=phrases_ids[$i] value='$data[id]'>
         <input type=text name=phrases_values[$i] value=\"$data[value]\" size=30>
         </td></tr> ";
         ++$i;
                }
                print "<tr><td colspan=2 align=center><input type=submit value=' $phrases[edit] '></td></tr>
                </table></form></center>";
                }else{
                	 print "<center><table width=60% class=grid><tr><td align=center> $phrases[cp_no_phrases] </td></tr></table></center>";
                	 }

}else{
print "<p class=title align=center> $phrases[the_phrases] </p><br>  ";
	$qr = db_query("select * from store_phrases_cats order by id asc");
	 print "<center><table width=60% class=grid>";
	while($data =db_fetch($qr)){
	print "<tr><td><a href='index.php?action=phrases&group=$data[id]'>$data[name]</a></td></tr>";
	}
	print "</table></center>";
}
}
 //--------------- Load Admin Plugins --------------------------
$pls = load_plugins("admin.php");
  if(is_array($pls)){foreach($pls as $pl){include($pl);}}                    
//--------------------------------------------------

?>
</td></tr></table>
<?

}else{
if(!$disable_auto_admin_redirect){
if(strchr($_SERVER['HTTP_HOST'],"www.")){
  print "<SCRIPT>window.location=\"http://".str_replace("www.","",$_SERVER['HTTP_HOST']).$_SERVER['REQUEST_URI']."\";</script>";
  die();
  }
 }

if($global_lang=="arabic"){
print "<html dir=$global_dir>
<title>$sitename  - ·ÊÕ… «· Õﬂ„ </title>";
}else{
	print "<html dir=$global_dir>
<title>$sitename  - Control Panel </title>";
	}
print "<META http-equiv=Content-Language content=\"$settings[site_pages_lang]\">
<META http-equiv=Content-Type content=\"text/html; charset=$settings[site_pages_encoding]\">";
print "<link href=\"images/style.css\" type=text/css rel=stylesheet>
<center>
<br>
<table width=60% class=grid><tr><td align=center>

<form action=\"index.php\" method=\"post\"\">
                 <table><tr><td><img src='images/users.gif'></td><td>

                <table dir=$global_dir cellpadding=\"0\" cellspacing=\"3\" border=\"0\">
                <tr>
                        <td class=\"smallfont\">$phrases[cp_username]</td>
                        <td><input type=\"text\" class=\"button\" name=\"username\"  size=\"10\" tabindex=\"1\" ></td>
                        <td class=\"smallfont\" colspan=\"2\" nowrap=\"nowrap\"></td>
                </tr>
                <tr>
                        <td class=\"smallfont\">$phrases[cp_password]</td>
                        <td><input type=\"password\"  name=\"password\" size=\"10\" tabindex=\"2\" /></td>
                        <td>
                        <input type=\"submit\" class=\"button\" value=\"$phrases[cp_login_do]\" tabindex=\"4\" accesskey=\"s\" /></td>
                </tr>

</td>
</tr>
                </table>
                <input type=\"hidden\" name=\"s\" value=\"\" />
                <input type=\"hidden\" name=\"action\" value=\"login\" />
                </td></tr></table>
                </form> </td></tr></table>
                </center>\n";


if(COPYRIGHTS_TXT_ADMIN_LOGIN){
if($global_lang=="arabic"){
	print "<br>
                <center>
<table width=60% class=grid><tr><td align=center>
  Ã„Ì⁄ ÕﬁÊﬁ «·»—„Ã… „Õ›ÊŸ… <a href='http://allomani.com' target='_blank'> ··Ê„«‰Ì ··Œœ„«  «·»—„ÃÌ… </a>  © 2009
</td></tr></table></center>";
}else{
print "<br>
                <center>
<table width=60% class=grid><tr><td align=center>
  Copyright © 2009 <a href='http://allomani.com' target='_blank'>Allomani&trade;</a>  - All Programming rights reserved
</td></tr></table></center>";
}
}

if(file_exists("demo_msg.php")){
include_once("demo_msg.php");
}
}
?>