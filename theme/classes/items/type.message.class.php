<?php
/**
* @package janitor.items
* Meant to allow local additions/overrides
*/

class TypeMessage extends TypeMessageCore {

	/**
	* Initialization: set variable names and validation rules.
	*
	* @return void 
	*/
	function __construct() {

		parent::__construct(get_class());


		$this->db_log = SITE_DB.".project_kbhffmessage_log";

		// // Department
		$this->addToModel("department_id", array(
			"type" => "select",
			"label" => "Afdeling",
			"required" => true,
			"hint_message" => "Vælg hvilke medlemmer der skal modtage mailen.",
			"error_message" => "Du skal vælge hvem der skal modtage mailen."
		));
	}
	
	function sendKbhffMessageTest($action) {

		$UC = new User();
		$user = $UC->getKbhffUser();

		$this->getPostedEntities();

		$name = $this->getProperty("name", "value");
		$description = $this->getProperty("description", "value");
		$department_id = getPost("department_id", "value");

		if(isset($_POST["html"]) && isset($_POST["description"]) && $name) {
			
			// custom handling of description
			$value = stripDisallowed($_POST["description"]);
			$this->setProperty("description", "value", $value);
			$description = $this->getProperty("description", "value");
			
			// custom handling of HTML
			$value = stripDisallowed($_POST["html"]);
			$this->setProperty("html", "value", $value);
			$html = $this->getProperty("html", "value");

			// create quasi message item
			$message = [
				"name" => $name,
				"description" => $description,
				"html" => $html,
				"layout" => "template-mass_mail.html"
			];
	
			// create final HTML
			$final_html = html_entity_decode($this->mergeMessageIntoLayout($message));
			$name = prepareForHTML($name);

			$recipients[] = $user["email"];

			// Set correct sender email
			if($department_id == "all_departments") {
				$from_email = "info@kbhff.dk";
			}
			else {

				if(!$department_id) {
					// use current user's department
					$department = $UC->getUserDepartment(["user_id" => session()->value("user_id")]);
				}
				else {
					global $DC;
					$department = $DC->getDepartment(["id"=>$department_id]);
				}

				$from_email = $department["email"];

			}

			// send final HTML
			if(mailer()->send(["from_email" => $from_email, "recipients" => $recipients, "subject" => $name, "html" => $final_html])) {
				return true;
			}
		}


		return false;

	}

	function sendKbhffMessage($action) {

		global $UC;
		include_once("classes/users/supermember.class.php");
		$MC = new SuperMember();

		$this->getPostedEntities();

		$name = $this->getProperty("name", "value");
		$description = $this->getProperty("description", "value");
		$department_id = getPost("department_id", "value");

		if(isset($_POST["html"]) && isset($_POST["description"]) && $name) {
			
			// custom handling of description
			$value = stripDisallowed($_POST["description"]);
			$this->setProperty("description", "value", $value);
			$description = $this->getProperty("description", "value");
			
			// custom handling of HTML
			$value = stripDisallowed($_POST["html"]);
			$this->setProperty("html", "value", $value);
			$html = $this->getProperty("html", "value");
			
			$recipients = [];
	
			if($department_id == "all_departments") {
				
				// get all active members
				$members = $MC->getMembers(["only_active_members" => true]);
	
				// convert member list to recipients list
				foreach ($members as $member) {
					
					$member_email = $UC->getUsernames(["user_id" => $member["user"]["id"], "type" => "email"]);
					$recipients[] = $member_email ? $member_email["username"] : "";
				}

				$from_email = "info@kbhff.dk";

			}
			else {

				// no department_id was sent
				if(!$department_id) {
					// use current user's department
					$department = $UC->getUserDepartment(["user_id" => session()->value("user_id")]);
					$department_id = $department ? $department["id"] : false;
				}
				else {
					global $DC;
					$department = $DC->getDepartment(["id"=>$department_id]);
				}

				// get department users with active membership
				$users = $UC->getDepartmentUsers($department_id, ["only_active_members" => true]);
	
				// convert user list to recipients list
				foreach ($users as $user) {
					
					$user_email = $UC->getUsernames(["user_id" => $user["id"], "type" => "email"]);
					$recipients[] = $user_email ? $user_email["username"] : "";
				}

				$from_email = $department["email"];
			}

			$recipients[] = "info@kbhff.dk";

	
			// create quasi message item
			$message = [
				"name" => $name,
				"description" => $description,
				"html" => $html,
				"layout" => "template-mass_mail.html"
			];

			// create final HTML
			$final_html = html_entity_decode($this->mergeMessageIntoLayout($message));
			$name = prepareForHTML($name);

			if(count($recipients) < 1000) {

				// send final HTML
				$result = mailer()->sendBulk(["from_email" => $from_email, "recipients" => $recipients, "subject" => $name, "html" => $final_html]);
			}
			else {

				$recipient_chunks = array_chunk($recipients, 1000);
				foreach ($recipient_chunks as $recipient_chunk) {

					// send final HTML
					$result = mailer()->sendBulk(["from_email" => $from_email, "recipients" => $recipient_chunk, "subject" => $name, "html" => $final_html]);

				}
			}

			if($result) {
	
				global $page;
				logger()->addLog("TypeKbhffMessage->sendKbhffMessage: user_id:".session()->value("user_id").", department_id:".$department_id);
	
				// add to Kbhff message log
				$query = new Query();
				$query->checkDbExistence($this->db_log);
	
				if($department_id == 'all_departments') {
					$receiving_department = "All departments";
				}
				else {
					global $DC;
					$department = $DC->getDepartment(["id" => $department_id]);
					$receiving_department = $department ? $department["name"] : false;
				}
	
				$sql = "INSERT INTO ".$this->db_log." SET name = '$name', recipient = '$receiving_department', html = '$html'";
				$query->sql($sql);
	
				// add receipt data to session
				session()->value("recipient_count", count($recipients));
				session()->value("department_id", $department_id);
	
				return true;
			}
		}

		return false;
		
	}

	function getLogEntries($_options = false) {
		
		$id = false;

		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {
					case "id"        : $id             = $_value; break;
				}
			}
		}

		$query = new Query();
		$sql = "SELECT * FROM ".$this->db_log;

		// get specific log entry
		if($id) {
			$sql .= " WHERE id = $id";
		}

		$sql .= " ORDER BY created_at DESC";

		if($query->sql($sql)) {

			if($id) {
				return $query->result(0);
			}

			return $query->results();
		}

		return false;
	}
}

?>