<?php

class QrCodesGateway {

	private $adapter;

	function __construct() {

		// no adapter selected yet
		$this->adapter = false;
	}

	function init_adapter() {

		if(!$this->adapter) {

			include_once("classes/adapters/qr_codes/endroid-qr-code-generator.class.php");
			$this->adapter = new JanitorEndroidQrCodeGenerator();
		}
	}

	/**
	 * QrCodesGateway::create
	 *
	 * @param string|array|mixed $content – array is converted to json-encoded string. Everything else is directly converted to string. 
	 * @param string $output_file – intended path of generated qr code
	 * @param array|false $_options
	 * * size (number): size in px
	 * * margin (number): margin size in px
	 * * foreground_color (array): rgba array, e.g. ["r" => 255, "g" => 255, "b" => 255, "a" => 0]
	 * * background_color (array): rgba array
	 * * format (string): png (default) or svg
	 * 
	 * @return string|false path of generated qr code (via adapter class). False on error.
	 */
	function create($content, $_options = false) {

		$this->init_adapter();

		if($this->adapter) {

			$size = false;
			$margin = false;
			$foreground_color = false;
			$background_color = false;
			$output_file = false;
			$format = false;

			if($_options !== false) {
				foreach($_options as $_option => $_value) {
					switch($_option) {
		
						case "size"                   : $size                   = $_value; break;
						case "margin"                 : $margin                 = $_value; break;
						case "foreground_color"       : $foreground_color       = $_value; break;
						case "background_color"       : $background_color       = $_value; break;
						
						case "output_file"            : $output_file            = $_value; break;
						case "format"                 : $format                 = $_value; break;
		
					}
				}
			}

			return $this->adapter->create($content, [

				"size" => $size,
				"margin" => $margin,
				"foreground_color" => $foreground_color,
				"background_color" => $background_color,

				"output_file" => $output_file,
				"format" => $format,

			]);

		}
	}
}