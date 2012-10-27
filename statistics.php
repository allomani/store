<?
require("global.php");
require(CWD . "/includes/framework_start.php");   
//----------------------------------------------------

   $year = intval($year);
$month = intval($month);
 require(CWD . '/includes/functions_statics.php');


 //-------- browser and os statics ---------
if($settings['count_visitors_info']){
open_table("$phrases[operating_systems]");
get_statics_info("select * from info_os where count > 0 order by count DESC","name","count");
close_table();

open_table("$phrases[the_browsers]");
get_statics_info("select * from info_browser where count > 0 order by count DESC","name","count");
close_table();

$printed  = 1 ;
}

//--------- hits statics ----------
if($settings['count_visitors_hits']){
$printed  = 1 ;

if (!$year){$year = date("Y");}

open_table("$phrases[monthly_statics_for] $year ");

for ($i=1;$i <= 12;$i++){

$dot = $year;

if($i < 10){$x="0$i";}else{$x=$i;}


$sql = "select * from info_hits where date like '%-$x-$dot' order by date" ;
$qr_stat=db_query($sql);

if (db_num($qr_stat)){
$total = 0 ;
while($data_stat=db_fetch($qr_stat)){
$total = $total + $data_stat['hits'];
}

$rx[$i-1]=$total  ;

}else{
        $rx[$i-1]=0 ;
        }

  }

    for ($i=0;$i <= 11;$i++){
    $total_all = $total_all + $rx[$i];
         }

         if ($total_all !==0){

         print "<br>";

  $l_size = @getimagesize("images/leftbar.gif");
    $m_size = @getimagesize("images/mainbar.gif");
    $r_size = @getimagesize("images/rightbar.gif");


 echo "<table cellspacing=\"0\" cellpadding=\"2\" border=\"0\" align=\"center\">";
 for ($i=1;$i <= 12;$i++)  {

    $rs[0] = $rx[$i-1];
    $rs[1] =  substr(100 * $rx[$i-1] / $total_all, 0, 5);
    $title = $i;

    echo "<tr><td>";



   print " $title:</td><td dir=ltr align='$global_align'><img src=\"images/leftbar.gif\" height=\"$l_size[1]\" width=\"$l_size[0]\">";
    print "<img src=\"images/mainbar.gif\"  height=\"$m_size[1]\" width=". $rs[1] * 2 ."><img src=\"images/rightbar.gif\" height=\"$r_size[1]\" width=\"$l_size[0]\">
    </td><td>
    $rs[1] % ($rs[0])</td>
    </tr>\n";

}
print "</table>";
 }else{
        print "<center>$phrases[no_results]</center>";
        }
  print "<br><center>[ $phrases[the_year] : ";
  $yl = date('Y') - 3 ;
  while($yl != date('Y')+1){
      print "<a href='statistics.php?year=$yl'>$yl</a> ";
      $yl++;
      }
  print "]";
close_table();

if (!$month){
        $month =  date("m")."-$year" ;
        }else{
                $month= "$month-$year";
                }

open_table("$phrases[daily_statics_for] $month ");
$dot = $month;
get_statics_info("select * from info_hits where date like '%$dot' order by date","date","hits");

print "<br><center>
          [ $phrases[the_month] :
          <a href='statistics.php?year=$year&month=1'>1</a> -
          <a href='statistics.php?year=$year&month=2'>2</a> -
          <a href='statistics.php?year=$year&month=3'>3</a> -
          <a href='statistics.php?year=$year&month=4'>4</a> -
          <a href='statistics.php?year=$year&month=5'>5</a> -
          <a href='statistics.php?year=$year&month=6'>6</a> -
          <a href='statistics.php?year=$year&month=7'>7</a> -
          <a href='statistics.php?year=$year&month=8'>8</a> -
          <a href='statistics.php?year=$year&month=9'>9</a> -
          <a href='statistics.php?year=$year&month=10'>10</a> -
          <a href='statistics.php?year=$year&month=11'>11</a> -
          <a href='statistics.php?year=$year&month=12'>12</a>
          ]";
          close_table();
}

if(!$printed){
    open_table();
   print "<center>$phrases[no_results]</center>";
    close_table();
    }
    
//---------------------------------------------------
require(CWD . "/includes/framework_end.php");  