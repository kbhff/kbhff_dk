<?php
/**
* @package janitor.shop
* Meant to allow local shop additions/overrides
*/


class Shop extends ShopCore {

	/**
	*
	*/
	function __construct() {

		parent::__construct(get_class());


		$this->order_statuses_dk = array(0 => "Ny", 1 => "Afventer", 2 => "Færdig", 3 => "Annulleret");


		// payment and shipping statuses
		$this->payment_statuses_dk = array(0 => "Ikke betalt", 1 => "Delvist betalt", 2 => "Betalt");
		$this->shipping_statuses_dk = array(0 => "Ikke modtaget", 1 => "Delvist afsendt", 2 => "Afsendt");

	}

}

?>