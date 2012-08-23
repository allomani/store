<?
class paypal {
 
    
  public $settings = array(
    'email'=>array('type'=>'text'),
    'currency_code'=>array('type'=>'text'),
    'sandbox'=>array('type'=>'select','options'=>array(0=>'No',1=>'yes'))
    );
    
    function __construct(){
    }
    
    function print_form(){
    global $phrases,$data_order,$gateway_settings;
         
    $values = array(
    'cmd'=>'_xclick',
    'upload'=>1,
    'currency_code'=>$gateway_settings['currency_code'],
    'business'=>$gateway_settings['email'],
    'notify_url'=>'',
    'item_name'=>"Order #".$data_order['id'],
    'return'=>'',
    'cancel_return'=>'',
    'invoice'=> $data_order['id'],
    'firstname'=> "",
    'lastname'=>'',
    'address1'=> '',
    'address2'=>'',
    'city'=> "",
    'state'=>"",
    'zip'=>"",
    'rm'=>'2',
    'amount'=> number_format($data_order['price'],2,".","")
    );
    
    print "<form name=\"payment\" action=\"https://www.".iif($gateway_settings['sandbox'],"sandbox.")."paypal.com/cgibin/webscr\" method=\"post\">";
    foreach($values as $k=>$v){
    print "<input type=\"hidden\" name=\"$k\" value=\"".htmlspecialchars($v)."\">\n\r";
    }
    print "<input type=\"submit\" value=\"$phrases[pay_now]\">
    </form>";
    
    }
    
    
}

