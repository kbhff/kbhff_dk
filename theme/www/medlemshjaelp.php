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
include_once("classes/users/supermember.class.php");
$MC = new SuperMember();

// page info
$page->bodyClass("member_help");
$page->pageTitle("Medlemshjælp");

// User must be active member
$user = $UC->getKbhffUser();
if($user["membership"] && !$user["membership"]["subscription_id"]) {

	$page->page(array(
		"templates" => "profile/inactive_membership.php",
		"type" => "member",
		"page_title" => "Inaktivt medlemskab"
	));
	exit();
}

// Allow accept terms
if($action && count($action) == 1 && $action[0] == "accept" && security()->validateCsrfToken()) {

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
		if(!security()->validatePath("/medlemshjaelp/globalSearch")) {
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
				"templates" => "member-help/signup_error.php",
				"type" => "admin"
			));
			exit();
			
		}
		
	}

	
	# /medlemshjaelp/save
	else if($action[0] == "save" && security()->validateCsrfToken()) {
		
		// create new user
		$user = $model->newUserFromMemberHelp(["newUserFromMemberHelp"]);
	
		// successful creation
		if(isset($user["user_id"])) {

			if(getPost("maillist")) {
				$model->addToMailchimp([
					"email_address" => $user["email"],
					"status" => "pending"
				]);
			}
			
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
						
						$SC->sendOrderMails($order);
						
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

		// Make sure this is a valid user
		$member_user = $model->getUsers(["user_id" => $action[1]]);

		if($action[1] == 1 || $member_user["status"] < 0) {

			$page->page(array(
				"templates" => "member-help/user_invalid.php",
				"page_title" => "Ugyldig bruger"
			));
			exit();

		}

		// Allow accept terms
		if(count($action) == 3 && $action[2] == "user_accept" && security()->validateCsrfToken()) {

			$model->acceptedTerms(["user_id" => $action[1]]);

			header("Location: /medlemshjaelp/brugerprofil/".$action[1]);
			exit();
		}

		// User must always accept terms - force dialogue if user has not accepted the terms
		else if(count($action) == 2 && !$model->hasAcceptedTerms(["user_id" => $action[1]])) {

			$page->page(array(
				"templates" => "member-help/user_accept_terms.php",
				"type" => "login",
				"page_title" => "Samtykke"
			));
			exit();
		}

		else if(count($action) == 2 && !$model->hasEmailAddress(["user_id" => $action[1]])) {

			$page->page(array(
				"templates" => "member-help/update_user_email.php",
				"type" => "login",
				"page_title" => "Angiv e-mailadresse"
			));
			exit();
		}

		# /medlemshjaelp/brugerprofil/#user_id#
		else  if(count($action) == 2) {

			$page->page(array(
				"templates" => "member-help/user_profile.php",
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

			# /medlemshjaelp/brugerprofil/#user_id#/medlemskab
			elseif($action[2] == "medlemskab") {

				$page->page(array(
					"templates" => "member-help/update_user_membership.php",
					"type" => "admin"
				));
				exit();
			}

			# /medlemshjaelp/brugerprofil/#user_id#/ordre-historik
			elseif($action[2] == "ordre-historik") {

				$page->page(array(
					"templates" => "member-help/user_order_history.php",
					"type" => "admin"
				));
				exit();
			}

			# /medlemshjaelp/brugerprofil/#user_id#/fornyelse
			else if($action[2] == "fornyelse") {
				$page->page(array(
					"templates" => "member-help/update_user_membership_renewal.php",
					"type" => "member"
				));
				exit();
			}

			# /medlemshjaelp/brugerprofil/#user_id#/genaktiver
			else if($action[2] == "genaktiver") {
				$page->page(array(
					"templates" => "member-help/reactivate_user_membership.php",
					"type" => "member"
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

			# /medlemshjaelp/brugerprofil/#user_id#/beskedcenter
			else if($action[2] == "beskedcenter") {
				$page->page(array(
					"templates" => "member-help/user_message_center.php",
					"type" => "admin"
				));
				exit();
			}



		}

	}
	
	# /medlemshjaelp/updateUserInformation
	else if($action[0] == "updateUserInformation" && security()->validateCsrfToken()) {

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
	
	# /medlemshjaelp/updateEmail
	else if($action[0] == "updateEmail" && security()->validateCsrfToken()) {

		//Method returns true
		if($model->updateEmail($action)) {
			message()->addMessage("Medlemmets e-mailadresse blev opdateret.");
		}
		else {
			message()->addMessage("Det lykkedes ikke at opdatere medlemmets e-mailadresse.");
		}
		
		header("Location: /medlemshjaelp/brugerprofil/$action[1]");
		exit();
	}

	# /medlemshjaelp/updateUserDepartment
	else if($action[0] == "updateUserDepartment" && security()->validateCsrfToken()) {

		//Method returns true
		
		if ($model->updateUserDepartment($action)) {
			header("Location: /medlemshjaelp/brugerprofil/$action[1]");
			exit();
		}
		
		// Method returns false
		else {
			header("Location: /medlemshjaelp/brugerprofil/$action[1]");
			exit();
		}
	}
	
	# /medlemshjaelp/updateUserMembership
	else if($action[0] == "updateUserMembership" && security()->validateCsrfToken()) {

		//Method returns true
		if ($model->changeMembership($action)) {
			header("Location: /medlemshjaelp/brugerprofil/$action[1]");
			exit();
		}
		// Method returns false
		else {
			message()->resetMessages();
			message()->addMessage("Medlemskab blev ikke opdateret.", ["type" => "error"]);
			header("Location: /medlemshjaelp/brugerprofil/$action[1]");
			exit();
		}
	}

	# /medlemshjaelp/updateUserMembershipRenewal/#user_id#
	else if($action[0] == "updateUserMembershipRenewal" && count($action) == 2 && security()->validateCsrfToken()) {

		$user_id = $action[1];
		$result = $model->updateUserRenewalOptOut($action);

		if($result === "REACTIVATION REQUIRED") {

			header("Location: /medlemshjaelp/brugerprofil/$user_id/genaktiver");
			exit();

		}
		else {

			header("Location: /medlemshjaelp/brugerprofil/$user_id");
			exit();
		}
	}

	# /medlemshjaelp/reactivateUserMembership/#user_id#
	else if($action[0] == "reactivateUserMembership" && count($action) == 2 && security()->validateCsrfToken()) {

		$user_id = $action[1];
		$order = $MC->switchMembership($action);

		if($order) {

			$SC->sendOrderMails($order);

			// update user renewal preference
			$_POST["membership_renewal"] = 1;
			$result = $model->updateUserRenewalOptOut(["updateUserRenewalOptOut", $user_id]);
			unset($_POST);
			if($result) {

				header("Location: /medlemshjaelp/betaling/".$order["order_no"]);
				exit();
			}
			
		}

		message()->addMessage("Der skete en fejl.", array("type" => "error"));
		$page->page([
			"templates" => "member-help/reactivate_user_membership.php",
			"type" => "member"
		]);
		exit();
	}


	// /profil/updateEmailAgreements/#user_id#
	else if($action[0] == "updateEmailAgreements" && security()->validateCsrfToken()) {

		$result = $model->updateEmailAgreements($action);

		if($result) {

			header("Location: /medlemshjaelp/brugerprofil/".$action[1]."/beskedcenter");
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
	else if($action[0] == "updateUserPassword" && security()->validateCsrfToken()) {

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
	
	# /medlemshjaelp/updateUserUserGroup/#user_id#
	else if($action[0] == "updateUserUserGroup" && security()->validateCsrfToken()) {

		//Method returns true
		if($model->updateUserUserGroup($action)) {
			message()->resetMessages();
			message()->addMessage("Brugergruppen er opdateret");
			header("Location: /medlemshjaelp/brugerprofil/$action[1]");
			exit();
		}
		//Method returns false
		else {
			message()->resetMessages();
			message()->addMessage("Der skete en fejl, så brugergruppen ikke blev opdateret.", array("type" => "error"));
			header("Location: /medlemshjaelp/brugerprofil/$action[1]");
			exit();
		}
	}



	# /medlemshjaelp/butik
	else if($action[0] == "butik") {
		
		# /medlemshjaelp/butik/#user_id#
		if(count($action) == 2) {


			$page->page(array(
				"templates" => "member-help/shop.php",
				"type" => "admin"
			));	
			exit();
		}
		else if(count($action) == 3) {

			# /medlemshjaelp/butik/kurv/#cart_reference#
			if($action[1] == "kurv") {
				
				$page->page(array(
					"templates" => "member-help/cart.php",
					"type" => "admin"
				));	
				exit();
			}
			
			# /medlemshjaelp/butik/addToCart/#cart_reference#
			else if($action[1] == "addToCart" && security()->validateCsrfToken()) {

				$cart_reference = $action[2];
				$cart = $SC->addToCart(["addToCart", $cart_reference]);

				if($cart) {

					header("Location: /medlemshjaelp/butik/".$cart["user_id"]);
					exit();
				}
				// something went wrong
				else {
					message()->addMessage("Noget gik galt.", array("type" => "error"));
				}

			}

		}
		else if(count($action) == 4) {

			# /medlemshjaelp/butik/updateCartItemQuantity/#cart_reference#/#cart_item_id#
			if($action[1] == "updateCartItemQuantity" && security()->validateCsrfToken()) {

				message()->resetMessages();


				$item = $SC->updateCartItemQuantity(["updateCartItemQuantity", $action[2], $action[3]]);

				if($item) {

					if(!message()->hasMessages()) {
						message()->addMessage("Mængde opdateret");
					}
					header("Location: /medlemshjaelp/butik/kurv/".$action[2]);
					exit();
				}
				// something went wrong
				else {
					message()->addMessage("Noget gik galt. Prøv igen.", array("type" => "error"));
				}

			}

			# /medlemshjaelp/butik/deleteFromCart/#cart_reference#/#cart_item_id#
			else if($action[1] == "deleteFromCart" && security()->validateCsrfToken()) {

				$cart = $SC->deleteFromCart(["deleteFromCart", $action[2], $action[3]]);

				if($cart) {

					message()->addMessage("Varen blev slettet fra kurven.");
					header("Location: /medlemshjaelp/butik/kurv/".$cart["cart_reference"]);
					exit();
				}
				// something went wrong
				else {
					message()->addMessage("Noget gik galt. Prøv igen.", array("type" => "error"));
				}

			}
			
			# /medlemshjaelp/butik/newOrderFromCart/#cart_reference#/#cart_id#
			else if($action[1] == "newOrderFromCart") {

				$cart_reference = $action[2];
				$cart_id = $action[3];
				$cart = $SC->getCarts(["cart_id" => $cart_id]);

				$user_id = $cart ? $cart["user_id"] : false;
				$user = $model->getKbhffUser(["user_id" => $user_id]);

				if($user["email"]) {

					// convert cart to order
					$order = $SC->newOrderFromCart(["newOrderFromCart", $cart_id, $cart_reference]);
					if($order) {	
						
						$SC->sendOrderMails($order);
						
						// redirect to payment
						message()->resetMessages();
						header("Location: /medlemshjaelp/betaling/".$order["order_no"]);
						exit();
					}
					
					// error
					else {
						message()->resetMessages();
						message()->addMessage("Det mislykkedes at omdanne indkøbskurven til en ordre.", array("type" => "error"));
						header("Location: /medlemshjaelp/fejl");
						exit();
					}
				}
				else {
					message()->resetMessages();
					header("Location: /medlemshjaelp/angiv-email");
				}

				
			}

			# /medlemshjaelp/butik/cancelOrder/#order_no#/#user_id#
			else if($action[1] == "cancelOrder") {

				$order_no = $action[2];
				$user_id = $action[3];

				$order = $SC->getOrders(["order_no" => $order_no]);

				if($SC->cancelOrder(["cancelOrder", $order["id"], $user_id])) {

					message()->addMessage("Ordren ".$order_no." blev annulleret.");
					header("Location: /medlemshjaelp/butik/".$user_id);
					exit();
				}
				// something went wrong
				else {
					message()->addMessage("Noget gik galt. Prøv igen.", array("type" => "error"));
				}
			}
		}
		
	}

	# /medlemshjaelp/betaling
	else if($action[0] == "betaling") {


		# /medlemshjaelp/betaling/selectPaymentMethodForOrder
		if(count($action) === 2 && $action[1] == "selectPaymentMethodForOrder" && security()->validateCsrfToken()) {

			// register payment method
			$result = $SC->selectPaymentMethodForOrder(array("selectPaymentMethodForOrder"));
			if($result) {

				if($result["status"] === "PROCEED_TO_GATEWAY") {

					$success_url = SITE_URL . "/medlemshjaelp/betaling/".$result["order_no"]."/".$result["payment_gateway"]."/success/{CHECKOUT_SESSION_ID}";
					$cancel_url = SITE_URL . "/medlemshjaelp/betaling/".$result["order_no"];

					$session = $SC->createOrderPaymentSession($result["order_no"], $success_url, $cancel_url);

					// Redirect to gateway
					if($session && $session["url"]) {

						header("Location: ".$session["url"], true, 303);
						exit();

					}
					else if($session["status"] === "ORDER_NOT_FOUND") {

						// Some error from Stripe
						message()->addMessage($result["message"], ["type" => "error"]);
						header("Location: /medlemshjaelp/betaling/".$result["order_no"]);
						exit();

					}
					else {

						// redirect to leave POST state
						message()->addMessage("Vi kunne ikke få en betalingssession fra ".ucfirst($result["payment_gateway"])." – prøv igen.", ["type" => "error"]);
						header("Location: /medlemshjaelp/betaling/".$result["order_no"]);
						exit();

					}

				}
				// else if($result["status"] === "PROCEED_TO_RECEIPT") {
				//
				// 	// redirect to leave POST state
				// 	header("Location: /butik/kvittering/ordre/".$result["order_no"]."/".superNormalize($result["payment_name"]));
				// 	exit();
				//
				// }

			}

			// redirect to leave POST state
			message()->addMessage("Ukendt betalingsmetode – prøv igen.", ["type" => "error"]);
			header("Location: /medlemshjaelp/betaling/".$result["order_no"]);
			exit();

		}


		// process payment session response
		// /medlemshjaelp/betaling/#order_no#/#gateway#/success/#session_id#
		else if(count($action) == 5 && $action[3] == "success") {


			$order_no = $action[1];
			$session_id = $action[4];
			$gateway = $action[2];


			include_once("classes/shop/supershop.class.php");
			$SC = new SuperShop();


			$checkout_session = payment()->retrieveCheckoutSession($session_id);
			if($checkout_session) {

				$result = [];

				$result["status"] = false;
				$result["reference"] = false;

				if(isset($checkout_session["metadata"])) {

					// Look for checkout reference (payment for order)
					if(isset($checkout_session["metadata"]["order_no"])) {

						$result["reference"] = "order";
						$result["order_no"] = $checkout_session["metadata"]["order_no"];


						// Is setup intent complete
						if(isset($checkout_session["setup_intent"]) && $checkout_session["setup_intent"]) {


							$setup_intent = payment()->retrieveSetupIntent($checkout_session["setup_intent"]);
							// debug([$setup_intent]);

							if($setup_intent && isset($setup_intent["payment_method"]) && $setup_intent["payment_method"]) {

								$payment_method = $setup_intent["payment_method"];
								if($payment_method) {

									$result["order"] =  $SC->getOrders(["order_no" => $result["order_no"]]);
									$result["user_id"] = $result["order"]["user_id"];

									$result["return_url"] = SITE_URL."/medlemshjaelp/betaling/stripe/register-paid-intent";
									// $result["return_url"] = str_replace("{GATEWAY}", $gateway, SITE_PAYMENT_REGISTER_PAID_INTENT);
									$intent_result = payment()->requestPaymentIntentForOrder(
										$result["order"], 
										$setup_intent["payment_method"], 
										$result["return_url"]
									);

									if($intent_result) {

										$result = array_merge($result, $intent_result);

									}

								}

							}
							else {
								$result["status"] === "CARD_NOT_FOUND";
							}

						}

					}

				}
				else {
					$result["status"] === "REFERENCE_NOT_FOUND";
				}


			}
			else {
				$result["status"] === "STRIPE_ERROR";
			}



			if($result["status"] === "PAYMENT_CAPTURED") {

				// redirect to leave POST state
				header("Location: ".$result["return_url"]."/?payment_intent=".$result["payment_intent_id"]);
				exit();

			}
			else if($result["status"] === "PAYMENT_READY") {

				// redirect to leave POST state
				header("Location: ".$result["return_url"]."/?payment_intent=".$result["payment_intent_id"]);
				exit();

			}
			else if($result["status"] === "ACTION_REQUIRED") {

				// redirect to leave POST state
				header("Location: ".$result["action"]);
				exit();
	
			}

			else if($result["status"] === "STRIPE_ERROR" || $result["status"] === "CARD_NOT_FOUND" || $result["status"] === "REFERENCE_NOT_FOUND") {

				if($result["status"] === "STRIPE_ERROR" || $result["status"] === "CARD_NOT_FOUND")	{
					$result["message"] = "Der skete en fejl i behandlingen af betalingen.";
				}
				else if($result["status"] === "REFERENCE_NOT_FOUND")	{
					$result["message"] = "Kurven eller ordren blev ikke fundet. Prøv igen.";
				}

				// Some error from Stripe
				message()->addMessage($result["message"], ["type" => "error"]);
				header("Location: /medlemshjaelp/betaling/".$order_no);
				exit();

			}

			message()->addMessage("Der skete en ukendt fejl. Prøv igen.", ["type" => "error"]);
			header("Location: /butik/betal");
			exit();

		}

		# /medlemshjaelp/betaling/#order_no#
		else if(count($action) === 2) {
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





		// # /medlemshjaelp/betaling/stripe/ordre/#order_no#/process
		// else if(count($action) === 5 && $action[4] == "process" && security()->validateCsrfToken()) {
		//
		// 	$gateway = $action[1];
		// 	$order_no = $action[3];
		//
		//
		// 	$payment_method_result = $SC->processCardForOrder($action);
		// 	if($payment_method_result) {
		//
		// 		if($payment_method_result["status"] === "success") {
		//
		// 			$return_url = SITE_URL."/medlemshjaelp/betaling/stripe/register-paid-intent";
		// 			$result = payment()->requestPaymentIntentForOrder(
		// 				$payment_method_result["order"],
		// 				$payment_method_result["card"]["id"],
		// 				$return_url
		// 			);
		// 			if($result) {
		//
		// 				if($result["status"] === "PAYMENT_CAPTURED") {
		//
		// 					// redirect to leave POST state
		// 					header("Location: $return_url/?payment_intent=".$result["payment_intent_id"]);
		// 					exit();
		//
		// 				}
		// 				else if($result["status"] === "ACTION_REQUIRED") {
		//
		// 					// redirect to leave POST state
		// 					header("Location: ".$result["action"]);
		// 					exit();
		//
		// 				}
		//
		// 				else if($result["status"] === "CARD_ERROR") {
		//
		// 					// Janitor Validation failed
		// 					message()->addMessage($result["message"], ["type" => "error"]);
		// 					// redirect to leave POST state
		// 					header("Location: /medlemshjaelp/betalingsgateway/".$gateway."/ordre/".$order_no);
		// 					exit();
		//
		// 				}
		//
		// 			}
		//
		// 		}
		// 		else if($payment_method_result["status"] === "STRIPE_ERROR" || $payment_method_result["status"] === "ORDER_NOT_FOUND") {
		//
		// 			if($payment_method_result["status"] === "STRIPE_ERROR")	{
		// 				$payment_method_result["message"] = "Der skete en fejl i behandlingen af din betaling.";
		//
		// 			}
		// 			else if($payment_method_result["status"] === "ORDER_NOT_FOUND")	{
		// 				$payment_method_result["message"] = "Ordren blev ikke fundet.";
		// 			}
		//
		// 			message()->addMessage($payment_method_result["message"], ["type" => "error"]);
		// 			// redirect to leave POST state
		// 			header("Location: /medlemshjaelp/betaling/$order_no");
		// 			exit();
		//
		// 		}
		// 		else if($payment_method_result["status"] === "CARD_ERROR") {
		//
		// 			switch($payment_method_result["code"]) {
		//
		// 				case "incorrect_number"         : $message = "Forkert kortnummer."; break;
		// 				case "invalid_number"           : $message = "Kortnummeret er ikke et gyldigt kreditkortnummer."; break;
		// 				case "invalid_expiry_month"     : $message = "Ugyldig udløbsdato."; break;
		// 				case "invalid_expiry_year"      : $message = "Ugyldigt udløbsår."; break;
		// 				case "invalid_cvc"              : $message = "Ugyldig sikkerhedskode."; break;
		// 				case "expired_card"             : $message = "Kortet er udløbet."; break;
		// 				case "incorrect_cvc"            : $message = "Forkert sikkerhedskode."; break;
		// 				case "incorrect_zip"            : $message = "Kortets postnummer kunne ikke bekræftes."; break;
		// 				case "card_declined"            : $message = "Kortet blev afvist."; break;
		// 			}
		// 			if($payment_method_result["decline_code"]) {
		// 				switch($payment_method_result["decline_code"]) {
		//
		// 					case "insufficient_funds"         : $message = "Kortet blev afvist. Der er ikke penge nok på kontoen."; break;
		// 				}
		// 			}
		//
		//
		// 			message()->addMessage($message, ["type" => "error"]);
		// 			// redirect to leave POST state
		// 			header("Location: /medlemshjaelp/betaling/$order_no");
		// 			exit();
		// 		}
		//
		// 	}
		//
		//
		// 	// Janitor Validation failed
		// 	message()->addMessage($payment_method_result["message"], ["type" => "error"]);
		// 	// redirect to leave POST state
		// 	header("Location: /medlemshjaelp/betaling/$order_no");
		// 	exit();
		//
		// }



		# /medlemshjaelp/betaling/stripe/register-paid-intent
		else if(count($action) == 3 && $action[2] == "register-paid-intent") {

			$payment_intent_id = getVar("payment_intent");

			$id_result = payment()->identifyPaymentIntent($payment_intent_id);
			if($id_result && $id_result["status"] === "success") {

				// Single order
				if($id_result["order_no"]) {

					$order = $SC->getOrders(["order_no" => $id_result["order_no"]]);
					if($order) {

						// Register intent for order (and subscription)
						$intent_registration_result = payment()->updatePaymentIntent($payment_intent_id, $order);
						if($intent_registration_result["status"] === "success") {

							// Register payment for order (if paid)
							if($id_result["payment_status"] === "succeeded") {

								$payment_registration_result = payment()->registerPayment($order, $id_result["payment_intent"]);

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
					$id_result["message"] = "Tredjepartsautentificeringen slog fejl. Prøv igen eller brug en andet kort eller betalingsmetode.";
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

	# /medlemshjaelp/ret-bestilling
	else if($action[0] == "ret-bestilling" && count($action) == 3) {
		$page->page(array(
			"templates" => "member-help/update_order_item_department_pickupdate.php",
		));
		exit();
	}

	# /medlemshjaelp/setOrderItemDepartmentPickupdate/#order_item_id#
	else if($action[0] == "setOrderItemDepartmentPickupdate" && security()->validateCsrfToken()) {

		$order_item_id = $action[1];
		$order_item = $SC->getOrderItems(["order_item_id" => $order_item_id]);
		$user_id = $order_item ? $order_item["user_id"] : false;


		if($SC->setOrderItemDepartmentPickupdate(["setOrderItemDepartmentPickupdate", $order_item_id])) {

			header("Location: /medlemshjaelp/brugerprofil/$user_id");
			exit();
		}
		// something went wrong
		else {
			message()->addMessage("Noget gik galt.", array("type" => "error"));
			header("Location: /medlemshjaelp/ret-bestilling/$order_item_id/$user_id");
			exit();
		}

	}

	# /medlemshjaelp/betalinger/#user_id#
	else if($action[0] == "betalinger" && count($action) == 2) {

		$page->page(array(
			"templates" => "member-help/payments.php",
			"type" => "admin"
		));
		exit();
	}
	
	# /medlemshjaelp/registerPayment/#order_no#
	else if(count($action) === 2 && $action[0] == "registerPayment") {
		
		// create payment id
		$payment_id = $SC->registerPayment(["registerPayment"]);
		if($payment_id) {		
			// redirect to receipt
			message()->resetMessages();
			header("Location: /medlemshjaelp/betaling/".$action[1]."/".$payment_id."/kvittering");
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