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



	// LOGIN

	// Custom DUAL CodeIgniter/Janitor login
	if(count($action) == 1 && $action[0] == "dual" && $page->validateCsrfToken()) {


		$username = getPost("username");


		// Requires existing data to be moved to Janitor

		// TODO
		// Detect username (member id / email / phone number)
		// If email, then look up member id for CI login



		// Do CI login
		include_once("classes/helpers/curl.class.php");
		$curl = new CurlRequest();
		$params = array(
			"useragent" => $_SERVER["HTTP_USER_AGENT"],
			"method" => "POST",
			"post_fields" => ["pw" => getPost("password"), "user" => getPost("username"), "hts" => time()]
		);
		$curl->init($params);
		$result = $curl->exec(SITE_URL."/minside/login");


		// Did login result in session cookie?
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
			
//			$_POST["username"] = "martin@think.dk";


			// check if user has Janitor password
			// otherwise we must create a new password since we have the password at hand
			$query = new Query();

			$sql = "SELECT user_id FROM ".SITE_DB.".user_usernames as usernames WHERE usernames.username='$username' AND user_id IN (SELECT user_id FROM ".SITE_DB.".user_passwords)";
//			print $sql."<br>\n";
			// No password stored - first login
			if(!$query->sql($sql)) {

				// create new password for user to prepare for Janitor login
				$password = password_hash(getPost("password"), PASSWORD_DEFAULT);
	
				// Let's try to get the user_id
				$sql = "SELECT usernames.user_id as user_id FROM ".SITE_DB.".user_usernames as usernames WHERE usernames.username='$username'";
//				print $sql."<br>\n";
				if($query->sql($sql)) {

					$user_id = $query->result(0,"user_id");
					$sql = "INSERT INTO ".SITE_DB.".user_passwords SET user_id = $user_id, password = '$password'";
					$query->sql($sql);

				}

			}


			session()->value("kbhff_session", true);
		}

		// Perform Janitor login and redirect to /profil
		session()->value("login_forward", "/profil");
		$login_status = $page->login();
		
		// remove any English messages added by Janitor backend
		message()->resetMessages();


		// User could not log in because user is not verified
		if ($login_status && isset($login_status["status"]) && $login_status["status"] == "NOT_VERIFIED") {

			session()->value("temp-username", getPost("username"));
//			message()->addMessage("Brugernavnet er endnu ikke bekræftet.", ["type" => "error"]);
			header("Location: /login/bekraeft-konto");
			exit();

		}
		// User could not log in because user is not verified
		else if ($login_status && isset($login_status["status"]) && $login_status["status"] == "NO_PASSWORD") {

			session()->value("temp-username", getPost("username"));
//			message()->addMessage("Din konto er endnu ikke aktiveret.", ["type" => "error"]);		
			header("Location: /login/bekraeft-konto");
			exit();

		}
		// User could not be logged in – save username temporarily and redirect back to login to leave POST state
		else {

			session()->value("temp-username", getPost("username"));
			message()->addMessage("Du har indtastet et forkert brugernavn eller password.", ["type"=>"error"]);
			header("Location: /login");
			exit();

		}

	}


	// LOGOFF

	// Manual dual logoff
	// login/logoff
	else if(count($action) == 1 && $action[0] == "logoff") {

		// CI logoff
		include_once("classes/curl.class.php");
		$curl = new CurlRequest();
		$params = array(
			"useragent" => $_SERVER["HTTP_USER_AGENT"],
			"method" => "GET",
			"cookie" => $_COOKIE["kbhff_session"]
		);
		$curl->init($params);
		$result = $curl->exec(SITE_URL."/logud");

		// Logoff seems to be successful
//		if($result["http_code"] == 200) {

			// clear CI session cookie
			setcookie(
				"kbhff_session",
				"",
				time()-3600,
				"/"
			);

//		}

		// Janitor logoff
		$page->logoff();

		exit();
	}



	// CONFIRM ACCOUNT

	// Confirm account / code form
	else if(count($action) == 1 && $action[0] == "bekraeft-konto") {

		// No confirm without a username
		if(session()->value("temp-username")) {

			$page->page(array(
				"templates" => "profile/confirm_account.php"
			));
			exit();
		}
		// return to login page
		else {

			message()->addMessage("Der skete en ukendt fejl. Prøv igen.");
			header("Location: /login");
			exit();
		}

	}
	// Create password
	else if(count($action) == 1 && $action[0] == "opret-password") {

		$page->page(array(
			"templates" => "profile/create_password.php"
		));
		exit();
	}
	// login/confirmAccount
	else if(count($action) == 1 && $action[0] == "confirmAccount" && $page->validateCsrfToken()) {

		// request password reset
		$user_id = $model->confirmAccount($action);
		if($user_id) {

			session()->value("user_id", $user_id);

			// if user has password, forward to login page
			if($model->hasPassword()) {
				print "login";

				message()->addMessage("Din konto er nu aktiveret og du kan logge ind.");
				header("Location: /login");
				exit();
				
			}
			// if user does not have password, forward to password creation page
			else {

				print "opret";
				header("Location: /login/opret-password");
				exit();
				
			}


		}

		// could not create reset request
		else {
			message()->addMessage("Beklager, du kan ikke aktivere den givne konto!", array("type" => "error"));
//			header("Location: /login");
			exit();
		}
		exit();
	}



	// RESET PASSWORD

	// login/glemt
	else if(count($action) == 1 && $action[0] == "glemt") {

		$page->page(array(
			"templates" => "pages/forgot_password.php"
		));
		exit();
	}
	// login/glemt/nulstilling
	else if(count($action) == 2 && $action[0] == "glemt" && $action[1] == "nulstilling") {

		$page->page(array(
			"templates" => "pages/forgot_password_code.php"
		));
		exit();
	}
	// login/glemt/nyt-password
	else if(count($action) == 2 && $action[0] == "glemt" && $action[1] == "nyt-password") {

		$page->page(array(
			"templates" => "pages/forgot_password_set_new_password.php"
		));
		exit();
	}
	// login/requestReset
	else if(count($action) == 1 && $action[0] == "requestReset" && $page->validateCsrfToken()) {

		// request password reset
		if($model->requestPasswordReset($action)) {
			header("Location: glemt/nulstilling");
			exit();
		}

		// could not create reset request
		else {
			message()->addMessage("Beklager, du kan ikke nulstille password for den givne bruger!", array("type" => "error"));
			header("Location: glemt");
			exit();
		}
	}

	// login/validateCode
	else if(count($action) == 1 && $action[0] == "validateCode" && $page->validateCsrfToken()) {

		// code is valid
		if($model->validateCode($action)) {
			header("Location: glemt/nyt-password");
			exit();
		}

		// code is not valid
		else {
			message()->addMessage("Beklager, din nulstillingskode er forkert!", array("type" => "error"));
			header("Location: glemt/nulstilling");
			exit();
		}
	}

	// login/resetPassword
	else if(count($action) == 1 && $action[0] == "resetPassword" && $page->validateCsrfToken()) {

		// creating new password
		if($model->resetPassword($action)) {
			header("Location: /login");
			exit();
		}

		// could not create new password
		else {
			message()->addMessage("Du kan ikke bruge dette password!", array("type" => "error"));
			header("Location: glemt/nyt-password");
			exit();
		}
	}
}

// Login will redirect here (due to dual login)
// If Janitor login was successful, redirect til CI page, "/minside"
// if(session()->value("user_group_id") > 1) {
//
// 	header("Location: /profil");
//
// }
// // User not logged in
// else {

	// plain login
	$page->page(array(
		"templates" => "pages/kbhff-login.php"
	));

// }

?>
