<?php
$access_item["/"] = true;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$action = $page->actions();
$IC = new Items();

set_time_limit(0);

$page->bodyClass("restructure");
$page->pageTitle("Restructure tool");


if(is_array($action) && count($action)) {

	// LIST/EDIT/NEW ITEM
	if(preg_match("/^(done)$/", $action[0])) {

		$page->page(array(
			"type" => "janitor",
			"templates" => "janitor/restructure/done.php"
		));
		exit();
	}

	// Class interface
	else if($page->validateCsrfToken() && preg_match("/[run]+/", $action[0])) {

		include_once("classes/system/upgrade.class.php");
		$UC = new Upgrade();


		$query = new Query();


		// Code Igniter upgrades

		// (2.0.3)
		// CREATE INDEX last_activity_idx ON ci_sessions(last_activity); 
		$UC->addKey(SITE_DB.".ff_sessions", "last_activity", "last_activity_idx");

		// ALTER TABLE ci_sessions MODIFY user_agent VARCHAR(120);
		$UC->modifyColumn(SITE_DB.".ff_sessions", "user_agent", "VARCHAR(120)");

		// (2.1.1)
		// ALTER TABLE ci_sessions CHANGE ip_address ip_address varchar(45) default '0' NOT NULL
		$UC->modifyColumn(SITE_DB.".ff_sessions", "ip_address", "VARCHAR(45) DEFAULT '0' NOT NULL");



		// DELETE UNUSED TABLES

		// ff_zipcodes
		$UC->dropTable(SITE_DB.".ff_zipcodes");

		// ff_jobs
		$UC->dropTable(SITE_DB.".ff_jobs");

		// ff_chores
		$UC->dropTable(SITE_DB.".ff_chores");

		// ff_xfer
		$UC->dropTable(SITE_DB.".ff_xfer");

		// fornavne
		$UC->dropTable(SITE_DB.".fornavne");

		// piger
		$UC->dropTable(SITE_DB.".piger");

		// unisex
		$UC->dropTable(SITE_DB.".unisex");

		// drenge
		$UC->dropTable(SITE_DB.".drenge");



		function getUser($puid) {
			$query = new Query();
			$sql = "SELECT * FROM kbhff_dk.ff_persons WHERE uid = $puid";
			if($query->sql($sql)) {
				return $query->result(0);
			}
			return false;
		}


		function hasGroupMembership($puid) {
			$query = new Query();
			$sql = "SELECT * FROM kbhff_dk.ff_groupmembers WHERE puid = $puid";
			if($query->sql($sql)) {
				return $query->results();
			}
			return false;
		}

		function hasMailAliases($puid) {
			$query = new Query();
			$sql = "SELECT * FROM kbhff_dk.ff_mail_aliases WHERE puid = $puid";
			if($query->sql($sql)) {
				return $query->results();
			}
			return false;
		}

		function hasMembernote($puid) {
			$query = new Query();
			$sql = "SELECT * FROM kbhff_dk.ff_membernote WHERE puid = $puid";
			if($query->sql($sql)) {
				return $query->results();
			}
			return false;
		}

		function hasOrderHead($puid) {
			$query = new Query();
			$sql = "SELECT * FROM kbhff_dk.ff_orderhead WHERE puid = $puid";
			if($query->sql($sql)) {
				return $query->results();
			}
			return false;
		}

		function hasOrderLines($puid) {
			$query = new Query();
			$sql = "SELECT * FROM kbhff_dk.ff_orderlines WHERE puid = $puid";
			if($query->sql($sql)) {
				return $query->results();
			}
			return false;
		}

		function hasPersonInfo($puid) {
			$query = new Query();
			$sql = "SELECT * FROM kbhff_dk.ff_persons_info WHERE puid = $puid";
			if($query->sql($sql)) {
				return $query->results();
			}
			return false;
		}

		function hasRoles($puid) {
			$query = new Query();
			$sql = "SELECT * FROM kbhff_dk.ff_roles WHERE puid = $puid";
			if($query->sql($sql)) {
				return $query->results();
			}
			return false;
		}

		function hasTransactions($puid) {
			$query = new Query();
			$sql = "SELECT * FROM kbhff_dk.ff_transactions WHERE puid = $puid";
			if($query->sql($sql)) {
				return $query->results();
			}
			return false;
		}


		function isValidLastname($lastname) {
			
			if($lastname && !preg_match("/Udmeld|Ukendt|Slettet|Ledig|fejl/i", $lastname)) {
				return true;
			}

			return false;
		}

		function isValidFirstname($firstname) {
			
			if($firstname && !preg_match("/Udmeld|^\* |Ukendt|xx|^00|zero|Slettet|N\/A|Ledig|fejl/i", $firstname)) {
				return true;
			}

			return false;
		}


		// Check if user email apears to be valid
		function isValidEmail($email) {
			
			if($email && !preg_match("/a@a.dk|udmeldt|slettet|i@dont\.know|rrr@rrr|fejl@fejl|0@0/i", $email)) {
				return true;
			}

			return false;
		}

		// Check if user phone apears to be valid
		function isValidPhone($phone) {
			
			if($phone && is_numeric(preg_replace("/[+ \-]/", "", $phone)) && !preg_match("/^(50239738|00000000|11111111|12345678|11|00112233|01234567|12211221|22222222|33333333|44444444|55555555|66666666|77777777|88888888|99999999|xxxxxxxx)$/i", $phone)) {
				return true;
			}

			return false;
		}


		function hasDoubleEntries($puid) {
			$query = new Query();
			$sql = "SELECT * FROM kbhff_dk.ff_persons WHERE uid = $puid";

			$match = false;

			if($query->sql($sql)) {
				$result = $query->result(0);
				$tel = $result["tel"];
				$tel2 = $result["tel2"];
				$email = $result["email"];


				// check for other members with same email / phone
				if(isValidEmail($email) || isValidPhone($tel) || isValidPhone($tel2)) {
					$sql = "SELECT uid FROM kbhff_dk.ff_persons WHERE uid != $puid AND (";

					if(isValidEmail($email)) {
						$sql .= "email = '$email'";
					}
					if(isValidPhone($tel) && isValidEmail($email)) {
						$sql .= " OR ";
					}
					if(isValidPhone($tel)) {
						$sql .= "tel = '$tel' OR tel2 = '$tel'";
					}
					if(isValidPhone($tel2) && (isValidPhone($tel) || isValidEmail($email))) {
						$sql .= " OR ";
					}
					if(isValidPhone($tel2)) {
						$sql .= "tel = '$tel2' OR tel2 = '$tel2'";
					}


//		. $email ? "email = '$email' " . ($tel ? "OR tel = '$tel' OR tel2 = '$tel'" : "") OR tel = '$tel2' OR tel2 = '$tel2'
					$sql .= ")";
					// print $sql;
					// exit;
					if($query->sql($sql)) {
//						print "MEMBERS:" .$puid."<br>\n"; 
						$match["members"] = $query->results();
					}
				}

				if(isValidEmail($email)) {
					$sql = "SELECT * FROM kbhff_dk.mail_aliases WHERE puid != $puid AND alias = '$email'";
					if($query->sql($sql)) {
						$match["aliases"] = $query->results();
					}
				}
			}
			return $match;
		}


		// CHECK IF MEMBER IS READY FOR DELETION
		// MEMBER ACTIVE = 'X'|''
		// NO MEMBER ORDERS, ORDERLINES OR TRANSACTIONS
		function shouldUserBeDeleted($puid) {
			$query = new Query();
			$sql = "SELECT * FROM kbhff_dk.ff_persons WHERE uid = $puid";
			if($query->sql($sql)) {
				$result = $query->result(0);
				if(
					(
						!$result["active"] || 
						preg_match("/^(X)$/i", $result["active"])
					)
					&& 
					(
						// Make sure to exclude KBHFF ADMIN users
						!preg_match("/^KBHFF/i", $result["firstname"]) &&
						!hasOrderHead($puid) && !hasOrderLines($puid) && !hasTransactions($puid)
					)
				) {
					return true;
				}

			}
			return false;
		}


		// Look for invalid input in user profiles
		function shouldUserProbablyBeDeleted($puid) {
			$query = new Query();
			$sql = "SELECT * FROM kbhff_dk.ff_persons WHERE uid = $puid";
			if($query->sql($sql)) {
				$result = $query->result(0);
				if(
					(
						($result["firstname"] && !isValidFirstname($result["firstname"])) ||
						($result["lastname"] && !isValidLastname($result["lastname"])) ||
						($result["tel"] && !isValidPhone($result["tel"])) ||
						($result["email"] && !isValidEmail($result["email"]))
					)		
					&& 
					(
						// Make sure to exclude KBHFF ADMIN users
						!preg_match("/^KBHFF/i", $result["firstname"]) &&
						!hasOrderHead($puid) && !hasOrderLines($puid) && !hasTransactions($puid)
					)
				) {
					// output($puid);
					// output($result["firstname"] && !isValidFirstname($result["firstname"]) ? "firstname" : "");
					// output($result["lastname"] && !isValidLastname($result["lastname"]) ? "lastname" : "");
					// output($result["tel"] && !isValidPhone($result["tel"]) ? "tel" : "");
					// output($result["email"] && !isValidEmail($result["email"]) ? "email" : "");

					return true;
				}

			}
			return false;
		}




		// CHECK IF MEMBER IS READY FOR DELETION
		// MEMBER ACTIVE = 'X'|''
		// NO MEMBER ORDERS, ORDERLINES OR TRANSACTIONS

		function shouldUserBeAnonymized($puid) {
			$query = new Query();
			$sql = "SELECT * FROM kbhff_dk.ff_persons WHERE uid = $puid";
			if($query->sql($sql)) {
				$result = $query->result(0);
				if(
					(
						(
							!$result["active"] || 
							preg_match("/^(X)$/i", $result["active"])
						)
					) 
					&& 
					(
					hasOrderHead($puid) || hasOrderLines($puid) || hasTransactions($puid)
					)
				) {
					return true;
				}

			}
			return false;
		}
		function isPassive($puid) {
			$query = new Query();
			$sql = "SELECT * FROM kbhff_dk.ff_persons WHERE uid = $puid";
			if($query->sql($sql)) {
				$result = $query->result(0);
				if(
					(
						preg_match("/^(no)$/i", $result["active"])
					) 
				) {
					return true;
				}

			}
			return false;
		}
		function isActive($puid) {
			$query = new Query();
			$sql = "SELECT * FROM kbhff_dk.ff_persons WHERE uid = $puid";
			if($query->sql($sql)) {
				$result = $query->result(0);
				if(
					(
						preg_match("/^(yes|paid)$/i", $result["active"])
					) 
				) {
					return true;
				}

			}
			return false;
		}


		function output($string) {
			print '<p>'.$string.'</p>';
		}


		function showMember($user) {

			$_ = "";
			$_ .= $user["firstname"] ? $user["firstname"]."," : "";
			$_ .= $user["middlename"] ? $user["middlename"]."," : "";
			$_ .= $user["lastname"] ? $user["lastname"]."," : "";
			$_ .= $user["email"] ? $user["email"]."," : "";
			$_ .= $user["tel"] ? $user["tel"]."," : "";
			$_ .= $user["tel2"] ? $user["tel2"]."," : "";
			$_ .= "ACTIVE:" . ($user["active"] ? $user["active"]."," : "");
			$_ .= "UID:" . ($user["uid"] ? $user["uid"]."," : "");
			output($_);
		}

		// delete member row
		// delete from any other table containing puid (except transaction tables)
		function deleteMember($puid) {

			// $user = getUser($puid);
			// showMember($user);

			if($puid) {
				$query = new Query();


				$sql1 = "SELECT puid FROM kbhff_dk.ff_orderhead WHERE puid = $puid";
				$sql2 = "SELECT puid FROM kbhff_dk.ff_transactions WHERE puid = $puid";

				if(!$query->sql($sql1) && !$query->sql($sql2)) {

					$sql = "DELETE FROM kbhff_dk.ff_persons WHERE uid = $puid";
					$query->sql($sql);

					$sql = "DELETE FROM kbhff_dk.ff_groupmembers WHERE puid = $puid";
					$query->sql($sql);

					$sql = "DELETE FROM kbhff_dk.ff_mail_aliases WHERE puid = $puid";
					$query->sql($sql);

					$sql = "DELETE FROM kbhff_dk.ff_membernote WHERE puid = $puid";
					$query->sql($sql);

					$sql = "DELETE FROM kbhff_dk.ff_persons_info WHERE puid = $puid";
					$query->sql($sql);

					$sql = "DELETE FROM kbhff_dk.ff_roles WHERE puid = $puid";
					$query->sql($sql);

					$sql = "DELETE FROM kbhff_dk.ff_division_members WHERE member = $puid";
					$query->sql($sql);

					output("DELETE:" . $puid);

				}
				else {

					output("COUND NOT DELETE:" . $puid);

				}

			}
			
		}

		// Change puid to active_puid
		function mergeUserIntoUser($puid, $active_puid) {

			$user = getUser($puid);
			// showMember($user);
			//
			// output(" INTO ");
			//
			$active_user = getUser($active_puid);
			// showMember($active_user);

			if($user && $active_user) {
				$query = new Query();


				$sql = "UPDATE kbhff_dk.ff_orderhead set puid = $active_puid WHERE puid = $puid";
				$query->sql($sql);

				$sql = "UPDATE kbhff_dk.ff_orderlines set puid = $active_puid WHERE puid = $puid";
				$query->sql($sql);

				$sql = "UPDATE kbhff_dk.ff_transactions set puid = $active_puid WHERE puid = $puid";
				$query->sql($sql);


				$sql = "DELETE FROM kbhff_dk.ff_persons WHERE uid = $puid";
				$query->sql($sql);

				$sql = "DELETE FROM kbhff_dk.ff_groupmembers WHERE puid = $puid";
				$query->sql($sql);

				$sql = "DELETE FROM kbhff_dk.ff_mail_aliases WHERE puid = $puid";
				$query->sql($sql);

				$sql = "DELETE FROM kbhff_dk.ff_membernote WHERE puid = $puid";
				$query->sql($sql);

				$sql = "DELETE FROM kbhff_dk.ff_persons_info WHERE puid = $puid";
				$query->sql($sql);

				$sql = "DELETE FROM kbhff_dk.ff_roles WHERE puid = $puid";
				$query->sql($sql);

				$sql = "DELETE FROM kbhff_dk.ff_division_members WHERE member = $puid";
				$query->sql($sql);


				output("MERGE: " . $puid . " INTO " . $active_puid);

			}
			else {

				output("COUND NOT MERGE:" . $puid);

			}

		}

		// Change puid to active_puid
		function anonymizeMember($puid) {

			$user = getUser($puid);
			// showMember($user);

			if($user) {
				$query = new Query();

				$sql = "UPDATE kbhff_dk.ff_persons set ";
				$sql .= "firstname=NULL, ";
				$sql .= "middlename='', ";
				$sql .= "lastname=NULL, ";
				$sql .= "sex=NULL, ";
				$sql .= "adr1='', ";
				$sql .= "adr2='', ";
				$sql .= "streetno='', ";
				$sql .= "floor='', ";
				$sql .= "door='', ";
				$sql .= "adr3='', ";
				$sql .= "zip='', ";
				$sql .= "city='', ";
				$sql .= "country='', ";
				$sql .= "languagepref=NULL, ";
				$sql .= "tel='', ";
				$sql .= "tel2='', ";
				$sql .= "email=NULL, ";
				$sql .= "birthday=NULL, ";
				$sql .= "user_activation_key=NULL, ";
				$sql .= "password=NULL, ";
				$sql .= "status1=NULL, ";
				$sql .= "status2='deleted', ";
				$sql .= "status3=NULL, ";
				$sql .= "rights=NULL, ";
				$sql .= "privacy='', ";
				$sql .= "ownupdate=NULL, ";
				$sql .= "last_login='0000-00-00 00:00:00', ";

				$sql .= "active='X' WHERE uid = $puid";
				$query->sql($sql);

				$sql = "DELETE FROM kbhff_dk.ff_groupmembers WHERE puid = $puid";
				$query->sql($sql);

				$sql = "DELETE FROM kbhff_dk.ff_mail_aliases WHERE puid = $puid";
				$query->sql($sql);

				$sql = "DELETE FROM kbhff_dk.ff_membernote WHERE puid = $puid";
				$query->sql($sql);

				$sql = "DELETE FROM kbhff_dk.ff_persons_info WHERE puid = $puid";
				$query->sql($sql);

				$sql = "DELETE FROM kbhff_dk.ff_roles WHERE puid = $puid";
				$query->sql($sql);

				$sql = "DELETE FROM kbhff_dk.ff_division_members WHERE member = $puid";
				$query->sql($sql);


				output("ANONYMIZE: " . $puid);

			}
			else {

				output("COUND NOT ANONYMIZE:" . $puid);

			}

		}

		function deleteOlderEmail($user, $match_user) {

			$query = new Query();

			$orders = hasOrderHead($user["uid"]);
			$last_order = 0;
			if($orders) {
				$last_order = array_pop($orders);
			}

			$match_orders = hasOrderHead($match_user["uid"]);
			$match_last_order = 0;
			if($match_orders) {
				$match_last_order = array_pop($match_orders);
			}


			if(
				(strtotime($user["last_login"]) >= strtotime($match_user["last_login"]))
				&&
				(strtotime($last_order["created"]) > strtotime($match_last_order["created"]))
			) {

				output("EMAIL DELETED ON ".$match_user["uid"]);

				// DELETE EMAIL ON $match["uid"]
				$sql = "UPDATE kbhff_dk.ff_persons set email = NULL WHERE uid = ".$match_user["uid"];
				$query->sql($sql);

			}
			else {

				output("EMAIL DELETED ON ".$user["uid"]);

				// DELETE EMAIL ON $puid
				$sql = "UPDATE kbhff_dk.ff_persons set email = NULL WHERE uid = ".$user["uid"];
				$query->sql($sql);

			}

		}

		function deleteOlderPhone($user, $match_user) {

			$query = new Query();

			$orders = hasOrderHead($user["uid"]);
			$last_order = 0;
			if($orders) {
				$last_order = array_pop($orders);
			}

			$match_orders = hasOrderHead($match_user["uid"]);
			$match_last_order = 0;
			if($match_orders) {
				$match_last_order = array_pop($match_orders);
			}

			if(
				(strtotime($user["last_login"]) >= strtotime($match_user["last_login"]))
				&&
				(strtotime($last_order["created"]) > strtotime($match_last_order["created"]))
			) {

				output("PHONE DELETED ON ".$match_user["uid"]);

				// DELETE PHONE ON $match["uid"]
				$sql = "UPDATE kbhff_dk.ff_persons set tel = NULL WHERE uid = ".$match_user["uid"];
				$query->sql($sql);

			}
			else {

				output("PHONE DELETED ON ".$user["uid"]);

				// DELETE PHONE ON PUID
				$sql = "UPDATE kbhff_dk.ff_persons set tel = NULL WHERE uid = ".$user["uid"];
				$query->sql($sql);

			}
		}



		// GET USERS THAT ARE READY TO BE DELETED
		function getDeletableMembers() {

			$should_be_deleted = [];

			$query = new Query();
			$sql = "SELECT uid FROM kbhff_dk.ff_persons";
			if($query->sql($sql)) {
			
				$results = $query->results();
				foreach($results as $result) {
					$puid = $result["uid"];

					if(shouldUserBeDeleted($puid)) {
						$should_be_deleted[] = $puid;
					}
				}

			}
			return $should_be_deleted;
		}

		// GET USERS THAT ARE (ALSO) READY TO BE DELETED
		function getProbablyDeletableMembers() {

			$users = [];

			$query = new Query();
			$sql = "SELECT uid FROM kbhff_dk.ff_persons";
			if($query->sql($sql)) {
			
				$results = $query->results();
				foreach($results as $result) {
					$puid = $result["uid"];

					if(!isActive($puid) && shouldUserProbablyBeDeleted($puid)) {
						$users[] = $puid;
					}
				}

			}
			return $users;
		}

		// GET USERS THAT SHOULD BE ANONYMIZED BUT COULD BE MERGED
		function getAnonymizableMembersWithDoubleEntries() {

			$users = [];

			$query = new Query();
			$sql = "SELECT * FROM kbhff_dk.ff_persons";
			if($query->sql($sql)) {
			
				$results = $query->results();
				foreach($results as $result) {
					$puid = $result["uid"];

					if(shouldUserBeAnonymized($puid)) {
						
						$db_entries = hasDoubleEntries($puid);
						if($db_entries) {
							$users[$puid] = $db_entries;
						}
					}
				}

			}
			return $users;
		}

		// GET USERS THAT SHOULD BE ANONYMIZED
		function getAnonymizableMembers() {

			$users = [];

			$query = new Query();
			$sql = "SELECT uid,status2 FROM kbhff_dk.ff_persons WHERE status2 != 'deleted' OR status2 IS NULL";
			if($query->sql($sql)) {
			
				$results = $query->results();
				foreach($results as $result) {
					$puid = $result["uid"];

					if(shouldUserBeAnonymized($puid)) {
						$users[] = $puid;
					}
				}

			}
			return $users;
		}

		// GET PASSIVE USERS THAT COULD BE MERGED
		function getPassiveMembersWithDoubleEntries() {

			$users = [];

			$query = new Query();
			$sql = "SELECT uid FROM kbhff_dk.ff_persons";
			if($query->sql($sql)) {
			
				$results = $query->results();
				foreach($results as $result) {
					$puid = $result["uid"];

					if(isPassive($puid)) {

						$db_entries = hasDoubleEntries($puid);
						if($db_entries) {
							$users[$puid] = $db_entries;
						}
					}
				}

			}
			return $users;
		}

		// GET INVALID USERS WHICH SHOULD BE DELETED, ANONYMIZED OR FIXED
		function getInvalidUsers() {

			$users = [];

			$query = new Query();
			$sql = "SELECT uid,firstname,lastname,tel,tel2,email FROM kbhff_dk.ff_persons";
			if($query->sql($sql)) {
			
				$results = $query->results();
				foreach($results as $result) {
					$puid = $result["uid"];

					if(
						(
							($result["firstname"] && !isValidFirstname($result["firstname"])) ||
							($result["lastname"] && !isValidLastname($result["lastname"])) ||
							($result["tel"] && !isValidPhone($result["tel"])) ||
							($result["tel2"] && !isValidPhone($result["tel2"])) ||
							($result["email"] && !isValidEmail($result["email"]))
						)
					) {
						$users[] = $puid;
					}
				}

			}
			return $users;
		}

		// GET ALL MEMBER WITH DOUBLE ENTRIES
		function getMembersWithDoubleEntries() {

			$users = [];

			$query = new Query();
			$sql = "SELECT * FROM kbhff_dk.ff_persons";
			if($query->sql($sql)) {
			
				$results = $query->results();
				foreach($results as $result) {
					$puid = $result["uid"];

					$db_entries = hasDoubleEntries($puid);
					if($db_entries) {
						$users[$puid] = $db_entries;
					}
				}

			}
			return $users;
		}



		// FIND AND DELETE READYLY "DELETABLE" USERS 
		$operations_1 = true;

		// FIND AND DELETE USERS THAT LOOK LIKE THEY ARE VERY LIKELY "DELETABLE" USERS 
		$operations_2 = true;

		// FIND AND MERGE USERS THAT ARE MARKED FOR DELETION BUT HAS DOUBLE ENTRIES 
		$operations_3 = true;

		// FIND AND ANONYMIZE ALL (REMAINING) DELETED MEMBERS
		$operations_4 = true;

		// FIND AND MERGE PASSIVE MEMBERS WITH DOUBLE ENTRIES
		$operations_5 = true;

		// FIX BROKEN DATASETS
		$operations_6 = true;

		// FIND AND FIX/MERGE/DELETE "DELETABLE" USERS WITH WRONG ACTIVE STATE
		$operations_7 = true;

		// FIND AND MERGE MEMBERS WITH DOUBLE ENTRIES
		$operations_8 = true;



		// TRANSFER ACCOUNTS TO NEW SYSTEM
		$operations_transfer = true;




		// START OPERATIONS




		// FIND READYLY "DELETABLE" USERS 
			// ACTIVE 'X'|' '
			// NO ORDERHEADS
			// NO ORDERLINES
			// NO TRANSACTIONS

			// DOUBLE ENTRY MATCH NOT IMPORTANT, NO RELEVANT INFORMATION TO MERGE FROM THIS USER

		// DELETE ANY MATCHES 
		if($operations_1) {

			$members = getDeletableMembers();
			output("DELETABLE MEMBERS: " . count($members));
			foreach($members as $puid) {

				deleteMember($puid);

			}

		}


		// FIND USERS THAT LOOK LIKE THEY MIGHT BE "DELETABLE" USERS 
			// NO ORDERHEADS
			// NO ORDERLINES
			// NO TRANSACTIONS
			
			// INVALID EMAIL, PHONE, FIRSTNAME, LASTNAME

		// DELETE ANY MATCHES 
		if($operations_2) {

			$members = getProbablyDeletableMembers();
			output("OTHER DELETABLE MEMBERS: " . count($members));
			foreach($members as $puid) {

				deleteMember($puid);

			}

		}


		// FIND USERS THAT ARE MARKED FOR DELETION BUT HAS DOUBLE ENTRIES
			// ACTIVE 'X'|' '
			// ORDERS, ORDERLINES OR TRANSACTIONS
			
			// DOUBLE ENTRIES

		// MERGE WITH ACTIVE OR LATEST ENTRY PASSIVE OR LATEST ENTRY (IN THAT ORDER)
		if($operations_3) {

			$members = getAnonymizableMembersWithDoubleEntries();
			output("ANONYMIZABLE MEMBERS WITH DOUBLE ENTRIES - MERGE WITH VALID PROFILES: " . count($members) . " possibilities");
			$merged = [];
			foreach($members as $puid => $entries) {

				// $user = getUser($puid);
				// output("NEW USER");
				// showMember($user);

				if(isset($entries["members"])) {

					$has_passive_id = 0;
					$has_invalid_match = 0;
					foreach($entries["members"] as $match) {
						// Active
						if(isActive($match["uid"]) && $match["uid"] != 1) {

							// output("ACTIVE:" . $match["uid"]);
							// $user = getUser($match["uid"]);
							// showMember($user);

							mergeUserIntoUser($puid, $match["uid"]);
							break;
						}
						// Passive
						else if(isPassive($match["uid"])) {

							// output("PASSIVE:" . $match["uid"]);
							// $user = getUser($match["uid"]);
							// showMember($user);

							// Is this passive ID newer than existing
							$has_passive_id = $has_passive_id < $match["uid"] ? $match["uid"] : $has_passive_id;

						}
						else if($match["uid"] != 1) {

							// output("INVALID MATCH:" . $match["uid"]);

							$has_invalid_match = $has_invalid_match < $match["uid"] ? $match["uid"] : $has_invalid_match;
						}

					}

					// IF PASSIVE MATCH FOUND
					if($has_passive_id) {
						// output("PASSIVE SELECTED:" . $has_passive_id);

						// $user = getUser($has_passive_id);
						// showMember($user);

						mergeUserIntoUser($puid, $has_passive_id);

					}
					// INVALUD
					else if($has_invalid_match && array_search($has_invalid_match, $merged) === false) {
						// output("INVALID SELECTED:" . $has_invalid_match);

						// $user = getUser($puid);
						// showMember($user);
						//
						// output("MERGE INTO");
						//
						// $user = getUser($has_invalid_match);
						// showMember($user);

						$merged[] = $puid;
						mergeUserIntoUser($puid, $has_invalid_match);

					}

				}

			}

		}



		// FIND ALL DELETED MEMBERS
			// ACTIVE 'X'|' '
			// ORDERS, ORDERLINES OR TRANSACTIONS

		// ANONYMIZE ALL MATCHING
		if($operations_4) {

			$members = getAnonymizableMembers();
			output("ANONYMIZABLE MEMBERS: " . count($members));
			foreach($members as $puid) {

				// $user = getUser($puid);
				// showMember($user);

				anonymizeMember($puid);

			}

		}



		// FIND ALL PASSIVE MEMBERS WITH DOUBLE ENTRIES
			// ACTIVE 'no'

			// DOUBLE ENTRIES

		// MERGE WITH ACTIVE OR LATEST ENTRY PASSIVE (IN THAT ORDER)
		if($operations_5) {

			$members = getPassiveMembersWithDoubleEntries();
			$merged = [];
			output("PASSIVE MEMBERS WITH DOUBLE ENTRIES: " . count($members));
			foreach($members as $puid => $entries) {

				// $user = getUser($puid);
				// showMember($user);

				if(isset($entries["members"])) {
				//
					$has_passive_id = 0;
				// 		$has_invalid_match = 0;
					foreach($entries["members"] as $match) {
						// Active
						if(isActive($match["uid"]) && $match["uid"] != 1) {

							// output("ACTIVE:" . $match["uid"]);
							// $user = getUser($match["uid"]);
							// showMember($user);

							mergeUserIntoUser($puid, $match["uid"]);
							break;
						}
						// Passive
						else if(isPassive($match["uid"])) {

							// output("PASSIVE:" . $match["uid"]);
							// $user = getUser($match["uid"]);
							// showMember($user);

							// Is this passive ID newer than existing
							$has_passive_id = $has_passive_id < $match["uid"] ? $match["uid"] : $has_passive_id;

						}

					}

					// IF PASSIVE MATCH FOUND
					if($has_passive_id && array_search($has_passive_id, $merged) === false) {
						$merged[] = $puid;

						// output("PASSIVE SELECTED:" . $has_passive_id);
						//
						// $user = getUser($has_passive_id);
						// showMember($user);

						mergeUserIntoUser($puid, $has_passive_id);
					}

				}

			}

		}



		// FIX BROKEN DATASETS
		// Two numbers in tel
		if($operations_6) {

			// Split phonenumber og phonenumber entries
			$sql = "SELECT * FROM kbhff_dk.ff_persons WHERE tel LIKE '% og %'";
			if($query->sql($sql)) {
				$results = $query->results();
				foreach($results as $result) {

					list($tel, $tel2) = explode(" og ", $result["tel"]);
					$sql = "UPDATE kbhff_dk.ff_persons set tel = '$tel', tel2 = '$tel2' WHERE uid = ".$result["uid"];
					$query->sql($sql);

					// $user = getUser($result["uid"]);
					// showMember($user);

				}

			}

			// Split phonenumber & phonenumber entries
			$sql = "SELECT * FROM kbhff_dk.ff_persons WHERE tel LIKE '% & %'";
			if($query->sql($sql)) {
				$results = $query->results();
				foreach($results as $result) {

					list($tel, $tel2) = explode(" & ", $result["tel"]);
					$sql = "UPDATE kbhff_dk.ff_persons set tel = '$tel', tel2 = '$tel2' WHERE uid = ".$result["uid"];
					$query->sql($sql);

					// $user = getUser($result["uid"]);
					// showMember($user);

				}

			}

			// remove n/a phonumbers
			$sql = "UPDATE kbhff_dk.ff_persons set tel = NULL WHERE tel = 'n/a'";
			$query->sql($sql);

			// Split phonenumber/phonenumber entries
			$sql = "SELECT * FROM kbhff_dk.ff_persons WHERE tel LIKE '%/%'";
			if($query->sql($sql)) {
				$results = $query->results();
				foreach($results as $result) {

					list($tel, $tel2) = explode("/", $result["tel"]);
					$sql = "UPDATE kbhff_dk.ff_persons set tel = '".trim($tel)."', tel2 = '".trim($tel2)."' WHERE uid = ".$result["uid"];
					$query->sql($sql);

					// $user = getUser($result["uid"]);
					// showMember($user);

				}

			}


			// remove tel2 if tel = tel2
			$sql = "UPDATE kbhff_dk.ff_persons set tel2 = NULL WHERE tel = tel2";
			$query->sql($sql);

			// remove tel2 if tel2 = 0
			$sql = "UPDATE kbhff_dk.ff_persons set tel2 = NULL WHERE tel2 = '0'";
			$query->sql($sql);

			output("BROKEN DATASETS FIXED");
		}



		// FIND "DELETABLE" USERS WITH WRONG ACTIVE STATE
			// INVALID EMAIL, PHONE, FIRSTNAME, LASTNAME

		// TREAT ACCORDINGLY
			// INVALID FIRSTNAME OR LASTNAME - DELETE OR ANONYMIZE
			// INVALID EMAIL OR PHONE - FIX
		if($operations_7) {

			$members = getInvalidUsers();
			output("INVALID USERS: " . count($members));
			foreach($members as $puid) {

				$user = getUser($puid);
				// showMember($user);

				if(($user["firstname"] && !isValidFirstname($user["firstname"])) || ($user["lastname"] && !isValidLastname($user["lastname"]))) {

					if(!hasOrderHead($puid) && !hasOrderLines($puid) && !hasTransactions($puid)) {
						// output("DEL");

						deleteMember($puid);
					}
					else {
						// output("ANO");

						anonymizeMember($puid);
					}

				}
				else {

					// output("FIX: " .$puid);
					// showMember($user);

					if($user["email"] && !isValidEmail($user["email"])) {

						$sql = "UPDATE kbhff_dk.ff_persons set email = NULL WHERE uid = ".$user["uid"];
						$query->sql($sql);

						output("FIX EMAIL: " .$puid);
						// showMember($user);

					}

					if($user["tel"] && !isValidPhone($user["tel"])) {

						$sql = "UPDATE kbhff_dk.ff_persons set tel = NULL WHERE uid = ".$user["uid"];
						$query->sql($sql);

						output("FIX TEL: " .$puid);
						// showMember($user);

					}

					if($user["tel2"] && !isValidPhone($user["tel2"])) {

						$sql = "UPDATE kbhff_dk.ff_persons set tel2 = NULL WHERE uid = ".$user["uid"];
						$query->sql($sql);

						output("FIX TEL2: " .$puid);
						// showMember($user);

					}

				}

			}

		}



		// FIND MEMBERS WITH DOUBLE ENTRIES
			// ACTIVE *
			
			// DOUBLE ENTRIES

		// MERGE WITH APPROPRIATE MATCHES
		if($operations_8) {

			$members = getMembersWithDoubleEntries();
			$merged = [];
			output("MEMBERS WITH DOUBLE ENTRIES: " . count($members));
			foreach($members as $puid => $entries) {

				$user = getUser($puid);

				// Only check active = yes (merge paid subscriptions later)
				if(isset($entries["members"]) && $user["active"] == "yes" && array_search($user["uid"], $merged) === false) {

	//				showMember($user);
				//
					$has_unpaid_id = 0;

					$orders = hasOrderHead($puid);
					$last_order = 0;
					if($orders) {
						$last_order = array_pop($orders);
					}

					// Find any active = paid and potential active = yes
					foreach($entries["members"] as $match) {

						if(array_search($match["uid"], $merged) === false) {

							$match_user = getUser($match["uid"]);

							$match_orders = hasOrderHead($match["uid"]);
							$match_last_order = 0;
							if($match_orders) {
								$match_last_order = array_pop($match_orders);
							}

							// Match is Active
							if(
								$match["uid"] != 1 &&
								$match_user["active"] == "paid"
							) {

								// Match order is later than current puid order
								if(
									(!$last_order || strtotime($last_order["created"]) < strtotime($match_last_order["created"]))
									&&
									(
										strtolower($match_user["firstname"]) == strtolower($user["firstname"])
										||
										strpos(strtolower($match_user["firstname"]), strtolower($user["firstname"])) !== false
										||
										strpos(strtolower($user["firstname"]), strtolower($match_user["firstname"])) !== false
									)
								) {

									// showMember($user);
									// output("LAST ORDER:" . $last_order["created"]);
									//
									// output("ACTIVE:" . $match["uid"]);
									// output("LAST MATCH ORDER:" . $match_last_order["created"]);
									//
									// showMember($match_user);

									mergeUserIntoUser($puid, $match["uid"]);
									$merged[] = $puid;

									break;

								}

							}
							else {

								$has_unpaid_id = $has_unpaid_id < $match["uid"] ? $match["uid"] : $has_unpaid_id;

							}

						}

					}

					// IF ACTIVE yes MATCH FOUND
					if($has_unpaid_id && array_search($has_unpaid_id, $merged) === false) {

						$match_orders = hasOrderHead($has_unpaid_id);
						$match_last_order = 0;
						if($match_orders) {
							$match_last_order = array_pop($match_orders);
						}

						// showMember($user);

						// Match order is later than current puid order
						// and same name
						if(
							(!$last_order || strtotime($last_order["created"]) < strtotime($match_last_order["created"]))
							&&
							strtotime($user["last_login"]) <= strtotime($match_user["last_login"])
							&&
							(
								strtolower($match_user["firstname"]) == strtolower($user["firstname"])
								||
								strpos(strtolower($match_user["firstname"]), strtolower($user["firstname"])) !== false
								||
								strpos(strtolower($user["firstname"]), strtolower($match_user["firstname"])) !== false
							)
						) {

							// OK TO MERGE

						 	// output("NEWER ACCOUNT:" . $match_user["uid"]);

							mergeUserIntoUser($puid, $match["uid"]);
							$merged[] = $puid;

						}

						// PROBABLY OLD ACCOUNT IS BEING USED
						// CHECK NAME
						else if(
							(
								strtolower($match_user["firstname"]) == strtolower($user["firstname"])
								||
								strpos(strtolower($match_user["firstname"]), strtolower($user["firstname"])) !== false
								||
								strpos(strtolower($user["firstname"]), strtolower($match_user["firstname"])) !== false
							)
						){

							// Check if there are newer orders on old account
							if(
								(strtotime($last_order["created"]) > strtotime($match_last_order["created"]))
							) {

							 	// output("ORDER ON OLD ACCOUNT:" . $match_user["uid"]);

								mergeUserIntoUser($match["uid"], $puid);
								$merged[] = $match_user["uid"];

							}

						}
						// Very, very likely same user – merge
						else if($user["email"] == $match_user["email"] && $user["tel"] == $match_user["tel"]) {


							if(
								(strtotime($user["last_login"]) >= strtotime($match_user["last_login"]))
								&&
								(strtotime($last_order["created"]) > strtotime($match_last_order["created"]))
							) {

								// output($user["uid"]." is NEWER than ".$match_user["uid"]);
							 	// output("NEWER ACCOUNT:" . $match_user["uid"]);

								mergeUserIntoUser($match_user["uid"], $user["uid"]);
								$merged[] = $puid;

							}
							else {

								// output($user["uid"]." is OLDER than ".$match_user["uid"]);
							 	// output("NEWER ACCOUNT:" . $match_user["uid"]);

								mergeUserIntoUser($user["uid"], $match_user["uid"]);
								$merged[] = $puid;

							}

						}
						// Names doesn't match
						// Does email OR phone match
						else if($user["email"] == $match_user["email"] || $user["tel"] == $match_user["tel"]) {

							// Email matches
							// REMOVE EMAIL FROM OLDER ACCOUNT
							if($user["email"] == $match_user["email"]) {

								// showMember($user);
								// output("DELETE OLDER EMAIL");
								// showMember($match_user);

								deleteOlderEmail($user, $match_user);

							}

							// Phone matches
							// REMOVE EMAIL FROM OLDER ACCOUNT
							if($user["tel"] == $match_user["tel"]) {

								// showMember($user);
								// output("DELETE OLDER PHONE");
								// showMember($match_user);

								deleteOlderPhone($user, $match_user);

							}

						}

					}
					// Valid match NOT FOUND - Critical errors on data comparison
					// Fix data by deleting duplicate data on account with the least resent order
					else if(array_search($has_unpaid_id, $merged) === false) {


						// showMember($user);
						// output("CRITICAL");
						// showMember($match_user);

						// Email matches
						if($user["email"] == $match_user["email"]) {
							// REMOVE EMAIL FROM OLDER ACCOUNT

							// showMember($user);
							// output("CRI DELETE OLDER EMAIL");
							// showMember($match_user);

							deleteOlderEmail($user, $match_user);

						}

						// Phone matches
						// REMOVE EMAIL FROM OLDER ACCOUNT
						if($user["tel"] == $match_user["tel"]) {

							// showMember($user);
							// output("CRI DELETE OLDER PHONE");
							// showMember($match_user);

							deleteOlderPhone($user, $match_user);

						}

					}

				}
				// PAID users
				else if(array_search($puid, $merged) === false) {

					$orders = hasOrderHead($puid);
					$last_order = 0;
					if($orders) {
						$last_order = array_pop($orders);
					}

					// output("ACTIVE PROFILE:" . $user["uid"]);
					// showMember($user);

					foreach($entries["members"] as $match) {

						if(array_search($match["uid"], $merged) === false) {

							$match_user = getUser($match["uid"]);

							$match_orders = hasOrderHead($match["uid"]);
							$match_last_order = 0;
							if($match_orders) {
								$match_last_order = array_pop($match_orders);
							}


							// Very, very likely same user – merge
							if($user["email"] == $match_user["email"] && $user["tel"] == $match_user["tel"]) {

								if(
									(strtotime($user["last_login"]) >= strtotime($match_user["last_login"]))
									&&
									(strtotime($last_order["created"]) > strtotime($match_last_order["created"]))
								) {

									// output($user["uid"]." is NEWER than ".$match_user["uid"]);
									// output("NEWER ACCOUNT:" . $match_user["uid"]);

									mergeUserIntoUser($match_user["uid"], $user["uid"]);
									$merged[] = $puid;

								}
								else {

									// output($user["uid"]." is OLDER than ".$match_user["uid"]);
									// output("NEWER ACCOUNT:" . $match_user["uid"]);

									mergeUserIntoUser($user["uid"], $match_user["uid"]);
									$merged[] = $puid;

								}

							}
							else {

								if($user["email"] == $match_user["email"]) {

									// REMOVE EMAIL FROM OLDER ACCOUNT
									// showMember($user);
									// output("PAID DELETE OLDER EMAIL");
									// showMember($match_user);

									deleteOlderEmail($user, $match_user);

								}

								if($user["tel"] == $match_user["tel"]) {

									// REMOVE EMAIL FROM OLDER ACCOUNT
									// showMember($user);
									// output("PAID DELETE OLDER PHONE");
									// showMember($match_user);

									deleteOlderPhone($user, $match_user);

								}

							}

						}

					}

				}

			}

		}



		if($operations_transfer) {


			$query->checkDbExistence(SITE_DB.".user_item_subscriptions");
			$query->checkDbExistence(SITE_DB.".user_members");


			// Get all paid users
			$sql = "SELECT * FROM ".SITE_DB.".ff_persons WHERE active = 'paid'";
			if($query->sql($sql)) {
				$results = $query->results();

				foreach($results as $result) {

					if($result["firstname"] != "KBHFF Superadministrator") {

						// get membership number
						$member_no = $result["uid"];

						// check if membership number already exists
						$sql = "SELECT * FROM ".SITE_DB.".user_members WHERE id = '$member_no'";
						if(!$query->sql($sql)) {


							$firstname = $result["firstname"] . (trim($result["middlename"]) ? " " .$result["middlename"] : "");
							$lastname = $result["lastname"];
							$nickname = $firstname . ($lastname ? ($firstname ? " " : "").$lastname : "");



							$mobile = $result["tel"];
							$email = $result["email"];

							$active = $result["active"];
							$created = $result["created"];

					
							// BUILD USER
							$sql = "INSERT INTO ".SITE_DB.".users SET ";
							$sql .= "user_group_id = 2";
							$sql .= ", firstname = '".$firstname."'"; 
							$sql .= ", lastname = '".$lastname."'"; 
							$sql .= ", nickname = '".$nickname."'"; 

							if($active == "yes" || $active == "paid") {
								$sql .= ", status = 1"; 
							}
							else {
								$sql .= ", status = 2";
							}
							$sql .= ", created_at = " . ($created ? "'".$created."'" : ""); 
//							output($sql);

							if($query->sql($sql)) {
								$user_id = $query->lastInsertId();
	
								// MEMBERSHIP
								$sql = "INSERT INTO ".SITE_DB.".user_members SET id = $member_no, user_id = $user_id";
//								output($sql);
								$query->sql($sql);

								$sql = "INSERT INTO ".SITE_DB.".user_usernames SET user_id = $user_id, username = '$member_no', type = 'memberno', verified=1, verification_code = '".randomKey(8)."'";
								$query->sql($sql);

								// USERNAMES
								
								// EMAIL
								if($email) {
									$sql = "SELECT * FROM ".SITE_DB.".user_usernames WHERE email = '$email'";
									if(!$query->sql($sql)) {
										$sql = "INSERT INTO ".SITE_DB.".user_usernames SET user_id = $user_id, username = '$email', type = 'email', verified=0, verification_code = '".randomKey(8)."'";
										$query->sql($sql);
									}
								}

								// MOBILE
								if($mobile) {
									$sql = "SELECT * FROM ".SITE_DB.".user_usernames WHERE mobile = '$mobile'";
									if(!$query->sql($sql)) {
										$sql = "INSERT INTO ".SITE_DB.".user_usernames SET user_id = $user_id, username = '$mobile', type = 'mobile', verified=0, verification_code = '".randomKey(8)."'";
										$query->sql($sql);
									}
								}

							}


//							exit();


						}

					}

//					output($sql);
				}

			}
			


			// Check if user exists
			
			// Create user data (firstname, etc)
			// Set appropriate status - 0 - 1 on first login/handout (we have to delete user )
			// Create usernames (email, phone)


			// Create member
			// Assign member_no as username


			// Check that createpassword on login works

		}

		// Create newsletter
		// Create simple membership model
		// Create two memberships



		exit();


		// FIND "DELETABLE" USERS WITH WRONG ACTIVE STATE
			// ACTIVE = 'no'
			// INVALID EMAIL, PHONE, FIRSTNAME, LASTNAME


		$members = getInvalidUsers();

		print "DELETABLE MEMBERS WITH DOUBLE ENTRIES:" . count($members)."<br>\n";
		foreach($members as $puid => $entries) {

			$user = getUser($puid);

			showMember($user);
			if(isset($entries["members"])) {
				foreach($entries["members"] as $match) {
					if(isActive($match["uid"])) {
						mergeUserIntoUser($puid, $match["uid"]);

						print "ACTIVE:" . $match["uid"]."<br>\n";
//						print_r($match);
					}
					else {
						print "NOT ACTIVE:" . $match["uid"]."<br>\n";
//						print_r($match);
					}
				}
				
			}

//			print_r($entries);


		}


		// FIND DOUBLE ENTRIES AND CHECK POSSIBILITY FOR MERGING

			// IF ONLY ONE IS ACTIVE – MERGE INTO THAT
			
			// IF TWO ARE ACTIVE – FLAG FOR MANUAL DECISION
			
			// IF NONE ARE ACTIVE - MERGE TO NEWEST

		//



		exit();



		// INSERT DATA IN JANITOR STRUCTURE
		// NAME DATA -> users
		// ADDRESS DATA -> user_addresses - SKIP

		// EMAIL + TEL -> user_usernames

		// PRIVACY -> user_maillists
		// UID -> user_members


		// DO NOT INSERT PASSWORDS

		// DELETE COLUMNS WITH OLD/MOVED DATA





		exit();

	}

}

$page->page(array(
	"type" => "janitor",
	"templates" => "janitor/restructure/index.php"
));

?>
