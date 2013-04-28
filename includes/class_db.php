<?php
class db {
    
    private static $instance;
 
     private function __construct() {
       
         }
     
     public static function instance() {
         
         $config = app::$config;
         
        if (!self::$instance) {
            $db_class = "db_{$config['db']['extension']}";
            if(!class_exists($db_class)){
                throw new Exception("Database Driver ".$config['db']['extension']." is not exist");
                }
            self::$instance = $db_class::instance($config);
            
        }
        return self::$instance;
    }
     
    }
?>
