<?
function get_comments_box($type='',$id){
    global $phrases,$settings,$global_align_x,$global_align,$comment_type,$comment_id;
  $comment_type = $type;
  $comment_id = $id;
  run_template('comments_box');
  
}


function get_comment($data){
    global $tr_class,$data,$links,$settings,$phrases,$member_data,$check_admin_login,$style;
    $c = "";
    if($data['username']){
     $c = "<table width=100%>";         
        $c .= "<tr class=\"$tr_class\" id='comment_{$data['id']}'>";
     
        $c .= "
        <td width='75%'><a href=\"".str_replace("{id}",$data['uid'],$links['profile'])."\" title=\"$data[username]\"><b>$data[username]</b></a>  : $data[content]  
       ".iif($check_admin_login || $data['uid'] == $member_data['id'],"&nbsp; &nbsp; &nbsp;[<a href=\"javascript:;\" onClick=\"if(confirm('$phrases[are_you_sure]')){comments_delete($data[id]);}\">$phrases[delete]</a>]")."
       </td><td width='25%'>
         ".time_duration((time()-$data['time']))." </td>
         ".iif($settings['reports_enabled'],"<td><a href=\"javascript:;\" onClick=\"report($data[id],'comment');\"><img src=\"$style[images]/report.gif\" title=\"$phrases[report_do]\" border=0></a></td>")."</tr>";
          $c .= "</table>"; 
    }   
        return $c;   
} 
?>