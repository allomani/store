<?

require('./start.php');  

print "
<fieldset>
<legend></legend>
<table width=100%>";
print "<tr><td width=24><img src='images/settings.gif' width=24></td><td class=row_2><a href='settings_general.php'>$phrases[the_settings]</a></td></tr>";


print "<tr><td width=24><img src='images/orders_status.gif' width=24></td><td class=row_2><a href='orders_status.php'>$phrases[orders_status]</a></td></tr>";

print "</table></fieldset>";
if(if_admin("clients",true)){
print "
<fieldset>
<legend>$phrases[the_members]</legend>
<table width=100%>";
print "<tr><td width=24><img src='images/clients_custom_fields.gif' width=24></td><td class=row_2><a href='clients_fields.php'> $phrases[members_custom_fields]</a></td></tr>\n";

if(if_admin("",true)){  
print "<tr><td width=24><img src='images/clients_remote_db.gif' width=24></td><td class=row_2><a href='clients_remote_db.php'>$phrases[cp_members_remote_db]</a></td></tr>\n";
print "<tr><td width=24><img src='images/db_clean.gif' width=24></td><td class=row_1><a href='clients_remote_db.php?action=clients_local_db_clean'>$phrases[members_local_db_clean_wizzard]</a></td></tr>\n";
}
print "</table></fieldset>";
}


if(if_admin("",true)){ 
print "
<fieldset>
<legend>$phrases[payment_and_shipping]</legend>
<table width=100%>";
print "<tr><td width=24><img src='images/payment_methods.gif' width=24></td><td class=row_1><a href='payment_methods.php'>$phrases[payment_methods]</a></td></tr>";
print "<tr><td width=24><img src='images/gateways.gif' width=24></td><td class=row_2><a href='payment_gateways.php'>$phrases[payment_gateways]</a></td></tr>";
print "<tr><td width=24><img src='images/shipping_methods.gif' width=24></td><td class=row_1><a href='shipping_methods.php'>$phrases[shipping_methods]</a></td></tr>";

print "</table></fieldset>"; 
}



if(if_admin("",true)){
print "
<fieldset>
<legend>Geo Settings</legend>
<table width=100%>
<tr><td width=24><img src='images/db_backup.gif' width=24></td><td class=row_2><a href='index.php?action=geo_zones'>Geo Zones</a></td></tr>
<tr><td width=24><img src='images/db_info.gif' width=24></td><td class=row_1><a href='index.php?action=countries'>Countries</a></td></tr>

</table></fieldset>";
}


print "
<fieldset>
<legend></legend>
<table width=100%>";
if(if_admin("templates",true)){
print "<tr><td width=24><img src='images/templates.gif' width=24></td><td class=row_1><a href='templates.php'> $phrases[the_templates] </a></td></tr>";
}

if(if_admin("phrases",true)){
print "<tr><td width=24><img src='images/phrases.gif' width=24></td><td class=row_2><a href='phrases.php'>$phrases[the_phrases]</a></td></tr>";
}


if(if_admin("",true)){ 
print "<tr><td width=24><img src='images/seo_settings.gif' width=24></td><td class=row_1><a href='seo.php'> $phrases[seo_settings]</a></td></tr>";
print "<tr><td width=24><img src='images/hooks.gif' width=24></td><td class=row_1><a href='hooks.php'>$phrases[cp_hooks]</a></td></tr>\n";
}


print "</table></fieldset>";

if(if_admin("",true)){
print "
<fieldset>
<legend>$phrases[the_database]</legend>
<table width=100%>
<tr><td width=24><img src='images/db_info.gif' width=24></td><td class=row_1><a href='index.php?action=db_info'>$phrases[cp_db_check_repair]</a></td></tr>
<tr><td width=24><img src='images/db_backup.gif' width=24></td><td class=row_2><a href='index.php?action=backup_db'>$phrases[backup]</a></td></tr>
</table></fieldset>";
}



//-----------end ----------------
 require(ADMIN_DIR.'/end.php');