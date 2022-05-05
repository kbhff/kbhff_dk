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

$ShopClass = new Shop();


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

$sql = "SELECT * FROM ".$ShopClass->db_order_items." WHERE name LIKE '%01/05/2022 - 01/01/1970%'";
if($query->sql($sql)) {

	$order_items = $query->results();
	foreach($order_items as $order_item) {

		// debug([$order_item]);

		$updated_order_item_name = preg_replace("/automatic renewal/", "automatisk fornyelse", $order_item["name"]);
		$updated_order_item_name = preg_replace("/01\/01\/1970/", "01/05/2023", $updated_order_item_name);

		$sql = "UPDATE ".$ShopClass->db_order_items." SET name = '$updated_order_item_name' WHERE id = ".$order_item["id"];
		$query->sql($sql);

		// debug([$sql]);

	}

}
