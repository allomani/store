<?
 require('./start.php'); 
 
//----------------  Banners -------------------------------------
   if(!$action || $action =="edit_ok" || $action =="del" || $action =="add_ok" || $action=="banner_disable" || $action=="banner_enable"){

   if_admin("banners");

//----------- add ----------------
if($action =="add_ok"){
    if($pages){
foreach ($pages as $value) {
       $pg_view .=  "$value," ;
     }
       }else{
               $pg_view = '' ;
               }


    $non_safe_content =  check_safe_functions($content);
if(!$non_safe_content){    
      db_query("insert into store_banners (title,url,img,ord,type,date,menu_id,menu_pos,pages,content,c_type,active,start_date,expire_date) values ('".db_escape($title)."','".db_escape($url)."','".db_escape($img)."','".intval($ord)."','".db_escape($type)."','".time()."','".intval($menu_id)."','".db_escape($menu_pos)."','".db_escape($pg_view)."','".db_escape($content,false)."','".db_escape($c_type)."','1','".iif($start_date,strtotime($start_date),0)."','".iif($expire_date,strtotime($expire_date),0)."')");
 }else{
    print_admin_table("<center> $non_safe_content </center>");
}
          }

//---------- edit --------------
if($action =="edit_ok"){

 if($pages){
foreach ($pages as $value) {
       $pg_view .=  "$value," ;
     }
       }else{
               $pg_view = '' ;
               }
     
$non_safe_content =  check_safe_functions($content);
if(!$non_safe_content){  
      db_query("update store_banners set title='".db_escape($title)."',url='".db_escape($url)."',img='".db_escape($img)."',ord='".intval($ord)."',type='".db_escape($type)."',menu_id='".intval($menu_id)."',menu_pos='".db_escape($menu_pos)."',pages='".db_escape($pg_view)."',content='".db_escape($content,false)."',c_type='".db_escape($c_type)."',start_date='".iif($start_date,strtotime($start_date),0)."',expire_date='".iif($expire_date,strtotime($expire_date),0)."' where id='$id'");
}else{
    print_admin_table("<center> $non_safe_content </center>");
}
          }

//---------- delete -------------
if($action =="del"){

      db_query("delete from store_banners where id='$id'");

 }


 if($action=="banner_disable"){
        db_query("update store_banners set active=0 where id='$id'");
        }

if($action=="banner_enable"){

       db_query("update store_banners set active=1 where id='$id'");
        }
//-------------------------------------

print "<p align=center class=title>$phrases[the_banners]</p>";
  print "<img src='images/add.gif'>&nbsp; <a href='banners.php?action=add'>$phrases[add_button]</a><br><br>";
     
//------------Bannners List -----------//

 $qr= db_query("select * from store_banners order by type , ord");
 
 if(db_num($qr)){
     

    print "
  <center>
  <table width=99% class=grid>

 
  
  <tr><td>
  ";
  $i=0;
  while($data=db_fetch($qr)){

          if($last_banner_type != $data['type']){
          if($i > 0){print "</div><hr class=separate_line>";}
          $types_array[] =  $data['type'] ;
        print "<div id='banners_list_".$data['type']."'>";
        $i++;
       } 
                          
       $last_banner_type = $data['type'];
       
       
  print "<div id=\"item_$data[id]\" style=\"".iif(!$data['active'],"background-color:#FFEAEA;")."\" 
     onmouseover=\"this.style.backgroundColor='#EFEFEE'\"
     onmouseout=\"this.style.backgroundColor='".iif($data['active'],"#FFFFFF","#FFEAEA")."'\">
  <table width=100%><tr>
   <td class=\"handle\"></td>
      ";
  if($data['c_type']=="code"){
      print "<td width=25><img src='images/code_icon.gif' alt='$phrases[bnr_ctype_code]'></td>";
      }else{
          print "<td width=25><img src='images/image_icon.gif' alt='$phrases[bnr_ctype_img]'></td>";
          }
          
  print "<td>$data[title]</td>";
                  
         $types_values = array_flip($banners_places);
         
   print "<td width=120>".$types_values[$data['type']]."</td>
   <td width=60> ";
   if($data['type'] == "menu"){
   print iif($data['menu_pos']=="r","$phrases[right]",iif($data['menu_pos']=="l","$phrases[left]",iif($data['menu_pos']=="c","$phrases[center]")));
   }else{
           print "-" ;
           }
           print "</td>
     
    <td width=160>";
     if($data['active']){
                        print "<a href='banners.php?action=banner_disable&id=$data[id]'>$phrases[disable]</a> - " ;
                        }else{
                        print "<a href='banners.php?action=banner_enable&id=$data[id]'>$phrases[enable]</a> - " ;
                        }
                        
    print "<a href='banners.php?action=edit&id=$data[id]'>$phrases[edit]</a> - 
    <a href='banners.php?action=del&id=$data[id]' onClick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete] </a></td>
  </tr>
  <tr>
  <td width=25><img src='images/arrow_".$global_dir.".gif'></td>
  <td colspan=6>
  $data[views] $phrases[bnr_views] , $data[clicks] $phrases[bnr_visits] , 
  <b>$phrases[from] : </b> ".iif($data['start_date'],iif($data['start_date']>=time(),"<font color='red'>".get_date($data['start_date'])."</font>","<font color=green>".get_date($data['start_date'])."</font>"),"<font color=green>".get_date($data['date'])."</font>")."
  , <b>$phrases[to] : </b> ".iif($data['expire_date'],iif($data['expire_date']<=time(),"<font color='red'>".get_date($data['expire_date'])."</font>","<font color=green>".get_date($data['expire_date'])."</font>"),"<font color=green>$phrases[without_expire]</font>")."  
      
  </td></tr>
  </table></div>" ;

      }
       print "</div>
       </td></tr></table></center>\n";
    


$types_array = (array) $types_array;
print "<script type=\"text/javascript\">"; 
foreach($types_array as $value){
    print "init_sortlist('banners_list_".$value."','banners');";
    }
print "</script>";

 }else{
    print_admin_table("<center>$phrases[no_banners]</center>");
 } 
}

//------------------ banners add ---------
if($action=="add"){
    if_admin("banners");
    
         print "
                  <img src='images/arrw.gif'>&nbsp; <a href='banners.php'>$phrases[the_banners]</a> / $phrases[add_button] <br><br> 
                
                
                <form method=\"POST\" action=\"banners.php\" name='sender'>
                 <input type='hidden' value='add_ok' name='action'>
                 
                    
                   <center>
<table width=\"80%\"><tr><td>

                   
<table  width=\"100%\" class=grid>
  <tr>
                <td>$phrases[bnr_appearance_places]</td>
                <td><select id=\"type\" name=\"type\" size=\"1\" onChange=\"show_banners_options();\">
             ";
             foreach($banners_places as $key => $value){
                 print "<option value=\"$value\">$key</option>";
             }
                print "
                </select></td>

                </tr>

                
                  
              <tr>
                   <td>$phrases[the_name]<td>
                <input type=\"text\" name=\"title\" size=\"38\"></td>
        </tr>
        
                        <tr>
                <td height=\"43\" width=\"131\">$phrases[start_date]</td>
                <td height=\"43\" width=\"308\"><input type=\"text\" id='start_date' name=\"start_date\"  size=\"10\" value=\"".date("d-m-Y",time())."\"><br> <font size=1>Ex : 01-04-2011 </font></td>
                </tr>
                
                
        
                        <tr>
                <td height=\"43\" width=\"131\">$phrases[expire_date]</td>
                <td height=\"43\" width=\"308\"><input type=\"text\" id='expire_date' name=\"expire_date\"  size=\"10\"><br> <font size=1>Ex : 23-04-2011 </font></td>
                </tr>
                
                
        
</table>


<table  width=\"100%\" class=grid id='bnr_content_type'>
           <tr>
                   <td >
                   $phrases[the_content_type]    <td >
                   <select id=\"c_type\" name=\"c_type\" onChange=\"show_banners_options();\">
                   <option value='img'> $phrases[bnr_ctype_img] </option>
                   <option value='code'>$phrases[bnr_ctype_code]</option>
                   </select>
              
                </td>
        </tr>
</table>

<table  width=\"100%\" class=grid id='banners_url_area'>

         <tr>
                <td>$phrases[the_url]</td>
                <td>
                <input type=\"text\" name=\"url\"  dir=ltr value='http://' size=\"38\"></td>
        </tr>
</table>

<table  width=\"100%\" class=grid id='banners_img_area'>
        <tr>
                <td >$phrases[the_image]</td>
                <td >

                <table><tr><td>
                                 <input type=\"text\" name=\"img\" size=\"30\" dir=ltr value=\"$data[img]\">   </td>

                                <td> <a href=\"javascript:uploader('banners','img');\"><img src='images/file_up.gif' border=0 alt='$phrases[upload_file]'></a>
                                 </td></tr></table>

                                 </td>
        </tr>
</table>

<table width=\"100%\" class=grid id='banners_code_area'>
<tr> <td>$phrases[the_content] </td>
<td>
<textarea dir=ltr rows=\"8\" name=\"content\" cols=\"50\"></textarea>
</td></tr>
</table>


<table width=\"100%\" class=grid id='add_after_menu'>
  <tr> <td> $phrases[add_after_menu_number] :</td>
                <td>
              
                <input type=\"text\"  name=\"menu_id\" value=0 size=\"4\">&nbsp;  $phrases[bnr_menu_pos]&nbsp;
                <select name=\"menu_pos\" size=\"1\">

                <option value=\"r\" >$phrases[the_right]</option>
                <option value=\"c\" >$phrases[the_center]</option>
                 <option value=\"l\" >$phrases[the_left]</option>

                </select>  </td>

                </tr>
</table>


 <table width=\"100%\" class=grid>
                <tr>
                <td>$phrases[the_order]</td>
                <td ><input type=\"text\" name=\"ord\" value='0' size=\"4\"></td>
                </tr>
                

                
                
 </table>
 
 <table width=\"100%\" class=grid id='banners_pages_area'>
                <tr><td>$phrases[bnr_appearance_pages]</td><td>

<table width=100%><tr><td>";


  if(is_array($actions_checks)){


  $c=0;
 for($i=0; $i < count($actions_checks);$i++) {

        $keyvalue = current($actions_checks);

if($c==3){
    print "</td><td>" ;
    $c=0;
    }

print "<input  name=\"pages[$i]\" type=\"checkbox\" value=\"$keyvalue\" checked>".key($actions_checks)."<br>";


$c++ ;

 next($actions_checks);
}
}
       print"</tr></table>
       </td></tr>
</table>
<table width=\"100%\" class=grid>
        <tr>
                <td  align=center>

                <input type=\"submit\" value=\"$phrases[add_button]\"></td>
        </tr>
</table>


</td></tr></table></center>
        </form>";
       ?>
        <script>
        show_banners_options();
        $(function() {
         $( "#start_date" ).datepicker({ dateFormat: 'dd-mm-yy' });
        $( "#expire_date" ).datepicker({ dateFormat: 'dd-mm-yy' });
        
    });
        </script>
    <?
}
   //-------------------EDIT BANNER-----------------------------
   if ($action == "edit"){
    if_admin("banners");
  
        $data=db_qr_fetch("select * from store_banners where id='$id'");
        print "<img src='images/arrw.gif'>&nbsp;<a href='banners.php'>$phrases[the_banners]</a> / $data[title] <br><br> 
                   
                   <form name=sender method=\"POST\" action=\"banners.php\">
                 <input type='hidden' value='edit_ok' name='action'>
                  <input type='hidden' value='$id' name='id'>
                  
                  
          <center>
          
        <table width=\"80%\"><tr><td>
        
        
          
          <table width=\"100%\" class=grid>
      

                  <tr>
                <td>$phrases[bnr_appearance_places]</td><td>
                 <select id=\"type\" name=\"type\" size=\"1\" onChange=\"show_banners_options();\">";
             foreach($banners_places as $key => $value){
                 print "<option value=\"$value\"".iif($data['type']==$value," selected").">$key</option>";
             }
                print "
                </select></td>

                </tr>

                  <tr>   
                         <td>
                       $phrases[the_name]</td><td>
                <input type=\"text\" name=\"title\" value='$data[title]' size=\"38\"></td>
        </tr>
        
                        <tr>
                <td height=\"43\" width=\"131\">$phrases[start_date]</td>
                <td height=\"43\" width=\"308\"><input type=\"text\" id='start_date' name=\"start_date\"  size=\"10\"".iif($data['start_date']," value=\"".date("d-m-Y",$data['start_date']))."\"><br> <font size=1>Ex : 01-04-2011 </font></td>
                </tr>
                
                                <tr>
                <td height=\"43\" width=\"131\">$phrases[expire_date]</td>
                <td height=\"43\" width=\"308\"><input type=\"text\" id='expire_date' name=\"expire_date\"  size=\"10\"".iif($data['expire_date']," value=\"".date("d-m-Y",$data['expire_date']))."\"><br> <font size=1>Ex : 23-04-2011 </font></td>
                </tr>
                
                
        </table>
        
        
        <table width=\"100%\" class=grid id='bnr_content_type'>";

      
         print " <tr>
                   <td>
                   $phrases[the_content_type]</td>
                  <td>
                   <select id=\"c_type\" name=\"c_type\" onChange=\"show_banners_options();\">
                   <option value='img'".iif($data['c_type'] == "img"," selected")."> $phrases[bnr_ctype_img] </option>
                   <option value='code'".iif($data['c_type'] == "code"," selected").">$phrases[bnr_ctype_code]</option>
                   </select>
              
                </td>
        </tr>
        </table>
        
        <table width=\"100%\" class=grid id='banners_url_area'>
       <tr><td >$phrases[the_url]</td>
                <td>
                <input type=\"text\" name=\"url\"  dir=ltr value='$data[url]' size=\"38\"></td>
        </tr>
        </table>
        
      <table width=\"100%\" class=grid id='banners_img_area'>
    <tr><td>$phrases[the_image]</td><td>

                <table><tr><td>
                                 <input type=\"text\" name=\"img\" size=\"30\" dir=ltr value=\"$data[img]\">   </td>

                                <td> <a href=\"javascript:uploader('banners','img');\"><img src='images/file_up.gif' border=0 alt='$phrases[upload_file]'></a>
                                 </td></tr></table>

         </td>
        </tr>
        </table>
        
  <table width=\"100%\" class=grid id='banners_code_area'>
<tr>
 <td valign=top>$phrases[the_code] </td><td> 
<textarea dir=ltr rows=\"8\" name=\"content\" cols=\"50\">$data[content]</textarea>
</td></tr>
</table>


<table width=\"100%\" class=grid id='add_after_menu'>
    
        <tr>
                <td> $phrases[add_after_menu_number]</td>
                <td>
                <input type=\"text\" value='$data[menu_id]' name=\"menu_id\" value='0' size=\"4\">  $phrases[bnr_menu_pos]
                <select name=\"menu_pos\" size=\"1\">
           
                <option value=\"r\"".iif($data['menu_pos']=="r"," selected").">$phrases[right]</option>
                <option value=\"c\"".iif($data['menu_pos']=="c"," selected").">$phrases[center]</option>
                 <option value=\"l\"".iif($data['menu_pos']=="l"," selected").">$phrases[left]</option>

                </select></td>

                </tr>
</table>
<table width=\"100%\" class=grid>

                <tr>
                <td>$phrases[the_order]</td>
                <td><input type=\"text\" value='$data[ord]' name=\"ord\" value='0' size=\"4\"></td>
                </tr>
                

                
                
 </table>
 <table width=\"100%\" class=grid id='banners_pages_area'>               
                
                <tr>
                <td>  $phrases[bnr_appearance_pages]</td>
                
                
                <td><table width=100%><tr><td>";

                         $pages_view = explode(",",$data['pages']);


  if(is_array($actions_checks)){

  $c=0;
 for($i=0; $i < count($actions_checks);$i++) {

        $keyvalue = current($actions_checks);

if($c==3){
    print "</td><td>" ;
    $c=0;
    }

if(in_array($keyvalue,$pages_view)){$chk = "checked" ;}else{$chk = "" ;}

print "<input  name=\"pages[$i]\" type=\"checkbox\" value=\"$keyvalue\" $chk>".key($actions_checks)."<br>";


$c++ ;

 next($actions_checks);
}
}



                          print "</tr></table>
                          </td><tr>
</table>
<table width=\"100%\" class=grid >
                          
        <tr>
                <td  align=center>
              
                <input type=\"submit\" value=\"$phrases[edit]\"></td>
        </tr>
</table>


</td></tr></table></center>
        </form>";
?>
        <script>
        show_banners_options();
        

    $(function() {
    $( "#start_date" ).datepicker({ dateFormat: 'dd-mm-yy' });
        $( "#expire_date" ).datepicker({ dateFormat: 'dd-mm-yy' });
    });
    
   </script>
<?
           }
//-----------end ----------------
 require(ADMIN_DIR.'/end.php');