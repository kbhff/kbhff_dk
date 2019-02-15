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

// page info
$page->bodyClass("member_help");
$page->pageTitle("Medlemshjælp");

if($action) {
	
	if($action[0] == "soeg") {
		$users = $model->searchUsers("searchUsers");
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
			$SC = new SuperShop();
			
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
	
	// /medlemshjaelp/brugerprofil/#user_id#
	// else if($action[0] == "brugerprofil" && $action[1] == $user["user_id"]) {
	else if($action[0] == "brugerprofil") {
		
		$page->page(array(
			"templates" => "member-help/user-profile.php"
		));
		exit();
		
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
		else if(count($action) === 3 && $action[2] == "kvittering") {
			$page->page(array(
				"templates" => "member-help/receipt/index.php"
			));
			exit();
		}

		
	}
	
	// /medlemshjaelp/registerPayment/#order_no#
	else if(count($action) === 2 && $action[0] == "registerPayment") {
		include_once("classes/shop/supershop.class.php");
		$SC = new SuperShop();

		// create payment id
		$payment_id = $SC->registerPayment(["registerPayment"]);
		if($payment_id) {		
			
			// redirect to receipt
			message()->resetMessages();
			header("Location: /medlemshjaelp/betaling/".$payment_id."/kvittering");
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