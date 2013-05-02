<?php

class app {

    static $phrases = array();
    static $settings = array();
    static $links = array();
    static $config = array();
    static $sitename = null;
    static $section_name = null;
    static $scripturl = null;
    static $mailing_email = null;
    static $siteurl = null;
    static $script_path = null;
    static $upload_types = null;

    public static function init() {

        try {
            db::instance()->connect();
        } catch (Exception $e) {
            die($e->getMessage());
        }


        self::load_phrases();
        self::load_settings();
        self::load_links();


        // timezone
        if (self::$settings['timezone']) {
            date_default_timezone_set(self::$settings['timezone']);
        }


        self::$sitename = self::$settings['sitename'];
        self::$section_name = self::$settings['section_name'];
        self::$siteurl = "http://$_SERVER[HTTP_HOST]";
        self::$script_path = trim(str_replace(rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $_SERVER['DOCUMENT_ROOT']), "/"), "", CWD), "/");
        self::$scripturl = self::$siteurl . iif(self::$script_path, "/" . self::$script_path, "");
        self::$upload_types = explode(',', str_replace(" ", "", strtolower(self::$settings['uploader_types'])));
        self::$mailing_email = str_replace("{domain_name}", $_SERVER['HTTP_HOST'], self::$settings['mailing_email']);
        
    }

    public static function load_phrases() {
        self::$phrases = array();
        $data_arr = db::instance()->fetch_all("select * from store_phrases");
        foreach ($data_arr as $data) {
            self::$phrases["$data[name]"] = $data['value'];
        }
    }

    public static function load_settings() {
        self::$settings = array();
        $data_arr = db::instance()->fetch_all("select * from store_settings");
        foreach ($data_arr as $data) {
            self::$settings["$data[name]"] = $data['value'];
        }
    }

    public static function load_links() {
        self::$links = array();
        $data_arr = db::instance()->fetch_all("select * from store_links");
        foreach ($data_arr as $data) {
            self::$links["$data[name]"] = $data['value'];
        }
    }

    public static function set_config($config = array()) {
        self::$config = $config;
    }

}

?>
