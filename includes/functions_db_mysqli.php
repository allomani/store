<?

$queries = 0;
$last_sql = '';

//------------ db escape -------------//
function db_escape($str, $specialchars = true) {
    global $cn;
    
    if ($specialchars) {
        $str = htmlspecialchars($str);
    }
    return mysqli_real_escape_string($cn,$str);
}

//----------- Connect ----------
function db_connect($host, $user, $pass, $dbname, $dbcharset = "") {
    global $log_mysqli_errors, $db_charset,$cn;

    if (!$dbcharset) {
        $dbcharset = $db_charset;
    }

    $cn = @mysqli_connect($host, $user, $pass);
    if (!$cn) {
        if (mysqli_errno($cn) == 1040) {
            die("<center> Mysql Server is Busy  , Please Try again later  </center>");
        } else {

            if ($log_mysqli_errors) {
                do_error_log(@mysqli_errno($cn) . " : " . @mysqli_error($cn), 'db');
            }

            die(@mysqli_errno($cn) . " : Database Connection Error");
        }
    }

    db_select($dbname, $dbcharset);
}

//--------- select db ------------
function db_select($db_name, $db_charset = "") {
    global $log_mysqli_errors,$cn;

    $db_select = @mysqli_select_db($cn,$db_name);
    if (!$db_select) {

        if ($log_mysqli_errors) {
            do_error_log(@mysqli_errno($cn) . " : " . @mysqli_error($cn), 'db');
        }

        die("Database Name Error");
    }

    if ($db_charset) {
// db_query("set names '$db_charset'");  
        mysqli_set_charset($cn,$db_charset);
    }
}

//----------- query ------------------
function db_query($sql) {

    global $show_mysqli_errors, $log_mysqli_errors, $queries, $last_sql,$cn;

    $queries++;
    // print $queries.$sql."<br>";
    //     print $queries . "." .$sql."<hr>";   

    $last_sql = $sql;


    $qr = @mysqli_query($cn,$sql);

    if (mysqli_errno($cn)) {
        if ($show_mysqli_errors) {
            print "
            <div style=\"align:left;direction:ltr;\">
            <span align=left><b>MySQL Error: </b></span> " . mysqli_error($cn) . "
                <br><br>";

            debug_print_backtrace();
            print "</div>";
        }
        if ($log_mysqli_errors) {
            do_error_log("$err \r\nSQL :  $last_sql", 'db');
        }


        return false;
    } else {
      

        return $qr;
    }
}

//---------------- fetch -------------------
function db_fetch($r) {
  
     if (is_object($r)) {
        $qr = $r;
    } else {
        $qr = db_query($r);
    }
    
    return @mysqli_fetch_assoc($qr);
}

// ------------------------ num -----------------------
function db_num($qr) {
   

    return @mysqli_num_rows($qr);
}

//------------------ Query + fetch ----------------------
function db_qr_fetch($sql) {

    return db_fetch($sql);
}

//--------------- Fetch First ---------------
function db_fetch_first($r) {
    
    if (is_object($r)) {
        $qr = $r;
    } else {
        $qr = db_query($r);
    }
    
    $data  = @mysqli_fetch_row($qr);
    return $data[0];
}

// ------------------- query + num --------------------
function db_qr_num($sql) {

    return db_num(db_query($sql));
}

//------------- query and return array ----------------
function db_fetch_all($r) {

    if (is_object($r)) {
        $qr = $r;
    } else {
        $qr = db_query($r);
    }

    while ($data = db_fetch($qr)) {
      
        $result[] = $data;
    }
    return (array) $result;
}


/* 
 * return last inserted ID
 * 
 */
function db_inserted_id(){
    global $cn;
    return mysqli_insert_id($cn);
    }
    
    
/** db sever info **/

function db_server_info(){
    global $cn;
    return @mysqli_get_server_info($cn);
}

function db_client_info(){
     global $cn;
    return @mysqli_get_client_info($cn);
}

