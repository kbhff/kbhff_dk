<?php
/* 
Foodnet platform
Copyright (C) 2018  Københavns Fødevarefællesskab and think.dk

Københavns Fødevarefællesskab
KPH-Projects
Enghavevej 80 C, 3. sal
2450 København SV
Denmark
mail: bestyrelse@kbhff.dk

think.dk
Æbeløgade 4
2100 København Ø
Denmark
mail: start@think.dk
	
This source code is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This source code is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this source code.  If not, see <http://www.gnu.org/licenses/>.
*/


class CurlRequest {

	private $ch;

	public function init($params) {

		$this->ch = curl_init();

		@curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		@curl_setopt($this->ch, CURLOPT_VERBOSE, 1);
		@curl_setopt($this->ch, CURLOPT_HEADER, 1);
		@curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);
		@curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
		@curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);

		@curl_setopt($this->ch, CURLOPT_COOKIEFILE, "");
 
		if(isset($params['header']) && $params['header']) {
			@curl_setopt($this->ch, CURLOPT_HTTPHEADER, $params['header']);
		}

		if($params['method'] == "HEAD") {
			@curl_setopt($this->ch, CURLOPT_NOBODY, 1);
		}

		if(isset($params['useragent'])) {
			@curl_setopt($this->ch, CURLOPT_USERAGENT, $params['useragent']);
		}

		if($params['method'] == "POST") {
			@curl_setopt($this->ch, CURLOPT_POST, true);
			@curl_setopt($this->ch, CURLOPT_POSTFIELDS, $params['post_fields']);
		}

		if(isset($params['referer'])) {
			@curl_setopt($this->ch, CURLOPT_REFERER, $params['referer']);
		}

		if(isset($params['cookie'])) {
			@curl_setopt($this->ch, CURLOPT_COOKIE, $params['cookie']);
		}

	}

	public function exec($url, $_options = false) {

		$debug = false;

		// overwrite model/defaults
		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {

					case "debug"           : $debug            = $_value; break;

				}
			}
		}


		@curl_setopt($this->ch, CURLOPT_URL, $url);

		$response = curl_exec($this->ch);
		$error = curl_error($this->ch);

		if($debug) {
			print_r($response);
		}

		$result = array(
			'header' => '',
			'body' => '',
			'curl_error' => '',
			'http_code' => '',
			'last_url' => ''
		);

		if($error) {
			$result['curl_error'] = $error;
			return $result;
		}

		$header_size = curl_getinfo($this->ch, CURLINFO_HEADER_SIZE);
		$result['header'] = substr($response, 0, $header_size);
		$result['body'] = substr($response, $header_size);
		$result['http_code'] = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
		$result['last_url'] = curl_getinfo($this->ch, CURLINFO_EFFECTIVE_URL);
		$result['cookies'] = curl_getinfo($this->ch, CURLINFO_COOKIELIST);
		

		if($result["http_code"] == 200 && $result['last_url'] == $url) {
			return $result;
		}
		else {
			return false;
		}

	}
}