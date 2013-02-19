<?

class db_pdo_mysql {

    private static $instance;
    private $link;
    private $config;

    private function __construct($config) {
        $this->config = $config;
    }

    public static function instance($config = array()) {
        if (!self::$instance) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

//------------ db escape -------------//
public function escape($str, $specialchars = true) {
    global $cn;
    if ($specialchars) {
        $str = htmlspecialchars($str);
    }
    return $cn->quote($str);
}

//----------- Connect ----------
public function connect($host, $user, $pass, $dbname, $dbcharset = "") {
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
public function select($db_name, $db_charset = "") {
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
public function query($sql, $type = "") {

    global $show_mysql_errors, $log_mysql_errors, $queries, $last_sql,$cn;

    $queries++;
    // print $queries.$sql."<br>";
    //     print $queries . "." .$sql."<hr>";   

    $last_sql = $sql;

 

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
       

   //     print "<br>";
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
public function fetch($qr) {
    $qr->setFetchMode(PDO::FETCH_ASSOC);
    return $qr->fetch();
}

// ------------------------ num -----------------------
public function num($qr) {
 
    return $qr->rowCount();
}

//------------------ Query + fetch ----------------------
public function qr_fetch($sql, $type = "") {

   $qr =  db_query($sql, $type);
    return $qr->fetchAll();
}

//--------------- Fetch First ---------------
public function qr_first($sql, $type = "") {
    $data = db_fetch(db_query($sql, $type));
    return $data[0];
}

// ------------------- query + num --------------------
public function qr_num($sql, $type = "") {

  //  return db_num(db_query($sql, $type));
}

//------------- query and return array ----------------
public function qr_array($sql, $type = "") {
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
public function inserted_id(){
    return mysql_insert_id();
    }
    }