<?

require("global.php");

define(THIS_PAGE, "index");
require(CWD . "/includes/framework_start.php");
//----------------------------------------------------

//---------------------------- Pages -------------------------------------
if ($action == "pages") {
    $qr = db_query("select * from store_pages where active=1 and id='" . intval($id) . "'");

    compile_hook('pages_start');

    if (db_num($qr)) {
        $data = db_fetch($qr);
        compile_hook('pages_before_data_table');
        open_table("$data[title]");
        compile_hook('pages_before_data_content');
        run_php($data['content']);
        compile_hook('pages_after_data_content');
        close_table();
        compile_hook('pages_after_data_table');
    } else {
        open_table();
        print "<center> $phrases[err_no_page] </center>";
        close_table();
    }
    compile_hook('pages_end');
}
//--------------------- Copyrights ----------------------------------
if ($action == "copyrights") {
    global $global_lang;

    open_table();
    if ($global_lang == "arabic") {
        print "<center>
             مرخص لـ : $_SERVER[HTTP_HOST]" ;
  	print "   من <a href='http://allomani.com/' target='_blank'>  اللوماني للخدمات البرمجية </a>
   <br><br>
برمجة <a target=\"_blank\" href=\"http://allomani.com/\"> اللوماني للخدمات البرمجية </a> © " . SCRIPT_YEAR;

        print "</center>";
     
    } else {
        print "<center>
     Licensed for : $_SERVER[HTTP_HOST]   by <a href='http://allomani.com/' target='_blank'>Allomani&trade; Programming Services </a> <br><br>

   <p align=center>
Programmed By <a target=\"_blank\" href=\"http://allomani.com/\"> Allomani&trade; Programming Services </a> � " . SCRIPT_YEAR;
    }
    close_table();
}

//---------------------------- Forget Password -------------------------
if ($action == "forget_pass" || $action == "lostpwd"){
    members::password_forget();
    }
    
    if($action == "rest_pwd") {
  members::password_reset();
}
//-------------------------- Resend Active Message ----------------
if ($action == "resend_active_msg") {

    $qr = members::db_query("select * from {{store_clients}} where ::email=':email'", array('email' => db_escape($email)));
    if (db_num($qr)) {
        $data = members::db_fetch($qr);
        open_table();
        if (in_array($data['usr_group'], $members_connector['allowed_login_groups'])) {
            print "<center> $phrases[this_account_already_activated] </center>";
        } elseif (in_array($data['usr_group'], $members_connector['disallowed_login_groups'])) {
            print "<center> $phrases[closed_account_cannot_activate] </center>";
        } elseif (in_array($data['usr_group'], $members_connector['waiting_conf_login_groups'])) {
            members::snd_email_activation_msg($data['id']);
            print "<center>  $phrases[activation_msg_sent_successfully] </center>";
        }
        close_table();
    } else {
        open_table();
        print "<center>  $phrases[email_not_exists] </center>";
        close_table();
    }
}
//-------------------------- Active Account ------------------------
if ($action == "activate_email") {
    open_table("$phrases[active_account]");
    $qr = db_query("select * from store_confirmations where code='" . db_escape($code) . "'");
    if (db_num($qr)) {
        $data = db_fetch($qr);

        $data_member = members::db_qr_fetch("select count(*) as count from {{store_clients}} where ::id=':id' and ::usr_group=':usr_group'", array('id' => $data['cat'], 'usr_group' => $members_connector['waiting_conf_login_groups'][0]));

        if ($data_member['count']) {
            members::db_query("update {{store_clients}} set ::usr_group=':usr_group' where ::id=':id'", array('id'=>$data['cat'],'usr_group'=>$members_connector['allowed_login_groups'][0]));
           db_query("delete from store_confirmations where code='" . db_escape($code) . "'");
            print "<center> $phrases[active_acc_succ] </center>";
        } else {
            print "<center> $phrases[active_acc_err] </center>";
        }
    } else {
        print "<center> $phrases[active_acc_err] </center>";
    }
    close_table();
}

//-------------------------- Confirmations ------------------------
if ($action == "confirmations") {
    //----- email change confirmation ------//
    if ($op == "member_email_change") {
        open_table();
        $qr = db_query("select * from store_confirmations where code='" . db_escape($code) . "' and type='" . db_escape($op) . "'");

        if (db_num($qr)) {
            $data = db_fetch($qr);

            members::db_query("update {{store_clients}} set ::email =':email' where ::id=':id'", array('id'=>$data['cat'],'email'=>$data['new_value']));
            db_query("delete from store_confirmations where code='" . db_escape($code) . "'");
            print "<center> $phrases[your_email_changed_successfully] </center>";
        } else {
            print "<center> $phrases[err_wrong_url] </center>";
        }
        close_table();
    }
}

//----------- Client CP ------//
require(CWD . "/client_cp.php");
//------------------------ Members Login ---------------------------
if ($action == "login") {
  require (CWD .  "/login_form.php");
}

//---------------------------------------------------
require(CWD . "/includes/framework_end.php");
?>