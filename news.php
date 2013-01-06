<?

require("global.php");

require(CWD . "/includes/framework_start.php");
//---------------------------------------------------------


$id = (int) $id;
$cat = (int) $cat;


compile_hook('news_start');

if ($id) {
    compile_hook('news_inside_start');
    $qr = db_query("select * from store_news where id='$id'");
    if (db_num($qr)) {
        $data = db_fetch($qr);

        if ($data['cat']) {
            $cat_name = db_qr_fetch("select name from store_news_cats where id='$data[cat]'");
        }
        print "<ul class='nav-bar'>
      <li><a href=\"" . $links['news'] . "\" title=\"$phrases[the_news]\">$phrases[the_news]</a></li>
          " . iif($cat_name['name'], "<li> <a href=\"" . str_replace("{cat}", $data['cat'], $links['browse_news']) . "\" title=\"$cat_name[name]\">$cat_name[name]</a></li>");
        if ($data[title]) {
            print "<li class='name'>$data[title]</li>";
        }
        print "</ul>";

        open_table($data['title']);
        run_template('news_details');
        close_table();

        db_query("update store_news set views=views+1 where id='$id'");

        //------ Comments -------------------
        if ($settings['enable_news_comments']) {
            open_table($phrases['members_comments']);
            get_comments_box('news', $id);
            close_table();
        }
    } else {
        open_table();
        print "<center>$phrases[err_wrong_url]</center>";
        close_table();
    }
    compile_hook('news_inside_end');
} else {

    if ($cat) {
        $cat_name = db_qr_fetch("select name from store_news_cats where id='$cat'");
    }
    print "<ul class='nav-bar'>
      <li><a href=\"" . $links['news'] . "\" title=\"$phrases[the_news]\">$phrases[the_news]</a></li>"
            . iif($cat_name['name'], "<li><a href=\"" . str_replace("{cat}", $cat, $links['browse_news']) . "\" title=\"$cat_name[name]\">$cat_name[name]</a></li>") . "
              </ul>";


//---------- cats ------------//
    $no_cats = false;
    if (!$cat) {
        $qr = db_query("select * from  store_news_cats order by ord asc");
        if (db_num($qr)) {

            $data_arr = db_fetch_all($qr);
            run_template('news_cats');
        } else {
            $no_cats = true;
        }
    } else {
        $no_cats = true;
    }
//----------------------------//
    //----------------- start pages system ----------------------
    $start = (int) $start;
    $page_string = str_replace('{cat}', $cat, $links['browse_news_w_pages']);
    $news_perpage = (int) $settings['news_perpage'];
    //----------------------------------------------------------


    $qr = db_query("select * from store_news where cat='$cat' order by id DESC limit $start,$news_perpage");


    if (db_num($qr)) {

        //-------------------
        $page_result = db_fetch_first("select count(*) as count from store_news where cat='$cat'");
        //--------------------------------------------------------------
        $data_arr = db_fetch_all($qr);
        run_template('browse_news');


        compile_hook('news_outside_before_pages');
//-------------------- pages system ------------------------
        print_pages_links($start, $page_result, $news_perpage, $page_string);
//------------ end pages system -------------
    } else {
        if ($no_cats) {
            open_table();
            print "<center>$phrases[no_news]</center>";
            close_table();
        }
    }



    compile_hook('news_outside_end');
}
compile_hook('news_end');

//---------------------------------------------
require(CWD . "/includes/framework_end.php");
?>