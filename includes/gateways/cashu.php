<?
class cashu {
 
    
  public $settings = array(
    'merchant_id'=>array('type'=>'text'),
    'secret_word'=>array('type'=>'text'),
    'language'=>array('type'=>'text'),
    'currency_code'=>array('type'=>'text')
    );
    
    function __construct(){
    }
    
    function print_form(){
    global $data_order,$gateway_settings;
    
    $phrases = app::$phrases;
    
    $price = number_format($data_order['price'],2,".","");
    $currency = strtolower($gateway_settings['currency_code']);
    $token = md5("{$gateway_settings['merchant_id']}:{$price}:{$currency}:{$gateway_settings['secret_word']}");
    
         
    $values = array(
    'merchant_id'=>$gateway_settings['merchant_id'],
    'currency'=>$gateway_settings['currency_code'],
    'token'=>$token,
    'display_text'=>"Invoice Number ".$data_order['id'],
    'txt1'=>"Invoice Number ".$data_order['id'],
    'amount'=>$price,
    'language'=>$gateway_settings['language'],
    'session_id'=>'' 
    );
    
    print "<form name=\"payment\" action=\"https://www.cashu.com/cgi-bin/pcashu.cgi\" method=\"post\">";
    foreach($values as $k=>$v){
    print "<input type=\"hidden\" name=\"$k\" value=\"".htmlspecialchars($v)."\">\n\r";
    }
    print "<input type=\"submit\" value=\"$phrases[pay_now]\">
    </form>";
    
    }
    
    
}

