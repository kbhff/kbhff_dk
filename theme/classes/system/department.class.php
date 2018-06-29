
<?php

/**
 * Manages departments.
 * 
 **/
class Department extends Model {

	/**
	* Inititialization: set variable names and validation rules.
	*/
	function __construct() {

		// Construct Model *before* adding it to Department model (to avoid Model overwriting Department) 
		parent::__construct(get_class());


		// Add departments table to database
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

		//Opening hours
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
	 * @param array $action Contains the REST parameters that are sent to the controller.
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
	 * Get department from database
	 *
	 * @param mixed $_options
	 * @return void
	 */
	function getDepartments($_options = false) {

		// $id = false;

		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {
					// case "id"        : $id             = $_value; break;
				}
			}
		}

		$query = new Query();
		$sql = "SELECT * FROM ".$this->db;
		
		if($query->sql($sql)) {
			return $query->results();
		}
		
		return false;
	}

	function getDepartment($_options = false) {

		$id = false;

		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {
					case "id"        : $id             = $_value; break;
				}
			}
		}

		if($id) {
			$query = new Query();
			$sql = "SELECT * FROM ".$this->db." WHERE id = '$id'";
			if($query->sql($sql)) {
				return $query->result(0);
			}
		}
		return false;
	}

	function updateDepartment($action) {
		$this->getPostedEntities();

		if(count($action) == 2 && $this->validateList(array("name", "address1", "address2", "city", "postal", "email", "opening_hours"))) {

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

	function deleteDepartment($action) {

		if(count($action) == 2) {
			
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