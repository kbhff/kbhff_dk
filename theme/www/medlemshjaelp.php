<?php

// enable access control
$access_item["/"] = true;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");
include_once("classes/users/superuser.class.php");
include_once("classes/shop/supershop.class.php");

// get REST parameters
$action = $page->actions();

// define which model this controller is referring to
$model = new SuperUser();
$SC = new SuperShop();

// page info
$page->bodyClass("member_help");
$page->pageTitle("Medlemshjælp");

if($action) {
	
	if($action[0] == "soeg") {
		$users = $model->searchUsers($action);
		$output = new Output();
		$output->screen($users);
		exit();
	}
	
	
	
	// /medlemshjaelp/tilmelding
	if($action[0] == "tilmelding") {
		// signup page
		if(count($action) === 1) {
			$page->page(array(
				"templates" => "member-help/signup.php"
			));
			exit();
		}

		// /medlemshjaelp/tilmelding/fejl
		// signup error
		else if($action[1] == "fejl") {
			
			$page->page(array(
				"templates" => "member-help/signup-error.php"
			));
			exit();
			
		}
		
	}

	
	// /medlemshjaelp/save
	else if($action[0] == "save" && $page->validateCsrfToken()) {
		
		// create new user (with a "fake" $action-array)
		$user = $model->newUserFromMemberHelp(array("newUserFromMemberHelp"));
		
		// successful creation
		if(isset($user["user_id"])) {
			
			// add user_id to $_POST array, which will be used to create a new cart with addCart()
			$_POST["user_id"] = $user["user_id"];
			
			// create cart
			$cart = $SC->addCart(array("addCart"));
			if($cart) {
				
				// add new user to cart
				if($SC->addToCart(array("addToCart", $cart["cart_reference"]))) {
					
					// convert cart to order
					$order = $SC->newOrderFromCart(array("newOrderFromCart", $cart["id"], $cart["cart_reference"]));
					if($order) {						
						
						// redirect to payment
						message()->resetMessages();
						header("Location: betaling/".$order["order_no"]);
						exit();
					}
					
					// error
					else {
						message()->resetMessages();
						message()->addMessage("Det mislykkedes at omdanne indkøbskurven til en ordre.", array("type" => "error"));
						header("Location: tilmelding/fejl");
						exit();
					}
				}
				
				// error
				else {
					message()->resetMessages();
					message()->addMessage("Det mislykkedes at føje medlemskabet til indkøbskurven.", array("type" => "error"));
					header("Location: tilmelding/fejl");
					exit();
				}
			}
			
			// error
			else {
				message()->resetMessages();
				message()->addMessage("Det mislykkedes at oprette en indkøbskurv.", array("type" => "error"));
				header("Location: tilmelding/fejl");
				exit();
			}
			
		}
		
		// user exists
		else if(isset($user["status"]) && $user["status"] == "USER_EXISTS") {
			message()->addMessage("Du forsøgte at oprette en bruger, der allerede findes i systemet. Du er derfor blevet videredirigeret til denne brugers profilside, hvorfra du kan opdatere brugerdata, oprette et medlemskab for brugeren m.m.", array("type" => "error"));
			
			header("Location: brugerprofil/".$user["user_id"]);
			exit();
		}
		
		// missing terms
		else if(isset($user["status"]) && $user["status"] == "MISSING_TERMS") {
			message()->addMessage("Brugeren skal acceptere betingelserne.", array("type" => "error"));
			
			$page->page(array(
				"templates" => "member-help/signup.php"
			));
			exit();
		}
		
		// something went wrong
		else {
			message()->addMessage("Beklager! Noget gik galt.", array("type" => "error"));
			
			$page->page(array(
				"templates" => "member-help/signup.php"
			));
			exit();
		}
		
	}
	
	
	// /medlemshjaelp/brugerprofil
	else if($action[0] == "brugerprofil") {
		
		//	/medlemshjaelp/brugerprofil/#user_id#
		if(count($action) == 2) {
			$page->page(array(
				"templates" => "member-help/user-profile.php"
			));
			exit();
		}
		// /medlemshjaelp/brugerprofil/#user_id#/
		
		else if(count($action) == 3) {
			// /medlemshjaelp/brugerprofil/#user_id#/afdeling
			if($action[2] == "afdeling") {
				$page->page(array(
				   "templates" => "member-help/update_user_department.php"
			   ));
			   exit();
			}
			// /medlemshjaelp/brugerprofil/#user_id#/opsig
			elseif($action[2] == "opsig") {
				$page->page(array(
					"templates" => "member-help/delete_user_information.php"
				));
				exit();
			}
			
			// /medlemshjaelp/brugerprofil/#user_id#/medlemsskab
			elseif($action[2] == "medlemsskab") {
				$page->page(array(
					"templates" => "member-help/update_user_membership.php"
				));
				exit();
			}
			
			// /medlemshjaelp/brugerprofil/#user_id#/oplysninger lead to template
			else if($action[2] == "oplysninger") {
				$page->page(array(
					"templates" => "member-help/update_user_information.php"
				));
				exit();
			}
			// medlemshjaelp/brugerprofil/#user_id#/kodeord lead to template
			else if($action[2] == "kodeord") {
				$page->page(array(
					"templates" => "member-help/update_user_password.php"
				));
				exit();
			}
			
			// medlemshjaelp/brugerprofil/#user_id#/kodeord lead to template
			else if($action[2] == "accepter") {
				//Method returns true
				if($model->acceptedTerms(["user_id" => $action[1]])) {
					header("Location: /medlemshjaelp/brugerprofil/$action[1]");
					exit();
				}
				//Method returns false
				else {
					header("Location: /medlemshjaelp/brugerprofil/$action[1]");
					exit();
				}
			}
		}
	}
	
	// profil/updateUserInformation
	else if($action[0] == "updateUserInformation" && $page->validateCsrfToken()) {

		//Method returns true
		if($model->updateUserInformation($action)) {
			header("Location: /medlemshjaelp/brugerprofil/$action[1]");
			exit();
		}
		//Method returns false
		else {
			header("Location: /medlemshjaelp/brugerprofil/$action[1]");
			exit();
		}
	}
	
	
	// Handling updateUserDepartment method, specified in superuser.class.php
	else if($action[0] == "updateUserDepartment" && $page->validateCsrfToken()) {

		//Method returns true
		
		if ($model->updateUserDepartment($action)) {
			header("Location: /medlemshjaelp/brugerprofil/$action[1]");
			exit();
		}
		
		// Method returns false
		else {
			message()->resetMessages();
			message()->addMessage("Afdeling blev ikke opdateret.", ["type" => "error"]);
			header("Location: /medlemshjaelp/brugerprofil/$action[1]");
			exit();
		}
	}
	
	// Handling updateUserDepartment method, specified in superuser.class.php
	else if($action[0] == "updateUserMembership" && $page->validateCsrfToken()) {

		//Method returns true
		if ($model->changeMembership($action)) {
			header("Location: /medlemshjaelp/brugerprofil/$action[1]");
			exit();
		}
		// Method returns false
		else {
			message()->resetMessages();
			message()->addMessage("Medlemsskab blev ikke opdateret.", ["type" => "error"]);
			header("Location: /medlemshjaelp/brugerprofil/$action[1]");
			exit();
		}
	}
	

	// medlemshjaelp/deleteUserInformation
	else if($action[0] == "deleteUserInformation") {
		
		// If the method is requested by JavaScript
		if($_SERVER["HTTP_X_REQUESTED_WITH"]) {
			// Method returns true and deletes user
			if ($model->deleteUserInformation(["user_id" => $action[1]])) {
				$JSrequest = "JS-request";
				$output = new Output();
				$output->screen($JSrequest, ["reset_messages" => false]);
				exit();	
			}
			// Method fails
			else {
				$page->page([
					"templates" => "member-help/delete_user_information.php"
				]);
				exit();
			}
		}
		// If the method is requested by default HTML
		else {
			// Method returns true and deletes user
			if($model->deleteUserInformation(["user_id" => $action[1]])) {
				header("Location: /medlemshjaelp");
				exit();
			}
			
			// Method fails
			else {
				header("Location: /medlemshjaelp/brugerprofil/$action[1]/opsig");
				exit();
			}
		}
		
	}

	// profil/updateUserPassword
	else if($action[0] == "updateUserPassword" && $page->validateCsrfToken()) {

		//Method returns true
		if($model->updateUserPassword($action[1])) {
			message()->resetMessages();
			message()->addMessage("Adgangskoden er opdateret");
			header("Location: /medlemshjaelp/brugerprofil/$action[1]");
			exit();
		}
		//Method returns false
		else {
			message()->resetMessages();
			message()->addMessage("Der skete en fejl, så adgangskoden ikke blev opdateret.", array("type" => "error"));
			header("Location: /medlemshjaelp/brugerprofil/$action[1]/kodeord");
			exit();
		}
	}

	else if($action[0] == "betaling") {
		
		// /medlemshjaelp/betaling/#order_no#
		if(count($action) === 2) {
			$page->page(array(
				"templates" => "member-help/payment.php"
			));
			exit();
		}
		
		// /medlemshjaelp/betaling/spring-over/kvittering
		else if(count($action) === 3 && $action[1] == "spring-over") {
			$page->page(array(
				"templates" => "member-help/receipt/skipped.php"
			));
			exit();
		} 

		// /medlemshjaelp/betaling/#payment_id#/kvittering
		else if(count($action) === 4 && $action[3] == "kvittering") {
			$page->page(array(
				"templates" => "member-help/receipt/index.php"
			));
			exit();
		}

		
	}
	
	// /medlemshjaelp/registerPayment/#order_no#
	else if(count($action) === 2 && $action[0] == "registerPayment") {
		
		// create payment id
		$payment_id = $SC->registerPayment(["registerPayment"]);
		if($payment_id) {		
			// redirect to receipt
			message()->resetMessages();
			header("Location: /medlemshjaelp/betaling/".$payment_id."/".$action[1]."/kvittering");
			exit();
		}

		// error
		else {
			
			// redirect back to payment
			message()->resetMessages();
			message()->addMessage("Der skete en fejl i registreringen af betalingen.", array("type" => "error"));
			header("Location: /medlemshjaelp/betaling/".$action[1]);
			exit();
		}
	
		exit();

	}
	
	// /medlemshjaelp/paymentError
	else if($action[0] == "paymentError") {
		$page->page(array(
			"templates" => "member-help/payment.php"
		));
		exit();
	}

} 

// member-help start page
// /member-help
$page->page(array(
	"templates" => "member-help/index.php"
));

?>