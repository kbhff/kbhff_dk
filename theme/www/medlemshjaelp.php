<?php

// enable access control
$access_item["/"] = true;
$access_item["/globalSearch"] = true;
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
$UC = new User();
include_once("classes/shop/pickupdate.class.php");
$PC = new Pickupdate();
include_once("classes/system/department.class.php");
$DC = new Department();

// page info
$page->bodyClass("member_help");
$page->pageTitle("Medlemshjælp");

// Allow accept terms
if($action && count($action) == 1 && $action[0] == "accept" && $page->validateCsrfToken()) {

	$UC->acceptedTerms(["name" => "memberhelp"]);

}

// User must always accept terms - force dialogue if user has not accepted the terms
if(!$UC->hasAcceptedTerms(["name" => "memberhelp"])) {

	$page->page(array(
		"templates" => "member-help/accept_terms.php",
		"type" => "login",
		"page_title" => "Samtykke"
	));
	exit();

}

if($action) {
	
	# /medlemshjaelp/soeg
	if($action[0] == "soeg") {
		
		// users that are not allowed to make global searches can only search their own department
		if(!$page->validatePath("/medlemshjaelp/globalSearch")) {
			$user_department = $UC->getUserDepartment();
			$department_id = getPost("department_id");
			
			if($department_id != $user_department["id"]) {
				
				// message()->resetMessages();
				// message()->addMessage("Søgning mislykkedes.", array("type" => "error"));
				exit();
			}
		}

		$users = $model->searchUsers($action);
		$output = new Output();
		$output->screen($users);
		exit();
	}
	
	
	
	# /medlemshjaelp/tilmelding
	if($action[0] == "tilmelding") {
		// signup page
		if(count($action) === 1) {
			$page->page(array(
				"templates" => "member-help/signup.php",
				"type" => "admin"
			));
			exit();
		}

		# /medlemshjaelp/tilmelding/fejl
		// signup error
		else if($action[1] == "fejl") {
			
			$page->page(array(
				"templates" => "member-help/signup-error.php",
				"type" => "admin"
			));
			exit();
			
		}
		
	}

	
	# /medlemshjaelp/save
	else if($action[0] == "save" && $page->validateCsrfToken()) {
		
		// create new user
		$user = $model->newUserFromMemberHelp(["newUserFromMemberHelp"]);
	
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
			
			header("Location: brugerprofil/".$user["existing_user_id"]);
			exit();
		}
		
		// missing terms
		else if(isset($user["status"]) && $user["status"] == "MISSING_TERMS") {
			message()->addMessage("Brugeren skal acceptere betingelserne.", array("type" => "error"));
			
			$page->page(array(
				"templates" => "member-help/signup.php",
				"type" => "admin"
			));
			exit();
		}
		
		// something went wrong
		else {
			message()->addMessage("Beklager! Noget gik galt.", array("type" => "error"));
			
			$page->page(array(
				"templates" => "member-help/signup.php",
				"type" => "admin"
			));
			exit();
		}
		
	}
	
	
	# /medlemshjaelp/brugerprofil
	else if($action[0] == "brugerprofil") {
		
		# /medlemshjaelp/brugerprofil/#user_id#
		if(count($action) == 2) {
			$page->page(array(
				"templates" => "member-help/user-profile.php",
				"type" => "admin"
			));
			exit();
		}
		
		# /medlemshjaelp/brugerprofil/#user_id#/...
		else if(count($action) == 3) {
			# /medlemshjaelp/brugerprofil/#user_id#/afdeling
			if($action[2] == "afdeling") {
				$page->page(array(
					"templates" => "member-help/update_user_department.php",
					"type" => "admin"
				));
				exit();
			}
			# /medlemshjaelp/brugerprofil/#user_id#/opsig
			elseif($action[2] == "opsig") {
				$page->page(array(
					"templates" => "member-help/delete_user_information.php",
					"type" => "admin"
				));
				exit();
			}
			
			# /medlemshjaelp/brugerprofil/#user_id#/medlemsskab
			elseif($action[2] == "medlemsskab") {
				$page->page(array(
					"templates" => "member-help/update_user_membership.php",
					"type" => "admin"
				));
				exit();
			}
			
			# /medlemshjaelp/brugerprofil/#user_id#/oplysninger
			else if($action[2] == "oplysninger") {
				$page->page(array(
					"templates" => "member-help/update_user_information.php",
					"type" => "admin"
				));
				exit();
			}
			# /medlemshjaelp/brugerprofil/#user_id#/kodeord
			else if($action[2] == "kodeord") {
				$page->page(array(
					"templates" => "member-help/update_user_password.php",
					"type" => "admin"
				));
				exit();
			}
			
			# /medlemshjaelp/brugerprofil/#user_id#/kodeord
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
	
	# /medlemshjaelp/updateUserInformation
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
	
	# /medlemshjaelp/updateUserDepartment
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
	
	# /medlemshjaelp/updateUserMembership
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
	

	# /medlemshjaelp/deleteUserInformation
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
					"templates" => "member-help/delete_user_information.php",
					"type" => "admin"
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

	# /medlemshjaelp/updateUserPassword
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

	# /medlemshjaelp/butik
	else if($action[0] == "butik") {
		
		# /medlemshjaelp/butik/#user_id#
		if(count($action) == 2) {


			$page->page(array(
				"templates" => "member-help/shop.php"
			));	
			exit();
		}
		
	}

	# /medlemshjaelp/betaling
	else if($action[0] == "betaling") {
		
		# /medlemshjaelp/betaling/#order_no#
		if(count($action) === 2) {
			$page->page(array(
				"templates" => "member-help/payment.php",
				"type" => "admin"
			));
			exit();
		}
		
		# /medlemshjaelp/betaling/spring-over/kvittering
		else if(count($action) === 3 && $action[1] == "spring-over") {
			$page->page(array(
				"templates" => "member-help/receipt/skipped.php",
				"type" => "admin"
			));
			exit();
		} 
		
		# /medlemshjaelp/betaling/stripe/ordre/#order_no#/process
		else if(count($action) === 5 && $action[4] == "process" && $page->validateCsrfToken()) {
			
			$gateway = $action[1];
			$order_no = $action[3];


			$payment_method_result = $SC->processCardForOrder($action);
			if($payment_method_result) {

				if($payment_method_result["status"] === "success") {

					$return_url = SITE_URL."/medlemshjaelp/betaling/stripe/register-paid-intent";
					$result = payments()->requestPaymentIntentForOrder(
						$payment_method_result["order"], 
						$payment_method_result["card"]["id"], 
						$return_url
					);
					if($result) {

						if($result["status"] === "PAYMENT_CAPTURED") {

							// redirect to leave POST state
							header("Location: $return_url/?payment_intent=".$result["payment_intent_id"]);
								exit();

						}
						else if($result["status"] === "ACTION_REQUIRED") {

							// redirect to leave POST state
							header("Location: ".$result["action"]);
							exit();
					
						}

						else if($result["status"] === "CARD_ERROR") {

							// Janitor Validation failed
							message()->addMessage($result["message"], ["type" => "error"]);
							// redirect to leave POST state
							header("Location: /medlemshjaelp/betalingsgateway/".$gateway."/ordre/".$order_no);
							exit();

						}

					}

				}
				else if($payment_method_result["status"] === "STRIPE_ERROR" || $payment_method_result["status"] === "ORDER_NOT_FOUND") {

					if($payment_method_result["status"] === "STRIPE_ERROR")	{
						$payment_method_result["message"] = "Der skete en fejl i behandlingen af din betaling.";
					}
					else if($payment_method_result["status"] === "ORDER_NOT_FOUND")	{
						$payment_method_result["message"] = "Ordren blev ikke fundet.";
					}

					message()->addMessage($payment_method_result["message"], ["type" => "error"]);
					// redirect to leave POST state
					header("Location: /medlemshjaelp/betaling/$order_no");
					exit();

				}
				else if($payment_method_result["status"] === "CARD_ERROR") {

					switch($payment_method_result["code"]) {

						case "incorrect_number"         : $message = "Forkert kortnummer."; break;
						case "invalid_number"           : $message = "Kortnummeret er ikke et gyldigt kreditkortnummer."; break;
						case "invalid_expiry_month"     : $message = "Ugyldig udløbsdato."; break;
						case "invalid_expiry_year"      : $message = "Ugyldigt udløbsår."; break;
						case "invalid_cvc"              : $message = "Ugyldig sikkerhedskode."; break;
						case "expired_card"             : $message = "Kortet er udløbet."; break;
						case "incorrect_cvc"            : $message = "Forkert sikkerhedskode."; break;
						case "incorrect_zip"            : $message = "Kortets postnummer kunne ikke bekræftes."; break;
						case "card_declined"            : $message = "Kortet blev afvist."; break;
					}


					message()->addMessage($message, ["type" => "error"]);
					// redirect to leave POST state
					header("Location: /medlemshjaelp/betaling/$order_no");
					exit();
				}

			}


			// Janitor Validation failed
			message()->addMessage($payment_method_result["message"], ["type" => "error"]);
			// redirect to leave POST state
			header("Location: /medlemshjaelp/betaling/$order_no");
			exit();	
			
		}

		# /medlemshjaelp/betaling/stripe/register-paid-intent
		else if(count($action) == 3 && $action[2] == "register-paid-intent") {

			$payment_intent_id = getVar("payment_intent");

			$id_result = payments()->identifyPaymentIntent($payment_intent_id);
			if($id_result && $id_result["status"] === "success") {

				// Single order
				if($id_result["order_no"]) {

					$order = $SC->getOrders(["order_no" => $id_result["order_no"]]);
					if($order) {

						// Register intent for order (and subscription)
						$intent_registration_result = payments()->updatePaymentIntent($payment_intent_id, $order);
						if($intent_registration_result["status"] === "success") {

							// Register payment for order (if paid)
							if($id_result["payment_status"] === "succeeded") {

								$payment_registration_result = payments()->registerPayment($order, $id_result["payment_intent"]);

								// Clear messages
								message()->resetMessages();

								// Successful registration of payment
								if($payment_registration_result && $payment_registration_result["status"] === "REGISTERED") {

									// redirect to leave POST state
									header("Location: /medlemshjaelp/betaling/".$order["order_no"]."/".$payment_registration_result["payment_id"]."/kvittering");
									exit();
								}
							}

						}

					}

				}

			}
			else if($id_result && $id_result["status"] === "error") {

				if($id_result["code"] === "payment_intent_authentication_failure") {
					$id_result["message"] = "Tredjepartsautentificeringen slog fejl. Prøv igen eller brug et andet kort.";
				}

				message()->addMessage($id_result["message"], ["type" => "error"]);
				// redirect to leave POST state
				header("Location: /medlemshjaelp/betaling/".$id_result["order_no"]);
				exit();

			}

			// Fatal error
			message()->addMessage("Det mislykkedes at behandle din betalingsanmodning. Prøv igen eller <a href=\"mailto:it@kbhff.dk?subject=Payment%20error&body=Payment%20Intent:%20$payment_intent_id\">kontakt os</a>, så vi kan løse problemet.", ["type" => "error"]);
			// redirect to leave POST state
			header("Location: /butik/kvittering/fejl");
			exit();

		}
		
		# /medlemshjaelp/betaling/#order_no/#payment_id#/kvittering
		else if(count($action) === 4 && $action[3] == "kvittering") {
			$page->page(array(
				"templates" => "member-help/receipt/index.php",
				"type" => "admin"
			));
			exit();
		}

		
	}
	
	# /medlemshjaelp/registerPayment/#order_no#
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
	
	# /medlemshjaelp/paymentError
	else if($action[0] == "paymentError") {
		$page->page(array(
			"templates" => "member-help/payment.php",
			"type" => "admin"
		));
		exit();
	}

} 

// member-help start page
// /member-help
$page->page(array(
	"templates" => "member-help/index.php",
	"type" => "admin"
));

?>