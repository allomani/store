<?
require('./start.php');  

if(!$action){
if_admin(); 

print "<p align=center class=title>$phrases[seo_settings]</p>";

print "<center><table width=60% class=grid>
<tr>
<td align=center><a href='seo.php?action=meta_settings'>$phrases[pages_meta_settings]</a></td>
<td align=center><a href='seo.php?action=links_settings'>$phrases[pages_links_settings]</a></td>  
</tr></table>";
   
}


//----------- Meta --------------
if($action=="meta_settings" || $action=="meta_settings_update"){
if_admin(); 
  
 print "<img src='images/arrw.gif'>&nbsp; <a href='seo.php'>$phrases[seo_settings]</a> / $phrases[pages_meta_settings] <br><br>"; 
   

if($action=="meta_settings_update"){
for($i=0;$i<count($values);$i++) {
    $cur = current($values);
db_query("update store_meta set title='".db_escape($cur['title'])."',description='".db_escape($cur['description'])."',keywords='".db_escape($cur['keywords'])."' where name='".db_escape(key($values))."'");
 next($values);
}    
}



$qr=db_query("select * from store_meta order by name asc");
         print "<center>
         <form action='seo.php' method='post'>
         <input type='hidden' name='action' value='meta_settings_update'> ";
         
while($data=db_fetch($qr)){
    print "<table width=60% class=grid>
    <tr><td colspan=2><b>$data[name]</b></td></tr>
                              <tr><td><b>$phrases[the_title] : </td><td>
                              <input type=text size=50 dir=ltr name=\"values[$data[name]][title]\" value=\"$data[title]\"></td></tr>
                              
                              <tr><td><b>$phrases[the_description] : </td><td>
                              <textarea dir=ltr name=\"values[$data[name]][description]\" cols=50 rows=4>$data[description]</textarea></td></tr>
                              
                              <tr><td><b>$phrases[the_page_keywords] : </td><td>
                            <textarea dir=ltr name=\"values[$data[name]][keywords]\" cols=50 rows=4>$data[keywords]</textarea></td></tr>
                              
                               </table><br>";
}

print "<input type=submit value='$phrases[edit]'>
</form></center>";

}

//---------- Links ----------------
if($action=="links_settings" || $action=="links_settings_update"){

if_admin();

 print "<img src='images/arrw.gif'>&nbsp; <a href='seo.php'>$phrases[seo_settings]</a> / $phrases[pages_links_settings] <br><br>"; 
   
   
if($action=="links_settings_update"){
 for($i=0;$i<count($links_values);$i++) {
db_query("update store_links set value='".db_escape(current($links_values))."' where name='".db_escape(key($links_values))."'");
 next($links_values);
}
}


$qr=db_query("select * from store_links order by name asc");
print "<center>
<form action='seo.php' method='post'>
<input type='hidden' name='action' value='links_settings_update'>

<table width=90% class=grid>";
while($data=db_fetch($qr)){
    print "<tr><td>$data[name]</td><td><input type='text' name=\"links_values[$data[name]]\" value=\"$data[value]\" size=50 dir=ltr></td></tr>";
}
print "<tr><td colspan=2 align=center><input type='submit' value='$phrases[edit]'></td></tr>
</table>
</form>
</center>";
    
}

//-----------end ----------------
 require(ADMIN_DIR.'/end.php');