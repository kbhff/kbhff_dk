<?php
$access_item["/"] = true;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$action = $page->actions();
$IC = new Items();
$UC = new User();
$MC = new Member();
$query = new Query();


$page->bodyClass("profil");
$page->pageTitle("Min side");


// Allow accept terms
if($action && count($action) == 1 && $action[0] == "accept" && security()->validateCsrfToken()) {

	$UC->acceptedTerms();

}

// Need to be able to delete account before user has accepted terms
else if ($action) {

	// profil/opsig
	if($action[0] == "opsig") {
		$page->page(array(
			"templates" => "profile/delete_user_information.php",
			"type" => "member"
		));
		exit();
	}
	

	// profil/deleteUserInformation
	else if($action[0] == "deleteUserInformation" && security()->validateCsrfToken()) {
		// If the method is requested by JavaScript
		if($_SERVER["HTTP_X_REQUESTED_WITH"]) {
			// Method returns true and deletes user
			if ($UC->deleteUserInformation($action)) {
				$JSrequest = "JS-request";
				$output = new Output();
				$output->screen($JSrequest, ["reset_messages" => false]);
				exit();	
			}
			// Method fails
			else {
				$page->page([
					"templates" => "profile/delete_user_information.php",
					"type" => "member"
				]);
				exit();
			}
		}
		// If the method is requested by default HTML
		else {
			// Method returns true and deletes user
			if($UC->deleteUserInformation($action)) {
				header("Location: /");
				exit();
			}

			// Method fails
			else {
				$page->page([
					"templates" => "profile/delete_user_information.php",
					"type" => "member"
				]);
				exit();
			}
		}
	}
}

// User must always accept terms - force dialogue if user has not accepted the terms
if(!$UC->hasAcceptedTerms()) {

	$page->page(array(
		"templates" => "profile/accept_terms.php",
		"type" => "login",
		"page_title" => "Samtykke"
	));
	exit();

}

if($action) {

	// Allow update
	if($action[0] == "update" && security()->validateCsrfToken()) {
		$UC->update($action);
	}

	// ../profil/afdeling lead to template
	else if($action[0] == "afdeling") {
		$page->page(array(
			"templates" => "profile/update_user_department.php",
			"type" => "member"
		));
		exit();
	}
	else if($action[0] == "ordre-historik") {
		$page->page(array(
			"templates" => "profile/order_history.php",
			"type" => "member"
		));
		exit();
	}
	
	else if($action[0] == "medlemskab") {


		// ../profil/medlemskab/fornyelse
		if(count($action) == 2 && $action[1] == "fornyelse") {
			$page->page(array(
				"templates" => "profile/update_membership_renewal.php",
				"type" => "member"
			));
			exit();
		}
		// ../profil/medlemskab/genaktiver
		else if(count($action) == 2 && $action[1] == "genaktiver") {
			$page->page(array(
				"templates" => "profile/reactivate_membership.php",
				"type" => "member"
			));
			exit();
		}

	}


	// ../profil/bruger 
	else if($action[0] == "bruger") {
		$page->page(array(
			"templates" => "profile/update_user_information.php",
			"type" => "member"
		));
		exit();
	}

	// ../profil/kodeord 
	else if($action[0] == "kodeord") {
		$page->page(array(
			"templates" => "profile/update_user_password.php",
			"type" => "member"
		));
		exit();
	}

	// /profil/beskedcenter
	else if($action[0] == "beskedcenter") {
		$page->page(array(
			"templates" => "profile/message_center.php",
			"type" => "member"
		));
		exit();
	}

	// /profil/updateUserDepartment
	// Handling updateUserDepartment method, specified in user.class.php
	else if($action[0] == "updateUserDepartment" && security()->validateCsrfToken()) {

		//Method returns true
		if($UC->updateUserDepartment($action)) {
			
			header("Location: /profil");
			exit();
		}
		// Method returns false
		else {
			message()->addMessage("Fejl!", array("type" => "error"));
			$page->page(array(
				"templates" => "profile/update_user_department.php",
				"type" => "member"
			));
			exit();
		}
	}

	// ../profil/ny-afdeling-advarsel 
	else if($action[0] == "ny-afdeling-advarsel") {
		$page->page(array(
			"templates" => "profile/new_department_warning.php",
			"type" => "member"
		));
		exit();
	}

	// /profil/updateMembershipRenewal
	else if($action[0] == "updateMembershipRenewal" && security()->validateCsrfToken()) {

		$result = $UC->updateRenewalOptOut($action);

		if($result === "REACTIVATION REQUIRED") {

			header("Location: /profil/medlemskab/genaktiver");
			exit();

		}
		else {

			header("Location: /profil");
			exit();
		}
	}

	// /profil/updateEmailAgreements
	else if($action[0] == "updateEmailAgreements" && security()->validateCsrfToken()) {

		$result = $UC->updateEmailAgreements($action);

		if($result) {

			header("Location: /profil/beskedcenter");
			exit();

		}
	}

	// profil/reactivateMembership
	else if($action[0] == "reactivateMembership" && security()->validateCsrfToken()) {

		$order = $MC->switchMembership($action);

		if($order) {

			$_POST["membership_renewal"] = 1;
			$result = $UC->updateRenewalOptOut(["updateRenewalOptOut"]);
			unset($_POST);
			if($result) {

				header("Location: /butik/betaling/".$order["order_no"]);
				exit();
			}
			
		}

		message()->addMessage("Der skete en fejl.", array("type" => "error"));
		$page->page([
			"templates" => "profile/reactivate_membership.php",
			"type" => "member"
		]);
		exit();
	}

	// profil/updateUserInformation
	else if($action[0] == "updateUserInformation" && security()->validateCsrfToken()) {

		//Method returns true
		if($UC->updateUserInformation($action)) {
			header("Location: /profil");
			exit();
		}
		//Method returns false
		else {
			$page->page([
				"templates" => "profile/update_user_information.php",
				"type" => "member"
			]);
			exit();
		}
	}

	// profil/updateEmail
	else if($action[0] == "updateEmail" && security()->validateCsrfToken()) {

		//Method returns true
		if($UC->updateEmail($action)) {

			message()->addMessage("Din e-mailadresse blev opdateret.");
		}
		else {
			message()->addMessage("Det lykkedes ikke at opdatere din e-mailadresse.");
		}
		
		header("Location: /profil");
		exit();
	}

	// profil/updateUserPassword
	else if($action[0] == "updateUserPassword" && security()->validateCsrfToken()) {

		$result = $UC->setPassword($action);
		//Method returns true
		if($result === true) {
			message()->resetMessages();
			message()->addMessage("Adgangskoden blev opdateret.");
			header("Location: /profil");
			exit();
		}
		else if(isset($result["error"]) && $result["error"] == "wrong_password") {
			message()->addMessage("Du har tastet en forkert adgangskode, så din adgangskode blev ikke opdateret.", array("type" => "error"));
			header("Location: /profil/kodeord");
			exit();
		}
		//Method returns false
		else {
			message()->addMessage("Der skete en fejl.", array("type" => "error"));
			header("Location: /profil/kodeord");
			exit();
		}
	}

	// Unaccept terms, for testing purposes
	// profil/unaccept
	else if($action[0] == "unaccept") {

		// Method returns true
		if($UC->unacceptTerms()) {
			$page->page(array(
				"templates" => "pages/unaccept_terms.php",
				"type" => "member"
			));
			exit();
		}

		// Fallback
		exit();
	}

}

if(session()->value("user_id") > 1 && !$UC->hasEmailAddress()) {

	$page->page(array(
		"templates" => "profile/update_email.php",
		"type" => "login",
		"page_title" => "Angiv e-mailadresse"
	));
	exit();
}

// Default template
$page->page(array(
	"templates" => "profile/index.php",
	"type" => "member"
));
exit();
