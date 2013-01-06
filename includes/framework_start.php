<?
if(!defined("GLOBAL_LOADED")){die("Access Denied");}

require(CWD ."/counter.php");

if($debug){$start_time=microtime();}

if(!$settings['enable_browsing']){
if(!check_admin_login()){
run_template('browsing_disabled_page');
die();
}
}

templates_cache(array('header','footer','block','table','page_head','js_functions','center_banners','blocks_banners'));
     
compile_hook('site_before_header');
site_header();
compile_hook('site_after_header');


print "<table id=\"main_container\">

         <tr>" ;
        //------------------------- Block Pages System ---------------------------
        function get_pg_view(){
                global $pg_view ,$action,$actions_checks,$c_pg_view ;
              
             
       
          if(CFN == "index.php"){
          $pg_view = iif($action,$action,"main"); 
          }else{
          $pg_view = CFN .iif($action,"_".$action);
          if(!in_array($pg_view,$actions_checks)){ $pg_view = CFN ;}
          }
        
          if(!in_array($pg_view,$actions_checks)){$c_pg_view = "none" ;$pg_view = "main" ;}else{ $c_pg_view =  $pg_view;}    
        }
        //-----------------------------Pre Cache Blocks ---------------------------------
        get_pg_view();
      
          
         
         unset($blocks);
        // $cached_blocks = cache_get("blocks:$pg_view:$c_pg_view");
        // if($cached_blocks == false){
         $qr=db_query("select * from store_blocks where active=1 and ((pages like '%$pg_view,%' and (pos='l' or pos='r')) or (pages like '%$c_pg_view,%' and pos='c')) order by pos,ord"); 
         while($data = db_fetch($qr)){
         $blocks[$data['pos']][$data['cat']][] = $data;
         }
      //  cache_set("blocks:$pg_view:$c_pg_view",$blocks);
      //   }else{
      //   $blocks = $cached_blocks;    
       //  }
       //-------------- Pre Cache Banners ----------------// 
        unset($banners);
      //   $cached_banners = cache_get("banners:$pg_view:$c_pg_view");
      //   if($cached_banners == false){
//$qr=db_query("select * from store_banners where active=1 and ((pages like '%$pg_view,%' and (menu_pos='l' or menu_pos='r')) or (pages like '%$c_pg_view,%' and menu_pos='c')) and (start_date <= ".time()." or start_date=0) and (expire_date >= ".time()." or expire_date=0) order by `type`,menu_pos,ord"); 
  $qr=db_query("select * from store_banners where active=1 and ((pages like '%$pg_view,%' and (menu_pos='l' or menu_pos='r')) or (pages like '%$c_pg_view,%' and menu_pos='c')) and (start_date <= ".time()." or start_date=0) and (expire_date >= ".time()." or expire_date=0) order by `type`,ord,menu_pos"); 

         while($data = db_fetch($qr)){
             
         $data['menu_pos'] = iif($data['type']=="menu",$data['menu_pos'],"x");
         $data['menu_id'] =iif($data['type']=="menu",intval($data['menu_id']),0);;
         
         $banners[$data['type']][$data['menu_pos']][$data['menu_id']][] = $data;
         }
         
    //       cache_set("banners:$pg_view:$c_pg_view",$banners);
           
      //   }else{
      //       $banners = $cached_banners;
      //   }
         
         
        unset($qr,$data);
       //----------------------- Left Content --------------------------------------------
      if(count($blocks['l'])){
        print "<td id=\"left_blocks\">
        <table width=100%>" ;

        $adv_c = 1 ;
         foreach($blocks['l'][0] as $xdata){

        print "<tr>
                <td  width=\"100%\" valign=\"top\">";
                
   //     $sub_qr = db_query("select * from store_blocks where active=1 and cat='$xdata[id]' and pages like '%$pg_view,%' order by ord");
          $sub_count = count($blocks['l'][$xdata['id']]);
          
        
             
            
           if($sub_count){
              
               $tabs = new tabs("block_".$xdata['id']);
 
        $tabs->start($xdata['title']);
           run_php($xdata['file']);
          $tabs->end();  
          
          foreach($blocks['l'][$xdata['id']] as $sub_data){
           $tabs->start($sub_data['title']);
           run_php($sub_data['file']);
          $tabs->end();     
          }
          
           $tabs->run(); 
         
          
           }else{
                open_block(iif(!$xdata['hide_title'] && !$sub_count,$xdata['title']),$xdata['template']);  
               run_php($xdata['file']);           
                close_block($xdata['template']);          
           } 
        
          
                print "</td>
        </tr>";

         //----------------Left block banners--------------------------
            
        if(count($banners['menu']['l'][$adv_c])){
       print_block_banners($banners['menu']['l'][$adv_c]);  
        unset($banners['menu']['l'][$adv_c]);
               }
            ++$adv_c ;
        //----------------------------------------------------
           }
           
//------- print remaining blocks banners --------//
if(count($banners['menu']['l'])){
    foreach($banners['menu']['l'] as $data_array){
         print_block_banners($data_array); 
    }
}

print "</table></center></td>";


//--------------------//

unset($xdata,$data,$adv_c);
}
 

print "<td  valign=\"top\" dir=$global_dir>";

//---------------------  Header Banners ----------------------------

 if(count($banners['header']['x'][0])){
  
 foreach($banners['header']['x'][0] as $data){
db_query("update store_banners set views=views+1 where id=$data[id]");
if($data['c_type']=="code"){
run_php($data['content']);
    }else{
        run_template("center_banners"); 
}
        }
 print "<br>";
 }

//-------------------------- CENTER CONTENT ---------------------------------------------
 

     //--------- open banners ----------//
    $bnx = 0 ;
   if(count($banners['open']['x'][0])){
 foreach($banners['open']['x'][0] as $data){

    if ($data['url']){
     db_query("update store_banners set views=views+1 where id='$data[id]'");
   print "<script>
   banner_pop_open(\"$data[url]\",\"displaywindow_$bnx\");
       </script>\n";
         $bnx++;
          }

    }
   }
    
    //----------- close banners ----------- //
  
   print "<script>
   function pop_close(){";
    if(count($banners['close']['x'][0])){ 
      db_query("update store_banners set views=views+1 where id='$data[id]'");        
   foreach($banners['close']['x'][0] as $data){   
       print "banner_pop_close(\"$data[url]\",\"displaywindow_close_$data[id]\");";
       
       }
    }
       print " }
        </script>\n";
   

      
 $adv_c = 1 ;
       
  if(count($blocks['c'][0])){
         foreach($blocks['c'][0] as $ydata){

    
           $sub_count = count($blocks['c'][$ydata['id']]);    
        

           if($sub_count){
             
               $tabs = new tabs("block_".$ydata['id']);
 
        $tabs->start($ydata['title']);
           run_php($ydata['file']);
          $tabs->end();  
          
          foreach($blocks['c'][$ydata['id']] as $sub_data){    

           $tabs->start($sub_data['title']);
           run_php($sub_data['file']);
          $tabs->end();     
          }
          
           $tabs->run(); 
         
           }else{
               open_table(iif(!$ydata['hide_title'],$ydata['title']),$ydata['template']);     
               run_php($ydata['file']);           
               close_table($ydata['template']);          
           }   
             

           

      //----------------- Center Menus Banners-----------------------
         
       if(count($banners['menu']['c'][$adv_c])){
              print_block_banners($banners['menu']['c'][$adv_c],"center");  
        unset($banners['menu']['c'][$adv_c]);
          
       }
       ++$adv_c ; 
        //----------------------------------------------------
           
        
                    }
        //------- print remaining blocks banners --------//
if(count($banners['menu']['c'])){
    foreach($banners['menu']['c'] as $data_array){
         print_block_banners($data_array,"center"); 
    }
}
            
                    }
  unset($yqr,$ydata,$data,$adv_c); 
  
  
 //--------------- Load freamwork Plugins --------------------------
  $pls = load_plugins("start_".CFN);
  if(is_array($pls)){foreach($pls as $pl){include($pl);}}
  
      
?>
    
    