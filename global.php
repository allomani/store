<?
//------------------------------
define('GLOBAL_LOADED', true);
//----------------------------------
define('SCRIPT_NAME', "store");
define('SCRIPT_VER', "2.0");
define('SCRIPT_YEAR', "2013");

//----------- current work dir definition -------
define('CWD', str_replace(DIRECTORY_SEPARATOR, "/", dirname(__FILE__)));
define('CFN', basename($_SERVER['SCRIPT_FILENAME']));


//---------- Classes Auto Load -----------------
spl_autoload_register('autoloadClass');

function autoloadClass($name, $ext = 'php') {
    $file = CWD . "/includes/class_" . strtolower($name) . "." . $ext;
    if (file_exists($file)) {
        require($file);
    } else {
        print "Class \"$name\" is not Exists !";
    }
}

//---------------------------------------------

$config = array();
require(CWD . "/config.php");
app::set_config($config);


//---------- custom error handler --------//
if ($config['debug']['custom_error_handler']) {
    $old_error_handler = set_error_handler("error_handler");
}

//----- remove slashes if magic quotes -----//
function stripslashes_deep($value) {
    return (is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value));
}

if (get_magic_quotes_gpc()) {
    $_POST = array_map('stripslashes_deep', $_POST);
    $_GET = array_map('stripslashes_deep', $_GET);
    $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
}

//--------- extract variabls -----------------------
if (!empty($_POST)) {
    extract($_POST);
}
if (!empty($_GET)) {
    extract($_GET);
}
//if (!empty($_ENV)) {extract($_ENV);}
//-----------------------------------------------------
//------ clean global vars ---------//
$_SERVER['QUERY_STRING'] = strip_tags($_SERVER['QUERY_STRING']);
$_SERVER['PHP_SELF'] = strip_tags($_SERVER['PHP_SELF']);
$_SERVER['REQUEST_URI'] = strip_tags($_SERVER['REQUEST_URI']);

define("CUR_FILENAME", $_SERVER['PHP_SELF']);

//---------------------- common variables types clean-----------------------
if ($id) {
    if (is_array($id)) {
        $id = array_map("intval", $id);
    } else {
        $id = (int) $id;
    }
}
if ($cat) {
    if (is_array($cat)) {
        $id = array_map("intval", $cat);
    } else {
        $cat = (int) $cat;
    }
}


//---------------- Cache -------------------
require(CWD . "/includes/functions_" . $config['cache']['engine'] . ".php");
cache_init();

//-------------- Database -------------------
require(CWD . "/includes/functions_db.php");

//db_connect($db_host, $db_username, $db_password, $db_name, $db_charset);
//---------------------------

$data_cat_cache = array();
$cat_fields_cache = array();
$cat_short_fields_cache = array();




// ------------- lang dir -------------
if ($global_lang == "arabic") {
    $global_dir = "rtl";
    $global_align = "right";
    $global_align_x = "left";
} else {
    $global_dir = "ltr";
    $global_align = "left";
    $global_align_x = "right";
}

app::init();

$phrases = app::$phrases;
$settings = app::$settings;
$links = app::$links;
$session = session::instance();



//-------- fields in short details count ----//
$data = db_qr_fetch("select count(*) as count from store_fields_sets where in_short_details=1 and active=1");
$short_details_fields_count = intval($data['count']);
//---------------------------------------------//


$actions_checks = array(
    "$phrases[main_page]" => 'main',
    "$phrases[browse_products]" => 'browse.php',
    "$phrases[product_details]" => 'product_details.php',
    "$phrases[the_news]" => 'news.php',
    "$phrases[pages]" => 'pages.php',
    "$phrases[the_search]" => 'search.php',
    "$phrases[the_votes]" => 'votes.php',
    "$phrases[the_statics]" => 'statics',
    "$phrases[contact_us]" => 'contactus.php'
);


$permissions_checks = array(
    "$phrases[hot_items]" => 'hot_items',
    "$phrases[the_templates]" => 'templates',
    "$phrases[the_news]" => 'news',
    "$phrases[the_phrases]" => 'phrases',
    "$phrases[the_banners]" => 'adv',
    "$phrases[the_votes]" => 'votes',
    "$phrases[the_clients]" => 'clients',
    "$phrases[the_comments]" => 'comments',
    "$phrases[the_orders]" => 'orders',
    "$phrases[orders_status]" => 'orders_status'
);


$banners_places = array(
    "$phrases[offers_menu]" => 'offer',
    "$phrases[bnr_header]" => 'header',
    "$phrases[bnr_footer]" => 'footer',
    "$phrases[bnr_open]" => 'open',
    "$phrases[bnr_close]" => 'close',
    "$phrases[bnr_menu]" => 'menu'
);


$orderby_checks = array(
    "$phrases[the_date]" => 'id',
    "$phrases[the_price]" => 'price',
    "$phrases[the_name]" => 'name',
    "$phrases[availability]" => 'available'
);

//---- comments --------
$comments_types_phrases = array(
    "product" => "$phrases[the_products]",
    "news" => "$phrases[the_news]");

$comments_types = array_keys($comments_types_phrases);

//----- reports ----- 
$reports_types_phrases = array(
    "comment" => $phrases['the_comments'],
    "product" => $phrases['the_products']
);

$reports_types = array_keys($reports_types_phrases);

//--------------------
$rating_types = array('news', 'products');


$sitename = $settings['sitename'];
$section_name = $settings['section_name'];
$siteurl = "http://$_SERVER[HTTP_HOST]";
$script_path = trim(str_replace(rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $_SERVER['DOCUMENT_ROOT']), "/"), "", CWD), "/");
$scripturl = $siteurl . iif($script_path, "/" . $script_path, "");
$upload_types = explode(',', str_replace(" ", "", strtolower($settings['uploader_types'])));
$mailing_email = str_replace("{domain_name}", $_SERVER['HTTP_HOST'], $settings['mailing_email']);

//------ validate styleid functon ------
function is_valid_styleid($styleid) {
    if (is_numeric($styleid)) {
        $data = db_qr_fetch("select count(id) as num from store_templates_cats where id='$styleid' and selectable=1");
        if ($data['num']) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

//----- check if valid styleid -------
$styleid = (isset($styleid) ? intval($styleid) : $session->get("styleid"));
if (!is_valid_styleid($styleid)) {
    $styleid = $settings['default_styleid'];
    if (!is_valid_styleid($styleid)) {
        $styleid = 1;
    }
}
//----- get style settings ----//
$data_style = db_qr_fetch("select images from  store_templates_cats where id='" . db_escape($styleid) . "'");
$style['images'] = iif($data_style['images'], $data_style['images'], "images");

$session->set('styleid', intval($styleid));


//------- theme file ---------
require(CWD . "/includes/functions_themes.php");
//---------
require(CWD . "/includes/functions_forms.php");
require(CWD . "/includes/functions_cart.php");
require(CWD . "/includes/functions_clients.php");

init_members_connector();

require(CWD . '/includes/functions_comments.php');

function if_admin($dep = "", $continue = 0) {
    global $user_info, $phrases;

    if (!$dep) {

        if ($user_info['groupid'] != 1) {



            if (!$continue) {

                print_admin_table("<center>$phrases[access_denied]</center>");

                die();
            }
            return false;
        } else {
            return true;
        }
    } else {
        if ($user_info['groupid'] != 1) {

            $data = db_qr_fetch("select * from store_user where id='$user_info[id]'");
            $prm_array = explode(",", $data['cp_permisions']);

            if (!in_array($dep, $prm_array)) {

                if (!$continue) {
                    print_admin_table("<center>$phrases[access_denied]</center>");
                    die();
                }
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }
}

//-------------------------------------------------------------
function get_image($src, $default = "", $path = "") {
    global $style;
    if ($src) {
        return $path . $src;
    } else {
        if ($default) {
            return $path . $default;
        } else {
            return $path . "$style[images]/no_pic.gif";
        }
    }
}

//------------ copyrights text ---------------------
function print_copyrights() {
    global $_SERVER, $settings, $copyrights_lang;

    if (COPYRIGHTS_TXT_MAIN) {
        if ($copyrights_lang == "arabic") {
            print "<p align=center>جميع الحقوق محفوظة لـ :
<a target=\"_blank\" href=\"http://$_SERVER[HTTP_HOST]\">$settings[copyrights_sitename]</a> © " . date('Y') . " <br>
برمجة <a target=\"_blank\" href=\"http://allomani.com/\"> اللوماني للخدمات البرمجية </a> © " . SCRIPT_YEAR;
        } else {
            print "<p align=center>Copyright © " . date('Y') . " <a target=\"_blank\" href=\"http://$_SERVER[HTTP_HOST]\">$settings[copyrights_sitename]</a> - All rights reserved <br>
Programmed By <a target=\"_blank\" href=\"http://allomani.com/\"> Allomani </a> © " . SCRIPT_YEAR;
        }
    }
}

//---------------------- Read File ------------------------
function read_file($filename) {
    $fn = fopen($filename, "r");
    $fdata = fread($fn, filesize($filename));
    fclose($fn);
    return $fdata;
}

//---------- validate email --------
function check_email_address($email) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        return false;
    } else {
        return true;
    }
}

//---------------------------------------------
function execphp_fix_tag($match) {
    // replacing WPs strange PHP tag handling with a functioning tag pair
    $output = '<?php' . $match[2] . '?>';
    return $output;
}

//------------------------------------------------------------
function run_php($content) {

    $content = str_replace(array("&#8216;", "&#8217;"), "'", $content);
    $content = str_replace(array("&#8221;", "&#8220;"), '"', $content);
    $content = str_replace("&Prime;", '"', $content);
    $content = str_replace("&prime;", "'", $content);
    // for debugging also group unimportant components with ()
    // to check them with a print_r($matches)
    $pattern = '/' .
            '(?:(?:<)|(\[))[\s]*\?php' . // the opening of the <?php or [?php tag
            '(((([\'\"])([^\\\5]|\\.)*?\5)|(.*?))*)' . // ignore content of PHP quoted strings
            '\?(?(1)\]|>)' . // the closing ? > or ?] tag
            '/is';
    $content = preg_replace_callback($pattern, 'execphp_fix_tag', $content);
    // to be compatible with older PHP4 installations
    // don't use fancy ob_XXX shortcut functions
    ob_start();
    $eval_result = eval(" ?> $content ");


    $output = ob_get_contents();
    ob_end_clean();

    /* if ( $eval_result === false && ( $error = error_get_last() ) ) {
      print_r($error);
      } */

    print $output;
    return $eval_result;
}

//---------------------------- Admin Login Function ---------------------------------
$user_info = array();

function check_admin_login() {
    global $user_info, $session;

    $user_info['username'] = $session->get('admin_username');
    $user_info['password'] = $session->get('admin_password');
    $user_info['id'] = intval($session->get('admin_id'));


    if ($user_info['id']) {

        $qr = db_query("select * from store_user where id='$user_info[id]'");
        if (db_num($qr)) {
            $data = db_fetch($qr);
            if ($data['username'] == $user_info['username'] && md5($data['password']) == $user_info['password']) {
                $user_info['email'] = $data['email'];
                $user_info['groupid'] = $data['group_id'];
                $user_info['perm_all_cats'] = $data['perm_all_cats'];

                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

//--------- Generate Random String -----------
function rand_string($length = 8) {

    // start with a blank password
    $password = "";

    // define possible characters
    $possible = "0123456789bcdfghjkmnpqrstvwxyz";

    // set up a counter
    $i = 0;

    // add random characters to $password until $length is reached
    while ($i < $length) {

        // pick a random character from the possible ones
        $char = substr($possible, mt_rand(0, strlen($possible) - 1), 1);

        // we don't want this character if it's already in the password
        if (!strstr($password, $char)) {
            $password .= $char;
            $i++;
        }
    }

    // done!
    return $password;
}

//---------------------- Send Email Function -------------------
function send_email($from_name, $from_email, $to_email, $subject, $msg, $html = 0, $encoding = "") {
    global $PHP_SELF, $config, $settings;
    $from_name = htmlspecialchars($from_name);
    $from_email = htmlspecialchars($from_email);
    $to_email = htmlspecialchars($to_email);
    $subject = htmlspecialchars($subject);
    // $msg=htmlspecialchars($msg);


    if (!$encoding) {
        $encoding = $settings['site_pages_encoding'];
    }

    // $from = "$from_name <$from_email>" ;
    $from = "=?" . $encoding . "?B?" . base64_encode($from_name) . "?= <$from_email>";
    $subject = "=?" . $encoding . "?B?" . base64_encode($subject) . "?=";


    $mailHeader = 'From: ' . $from . ' ' . "\r\n";
    $mailHeader .= "Reply-To: $from_email\r\n";
    $mailHeader .= "Return-Path: $from_email\r\n";
    $mailHeader .= "To: $to_email\r\n";
    $mailHeader.="MIME-Version: 1.0\r\n";
    $mailHeader .= "Content-Type: " . iif($html, "text/html", "text/plain") . "; charset=" . $encoding . "\r\n";

    if ($config['smtp']['enable']) {
        $mailHeader .= "Subject: $subject\r\n";
    }

    $mailHeader .= "Date: " . strftime("%a, %d %b %Y %H:%M:%S %Z") . "\r\n";
    $mailHeader .= "X-EWESITE: Allomani\r\n";
    $mailHeader .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $mailHeader .= "X-Mailer-File: " . "http://" . $_SERVER['HTTP_HOST'] . ($script_path ? "/" . $script_path : "") . $PHP_SELF . "\r\n";
    $mailHeader .= "X-Sender-IP: " . get_ip() . "\r\n";




    if ($config['smtp']['enable']) {

        if (!class_exists("smtp_class")) {
            require_once(CWD . "/includes/class_smtp.php");
        }

        $smtp = new smtp_class;

        $smtp->host_name = $config['smtp']['host_name'];
        $smtp->host_port = $config['smtp']['host_port'];
        $smtp->ssl = $config['smtp']['ssl'];
        $smtp->localhost = "localhost";       /* Your computer address */
        $smtp->direct_delivery = 0;           /* Set to 1 to deliver directly to the recepient SMTP server */
        $smtp->timeout = $config['smtp']['timeout'];    /* Set to the number of seconds wait for a successful connection to the SMTP server */
        $smtp->data_timeout = 0;              /* Set to the number seconds wait for sending or retrieving data from the SMTP server.
          Set to 0 to use the same defined in the timeout variable */
        $smtp->debug = $config['smtp']['debug'];                     /* Set to 1 to output the communication with the SMTP server */
        $smtp->html_debug = 1;                /* Set to 1 to format the debug output as HTML */

        if ($config['smtp']['username'] && $config['smtp']['password']) {
            $smtp->pop3_auth_host = $config['smtp']['host_name'];           /* Set to the POP3 authentication host if your SMTP server requires prior POP3 authentication */
            $smtp->user = $config['smtp']['username'];                     /* Set to the user name if the server requires authetication */
            $smtp->password = $config['smtp']['password'];                 /* Set to the authetication password */
            $smtp->realm = "";                    /* Set to the authetication realm, usually the authentication user e-mail domain */
        }

        $smtp->workstation = "";              /* Workstation name for NTLM authentication */
        $smtp->authentication_mechanism = ""; /* Specify a SASL authentication method like LOGIN, PLAIN, CRAM-MD5, NTLM, etc..
          Leave it empty to make the class negotiate if necessary */

        $mailResult = $smtp->SendMessage(
                $from_email, array(
            $to_email
                ), array(
            $mailHeader
                ), $msg, 0);

        if ($mailResult) {
            return true;
        } else {
            if ($config['smtp']['show_errors']) {
                print "<b>SMTP Error: </b> " . $smtp->error . "<br>";
            }
            return false;
        }
    } else {
        $mailResult = @mail($to_email, $subject, $msg, $mailHeader);

        if ($mailResult) {
            return true;
        } else {
            return false;
        }
    }
}

//----------- Get Hooks ------------
function get_plugins_hooks() {

    $hooklocations = array();

    $handle = opendir(CWD . '/xml/');
    while (($file = readdir($handle)) !== false) {
        if (!preg_match('#^hooks_(.*).xml$#i', $file, $matches)) {
            continue;
        }
        $product = $matches[1];

        $xml = @simplexml_load_file(CWD . "/xml/$file");
        if (count($xml->hooktype)) {
            foreach ($xml->hooktype as $hooktype) {
                foreach ($hooktype->hook as $hook) {
                    $type = $product . " : " . (string) $hooktype['type'];
                    $value = (string) $hook;
                    $hooklocations[$type][$value] = $value;
                }
            }
        }
    }
    ksort($hooklocations);
    return $hooklocations;
}

//--------- Get used hooks List -----------
$qr = db_query("select hookid from store_hooks where active='1'");
while ($data = db_fetch($qr)) {
    $used_hooks[] = $data['hookid'];
}
unset($qr, $data);

//-------------- compile hook --------------
function compile_hook($hookid) {
    global $used_hooks;
    if (is_array($used_hooks)) {
        if (in_array($hookid, $used_hooks)) {
            $qr = db_query("select code from store_hooks where hookid='" . db_escape($hookid) . "' and active='1' order by ord asc");
            if (db_num($qr)) {
                while ($data = db_fetch($qr)) {
                    run_php($data['code']);
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

//----- rating stars --------
function print_rating($type, $id, $rating = 0, $readonly = false) {
    global $style;

    print "
        <div class='rating_stars {$type}_rating'>
       <div id=\"" . $type . $id . "_rating_div\" class='rating_div'></div> 
       <div id=\"" . $type . $id . "_rating_status_div\" class='rating_status'></div>
       <div id='" . $type . $id . "_rating_loading_div' style=\"display:none;\" class='rating_loading'><img src='images/loading.gif'></div>
           </div>
        ";
    ?>     
    <script>
        rating_init('<?= $type ?>','<?= $id ?>',<?= $rating ?>,<?= iif($readonly, "true", "false") ?>,'<?= $style['images'] ?>');
    </script>
    <?
}

//---- time duration ----       
function time_duration($seconds, $use = null, $zeros = false) {
    global $phrases;
    // Define time periods
    $periods = array(
        'years' => 31556926,
        'Months' => 2629743,
        'weeks' => 604800,
        'days' => 86400,
        'hours' => 3600,
        'minutes' => 60,
        'seconds' => 1
    );

    $periods_names = array(
        'years' => $phrases['year_ago'],
        'Months' => $phrases['months_ago'],
        'weeks' => $phrases['weeks_ago'],
        'days' => $phrases['days_ago'],
        'hours' => $phrases['hours_ago'],
        'minutes' => $phrases['minutes_ago'],
        'seconds' => $phrases['seconds_ago']
    );



    // Break into periods
    $seconds = (float) $seconds;
    $segments = array();
    foreach ($periods as $period => $value) {
        if ($use && strpos($use, $period[0]) === false) {
            continue;
        }
        $count = floor($seconds / $value);
        if ($count == 0 && !$zeros) {
            continue;
        }
        $segments[$period] = $count;
        $seconds = $seconds % $value;
    }

    if (count($segments) == 0) {
        $segments['seconds'] = 1;
    }
    // Build the string
    $string = array();

    foreach ($segments as $key => $value) {



        $segment = $value . ' ' . $periods_names[$key];

        $string[] = $segment;
        break;
    }

    return "$phrases[since] " . implode(', ', $string);
}

//--------- iif expression ------------
function iif($expression, $returntrue, $returnfalse = '') {
    return ($expression ? $returntrue : $returnfalse);
}

function array_get_key($arr, $value) {
    foreach ($arr as $key => $val) {
        if ($val == $value) {
            return $key;
        }
    }
    return false;
}

//---------- Flush Function -------------
function data_flush() {
    static $output_handler = null;
    if ($output_handler === null) {
        $output_handler = @ini_get('output_handler');
    }

    if ($output_handler == 'ob_gzhandler') {
        // forcing a flush with this is very bad
        return;
    }

    flush();
    if (PHP_VERSION >= '4.2.0' AND function_exists('ob_flush') AND function_exists('ob_get_length') AND ob_get_length() !== false) {
        @ob_flush();
    } else if (function_exists('ob_end_flush') AND function_exists('ob_start') AND function_exists('ob_get_length') AND ob_get_length() !== FALSE) {
        @ob_end_flush();
        @ob_start();
    }
}


//--------------- Get file Extension ----------
function file_extension($filename) {
    return substr(strrchr($filename, '.'), 1);
}

//-------------- Get Dir Files List ----------
function get_files($dir, $allowed_types = "", $subdirs_search = 1) {
    $dir = (substr($dir, -1, 1) == "/" ? substr($dir, 0, strlen($dir) - 1) : $dir);

    if ($dh = opendir($dir)) {

        $files = Array();
        $inner_files = Array();

        while ($file = readdir($dh)) {
            if ($file != "." && $file != ".." && $file[0] != '.') {
                if (is_dir($dir . "/" . $file) && $subdirs_search) {
                    $inner_files = get_files($dir . "/" . $file, $allowed_types);
                    if (is_array($inner_files))
                        $files = array_merge($files, $inner_files);
                }else {
                    $fileinfo = pathinfo($dir . "/" . $file);
                    $imtype = $fileinfo["extension"];
                    if (is_array($allowed_types)) {
                        if (in_array($imtype, $allowed_types)) {
                            $files[] = $dir . "/" . $file;
                        }
                    } else {
                        $files[] = $dir . "/" . $file;
                    }
                }
            }
        }

        closedir($dh);
        return $files;
    }
}

//----------- Number Format --------------------
function convert_number_format($number, $decimals = 0, $bytesize = false, $decimalsep = null, $thousandsep = null) {

    $type = '';

    if (empty($number)) {
        return 0;
    } else if (preg_match('#^(\d+(?:\.\d+)?)(?>\s*)([mkg])b?$#i', trim($number), $matches)) {
        switch (strtolower($matches[2])) {
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

    if ($bytesize) {
        if ($number >= 1073741824) {
            $number = $number / 1073741824;
            $decimals = 2;
            $type = " GB";
        } else if ($number >= 1048576) {
            $number = $number / 1048576;
            $decimals = 2;
            $type = " MB";
        } else if ($number >= 1024) {
            $number = $number / 1024;
            $decimals = 1;
            $type = " KB";
        } else {
            $decimals = 0;
            $type = " Byte";
        }
    }

    if ($decimalsep === null) {
        //   $decimalsep = ".";
    }
    if ($thousandsep === null) {
        //    $thousandsep = ",";
    }

    if ($decimalsep && $thousandsep) {
        return str_replace('_', '&nbsp;', number_format($number, $decimals, $decimalsep, $thousandsep)) . $type;
    } else {
        return str_replace('_', '&nbsp;', round($number, $decimals)) . $type;
    }
}

//--------------------- preview Text ------------------------------------
function getPreviewText($text) {
    global $preview_text_limit;
    // Strip all tags
    $desc = strip_tags(html_entity_decode($text), "<a><em>");
    $charlen = 0;
    $crs = 0;
    if (strlen_HTML($desc) == 0)
        $preview = substr($desc, 0, $preview_text_limit);
    else {
        $i = 0;
        while ($charlen < 80) {
            $crs = strpos($desc, " ", $crs) + 1;
            $lastopen = strrpos(substr($desc, 0, $crs), "<");
            $lastclose = strrpos(substr($desc, 0, $crs), ">");
            if ($lastclose > $lastopen) {
                // we are not in a tag
                $preview = substr($desc, 0, $crs);
                $charlen = strlen_noHTML($preview);
            }
            $i++;
        }
    }
    return trim($preview);
}

function strlen_noHtml($string) {
    $crs = 0;
    $charlen = 0;
    $len = strlen($string);
    while ($crs < $len) {
        $offset = $crs;
        $crs = strpos($string, "<", $offset);
        if ($crs === false) {
            $crs = $len;
            $charlen += $crs - $offset;
        } else {
            $charlen += $crs - $offset;
            $crs = strpos($string, ">", $crs) + 1;
        }
    }
    return $charlen;
}

function strlen_Html($string) {
    $crs = 0;
    $charlen = 0;
    $len = strlen($string);
    while ($crs < $len) {
        $scrs = strpos($string, "<", $crs);
        if ($scrs === false) {
            $crs = $len;
        } else {
            $crs = strpos($string, ">", $scrs) + 1;
            if ($crs === false)
                $crs = $len;
            $charlen += $crs - $scrs;
        }
    }
    return $charlen;
}

//------------- convert ar 2 en ------------------

function convert2en($filename) {

    if (!preg_match("/^([-a-zA-Z0-9_.!@#$&*+=|~%^()\/\\'])*$/", $filename)) {
        $filename = str_replace("'", "", $filename);
        $filename = str_replace(" ", "_", $filename);
        $filename = str_replace("ا", "a", $filename);
        $filename = str_replace("أ", "a", $filename);
        $filename = str_replace("إ", "i", $filename);
        $filename = str_replace("ب", "b", $filename);
        $filename = str_replace("ت", "t", $filename);
        $filename = str_replace("ث", "th", $filename);
        $filename = str_replace("ج", "g", $filename);
        $filename = str_replace("ح", "7", $filename);
        $filename = str_replace("خ", "k", $filename);
        $filename = str_replace("د", "d", $filename);
        $filename = str_replace("ذ", "d", $filename);
        $filename = str_replace("ر", "r", $filename);
        $filename = str_replace("ز", "z", $filename);
        $filename = str_replace("س", "s", $filename);
        $filename = str_replace("ش", "sh", $filename);
        $filename = str_replace("ص", "s", $filename);
        $filename = str_replace("ض", "5", $filename);
        $filename = str_replace("ع", "a", $filename);
        $filename = str_replace("غ", "gh", $filename);
        $filename = str_replace("ف", "f", $filename);
        $filename = str_replace("ق", "k", $filename);
        $filename = str_replace("ك", "k", $filename);
        $filename = str_replace("ل", "l", $filename);
        $filename = str_replace("ن", "n", $filename);
        $filename = str_replace("ه", "h", $filename);
        $filename = str_replace("ي", "y", $filename);
        $filename = str_replace("ط", "6", $filename);
        $filename = str_replace("ظ", "d", $filename);
        $filename = str_replace("و", "w", $filename);
        $filename = str_replace("ؤ", "o", $filename);
        $filename = str_replace("ئ", "i", $filename);
        $filename = str_replace("لا", "la", $filename);
        $filename = str_replace("لأ", "la", $filename);
        $filename = str_replace("ى", "a", $filename);
        $filename = str_replace("ة", "t", $filename);
        $filename = str_replace("م", "m", $filename);
    }


    return $filename;
}

//--------------------------- Create Thumb ----------------------------
function create_thumb($filename, $width = 65, $height = 65, $fixed = false, $suffix = '', $replace_exists = false, $save_filename = '') {
    //  require_once(CWD . '/includes/class_img_resize.php');

    if (function_exists("ImageCreateTrueColor") && file_exists(CWD . "/$filename")) {



        if ($fixed) {
            $option = 'crop';
        } else {
            $option = 'auto';
        }

        $resizeObj = new img_resize(CWD . "/" . $filename);

        // *** 2) Resize image (options: exact, portrait, landscape, auto, crop)
        $resizeObj->resizeImage($width, $height, $option);



        $imtype = strtolower(file_extension(CWD . "/$filename"));
        if ($save_filename) {
            $save_name = $save_filename;
            $save_path = str_replace("/" . basename($filename), '', $filename);
        } else {
            $save_name = basename($filename);
            $save_path = str_replace("/" . $save_name, '', $filename);

            $imtype = strtolower(file_extension($save_name));
            $save_name = convert2en($save_name);
            $save_name = strtolower($save_name);
            $save_name = str_replace(" ", "_", $save_name);


            if ($suffix) {
                $save_name = str_replace(".$imtype", "", $save_name) . "_" . $suffix . ".$imtype";
            }



            while (file_exists(CWD . "/" . $save_path . "/" . $save_name)) {
                $save_name = str_replace(".$imtype", "", $save_name) . "_" . rand(0, 999) . ".$imtype";
            }
        }

        // *** 3) Save image

        $resizeObj->saveImage(CWD . "/" . $save_path . "/" . $save_name, 100);
        return ($save_path . "/" . $save_name);
    } else {
        return false;
    }
}

//--------------------- Delete File ---------------
function delete_file($filename) {

    if (file_exists($filename)) {
        @unlink($filename);
    }
}

//---------- Pages Links ---------//
function print_pages_links($start, $items_count, $items_perpage, $page_string) {
    global $f_page_string, $nextpag, $prevpag, $f_items_perpage, $f_start, $f_end, $start, $phrases, $f_cur_page, $f_pages;




    $pages = intval($items_count / $items_perpage);
    if ($items_count % $items_perpage) {
        $pages++;
    }


    $pages_line_limit = 8;
    $pages_line_min = $pages_line_limit / 2;

    $f_cur_page = iif($start, ($start / $items_perpage) + 1, 1);
    $f_start = iif($f_cur_page <= $pages_line_min, 1, $f_cur_page - $pages_line_min);
    $f_end = iif($pages < $pages_line_min, $pages, iif($f_start + $pages_line_limit <= $pages, $f_start + $pages_line_limit, $pages));


    if ($items_count > $items_perpage) {

        $f_page_string = $page_string;
        $f_items_perpage = $items_perpage;
        $f_pages = $pages;


        run_template('pages_links');
    }
}

//------------- if Product Cat Admin ---------
function if_products_cat_admin($cats, $skip_zero_id = true) {
    global $user_info, $phrases;

    if ($user_info['perm_all_cats'] || $user_info['groupid'] == 1) {
        return true;
    }


    $cats = (array) $cats;

    foreach ($cats as $cat) {
        if ($cat) {
            $cat_users = get_product_cat_users($cat, true);


            if (!in_array($user_info['id'], $cat_users)) {
                print_admin_table("<center>$phrases[err_cat_access_denied]</center>");
                die();
            }
        } else {
            if (!$skip_zero_id) {
                print_admin_table("<center>$phrases[err_cat_access_denied]</center>");
                die();
            }
        }
    }
}

//---------- Get Products Cats --------//
function get_products_cats($id) {
    $cats_arr = array();
    $cats_arr[] = $id;

    $qr1 = db_query("select id from store_products_cats where cat='$id'");
    while ($data1 = db_fetch($qr1)) {
        $nxx = get_products_cats($data1['id']);
        if (is_array($nxx)) {
            $cats_arr = array_merge($nxx, $cats_arr);
        }
        unset($nxx);
    }

    return $cats_arr;
}

//-------- Get Cat custom fields -------
function get_product_cat_fields($id, $fields_only = false, $in_search = true, $in_details = true, $in_short_details = true) {
    global $cat_fields_cache;

    $fields_array = array();
    //     $dir_data['cat'] = intval($id) ;
    if (!$id) {
        return array();
    }

    $data_path = db_qr_fetch("select id,path,`fields` from store_products_cats where id='$id'");
    $cat_fields_cache[$data_path['id']] = $data_path;

    $path_array = explode(",", $data_path['path']);
    $path_array = (array) $path_array;


    //--------- pre caching Path ------//     
    $qr = db_query("select name,id,cat,`fields` from store_products_cats where id IN (" . $data_path['path'] . ")");
    while ($data = db_fetch($qr)) {
        if (!isset($cat_fields_cache[$data['id']])) {
            $cat_fields_cache[$data['id']] = $data;
        }
    }

//---------------------------------------    


    foreach ($path_array as $cat_id) {
        if ($cat_id) {



            //--------- caching ------//
            if (isset($cat_fields_cache[$cat_id])) {
                $dir_data = $cat_fields_cache[$cat_id];
            } else {
                $dir_data = db_qr_fetch("select `fields` from store_products_cats where id='$cat_id'");
                $cat_fields_cache[$cat_id] = $dir_data;
            }
            //--------------------------//   


            if ($dir_data['fields']) {

                if ($in_search && $in_details && $in_short_details) {
                    $cat_fields = explode(",", $dir_data['fields']);
                } else {

                    $qr_fields = db_query("select id from store_fields_sets where id IN ($dir_data[fields])" . iif($in_search, " and in_search=1") . iif($in_details, " and in_details=1") . iif($in_short_details, " and in_short_details=1 and active=1"));
                    while ($data_fields = db_fetch($qr_fields)) {
                        $cat_fields[] = $data_fields['id'];
                    }
                }
            } else {
                $cat_fields = array();
            }



            //  $data = db_qr_fetch("select `fields` from store_products_cats where id='".$dir_data['id']."'");


            for ($z = 0; $z < count($cat_fields); $z++) {
                if ($fields_only) {
                    if (!in_array($cat_fields[$z], $fields_array)) {
                        $fields_array[] = $cat_fields[$z];
                    }
                } else {
                    $fields_array[$cat_fields[$z]] = $cat_id;
                }
            }
        }
    }




    return $fields_array;
}

//-------- Get Cat Payment Methods -------
function get_product_cat_payment_methods($id, $fields_only = false) {

    $fields_array = array();
    $dir_data['cat'] = intval($id);
    while ($dir_data['cat'] != 0) {
        $dir_data = db_qr_fetch("select id,cat from store_products_cats where id='$dir_data[cat]'");

        $data = db_qr_fetch("select name,`payment_methods` from store_products_cats where id='" . $dir_data['id'] . "'");
        if (trim($data['payment_methods'])) {
            $cat_fields = explode(",", $data['payment_methods']);

            for ($z = 0; $z < count($cat_fields); $z++) {

                if ($fields_only) {
                    if (!in_array($cat_fields[$z], $fields_array)) {
                        $fields_array[] = (int) $cat_fields[$z];
                    }
                } else {
                    $fields_array[$cat_fields[$z]] = (int) $dir_data['id'];
                }
            }
        }
    }

    return $fields_array;
}

//-------- Get Cat Shipping Methods -------
function get_product_cat_shipping_methods($id, $fields_only = false) {

    $fields_array = array();
    $dir_data['cat'] = intval($id);
    while ($dir_data['cat'] != 0) {
        $dir_data = db_qr_fetch("select id,cat from store_products_cats where id='$dir_data[cat]'");

        $data = db_qr_fetch("select `shipping_methods` from store_products_cats where id='" . $dir_data['id'] . "'");
        if (trim($data['shipping_methods'])) {
            $cat_fields = explode(",", $data['shipping_methods']);

            for ($z = 0; $z < count($cat_fields); $z++) {

                if ($fields_only) {
                    if (!in_array($cat_fields[$z], $fields_array)) {
                        $fields_array[] = $cat_fields[$z];
                    }
                } else {
                    $fields_array[$cat_fields[$z]] = $dir_data['id'];
                }
            }
        }
    }

    return $fields_array;
}

//-------- Get Cat users  -------
function get_product_cat_users($id, $fields_only = false, $type = '') {



    $fields_array = array();
    $dir_data['cat'] = intval($id);
    while ($dir_data['cat'] != 0) {
        $dir_data = db_qr_fetch("select id,cat from store_products_cats where id='$dir_data[cat]'");



        $data = db_qr_fetch("select `users` from store_products_cats where id='" . $dir_data['id'] . "'");
        if (trim($data['users'])) {
            $cat_fields = explode(",", $data['users']);

            for ($z = 0; $z < count($cat_fields); $z++) {
                if ($fields_only) {
                    if (!in_array($cat_fields[$z], $fields_array)) {
                        $fields_array[] = $cat_fields[$z];
                    }
                } else {
                    $fields_array[$cat_fields[$z]] = $dir_data['id'];
                }
            }
        }
    }




    return $fields_array;
}

//----------get short details fields data  ---------
function get_short_details_fields_data($id, $cat) {
    global $phrases, $style, $cat_short_fields_cache, $short_details_fields_count;
    ;
    if ($short_details_fields_count) {
        $id = intval($id);
        $cat = intval($cat);



        $qrf = db_query("select * from store_fields_data where product_id='$id'");
        if (db_num($qrf)) {
            //--- caching data --//
            while ($pre_dataf_x = db_fetch($qrf)) {
                $pre_dataf[$pre_dataf_x['cat']] = $pre_dataf_x;
                $sets_ids[] = $pre_dataf_x['cat'];
            }
            unset($qrf, $pre_dataf_x);
            //--- caching sets ---//
            $fs_qr = db_query("select id,name,title,type,img from store_fields_sets where id IN (" . implode(",", $sets_ids) . ") and in_short_details=1 and active=1 order by ord");
            while ($fs_data = db_fetch($fs_qr)) {
                $sets_array[] = $fs_data;
            }
            unset($fs_data, $fs_qr, $sets_ids);
            //--------------------//

            if (count($sets_array)) {
                $fields_content = "<br><table>";
                foreach ($sets_array as $field_name) {


                    if (isset($pre_dataf[$field_name['id']])) {

                        $dataf = $pre_dataf[$field_name['id']];


                        $fields_content .= "<tr><td>" . iif($field_name['img'], "<img src=\"$field_name[img]\">&nbsp;") . "<b>" . iif($field_name['title'], $field_name['title'], $field_name['name']) . "</b></td><td>";
                        if ($field_name['type'] == "text") {
                            $fields_content .= "$dataf[value]";
                        } elseif ($field_name['type'] == "select") {
                            $option_name = db_qr_fetch("select value from store_fields_options where id='$dataf[value]'");
                            $fields_content .= iif($option_name['value'], $option_name['value'], "$phrases[not_selected]");
                        } elseif ($field_name['type'] == "checkbox") {
                            $values_arr = unserialize($dataf['value']);
                            if (!is_array($values_arr)) {
                                $values_arr = array();
                            }


                            $qr_options = db_query("select id,value,img from store_fields_options where field_id='$dataf[cat]' order by ord");
                            if (db_num($qr_options)) {
                                $fields_content .= "<table>";
                                while ($data_options = db_fetch($qr_options)) {
                                    $fields_content .= "<tr><td>" . iif($data_options['img'], "<img src=\"$data_options[img]\">&nbsp;") . "$data_options[value] </td><td><img src=\"$style[images]/" . iif(in_array($data_options['id'], $values_arr), "true.gif", "false.gif") . "\" border=0></td></tr>";
                                }
                                $fields_content .= "</table>";
                            }
                        }
                        $fields_content .= "</td></tr>
  <tr><td colspan=2><hr class='separate_line' size=1></td></tr>";
                    }
                }

                $fields_content .= "</table><br><br>";
            } else {
                $fields_content = "";
            }

            //------------------------------
        }
        print $fields_content;
    }
}

//------ cat path str -----
function get_cat_path_str($cat) {
    $dir_data['cat'] = intval($cat);
    $path_arr[] = $dir_data['cat'];
    while ($dir_data['cat'] != 0) {

        $dir_data = db_qr_fetch("select id,cat,`fields` from store_products_cats where id='$dir_data[cat]'");
        $path_arr[] = $dir_data['cat'];
    }
    return implode(",", $path_arr);
}

//--------- Login Redirection -----------
function login_redirect() {
    global $phrases;
    print "<form action='index.php' method=post name=lg_form>
<input type=hidden name=action value='login'>
 <input type=hidden name='re_link' value=\"http://$_SERVER[HTTP_HOST]" . "$_SERVER[REQUEST_URI]\">
 $phrases[redirection_msg] <input type=submit value='$phrases[click_here]'> 
 </form>
 
 <script>
 document.forms['lg_form'].submit();
 </script>";
}

//----------------- Path Links ---------
function print_path_links($cat, $filename = "") {
    global $phrases, $style, $links, $global_align, $cats_data, $cats_array;

    $cat = intval($cat);
    if ($cat) {
        $data_cat = db_qr_fetch("select name,id,cat,path from store_products_cats where id='$cat'");
        $qr = db_query("select name,id,cat from store_products_cats where id IN (" . $data_cat['path'] . ")");
        while ($data = db_fetch($qr)) {
            $cats_data[$data['id']] = $data;
        }

        $cats_array = explode(",", $data_cat['path']);
    } else {
        $cats_array = array();
    }

    run_template('path_links');
}

function login_header() {
    print "
";
}

function print_redirection($link, $print_header = 1) {
    global $sitename, $settings, $global_dir, $style, $re_link;

    $re_link = htmlspecialchars($link);

    if ($print_header) {
        run_template('page_head');
    }


    run_template('redirection_page');



    print "<script>window.location=\"$re_link\";</script>";
}

//------ is serialized ---------
function is_serialized($string) {
    if (preg_match("/(a|O|s|b)\x3a[0-9]*?((\x3a((\x7b?(.+)\x7d)|(\x22(.+)\x22\x3b)))|(\x3b))/", $string)) {
        return true;
    } else {
        return false;
    }
}

//--------------- date -------------
function get_date($time = 0, $format = "") {
    global $settings;

    if (!$time) {
        $time = time();
    }
    if (!$format) {
        $format = $settings['date_format'];
    }
    $date = date($format, $time);
    if ($settings['arabic_date_months']) {
        $date = str_ireplace(array("jan", "feb", "mar", "apr", "may", "jun", "Jul", "aug", "sep", "oct", "nov", "dec"), array("يناير", "فبراير", "مارس", "ابريل", "مايو", "يونيو", "يوليو", "اغسطس", "سبتمبر", "اكتوبر", "نوفمبر", "ديسمبر"), $date);
    }

    if ($settings['arabic_date_days']) {
        $date = str_ireplace(array("sat", "sun", "mon", "tue", "wed", "thu", "fri"), array("السبت", "الاحد", "الاثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعة"), $date);
    }

    return $date;
}

//----------- product del ------
function product_del($id, $type = "id") {
    if ($type == "cat") {
        $qr = db_query("select id from store_products_data where cat='$id'");
        while ($data = db_fetch($qr)) {
            db_query("delete from store_products_data where id='$data[id]'");
            db_query("delete from store_fields_data where product_id='$data[id]'");
            db_query("delete from store_products_photos where product_id='$data[id]'");
        }
    } else {
        db_query("delete from store_products_data where id='$id'");
        db_query("delete from store_fields_data where product_id='$id'");
        db_query("delete from store_products_photos where product_id='$id'");
    }
}

//------- array remove empty values --------//
function array_remove_empty_values($arr) {
    for ($i = 0; $i < count($arr); $i++) {
        $key = key($arr);
        $value = current($arr);
        if ($value) {
            $new_arr[$key] = $value;
        }
        next($arr);
    }
    return $new_arr;
}

// ---- Date / Time -------
function datetime($format = "", $time = "") {
    return date(iif($format, $format, "Y-m-d h:i:s"), iif($time, $time, time()));
}

//------- Error Handler ----------//
function error_handler($errno, $errstr, $errfile, $errline, $vars) {
    global $config;

    switch ($errno) {
        case E_WARNING:
        case E_USER_WARNING:
            /* Don't log warnings due to to the false bug reports about valid warnings that we suppress, but still appear in the log
             */

            if ($config['debug']['log_errors']) {
                $message = "Warning: $errstr in $errfile on line $errline";
                do_error_log($message, 'php');
            }


            if (!$config['debug']['display_errors'] || !error_reporting()) {
                return;
            }

            $errfile = str_replace(CWD . DIRECTORY_SEPARATOR, '', $errfile);
            echo "<br /><strong>Warning</strong>: $errstr in <strong>$errfile</strong> on line <strong>$errline</strong><br />";
            break;

        case E_USER_ERROR:

            if ($config['debug']['log_errors']) {
                $message = "Fatal error: $errstr in $errfile on line $errline";
                do_error_log($message, 'php');
            }


            if ($config['debug']['display_errors']) {
                $errfile = str_replace(CWD . DIRECTORY_SEPARATOR, '', $errfile);
                echo "<br /><strong>Fatal error:</strong> $errstr in <strong>$errfile</strong> on line <strong>$errline</strong><br />";
            }
            exit;
            break;
    }
}

//--------- Error Log ---------//
function do_error_log($msg, $type = 'php') {
    global $config;

    $trace = @debug_backtrace();

    //$args = (array) $trace[1]['args'];
    //".implode(",",$args)."

    $dt = date("Y-m-d H:i:s (T)");
    $err = $dt . " : " . $msg . "\r\n";
    if ($trace[1]['function']) {
        $err .=$trace[1]['function'] . "() in : " . $trace[1]['file'] . ":" . $trace[1]['line'] . "\r\n";
    }
    $err .= "-------------- \r\n";

    if ($config['debug']['custom_error_handler']) {
        if (!file_exists($config['debug']['logs_path'])) {
            @mkdir($config['debug']['logs_path']);
        }

        if ($type == "db") {
            $log_file = $config['debug']['logs_path'] . "/error_db.log";
            $log_file_new = $config['debug']['logs_path'] . "/error_db_" . date("Y_m_d_h_i_s") . ".log";
        } else {
            $log_file = $config['debug']['logs_path'] . "/error.log";
            $log_file_new = $config['debug']['logs_path'] . "/error_" . date("Y_m_d_h_i_s") . ".log";
        }

        if (@filesize($log_file) >= $config['debug']['log_max_size']) {
            @rename($log_file, $log_file_new);
        }

        error_log($err, 3, $log_file);
    } else {
        error_log($err);
    }
}

//------- print block banners -----------//
function print_block_banners($data_array, $pos = "block") {
    global $data;
    foreach ($data_array as $data) {


        $ids[] = $data['id'];

        if ($pos == "block") {
            print "<tr>
                <td  width=\"100%\" valign=\"top\">";
        }

        if ($data['c_type'] == "code") {
            run_php($data['content']);
        } else {
            $template = iif($pos == "center", "center_banners", "blocks_banners");
            run_php(get_template($template));
        }
        if ($pos == "block") {
            print "</td>
        </tr>";
        }
    }

    if (is_array($ids)) {
        db_query("update store_banners set views=views+1 where id IN (" . implode(",", $ids) . ")");
    }
}

//----- valueof -------------//
function valueof($data, $index) {
    return $data[$index];
}

function toggle_tr_class() {
    global $tr_class;
    if ($tr_class == "row_1") {
        $tr_class = "row_2";
    } else {
        $tr_class = "row_1";
    }
}

//-------- Categories to Tree Array --------------
function cats_to_tree(&$categories) {

    $map = array(
        0 => array('children' => array())
    );

    foreach ($categories as &$category) {
        $category['children'] = array();
        $map[$category['key']] = &$category;
    }

    foreach ($categories as &$category) {
        $map[$category['parent']]['children'][] = &$category;
    }

    return $map[0]['children'];
}

function print_dynatree_node($data, $parent_selected = false) {

    foreach ($data as $x) {
        print "<li id='$x[key]' class='folder" . iif($x['select'] || $parent_selected, ' selected') . "'>$x[title]
                    ";
        if ($x['children']) {
            print "<ul>";
            print_dynatree_node($x['children'], ($x['select'] || $parent_selected));
            print "</ul>";
        }
        //  print "</li>";
    }
}

function print_dynatree_div($cats_arr, $div_name = 'cats_tree', $ul_name = 'treeData') {
    $arr = cats_to_tree($cats_arr);
    print "<div id='$div_name'>
    <ul id='$ul_name'>";
    print_dynatree_node($arr);
    print "</ul>
    </div>";
}

function get_ip() {
    // $_SERVER['HTTP_X_FORWARDED_FOR'];
    return $_SERVER['REMOTE_ADDR'];
}

//----- header redirect ------
function redirect($url) {
    header("Location: $url");
    die();
}

//---- Js Redirect -----//
function js_redirect($url, $with_body = false) {
    global $phrases;

    if ($with_body) {
        print "<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=$settings[site_pages_encoding]\" />
<head>
</head><body>";
    }

    print "<script>
var url = \"$url\";
 var a = document.createElement(\"a\");
 if(!a.click) { 
  window.location = url;
 }else{
 a.setAttribute(\"href\", url);
 a.style.display = \"none\";
 document.body.appendChild(a);
 a.click();
 }
 </script>
 
 <center> $phrases[redirection_msg] <a href=\"$url\">$phrases[click_here]</a></center>";
}

//--- get country available shipping methods ------//
function country_available_shipping_methods($country) {
    $data = db_fetch_all("select geo_id from store_geo_index where country_code = '" . db_escape($country) . "'");
    if (count($data)) {
        foreach ($data as $geo_id) {
            $geo_ids[] = $geo_id;
        }
        $data_methods = db_fetch_all("select shipping_methods from store_geo where id IN (" . implode(",", $geo_ids) . ")");
        $methods = array();
        foreach ($data_methods as $dm) {
            $zone_methods = (array) explode(",", $dm['shipping_methods']);
            foreach ($zone_methods as $method_id) {
                if (!in_array($method_id, $methods)) {
                    $methods[] = $method_id;
                }
            }
        }
    }

    $methods[] = 0;


    $data = db_fetch_all("select id from store_shipping_methods where id IN(" . implode(",", $methods) . ") or all_geo_zones='1' and active=1");
    foreach ($data as $f) {
        $final_ids[] = $f['id'];
    }


    return (array) $final_ids;
}

//--- get country available payment methods ------//
function country_available_payment_methods($country) {
    $data = db_fetch_all("select geo_id from store_geo_index where country_code = '" . db_escape($country) . "'");
    if (count($data)) {
        foreach ($data as $geo_id) {
            $geo_ids[] = $geo_id;
        }
        $data_methods = db_fetch_all("select payment_methods from store_geo where id IN (" . implode(",", $geo_ids) . ")");
        $methods = array();
        foreach ($data_methods as $dm) {
            $zone_methods = (array) explode(",", $dm['payment_methods']);
            foreach ($zone_methods as $method_id) {
                if (!in_array($method_id, $methods)) {
                    $methods[] = $method_id;
                }
            }
        }
    }

    $methods[] = 0;


    $data = db_fetch_all("select id from store_payment_methods where id IN(" . implode(",", $methods) . ") or all_geo_zones='1' and active=1");
    foreach ($data as $f) {
        $final_ids[] = $f['id'];
    }


    return (array) $final_ids;
}

//----- get items shared shipping methods -----//
function items_available_shipping_methods($items) {

    $items_cats = array();
    $total_price = 0;
    $total_weight = 0;
    $total_items = count($items);

    foreach ($items as $item) {

        $data = $item['data'];

        $total_price += $data['item_price'];
        $total_weight += $data['weight'];


        if (!in_array($data['cat'], $items_cats)) {
            $items_cats[] = $data['cat'];

            $cat_shipping = get_product_cat_shipping_methods($data['cat'], true);

            if (count($shipping_ids)) {

                unset($tmp_arr);
                foreach ($cat_shipping as $cat_shipping_id) {

                    if (in_array($cat_shipping_id, $shipping_ids)) {
                        $tmp_arr[] = $cat_shipping_id;
                    }
                }
                $shipping_ids = $tmp_arr;
            } else {
                $shipping_ids = $cat_shipping;
            }
            unset($cat_shipping);
        }
    }
    $shipping_ids[] = 0;

    $final_ids = array();
    $data_arr = db_fetch_all("select id from store_shipping_methods where (id IN (" . implode(",", $shipping_ids) . ") or all_cats=1) and (min_price <= $total_price or min_price=0) and (max_price >= $total_price or max_price=0) and (min_weight <= $total_weight or min_weight=0) and (max_weight >= $total_weight or max_weight=0) and (min_items <= $total_items or min_items=0) and (max_items >= $total_items or max_items=0) and active=1");
    foreach ($data_arr as $data_sm) {
        $final_ids[] = $data_sm['id'];
    }

    return (array) $final_ids;
}

//----- get items shared payment methods -----//
function items_available_payment_methods($items) {
    $items_cats = array();
    $total_price = 0;
    $total_weight = 0;
    $total_items = count($items);


    foreach ($items as $item) {

        $data = $item['data'];

        $total_price += $data['item_price'];
        $total_weight += $data['weight'];


        if (!in_array($data['cat'], $items_cats)) {
            $items_cats[] = $data['cat'];

            $cat_payment = get_product_cat_payment_methods($data['cat'], true);

            if (count($payment_ids)) {

                unset($tmp_arr);
                foreach ($cat_payment as $cat_payment_id) {

                    if (in_array($cat_payment_id, $payment_ids)) {
                        $tmp_arr[] = $cat_payment_id;
                    }
                }
                $payment_ids = $tmp_arr;
            } else {
                $payment_ids = $cat_payment;
            }
            unset($cat_payment);
        }
    }
    $payment_ids[] = 0;


    $final_ids = array();

    $data_arr = db_fetch_all("select id from store_payment_methods where (id IN (" . implode(",", $payment_ids) . ") or all_cats=1) and (min_price <= $total_price or min_price=0) and (max_price >= $total_price or max_price=0)  and (min_items <= $total_items or min_items=0) and (max_items >= $total_items or max_items=0) and active=1");
    foreach ($data_arr as $data_sm) {
        $final_ids[] = $data_sm['id'];
    }

    return (array) $final_ids;
}

function show_alert($msg = "", $type = "") {
    print "<div class=\"alert $type\">$msg</div>";
}

//-------- get country name by code ------------
function get_country_name($code) {
    return db_fetch_first("select name from store_countries where code = '" . db_escape($code) . "'");
}

//--------- load plugins function --------     
function load_plugins($file) {
    $dhx = @opendir(CWD . "/plugins");
    while ($rdx = @readdir($dhx)) {
        if ($rdx != "." && $rdx != "..") {
            $cur_fl = CWD . "/plugins/" . $rdx . "/" . $file;
            if (@file_exists($cur_fl)) {
                $pl_files[] = $cur_fl;
            }
        }
    }
    @closedir($dhx);

    return $pl_files;
}

//--------------- Load Global Plugins --------------------------
$pls = load_plugins("global.php");
if (is_array($pls)) {
    foreach ($pls as $pl) {
        include($pl);
    }
}
?>