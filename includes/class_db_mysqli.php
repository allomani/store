<?

class db_mysqli {

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
        return mysqli_real_escape_string($this->link, $str);
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

        $this->link = @mysqli_connect($host, $user, $pass);
        if (!$this->link) {
            if (mysqli_errno($this->link) == 1040) {
                throw new Exception("Mysql Server is Busy  , Please Try again later");
            } else {

                if ($this->config['debug']['log_mysqli_errors']) {
                    do_error_log(@mysqli_errno($this->link) . " : " . @mysqli_error($this->link), 'db');
                }

                throw new Exception(@mysqli_errno($this->link) . " : Database Connection Error");
            }
        }

        $this->select($dbname, $dbcharset);
    }

//--------- select db ------------
    public function select($db_name, $db_charset = "") {

        $db_select = @mysqli_select_db($this->link, $db_name);
        if (!$db_select) {

            if ($this->config['debug']['log_mysqli_errors']) {
                do_error_log(@mysqli_errno($this->link) . " : " . @mysqli_error($this->link), 'db');
            }

            throw new Exception("Database Name Error");
        }

        if ($db_charset) {
// db_query("set names '$db_charset'");  
            mysqli_set_charset($this->link, $db_charset);
        }
    }

//----------- query ------------------
    public function query($sql) {


        $qr = @mysqli_query($this->link, $sql);

        if (mysqli_errno($this->link)) {
            if ($this->config['debug']['show_mysqli_errors']) {
                print "
            <div style=\"align:left;direction:ltr;\">
            <span align=left><b>MySQL Error: </b></span> " . mysqli_error($this->link) . "
                <br><br>";

                debug_print_backtrace();
                print "</div>";
            }
            if ($this->config['debug']['log_mysqli_errors']) {
                do_error_log("$err \r\nSQL :  $last_sql", 'db');
            }


            return false;
        } else {


            return $qr;
        }
    }

//---------------- fetch -------------------
    public function fetch($r) {

        if (is_object($r)) {
            $qr = $r;
        } else {
            $qr = $this->query($r);
        }

        return @mysqli_fetch_assoc($qr);
    }

// ------------------------ num -----------------------
    public function num($qr) {


        return @mysqli_num_rows($qr);
    }

//------------------ Query + fetch ----------------------
    public function qr_fetch($sql) {

        return $this->fetch($sql);
    }

//--------------- Fetch First ---------------
    public function fetch_first($r) {

        if (is_object($r)) {
            $qr = $r;
        } else {
            $qr = $this->query($r);
        }

        $data = @mysqli_fetch_row($qr);
        return $data[0];
    }

// ------------------- query + num --------------------
    public function qr_num($sql) {

        return $this->num($this->query($sql));
    }

//------------- query and return array ----------------
    public function fetch_all($r) {

        if (is_object($r)) {
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

        return mysqli_insert_id($this->link);
    }

    /** db sever info * */
    public function server_info() {

        return @mysqli_get_server_info($this->link);
    }

    public function client_info() {

        return @mysqli_get_client_info($this->link);
    }

}