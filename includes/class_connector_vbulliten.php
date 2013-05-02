<?php

class connector_vbulletin {

    private static $instance = null;
    public $members_tables_replacement = array();
    public $members_fields_replacement = array();
    public $time_type = "timestamp";
    public $time_format = "d-m-Y";
    public $is_md5_password = true;
    public $allowed_login_groups = array('2', '6', '5');
    public $disallowed_login_groups = array('1');
    public $waiting_conf_login_groups = array('3');
    public $search_fields = array();
    public $required_database_fields_names = array('country', 'active_code');
    public $required_database_fields_types = array('text', 'text');

    private function __construct() {


        if ($members_connector['custom_members_table']) {
            $members_connector['members_table'] = $members_connector['custom_members_table'];
        } else {
            $members_connector['members_table'] = "user";
        }



        $this->members_tables_replacement = array(
            'store_clients' => app::$config['connector']['members_table']
        );

        $this->members_fields_replacement = array(
            'id' => 'userid',
            'password' => 'password',
            'last_login' => 'lastvisit',
            'birth' => 'birthday',
            'date' => 'joindate',
            'usr_group' => 'usergroupid'
        );
    }

    public static function instance() {
        if (!self::$instance) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    public function get_groups_array() {

        $members_groups_array = array(
            "6" => "Administrators",
            "8" => "Banned Users",
            "7" => "Moderators",
            "2" => "Registered Users",
            "5" => "Super Moderators",
            "1" => "Unregistered / Not Logged In",
            "3" => "Users Awaiting Email Confirmation",
        );
        return $members_groups_array;
    }

    function fetch_user_salt($length = SALT_LENGTH) {
        $salt = '';

        for ($i = 0; $i < $length; $i++) {
            $salt .= chr(rand(32, 126));
        }

        return $salt;
    }

    public function password_update($userid, $pwd) {

        $salt = fetch_user_salt(3);
        $pwdz = md5(md5($pwd) . $salt);
        members::db_query("update user set password='" . $pwdz . "',salt='$salt' where userid='" . intval($userid) . "'");
    }

    public function register_post_action() {
        global $member_id;
        members::db_query("insert into userfield (userid) values('$member_id')");
        members::db_query("insert into usertextfield (userid) values('$member_id')");
        members::db_query("update user set options='3159' where userid='$member_id'");
    }

    public function get_date($date, $op) {

        if ($op == "member_reg_date") {
            return $date;
        } elseif ($op == "member_birth_date") {
            $tm = strtotime($date);
            return date("m-d-Y", $tm);
        } elseif ($op == "member_last_login") {
            return $date;
        } elseif ($op == "member_birth_array") {
            $birth_data = explode("-", $date);
            $new_arr['year'] = $birth_data[2];
            $new_arr['month'] = $birth_data[0];
            $new_arr['day'] = $birth_data[1];
            return $new_arr;
        }
    }

    public function password_verify($userid, $pwd, $md5pwd = "") {

        $qr = members::db_query("select ::password,::salt from {{store_clients}} where ::id=':id'", array('id' => $userid));

        if (db_num($qr)) {
            $data = db_fetch($qr);

            if ($data['password'] == md5($md5pwd . $data['salt']) || $data['password'] == md5(md5($pwd) . $data['salt'])) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

//---------------- PWD REST --------------

    public function password_forget() {

        $phrases = app::$phrases;
        $user_email = trim($_POST['user_email']);


        open_table("$phrases[forgot_pass]");
        if ($user_email) {
            $qr = members::db_query("select * from {{store_clients}} where ::email=':email'", array('email' => db_escape($user_email)));
            if (db_num($qr)) {
                $data = members::db_fetch($qr);
                $active_code = md5($data['email'] . time() . rand(1, 999) . rand(1, 999) . $data['id']);
                $url = $scripturl . "/index.php?action=rest_pwd&code=$active_code";

                db_query("delete from store_confirmations where type='rest_pwd' and cat='$data[id]'");
                db_query("insert into store_confirmations (type,cat,code) values('rest_pwd','$data[userid]','$active_code')");

                $msg = get_template('pwd_rest_request_msg', array('{name}', '{url}', '{code}', '{siteurl}', '{sitename}'), array($data['username'], $url, $active_code, $siteurl, $sitename));


                send_email(app::$sitename, app::$mailing_email, $user_email, $phrases['pwd_rest_request_msg_subject'], $msg, app::$settings['mailing_default_use_html'], app::$settings['mailing_default_encoding']);

                print "<center>$phrases[rest_pwd_request_msg_sent]</center>";
            } else {
                print "<center>  $phrases[email_not_exists]</center>";
            }
        } else {

            print "<form action=index.php method=post>
         <input type=hidden name=action value=forget_pass>
         <center><table ><tr><td width=100>  $phrases[email] : </td>
         <td><input type=text name=user_email size=20></td><td><input type=submit value='$phrases[continue]'></tr></table></form></center>";
        }
        close_table();
    }

    function password_reset() {

        $phrases = app::$phrases;
        $code = trim($_REQUEST['code']);



        $qr = db_query("select * from store_confirmations where code='" . db_escape($code) . "'");
        if (db_num($qr)) {
            $data = db_fetch($qr);
            $new_pwd = rand_string();
            connector_member_pwd($data['cat'], $new_pwd, "update");
            $data_member = members::db_qr_fetch("select username from user where userid='$data[cat]'");


            $msg = get_template('pwd_rest_done_msg', array('{name}', '{password}', '{siteurl}', '{sitename}'), array($data_member['username'], $new_pwd, $siteurl, $sitename));


            send_email(app::$sitename, app::$mailing_email, $user_email, $phrases['pwd_rest_done_msg_subject'], $msg, app::$settings['mailing_default_use_html'], app::$settings['mailing_default_encoding']);

            db_query("delete from store_confirmations where type='rest_pwd' and code='" . db_escape($code) . "'");

            open_table();
            print "<center> $phrases[pwd_rest_done]</center>";
            close_table();
        } else {
            open_table();
            print "<center> $phrases[err_wrong_url]</center>";
            close_table();
        }
    }

}
?>
