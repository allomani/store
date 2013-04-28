<?php

class app {

    static $phrases = array();
    static $settings = array();
    static $links = array();
    static $config = array();

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
