<?
print "<table width=100%>

<tr><td width=24><img src='images/home.gif' width=24></td><td bgcolor=#FFFFFF><a href='index.php'> $phrases[main_page] </a></td></tr>
</table>";

$admin_menu_content  = "";



print "<br>
<fieldset style=\"padding: 2\">
<legend>$phrases[the_orders]</legend>
<table width=100%>";
print "<tr><td width=24><img src='images/orders.gif' width=24></td><td class=row_1><a href='orders.php'>$phrases[the_orders]</a></td></tr>";
print "<tr><td width=24><img src='images/orders_status.gif' width=24></td><td class=row_2><a href='orders_status.php'>$phrases[orders_status]</a></td></tr>";


print "</table></fieldset>"; 





print "<br>
<fieldset style=\"padding: 2\">
<legend>$phrases[the_products]</legend>
<table width=100%>";
print "<tr><td width=24><img src='images/products.gif' width=24></td><td class=row_1><a href='products.php'>$phrases[the_products_and_cats]</a></td></tr>";

print "<tr><td width=24><img src='images/hot_items.gif' width=24></td><td class=row_2><a href='hot_items.php'>$phrases[hot_items]</a></td></tr>"; 


if(if_admin("store_fields",true)){ 
print "<tr><td width=24><img src='images/fields.gif' width=24></td><td class=row_1><a href='store_fields.php'>$phrases[products_fields]</a></td></tr>";
}
print "</table></fieldset>"; 


if(if_admin("",true)){ 
print "<br>
<fieldset style=\"padding: 2\">
<legend>$phrases[payment_and_shipping]</legend>
<table width=100%>";
print "<tr><td width=24><img src='images/payment_methods.gif' width=24></td><td class=row_1><a href='index.php?action=payment_methods'>$phrases[payment_methods]</a></td></tr>";
print "<tr><td width=24><img src='images/gateways.gif' width=24></td><td class=row_2><a href='payment_gateways.php'>$phrases[payment_gateways]</a></td></tr>";
print "<tr><td width=24><img src='images/shipping_methods.gif' width=24></td><td class=row_1><a href='index.php?action=shipping_methods'>$phrases[shipping_methods]</a></td></tr>";

print "</table></fieldset>"; 
}

//-----------------------------
$admin_menu_content  = "";
if(if_admin("",true)){
$admin_menu_content .= "<tr><td width=24><img src='images/blocks.gif' width=24></td><td class=row_1><a href='blocks.php'> $phrases[the_blocks] </a></td></tr>";
}



if(if_admin("votes",true)){
$admin_menu_content .= "<tr><td width=24><img src='images/votes.gif' width=24></td><td class=row_2><a href='index.php?action=votes'> $phrases[the_votes] </a></td></tr>";
}

if(if_admin("news",true)){
$admin_menu_content .= "<tr><td width=24><img src='images/news.gif' width=24></td><td class=row_1><a href='news.php'> $phrases[the_news] </a></td></tr>";
}

if(if_admin("",true)){
$admin_menu_content .= "<tr><td width=24><img src='images/pages.gif' width=24></td><td class=row_2><a href='pages.php'> $phrases[the_pages] </a></td></tr>";
}
//--------------------
if($admin_menu_content){
print "<br>
<fieldset style=\"padding: 2\">
<table width=100%>";
print $admin_menu_content; 
print "</table></fieldset>";
}
//---------------------


if(if_admin("clients",true)){
print "<br>
<fieldset style=\"padding: 2\">
<legend>$phrases[the_members]</legend>
<table width=100%>";
print "<tr><td width=24><img src='images/clients.gif' width=24></td><td class=row_1><a href='index.php?action=clients'> $phrases[cp_mng_members]</a></td></tr>\n";
print "<tr><td width=24><img src='images/clients_custom_fields.gif' width=24></td><td class=row_2><a href='index.php?action=clients_fields'> $phrases[members_custom_fields]</a></td></tr>\n";
print "<tr><td width=24><img src='images/clients_mailing.gif' width=24></td><td class=row_1><a href='index.php?action=clients_mailing'> $phrases[members_mailing]</a></td></tr>\n";

if(if_admin("",true)){  
print "<tr><td width=24><img src='images/clients_remote_db.gif' width=24></td><td class=row_2><a href='index.php?action=clients_remote_db'>$phrases[cp_members_remote_db]</a></td></tr>\n";
print "<tr><td width=24><img src='images/db_clean.gif' width=24></td><td class=row_1><a href='index.php?action=clients_local_db_clean'>$phrases[members_local_db_clean_wizzard]</a></td></tr>\n";
}
print "</table></fieldset>";
}

if(if_admin("",true)){
print "<br>
<fieldset style=\"padding: 2\">
<legend>Geo Settings</legend>
<table width=100%>
<tr><td width=24><img src='images/db_backup.gif' width=24></td><td class=row_2><a href='index.php?action=geo_zones'>Geo Zones</a></td></tr>
<tr><td width=24><img src='images/db_info.gif' width=24></td><td class=row_1><a href='index.php?action=countries'>Countries</a></td></tr>

</table></fieldset>";
}


if(if_admin("",true)){
print "<br>
<fieldset style=\"padding: 2\">
<legend>$phrases[the_database]</legend>
<table width=100%>
<tr><td width=24><img src='images/db_info.gif' width=24></td><td class=row_1><a href='index.php?action=db_info'>$phrases[cp_db_check_repair]</a></td></tr>
<tr><td width=24><img src='images/db_backup.gif' width=24></td><td class=row_2><a href='index.php?action=backup_db'>$phrases[backup]</a></td></tr>
</table></fieldset>";
}

//--------------- Load Menu Plugins --------------------------
$pls = load_plugins("menu.php");
  if(is_array($pls)){foreach($pls as $pl){include($pl);}}
//------------------------//

$admin_menu_content = "";
if(if_admin("adv",true)){
$admin_menu_content .= "<tr><td width=24><img src='images/adv.gif' width=24></td><td class=row_1><a href='banners.php'> $phrases[the_banners] </a></td></tr>";
}

if(if_admin("",true)){
$admin_menu_content .= "<tr><td width=24><img src='images/statics.gif' width=24></td><td class=row_2><a href='index.php?action=statics'>$phrases[the_statics_and_counters]</a></td></tr>";
}
if(if_admin("templates",true)){
$admin_menu_content .= "<tr><td width=24><img src='images/templates.gif' width=24></td><td class=row_1><a href='templates.php'> $phrases[the_templates] </a></td></tr>";
}

if(if_admin("phrases",true)){
$admin_menu_content .= "<tr><td width=24><img src='images/phrases.gif' width=24></td><td class=row_2><a href='index.php?action=phrases'>$phrases[the_phrases]</a></td></tr>";
}


if(if_admin("",true)){ 
$admin_menu_content .= "<tr><td width=24><img src='images/seo_settings.gif' width=24></td><td class=row_1><a href='seo.php'> $phrases[seo_settings]</a></td></tr>";
}


if(if_admin("",true)){  
$admin_menu_content .= "<tr><td width=24><img src='images/hooks.gif' width=24></td><td class=row_1><a href='index.php?action=hooks'>$phrases[cp_hooks]</a></td></tr>\n";



$admin_menu_content .= "<tr><td width=24><img src='images/stng.gif' width=24></td><td class=row_2><a href='index.php?action=settings'> $phrases[the_settings]</a></td></tr>";
} 


//--------------------
if($admin_menu_content){
print "<br>
<fieldset style=\"padding: 2\">
<table width=100%>";
print $admin_menu_content; 
print "</table></fieldset>";
}
//---------------------


print "<br> 
<fieldset style=\"padding: 2\"> 
<table width=100%>";
print "<tr><td width=24><img src='images/users2.gif' width=24></td><td class=row_1><a href='index.php?action=users'>$phrases[users_and_permissions]</a></td></tr>";

if(if_admin("",true)){ 
print "<tr><td width=24><img src='images/access_log.gif' width=24></td><td class=row_2><a href='index.php?action=access_log'>$phrases[access_log]</a></td></tr>";
}


print "<tr><td width=24><img src='images/logout.gif' width=24></td><td class=row_1><a href='login.php?action=logout'> $phrases[logout] </a></td></tr>";


print "</table></fieldset>";


unset($admin_menu_content);