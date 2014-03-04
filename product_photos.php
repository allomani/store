<?
include "global.php";
print "<html dir=\"$settings[html_dir]\">
<head>
<META http-equiv=Content-Language content=\"$settings[site_pages_lang]\">
<META http-equiv=Content-Type content=\"text/html; charset=$settings[site_pages_encoding]\">";
print "<LINK href='css.php' type=text/css rel=StyleSheet>";
?>
<script language='javascript'> 
 
   var NS = (navigator.appName=="Netscape")?true:false; 

     function FitPic() { 
       iWidth = (NS)?window.innerWidth:document.body.clientWidth; 
       iHeight = (NS)?window.innerHeight:document.body.clientHeight; 
      
      if(document.images[0].width < 500){
         iWidth = 500 - iWidth ;
      }else{
          iWidth = document.images[0].width - iWidth;   
      } 
       
      
       iHeight = (document.images[0].height - iHeight)+200; 
       
      
       
       window.resizeBy(iWidth, iHeight); 
       self.focus(); 
     }; 
 </script>
 <?
$pid = intval($pid);


$qr=db_query("select * from store_products_photos where product_id='$pid' order by id");
if(db_num($qr)){
    
    unset($photos_arr);
    $c=0;
    while($data=db_fetch($qr)){
    $photos_arr[$c]['name'] = $data['name'];
    $photos_arr[$c]['id'] = $data['id'];
    $photos_arr[$c]['img']=$data['img'];
    $photos_arr[$c]['thumb']=$data['thumb'];
    if($id == $data['id']){$cur_index=$c;}  
    $c++;  
    }
 
$cur_index = intval($cur_index);

//---- show img -----//
if($photos_arr[$cur_index]['img']){
    
$prev_index = $cur_index - 1;
$next_index = $cur_index + 1;
 $photo_title = "Photo # ".($cur_index+1)." of ".count($photos_arr).iif($photos_arr[$cur_index]['name']," - ".html_encode_chars($photos_arr[$cur_index]['name'])) ;
 print "
 <title>$photo_title</title> 
</head> 
<BODY onload='FitPic();' topmargin=\"0\" marginheight=\"0\" leftmargin=\"0\" marginwidth=\"0\">"; 


    print "<center>".iif($photos_arr[$next_index]['id'],"<a href='product_photos.php?pid=$pid&id=".$photos_arr[$next_index]['id']."'>");
    print "<img src=\"".$photos_arr[$cur_index]['img']."\" border=0 title=\"$photo_title\">";
    print iif($photos_arr[$next_index]['id'],"</a>")."<br>".$photos_arr[$cur_index]['name']."</center>";
    
    print "<table width=100% dir='$global_dir'><tr>
    <td align=\"$global_align\">
    ".iif($photos_arr[$prev_index]['id'],"<a href='product_photos.php?pid=$pid&id=".$photos_arr[$prev_index]['id']."'><img border=0 src=\"images/arrow_".$global_align.".gif\" id=\"prev\" title=\"$phrases[prev]\"/></a>")."</td>
    <td width=100% align=center>
    ".iif($photos_arr[$prev_index]['id'],"<a href='product_photos.php?pid=$pid&id=".$photos_arr[$prev_index]['id']."'><img border=0 src=\"".$photos_arr[$prev_index]['thumb']."\"></a>&nbsp;")
    ."<img border=0 src=\"".$photos_arr[$cur_index]['thumb']."\" border=1 style=\"border-color:#000000;border:1;border-style: solid; border-width: 2px\">"
    .iif($photos_arr[$next_index]['id'],"&nbsp;<a href='product_photos.php?pid=$pid&id=".$photos_arr[$next_index]['id']."'><img border=0 src=\"".$photos_arr[$next_index]['thumb']."\"></a>")."
    </td>
    <td align=\"$global_align_x\">
    ".iif($photos_arr[$next_index]['id'],"<a href='product_photos.php?pid=$pid&id=".$photos_arr[$next_index]['id']."'><img border=0 src=\"images/arrow_".$global_align_x.".gif\" id=\"next\" title=\"$phrases[next]\" /></a>")."</td>
    
    </tr></table>
    
    </body></html>";
}
    
}else{
    print "<center>$phrases[err_wrong_url]</center>";
}