<?php

class members {

    public static $connector = null;
    public static $same_connection = false;
    public static $same_db = false;
    public static $groups_array = array();
    public static $member_data = array();
    public static $allowed_login_groups = array();
    public static $disallowed_login_groups = array();
    public static $waiting_conf_login_groups = array();

    public static function init() {
        $config = app::$config;

        if ($config['connector']['enable']) {
            $class_name = 'connector_' . $config['connector']['type'];
        } else {
            $class_name = 'connector_default';
        }

        if (!class_exists($class_name)) {
            throw new Exception("Connector : $class_name is not exist");
        }

        // check if same connection
        if ($config['db']['host'] == $config['connector']['db_host'] && $config['connector']['db_username'] == $config['db']['username']) {
            self::$same_connection = true;
        } else {
            self::$same_connection = false;
        }
// check if same db
        if ($config['db']['name'] == $config['connector']['db_name'] && self::$same_connection) {
            self::$same_db = true;
        } else {
            self::$same_db = false;
        }


        self::$connector = $class_name::instance();

        self::$groups_array = self::$connector->get_groups_array();

        self::$allowed_login_groups = self::$connector->allowed_login_groups;
        self::$disallowed_login_groups = self::$connector->disallowed_login_groups;
        self::$waiting_conf_login_groups = self::$connector->waiting_conf_login_groups;
    }

    private static function sql_parse($sql, $data = array()) {

        $members_tables_replacement = self::$connector->members_tables_replacement;
        $members_fields_replacement = self::$connector->members_fields_replacement;


        /* tables */
        preg_match_all("/{{([a-zA-z0-9_-]+)}}/", $sql, $matchs);
        $matchs[1] = (array) $matchs[1];
        foreach ($matchs[1] as $table) {
            $sql = str_replace("{{" . $table . "}}", iif($members_tables_replacement[$table], $members_tables_replacement[$table], $table), $sql);
        }

        /* fields */
        preg_match_all("/::([a-zA-z0-9_-]+)/", $sql, $matchs_f);
        $matchs_f[1] = (array) $matchs_f[1];
        foreach ($matchs_f[1] as $field) {
            $sql = str_replace("::" . $field, iif($members_fields_replacement[$field], $members_fields_replacement[$field], $field), $sql);
        }

        /* data */

        foreach ($data as $k => $v) {
            $sql = str_replace(":" . $k, $v, $sql);
        }

        return $sql;
    }

    public static function db_query($sql, $data = array()) {
        self::remote_db_connect();
        $qr = db_query(self::sql_parse($sql, $data));
        self::local_db_connect();
        return $qr;
    }

    public static function db_qr_fetch($sql, $data = array()) {

        return self::db_fetch(self::db_query($sql, $data));
    }

    public static function db_fetch($qr) {
        $members_fields_replacement = self::$connector->members_fields_replacement;
        $members_fields_replacement_x = array_flip($members_fields_replacement);

        $data = db_fetch($qr);
        foreach ($data as $field => $value) {
            $new_field = iif($members_fields_replacement_x[$field], $members_fields_replacement_x[$field], $field);
            $new_data[$new_field] = $value;
        }
        return $new_data;
    }

    public static function time_replace($time) {

        if (self::$connector->time_type == "timestamp") {
            return date(self::$connector->time_format, $time);
        } else {
            return $time;
        }
    }

//--------------- remote db connect ------------------
    public static function remote_db_connect() {
        $config = app::$config;

        if ($config['connector']['enable'] && !self::same_db) {


//----- connect -----
            if (self::same_connection) {
                db_select($config['connector']['db_name'], $config['connector']['db_charset']);
            } else {
                db_connect($config['connector']['db_host'], $config['connector']['db_username'], $config['connector']['db_password'], $config['connector']['db_name'], $config['connector']['db_charset']);
            }
//-------
        }
    }

    public static function local_db_connect() {
        $config = app::$config;


        if ($config['connector']['enable'] && !self::same_db) {

//----- connect -----
            if (self::same_connection) {
                db_select($config['db']['name'], $config['db']['charset']);
            } else {
                db_connect($config['db']['host'], $config['db']['username'], $config['db']['password'], $config['db']['name'], $config['db']['charset']);
            }
//-------
        }
    }

//----------------------------- Members -----------------


    public static function check_login() {


        $session = session::instance();


        self::$member_data['id'] = $session->get('member_data_id');
        self::$member_data['password'] = $session->get('member_data_password');

        if (self::$member_data['id']) {

            $qr = self::db_query("select * from {{store_clients}} where ::id=':id'", array('id' => self::$member_data['id']));

            if (db_num($qr)) {
                $data = self::db_fetch($qr);
                
                if ($data['password'] == self::$member_data['password']) {

                    if (in_array($data['usr_group'], self::$allowed_login_groups)) {

                        self::db_query("update {{store_clients}} set ::last_login=':last_login',::ip_address=':ip_address' where ::id=':id'", array(
                            'id' => self::$member_data['id'],
                            'last_login' => self::get_date(time(), 'member_last_login'),
                            'ip_address' => get_ip()
                                )
                        );
                        self::$member_data['username'] = $data['username'];
                        self::$member_data['email'] = $data['email'];
                        self::$member_data['usr_group'] = $data['usr_group'];
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {

            return false;
        }
    }

    public static function get_date($date, $op) {
        return self::$connector->get_date($date, $op);
    }

    public static function password_update($userid, $pwd) {
        return self::$connector->password_update($userid, $pwd);
    }

    public static function register_post_action() {
        return self::$connector->register_post_action();
    }

    public static function password_verify($userid, $pwd, $md5pwd = "") {
        return self::$connector->password_verify($userid, $pwd, $md5pwd);
    }

    public static function password_forget() {
        return self::$connector->password_forget();
    }

    public static function password_reset() {
        return self::$connector->password_reset();
    }

//----------- members custom fields ----------

    public static function get_member_field($name, $data, $value = "", $search = false) {
        global $phrases;

        $cntx = "";

//----------- text ---------------
        if ($data['type'] == "text") {

            $cntx .= "<input type=text name=\"$name\" value=\"" . iif($search, "", iif($value, $value, $data['value'])) . "\" $data[style]>";

//---------- text area -------------
        } elseif ($data['type'] == "textarea") {

            $cntx .= "<textarea name=\"$name\" $data[style]>" . iif($search, "", iif($value, $value, $data['value'])) . "</textarea>";

//-------- select -----------------
        } elseif ($data['type'] == "select") {

            $cntx .= "<select name=\"$name\" $data[style]>";
            if ($search || !$data['required']) {
                $cntx .= "<option value=\"\">$phrases[without_selection]</option>";
            }

            $vx = explode("\n", $data['value']);
            foreach ($vx as $value_f) {
                $value_f = trim($value_f);
                $cntx .= "<option value=\"$value_f\"" . iif($value == $value_f, " selected") . ">$value_f</option>";
            }
            $cntx .= "</select>";

//--------- radio ------------
        } elseif ($data['type'] == "radio") {

            if ($search || !$data['required']) {
                $cntx .= "<input type=\"radio\" name=\"$name\" value=\"\" $data[style] checked>$phrases[without_selection]<br>";
            }


            $vx = explode("\n", $data['value']);
            foreach ($vx as $value_f) {
                $cntx .= "<input type=\"radio\" name=\"$name\" value=\"$value_f\" $data[style] " . iif($value == $value_f, " checked") . "> $value_f<br>";
            }

//-------- checkbox -------------
        } elseif ($data['type'] == "checkbox") {


            $vx = explode("\n", $data['value']);
            foreach ($vx as $value_f) {

                $cntx .= "<input type=\"checkbox\" name=\"$name\" value=\"$value_f\" " . iif($value == $value_f, "checked") . ">$value_f<br>";
            }
        }
        return $cntx;
    }

//--------------- Account Activation Email --------------------
    public static function snd_email_activation_msg($id) {
       $phrases = app::$phrases;
       
        $qr = members_db_query("select * from {{store_clients}} where ::id=':id'", array('id' => $id));
        if (db_num($qr)) {
            $data = members_db_fetch($qr);

            $active_code = md5(rand(0, 999) . time() . $data['email'] . rand() . $id);

            db_query("delete from store_confirmations where type='validate_email' and cat='" . $data['id'] . "'");
            db_query("insert into store_confirmations (type,cat,code) values('validate_email','" . $data['id'] . "','$active_code')");

            $url = $scripturl . "/index.php?action=activate_email&code=$active_code";

            $msg = get_template('email_activation_msg', array('{name}', '{url}', '{code}', '{siteurl}', '{sitename}'), array($data['username'], $url, $active_code, app::$siteurl, app::$sitename));

            send_email(app::$sitename, app::$mailing_email, $data['email'], $phrases['email_activation_msg_subject'], $msg, app::$settings['mailing_default_use_html'], app::$settings['mailing_default_encoding']);
        }
    }

//--------------- Change Email Confirmation --------------------
    public static function snd_email_chng_conf($username, $email, $active_code) {
       $phrases = app::$phrases;

        $active_link = $scripturl . "/index.php?action=confirmations&op=member_email_change&code=$active_code";


        $msg = get_template("email_change_confirmation_msg", array('{username}', '{active_link}', '{sitename}', '{siteurl}'), array($username, $active_link, app::$sitename, app::$siteurl));


        $mailResult = send_email(app::$sitename, app::$mailing_email, $email, $phrases['chng_email_msg_subject'], $msg, app::$settings['mailing_default_use_html'], app::$settings['mailing_default_encoding']);
    }



}

?>
