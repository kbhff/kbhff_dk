<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$action = $page->actions();
$model = new Shop();

include_once("classes/shop/pickupdate.class.php");
$PC = new Pickupdate();

include_once("classes/system/department.class.php");
$DC = new Department();


$page->bodyClass("shop");
$page->pageTitle("Butik");

$UC = new User();
$user = $UC->getKbhffUser();
// current user is active member
if($user && $user["membership"] && $user["membership"]["subscription_id"]) {

	// active members should not have signupfees in their cart
	$model->deleteItemtypeFromCart("signupfee");
}

if(session()->value("user_id") > 1 && !$UC->hasEmailAddress()) {

	$page->page(array(
		"templates" => "profile/update_email.php",
		"type" => "login",
		"page_title" => "Angiv e-mailadresse"
	));
	exit();
}

if($action) {
	
	# /butik/kurv
	if($action[0] == "kurv") {

		$page->page(array(
			"templates" => "shop/cart.php"
		));
		exit();
		
	}

	# /butik/addToCart
	else if($action[0] == "addToCart" && $page->validateCsrfToken()) {

		$cart = $model->addToCart(array("addToCart"));

		// successful creation
		if($cart) {

			message()->addMessage("Item added");
			header("Location: /butik");
			exit();
		}
		// something went wrong
		else {
			message()->addMessage("Noget gik galt.", array("type" => "error"));
		}

	}

	# /butik/cancelOrder/#order_id#
	else if(count($action) == 2 && $action[0] == "cancelOrder" && $page->validateCsrfToken()) {

		$result = $model->cancelOrder(["cancelOrder", $action[1]]);

		// successful creation
		if($result) {

			message()->addMessage("Order cancelled");
			header("Location: /butik");
			exit();
		}
		// something went wrong
		else {
			message()->addMessage("Noget gik galt.", array("type" => "error"));
		}

	}

	# /butik/updateCartItemQuantity/#cart_reference#/#cart_item_id#
	else if($action[0] == "updateCartItemQuantity" && $page->validateCsrfToken()) {

		message()->resetMessages();


		// create new user
		$item = $model->updateCartItemQuantity($action);

		// successful creation
		if($item) {

			if(!message()->hasMessages()) {
				message()->addMessage("Mængde opdateret");
			}
			header("Location: /butik/kurv");
			exit();
		}
		// something went wrong
		else {
			message()->addMessage("Noget gik galt. Prøv igen.", array("type" => "error"));
		}

	}

	# /butik/deleteFromCart/#cart_reference#/#cart_item_id#
	else if($action[0] == "deleteFromCart" && $page->validateCsrfToken()) {

		// create new user
		$cart = $model->deleteFromCart($action);

		// successful creation
		if($cart) {

			message()->addMessage("Varen blev slettet fra kurven.");
			header("Location: /butik/kurv");
			exit();
		}
		// something went wrong
		else {
			message()->addMessage("Noget gik galt. Prøv igen.", array("type" => "error"));
		}

	}

	# /butik/betal [POST]
	else if($action[0] == "betal" && $_SERVER['REQUEST_METHOD'] === "POST") {

		// redirect to leave POST state
		header("Location: /butik/betal");
		exit();
	}

	# /butik/betal
	else if($action[0] == "betal") {

		$page->page(array(
			"templates" => "shop/checkout.php"
		));
		exit();
		
	}

	# /butik/kvittering
	else if($action[0] == "kvittering") {

		# /butik/kvittering/fejl
		if(count($action) == 2 && $action[1] == "fejl") {
			
			$page->page(array(
				"templates" => "shop/receipt/error.php"
			));
			exit();
			
		}
		
		# /butik/kvittering/ordrer
		else if(count($action) >= 3 && $action[1] === "ordrer") {
			
			$page->page(array(
				"templates" => "shop/receipt/orders.php"
			));
			exit();
			
		}
		# /butik/kvittering/ny-ordre
		else if(count($action) >= 3 && $action[1] === "ny-ordre") {
			
			$page->page(array(
				"templates" => "shop/receipt/new-order.php"
			));
			exit();
			
		}
		# /butik/kvittering/ordre
		else if(count($action) >= 3 && $action[1] === "ordre") {

			$page->page(array(
				"templates" => "shop/receipt/order.php"
			));
			exit();

		}
		// all other variations (than error) are handled in receipt template
		else {

			$page->page(array(
				"templates" => "shop/receipt/index.php"
			));
			exit();

		}

		
		

	}

	# /butik/betalingsgateway/#gateway#/kurv/#cart_reference#/[process]
	# /butik/betalingsgateway/#gateway#/ordre/#order_no#/[process]
	# /butik/betalingsgateway/#gateway#/ordrer/#order_ids#/[process]
	else if($action[0] == "betalingsgateway") {

		// specific gateway payment window for cart
		if(count($action) == 4 && $action[2] === "kurv") {

			$page->page(array(
				"type" => "payment",
				"templates" => "shop/gateway/".$action[1]."-cart.php"
			));
			exit();

		}

		// specific gateway payment window for order
		else if(count($action) == 4 && $action[2] === "ordre") {

			$page->page(array(
				"type" => "payment",
				"templates" => "shop/gateway/".$action[1]."-order.php"
			));
			exit();

		}

		// specific gateway payment window for orders
		else if(count($action) == 4 && $action[2] === "ordrer") {

			$page->page(array(
				"type" => "payment",
				"templates" => "shop/gateway/".$action[1]."-orders.php"
			));
			exit();

		}

		// process payment method for cart
		else if(count($action) == 5 && $action[2] === "kurv" && $action[4] == "process" && $page->validateCsrfToken()) {

			$gateway = $action[1];
			$cart_reference = $action[3];

			$payment_method_result = $model->processCardForCart($action);
			if($payment_method_result) {

				if($payment_method_result["status"] === "success") {

					$return_url = str_replace("{GATEWAY}", $gateway, SITE_PAYMENT_REGISTER_INTENT);
					$result = payments()->requestPaymentIntentForCart($payment_method_result["cart"], $payment_method_result["card"]["id"], $return_url);
					if($result) {

						if($result["status"] === "PAYMENT_READY") {

							// redirect to leave POST state
							header("Location: $return_url/?payment_intent=".$result["payment_intent_id"]);
							exit();

						}
						else if($result["status"] === "ACTION_REQUIRED") {

							// redirect to leave POST state
							header("Location: ".$result["action"]);
							exit();
					
						}

						else if($result["status"] === "error") {

							// Some error from Stripe
							message()->addMessage($result["message"], ["type" => "error"]);
							// redirect to leave POST state
							header("Location: /butik/betalingsgateway/".$gateway."/kurv/".$cart_reference);
							exit();

						}

					}

				}
				else if($payment_method_result["status"] === "STRIPE_ERROR" || $payment_method_result["status"] === "CART_NOT_FOUND") {

					if($payment_method_result["status"] === "STRIPE_ERROR")	{
						$payment_method_result["message"] = "Der skete en fejl i behandlingen af din betaling.";
					}
					else if($payment_method_result["status"] === "CART_NOT_FOUND")	{
						$payment_method_result["message"] = "Kurven blev ikke fundet.";
					}

					message()->addMessage($payment_method_result["message"], ["type" => "error"]);
					// redirect to leave POST state
					header("Location: /butik/betaling");
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
						case "resource_missing"         : $message = "Ugyldigt ID."; break;
					}

					if($payment_method_result["decline_code"]) {
						switch($payment_method_result["decline_code"]) {

							case "insufficient_funds"         : $message = "Kortet blev afvist. Der er ikke penge nok på kontoen."; break;
						}
					}


					message()->addMessage($message, ["type" => "error"]);
					// redirect to leave POST state
					header("Location: /butik/betalingsgateway/".$gateway."/kurv/".$cart_reference);
					exit();
				}

			}

			// Janitor Validation failed
			// redirect to leave POST state
			header("Location: /butik/betalingsgateway/".$gateway."/kurv/".$cart_reference);
			exit();

		}

		// process payment method for order
		else if(count($action) == 5 && $action[2] === "ordre" && $action[4] == "process" && $page->validateCsrfToken()) {

			$gateway = $action[1];
			$order_no = $action[3];


			$payment_method_result = $model->processCardForOrder($action);
			if($payment_method_result) {

				if($payment_method_result["status"] === "success") {

					$return_url = str_replace("{GATEWAY}", $gateway, SITE_PAYMENT_REGISTER_PAID_INTENT);
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
							header("Location: /butik/betalingsgateway/".$gateway."/ordre/".$order_no);
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
					header("Location: /butik/betaling/$order_no");
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
						case "resource_missing"         : $message = "Ugyldigt ID."; break;
					}
					if($payment_method_result["decline_code"]) {
						switch($payment_method_result["decline_code"]) {

							case "insufficient_funds"         : $message = "Kortet blev afvist. Der er ikke penge nok på kontoen."; break;
						}
					}


					message()->addMessage($message, ["type" => "error"]);
					// redirect to leave POST state
					header("Location: /butik/betaling/$order_no");
					exit();
				}

			}


			// Janitor Validation failed
			message()->addMessage($payment_method_result["message"], ["type" => "error"]);
			// redirect to leave POST state
			header("Location: /butik/betaling/$order_no");
			exit();				

		}

		// process payment method for orders
		else if(count($action) == 5 && $action[2] === "ordrer" && $action[4] == "process" && $page->validateCsrfToken()) {

			$gateway = $action[1];
			$order_ids = $action[3];


			$payment_method_result = $model->processCardForOrders($action);
			if($payment_method_result) {

				if($payment_method_result["status"] === "success") {

					$return_url = str_replace("{GATEWAY}", $payment_method_result["payment_gateway"], SITE_PAYMENT_REGISTER_PAID_INTENT);
					$result = payments()->requestPaymentIntentForOrders(
						$payment_method_result["orders"],
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
							header("Location: /butik/betalingsgateway/".$gateway."/ordrer/".$order_ids);
							exit();

						}

					}

				}
				else if($payment_method_result["status"] === "STRIPE_ERROR" || $payment_method_result["status"] === "ORDER_NOT_FOUND") {

					if($payment_method_result["status"] === "STRIPE_ERROR")	{
						$payment_method_result["message"] = "Der skete en fejl i behandlingen af din betaling.";
					}
					else if($payment_method_result["status"] === "ORDER_NOT_FOUND")	{
						$payment_method_result["message"] = "Ordrerne blev ikke fundet.";
					}

					message()->addMessage($payment_method_result["message"], ["type" => "error"]);
					// redirect to leave POST state
					header("Location: /butik/betalinger");
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
						case "resource_missing"         : $message = "Ugyldigt ID."; break;
					}
					if($payment_method_result["decline_code"]) {
						switch($payment_method_result["decline_code"]) {

							case "insufficient_funds"         : $message = "Kortet blev afvist. Der er ikke penge nok på kontoen."; break;
						}
					}


					message()->addMessage($message, ["type" => "error"]);
					// redirect to leave POST state
					header("Location: /butik/betalinger");
					exit();
				}

			}


			// Janitor Validation failed
			message()->addMessage($payment_method_result["message"], ["type" => "error"]);
			// redirect to leave POST state
			header("Location: /butik/betalinger");
			exit();				

		}


		// Register intent
		else if(count($action) == 3 && $action[2] == "register-intent") {

			$payment_intent_id = getVar("payment_intent");

			$id_result = payments()->identifyPaymentIntent($payment_intent_id);

			if($id_result && $id_result["status"] === "success") {

				if($id_result["order_no"]) {
					$order = $model->getOrders(["order_no" => $id_result["order_no"]]);

				}
				else if($id_result["cart_reference"]) {

					$order = $model->getOrders(["cart_reference" => $id_result["cart_reference"]]) ?: $model->newOrderFromCart(["newOrderFromCart", $id_result["cart_reference"]]);
					// Clear messages
					message()->resetMessages();
					
				}
				else {
					$order = false;
				}

				if($order) {

					// get payment intent
					$registration_result = payments()->registerPaymentIntent($payment_intent_id, $order);
					if($registration_result["status"] === "success") {

						$total_order_price = $model->getTotalOrderPrice($order["id"]);
						payments()->capturePayment($payment_intent_id, $total_order_price["price"]);

						// redirect to leave POST state
						header("Location: /butik/kvittering/ny-ordre/".$order["order_no"]."/".superNormalize($id_result["gateway"]));
						exit();
					}
					else if($order["payment_status"] == 2) {
						
						// redirect to leave POST state
						header("Location: /butik/kvittering/ny-ordre/".$order["order_no"]."/".superNormalize($id_result["gateway"]));
						exit();
					}

				}

			}
			else if($id_result && $id_result["status"] === "error") {

				if($id_result["code"] === "payment_intent_authentication_failure") {
					$id_result["message"] = "Tredjepartsautentificeringen slog fejl. Prøv igen eller brug et andet kort.";
				}

				message()->addMessage($id_result["message"], ["type" => "error"]);
				// redirect to leave POST state
				if($id_result["cart_reference"]) {
					header("Location: /butik/betalingsgateway/".$id_result["gateway"]."/kurv/".$id_result["cart_reference"]);
				}
				else if($id_result["order_no"]) {
					header("Location: /butik/betalingsgateway/".$id_result["gateway"]."/ordre/".$id_result["order_no"]);
				}
				
				exit();

			}

			// Fatal error
			message()->addMessage("Det mislykkedes at behandle din betalingsanmodning. Prøv igen eller <a href=\"mailto:it@kbhff.dk?subject=Payment%20error&body=Payment%20Intent:%20$payment_intent_id\">kontakt os</a>, så vi kan løse problemet.", ["type" => "error"]);
			// redirect to leave POST state
			header("Location: /butik/kvittering/fejl");
			exit();

		}


		// Register paid intent – for orders only
		else if(count($action) == 3 && $action[2] == "register-paid-intent") {

			$payment_intent_id = getVar("payment_intent");

			$id_result = payments()->identifyPaymentIntent($payment_intent_id);
			if($id_result && $id_result["status"] === "success") {

				// Single order
				if($id_result["order_no"]) {

					$order = $model->getOrders(["order_no" => $id_result["order_no"]]);
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
									header("Location: /butik/kvittering/ordre/".$order["order_no"]."/".superNormalize($id_result["gateway"]));
									exit();

								}

							}

						}

					}

				}
				// Multiple orders
				else if($id_result["order_nos"]) {

					// Register payment for order (if paid)
					if($id_result["payment_status"] === "succeeded") {

						$orders = [];
						$order_nos = explode(",", $id_result["order_nos"]);
						foreach($order_nos as $order_no) {

							$order = $model->getOrders(["order_no" => $order_no]);
							if($order) {
								$orders[] = $order;
							}

						}

						$payments_registration_result = payments()->registerPayments($orders, $id_result["payment_intent"]);


						// Clear messages
						message()->resetMessages();

						// Successful registration of payment
						if($payments_registration_result && $payments_registration_result["status"] === "REGISTERED") {

							// redirect to leave POST state
							header("Location: /butik/kvittering/ordrer/".$id_result["order_nos"]."/".superNormalize($id_result["gateway"]));
							exit();

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
				if($id_result["order_no"]) {
					header("Location: /butik/betalingsgateway/".$id_result["gateway"]."/ordre/".$id_result["order_no"]);
				}
				else {
					header("Location: /butik/betalinger");
				}
				exit();

			}

			// Fatal error
			message()->addMessage("Det mislykkedes at behandle din betalingsanmodning. Prøv igen eller <a href=\"mailto:it@kbhff.dk?subject=Payment%20error&body=Payment%20Intent:%20$payment_intent_id\">kontakt os</a>, så vi kan løse problemet.", ["type" => "error"]);
			// redirect to leave POST state
			header("Location: /butik/kvittering/fejl");
			exit();

		}

	}

	
	# /butik/confirmCartAndSelectPaymentMethod
	else if($action[0] == "confirmCartAndSelectPaymentMethod" && count($action) == 1) {

		// register payment method
		$result = $model->selectPaymentMethodForCart(["selectPaymentMethodForCart"]);
		if($result) {

			if($result["status"] === "PROCEED_TO_GATEWAY") {

				// redirect to leave POST state
				header("Location: /butik/betalingsgateway/".$result["payment_gateway"]."/kurv/".$result["cart_reference"]);
				exit();

			}
			else if($result["status"] === "PROCEED_TO_RECEIPT") {

				// redirect to leave POST state
				header("Location: /butik/kvittering/ordre/".$result["order_no"]."/".superNormalize($result["payment_name"]));
				exit();

			}
			else if($result["status"] === "ORDER_FAILED") {

				// redirect to leave POST state
				message()->addMessage("Ordren kunne ikke oprettes – prøv igen.", ["type" => "error"]);
				header("Location: /butik/betaling");
				exit();

			}

		}

		// redirect to leave POST state
		message()->addMessage("Ukendt betalingsmetode – prøv igen.", ["type" => "error"]);
		header("Location: /butik/betaling");
		exit();

	}

	# /butik/confirmCartAndSelectUserPaymentMethod
 	else if($action[0] == "confirmCartAndSelectUserPaymentMethod" && count($action) == 1) {

		// register payment method
		$payment_method_result = $model->selectUserPaymentMethodForCart(["selectUserPaymentMethodForCart"]);
		if($payment_method_result && $payment_method_result["status"] === "PROCEED_TO_INTENT") {

			$return_url = str_replace("{GATEWAY}", $payment_method_result["payment_gateway"], SITE_PAYMENT_REGISTER_INTENT);
			$result = payments()->requestPaymentIntentForCart(
				$payment_method_result["cart"], 
				$payment_method_result["gateway_payment_method_id"], 
				$return_url
			);
			if($result) {

				if($result["status"] === "PAYMENT_READY") {

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
					header("Location: /butik/betalingsgateway/".$gateway."/kurv/".$cart_reference);
					exit();

				}

			}
		}

		else if($payment_method_result["status"] === "PROCEED_TO_RECEIPT") {

			// redirect to leave POST state
			header("Location: /butik/kvittering/ordre/".$result["order_no"]."/".superNormalize($result["payment_name"]));
			exit();

		}
		else if($payment_method_result["status"] === "ORDER_FAILED") {

			// redirect to leave POST state
			message()->addMessage("Ordren kunne ikke oprettes – prøv igen.", ["type" => "error"]);
			header("Location: /butik/betaling");
			exit();

		}

		// redirect to leave POST state
		message()->addMessage("Ukendt betalingsmetode – prøv igen.", ["type" => "error"]);
		header("Location: /butik/betaling");
		exit();

	}


	# /butik/selectPaymentMethodForOrder
	else if($action[0] == "selectPaymentMethodForOrder" && $page->validateCsrfToken()) {

		// register payment method
		$result = $model->selectPaymentMethodForOrder(array("selectPaymentMethodForOrder"));
		if($result["status"] === "PROCEED_TO_GATEWAY") {

			// redirect to leave POST state
			header("Location: /butik/betalingsgateway/".$result["payment_gateway"]."/ordre/".$result["order_no"]);
			exit();

		}
		else if($result["status"] === "PROCEED_TO_RECEIPT") {

			// redirect to leave POST state
			header("Location: /butik/kvittering/ordre/".$result["order_no"]."/".superNormalize($result["payment_name"]));
			exit();

		}

		// redirect to leave POST state
		message()->addMessage("Ukendt betalingsmetode – prøv igen.", ["type" => "error"]);
		header("Location: /butik/betalinger");
		exit();

	}

	# /butik/selectUserPaymentMethodForOrder
	else if($action[0] == "selectUserPaymentMethodForOrder" && count($action) == 1) {

		// register payment method
		$payment_method_result = $model->selectUserPaymentMethodForOrder(["selectUserPaymentMethodForOrder"]);
		if($payment_method_result && $payment_method_result["status"] === "PROCEED_TO_INTENT") {

			$return_url = str_replace("{GATEWAY}", $payment_method_result["payment_gateway"], SITE_PAYMENT_REGISTER_PAID_INTENT);
			$result = payments()->requestPaymentIntentForOrder(
				$payment_method_result["order"], 
				$payment_method_result["gateway_payment_method_id"], 
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

			}

			// redirect to leave POST state
			message()->addMessage("Ukendt betalingsmetode – prøv igen.", ["type" => "error"]);
			header("Location: /butik/betalinger");
			exit();
		}

		else if($payment_method_result["status"] === "PROCEED_TO_RECEIPT") {

			// redirect to leave POST state
			header("Location: /butik/kvittering/ordre/".$payment_method_result["order_no"]."/".superNormalize($payment_method_result["payment_name"]));
			exit();

		}

		// redirect to leave POST state
		message()->addMessage("Ukendt betalingsmetode – prøv igen.", ["type" => "error"]);
		header("Location: /butik/betalinger");
		exit();

	}

	# /butik/selectPaymentMethodForOrders
	else if($action[0] == "selectPaymentMethodForOrders" && $page->validateCsrfToken()) {

		// register payment method
		$result = $model->selectPaymentMethodForOrders(array("selectPaymentMethodForOrders"));
		if($result["status"] === "PROCEED_TO_GATEWAY") {

			// redirect to leave POST state
			header("Location: /butik/betalingsgateway/".$result["payment_gateway"]."/ordrer/".$result["order_ids"]);
			exit();

		}
		else if($result["status"] === "PROCEED_TO_RECEIPT") {

			// redirect to leave POST state
			header("Location: /butik/kvittering/ordrer/".$result["order_nos"]."/".superNormalize($result["payment_name"]));
			exit();

		}

		// redirect to leave POST state
		message()->addMessage("Ukendt betalingsmetode – prøv igen.", ["type" => "error"]);
		header("Location: /butik/betalinger");
		exit();

	}

	# /butik/selectUserPaymentMethodForOrders
	else if($action[0] == "selectUserPaymentMethodForOrders" && count($action) == 1) {

		// register payment method
		$payment_method_result = $model->selectUserPaymentMethodForOrders(["selectUserPaymentMethodForOrders"]);
		if($payment_method_result && $payment_method_result["status"] === "PROCEED_TO_INTENT") {

			$return_url = str_replace("{GATEWAY}", $payment_method_result["payment_gateway"], SITE_PAYMENT_REGISTER_PAID_INTENT);
			$result = payments()->requestPaymentIntentForOrders(
				$payment_method_result["orders"], 
				$payment_method_result["gateway_payment_method_id"], 
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

			}

			// redirect to leave POST state
			message()->addMessage("Ukendt betalingsmetode – prøv igen.", ["type" => "error"]);
			header("Location: /butik/betalinger");
			exit();
		}

		else if($payment_method_result["status"] === "PROCEED_TO_RECEIPT") {

			// redirect to leave POST state
			header("Location: /butik/kvittering/ordrer/".$payment_method_result["order_nos"]."/".superNormalize($payment_method_result["payment_name"]));
			exit();

		}

		// redirect to leave POST state
		message()->addMessage("Ukendt betalingsmetode – prøv igen.", ["type" => "error"]);
		header("Location: /butik/betalinger");
		exit();

	}

	# /butik/confirmOrder/#cart_reference#
	else if($action[0] == "confirmOrder" && count($action) == 2) {

		$cart_reference = $action[1];
		$cart = $model->getCarts(["cart_reference" => $cart_reference]);
		if($cart) {

			$order = $model->newOrderFromCart(["newOrderFromCart", $cart["cart_reference"]]);
			if($order) {

				// Clear messages
				message()->resetMessages();

				$total_order_price = $model->getTotalOrderPrice($order["id"]);
				if($total_order_price["price"] > 0) {

					// redirect to leave POST state
					header("Location: /butik/betalingsmuligheder/".$order["order_no"]);
					exit();

				}
				// 0-order, no payment required
				else {

					// redirect to leave POST state
					header("Location: /butik/kvittering/ny-ordre/".$order["order_no"]);
					exit();

				}

			}

		}
		else {

			message()->addMessage("Ordren kunne ikke behandles – prøv igen.", ["type" => "error"]);

			// redirect to leave POST state
			header("Location: /butik/kurv");
			exit();

		}
		exit();
	}


	# /butik/betalingsmuligheder/#order_no#
	else if($action[0] == "betalingsmuligheder" && count($action) == 2) {

		$page->page(array(
			"templates" => "shop/payment-options.php"
		));
		exit();
	}

	# /butik/betaling/#order_no#
	else if($action[0] == "betaling" && count($action) == 2) {

		$page->page(array(
			"templates" => "shop/payment.php"
		));
		exit();
	}

	# /butik/betalinger (all open payments)
	else if($action[0] == "betalinger" && count($action) == 1) {

		$page->page(array(
			"templates" => "shop/payments.php"
		));
		exit();
	}
	
	# /butik/profil
	else if($action[0] == "profil") {

		$page->page(array(
			"templates" => "shop/profile.php"
		));
		exit();
	}

	# /butik/updateProfile
	else if($action[0] == "updateProfile" && $page->validateCsrfToken()) {

		// create new user
		$UC = new User();
		$user = $UC->update(array("update"));

		// redirect to leave POST state
		header("Location: /butik/betal");
		exit();
	}

	# /butik/selectAddress
	else if($action[0] == "selectAddress" && $page->validateCsrfToken()) {

		if($model->updateCart(array("updateCart"))) {
			// redirect to leave POST state
			header("Location: /butik/betal");
			exit();
		}
		else {
			$page->page(array(
				"templates" => "shop/address.php"
			));
			exit();
		}

	}

	# /butik/addAddress
	else if($action[0] == "addAddress" && count($action) == 2 && $page->validateCsrfToken()) {

		$UC = new User();
		$address = $UC->addAddress(array("addAddress"));
		if($address) {
			$_POST[$action[1]."_address_id"] = $address["id"];

			if($model->updateCart(array("updateCart"))) {

				// redirect to leave POST state
				header("Location: /butik/betal");
				exit();
			}
		}

		$page->page(array(
			"templates" => "shop/address.php"
		));
		exit();
	}

	# /butik/adresse
	else if($action[0] == "adresse" && count($action) == 2) {

		$page->page(array(
			"templates" => "shop/address.php"
		));
		exit();
	}


	# /butik/ret-bestilling/#order_item_id#
	else if($action[0] == "ret-bestilling" && count($action) == 2) {
		$page->page(array(
			"templates" => "shop/update_order_item_department_pickupdate.php",
		));
		exit();
	}

	# /butik/setOrderItemDepartmentPickupdate/#order_item_id#
	else if($action[0] == "setOrderItemDepartmentPickupdate" && $page->validateCsrfToken()) {

		if($model->setOrderItemDepartmentPickupdate($action)) {

			header("Location: /profil");
			exit();
		}
		// something went wrong
		else {
			message()->addMessage("Noget gik galt.", array("type" => "error"));
			header("Location: /butik/ret-bestilling/".$action[1]);
			exit();
		}

	}

	// Class interface
	else if(preg_match("/^removePastPickupdateCartItems$/", $action[0])) {

		include_once("classes/shop/supershop.class.php");
		$SC = new SuperShop();

		$output = new Output();
		$output->screen($SC->removePastPickupdateCartItems($action));
		exit();

	}

}

// go to shop index directly
# /butik
$page->page(array(
	"templates" => "shop/index.php"
));

?>
