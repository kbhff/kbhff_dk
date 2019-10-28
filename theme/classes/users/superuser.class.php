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
		
		$user = $this->getUser(["user_id" => $user_id]);
		$user["department"] = $this->getUserDepartment();
		// print_r($user);

		return $user;
	}


	function getUser($_options=false) {
		
		// default values
		$user_id = false;

		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {

					case "user_id"        : $user_id          = $_value; break;
				}
			}
		}

		// default values

		$query = new Query();
		
		$sql = "SELECT * FROM ".$this->db." WHERE id = $user_id";
//			print $sql;
		if($query->sql($sql)) {
			$user = $query->result(0);


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
					"subject" => "Dit medlemskab af Københavns Fødevarefællesskab er opsagt",
					"message" => "Du har meldt dig ud af Københavns Fødevarefællesskab. Tak for denne gang.",
					"recipients" => [$user_email]
					]);

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
			
			$user_id = $action[1];
			$item_id = $this->getProperty("item_id", "value");

			$member = $this->getMembers(array("user_id" => $user_id));
		
			if($member) {
				$sql = "UPDATE ".SITE_DB.".user_item_subscriptions SET item_id = $item_id WHERE user_id = $user_id";
				
				if($query->sql($sql)) {
					message()->resetMessages();
					message()->addMessage("Medlemsskab er opdateret");
					return true;
				}
				else {
					return false;
				}	
			}
			
			else {
				return false;
				
			}
		}
	}
	/**
	 * Update user account information
	 *
	 * @param array $action REST parameters
	 * @return void
	 */
	function updateUserInformation($action) {
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
			
			if($email) {
				if($this->updateEmail(["updateEmail", $user_id])) {
					if($mobile) {
						if($this->updateMobile(["updateMobile", $user_id])) {
							message()->resetMessages();
							message()->addMessage("Dine oplysninger blev opdateret");
						}
					}
				}
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
	/**
	 * Update or set the current user's associated department.
	 *
	 * @param array $action REST parameters of current request
	 * @return boolean
	 */
	
	function updateUserDepartment($action) {
		// Get content of $_POST array that have been "quality-assured" by Janitor
		$this->getPostedEntities();

		// Check that the number of REST parameters is as expected and that the listed entries are valid.
		if(count($action) == 2 && $this->validateList(array("department_id"))) {
			
			$user_id = $action[1];
			$department_id = $this->getProperty("department_id", "value");

			// Create user_department table if it doesn't already exist
			$query = new Query();
			$query->checkDbExistence(SITE_DB.".user_department");

			// Check if the user is associated with a department and adjust query accordingly
			$user_department = $this->getUserDepartment(array("user_id" => $user_id));
			
		
			if ($user_department) {
					// Update department
				$sql = "UPDATE ".SITE_DB.".user_department SET department_id = $department_id WHERE user_id = $user_id";

				if($query->sql($sql)) {
					message()->resetMessages();
					message()->addMessage("Afdeling er opdateret");
					return true;
				}
				else {
					return false;
				}
			}
			
			else {
				// Set department
				$sql = "INSERT INTO ".SITE_DB.".user_department SET department_id = $department_id, user_id = $user_id";
				if($query->sql($sql)) {
					message()->resetMessages();
					message()->addMessage("Afdeling er opdateret");
					return true;
				}
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
			$sql = "SELECT user_id FROM ".SITE_DB.".user_log_agreements WHERE user_id = $user_id";
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
				$sql = "SELECT u.nickname AS nickname, u.firstname AS firstname, u.lastname as lastname, ud.department_id as department, u.id as user_id, (select un.username from ".SITE_DB.".user_usernames as un where un.user_id = u.id and un.type = 'email') as email, (select un.username from ".SITE_DB.".user_usernames as un where un.user_id = u.id and un.type = 'mobile') as mobile, (select un.username from ".SITE_DB.".user_usernames as un where un.user_id = u.id and un.type = 'member_no') as member_no from ".SITE_DB.".users u LEFT OUTER JOIN ".SITE_DB.".user_department ud ON u.id = ud.user_id LEFT JOIN ".SITE_DB.".user_usernames un ON un.user_id = u.id WHERE u.id <> $user_id AND (un.username like '%$search_value%' OR u.nickname like '%$search_value%' OR u.firstname like '%$search_value%' OR u.lastname like '%$search_value%')";
				
				if ($department_id != "all") {
					$sql .= " and ud.department_id = $department_id";
				}
				
				$sql .= " group by u.id";

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


}
?>