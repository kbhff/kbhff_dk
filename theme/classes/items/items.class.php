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
		if(isset($item["id"]) && isset($item["itemtype"])) {	
			// add departments (for item)
			$item["departments"] = array();
			if($all || $departments) {
				include_once("classes/system/department.class.php");
				$DC = new Department();
				$item["departments"] = $DC->getDepartments(array("item_id" => $item["id"]));
			} 
		}
		return $item;
	}

	function getItem($_options = false) {


		$item = parent::getItem($_options);

		$extend = false;

		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {
					case "extend"    : $extend         = $_value; break;
				}
			}
		}

		if($extend) {
			// only pass on extend settings if they are not empty
			$item = $this->extendItem($item, (is_array($extend) ? $extend : false));
		}
		return $item;

	}



	/**
	* Helper funtion to get simple deartment class
	*/
	private function getDepartmentClass() {
		if($this->DC == false) {
			$this->DC = new Department();
		}
		return $this->DC;
	}

	function getMemberships() {
		$IC = new Items();
		$itemtype = "membership";
		$items = $IC->getItems(array("itemtype" => $itemtype, "order" => "position ASC, status DESC", "extend" => array("tags" => true, "mediae" => true)));
		return $items;
	}



}

?>
