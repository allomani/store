<?
 require('./start.php'); 
 
//------------------------------------- New Stores Menu ------------------------------
if(!$action || $action=="hot_items" || $action=="add" || $action=="del"){
       if_admin("hot_items");

    print "<p class=title align=center>$phrases[hot_items]</p>";
    
if($action=="add"){
    $id = (array) $id ;
    
 foreach($id as $idx){
   
   $idx = intval($idx);
   
     
$cntx = db_qr_fetch("select count(id) as count from store_products_data where id='$idx'");

     if($cntx['count']){
         db_query("update store_hot_items set ord=ord+1");
        db_query("insert into store_hot_items (`product_id`,ord) values ('$idx','0')");
        
        }else{
        print_admin_table("<center>$phrases[err_invalid_id] : $idx</center>");
        print "<br>";
        }
 }
 
 
        }  
//------ del ----------//        
if($action=="del"){
 db_query("delete from store_hot_items where id='$id'");
  }
//------------------//

  print "<center>
  <form action=hot_items.php method=post name=sender>
  <input type=hidden name=action value='add'>
  <table width=50% class=grid><tr>
  <td> <b> ID   :</b>
  <input type=text name=id[] size=4>
  </td>
  <td align=$global_align_x>
  
  <input type=submit value='$phrases[add_button]'></td></tr></table></form>
              <br>
          <table width=100% class=grid><tr><td>";
          
print "
          <div id=\"hot_items_list\" class='sortable'>";
          
$qr=db_query("select * from store_hot_items order by ord asc");
if(db_num($qr)){
while($data = db_fetch($qr)){

   
     $qr2=db_query("select store_products_data.id as id ,store_products_data.name as name,store_products_data.img as img,store_products_cats.name as cat from store_products_data,store_products_cats where store_products_data.cat=store_products_cats.id and store_products_data.id='$data[product_id]'");
    
       if(db_num($qr2)){
               $data2 = db_fetch($qr2);
               toggle_tr_class();
        print "<div id=\"item_$data[id]\" class='$tr_class'>
             
        <table width=100%><tr>
        <td class='handle'></td>
        <td width=23 align=center><img width=30 height=30 src=\"$scripturl/".get_image($data2['img'])."\"></td>
        <td>$data2[cat] ->  <b>$data2[name]</b></td>
      <td width=100><a href=\"hot_items.php?action=del&id=$data[id]\" onClick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete]</a></td>
       </tr></table>
       </div>
       ";
       $found=true;
       }else{
          // print "wrong $data[cat] . $data[type]";
       db_query("delete from store_hot_items where product_id='$data[product_id]'");
               }
        }
        }else{
                print "<center> $phrases[no_data] </center>";
                 $found=true; 
                }
                
         if(!$found){
         print "<center> $phrases[no_data] </center>";   
         }
         
        print "</div></td></tr></table></center>";

        
  print "<script type=\"text/javascript\">
        init_sortlist('hot_items_list','hot_items');
</script>";      
        }
        
//-----------end ----------------
 require(ADMIN_DIR.'/end.php');