<?php

class QrCodesGateway {

	private $adapter;

	function __construct() {

		// no adapter selected yet
		$this->adapter = false;
	}

	function init_adapter() {

		if(!$this->adapter) {

			include_once("classes/adapters/qr_codes/bacon-qr-code.class.php");
			$this->adapter = new JanitorBaconQrCode();
		}
	}

	/**
	 * QrCodesGateway::create
	 *
	 * @param string|array|mixed $content – array is converted to json-encoded string. Everything else is directly converted to string. 
	 * @param array|false $_options
	 * * size (number): size in px
	 * * margin (boolean): toggles a column-sized margin
	 * * foreground_color (array): rgba array, e.g. ["r" => 255, "g" => 255, "b" => 255, "a" => 0]
	 * * background_color (array): rgba array
	 * * output_file (string): will save the QR code as the specified filename
	 * * format (string): png (default) or svg
	 * 
	 * @return string|false qr code as binary string or path of qr code. False on error.
	 */
	function create($content, $_options = false) {

		$this->init_adapter();

		if($this->adapter) {

			$size = false;
			$margin = true;
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

			$margin ? $margin = 1 : $margin = 0;

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