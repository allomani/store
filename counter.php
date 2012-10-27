<?

if (!defined("GLOBAL_LOADED")) {
    die("Access Denied");
}

$http_agent = getenv("HTTP_USER_AGENT");


if ($settings['count_visitors_info']) {
    //   print getenv("HTTP_USER_AGENT");
//------------------- Get Browser info ------------
    if (stristr($http_agent, "MSIE"))
        $browser = "MSIE";
    elseif (stristr($http_agent, "Chrome"))
        $browser = "Chrome";
    elseif (stristr($http_agent, "Firefox"))
        $browser = "Firefox";
    elseif (stristr($http_agent, "Nokia"))
        $browser = "Nokia";
    elseif (stristr($http_agent, "BlackBerry"))
        $browser = "BlackBerry";
    elseif (stristr($http_agent, "iPhone"))
        $browser = "iPhone";
    elseif (stristr($http_agent, "iPod"))
        $browser = "iPod";
    elseif (stristr($http_agent, "Android"))
        $browser = "Android";
    elseif (stristr($http_agent, "Lynx"))
        $browser = "Lynx";
    elseif (stristr($http_agent, "Opera"))
        $browser = "Opera";
    elseif (stristr($http_agent, "WebTV"))
        $browser = "WebTV";
    elseif (stristr($http_agent, "Konqueror"))
        $browser = "Konqueror";
    elseif ((stristr($http_agent, "Nav")) || (stristr($http_agent, "Gold")) || (stristr($http_agent, "X11")) || (stristr($http_agent, "Mozilla")) || (stristr($http_agent, "Netscape")) AND (!stristr($http_agent, "MSIE") AND (!stristr($http_agent, "Konqueror"))))
        $browser = "Netscape";
    elseif ((stristr($http_agent, "bot")) || (stristr($http_agent, "Google")) || (stristr($http_agent, "Slurp")) || (stristr($http_agent, "Scooter")) || (stristr($http_agent, "Spider")) || (stristr($http_agent, "Infoseek")))
        $browser = "Bot";
    else
        $browser = "Other";

//--------- Get Os info -------------------

    if (stristr($http_agent, "Win"))
        $os = "Windows";
    elseif ((stristr($http_agent, "Mac")) || (stristr($http_agent, "PPC")))
        $os = "Mac";
    elseif (stristr($http_agent, "Linux"))
        $os = "Linux";
    elseif (stristr($http_agent, "FreeBSD"))
        $os = "FreeBSD";
    elseif (stristr($http_agent, "SunOS"))
        $os = "SunOS";
    elseif (stristr($http_agent, "IRIX"))
        $os = "IRIX";
    elseif (stristr($http_agent, "BeOS"))
        $os = "BeOS";
    elseif (stristr($http_agent, "OS/2"))
        $os = "OS/2";
    elseif (stristr($http_agent, "AIX"))
        $os = "AIX";
    elseif (stristr($http_agent, "Symbian"))
        $os = "Symbian";
    elseif (stristr($http_agent, "BlackBerry"))
        $os = "BlackBerry";
    else
        $os = "Other";


//-------- OS and Browser Info ------------

    db_query("insert into info_browser (name,count) values ('".db_escape($browser)."','1') ON DUPLICATE KEY UPDATE count=count+1");
    db_query("insert into info_os (name,count) values ('".db_escape($os)."','1') ON DUPLICATE KEY UPDATE count=count+1");
}


if ($settings['count_visitors_hits']) {
//------ Visitors Count ----------------
    $today = date("d-m-Y");
    db_query("insert into info_hits (date,hits) values ('$today','1') ON DUPLICATE KEY UPDATE hits=hits+1");
}


if ($settings['count_online_visitors']) {
// ---- visitor timeout ----------------
    if ($online_visitor_timeout) {
        $timeoutseconds = intval($online_visitor_timeout);
    } else {
        $timeoutseconds = 800;
    }


    $ip = getenv("REMOTE_ADDR");
    //  $ip = "213.25.52.40";
//$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

    $time = time();
    $timeout = $time - $timeoutseconds;

    db_query("DELETE FROM info_online WHERE time<$timeout");
    db_query("INSERT INTO info_online (time,ip) VALUES ('$time', '" . db_escape($ip) . "') ON DUPLICATE KEY UPDATE time='".$time."'");

//---------- Now Online Visitors ------------

    $visitors_data = db_qr_fetch("select count(*) as count FROM info_online ");
    $users = $visitors_data['count'];



//=========Best Visitors Record ==============================================
    $now_dt = date("d-M-Y") . " $phrases[the_hour] : " . date("H:i");
    $data = db_qr_fetch("select v_count,time from info_best_visitors");

    if ($users > $data['v_count']) {

        $counter['best_visit'] = $users;
        $counter['best_visit_time'] = $now_dt;

        db_query("update info_best_visitors set v_count='$users',time='$now_dt'");
    } else {

        $counter['best_visit'] = $data['v_count'];
        $counter['best_visit_time'] = $data['time'];
    }
//==========================================================================


    if ($settings['online_members_count']) {
        $data = db_qr_fetch("select count(*) as count from " . members_table_replace("store_clients") . " where " . members_fields_replace("last_login") . " >= " . connector_get_date($timeout, 'member_last_login'), MEMBER_SQL);

        $counter['online_members'] = intval($data['count']);
        $counter['online_users'] = $users - $counter['online_members'];
    } else {
        $counter['online_users'] = $users;
    }
}
?>