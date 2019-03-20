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
?>
<HEAD>  
 <script language='javascript'> 
 
   var NS = (navigator.appName=="Netscape")?true:false; 

     function FitPic() { 
       iWidth = (NS)?window.innerWidth:document.body.clientWidth; 
       iHeight = (NS)?window.innerHeight:document.body.clientHeight; 
       iWidth = document.images[0].width - iWidth; 
       iHeight = document.images[0].height - iHeight; 
       window.resizeBy(iWidth, iHeight); 
       self.focus(); 
     }; 
 </script>

<?
if(!strpos(strtolower($_GET['url']),"javascript")){
print "<title>".htmlspecialchars($_GET['title'])."</title> 
</HEAD> 
<BODY onload='FitPic();' topmargin=\"0\"  
marginheight=\"0\" leftmargin=\"0\" marginwidth=\"0\">"; 

 print "<img src=\"" . htmlspecialchars(strip_tags($_GET['url'])) . "\" border=0> 

</BODY> 
</HTML>";
}else{
    die();
}
?> 

