<?
$queries=0;
$last_sql = '' ;

 //----------- Clean String ----------
 function db_clean_string($str,$type="text",$op="write",$is_gpc=true){

 //if(get_magic_quotes_gpc() && $is_gpc){ $str = stripslashes($str);}

if($type=="num"){
return intval($str);
}elseif($type=="text"){

if($op=="write"){
return db_escape($str);
}else{
return db_escape($str,false);
}
}elseif($type=="code"){
return db_escape($str,false);
}
 }
 
 //------------ db escape -------------//
 function db_escape($str,$specialchars=true){
 if($specialchars) { $str =  html_encode_chars($str) ;}
 return db_escape_string($str); 
 }
 //----------- escape String -----------
 function db_escape_string($str){
               //                  print_r($str);
 if(function_exists('mysql_real_escape_string')){
 	return mysql_real_escape_string($str);
 	}else{
 	return mysql_escape_string($str);
 	}
 }
 //----------- Connect ----------
 function db_connect($host,$user,$pass,$dbname){
     global $log_mysql_errors;
     
     $cn = @mysql_connect($host,$user,$pass) ;
if(!$cn){
        if(mysql_errno()==1040){
     die("<center> Mysql Server is Busy  , Please Try again later  </center>");
        }else{
                       
 if($log_mysql_errors){
              do_error_log(@mysql_errno()." : ".@mysql_error(),'db');
          }
          
          
die(@mysql_errno()." : Database Connection Error");
                }
                }


$db_select = @mysql_select_db($dbname); 
if(!$db_select){
                     
  if($log_mysql_errors){
              do_error_log(@mysql_errno()." : ".@mysql_error(),'db');
          }
             
 die("Database Name Error");
}
 }
 //----------- query ------------------
   function db_query($sql,$type=""){
   global $show_mysql_errors,$log_mysql_errors,$queries,$last_sql ; 
  
   $queries++;
    //   print $sql."<br>";
  //     print $queries . "." .$sql."<hr>";   
       
   $last_sql = $sql;	
     
if($type==MEMBER_SQL){
	members_remote_db_connect();
	}
             
      $qr  = @mysql_query($sql);
      $err =  mysql_error() ;

      if($err){
          
          
      	 	   if($show_mysql_errors){
           print  "<p align=left><b> MySQL Error: </b> $err </p>";
          }
          
          if($log_mysql_errors){
              do_error_log("$err \r\nSQL :  $last_sql",'db');
          }
          
          
      	 	return false;
      }else{
      if($type==MEMBER_SQL){
	members_local_db_connect();
	}

         return $qr ;
      }


           }

 //---------------- fetch -------------------
    function db_fetch($qr){
    global $show_mysql_errors,$log_mysql_errors,$last_sql ;

         $fetch = @mysql_fetch_array($qr);

     $err =  mysql_error() ;

      if($err){
          
          if($show_mysql_errors){
       	print  "<p align=left><b> MySQL Error: </b> $err </p>";
          }
          
          if($log_mysql_errors){
              do_error_log("$err \r\nSQL :  $last_sql",'db');
          }
          
       		return false;
      }else{
            return $fetch;
            }
            }



// ------------------------ num -----------------------
      function db_num($qr){
     global  $show_mysql_errors,$log_mysql_errors,$last_sql ;
     

      $num =  @mysql_num_rows($qr);
      $err =  mysql_error() ;

      if($err){
          
          if($show_mysql_errors){
           print  "<p align=left><b> MySQL Error: </b> $err </p>";
          }
          
          if($log_mysql_errors){
              do_error_log("$err \r\nSQL :  $last_sql",'db');
          }
          
          
       		return false;
      }else{
            return $num;
            }



            }
            
            
  //------------------ Query + fetch ----------------------
    function db_qr_fetch($sql,$type=""){
        
        return db_fetch(db_query($sql,$type));
            }
            
// ------------------- query + num --------------------
             function db_qr_num($sql,$type=""){
     
            return db_num(db_query($sql,$type));
            }
            
            