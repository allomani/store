<?

require('./start.php');
//-------------------- Permisions------------------------
if ($action == "permisions") {

    if_admin();
    $data = db_qr_fetch("select * from store_user where id='$id'");

    print "<img src='images/arrw.gif'>&nbsp;<a href='users.php?action=users'>$phrases[the_users]</a> / $phrases[permissions_manage]  / $data[username] <br><br>
    <form method=post action=users.php>
           <input type=hidden value='$id' name='user_id'>
               <input type=hidden value='permisions_edit' name='action'>";

    print "<center><span class=title>$phrases[permissions_manage]</span><br><br>
           <table  width=\"100%\" class=\"grid\">

        <tr><td>
        <center><span class=title>$phrases[cats_permissions]</span> <br><br>
$phrases[cats_permissions_note]";

    print " </center></td></tr>
           </table><br>";

    //------------------------------------------------------------------------------


    print "<table  width=\"100%\" class=\"grid\">
     <tr> <td colspan=5 align=center><span class=title>$phrases[cp_sections_permissions]</span></td></tr>
            <tr><td><table width=100%><tr>";

    $prms = explode(",", $data['cp_permisions']);


    if (is_array($permissions_checks)) {

        $c = 0;
        for ($i = 0; $i < count($permissions_checks); $i++) {

            $keyvalue = current($permissions_checks);

            if ($c == 4) {
                print "</tr><tr>";
                $c = 0;
            }

            if (in_array($keyvalue, $prms)) {
                $chk = "checked";
            } else {
                $chk = "";
            }

            print "<td width=25%><input  name=\"cp_permisions[$i]\" type=\"checkbox\" value=\"$keyvalue\" $chk>" . key($permissions_checks) . "</td>";


            $c++;

            next($permissions_checks);
        }
    }
    print "</tr></table></td>

            </tr></table>";

    print "<center> <br><input type=submit value='$phrases[edit]'></form>";
}
//---------------------------- Users ------------------------------------------
if (!$action || $action == "users" or $action == "edit_ok" or $action == "add_ok" or $action == "del" || $action == "permisions_edit") {


    if ($action == "permisions_edit") {

        if_admin();

        $user_id = intval($user_id);

        if ($cp_permisions) {
            foreach ($cp_permisions as $value) {
                $perms .= "$value,";
            }
        } else {
            $perms = '';
        }

        db_query("update store_user set cp_permisions='$perms' where id='$user_id'");
    }

    //---------------------------------------------
    if ($action == "del" && $id) {
        if ($user_info['groupid'] == 1) {
            db_query("delete from store_user where id='$id'");
        } else {
            print_admin_table("<center>$phrases[access_denied]</center>");
            die();
        }
    }
    //---------------------------------------------
    if ($action == "add_ok") {
        if ($user_info['groupid'] == 1) {
            if (trim($username) && trim($password)) {
                if (db_qr_num("select username from store_user where username='" . db_escape($username, false) . "'")) {
                    print "<center> $phrases[cp_err_username_exists] </center>";
                } else {
                    db_query("insert into store_user (username,password,email,group_id) values ('" . db_escape($username, false) . "','" . db_escape($password, false) . "','" . db_escape($email) . "','" . intval($group_id) . "')");
                }
            } else {
                print "<center>  $phrases[cp_plz_enter_usr_pwd] </center>";
            }
        } else {
            print_admin_table("<center>$phrases[access_denied]</center>");
            die();
        }
    }
    //------------------------------------------------------------------------------
    if ($action == "edit_ok") {
        if ($password) {
            $ifeditpassword = ", password='" . db_escape($password, false) . "'";
        }

        if ($user_info['groupid'] == 1) {
            db_query("update store_user set username='" . db_escape($username, false) . "'  , email='" . db_escape($email) . "' ,group_id='" . intval($group_id) . "' $ifeditpassword where id='$id'");
        } else {
            if ($user_info['id'] == $id) {
                db_query("update store_user set username='" . db_escape($username, false) . "'  , email='" . db_escape($email) . "'  $ifeditpassword where id='$id'");
            } else {
                print_admin_table("<center>$phrases[access_denied]</center>");
                die();
            }
        }

        print "<center>  $phrases[cp_edit_user_success]  </center>";
    }

    if ($user_info['groupid'] == 1) {
        print "<a href='users.php?action=add' class='add'>$phrases[cp_add_user]</a>";

//----------------------------------------------------
        print "<p align=center class=title>$phrases[the_users]</p>";
        $result = db_query("select * from store_user order by id asc");


        print " <center> <table width=\"100%\" class=\"grid\">

        <tr>
             <td><b>$phrases[cp_username]</b></td>
                <td><b>$phrases[cp_email]</b></td>
                <td><b>$phrases[cp_user_group]</b></td>
                <td align='center'><b>$phrases[the_options]</b></td>
        </tr>";

        while ($data = db_fetch($result)) {





            print "<tr>
                <td> $data[username]</td>
                <td>" . iif($data[email], $data[email], '-') . "</td>
                <td>" . iif($data['group_id'] == 1, $phrases[cp_user_admin], $phrases[cp_user_mod]) . "</td>
                 <td align=center>
                 " . iif($data['group_id'] != 1, "<a href='users.php?action=permisions&id=$data[id]'>$phrases[permissions_manage]</a>", "$phrases[permissions_manage]") . " - 
                     <a href='users.php?action=edit&id=$data[id]'> $phrases[edit] </a> - "
                    . iif($data['id'] != "1", "<a href='users.php?action=del&id=$data[id]' onClick=\"return confirm('" . $phrases['are_you_sure'] . "');\">$phrases[delete]</a>", "$phrases[delete]") . "
              </td>
        </tr>";
        }

        print "</table>";
    } else {

        print "<br><center><table width=70% class=grid><tr><td align=center>
                $phrases[edit_personal_acc_only] <br>
                <a href='users.php?action=edit'> $phrases[click_here_to_edit_ur_account] </a>
                </td></tr></table></center>";
    }
}
//-------------------------Edit User------------------------------------------

if ($action == "edit") {
    $id = intval($id);

    if ($user_info['groupid'] != 1) {
        $id = $user_info['id'];
    }

    $qr = db_query("select * from store_user where id='$id'");
    if (db_num($qr)) {

        $data = db_fetch($qr);

        print "<img src='images/arrw.gif'>&nbsp;<a href='users.php?action=users'>$phrases[the_users]</a> / $data[username] <br><br>


<center>
<FORM METHOD=\"post\" ACTION=\"users.php\">

 <TABLE width=70% class=grid>
    <TR>

    <INPUT TYPE=\"hidden\" NAME=\"id\" \" value=\"$data[id]\" >
<INPUT TYPE=\"hidden\" NAME=\"action\"  value=\"edit_ok\" >

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

        if ($user_info['groupid'] != 1) {
            print "<input type='hidden' name='group_id' value='2'>";
        } else {
            print "<TR>
   <TD width=\"100\"><font color=\"#006699\"><b>$phrases[cp_user_group]: </b></font> </TD>
   <TD width=\"614\">\n";


            if ($data['group_id'] == 1) {
                $ifselected1 = "selected";
            } else {
                $ifselected2 = "selected";
            }

            print "  <p><select size=\"1\" name=group_id>\n
        <option value='1' $ifselected1> $phrases[cp_user_admin] </option>
  <option value='2' $ifselected2>$phrases[cp_user_mod] </option>";


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
    } else {
        print "<center> $phrases[err_wrong_url]</center>";
    }
}
//--------------------- Add User Form -------------------------------------------------------
if ($action == "add") {
    print "   <img src='images/arrw.gif'>&nbsp;<a href='users.php?action=users'>$phrases[the_users]</a> / $phrases[add_button] <br><br>

   <center>

<form METHOD=\"post\" ACTION=\"users.php\">
<INPUT TYPE=\"hidden\" NAME=\"action\"  value=\"add_ok\" >


 <TABLE width=\"70%\" class=grid>
    <TR>
   <td colspan=2 align=center><span class=title> $phrases[cp_add_user] </span></td></tr>
   <tr>


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
  <option value='2' > $phrases[cp_user_mod]</option>";


    print "  </select>";


    print " </TD>
  </TR>


  <TR>
   <TD COLSPAN=\"2\" >
   <p align=\"center\"><INPUT TYPE=\"submit\" name=\"addbutton\" VALUE=\"$phrases[add_button]\"></TD>
  </TR>
 </TABLE>
</form>
</center><br><br>\n";
}

//-----------end ----------------
require(ADMIN_DIR . '/end.php');