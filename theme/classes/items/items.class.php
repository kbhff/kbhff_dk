<?php
/**
* This file contains the item custom backbone
* This class basically only exists to make it easy to add custom page functionality or overwrite behaviours.
*/


/**
* Item custom backbone - extends the ItemCore base functionality
*/
class Items extends ItemsCore {

	/**
	* Get required page information
	*/
	function __construct() {
		parent::__construct();
	}

	function extendItem($item, $_options = false) {
		$item = parent::extendItem($item, $_options);
		
		$departments = false;
		$all = false;

		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {
					case "departments"           : $departments             = $_value; break;
					case "all"                   : $all                     = $_value; break;
				}
			}
		}
		// add departments (for item)
		$item["departments"] = array();
		if($all || $departments) {
			include_once("classes/system/department.class.php");
			$DC = new Department();
			$item["departments"] = $DC->getDepartments(array("item_id" => $item["id"]));
		} 
		return $item;
	}



}

?>
