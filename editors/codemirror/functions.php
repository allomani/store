<?
function code_editor_init($field_id){
    global $code_editor_path,$scripturl;
    ?>
 <link rel="stylesheet" href="<?=$scripturl."/".$code_editor_path?>/lib/codemirror.css">
    <script src="<?=$scripturl."/".$code_editor_path?>/lib/codemirror.js"></script>
    <script src="<?=$scripturl."/".$code_editor_path?>/mode/php/php.js"></script>
    <script src="<?=$scripturl."/".$code_editor_path?>/mode/clike/clike.js"></script> 
    <script src="<?=$scripturl."/".$code_editor_path?>/mode/xml/xml.js"></script> 
    <script src="<?=$scripturl."/".$code_editor_path?>/mode/css/css.js"></script> 
    <script src="<?=$scripturl."/".$code_editor_path?>/mode/javascript/javascript.js"></script> 
        
   

    <style type="text/css">
      .CodeMirror {
        border: 1px solid #ccc;
        direction:ltr;
        text-align:left;
       width:99%;
      }
      .CodeMirror-scroll {
        height: 300px;
        overflow-y: auto;
        overflow-x: scroll;
        width: 100%;
      }
    </style>
    
    <script>
      var editor = CodeMirror.fromTextArea(document.getElementById("<?=$field_id;?>"), {
       matchBrackets: true,
        mode: "application/x-httpd-php",
        indentUnit: 4,
        indentWithTabs: true,
        enterMode: "keep",
        tabMode: "shift",
        lineNumbers: true,
        lineWrapping: true,
        fixedGutter : true
      });
    </script>
    
<?
    }