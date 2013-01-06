<?
function get_comments_box($type='',$id){
    global $phrases,$settings,$global_align_x,$global_align,$comment_type,$comment_id;
  $comment_type = $type;
  $comment_id = $id;
  run_template('comments_box');
  
}


function get_comment($data){
    global $tr_class,$data,$links,$settings,$phrases,$member_data,$check_admin_login,$style;
    ob_start();
    run_template("comment");
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
} 
?>