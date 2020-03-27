
<?php

/**
 * Manages tallies.
 * 
 **/
class Tally extends Model {

	/**
	* Initialization: set variable names and validation rules for Tally model.
	*
	* @return void 
	*/
	function __construct() {

		// Construct Model class, passing the Tally model as a parameter. 
		// The Model class is constructed *before* adding values to Tally model to ensure Tally overwrites the standard values of Model 
		parent::__construct(get_class());


		// Define the name of tallies table in database
		$this->db = SITE_DB.".shop_tallies";
		$this->db_payouts = SITE_DB.".shop_tally_payouts";
		$this->db_misc_revenues = SITE_DB.".shop_tally_misc_revenues";
		$this->db_cash_payments = SITE_DB.".shop_tally_cash_payments";


		// Name
		$this->addToModel("name", array(
			"max" => "50",
			"type" => "string",
			"label" => "Name",
			"required" => true,
			"hint_message" => "Name of the tally", 
			"error_message" => "A tally must have a name."
		));

		// Department
		$this->addToModel("department_id", array(
			"type" => "select",
			"label" => "Associated department",
			"required" => true,
			"hint_message" => "Select a department for this tally",
			"error_message" => "A tally must be associated with a department"
			
		));

		$this->addToModel("start_cash", [
			"type" => "number",
			"label" => "Kassebeholdning ved vagtstart",
			"required" => true,
			"hint_message" => "Hvor mange DKK var der i kassen ved vagtstart?",
			"error_message" => "Ugyldigt beløb"
		]);

		$this->addToModel("end_cash", [
			"type" => "number",
			"label" => "Kassebeholdning ved vagtens slutning",
			"required" => true,
			"hint_message" => "Hvor mange DKK var der i kassen ved vagtens slutning?",
			"error_message" => "Ugyldigt beløb"
		]);

		$this->addToModel("deposited", [
			"type" => "number",
			"label" => "Evt. deponeret beløb",
			"hint_message" => "Hvor mange DKK er blevet deponeret?",
			"error_message" => "Ugyldigt beløb"
		]);

		$this->addToModel("misc_cash_revenue", [
			"type" => "number",
			"label" => "Anden kontant indtægt",
			"hint_message" => "Hvor mange DKK er blevet indbetalt?",
			"error_message" => "Ugyldigt beløb"
		]);

		$this->addToModel("comment", array(
			"type" => "text",
			"label" => "Kommentarer til regnskabet",
			"hint_message" => "Forklar eventuelle uregelmæssigheder",
			"error_message" => "Ugyldig tekst"
		));

		// PAYOUT
		$this->addToModel("payout_amount", [
			"type" => "number",
			"label" => "Udbetaling fra kassen",
			"hint_message" => "Hvor mange DKK er blevet udbetalt?",
			"error_message" => "Ugyldigt beløb"
		]);

		$this->addToModel("payout_name", [
			"type" => "string",
			"label" => "Hvad er der betalt for?",
			"hint_message" => "Hvad er der betalt for?",
			"error_message" => "Fejl"
		]);

		$this->addToModel("payout_comment", [
			"type" => "string",
			"label" => "Kommentar til udbetalingen",
			"hint_message" => "Kommentar til udbetalingen",
			"error_message" => "Fejl"
		]);

		// MISC REVENUES
		$this->addToModel("revenue_amount", [
			"type" => "number",
			"label" => "Indtægt",
			"hint_message" => "Hvor mange DKK er blevet indbetalt?",
			"error_message" => "Ugyldigt beløb"
		]);

		$this->addToModel("revenue_name", [
			"type" => "string",
			"label" => "Indtægtskilde?",
			"hint_message" => "Hvor kommer indtægten fra?",
			"error_message" => "Fejl"
		]);

		$this->addToModel("revenue_comment", [
			"type" => "string",
			"label" => "Kommentar til indtægten",
			"hint_message" => "Kommentar til indtægten",
			"error_message" => "Fejl"
		]);




	}
	
	/**
	 * Get list of tallies from database.
	 *
	 * @return array|false Tally object. False on error. 
	 */
	function getTallies($_options = false) {

		// define default sorting order
		$order = "name ASC";
		
		$department_id = false;
		$creation_date = false;
		$status = false;


		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {
					case "order"           : $order                = $_value; break;
					case "department_id"   : $department_id        = $_value; break;
					case "status"          : $status               = $_value; break;
					case "creation_date"   : $creation_date        = $_value; break;
				}
			}
		}

		$query = new Query();
		$sql = "SELECT * FROM ".$this->db;		
		$where = [];

		if($department_id || $status || $creation_date) {
			$sql .= " WHERE ";
		}

		if($department_id) {

			$sql .= "department_id = $department_id";
		}
		if($creation_date) {

			if($department_id) {
				
				$sql .= " AND ";
			}

			$sql .= "created_at LIKE '$creation_date%'";
		}
		if($status) {

			if($creation_date || $department_id) {
				
				$sql .= " AND ";
			}

			$sql .= "status = $status";
		}

		$sql .= " ORDER BY $order";


		if($query->sql($sql)) {
			return $query->results();
		}
		
		return false;
	}

	
	/**
	 * Saves the tally to the database.
	 * 
	 * @param array $action REST parameters of current request
	 * @return array|false Tally id. False on error.
	 */
	function saveTally($action) {
		
		$this->getPostedEntities();

		
		if(count($action) == 1 && $this->validateList(["name", "department_id"])) {
			
			$query = new Query();
			$query->checkDbExistence($this->db);
			
			// Get posted values
			$name = $this->getProperty("name", "value");
			$department_id = $this->getProperty("department_id", "value");
	
			$opened_by = session()->value("user_id");
			
			// Check if the tally is already created (to avoid faulty double entries)  
			$sql = "SELECT * FROM ".$this->db." WHERE status = 1 AND department_id = $department_id";
			if(!$query->sql($sql)) {


				// enter the tally into the database
				$sql = "INSERT INTO ".$this->db." SET name='$name', department_id = $department_id, opened_by = $opened_by";
				
				// if successful, add message and return tally id
				if($query->sql($sql)) {
					return $query->lastInsertId();
				}
			}
			else {
				message()->addMessage("An open tally already exists for this department.", ["type" => "error"]);
				return false;
			}

		}

		// something went wrong
		message()->addMessage("Could not create tally.", ["type" => "error"]);
		return false;
	}

	/**
	 * Get a single tally from database.
	 *
	 * @param array|boolean $_options Associative array containing unsorted function parameters.
	 * 		$id				int			Tally id
	 * 		$name			string		Tally name
	 * 		$department_id	int			Will search for active tally for given department
	 * 
	 * @return array|false Tally item (via callback to Query->result(0))
	 */
	function getTally($_options = false) {

		// Define default values
		$id = false;
		$name = false;
		$department_id = false;
		
		// Search through $_options to find recognized parameters
		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {
					case "id"                   : $id             = $_value; break;
					case "name"                 : $name           = $_value; break;
					case "department_id"       : $department_id  = $_value; break;
				}
			}
		}


		// Query database for tally with specific id.
		if($id) {
			$query = new Query();
			$sql = "SELECT * FROM ".$this->db." WHERE id = '$id'";
			if($query->sql($sql)) {
				return $query->result(0);
			}
		}
		else if($name) {
			$query = new Query();
			$sql = "SELECT * FROM ".$this->db." WHERE name = '$name'";
			if($query->sql($sql)) {
				return $query->result(0);
			}
		}
		else if($department_id) {
			$query = new Query();
			$sql = "SELECT * FROM ".$this->db." WHERE department_id = '$department_id' AND status = 1";
			if($query->sql($sql)) {
				return $query->result(0);
			}
			else {

				include_once("classes/system/department.class.php");
				$DC = new Department;

				$department = $DC->getDepartment(["id" => $department_id]); 
				$department_name = $department ? superNormalize($department["name"]) : "";

				$_POST["name"] = date("Y-m-d", time())."_".$department_name;
				$_POST["department_id"] = $department_id;
				$tally_id = $this->saveTally(["saveTally"]);
				unset($_POST);

				$sql = "SELECT * FROM ".$this->db." WHERE id = '$tally_id'";
				if($query->sql($sql)) {

					return $query->result(0);
				}
			}
		}

		return false;
	}

	/**
	 * Update a single tally.
	 *
	 * @param array $action REST parameters of current request
	 * @return array|false Updated Tally data object
	 */
	function updateTally($action) {

		// Get content of $_POST array which have been "quality-assured" by Janitor 
		$this->getPostedEntities();

		// Check that the number of REST parameters is as expected.
		if(count($action) == 3) {
			
			$id = $action[1];
			$name = $this->getProperty("name", "value");

			$start_cash = $this->getProperty("start_cash", "value");
			$end_cash = $this->getProperty("end_cash", "value");
			$deposited = $this->getProperty("deposited", "value");
			
			$misc_cash_revenue = $this->getProperty("misc_cash_revenue", "value");

			$comment = $this->getProperty("comment", "value");


			$query = new Query();

			// base sql
			$sql = "UPDATE ".$this->db." SET modified_at=CURRENT_TIMESTAMP";

			if($name) {
				$sql .= ", name='$name'";
			}
			if($start_cash !== false) {

				if($start_cash === "") {
					$start_cash = 0;
				}
				
				$sql .= ", start_cash = $start_cash";
			}
			if($end_cash !== false) {

				if($end_cash === "") {
					$end_cash = 0;
				}

				$sql .= ", end_cash = $end_cash";
			}
			if($deposited !== false) {

				if($deposited === "") {
					$deposited = 0;
				}

				$sql .= ", deposited = $deposited";
			}
			if($misc_cash_revenue) {
				$sql .= ", misc_cash_revenue = $misc_cash_revenue";
			}
			if($comment) {
				$sql .= ", comment = '$comment'";
			}
			
			$sql .= " WHERE id = '$id'";
			// debug($sql);

			// if successful, add message and return the tally data object
			if($query->sql($sql)) {
				message()->addMessage("Tally updated");

				global $page;
				$user_id = session()->value("user_id");
				$page->addLog("Tally: tally_id:$id updated by user_id:$user_id; sql = '$sql'");

				return $this->getTally(["id"=>$id]);
			}

		}

		// something went wrong
		return false;
	}

	/**
	 * Delete a single tally.
	 *
	 * @param array $action REST parameters
	 * @return boolean
	 */
	function deleteTally($action) {

		// Checks for unexpected number of parameters
		if(count($action) == 2) {
			
			// Ask the database to delete the row with the id that came from $action. 
			$id = $action[1];
			$query = new Query();
			
			$sql = "SELECT * FROM ".SITE_DB.".user_tally as u WHERE u.tally_id = $id";
			// print_r($sql);exit();
			if($query->sql($sql)) {
				message()->addMessage("Tally could not be deleted due to its users.", array("type" => "error"));
				return false;
			}
			
			$sql = "DELETE FROM ".$this->db." WHERE id = '$id'";
			if($query->sql($sql)) {
				message()->addMessage("Tally deleted");

				global $page;
				$user_id = session()->value("user_id");
				$page->addLog("Tally: tally_id:$id deleted by user_id:$user_id");

				return true;		
			}

		}
		message()->addMessage("Tally could not be deleted.", array("type" => "error"));
		return false;
	}

	function addPayout($action) {

		$tally_id = $action[1];
		$this->getPostedEntities();

		if(count($action) == 4  && $this->validateList(["payout_name", "payout_amount"])) {

			$query = new Query();
 
			$query->checkDbExistence($this->db_payouts);
			
			// Get posted values
			$name = $this->getProperty("payout_name", "value");
			$amount = $this->getProperty("payout_amount", "value");
			$comment = $this->getProperty("payout_comment", "value");


			$sql = "INSERT INTO ".$this->db_payouts." SET name='$name', tally_id = $tally_id, amount = $amount, comment = '$comment'";
			if($query->sql($sql)) {

				$payout_id = $query->lastInsertId();

				global $page;
				$user_id = session()->value("user_id");
				$page->addLog("Tally: payout_id:$payout_id added to tally_id:$tally_id by user_id:$user_id");

				return $payout_id;
			}

		}

		return false;

	}

	function deletePayout($action) {

		$payout_id = $action[4];

		if(count($action) == 5) {

			$query = new Query();
			$tally_id = $action[1];
 
			$sql = "DELETE FROM ".$this->db_payouts." WHERE id = $payout_id";
			if($query->sql($sql)) {

				global $page;
				$user_id = session()->value("user_id");
				$page->addLog("Tally: payout_id:$payout_id deleted from tally_id:$tally_id by user_id:$user_id");

				return true;
			}

		}

		return false;

	}

	function getPayouts($tally_id) {

		$query = new Query();
		$query->checkDbExistence($this->db_payouts);

		$sql = "SELECT * FROM ".$this->db_payouts." WHERE tally_id = $tally_id";
		if($query->sql($sql)) {

			return $query->results();
		}

		return false;
	}

	function getMiscRevenues($tally_id) {

		$query = new Query();
		$query->checkDbExistence($this->db_misc_revenues);

		$sql = "SELECT * FROM ".$this->db_misc_revenues." WHERE tally_id = $tally_id";
		if($query->sql($sql)) {

			return $query->results();
		}

		return false;
	}

	function addRevenue($action) {


		$tally_id = $action[1];

		$this->getPostedEntities();


		if(count($action) == 4  && $this->validateList(["revenue_name", "revenue_amount"])) {

			$query = new Query();
			$query->checkDbExistence($this->db_misc_revenues);

			// Get posted values
			$name = $this->getProperty("revenue_name", "value");
			$amount = $this->getProperty("revenue_amount", "value");
			$comment = $this->getProperty("revenue_comment", "value");

			$sql = "INSERT INTO ".$this->db_misc_revenues." SET name='$name', tally_id = $tally_id, amount = $amount, comment = '$comment'";

			if($query->sql($sql)) {

				$revenue_id = $query->lastInsertId();

				global $page;
				$user_id = session()->value("user_id");
				$page->addLog("Tally: revenue_id:$revenue_id added to tally_id:$tally_id by user_id:$user_id");

				return $revenue_id;
			}

		}

		return false;

	}



	function deleteRevenue($action) {

		$revenue_id = $action[4];

		if(count($action) == 5) {

			$query = new Query();
			$tally_id = $action[1];

			$sql = "DELETE FROM ".$this->db_misc_revenues." WHERE id = $revenue_id";
			if($query->sql($sql)) {

				global $page;
				$user_id = session()->value("user_id");
				$page->addLog("Tally: revenue_id:$revenue_id deleted from tally_id:$tally_id by user_id:$user_id");

				return true;
			}

		}

		return false;

	}

	function calculateSalesByThePiece($tally_id) {

		$start_cash = $this->getStartCash($tally_id);
		$end_cash = $this->getEndCash($tally_id);
		
		$cash_sales_sum = $this->calculateCashSalesSum($this->cashOrderItemsSummary($tally_id)) ?: 0;
		
		$misc_revenues = $this->getMiscRevenuesSum($tally_id);
		$payouts = $this->getPayoutsSum($tally_id);

		if($start_cash !== false && $end_cash !== false && $cash_sales_sum !== false && $misc_revenues !== false && $payouts !== false) {

			$calculated_sales_by_the_piece = $end_cash - $start_cash - $cash_sales_sum -$misc_revenues + $payouts;
	
			return $calculated_sales_by_the_piece;
		}

		return false;

	}

	function calculateCashSalesSum($cash_order_items_summary) {

		if($cash_order_items_summary) {

			$cash_orders_sum = 0; 
			foreach($cash_order_items_summary as $item_id => $values) {
	
				$cash_orders_sum += $values["total_price"];
			}
	
			return $cash_orders_sum;
		}	

		return false;
	}

	function calculateChange($tally_id) {

		$end_cash = $this->getEndCash($tally_id);
		$deposited = $this->getDeposited($tally_id);

		if($end_cash !== false && $deposited !== false) {

			return $end_cash - $deposited;
		}

		return false;
	}

	function getDeposited($tally_id) {

		$query = new Query();

		$sql = "SELECT deposited FROM ".$this->db." WHERE id = $tally_id";
		if($query->sql($sql)) {

			return $query->result(0, "deposited");
		}

		return false;
	}

	function getMiscRevenuesSum($tally_id) {

		$revenues = $this->getMiscRevenues($tally_id);

		$sum = 0;
		
		if($revenues) {

			foreach($revenues as $revenue) {
				$sum += $revenue["amount"];
			}
	
		}
		
		return $sum;

	}

	function getPayoutsSum($tally_id) {

		$payouts = $this->getPayouts($tally_id);

		$sum = 0;
		
		if($payouts) {

			foreach($payouts as $payout) {
				$sum += $payout["amount"];
			}
	
		}
		
		return $sum;

	}

	function getTotalCashRevenue($tally_id) {
		
		$misc_revenues = $this->getMiscRevenuesSum($tally_id);
		$cash_sales = $this->calculateCashSalesSum($this->cashOrderItemsSummary($tally_id)) ?: 0;

		if($misc_revenues !== false) {

			return $misc_revenues + $cash_sales;
		}

		return false;
		
	}

	function getStartCash($tally_id) {

		$query = new Query();

		$sql = "SELECT start_cash FROM ".$this->db." WHERE id = $tally_id";
		if($query->sql($sql)) {

			return $query->result(0, "start_cash");
		}

		return false;
	}
	
	function getEndCash($tally_id) {

		$query = new Query();

		$sql = "SELECT end_cash FROM ".$this->db." WHERE id = $tally_id";
		if($query->sql($sql)) {

			return $query->result(0, "end_cash");
		}

		return false;
	}

	function closeTally($action) {

		if(count($action) == 3)	{

			$tally_id = $action[1];
			$user_id = session()->value("user_id");
	
			$this->getPostedEntities();
	
			$tally = $this->updateTally(["kasse", $tally_id, "updateTally"]);

			if(isset($tally["start_cash"]) && isset($tally["end_cash"])) {

				$query = new Query();
				$sql = "UPDATE ".$this->db." SET status = 2, closed_by = $user_id WHERE id = $tally_id";
				if($query->sql($sql)) {
	
					global $page;
					$page->addLog("Tally: tally_id:$tally_id closed by user_id:$user_id");
		
					return $tally_id;
				}
			}
		}

		message()->addMessage("Kunne ikke lukke regnskabet. Tjek at startbeholdning og slutbeholdning er udfyldt.", ["type" => "error"]);
		return false;
	}

	function addRegisteredCashPayment($tally_id, $payment_id) {

		$query = new Query();
		$query->checkDbExistence($this->db_cash_payments);

		$sql = "INSERT INTO ".$this->db_cash_payments." SET tally_id = $tally_id, payment_id = $payment_id";

		if($query->sql($sql)) {
		
			message()->addMessage("New registered cash payment was added.");
			return ["item_id" => $query->lastInsertId()];
		}


		return false;

	}

	function getRegisteredCashPayments($tally_id) {

		$query = new Query();
		$query->checkDbExistence($this->db_cash_payments);

		$sql = "SELECT * FROM ".$this->db_cash_payments." WHERE tally_id = $tally_id";

		if($query->sql($sql)) {

			return $query->results();
		}

		return false;
	}


	function cashOrderItemsSummary($tally_id) {

		include_once("classes/shop/supershop.class.php");
		$SC = new SuperShop();
		$IC = new Items();
		$query = new Query();
		$cash_orders = [];
		
		$registered_cash_payments = $this->getRegisteredCashPayments($tally_id);
		$cash_order_items_summary = [];
		
		if($registered_cash_payments) {
			
			// create list of orders
			foreach($registered_cash_payments as $registered_cash_payment) {
				
				$sql = "SELECT order_id FROM ".SITE_DB.".shop_payments WHERE id = ".$registered_cash_payment["payment_id"];
				if($query->sql($sql)) {
					
					$order_id = $query->result(0, "order_id");
					$cash_orders[] = $SC->getOrders(["order_id" => $order_id]);
				}
				
			}
			
			// create list of items
			foreach($cash_orders as $cash_order) {
				
				
				foreach($cash_order["items"] as $order_item) {
					
					
					$cash_order_items_summary[$order_item["item_id"]]["items"][] = $order_item;

					
					if(count($cash_order_items_summary[$order_item["item_id"]]["items"]) == 1) {
						$item = $IC->getItem(["id" => $order_item["item_id"], "extend" => true]);

						$cash_order_items_summary[$order_item["item_id"]]["count"] = 1;
						$cash_order_items_summary[$order_item["item_id"]]["name"] = $item["name"];
						$cash_order_items_summary[$order_item["item_id"]]["itemtype"] = $item["itemtype"];
						$cash_order_items_summary[$order_item["item_id"]]["unit_price"] = $order_item["unit_price"];	
						$cash_order_items_summary[$order_item["item_id"]]["total_price"] = $order_item["unit_price"];	
						
					}
					else {
						
						$cash_order_items_summary[$order_item["item_id"]]["count"] = ++$cash_order_items_summary[$order_item["item_id"]]["count"];
						$cash_order_items_summary[$order_item["item_id"]]["total_price"] += $order_item["unit_price"];	
					}

				}
				
			}

			return $cash_order_items_summary;
		}

		return false;

	}

	function getPreviousTally($tally_id) {

		$query = new Query();

		$tally = $this->getTally(["id" => $tally_id]);
		$department_id = $tally["department_id"];
		$tallies = $this->getTallies(["department_id" => $department_id, "status" => 2, "order" => "created_at ASC"]);

		$tally_index = arrayKeyValue($tallies, "id", $tally["id"]);
		if($tally_index) {

			$previous_tally = $tallies[$tally_index - 1];

			return $previous_tally;
		}

		return false; 
	}

	function createCsv($creation_date) {

		include_once("classes/system/department.class.php");
		$DC = new Department();
		include_once("classes/users/superuser.class.php");
		
		$UC = new SuperUser();


		$tallies = $this->getTallies(["creation_date" => $creation_date, "status" => 2, "order" => "department_id ASC"]);

		$csv_arr = [];

		foreach($tallies as $tally) {

			$department = $DC->getDepartment(["id" => $tally["department_id"]]);
			$opened_by = $UC->getUser(["user_id" => $tally["opened_by"]]);
			$closed_by = $UC->getUser(["user_id" => $tally["closed_by"]]);
			$previous_tally = $this->getPreviousTally($tally["id"]);
			if($previous_tally) {

				$previous_tally_change = $this->calculateChange($previous_tally["id"]);
			}

			$payouts = $this->getPayouts($tally["id"]);
			$revenues = $this->getMiscRevenues($tally["id"]);
			$calculated_sales_by_the_piece = $this->calculateSalesByThePiece($tally["id"]);
			$cash_order_items_summary = $this->cashOrderItemsSummary($tally["id"]);

			$csv_arr[] = $department["name"].",Åbnet af,".$opened_by["nickname"]." (".$opened_by["email"].")";
			$csv_arr[] = $department["name"].",Åbningstidspunkt,".date("d/m-Y H:i", strtotime($tally["created_at"]));
			$csv_arr[] = $department["name"].",Lukket af,".$closed_by["nickname"]." (".$closed_by["email"].")";
			$csv_arr[] = $department["name"].",Lukningstidspunkt,".date("d/m-Y H:i", strtotime($tally["modified_at"]));
			$csv_arr[] = $department["name"].",Forrige kasseåbning,".($previous_tally ? date("d/m-Y H:i", strtotime($previous_tally["created_at"])) : "Ikke tilgængelig");
			$csv_arr[] = $department["name"].",Byttepenge efter forrige kasselukning (kr.),".($previous_tally_change ?? "Ikke tilgængelig");
			$csv_arr[] = $department["name"].",Kassebeholdning ved vagtstart (kr.),".$tally["start_cash"];
			$csv_arr[] = $department["name"].",Kassebeholdning ved vagtafslutning (kr.),".$tally["end_cash"];
			$csv_arr[] = $department["name"].",Evt. deponeret (kr.),".($tally["deposited"] ?? 0);
			$csv_arr[] = $department["name"].",Udbetalinger fra kassen I ALT (kr.),".$this->getPayoutsSum($tally["id"]);
			
			if($payouts) {
				
				foreach($payouts as $payout) {
					
					$csv_arr[] = $department["name"].",Udbetaling: ".$payout["name"]." (kr.),".$payout["amount"];
					
				}
			}
			
			
			$csv_arr[] = $department["name"].",Andre kontante indtægter I ALT (kr.),".$this->getMiscRevenuesSum($tally["id"]);
			
			if($revenues) {
				
				foreach($revenues as $revenue) {
					
					$csv_arr[] = $department["name"].",Indtægt: ".$revenue["name"]." (kr.),".$revenue["amount"];
					
				}
			}
			
			$csv_arr[] = $department["name"].",Registreret kontantsalg I ALT (kr.),".($this->calculateCashSalesSum($cash_order_items_summary) ?: "0");
			
			if($cash_order_items_summary) {
				
				foreach($cash_order_items_summary as $item_id => $values) {
					
					$csv_arr[] = $department["name"].",".$values["count"]."x ".$values["name"]." (".$values["itemtype"].") á ".$values["unit_price"]."kr. (kr.),".$values["total_price"];
				}
			}
			
			$csv_arr[] = $department["name"].",Beregnet løssalg I ALT (kr.),".$this->calculateSalesByThePiece($tally["id"]);
			$csv_arr[] = $department["name"].",Byttepenge til næste uge (kassebeholdning ved slut minus deponerede penge) (kr.),".$this->calculateChange($tally["id"]);
			$csv_arr[] = $department["name"].",Noter,".$tally["comment"];
			

		}

		$csv = implode($csv_arr, "\n");

		return $csv;
	}
}

?>