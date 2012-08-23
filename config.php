<?
//----------- Database Settings -------------- 

$db_host = "localhost" ;
$db_name = "store" ;
$db_username = "root" ;
$db_password = "" ;


//---------- Script Settings ---------- 
$blocks_width = "17%" ;

$editor_path  = "ckeditor";       // no_editor : to remove editor 

$global_lang = "arabic" ;

$copyrights_lang = "arabic";

$preview_text_limit = 300 ;   //letters

$online_visitor_timeout = 800; // in seconds 

$use_editor_for_pages = 1 ;  // 1 enable - 0 disable

$default_timezone = "Etc/GMT-3"; //time zone
                        
$access_log_expire=90 ; // days

$full_text_search = true;  
 
//$default_uploader_chmod = "777";

//$disable_backup = "кнцЧ , хах ЧсЮЧеэЩ лэб унксЩ нэ ЧсфгЮЩ ЧсЪЬбэШэЩ" ;
//$disable_repair = "кнцЧ , хах ЧсЮЧеэЩ лэб унксЩ нэ ЧсфгЮЩ ЧсЪЬбэШэЩ" ;

//----------- Error Handling  ---------
$custom_error_handler = false;

$display_errors = false;
$log_errors  = false;
 
 
$show_mysql_errors = false ;
$log_mysql_errors = false;


$logs_path = "data/logs";
$log_max_size = 1024*1024;

$debug =false;

//---------- to use remote members database ----------
$members_connector['enable'] = 0;
$members_connector['db_host'] = "localhost";
$members_connector['db_name'] = "forum";
$members_connector['db_username'] = "root";
$members_connector['db_password'] = "";
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
//----------Safe Functions -------------
// this function may used by moderators so dont include any mysql or file related functions .
$safe_functions = array('and',
                        'or',
                        'xor',
                        'if',
                        'lsn',
                        'snd',
                        'add2fav',
                        'substr',
                        'get_image',
                        'check_member_login',
                        'print',
                        'echo',
                        'in_array',
                        'is_array',
                        'is_numeric',
                        'isset',
                        'empty',
                        'defined',
                        'array',
                        'open_table',
                        'close_table',
                        'strpos',
                        'strlen',
                        'get_rss_head_links',
                        'login_redirect',
                        'get_file_field_value',
                        'get_member_field_value',
                        'print_style_selection',
                        'iif',
                        'get_template','urlencode','count','str_replace','strchr',
                        'get_song_field_value','sync_urls_sets','sync_songs_fields_sets','compile_template','strtotime','date',
                        'intval','htmlspecialchars','number_format',
                        'open_block','close_block','get_short_details_fields_data');

?>