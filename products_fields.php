<?php
/**
 *  Allomani E-Store v1.0
 * 
 * @package Allomani.E-Store
 * @version 1.0
 * @copyright (c) 2006-2018 Allomani , All rights reserved.
 * @author Ali Allomani <info@allomani.com>
 * @link http://allomani.com
 * @license GNU General Public License version 3.0 (GPLv3)
 * 
 */


// Edited : 07-10-2009

global $action,$cat,$id,$field_option,$price_from,$price_to,$style;

if(THIS_PAGE !="index"){die();}

if($action=="browse" || $action=="product_details"){

if($cat || $id){
if($action=="browse"){
 $fields_array = get_product_cat_fields($cat,true); 
$data_fields = db_qr_fetch("select name,id from store_products_cats where id='$cat'");
}else{    
$data_cat = db_qr_fetch("select id,name,cat from store_products_data where id='$id'");
$data_fields = db_qr_fetch("select id,name from store_products_cats where id='$data_cat[cat]'");
$fields_array = get_product_cat_fields($data_cat['cat'],true);   
}
//$fields_array=iif($data_fields['fields'],explode(',',$data_fields['fields']),array());

  //$fields_array = array_map('intval',$fields_array);


print "<form action=index.php method=get>
<input type=hidden name=action value='browse'>
<input type=hidden name='hide_subcats' value='1'>


<b><img src=\"$style[images]/cat.gif\">&nbsp;$phrases[the_cat] : </b> $data_fields[name]
<input type=hidden name='cat' value='$data_fields[id]'>";
//---- if subcats -----//
$subcats = db_qr_fetch("select count(id) as count from store_products_cats where cat='$data_fields[id]'");
if($subcats['count']){
print "<br>
<input type=checkbox name=\"include_subcats\" value=1 checked> $phrases[search_in_subcats]  ";
}
print "<hr class=separate_line size=1>"; 


   
if(count($fields_array)){      
  
 
      
$qr = db_query("select * from store_fields_sets where id IN (".implode(",",$fields_array).") and active=1 and type like 'select' and in_search=1 order by ord");
if(db_num($qr)){
    
    //----- cache options -----
     $qr_options = db_query("select * from store_fields_options where field_id IN (".implode(",",$fields_array).") order by ord"); 
 unset($fields_options);
 while($data_options = db_fetch($qr_options)){
 $fields_options[$data_options['field_id']][] =  $data_options ;   
 }
 //---------------------------//
 
 
//open_block("$phrases[features]"); 

while($data =db_fetch($qr)){
  
   
    if(is_array($fields_options[$data['id']])){
    print iif($data['img'],"<img src=\"$data[img]\">&nbsp;")."<b>".iif($data['title'],$data['title'],$data['name'])." : </b><br><br> ";  
     
      
    print "<select name='field_option[".$data['id']."]'>
    <option value=''>$phrases[all]</option>";
       
    foreach($fields_options[$data['id']] as $data_options){
        print "<option value=\"$data_options[id]\"".iif($data_options['id']==$field_option[$data['id']]," selected").">$data_options[value]</option>";
    }
    print "</select><hr class=separate_line size=1>";
    }
}

//close_block();
unset($fields_options,$data,$data_fields,$fields_array,$data_cat);

}
}


//---- price --------
if($price_from){$price_from = intval($price_from);}
if($price_to){$price_to = intval($price_to);}

print "<b> $phrases[the_price] : </b><br><br>
$phrases[from] : <input type=text size=2 name=price_from value=\"".iif($price_from,$price_from)."\">
$phrases[to] : <input type=text size=2 name=price_to value=\"".iif($price_to,$price_to)."\">
<br><br>";
//-------

print "<center><input type=submit value=' $phrases[update] '></center>
</form>";
}else{
    print "<center>$phrases[no_options]</center>";
}
}
