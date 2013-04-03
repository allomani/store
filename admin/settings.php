<?

require('./start.php');  

print "
<fieldset>
<legend>$phrases[the_settings]</legend>
<ul class='settings'>";
print "<li><a href='settings_general.php'><img src='images/stng.gif'><br>$phrases[general_settings]</a></li>";
print "<li><a href='orders_status.php'><img src='images/orders_status.gif'><br>$phrases[orders_status]</a></li>";
print "</ul>
  </fieldset>";

if(if_admin("clients",true)){
print "
<fieldset>
<legend>$phrases[the_members]</legend>
<ul class='settings'>";
print "<li><a href='clients_fields.php'><img src='images/clients_custom_fields.gif'><br>$phrases[members_custom_fields]</a></li>";

if(if_admin("",true)){  
print "<li><a href='clients_remote_db.php'><img src='images/clients_remote_db.gif'><br>$phrases[cp_members_remote_db]</a></li>\n";
print "<li><a href='clients_remote_db.php?action=clients_local_db_clean'><img src='images/db_clean.gif'><br>$phrases[members_local_db_clean_wizzard]</a></li>\n";
}
print "</ul></fieldset>";
}


if(if_admin("",true)){ 
print "
<fieldset>
<legend>$phrases[payment_and_shipping]</legend>
<ul class='settings'>";
print "<li><a href='payment_methods.php'><img src='images/payment_methods.gif'><br>$phrases[payment_methods]</a></li>";
print "<li><a href='payment_gateways.php'><img src='images/gateways.gif'><br>$phrases[payment_gateways]</a></li>";
print "<li><a href='shipping_methods.php'><img src='images/shipping_methods.gif'><br>$phrases[shipping_methods]</a></li>";

print "</ul></fieldset>"; 
}



if(if_admin("",true)){
print "
<fieldset>
<legend>الإعدادات الجغرافية</legend>
<ul class='settings'>
<li><a href='index.php?action=geo_zones'><img src='images/db_backup.gif'><br>المناطق الجغرافية</a></li>
<li><a href='index.php?action=countries'><img src='images/db_info.gif'><br>الدول</a></li>

</ul></fieldset>";
}


print "
<fieldset>
<legend></legend>
<ul class='settings'>";
if(if_admin("templates",true)){
print "<li><a href='templates.php'><img src='images/templates.gif'><br>$phrases[the_templates] </a></li>";
}

if(if_admin("phrases",true)){
print "<li><a href='phrases.php'><img src='images/phrases.gif'><br>$phrases[the_phrases]</a></li>";
}


if(if_admin("",true)){ 
print "<li><a href='seo.php'><img src='images/seo_settings.gif'><br>$phrases[seo_settings]</a></li>";
print "<li><a href='hooks.php'><img src='images/hooks.gif'><br>$phrases[cp_hooks]</a></li>\n";
}


print "</ul></fieldset>";

if(if_admin("",true)){
print "
<fieldset>
<legend>$phrases[the_database]</legend>
<ul class='settings'>
<li><a href='index.php?action=db_info'><img src='images/db_info.gif'><br>$phrases[cp_db_check_repair]</a></li>
<li><a href='index.php?action=backup_db'><img src='images/db_backup.gif'><br>$phrases[backup]</a></li>
</ul></fieldset>";
}



//-----------end ----------------
 require(ADMIN_DIR.'/end.php');