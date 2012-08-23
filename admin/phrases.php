<?
  if(!defined('IS_ADMIN')){die('No Access');}   

  
  //------------------------------ Phrases -------------------------------------
if($action=="phrases" || $action=="phrases_add_ok" || $action=="phrases_update"){

if_admin("phrases");

$cat = intval($cat);

if($action=="phrases_update"){
        $i = 0;
        foreach($phrases_ids  as $id){
            $phrases_ids[$i] = intval($phrases_ids[$i]);
        db_query("update store_phrases set value='".db_escape($phrases_values[$i],false)."' where id='$phrases_ids[$i]'");

        ++$i;
                }
     }
     
if($action=="phrases_add_ok"){
    $name=trim($name);
    $value=trim($value);
    if($name && $value){
    $qrc = db_query("select id from store_phrases where name like '".db_escape($name)."'");
    if(!db_num($qrc)){
       
  db_query("insert into store_phrases (name,value,`cat`) values ('".db_escape($name)."','".db_escape($value)."','".db_escape($group)."')");
    }else{
        print_admin_table("<center>$phrases[phrases_name_exists]</center>");
    }
    }  
}
 


if($group){
 
$cat_data = db_qr_fetch("select name from store_phrases_cats where id='".db_escape($group)."'");

print "<p align=$global_align><img src='images/link.gif'><a href='index.php?action=phrases'>$phrases[the_phrases] </a> / $cat_data[name]</p>";


         $qr = db_query("select * from store_phrases where cat='".db_escape($group)."'");
         print "<center>
         <form action='index.php' method=post>
         <input type=hidden name='action' value='phrases_add_ok'>
         <input type=hidden name='group' value='".htmlspecialchars($group)."'>
           
          
         <table width=70% class=grid>
         <tr><td><b>$phrases[the_name]</b></td><td><input type=text size=30 name='name'></td>
         <td rowspan=2><input type=submit value='$phrases[add]'></td></tr>
         <tr><td><b>$phrases[the_value]</b></td><td><input type=text size=30 name='value'></td></tr>
         </table></form></center><br>";
         
        if (db_num($qr)){

        print "<form action=index.php method=post>
        <input type=hidden name=action value='phrases_update'>
        <input type=hidden name=group value='".htmlspecialchars($group)."'>
        <center><table width=90% class=grid>";

        $i = 0;
        while($data=db_fetch($qr)){
            
        if($tr_class == "row_1"){
            $tr_class = "row_2";
        }else{
            $tr_class = "row_1";
        }
        
         print "<tr class='$tr_class'><td>$data[name]</td><td>
         <input type=hidden name=\"phrases_ids[$i]\" value='$data[id]'>
         <input type=text name=\"phrases_values[$i]\" value=\"".htmlspecialchars($data['value'])."\" size=50>
         </td></tr> ";
         ++$i;
                }
                print "<tr><td colspan=2 align=center><input type=submit value=' $phrases[edit] '></td></tr>
                </table></form></center>";
                }else{
                     print "<center><table width=60% class=grid><tr><td align=center> $phrases[cp_no_phrases] </td></tr></table></center>";
                     }

}else{
print "<p class=title align=center> $phrases[the_phrases] </p><br>  ";
    $qr = db_query("select * from store_phrases_cats order by id asc");
     print "<center><table width=60% class=grid>";
    while($data =db_fetch($qr)){
    print "<tr><td><a href='index.php?action=phrases&group=$data[id]'>$data[name]</a></td></tr>";
    }
    print "</table></center>";
}
}