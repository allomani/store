<?
require("global.php");
require(CWD . "/includes/framework_start.php");
//----------------------------------------------------

$vote_id = (int) $vote_id;

compile_hook('votes_start');

if ($action == "vote_add") {
$session_name = "vote_{$vote_id}_added";
    if ($session->get($session_name) > time() - ($settings['votes_expire_hours'] * 60*60)) {
       open_table();
       print "<center>" . str_replace('{vote_expire_hours}', $settings['votes_expire_hours'], $phrases['err_vote_expire_hours']) . "</center>";
       close_table();
        
    }else{
     db_query("update store_votes set cnt=cnt+1 where id='$vote_id'");
         $session->set($session_name,time());
    }
}


$qr = db_query("select * from store_votes_cats where " . iif($id, "id='$id'", "active=1"));
if (db_num($qr)) {
    $data = db_fetch($qr);
    open_table("$data[title]");

    $qr_stat = db_query("select * from store_votes where cat='$data[id]'");


    if (db_num($qr_stat)) {
        while ($data_stat = db_fetch($qr_stat)) {
            $votes[] = $data_stat;
            $total = $total + $data_stat['cnt'];
        }

       ?>
<?
        if ($total) {
            print "<br>";

            $l_size = @getimagesize("$style[images]/leftbar.gif");
            $m_size = @getimagesize("$style[images]/mainbar.gif");
            $r_size = @getimagesize("$style[images]/rightbar.gif");


            print "<table>";
            foreach ($votes as $data_stat) {

                $rs[0] = $data_stat['cnt'];
                $rs[1] = substr(100 * $data_stat['cnt'] / $total, 0, 5);
                $title = $data_stat['title'];

                print "<tr><td>";
                print " $title :</td><td dir=ltr align='$global_align'>
                    <img src=\"$style[images]/leftbar.gif\"  width=\"$l_size[0]\">";
                print "<img src=\"$style[images]/mainbar.gif\"  height=\"$m_size[1]\" width=" . $rs[1] * 2 . "><img src=\"$style[images]/rightbar.gif\" height=\"$r_size[1]\" width=\"$l_size[0]\">
    </td><td>
    $rs[1] % ($rs[0])</td>
    </tr>\n";
            }
            print "</table>";
        } else {
            print "<center> $phrases[no_results] </center>";
        }
    } else {
        print "<center> $phrases[no_options] </center>";
    }
} else {
    print "<center>$phrases[err_wrong_url]</center>";
}

close_table();

if ($settings['other_votes_show']) {
    $qr = db_query("select id,title from store_votes_cats where " . iif($id, "id != '$id'", "active != 1") . " order by $settings[other_votes_orderby] limit $settings[other_votes_limit]");
    if (db_num($qr)) {
        open_table("$phrases[prev_votes]");
        print "<ul class='another_votes'>";
        while ($data = db_fetch($qr)) {
            print "<li><a href='index.php?action=votes&id=$data[id]'>$data[title]</li>";
        }
        print "</ul>";
        close_table();
    }
}
compile_hook('votes_end');

//---------------------------------------------------
require(CWD . "/includes/framework_end.php");
