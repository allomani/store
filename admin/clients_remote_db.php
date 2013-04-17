<?
 require('./start.php'); 
 //---------------------------------

//-------------- Remote Members Database ---------------
   if(!$action || $action=="clients_remote_db"){
   if_admin();

print "<p align=center class=title> $phrases[cp_remote_members_db] </p>";

show_alert("$phrases[you_can_edit_this_values_from_config_file]","info");
print "<br><center><table width=60% class=grid><tr><td><b>$phrases[use_remote_db]</b></td><td>".($members_connector['enable'] ? $phrases['yes'] : $phrases['no'])."</td></tr>";
if($members_connector['enable']){
print "<tr><td><b>$phrases[db_host]</b></td><td>$members_connector[db_host]</td></tr>
<tr><td><b>$phrases[db_name]</b></td><td>$members_connector[db_name]</td></tr>
<tr><td><b>$phrases[members_table]</b></td><td>$members_connector[members_table]</td></tr>";
}
print "</table>
<br>
<fieldset >
<legend>$phrases[note]</legend>
$phrases[members_remote_db_wizzard_note]
</fieldset>
<br><br>
<form action='clients_remote_db.php' method=get>
<input type=hidden name=action value='clients_remote_db_wizzard'>
<input type=submit value=' $phrases[members_remote_db_wizzard] '>
</form></center>";

   }
 //------------ Members Remote DB Wizzard ---------------
 if($action=="clients_remote_db_wizzard"){
     if_admin();
print "<p align=center class=title>$phrases[members_remote_db_wizzard]</p>";


if($members_connector['enable']){
$conx  = @mysql_connect($members_connector['db_host'],$members_connector['db_username'],$members_connector['db_password']);
if($conx){
if(@mysql_select_db($members_connector['db_name'])){




//---------------- STEP 1 : CHECK TABLES FIELDS ---------------
  $tables_ok = 1 ;
 if(is_array($required_database_fields_names)){


 $qr = members_db_query("SHOW FIELDS FROM {{store_clients}}");
  $c=0;
while($data =members_db_fetch($qr)){

    $table_fields['name'][$c] = $data['Field'];
    $table_fields['type'][$c] = $data['Type'];
    $c++;
    }

print "<center><br><table width=80% class=grid>";
for($i=0;$i<count($required_database_fields_names);$i++){
    
//--------- Neme TD ------
print "<tr><td>".$required_database_fields_names[$i]."</td>";
//------- Type TD  ---------
if(is_array($required_database_fields_types[$i])){$req_type = $required_database_fields_types[$i];}else{$req_type=array($required_database_fields_types[$i]);}

print "<td>";
foreach($req_type as $value){
    print "$value &nbsp;";
    }
    print "</td><td>";
//----------------------------

$searchkey =  array_search($required_database_fields_names[$i],$table_fields['name']);
if($searchkey){


if(in_array($table_fields['type'][$searchkey],$req_type)){
print "<b><font color=green>Valid</font></b>";
}else{
print "<b><font color=red>Not Valid Type</font></b>";
$qrx = members_db_query("ALTER TABLE {{store_clients}} CHANGE `".$required_database_fields_names[$i]."` `".$required_database_fields_names[$i]."` ".$req_type[0]." NOT NULL ;");

    if(!$qrx){
    print "<td><b><font color=red> $phrases[chng_field_type_failed] </font></b></td>";
        $tables_ok = 0;
        }else{
        print "<td><b><font color=green> $phrases[chng_field_type_success] </font></b></td>";
            }
            unset($qrx);
    }
print "</td>";
    }else{
    print "<td><b><font color=red>Not found</font></b></td>";

    $qrx = members_db_query("ALTER TABLE {{store_clients}} ADD `".$required_database_fields_names[$i]."` ".$req_type[0]." NOT NULL ;");

    if(!$qrx){
    print "<td><b><font color=red> $phrases[add_field_failed] </font></b></td>";
        $tables_ok = 0;
        }else{
        print "<td><b><font color=green>$phrases[add_field_success] </font></b></td>";
            }
            unset($qrx);
        }
        }
        print "</table></center><br>";
        }
        //----------- end tables check -----------
        if($tables_ok){
        show_alert($phrases['members_remote_db_compatible'],"success");
            }else{
            show_alert($phrases['members_remote_db_uncompatible'],"warning");
                }
        //--------- clean local db note ------------
        print "<center> <br>
<fieldset >
<legend>$phrases[note]</legend>
$phrases[members_local_db_clean_note]
</fieldset>
<br><br>
<form action='clients_remote_db.php' method=get>
<input type=hidden name=action value='clients_local_db_clean'>
<input type=submit value=' $phrases[members_local_db_clean_wizzard] '>
</form></center>";

        }else{
        show_alert($phrases['wrong_remote_db_name'],"error");
            }
        }else{
            show_alert($phrases['wrong_remote_db_connect_info'],"error");
            }
        }else{
        show_alert($phrases['members_remote_db_disabled'],"error");
            }
 }

 //-------------- Clean Members Local DB -------------
 if($action=="clients_local_db_clean"){
     if_admin();
 print "<p align=center class=title> $phrases[members_local_db_clean_wizzard] </p>
 <center><table width=70% class=grid><tr><td>";
 if($process){
 db_query("TRUNCATE TABLE `store_clients_favorites`");
 db_query("TRUNCATE TABLE `store_clients_msgs`");
 db_query("TRUNCATE TABLE `store_confirmations`");


  print "<center><b> $phrases[process_done_successfully]</b></center>";
 }else{
 print "<br> <b>$phrases[members_local_db_clean_description]
 <ul>
 <li>$phrases[members_msgs_table]</li>
 <li>$phrases[members_favorite_table]</li>
 <li>$phrases[members_custom_fields_table]</li>
 <li>$phrases[members_confirmations_table]</li>

 </ul></b>
 <center>
 <form action='clients_remote_db.php' method=post>
 <input type=hidden name=action value='clients_local_db_clean'>
 <input type=hidden name=process value='1'>
 <input type=submit value=' $phrases[do_button] ' onClick=\"return confirm('$phrases[are_you_sure]');\">
 </form>
 </center>";
 }
 print "</td></tr></table></center>";


 }
 
//-----------end ----------------
 require(ADMIN_DIR.'/end.php');
?>
