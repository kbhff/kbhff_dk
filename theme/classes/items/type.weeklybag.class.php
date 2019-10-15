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
* @package janitor.items
* This file contains item type functionality
*/

class TypeWeeklybag extends Itemtype {

	/**
	* Init, set varnames, validation rules
	*/
	function __construct() {

		// Construct Itemtype class and pass itemtype as parameter
		parent::__construct(get_class());


		// itemtype database
		$this->db = SITE_DB.".item_weeklybag";


		// Name
		$this->addToModel("name", array(
			"type" => "string",
			"label" => "Name",
			"required" => true,
			"hint_message" => "Give the weekly bag a name. Preferably use Week number in Danish as name, i.e. 'Uge 21' for consistancy.",
			"error_message" => "The weekly bag needs a title."
		));

		// week
		$this->addToModel("week", array(
			"type" => "integer",
			"label" => "Week number, year",
			"min" => 1,
			"max" => 53,
			"required" => true,
			"hint_message" => "State the week number for this bag."
		));
		// year
		$this->addToModel("year", array(
			"type" => "integer",
			"label" => "Year",
			"min" => 2019,
			"max" => 2100,
			"required" => true,
			"hint_message" => "State the year for this bag."
		));

		// description
		$this->addToModel("html", array(
			"type" => "html",
			"label" => "Content of bag",
			"required" => true,
			"allowed_tags" => "p,h4,ul",
			"hint_message" => "List the content of the weekly bag for showing in sidebar on frontpage. You can include a small description if needed.",
			"error_message" => "A weekly bag without content? How weird."
		));

		// Full description
		$this->addToModel("full_description", array(
			"type" => "html",
			"label" => "Full description",
			"required" => true,
			"allowed_tags" => "p,h3,h4,ul,jpg,png",
			"hint_message" => "Full description of weekly bag and other product availability.",
			"error_message" => "A weekly bag without a full description? How weird."
		));

	}

	function getWeeklyBagDate($week = false, $year = false) {

		if(!$week) {
			$week = date("W", strtotime("WEDNESDAY"));
		}

		if(!$year) {
			$year = date("Y", strtotime("WEDNESDAY"));
		}
		
		// always use two digits in week no
		$week = $week < 10 ? "0$week" : $week;

		$time = strtotime("{$year}W{$week} +2 days");
		$months = ["januar", "februar", "marts", "april", "maj", "juni", "juli", "august", "september", "oktober", "november", "december"];

		return date ("d.", $time) . " " . $months[date("m", $time)-1] . ", " . date ("Y", $time);
	}

	function getWeeklyBag($week = false, $year = false) {
		
		if(!$week) {
			$week = date("W", strtotime("WEDNESDAY"));
		}

		if(!$year) {
			$year = date("Y", strtotime("WEDNESDAY"));
		}

		$query = new Query();

		$sql = "SELECT item_id FROM ".$this->db." WHERE week = $week AND year = $year";
		// debug([$sql]);
		if($query->sql($sql)) {

			$IC = new Items();
			return $IC->getItem(["id" => $query->result(0, "item_id"), "extend" => true]);

		}
		
		return false;

	}
}

?>
