<HTML> 
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
print "<title>".html_encode_chars($_GET['title'])."</title> 
</HEAD> 
<BODY onload='FitPic();' topmargin=\"0\"  
marginheight=\"0\" leftmargin=\"0\" marginwidth=\"0\">"; 

 print "<img src=\"" . html_encode_chars(strip_tags($_GET['url'])) . "\" border=0> 

</BODY> 
</HTML>";
}else{
    die();
}
?> 

