<?
require(CWD ."/includes/functions_meta.php");
$templates_cache = array(); 
                


//----------- get template ----------------
function get_template($name,$tfind="",$treplace=""){
 global $styleid,$templates_cache ;

$name=strtolower($name) ;

if(isset($templates_cache[$name])){
    
    $content = $templates_cache[$name] ;
}else{  
$cached_template = cache_get("template:$styleid:$name");

if($cached_template === false){
 
$qr = db_query("select content from store_templates where name like '$name' and cat='$styleid'");
 
 if(db_num($qr)){
     
     $data = db_fetch($qr);
    $content = $data['content'] ;
    $templates_cache[$name] = $data['content'];
    cache_set("template:$styleid:$name",$data['content']);
    unset($data);
   }else{
   $content =  "<b>Error : </b> Template ".htmlspecialchars($name)." Not Exists <br>";
       }
}else{
  
 $content =  $cached_template;  
 
}
}

return iif(($tfind || $tfind=="0")&&($treplace || $treplace=="0"),str_replace($tfind,$treplace,$content),$content) ;

}

function run_template($name,$tfind="",$treplace=""){
  
  $result =  run_php(get_template($name,$tfind,$treplace));
  if ( $result === false && ( $error = error_get_last() ) ) {
            print "<p><b> error: </b> $error[message] in <b>\"$name\" template</b> on line <b> $error[line]</b> </p>";
        } 
}

//---------------------- Run Template -------------------
function compile_template($template)
{
 run_php($template);       
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
global $theme,$sitename,$phrases,$settings,$keyword,$action,$id,$op,$cat,$section_name,$sec_name,$meta_description,$meta_keywords,$title_sub,$album_id,$global_dir;
              
set_meta_values();

 
run_template('page_head');
if(COPYRIGHTS_TXT_ADMIN){ 
print "
<META name=\"Developer\" content=\"www.allomani.com\" >";
}
print "
</head>
";

//----------------- Disable Browsing ------------------
if(!$settings['enable_browsing']){
if(check_admin_login()){
print "<table width=100% dir=\"$global_dir\"><tr><td><font color=red> $phrases[site_closed_for_visitors] </font></td></tr></table>";
}
}
//----------------------------------------------------------


run_template("header");


}

function site_footer(){
run_template('footer');
}

//-------------- open block ------------------//
function open_block($table_title="",$template=""){
         
if(!$template){
    $template = "block";
     $block_template =  get_template("block") ;  
   
      }else{
         
            $block_template = get_template($template,"","",1) ;  
            $block_template = iif($block_template,$block_template,get_template("block"));
      }
      

       $theme['block'] = explode("{content}",$block_template) ; 
     
      $table_content = $theme['block'][0];
      
      
if($table_title){

        $table_content = str_replace("{title}","<center><span class='".iif($template=="block","block_title title",iif($template=="table","table_title title","title"))."'>$table_title</span></center>", $table_content);
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