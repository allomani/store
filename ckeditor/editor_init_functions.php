<?

function encodeHTML($sHTML)
		{
		$sHTML=str_replace("&","&amp;",$sHTML);
        $sHTML=str_replace("<","&lt;",$sHTML);
        $sHTML=str_replace(">","&gt;",$sHTML);
		return $sHTML;
		}

function editor_html_init(){
global $scripturl ;

print "<script language=JavaScript src='".$scripturl."/ckeditor/ckeditor.js'></script>" ;

}

function editor_init() {

}

function editor_print_form($name,$width,$height,$content){
    global $global_dir,$global_lang;
	print "<textarea id=\"$name\" name=\"$name\" rows=4 cols=30>\n";



if($content){
	print encodeHTML($content);
	}else{
        print encodeHTML("<div dir=$global_dir></div>");
    }

print "</textarea>

	<script>
		CKEDITOR.replace('$name',{language:'".iif($global_lang=="arabic","ar","en")."'});
	</script>";

	}
