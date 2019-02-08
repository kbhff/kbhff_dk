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
					message()->addMessage("Department updated");
					return true;
				}
			}
			else {
				// Set department
				$sql = "INSERT INTO ".SITE_DB.".user_department SET department_id = $department_id, user_id = $user_id";
				if($query->sql($sql)) {
					message()->addMessage("Department assigned");
					return true;
				}
			}

		}

		return false;
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
			if($this->userExists(array("email" => $email))) {
				$page->addLog("user->newUserFromMemberHelp: user exists ($email)");
				return array("status" => "USER_EXISTS");
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
								"template" => "signup"
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
	function getUsersByDepartment($action) {
		// Log that the method has been started
		global $page;
		$page->addLog("result->getUsersByDepartment: initiated");


		// Get content of $_POST array which have been "quality-assured" by Janitor 
		$this->getPostedEntities();
		
		if($this->validateList(array("department_id", "active_member", "user_id"))) {
			
			$department_id= $this->getProperty("department_id", "value");
			// $active = $this->getProperty("active_member", "value");
			$user_id = $this->getProperty("user_id", "value");
			$query = new Query();
			if($department_id) {
			
			
				// filtrates users by department and current user
				// $sql = "SELECT user_id FROM ".SITE_DB.".user_department WHERE department_id = $department_id AND NOT ".SITE_DB.".user_department.user_id = $user_id";
				
				// query to get correct information from different tables but only with mobilenr as username and not email 
			 	$sql = "SELECT ".SITE_DB.".user_usernames.username as mobilnr, ".SITE_DB.".users.nickname as navn, ".SITE_DB.".user_usernames.user_id, ".SITE_DB.".system_departments.name as afdeling
			 	from ".SITE_DB.".user_usernames
			 	JOIN ".SITE_DB.".users
			 	ON ".SITE_DB.".user_usernames.user_id = ".SITE_DB.".users.id
			 	JOIN ".SITE_DB.".user_department
			 	ON ".SITE_DB.".user_department.user_id = ".SITE_DB.".users.id
			 	JOIN ".SITE_DB.".system_departments
			 	ON ".SITE_DB.".system_departments.id = ".SITE_DB.".user_department.department_id
			 	WHERE ".SITE_DB.".user_department.department_id = $department_id
			 	AND NOT ".SITE_DB.".users.id = $user_id
			 	AND ".SITE_DB.".user_usernames.type = 'mobile' 
			 	group by user_id
				limit 200";
				
				if($query->sql($sql)) {
					$users = $query->results();
				}
				else {
					return false;
				}
				
				$sql = "select ".SITE_DB.".user_usernames.username as email, ".SITE_DB.".user_usernames.user_id
				from ".SITE_DB.".user_usernames
				where ".SITE_DB.".user_usernames.type = 'email'";
				
				if($query->sql($sql)) {
					$user_email = $query->results();
					// print_r($user_email);
				}
				else {
					return false;
				}
				// selects username by member_no. However, this should be asked for in the first query, since every member has a member_no but every members has a mobile or email.
				// $sql = "select ".SITE_DB.".user_usernames.username as Medlemsnr, ".SITE_DB.".user_usernames.user_id
				// from ".SITE_DB.".user_usernames
				// where ".SITE_DB.".user_usernames.type = 'member_no'";
				// 
				// if($query->sql($sql)) {
				// 	$user_no = $query->results();
				// 	print_r($user_no);
				// }
				// should also contain mobile
			
				if ($users & $user_email) {
					$kv = [];
					foreach($users as $k => $v) {
						$kv[ $v["user_id"] ] = $k;
					}
					foreach ($user_email as $k => $v) {
						if (array_key_exists( $v["user_id"] , $kv ) ) {
							$users[ $kv [$v["user_id"]] ] = array_merge( $users[$kv[$v["user_id"]]] , $user_email[$k] );
						}
					}
					
					// print_r($users);
					return $users;
				}
				 else if ($users) {
					 return $users;
				 }
				 else {
					 return false;
				 }
			}
			return false; 	
		}
	}
				// query to get email and mobile as keys, but they can only get either mobile or email as values. 
				// $sql = "select count(*) as repetitions, ".SITE_DB.".user_usernames.username as Email, ".SITE_DB.".user_usernames.username as Mobilnr
				// FROM ".SITE_DB.".user_usernames
				// WHERE  ".SITE_DB.".user_usernames.type = 'email'
				// AND ".SITE_DB.".user_usernames.type = 'mobile'					
				// group by user_id ";
				
				// query to get email and mobile as keys, but they can only get either mobile or email as values. 
				// $sql = "SELECT ".SITE_DB.".user_usernames.username as Email, ".SITE_DB.".user_usernames.username as Mobilnr
				// from ".SITE_DB.".user_usernames 
				// where ".SITE_DB.".user_usernames.type IN ('email', 'mobile')";
				
				
				// finds duplicates and counts them 
				// $sql = "select count(*) as repetitions, username as email, username as mobile, user_id
				// from ".SITE_DB.".user_usernames 
				// group by user_id
				// having repetitions > 1";
				
				// if($query->sql($sql)) {
				// 	$users = $query->results();
				// 	// print_r($users);
				// 	if($users) {
				// 
				// 		// loops over $result and convert the values (user_id) to a comma seperated string
				// 		 foreach($users as $u => $user) {
				// 		 // 	$str = "".implode("', '", $user).", ";
				// 	 		$sql = "SELECT ".SITE_DB.".user_usernames.username as Mobilnr, ".SITE_DB.".users.nickname as Navn, ".SITE_DB.".user_usernames.user_id, ".SITE_DB.".system_departments.name as Afdeling
				// 			from ".SITE_DB.".user_usernames
				// 			JOIN ".SITE_DB.".users
				// 			ON ".SITE_DB.".user_usernames.user_id = ".SITE_DB.".users.id
				// 			JOIN ".SITE_DB.".user_department
				// 			ON ".SITE_DB.".user_department.user_id = ".SITE_DB.".users.id
				// 			JOIN ".SITE_DB.".system_departments
				// 			ON ".SITE_DB.".system_departments.id = ".SITE_DB.".user_department.department_id
				// 			WHERE department_id = $department_id 
				// 			AND NOT ".SITE_DB.".user_department.user_id = $user_id
				// 			AND ".SITE_DB.".user_usernames.type = 'mobile'
				// 			Group By username
				// 			LIMIT 200";
				// 
				// 			if($query->sql($sql)) {
				// 				$users = $query->results();
				// 				print_r($users);
				// 			}
				// 			$sql = "select ".SITE_DB.".user_usernames.username as Email, ".SITE_DB.".user_usernames.user_id
				// 			from ".SITE_DB.".user_usernames
				// 			where ".SITE_DB.".user_usernames.type = 'email'";
				// 
				// 			if($query->sql($sql)) {
				// 				$user_email = $query->results();
				// 				print_r($user_email);
				// 			}
				// 
				// 			$sql = "select ".SITE_DB.".user_usernames.username as Medlemsnr, ".SITE_DB.".user_usernames.user_id
				// 			from ".SITE_DB.".user_usernames
				// 			where ".SITE_DB.".user_usernames.type = 'member_no'";
				// 
				// 			if($query->sql($sql)) {
				// 				$user_no = $query->results();
				// 				print_r($user_no);
				// 			}
						// }
}
?>