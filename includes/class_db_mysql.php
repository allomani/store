<?php
class db_mysql {

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
        if ($specialchars) {
            $str = htmlspecialchars($str);
        }
        return mysql_real_escape_string($str);
    }

//----------- Connect ----------
    public function connect($host = "", $user = "", $pass = "", $dbname = "", $dbcharset = "") {

        if (!$host) {
            $host = $this->config['db']['host'];
        }
     
        if (!$user) {
            $user = $this->config['db']['username'];
        }
        if (!$pass) {
            $pass = $this->config['db']['password'];
        }
        if (!$dbname) {
            $dbname = $this->config['db']['name'];
        }

        if (!$dbcharset) {
            $dbcharset = $this->config['db']['charset'];
        }

        $this->link = @mysql_connect($host, $user, $pass);
        if (!$this->link) {
            if (mysql_errno() == 1040) {
                throw new Exception("Mysql Server is Busy  , Please Try again later");
            } else {

                if ($this->config['debug']['log_mysql_errors']) {
                    do_error_log(@mysql_errno() . " : " . @mysql_error(), 'db');
                }

                throw new Exception(@mysql_errno() . " : Database Connection Error");
            }
        }

        $this->select($dbname, $dbcharset);
    }

//--------- select db ------------
    public function select($db_name, $db_charset = "") {


        $db_select = @mysql_select_db($db_name);
        if (!$db_select) {

            if ($this->config['debug']['log_mysql_errors']) {
                do_error_log(@mysql_errno() . " : " . @mysql_error(), 'db');
            }
            throw new Exception("Database Name Error");
        }

        if ($db_charset) {
            mysql_set_charset($db_charset);
        }
    }

//----------- query ------------------
    public function query($sql) {

     //   global $show_mysql_errors, $log_mysql_errors, $queries, $last_sql;

        $queries++;
        // print $queries.$sql."<br>";
        //     print $queries . "." .$sql."<hr>";   




        $last_sql = $sql;

        $qr = @mysql_query($sql);

        if (mysql_errno()) {
            if ($this->config['debug']['show_mysql_errors']) {
                print "
            <div style=\"align:left;direction:ltr;\">
            <span align=left><b>MySQL Error: </b></span> " . mysql_error() . "
                <br><br>";

                debug_print_backtrace();
                print "</div>";
            }
            if ($config['debug']['log_mysql_errors']) {
                do_error_log("$err \r\nSQL :  $last_sql", 'db');
            }


            return false;
        } else {


            return $qr;
        }
    }

//---------------- fetch -------------------
    public function fetch($r) {

        if (is_resource($r)) {
            $qr = $r;
        } else {
            $qr = $this->query($r);
        }

        return @mysql_fetch_assoc($qr);
    }

// ------------------------ num -----------------------
    public function num($qr) {
        return @mysql_num_rows($qr);
    }

//------------------ Query + fetch ----------------------
    public function qr_fetch($sql) {
        return $this->fetch($sql);
    }

 //--------------- Fetch row ---------------
    public function fetch_row($r) {

        if (is_resource($r)) {
            $qr = $r;
        } else {
            $qr = $this->query($r);
        }

        $data = @mysql_fetch_row($qr);
        return $data;
    }   
    
 //------- data seek  ------
public function data_seek($r,$row_number=0){
   return  mysql_data_seek($r,$row_number);
    }
    
//--------------- Fetch First ---------------
    public function fetch_first($r) {

        if (is_resource($r)) {
            $qr = $r;
        } else {
            $qr = $this->query($r);
        }

        $data = @mysql_fetch_row($qr);
        return $data[0];
    }

// ------------------- query + num --------------------
    public function qr_num($sql) {

        return $this->num($this->query($sql));
    }

//------------- query and return array ----------------
    public function fetch_all($r) {

        if (is_resource($r)) {
            $qr = $r;
        } else {
            $qr = $this->query($r);
        }

        while ($data = $this->fetch($qr)) {
            $result[] = $data;
        }
        return (array) $result;
    }

    /*
     * return last inserted ID
     * 
     */

    public function inserted_id() {
        return mysql_insert_id();
    }

    public function server_info() {
        return @mysql_get_server_info();
    }

    public function client_info() {
        return @mysql_get_client_info();
    }

}

?>
