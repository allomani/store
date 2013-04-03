<?  
define('ADMIN_DIR', (($getcwd = str_replace("\\","/",getcwd())) ? $getcwd : '.'));  
chdir('./../');
define('CWD', (($getcwd = str_replace("\\","/",getcwd())) ? $getcwd : '.'));
define('IS_ADMIN', 1);
define('THIS_PAGE', "admin"); 


require(CWD . "/global.php") ;
require(CWD . "/includes/functions_admin.php") ; 


//--------- add Main user --------//
if($op=="add_main_user"){
$users_num = db_qr_fetch("select count(id) as count from store_user");
if($users_num['count'] == 0 && trim($cp_username) && trim($cp_password)){
db_query("insert into store_user (username,password,email,group_id) values('".db_escape($cp_username,false)."','".db_escape($cp_password,false)."','".db_escape($cp_email)."','1')");
}
}
//-------- First time setup ----------//
$users_num = db_qr_fetch("select count(*) as count from store_user");       
if($users_num['count'] == 0){

if($global_lang=="arabic"){
$global_dir = "rtl" ;
print "<html dir=$global_dir>
<title>$sitename - لوحة التحكم </title>" ;
}else{
$global_dir = "ltr" ;
print "<html dir=$global_dir>
<title>$sitename - Control Panel </title>" ;
}
print "<META http-equiv=Content-Language content=\"$settings[site_pages_lang]\">
<META http-equiv=Content-Type content=\"text/html; charset=$settings[site_pages_encoding]\">
<link href=\"css/style.css\" type=text/css rel=stylesheet>
<script src='$scripturl/js/StrongPassword.js'></script>";

print "<center>
<form action='index.php' method=post>
<input type=hidden name=op value='add_main_user'>
<br><br><table width=50% class=grid>
<tr><td colspan=2><h2>$phrases[create_main_user]</h2></td></tr>
<tr><td>$phrases[username]</td><td><input type=text name=cp_username dir=ltr required='required'></td></tr>
<tr><td>$phrases[password]</td><td><input type=text id='cp_password'  name='cp_password' dir=ltr required='required'> &nbsp; <input type=button value=\"Generate\" id='generate_pwd'></td></tr>
<tr><td>$phrases[email]</td><td><input type=text name=cp_email dir=ltr></td></tr>

<tr><td></td><td>
<div id=\"passwordDescription\">-</div>
<div id=\"passwordStrength\" class=\"strength0\"></div>
</td></tr>
<tr><td colspan=2 align=center><input type=submit value=' $phrases[add_button] '></td></tr>
</table>
</form></center>";
?>
 <script>
        $(function(){
           $('#generate_pwd').click(function(e){
           $('#cp_password').val(GenerateAndValidate(12,1));
             passwordStrength($('#cp_password').val());
        });
        $('#cp_password').on('change',function(){
            passwordStrength($(this).val());
        });
         $('#cp_password').on('keyup',function(){
            passwordStrength($(this).val());
        });

        });
        </script>
<?
die();   
}

 if(!$disable_auto_admin_redirect){
if(strchr($_SERVER['HTTP_HOST'],"www.")){
  print "<SCRIPT>window.location=\"http://".str_replace("www.","",$_SERVER['HTTP_HOST']).$_SERVER['REQUEST_URI']."\";</script>";
  die();
  }
 }
 
            
if (!check_admin_login()) {
 redirect("login.php");
}

//--------------------------- Backup Job ------------------------------
if($action=="backup_db_do"){
if(!$disable_backup){
if_admin();
$backups_folder = CWD . '/' . $settings['uploader_path'] . '/backups/';
$b = new DB_Backup();
$r = $b->start($op,$backups_folder,$filename);
if($r){
    $output = $phrases['backup_done_successfully'];
    }else{
        $output = $b->error;
}

        }else{
        $output =  $disable_backup ;
                }
}


//---------- load editor --------//
require(CWD."/".$code_editor_path."/functions.php");
require (CWD."/".$wysiwyg_editor_path."/editor_init_functions.php") ;
editor_init();


//----- load headers ------//
print "<!doctype html>";
if($global_lang=="arabic"){
$global_dir = "rtl" ;
print "<html dir=$global_dir>
<title>$sitename - لوحة التحكم </title>" ;
}else{
$global_dir = "ltr" ;
print "<html dir=$global_dir>
<title>$sitename - Control Panel </title>" ;
}
print "<META http-equiv=Content-Language content=\"$settings[site_pages_lang]\">
<META http-equiv=Content-Type content=\"text/html; charset=$settings[site_pages_encoding]\">

<link href=\"css/style.css\" type=text/css rel=stylesheet />
<link href=\"css/jquery-ui.css\" type=text/css rel=stylesheet />

<script src='javascript.js' type=\"text/javascript\" language=\"javascript\"></script>


<script src=\"$scripturl/js/jquery.js\" type=\"text/javascript\"></script>
<script src=\"$scripturl/js/jquery-ui.min.js\" type=\"text/javascript\"></script>
<script src=\"$scripturl/js/jquery.cookie.js\" type=\"text/javascript\"></script>";
?>

    <link href="css/ui.dynatree.<?=$global_dir;?>.css" rel="stylesheet" type="text/css" id="skinSheet">
    <script src="<?=$scripturl;?>/js/jquery.dynatree.min.js" type="text/javascript"></script>
        
<?        
print "

<script src=\"$scripturl/js/StrongPassword.js\"></script>";
editor_html_init();

if(file_exists(CWD . "/install/")){
print "<h3><center><font color=red>Warning : Installation Folder Exists , Please Delete it</font></center></h3>";
}

?>  

<table width=100% height=100%><tr><td class="main_side">

<?
print str_replace("{username}",$user_info['username'],$phrases['cp_welcome_msg']); 
print " <br><br>";

 require(ADMIN_DIR."/admin_menu.php") ;
?>

</td>
<td class="main_content">