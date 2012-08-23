
jQuery.noConflict(); 
var slideshow=true;

var timer;

function goNext(){
  jQuery('#next').click();  
}

 
function slider_timeout(){
clearTimeout(timer);
 timer = setTimeout("goNext()" , 10000);    
}

jQuery(document).ready(function($){

jQuery('#slider').cycle({
                fx:  'fade',
                timeout:  0,
                pager:  '#nav',
                next : "#next",
                prev : "#prev",
                prevNextClick: function(isNext, zeroBasedSlideIndex, slideElement){slider_timeout();},
                pagerClick: function(zeroBasedSlideIndex, slideElement){slider_timeout();}
            });
});      

   timer = setTimeout("goNext()" , 10000);   
      


