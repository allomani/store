<?php

class session {

    var $prefix = '';

    function __construct() {

        global $session_prefix, $session_cookie_name, $session_cookie_expire, $session_ip_check;

        $this->prefix = $session_prefix;

        $sid = get_cookie($session_cookie_name);

        if (!$sid || !$this->is_valid_session_id($sid)) {
            $sid = $this->gen_session_id();
        }

        $this->start($sid);
        
        if ($session_ip_check && $this->get('ip') != $_SERVER['REMOTE_ADDR']) {
            session_destroy();
            $sid = $this->gen_session_id();
            $this->start($sid);
            $this->set('ip', $_SERVER['REMOTE_ADDR']);
        }

        set_cookie($session_cookie_name, $sid, $session_cookie_expire);
    }

    function start($sid) {
        session_id($sid);
        session_start();
    }

    function set($name, $value) {
        $_SESSION[$this->prefix][$name] = $value;
        return true;
    }

    function get($name) {
        return $_SESSION[$this->prefix][$name];
    }

    function is_valid_session_id($session_id) {
        return !empty($session_id) && preg_match('/^[a-zA-Z0-9]{32}$/', $session_id);
    }

    function gen_session_id() {
        return md5($_SERVER['HTTP_HOST'] . $_SERVER['REMOTE_ADDR'] . time() . rand(1, 999));
    }

}

?>
