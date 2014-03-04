<? 
// Edited  : 24-10-2009

$version_number = "1.0" ;  
//-----------------------------
define("GLOBAL_LOADED",true);
define("MEMBER_SQL","member_sql");
//------------------------------
//----------- current work dir definition -------
define('CWD', (($getcwd = getcwd()) ? str_replace("\\","/",$getcwd) : '.'));
//-----------------------------

require(CWD . "/config.php") ;


 $data_cat_cache = array();
$cat_fields_cache = array();
$cat_short_fields_cache = array();


//---------- custom error handler --------//
if($custom_error_handler){
$old_error_handler = set_error_handler("error_handler");
}  


//----- remove slashes if magic quotes -----//
function stripslashes_deep($value){
   return (is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value));
}

if(get_magic_quotes_gpc()){ 
$_POST = array_map('stripslashes_deep',$_POST);
$_GET = array_map('stripslashes_deep',$_GET); 
$_COOKIE = array_map('stripslashes_deep',$_COOKIE); 
}

//--------- extract variabls -----------------------
 if (!empty($_POST)) {extract($_POST);}
if (!empty($_GET)) {extract($_GET);}
if (!empty($_ENV)) {extract($_ENV);}
//-----------------------------------------------------


//------ clean global vars ---------//
$_SERVER['QUERY_STRING'] = strip_tags($_SERVER['QUERY_STRING']);
$_SERVER['PHP_SELF'] = strip_tags($_SERVER['PHP_SELF']);
$_SERVER['REQUEST_URI'] = strip_tags($_SERVER['REQUEST_URI']);
$PHP_SELF = strip_tags($_SERVER['PHP_SELF']);

define("CUR_FILENAME",$PHP_SELF);

//---------------------- common variables types -----------------------
 if($id && !is_array($id)){$id=intval($id);}
 if($cat && !is_array($cat)){ $cat=intval($cat);}

//------------- timezone ------------------
if($default_timezone){date_default_timezone_set($default_timezone);} 
//-------------------------------------------
//-------------------------------------------------------------------

require(CWD . "/includes/functions_db.php") ;
//---------------------------
db_connect($db_host,$db_username,$db_password,$db_name);




// ------------- lang dir -------------
if($global_lang=="arabic"){
$global_dir = "rtl" ;
$global_align = "right" ;
$global_align_x = "left"; 
}else{
$global_dir = "ltr" ;
$global_align = "left" ;
$global_align_x = "right" ; 
}

//--------------- Load Phrases ---------------------
$phrases = array();
$qr = db_query("select * from store_phrases");
while($data = db_fetch($qr)){

$phrases["$data[name]"] = $data['value'] ;
        }
 //------------------------------       
 
 
 //-------- fields in short details count ----//
 $data=db_qr_fetch("select count(*) as count from store_fields_sets where in_short_details=1 and active=1");
 $short_details_fields_count = intval($data['count']);
 //---------------------------------------------//
 
   
$actions_checks = array(
"$phrases[main_page]" => 'main' ,
"$phrases[browse_products]" => 'browse',
"$phrases[product_details]" => 'product_details',   
"$phrases[the_news]" => 'news',
"$phrases[pages]" => 'pages',
"$phrases[the_search]" => 'search' ,
"$phrases[the_votes]" => 'votes',
"$phrases[the_statics]" => 'statics',
"$phrases[contact_us]" => 'contactus'
);


$permissions_checks = array(
"$phrases[hot_items]" => 'hot_items' , 
"$phrases[the_templates]" => 'templates' ,
"$phrases[the_news]" => 'news' ,
"$phrases[the_phrases]" => 'phrases' ,
"$phrases[the_banners]" => 'adv',
"$phrases[the_votes]" => 'votes',
"$phrases[the_clients]" => 'clients',
"$phrases[the_orders]" => 'orders', 
"$phrases[orders_status]" => 'orders_status'
);


$banners_places = array(
"$phrases[offers_menu]"=>'offer',
"$phrases[bnr_header]"=> 'header',
"$phrases[bnr_footer]"=> 'footer',
"$phrases[bnr_open]" => 'open',
"$phrases[bnr_close]" => 'close',
"$phrases[bnr_menu]"=> 'menu'

);


$orderby_checks = array(
"$phrases[the_date]" => 'id',
"$phrases[the_price]" => 'price',
"$phrases[the_name]" => 'name',
"$phrases[availability]" => 'available' 
);
        

        
$settings = array();

//--------------- Get Settings --------------------------
function load_settings(){
global  $settings ;
$qr = db_query("select * from store_settings");
while($data = db_fetch($qr)){

$settings["$data[name]"] = $data['value'] ;
        }
}

 //------------------ Load Settings ---------
load_settings();


$sitename = $settings['sitename'] ;
$section_name = $settings['section_name'] ;
$siteurl = "http://$_SERVER[HTTP_HOST]" ;
$script_path = trim(str_replace(rtrim(str_replace('\\', '/',$_SERVER['DOCUMENT_ROOT']),"/"),"",CWD),"/");
$scripturl = $siteurl . iif($script_path,"/".$script_path,"");
$upload_types = explode(',',str_replace(" ","",$settings['uploader_types']));
$mailing_email = $settings['mailing_email'];


//------ validate styleid functon ------
function is_valid_styleid($styleid){
if(is_numeric($styleid)){
$data = db_qr_fetch("select count(id) as num from store_templates_cats where id='$styleid' and selectable=1");
if($data['num']){
    return true;
}else{
    return false;
    }
}else{
    return false;
}
}
//----- check if valid styleid -------
$styleid=(isset($styleid) ? intval($styleid) : get_cookie("styleid"));
if(!is_valid_styleid($styleid)){
$styleid = $settings['default_styleid'];
if(!is_valid_styleid($styleid)){
$styleid = 1;
}
}
//----- get style settings ----//
$data_style = db_qr_fetch("select images from  store_templates_cats where id='$styleid'");
$style['images'] =  iif($data_style['images'],$data_style['images'],"images");

set_cookie('styleid', intval($styleid));


//----------- Load links -----------
 $qr=db_query("select * from store_templates where name like 'links_%'  and cat='$styleid'");
 while($data=db_fetch($qr)){
 $links[$data['name']] = $data['content'];
 }
 
 
//------- theme file ---------
require(CWD . "/includes/functions_themes.php") ;
//---------

require(CWD . "/includes/functions_cart.php") ;  
require(CWD . "/includes/functions_clients.php") ;

init_members_connector(); 

require(CWD . '/includes/class_tabs.php') ; 


require(CWD . '/includes/class_security_img.php');
$sec_img = new sec_img_verification();

//-------- counter file ---------
require(CWD . "/counter.php");


function if_admin($dep="",$continue=0){
        global $user_info,$phrases ;

        if(!$dep){

        if($user_info['groupid'] != 1){



        if(!$continue){

        print_admin_table("<center>$phrases[access_denied]</center>");

         die();

         }
           return false;
         }else{
                 return true;
                 }
          }else{
           if($user_info['groupid'] != 1){

                  $data=db_qr_fetch("select * from store_user where id='$user_info[id]'");
                  $prm_array = explode(",",$data['cp_permisions']);

                  if(!in_array($dep,$prm_array)){

        if(!$continue){
         print_admin_table("<center>$phrases[access_denied]</center>");
         die();
                           }
                            return false;
                          }else{
                          return true;
                                  }
                 }else{
                         return true;
                         }
            }
         }
//-------------------------------------------------------------
function get_image($src,$default="",$path=""){
    global $style;
         if($src){
              return $path.$src ;
            }else{
    if($default){
        return $path.$default;
        }else{
    return $path."$style[images]/no_pic.gif" ;
    }
    }
    }
//------------ copyrights text ---------------------
function print_copyrights(){
global $_SERVER,$settings,$copyrights_lang ;
          
if(COPYRIGHTS_TXT_MAIN){
if($copyrights_lang == "arabic"){
print "<p align=center>Ã„Ì⁄ «·ÕﬁÊﬁ „Õ›ÊŸ… ·‹ :
<a target=\"_blank\" href=\"http://$_SERVER[HTTP_HOST]\">$settings[copyrights_sitename]</a> © " . date('Y') . " <br>
»—„Ã… <a target=\"_blank\" href=\"http://allomani.com/\"> «··Ê„«‰Ì ··Œœ„«  «·»—„ÃÌ… </a> © 2009";
}else{
print "<p align=center>Copyright © ". date('Y')." <a target=\"_blank\" href=\"http://$_SERVER[HTTP_HOST]\">$settings[copyrights_sitename]</a> - All rights reserved <br>
Programmed By <a target=\"_blank\" href=\"http://allomani.com/\"> Allomani </a> © 2009";
    }
}
        }

//---------------------- Read File ------------------------
function read_file($filename){
$fn = fopen($filename,"r");
$fdata = fread($fn,filesize($filename));
fclose($fn);
return $fdata ;
}


//---------- validate email --------       
function check_email_address($email) {
if(filter_var($email, FILTER_VALIDATE_EMAIL) === false){
    return false;
}else{
    return true;
}
}
/*
function check_email_address($email) {
    // First, we check that there's one @ symbol, and that the lengths are right
    if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
        // Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
        return false;
    }
    // Split it into sections to make life easier
    $email_array = explode("@", $email);
    $local_array = explode(".", $email_array[0]);
    for ($i = 0; $i < sizeof($local_array); $i++) {
         if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
            return false;
        }
    }
    if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
        $domain_array = explode(".", $email_array[1]);
        if (sizeof($domain_array) < 2) {
                return false; // Not enough parts to domain
        }
        for ($i = 0; $i < sizeof($domain_array); $i++) {
            if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
                return false;
            }
        }
    }
    return true;
}   */
        
        
//---------------------------------------------
function execphp_fix_tag($match)
{
        // replacing WPs strange PHP tag handling with a functioning tag pair
        $output = '<?php'. $match[2]. '?>';
        return $output;
}
//------------------------------------------------------------
function run_php($content)
{

$content = str_replace(array("&#8216;", "&#8217;"), "'",$content);
$content = str_replace(array("&#8221;", "&#8220;"), '"', $content);
$content = str_replace("&Prime;", '"', $content);
$content = str_replace("&prime;", "'", $content);
        // for debugging also group unimportant components with ()
        // to check them with a print_r($matches)
        $pattern = '/'.
                '(?:(?:<)|(\[))[\s]*\?php'. // the opening of the <?php or [?php tag
                '(((([\'\"])([^\\\5]|\\.)*?\5)|(.*?))*)'. // ignore content of PHP quoted strings
                '\?(?(1)\]|>)'. // the closing ? > or ?] tag
                '/is';
      $content = preg_replace_callback($pattern, 'execphp_fix_tag', $content);
        // to be compatible with older PHP4 installations
        // don't use fancy ob_XXX shortcut functions
        ob_start();
         eval(" ?> $content ");


        $output = ob_get_contents();
        ob_end_clean();
        print $output;
}
//---------------------------- Admin Login Function ---------------------------------
$user_info = array();
function check_login_cookies(){
      global $user_info;

$user_info['username'] = get_cookie('admin_username');
$user_info['password'] = get_cookie('admin_password');
$user_info['id'] = intval(get_cookie('admin_id'));


   if($user_info['id']){
   $qr = db_query("select * from store_user where id='$user_info[id]'");
         if(db_num($qr)){
           $data = db_fetch($qr);
           if($data['username'] == $user_info['username'] && md5($data['password']) == $user_info['password']){
                   $user_info['email'] = $data['email'];
           $user_info['groupid'] = $data['group_id'];
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

        }
 

//--------- Generate Random String -----------
function rand_string($length = 8){

  // start with a blank password
  $password = "";

  // define possible characters
  $possible = "0123456789bcdfghjkmnpqrstvwxyz";

  // set up a counter
  $i = 0;

  // add random characters to $password until $length is reached
  while ($i < $length) {

    // pick a random character from the possible ones
    $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);

    // we don't want this character if it's already in the password
    if (!strstr($password, $char)) {
      $password .= $char;
      $i++;
    }

  }

  // done!
  return $password;

}
//--------------------------------- Check Functions ---------------------------------
function check_safe_functions($condition_value){

  global $safe_functions,$phrases ;
      if (preg_match_all('#([a-z0-9_{}$>-]+)(\s|/\*.*\*/|(\#|//)[^\r\n]*(\r|\n))*\(#si', $condition_value, $matches))
                        {

                                $functions = array();
                                foreach($matches[1] AS $key => $match)
                                {
                                        if (!in_array(strtolower($match), $safe_functions) && function_exists(strtolower($match)))
                                        {
                                                $funcpos = strpos($condition_value, $matches[0]["$key"]);
                                                $functions[] = array(
                                                        'func' => stripslashes($match),
                                                    //    'usage' => substr($condition_value, $funcpos, (strpos($condition_value, ')', $funcpos) - $funcpos + 1)),
                                                );
                                        }
                                }
                                if (!empty($functions))
                                {
                                        unset($safe_functions[0], $safe_functions[1], $safe_functions[2]);



                                        foreach($functions AS $error)
                                        {
                                                $errormsg .= "$phrases[err_function_usage_denied]: <code>" . htmlspecialchars($error['func']) . "</code>
                                                <br>\n";
                                        }

                                        echo "<p dir=rtl>$errormsg</p>";
                                        return false ;
                                }else{
                                         return true ;
                                          }
                        }
                        return true ;
                        }
//---------------------- Compile Safe Tempalte -------------------
function compile_template($template)
{
global $safe_functions ;
     //   $orig_template = $template;
     //   $template = addslashes($template);
     //   $template = process_template_conditionals($template) ;




    //    $template = str_replace('\\\\$', '\\$', $template);

       if(check_safe_functions($template)){

      run_php($template);
        }
}


//---------------------- Send Email Function -------------------
function send_email($from_name,$from_email,$to_email,$subject,$msg,$html=0,$encoding=""){
        global $PHP_SELF,$smtp_settings,$settings ;
    $from_name = htmlspecialchars($from_name);
    $from_email = htmlspecialchars($from_email);
    $to_email = htmlspecialchars($to_email);
    $subject = htmlspecialchars($subject);
   // $msg=htmlspecialchars($msg);


   if(!$encoding){$encoding =  $settings['site_pages_encoding'];}
   
   // $from = "$from_name <$from_email>" ;
   $from = "=?".$encoding."?B?".base64_encode($from_name)."?= <$from_email>" ;
    $subject = "=?".$encoding."?B?".base64_encode($subject)."?=";

    
    $mailHeader  = 'From: '.$from.' '."\r\n"; 
    $mailHeader .= "Reply-To: $from_email\r\n";
    $mailHeader .= "Return-Path: $from_email\r\n";
    $mailHeader .= "To: $to_email\r\n";
    $mailheader.="MIME-Version: 1.0\r\n";
    $mailHeader .= "Content-Type: ".iif($html,"text/html","text/plain")."; charset=".$encoding."\r\n";
    
    if($smtp_settings['enable']){ 
    $mailHeader .= "Subject: $subject\r\n";
        }
        
    $mailHeader .= "Date: ".strftime("%a, %d %b %Y %H:%M:%S %Z")."\r\n";
    $mailHeader .= "X-EWESITE: Allomani\r\n";
    $mailHeader .= "X-Mailer: PHP/".phpversion()."\r\n";
    $mailHeader .= "X-Mailer-File: "."http://".$_SERVER['HTTP_HOST'].($script_path ? "/".$script_path:"").$PHP_SELF."\r\n";
    $mailHeader .= "X-Sender-IP: {$_SERVER['REMOTE_ADDR']}\r\n";




    if($smtp_settings['enable']){

   if(!class_exists("smtp_class")){
   require_once(CWD ."/includes/class_smtp.php");
   }

   $smtp=new smtp_class;

    $smtp->host_name=$smtp_settings['host_name'];
    $smtp->host_port=$smtp_settings['host_port'];
    $smtp->ssl=$smtp_settings['ssl'];
    $smtp->localhost="localhost";       /* Your computer address */
    $smtp->direct_delivery=0;           /* Set to 1 to deliver directly to the recepient SMTP server */
    $smtp->timeout=$smtp_settings['timeout'];    /* Set to the number of seconds wait for a successful connection to the SMTP server */
    $smtp->data_timeout=0;              /* Set to the number seconds wait for sending or retrieving data from the SMTP server.
                                           Set to 0 to use the same defined in the timeout variable */
    $smtp->debug=$smtp_settings['debug'];                     /* Set to 1 to output the communication with the SMTP server */
    $smtp->html_debug=1;                /* Set to 1 to format the debug output as HTML */

    if($smtp_settings['username'] && $smtp_settings['password']){
    $smtp->pop3_auth_host=$smtp_settings['host_name'];           /* Set to the POP3 authentication host if your SMTP server requires prior POP3 authentication */
    $smtp->user=$smtp_settings['username'];                     /* Set to the user name if the server requires authetication */
     $smtp->password=$smtp_settings['password'];                 /* Set to the authetication password */
    $smtp->realm="";                    /* Set to the authetication realm, usually the authentication user e-mail domain */
    }

    $smtp->workstation="";              /* Workstation name for NTLM authentication */
    $smtp->authentication_mechanism=""; /* Specify a SASL authentication method like LOGIN, PLAIN, CRAM-MD5, NTLM, etc..
                                           Leave it empty to make the class negotiate if necessary */

   $mailResult =  $smtp->SendMessage(
        $from_email,
        array(
            $to_email
        ),
        array(
            $mailHeader
        ),
        $msg,0);

        if($mailResult){
              return true ;
                }else{
                    if($smtp_settings['show_errors']){
                    print "<b>SMTP Error: </b> ".$smtp->error ."<br>";
                    }
               return false;
               }

    }else{
    $mailResult = @mail($to_email,$subject,$msg,$mailHeader);

               if($mailResult){
              return true ;
                }else{
               return false;
               }
    }
        }

//----------- Get Hooks ------------
function get_plugins_hooks(){

$hooklocations = array();
    require_once(CWD . '/includes/class_xml.php');
    $handle = opendir(CWD . '/xml/');
    while (($file = readdir($handle)) !== false)
    {
        if (!preg_match('#^hooks_(.*).xml$#i', $file, $matches))
        {
            continue;
        }
        $product = $matches[1];

        $phrased_product = $products[($product ? $product : 'allomani')];
        if (!$phrased_product)
        {
            $phrased_product = $product;
        }

        $xmlobj = new XMLparser(false, CWD . "/xml/$file");
        $xml = $xmlobj->parse();

        if (!is_array($xml['hooktype'][0]))
        {
            // ugly kludge but it works...
            $xml['hooktype'] = array($xml['hooktype']);
        }

        foreach ($xml['hooktype'] AS $key => $hooks)
        {
            if (!is_numeric($key))
            {
                continue;
            }
            //$phrased_type = isset($vbphrase["hooktype_$hooks[type]"]) ? $vbphrase["hooktype_$hooks[type]"] : $hooks['type'];
            $phrased_type =  $hooks['type'];
            $hooktype = $phrased_product . ' : ' . $phrased_type;

            $hooklocations["$hooktype"] = array();

            if (!is_array($hooks['hook']))
            {
                $hooks['hook'] = array($hooks['hook']);
            }

            foreach ($hooks['hook'] AS $hook)
            {
                $hookid = (is_string($hook) ? $hook : $hook['value']);
                $hooklocations["$hooktype"]["$hookid"] = $hookid;
            }
        }
    }
    ksort($hooklocations);
    return $hooklocations ;
    }

//--------- Get used hooks List -----------
$qr = db_query("select hookid from store_hooks where active='1'");
while($data = db_fetch($qr)){
$used_hooks[] = $data['hookid'];
}
unset($qr,$data);
//-------------- compile hook --------------
function compile_hook($hookid){
global $used_hooks;
if(is_array($used_hooks)){
if(in_array($hookid,$used_hooks)){
$qr = db_query("select code from store_hooks where hookid='".db_escape($hookid)."' and active='1' order by ord asc");
if(db_num($qr)){
while($data=db_fetch($qr)){
run_php($data['code']);
    }
}else{
 return false;
 }
 }else{
     return false;
     }
     }else{
         return false;
         }
}

//--------- iif expression ------------
function iif($expression, $returntrue, $returnfalse = '')
{
    return ($expression ? $returntrue : $returnfalse);
}

//------- set cookies function -----------
function set_cookie($name,$value=""){
global $cookies_prefix,$cookies_timemout,$cookies_path,$cookies_domain;
$name = $cookies_prefix . $name;
$k_timeout = time() + (60 * 60 * 24 * intval($cookies_timemout));
setcookie($name, $value, $k_timeout,$cookies_path,$cookies_domain);
}
//--------- get cookies funtion ---------
function get_cookie($name){
global $cookies_prefix,$_COOKIE;
$name = $cookies_prefix . $name;
return $_COOKIE[$name];
}


//--------- array replace --------
if(!function_exists('array_replace')){   
function array_replace($tofind, $toreplace,$a){

if(!is_array($a)){$a = array($a);}

for($i=0;$i<count($a);$i++){
$a[$i] = str_replace($tofind,$toreplace,$a[$i]);
}

return $a ;
}
}

//---------- Flush Function -------------
function data_flush()
{
    static $output_handler = null;
    if ($output_handler === null)
    {
        $output_handler = @ini_get('output_handler');
    }

    if ($output_handler == 'ob_gzhandler')
    {
        // forcing a flush with this is very bad
        return;
    }

    flush();
    if (PHP_VERSION  >= '4.2.0' AND function_exists('ob_flush') AND function_exists('ob_get_length') AND ob_get_length() !== false)
    {
        @ob_flush();
    }
    else if (function_exists('ob_end_flush') AND function_exists('ob_start') AND function_exists('ob_get_length') AND ob_get_length() !== FALSE)
    {
        @ob_end_flush();
        @ob_start();
    }
}

//----------- select row ------------
function print_select_row($name, $array, $selected = '', $options="" , $size = 0, $multiple = false,$same_values=false)
{
    global $vbulletin;

    $select = "<select name=\"$name\" id=\"sel_$name\"" . iif($size, " size=\"$size\"") . iif($multiple, ' multiple="multiple"') . iif($options , " $options").">\n";
    $select .= construct_select_options($array, $selected,$same_values);
    $select .= "</select>\n";

    print $select;
}


function construct_select_options($array, $selectedid = '',$same_values=false)
{
    if (is_array($array))
    {
        $options = '';
        foreach($array AS $key => $val)
        {
            if (is_array($val))
            {
                $options .= "\t\t<optgroup label=\"" . $key . "\">\n";
                $options .= construct_select_options($val, $selectedid, $tabindex, $htmlise);
                $options .= "\t\t</optgroup>\n";
            }
            else
            {
                if (is_array($selectedid))
                {
                    $selected = iif(in_array($key, $selectedid), ' selected="selected"', '');
                }
                else
                {
                    $selected = iif($key == $selectedid, ' selected="selected"', '');
                }
                $options .= "\t\t<option value=\"".($same_values ? $val : $key). "\"$selected>" . $val . "</option>\n";
            }
        }
    }
    return $options;
}
//---------- print text row ----------
function print_text_row($name,$value="",$size="",$dir="",$options=""){
print "<input type=text name=\"$name\"".iif($value," value=\"$value\"").iif($size," size=\"$size\"").iif($dir," dir=\"$dir\"").iif($options," $options").">";
}

//--------- print admin table -------------
function print_admin_table($content,$width="50%",$align="center"){
    print "<center><table class=grid width='$width'><tr><td align='$align'>$content</td></tr></table></center>";
    }

 //-------------- Get Remote filesize --------
function fetch_remote_filesize($url)
    {
        // since cURL supports any protocol we should check its http(s)
        preg_match('#^((http|ftp)s?):\/\/#i', $url, $check);
        if (ini_get('allow_url_fopen') != 0 AND $check[1] == 'http')
        {
            $urlinfo = @parse_url($url);

            if (empty($urlinfo['port']))
            {
                $urlinfo['port'] = 80;
            }

            if ($fp = @fsockopen($urlinfo['host'], $urlinfo['port'], $errno, $errstr, 30))
            {
                fwrite($fp, 'HEAD ' . $url . " HTTP/1.1\r\n");
                fwrite($fp, 'HOST: ' . $urlinfo['host'] . "\r\n");
                fwrite($fp, "Connection: close\r\n\r\n");

                while (!feof($fp))
                {
                    $headers .= fgets($fp, 4096);
                }
                fclose ($fp);

                $headersarray = explode("\n", $headers);
                foreach($headersarray as $header)
                {
                    if (stristr($header, 'Content-Length') !== false)
                    {
                        $matches = array();
                        preg_match('#(\d+)#', $header, $matches);
                        return sprintf('%u', $matches[0]);
                    }
                }
            }
        }
        else if (false AND !empty($check) AND function_exists('curl_init') AND $ch = curl_init())
        {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'HEAD');
            /* Need to enable this for self signed certs, do we want to do that?
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            */

            $header = curl_exec($ch);
            curl_close($ch);

            if ($header !== false)
            {
                preg_match('#Content-Length: (\d+)#i', $header, $matches);
                return sprintf('%u', $matches[1]);
            }
        }
        return false;
    }
 //--------------- Get file Extension ----------
 function file_extension($filename)
{
    return substr(strrchr($filename, '.'), 1);
}

//-------------- Get Dir Files List ----------
function get_files($dir,$allowed_types="",$subdirs_search=1) {
      $dir = (substr($dir,-1,1)=="/" ? substr($dir,0,strlen($dir)-1) : $dir);

    if($dh = opendir($dir)) {

        $files = Array();
        $inner_files = Array();

        while($file = readdir($dh)) {
            if($file != "." && $file != ".." && $file[0] != '.') {
                if(is_dir($dir . "/" . $file) && $subdirs_search) {
                    $inner_files = get_files($dir . "/" . $file,$allowed_types);
                    if(is_array($inner_files)) $files = array_merge($files, $inner_files);
                }else{
                  $fileinfo= pathinfo($dir . "/" . $file);
                $imtype = $fileinfo["extension"];
          if(is_array($allowed_types)){
          if(in_array($imtype,$allowed_types)){
               $files[] =  $dir . "/" . $file;
           }
          }else{
               $files[] =  $dir . "/" . $file;
          }
                }
            }
        }

        closedir($dh);
        return $files;
    }
}

//----------- Number Format --------------------
function convert_number_format($number, $decimals = 0, $bytesize = false, $decimalsep = null, $thousandsep = null)
{

    $type = '';

    if (empty($number))
    {
        return 0;
    }
    else if (preg_match('#^(\d+(?:\.\d+)?)(?>\s*)([mkg])b?$#i', trim($number), $matches))
    {
        switch(strtolower($matches[2]))
        {
            case 'g':
                $number = $matches[1] * 1073741824;
                break;
            case 'm':
                $number = $matches[1] * 1048576;
                break;
            case 'k':
                $number = $matches[1] * 1024;
                break;
            default:
                $number = $matches[1] * 1;
        }
    }

    if ($bytesize)
    {
        if ($number >= 1073741824)
        {
            $number = $number / 1073741824;
            $decimals = 2;
            $type = " GB";
        }
        else if ($number >= 1048576)
        {
            $number = $number / 1048576;
            $decimals = 2;
            $type = " MB";
        }
        else if ($number >= 1024)
        {
            $number = $number / 1024;
            $decimals = 1;
            $type = " KB";
        }
        else
        {
            $decimals = 0;
            $type = " Byte";
        }
    }

    if ($decimalsep === null)
    {
     //   $decimalsep = ".";
    }
    if ($thousandsep === null)
    {
    //    $thousandsep = ",";
    }

    if($decimalsep && $thousandsep){
    return str_replace('_', '&nbsp;', number_format($number, $decimals, $decimalsep, $thousandsep)) . $type;
    }else{
         return str_replace('_', '&nbsp;', round($number,$decimals)) . $type;
    }
}

//--------------------- preview Text ------------------------------------
function getPreviewText($text) {
             global $preview_text_limit ;
    // Strip all tags
    $desc = strip_tags(html_entity_decode($text), "<a><em>");
    $charlen = 0; $crs = 0;
    if(strlen_HTML($desc) == 0)
        $preview = substr($desc, 0, $preview_text_limit);
    else
    {
        $i = 0;
        while($charlen < 80)
        {
            $crs = strpos($desc, " ", $crs)+1;
            $lastopen = strrpos(substr($desc, 0, $crs), "<");
            $lastclose = strrpos(substr($desc, 0, $crs), ">");
            if($lastclose > $lastopen)
            {
                // we are not in a tag
                $preview = substr($desc, 0, $crs);
                $charlen = strlen_noHTML($preview);
            }
            $i++;
        }
    }
    return trim($preview)  ;

}


function strlen_noHtml($string){
    $crs = 0;
    $charlen = 0;
    $len = strlen($string);
    while($crs < $len)
    {
        $offset = $crs;
        $crs = strpos($string, "<", $offset);
        if($crs === false)
        {
           $crs = $len;
           $charlen += $crs - $offset;
        }
        else
        {
            $charlen += $crs - $offset;
            $crs = strpos($string, ">", $crs)+1;
        }
    }
    return $charlen;
}


function strlen_Html($string){
    $crs = 0;
    $charlen = 0;
    $len = strlen($string);
    while($crs < $len)
    {
        $scrs = strpos($string, "<", $crs);
        if($scrs === false)
        {
           $crs = $len;
        }
        else
        {
            $crs = strpos($string, ">", $scrs)+1;
            if($crs === false)
                $crs = $len;
            $charlen += $crs - $scrs;
        }
    }
    return $charlen;
}


//------------- convert ar 2 en ------------------

function convert2en($filename){
    
if(!preg_match("/^([-a-zA-Z0-9_.!@#$&*+=|~%^()\/\\'])*$/", $filename)){
$filename= str_replace("'","",$filename);
$filename= str_replace(" ","_",$filename);
$filename= str_replace("«","a",$filename);
$filename= str_replace("√","a",$filename);
$filename= str_replace("≈","i",$filename);
$filename= str_replace("»","b",$filename);
$filename= str_replace(" ","t",$filename);
$filename= str_replace("À","th",$filename);
$filename= str_replace("Ã","g",$filename);
$filename= str_replace("Õ","7",$filename);
$filename= str_replace("Œ","k",$filename);
$filename= str_replace("œ","d",$filename);
$filename= str_replace("–","d",$filename);
$filename= str_replace("—","r",$filename);
$filename= str_replace("“","z",$filename);
$filename= str_replace("”","s",$filename);
$filename= str_replace("‘","sh",$filename);
$filename= str_replace("’","s",$filename);
$filename= str_replace("÷","5",$filename);
$filename= str_replace("⁄","a",$filename);
$filename= str_replace("€","gh",$filename);
$filename= str_replace("›","f",$filename);
$filename= str_replace("ﬁ","k",$filename);
$filename= str_replace("ﬂ","k",$filename);
$filename= str_replace("·","l",$filename);
$filename= str_replace("‰","n",$filename);
$filename= str_replace("Â","h",$filename);
$filename= str_replace("Ì","y",$filename);
$filename= str_replace("ÿ","6",$filename);
$filename= str_replace("Ÿ","d",$filename);
$filename= str_replace("Ê","w",$filename);
$filename= str_replace("ƒ","o",$filename);
$filename= str_replace("∆","i",$filename);
$filename= str_replace("·«","la",$filename);
$filename= str_replace("·√","la",$filename);
$filename= str_replace("Ï","a",$filename);
$filename= str_replace("…","t",$filename);
$filename= str_replace("„","m",$filename);
}


return $filename ;

}

 
//--------------------------- Create Thumb ----------------------------
function create_thumb($filename , $width , $height,$fixed,$suffix='',$replace_exists=false){
    
require_once(CWD .'/includes/class_thumb.php');
if(function_exists("ImageCreateTrueColor")){
 if(file_exists(CWD . "/$filename")){ 
 $img_info = @getimagesize(CWD . "/$filename"); 
 $thumb=new thumbnail(CWD . "/$filename");

 if($fixed){
 $thumb->size_fixed($width,$height);
 }else{    
 if($img_info[0] < $width){
 $width = $img_info[0];
 }
 if($img_info[1] < $height){
$height = $img_info[1];
  }
  
 if($height > $width){
  $thumb->size_height($height); 
 }else{
 $thumb->size_width($width);
 } 
 }        
           

   $imtype = file_extension(CWD . "/$filename");


$thumb->jpeg_quality(100); 

$save_name  =  basename($filename);
$save_path = str_replace("/".$save_name,'',$filename);

$imtype = file_extension($save_name);
$save_name = convert2en($save_name);
$save_name = strtolower($save_name);
$save_name= str_replace(" ","_",$save_name);

if($suffix){
$save_name = str_replace(".$imtype","",$save_name)."_".$suffix.".$imtype";
}


    
while(file_exists(CWD . "/" .$save_path."/".$save_name)){
$save_name = str_replace(".$imtype","",$save_name)."_".rand(0,999).".$imtype";    
}
    
    
$thumb->save(CWD . "/" .$save_path."/".$save_name);           
return ($save_path."/".$save_name) ;
 }else{
     return false;
 }
 }else{
return $false;     
 }
        }
 

//---------- Pages Links ---------//
function print_pages_links($start,$items_count,$items_perpage,$page_string){
  
 global $phrases ;
   
//$previous_page=$start - $songs_perpage;
//$next_page=$start + $songs_perpage;


if ($items_count>$items_perpage){
echo "<p align=center>$phrases[pages] : ";
if($start >0){
$previouspage = $start - $items_perpage;
echo "<a href='".str_replace("{start}",$previouspage,$page_string)."'><</a>\n";
}


$pages=intval($items_count/$items_perpage);
if ($items_count%$items_perpage){$pages++;}
for ($i = 1; $i <= $pages; $i++) {
$nextpag = $items_perpage*($i-1);
if ($nextpag == $start){
echo "<font size=2 face=tahoma><b>$i</b></font>&nbsp;\n";
}else{
echo "<a href='".str_replace("{start}",$nextpag,$page_string)."'>[$i]</a>&nbsp;\n";}
}
if (! ( ($start/$items_perpage) == ($pages - 1) ) && ($pages != 1) )
{$nextpag = $start+$items_perpage;
echo "<a href='".str_replace("{start}",$nextpag,$page_string)."'>></a>\n";}
echo "</p>";}
}  

//------------- if Product Cat Admin ---------
function if_products_cat_admin($cat,$skip_zero_id=true){
 global $user_info,$phrases ;

 if($user_info['groupid'] != 1){
  

  if($cat){
          $cat_users =get_product_cat_users($cat,true);
              
  
         if(!in_array($user_info['id'],$cat_users)){
              print_admin_table("<center>$phrases[err_cat_access_denied]</center>");
         die();
    }
    }else{
        if(!$skip_zero_id){
          print_admin_table("<center>$phrases[err_cat_access_denied]</center>");
         die();
        }
    }
      }
}

//---------- Get Products Cats --------//
function get_products_cats($id){
  $cats_arr = array();
   $cats_arr[]=$id;
                               
         $qr1 = db_query("select id from store_products_cats where cat='$id'");
         while($data1 = db_fetch($qr1)){
          $nxx = get_products_cats($data1['id']);
          if(is_array($nxx)){
              $cats_arr = array_merge($nxx,$cats_arr);
          }
           unset($nxx);
          }

          return  $cats_arr ;
         }
//-------- Get Cat custom fields -------
function get_product_cat_fields($id,$fields_only=false,$in_search=true,$in_details=true,$in_short_details=true){
        global $cat_fields_cache ;
        
         $fields_array = array();
    //     $dir_data['cat'] = intval($id) ;
if(!$id){return array();}
         
$data_path = db_qr_fetch("select id,path,`fields` from store_products_cats where id='$id'");
$cat_fields_cache[$data_path['id']]= $data_path;

$path_array = explode(",",$data_path['path']);
$path_array = (array) $path_array ;

   
 //--------- pre caching Path ------//     
 $qr=db_query("select name,id,cat,`fields` from store_products_cats where id IN (".$data_path['path'].")");
   while($data=db_fetch($qr)){
   if(!isset($cat_fields_cache[$data['id']])){
   $cat_fields_cache[$data['id']] = $data ;
    }  
   }
 
//---------------------------------------    
     
         
foreach($path_array as $cat_id){
  if($cat_id){  
   
      
           
    //--------- caching ------//
  if(isset($cat_fields_cache[$cat_id])){
   $dir_data =  $cat_fields_cache[$cat_id];
    }else{    
   $dir_data = db_qr_fetch("select `fields` from store_products_cats where id='$cat_id'");
   $cat_fields_cache[$cat_id] = $dir_data ;
    } 
   //--------------------------//   
  
     
   if($dir_data['fields']){
     
        if($in_search && $in_details && $in_short_details){   
         $cat_fields = explode(",",$dir_data['fields']);  
        }else{ 
         
   $qr_fields = db_query("select id from store_fields_sets where id IN ($dir_data[fields])".iif($in_search," and in_search=1").iif($in_details," and in_details=1").iif($in_short_details," and in_short_details=1 and active=1"));
   while($data_fields = db_fetch($qr_fields)){
   $cat_fields[] = $data_fields['id'];    
   } 
        }
   }else{
    $cat_fields=array();  
   }    
   

   
  //  $data = db_qr_fetch("select `fields` from store_products_cats where id='".$dir_data['id']."'");
   
    
    for($z=0;$z<count($cat_fields);$z++){  
    if($fields_only){ 
    if(!in_array($cat_fields[$z],$fields_array)){$fields_array[]=$cat_fields[$z];}
    }else{
   $fields_array[$cat_fields[$z]]=$cat_id;  
    }
    }      
    
  }
        }
        
     
    

          return  $fields_array ;
} 

//-------- Get Cat Shipping Methods -------
function get_product_cat_shipping_methods($id,$fields_only=false){
   
         $fields_array = array();
         $dir_data['cat'] = intval($id) ;
while($dir_data['cat']!=0){
   $dir_data = db_qr_fetch("select id,cat from store_products_cats where id='$dir_data[cat]'");


   
    $data = db_qr_fetch("select `shipping_methods` from store_products_cats where id='".$dir_data['id']."'");
    if(trim($data['shipping_methods'])){
       $cat_fields = explode(",",$data['shipping_methods']);
          
    for($z=0;$z<count($cat_fields);$z++){  
   
    if($fields_only){ 
    if(!in_array($cat_fields[$z],$fields_array)){$fields_array[]=$cat_fields[$z];}
    }else{
    $fields_array[$cat_fields[$z]]=$dir_data['id'];  
    }
    

    
    }      
    } 

        }
 
          return  $fields_array ;
} 
//-------- Get Cat users  -------
function get_product_cat_users($id,$fields_only=false,$type=''){

  
   
         $fields_array = array();
         $dir_data['cat'] = intval($id) ;
while($dir_data['cat']!=0){
   $dir_data = db_qr_fetch("select id,cat from store_products_cats where id='$dir_data[cat]'");


   
    $data = db_qr_fetch("select `users` from store_products_cats where id='".$dir_data['id']."'");
    if(trim($data['users'])){
       $cat_fields = explode(",",$data['users']);
    
    for($z=0;$z<count($cat_fields);$z++){  
    if($fields_only){ 
    if(!in_array($cat_fields[$z],$fields_array)){$fields_array[]=$cat_fields[$z];}
    }else{
    $fields_array[$cat_fields[$z]]=$dir_data['id'];  
    }
    }      
    } 

        }
        
     
    

          return  $fields_array ;
}
        



//----------get short details fields data  ---------
function get_short_details_fields_data($id,$cat){
    global $phrases,$style, $cat_short_fields_cache,$short_details_fields_count;  ;
  if($short_details_fields_count){              
  $id=intval($id);
  $cat=intval($cat);

  
 
  $qrf = db_query("select * from store_fields_data where product_id='$id'");
  if(db_num($qrf)){
   //--- caching data --//
  while($pre_dataf_x = db_fetch($qrf)){
  $pre_dataf[$pre_dataf_x['cat']] =  $pre_dataf_x ;
  $sets_ids[] = $pre_dataf_x['cat'];   
  }
   unset($qrf,$pre_dataf_x);
  //--- caching sets ---//
  $fs_qr = db_query("select id,name,title,type,img from store_fields_sets where id IN (".implode(",",$sets_ids).") and in_short_details=1 and active=1 order by ord"); 
  while($fs_data = db_fetch($fs_qr)){
  $sets_array[]  =  $fs_data;  
  }
  unset($fs_data,$fs_qr,$sets_ids);
  //--------------------//
         
  if(count($sets_array)){
   $fields_content = "<br><table>";           
  foreach($sets_array as $field_name){
    
 
  if(isset($pre_dataf[$field_name['id']])){    
  
   $dataf = $pre_dataf[$field_name['id']];
   
  
  $fields_content .= "<tr><td>".iif($field_name['img'],"<img src=\"$field_name[img]\">&nbsp;")."<b>".iif($field_name['title'],$field_name['title'],$field_name['name'])."</b></td><td>";
  if($field_name['type']=="text"){
  $fields_content .= "$dataf[value]";    
  }elseif($field_name['type']=="select"){
       $option_name = db_qr_fetch("select value from store_fields_options where id='$dataf[value]'"); 
       $fields_content .= iif($option_name['value'],$option_name['value'],"$phrases[not_selected]");
  }elseif($field_name['type']=="checkbox"){      
   $values_arr = unserialize($dataf['value']);
  if(!is_array($values_arr)){$values_arr=array();} 
      
      
    $qr_options = db_query("select id,value,img from store_fields_options where field_id='$dataf[cat]' order by ord"); 
   if(db_num($qr_options)){  
   $fields_content .= "<table>"; 
   while($data_options = db_fetch($qr_options)){
       $fields_content .= "<tr><td>".iif($data_options['img'],"<img src=\"$data_options[img]\">&nbsp;")."$data_options[value] </td><td><img src=\"$style[images]/".iif(in_array($data_options['id'],$values_arr),"true.gif","false.gif")."\" border=0></td></tr>";
   } 
   $fields_content .= "</table>";
   }    
  }
  $fields_content .= "</td></tr>
  <tr><td colspan=2><hr class='separate_line' size=1></td></tr>";
  }
  
  }
  
  $fields_content .= "</table><br><br>";
  }else{
      $fields_content  = "";
  }

 //------------------------------
  } 
print $fields_content; 
  
}
}
//------ cat path str -----
function get_cat_path_str($cat){
             $dir_data['cat'] = intval($cat) ;
               $path_arr[] = $dir_data['cat'];
while($dir_data['cat']!=0){
   
   $dir_data = db_qr_fetch("select id,cat,`fields` from store_products_cats where id='$dir_data[cat]'");
   $path_arr[] = $dir_data['cat'];
}
return implode(",",$path_arr);
}
//--------- Login Redirection -----------
function login_redirect(){
    global $phrases;
print "<form action='index.php' method=post name=lg_form>
<input type=hidden name=action value='login'>
 <input type=hidden name='re_link' value=\"http://$_SERVER[HTTP_HOST]"."$_SERVER[REQUEST_URI]\">
 $phrases[redirection_msg] <input type=submit value='$phrases[click_here]'> 
 </form>
 
 <script>
 document.forms['lg_form'].submit();
 </script>";
 
 }  

   //----------------- Admin Path Links ---------
 function print_admin_path_links($cat,$filename=""){
     global $phrases,$global_align;
     
     $dir_data['cat'] = intval($cat) ;
while($dir_data['cat']!=0){
   $dir_data = db_qr_fetch("select name,id,cat from store_products_cats where id='$dir_data[cat]'");


        $dir_content = "<a href='index.php?action=products&cat=$dir_data[id]'>$dir_data[name]</a> / ". $dir_content  ;

        }
   print "<p align=$global_align><img src='images/link.gif'> <a href='index.php?action=products&cat=0'>$phrases[the_products]  </a> / $dir_content " . "<b>$filename</b></p>";

 }
 
 //----------------- Path Links ---------
 function print_path_links($cat,$filename=""){
     global $phrases,$style,$links,$global_align;
     
     $cat=intval($cat);
   if($cat) { 
   $data_cat = db_qr_fetch("select name,id,cat,path from store_products_cats where id='$cat'");     
   $qr=db_query("select name,id,cat from store_products_cats where id IN (".$data_cat['path'].")");
   while($data=db_fetch($qr)){
       $cats_data[$data['id']] = $data;
   }
    
   $cats_array = explode(",",$data_cat['path']);
   
foreach($cats_array as $id){
  // $dir_data = db_qr_fetch("select name,id,cat from store_products_cats where id='$dir_data[cat]'");
     if($id){
      $dir_data =  $cats_data[$id];

        $dir_content = "<a href='".str_replace('{id}',$dir_data['id'],$links['links_browse_products'])."'>$dir_data[name]</a> / ". $dir_content  ;
     }
        }      
 }                                   
   print "<p align=$global_align><img src='$style[images]/arrw.gif'> <a href='".str_replace('{id}','0',$links['links_browse_products'])."'>$phrases[the_products] </a> / $dir_content " . "<b>$filename</b></p>";

 }
 

function login_header(){
print "
";
}




function print_redirection($link,$print_header=1){
global $sitename,$settings,$global_dir,$style,$re_link;

$re_link = htmlspecialchars($link); 

if($print_header){
print "<html dir=$global_dir>
<title>$sitename</title>
<META http-equiv=Content-Language content=\"$settings[site_pages_lang]\">
<META http-equiv=Content-Type content=\"text/html; charset=$settings[site_pages_encoding]\">
<link href=\"css.php\" type=text/css rel=stylesheet><br><br>";
}


compile_template(get_template('redirection_page'));

   
    
    print "<script>window.location=\"$re_link\";</script>";
}
    
 //------ is serialized ---------
function is_serialized($string){
 if(preg_match("/(a|O|s|b)\x3a[0-9]*?((\x3a((\x7b?(.+)\x7d)|(\x22(.+)\x22\x3b)))|(\x3b))/", $string))
{
return true;
}
else
{
return false;
}

} 
//----------- product del ------
function product_del($id,$type="id"){
    if($type=="cat"){
     $qr = db_query("select id from store_products_data where cat='$id'");
     while($data=db_fetch($qr)){
        db_query("delete from store_products_data where id='$data[id]'");  
        db_query("delete from store_fields_data where product_id='$data[id]'");   
        db_query("delete from store_products_photos where product_id='$data[id]'");      
     }  
        
    }else{
    db_query("delete from store_products_data where id='$id'");
    db_query("delete from store_fields_data where product_id='$id'"); 
     db_query("delete from store_products_photos where product_id='$id'");      
    }
    
}

//------- array remove empty values --------//
function array_remove_empty_values($arr){
       for($i=0;$i<count($arr);$i++){
         $key = key($arr);
            $value = current($arr);
           if($value){
               $new_arr[$key] = $value;
           }
            next($arr);    
       }
       return  $new_arr;
   } 
   
   //------------ Access Log ------------
   function access_log_record($username,$status){
       global $access_log_expire ;
        
       $expire_date  = datetime("",time()-(24*60*60*$access_log_expire));
       db_query("delete from store_access_log where date < '$expire_date'");
       db_query("insert into store_access_log (username,date,status) values ('".db_clean_string($username)."','".datetime()."','$status')");
   } 
   
   // ---- Date / Time -------
   function datetime($format="",$time=""){
       return date(iif($format,$format,"Y-m-d h:i:s"),iif($time,$time,time()));
   }
   
   
   //------- Error Handler ----------//
   function error_handler($errno, $errstr, $errfile, $errline,$vars) {
        global $display_errors,$log_errors;
       
       switch ($errno)
    {
        case E_WARNING:
        case E_USER_WARNING:
            /* Don't log warnings due to to the false bug reports about valid warnings that we suppress, but still appear in the log
            */

            if($log_errors){ 
            $message = "Warning: $errstr in $errfile on line $errline";
            do_error_log($message, 'php');
            }
           

            if (!$display_errors || !error_reporting())
            {
                return;
            }
            
            $errfile = str_replace(CWD.DIRECTORY_SEPARATOR, '', $errfile);
            echo "<br /><strong>Warning</strong>: $errstr in <strong>$errfile</strong> on line <strong>$errline</strong><br />";
        break;

        case E_USER_ERROR:  
            
            if($log_errors){ 
            $message = "Fatal error: $errstr in $errfile on line $errline";
            do_error_log($message, 'php');
            }
            
            
            if ($display_errors)
            {
                $errfile = str_replace(CWD.DIRECTORY_SEPARATOR, '', $errfile);
                echo "<br /><strong>Fatal error:</strong> $errstr in <strong>$errfile</strong> on line <strong>$errline</strong><br />";
            }
            exit;
        break;
    }
}

//--------- Error Log ---------//
function do_error_log($msg , $type='php'){ 
global $logs_path,$log_max_size,$custom_error_handler;

$trace =  @debug_backtrace() ;
 
 //$args = (array) $trace[1]['args'];
  //".implode(",",$args)."
       
$dt = date("Y-m-d H:i:s (T)");
$err = $dt." : ".$msg."\r\n";
if($trace[1]['function']){
$err .=$trace[1]['function']."() in : ".$trace[1]['file'].":".$trace[1]['line']."\r\n";
}
$err .= "-------------- \r\n";

 if($custom_error_handler){
if(!file_exists($logs_path)){@mkdir($logs_path);}
 
  if($type=="db"){
  $log_file =  "$logs_path/error_db.log" ;
  $log_file_new  = "$logs_path/error_db_".date("Y_m_d_h_i_s").".log" ;  
  }else{
       $log_file =  "$logs_path/error.log" ;
  $log_file_new  = "$logs_path/error_".date("Y_m_d_h_i_s").".log" ;              
  }  
                
    if(@filesize($log_file) >= $log_max_size){
    @rename($log_file,$log_file_new);   
    }
    
      error_log($err, 3, $log_file);  
 }else{
      error_log($err);   
 } 
  
}

//------- print block banners -----------//
  function print_block_banners($data_array,$pos="block"){
         global $data;
              foreach($data_array as $data){
              
                  
                  $ids[] = $data['id'] ;
                  
              if($pos=="block"){
                print "<tr>
                <td  width=\"100%\" valign=\"top\">";
              }
              
     if($data['c_type']=="code"){
    compile_template($data['content']);
    }else{              
   $template = iif($pos=="center","center_banners","blocks_banners");
    compile_template(get_template($template));
   }
    if($pos=="block"){  
                print "</td>
        </tr>";
    }
        }
        
        if(is_array($ids)){
        db_query("update store_banners set views=views+1 where id IN (".implode(",",$ids).")");
        
        }        
       }
   
  //--------- load plugins function --------     
   function load_plugins($file){
       $dhx = @opendir(CWD ."/plugins");
while ($rdx = @readdir($dhx)){
         if($rdx != "." && $rdx != "..") {
                 $cur_fl = CWD ."/plugins/" . $rdx . "/".$file ;
        if(@file_exists($cur_fl)){
             $pl_files[] =     $cur_fl ; 
                }
          }

    }
@closedir($dhx);

return $pl_files;
   }
 //--------------- Load Global Plugins --------------------------
  $pls = load_plugins("global.php");
  if(is_array($pls)){foreach($pls as $pl){include($pl);}}

?>