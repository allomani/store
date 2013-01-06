<?

require("global.php");

require(CWD . "/includes/framework_start.php");
//----------------------------------------------------

if (check_member_login()) {

    //---- del ----
    if ($action == "del") {
        db_query("delete from store_clients_favorites where id='$id' and userid='$member_data[id]'");
    }
    //------------

        //----------------- start pages system ----------------------
        $start = (int) $start;
        $page_string = "favorites.php?start={start}";
        $perpage = (int) $settings['products_perpage'];
        //--------------------------------------------------------------
        
        
    $qr = db_query("select a.id as fav_id,b.*,c.name as cat_name from store_clients_favorites a inner join store_products_data b on b.id=a.product_id left join store_products_cats c on c.id = b.cat where a.userid='$member_data[id]' order by a.id desc limit $start,$perpage");

    if (db_num($qr)) {

        $products_count = db_fetch_first("select count(*) from store_clients_favorites a inner join store_products_data b on b.id=a.product_id where a.userid='$member_data[id]'");


  
        $data_arr = db_fetch_all($qr);

        run_template('browse_products');




        //-------------------- pages system ------------------------
        print_pages_links($start, $products_count, $perpage, $page_string);
        //-----------------------------
    } else {

        open_table();
        print "<center> $phrases[no_products] </center>";
        close_table();
    }
} else {
    login_redirect();
}

require(CWD . "/includes/framework_end.php");
?>
