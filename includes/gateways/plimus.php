<?
class plimus {
 
    
  public $settings = array(
    'user_id'=>array('type'=>'text'),
    'currency_code'=>array('type'=>'text')
    );
    
    function __construct(){
    }
    
    function print_form(){
    global $phrases,$data_order,$gateway_settings;
         
    $values = array(
    'currency'=>$gateway_settings['currency_code'],
    'bCur'=>$gateway_settings['currency_code'],
    'contractId'=>$gateway_settings['user_id'],
    'order details'=>"Invoice #".$data_order['id'],
    'custom1'=>"Invoice #".$data_order['id'],
    'custom2'=>'',
    'overridePrice'=> number_format($data_order['price'],2,".","")
    );
    
    print "<form name=\"payment\" action=\"https://www.plimus.com/jsp/buynow.jsp\" method=\"post\">";
    foreach($values as $k=>$v){
    print "<input type=\"hidden\" name=\"$k\" value=\"".htmlspecialchars($v)."\">\n\r";
    }
    print "<input type=\"submit\" value=\"$phrases[pay_now]\">
    </form>";
    
    }
    
    
}

