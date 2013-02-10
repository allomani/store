<?php

class cookie {

    private static $instance;
    private static $path;
    private static $domain;
    private static $prefix;
    private static $timeout;

    private function __construct($config) {
        $this->path = $config['path'];
        $this->domain = $config['domain'];
        $this->prefix = $config['prefix'];
        $this->timeout = $config['timeout'];
    }

 

    public static function instance($config = array()) {
        if (!self::$instance) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }
    
    
      public function set($name, $value = "", $expire = null) {

        $name = $this->prefix . $name;

        if (!isset($expire)) {
            $k_timeout = time() + (60 * 60 * 24 * intval($this->timemout));
        } else {
            $k_timeout = time() + (60 * 60 * 24 * intval($expire));
        }

        setcookie($name, $value, $k_timeout, $this->path, $this->domain);
    }

    public function get($name) {

        $name = $this->prefix . $name;
        return $_COOKIE[$name];
    }
    
    

}
?>