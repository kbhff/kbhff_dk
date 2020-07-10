
<?php

/**
 * Manages departments.
 * 
 **/
class Department extends Model {

	/**
	* Initialization: set variable names and validation rules for Department model.
	*
	* @return void 
	*/
	function __construct() {

		// Construct Model class, passing the Department model as a parameter. 
		// The Model class is constructed *before* adding values to Department model to ensure Department overwrites the standard values of Model 
		parent::__construct(get_class());


		// Define the name of departments table in database
		$this->db = SITE_DB.".project_departments";
		$this->db_products = SITE_DB.".project_department_products";
		$this->db_pickupdates = SITE_DB.".project_department_pickupdates";


		// Name
		$this->addToModel("name", array(
			"max" => "50",
			"type" => "string",
			"label" => "Name",
			"required" => true,
			"hint_message" => "Name of the department", 
			"error_message" => "A department must have a name."
		));
		
		// Abbreviation
		$this->addToModel("abbreviation", array(
			"max" => "3",
			"type" => "string",
			"label" => "Abbreviation",
			"hint_message" => "Shorthand for the department. Maximum 3 characters.", 
			"error_message" => "Invalid abbreviation."
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
			"error_message" => "You must enter a city."
		));

		// Postal code
		$this->addToModel("postal", array(
			"max" => "50",
			"type" => "string",
			"label" => "Postal code",
			"required" => true,
			"hint_message" => "Postal code",
			"error_message" => "You must enter a valid postal code."
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

		// MobilePay number for signup in shop
		$this->addToModel("mobilepay_id", array(
			"type" => "string",
			"label" => "MobilePay number",
			"required" => true,
			"hint_message" => "The department has a MobilePay number.",
			"error_message" => "MobilePay number is required"
		));

		// Accepts new members?
		$this->addToModel("accepts_signup", array(
			"type" => "checkbox",
			"label" => "New members can sign up for this department",
			"hint_message" => "A few departments do not accept new members. If this department is one of them, uncheck this box.",
		));

		// Location
		$this->addToModel("geolocation", array(
			"type" => "string",
			"label" => "Location",
			"required" => true,
			"hint_message" => "Name and Geo coordinates of location",
			"error_message" => "Name and Geo coordinates must be filled out"
		));
		// latitude
		$this->addToModel("latitude", array(
			"type" => "number",
			"label" => "Latitude"
		));
		// longitude
		$this->addToModel("longitude", array(
			"type" => "number",
			"label" => "Longitude"
		));

		// description
		$this->addToModel("description", array(
			"type" => "html",
			"label" => "Short description",
			"hint_message" => "Write a short description of the department – this will be shown on the department list.",
			"error_message" => "A short description without any words? How weird."
		));

		// HTML
		$this->addToModel("html", array(
			"type" => "html",
			"label" => "Full description",
			"allowed_tags" => "p,h2,h3,h4,ul,ol,vimeo,youtube",
			"hint_message" => "Write the full department description",
			"error_message" => "No words? How weird."
		));

	}
	
	/**
	 * Saves the department to the database.
	 * 
	 * @param array $action REST parameters of current request
	 * @return array|false Department id, if everything goes well
	 */
	function saveDepartment($action) {
		
		// Get content of $_POST array which have been "quality-assured" by Janitor 
		$this->getPostedEntities();

		if(count($action) == 1 && $this->validateList(array("name", "abbreviation", "address1", "address2", "city", "postal", "email", "opening_hours", "mobilepay_id", "accepts_signup", "geolocation", "latitude", "longitude", "description", "html"))) {

			$IC = new Items();
			$query = new Query();
			
 
			$query->checkDbExistence($this->db);
			
			// Get posted values
			$name = $this->getProperty("name", "value");
			$abbreviation = $this->getProperty("abbreviation", "value");
			$address1 = $this->getProperty("address1", "value");
			$address2 = $this->getProperty("address2", "value");
			$postal = $this->getProperty("postal", "value");
			$city = $this->getProperty("city", "value");
			$email = $this->getProperty("email", "value");
			$opening_hours = $this->getProperty("opening_hours", "value");
			$mobilepay_id = $this->getProperty("mobilepay_id", "value");
			$accepts_signup = $this->getProperty("accepts_signup", "value");

			$geolocation = $this->getProperty("geolocation", "value");
			$latitude = $this->getProperty("latitude", "value");
			$longitude = $this->getProperty("longitude", "value");

			$description = $this->getProperty("description", "value");
			$html = $this->getProperty("html", "value");


			// Check if the department is already created (to avoid faulty double entries)  
			$sql = "SELECT * FROM ".$this->db." WHERE name = '$name'";
			if(!$query->sql($sql)) {
				// enter the department into the database
				$sql = "INSERT INTO ".$this->db." SET name='$name', abbreviation='$abbreviation', address1='$address1',address2='$address2',postal='$postal',city='$city',opening_hours='$opening_hours',email='$email',mobilepay_id='$mobilepay_id',accepts_signup='$accepts_signup',geolocation='$geolocation',latitude='$latitude',longitude='$longitude',description='$description',html='$html' ";
				
				// if successful, add message and return department id
				if($query->sql($sql)) {

					$department_id = $query->lastInsertId();

					// add all products to the new department
					$products = $IC->getItems(["where" => "itemtype REGEXP '^product'"]);
					foreach($products as $product) {
						
						$this->addProduct($department_id, $product["id"]);
					}

					// add all pickup dates to the new department
					// only opening dates that after current date? 
					include_once("classes/shop/pickupdate.class.php");
					$PC = new Pickupdate();
					$pickupdates = $PC->getPickupdates();
					foreach($pickupdates as $pickupdate) {

						$this->addPickupdate($department_id, $pickupdate["id"]);
					}


					message()->addMessage("Department created");
					return array("item_id" => $department_id);
				}
			}
			else {
				message()->addMessage("Department already exists.", array("type"=>"error"));
				return false;
			}

		}

		// something went wrong
		message()->addMessage("Could not create department.", array("type"=>"error"));
		return false;
	}


	/**
	 * Get list of departments from database.
	 *
	 * @return array|false Department data object (via callback to Query->results()
	 */
	function getDepartments($_options = false) {

		// Define default values
		$order = "name ASC";


		// Search through $_options to find recognized parameters
		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {
					case "order"        : $order             = $_value; break;
				}
			}
		}

		// Query database for all departments.  
		$query = new Query();
		$sql = "SELECT * FROM ".$this->db . " ORDER BY $order";		
		if($query->sql($sql)) {
			return $query->results();
		}
		
		return false;
	}

	/**
	 * Get a single department from database.
	 *
	 * @param array|boolean $_options Associative array containing unsorted function parameters.
	 * 		$id		int		Department id
	 * 
	 * @return array|false Department item (via callback to Query->result(0))
	 */
	function getDepartment($_options = false) {

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


		// Query database for department with specific id.
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
	 * Get list of departments that accept signups from database.
	 *
	 * @return array|false Department data object (via callback to Query->results()
	 */
	function getDepartmentsAcceptSignups() {

		// Query database for all departments.
		$query = new Query();
		$sql = "SELECT * FROM ".$this->db." WHERE accepts_signup = 1";
		if($query->sql($sql)) {
			return $query->results();
		}

		return false;
	}

	/**
	 * Update a single department.
	 *
	 * @param array $action REST parameters of current request
	 * @return array|false Updated Department data object (via callback to getDepartment())
	 */
	function updateDepartment($action) {

		// Get content of $_POST array which have been "quality-assured" by Janitor 
		$this->getPostedEntities();

		// Check that the number of REST parameters is as expected and that the listed entries are valid.
		if(count($action) == 2 && $this->validateList(array("name", "abbreviation", "address1", "address2", "city", "postal", "email", "opening_hours", "mobilepay_id", "accepts_signup", "geolocation", "latitude", "longitude", "description", "html"))) {
			
			$id = $action[1];
			$name = $this->getProperty("name", "value");
			$abbreviation = $this->getProperty("abbreviation", "value");
			$address1 = $this->getProperty("address1", "value");
			$address2 = $this->getProperty("address2", "value");
			$postal = $this->getProperty("postal", "value");
			$city = $this->getProperty("city", "value");
			$email = $this->getProperty("email", "value");
			$opening_hours = $this->getProperty("opening_hours", "value");
			$mobilepay_id = $this->getProperty("mobilepay_id", "value");
			$accepts_signup = $this->getProperty("accepts_signup", "value");

			$geolocation = $this->getProperty("geolocation", "value");
			$latitude = $this->getProperty("latitude", "value");
			$longitude = $this->getProperty("longitude", "value");

			$description = $this->getProperty("description", "value");
			$html = $this->getProperty("html", "value");

			// Ask the database to update the row with the id that came from $action. Update with the values that were received from getPostedEntities(). 
			$query = new Query();
			$sql = "UPDATE ".$this->db." SET name='$name', abbreviation='$abbreviation',address1='$address1',address2='$address2',postal='$postal',city='$city',opening_hours='$opening_hours',email='$email',mobilepay_id='$mobilepay_id',accepts_signup='$accepts_signup',geolocation='$geolocation',latitude='$latitude',longitude='$longitude',description='$description',html='$html' WHERE id = '$id'";
			// debug($sql);
			// if successful, add message and return the department data object
			if($query->sql($sql)) {
				message()->addMessage("Department updated");
				return $this->getDepartment(["id"=>$id]);
			}

		}

		// something went wrong
		return false;
	}

	/**
	 * Delete a single department.
	 *
	 * @param array $action REST parameters
	 * @return boolean
	 */
	function deleteDepartment($action) {

		// Checks for unexpected number of parameters
		if(count($action) == 2) {
			
			// Ask the database to delete the row with the id that came from $action. 
			$id = $action[1];
			$query = new Query();
			
			$sql = "SELECT * FROM ".SITE_DB.".user_department as u WHERE u.department_id = $id";
			// print_r($sql);exit();
			if($query->sql($sql)) {
				message()->addMessage("Department could not be deleted due to its users.", array("type" => "error"));
				return false;
			}
			
			$sql = "DELETE FROM ".$this->db." WHERE id = '$id'";
			if($query->sql($sql)) {
				message()->addMessage("Department deleted");
				return true;		
			}

		}
		message()->addMessage("Department could not be deleted.", array("type" => "error"));
		return false;
	}

	/**
	 * Add a product to the department
	 *
	 * @param int $department_id
	 * @param int $product_id
	 * @return boolean
	 */
	function addProduct($department_id, $product_id) {

		$query = new Query();
		$query->checkDbExistence($this->db_products);

		$sql = "INSERT INTO ".$this->db_products." SET department_id = $department_id, product_id = $product_id";
		if($query->sql($sql)) {

			return true;
		}

		return false;
	}

	/**
	 * Remove a product from the department
	 *
	 * @param int $department_id
	 * @param int $product_id
	 * @return boolean
	 */
	function removeProduct($department_id, $product_id) {
		
		$query = new Query();

		$sql = "DELETE FROM ".$this->db_products." WHERE department_id = $department_id AND product_id = $product_id";

		if($query->sql($sql)) {
			return true;
		}

		return false;

	}

	/**
	 * Add a pickup date to the department
	 *
	 * @param int $department_id
	 * @param int $pickupdate_id
	 * @return boolean
	 */
	function addPickupdate($department_id, $pickupdate_id) {

		$query = new Query();
		$query->checkDbExistence($this->db_pickupdates);

		$sql = "INSERT INTO ".$this->db_pickupdates." SET department_id = $department_id, pickupdate_id = $pickupdate_id";
		if($query->sql($sql)) {

			return true;
		}

		return false;
	}

	/**
	 * Remove a pickup date from the department
	 *
	 * @param int $department_id
	 * @param int $pickupdate_id
	 * @return boolean
	 */
	function removePickupdate($department_id, $pickupdate_id) {
		
		$query = new Query();

		$sql = "DELETE FROM ".$this->db_pickupdates." WHERE department_id = $department_id AND pickupdate_id = $pickupdate_id";

		if($query->sql($sql)) {

			return true;
		}

		return false;

	}
}

?>