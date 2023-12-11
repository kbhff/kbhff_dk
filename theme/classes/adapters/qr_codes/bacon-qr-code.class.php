<?php

require_once('includes/qr_codes/bacon-qr-code-2.0.0/vendor/autoload.php');

use BaconQrCode\Renderer\Color\ColorInterface;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;

use BaconQrCode\Writer;

class JanitorBaconQrCode {


	/**
	* Init, set varnames, validation rules
	*/
	function __construct() {}

	function create($content, $_options) {

		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {
	
					case "size"                   : $size                   = $_value; break;
					case "margin"                 : $margin                 = $_value; break;
					// case "foreground_color"       : $foreground_color       = $_value; break;
					// case "background_color"       : $background_color       = $_value; break;
					
					case "output_file"            : $output_file            = $_value; break;
					case "format"                 : $format                 = $_value; break;
	
				}
			}
		}

		// set defaults
		if($size === false) {
			$size = 300;
		}

		if($format) {
	
			if($format = "svg") {
				
				$renderer = new ImageRenderer(
					new RendererStyle($size, $margin),
					new SvgImageBackEnd()
				);
			}
		}
		else {

			$renderer = new ImageRenderer(
				new RendererStyle($size, $margin),
				new ImagickImageBackEnd()
			);
		}

		$writer = new Writer($renderer);


		if($content !== false) {

			if(is_array($content)) {

				$content_string = json_encode($content);
			}

			else {

				$content_string = $content;
			}

			// if($foreground_color) {
			// 	$renderer->setForegroundColor(new \BaconQrCode\Renderer\Color\Rgb(170, 45, 76));
			// }
	
			// if($background_color) {
			// 	$QRC->setBackgroundColor($background_color);
			// }
	
			

			if($output_file) {

				$writer->writeFile($content_string, $output_file);
		
				if(file_exists($output_file)) {
					
					return $output_file;
				}
			}
			else {

				$output_string = $writer->writeString($content_string);

				if($output_string) {

					return $output_string;
				}
			}
	
		}

		return false;

	}

}
