<?
$templates_cache = array();
//----------- get template ----------------
function get_template($name,$tfind="",$treplace="",$hide_error=""){
 global $styleid,$templates_cache ;

$name=strtolower($name) ;

if(isset($templates_cache[$name])){
    
    $content = $templates_cache[$name] ;
}else{
$qr = db_query("select content from store_templates where name like '$name' and cat='$styleid'");
 
 if(db_num($qr)){
     
     $data = db_fetch($qr);
    $content = $data['content'] ;
    $templates_cache[$name] = $data['content'];
    unset($data);
   }else{
   if(!$hide_error){
   $content =  "<b>Error : </b> Template ".htmlspecialchars($name)." Not Exists <br>";
   }else{
   $content =  "";
   }
       }
}

return iif(($tfind || $tfind=="0")&&($treplace || $treplace=="0"),str_replace($tfind,$treplace,$content),$content) ;
  
}

//----------  templates cache --------------
function templates_cache($names){
    global $templates_cache,$styleid;
    
if(!is_array($names)){$names[]=$names;}

$sql = "select name,content from store_templates where name IN (";

for($i=0;$i<count($names);$i++){
$sql .= "'".$names[$i]."'".iif($i < count($names)-1,",");    
}

$sql .= ") and cat='$styleid'";

$qr = db_query($sql);
while($data=db_fetch($qr)){
$template_name = strtolower($data['name']);    
$templates_cache[$template_name] = $data['content'];   
}
}


//------ style selection ---//
function print_style_selection(){
global $styleid;
$qr=db_query("select * from store_templates_cats where selectable=1 order by id asc");
if(db_num($qr)){
print "<select name=styleid onChange=\"window.location='index.php?styleid='+this.value;\">";
while($data =db_fetch($qr)){
print "<option value=\"$data[id]\"".iif($styleid==$data['id']," selected").">$data[name]</option>";
}
print "</select>";
}
}








function site_header(){
global $sitename,$phrases,$settings,$keyword,$action,$id,$op,$cat,$section_name,$sec_name,$meta_description,$meta_keywords,$title_sub;
                                             
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
//if($section_name){
//$sec_name = " -  $section_name" ;
       // }
   
 if(!$meta_description){ $meta_description= $settings['header_description']." , ".iif($title_sub,$title_sub,$sitename);}
 
if(!$meta_keywords){
if($title_sub){
$keys_arr = explode(" ",htmlspecialchars($title_sub));
if(count($keys_arr)){
    foreach($keys_arr as $value){
        
    if(trim($value)){$meta_keywords .= trim($value).",";}
    }
    unset($keys_arr);
}else{
    $meta_keywords = htmlspecialchars($keyword);
}
      }else{
      $meta_keywords = $sitename;
       }
  }
   
 // if($title_sub){ $title_sub = " -  $title_sub";}
 
compile_template(get_template('page_head'));
print "
<META name=\"Developer\" content=\"www.allomani.com\" />";
print "
</HEAD>
";

compile_template(get_template("header"));


print "<div id=\"status_bar\" class=status_bar>";
compile_template(get_template('status_bar'));
print "</div>";


print get_template("js_functions");
}

//---------- footer ------------//
function site_footer (){
compile_template(get_template("footer"));
}

//-------------- open block ------------------//
function open_block($table_title="",$template=""){
         
if(!$template){
     $block_template =  get_template("block") ;  
   
      }else{
         
            $block_template = get_template($template,"","",1) ;  
            $block_template = iif($block_template,$block_template,get_template("block"));
      }
      

       $theme['block'] = explode("{content}",$block_template) ; 
     
      $table_content = $theme['block'][0];
      
      
if($table_title){

        $table_content = str_replace("{title}","<center><span class=title>$table_title</span></center>", $table_content);
         $table_content = str_replace("{new_line}","<br>",$table_content);
        }else{
            $table_content = str_replace("{title}","", $table_content);
            $table_content = str_replace("{new_line}","",$table_content);
                }

print $table_content ;
}


//-------------- close block ---------------
function close_block($template=""){
if(!$template){
     $block_template =  get_template("block") ;  
   
      }else{
         
            $block_template = get_template($template,"","",1) ;  
            $block_template = iif($block_template,$block_template,get_template("block"));
      }
      

       $theme['block'] = explode("{content}",$block_template) ; 
  

     
      $table_content = $theme['block'][1] ;
      
      
if($table_title){

        $table_content = str_replace("{title}","<center><span class=title>$table_title</span></center>", $table_content);
         $table_content = str_replace("{new_line}","<br>",$table_content);
        }else{
            $table_content = str_replace("{title}","", $table_content);
            $table_content = str_replace("{new_line}","",$table_content);
                }

print $table_content ;
}



//----------- open table -------------//
function open_table($table_title="",$template=""){
 $template = iif($template,$template,"table");   
 open_block($table_title,$template);
}


//-------------- close_table ------------//
function close_table($template=""){
    $template = iif($template,$template,"table"); 
close_block($template);
}



?>