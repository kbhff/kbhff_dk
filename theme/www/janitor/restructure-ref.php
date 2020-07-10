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
		$UC->dropTable(SITE_DB.".ff_fornavne");

		// piger
		$UC->dropTable(SITE_DB.".ff_piger");

		// unisex
		$UC->dropTable(SITE_DB.".ff_unisex");

		// drenge
		$UC->dropTable(SITE_DB.".ff_drenge");



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


		// ORDER HELPERS

		function getAllOrders() {
			$orders = [];

			$query = new Query();
			// $sql = "SELECT uid, orderkey, orderno, puid FROM kbhff_dk.ff_orderhead LIMIT 81380,850000";
			// $sql = "SELECT * FROM kbhff_dk.ff_orderhead ORDER BY uid ASC LIMIT 1000";
			$sql = "SELECT * FROM kbhff_dk.ff_orderhead ORDER BY uid ASC";
			if($query->sql($sql)) {

				$orders = $query->results();

			}
			return $orders;
		}

		function getAllOrdersLines() {
			$orders = [];

			$query = new Query();
			$sql = "SELECT uid, orderkey, orderno, puid FROM kbhff_dk.ff_orderlines";
			if($query->sql($sql)) {

				$orders = $query->results();

			}
			return $orders;
		}

		function getAllTransactions() {
			$transactions = [];

			$query = new Query();
			$sql = "SELECT uid, orderno, puid FROM kbhff_dk.ff_transactions";
			if($query->sql($sql)) {

				$transactions = $query->results();

			}
			return $transactions;
		}

		function getExpiredOrders() {
			
			$orders = [];

			$query = new Query();

			$sql = "SELECT uid, orderno, created FROM kbhff_dk.ff_orderhead WHERE created < '".date("Y-m-d H:i:s", time() - (60 * 60 * 24 * 365 * 5))."'";
			if($query->sql($sql)) {

				$orders = $query->results();


			}

			return $orders;
		}

		function deleteOrder($orderno) {

			$query = new Query();

			$sql = "DELETE FROM kbhff_dk.ff_orderhead WHERE orderno = $orderno";
			$query->sql($sql);

			$sql = "DELETE FROM kbhff_dk.ff_orderlines WHERE orderno = $orderno";
			$query->sql($sql);

			$sql = "DELETE FROM kbhff_dk.ff_transactions WHERE orderno = $orderno";
			$query->sql($sql);

		}




		// FIND AND DELETE READYLY "DELETABLE" USERS 
		$user_operations_1 = false;

		// FIND AND DELETE USERS THAT LOOK LIKE THEY ARE VERY LIKELY "DELETABLE" USERS 
		$user_operations_2 = false;

		// FIND AND MERGE USERS THAT ARE MARKED FOR DELETION BUT HAS DOUBLE ENTRIES 
		$user_operations_3 = false;

		// FIND AND ANONYMIZE ALL (REMAINING) DELETED MEMBERS
		$user_operations_4 = false;

		// FIND AND MERGE PASSIVE MEMBERS WITH DOUBLE ENTRIES
		$user_operations_5 = false;

		// FIX BROKEN DATASETS
		$user_operations_6 = false;

		// FIND AND FIX/MERGE/DELETE "DELETABLE" USERS WITH WRONG ACTIVE STATE
		$user_operations_7 = false;

		// FIND AND MERGE MEMBERS WITH DOUBLE ENTRIES
		$user_operations_8 = false;



		// PRE USER IMPORT OPERATIONS (CREATE DEPARTMENT, MAILLIST, ETC)
		$pre_user_operations = false;



		// TRANSFER ACCOUNTS TO NEW SYSTEM
		$user_operations_transfer = false;



		// ORDERS

		// CROSSREFERENCE ORDERNO / ORDERKEY
		$order_operations_1 = false;

		// LOOK FOR UNUSED PRODUCTS
		$order_operations_2 = false;

		// DELETE UNUSED PRODUCTS
		$order_operations_3 = false;

		// DELETE EXPIRED ORDERS (MORE THAN 5 YEARS OLD)
		$order_operations_4 = false;

		// DELETE ORDERS WITHOUT LINES
		$order_operations_5 = false;

		// CROSS-REFERENCE ALL ORDERS AND CHECK VALIDITY
		$order_operations_6 = false;

		// DELETE LOST ORDER LINES AND TRANSACTIONS, ADMIN ORDERS AND CANCELLED ORDERS
		$order_operations_7 = false;



		// CREATE REQUIRED LEGACY PRODUCTS
		$pre_order_operations = false;



		// TRANSFER ALL ORDERS
		$order_operations_transfer = false;


		// REMOVE DEPRECATED TABLES AND COLUMNS
		$cleanup_operation = false;



		// START OPERATIONS




		// FIND READYLY "DELETABLE" USERS 
			// ACTIVE 'X'|' '
			// NO ORDERHEADS
			// NO ORDERLINES
			// NO TRANSACTIONS

			// DOUBLE ENTRY MATCH NOT IMPORTANT, NO RELEVANT INFORMATION TO MERGE FROM THIS USER

		// DELETE ANY MATCHES 
		if($user_operations_1) {

			output("REPEAT UNTIL NO MATCHES ARE FOUND");

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
		if($user_operations_2) {

			output("REPEAT UNTIL NO MATCHES ARE FOUND");

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
		if($user_operations_3) {

			output("REPEAT UNTIL NO MATCHES ARE FOUND");

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
		if($user_operations_4) {

			output("REPEAT UNTIL NO MATCHES ARE FOUND");

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
		if($user_operations_5) {

			output("REPEAT UNTIL NO MATCHES ARE FOUND");

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
		if($user_operations_6) {

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
		if($user_operations_7) {

			output("REPEAT UNTIL NO MATCHES ARE FOUND");

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
		if($user_operations_8) {

			output("REPEAT UNTIL NO MATCHES ARE FOUND");
			output("THIS MIGHT PRODUCE UNRESOLVABLE MATCHES DUE TO tel AND tel2 OVERLAP – THIS IS OK");

			$members = getMembersWithDoubleEntries();
			$merged = [];
			output("MEMBERS WITH DOUBLE ENTRIES: " . count($members));
			foreach($members as $puid => $entries) {

				$user = getUser($puid);

				// showMember($user);

				// Only check active = yes (merge paid subscriptions later)
				if(isset($entries["members"]) && $user["active"] == "yes" && array_search($user["uid"], $merged) === false) {

					// showMember($user);
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



		// CREATE DEPENDENCIES BEFORE IMPORTING ALL REMAINING USERS
		if($pre_user_operations) {

			$query->checkDbExistence(SITE_DB.".project_departments");
			$query->checkDbExistence(SITE_DB.".system_maillists");

			$query->checkDbExistence(SITE_DB.".user_item_subscriptions");
			$query->checkDbExistence(SITE_DB.".user_members");
			$query->checkDbExistence(SITE_DB.".user_department");
			$query->checkDbExistence(SITE_DB.".user_maillists");


			// MAILLIST
			// Create general newletter if it does not exist
			$sql = "SELECT * FROM ".SITE_DB.".system_maillists WHERE name = 'Nyheder'";
			if(!$query->sql($sql)) {

				$sql = "INSERT INTO ".SITE_DB.".system_maillists SET name = 'Nyheder', description = 'Generelt nyhedsbrev'";
				$query->sql($sql);
				output("CREATED MAILLIST");

			}
			else {
				output("MAILLIST EXISTS");
			}


			// DEPARTMENTS
			// cross-reference divisions to make sure they are relevant
			$sql = "SELECT * FROM ".SITE_DB.".ff_divisions";
			if($query->sql($sql)) {
				$divisions = $query->results();

				foreach($divisions as $division) {

					if($division["shortname"] == "IB" && $division["name"] == "Islands Brygge") {
						$sql = "UPDATE ".SITE_DB.".ff_divisions SET shortname = 'ISB' WHERE uid = ".$division["uid"];
						$query->sql($sql);

						$division["shortname"] = "ISB";
					}

					if($division["shortname"] == "IB" && $division["name"] == "Indre by") {
						$sql = "UPDATE ".SITE_DB.".ff_divisions SET shortname = 'INB' WHERE uid = ".$division["uid"];
						$query->sql($sql);

						$division["shortname"] = "INB";
					}


					// Division has either members or products (and does not already exist)
					$sql_members = "SELECT * FROM ".SITE_DB.".ff_divisions_members";
					$sql_items = "SELECT * FROM ".SITE_DB.".ff_items";
					$sql_exists = "SELECT * FROM ".SITE_DB.".project_departments WHERE abbreviation = '".$division["shortname"]."'";
					if(($query->sql($sql_members) || $query->sql($sql_items)) && !$query->sql($sql_exists)) {

						// CREATE DEPARTMENT IN NEW TABLE
						$sql = "INSERT INTO ".SITE_DB.".project_departments SET name = '".$division["name"]."', abbreviation = '".$division["shortname"]."', email = '".$division["kontakt"]."'";
						if($query->sql($sql)) {
							output("CREATED DIVISION:". $division["shortname"]);
						}
						else {
							output("FAILED DIVISION:". $division["shortname"] ." FIX ISSUES TO CONTINUE");
							exit();
							
						}

					}
					else {
						output("DIVISION EXISTS:". $division["shortname"]);
					}

				}

			}


			// UPDATE OLD DEPARTMENT FALLBACK VALUES (stored in status1)
			$sql = "UPDATE ".SITE_DB.".ff_persons SET status1 = 'ØB' WHERE status1 = 'OES'";
			$query->sql($sql);

			$sql = "UPDATE ".SITE_DB.".ff_persons SET status1 = 'VBR' WHERE status1 = 'VES'";
			$query->sql($sql);

			$sql = "UPDATE ".SITE_DB.".ff_persons SET status1 = 'NBR' WHERE status1 = 'NØR'";
			$query->sql($sql);

			$sql = "UPDATE ".SITE_DB.".ff_persons SET status1 = 'YN' WHERE status1 = 'YNB'";
			$query->sql($sql);

			$sql = "UPDATE ".SITE_DB.".ff_persons SET status1 = 'AM' WHERE status1 = 'AMA'";
			$query->sql($sql);

			output("FALLBACK DEPARTMENT STRINGS UPDATED (status1)");


			// CHECK MEMBERSHIP 
			$sql = "SELECT * FROM ".SITE_DB.".item_membership WHERE classname='volunteer'";
			if($query->sql($sql)) {
				output("NEW MEMBERSHIP EXISTS – OK TO PROCEED");
			}
			else {
				output("NO MEMBERSHIP – CREATE (OR UPDATE) NEW volunteer MEMBERSHIP TO CONTINUE");
			}


			if(!$query->sql("SELECT DISTINCT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE column_name = 'new_user_id' AND TABLE_NAME = 'ff_persons' AND TABLE_SCHEMA = 'kbhff_dk'")) {

				// ADD new_user_id COLUMN TO ff_persons (to keep track of user-relation)
				$sql = "ALTER TABLE ".SITE_DB.".ff_persons ADD new_user_id int(11) AFTER uid";
				$query->sql($sql);
				output("ADDED new_user_id MAPPING COLUMN");

			}
			else {
				output("new_user_id EXISTS");
			}

		}




		// TRANSFER ALL REMAINING USERS
		// - MAP NEW user_id TO ff_persons
		if($user_operations_transfer) {


			// Get required base data


			// Maillist id
			$sql = "SELECT * FROM ".SITE_DB.".system_maillists WHERE name = 'Nyheder'";
			$query->sql($sql);
			$maillist_id = $query->result(0, "id");

			output("MAILLIST ID: " . $maillist_id);


			// departments?
			$sql = "SELECT * FROM ".SITE_DB.".project_departments";
			if($query->sql($sql)) {

				$department_index = [];

				$departments = $query->results();
				foreach($departments as $department) {
					$department_index[$department["abbreviation"]] = $department["id"];
				}

				output("DEPARTMENT INDEX CREATED");
				// print_r($department_index);
			}
			else {
				output("NO DEPARTMENTS – CREATE DEPARTMENTS TO CONTINUE");
				exit();
			}


			// Membership ID
			// CHECK MEMBERSHIP 
			$sql = "SELECT * FROM ".SITE_DB.".item_membership WHERE classname='volunteer'";
			if($query->sql($sql)) {
				$membership_id = $query->result(0, "item_id");
				output("MEMBERSHIP ID: " . $membership_id);
			}
			else {
				output("NO MEMBERSHIP – CREATE (OR UPDATE) NEW volunteer MEMBERSHIP TO CONTINUE");
				exit();
			}






			// Get all paid users
			// $sql = "SELECT * FROM ".SITE_DB.".ff_persons WHERE active = 'paid'";

			// Get all remaining users
			$sql = "SELECT * FROM ".SITE_DB.".ff_persons ORDER BY uid ASC";
			// $sql = "SELECT * FROM ".SITE_DB.".ff_persons WHERE email = 'martin@think.dk' ORDER BY uid ASC";
			// $sql = "SELECT * FROM ".SITE_DB.".ff_persons ORDER BY uid ASC LIMIT 250";
			if($query->sql($sql)) {
				$results = $query->results();

				output("TRANSFERRING " . count($results) . " USERS");

				foreach($results as $result) {

					// Do no transfer superuser (uid = 0 or special firstname)
					if($result["uid"] && $result["firstname"] != "KBHFF Superadministrator") {

						// Transfer anonymized users separately
						if(preg_match("/yes|no|paid/", $result["active"])) {

							// get membership number
							$member_no = $result["uid"];

							// check if new_user_id or membership number already exists
							$sql = "SELECT * FROM ".SITE_DB.".user_members WHERE id = '$member_no'";
							if(!$result["new_user_id"] && !$query->sql($sql)) {


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
								$sql .= ", firstname = '".prepareForDB($firstname)."'"; 
								$sql .= ", lastname = '".prepareForDB($lastname)."'"; 
								$sql .= ", nickname = '".prepareForDB($nickname)."'"; 

								$sql .= ", status = 1"; 

								$sql .= ", created_at = " . ($created ? "'".$created."'" : ""); 


								if($query->sql($sql)) {
									$user_id = $query->lastInsertId();


									output("CREATED MEMBER:" . $member_no . " (".$nickname.")");


									// ADD new_user_id to ff_persons
									$sql = "UPDATE ".SITE_DB.".ff_persons SET new_user_id = $user_id WHERE uid = $member_no";
									$query->sql($sql);


									// Only add subscription for active members
									if($active == "yes" || $active == "paid") {

										// ADD MEMBERSHIP SUBSCRIPTION
										$sql = "INSERT INTO ".SITE_DB.".user_item_subscriptions SET user_id = $user_id, item_id = $membership_id, created_at = '".$result["created"]."', renewed_at = '2018-05-01', expires_at = '2019-05-01'";
			//								output($sql);
										$query->sql($sql);
										$subscription_id = $query->lastInsertId();


										// ADD MEMBERSHIP
										$sql = "INSERT INTO ".SITE_DB.".user_members SET id = $member_no, user_id = $user_id, subscription_id = $subscription_id, created_at = '".$result["created"]."'";
			//								output($sql);
										$query->sql($sql);

									}
									// Add passive membership for passive members
									else {

										// ADD MEMBERSHIP
										$sql = "INSERT INTO ".SITE_DB.".user_members SET id = $member_no, user_id = $user_id, created_at = '".$result["created"]."'";
			//								output($sql);
										$query->sql($sql);

									}

									// USERNAMES

									// Add member number as username
									$sql = "INSERT INTO ".SITE_DB.".user_usernames SET user_id = $user_id, username = '$member_no', type = 'member_no', verified=1, verification_code = '".randomKey(8)."'";
									$query->sql($sql);


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


									// Look up department info
									// $member_no -> ff_division_members.member
									// ff_division_members.division -> ff_divisions.uid

									// DEPARTMENT
									$sql = "SELECT shortname FROM ".SITE_DB.".ff_division_members as assoc, ".SITE_DB.".ff_divisions as divis WHERE assoc.member = $member_no AND assoc.division = divis.uid";
									if($query->sql($sql)) {

										$dep_abbr = $query->result(0, "shortname");

										// Add user to department
										$sql = "INSERT INTO ".SITE_DB.".user_department SET user_id = $user_id, department_id = ".$department_index[$dep_abbr];
										$query->sql($sql);

									}
									// CHECK OLD REGISTRATION IN 'status1'
									else if($result["status1"] && isset($department_index[$result["status1"]])) {

										// Add user to department
										$sql = "INSERT INTO ".SITE_DB.".user_department SET user_id = $user_id, department_id = ".$department_index[$result["status1"]];
										$query->sql($sql);

									}
									else {

										output("MEMBER HAS NO DEPARTMENT?? – " . $member_no);

									}



									// Maillist
									// ff_persons.privacy = Y
									if($result["privacy"] === "Y") {
									
										// Add user to maillist
										$sql = "INSERT INTO ".SITE_DB.".user_maillists SET user_id = $user_id, maillist_id = ".$maillist_id;
									
										$query->sql($sql);
									
										output("MEMBER ADDED TO MAILLIST");
									
									}

								}

							}
							else {

								$firstname = $result["firstname"] . (trim($result["middlename"]) ? " " .$result["middlename"] : "");
								$lastname = $result["lastname"];
								$nickname = $firstname . ($lastname ? ($firstname ? " " : "").$lastname : "");
								
								output("MEMBER EXISTS:" . $member_no . " (".$nickname.")");
							}

						}

						// TRANSFER ANONYMIZED USERS
						else {

							// get membership number
							$member_no = $result["uid"];


							if(!$result["new_user_id"]) {


								$nickname = "Anonymous";
								$created = $result["created"];

					
								// BUILD USER
								$sql = "INSERT INTO ".SITE_DB.".users SET ";
								$sql .= "nickname = '".$nickname."'"; 

								$sql .= ", status = -1"; 

								$sql .= ", created_at = " . ($created ? "'".$created."'" : ""); 

								if($query->sql($sql)) {
									$user_id = $query->lastInsertId();


									output("CREATED ANONYMOUS MEMBER:" . $member_no);


									// ADD new_user_id to ff_persons
									$sql = "UPDATE ".SITE_DB.".ff_persons SET new_user_id = $user_id WHERE uid = $member_no";
									$query->sql($sql);

								}

							}
							else {

								output("ANONYMIZED MEMBER EXISTS:" . $member_no);
								
							}

						}


					}

				}

			}

		}




		// CHECK ORDER NO INTEGRITY
		// - so far none was found – but check again before doing final import
		if($order_operations_1) {

			$orders = getAllOrders();
			output("TOTAL ORDERS: " . count($orders));

			foreach($orders as $order) {

//				debug("Is orderno used in other orders");
				// Is orderno used in other orders
				$sql = "SELECT uid FROM kbhff_dk.ff_orderhead WHERE orderno = '".$order["orderno"]."' AND uid != '".$order["uid"]."'";
				// debug($sql);

				if($query->sql($sql)) {

					$matches = $query->results();
					foreach($matches as $match) {
						output("DUPLET ORDERNO: " . $order["orderno"] .", " . $order["uid"]." = ". implode($match, ","));
					}
				}

//				debug("Is orderkey used in other orders");
				// Is orderkey used in other orders
				$sql = "SELECT uid FROM kbhff_dk.ff_orderhead WHERE orderkey = '".$order["orderkey"]."' AND  uid != '".$order["uid"]."'";
//				debug($sql);

				if($query->sql($sql)) {

					$matches = $query->results();
					foreach($matches as $match) {
						output("DUPLET ORDERKEY: " . $order["orderkey"] . ", " . $order["uid"]." = ". implode($match, ","));
					}
				}

			}

		}


		// FIX MISSING ITEM IN ORDER LINES
		if($order_operations_2) {

			$sql = "SELECT * FROM kbhff_dk.ff_orderlines WHERE item = '' OR item IS NULL AND puid != 1";
			if($query->sql($sql)) {
				$lines = $query->results();

				output("BROKEN LINES:" . count($lines));
				// print_r($lines);

				foreach($lines as $line) {
					
					// Check if order has more lines
					$sql = "SELECT * FROM kbhff_dk.ff_orderlines WHERE orderno = '".$line["orderno"]."' AND uid != ".$line["uid"];
					if($query->sql($sql)) {
						$alllines = $query->results();

						// output("ORDER HAS MORE LINES");
						// print_r($line);


						// Check order head
						$sql = "SELECT * FROM kbhff_dk.ff_orderhead WHERE orderno = '".$line["orderno"]."'";
						if($query->sql($sql)) {
							$head = $query->result(0);
							// output("ORDER PRICE:" . $head["cc_trans_amount"]);
						}

						// print_r($alllines);

						$total = 0;
						foreach($alllines as $allline) {
							$total += $allline["amount"];
							
						}

						// ORDER HAS OTHER LINES – THIS ONE ISN'T NEEDED TO MAINTAIN INTEGITY
						if($line["amount"] == 0 && $total == $head["cc_trans_amount"]) {

							$sql = "DELETE FROM kbhff_dk.ff_orderlines WHERE uid = ".$line["uid"];
							if($query->sql($sql)) {
								output("DELETE FLAWED ORDER LINE");
							}


						}
						// MUST ASSIGN ITEM TO ORDER
						else {

							// REGISTER AS STOFPOSE (119)
							$sql = "UPDATE kbhff_dk.ff_orderlines SET item = 119 WHERE uid = ".$line["uid"];
							if($query->sql($sql)) {
								output("FIXED MISSING ITEM");
							}

						}


					}
					// LINE REQUIRED FOR ORDER – FIX WITH BEST OPTION
					else {

						// REGISTER AS STOFPOSE (119)
						$sql = "UPDATE kbhff_dk.ff_orderlines SET item = 119 WHERE uid = ".$line["uid"];
						if($query->sql($sql)) {
							output("FIXED MISSING ITEM");
						}

					}

				}

			}

		}


		// DELETE UNUSED PRODUCTS (EXIST IN PRODUCTTYPES, BUT NOT USED)
		if($order_operations_3) {


			// DELETE UNUSED PRODUCT TYPES
			$sql = "SELECT * FROM kbhff_dk.ff_producttypes WHERE id NOT IN(SELECT producttype_id FROM kbhff_dk.ff_items)";
			if($query->sql($sql)) {
				$items = $query->results();

				foreach($items as $item) {

					$sql = "DELETE FROM kbhff_dk.ff_producttypes WHERE id = ".$item["id"];
					if($query->sql($sql)) {
						output("DELETED:".$item["explained"]);
					}
				}
			}
			else {
				output("NO UNUSED PRODUCTS");
			}

		}


		// DELETE EXPIRED ORDERS (MORE THAN 5 YEARS OLD)
		if($order_operations_4) {

			$orders = getExpiredOrders();
			output("EXPIRED ORDERS: " . count($orders));
			foreach($orders as $order) {

				// output($order["created"]);
				deleteOrder($order["orderno"]);

			}

		}


		// DELETE ORDERS WITHOUT LINES
		if($order_operations_5) {

			$sql = "SELECT orderno FROM kbhff_dk.ff_orderhead WHERE puid != 0 AND puid != 1 AND orderno NOT IN(SELECT orderno FROM kbhff_dk.ff_orderlines)";
			if($query->sql($sql)) {

				$orders = $query->results();
				foreach($orders as $order) {

					$sql = "SELECT orderno FROM kbhff_dk.ff_orderlines WHERE orderno = ".$order["orderno"];
					if(!$query->sql($sql)) {

						$sql = "SELECT * FROM kbhff_dk.ff_transactions WHERE orderno = ".$order["orderno"];
						if(!$query->sql($sql)) {
							
							$sql = "DELETE FROM kbhff_dk.ff_orderhead WHERE orderno = ".$order["orderno"];
							$query->sql($sql);

							output("EMPTY ORDER DELETED");
						}
						else {
							output("TRANS EXISTS");
						}

					}
					else {
						output("LINE EXISTS");
					}

				}

			}
			else {
				output("NO EMPTY ORDERS");
			}

		}


		// CROSS-REFERENCE ALL ORDERS AND CHECK VALIDITY
		// - CHECK THAT PAYMENT, ORDERLINES AND ORDERHEAD AMOUNTS ADD UP
		// - FIX BROKEN USER ASSIGNMENT
		// - DELETE ALL INVALID OR INCOMPLETE ORDERS
		if($order_operations_6) {

			$orders = getAllOrders();
			output("TOTAL ORDERS: " . count($orders));

			// $delete_count = 0;

			foreach($orders as $order) {

				
				$order_lines = false;
				$order_transactions = false;


				$order_transaction_user_issue = false;
				$order_amount_issue = false;


				$sql = "SELECT * FROM kbhff_dk.ff_orderlines WHERE orderno = ".$order["orderno"];
				if($query->sql($sql)) {

					$order_lines = $query->results();

					$sql = "SELECT * FROM kbhff_dk.ff_transactions WHERE orderno = ".$order["orderno"];
					if($query->sql($sql)) {
						$order_transactions = $query->results();
					}

				}


				// Do we have overlines and order transactions
				if($order_lines && $order_transactions) {

					$order_lines_total = 0;


					// Check order lines
					foreach($order_lines as $index => $order_line) {

						if($order_line["amount"] == "0") {
							output("ORDERLINE ZERO");

							$sql = "SELECT amount FROM kbhff_dk.ff_items WHERE id = ".$order_line["item"];
							$query->sql($sql);
							$correct_amount = $query->result(0, "amount");

							// print_r($order_line);
							output("SET CORRECT AMOUNT:" . $correct_amount);

							$sql = "UPDATE kbhff_dk.ff_orderlines set amount = $correct_amount WHERE uid = ".$order_line["uid"];
							$query->sql($sql);

							$order_line["amount"] = $correct_amount;
							$order_lines[$index]["amount"] = $correct_amount;

						}


						$order_lines_total += $order_line["amount"];



						// Check if user information can be made useful – or delete order
						if($order["puid"] != $order_line["puid"] && $order["puid"] != $order_line["status1"]) {

							$sql = "DELETE FROM kbhff_dk.ff_orderhead WHERE orderno = ".$order["orderno"];
							$query->sql($sql);

							$sql = "DELETE FROM kbhff_dk.ff_orderlines WHERE orderno = ".$order["orderno"];
							$query->sql($sql);

							$sql = "DELETE FROM kbhff_dk.ff_transactions WHERE orderno = ".$order["orderno"];
							$query->sql($sql);

							output("INVALID ORDER DELETED");

							// Stop evaluating current order and continue order loop
							continue 2;

						}

						// Normalize order line to correct puid
						else if($order["puid"] != $order_line["puid"] && $order["puid"] == $order_line["status1"]) {

							output("NORMALIZE ORDERLINE PUID");

							$sql = "UPDATE kbhff_dk.ff_orderlines set puid = ".$order["puid"]." WHERE orderno = ".$order["orderno"];
							$query->sql($sql);

						}

					}


					$order_transactions_total = 0;

					// Check transaction lines
					foreach($order_transactions as $order_transaction) {


						$order_transactions_total += $order_transaction["amount"];

						if($order["puid"] != $order_transaction["puid"] && $order["puid"] != $order_transaction["authorized_by"] && $order_transaction["puid"] !== "0" && $order_transaction["puid"] !== 1) {

							// output("USER ISSUE (TRANS)???");
							$order_transaction_user_issue = true;

						}

					}


					// Compare amounts of order, lines and transactions
					if($order_lines_total != $order["cc_trans_amount"]) {

						output("ORDERLINE AMOUNT ISSUE (".$order["orderno"].") - DELETED");
						$orderline_amount_issue = true;


						$sql = "DELETE FROM kbhff_dk.ff_orderhead WHERE orderno = ".$order["orderno"];
						$query->sql($sql);

						$sql = "DELETE FROM kbhff_dk.ff_orderlines WHERE orderno = ".$order["orderno"];
						$query->sql($sql);

						$sql = "DELETE FROM kbhff_dk.ff_transactions WHERE orderno = ".$order["orderno"];
						$query->sql($sql);

					}


					// Compare amounts of order, lines and transactions
					if($order_lines_total != $order["cc_trans_amount"] || $order_lines_total != $order_transactions_total) {

						// output("AMOUNT ISSUE???");
						$order_amount_issue = true;

					}



					if($order_amount_issue && count($order_transactions) > 1) {

						output("TRANSACTIONS ISSUE???");

						if($order["status1"] == $order_transactions[0]["method"] && $order_lines_total == $order["cc_trans_amount"] && $order_lines_total == $order_transactions[0]["amount"]) {

							output("PAYMENTS REGISTERED WRONG");


							for($i = 1; $i < count($order_transactions); $i++) {

								output("REMOVE PAYMENT FROM ORDER");

								$sql = "UPDATE kbhff_dk.ff_transactions set orderno = 0 WHERE uid = ".$order_transactions[$i]["uid"];
								$query->sql($sql);

								$order_amount_issue = false;
							}

						}
						else if($order["status1"] == $order_transactions[1]["method"] && $order_lines_total == $order["cc_trans_amount"] && $order_lines_total == $order_transactions[1]["amount"]) {

							output("PAYMENTS REGISTERED WRONG");

							$sql = "UPDATE kbhff_dk.ff_transactions set orderno = 0 WHERE uid = ".$order_transactions[0]["uid"];
							$query->sql($sql);

							$order_amount_issue = false;

						}

					}





					// If transaction has wrong user, but amounts otherwise adds up, 
					// then set correct usre for transaction
					if($order_transaction_user_issue && !$order_amount_issue) {
						
						output("USER BAD – AMOUNT OK");

						$sql = "UPDATE kbhff_dk.ff_transactions set puid = ".$order["puid"]." WHERE orderno = ".$order["orderno"];
						$query->sql($sql);

					}

					else if($order_transaction_user_issue && $order_amount_issue) {

						output("TRANSACTION USER AND AMOUNT ISSUE (".$order["orderno"].") - DELETED");


						$sql = "DELETE FROM kbhff_dk.ff_orderhead WHERE orderno = ".$order["orderno"];
						$query->sql($sql);

						$sql = "DELETE FROM kbhff_dk.ff_orderlines WHERE orderno = ".$order["orderno"];
						$query->sql($sql);

						$sql = "DELETE FROM kbhff_dk.ff_transactions WHERE orderno = ".$order["orderno"];
						$query->sql($sql);


					}
					else if($order_amount_issue) {


						output("AMOUNT ISSUE");



					}


					if($order["cc_trans_amount"] == "0") {

						output("ZERO ORDER");

						// print_r($order);
						// print_r($order_lines);
						//
						// print_r($order_transactions);

					}


				}

				// Incomplete or invalid order – delete it
				else {

					// $delete_count++;
					output("INVALID OR INCOMPLETE ORDER (".$order["orderno"].") - DELETED");


					$sql = "DELETE FROM kbhff_dk.ff_orderhead WHERE orderno = ".$order["orderno"];
					$query->sql($sql);

					$sql = "DELETE FROM kbhff_dk.ff_orderlines WHERE orderno = ".$order["orderno"];
					$query->sql($sql);

					$sql = "DELETE FROM kbhff_dk.ff_transactions WHERE orderno = ".$order["orderno"];
					$query->sql($sql);


				}

			}

		}


		// REMOVE LAST EXCEESS ORDERS 
		// - LOST ORDER LINES AND TRANSACTIONS
		// – ADMIN ORDERS
		// - ORDERS WITH 'annulleret' AS status1
		if($order_operations_7) {

			output("LAST CLEAN OUT");

			// Order lines without order head
			$sql = "SELECT uid, orderno FROM kbhff_dk.ff_orderlines WHERE orderno NOT IN(SELECT orderno FROM kbhff_dk.ff_orderhead)";
			if($query->sql($sql)) {

				$orders = $query->results();
				output("LOST ORDERLINES:" . count($orders));

				foreach($orders as $order) {
					$sql = "DELETE FROM kbhff_dk.ff_orderlines WHERE uid = ".$order["uid"];
					$query->sql($sql);					
				}

			}


			// Order transactions without order head
			$sql = "SELECT uid, orderno FROM kbhff_dk.ff_transactions WHERE orderno NOT IN(SELECT orderno FROM kbhff_dk.ff_orderhead)";
			if($query->sql($sql)) {

				$orders = $query->results();
				output("LOST TRANSACTIONS:" . count($orders));

				foreach($orders as $order) {
					$sql = "DELETE FROM kbhff_dk.ff_transactions WHERE uid = ".$order["uid"];
					$query->sql($sql);
				}

			}


			// Admin orders or 'annulleret' orders
			$sql = "SELECT uid, orderno FROM kbhff_dk.ff_orderhead WHERE puid = 1";
			if($query->sql($sql)) {

				$orders = $query->results();
				output("ADMIN ORDERS:" . count($orders));

				foreach($orders as $order) {
					$sql = "DELETE FROM kbhff_dk.ff_orderhead WHERE orderno = ".$order["orderno"];
					$query->sql($sql);

					$sql = "DELETE FROM kbhff_dk.ff_orderlines WHERE orderno = ".$order["orderno"];
					$query->sql($sql);

					$sql = "DELETE FROM kbhff_dk.ff_transactions WHERE orderno = ".$order["orderno"];
					$query->sql($sql);
				}

			}

			// Admin orders or 'annulleret' orders
			$sql = "SELECT uid, orderno FROM kbhff_dk.ff_orderhead WHERE status1 = 'annulleret' OR status1 = 'anulleret'";
			if($query->sql($sql)) {

				$orders = $query->results();
				output("CANCELLED ORDERS:" . count($orders));

				foreach($orders as $order) {
					$sql = "DELETE FROM kbhff_dk.ff_orderhead WHERE orderno = ".$order["orderno"];
					$query->sql($sql);

					$sql = "DELETE FROM kbhff_dk.ff_orderlines WHERE orderno = ".$order["orderno"];
					$query->sql($sql);

					$sql = "DELETE FROM kbhff_dk.ff_transactions WHERE orderno = ".$order["orderno"];
					$query->sql($sql);
				}

			}


		}




		// IDENTIFY PRODUCT/SIGNUP/MEMBERSHIP (EQUIVALENT MUST BE CREATED IN NEW SYSTEM)
		if($pre_order_operations) {

			output("ORDER IMPORT PREREQUISITES");

			$query = new Query();
			$IC = new Items();
			$model = $IC->typeObject("legacyproduct");

			$sql = "SELECT * FROM kbhff_dk.ff_producttypes";

			if($query->sql($sql)) {

				$products = $query->results();

				foreach($products as $product) {

					if(!preg_match("/medlemskab|Kontingent/", $product["explained"])) {

						// Does product exist
						$matches = $IC->getItems(["itemtype" => "legacyproduct", "where" => "name = '".$product["explained"]."'", "limit" => 1]);
						if(!$matches) {

							$_POST["name"] = $product["explained"];
							$model->save(["save"]);

							output($product["explained"] . " CREATED");
						}

					}

				}

			}


			$payment_method_index = [];
			$payment_methods = $page->paymentMethods();

			foreach($payment_methods as $payment_method) {

				$payment_method_index[$payment_method["classname"]] = $payment_method;

			}

			$error = false;

			if(!isset($payment_method_index["kontant"])) {
				output("kontant PAYMENT OPTION MISSING");
				$error = true;
			}
			if(!isset($payment_method_index["nets"])) {
				output("nets PAYMENT OPTION MISSING");
				$error = true;
			}
			if(!isset($payment_method_index["mobilepay"])) {
				output("mobilepay PAYMENT OPTION MISSING");
				$error = true;
			}

			if($error) {
				exit();
			}


//			print_r($payment_methods);

		}


		// FINAL ORDER OPERATION
		// MAP ORDER TO SUBSCRIPTION FOR EACH USER (FIND THE ORDER THAT MATCHES THE LAST KONTINGENT PAYMENT)
		if($order_operations_transfer) {

			$orders = getAllOrders();
			output("TOTAL ORDERS TO IMPORT: " . count($orders));
			$SC = new Shop();


			// Membership ID
			// CHECK MEMBERSHIP 
			$sql = "SELECT * FROM ".SITE_DB.".item_membership WHERE classname='volunteer'";
			if($query->sql($sql)) {
				$membership_id = $query->result(0, "item_id");
				output("MEMBERSHIP ID: " . $membership_id);
			}
			else {
				output("NO MEMBERSHIP – CREATE (OR UPDATE) NEW volunteer MEMBERSHIP TO CONTINUE");
				exit();
			}



			// New products
			$legacy_products = $IC->getItems(["itemtype" => "legacyproduct", "extend" => true]);
			$legacy_products_index = [];
			foreach($legacy_products as $legacy_product) {
				$legacy_products_index[$legacy_product["name"]] = $legacy_product;
			}

			// print_r($legacy_products_index);

			// GET LEGACY PRODUCT INDEX
			$sql = "SELECT items.id as id, products.id as product_id, products.explained FROM kbhff_dk.ff_producttypes as products, kbhff_dk.ff_items as items WHERE items.producttype_id = products.id";
			$query->sql($sql);

			$product_index = [];
			$products = $query->results();
			foreach($products as $product) {




				// $matches = $IC->getItems(["itemtype" => "legacyproduct", "where" => "name = '".$product["explained"]."'", "limit" => 1]);
				if(isset($legacy_products_index[trim($product["explained"])])) {
					$product["item_id"] = $legacy_products_index[trim($product["explained"])]["item_id"];
					// $product["item_id"] = $legacy_products_index[$product["explained"]];
				}
				else if(preg_match("/medlemskab|Kontingent/", $product["explained"])) {

					$product["item_id"] = $membership_id;
				}
				// else {
				// 	output("SHIT:" . $product["explained"] . ", " . $legacy_products_index["støttepose (grøntsager)"]);
				// 	exit;
				// }


				$product_index[$product["id"]] = $product;

			}



			$payment_method_index = [];
			$payment_methods = $page->paymentMethods();

			foreach($payment_methods as $payment_method) {

				$payment_method_index[$payment_method["classname"]] = $payment_method;

			}


			// print_r($product_index);
			// exit();


			foreach($orders as $order) {

				
				$order_lines = false;
				$order_transactions = false;
				$order_no = false;
				$user_id = false;

				$sql = "SELECT * FROM kbhff_dk.ff_orderlines WHERE orderno = ".$order["orderno"];
				$query->sql($sql);
				$order_lines = $query->results();

				$sql = "SELECT * FROM kbhff_dk.ff_transactions WHERE orderno = ".$order["orderno"];
				$query->sql($sql);
				$order_transactions = $query->results();

				// Everything looks good
				if($order_lines && $order_transactions) {

					// Get some additional details
					$sql = "SELECT new_user_id FROM kbhff_dk.ff_persons WHERE uid = ".$order["puid"];
					if($query->sql($sql)) {

						$user_id = $query->result(0, "new_user_id");

						$order_no = $SC->getNewOrderNumber();
						
						$sql = "SELECT id FROM kbhff_dk.shop_orders WHERE order_no = '$order_no'";
						$query->sql($sql);
						$order_id = $query->result(0, "id");

					}


					if($order_no && $user_id) {

						// GET USER NICKNAME
						$sql = "SELECT nickname FROM kbhff_dk.users WHERE id = $user_id";
						$query->sql($sql);
						$nickname = $query->result(0, "nickname");


						// INSERT ORDER
						$sql = "UPDATE kbhff_dk.shop_orders SET ";
						
						$sql .= "user_id = $user_id, ";
						$sql .= "country = 'DK', ";
						$sql .= "currency = 'DKK', ";
						$sql .= "status = 2, ";
						$sql .= "payment_status = 2, ";
						$sql .= "shipping_status = 2, ";

						$sql .= "billing_name = '$nickname', ";
						$sql .= "comment = 'Transferred from old system', ";
						$sql .= "created_at = '".$order["created"]."', ";
						$sql .= "modified_at = '".date("Y-m-d H:i:s")."' ";

						$sql .= "WHERE order_no = '$order_no'";
					
						output($sql);

						$query->sql($sql);


						// INSERT ORDERLINES
						foreach($order_lines as $order_line) {

							$item_id = $product_index[$order_line["item"]]["item_id"];

							$sql = "INSERT INTO kbhff_dk.shop_order_items SET ";
							$sql .= "order_id = $order_id, ";
							$sql .= "quantity = ".$order_line["quant"].", ";

							$sql .= "item_id = ".$item_id.", ";

							$sql .= "name = '".$product_index[$order_line["item"]]["explained"]."', ";

							$sql .= "unit_price = ".($order_line["amount"] / $order_line["quant"]).", ";
							$sql .= "unit_vat = ".(($order_line["amount"] / $order_line["quant"]) * 0.2).", ";
							$sql .= "total_price = ".$order_line["amount"].", ";
							$sql .= "total_vat = ".$order_line["amount"] * 0.2;

							$query->sql($sql);

							output($sql);

							// IF PRODUCT (medlemskab/kontingent) UPDATE SUBSCRIPTION ORDER
							if($item_id == $membership_id) {

								$sql = "UPDATE kbhff_dk.user_item_subscriptions SET order_id = $order_id WHERE user_id = $user_id AND item_id = $membership_id";
								output($sql);

								$query->sql($sql);

							}

						}


					
						// INSERT TRANSACTIONS
						foreach($order_transactions as $order_transaction) {

							$sql = "INSERT INTO kbhff_dk.shop_payments SET ";
							$sql .= "order_id = $order_id, ";
							$sql .= "currency = 'DKK', ";
							$sql .= "payment_amount = ".$order_transaction["amount"].", ";
							$sql .= "transaction_id = '".($order_transaction["trans_id"] ? $order_transaction["trans_id"] : (date("Y-m-d", strtotime($order_transaction["created"])) . "(".$order_transaction["method"].")"))."', ";

							$sql .= "payment_method = ".$payment_method_index[$order_transaction["method"]]["id"].", ";

							$sql .= "created_at = '".$order_transaction["created"]."'";

							output($sql);

							$query->sql($sql);


						}


						// ALL ORDER PARTS TRANSFERRED - DELETE ORDER
						$sql = "DELETE FROM kbhff_dk.ff_orderhead WHERE orderno = ".$order["orderno"];
						$query->sql($sql);

						$sql = "DELETE FROM kbhff_dk.ff_orderlines WHERE orderno = ".$order["orderno"];
						$query->sql($sql);

						$sql = "DELETE FROM kbhff_dk.ff_transactions WHERE orderno = ".$order["orderno"];
						$query->sql($sql);


					}
					else {
						output("UNEXPECTED ERROR – MISSING USER");
						print_r($order);
						print_r($order_lines);
						print_r($order_transactions);

						exit();
					}



				}
				else {
					output("UNEXPECTED ERROR");
					print_r($order);
					print_r($order_lines);
					print_r($order_transactions);

					exit();
				}


			}


		}



		// CLEAN FF_PERSONS, SO ONLY PASSWORD REMAINS
		// REMOVE OTHER FF_ TABLES (UNLESS THEY ARE GOOD FOR SOMETHING)
		if($cleanup_operation) {

			$UC->dropTable(SITE_DB.".ff_teams");
			$UC->dropTable(SITE_DB.".ff_statistics_log");
			$UC->dropTable(SITE_DB.".ff_reportfields");
			$UC->dropTable(SITE_DB.".ff_report_data");
			$UC->dropTable(SITE_DB.".ff_producttypes");
			$UC->dropTable(SITE_DB.".ff_pickupdates");
			$UC->dropTable(SITE_DB.".ff_personsjan19");
			$UC->dropTable(SITE_DB.".ff_persons_info");
			$UC->dropTable(SITE_DB.".ff_massmail_log");
			$UC->dropTable(SITE_DB.".ff_log");
			$UC->dropTable(SITE_DB.".ff_items");
			$UC->dropTable(SITE_DB.".ff_itemdays");
			$UC->dropTable(SITE_DB.".ff_division_newmemberinfo");
			$UC->dropTable(SITE_DB.".ff_division_members");
			$UC->dropTable(SITE_DB.".ff_division_chores");

			// Required to login via CI
			// $UC->dropTable(SITE_DB.".ff_roles");
			// $UC->dropTable(SITE_DB.".ff_membernote");
			// $UC->dropTable(SITE_DB.".ff_groups");
			// $UC->dropTable(SITE_DB.".ff_groupmembers");
			// $UC->dropTable(SITE_DB.".ff_divisions");
			// $UC->dropTable(SITE_DB.".ff_chore_types");


			$UC->dropTable(SITE_DB.".ff_orderhead");
			$UC->dropTable(SITE_DB.".ff_orderlines");
			$UC->dropTable(SITE_DB.".ff_transactions");


			// CLEAN UP ff_persons
			// $UC->dropColumn(SITE_DB.".ff_persons", "firstname");
			// $UC->dropColumn(SITE_DB.".ff_persons", "middlename");
			// $UC->dropColumn(SITE_DB.".ff_persons", "lastname");

			$UC->dropColumn(SITE_DB.".ff_persons", "sex");
			$UC->dropColumn(SITE_DB.".ff_persons", "adr1");
			$UC->dropColumn(SITE_DB.".ff_persons", "adr2");
			$UC->dropColumn(SITE_DB.".ff_persons", "streetno");
			$UC->dropColumn(SITE_DB.".ff_persons", "floor");
			$UC->dropColumn(SITE_DB.".ff_persons", "door");
			$UC->dropColumn(SITE_DB.".ff_persons", "adr3");

			$UC->dropColumn(SITE_DB.".ff_persons", "zip");
			$UC->dropColumn(SITE_DB.".ff_persons", "city");
			$UC->dropColumn(SITE_DB.".ff_persons", "country");

			$UC->dropColumn(SITE_DB.".ff_persons", "languagepref");

			$UC->dropColumn(SITE_DB.".ff_persons", "tel");
			$UC->dropColumn(SITE_DB.".ff_persons", "tel2");

			$UC->dropColumn(SITE_DB.".ff_persons", "birthday");
			$UC->dropColumn(SITE_DB.".ff_persons", "user_activation_key");

			$UC->dropColumn(SITE_DB.".ff_persons", "status1");
			$UC->dropColumn(SITE_DB.".ff_persons", "status2");
			$UC->dropColumn(SITE_DB.".ff_persons", "status3");

			$UC->dropColumn(SITE_DB.".ff_persons", "rights");
			$UC->dropColumn(SITE_DB.".ff_persons", "privacy");
			$UC->dropColumn(SITE_DB.".ff_persons", "ownupdate");
			


			output("REMOVED ALL DEPRECATED TABLES");
		}



		exit();


	}

}

$page->page(array(
	"type" => "janitor",
	"templates" => "janitor/restructure/index.php"
));

?>
