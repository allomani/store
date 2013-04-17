<?

require('./start.php');
//-------------------- Permisions------------------------
if ($action == "permisions") {

    if_admin();
    $data = db_qr_fetch("select * from store_user where id='$id'");

    print "<ul class='nav-bar'>
        <li><a href='users.php?action=users'>$phrases[the_users]</a></li>
    <li>$phrases[permissions_manage]</li>
    <li>$data[username]</li>
        </ul>
        
    <form method=post action='users.php'>
           <input type=hidden value='$id' name='id'>
               <input type=hidden value='permisions_edit' name='action'>";

    print "<p class=title align=center>$phrases[permissions_manage]</p>
   
<fieldset>
<legend>$phrases[cats_permissions]</legend>";

         //--------------- categories -------------------
        $categories = array();
        $qr_cats = db_query("select id, name ,cat,users from store_products_cats order by ord");
        while ($data_cats = db_fetch($qr_cats)) {
            //  $shipping_methods_arr = ;// get_product_cat_shipping_methods($data_cats['id'],true);//

            $categories[] = array(
                "key" => $data_cats['id'],
                "title" => $data_cats['name'],
                "parent" => $data_cats['cat'],
                "select" => iif(in_array($id, explode(',', $data_cats['users'])), true, false)
            );
        }


        print "
        
    <input type='radio' id='all_cats_yes' name='all_cats' value=1 onClick=\"\$('#cats_tree_wrapper').hide();\" " . iif($data['perm_all_cats'], " checked") . ">
    <label for='all_cats_yes'>جميع الأقسام </label><br>
           
    <input type='radio' id='all_cat_no' name='all_cats' value=0 onClick=\"\$('#cats_tree_wrapper').show();\"" . iif(!$data['perm_all_cats'], " checked") . ">
    <label for='all_cat_no'>أقسام محددة </label> 
    ";

        print "<div id='cats_tree_wrapper'>";
        print_dynatree_div($categories, 'cats_tree');
        print "</div>
      <input type='hidden' name='cats' id='cats' value=''>";
        print "</fieldset>";
        print iif($data['perm_all_cats'], "<script>$('#cats_tree_wrapper').hide();</script>");


    //------------------------------------------------------------------------------


    print "<fieldset>
    <legend>$phrases[cp_sections_permissions]</legend>
     <table width=100%><tr>";

    $prms = explode(",", $data['cp_permisions']);

    $permissions_checks = (array) $permissions_checks;
    

        $c = 0;
       $i=0;
       foreach($permissions_checks as $key=>$val){


            if ($c == 4) {
                print "</tr><tr>";
                $c = 0;
            }

         
            print "<td width=25%><input  name=\"cp_permisions[$i]\" type=\"checkbox\" value=\"$val\" ".iif(in_array($val, $prms)," checked").">" . $key . "</td>";


            $c++;
            $i++;

        }
    
    print "
        </table>
        </fieldset>";

    print "<fieldset style='text-align:center;'>
        <input type=submit value='$phrases[edit]'>
            </fieldset>
            
</form>";
    
      ?>
        <script type="text/javascript">
            $(function(){
                init_dynatree('cats_tree','cats');
            });                                                 
        </script>
        <?

}
//---------------------------- Users ------------------------------------------
if (!$action || $action == "users" || $action == "edit_ok" || $action == "add_ok" || $action == "del" || $action == "permisions_edit") {


    if ($action == "permisions_edit") {

        if_admin();

        $id = (int) $id;
        $all_cats = (int) $all_cats;
        $perms = implode(",", (array) $cp_permisions);
      
        db_query("update store_user set cp_permisions='".db_escape($perms)."',perm_all_cats='$all_cats' where id='$id'");
      
          //--------------
        $cats_arr = (array) explode(",", $cats);
        $qr = db_query("select id,users from store_products_cats");
        while ($data = db_fetch($qr)) {
            $cat_users = (array) explode(",", $data['users']);
            if (($key = array_search($id, $cat_users)) !== false) {
                unset($cat_users[$key]);
            }
            if (in_array($data['id'], $cats_arr)) {
                $cat_users[] = $id;
            }
            db_query("update store_products_cats set users = '" . implode(",", $cat_users) . "' where id='$data[id]'");
        }
    //---------------
    }

    //---------------------------------------------
    if ($action == "del") {
        if ($user_info['groupid'] == 1) {
            db_query("delete from store_user where id='$id'");
        } else {
            show_alert("$phrases[access_denied]","error");
            die();
        }
    }
    //---------------------------------------------
    if ($action == "add_ok") {
        if ($user_info['groupid'] == 1) {
            if (trim($username) && trim($password)) {
                if (db_qr_num("select username from store_user where username='" . db_escape($username, false) . "'")) {
                  show_alert("$phrases[cp_err_username_exists]","error");
                } else {
                    db_query("insert into store_user (username,password,email,group_id) values ('" . db_escape($username, false) . "','" . db_escape($password, false) . "','" . db_escape($email) . "','" . intval($group_id) . "')");
                }
            } else {
              show_alert("$phrases[cp_plz_enter_usr_pwd]","error");
            }
        } else {
            show_alert("$phrases[access_denied]","error");
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
                show_alert("$phrases[access_denied]","error");
                die();
            }
        }

       show_alert("$phrases[cp_edit_user_success]","success");
    }

    if ($user_info['groupid'] == 1) {
   

//----------------------------------------------------
        print "<p align=center class=title>$phrases[the_users]</p>";
        
             print "<p><a href='users.php?action=add' class='add'>$phrases[cp_add_user]</a></p>";
             
        $qr = db_query("select * from store_user order by id asc");


        print " <center> <table width=\"100%\" class=\"grid\">

        <tr>
             <td><b>$phrases[cp_username]</b></td>
                <td><b>$phrases[cp_email]</b></td>
                <td><b>$phrases[cp_user_group]</b></td>
                <td align='center'><b>$phrases[the_options]</b></td>
        </tr>";

        while ($data = db_fetch($qr)) {

            toggle_tr_class();

            print "<tr class='$tr_class'>
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

        print "<ul class='nav-bar'>
        <li><a href='users.php?action=users'>$phrases[the_users]</a></li>
        <li>$data[username]</li>
            </ul>
            


<center>
<form method=\"post\" action=\"users.php\">

 <TABLE width=70% class=grid>
    <TR>

    <INPUT TYPE=\"hidden\" NAME=\"id\" \" value=\"$data[id]\" >
<INPUT TYPE=\"hidden\" NAME=\"action\"  value=\"edit_ok\" >

   <TD width=\"100\"><b>$phrases[cp_username] : </b></TD>
   <TD width=\"614\"><INPUT TYPE=\"text\" NAME=\"username\" size=\"32\" value=\"$data[username]\" required='required'> </TD>
  </TR>
    <TR>
   <TD width=\"100\"><b>$phrases[cp_password] : </b></TD>
   <TD width=\"614\"><INPUT type=\"text\" name=\"password\" id='password'  dir=ltr size=\"32\"> &nbsp; <input type=button value=\"Generate\" id='generate_pwd'>
    <br>* $phrases[leave_blank_for_no_change] </TD>
  </TR>
  <tr><td></td><td>
<div id=\"passwordDescription\">-</div>
<div id=\"passwordStrength\" class=\"strength0\"></div>
</td></tr>
   <TR>
   <TD width=\"100\"><b>$phrases[cp_email] : </b></TD>
   <TD width=\"614\"><INPUT TYPE=\"text\" NAME=\"email\" size=\"32\" value=\"$data[email]\" > </TD>
  </TR>\n";

        if ($user_info['groupid'] != 1) {
            print "<input type='hidden' name='group_id' value='2'>";
        } else {
            print "<TR>
   <td width=\"100\"><b>$phrases[cp_user_group]: </b> </TD>
   <td>";


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
   <td colspan=\"2\" align=center>
  <input TYPE=\"submit\"  value=\"$phrases[edit]\"></td>
  </TR>
 </table>
</form>";
        
      ?>
        <script>
        $(function(){
           $('#generate_pwd').click(function(e){
           $('#password').val(GenerateAndValidate(12,1));
             passwordStrength($('#password').val());
        });
        $('#password').on('change',function(){
            passwordStrength($(this).val());
        });
         $('#password').on('keyup',function(){
            passwordStrength($(this).val());
        });

        });
        </script>
        <?
    } else {
        print "<center> $phrases[err_wrong_url]</center>";
    }
}
//--------------------- Add User Form -------------------------------------------------------
if ($action == "add") {
    print "<ul class='nav-bar'>
        <li><a href='users.php?action=users'>$phrases[the_users]</a></li>
    <li>$phrases[add_button]</li>
        </ul>
  
<p class=title align=center> $phrases[cp_add_user] </p>
    
<form method=\"post\" action=\"users.php\">
<input TYPE=\"hidden\" name=\"action\"  value=\"add_ok\" >


 <table class=grid>
  


   <TD width=\"150\"><b>$phrases[cp_username]: </b></TD>
   <TD ><INPUT TYPE=\"text\" NAME=\"username\" size=\"32\" required='required'>  </TD>
  </TR>
    <TR>
   <TD width=\"150\"><b>$phrases[cp_password] : </b> </TD>
   <TD ><INPUT TYPE=\"text\" name=\"password\" id='password' size=\"32\" required='required'>  &nbsp; <input type=button value=\"Generate\" id='generate_pwd'> </td>
  </TR>
  <tr><td></td><td>
<div id=\"passwordDescription\">-</div>
<div id=\"passwordStrength\" class=\"strength0\"></div>
</td></tr>
   <TR>
   <TD width=\"150\"><b>$phrases[cp_email] : </b> </TD>
   <TD ><input type=\"text\" name=\"email\" size=\"32\" > </TD>
  </TR>

   <TR>
   <TD width=\"150\"><b>$phrases[cp_user_group]: </b> </TD>
   <TD >\n";


    print "  <p><select size=\"1\" name=group_id>\n
        <option value='1' > $phrases[cp_user_admin] </option>
  <option value='2' > $phrases[cp_user_mod]</option>";


    print "  </select>";


    print " </TD>
  </TR>


  <TR>
   <TD colspan=\"2\" align=center>
<input TYPE=\"submit\" value=\"$phrases[add_button]\"></td>
  </TR>
 </TABLE>
</form>
";
    ?>
      <script>
        $(function(){
           $('#generate_pwd').click(function(e){
           $('#password').val(GenerateAndValidate(12,1));
             passwordStrength($('#password').val());
        });
        $('#password').on('change',function(){
            passwordStrength($(this).val());
        });
         $('#password').on('keyup',function(){
            passwordStrength($(this).val());
        });

        });
        </script>
 <?
}

//-----------end ----------------
require(ADMIN_DIR . '/end.php');