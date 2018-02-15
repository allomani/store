<?php
/**
 *  Allomani E-Store v1.0
 * 
 * @package Allomani.E-Store
 * @version 1.0
 * @copyright (c) 2006-2018 Allomani , All rights reserved.
 * @author Ali Allomani <info@allomani.com>
 * @link http://allomani.com
 * @license GNU General Public License version 3.0 (GPLv3)
 * 
 */

// Require the class code...
require ('includes/class_security_img.php'); 

// Initialize class
$gd = new sec_img_verification();

// Output image
$gd->output_image();
?>
