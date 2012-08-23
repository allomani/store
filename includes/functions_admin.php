<?

//---------------- get timezones --------------------
function get_timezones() {
 /*   require_once(CWD . '/includes/class_xml.php');
    $xmlobj = new XMLparser(false, CWD . "/xml/time_zones.xml");
    $xml = $xmlobj->parse();
    return (array) $xml['zone'];*/
    $zones = array();
   $xml = @simplexml_load_file(CWD . "/xml/time_zones.xml");
   if(count($xml->zone)){
       foreach($xml->zone as $zone){
           $val = (string) $zone['name'];
           $key = (string) $zone;
           $zones[$key] = $val;
           }
       }
   return  $zones;
}
//----------------- Admin Path Links ---------
function print_admin_path_links($cat, $filename = "") {
    global $phrases, $global_align;

    $dir_data['cat'] = intval($cat);
    while ($dir_data['cat'] != 0) {
        $dir_data = db_qr_fetch("select name,id,cat from store_products_cats where id='$dir_data[cat]'");


        $dir_content = "<a href='products.php?cat=$dir_data[id]'>$dir_data[name]</a> / " . $dir_content;
    }
    print "<p align=$global_align><img src='images/link.gif'> <a href='products.php?cat=0'>$phrases[the_products]  </a> / $dir_content " . "$filename</p>";
}

//--------------------------------- Check Functions ---------------------------------
function check_safe_functions($condition_value) {

    global $phrases;


    //------ get safe functions ----------

    $xml = (array) @simplexml_load_file(CWD . "/xml/safe_functions.xml");
    $safe_functions =  (array) $xml['func'];
   
    if (!count($safe_functions)) {
        return "Error : Please check safe functions XML File";
    }
//------------------------------------------


    if (preg_match_all('#([a-z0-9_{}$>-]+)(\s|/\*.*\*/|(\#|//)[^\r\n]*(\r|\n))*\(#si', $condition_value, $matches)) {

        $functions = array();
        foreach ($matches[1] AS $key => $match) {
            if (!in_array(strtolower($match), $safe_functions) && function_exists(strtolower($match))) {
                $funcpos = strpos($condition_value, $matches[0]["$key"]);
                $functions[] = array(
                    'func' => stripslashes($match),
                        //    'usage' => substr($condition_value, $funcpos, (strpos($condition_value, ')', $funcpos) - $funcpos + 1)),
                );
            }
        }
        if (!empty($functions)) {
            unset($safe_functions[0], $safe_functions[1], $safe_functions[2]);



            foreach ($functions AS $error) {
                $errormsg .= "$phrases[err_function_usage_denied]: <code>" . htmlspecialchars($error['func']) . "</code>
                                                <br>\n";
            }

            return "$errormsg";
            //   return false ;
        } else {
            //   return true ;
            return false;
        }
    }
    //   return true ;
    return false;
}

//--------- print admin table -------------
function print_admin_table($content, $width = "50%", $align = "center") {
    print "<center><table class=grid width='$width'><tr><td align='$align'>$content</td></tr></table></center>";
}

//------------ Access Log ------------
   function access_log_record($username,$status){
       global $access_log_expire ;
        
       $expire_date  = datetime("",time()-(24*60*60*$access_log_expire));
       db_query("delete from store_access_log where date < '$expire_date'");
       db_query("insert into store_access_log (username,date,status) values ('".db_escape($username)."','".datetime()."','$status')");
   } 