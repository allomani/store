<?
if(!defined('IS_ADMIN')){die('No Access');}  

if($action=="cities"){
   if_admin();
    
    $cat = (int) $cat;
$qr = db_query("select * from store_cities where cat='$cat'");

if(db_num($qr)){
    
      
    print "<table width=100% class=grid>";
    while($data=db_fetch($qr)){
        if($tr_class=="row_1"){$tr_class="row_2";}else{$tr_class="row_1";}  
        
    print "<tr class='$tr_class'><td>$data[name]</td></tr>";
    }
    print "</table>";
}else{
    print_admin_table("<center> no cities </center>");
}

}