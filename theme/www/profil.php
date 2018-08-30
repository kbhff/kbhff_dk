<?php
$access_item["/"] = true;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$action = $page->actions();
$IC = new Items();
$UC = new User();


$page->bodyClass("profil");
$page->pageTitle("Min side");


// Allow accept terms
if(is_array($action) && count($action) == 1 && $action[0] == "accept" && $page->validateCsrfToken()) {

	$UC->acceptedTerms();

}
// User must always accept terms - force dialogue if user has not accepted the terms
else if(!$UC->hasAcceptedTerms()) {

	$page->page(array(
		"templates" => "profile/accept_terms.php",
		"type" => "terms"
	));
	exit();

}



if(is_array($action) && count($action)) {

	if($action[0] == "update" && $page->validateCsrfToken()) {
		$UC->update();
	}

	// ../profil/afdeling lead to template
	else if($action[0] == "afdeling") {
		$page->page(array(
			"templates" => "pages/update_user_department.php"
		));
		exit();
	}

	// ../profil/bruger lead to template
	else if($action[0] == "bruger") {
		$page->page(array(
			"templates" => "pages/update_user_information.php"
		));
		exit();
	}

	// --/profil/kodeord lead to template
	else if($action[0] == "kodeord") {
		$page->page(array(
			"templates" => "pages/update_user_password.php"
		));
		exit();
	}

	// Handling updateUserDepartment method, specified in user.class.php
	else if($action[0] == "updateUserDepartment" && $page->validateCsrfToken()) {

		//Method returns true
		if($UC->updateUserDepartment($action)) {
			header("Location: /profil");
			exit();
		}
		// Method returns false
		else {
			message()->addMessage("Fejl!", array("type" => "error"));
			$page->page(array(
				"templates" => "pages/update_user_department.php"
			));
			exit();
		}
	}

	// Handling updateUserInformation method, specified in user.class.php
	else if($action[0] == "updateUserInformation" && $page->validateCsrfToken()) {

		//Method returns true
		if($UC->updateUserInformation($action)) {
			header("Location: /profil");
			exit();
		}
		//Method returns false
		else {
			// message()->addMessage("Fejl!", array("type" => "error"));
			$page->page([
				"templates" => "pages/update_user_information.php"
			]);
			exit();
		}
	}

	// Handling updateUserPassword method, specified in user.class.php
	else if($action[0] == "updateUserPassword" && $page->validateCsrfToken()) {

		//Method returns true
		if($UC->updateUserPassword($action)) {
			header("Location: /profil");
			exit();
		}
		//Method returns false
		else {
			// message()->addMessage("Fejl!", array("type" => "error"));
			$page->page([
				"templates" => "pages/update_user_password.php"
			]);
			exit();
		}
	}

	//Cancel membership
	else if($action[0] == "opsig") {
		$page->page(array(
			"templates" => "pages/delete_user_information.php"
		));
		exit();
	}

	else if($action[0] == "deleteUserInformation" && $page->validateCsrfToken()) {
		if($UC->deleteUserInformation($action)) {
			header("Location: /");
			exit();
		}

		else {
			// message()->addMessage("Fejl!", array("type" => "error"));
			$page->page([
				"templates" => "pages/delete_user_information.php"
			]);
			exit();
		}
	}

	//Unaccept terms
	else if($action[0] == "unaccept") {
		if($UC->unacceptTerms()) {
			$page->page(array(
				"templates" => "pages/unaccept_terms.php"
			));
			exit();
		}

		exit();
	}	

}


// Fallback
$page->page(array(
	"templates" => "profile/index.php"
));
exit();


?>
 