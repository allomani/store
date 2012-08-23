<?
function set_meta_values(){
       
global $sitename,$phrases,$start,$settings,$keyword,$action,$id,$op,$cat,$section_name,$sec_name,$meta_description,$meta_keywords,$title_sub,$album_id,$year,$letter,$albums_only,$store_only;
 
//------ Product Details Page ---------------
if($action == "product_details" && $id){
$qr = db_query("select name,page_title,page_description,page_keywords from store_products_data where id='$id'");
if(db_num($qr)){
$data = db_fetch($qr) ;
$title_sub = iif($data['page_title'],$data['page_title'],$data['name']) ;
$meta_description = iif($data['page_description'],$data['page_description']);
$meta_keywords = iif($data['page_keywords'],$data['page_keywords']);


        }else{
 $title_sub = "" ;
 }
 }

//------ Product Cat Name ---------------
if($action == "browse" && $cat){
$qr = db_query("select name,page_title,page_description,page_keywords from store_products_cats where id='$cat'");
if(db_num($qr)){
$data = db_fetch($qr) ;
$title_sub = iif($data['page_title'],$data['page_title'],$data['name']) ;
$meta_description = iif($data['page_description'],$data['page_description']);
$meta_keywords = iif($data['page_keywords'],$data['page_keywords']);
        }else{
 $title_sub = "" ;
 }
 }

 //------ News Title ---------------
if($action == "news"){
$qr = db_query("select title from store_news where id='$id'");
if(db_num($qr)){
$data = db_fetch($qr) ;
$title_sub = "$data[title]" ;
        }else{
 $title_sub = "$phrases[the_news]" ;
 }
 }
   //------ Search Title ---------------
if($action == "search" && $keyword){

$title_sub = $phrases['the_search'] ;
$meta_description = htmlspecialchars($keyword);    

 }
//-------------------------------------
if(!$meta_description){ $meta_description= $settings['header_description']." , ".iif($title_sub,$title_sub,$sitename);}
if(!$meta_keywords){$meta_keywords = iif($settings['header_keywords'],$settings['header_keywords'],$settings['header_description'])." , ".iif($title_sub,$title_sub,$sitename); }  

}



function get_meta_values($name){
    $data = db_qr_fetch("select * from store_meta where name like '".db_escape($name)."'");
    return $data;
}

?>