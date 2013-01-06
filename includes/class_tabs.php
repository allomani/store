<?
class tabs {
	var $name;
	var $tabs;
	var $tabs_names;
        var $current;
        
	function __construct($name){
		$this->name = $name;
                $this->current = 0;    
	}

	function start($name){
            $this->current +=  1;
            $this->tabs_names[$this->current] = $name;
		ob_start();
	}

	function end(){
		$this->tabs[$this->current] = ob_get_contents();
		ob_end_clean();
	}

	function run(){
		if (count($this->tabs) > 0){
			print "<div class='tabs_wrapper'>
                            <div id=\"{$this->name}\">\n";
		
                     print "<ul>";
                foreach($this->tabs as $tabname => $tabcontent){
                    print "<li><a href=\"#{$this->name}_{$tabname}\">{$this->tabs_names[$tabname]}</a></li>";
                    }
                    print "</ul>";
                    
            	foreach($this->tabs as $tabname => $tabcontent){
				print "<div id=\"{$this->name}_{$tabname}\">{$tabcontent}</div>";
			}
          
			echo "</div>
                            </div>\n";
			echo "<div class='clear'></div>\n";
                        print "
                            <script>
                            init_tabs('{$this->name}');
                            </script>";
		}
	}
}
?>
