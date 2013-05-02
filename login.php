<?

require("global.php");
if (!$re_link) {
    $re_link = iif($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_REFERER'], "index.php");
}

$re_link = htmlspecialchars($re_link);

if ($action == "login") {

    if (trim($username) == "" || trim($password) == "") {
        site_header();
        open_table($phrases['login']);
        print "<center>$phrases[plz_enter_username_and_pwd]</center>";
        close_table();
        site_footer();
        die();
    }


    $qr = members::db_query("select * from {{store_clients}} where ::username=':username'", array('username' => db_escape($username, false)));



    if (db_num($qr)) {

        $data = members::db_fetch($qr);

        if (members::password_verify($data['id'], $password, $md5pwd)) {

            if (in_array($data['usr_group'], members::$allowed_login_groups)) {


                $session->set('member_data_id', $data['id']);
                $session->set('member_data_password', $data['password']);
                js_redirect($re_link,true);
                // ------------- Closed Account -----------------       
            } elseif (in_array($data['usr_group'], members::$disallowed_login_groups)) {
                site_header();
                open_table($phrases['login']);
                print "<center> $phrases[this_account_closed_cant_login] </center>";
                close_table();
                site_footer();

                //------------- Not Activated Member --------------------                
            } elseif (in_array($data['usr_group'], members::$waiting_conf_login_groups)) {

                site_header();
                open_table($phrases['login']);
                print "<center>  $phrases[this_account_not_activated] </center>";
                close_table();

                //------ resend activation msg form ----------
                open_table($phrases['resend_activation_msg']);
                print "<form action=index.php method=post>
                    <input type=hidden name=action value='resend_active_msg'>
                    <center><table><tr><td>
                    $phrases[your_email] : </td><td>
                    <input type=text size=30 name=email dir=ltr>
                    </td><td><input type=submit value=' $phrases[send] '>
                    </td></tr></table></center></form>";
                close_table();
                site_footer();

                //-------- invalid group id -----------------
            } else {

                site_header();
                open_table($phrases['login']);
                print "<center> Invalid Member Group </center>";
                close_table();
                site_footer();
            }


            //---------- if not valid password ---------------------           
        } else {
            site_header();
            open_table($phrases['login']);
            print "<center> $phrases[invalid_pwd] </center>";
            close_table();
            site_footer();
        }

        //--------- if not valid username ---------------                 
    } else {
        site_header();
        open_table($phrases['login']);
        print "<center>  $phrases[invalid_username] </center>";
        close_table();
        site_footer();
    }


    //--------------- Logout ---------------
} elseif ($action == "logout") {

    $session->set('member_data_id', "");
    $session->set('member_data_password', "");
    js_redirect($re_link,true);
  

    //---------- Login Form Redirect -----------------
} else {

    print "<form action=index.php method=post name=lg_form>
        <input type=hidden name='re_link' value=\"$re_link\">
        <input type=hidden name='action' value='login'>
        </form>
        <script>
        lg_form.submit();
        </script>";
}