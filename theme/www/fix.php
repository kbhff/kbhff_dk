<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");

$IC = new Items();
$query = new Query();


include_once("classes/shop/supersubscription.class.php");
$SuperSubscriptionClass = new SuperSubscription();


$sql = "SELECT * FROM ".$SuperSubscriptionClass->db_subscriptions." WHERE expires_at IS NULL";
if($query->sql($sql)) {

	$user_subscriptions = $query->results();
	foreach($user_subscriptions as $user_subscription) {

		$new_expiry = $SuperSubscriptionClass->calculateSubscriptionExpiry("annually", $user_subscription["renewed_at"]);
		// debug([$user_subscription, $new_expiry]);

		$sql = "UPDATE ".$SuperSubscriptionClass->db_subscriptions." SET expires_at = '$new_expiry' WHERE id = ".$user_subscription["id"];
		$query->sql($sql);

		// debug([$sql]);

	}

}
