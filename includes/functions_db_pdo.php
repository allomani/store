<?

$queries = 0;
$last_sql = '';


//------------ db escape -------------//
function db_escape($str, $specialchars = true) {
    global $cn;
    if ($specialchars) {
        $str = htmlspecialchars($str);
    }
    return $cn->quote($str);
}

//----------- Connect ----------
function db_connect($host, $user, $pass, $dbname, $dbcharset = "") {
    global $log_mysql_errors, $db_charset,$cn;

    if (!$dbcharset) {
        $dbcharset = $db_charset;
    }
           
    try{
    $cn = new PDO("mysql:host=$host;dbname=$dbname;charset=$dbcharset", $user, $pass);
  
  $cn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$cn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    }catch(PDOException  $e){ 
    //    if (mysql_errno() == 1040) {
            die("<center> Mysql Server is Busy  , Please Try again later  </center>");
     //   } else {

            if ($log_mysql_errors) {
                do_error_log($e->getMessage(), 'db');
            }

            die(" Database Connection Error");
     //   }
    }
    

    db_select($dbname, $dbcharset);
}

//--------- select db ------------
function db_select($db_name, $db_charset = "") {
    global $log_mysql_errors,$cn;
  //db_qr_fetch("use $db_name");
   
    
   // $db_select = @mysql_select_db($db_name);
    if (!$db_select) {

        if ($log_mysql_errors) {
            do_error_log(@mysql_errno() . " : " . @mysql_error(), 'db');
        }

    //    die("Database Name Error");
    }

    if ($db_charset) {
        db_qr_fetch("set names '$db_charset'");  
        
     //   mysql_set_charset($db_charset);
    }
}

//----------- query ------------------
function db_query($sql, $type = "") {

    global $show_mysql_errors, $log_mysql_errors, $queries, $last_sql,$cn;

    $queries++;
    // print $queries.$sql."<br>";
    //     print $queries . "." .$sql."<hr>";   

    $last_sql = $sql;

    if ($type == MEMBER_SQL) {
        members_remote_db_connect();
    }

    try{
    
      //  $stm = $cn->prepare($sql,array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => 1));
    $qr = $cn->query($sql);
 //$qr =  $stm->execute();
    
  //  if (mysql_errno()) {
     /*   if ($show_mysql_errors) {
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


        return false;  */
  //  } else {
        if ($type == MEMBER_SQL) {
            members_local_db_connect();
        }
        print_r($qr);
        print "<br>";
     //  $qr->fetchAll();
     //  $qr->closeCursor();
        return $qr;
    }catch(PDOException $e){
        print_r($cn->errorInfo());
        print "<br>";
        print "query : ".$e->getMessage();
          debug_print_backtrace();
          return false;
    }
    
 //   }
}

//---------------- fetch -------------------
function db_fetch($qr) {
    $qr->setFetchMode(PDO::FETCH_ASSOC);
    return $qr->fetch();
}

// ------------------------ num -----------------------
function db_num($qr) {
 
    return $qr->rowCount();
}

//------------------ Query + fetch ----------------------
function db_qr_fetch($sql, $type = "") {

   $qr =  db_query($sql, $type);
    return $qr->fetchAll();
}

//--------------- Fetch First ---------------
function db_qr_first($sql, $type = "") {
    $data = db_fetch(db_query($sql, $type));
    return $data[0];
}

// ------------------- query + num --------------------
function db_qr_num($sql, $type = "") {

  //  return db_num(db_query($sql, $type));
}

//------------- query and return array ----------------
function db_qr_array($sql, $type = "") {
  //  $qr = db_query($sql, $type);
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