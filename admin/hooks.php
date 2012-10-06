<?
require('./start.php');

//----------------------hooks ----------------------------
if(!$action || $action=="hooks" || $action=="disable" || $action=="enable" || $action=="add_ok" || $action=="edit_ok" || $action=="del" || $action=="hooks_fix_order"){


    if_admin();
//--------- hook add ---------------
if($action=="add_ok"){
db_query("insert into store_hooks (name,hookid,code,ord,active) values (
'".db_escape($name)."',
'".db_escape($hookid)."',
'".db_escape($code,false)."',
'".intval($ord)."','1')");
}
//------- hook edit ------------
if($action=="edit_ok"){
db_query("update store_hooks set
name='".db_escape($name)."',
hookid='".db_escape($hookid)."',
code='".db_escape($code,false)."',
ord='".intval($ord)."' where id='".intval($id)."'");
}
//--------- hook del --------
if($action=="del"){
    db_query("delete from store_hooks where id='".intval($id)."'");
    }
//--------- enable / disable -----------------
if($action=="disable"){
        db_query("update store_hooks set active=0 where id='".intval($id)."'");
        }

if($action=="enable"){

       db_query("update store_hooks set active=1 where id='".intval($id)."'");
        }
//-------- fix order -----------
if($action=="hooks_fix_order"){

   $qr=db_query("select hookid,id from store_hooks order by hookid,ord ASC");
    if(db_num($qr)){
    $c = 1 ;
    while($data = db_fetch($qr)){

    if($last_hookid !=$data['hookid']){$c=1;}

    db_query("update store_hooks set ord='$c' where id='$data[id]'");
     $last_hookid = $data['hookid'];
    ++$c;
    }
     }
     unset($last_hookid);
     }
//---------------------------------------------


$qr =db_query("select * from store_hooks order by hookid,ord,active");

print "<center><p class=title> $phrases[cp_hooks] </p>

<p align=$global_align><a href='hooks.php?action=add' class='add'>$phrases[add] </a></p>";

if(db_num($qr)){
              print "<table width=80% class=grid><tr>";

print "<tr><td><b>$phrases[the_name]</b></td><td><b>$phrases[the_order]</b></td><td><b>$phrases[the_place]</b></td><td><b>$phrases[the_options]</b></td></tr>";
while($data = db_fetch($qr)){

     if($last_hookid !=$data['hookid']){print "<tr><td colspan=4><hr class=separate_line></td></tr>";}

print "<tr><td>$data[name]</td><td><b>$data[ord]</b></td><td>$data[hookid]</td><td>";
 if($data['active']){
                        print "<a href='hooks.php?action=disable&id=$data[id]'>$phrases[disable]</a>" ;
                        }else{
                        print "<a href='hooks.php?action=enable&id=$data[id]'>$phrases[enable]</a>" ;
                        }

print "- <a href='hooks.php?action=edit&id=$data[id]'>$phrases[edit] </a>
- <a href='hooks.php?action=del&id=$data[id]' onClick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete] </a>
</td></tr>";


    $last_hookid = $data['hookid'];
    }

          print "</table>
 <br><form action='hooks.php' method=post>
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
if($action=="add"){

    if_admin();

print "<center>
<form action='hooks.php' method=post>
<input type=hidden name=action value='add_ok'>
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
if($action=="edit"){

    if_admin();
$id=intval($id);

$qr = db_query("select * from store_hooks where id='$id'");

if(db_num($qr)){
    $data = db_fetch($qr);
print "<center>
<form action='hooks.php' method=post>
<input type=hidden name=action value='edit_ok'>
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

//-----------end ----------------
 require(ADMIN_DIR.'/end.php');
