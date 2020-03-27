<?php


$access_item["/"] = true;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");

// get REST parameters
$action = $page->actions();

include_once("classes/shop/tally.class.php");
$TC = new Tally();

// page info
$page->bodyClass("shop_shift");
$page->pageTitle("Butiksvagt");

if($action) {

	if($action[0] == "kasse") {

		$tally_id = $action[1];

		// /butiksvagt/kasse/#tally_id#
		if(count($action) == 2) {
	
			$page->page([
				"templates" => "shop-shift/tally.php",
				"type" => "admin"
			]);
	
			exit();
	
		}
		// /butiksvagt/kasse/#tally_id#/updateTally
		elseif(count($action) == 3 && $action[2] == "updateTally" && $page->validateCsrfToken()) {
	
			$tally = $TC->updateTally($action);
	
			if($tally) {
				message()->resetMessages();
	
				// redirect to leave POST state
				header("Location: /butiksvagt");
				exit();
				
			}
			// Something went wrong
			else {
				message()->resetMessages();
				message()->addMessage("Hov, noget gik galt. Prøv igen.", array("type" => "error"));
				// redirect to leave POST state
				header("Location: /butiksvagt/kasse/".$action[1]);
				exit();
	
			}
	
			exit();
	
		}

		// /butiksvagt/kasse/#tally_id#/closeTally
		elseif(count($action) == 3 && $action[2] == "closeTally" && $page->validateCsrfToken()) {
	
			$tally_id = $TC->closeTally($action);
	
			if($tally_id) {
				message()->resetMessages();
	
				// redirect to leave POST state
				header("Location: /butiksvagt");
				exit();
				
			}
			// Something went wrong
			else {
				// redirect to leave POST state
				header("Location: /butiksvagt/kasse/".$action[1]);
				exit();
	
			}
	
			exit();
	
		}
		
		// /butiksvagt/kasse/#tally_id#/udbetaling/...
		elseif(count($action) > 2 && $action[2] == "udbetaling") {
			
			// /butiksvagt/kasse/#tally_id#/udbetaling
			if(count($action) == 3 && $action[2] == "udbetaling") {
		
				$page->page([
					"templates" => "shop-shift/tally_payout.php",
					"type" => "admin"
				]);
		
				exit();
		
			}

			// /butiksvagt/kasse/#tally_id#/udbetaling/addPayout
			elseif(count($action) == 4 && $action[3] == "addPayout" && $page->validateCsrfToken()) {
		
				$payout = $TC->addPayout($action);
		
				if($payout) {
					message()->resetMessages();
		
					// redirect to leave POST state
					header("Location: /butiksvagt/kasse/$tally_id");
					exit();
					
				}
				// Something went wrong
				else {
					message()->resetMessages();
					message()->addMessage("Hov, noget gik galt. Prøv igen.", array("type" => "error"));
					// redirect to leave POST state
					header("Location: /butiksvagt/kasse/$tally_id");
					exit();
		
				}
		
				exit();
		
			}

			// /butiksvagt/kasse/#tally_id#/udbetaling/deletePayout/#payout_id#
			elseif(count($action) == 5 && $action[3] == "deletePayout" && $page->validateCsrfToken()) {
		
				$payout_id = $action[4];

				$result = $TC->deletePayout($action);
		
				if($result) {
					message()->resetMessages();
		
					// redirect to leave POST state
					header("Location: /butiksvagt/kasse/$tally_id");
					exit();
					
				}
				// Something went wrong
				else {
					message()->resetMessages();
					message()->addMessage("Hov, noget gik galt. Prøv igen.", array("type" => "error"));
					// redirect to leave POST state
					header("Location: /butiksvagt/kasse/$tally_id");
					exit();
		
				}
		
				exit();
		
			}

		}

		// /butiksvagt/kasse/#tally_id#/andre-indtaegter/...
		elseif(count($action) > 2 && $action[2] == "andre-indtaegter") {
			
			// /butiksvagt/kasse/#tally_id#/andre-indtaegter
			if(count($action) == 3 && $action[2] == "andre-indtaegter") {
		
				$page->page([
					"templates" => "shop-shift/tally_revenue.php",
					"type" => "admin"
				]);
		
				exit();
		
			}

			// /butiksvagt/kasse/#tally_id#/andre-indtaegter/addRevenue
			elseif(count($action) == 4 && $action[3] == "addRevenue" && $page->validateCsrfToken()) {
		
				$revenue = $TC->addRevenue($action);
		
				if($revenue) {
					message()->resetMessages();
		
					// redirect to leave POST state
					header("Location: /butiksvagt/kasse/$tally_id");
					exit();
					
				}
				// Something went wrong
				else {
					message()->resetMessages();
					message()->addMessage("Hov, noget gik galt. Prøv igen.", array("type" => "error"));
					// redirect to leave POST state
					header("Location: /butiksvagt/kasse/$tally_id");
					exit();
		
				}
		
				exit();
		
			}

			// /butiksvagt/kasse/#tally_id#/andre-indtaegter/deleteRevenue/#revenue_id#
			elseif(count($action) == 5 && $action[3] == "deleteRevenue" && $page->validateCsrfToken()) {
		
				$revenue_id = $action[4];

				$result = $TC->deleteRevenue($action);
		
				if($result) {
					message()->resetMessages();
		
					// redirect to leave POST state
					header("Location: /butiksvagt/kasse/$tally_id");
					exit();
					
				}
				// Something went wrong
				else {
					message()->resetMessages();
					message()->addMessage("Hov, noget gik galt. Prøv igen.", array("type" => "error"));
					// redirect to leave POST state
					header("Location: /butiksvagt/kasse/$tally_id");
					exit();
		
				}
		
				exit();
		
			}

		}

		

		
	}
	
	

	
}

// standard template
$page->page(array(
	"templates" => "shop-shift/index.php",
	"type" => "admin"
));
exit();

?>
 