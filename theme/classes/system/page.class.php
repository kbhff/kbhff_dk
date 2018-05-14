<?php
/**
* This file contains the site custom backbone, the Page Class.
* This class basically only exists to make it easy to add custom page functionality or overwrite behaviours.
*/


/**
* Site custom backbone, the Page class - extends the PageCore base functionality
*/
class Page extends PageCore {

	/**
	* Get required page information
	*/
	function __construct() {
		parent::__construct();
	}



	/**
	* Get/set current user country
	*
	* Pass value to set country
	*
	* @return country ISO id on get
	*/
	function department($value = false) {
		// set
		if($value !== false) {

			$query = new Query();
			// only allow valid country
			// look for country in DB
			if($query->sql("SELECT * FROM ".SITE_DB.".system_departments WHERE id = '".$value."'")) {
				session()->value("department", $value);
			}
			// $value is not valid country
			else {
				session()->value("department", "");
			}
		}

		// get
		else {

			// country has not been set for current user session yet
			if(!session()->value("department")) {
				// set default country
				$this->department("");
			}

			// return current user country
			return session()->value("department");
		}
	}

	/**
	* Get array of available countries (with details)
	* Optional get details for specific country
	*
	* @return Array of countries or array of country details
	*/
	function departments($id = false) {

		if(!cache()->value("departments")) {

			$query = new Query();
			$query->sql("SELECT * FROM ".SITE_DB.".system_departments");
			cache()->value("departments", $query->results());
		}

		// looking for specific country details
		if($id !== false) {
			$departments = cache()->value("departments");
			$key = arrayKeyValue($departments, "id", $id);
			if($key !== false) {
				return $departments[$key];
			}
			// invalid country requested - return default country
			else {
				$key = arrayKeyValue($departments, "id", $this->department());
				return $departments[$key];
			}
		}
		// return complete array of departments
		else {
			return cache()->value("departments");
		}

	}



}

?>
