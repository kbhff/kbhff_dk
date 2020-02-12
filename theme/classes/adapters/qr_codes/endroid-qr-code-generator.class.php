<?php

require_once('includes/qr_codes/endroid-qr-code-3.7.5/vendor/autoload.php');

use Endroid\QrCode\QrCode;

class JanitorEndroidQrCodeGenerator {


	/**
	* Init, set varnames, validation rules
	*/
	function __construct() {}

	function create($content, $output_file, $_options) {

		$size = false;
		$margin = false;
		$foreground_color = false;
		$background_color = false;
		$format = false;

		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {
	
					case "size"                   : $size                   = $_value; break;
					case "margin"                 : $margin                 = $_value; break;
					case "foreground_color"       : $foreground_color       = $_value; break;
					case "background_color"       : $background_color       = $_value; break;
					
					case "format"                 : $format                 = $_value; break;
	
				}
			}
		}

		if($content !== false) {

			if(is_array($content)) {

				$content_string = json_encode($content);
			}

			else {

				$content_string = $content;
			}

			$QRC = new QrCode($content_string);
			$QRC->setMargin(0);
	
			if($size) {
				$QRC->setSize($size);
			}
	
			if($margin !== false) {
				$QRC->setMargin($margin);
			}
	
			if($foreground_color) {
				$QRC->setForegroundColor($foreground_color);
			}
	
			if($background_color) {
				$QRC->setBackgroundColor($background_color);
			}
	
			if($format) {
	
				$QRC->setWriterByName($format);
			}
	
			$QRC->writeFile($output_file);
	
			if(file_exists($output_file)) {
				
				return $output_file;
			}
		}

		return false;

	}

}
