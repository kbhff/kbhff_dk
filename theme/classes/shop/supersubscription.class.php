<?php
/**
* @package janitor.subscription
* Meant to allow local subscription additions/overrides, with superuser privileges
*/

include_once("classes/shop/supersubscription.core.class.php");


class SuperSubscription extends SuperSubscriptionCore {

	/**
	*
	*/
	function __construct() {

		parent::__construct(get_class());

	}

	function allowRenewal($subscription) {
		
		if($subscription) {
			
			$IC = new Items();
			$query = new Query();

			// get item with subscription method
			$item = $IC->getItem(["id" => $subscription["item_id"], "extend" => ["subscription_method" => true]]);
			$user_id = $subscription["user_id"];

			if($item && $user_id) {

				if($item["itemtype"] == "membership") {
	
					$sql = "SELECT * FROM ".SITE_DB.".user_log_agreements WHERE user_id = $user_id AND name = 'disable_membership_renewal'";
					if(!$query->sql($sql) && $item["subscription_method"] && $item["subscription_method"]["duration"] != "*") {
			
						return true;
					}
				}
				else {
					if($item["subscription_method"] && $item["subscription_method"]["duration"] != "*") {
			
						return true;
					}
				}
			}
	
		}

		return false;
	}

	function renewalDenied($subscription) {

		logger()->addLog("SuperUser->renewalDenied: item_id:".$subscription["item_id"].", subscription_id:".$subscription["id"].", user_id:".$subscription["user_id"].", expires_at:".$subscription["expires_at"]);

		include_once("classes/users/supermember.class.php");
		$MC = new SuperMember();

		$IC = new Items();

		// get item with subscription method
		$item = $IC->getItem(["id" => $subscription["item_id"], "extend" => ["subscription_method" => true]]);
		$user_id = $subscription["user_id"];

		if($item && $user_id && $item["itemtype"] == "membership") {

			$member = $MC->getMembers(["user_id" => $user_id]);

			if($member) {

				$MC->cancelMembership(["cancelMembership", $user_id, $member["id"]]);
			}

		}

	}



	// CRON RELATED METHODS


	// #controller#/sendRenewalNotices[/#user_id#]
	function sendRenewalNotices($action) {

		if(count($action) >= 1) {

			global $page;

			$query = new Query();
			$IC = new Items();

			include_once("classes/shop/supershop.class.php");
			$SC = new SuperShop();

			include_once("classes/users/superuser.class.php");
			$UC = new SuperUser();

			$midnight_next_week = date("Y-m-d H:i:s", strtotime("+ 7 days midnight"));

			// send notice to specific user
			if(count($action) == 2) {
				$user_id = $action[1];
				// get all user's subscriptions where expires_at is a week from now
				$sql = "SELECT * FROM ".$this->db_subscriptions." WHERE expires_at = '".$midnight_next_week."' AND user_id = $user_id";
				// debug($sql);
			}
			else {
				// get all subscriptions where expires_at is a week from now
				$sql = "SELECT * FROM ".$this->db_subscriptions." WHERE expires_at = '".$midnight_next_week."'";
				// debug($sql);
			}
			
			if($query->sql($sql)) {
				$expiring_subscriptions = $query->results();
				// debug(["expired_subscriptions", $expired_subscriptions]);
				
				foreach($expiring_subscriptions as $subscription) {
					
					$item = $IC->getItem(["id" => $subscription["item_id"], "extend" => true]);
					$user = $UC->getKbhffUser(["user_id" => $subscription["user_id"]]);
					$user_id = $user["id"];

					// debug([$item]);

					if($this->allowRenewal($subscription)) {

						email()->send(array(
							"values" => array(
								"FROM" => ADMIN_EMAIL,
								"NICKNAME" => $user["nickname"],
								"EXPIRES_AT" => date("d.m.Y",strtotime($subscription["expires_at"]))
							),
							"recipients" => $user["email"],
							"template" => "renewal_notice",
							"track_clicks" => false
						));

						logger()->addLog("SuperSubscription->sendRenewalNotices: renewal notice sent to user_id:".$user_id);
		
					}
					else {

						email()->send(array(
							"values" => array(
								"FROM" => ADMIN_EMAIL,
								"NICKNAME" => $user["nickname"],
								"EXPIRES_AT" => date("d.m.Y",strtotime($subscription["expires_at"]))
							),
							"recipients" => $user["email"],
							"template" => "deactivation_notice",
							"track_clicks" => false
						));

						logger()->addLog("SuperSubscription->sendRenewalNotices: deactivation notice sent to user_id:".$user_id);


					}

					message()->resetMessages();

				}

			}
			return true;
		}

		return false;
	}
}
