<?

$queries = 0;
$last_sql = '';

//------------ db escape -------------//
function db_escape($str, $specialchars = true) {
    if ($specialchars) {
        $str = htmlspecialchars($str);
    }
    return mysql_real_escape_string($str);
}

//----------- Connect ----------
function db_connect($host, $user, $pass, $dbname, $dbcharset = "") {
    global $log_mysql_errors, $db_charset;

    if (!$dbcharset) {
        $dbcharset = $db_charset;
    }

    $cn = @mysql_connect($host, $user, $pass);
    if (!$cn) {
        if (mysql_errno() == 1040) {
            die("<center> Mysql Server is Busy  , Please Try again later  </center>");
        } else {

            if ($log_mysql_errors) {
                do_error_log(@mysql_errno() . " : " . @mysql_error(), 'db');
            }

            die(@mysql_errno() . " : Database Connection Error");
        }
    }

    db_select($dbname, $dbcharset);
}

//--------- select db ------------
function db_select($db_name, $db_charset = "") {
    global $log_mysql_errors;

    $db_select = @mysql_select_db($db_name);
    if (!$db_select) {

        if ($log_mysql_errors) {
            do_error_log(@mysql_errno() . " : " . @mysql_error(), 'db');
        }

        die("Database Name Error");
    }

    if ($db_charset) {
// db_query("set names '$db_charset'");  
        mysql_set_charset($db_charset);
    }
}

//----------- query ------------------
function db_query($sql, $type = "") {

    global $show_mysql_errors, $log_mysql_errors, $queries, $last_sql;

    $queries++;
    // print $queries.$sql."<br>";
    //     print $queries . "." .$sql."<hr>";   

    $last_sql = $sql;

    if ($type == MEMBER_SQL) {
        members_remote_db_connect();
    }

    $qr = @mysql_query($sql);

    if (mysql_errno()) {
        if ($show_mysql_errors) {
            print "
            <div style=\"align:left;direction:ltr;\">
            <span align=left><b>MySQL Error: </b></span> " . mysql_error() . "
                <br><br>";

            debug_print_backtrace();
            print "</div>";
        }
        if ($log_mysql_errors) {
            do_error_log("$err \r\nSQL :  $last_sql", 'db');
        }


        return false;
    } else {
        if ($type == MEMBER_SQL) {
            members_local_db_connect();
        }

        return $qr;
    }
}

//---------------- fetch -------------------
function db_fetch($qr) {
    //  global $show_mysql_errors,$log_mysql_errors,$last_sql ;

    /*     $fetch = @mysql_fetch_array($qr);

      $err =  mysql_error() ;

      if($err){

      if($show_mysql_errors){
      print  "<p align=left><b> MySQL Error: </b> $err </p>";
      }

      if($log_mysql_errors){
      do_error_log("$err \r\nSQL :  $last_sql",'db');
      }

      return false;
      }else{
      return $fetch;
      } */
    return @mysql_fetch_assoc($qr);
}

// ------------------------ num -----------------------
function db_num($qr) {
    //  global  $show_mysql_errors,$log_mysql_errors,$last_sql ;

    /*
      $num =  @mysql_num_rows($qr);
      $err =  mysql_error() ;

      if($err){

      if($show_mysql_errors){
      print  "<p align=left><b> MySQL Error: </b> $err </p>";
      }

      if($log_mysql_errors){
      do_error_log("$err \r\nSQL :  $last_sql",'db');
      }


      return false;
      }else{
      return $num;
      } */

    return @mysql_num_rows($qr);
}

//------------------ Query + fetch ----------------------
function db_qr_fetch($sql, $type = "") {

    return db_fetch(db_query($sql, $type));
}

//--------------- Fetch First ---------------
function db_qr_first($sql, $type = "") {
    $data = db_fetch(db_query($sql, $type));
    return $data[0];
}

// ------------------- query + num --------------------
function db_qr_num($sql, $type = "") {

    return db_num(db_query($sql, $type));
}

//------------- query and return array ----------------
function db_qr_array($sql, $type = "") {
    $qr = db_query($sql, $type);
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
    return mysql_insert_id();
    }