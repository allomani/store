<?
/*
if($_COOKIE['sess'] && preg_match("/^([a-zA-Z-0-9_])*$/",$_COOKIE['sess'])){
session_id($_COOKIE['sess']);
}

session_start();


setcookie('sess',session_id(),time()+3600);
*/

include "global.php";



if($_GET['op'] == "set"){
$_SESSION['name'] = "data here ";
}




print "value : ".$_SESSION['name']."<br><br>";;

print "id : ".session_id()."<br><br>";
print "sid : ".SID."<br><br>";
print_r($_SESSION);
print "<hr>";
print_r($_COOKIE);