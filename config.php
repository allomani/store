<?
//----------- Database Settings -------------- 

$config['db']['host'] = "127.0.0.1" ;
$config['db']['name'] = "store200" ;
$config['db']['username'] = "root" ;
$config['db']['password'] = "" ;
$config['db']['charset'] = "utf8"; 
$config['db']['extension'] = 'mysqli';

//---------- Script Settings ---------- 

$global_lang = "arabic" ;

$copyrights_lang = "arabic";

$preview_text_limit = 300 ;   //letters

$online_visitor_timeout = 800; // in seconds 

$use_editor_for_pages = 1 ;  // 1 enable - 0 disable
                
$access_log_expire=90 ; // days

$sitemap_perpage = 40000;

$arabic_date_months = true;
$arabic_date_days = true;

$admin_referer_check = true;

//$disable_backup = "عفوا , هذه الخاصية غير مفعلة في النسخة التجريبية" ;
//$disable_repair = "عفوا , هذه الخاصية غير مفعلة في النسخة التجريبية" ;

//--------- Editors ---------------
$wysiwyg_editor_path  = "editors/ckeditor";       // no_editor : to remove editor 
$code_editor_path = "editors/codemirror";



//------------ Cache --------------
$config['cache']['engine'] = "nocache" ; // memcache - xcache - filecache - nocache 
$config['cache']['expire'] = 3600 ; //seconds
$config['cache']['memcache_host'] = "localhost";
$config['cache']['memcache_port'] = 11211;
$config['cache']['filecache_dir'] = "cache";
$config['cache']['prefix'] = "main:";


//----------- Error Handling  ---------

$config['debug']['enable'] =true;

$config['debug']['custom_error_handler'] = true;

$config['debug']['display_errors'] = true;
$config['debug']['log_errors']  = false;
 
 
$config['debug']['show_mysql_errors'] = true ;
$config['debug']['log_mysql_errors'] = false;


$config['debug']['logs_path'] = "data/logs";
$config['debug']['log_max_size'] = 1024*1024;

//---------- to use remote members database ----------
$config['connector']['enable'] = false;
$config['connector']['db_host'] = "localhost";
$config['connector']['db_name'] = "forum";
$config['connector']['db_username'] = "root";
$config['connector']['db_password'] = "";
$config['connector']['db_charset'] = "utf8";
$config['connector']['custom_members_table'] = "";
$config['connector']['type'] = "vbulletin";

//--------------- to use SMTP Server ---------
$config['smtp']['enable'] = false;
$config['smtp']['host_name']="mail.allomani.com";
$config['smtp']['host_port']= 25;
$config['smtp']['ssl']=0;
$config['smtp']['username'] = "info@allomani.com";
$config['smtp']['password'] = "password_here";
$config['smtp']['timeout'] = 10;
$config['smtp']['debug'] = 0;
$config['smtp']['show_errors'] = 1;


//-------- Cookies Settings  -----------
$config['cookies']['prefix'] = "store200_";
$config['cookies']['timemout'] = 365 ; //days
$config['cookies']['path'] = "/" ;
$config['cookies']['domain'] = "";

//---- session Settings ----
$config['session']['cookie_expire'] = $config['cookies']['timemout'];
$config['session']['cookie_name'] = "sid";
$config['session']['prefix'] = "store";
$config['session']['ip_check'] = true;

?>