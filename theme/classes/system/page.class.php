<?php
/**
* Foodnet platform
* Copyright (C) 2018  Københavns Fødevarefællesskab and think.dk
*
* Københavns Fødevarefællesskab
* KPH-Projects
* Enghavevej 80 C, 3. sal
* 2450 København SV
* Denmark
* mail: bestyrelse@kbhff.dk
*
* think.dk
* Æbeløgade 4
* 2100 København Ø
* Denmark
* mail: start@think.dk
*	
* This source code is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This source code is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this source code.  If not, see <http://www.gnu.org/licenses/>.
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
