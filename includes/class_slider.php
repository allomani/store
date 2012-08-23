<?
// Edited : 07-10-2009 

class slider {
    
    
	var $name;
	var $tabs;
	var $active;
	var $current;

	function __construct($name){
		$this->name = $name;
	}

	function start($name){
		if (empty($this->active)){ $this->active = $name; }
		$this->current = $name;
		ob_start();
	}

	function end(){
		$this->tabs[$this->current] = ob_get_contents();
		ob_end_clean();
	}

	function run(){
        
        global $global_align_x,$global_align,$phrases,$global_dir;
		if (count($this->tabs) > 0){
?>            


<div id="HPSlider" align="center" >
<?
print "

<div class=\"bar\">
<table width=\"100%\"><tr>

     
     <td align=$global_align width=30>
     <div class=\"slider_btnz\">
         <a href='javascript:prevSlide();'><img border=0 src=\"images/arrow_$global_align.gif\" id=\"prev\" title=\"$phrases[prev]\"/></a>
    </div>
    </td>
    
<td align=center dir=$global_dir>

    
    <div id=\"nav\"><table cellpadding=\"0\" cellspacing=\"0\"><tr>";
    $i=1;
           foreach($this->tabs as $tabname => $tabcontent){
                $tabid = "slider_nav_".$i;
                $contentid = "tabcontent_".$this->name."_$tabname";
                echo "<td align=center><DIV CLASS='";
                if ($this->active == $tabname){ echo "slider_active"; }else{ echo "slider_inactive"; }
                echo "' ID='$tabid' ";
                echo "onClick=\"gotoSlide($i);\">$i</DIV></td>\n";
                $i++;
            }
            
           
    print "</tr></table></div></td>
    
    
       <td align=$global_align_x width=30>
   
    <div class=\"slider_btnz\">
        <a href='javascript:nextSlide();'><img border=0 src=\"images/arrow_$global_align_x.gif\" id=\"next\" title=\"$phrases[next]\" /> </a>
    </div>
    </td>
    
   
    
    
   </tr>
    </table>
</div>
             <br>
<div id=\"slider\" style=\"width:100%;\">";



            //echo "<DIV CLASS='tabs'>\n";
            $jsClear = "";
            $i=1;
            foreach($this->tabs as $tabname => $tabcontent){
                //$tabid = "tab_".$this->name."_$tabname";
                //$contentid = "tabcontent_".$this->name."_$tabname";
            //    $jsClear .= "\tdocument.getElementById('$tabid').className = 'tab_inactive';\n";
                //$jsClear .= "\tdocument.getElementById('$contentid').style.display = 'none';\n";
print "<div id=\"slide".$i."\"".iif($i>1,"style=\"display:none;\"").">
 $tabcontent
</div>";

    $i++;
            }
            /*
            echo "<script type=\"text/javascript\">\n";
            echo "function tab_".$this->name."(id){\n";
            echo "$jsClear";
            echo "\tdocument.getElementById('tab_".$this->name."_'+id).className = 'tab_active';\n";
            echo "\tdocument.getElementById('tabcontent_".$this->name."_'+id).style.display = '';\n";
            echo "}\n";
            echo "</script>\n";
         
         
             
             
            echo "<DIV STYLE='float: left; clear:both;'></DIV>\n";
            foreach($this->tabs as $tabname => $tabcontent){
                $contentid = "tabcontent_".$this->name."_$tabname";
                echo "<DIV ID = '$contentid' CLASS='tab_content' STYLE='display: ";
                if ($this->active == $tabname){ echo "block"; }else{ echo "none"; }
                echo ";'>$tabcontent</DIV>\n";
            }
            echo "</DIV>\n";
            */
print "</div>
</div>";
     ?>
            <script language="JavaScript" type="text/javascript">
           //<!--
           //<![CDATA[
           
         var  first = 1;
          var last = <? print $i-1;?>;
          var current = 1;
          var slider_timeout_var = 10000;
           
function slider_timeout(){
clearTimeout(timer);
 timer = setTimeout("nextSlide()" , slider_timeout_var);    
}

//----- run default timer ----//
   timer = setTimeout("nextSlide()" , slider_timeout_var); 
  
  
           function nextSlide() {
               // Hide current picture
               object = document.getElementById('slide' + current);
               object.style.display = 'none';
               
               // Show next picture, if last, loop back to front
               if (current == last) { current = 1; }
               else { current++ }
               object = document.getElementById('slide' + current);
            
               object.style.display = 'block';
               
               for(i=first;i<=last;i++){
              document.getElementById('slider_nav_'+i).className = 'slider_inactive';      
               }
                 document.getElementById('slider_nav_'+current).className = 'slider_active';   
                 
           slider_timeout();     
           }

            function gotoSlide(id) {
               // Hide current picture
               object = document.getElementById('slide' + current);
               object.style.display = 'none';
               
              current=id;
              
               object = document.getElementById('slide' + current);
            
               object.style.display = 'block';
               
               for(i=first;i<=last;i++){
              document.getElementById('slider_nav_'+i).className = 'slider_inactive';      
               }
                 document.getElementById('slider_nav_'+current).className = 'slider_active';   
                 
            slider_timeout();    
           }
           
           function prevSlide() {
               // Hide current picture
               object = document.getElementById('slide' + current);
              object.style.display = 'none';
                
                 
               if (current == first) { current = last; }
               else { current--; }
               object = document.getElementById('slide' + current);
               object.style.display = 'block';
               
               for(i=first;i<=last;i++){
              document.getElementById('slider_nav_'+i).className = 'slider_inactive';      
               }
                 document.getElementById('slider_nav_'+current).className = 'slider_active'; 
                 
              slider_timeout();         
           }
           
           
           function opacity(id, opacStart, opacEnd, millisec) { 
    //speed for each frame 
    var speed = Math.round(millisec / 100); 
    var timer = 0; 

    //determine the direction for the blending, if start and end are the same nothing happens 
    if(opacStart > opacEnd) { 
        for(i = opacStart; i >= opacEnd; i--) { 
            setTimeout("changeOpac(" + i + ",'" + id + "')",(timer * speed)); 
            timer++; 
        } 
    } else if(opacStart < opacEnd) { 
        for(i = opacStart; i <= opacEnd; i++) 
            { 
            setTimeout("changeOpac(" + i + ",'" + id + "')",(timer * speed)); 
            timer++; 
        } 
    } 
} 

//change the opacity for different browsers 
function changeOpac(opacity, id) { 
    var object = document.getElementById(id).style; 
    object.opacity = (opacity / 100); 
    object.MozOpacity = (opacity / 100); 
    object.KhtmlOpacity = (opacity / 100); 
    object.filter = "alpha(opacity=" + opacity + ")"; 
} 


           //]]>
           // -->
       </script>
      
       <?
                     
    
		}
	}
}
?>
