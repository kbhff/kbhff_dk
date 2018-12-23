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

		parent::__construct(get_class());

	
	}


	// save user department on save user
	function saved($user_id) {
		$this->updateUserDepartment(["updateUserDepartment", $user_id]);
	}

	/**
	 * Get the current user's associated department.
	 *
	 * @param int $user_id
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


		//Query current user ID i user_departments and get the associated department ID
		$query = new Query();
		
		if($user_id) {
			$sql = "SELECT department_id FROM ".SITE_DB.".user_department WHERE user_id = $user_id";

			if($query->sql($sql)) {
				$department_id = $query->result(0,"department_id");
				// print_r ($department_id);

				//Use getDepartment to find the department with the specified department ID.
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


	// TODO: should be implemented later
	function updateUserDepartment($action) {
		// Get content of $_POST array that have been "quality-assured" by Janitor
		$this->getPostedEntities();

		// Check that the number of REST parameters is as expected and that the listed entries are valid.
		if(count($action) == 2 && $this->validateList(array("department_id"))) {

			$user_id = $action[1];
			$department_id = $this->getProperty("department_id", "value");

			$query = new Query();

			$query->checkDbExistence(SITE_DB.".user_department");

			$user_department = $this->getUserDepartment(array("user_id" => $user_id));

			//Check if the user is associated with a department and adjust query accordingly
			if ($user_department) {
				//Update department
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


	// shop clerk creates new user via member help
	function newUserFromMemberHelp($action) {

		global $page;
		$page->addLog("user->newUserFromMemberHelp: initiated");

		// only attempt user creation if signups are allowed for this site
		if(defined("SITE_SIGNUP") && SITE_SIGNUP) {

			// Get posted values to make them available for models
			$this->getPostedEntities();
			$terms = $this->getProperty("terms", "value");
			$email = $this->getProperty("email", "value");


			// if user hasn't accepted terms
			if(!$terms) {
				$page->addLog("user->newUserFromMemberHelp: missing terms agreement");
				return array("status" => "MISSING_TERMS");
			}


			// if user already exists, return error
			if($this->userExists(array("email" => $email))) {
				$page->addLog("user->newUserFromMemberHelp: user exists ($email)");
				return array("status" => "USER_EXISTS");
			}


			// does values validate - minimum is email and nickname
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

				// if no nickname were posted, use email
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


				// add member user group
				$values[] = "user_group_id=2";


				$sql = "INSERT INTO ".$this->db." SET " . implode(",", $values);
				// print $sql."<br>\n";
				if($query->sql($sql)) {

					$user_id = $query->lastInsertId();


					// Gererate verification code
					$verification_code = randomKey(8);

					// add email to user_usernames
					$sql = "INSERT INTO $this->db_usernames SET username = '$email', verified = 0, verification_code = '$verification_code', type = 'email', user_id = $user_id";
					// print $sql."<br>\n";
					if($query->sql($sql)) {


						$mobile = $this->getProperty("mobile", "value");
						if($mobile) {
							$sql = "INSERT INTO $this->db_usernames SET username = '$mobile', verified = 1, verification_code = '$verification_code', type = 'mobile', user_id = $user_id";
			//				print $sql;
							$query->sql($sql);
						}


						// user can send password on signup
						$raw_password = $this->getProperty("password", "value");
						$mail_password = "******** (password is encrypted)";

						// if raw password was not sent - set temp password and include it in activation email
						if(!$raw_password || $raw_password == "Password") {
							// add temp password
							$raw_password = randomKey(8);
							$mail_password = $raw_password." (autogenerated password)";
						}

						// encrypt password
						$password = password_hash($raw_password, PASSWORD_DEFAULT);
						$sql = "INSERT INTO ".$this->db_passwords." SET user_id = $user_id, password = '$password'";
						// password added successfully
						if($query->sql($sql)) {

							// store signup email for receipt page
							session()->value("signup_email", $email);



							// VERIFICATION EMAIL

							// add log
							$page->addLog("user->newUserFromMemberHelp: created: " . $email . ", user_id:$user_id");

							// success
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
							// error
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

							// maillist subscription sent as string?
							$maillist = getPost("maillist");
							if($maillist) {
								// check if maillist exists
								$maillists = $page->maillists();
								$maillist_match = arrayKeyValue($maillists, "name", $maillist);
								if($maillist_match !== false) {
									$maillist_id = $maillists[$maillist_match]["id"];
									$_POST["maillist_id"] = $maillist_id;

									// add maillist for current user
									$this->addMaillist(array("addMaillist"));
								}

								// ignore subscription if maillist does not exist

							}

							// itemtype post save handler?
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
		}

		$page->addLog("user->newUserFromMemberHelp failed: (missing info)");
		return false;
	}


}

?>
