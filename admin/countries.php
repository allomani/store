<?
if(!defined('IS_ADMIN')){die('No Access');}  

if($action=="countries" || $action=="countries_disable" || $action=="countries_enable"){
 
 if_admin();
    
   if($action=="countries_disable"){
   db_query("update store_countries set active=0 where id='$id'");
   }
  
  if($action=="countries_enable"){
   db_query("update store_countries set active=1 where id='$id'");
   }
     
    
    $qr = db_query("select * from store_countries order by name asc");
    print "<table width=100% class='grid'>";
    while($data=db_fetch($qr)){
        if($tr_class=="row_1"){$tr_class="row_2";}else{$tr_class="row_1";}
        
    print "<tr class='$tr_class'><td width=10><input type='checkbox' name='id[]' value='$data[id]'></td>
    <td><a href='index.php?action=countries_edit&id=$data[id]'>$data[name]</a></td>
    <td>$data[code]</td>
   
    <td align=center>";
    if($data['active']){
                        print "<a href='index.php?action=countries_disable&id=$data[id]'>$phrases[disable]</a>" ;
                        }else{
                        print "<a href='index.php?action=countries_enable&id=$data[id]'>$phrases[enable]</a>" ;
                        }
    print "</td> 
    </tr>";    
    }
    print "</table>";
    
}


//------------ edit ---------------
if($action=="countries_edit" || $action=="countries_edit_ok"){

if_admin();

if($action=="countries_edit_ok"){
    db_query("update store_countries set name='".db_escape($name)."',code='".db_escape($code)."' where id='$id'");
}


$qr = db_query("select * from store_countries where id='$id'");
if(db_num($qr)){
 $data = db_fetch($qr);   
    print "<a href='index.php?action=cities&cat=$id'>Cities</a>";
 
 print "
 <form action='index.php' method='post'>
 <input type='hidden' name='action' value='countries_edit_ok'>
 <input type='hidden' name='id' value='$id'>
 
 <table width=100% class='grid'>
 <tr><td><b>$phrases[the_name]</b></td><td><input type=text name='name' value=\"$data[name]\"></td></tr>
 <tr><td><b>ISO</b></td><td><input type=text name='code' value=\"$data[code]\"></td></tr>
 
 <tr><td colspan=2 align=center><input type=submit value=\"$phrases[edit]\"></td></tr>
 </table>
 </form>";
}else{
print_admin_table("<center>$phrases[err_wrong_url]</center>");
}   
}


