<?php
/**
 *  Allomani E-Store v1.0
 * 
 * @package Allomani.E-Store
 * @version 1.0
 * @copyright (c) 2006-2018 Allomani , All rights reserved.
 * @author Ali Allomani <info@allomani.com>
 * @link http://allomani.com
 * @license GNU General Public License version 3.0 (GPLv3)
 * 
 */

require("global.php");
print "<?xml version=\"1.0\" encoding=\"$settings[site_pages_encoding]\" ?> \n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.google.com/schemas/sitemap/0.84 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
<?

//---------- cats -------------
$qr=db_query("select id,name from store_products_cats where active=1 order by id desc");
while($data = db_fetch($qr)){
print "<url>
<loc>$scripturl/".str_replace('{id}',$data['id'],$links['links_browse_products'])."</loc>
<changefreq>daily</changefreq>
<priority>0.50</priority>
</url>";    
}
//---------- Products -------------
$qr=db_query("select id,name from store_products_data where active=1 order by id desc");
while($data = db_fetch($qr)){
print "<url>
<loc>$scripturl/".str_replace('{id}',$data['id'],$links['links_product_details'])."</loc>
<changefreq>daily</changefreq>
<priority>0.50</priority>
</url>";    
}


//---------- Pages -------------
$qr=db_query("select id from store_pages where active=1 order by id desc");
while($data = db_fetch($qr)){
print "<url>
<loc>$scripturl/".str_replace('{id}',$data['id'],$links['links_pages'])."</loc>
<changefreq>daily</changefreq>
<priority>0.50</priority>
</url>";    
}

//---------- News -------------
$qr=db_query("select id from store_news order by id desc");
while($data = db_fetch($qr)){
print "<url>
<loc>$scripturl/".str_replace('{id}',$data['id'],$links['links_browse_news'])."</loc>
<changefreq>daily</changefreq>
<priority>0.50</priority>
</url>";    
}
//--------------------------

print "</urlset>";

