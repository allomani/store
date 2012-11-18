<?
//----------- Database Settings -------------- 

$db_host = "127.0.0.1" ;
$db_name = "store200" ;
$db_username = "root" ;
$db_password = "" ;
$db_charset = "utf8"; 
$db_extension = "mysqli";


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

$access_log_expire=90 ; // days

$admin_referer_check = true;

//$disable_backup = "عفوا , هذه الخاصية غير مفعلة في النسخة التجريبية" ;
//$disable_repair = "عفوا , هذه الخاصية غير مفعلة في النسخة التجريبية" ;

//--------- Editors ---------------
$wysiwyg_editor_path  = "editors/ckeditor";       // no_editor : to remove editor 
$code_editor_path = "editors/codemirror";



//------------ Cache --------------
$cache_srv['engine'] = "nocache" ; // memcache - xcache - filecache - nocache 
$cache_srv['expire'] = 3600 ; //seconds
$cache_srv['memcache_host'] = "localhost";
$cache_srv['memcache_port'] = 11211;
$cache_srv['filecache_dir'] = "cache";
$cache_srv['prefix'] = "main:";


//----------- Error Handling  ---------
$custom_error_handler = true;

$display_errors = true;
$log_errors  = false;
 
 
$show_mysql_errors = true ;
$log_mysql_errors = false;


$logs_path = "data/logs";
$log_max_size = 1024*1024;

$debug =true;

//---------- to use remote members database ----------
$members_connector['enable'] = 0;
$members_connector['db_host'] = "localhost";
$members_connector['db_name'] = "forum";
$members_connector['db_username'] = "root";
$members_connector['db_password'] = "";
$members_connector['db_charset'] = "utf8";
$members_connector['custom_members_table'] = "";
$members_connector['connector_file'] = "vbulliten.php";

//--------------- to use SMTP Server ---------
$smtp_settings['enable'] = 0;
$smtp_settings['host_name']="mail.allomani.com";
$smtp_settings['host_port']= 25;
$smtp_settings['ssl']=0;
$smtp_settings['username'] = "info@allomani.com";
$smtp_settings['password'] = "password_here";
$smtp_settings['timeout'] = 10;
$smtp_settings['debug'] = 0;
$smtp_settings['show_errors'] = 1;


//-------- Cookies Settings  -----------
$cookies_prefix = "store_";
$cookies_timemout = 365 ; //days
$cookies_path = "/" ;
$cookies_domain = "";
$session_cookie_save = true;
$session_cookie_name = "sid";
$session_prefix = "store";
$session_ip_check = true;

?>