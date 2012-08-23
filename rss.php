<?
header('Content-type: text/xml');
include "global.php" ;
print "<?xml version=\"1.0\" encoding=\"$settings[site_pages_encoding]\" ?> \n";
?>
<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/" 
                   xmlns:av="http://www.searchvideo.com/schemas/av/1.0">

<channel>
<? print "<title><![CDATA[$sitename]]></title>\n
<description><![CDATA[$settings[header_description]]]></description>";?> 
<?print "<link>http://".$_SERVER['HTTP_HOST']."</link>\n";
print "<copyright><![CDATA[$settings[copyrights_sitename]]]></copyright>";

$cat = (int) $cat  ;




  $sql_query = "select * from store_products_data " ;
 if($cat){ 
   $cats_arr = get_products_cats($cat);
  
    $sql_query .= " where cat IN (".implode(',',$cats_arr).") ";   
   }
   
     $sql_query .= " order by id desc limit 200";

$qr=db_query($sql_query) ;  

while($data = db_fetch($qr)){

$data_cat = db_qr_fetch("select name,id from store_products_cats where id='$data[cat]'");
   print "  <item>
        <title><![CDATA[".$data["name"]."]]></title>
        <description><![CDATA[<img src=\"$scripturl/".get_image($data['thumb'])."\" width=80 height=80><br>".$data["content"]."\n\n <br><br>
        <b>$phrases[the_price] : <b> $data[price] $settings[currency] ]]></description>"; 
                print "
        <link>".htmlentities($scripturl."/".str_replace(array('{cat}','{id}'),array('1',$data['id']),$links['links_product_details']))."</link>
        <pubDate>$data[date]</pubDate>
       <category><![CDATA[$data_cat[name]]]></category>
     </item>\n";
     }

	
print "</channel>
</rss>";