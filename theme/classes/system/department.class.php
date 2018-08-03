
<?php

/**
 * Manages departments.
 * 
 **/
class Department extends Model {

	/**
	* Initialization: set variable names and validation rules for Department model.
	*/
	function __construct() {

		// Construct Model *before* adding it to Department model (to avoid Model overwriting Department) 
		parent::__construct(get_class());


		// Define the name of departments table in database
		$this->db = SITE_DB.".system_departments";


		// Name
		$this->addToModel("name", array(
			"max" => "50",
			"type" => "string",
			"label" => "Name",
			"required" => true,
			"hint_message" => "Name of the department", 
			"error_message" => "A department must have a name."
		));

		// Address 1
		$this->addToModel("address1", array(
			"max" => "50",
			"type" => "string",
			"label" => "Street name and number",
			"required" => true,
			"hint_message" => "Street name and number",
			"error_message" => "You must enter a street name and number."
		));

		// Address 2
		$this->addToModel("address2", array(
			"type" => "text",
			"label" => "Additional address information",
			"hint_message" => "Additional address information.",
			"error_message" => "Invalid address"
		));

		// City
		$this->addToModel("city", array(
			"max" => "50",
			"type" => "string",
			"label" => "City",
			"required" => true,
			"hint_message" => "In which city is the department located?",
			"error_message" => "Invalid city"
		));

		// Postal code
		$this->addToModel("postal", array(
			"max" => "50",
			"type" => "string",
			"label" => "Postal code",
			"required" => true,
			"hint_message" => "Postal code",
			"error_message" => "Invalid postal code"
		));

		// Contact email
		$this->addToModel("email", array(
			"max" => "50",
			"type" => "email",
			"label" => "Email address",
			"hint_message" => "The contact email for the department.",
			"error_message" => "Invalid email address"
		));

		// Opening hours
		$this->addToModel("opening_hours", array(
			"type" => "text",
			"label" => "Opening hours",
			"hint_message" => "The department's normal opening hours",
			"error_message" => "Invalid opening hours"
		));

	}
	
	/**
	 * Saves the department to the database.
	 * 
	 * @param array $action REST parameters of current request
	 * @return void
	 */

	function saveDepartment($action) {
		$this->getPostedEntities();

		if(count($action) == 1 && $this->validateList(array("name", "address1", "address2", "city", "postal", "email", "opening_hours"))) {

			$query = new Query();

			$query->checkDbExistence($this->db);

			$name = $this->getProperty("name", "value");
			$address1 = $this->getProperty("address1", "value");
			$address2 = $this->getProperty("address2", "value");
			$postal = $this->getProperty("postal", "value");
			$city = $this->getProperty("city", "value");
			$email = $this->getProperty("email", "value");
			$opening_hours = $this->getProperty("opening_hours", "value");
			


			// Check if the department is already created (to avoid faulty double entries)
			$sql = "SELECT * FROM ".$this->db." WHERE name = '$name'";
			if(!$query->sql($sql)) {
				$sql = "INSERT INTO ".$this->db." SET name='$name',address1='$address1',address2='$address2',postal='$postal',city='$city',opening_hours='$opening_hours',email='$email'";
				if($query->sql($sql)) {
					message()->addMessage("Department created");
					return array("item_id" => $query->lastInsertId());		
				}
			}
			else {
				message()->addMessage("Department already exists.", array("type"=>"error"));
				return false;
			}

		}


		message()->addMessage("Could not create department.", array("type"=>"error"));
		return false;
	}


	/**
	 * Get list of departments from database.
	 *
	 * @param array|boolean $_options Array containing unsorted function parameters
	 * @return void
	 */
	function getDepartments($_options = false) {

		// Define default values
		// $id = false;
		
		
		// Search through $_options to find recognized parameters
		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {
					// case "id"        : $id             = $_value; break;
				}
			}
		}

		// Query database for all departments.  
		$query = new Query();
		$sql = "SELECT * FROM ".$this->db;		
		if($query->sql($sql)) {
			return $query->results();
		}
		
		return false;
	}

	/**
	 * Get a single department from database.
	 *
	 * @param array|boolean $_options Associative array containing unsorted function parameters. Findes der et tag for at angive de mulige parametre?
	 * @return void
	 */
	function getDepartment($_options = false) {

		// Define default values
		$id = false;
		
		
		// Search through $_options to find recognized parameters
		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {
					case "id"        : $id             = $_value; break;
				}
			}
		}


		// Query database for department with specific id.
		if($id) {
			$query = new Query();
			$sql = "SELECT * FROM ".$this->db." WHERE id = '$id'";
			if($query->sql($sql)) {
				return $query->result(0);
			}
		}
		return false;
	}


	/**
	 * Update a single department.
	 *
	 * @param array $action REST parameters of current request
	 * @return void
	 */
	function updateDepartment($action) {
		
		// Get content of $_POST array that have been "quality-assured" by Janitor 
		$this->getPostedEntities();
		

		// Check that the number of REST parameters is as expected and that the listed entries are valid.
		if(count($action) == 2 && $this->validateList(array("name", "address1", "address2", "city", "postal", "email", "opening_hours"))) {

			// Ask the database to update the row with the id that came from $action. Update with the values that were received from getPostedEntities(). 
			$query = new Query();
			
			$id = $action[1];
			$name = $this->getProperty("name", "value");
			$address1 = $this->getProperty("address1", "value");
			$address2 = $this->getProperty("address2", "value");
			$postal = $this->getProperty("postal", "value");
			$city = $this->getProperty("city", "value");
			$email = $this->getProperty("email", "value");
			$opening_hours = $this->getProperty("opening_hours", "value");

			$sql = "UPDATE ".$this->db." SET name='$name',address1='$address1',address2='$address2',postal='$postal',city='$city',opening_hours='$opening_hours',email='$email' WHERE id = '$id'";
				if($query->sql($sql)) {
					message()->addMessage("Department updated");
					return $this->getDepartment(["id"=>$id]);		
				}

		}

		return false;
	}

	/**
	 * Delete a single department.
	 *
	 * @param array $action REST parameters
	 * @return void
	 */
	function deleteDepartment($action) {

		// Checks for unexpected number of parameters
		if(count($action) == 2) {
			
			// Ask the database to delete the row with the id that came from $action. 
			$id = $action[1];
			$query = new Query();
			$sql = "DELETE FROM ".$this->db." WHERE id = '$id'";
			if($query->sql($sql)) {
				message()->addMessage("Department deleted");
				return true;		
			}

		}

		return false;
	}
}

?>