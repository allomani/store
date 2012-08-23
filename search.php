<?
require("global.php");

require(CWD . "/includes/framework_start.php");   
//----------------------------------------------------
     
 if($settings['enable_search']){ 
     
  
         $keyword = trim($keyword);
         
        if(strlen($keyword) >= $settings['search_min_letters']){
          
              $keyword = htmlspecialchars($keyword); 
              
                compile_hook('search_start');   
      
       

 if(!$op){
      
   //----------------- start pages system ----------------------
    $start=intval($start);
       $page_string= "index.php?action=search&keyword=".urlencode($keyword)."&start={start}" ;
       $perpage = $settings['products_perpage'];
   //--------------------------------------------------------------
        
        
   
      if($full_text_search){  
         $qr=db_query("select *,match(name) against('".db_escape($keyword)."') as score from store_products_data where match(name) against('".db_escape($keyword)."') order by score desc limit $start,$perpage");
         $products_count=db_qr_fetch("select count(*) as count from store_products_data where match(name) against('".db_escape($keyword)."')");
       
     
      }else{
              $qr=db_query("select * from store_products_data where name like '%".db_escape($keyword)."%' order by id desc limit $start,$perpage");
             $products_count = db_qr_fetch("SELECT count(*) as count from store_products_data where name like '%".db_escape($keyword)."%'");
   
      }
     
       $cnt2 = db_num($qr) ;

         if($cnt2 > 0){
        
         
       
             run_template('browse_products_header');  
    $c=0;
        while($data = db_fetch($qr)){

  $data_cat = db_qr_fetch("select id,name from store_products_cats where id='$data[cat]'"); 

if ($c==$settings['img_cells']) {
    run_template('browse_products_spect');  
$c = 0 ;
}
    ++$c ;

    run_template('browse_products');


           }
           run_template('browse_products_footer'); 
        
//-------------------- pages system ------------------------
print_pages_links($start,$products_count['count'],$perpage,$page_string); 
//-----------------------------
 
              }else{
                  open_table();
                 print "<center>  $phrases[no_results] </center>";
                 close_table();   
                      }

//-----------------------------------------------------
}elseif($op=="news"){

  open_table("$phrases[search_results]" );   
              //----------------- start pages system ----------------------
    $start=intval($start);
       $page_string= "index.php?action=search&op=news&keyword=".urlencode($keyword)."&start={start}" ;
       $news_perpage = $settings['news_perpage'];
        //--------------------------------------------------------------


    
    if($full_text_search){   
    $qr = db_query("select *,match(`content`,`title`,`details`) against('".db_escape($keyword)."') as score from store_news where match(`content`,`title`,`details`) against('".db_escape($keyword)."') order by score desc limit $start,$news_perpage");
    $page_result = db_qr_fetch("select count(*) as count from store_news where match(`content`,`title`,`details`) against('".db_escape($keyword)."')");
    }else{
   $qr = db_query("select * from store_news where title like '%".db_escape($keyword)."%' or content  like '%".db_escape($keyword)."%' or details  like '%".db_escape($keyword)."%' order by id desc limit $start,$news_perpage");
       $page_result = db_qr_fetch("SELECT count(*) as count from store_news where title like '%".db_escape($keyword)."%' or content  like '%".db_escape($keyword)."%' or details  like '%".db_escape($keyword)."%'");
    }


$numrows=$page_result['count'];



    if(db_num($qr)){

       print "<hr class=separate_line size=\"1\">";
    while($data = db_fetch($qr)){

    $data['content'] = str_replace("$keyword","<font class=\"search_replace\">$keyword</font>",$data['content']);
  
  compile_template(get_template('browse_news'));  
       print "<hr class=separate_line size=\"1\">" ;


             }

//-------------------- pages system ------------------------
print_pages_links($start,$numrows,$settings['news_perpage'],$page_string);
//------------ end pages system -------------

            }else{
               print "<center>  $phrases[no_results] </center>";

        }
 close_table();          
        }
//-----------------------------------------------------


compile_hook('search_end'); 
//----------------
         }else{
         open_table();
         $phrases['type_search_keyword'] = str_replace('{letters}',$settings['search_min_letters'],$phrases['type_search_keyword']);
                 print "<center>  $phrases[type_search_keyword] </center>";
                 close_table();
                 }
                 
                 
}else{
 open_table();
 print "<center> $phrases[sorry_search_disabled]</center>";
 close_table();
     }


      
         
//---------------------------------------------------
require(CWD . "/includes/framework_end.php"); 