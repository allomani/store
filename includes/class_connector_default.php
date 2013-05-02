<?php

class connector_default {

    private static $instance = null;
    public  $allowed_login_groups = array('1');
    public  $disallowed_login_groups = array('2');
    public  $waiting_conf_login_groups = array('0');
    public  $time_type = "timestamp";
    public  $time_format = "d-m-Y h:s";
    public  $is_md5_password = true;
    public  $members_tables_replacement = array();
    public  $members_fields_replacement = array();

    private function __construct() {
        
    }

    public static function instance() {
        if (!self::$instance) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    public function get_groups_array() {
        $phrases = app::$phrases;
        $members_groups_array = array("0" => "$phrases[acc_type_not_activated]", "1" => "$phrases[acc_type_activated]", "2" => "$phrases[acc_type_closed]");
        return $members_groups_array;
    }

    public function get_date($date, $op) {

        if ($op == "member_reg_date") {
            return $date;
        } elseif ($op == "member_last_login") {
            return $date;
        } elseif ($op == "member_birth_date") {
            //   $tm = strtotime($date);
            //   return date("Y-m-d",$tm);
            return $date;
        } elseif ($op == "member_birth_array") {
            $birth_data = explode("-", $date);
            $new_arr['year'] = $birth_data[0];
            $new_arr['month'] = $birth_data[1];
            $new_arr['day'] = $birth_data[2];
            return $new_arr;
        }
    }

    public function password_update($userid, $pwd) {
       $pwdz = md5($pwd);
            db_query("update store_clients set password='" . $pwdz . "' where id='" . intval($userid) . "'");
       
    }

    public function register_post_action() {
        
    }

    public function password_verify($userid, $pwd, $md5pwd = "") {
        $qr = db_query("select password from store_clients where id='$userid'");
        if (db_num($qr)) {
            $data = db_fetch($qr);

            if ($data['password'] == $md5pwd ||  $data['password'] == md5($pwd)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

   public function password_forget(){
       
       $phrases = app::$phrases;
       $user_email = trim($_POST['user_email']);
       
        open_table("$phrases[forgot_pass]");
         
        
          if ($user_email) {
                $qr = db_query("select * from store_clients where email='" . db_escape($user_email) . "'");
                if (db_num($qr)) {
                    $data = db_fetch($qr);
                    $active_code = md5($data['email'] . time() . rand(1, 999) . rand(1, 999) . $data['id']);
                    $url = $scripturl . "/index.php?action=rest_pwd&code=$active_code";

                    db_query("delete from store_confirmations where type='rest_pwd' and cat='$data[id]'");
                    db_query("insert into store_confirmations (type,cat,code) values('rest_pwd','$data[id]','$active_code')");

                    $msg = get_template('pwd_rest_request_msg', array('{name}', '{url}', '{code}', '{siteurl}', '{sitename}'), array($data['username'], $url, $active_code, app::$siteurl, app::$sitename));

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
//---------------- PWD REST --------------

    public function password_reset() {

            $phrases = app::$phrases;
            $code = trim($_REQUEST['code']);
            
            $qr = db_query("select * from store_confirmations where code='" . db_escape($code) . "'");
            if (db_num($qr)) {
                $data = db_fetch($qr);
                $new_pwd = rand_string();
                $this->password_update($data['cat'], $new_pwd);
                $data_member = db_qr_fetch("select username,email from store_clients where id='$data[cat]'");


                $msg = get_template('pwd_rest_done_msg', array('{name}', '{password}', '{siteurl}', '{sitename}'), array($data_member['username'], $new_pwd, $siteurl, $sitename));


                send_email(app::$sitename, app::$mailing_email, $data_member['email'], $phrases['pwd_rest_done_msg_subject'], $msg, app::$settings['mailing_default_use_html'], app::$settings['mailing_default_encoding']);

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
