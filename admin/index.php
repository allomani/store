<?
require('./start.php');   

//----------------------Start -------------------------------------------------------
if(!$action){
    
//-------- counter file ---------
require(CWD . "/counter.php");


  $count_products = db_qr_fetch("select count(id) as count from store_products_data");

   $data4 = db_qr_fetch("select count(id) as count from store_user");
   
   $count_members = members_db_qr_fetch("select count(::id) as count from {{store_clients}}");

print "<center><table class=grid style='width:50%;'><tr><td align=center><b>$phrases[welcome_to_cp] <br><br>";


   if($global_lang=="arabic"){
  print "مرخص لـ : $_SERVER[HTTP_HOST]" ;
  if(COPYRIGHTS_TXT_ADMIN){
  	print "   من <a href='http://allomani.com/' target='_blank'>  اللوماني للخدمات البرمجية </a> " ;
  	}

  	print "<br><br>

   إصدار : ".SCRIPT_VER." <br><br>";
  }else{
  print "Licensed For : $_SERVER[SERVER_NAME]" ;
  if(COPYRIGHTS_TXT_ADMIN){
  	print "   By  <a href='http://allomani.com/' target='_blank'>Allomani&trade;</a> " ;
  	}

  	print "<br><br>

   Version : ".SCRIPT_VER." <br><br>";
  	}

  print "$phrases[cp_statics] : </b><br> $phrases[products_count] : $count_products[count] <br>  
  $phrases[clients_count] : $count_members[count] <br>
   $phrases[users_count] : $data4[count] </font></td></tr></table></center>";

  print "<br><center><table class=grid style='width:50%;'><td align=center>";
    
    print "<span dir='$global_dir'><b>$phrases[php_version] : </b></span> <br><span dir='ltr'>".@phpversion()." </span><br><br> ";

      print "<b><span dir=$global_dir>$phrases[mysql_version] :</span> </b><br><span dir=ltr>" .db_server_info() ."</span><br><br>";
  
   if(extension_loaded('ionCube Loader')){
   print "<b><span dir=$global_dir>$phrases[ioncube_version] :</span> </b><br><span dir=ltr>" . ioncube_loader_version()  ."</span><br><br>";
    }

   if(@function_exists("gd_info")){
   $gd_info = @gd_info();
   print "
   <b>  $phrases[gd_library] : </b> <font color=green> $phrases[cp_available] </font><br>
  <b>$phrases[the_version] : </b> <span dir=ltr>".$gd_info['GD Version'] ."</span>";
  }else{
  print "
  <b>  $phrases[gd_library] : </b> <font color=red> $phrases[cp_not_available] </font><br>
  $phrases[gd_install_required] ";
          }
          
 
   
   print "</td></tr></table>";

     if(if_admin("reports",true)){  
    print "<br>";  
 $reports_cnt = db_qr_fetch("select count(*) as count from store_reports where opened=0");  
 print_admin_table("$phrases[new_reports] : <a href='reports.php'>$reports_cnt[count]</a>");
   }
   
 
    if(if_admin("comments",true)){   
  print "<br>";  
 $comments_cnt = db_qr_fetch("select count(*) as count from store_comments where active=0");  
 print_admin_table("$phrases[comments_waiting_admin_review] : <a href='comments.php'>$comments_cnt[count]</a>");
    }
 
    
    

  print "<br><center><table class=grid style='width:50%;'><td align=center>
  <p><b> $phrases[cp_addons] </b></p>";

   //--------------- Check Installed Plugins --------------------------
$dhx = @opendir(CWD ."/plugins");
  $plgcnt = 0 ;
while ($rdx = @readdir($dhx)){
         if($rdx != "." && $rdx != "..") {
                 $cur_fl = CWD ."/plugins/" . $rdx . "/index.php" ;
        if(@file_exists($cur_fl)){
                print $rdx ."<br>" ;
                $plgcnt = 1 ;
                }
          }

    }
@closedir($dhx);
if(!$plgcnt){
	print "<center> $phrases[no_addons] </center>";
	}
 print "</td></tr></table>";

if($global_lang=="arabic"){
    print "<br><center><table class=grid style='width:50%;'><td align=center>
     يتصفح الموقع حاليا $counter[online_users] زائر
                                               <br><br>
   أكبر تواجد كان  $counter[best_visit] في : <br> $counter[best_visit_time] <br></td></tr></table>";
 }else{
 	    print "<br><center><table class=grid style='width:50%;'><td align=center>
     Now Browsing : $counter[online_users] Visitor
                                               <br><br>
   Best Visitors Count : $counter[best_visit] in : <br> $counter[best_visit_time] <br></td></tr></table>";

 	}
   }



//-------- Geo --------
require(ADMIN_DIR ."/countries.php");
require(ADMIN_DIR ."/cities.php");
require(ADMIN_DIR ."/geo_zones.php"); 






 
//------------------- DATABASE BACKUP --------------------------
if($action=="backup_db_do"){
    $output = htmlspecialchars($output) ;
print "<br><center> <table class=grid style='width:50%;'><tr><td align=center>  $output </td></tr></table>";
}

  if($action=="backup_db"){

   if_admin();
      print "<br><center>
      <p align=center class=title> $phrases[cp_db_backup] </p>

      <form action=index.php method=post>
      <input type=hidden name=action value='backup_db_do'>
      <table width=50% class=grid><tr><td>
      <input type=\"radio\" name=op value='local' checked onclick=\"document.getElementById('backup_server').style.display = 'none';\"> $phrases[db_backup_saveto_pc]
      <br><input type=\"radio\" name=op value='server' onclick=\"document.getElementById('backup_server').style.display = 'inline';\" > $phrases[db_backup_saveto_server]
      </td></tr>
      <tr><td>
      <div id=backup_server style=\"display: none; text-decoration: none\">
      <b> $phrases[the_file_path] : &nbsp; </b> <div dir=ltr>data/backups/<input type=text name=filename dir=ltr size=40 value='store_".date("d-m-Y-h-i-s").".sql.gz'></div>
      </div>
     </td></tr><tr> <td align=center>
      <input type=submit value=' $phrases[cp_db_backup_do] '>
      </form></td></tr></table></center>";

          }
// ----------------- Repair Database -----------------------

if($action=="db_info"){

    if_admin();

if(!$disable_repair){


        $tables = db_query("SHOW TABLE STATUS");
        print "<form name=\"form1\" method=\"post\" action=\"index.php\"/>
        <input type=hidden name=action value='repair_db_ok'>
        <center><table  class=grid>";
        print "<tr><td colspan=\"5\"> <font size=4><b>$phrases[the_database]</b></font> </td></tr>
        <tr><td></td>
        ";
        print "<td><b>$phrases[the_table]</b></td><td align=left><b>$phrases[the_size]</b></td>
        <td align=center><b>$phrases[the_status]</b></td>
            </tr>";
        while($table = db_fetch($tables))
        {
            $size = round($table['Data_length']/1024, 2);
            $status = db_qr_fetch("ANALYZE TABLE `$table[Name]`");
            print "<tr>
            <td width=\"5%\"><input type=\"checkbox\" name=\"check[]\" value=\"$table[Name]\" checked=\"checked\" /></td>
            <td width=\"50%\">$table[Name]</td>
            <td width=\"10%\" align=left dir=ltr>$size KB</td>
            <td align=center>$status[Msg_text]</td>
            </tr>";
        }

        print "</table>
             <table width=100% class='grid'><tr>
          <td width=2><img src='images/arrow_".$global_dir.".gif'></td>   
          <td>

          <a href='#' onclick=\"CheckAll('form1'); return false;\"> $phrases[select_all] </a> -
          <a href='#' onclick=\"UncheckAll('form1'); return false;\">$phrases[select_none] </a> 
          &nbsp;&nbsp; 
          
<input type=\"submit\" name=\"submit\" value=\"$phrases[db_repair_tables_do]\" /></center> <br>
    </td></tr>
    </table>
        </form>";
        }else{
              print_admin_table("<center> $disable_repair </center>") ;
            }
    }
//------------------------------------------------
    if($action=="repair_db_ok"){
       if_admin();

    if(!$disable_repair){
        if(!$check){
            print "<center><table width=50% class=grid><tr><td align=center> $phrases[please_select_tables_to_rapair] </td></tr></table></center>";
    }else{
        $tables = $_POST['check'];
        print "<center><table width=\"60%\"  class=grid>";

        foreach($tables as $table)
        {
            $query = db_query("REPAIR TABLE `". $table . "`");
            $que = db_fetch($query);
            print "<tr><td>{$que['Table']}</td>
              <td>".iif($que['Msg_text']=="OK","<span style='color:green;font-weight:bold;'>$phrases[done]</span>","<span style='color:red;font-weight:bold;'>{$que['Msg_text']}</span>")."
           </td></tr>";
        }

        print "</table></center>";

        }

        }else{
              print_admin_table("<center> $disable_repair </center>") ;
            }
    }




//---------------------------------- Statics ---------------------
if($action=="statics"){
        if_admin();


                if($op){
     print "<center><table width=50% class=grid>
<tr><td><ul>";
  foreach($op as $op){
 //---------------------
 if($op=="statics_rest"){
        db_query("delete from info_hits");
        db_query("update info_browser set count=0");
        db_query("update info_os set count=0");
        db_query("update info_best_visitors  set v_count=0");
        print "<li>$phrases[visitors_statics_rest_done]</li>" ;
                }

 //---------------------
          }
          print "</ul></td></tr></table>";
          }
$data_frstdate = db_qr_fetch("select * from info_hits order by date asc limit 1");
 if(!$data_frstdate['date']){$data_frstdate['date']= "$phrases[cp_not_available]"; }
 $qr_total=db_query("select hits from info_hits");
 $total_hits = 0 ;
 while($data_total = db_fetch($qr_total)){
 $total_hits += $data_total['hits'];
         }

print "<center><p class=title> $phrases[cp_visitors_statics] </p>
<table width=50% class=grid>
<tr><td><b> $phrases[cp_counters_start_date] </b></td><td>$data_frstdate[date]
</td></tr>
<tr><td><b> $phrases[cp_total_visits] </b></td><td>$total_hits
</td></tr>
</table>
<br>
 <p class=title>  $phrases[cp_rest_counters] </p>
<form action='index.php' method=post onSubmit=\"return confirm('$phrases[are_you_sure]');\">
<input type=hidden name=action value='statics'>
<table width=50% class=grid><tr><td>
<input type='checkbox' value='statics_rest'  name='op[]' >$phrases[cp_visitors_statics]<br><br>



</td></tr><tr><td align=center>
<input type=submit value=' $phrases[cp_rest_counters_do] '>
</table></center>
</form>";
        }
        

      

 //-------------------- Access Log -------------
 if($action=="access_log"){
     if_admin();
     
     $qr=db_query("select * from store_access_log order by id desc");
     print "<center>
     <p class=title>$phrases[access_log]</p>
     <table class=grid>";
       print "<tr><td><b>$phrases[username]</b></td><td><b>$phrases[the_date]</b></td><td><b>$phrases[the_status]</b></td><td><b>IP</b></td></tr>";   
     while($data = db_fetch($qr)){
         toggle_tr_class();
         print "<tr class='$tr_class'><td>$data[username]</td><td>$data[date]</td><td>$data[status]</td><td>$data[ip]</td></tr>";
     }
     print "</table></center>";
 }

 //-----------end ----------------
 require(ADMIN_DIR.'/end.php');