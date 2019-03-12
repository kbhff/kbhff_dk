<?php
/**
* Foodnet platform
* Copyright (C) 2018  Københavns Fødevarefællesskab and think.dk
*
* Københavns Fødevarefællesskab
* KPH-Projects
* Enghavevej 80 C, 3. sal
* 2450 København SV
* Denmark
* mail: bestyrelse@kbhff.dk
*
* think.dk
* Æbeløgade 4
* 2100 København Ø
* Denmark
* mail: start@think.dk
*
* This source code is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This source code is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this source code.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
* @package janitor.users
* This file contains simple user extensions
* Meant to allow local user additions/overrides
*/

/**
* User customization class
*/
class User extends UserCore {


	/**
	* Init, set varnames, validation rules
	*/
	function __construct() {

		parent::__construct(get_class());

		// Reset token, used in "user forgot password" flow
		$this->addToModel("reset-token", array(
			"type" => "string",
			"label" => "Kode",
			"required" => true,
			"pattern" => "^[0-9A-Za-z]{24}$",
			"hint_message" => "Din verificeringskode",
			"error_message" => "Ugyldig kode. Kunne der være mellemrum i enden af din indtastede kode?"
		));

		// Department ID, in order to change users department
		$this->addToModel("department_id", array(
			"type" => "string",
			"label" => "Afdeling",
			"required" => true,
			"hint_message" => "Vælg en afdeling",
			"error_message" => "Du skal vælge en afdeling."
		));
		// Search field in order to search for members
		 $this->addToModel("search_member", array(
		 	"type" => "string",
		 	"label" => "Søg blandt medlemmer her", 
		 	"hint_message" => "Navn, email, mobilnr eller medlemsnr",
		 	"error_message" => "Du skal som minimum angive 4 tegn."
		 ));
		 
	}

	/**
	 * Callback that saves user department after saving user
	 *
	 * @param integer $user_id
	 * @return void
	 */
	function saved($user_id) {
		$this->updateUserDepartment(["updateUserDepartment"]);
	}

	/**
	 * Check if reset-token is correct
	 *
	 * @param array $action REST parameters
	 * @return string|false 
	 */
	function validateCode($action) {
		// get posted variables
		$this->getPostedEntities();
		$token = $this->getProperty("reset-token", "value");

		// correct information available
		if(count($action) == 1 && $this->checkResetToken($token)) {
			return $token;
		}

		// Error
		return false;
	}

	// reset password using reset-token
	function resetPassword($action) {

		// perform cleanup routine
		$this->cleanUpResetRequests();

		// get posted variables
		$this->getPostedEntities();

		$reset_token = getPost("reset-token");
		$new_password = password_hash($this->getProperty("new_password", "value"), PASSWORD_DEFAULT);

		// correct information available
		if(count($action) == 1 && $new_password && $this->checkResetToken($reset_token)) {

			$query = new Query();

			// get user_id for reset token
			$sql = "SELECT user_id FROM ".$this->db_password_reset_tokens." WHERE token = '$reset_token'";
			if($query->sql($sql)) {

				// get user id
				$user_id = $query->result(0, "user_id");
				session()->value("user_id", $user_id);

				if(!$this->hasPassword()) {
					
					// SAVE NEW PASSWORD
					$sql = "INSERT INTO ".$this->db_passwords." SET user_id = $user_id, password = '$new_password'";
					if($query->sql($sql)) {

						// send notification email to admin
						// TODO: consider disabling this once it has proved itself worthy
						mailer()->send(array(
							"subject" => "Password was created: " . $user_id,
							"message" => "Check out the user: " . SITE_URL . "/janitor/admin/user/edit/" . $user_id
						));

						message()->addMessage("Password created");
						return true;
					}

				}



				// delete token (a token can only be used once)
				$sql = "DELETE FROM ".$this->db_password_reset_tokens." WHERE token = '$reset_token'";
				$query->sql($sql);


				// DELETE OLD PASSWORD
				$sql = "DELETE FROM ".$this->db_passwords." WHERE user_id = $user_id";
				if($query->sql($sql)) {

					// SAVE NEW PASSWORD
					$sql = "INSERT INTO ".$this->db_passwords." SET user_id = $user_id, password = '$new_password'";
					if($query->sql($sql)) {

						// send notification email to admin
						// TODO: consider disabling this once it has proved itself worthy
						mailer()->send(array(
							"subject" => "Password was resat: " . $user_id,
							"message" => "Check out the user: " . SITE_URL . "/janitor/admin/user/edit/" . $user_id
						));

						message()->addMessage("Password updated");
						return true;
					}
				}

			}

		}

		return false;
	}
	

	/**
	 * Get the current user, including associated department.
	 *
	 * @return array The user object, with the department object appended as a new property.
	 */
	 function getKbhffUser(){
		$user = $this->getUser();
		$user["department"] = $this->getUserDepartment();
		// print_r($user);

		return $user;
	}

	/**
	 * Get the current user's associated department.
	 *
	 * @param int $user_id
	 * @return array|false The department object, or false if the current user isn't associated with a department.
	 */
	function getUserDepartment($_options=false){

		$user_id = session()->value("user_id");

		//Query current user ID i user_departments and get the associated department ID
		$query = new Query();
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

		return false;

	}

	/**
	 * Update or set the current user's associated department.
	 *
	 * @param array $action REST parameters of current request
	 * @return boolean
	 */
	function updateUserDepartment($action){
		// Get content of $_POST array that have been mapped to the model entities object
		$this->getPostedEntities();

		print_r ($action);
		// Check that the number of REST parameters is as expected and that the listed entries are valid.
		if(count($action) == 1 && $this->validateList(array("department_id"))) {

			$user = $this->getKbhffUser();
			$user_id = $user["id"];
			$department_id = $this->getProperty("department_id", "value");

			$query = new Query();

			$query->checkDbExistence(SITE_DB.".user_department");



			//Check if the user is associated with a department and adjust query accordingly
			if ($user["department"]) {
				//Update department
				$sql = "UPDATE ".SITE_DB.".user_department SET department_id = $department_id WHERE user_id = $user_id";

				if($query->sql($sql)) {
					message()->addMessage("Afdeling blev opdateret.");
					return true;
				}
			}
			else {
				// Set department
				$sql = "INSERT INTO ".SITE_DB.".user_department SET department_id = $department_id, user_id = $user_id";
				if($query->sql($sql)) {
					message()->addMessage("Afdeling blev tildelt.");
					return true;
				}
			}

		}

		return false;

	}

	/**
	 * Remove current user's acceptance of terms – for testing purposes
	 *
	 * @return boolean
	 */
	function unacceptTerms() {

		$user_id = session()->value("user_id");

		$query = new Query();

		$sql = "DELETE FROM ".SITE_DB.".user_log_agreements WHERE user_id = $user_id";

		if($query->sql($sql)) {
			return true;
		}

		return false;
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

		// Prevent "nickname not assigned" error
		$nickname = $this->getProperty("nickname", "value");
		if (!$nickname) {
			$firstname = $this->getProperty("firstname", "value");
			$lastname = $this->getProperty("lastname", "value");
			$_POST["nickname"] = $firstname . " " . $lastname;
		}

		// Updates and checks if it went true(good) or false(bad)
		if ($this->update(["update"])) {
			message()->addMessage("Dine oplysninger blev opdateret");
		}
		else {
			message()->addMessage("Opdateringen slog fejl", ["type" => "error"]);
		}

		return true;
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
		$user_id = session()->value("user_id");

		if(count($action) == 1 && $user_id) {
			// If user already has a password
			if($this->hasPassword()) {
				// does values validate
				if($this->validateList(array("new_password", "old_password"))) {
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
				else {
					message()->addMessage("Forkert password", array("type" => "error"));
					return false;
				}
			}
			// user does not have a password
			else {
				// does values validate
				if($this->validateList(array("new_password"))) {
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

	/**
	 * Delete kbhff account
	 *
	 * @param array $action REST parameters
	 * @return boolean
	 */
	function deleteUserInformation($action) {

		$user = $this->getKbhffUser();
		$user_email = $user["email"];

		$cancel_result = $this->cancel(["cancel"]);

		// if cancel goes through and returns true then send a mail
		if ($cancel_result === true) {
			message()->addMessage("Dine oplysninger blev slettet");
			mailer()->send([
				"subject" => "Dit medlemskab af Københavns Fødevarefællesskab er opsagt",
				"message" => "Du har meldt dig ud af Københavns Fødevarefællesskab. Tak for denne gang.",
				"recipients" => [$user_email]
				]);

			return true;
		}
		// Cannot cancel account due to unpaid orders
		else if(isset($cancel_result["error"]) && $cancel_result["error"] == "unpaid_orders") {
			message()->addMessage("Du kan ikke udmelde dig, da du har ubetalte ordrer. Du er velkommen til at kontakte it@kbhff.dk, der altid står klar til at hjælpe.", array("type" => "error"));
			return false;

		}
		// Cannot cancel account due to wrong password
		else if(isset($cancel_result["error"]) && $cancel_result["error"] == "wrong_password") {
			message()->addMessage("Du kan ikke udmelde dig, da du har angivet et forkert password.", array("type" => "error"));
			return false;

		}
		// Any unknown error
		else {
			message()->addMessage("Udmeldelsen slog fejl", ["type" => "error"]);
			return false;
		}

		//PERHAPS TODO: delete department affiliation

	}

	/**
	 * Infer user_id from username for user that is not yet fully logged in.
	 *
	 * @param string $username
	 * @return int|false $user_id
	 */
	function getLoginUserId($username) {

		$query = new Query();

	
		// Infer user_id from username
		$sql = "SELECT user_id FROM ".$this->db_usernames." WHERE username = '$username'";
		if($query->sql($sql)) {

			$user_id = $query->result(0, "user_id");
			return $user_id;

		}

		// no user found
		return false;
	}

	/**
	 * Check if username is verified for user that is not yet fully logged in.
	 *
	 * @param string $username
	 * @return boolean
	 */
	function loginUserIsVerified($username, $user_id) {		
		
		$query = new Query();
		
		// user is not a guest user
		if($user_id != 1) {
			$sql = "SELECT user_id FROM ".SITE_DB.".user_usernames WHERE user_id = $user_id AND username = '$username' AND verified = 1";			
			// user is verified
			if($query->sql($sql)) {
				return true;
			}
		}

		// user is not verified
		return false;

	}

	/**
	 * Check if not-yet-fully-logged-in user has a password
	 *
	 * @param string $user_id
	 * @param array $_options
	 * 
	 * @return boolean
	 */
	function loginUserHasPassword($user_id, $_options = false) {

		$include_empty = false;

		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {
					case "include_empty"     : $include_empty       = $_value; break;
				}
			}
		}

		$query = new Query();

		// user is not a guest user
		if($user_id != 1) {
						
			// user has password
			$sql = "SELECT id FROM ".$this->db_passwords." WHERE user_id = $user_id" . ($include_empty ? " AND (password != '' OR upgrade_password != '')" : "");
			if($query->sql($sql)) {
				return true;
			}
		}

		// user has no password
		return false;

	}


	

	/**
	 * Set password for verified user that does not yet have a password (e.g. was signed up via member-help)
	 * 
	 * @param array $action
	 *
	 * @return array|false via callback to confirmUsername()
	 */
	function setFirstPassword() {
	
		// Get posted values and session values to make them available for models
		$this->getPostedEntities();
		$username = session()->value("temp-username");
		$user_id = $this->getLoginUserId($username);

		// user already has a password
		if($this->loginUserHasPassword($user_id)) {

			return array("status" => "HAS_PASSWORD");

		}

		// user does not have a password
		else {

			// does values validate
			if($this->validateList(array("new_password"))) {


				$query = new Query();

				// make sure type tables exist
				$query->checkDbExistence($this->db_passwords);

				// create hash to inject
				$new_password = password_hash($this->getProperty("new_password", "value"), PASSWORD_DEFAULT);

				// save new password
				$sql = "INSERT INTO ".$this->db_passwords." SET user_id = $user_id, password = '$new_password'";
				if($query->sql($sql)) {
					return true;
				}
			}

		}

		// error
		return false;
	}

	// start reset password procedure
	function requestPasswordReset($action) {

		// perform cleanup routine
		$this->cleanUpResetRequests();

		// get posted variables
		$this->getPostedEntities();
		$username = $this->getProperty("username", "value");

		// correct information available
		if(count($action) == 1 && $username) {

			$query = new Query();

			// make sure type tables exist
			$query->checkDbExistence($this->db_password_reset_tokens);


			// find the user with specified username
			$sql = "SELECT user_id FROM ".$this->db_usernames." WHERE username = '$username'";
			if($query->sql($sql)) {

				// user_id
				$user_id = $query->result(0, "user_id");


				// find email for this user
				$sql = "SELECT username FROM ".$this->db_usernames." WHERE user_id = '$user_id' AND type = 'email'";
				if($query->sql($sql)) {

					// email
					$email = $query->result(0, "username");

					// create reset token
					$reset_token = randomKey(24);
					

					// insert reset token
					$sql = "INSERT INTO ".$this->db_password_reset_tokens." VALUES(DEFAULT, $user_id, '$reset_token', '".date("Y-m-d H:i:s")."')";
					if($query->sql($sql)) {
						
						$sql = "SELECT nickname FROM ".$this->db." WHERE id = '$user_id'";
						
						if($query->sql($sql)) {
							
							// nickname 
							$nickname = $query->result(0, "nickname");
						

							// send email
							mailer()->send(array(
								"values" => array(
									"TOKEN" => $reset_token,
									"NICKNAME" => $nickname
								),
								"track_clicks" => false,
								"recipients" => $email,
								"template" => "reset_password"
							));

							// send notification email to admin
							// TODO: consider disabling this once it has proved itself worthy
							mailer()->send(array(
								"subject" => "Password reset requested: " . $email,
								"message" => "Check out the user: " . SITE_URL . "/janitor/admin/user/edit/" . $user_id,
								"template" => "system"
							));

							return true;
						}
					}

				}

			}

		}

		// user could not be found or reset request could not be satisfied
		// - but this is not reflected towards to user to avoid revealing user existence
		// - standard error message created in login-controller
		return false;
	}



}

?>
