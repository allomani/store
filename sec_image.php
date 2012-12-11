<?php
require ('global.php');
require (CWD . '/includes/class_captcha.php'); 

// Initialize class
$gd = new captcha($op);

// Output image
$gd->output_image();
?>