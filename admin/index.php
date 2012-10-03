<?
require('./start.php');   

//----------------------Start -------------------------------------------------------
if(!$action){
    
//-------- counter file ---------
require(CWD . "/counter.php");


  $count_products = db_qr_fetch("select count(id) as count from store_products_data");

   $data4 = db_qr_fetch("select count(id) as count from store_user");
   
   $count_members = db_qr_fetch("select count(".members_fields_replace("id").") as count from ".members_table_replace("store_clients"),MEMBER_SQL);

print "<center><table width=50% class=grid><tr><td align=center><b>$phrases[welcome_to_cp] <br><br>";


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

  print "<br><center><table width=50% class=grid><td align=center>";
    
    print "<span dir='$global_dir'><b>$phrases[php_version] : </b></span> <br><span dir='ltr'>".@phpversion()." </span><br><br> ";

      print "<b><span dir=$global_dir>$phrases[mysql_version] :</span> </b><br><span dir=ltr>" .@mysql_get_server_info() ."</span><br><br>";
  
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

   
      if(if_admin("comments",true)){   
  print "<br>";  
 $comments_cnt = db_qr_fetch("select count(*) as count from store_comments where active=0");  
 print_admin_table("$phrases[comments_waiting_admin_review] : <a href='comments.php'>$comments_cnt[count]</a>");
    }
 
    
    

  print "<br><center><table width=50% class=grid><td align=center>
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
    print "<br><center><table width=50% class=grid><td align=center>
     يتصفح الموقع حاليا $counter[online_users] زائر
                                               <br><br>
   أكبر تواجد كان  $counter[best_visit] في : <br> $counter[best_visit_time] <br></td></tr></table>";
 }else{
 	    print "<br><center><table width=50% class=grid><td align=center>
     Now Browsing : $counter[online_users] Visitor
                                               <br><br>
   Best Visitors Count : $counter[best_visit] in : <br> $counter[best_visit_time] <br></td></tr></table>";

 	}
   }


//------------ Votes ---------
require(ADMIN_DIR ."/votes.php");
 //------------- News --------
// require(ADMIN_DIR ."/news.php");
//---------- Members --------
require(ADMIN_DIR ."/clients.php");
//-------- Payments Methods --------
require(ADMIN_DIR ."/payment_methods.php");

//-------- Shipping Methods --------
require(ADMIN_DIR ."/shipping_methods.php");

//-------- Geo --------
require(ADMIN_DIR ."/countries.php");
require(ADMIN_DIR ."/cities.php");
require(ADMIN_DIR ."/geo_zones.php"); 

//------- Settings ------------
require(ADMIN_DIR ."/settings.php");

//------- Phrases ------------
require(ADMIN_DIR ."/phrases.php");

//------- Templates ------------
//require(ADMIN_DIR ."/templates.php");


//-------------------- Permisions------------------------
if($action=="permisions"){

    if_admin();
    $data =db_qr_fetch("select * from store_user where id='$id'");     
    
    print "<img src='images/arrw.gif'>&nbsp;<a href='index.php?action=users'>$phrases[the_users]</a> / $phrases[permissions_manage]  / $data[username] <br><br>
    <form method=post action=index.php>
           <input type=hidden value='$id' name='user_id'>
               <input type=hidden value='permisions_edit' name='action'>";


         print "<center><span class=title>$phrases[permissions_manage]</span><br><br>
           <table cellpadding=\"0\" border=0 cellspacing=\"0\" width=\"80%\" class=\"grid\">

        <tr><td>
        <center><span class=title>$phrases[cats_permissions]</span> <br><br>
$phrases[cats_permissions_note]";
          
           print " </center></td></tr>
           </table><br>";

     //------------------------------------------------------------------------------

    

    


      print "<table cellpadding=\"0\" border=0 cellspacing=\"0\" width=\"80%\" class=\"grid\">
     <tr> <td colspan=5 align=center><span class=title>$phrases[cp_sections_permissions]</span></td></tr>
            <tr><td><table width=100%><tr>";

            $prms = explode(",",$data['cp_permisions']);
                      

  if(is_array($permissions_checks)){

  $c=0;
 for($i=0; $i < count($permissions_checks);$i++) {

        $keyvalue = current($permissions_checks);

if($c==4){
    print "</tr><tr>" ;
    $c=0;
    }

if(in_array($keyvalue,$prms)){$chk = "checked" ;}else{$chk = "" ;}

print "<td width=25%><input  name=\"cp_permisions[$i]\" type=\"checkbox\" value=\"$keyvalue\" $chk>".key($permissions_checks)."</td>";


$c++ ;

 next($permissions_checks);
}
  }
print "</tr></table></td>

            </tr></table>";

          print "<center> <br><input type=submit value='$phrases[edit]'></form>" ;

        }
//---------------------------- Users ------------------------------------------
if ($action == "users" or $action=="edituserok" or $action=="adduserok" or $action=="deluser" || $action=="permisions_edit"){


if($action=="permisions_edit"){

        if_admin();

$user_id = intval($user_id);

if($cp_permisions){
foreach ($cp_permisions as $value) {
       $perms .=  "$value," ;
     }
       }else{
               $perms = '' ;
               }

 db_query("update store_user set cp_permisions='$perms' where id='$user_id'");
 

           }

        //---------------------------------------------
        if ($action=="deluser" && $id){
        if($user_info['groupid']==1 ){
db_query("delete from store_user where id='$id'");
}else{
        print_admin_table("<center>$phrases[access_denied]</center>");
                          die();
        }
        }
        //---------------------------------------------
        if ($action == "adduserok"){
        if($user_info['groupid']==1){
                if(trim($username) && trim($password)){
                if(db_qr_num("select username from store_user where username='".db_escape($username,false)."'")){
                        print "<center> $phrases[cp_err_username_exists] </center>";
                        }else{
        db_query("insert into store_user (username,password,email,group_id) values ('".db_escape($username,false)."','".db_escape($password,false)."','".db_escape($email)."','".intval($group_id)."')");
        }
        }else{
                print "<center>  $phrases[cp_plz_enter_usr_pwd] </center>";
                }
                }else{
                          print_admin_table("<center>$phrases[access_denied]</center>");
                          die();
        }
        }
        //------------------------------------------------------------------------------
        if ($action == "edituserok"){
                if ($password){
                $ifeditpassword = ", password='".db_escape($password,false)."'" ;
                }

        if ($user_info['groupid'] == 1){
        db_query("update store_user set username='".db_escape($username,false)."'  , email='".db_escape($email)."' ,group_id='".intval($group_id)."' $ifeditpassword where id='$id'");
        }else{
         if($user_info['id'] == $id){
        db_query("update store_user set username='".db_escape($username,false)."'  , email='".db_escape($email)."'  $ifeditpassword where id='$id'");

                 }else{
                   print_admin_table("<center>$phrases[access_denied]</center>");
                   die(); 
                         }
                }
     
                print "<center>  $phrases[cp_edit_user_success]  </center>";
       
        }

if ($user_info['groupid'] == 1){
print "<img src='images/add.gif'><a href='index.php?action=useradd'>$phrases[cp_add_user]</a>";

//----------------------------------------------------
     print "<p align=center class=title>$phrases[the_users]</p>";
       $result=db_query("select * from store_user order by id asc");


  print " <center> <table cellpadding=\"0\" border=0 cellspacing=\"0\" width=\"80%\" class=\"grid\">

        <tr>
             <td height=\"18\" width=\"134\" valign=\"top\" align=\"center\">$phrases[cp_username]</td>
                <td height=\"18\" width=\"240\" valign=\"top\">
                <p align=\"center\">$phrases[cp_email]</td>
                <td height=\"18\" width=\"105\" valign=\"top\">
                <p align=\"center\">$phrases[cp_user_group]</td>
                <td height=\"18\" width=\"193\" valign=\"top\" colspan=2>
                <p align=\"center\">$phrases[the_options]</td>
        </tr>";

      while($data = db_fetch($result)){


        if ($data['group_id']==1){$groupname="$phrases[cp_user_admin]";
             $permision_link="";
      }elseif($data['group_id']==2){$groupname="$phrases[cp_user_mod]";
       $permision_link="<a href='index.php?action=permisions&id=$data[id]'>$phrases[permissions_manage]</a>";

      }


        print "<tr>
                <td  width=\"134\" >
                <p align=\"center\">$data[username]</p></td>
                <td  width=\"240\" >
                <p align=\"center\">$data[email]</p></td>
                <td  width=\"105\"><p align=\"center\">$groupname</p></td>
                 <td  width=\"105\"><p align=\"center\">$permision_link</p></td>
                <td  width=\"193\"><p align=\"center\">
                 <a href='index.php?action=edituser&id=$data[id]'> $phrases[edit] </a> ";
        if ($data['id'] !="1"){
                print "- <a href='index.php?action=deluser&id=$data[id]' onClick=\"return confirm('".$phrases['are_you_sure']."');\"> $phrases[delete] </a>";
        }
                print " </p>
                </td>
        </tr>";
          }

print "</table></center>\n";




        }else{

                print "<br><center><table width=70% class=grid><tr><td align=center>
                $phrases[edit_personal_acc_only] <br>
                <a href='index.php?action=edituser'> $phrases[click_here_to_edit_ur_account] </a>
                </td></tr></table></center>";
        }
        }
//-------------------------Edit User------------------------------------------

if ($action=="edituser"){
       $id = intval($id);

if($user_info['groupid']!=1){
        $id=$user_info['id'];
}

$qr=db_query("select * from store_user where id='$id'") ;
if (db_num($qr)){

$data = db_fetch($qr) ;

print "<img src='images/arrw.gif'>&nbsp;<a href='index.php?action=users'>$phrases[the_users]</a> / $data[username] <br><br>


<center>
<FORM METHOD=\"post\" ACTION=\"index.php\">

 <TABLE width=70% class=grid>
    <TR>

    <INPUT TYPE=\"hidden\" NAME=\"id\" \" value=\"$data[id]\" >
<INPUT TYPE=\"hidden\" NAME=\"action\"  value=\"edituserok\" >

   <TD width=\"100\"><font color=\"#006699\"><b>$phrases[cp_username] : </b></font> </TD>
   <TD width=\"614\"><INPUT TYPE=\"text\" NAME=\"username\" size=\"32\" value=\"$data[username]\" > </TD>
  </TR>
    <TR>
   <TD width=\"100\"><font color=\"#006699\"><b>$phrases[cp_password] : </b></font> </TD>
   <TD width=\"614\"><INPUT TYPE=\"text\" NAME=\"password\" size=\"32\" onChange=\"passwordStrength(this.value);\" onkeyup=\"passwordStrength(this.value);\"> &nbsp; <input type=button value=\"Generate\" onClick=\"document.getElementById('password').value=GenerateAndValidate(12,1);passwordStrength(document.getElementById('password').value);\">
    <br>* $phrases[leave_blank_for_no_change] </TD>
  </TR>
  <tr><td></td><td>
<div id=\"passwordDescription\">-</div>
<div id=\"passwordStrength\" class=\"strength0\"></div>
</td></tr>
   <TR>
   <TD width=\"100\"><font color=\"#006699\"><b>$phrases[cp_email] : </b></font> </TD>
   <TD width=\"614\"><INPUT TYPE=\"text\" NAME=\"email\" size=\"32\" value=\"$data[email]\" > </TD>
  </TR>\n";

  if($user_info['groupid'] != 1){
          print "<input type='hidden' name='group_id' value='2'>";
  }else {
   print "<TR>
   <TD width=\"100\"><font color=\"#006699\"><b>$phrases[cp_user_group]: </b></font> </TD>
   <TD width=\"614\">\n";


if ($data['group_id'] == 1){$ifselected1 = "selected" ; }else{$ifselected2 = "selected";}

print "  <p><select size=\"1\" name=group_id>\n
        <option value='1' $ifselected1> $phrases[cp_user_admin] </option>
  <option value='2' $ifselected2>$phrases[cp_user_mod] </option>" ;


 print "  </select>";
  }

   print "</TD>
  </TR>


  <TR>
   <TD COLSPAN=\"2\" width=\"685\">
   <p align=\"center\"><INPUT TYPE=\"submit\" name=\"usereditbutton\" VALUE=\"$phrases[edit]\"></TD>
  </TR>
 </TABLE>
</FORM>
</center>\n";


}else{
    print "<center> $phrases[err_wrong_url]</center>" ;
    }
}
//--------------------- Add User Form -------------------------------------------------------
if($action=="useradd"){
print "   <img src='images/arrw.gif'>&nbsp;<a href='index.php?action=users'>$phrases[the_users]</a> / $phrases[add_button] <br><br>

   <center>

<form METHOD=\"post\" ACTION=\"index.php\">

 <TABLE width=\"70%\" class=grid>
    <TR>
   <td colspan=2 align=center><span class=title> $phrases[cp_add_user] </span></td></tr>
   <tr>
<INPUT TYPE=\"hidden\" NAME=\"action\"  value=\"adduserok\" >

   <TD width=\"150\"><font color=\"#006699\"><b>$phrases[cp_username]: </b></font> </TD>
   <TD ><INPUT TYPE=\"text\" NAME=\"username\" size=\"32\"  </TD>
  </TR>
    <TR>
   <TD width=\"150\"><font color=\"#006699\"><b>$phrases[cp_password] : </b></font> </TD>
   <TD ><INPUT TYPE=\"text\" NAME=\"password\" size=\"32\" onChange=\"passwordStrength(this.value);\" onkeyup=\"passwordStrength(this.value);\"> &nbsp; <input type=button value=\"Generate\" onClick=\"document.getElementById('password').value=GenerateAndValidate(12,1);passwordStrength(document.getElementById('password').value);\"> </TD>
  </TR>
  <tr><td></td><td>
<div id=\"passwordDescription\">-</div>
<div id=\"passwordStrength\" class=\"strength0\"></div>
</td></tr>
   <TR>
   <TD width=\"150\"><font color=\"#006699\"><b>$phrases[cp_email] : </b></font> </TD>
   <TD ><INPUT TYPE=\"text\" NAME=\"email\" size=\"32\" > </TD>
  </TR>

   <TR>
   <TD width=\"150\"><font color=\"#006699\"><b>$phrases[cp_user_group]: </b></font> </TD>
   <TD >\n";


print "  <p><select size=\"1\" name=group_id>\n
        <option value='1' > $phrases[cp_user_admin] </option>
  <option value='2' > $phrases[cp_user_mod]</option>" ;


 print "  </select>";


  print " </TD>
  </TR>


  <TR>
   <TD COLSPAN=\"2\" >
   <p align=\"center\"><INPUT TYPE=\"submit\" name=\"useraddbutton\" VALUE=\"$phrases[add_button]\"></TD>
  </TR>
 </TABLE>
</form>
</center><br><br>\n";
}

 //----------------------hooks ----------------------------
if($action=="hooks" || $action=="hook_disable" || $action=="hook_enable" || $action=="hook_add_ok" || $action=="hook_edit_ok" || $action=="hook_del" || $action=="hooks_fix_order"){


    if_admin();
//--------- hook add ---------------
if($action=="hook_add_ok"){
db_query("insert into store_hooks (name,hookid,code,ord,active) values (
'".db_escape($name)."',
'".db_escape($hookid)."',
'".db_escape($code,false)."',
'".intval($ord)."','1')");
}
//------- hook edit ------------
if($action=="hook_edit_ok"){
db_query("update store_hooks set
name='".db_escape($name)."',
hookid='".db_escape($hookid)."',
code='".db_escape($code,false)."',
ord='".intval($ord)."' where id='".intval($id)."'");
}
//--------- hook del --------
if($action=="hook_del"){
    db_query("delete from store_hooks where id='".intval($id)."'");
    }
//--------- enable / disable -----------------
if($action=="hook_disable"){
        db_query("update store_hooks set active=0 where id='".intval($id)."'");
        }

if($action=="hook_enable"){

       db_query("update store_hooks set active=1 where id='".intval($id)."'");
        }
//-------- fix order -----------
if($action=="hooks_fix_order"){

   $qr=db_query("select hookid,id from store_hooks order by hookid,ord ASC");
    if(db_num($qr)){
    $hook_c = 1 ;
    while($data = db_fetch($qr)){

    if($last_hookid !=$data['hookid']){$hook_c=1;}

    db_query("update store_hooks set ord='$hook_c' where id='$data[id]'");
     $last_hookid = $data['hookid'];
    ++$hook_c;
    }
     }
     unset($last_hookid);
     }
//---------------------------------------------


$qr =db_query("select * from store_hooks order by hookid,ord,active");

print "<center><p class=title> $phrases[cp_hooks] </p>

<p align=$global_align><a href='index.php?action=hook_add'><img src='images/add.gif' border=0> $phrases[add] </a></p>";

if(db_num($qr)){
              print "<table width=80% class=grid><tr>";

print "<tr><td><b>$phrases[the_name]</b></td><td><b>$phrases[the_order]</b></td><td><b>$phrases[the_place]</b></td><td><b>$phrases[the_options]</b></td></tr>";
while($data = db_fetch($qr)){

     if($last_hookid !=$data['hookid']){print "<tr><td colspan=4><hr class=separate_line></td></tr>";}

print "<tr><td>$data[name]</td><td><b>$data[ord]</b></td><td>$data[hookid]</td><td>";
 if($data['active']){
                        print "<a href='index.php?action=hook_disable&id=$data[id]'>$phrases[disable]</a>" ;
                        }else{
                        print "<a href='index.php?action=hook_enable&id=$data[id]'>$phrases[enable]</a>" ;
                        }

print "- <a href='index.php?action=hook_edit&id=$data[id]'>$phrases[edit] </a>
- <a href='index.php?action=hook_del&id=$data[id]' onClick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete] </a>
</td></tr>";


    $last_hookid = $data['hookid'];
    }

          print "</table>
 <br><form action='index.php' method=post>
                <input type=hidden name=action value='hooks_fix_order'>
                <input type=submit value=' $phrases[cp_hooks_fix_order] '>
                </form></center>";

}else{
print "<table width=80% class=grid><tr>
    <tr><td align=center>  $phrases[no_hooks] </td></tr>
    </table></center>";
    }

}

//-------- add hook -------
if($action=="hook_add"){

    if_admin();

print "<center>
<form action='index.php' method=post>
<input type=hidden name=action value='hook_add_ok'>
<table width=80% class=grid>
<tr><td><b>$phrases[the_name]</b></td><td><input type=text size=20 name=name></td></tr>
<tr><td><b>$phrases[the_place]</b></td><td>";
$hooklocations = get_plugins_hooks();
print_select_row("hookid",$hooklocations,"","dir=ltr");
print "</td></tr>
  <tr>
              <td width=\"70\">
                <b>$phrases[the_code]</b></td><td width=\"223\">
                  <textarea name='code' rows=20 cols=45 dir=ltr ></textarea></td>
                        </tr>
<tr><td><b>$phrases[the_order]</b></td><td><input type=text size=3 name=ord value='0'></td></tr>
<tr><td colspan=2 align=center><input type=submit value=' $phrases[add_button] '></td></tr>
</table>
</form></center>";
}

//-------- edit hook -------
if($action=="hook_edit"){

    if_admin();
$id=intval($id);

$qr = db_query("select * from store_hooks where id='$id'");

if(db_num($qr)){
    $data = db_fetch($qr);
print "<center>
<form action='index.php' method=post>
<input type=hidden name=action value='hook_edit_ok'>
<input type=hidden name=id value='$id'>
<table width=80% class=grid>
<tr><td><b>$phrases[the_name]</b></td><td><input type=text size=20 name=name value=\"$data[name]\"></td></tr>
<tr><td><b>$phrases[the_place]</b></td><td>";
$hooklocations = get_plugins_hooks();
print_select_row("hookid",$hooklocations,"$data[hookid]","dir=ltr");
print "</td></tr>
  <tr>
              <td width=\"70\">
                <b>$phrases[the_code]</b></td><td width=\"223\">
                  <textarea name='code' rows=20 cols=45 dir=ltr >".htmlspecialchars($data['code'])."</textarea></td>
                        </tr>
<tr><td><b>$phrases[the_order]</b></td><td><input type=text size=3 name=ord value=\"$data[ord]\"></td></tr>
<tr><td colspan=2 align=center><input type=submit value=' $phrases[edit] '></td></tr>
</table>
</form></center>";
}else{
print "<center><table width=50% class=grid><tr><td align=center>$phrases[err_wrong_url]</td></tr></table></center>";
}
}         
//------------------- DATABASE BACKUP --------------------------
if($action=="backup_db_do"){
    $output = htmlspecialchars($output) ;
print "<br><center> <table width=50% class=grid><tr><td align=center>  $output </td></tr></table>";
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
print "<script language=\"JavaScript\">\n";
print "function checkAll(form){\n";
print "  for (var i = 0; i < form.elements.length; i++){\n";
print "    eval(\"form.elements[\" + i + \"].checked = form.elements[0].checked\");\n";
print "  }\n";
print "}\n";
print "</script>\n";

        $tables = db_query("SHOW TABLE STATUS");
        print "<form name=\"form1\" method=\"post\" action=\"index.php\"/>
        <input type=hidden name=action value='repair_db_ok'>
        <center><table width=\"96%\"  class=grid>";
        print "<tr><td colspan=\"5\"> <font size=4><b>$phrases[the_database]</b></font> </td></tr>
        <tr><td>
        <input type=\"checkbox\" name=\"check_all\" checked=\"checked\" onClick=\"checkAll(this.form)\"/></td>
        ";
        print "<td><b>$phrases[the_table]</b></td><td><b>$phrases[the_size]</b></td>
        <td><b>$phrases[the_status]</b></td>
            </tr>";
        while($table = db_fetch($tables))
        {
            $size = round($table['Data_length']/1024, 2);
            $status = db_qr_fetch("ANALYZE TABLE `$table[Name]`");
            print "<tr>
            <td  width=\"5%\"><input type=\"checkbox\" name=\"check[]\" value=\"$table[Name]\" checked=\"checked\" /></td>
            <td width=\"50%\">$table[Name]</td>
            <td width=\"10%\" align=left dir=ltr>$size KB</td>
            <td>$status[Msg_text]</td>
            </tr>";
        }

        print "</table><br> <center><input type=\"submit\" name=\"submit\" value=\"$phrases[db_repair_tables_do]\" /></center> <br>
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
            print "<tr><td width=\"20%\">";
            print "$phrases[cp_repairing_table] " . $que['Table'] . " , <font color=green><b>$phrases[done]</b></font>";
            print "</td></tr>";
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
     <table width=90% class=grid>";
       print "<tr><td><b>$phrases[username]</b></td><td><b>$phrases[the_date]</b></td><td><b>$phrases[the_status]</b></td></tr>";   
     while($data = db_fetch($qr)){
         print "<tr><td>$data[username]</td><td>$data[date]</td><td>$data[status]</td></tr>";
     }
     print "</table></center>";
 }

 //-----------end ----------------
 require(ADMIN_DIR.'/end.php');