<?php

class captcha {

    var $im = NULL;
    var $string = NULL;
    var $height = 150;
    var $width = 35;
    var $s_name = 'default';

    function __construct($s_name = 'default',$height = 150, $width = 35) {
    $this->height = $height;
    $this->width = $width;
    $this->s_name = $s_name;
    }

    function generate_string() {
        global $session;
        // Create random string
        $this->string = substr(sha1(mt_rand()), 17, 6);

        // Set session variable
        $session->set('gd_string',$this->string);
    }

    function verify_string($gd_string) {
        global $session;
        
        // Check if the original string and the passed string match...
        if (strtolower($session->get('gd_string')) === strtolower($gd_string)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function output_input_box($name, $parameters = NULL) {
        return '<input type="text" name="' . $name . '" ' . $parameters . ' /> ';
    }
    
    function output_img_box(){
       return  "<img src=\"sec_image.php?op=".$this->s_name."\" class=\"captcha_img\" />";
        }

    function create_image() {
        // Seed string
        $this->generate_string();

        $this->im = imagecreatetruecolor($this->height, $this->width); // Create image
        // Get width and height
        $img_width = imagesx($this->im);
        $img_height = imagesy($this->im);

        // Define some common colors
        $black = imagecolorallocate($this->im, 0, 0, 0);
        $white = imagecolorallocate($this->im, 255, 255, 255);
        $red = imagecolorallocatealpha($this->im, 255, 0, 0, 75);
        $green = imagecolorallocatealpha($this->im, 0, 255, 0, 75);
        $blue = imagecolorallocatealpha($this->im, 0, 0, 255, 75);

        // Background
        imagefilledrectangle($this->im, 0, 0, $img_width, $img_height, $white);

        // Ellipses (helps prevent optical character recognition)
        imagefilledellipse($this->im, ceil(rand(5, 145)), ceil(rand(0, 35)), 30, 30, $red);
        imagefilledellipse($this->im, ceil(rand(5, 145)), ceil(rand(0, 35)), 30, 30, $green);
        imagefilledellipse($this->im, ceil(rand(5, 145)), ceil(rand(0, 35)), 30, 30, $blue);

        // Borders
        imagefilledrectangle($this->im, 0, 0, $img_width, 0, $black);
        imagefilledrectangle($this->im, $img_width - 1, 0, $img_width - 1, $img_height - 1, $black);
        imagefilledrectangle($this->im, 0, 0, 0, $img_height - 1, $black);
        imagefilledrectangle($this->im, 0, $img_height - 1, $img_width, $img_height - 1, $black);

        imagestring($this->im, 5, intval(($img_width - (strlen($this->string) * 9)) / 2), intval(($img_height - 15) / 2), $this->string, $black); // Write string to photo
    }

    function output_image() {
        $this->create_image(); // Generate image

        header("Content-type: image/png"); 

        imagepng($this->im); // Output Image
        imagedestroy($this->im); // Flush Image
        die();
    }

}

?>