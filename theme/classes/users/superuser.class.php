<?php
/**
* @package janitor.users
* This file contains simple superuser extensions
* Meant to allow local user additions/overrides
*/


include_once("classes/users/superuser.core.class.php");

/**
* SuperUser customization class
*/
class SuperUser extends SuperUserCore {


	/**
	* Init, set varnames, validation rules
	*/
	function __construct() {

		// Construct SuperUserCore class
		parent::__construct(get_class());

	
	}


	/**
	 * Callback that saves user department after saving user
	 *
	 * @param integer $user_id
	 * @return void
	 */	
	function saved($user_id) {
		
		// Call updateUserDepartment with a "fake" $action array
		$this->updateUserDepartment(["updateUserDepartment", $user_id]);
	}


	/**
	 * Get the specified user, including associated department.
	 *
	 * @return array The user object, with the department object appended as a new property.
	 */
	 function getKbhffUser($_options=false) {
 		
 		// default values
 		$user_id = false;

 		if($_options !== false) {
 			foreach($_options as $_option => $_value) {
 				switch($_option) {

 					case "user_id"        : $user_id          = $_value; break;
 				}
 			}
 		}
		
		$user = $this->getUsers(["user_id" => $user_id]);

		if($user) {

			 
			$query = new Query();

			$user["mobile"] = "";
			$user["email"] = "";
	
			$sql = "SELECT * FROM ".$this->db_usernames." WHERE user_id = $user_id";
			if($query->sql($sql)) {
				$usernames = $query->results();
				foreach($usernames as $username) {
					$user[$username["type"]] = $username["username"];
				}
			}
	
	
			$user["addresses"] = $this->getAddresses();
	
			$user["maillists"] = $this->getMaillists();
	
			if((defined("SITE_SHOP") && SITE_SHOP)) {
				include_once("classes/users/supermember.class.php");
				$MC = new SuperMember();
				$user["membership"] = $MC->getMembers(["user_id" => $user_id]);
			}
	
			$user["department"] = $this->getUserDepartment(["user_id" => $user_id]);
			// print_r($user);
	
			return $user;
		}

		return false;
	}


	/**
	 * Delete kbhff account
	 *
	 * @param array $action REST parameters
	 * @return boolean
	 */
	function deleteUserInformation($_options=false) {
		
		// default values
		$user_id = false;

		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {

					case "user_id"        : $user_id          = $_value; break;
				}
			}
		}
		
		if($user_id) {

			$user = $this->getKbhffUser(["user_id" => $user_id]);
		
			$user_email = $user["email"];
		
			$cancel_result = $this->cancel(["cancel", $user_id]);
			
			
			// if cancel goes through and returns true then send a mail
			if ($cancel_result === true) {
				message()->resetMessages();
				message()->addMessage("Brugeroplysningerne blev slettet");
		
				mailer()->send([
					"template" => "confirmation_membership_cancellation",
					"recipients" => [$user_email]
				]);

				if($user["department"]) {

					// send notification to admin
					$this->sendMemberLeftNotification($user);
				}

				return true;
			}
			// Cannot cancel account due to unpaid orders
			else if(isset($cancel_result["error"]) && $cancel_result["error"] == "unpaid_orders") {
				message()->resetMessages();
				message()->addMessage("Brugeren blev ikke udmeldt grundet ubetalte ordrer. Du er velkommen til at kontakte it@kbhff.dk, der altid står klar til at hjælpe.", array("type" => "error"));
			
				return false;
			
			}
			// Any unknown error
			else {
				message()->resetMessages();
				message()->addMessage("Udmeldelsen slog fejl", ["type" => "error"]);
				return false;
			}
			
			//PERHAPS TODO: delete department affiliation
		}
	}

	// change membership type

	# /medlemshjaelp/updateUserDepartmentAndMembership/#user_id#
	function changeMembership($action) {

		// Get posted values to make them available for models
		$this->getPostedEntities();

		// does values validate
		if(count($action) == 2 && $this->validateList(array("item_id"))) {

			$query = new Query();
			$IC = new Items();
			
			include_once("classes/users/supermember.class.php");
			$MC = new SuperMember();

			$user_id = $action[1];
			$item_id = $this->getProperty("item_id", "value");
			$item = $IC->getItem(["id" => $item_id, "extend" => true]);

			$member = $MC->getMembers(array("user_id" => $user_id));

			$old_membership = isset($member["item"]["fixed_url_identifier"]) ? $member["item"]["fixed_url_identifier"] : false;

			$new_membership = isset($item["fixed_url_identifier"]) ? $item["fixed_url_identifier"] : false;
		
			if($member) {

				$sql = "UPDATE ".SITE_DB.".user_item_subscriptions SET item_id = $item_id WHERE user_id = $user_id";
				
				if($query->sql($sql)) {

					// reset user_group to User if new membership is Støttemedlem
					if($new_membership == "stoettemedlem") {

						include_once("classes/users/superuser.class.php");
						$UC = new SuperUser();

						$user_groups = $UC->getUserGroups();
						$user_key = arrayKeyValue($user_groups, "user_group", "User");
						$_POST["user_group_id"] = $user_groups[$user_key] ? $user_groups[$user_key]["id"] : false;
						$UC->update(["update", $member["user_id"]]);
						unset($_POST);
						
					}

					// send notification to admin
					$this->sendMembershipChangeNotification($member, $item);

					message()->resetMessages();
					message()->addMessage("Medlemskab er opdateret");
					return true;
				}
			}
			
			return false;
		}
	}

	function sendMembershipChangeNotification($member, $item) {

		$user_id = $member["user_id"];
		$user = $this->getKbhffUser(["user_id" => $user_id]);
		$email = $this->getUsernames(["user_id" => $user_id, "type" => "email"]);

		$old_membership = isset($member["item"]["fixed_url_identifier"]) ? $member["item"]["fixed_url_identifier"] : false;

		$new_membership = isset($item["fixed_url_identifier"]) ? $item["fixed_url_identifier"] : false;

		if($old_membership == "stoettemedlem" && $new_membership == "frivillig") {
			// send notification to admin
			mailer()->send([
				"subject" => "Medlem i afdeling ".$user["department"]["name"]." har ændret medlemstype.",
				"message" => "
Hej ".$user["department"]["name"]." butiksgruppe,

Følgende medlem har skiftet status fra støttemedlem til frivilligt medlem og vil fremover indgå i driften af foreningen gennem vagtplanen og arbejdsgrupper.

I får denne notifikation, så I kan kontakte medlemmet ift. at tilbyde en plads på afdelingens vagtplan.

Navn: ".$user["firstname"]." ".$user["lastname"]."
Email: ".($email ? $email["username"] : "-")."
				 			
Med venlig hilsen,
IT",
				"recipients" => ADMIN_EMAIL
			]);
			
		}
		else if($old_membership == "frivillig" && $new_membership == "stoettemedlem") {
			// send notification to admin
			mailer()->send([
				"subject" => "Medlem i afdeling ".$user["department"]["name"]." har ændret medlemstype.",
				"message" => "
Hej ".$user["department"]["name"]." butiksgruppe,

".$user["firstname"]." ".$user["lastname"]." har skiftet status fra frivilligt medlem til støttemedlem og forventes dermed ikke længere at indgå i vagtplanen.

Hvis det endnu ikke er sket, kan det være en god idé at fjerne vedkommende fra vagtplaner / holdoversigter.

Med venlig hilsen,
IT
",
				"recipients" => ADMIN_EMAIL
			]);
		}	
	}

	/**
	 * Update user account information
	 *
	 * @param array $action REST parameters
	 * @return void
	 */
	function updateUserInformation($action) {

		include_once("classes/users/superuser.class.php");
		$UC = new SuperUser();

		// Get posted values from form
		$this->getPostedEntities();
		$user_id = $action[1];
		$email = $this->getProperty("email", "value");
		$mobile = $this->getProperty("mobile", "value");
		// Prevent "nickname not assigned" error
		$nickname = $this->getProperty("nickname", "value");
		if (!$nickname) {
			$firstname = $this->getProperty("firstname", "value");
			$lastname = $this->getProperty("lastname", "value");
			$_POST["nickname"] = $firstname . " " . $lastname;
		}

		// Updates and checks if it went true(good) or false(bad)
		if ($this->update(["update", $user_id])) {
			
			$update_success = true;

			if($email) {
				$existing_email = $UC->getUsernames(["user_id" => $user_id, "type" => "email"]);
				$_POST["username_id"] = $existing_email ? $existing_email["id"] : false;
				if(!$this->updateEmail(["updateEmail", $user_id])) {
					$update_success = false;
				}
				unset($_POST["username_id"]);
			}
			if($mobile !== false) {
				if(!$this->updateMobile(["updateMobile", $user_id])) {
					$update_success = false;
				}
			}

			if($update_success) {
				message()->resetMessages();
				message()->addMessage("Dine oplysninger blev opdateret");	
			}
			else {
				message()->resetMessages();
				message()->addMessage("En del af opdateringen slog fejl. Opdater venligst browseren for at tjekke opdateringen.", ["type" => "error"]);
				return false;	
			}
		}
		
		else {
			message()->resetMessages();
			message()->addMessage("Opdateringen slog fejl", ["type" => "error"]);
			return false;
		}
	}

	/**
	 * Get a user's associated department.
	 *
	 * @param array|false $_options Associative array containing unsorted function parameters. In this case it should contain a user ID.
	 * 		$user_id 	int	
	 *
	 * @return array|false The department object, or false if the current user isn't associated with a department.
	 */
	
	
	function getUserDepartment($_options=false) {
		
		// default values
		$user_id = false;

		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {

					case "user_id"        : $user_id          = $_value; break;
				}
			}
		}


		//Query user ID in user_departments and get the associated department ID
		$query = new Query();
		
		if($user_id) {
			$sql = "SELECT department_id FROM ".SITE_DB.".user_department WHERE user_id = $user_id";

			if($query->sql($sql)) {
				$department_id = $query->result(0,"department_id");

				// Use getDepartment to find the department with the specified department ID.
				include_once("classes/system/department.class.php");

				$DC = new Department();
				$department = $DC->getDepartment(["id"=>$department_id]);

				return $department;
			}
		}

		return false;

	}

	function getDepartmentUsers($department_id, $_options = false) {

		$query = new Query();
		
		if($department_id) {

			$only_active_members = false;
			if($_options !== false) {
				foreach($_options as $_option => $_value) {
					switch($_option) {
						case "only_active_members"       : $only_active_members         = $_value; break;
					}
				}
			}

			$sql = "SELECT users.*, user_department.department_id, user_members.id AS member_id, user_members.subscription_id FROM ".SITE_DB.".user_members AS user_members, ".SITE_DB.".user_department AS user_department, $this->db AS users WHERE users.status = 1 AND users.user_group_id > 1 AND user_department.user_id = users.id AND user_members.user_id = users.id AND user_department.department_id = $department_id";

			if($only_active_members) {
				$sql .= " AND user_members.subscription_id IS NOT NULL";
			}

			$sql .= " ORDER BY nickname";

			if($query->sql($sql)) {

				return $query->results();
			}
		}

		return false;

	}

	function validateUserGroupUpdate($clerk_user_user_group, $member_user_user_group, $new_user_group) {

		if(isset($clerk_user_user_group["user_group"]) && isset($member_user_user_group["user_group"]) && isset($new_user_group["user_group"])) {

			// Shop shifts can upgrade User to Shop shift
			if ($clerk_user_user_group["user_group"] == "Shop shift" && $member_user_user_group["user_group"] == "User" && $new_user_group["user_group"] == "Shop shift") {
				return true;
			}
			// Local administrators can update User to either Shop shift or Local admin
			elseif ($clerk_user_user_group["user_group"] == "Local administrator" && $member_user_user_group["user_group"] == "User" && ($new_user_group["user_group"] == "Shop shift" || $new_user_group["user_group"] == "Local administrator")) {
				return true;
			}

			elseif (
				(
					// Clerks in these user groups...
					$clerk_user_user_group["user_group"] == "Local administrator"
					|| $clerk_user_user_group["user_group"] == "Purchasing group"
					|| $clerk_user_user_group["user_group"] == "Communication group"
				)
				&& (
					// ... can upgrade Members in these user groups
					$member_user_user_group["user_group"] == "User"
					|| $member_user_user_group["user_group"] == "Shop shift"
					|| $member_user_user_group["user_group"] == "Local administrator"
					|| $member_user_user_group["user_group"] == "Purchasing group"
					|| $member_user_user_group["user_group"] == "Communication group"
				)
				// ... to the Clerk's own user group
				&& $clerk_user_user_group["user_group"] == $new_user_group["user_group"]
			) {
				return true;
			}
		}
		
		return false;
	}

	// /janitor/user_group/updateUserUserGroup/#user_id# (values in POST)
	function updateUserUserGroup($action) {

		$clerk_user = $this->getKbhffUser(["user_id" => session()->value("user_id")]);
		$clerk_user_user_group = $this->getUserGroups(["user_group_id" => $clerk_user["user_group_id"]]);

		$member_user = $this->getKbhffUser(["user_id" => $action[1]]);
		$member_user_user_group = $this->getUserGroups(["user_group_id" => $member_user["user_group_id"]]);
		
		$posted_user_group = $this->getUserGroups(["user_group_id" => $_POST["user_group_id"]]);
		
		if($this->validateUserGroupUpdate($clerk_user_user_group, $member_user_user_group, $posted_user_group) && $this->update(["update", $action[1]])) {
			
			$member_user = $this->getKbhffUser(["user_id" => $action[1]]);
			$new_user_group = $this->getUserGroups(["user_group_id" => $member_user["user_group_id"]]);

			$new_user_group_info = [
				"User" => 'Som medlem af brugergruppen "User" er du i stand til at købe grøntsager i webshoppen, såfremt dit medlemskab er aktivt.',
				"Shop shift" => 'Som medlem af brugergruppen "Shop shift" er du i stand til at tage butiksvagter. Du kan selvfølgelig også selv købe grøntsager i webshoppen.',
				"Local administrator" => 'Som medlem af brugergruppen "Local administrator" er du i stand til at tage butiksvagter og sende beskeder til alle medlemmer i din lokalafdeling. Du kan også flytte medlemmer til din afdeling. Og du kan selvfølgelig også selv købe grøntsager i webshoppen.',
				"Purchasing group" => 'Som medlem af brugergruppen "Purchasing group" er du i stand til at indgå i arbejdet med at indkøbe produkter. Du kan også tage butiksvagter. Og du kan selvfølgelig også selv købe grøntsager i webshoppen.',
				"Communication group" => 'Som medlem af brugergruppen "Communication group" er du i stand til at indgå i arbejdet med udsendelse af nyhedsbreve. Du kan også tage butiksvagter. Og du kan selvfølgelig også selv købe grøntsager i webshoppen.',
				"Super User" => 'Som medlem af brugergruppen "Super User" har du adgang til alle systemets funktioner.'
			];

			message()->resetMessages();
			message()->addMessage("User group was updated");
			
			// send notification mail
			mailer()->send(array(
				"values" => array(
					"FROM" => ADMIN_EMAIL,
					"NICKNAME" => $member_user["nickname"],
					"OLD_USER_GROUP" => $member_user_user_group["user_group"],
					"NEW_USER_GROUP" => $new_user_group["user_group"], 
					"NEW_USER_GROUP_INFO" => $new_user_group_info[$new_user_group["user_group"]], 
				),
				"recipients" => $member_user["email"],
				"template" => "user_group_change_notice",
				"track_clicks" => false
			));


			return $new_user_group;
		}	

		return false;
	}

	function getAllActiveUsers() {

		$query = new Query();
		
		$sql = "SELECT * FROM ".$this->db." WHERE id != 1 AND user_group_id IS NOT NULL ORDER BY nickname";
		if($query->sql($sql)) {
			return $query->results();
	   }
	}


	/**
	 * Update or set the current user's associated department.
	 *
	 * @param array $action REST parameters of current request
	 * @return boolean
	 */
	
	function updateUserDepartment($action) {
		// Get content of $_POST array that have been "quality-assured" by Janitor
		$this->getPostedEntities();
		message()->resetMessages();

		$user_id = $action[1];
		$user = $this->getKbhffUser(["user_id" => $user_id]);

		// Check that the number of REST parameters is as expected and that the listed entries are valid.
		if(count($action) == 2 && $this->validateList(array("department_id")) && $user && !($user["membership"] && !isset($user["membership"]["subscription_id"]))) {
			
			$department_id = $this->getProperty("department_id", "value");

			// Create user_department table if it doesn't already exist
			$query = new Query();
			$query->checkDbExistence(SITE_DB.".user_department");

			// Check if the user is associated with a department and adjust query accordingly
			$user = $this->getKbhffUser(["user_id" => $user_id]);
			$old_department = $user["department"];

			if ($old_department) {
				
				if($old_department["id"] != $department_id) {

					// Update department
					$sql = "UPDATE ".SITE_DB.".user_department SET department_id = $department_id WHERE user_id = $user_id";
	
					if($query->sql($sql)) {
	
						// get updated user
						$user = $this->getKbhffUser(["user_id" => $user_id]);
	
						if($user["membership"]) {
							// send notifications to admin
							$this->sendMemberLeftNotification($user, ["old_department" => $old_department]);
							$this->sendNewMemberNotification($user);
						}
	
						message()->addMessage("Afdeling er opdateret");
						return true;
					}

					message()->addMessage("Afdeling blev ikke opdateret.", ["type" => "error"]);
				}

			}
			
			else {
				// Set department
				$sql = "INSERT INTO ".SITE_DB.".user_department SET department_id = $department_id, user_id = $user_id";
				if($query->sql($sql)) {

					// get updated user
					$user = $this->getKbhffUser(["user_id" => $user_id]);

					if($user["membership"]) {
						$this->sendNewMemberNotification($user);
					}

					message()->addMessage("Afdeling er opdateret");
					return true;
				}

				message()->addMessage("Afdeling blev ikke opdateret.", ["type" => "error"]);
			}

		}

		return false;
	}

	/**
	 * Update user password
	 *
	 * @param array $action REST parameters
	 * @return boolean
	 */
	function updateUserPassword($action) {
		
		// Get posted values to make them available for models
		$this->getPostedEntities();
		$user_id = $action;
		
		// print_r($action);exit();
		if($user_id) {
			
			// If user already has a password
			if($this->hasPassword(["user_id" => $user_id])) {
				
				// does values validate
				if($this->validateList(array("new_password"))) {
					$query = new Query();
					
					// make sure type tables exist
					$query->checkDbExistence($this->db_passwords);
					$new_password = password_hash($this->getProperty("new_password", "value"), PASSWORD_DEFAULT);
					
					// Delete old password
					$sql = "DELETE FROM ".$this->db_passwords." WHERE user_id = $user_id";
					if($query->sql($sql)) {

						// Save new password
						$sql = "INSERT INTO ".$this->db_passwords." SET user_id = $user_id, password = '$new_password'";
						if($query->sql($sql)) {
							return true;
						}
					}
				}
			}
			// user does not have a password
			else {
				// does values validate
				if($this->validateList(array("new_password", "confirm_password"))) {
					$query = new Query();

					// make sure type tables exist
					$query->checkDbExistence($this->db_passwords);

					// Hash to inject
					$new_password = password_hash($this->getProperty("new_password", "value"), PASSWORD_DEFAULT);

					// Save new password
					$sql = "INSERT INTO ".$this->db_passwords." SET user_id = $user_id, password = '$new_password'";
					if($query->sql($sql)) {
						return true;
					}
				}
			}
		}
		return false;
	}

	// Check if user has accepted terms
	function hasAcceptedTerms($_options=false) {
		
		// default values
		$user_id = false;

		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {

					case "user_id"        : $user_id          = $_value; break;
				}
			}
		}
		 
		if($user_id) {
			
			$query = new Query();
		
			$query->checkDbExistence(SITE_DB.".user_log_agreements");
			$sql = "SELECT user_id FROM ".SITE_DB.".user_log_agreements WHERE name = 'terms' AND user_id = $user_id";
			if($query->sql($sql)) {
				return true;
			}
		}
		return false;
	}
	
	// User has accepted terms
	// Add to database
	function acceptedTerms($_options=false) {
		
		// default values
		$user_id = false;

		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {

					case "user_id"        : $user_id          = $_value; break;
				}
			}
		}
		 
		if($user_id) {
				
			$query = new Query();
			

			$query->checkDbExistence(SITE_DB.".user_log_agreements");
			$sql = "INSERT INTO ".SITE_DB.".user_log_agreements SET user_id = $user_id, name = 'terms'";
			$query->sql($sql);

		}
	}
	
	/**
	 * Shop clerk creates new user via member help
	 *
	 * @param array $action REST parameters of current request
	 * @return array|false Success: array with user ID, nickname, and email. Error: false or array with error message. 
	 */
	function newUserFromMemberHelp($action) {

		// Log that the method has been started
		global $page;
		$page->addLog("user->newUserFromMemberHelp: initiated");

		// only attempt user creation if signups are allowed for this site
		if(defined("SITE_SIGNUP") && SITE_SIGNUP) {

			// Get content of $_POST array which have been "quality-assured" by Janitor 
			$this->getPostedEntities();
			$terms = $this->getProperty("terms", "value");
			$email = $this->getProperty("email", "value");


			// if user hasn't accepted terms, return error
			if(!$terms) {
				$page->addLog("user->newUserFromMemberHelp: missing terms agreement");
				return array("status" => "MISSING_TERMS");
			}
			
			
			// if user already exists, return error
			$user_id = $this->getLoginUserId($email);
			if($user_id) {
				// print_r($user_id[0]["user_id"]);exit();
				$page->addLog("user->newUserFromMemberHelp: user exists ($email)");
				return array("status" => "USER_EXISTS", "existing_user_id" => $user_id);
			}


			// Check if values validate – minimum is email, firstname, and lastname
			if(count($action) == 1 && $this->validateList(array("email", "firstname", "lastname")) && $email) {

				$query = new Query();
				$nickname = $this->getProperty("nickname", "value");
				$firstname = $this->getProperty("firstname", "value");
				$lastname = $this->getProperty("lastname", "value");


				// get entities for current value
				$entities = $this->getModel();
				$names = array();
				$values = array();

				foreach($entities as $name => $entity) {
					if($entity["value"] !== false && preg_match("/^(nickname|firstname|lastname|language)$/", $name)) {
						$names[] = $name;
						$values[] = $name."='".$entity["value"]."'";
					}
				}

				// if no nickname were posted, attempt to construct it, or, if everything fails, use email
				if(!$nickname) {
					if($firstname && $lastname) {
						$nickname = $firstname . " " . $lastname;
					}
					else if($firstname) {
						$nickname = $firstname;
					}
					else if($lastname) {
						$nickname = $lastname;
					}
					else {
						$nickname = $email;
					}

					$values[] = "nickname='".$nickname."'";
					$quantity = $this->getProperty("quantity", "value");
					$item_id = $this->getProperty("item_id", "value");
				}


				// add the "Member" user group to $values array, which will define the new user
				$values[] = "user_group_id=2";

				// Create query string by imploding the $values array into a comma-separated string
				$sql = "INSERT INTO ".$this->db." SET " . implode(",", $values);
				// print $sql."<br>\n";
				
				if($query->sql($sql)) {

					$user_id = $query->lastInsertId();

					// Generate verification code
					$verification_code = randomKey(8);

					// add email and verification code to user_usernames. Use email as username.
					$sql = "INSERT INTO $this->db_usernames SET username = '$email', verified = 0, verification_code = '$verification_code', type = 'email', user_id = $user_id";
					// print $sql."<br>\n";
					
					if($query->sql($sql)) {

						// If there's a mobile number, use it as username
						$mobile = $this->getProperty("mobile", "value");
						if($mobile) {
							$sql = "INSERT INTO $this->db_usernames SET username = '$mobile', verified = 1, verification_code = '$verification_code', type = 'mobile', user_id = $user_id";
			//				print $sql;
							$query->sql($sql);
						}


						// user can send password on signup
						$raw_password = $this->getProperty("password", "value");
						$mail_password = "******** (password is encrypted)";

						// encrypt password
						if($raw_password) {
							$password = password_hash($raw_password, PASSWORD_DEFAULT);
							$sql = "INSERT INTO ".$this->db_passwords." SET user_id = $user_id, password = '$password'";
							if(!$query->sql($sql)) {
								$page->addLog("user->newUserFromMemberHelp failed: (couldn't write password to db)");
								return false;
							}
						}

						// store signup email for receipt page
						session()->value("signup_email", $email);
	
	
	
						// VERIFICATION EMAIL
	
						// add log
						$page->addLog("user->newUserFromMemberHelp: created: " . $email . ", user_id:$user_id");
	
						// verification code success
						// send activation email
						if($verification_code) {
	
							// send verification email to user
							mailer()->send(array(
								"values" => array(
									"NICKNAME" => $nickname,
									"EMAIL" => $email,
									"VERIFICATION" => $verification_code,
									"PASSWORD" => $mail_password
								),
								"track_clicks" => false,
								"recipients" => $email,
								"template" => "signup_memberhelp"
							));
	
							// send notification email to admin
							mailer()->send(array(
								"subject" => SITE_URL . " - New User: " . $email,
								"message" => "Check out the new user: " . SITE_URL . "/janitor/admin/user/edit/" . $user_id,
								"tracking" => false
								// "template" => "system"
							));
						}							
						// verification code error
						else {
							// send error email notification
							mailer()->send(array(
								"recipients" => $email,
								"template" => "signup_error"
							));
	
							// send notification email to admin
							mailer()->send(array(
								"subject" => "New User created ERROR: " . $email,
								"message" => "Check out the new user: " . SITE_URL . "/janitor/admin/user/edit/" . $user_id,
								"tracking" => false
								// "template" => "system"
							));
						}
					
	
	
	
						// TERMS
	
						// Add terms agreement
						$query->checkDbExistence(SITE_DB.".user_log_agreements");
						$sql = "INSERT INTO ".SITE_DB.".user_log_agreements SET user_id = $user_id, name = 'terms'";
						$query->sql($sql);
	
	
						// MAILLIST
	
						// maillist subscription sent as integer
						$maillist = getPost("maillist");
						if($maillist == 1) {	
							// check if Nyheder maillist exists
							$maillists = $page->maillists();
							$maillist_match = arrayKeyValue($maillists, "name", "Nyheder");
							if($maillist_match !== false) {
								$maillist_id = $maillists[$maillist_match]["id"];
								$_POST["maillist_id"] = $maillist_id;
								
								// add maillist for current user
								$this->addMaillist(array("addMaillist", $user_id));
							}
	
							// ignore subscription if maillist does not exist
	
						}
						// itemtype post save handler
						// TODO: Consider if failed postSave should have consequences
						if(method_exists($this, "saved")) {
							$this->saved($user_id);
						}
	
	
						message()->resetMessages();
	
						// return enough information to the frontend
						return array("user_id" => $user_id, "nickname" => $nickname, "email" => $email);
					}

				}
			}
		}

		$page->addLog("user->newUserFromMemberHelp failed: (missing info)");
		return false;
	}
	
		
		
		/** a memberhelp can search for users 
		* @param array $action REST parameters
		* @return boolean
		*/
	
	function searchUsers($action) {
	
		// Get content of $_POST array which have been "quality-assured" by Janitor 
		$this->getPostedEntities();

		if($this->validateList(array("search_member"))) {

			$department_id= $this->getProperty("department_id", "value");
			$search_value = $this->getProperty("search_member", "value");
			
			$user_id = session()->value("user_id");

			$query = new Query();

			if($search_value) {
				$sql = "
				SELECT 
					u.nickname AS nickname, 
					u.firstname AS firstname, 
					u.lastname AS lastname, 
					ud.department_id AS department, 
					u.id AS user_id, 
					(SELECT un.username FROM ".SITE_DB.".user_usernames as un WHERE un.user_id = u.id and un.type = 'email') AS email, 
					(SELECT un.username FROM ".SITE_DB.".user_usernames AS un WHERE un.user_id = u.id and un.type = 'mobile') AS mobile, 
					(SELECT un.username FROM ".SITE_DB.".user_usernames AS un WHERE un.user_id = u.id and un.type = 'member_no') AS member_no 
				FROM ".
					SITE_DB.".users u LEFT OUTER JOIN ".SITE_DB.".user_department ud ON u.id = ud.user_id LEFT JOIN ".SITE_DB.".user_usernames un ON un.user_id = u.id 
				WHERE 
					u.id <> $user_id 
					AND (un.username LIKE '%$search_value%' 
					OR u.nickname LIKE '%$search_value%' 
					OR u.firstname LIKE '%$search_value%' 
					OR u.lastname LIKE '%$search_value%')";
				
				if ($department_id != "all") {
					$sql .= " AND ud.department_id = $department_id";
				}
				
				$sql .= " GROUP BY u.id ORDER BY u.lastname";

				include_once("classes/system/department.class.php");
 				$DC = new Department();
 				$departments = $DC->getDepartments();

				foreach ($departments as $d => $department): 
					$department = array_column($departments, "name", "id");
				endforeach;

				if ($query->sql($sql)) {
					$users = $query->results();

					if ($users):
						foreach($users as $u => $user):
							foreach($department as $d => $depart):
								if ($d == $user["department"]) :
									$users[$u]["department"] = $depart;
								endif;
							endforeach;
						endforeach;
					endif;

					return array("users" => $users, "search_value" => $search_value, "department_id" => $department_id);
				}
				return array("users" => false, "search_value" => $search_value, "department_id" => $department_id);
			}
		}

		return false;
	}

	function hasUnpaidMembership($_options = false) {

		$query = new Query();
		$MC = new SuperMember();
		$IC = new Items();
		$SC = new SuperShop();

		// default values
		$user_id = false;

		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {

					case "user_id"        : $user_id          = $_value; break;
				}
			}
		}
		

		$member = $MC->getMembers(["user_id" => $user_id]);
		if($member && $member["order"] && $member["order"]["payment_status"] <= 1) {

			$order_item = $IC->getItem(["id" => $member["order"]["items"][0]["item_id"]]);

			if($order_item && $order_item["itemtype"] == "signupfee") {
				$result["type"] = "signupfee";
				$result["order_no"] = $member["order"]["order_no"];
				return $result;
			}
			else if($order_item && $order_item["itemtype"] == "membership") {
				$result["type"] = "membership";
				$result["order_no"] = $member["order"]["order_no"];
				return $result;
			}
 
		}
		// handle odd cases where an unpaid signupfee exists but no membership
		else {

			$unpaid_orders = $SC->getUnpaidOrders(["user_id" => $user_id]);

			if($unpaid_orders && count($unpaid_orders) == 1) {
				
				$unpaid_order = $SC->getOrders(["order_id" => $unpaid_orders[0]["id"]]);
				$item = $IC->getItem(["id" => $unpaid_order["items"][0]["item_id"]]);

				if($item && $item["itemtype"] == "signupfee") {
					$result["type"] = "signupfee";
					$result["order_no"] = $unpaid_order["order_no"];
					return $result;
				}
			}
		}

		return false;

	}

	function getUserRenewalOptOut($user_id) {

		if($user_id) {
			$query = new Query();
			$sql = "SELECT * FROM ".SITE_DB.".user_log_agreements WHERE user_id = $user_id AND name = 'disable_membership_renewal'";
			if($query->sql($sql)) {
				
				$renewal_optout_time = $query->result(0, "accepted_at");
				return $renewal_optout_time;
			}
		}

		return false;
	}

	function setUserRenewalOptOut($user_id) {

		if($user_id) {

			$query = new Query();
	
			if(!$this->getUserRenewalOptOut($user_id)) {
				
				$sql = "INSERT INTO ".SITE_DB.".user_log_agreements SET user_id = $user_id, name = 'disable_membership_renewal'";
				if($query->sql($sql)) {
					
					return true;
				}
			}
		}
	
		return false;
	}

	function unsetUserRenewalOptOut($user_id) {
		
		if($user_id) {

			$query = new Query();
	
			if($this->getUserRenewalOptOut($user_id)) {
	
				$sql = "DELETE FROM ".SITE_DB.".user_log_agreements WHERE user_id = $user_id AND name = 'disable_membership_renewal'";
				if($query->sql($sql)) {
					return true;
				}
				else {
					return false;
				}
			}

			return true;
		}

	}

	/**
	 * Update or set the current user's associated department.
	 *
	 * @param array $action REST parameters of current request
	 * @return boolean
	 */
	function updateUserRenewalOptOut($action){
		// Get content of $_POST array that have been mapped to the model entities object
		$this->getPostedEntities();

		// print_r ($action);
		// Check that the number of REST parameters is as expected and that the listed entries are valid.
		if(count($action) == 2 && $this->validateList(array("membership_renewal"))) {

			$user_id = $action[1];

			$user = $this->getKbhffUser(["user_id" => $user_id]);
			$membership_renewal = $this->getProperty("membership_renewal", "value");

			$query = new Query();

			if($membership_renewal && $this->getUserRenewalOptOut($user_id)) {


				// user is inactive member
				if($user["membership"] && !$user["membership"]["subscription_id"]) {

					return "REACTIVATION REQUIRED";
				}	
				
				$this->unsetUserRenewalOptOut($user_id);

				return "RENEWAL ENABLED";

			}
			elseif(!$membership_renewal && !$this->getUserRenewalOptOut($user_id)) {

				$this->setUserRenewalOptOut($user_id);

				return "RENEWAL DISABLED";
			}
			else {
				return true;
			}

		}

		return false;
		
		
	}
	
	
	// MEMBERSHIP

	// get membership for current user
	// includes membership item and order
	// function getMembership($_options=false) {
		
	// 	// default values
	// 	$user_id = false;

	// 	if($_options !== false) {
	// 		foreach($_options as $_option => $_value) {
	// 			switch($_option) {

	// 				case "user_id"        : $user_id          = $_value; break;
	// 			}
	// 		}
	// 	} 

		
	// 	$query = new Query();
	// 	$IC = new Items();
	// 	$SC = new SuperShop();


	// 	// membership with subscription
	// 	$sql = "SELECT members.id as id, subscriptions.id as subscription_id, subscriptions.item_id as item_id, subscriptions.order_id as order_id, members.user_id as user_id, members.created_at as created_at, members.modified_at as modified_at, subscriptions.renewed_at as renewed_at, subscriptions.expires_at as expires_at FROM ".$this->db_subscriptions." as subscriptions, ".$this->db_members." as members WHERE members.user_id = $user_id AND members.subscription_id = subscriptions.id LIMIT 1";
		
	// 	if($query->sql($sql)) {
	// 		$membership = $query->result(0);
	// 		$membership["item"] = $IC->getItem(array("id" => $membership["item_id"], "extend" => array("prices" => true, "subscription_method" => true)));
	// 		if($membership["order_id"]) {
	// 			$membership["order"] = $SC->getOrders(array("order_id" => $membership["order_id"]));
	// 		}
	// 		else {
	// 			$membership["order"] = false;
	// 		}

	// 		return $membership;
	// 	}
	// 	// membership without subscription
	// 	else {
	// 		$sql = "SELECT * FROM ".$this->db_members." WHERE user_id = $user_id LIMIT 1";
	// 		if($query->sql($sql)) {
	// 			$membership = $query->result(0);

	// 			$membership["item"] = false;
	// 			$membership["order"] = false;
	// 			$membership["order_id"] = false;
	// 			$membership["item_id"] = false;
	// 			$membership["expires_at"] = false;
	// 			$membership["renewed_at"] = false;

	// 			return $membership;
	// 		}
	// 	}

	// 	return false;
	// }


	function hasEmailAddress($_options = false) {
		
		// default values
		$user_id = false;

		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {

					case "user_id"        : $user_id          = $_value; break;
				}
			}
		}

		if($user_id) {

			$user = $this->getKbhffUser(["user_id" => $user_id]);
			if($user && $user["email"]) {
				return $user["email"];
			}
		}

		return false;
	}


	function getUserLogAgreement($agreement_name, $_options = false) {

		$user_id = false;

		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {

					case "user_id"        : $user_id          = $_value; break;
				}
			}
		}

		if($user_id && $agreement_name) {
			$query = new Query();
			$sql = "SELECT * FROM ".SITE_DB.".user_log_agreements WHERE user_id = $user_id AND name = '".$agreement_name."'";
			if($query->sql($sql)) {
				
				$accepted_at = $query->result(0, "accepted_at");
				return $accepted_at;
			}
		}

		return false;
	}

	function setUserLogAgreement($agreement_name, $_options = false) {

		$user_id = false;
		$query = new Query();

		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {

					case "user_id"        : $user_id          = $_value; break;
				}
			}
		}

		if(!$this->getUserLogAgreement($agreement_name, ["user_id" => $user_id])) {
			
			$sql = "INSERT INTO ".SITE_DB.".user_log_agreements SET user_id = $user_id, name = '".$agreement_name."'";
			if($query->sql($sql)) {
				
				return true;
			}
		}

		return false;
	}

	function unsetUserLogAgreement($agreement_name, $_options = false) {

		$user_id = false;
		$query = new Query();

		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {

					case "user_id"        : $user_id          = $_value; break;
				}
			}
		}

		if($this->getUserLogAgreement($agreement_name, ["user_id" => $user_id])) {

			$sql = "DELETE FROM ".SITE_DB.".user_log_agreements WHERE user_id = $user_id AND name = '".$agreement_name."'";
			if($query->sql($sql)) {
				return true;
			}
			else {
				return false;
			}
		}

		return true;
	}

	/**
	 * Update the current user's email agreements.
	 *
	 * @param array $action REST parameters of current request
	 * @return boolean
	 */
	function updateEmailAgreements($action){
		// Get content of $_POST array that have been mapped to the model entities object
		$this->getPostedEntities();

		// print_r ($action);
		// Check that the number of REST parameters is as expected and that the listed entries are valid.
		if(count($action) == 2 && $this->validateList(["ordering_reminder", "pickup_reminder"] )) {

			$user_id = $action[1];
			$ordering_reminder = $this->getProperty("ordering_reminder", "value");
			$pickup_reminder = $this->getProperty("pickup_reminder", "value");

			$query = new Query();

			if($ordering_reminder && $this->getUserLogAgreement("disable_ordering_reminder", ["user_id" => $user_id])) {

				$this->unsetUserLogAgreement("disable_ordering_reminder", ["user_id" => $user_id]);

			}
			elseif(!$ordering_reminder && !$this->getUserLogAgreement("disable_ordering_reminder", ["user_id" => $user_id])) {

				$this->setUserLogAgreement("disable_ordering_reminder", ["user_id" => $user_id]);

			}

			if($pickup_reminder && $this->getUserLogAgreement("disable_pickup_reminder", ["user_id" => $user_id])) {

				$this->unsetUserLogAgreement("disable_pickup_reminder", ["user_id" => $user_id]);

			}
			elseif(!$pickup_reminder && !$this->getUserLogAgreement("disable_pickup_reminder", ["user_id" => $user_id])) {

				$this->setUserLogAgreement("disable_pickup_reminder", ["user_id" => $user_id]);

			}

			return true;

		}

		return false;
		
	}

	function sendPickupReminders($action) {

		if(count($action) == 2) {

			$user_id = $action[1];
			$user = $this->getKbhffUser(["user_id" => $user_id]);


			mailer()->send([
				"recipients" => [$user["email"]],
				"template" => "pickup_reminder",
				"values" => [
					"NICKNAME" => $user["nickname"],
				]
			]);
		}
		else {

			include_once("classes/shop/pickupdate.class.php");
			$PC = new Pickupdate;
			
			$recipients = [];

			// get next scheduled pickupdate
			$pickupdate = $PC->getPickupdate(["pickupdate" => date("Y-m-d", strtotime("next ".PICKUP_DAY))]);



			$pickup_reminder_datetime = date("Y-m-d H", strtotime("next ".PICKUP_DAY) - PICKUP_REMINDER_TIME_DELTA_HOURS*60*60);
			$current_datetime = date("Y-m-d H");

			if($pickupdate && $pickup_reminder_datetime == $current_datetime) {

				$SC = new Shop;

				$pickupdate_order_items = $SC->getPickupdateOrderItems($pickupdate["id"]);
				if($pickupdate_order_items) {

					foreach($pickupdate_order_items as $poi) {

						$user = $this->getKbhffUser(["user_id" => $poi["user_id"]]);

						// order_item user is not already among recipients 
						// and has not opted out from pickup reminders
						if(!in_array($user["email"], $recipients) && !$this->getUserLogAgreement("disable_pickup_reminder", ["user_id" => $poi["user_id"]])) {

							// add to recipients
							$recipients[] = $user["email"];

							// send reminder
							mailer()->send([
								"recipients" => [$user["email"]],
								"template" => "pickup_reminder",
								"values" => [
									"NICKNAME" => $user["nickname"],
									"PICKUPDATE" => date("d.m.Y", strtotime($pickupdate["pickupdate"]))
								]
							]);
						}
					}
				}
				return true;
			}

			return false;
		}

	}

	function sendOrderingReminders($action) {

		if(count($action) == 2) {

			$user_id = $action[1];
			$user = $this->getKbhffUser(["user_id" => $user_id]);


			mailer()->send([
				"recipients" => [$user["email"]],
				"template" => "ordering_reminder",
				"values" => [
					"NICKNAME" => $user["nickname"],
					"DEADLINE_DATE" => date("d.m.Y", strtotime(ORDERING_DEADLINE_TIME)),
					"DEADLINE_TIME" => date("H:i", strtotime(ORDERING_DEADLINE_TIME))
				]
			]);
		}
		else {

			include_once("classes/shop/pickupdate.class.php");
			$PC = new Pickupdate;
			
			$recipients = [];

			// get next+1 scheduled pickupdate
			$pickupdate = $PC->getPickupdate(["pickupdate" => date("Y-m-d", strtotime(PICKUP_DAY." next week"))]);

			$ordering_reminder_datetime = date("Y-m-d H:i", strtotime(ORDERING_DEADLINE_TIME) - ORDERING_REMINDER_TIME_DELTA_HOURS*60*60);
			$current_datetime = date("Y-m-d H:i");

			if($pickupdate && $ordering_reminder_datetime == $current_datetime) {

				$SC = new Shop;

				$users = $this->getAllActiveUsers();

				if($users) {

					foreach ($users as $user) {

						$kbhff_user = $this->getKbhffUser(["user_id" => $user["id"]]);

						// order_item user is not already among recipients 
						// and has not opted out from pickup reminders
						if(!in_array($kbhff_user["email"], $recipients) && !$this->getUserLogAgreement("disable_ordering_reminder", ["user_id" => $user["id"]])) {

							// add to recipients
							$recipients[] = $kbhff_user["email"];

							// send reminder
							mailer()->send([
								"recipients" => [$kbhff_user["email"]],
								"template" => "ordering_reminder",
								"values" => [
									"NICKNAME" => $kbhff_user["nickname"],
									"DEADLINE_DATE" => date("d.m.Y", strtotime(ORDERING_DEADLINE_TIME)),
									"DEADLINE_TIME" => date("H:i", strtotime(ORDERING_DEADLINE_TIME))
								]
							]);
						}
					}
				}
			}
			
			return true;
		}

	}

		/**
	 * Update usernames from posted values. 
	 * 
	 * Expects $email and $username_id from $_POST.
	 * /janitor/admin/user/updateEmail/#user_id#
	 *
	 * @param array $action user_id in $action[1]
	 * 
	 * @return array|true|false Returns status code indicating whether email was updated/unchanged/already existing. Returns true if email was deleted (updated to blank). False on error.
	 */
	function updateEmail($action) {

		// Get posted values to make them available for models
		$this->getPostedEntities();

		// does action match expected
		if(count($action) == 2) {

			$user_id = $action[1];
			$query = new Query();

			$user = $this->getKbhffUser(["user_id" => $user_id]);

			// make sure type tables exist
			$query->checkDbExistence($this->db_usernames);

			$email = $this->getProperty("email", "value");
			$verification_status = $this->getProperty("verification_status", "value");

			$username_id = getPost("username_id");

			// email was sent
			if($email) {

				// check if email already exists

				// On current user_id
				$sql = "SELECT id FROM $this->db_usernames WHERE username = '$email' AND user_id = $user_id".($username_id ? " AND id != $username_id" : "");
				// debug([$sql]);
				if($query->sql($sql)) {

					message()->addMessage("Email already exists for this user", array("type" => "error"));
					$status = ["email_status" => "ALREADY_EXISTS"];
					return $status;
				}

				// On other user_id
				else {
					$sql = "SELECT id FROM $this->db_usernames WHERE username = '$email'".($username_id ? " AND id != $username_id" : "");
					// debug([$sql]);
					if($query->sql($sql)) {

						message()->addMessage("Email is used by another user", array("type" => "error"));
						$status = ["email_status" => "ALREADY_EXISTS"];
						return $status;
					}
				}


				// Generate new verification code
				$verification_code = randomKey(8);



				// New username
				if(!$username_id) {

					// Insert new username
					$sql = "INSERT INTO $this->db_usernames SET username = '$email', verification_code = '$verification_code', type = 'email', user_id = $user_id";
					// debug([$sql]);
					if($query->sql($sql)){

						$username_id = $query->lastInsertId();

						message()->addMessage("Email added");
						$status = [
							"email_status" => "UPDATED",
						];

					}

				}


				// Modifying existing username
				else if($username_id) {

					$current_username = $this->getUsernames(array("username_id" => $username_id));



					// email is changed
					if($current_username && $current_username["type"] === "email" && $email != $current_username["username"]) {

						$sql = "UPDATE $this->db_usernames SET username = '$email', verified = 0, verification_code = '$verification_code' WHERE id = $username_id";
						// debug([$sql]);
						if($query->sql($sql)) {

							// send verification email to user's new email
							mailer()->send(array(
								"values" => array(
									"NICKNAME" => $user["nickname"], 
									"EMAIL" => $email, 
									"VERIFICATION" => $verification_code,
									// "PASSWORD" => $mail_password
								), 
								"track_clicks" => false,
								"recipients" => $email, 
								"template" => "verify_changed_email"
							));

							// send verification email to user's old email
							mailer()->send(array(
								"values" => array(
									"NICKNAME" => $user["nickname"], 
									"NEW_EMAIL" => $email, 
									// "PASSWORD" => $mail_password
								), 
								"track_clicks" => false,
								"recipients" => $current_username["username"], 
								"template" => "email_change_notice"
							));

							// Delete verification logs
							$sql = "DELETE FROM ".SITE_DB.".user_log_verification_links WHERE username_id = $username_id";
							// debug([$sql]);
							$query->sql($sql);

							message()->addMessage("Email updated");
							$status = ["email_status" => "UPDATED"];

						}

					}

					// email is NOT changed
					else if($current_username && $current_username["type"] === "email" && $email == $current_username["username"]) {

						message()->addMessage("Email unchanged");
						$status = ["email_status" => "UNCHANGED"];

					}

				}


				// Map username_id to response
				$status["username_id"] = $username_id;


				// update verification status
				$result = $this->setVerificationStatus($username_id, $user_id, $verification_status);
				if($result && isset($result["verification_status"])) {

					$status["verification_status"] = $result["verification_status"];
					return $status;

				}

			}
			// Email was empty, username_id was sent – delete username
			else if(!$email && $username_id) {

				$sql = "DELETE FROM $this->db_usernames WHERE id = $username_id AND user_id = $user_id AND type = 'email'";
				// debug([$sql]);
				if($query->sql($sql)) {
					message()->addMessage("Email deleted");
					return true;
				}

			}

		}

		message()->addMessage("Could not update email", array("type" => "error"));
		return false;

	}
}
?>