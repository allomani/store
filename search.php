<?

require("global.php");

require(CWD . "/includes/framework_start.php");
//----------------------------------------------------

if ($settings['enable_search']) {


    $keyword = trim($keyword);

    if (strlen($keyword) >= $settings['search_min_letters']) {

        $keyword = htmlspecialchars($keyword);

        compile_hook('search_start');



        if (!$op) {

            //----------------- start pages system ----------------------
            $start = (int) $start;
            $page_string = "search.php?keyword=" . urlencode($keyword) . "&start={start}";
            $perpage = $settings['products_perpage'];
            //--------------------------------------------------------------




            $data_arr = db_fetch_all("select a.*,b.name as cat_name from store_products_data a left join store_products_cats b on b.id = a.cat where a.name like '%" . db_escape($keyword) . "%' order by a.id desc limit $start,$perpage");

            if (count($data_arr)) {

                $products_count = db_fetch_first("SELECT count(*) as count from store_products_data where name like '%" . db_escape($keyword) . "%'");



                run_template('browse_products');

//-------------------- pages system ------------------------
                print_pages_links($start, $products_count, $perpage, $page_string);
//-----------------------------
            } else {
                open_table();
                print "<center>  $phrases[no_results] </center>";
                close_table();
            }

//-----------------------------------------------------
        } elseif ($op == "news") {

            open_table("$phrases[search_results]");
            //----------------- start pages system ----------------------
            $start = (int) $start;
            $page_string = "search.php?op=news&keyword=" . urlencode($keyword) . "&start={start}";
            $news_perpage = $settings['news_perpage'];
            //--------------------------------------------------------------

            $data_arr = db_fetch_all("select * from store_news where title like '%" . db_escape($keyword) . "%' or content  like '%" . db_escape($keyword) . "%' or details  like '%" . db_escape($keyword) . "%' order by id desc limit $start,$news_perpage");


            if (count($data_arr)) {

                $page_result = db_fetch_first("select count(*) as count from store_news where title like '%" . db_escape($keyword) . "%' or content  like '%" . db_escape($keyword) . "%' or details  like '%" . db_escape($keyword) . "%'");

                run_template('browse_news');

                print_pages_links($start, $page_result, $settings['news_perpage'], $page_string);
            } else {
                print "<center>  $phrases[no_results] </center>";
            }
            close_table();
        }
//-----------------------------------------------------


        compile_hook('search_end');
//----------------
    } else {
        open_table();
        $phrases['type_search_keyword'] = str_replace('{letters}', $settings['search_min_letters'], $phrases['type_search_keyword']);
        print "<center>  $phrases[type_search_keyword] </center>";
        close_table();
    }
} else {
    open_table();
    print "<center> $phrases[sorry_search_disabled]</center>";
    close_table();
}




//---------------------------------------------------
require(CWD . "/includes/framework_end.php");
