<?
require("global.php");
require(CWD . "/includes/framework_start.php");
//-------------------------------------------------
  if(check_member_login()){  
      
          open_table("$phrases[my_orders]");
          $qr = db_query("select * from store_orders where userid='$member_data[id]' order by id desc"); 
          if(db_num($qr)){
              print "<table width=100% class='center'>
              <tr>
              <th><b>#</b></th>
              <th><b>$phrases[billing_name]</b></th>
              <th><b>$phrases[order_date]</b></th>";
               if($settings['show_paid_option']){ 
             print " <th><b>$phrases[paid]</b></th>";
              }
               
              print " <th><b>$phrases[the_total]</b></th>
              <th></th>
              </tr> 
              ";
              while($data=db_fetch($qr)){
                  
if($tr_class=="row_1"){
$tr_class = "row_2";
}else{
$tr_class="row_1";
}


                  print "<tr class='$tr_class'><td>$data[id]</td>
                  <td>$data[billing_name]</td>
                  <td>".get_date($data['date'])."</td>";
                  
                   if($settings['show_paid_option']){
                  print "<td>".iif($data['paid'],"<font color=green>$phrases[yes]</font>","<font color=red>$phrases[no]</font>")."</td>";
                  }
                  
                  print "<td>".get_order_total_price($data['id'])." $settings[currency]</td><td><a href='invoice.php?id=$data[id]'>$phrases[view]</td></tr>";
              }
              print "</table>";
          }else{
              print "<center> $phrases[no_orders] </center>";
          }  
         close_table();
  }else{
 login_redirect();

 }
          
 //---------------------------------------------
require(CWD . "/includes/framework_end.php");    
?>