<?
require('./start.php');   
?>
<style>
	.connectedSortable { list-style-type: none; margin: 0; padding: 0 0 2.5em; float: left; margin-right: 10px; }
	.connectedSortable li { margin: 0 5px 5px 5px; padding: 5px; font-size: 1.2em; width: 120px; }
	</style>
	<script>
	jQuery(function() {
		jQuery(".connectedSortable" ).sortable({
			connectWith: ".connectedSortable",
                        update: function(event,ui) { 
                            
                         var sortable = jQuery(this);

            // To avoid double-firing the event, return if it's not the sortable
            // where the item was dropped into.
            if(ui.item.parent()[0] != this) return;

            // Create object from the current sortable to post
            var postData = {};
            postData['action'] = 'test';
            postData['blocks'] = {};
            postData['blocks'][sortable.attr('id')] =sortable.sortable('serialize');
        

            // If the item came from a connected sortable, include that in the post too
            if(ui.sender){
                postData['blocks'][ui.sender.attr('id')] = ui.sender.sortable('serialize');
    
            } 
                
	jQuery.post("ajax.php",postData,function(data){alert(data);});	
	}
        });
        });
	</script>


<div class="demo">

<ul id="l" class="connectedSortable">
	<li class="ui-state-default" id="item_1">Item 1</li>
	<li class="ui-state-default" id="item_2">Item 2</li>
	<li class="ui-state-default" id="item_3">Item 3</li>
</ul>
    
<ul id="r" class="connectedSortable">
	<li class="ui-state-default" id="item_4">Item 4</li>
	<li class="ui-state-default" id="item_5">Item 5</li>
	<li class="ui-state-default" id="item_6">Item 6</li>
</ul>
    
 
<ul id="c" class="connectedSortable">
	<li class="ui-state-default" id="item_7">Item 7</li>
	<li class="ui-state-default" id="item_8">Item 8</li>
	<li class="ui-state-default" id="item_9">Item 9</li>
</ul>
    
    


</div>