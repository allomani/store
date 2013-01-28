<?
function get_statics_info($sql,$count_name,$count_data){

global $if_img,$year,$global_align,$style,$scripturl ;

 $qr_stat=db_query($sql);
if (db_num($qr_stat)){
   $stats = array(); 
while($data_stat=db_fetch($qr_stat)){
$stats[$data_stat[$count_name]] = $data_stat[$count_data];
$total = $total + $data_stat[$count_data];
}

$name_suffix = rand(0,99);

?>
 <div id="stats_<?=$name_suffix;?>" style="width:600px;height:300px;"></div>
  <script language="javascript" type="text/javascript" src="<?=$scripturl;?>/js/jquery.flot.js"></script>
    <script language="javascript" type="text/javascript" src="<?=$scripturl;?>/js/jquery.flot.categories.js"></script>
<script type="text/javascript">
$(function () {
    var data = [ <? foreach($stats as $key=>$val){ print "['".$key."',".$val."],";}?> ];
    
    $.plot($("#stats_<?=$name_suffix;?>"), [ data ], {
        series: {
            bars: {
                show: true,
                barWidth: 0.6,
                align: "center" }
        },
        xaxis: {
            mode: "categories",
            tickLength: 0
        }
    });
});
</script>
 <?
         print "<br>";

  $l_size = @getimagesize("$style[images]/leftbar.gif");
    $m_size = @getimagesize("$style[images]/mainbar.gif");
    $r_size = @getimagesize("$style[images]/rightbar.gif");

$qr_stat=db_query($sql);
 print "<table cellspacing=\"0\" cellpadding=\"2\" border=\"0\" align=\"center\">";
while($data_stat=db_fetch($qr_stat)){

    $rs[0] = $data_stat[$count_data];
    $rs[1] =  substr(100 * $data_stat[$count_data] / $total, 0, 5);
    $title = $data_stat[$count_name];

    print "<tr><td>";
    if ($if_img){
            print "<img src=\"$style[images]/flags/$data_stat[code].jpg\" border=\"0\" alt=\"\">";}


   print " $title:</td><td dir=ltr align='$global_align'><img src=\"$style[images]/leftbar.gif\" height=\"$l_size[1]\" width=\"$l_size[0]\">";
    print "<img src=\"$style[images]/mainbar.gif\"  height=\"$m_size[1]\" width=". $rs[1] * 2 ."><img src=\"$style[images]/rightbar.gif\" height=\"$r_size[1]\" width=\"$l_size[0]\">
    </td><td>
    $rs[1] % ($rs[0])</td>
    </tr>\n";

}
print "</table>";
}else{
        print "<center>$phrases[no_results]</center>" ;
        }

}
?>