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
$backup_obj->comments = false;
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

<table width=100% height=100%><tr><td width=20% valign=top>

<?
print str_replace("{username}",$user_info['username'],$phrases['cp_welcome_msg']); 
print " <br><br>";

 require(ADMIN_DIR."/admin_menu.php") ;
?>

</td>
 <td width=1 background='images/dot.gif'></td>
<td valign=top> <br>