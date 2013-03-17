<?

require("global.php");


$sec_img = new captcha('contactus');


require(CWD . "/includes/framework_start.php");
//----------------------------------------------------
compile_hook("contactus_start");

$email_name = htmlspecialchars(trim($email_name));
$email_email = htmlspecialchars(trim($email_email));
$email_msg = htmlspecialchars(trim($email_msg));
$email_subject = htmlspecialchars(trim($email_subject));


if ($action == "send") {

    if ($sec_img->verify_string($sec_string)) {




        if (!$email_subject) {
            $email_subject = $phrases['without_subject'];
        }

        if ($email_name && $email_email && $email_msg) {

            if (check_email_address($email_email)) {


                $email_msg = nl2br($email_msg);
                $ip = get_ip();
                $msg = get_template('contactus_msg');
                $msg = str_replace(array('{name}', '{email}', '{message}', '{ip}'), array($email_name, $email_email, $email_msg, $ip), $msg);


                $mailResult = send_email($email_name, $mailing_email, $settings['admin_email'], $email_subject, $msg, true, '', $email_email);
                open_table();
                if ($mailResult) {
                    print "<center>  $phrases[contactus_send_successfully] </center>";
                } else {
                    print "<center>  $phrases[contactus_send_failed] </center>";
                }
                close_table();
            } else {
                open_table();
                print "<center> $phrases[err_email_not_valid] </center>";
                close_table();
            }
        } else {
            open_table();
            print "<center> $phrases[err_fileds_not_complete] </center>";
            close_table();
        }
    } else {
        open_table();
        print "<center>$phrases[err_sec_code_not_valid]</center>";
        close_table();
    }
}


if (!$send_done) {

    run_template("contactus");
}

compile_hook("contactus_end");
//---------------------------------------------------
require(CWD . "/includes/framework_end.php");
?>