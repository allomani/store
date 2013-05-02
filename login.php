<?

require("global.php");
if (!$re_link) {
    $re_link = iif($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_REFERER'], "index.php");
}

if (stristr($re_link, "login.php")) {
    $re_link = "index.php";
}

$re_link = htmlspecialchars($re_link);

if ($action == "login") {

    $username = trim($username);

    if ($username == "" || trim($password) == "") {
        $session->set('login_error', $phrases[plz_enter_username_and_pwd]);
        js_redirect('login.php', true);
    }


    $qr = members::db_query("select * from {{store_clients}} where ::username=':username'", array('username' => db_escape($username, false)));



    if (!db_num($qr)) {
        $session->set('login_error', $phrases[invalid_username]);
        js_redirect('login.php', true);
    }

    $data = members::db_fetch($qr);

    if (!members::password_verify($data['id'], $password, $md5pwd)) {
        $session->set('login_error', $phrases[invalid_pwd]);
        js_redirect('login.php', true);
    }
    if (in_array($data['usr_group'], members::$allowed_login_groups)) {


        $session->set('member_data_id', $data['id']);
        $session->set('member_data_password', $data['password']);
        $session->set('login_error', '');
        js_redirect($re_link, true);
        // ------------- Closed Account -----------------       
    } elseif (in_array($data['usr_group'], members::$disallowed_login_groups)) {
        $session->set('login_error', $phrases[this_account_closed_cant_login]);
        js_redirect('login.php', true);

        //------------- Not Activated Member --------------------                
    } elseif (in_array($data['usr_group'], members::$waiting_conf_login_groups)) {
        $session->set('login_error', $phrases[this_account_not_activated]);
        js_redirect('login.php', true);

        //-------- invalid group id -----------------
    } else {

        $session->set('login_error', 'Invalid Member Group');
        js_redirect('login.php', true);
    }



    //--------------- Logout ---------------
} elseif ($action == "logout") {

    $session->set('member_data_id', "");
    $session->set('member_data_password', "");
    js_redirect($re_link, true);


    //---------- Login Form Redirect -----------------
} else {

    if (check_member_login()) {
        js_redirect($re_link, true);
    }

    require(CWD . "/includes/framework_start.php");



    $login_error = $session->get('login_error');
    if ($login_error) {
        show_alert($login_error, 'error');
    }
    open_table($phrases['login']);
    run_template('login_form');
    close_table();

    require(CWD . "/includes/framework_end.php");
}