<?
print "<table width=100%>

<tr><td width=24><img src='images/home.gif' width=24></td><td bgcolor=#FFFFFF><a href='index.php'> $phrases[main_page] </a></td></tr>
</table>";

$admin_menu_content  = "";



print "
<fieldset>
<legend>$phrases[the_orders]</legend>
<table width=100%>";
print "<tr><td width=24><img src='images/orders.gif' width=24></td><td class=row_1><a href='orders.php'>$phrases[the_orders]</a></td></tr>";
print "<tr><td width=24><img src='images/votes.gif' width=24></td><td class=row_1><a href='statistics.php'>$phrases[the_statics]</a></td></tr>";


print "</table></fieldset>"; 





print "
<fieldset>
<legend>$phrases[the_products]</legend>
<table width=100%>";
print "<tr><td width=24><img src='images/products.gif' width=24></td><td class=row_1><a href='products.php'>$phrases[the_products_and_cats]</a></td></tr>";

print "<tr><td width=24><img src='images/hot_items.gif' width=24></td><td class=row_2><a href='hot_items.php'>$phrases[hot_items]</a></td></tr>"; 


if(if_admin("store_fields",true)){ 
print "<tr><td width=24><img src='images/fields.gif' width=24></td><td class=row_1><a href='store_fields.php'>$phrases[products_fields]</a></td></tr>";
}
print "</table></fieldset>"; 




//-----------------------------
$admin_menu_content  = "";
if(if_admin("",true)){
$admin_menu_content .= "<tr><td width=24><img src='images/blocks.gif' width=24></td><td class=row_1><a href='blocks.php'> $phrases[the_blocks] </a></td></tr>";
}



if(if_admin("votes",true)){
$admin_menu_content .= "<tr><td width=24><img src='images/votes.gif' width=24></td><td class=row_2><a href='votes.php'> $phrases[the_votes] </a></td></tr>";
}

if(if_admin("news",true)){
$admin_menu_content .= "<tr><td width=24><img src='images/news.gif' width=24></td><td class=row_1><a href='news.php'> $phrases[the_news] </a></td></tr>";
}

if(if_admin("",true)){
$admin_menu_content .= "<tr><td width=24><img src='images/pages.gif' width=24></td><td class=row_2><a href='pages.php'> $phrases[the_pages] </a></td></tr>";
}

//--------------------
if($admin_menu_content){
print "
<fieldset>
<table width=100%>";
print $admin_menu_content; 
print "</table></fieldset>";
}
//---------------------


$admin_menu_content = "";
 
if(if_admin("comments",true)){
$admin_menu_content .= "<tr><td width=24><img src='images/comments.gif' width=24></td><td class=row_1><a href='comments.php'> $phrases[the_comments]</a></td></tr>\n";
}

if(if_admin("reports",true)){
$admin_menu_content .= "<tr><td width=24><img src='images/reports.gif' width=24></td><td class=row_2><a href='reports.php'> $phrases[the_reports]</a></td></tr>\n";
}




//--------------------
if($admin_menu_content){
print "
<fieldset>
<table width=100%>";
print $admin_menu_content; 
print "</table></fieldset>";
unset($admin_menu_content); 
}
//---------------------



if(if_admin("clients",true)){
print "
<fieldset>
<legend>$phrases[the_members]</legend>
<table width=100%>";
print "<tr><td width=24><img src='images/clients.gif' width=24></td><td class=row_1><a href='index.php?action=clients'> $phrases[cp_mng_members]</a></td></tr>\n";
print "<tr><td width=24><img src='images/clients_mailing.gif' width=24></td><td class=row_1><a href='index.php?action=clients_mailing'> $phrases[members_mailing]</a></td></tr>\n";
print "</table></fieldset>";
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


$admin_menu_content .= "<tr><td width=24><img src='images/stng.gif' width=24></td><td class=row_2><a href='settings.php'> $phrases[the_settings]</a></td></tr>";



//--------------------
if($admin_menu_content){
print "
<fieldset>
<table width=100%>";
print $admin_menu_content; 
print "</table></fieldset>";
}
//---------------------


print " 
<fieldset> 
<table width=100%>";
print "<tr><td width=24><img src='images/users2.gif' width=24></td><td class=row_1><a href='users.php'>$phrases[users_and_permissions]</a></td></tr>";

if(if_admin("",true)){ 
print "<tr><td width=24><img src='images/access_log.gif' width=24></td><td class=row_2><a href='index.php?action=access_log'>$phrases[access_log]</a></td></tr>";
}


print "<tr><td width=24><img src='images/logout.gif' width=24></td><td class=row_1><a href='login.php?action=logout'> $phrases[logout] </a></td></tr>";


print "</table></fieldset>";


unset($admin_menu_content);