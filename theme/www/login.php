<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$action = $page->actions();
$model = new User();


$page->bodyClass("login");
$page->pageTitle("Login");


if(is_array($action) && count($action)) {

	if(count($action) == 1 && $action[0] == "dual" && $page->validateCsrfToken()) {


		$username = getPost("username");


		// Requires existing data to be moved to Janitor

		// Detect username (member id / email / phone number)
		// if member id, then look up email
		// If email, then look up member id



		// Do CI login
		include_once("classes/curl.class.php");
		$curl = new CurlRequest();
		$params = array(
			"useragent" => $_SERVER["HTTP_USER_AGENT"],
			"method" => "POST",
			"post_fields" => ["pw" => getPost("password"), "user" => getPost("username"), "hts" => time()]
		);
		$curl->init($params);
		$result = $curl->exec(SITE_URL."/minside/login");
		// Did login result in session cookie
		if($result["cookies"] && preg_match("/kbhff_session/", $result["cookies"][0])) {
			// get cookie details
			list($hostname, $subdomain, $path, $secure, $expiry, $name, $value) = explode("\t", $result["cookies"][0]);

			// set CI session cookie
			setcookie(
				$name,
				$value,
				false,
				"/"
			);



			// Requires existing data to be moved to Janitor

			// Check if user has valid Janitor password yet
			// If not, then set user password, based on current successful CI login
			
			$_POST["username"] = "martin@think.dk";

			// Perform Janitor login
			session()->value("login_forward", "/minside");
			$page->login();

		}



		print "check";
		exit();

	}

	// login/forgot
	if(count($action) == 1 && $action[0] == "forgot") {

		$page->page(array(
			"templates" => "pages/forgot_password.php"
		));
		exit();
	}
	// login/forgot/receipt
	else if(count($action) == 2 && $action[0] == "forgot" && $action[1] == "receipt") {

		$page->page(array(
			"templates" => "pages/forgot_password_receipt.php"
		));
		exit();
	}
	// login/requestReset
	else if(count($action) == 1 && $action[0] == "requestReset" && $page->validateCsrfToken()) {

		// request password reset
		if($model->requestPasswordReset($action)) {
			header("Location: forgot/receipt");
			exit();
		}

		// could not create reset request
		else {
			message()->addMessage("Sorry, you cannot reset the password for the specified user!", array("type" => "error"));
			header("Location: glemt");
			exit();
		}
	}
}

// plain login
$page->page(array(
	"templates" => "pages/login.php"
));

?>
