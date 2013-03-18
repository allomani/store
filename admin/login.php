<?
define('ADMIN_DIR', (($getcwd = str_replace("\\","/",getcwd())) ? $getcwd : '.'));  
chdir('./../');
define('CWD', (($getcwd = str_replace("\\","/",getcwd())) ? $getcwd : '.'));
define('IS_ADMIN', 1);
define('THIS_PAGE', "admin"); 


require(CWD . "/global.php") ;
require(CWD . "/includes/functions_admin.php") ; 
    
     if(!$re_link){$re_link=iif($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_REFERER'],"index.php");} 
              
//----------- Login Script ----------------------------------------------------------
if ($action == "login" && $username && $password ){

      
     $result=db_query("select * from store_user where username='".db_escape($username,false)."'");
     if(db_num($result)){
     $login_data=db_fetch($result);

 
       if($login_data['password']==$password){
 access_log_record($login_data['username'],"Login Done");
 
$session->set('admin_id', $login_data['id']);
$session->set('admin_username', $login_data['username']);
$session->set('admin_password', md5($login_data['password']));
   redirect($re_link);
  //   print "<SCRIPT>window.location=\"index.php\";</script>";
      exit();
       }else{
            access_log_record($login_data['username'],"Login Invalid Password"); 
              print "<link href=\"images/style.css\" type=text/css rel=stylesheet>\n";
              print "<br><center><table width=60% class=grid><tr><td align=center> $phrases[cp_invalid_pwd] </td></tr></table></center>";

              }
            }else{
                  access_log_record($username,"Login Invalid Username");        
                 print " <link href=\"css/style.css\" type=text/css rel=stylesheet>    \n";
                    print "<br><center><table width=60% class=grid><tr><td align=center>   $phrases[cp_invalid_username] </td></tr></table></center>";

                    }
              }elseif($action == "logout"){
                  
                
                    $session->set('admin_id');
                    $session->set('admin_username');
                    $session->set('admin_password');
                    

                  redirect("login.php");
                  //print "<SCRIPT>window.location=\"index.php\";</script>";

                      }
//-------------------------------------------------------------------------------------------


if($global_lang=="arabic"){
print "<html dir=$global_dir>
<title>$sitename  - لوحة التحكم </title>";
}else{
    print "<html dir=$global_dir>
<title>$sitename  - Control Panel </title>";
    }
print "<META http-equiv=Content-Language content=\"$settings[site_pages_lang]\">
<META http-equiv=Content-Type content=\"text/html; charset=$settings[site_pages_encoding]\">";
print "<link href=\"css/style.css\" type=text/css rel=stylesheet>


<form action=\"login.php\" method=\"post\">
  <input type=\"hidden\" name=\"action\" value=\"login\" />
  <input type=\"hidden\" name=\"re_link\" value=\"".htmlspecialchars($re_link)."\">
           <div class='login_form_wrapper'>
           <div class='login_form'>      
<table>
                  <tr>
                   
                        <td><input type=\"text\" class=\"button\" name=\"username\"  size=\"40\" tabindex=\"1\" placeholder=\"$phrases[cp_username]\" required='required'></td>
                   
                </tr>
                <tr>
               
                        <td>
                       
                        <input type=\"password\"  id='password' name=\"password\" size=\"40\" tabindex=\"2\" placeholder=\"$phrases[cp_password]\" required='required'></td> 
                </tr>

                <tr>
                <td class='text-center'>
                   <input type=\"submit\" class=\"button\" value=\"$phrases[cp_login_do]\" tabindex=\"4\">
                </td>
                </table>
                </div>
             </div>
                </form>";


if(COPYRIGHTS_TXT_ADMIN_LOGIN){
if($global_lang=="arabic"){
    print "<br>
                <center>
<table style='border-radius: 8px;position: absolute;bottom: 10px;margin: 5px;' class=grid><tr><td align=center>
  جميع حقوق البرمجة محفوظة <a href='http://allomani.com' target='_blank'> للوماني للخدمات البرمجية </a>  © ".SCRIPT_YEAR."
</td></tr></table></center>";
}else{
print "<br>
                <center>
<table style='border-radius: 8px;position: absolute;bottom: 10px;margin: 5px;' class=grid><tr><td align=center>
  Copyright © ".SCRIPT_YEAR." <a href='http://allomani.com' target='_blank'>Allomani&trade;</a>  - All Programming rights reserved
</td></tr></table></center>";
}
}

if(file_exists("demo_msg.php")){
include_once("demo_msg.php");
}