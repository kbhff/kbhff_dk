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
}

?>