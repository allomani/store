<?
 require('./start.php'); 
 
// -------------- Blocks ----------------------------------
if (!$action || $action == "blocks" || $action=="del" || $action=="edit_ok" || $action=="add_ok"
|| $action=="disable" || $action=="enable" || $action=="order" || $action=="fix_order"){


if_admin();
if($action=="fix_order"){

   $qr=db_query("select * from store_blocks where pos='r' order by ord ASC");
    if(db_num($qr)){
    $block_c = 1 ;
    while($data = db_fetch($qr)){
    db_query("update store_blocks set ord='$block_c' where id='$data[id]'");
    ++$block_c;
    }
     }
//-------------------------------
  $qr=db_query("select * from store_blocks where pos='c' order by ord ASC");
    if(db_num($qr)){
    $block_c = 1 ;
    while($data = db_fetch($qr)){
    db_query("update store_blocks set ord='$block_c' where id='$data[id]'");
    ++$block_c;
    }
     }
//-------------------------------
  $qr=db_query("select * from store_blocks where pos='l' order by ord ASC");
    if(db_num($qr)){
    $block_c = 1 ;
    while($data = db_fetch($qr)){
    db_query("update store_blocks set ord='$block_c' where id='$data[id]'");
    ++$block_c;
    }
     }
        }

if($action=="order"){
        db_query("update store_blocks set ord='$ord' where id = '$idrep'");
        db_query("update store_blocks set ord='$ordrep' where id = '$id'");
        }


if($action=="disable"){
        db_query("update store_blocks set active=0 where id='$id'");
        }

if($action=="enable"){

       db_query("update store_blocks set active=1 where id='$id'");
        }
//---------------------------------------------------------
if($action=="add_ok"){
if($pages){
foreach ($pages as $value) {
       $pg_view .=  "$value," ;
     }
       }else{
               $pg_view = '' ;
               }


if($pos != "l" && $pos != "r" && $pos != "c"){$pos = "c";}

db_query("insert into store_blocks(title,pos,file,ord,active,template,pages,hide_title,cat)
values(
'".db_escape($title,false)."',
'".db_escape($pos)."',
'".db_escape($file,false)."',
'".intval($ord)."','1',
'".db_escape($template)."',
'".db_escape($pg_view)."','".db_escape($hide_title)."','$cat')");
        }
//------------------------------------------------------------
if ($action=="del"){
          db_query("delete from store_blocks where id='$id'");
            }
//----------------------------------------------------------------
if ($action=="edit_ok"){
if($pages){
foreach ($pages as $value) {
       $pg_view .=  "$value," ;
     }
}else{
$pg_view = '' ;
}


if($pos != "l" && $pos != "r" && $pos != "c"){$pos = "c";}

db_query("update store_blocks set
title='".db_escape($title,false)."',
file='".db_escape($file,false)."',
pos='".db_escape($pos)."',
ord='".intval($ord)."',
template='".db_escape($template)."',
pages='".db_escape($pg_view)."',hide_title='".db_escape($hide_title)."',cat='$cat' where id='".intval($id)."'");

                    }
//------------------------------------------------------------

print "<p align=center class=title>$phrases[the_blocks]</p><br>
<a href='blocks.php?action=add' class='add'>$phrases[add_button]</a><br><br>";



       $qr_arr[0]=db_query("select * from store_blocks where pos='l' order by ord asc");
       $qr_arr[1]=db_query("select * from store_blocks where pos='c' order by ord asc");
       $qr_arr[2]=db_query("select * from store_blocks where pos='r' order by ord asc");

       if (db_num($qr_arr[0]) || db_num($qr_arr[1]) || db_num($qr_arr[2])){
           print "<center><table border=\"0\" width=\"99%\" cellpadding=\"0\" cellspacing=\"0\" class=\"grid\" dir=ltr>
           <tr>
          <td align=center><b>$phrases[left]</b></td>  
           <td align=center><b>$phrases[center]</b></td>
           
            <td align=center><b>$phrases[right]</b></td>
           </tr>
           <tr>";
           /*
           <tr><td><b>  $phrases[the_title] </b><td><b> $phrases[the_position] </b></td><td><b> $phrases[the_order] </b></td>
           <td colspan=3 align=center><b>  $phrases[the_options] </b></td></tr>";

                                         */
        $i = 0 ;  
       foreach($qr_arr as $qr){  
                                          
         while($data= db_fetch($qr)){
       
       if($last_block_pos != $data['pos']){
          
           if($i > 0){print "</ul>";}
           print "</td><td valign=top dir=$global_dir>";
           
            print "<ul id='".$data['pos']."' class='blocks_group'>";
            $i++;
       } 
                          
       $last_block_pos = $data['pos'];
     print "<li id=\"item_$data[id]\" class='".iif($data['active'],"active","inactive")."'><center>
     <table width=100%>
     <tr>
     <td class=\"handle\">
     
      </td>
     
     
  
     
                <td align=center><span class='title'>";
                if($data['title']){
                    print $data['title'] ;
                    }else{
                    print "[ $phrases[without_title] ]" ;
                        }
                        print "</span></td>
                        
                       <td align=$global_align_x width=31>".iif($data['cat'],"<img src='images/tabbed.gif' alt='Tabbed Menu'>")."</td>  
                        
                        </tr>
                        <tr>
               
             
                <td align=center colspan=3>";

                if($data['active']){
                        print "<a href='blocks.php?action=disable&id=$data[id]'>$phrases[disable]</a>" ;
                        }else{
                        print "<a href='blocks.php?action=enable&id=$data[id]'>$phrases[enable]</a>" ;
                        }

                print "- <a href='blocks.php?action=edit_block&id=$data[id]'>$phrases[edit] </a>
                - <a href='blocks.php?action=del&id=$data[id]' onClick=\"return confirm('Are you sure you want to delete ?');\">$phrases[delete] </a></td>
        </tr>
        </table></center></li>";
            //    $i++;
                 }
       }
                print "</ul>
       


               ";
                ?>
<script type="text/javascript">
    init_blocks_sortlist(); 
</script>
<?
 //<div id=result>result here</div>
                print"</td></tr></table>"; 
                
              /*
                print "<br><form action='blocks.php' method=post>
                <input type=hidden name=action value='fix_order'>
                <input type=submit value=' $phrases[cp_fix_order] '>
                </form><br>";
                 */
                }else{
                        print "<br><center><table width=50% class=grid><tr><td align=center>$phrases[cp_no_blocks]</td></tr></table></center>";
                        }

}
//--------------------- Block Edit ---------------------------
if($action == "edit_block"){

    if_admin();
  $data=db_qr_fetch("select * from store_blocks where id='$id'");
      $data['file'] = htmlspecialchars($data['file']) ;


 print "
     <ul class='nav-bar'>
     <li><a href='blocks.php?action=blocks'>$phrases[the_blocks]</a></li>
      <li>$data[title]</li>
          </ul>
 
 <form method=\"POST\" action=\"blocks.php\">  
 <input type=hidden name=\"action\" value='edit_ok'>
 <input type=hidden name=\"id\" value='$id'>
        <table border=\"0\" width=\"99%\"  class=\"grid\" >

                 <tr>
                 <td width=150>
                <b>$phrases[the_title]</b></td><td>
                <input type=\"text\" name=\"title\" value='$data[title]' size=\"29\">&nbsp; <input type=checkbox value=1 name=\"hide_title\"".iif($data['hide_title']," checked")."> $phrases[hide_title]</td>
                        </tr>
              </table><br>
                    
                 <textarea name='file' id='file'>$data[file]</textarea>
                 <br> <table border=\"0\" width=\"99%\"  class=\"grid\" >
         <tr> <td>
                <b>$phrases[the_position]</b></td>
                                <td>";
                print_select_row("pos",array("r"=>$phrases['right'],"c"=>$phrases['center'],"l"=>$phrases['left']),$data['pos']);
                      print "
                      </td>
                        </tr>

                   <tr><td><b>$phrases[the_template] </b></td><td><select name=template><option value='0'".iif($data['template']==0," selected")."> $phrases[the_default_template] </option>";

  $qr_template = db_query("select name,id,cat from store_templates where protected !=1 order by cat,id");
              while($data_template = db_fetch($qr_template)){
              if($data['template'] == $data_template['name']){
                      $chk = "selected" ;
                      }else{
                              $chk = "";
                              }
                      $t_catname = db_qr_fetch("select name from store_templates_cats where id='$data_template[cat]'");
                      print "<option value=\"$data_template[name]\" $chk>$t_catname[name] : $data_template[name]</option>";
                      }
                      print "</select></td></tr>

                                        <tr><td><b>$phrases[tabbed_to]</b></td><td>
                                        <select name=cat><option value='0'>$phrases[without_tabbed_menu]</option>";

  $qr_cat = db_query("select title,id from store_blocks where id !='$data[id]' and cat=0 order by pos,ord");
              while($data_cat = db_fetch($qr_cat)){
              if($data['cat'] == $data_cat['id']){
                      $chk = "selected" ;
                      }else{
                              $chk = "";
                              }
                              
                  
                      print "<option value='$data_cat[id]' $chk>$data_cat[title]</option>";
                      }
                      print "</select></td></tr>
                      
                      
                              <tr>
                                <td>
                <b>$phrases[the_order]</b></td><td>
                <input type='text' name='ord' value='$data[ord]' size='2'></td>
                        </tr>
                        </table>
                        
                        <fieldset>
                        <legend>$phrases[appearance_places]</legend>
                            
<table width=100%><tr><td>";

                         $pages_view = explode(",",$data['pages']);


  if(is_array($actions_checks)){

  $c=0;
  $i=0;
 //for($i=0; $i < count($actions_checks);$i++) {

  //      $keyvalue = current($actions_checks);

 foreach($actions_checks as $key=>$keyvalue){
if($c==4){
    print "</td><td>" ;
    $c=0;
    }

if(in_array($keyvalue,$pages_view)){$chk = "checked" ;}else{$chk = "" ;}

print "<input  name=\"pages[$i]\" type=\"checkbox\" value=\"$keyvalue\" $chk>".$key."<br>";


$c++ ;
$i++;

 //next($actions_checks);
}
}



                          print "
                              

          
</td></tr></table>

<img src='images/arrow_" . $global_dir . ".gif'>    
        
          <a href='#' onclick=\"CheckAll(); return false;\"> $phrases[select_all] </a> -
          <a href='#' onclick=\"UncheckAll(); return false;\">$phrases[select_none] </a>
          &nbsp;  &nbsp;
                                  
           </fieldset>
         
<fieldset>
               <center><input type=\"submit\" value=\"$phrases[edit]\"> </center>
    </fieldset>
</form>";

           code_editor_init('file');

        }
        
//------------ Block Add ------------
if($action=="add"){
     print "
         <ul class='nav-bar'>
         <li><a href='blocks.php?action=blocks'>$phrases[the_blocks]</a></li>
        <li>$phrases[add_button]</li>
    </ul>";     
  
   print "
    <form method=\"POST\" action=\"blocks.php\" name=submit_form>
    <input type=hidden name=\"action\" value='add_ok'>
   
    <table class=\"grid\">

    <tr>
                                <td>
                <b>$phrases[the_title]</b></td><td >
                <input type=\"text\" name=\"title\" size=\"29\">&nbsp; <input type=checkbox name=\"hide_title\" value=1> $phrases[hide_title]</td>
                        </tr>
                 </table><br>
                       
                  <textarea name='file' id='file'></textarea>
                           <br>
                <table border=\"0\" width=\"99%\" class=\"grid\">  
                     <tr> <td>
                <b>$phrases[the_position]</b></td>
                                <td>";
                print_select_row("pos",array("r"=>$phrases['right'],"c"=>$phrases['center'],"l"=>$phrases['left']));     
                        print "
                        </td>
                        </tr>
              <tr><td><b>$phrases[the_template]</b></td><td><select name=template><option value='0' selected> $phrases[the_default_template] </option>";
              $qr = db_query("select name,id,cat from store_templates where protected !=1 order by cat,id ");
              while($data = db_fetch($qr)){
              $t_catname = db_qr_fetch("select name from store_templates_cats where id='$data[cat]'");
                      print "<option value=\"$data[name]\">$t_catname[name] : $data[name]</option>";
                      }
                      print "</select></td></tr>
                      
                                   <tr><td><b>$phrases[tabbed_to]</b></td><td>
                                        <select name=cat><option value='0'>$phrases[without_tabbed_menu]</option>";

  $qr_cat = db_query("select title,id from store_blocks where cat=0 order by pos,ord");
              while($data_cat = db_fetch($qr_cat)){
              if($data['cat'] == $data_cat['id']){
                      $chk = "selected" ;
                      }else{
                              $chk = "";
                              }
                              
                  
                      print "<option value='$data_cat[id]' $chk>$data_cat[title]</option>";
                      }
                      print "</select></td></tr>
                      
                      
                        <tr>
                                <td>
                <b>$phrases[the_order]</b></td><td>
                <input type=\"text\" name=\"ord\" value=\"1\" size=\"2\"></td>
                        </tr>
                        </table>
                        
                        <fieldset>
                        <legend>$phrases[appearance_places]</legend>
                            <table width=100%><tr><td>
";


  if(is_array($actions_checks)){
$c=0;
$i=0;
 //for($i=0; $i < count($actions_checks);$i++) {

   //  $keyvalue = current($actions_checks);

 foreach($actions_checks as $key=>$keyvalue){
if($c==4){
    print "</td><td>" ;
    $c=0;
    }

print "<input  name=\"pages[$i]\" type=\"checkbox\" value=\"$keyvalue\" checked>".$key."<br>";


$c++ ;
$i++;
// next($actions_checks);
}
}
        print "
            </tr></table>
<img src='images/arrow_" . $global_dir . ".gif'>    
        
          <a href='#' onclick=\"CheckAll(); return false;\"> $phrases[select_all] </a> -
          <a href='#' onclick=\"UncheckAll(); return false;\">$phrases[select_none] </a>
          &nbsp;  &nbsp;
          

            </fieldset>
            
            <fieldset>
            <center><input type=\"submit\" value=\"$phrases[add_button]\"></center>


</fieldset>

</form>";

        code_editor_init("file");

}

 //-----------end ----------------
 require(ADMIN_DIR.'/end.php');