<?php
require ('global.php');

// Initialize class
$gd = new captcha($op);

// Output image
$gd->output_image();
?>