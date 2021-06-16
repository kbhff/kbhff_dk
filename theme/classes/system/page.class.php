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

		if(getVar("itgroup") !== false) {
			session()->value("itgroup", getVar("itgroup"));
		}
	
	}


	/**
	* Compile complete page HTML 
	* Render order: templates, header, footer
	* Output order: header, templates, footer
	*
	* TODO: consider implementing 404 response code for 404 template - http_response_code(404);
	*
	* @return String page header
	*/
	function page($_options = false) {
		global $HTML;
		global $JML;

		if(session()->value("itgroup")) {
			$type = "www";
			$templates = false;
			$error = "pages/404.php";

			if($_options !== false) {
				foreach($_options as $_option => $_value) {
					switch($_option) {
						case "type"              : $type       = $_value; break;

						case "templates"         : $templates  = $_value; break;

						case "error"             : $error      = $_value; break;

						case "body_class"        : $this->bodyClass($_value); break;
						case "page_title"        : $this->pageTitle($_value); break;
						case "page_descriptiton" : $this->pageDescription($_value); break;
						case "content_class"     : $this->contentClass($_value); break;
					}
				}
			}

			$_template = "";
			$_header = "";
			$_footer = "";

			if($templates) {
				$templates_array = explode(",", $templates);
				foreach($templates_array as $template) {
	//				print "buffering: " . $template;

					$_template .= $this->template($template, array("buffer" => true, "error" => $error));

	//				print "buffered: " . $_template;
				}
			}

			$_header = $this->header(array("type" => $type, "buffer" => true));
			$_footer = $this->footer(array("type" => $type, "buffer" => true));

			print $_header.$_template.$_footer;
		}

		else {
			print $this->template("downtime.php");

		}
	}


	/**
	* Get/set current user department
	*
	* Pass value to set department
	*
	* @return string department name
	*/
	function department($value = false) {
		// set
		if($value !== false) {

			$query = new Query();
			// only allow valid department
			// look for department in DB
			if($query->sql("SELECT * FROM ".SITE_DB.".project_departments WHERE id = '".$value."'")) {
				session()->value("department", $value);
			}
			// $value is not a valid department
			else {
				session()->value("department", "");
			}
		}

		// get
		else {

			// department has not been set for current user session yet
			if(!session()->value("department")) {
				// set default department
				$this->department("");
			}

			// return current user department
			return session()->value("department");
		}
	}

	/**
	* Get array of available departments (with details)
	* Optionally get details for specific department
	*
	* @return array Array of departments or array of department details
	*/
	function departments($id = false) {

		// load apartments into cache if not already there
		if(!cache()->value("departments")) {

			$query = new Query();
			$query->sql("SELECT * FROM ".SITE_DB.".project_departments");
			cache()->value("departments", $query->results());
		}

		// looking for specific department details
		if($id !== false) {
			$departments = cache()->value("departments");
			$key = arrayKeyValue($departments, "id", $id);
			if($key !== false) {
				return $departments[$key];
			}
			// invalid department requested - return default department
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
