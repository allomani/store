<?

class shipping_manual {

    public $data;
    public $shipping_settings;
    public $settings;

    function __construct($data, $shipping_settings=array()) {
        global $phrases, $settings;

        $this->settings = array(
            'price' => array('type' => 'text', 'title' => $phrases['the_price'], 'ext' => $settings['currency'])
        );


        $this->data = $data;
        $this->shipping_settings = $shipping_settings;
    }

    function get_price() {
        global $phrases;

        return array('status'=>true,
                     'price'=>$this->shipping_settings['price']
                     );
    }

}

