<?
    require("global.php");

    require(CWD . "/includes/framework_start.php");   
    //----------------------------------------------------


    $cat= (int) $cat;
    $hide_subcats = intval($hide_subcats);
    $include_subcats = intval($include_subcats); 


    compile_hook('browse_products_start');

    print_path_links($cat);

    compile_hook('browse_products_after_path_links'); 

    //-------- cats -------
    if(!$hide_subcats){
        $qr2 = db_query("select * from store_products_cats where cat='$cat' and active=1 order by ord asc");
        if(db_num($qr2)){

            $data_arr = db_fetch_all($qr2);
            
            compile_hook('browse_products_before_cats_table'); 
           
                run_template('browse_products_cats');
       
            compile_hook('browse_products_after_cats_table');
        }else{

            $no_cats = true;
        }
    }else{
        $no_cats = true;
    }
    //------------------------





    //------------- Getting Required Fields Products Ids ------------//
    $field_option = array_remove_empty_values($field_option);
    $count_fields = count($field_option);
    $fields_ok_array = array(0);
    if(is_array($field_option)){

        for($i=0; $i < $count_fields;$i++) { 
            $key = key($field_option);
            $value = current($field_option);

            //     print $key ." | ".$value ."--" ;
            if($key && $value){

                $qrz = db_query("select product_id from store_fields_data where value='".db_escape($value)."'");  
                while($dataz=db_fetch($qrz)){ 

                    if($fields_p_array[$dataz['product_id']] > 0){
                        $fields_p_array[$dataz['product_id']] = $fields_p_array[$dataz['product_id']]+1 ;

                    }else{
                        $fields_p_array[$dataz['product_id']] = 1;
                    }
                    //---
                    if($fields_p_array[$dataz['product_id']] == $count_fields){

                        $fields_ok_array[] = $dataz['product_id'];
                    }
                    //----
                } 
            } 
            next($field_option);
        }
    }

    // print_r($fields_ok_array);

    //---- order by filtering -------//    
    if(!$orderby || !$settings['visitors_can_sort_products'] || !in_array($orderby,$orderby_checks)){$orderby=($settings['products_default_orderby'] ? $settings['products_default_orderby'] : "id");}
    if(!$sort || !$settings['visitors_can_sort_products'] || !in_array($sort,array('asc','desc'))){$sort=($settings['products_default_sort'] ? $settings['products_default_sort'] : "asc");}




    //----------------------
    $start = intval($start);
    $price_from = intval($price_from);
    $price_to = intval($price_to);



    $perpage = $settings['products_perpage'];

    if(is_array($field_option) || $orderby != $settings['products_default_orderby'] || $sort !=$settings['products_default_sort'] || $price_from || $price_to){
        $page_string = "browse.php?hide_subcats=$hide_subcats&include_subcats=$include_subcats&cat=$cat&start={start}&orderby=$orderby&sort=$sort";

        if($price_from){$page_string .=  "&price_from=$price_from";}
        if($price_to){$page_string .=  "&price_to=$price_to";} 

        if(is_array($field_option)){
            foreach($field_option as $key => $value) { 
                $page_string .= "&field_option[$key]=$value";
            }
        }
        $page_string .= "&start={start}";  

    }else{
        $page_string = str_replace('{id}',$cat,$links['browse_products_w_pages']);
    }
    //---------------------




    //----------- products Query ------------------------//  
    $sql_query = "select store_products_data.*,store_products_cats.name as cat_name,store_products_cats.id as cat_id from store_products_data,store_products_cats where ";

    $sql_where = "store_products_data.cat=store_products_cats.id and store_products_cats.active=1 and store_products_data.active=1"; 

    if($include_subcats){ 
        $cats_arr = get_products_cats($cat);

        $sql_where .= " and store_products_cats.id IN (".implode(',',$cats_arr).")";   

        //----- cache cats data ----//
        // $qr=db_query("select id,name from store_products_cats where id IN (".implode(',',$cats_arr).")
    }else{
        $sql_where .= " and store_products_cats.id='$cat'";
    }




    if($price_from){$sql_where .= " and price >= $price_from";}

    if($price_to){$sql_where .= " and price <= $price_to";} 




    if($count_fields){
        $sql_where .= " and  store_products_data.id IN (".implode(',',$fields_ok_array).") ";   
    }

    $sql_query .= $sql_where  ;
    $sql_query .= " order by  store_products_data.$orderby ".iif($orderby=="available",iif($sort=="asc","desc","asc"),$sort)." limit $start,$perpage";
    // print   $sql_query;
    $qr = db_query($sql_query);
    //-----------------------------------------------------//



    if(db_num($qr)){

        $products_count  = db_fetch_first("select count(store_products_data.id) as count from store_products_data,store_products_cats where $sql_where");
        $data_cat = db_fetch("select name from store_products_cats where id='$cat'");
        $data_arr  = db_fetch_all($qr);
        run_template('browse_products');  
      
        //-------------------- pages system ------------------------
        print_pages_links($start,$products_count,$perpage,$page_string); 
        //-----------------------------

    }else{
        if($no_cats){
            open_table();    
            print "<center> $phrases[no_products] </center>";
            close_table();
        }
    }


    compile_hook('browse_products_end');      


    //---------------------------------------------------
    require(CWD . "/includes/framework_end.php");
?>