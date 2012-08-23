<?
class moneybookers {
 
    
  public $settings = array(
    'email'=>array('type'=>'text'),
    'currency_code'=>array('type'=>'text')
    );
    
    function __construct(){
    }
    
    function print_form(){
    global $phrases,$data_order,$gateway_settings;
         
    $values = array(
    'currency'=>$gateway_settings['currency_code'],
    'pay_to_email'=>$gateway_settings['email'],
    'detail1_description'=>"Invoice #",
    'detail1_text'=>$data_order['id'],
    'return_url'=>'',
    'language'=>'EN',
    'amount'=> number_format($data_order['price'],2,".","")
    );
    
    print "<form name=\"payment\" action=\"https://www.moneybookers.com/app/payment.pl\" method=\"post\">";
    foreach($values as $k=>$v){
    print "<input type=\"hidden\" name=\"$k\" value=\"".htmlspecialchars($v)."\">\n\r";
    }
    print "<input type=\"submit\" value=\"$phrases[pay_now]\">
    </form>";
    
    }
    
    
}

