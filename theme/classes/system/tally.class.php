
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
		$this->db = SITE_DB.".system_tallies";
		$this->db_payouts = SITE_DB.".system_tally_payouts";
		$this->db_misc_revenues = SITE_DB.".system_tally_misc_revenues";


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


		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {
					case "order"        : $order             = $_value; break;
				}
			}
		}

		$query = new Query();
		$sql = "SELECT * FROM ".$this->db . " ORDER BY $order";		
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

			// Check if the tally is already created (to avoid faulty double entries)  
			$sql = "SELECT * FROM ".$this->db." WHERE name = '$name'";
			if(!$query->sql($sql)) {

				// enter the tally into the database
				$sql = "INSERT INTO ".$this->db." SET name='$name', department_id = $department_id";
				
				// if successful, add message and return tally id
				if($query->sql($sql)) {
					message()->addMessage("Tally created");
					return ["item_id" => $query->lastInsertId()];
				}
			}
			else {
				message()->addMessage("Tally already exists.", ["type" => "error"]);
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
	 * 		$id		int		Tally id
	 * 
	 * @return array|false Tally item (via callback to Query->result(0))
	 */
	function getTally($_options = false) {

		// Define default values
		$id = false;
		$name = false;
		
		// Search through $_options to find recognized parameters
		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {
					case "id"        : $id             = $_value; break;
					case "name"      : $name           = $_value; break;
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

		return false;
	}

	/**
	 * Update a single tally.
	 *
	 * @param array $action REST parameters of current request
	 * @return array|false Updated Tally data object (via callback to getTally())
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
			if($start_cash) {
				$sql .= ", start_cash = $start_cash";
			}
			if($end_cash) {
				$sql .= ", end_cash = $end_cash";
			}
			if($deposited) {
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

				return $payout_id;
			}

		}

		return false;

	}

	function deletePayout($action) {

		$payout_id = $action[4];

		if(count($action) == 5) {

			$query = new Query();
 
			$sql = "DELETE FROM ".$this->db_payouts." WHERE id = $payout_id";
			if($query->sql($sql)) {

				return true;
			}

		}

		return false;

	}

	function getPayouts($tally_id) {

		$query = new Query();

		$sql = "SELECT * FROM ".$this->db_payouts." WHERE tally_id = $tally_id";
		if($query->sql($sql)) {

			return $query->results();
		}

		return false;
	}

	function getMiscRevenues($tally_id) {

		$query = new Query();

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

				return $revenue_id;
			}

		}

		return false;

	}



	function deleteRevenue($action) {

		$revenue_id = $action[4];

		if(count($action) == 5) {

			$query = new Query();

			$sql = "DELETE FROM ".$this->db_misc_revenues." WHERE id = $revenue_id";
			if($query->sql($sql)) {

				return true;
			}

		}

		return false;

	}

}

?>