<?

function form_start($action, $method = "post") {

    print "<form action=\"$action\" method=\"$method\">";
}

function form_end() {
    print "</form>";
}

function form_text($title, $name, $value = "", $size = 20) {
    //  print "<p><label for=\"$name\">$title</lable>";
    print "<input type=\"text\" name=\"$name\" id=\"$name\" value=\"$value\" size=\"$size\">";
}

function form_hidden($name, $value) {
    print "<input type=\"hidden\" name=\"$name\" value=\"$value\">";
}

function form_submit($value) {
    print "<input type='submit' value=\"$value\">";
}

function form_editor($title, $name, $value = "") {
    //  print "<p> <label for=\"$name\">$title</lable> ";
    editor_print_form($name, 600, 300, $value);
    //   print "</p>";
}

function form_textarea($title, $name, $value = "", $cols = 30, $rows = 5, $dir = 'rtl') {
    //   print "<label for=\"$name\">$title</lable> 
    print "<textarea cols=\"$cols\" rows=\"$rows\" name=\"$name\" dir=\"$dir\">" . htmlspecialchars($value) . "</textarea>";
}

//----------- select row ------------
function print_select_row($name, $array, $selected = '', $options = "", $size = 0, $multiple = false, $same_values = false) {

    $select = "<select name=\"$name\" id=\"sel_$name\"" . iif($size, " size=\"$size\"") . iif($multiple, ' multiple="multiple"') . iif($options, " $options") . ">\n";
    $select .= construct_select_options($array, $selected, $same_values);
    $select .= "</select>\n";

    print $select;
}

function construct_select_options($array, $selectedid = '', $same_values = false) {
    if (is_array($array)) {
        $options = '';
        foreach ($array AS $key => $val) {
            if (is_array($val)) {
                $options .= "\t\t<optgroup label=\"" . $key . "\">\n";
                $options .= construct_select_options($val, $selectedid, $tabindex, $htmlise);
                $options .= "\t\t</optgroup>\n";
            } else {
                if (is_array($selectedid)) {
                    $selected = iif(in_array($key, $selectedid), ' selected="selected"', '');
                } else {
                    $selected = iif($key == $selectedid, ' selected="selected"', '');
                }
                $options .= "\t\t<option value=\"" . ($same_values ? $val : $key) . "\"$selected>" . $val . "</option>\n";
            }
        }
    }
    return $options;
}

//---------- print text row ----------
function print_text_row($name, $value = "", $size = "", $dir = "", $options = "") {
    print "<input type=text name=\"$name\"" . iif($value, " value=\"$value\"") . iif($size, " size=\"$size\"") . iif($dir, " dir=\"$dir\"") . iif($options, " $options") . ">";
}
