<?php

class session {

    private  $prefix = '';
    private static $instance;
    
    private  function __construct($config) {
    
       $this->prefix = $config['prefix'];

        $sid = cookie::instance()->get($config['cookie_name']);

        if (!$sid || !$this->is_valid_session_id($sid)) {
            $sid = $this->gen_session_id();
        }

        $this->start($sid);
        
        if ($config['ip_check'] && $this->get('ip') != get_ip()) {
            session_destroy();
            $sid = $this->gen_session_id();
            $this->start($sid);
            $this->set('ip', get_ip());
        }

        cookie::instance()->set($config['cookie_name'], $sid, $config['cookie_expire']);
    }
    
      public static function instance($config=array()) {
        if (!self::$instance) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }
    
    private function start($sid) {
        session_id($sid);
        session_start();
    }

    public function set($name, $value) {
        $_SESSION[$this->prefix][$name] = $value;
        return true;
    }

    public function get($name) {
        return $_SESSION[$this->prefix][$name];
    }

    private function is_valid_session_id($session_id) {
        return !empty($session_id) && preg_match('/^[a-zA-Z0-9]{32}$/', $session_id);
    }

    private function gen_session_id() {
        return md5($_SERVER['HTTP_HOST'] . get_ip() . time() . rand(1, 999));
    }

}

?>
