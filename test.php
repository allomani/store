<?
include "global.php";   ?>

    <link rel="stylesheet" href="themes/default/default.css" type="text/css" media="screen" />
      <link rel="stylesheet" href="themes/pascal/pascal.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="themes/orman/orman.css" type="text/css" media="screen" />
   
    <link rel="stylesheet" href="nivo-slider.css" type="text/css" media="screen" />
 
      <link rel="stylesheet" href="xstyle.css" type="text/css" media="screen" /> 


    
     

        <div class="slider-wrapper theme-default">
        
            <div id="slider" class="nivoSlider">
            
        <?
       $qr=db_query("select * from store_banners where `type` like 'offer' and active=1 order by ord");   
        while($data=db_fetch($qr)){

$ids[] = $data['id'];


print "<a href=\"banner.php?id=$data[id]\"><img border=0 src=\"$data[img]\"  title=\"$data[title]\" /></a>
";

}

   ?>

       
                       
            </div>
          
        </div>

   
    <script type="text/javascript" src="js/jquery.js"></script>
    <script> jQuery.noConflict(); </script>  
    <script type="text/javascript" src="js/jquery.nivo.slider.js"></script>
    <script type="text/javascript">
    jQuery(window).load(function() {
        jQuery('#slider').nivoSlider();
    });
    </script>


<?

/*
include "global.php";


$qr=db_query("select * from store_banners where `type` like 'offer' and active=1 order by ord");
if(db_num($qr)){


print "<script type=\"text/javascript\" src=\"js/jquery.js\"></script> 
<script> jQuery.noConflict(); </script>
<script type=\"text/javascript\" src=\"js/jquery.nivo.slider.js\"></script>

<link rel=\"stylesheet\" href=\"themes/default/default.css\" type=\"text/css\" media=\"screen\" />

<link rel=\"stylesheet\" href=\"nivo-slider.css\" type=\"text/css\" media=\"screen\" />
";
 
print "
  <div id=\"wrapper\">

<div class=\"slider-wrapper theme-default\">
  <div class=\"ribbon\"></div>
 <div id=\"slider\" class=\"nivoSlider\">";

while($data=db_fetch($qr)){

$ids[] = $data['id'];


print iif($data['url'],"<a href=\"banner.php?id=$data[id]\" target=_blank>")."<img border=0 src=\"$data[img]\" title=\"$data[title]\" />".iif($data['url'],"</a>
");
}

   ?>
    <a href="http://dev7studios.com"><img src="http://localhost/store200/data/banners/offer_1.gif" alt="" title="This is an example of a caption" /></a>
    <?
print " </div>

    </div>
    
    </div>";
?>
  <script type="text/javascript">
    jQuery(window).load(function() {
        jQuery('#slider').nivoSlider();
    });
    </script>
<?

db_query("update store_banners set views=views+1 where id IN (".implode(",",$ids).")");

}         */
?>
