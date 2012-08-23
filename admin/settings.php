<?

  if(!defined('IS_ADMIN')){die('No Access');} 
  
  
 //----------------------- Settings --------------------------------
 if($action == "settings" || $action=="settings_edit"){
 
     if_admin();


 if($action=="settings_edit"){
     

  if(is_array($stng)){
 for($i=0;$i<count($stng);$i++) {

        $keyvalue = current($stng);

       db_query("update store_settings set value='".db_escape($keyvalue,false)."' where name='".db_escape(key($stng))."'");


 next($stng);
}
}

         }


 load_settings();
 

 print "<center>
 <p align=center class=title>  $phrases[the_settings] </p>
 <form action=index.php method=post>
 <input type=hidden name=action value='settings_edit'>
 <table width=70% class=grid>
  
 <tr><td>  $phrases[site_name] : </td><td><input type=text name=stng[sitename] size=30 value='$settings[sitename]'> &nbsp; </td></tr>
  <tr><td> $phrases[show_sitename_in_subpages] </td><td>";
  print_select_row("stng[sitename_in_subpages]",array($phrases['no'],$phrases['yes']),$settings['sitename_in_subpages']);
  print "</td></tr>
 
 
 <tr><td>  $phrases[section_name] : </td><td><input type=text name=stng[section_name] size=30 value='$settings[section_name]'></td></tr>
 <tr><td> $phrases[show_section_name_in_subpages] </td><td>";
  print_select_row("stng[section_name_in_subpages]",array($phrases['no'],$phrases['yes']),$settings['section_name_in_subpages']);
  print "</td></tr>
 
  <tr><td>  $phrases[copyrights_sitename] : </td><td><input type=text name=stng[copyrights_sitename] size=30 value='$settings[copyrights_sitename]'></td></tr>
 
   <tr><td>  $phrases[mailing_email] : </td><td><input type=text dir=ltr name=stng[mailing_email] size=30 value='$settings[mailing_email]'></td></tr>
   <tr><td>  $phrases[admin_email] : </td><td><input type=text dir=ltr name=stng[admin_email] size=30 value='$settings[admin_email]'></td></tr>

 <tr><td> $phrases[page_dir] : </td><td><select name=stng[html_dir]>" ;
 if($settings['html_dir'] == "rtl"){$chk1 = "selected" ; $chk2=""; }else{ $chk2 = "selected" ; $chk1="";}
 print "<option value='rtl' $chk1>$phrases[right_to_left]</option>
 <option value='ltr' $chk2>$phrases[left_to_right]</option>
 </select>
 </td></tr>
  <tr><td>  $phrases[pages_lang] : </td><td><input type=text name=stng[site_pages_lang] size=30 value='$settings[site_pages_lang]'></td></tr>
    <tr><td>  $phrases[pages_encoding] : </td><td><input type=text name=stng[site_pages_encoding] size=30 value='$settings[site_pages_encoding]'></td></tr>
  <tr><td> $phrases[page_keywords] : </td><td><input type=text name=stng[header_keywords] size=30 value='$settings[header_keywords]'></td></tr>
   <tr><td> $phrases[page_description] : </td><td><input type=text name=stng[header_description] size=30 value='$settings[header_description]'></td></tr>

   
  </table>
   <br>
   <table width=70% class=grid>
  <tr><td>  $phrases[cp_enable_browsing]</td><td><select name=stng[enable_browsing]>";
  if($settings['enable_browsing']=="1"){$chk1="selected";$chk2="";}else{$chk1="";$chk2="selected";}
  print "<option value='1' $chk1>$phrases[cp_opened]</option>
  <option value='0' $chk2>$phrases[cp_closed]</option>
  </select></td></tr>
  <tr><td>$phrases[cp_browsing_closing_msg]</td><td><textarea cols=30 rows=5 name=stng[disable_browsing_msg]>$settings[disable_browsing_msg]</textarea>
  </td></tr>
   </table>
   <br>
   <table width=70% class=grid>
 <tr><td>  $phrases[products_perpage] : </td><td><input type=text name=stng[products_perpage] size=5 value='$settings[products_perpage]'></td></tr>
 <tr><td>  $phrases[admin_products_perpage] : </td><td><input type=text name=stng[admin_products_perpage] size=5 value='$settings[admin_products_perpage]'></td></tr>
  
  <tr><td>  $phrases[orders_perpage] : </td><td><input type=text name=stng[orders_perpage] size=5 value='$settings[orders_perpage]'></td></tr>
  
 
  <tr><td>  $phrases[news_perpage] : </td><td><input type=text name=stng[news_perpage] size=5 value='$settings[news_perpage]'></td></tr>

 
 <tr><td>  $phrases[images_cells_count] : </td><td><input type=text name=stng[img_cells] size=5 value='$settings[img_cells]'></td></tr>
<tr><td>  $phrases[votes_expire_time] : </td><td><input type=text name=stng[votes_expire_hours] size=5 value='$settings[votes_expire_hours]'> $phrases[hour] </td></tr>
<tr><td>  $phrases[rating_exire_time] : </td><td><input type=text name=stng[rating_expire_hours] size=5 value='$settings[rating_expire_hours]'> $phrases[hour] </td></tr>

  
<tr><td>  $phrases[currency_mark] : </td><td><input type=text name=stng[currency] size=5 value='$settings[currency]'></td></tr>



    </table>
     <br>
   <table width=70% class=grid>
   <tr><td> $phrases[visitors_can_sort_products] : </td><td>" ;
 print_select_row("stng[visitors_can_sort_products]",array($phrases['no'],$phrases['yes']),$settings['visitors_can_sort_products']);
 print "</td></tr>
   <tr><td> $phrases[show_paid_option] : </td><td>" ;
 print_select_row("stng[show_paid_option]",array($phrases['no'],$phrases['yes']),$settings['show_paid_option']);
 print "</td></tr>

  <tr><td> $phrases[notify_client_when_order_status_change] : </td><td>" ;
 print_select_row("stng[default_status_change_notify]",array($phrases['no'],$phrases['yes']),$settings['default_status_change_notify']);
 print "</td></tr>
  
 
 
 <tr><td>$phrases[products_default_orderby] : </td><td>
<select size=\"1\" name=\"stng[products_default_orderby]\">";
for($i=0; $i < count($orderby_checks);$i++) {

$keyvalue = current($orderby_checks);
if($keyvalue==$settings['products_default_orderby']){$chk="selected";}else{$chk="";}

print "<option value=\"$keyvalue\" $chk>".key($orderby_checks)."</option>";;

 next($orderby_checks);
}
print "</select>&nbsp;&nbsp; <select name=stng[products_default_sort]> ";
if($settings['products_default_sort']=="asc"){$chk1="selected";$chk2="";}else{$chk1="";$chk2="selected";}
print "<option value='asc' $chk1>$phrases[asc]</option>
<option value='desc' $chk2>$phrases[desc]</option>
</select>
</td></tr>
   </table>

 
  <br>    
  <fieldset style=\"width:70%;\">
  <legend>$phrases[product_img]</legend>    
   <table width=100%>
 
 <tr><td>  $phrases[width] : </td><td><input type=text name=stng[products_img_width] size=5 value='$settings[products_img_width]'></td></tr>
  <tr><td>  $phrases[height] : </td><td><input type=text name=stng[products_img_height] size=5 value='$settings[products_img_height]'></td></tr>
    <tr><td>  $phrases[fixed] : </td><td>";
    print_select_row('stng[products_img_fixed]',array("$phrases[no]","$phrases[yes]"),$settings['products_img_fixed']);
    print "</td></tr>

  </table>
  </fieldset>          
    
 <br>  
 <fieldset style=\"width:70%;\">
  <legend>$phrases[product_thumb]</legend>    
   <table width=100%>
 
 <tr><td>  $phrases[width] : </td><td><input type=text name=stng[products_thumb_width] size=5 value='$settings[products_thumb_width]'></td></tr>
  <tr><td>  $phrases[height] : </td><td><input type=text name=stng[products_thumb_height] size=5 value='$settings[products_thumb_height]'></td></tr>
    <tr><td>  $phrases[fixed] : </td><td>";
    print_select_row('stng[products_thumb_fixed]',array("$phrases[no]","$phrases[yes]"),$settings['products_thumb_fixed']);
    print "</td></tr>

  </table>
  </fieldset>
  
  
  <br>  
 <fieldset style=\"width:70%;\">
  <legend>$phrases[products_photos_thumb]</legend>    
   <table width=100%>
 
 <tr><td>  $phrases[width] : </td><td><input type=text name=stng[products_photos_thumb_width] size=5 value='$settings[products_photos_thumb_width]'></td></tr>
  <tr><td>  $phrases[height] : </td><td><input type=text name=stng[products_photos_thumb_height] size=5 value='$settings[products_photos_thumb_height]'></td></tr>
    <tr><td>  $phrases[fixed] : </td><td>";
    print_select_row('stng[products_photos_thumb_fixed]',array("$phrases[no]","$phrases[yes]"),$settings['products_photos_thumb_fixed']);
    print "</td></tr>

  </table>
  </fieldset>
  
  
                      <br>
 <table width=70% class=grid>

 <tr><td>$phrases[the_search] : </td><td><select name=stng[enable_search]>" ;
 if($settings['enable_search']){$chk1 = "selected" ; $chk2 ="" ;}else{ $chk2 = "selected" ; $chk1 ="" ;}
 print "<option value=1 $chk1>$phrases[enabled]</option>
 <option value=0 $chk2>$phrases[disabled]</option>
 </select>
 </td></tr>

<tr><td>  $phrases[search_min_letters] : </td><td><input type=text name=stng[search_min_letters] size=5 value='$settings[search_min_letters]'>  </td></tr>

   </table>
   <br>
 <table width=70% class=grid>
  <tr><td>$phrases[default_style]</td><td><select name=stng[default_styleid]>";
  $qrt=db_query("select * from store_templates_cats order by id asc");
while($datat =db_fetch($qrt)){
print "<option value=\"$datat[id]\"".iif($settings['default_styleid']==$datat['id']," selected").">$datat[name]</option>";
}
  print "</select>
  </td>
 </table>          
                     
   <br>
 
  <fieldset style=\"width:70%\">
 <legend><b>$phrases[time_and_date]</b></legend>
 <table width=100%>
 <tr><td>$phrases[timezone]</td><td>";
  print_select_row("stng[timezone]",get_timezones(),$settings['timezone'],"dir='ltr'");
  /*
 <select name='stng[timezone]' dir='ltr'> ";
  $zones = get_timezones();
  foreach($zones as $zone){
  print "<option value=\"$zone[value]\"".iif($zone[value]==$settings['timezone'], " selected").">$zone[name]</option>";           
  }
  
 print "</select>*/
  print "</td></tr>
    <tr><td>  $phrases[date_format] </td><td><input type=text dir=ltr name=stng[date_format] size=30 value=\"$settings[date_format]\"></td></tr>
</table>
 
 </fieldset>
   <br>
 <table width=70% class=grid>


 <tr><td>$phrases[os_and_browsers_statics] : </td><td><select name=stng[count_visitors_info]>" ;
 if($settings['count_visitors_info']){$chk1 = "selected" ; $chk2 ="" ;}else{ $chk2 = "selected" ; $chk1 ="" ;}
 print "<option value=1 $chk1>$phrases[enabled]</option>
 <option value=0 $chk2>$phrases[disabled]</option>
 </select>
 </td></tr>

  <tr><td>$phrases[visitors_hits_statics] : </td><td><select name=stng[count_visitors_hits]>" ;
 if($settings['count_visitors_hits']){$chk1 = "selected" ; $chk2 ="" ;}else{ $chk2 = "selected" ; $chk1 ="" ;}
 print "<option value=1 $chk1>$phrases[enabled]</option>
 <option value=0 $chk2>$phrases[disabled]</option>
 </select>
 </td></tr>

  <tr><td>$phrases[online_visitors_statics] : </td><td><select name=stng[count_online_visitors]>" ;
 if($settings['count_online_visitors']){$chk1 = "selected" ; $chk2 ="" ;}else{ $chk2 = "selected" ; $chk1 ="" ;}
 print "<option value=1 $chk1>$phrases[enabled]</option>
 <option value=0 $chk2>$phrases[disabled]</option>
 </select>
 </td></tr>


    </table>
                     
                     <br>
 <table width=70% class=grid>
    <tr><td>$phrases[registration] : </td><td><select name=stng[members_register]>" ;
 if($settings['members_register']){$chk1 = "selected" ; $chk2 ="" ;}else{ $chk2 = "selected" ; $chk1 ="" ;}
 print "<option value=1 $chk1>$phrases[cp_opened]</option>
 <option value=0 $chk2>$phrases[cp_closed]</option>
 </select>
 </td></tr>


  <tr><td>$phrases[security_code_in_registration] : </td><td><select name=stng[register_sec_code]>" ;
 if($settings['register_sec_code']){$chk1 = "selected" ; $chk2 ="" ;}else{ $chk2 = "selected" ; $chk1 ="" ;}
 print "<option value=1 $chk1>$phrases[enabled]</option>
 <option value=0 $chk2>$phrases[disabled]</option>
 </select>
 </td></tr>

 <tr><td>$phrases[auto_email_activate]: </td><td><select name=stng[auto_email_activate]>" ;
 if($settings['auto_email_activate']){$chk1 = "selected" ; $chk2 ="" ;}else{ $chk2 = "selected" ; $chk1 ="" ;}
 print "<option value=1 $chk1>$phrases[enabled]</option>
 <option value=0 $chk2>$phrases[disabled]</option>
 </select>
 </td></tr>

 <tr><td>  $phrases[msgs_count_limit] : </td><td><input type=text name=stng[msgs_count_limit] size=5 value='$settings[msgs_count_limit]'>  $phrases[message] </td></tr>

<tr><td>  $phrases[username_min_letters] : </td><td><input type=text name=stng[register_username_min_letters] size=5 value='$settings[register_username_min_letters]'> </td></tr>

<tr><td> $phrases[username_exludes] : </td><td><input type=text name=stng[register_username_exclude_list] dir=ltr size=20 value='$settings[register_username_exclude_list]'> </td></tr>


  </table>
                     <br>
    
 <table width=70% class=grid>                
                   
 <tr><td>$phrases[show_prev_votes] : </td><td><select name=stng[other_votes_show]>" ;
 if($settings['other_votes_show']){$chk1 = "selected" ; $chk2 ="" ;}else{ $chk2 = "selected" ; $chk1 ="" ;}
 print "<option value=1 $chk1>$phrases[enabled]</option>
 <option value=0 $chk2>$phrases[disabled]</option>
 </select>
 </td></tr>
 <tr><td> $phrases[max_count] : </td><td><input type=text name=stng[other_votes_limit] dir=ltr size=4 value='$settings[other_votes_limit]'> </td></tr>

  <tr><td>$phrases[orderby] : </td><td> ";
  print_select_row("stng[other_votes_orderby]",array("rand()"=>"$phrases[random]","id asc"=>"$phrases[the_date] $phrases[asc]","id desc"=>"$phrases[the_date] $phrases[desc]"),$settings['other_votes_orderby']);
  print "</td></tr>
 </table><br>
 
 
 <table width=70% class=grid>
 <tr><td>$phrases[emails_msgs_default_type] : </td><td><select name=stng[mailing_default_use_html]>" ;
 if($settings['mailing_default_use_html']){$chk1 = "selected" ; $chk2 ="" ;}else{ $chk2 = "selected" ; $chk1 ="" ;}
 print "<option value=1 $chk1>HTML</option>
 <option value=0 $chk2>TEXT</option>
 </select>
 </td></tr>
 <tr><td> $phrases[emails_msgs_default_encoding] : </td><td><input type=text name=stng[mailing_default_encoding] size=20 value='$settings[mailing_default_encoding]'> <br> * $phrases[leave_blank_to_use_site_encoding]</td></tr>
</table>";


   //--------------- Load Settings Plugins --------------------------
$pls = load_plugins("settings.php");
  if(is_array($pls)){foreach($pls as $pl){include($pl);}}
//----------------------------------------------------------------

  print "
  <br>
                    <table width=70% class=grid>
  <tr><td>  $phrases[uploader_system] : </td><td><select name=stng[uploader]>" ;
 if($settings['uploader']){$chk1 = "selected" ; $chk2=""; }else{ $chk2 = "selected" ; $chk1="";}
 print "<option value=1 $chk1>$phrases[enabled]</option>
 <option value=0 $chk2>$phrases[disabled]</option>
 </select>
 </td></tr>
 <tr><td> $phrases[disable_uploader_msg]  : </td><td><input type=text name=stng[uploader_msg] size=30 value='$settings[uploader_msg]'></td></tr>
 <tr><td>  $phrases[uploader_path] : </td><td><input dir=ltr type=text name=stng[uploader_path] size=30 value='$settings[uploader_path]'></td></tr>
 <tr><td>  $phrases[uploader_allowed_types] : </td><td><input dir=ltr type=text name=stng[uploader_types] size=30 value='$settings[uploader_types]' style=\"font-family:Arial, Helvetica, sans-serif;font-weight:bold;\"></td></tr>

<tr><td> $phrases[uploader_thumb_width] : </td><td><input type=text name=stng[uploader_thumb_width] size=5 value='$settings[uploader_thumb_width]'> $phrases[pixel] </td></tr>
<tr><td>  $phrases[uploader_thumb_hieght]  : </td><td><input type=text name=stng[uploader_thumb_hieght] size=5 value='$settings[uploader_thumb_hieght]'> $phrases[pixel] </td></tr>


 <tr><td colspan=2 align=center><input type=submit value=' $phrases[edit] '></td></tr>
 </table></center>" ;

         }