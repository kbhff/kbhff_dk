<?php
$access_item["/"] = true;
if(isset($read_access) && $read_access) {
	return;
}

// exit();

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$action = $page->actions();
$IC = new Items();
$query = new Query();

include_once("classes/shop/supersubscription.class.php");
$subs_cl = new SuperSubscription();
include_once("classes/shop/supershop.class.php");
$shop_cl = new SuperShop();
include_once("classes/users/superuser.class.php");
$user_cl = new SuperUser();


// session()->value("user_id", 8739);
// session()->value("user_group_id", 2);



function update_2023_05_05() {

	global $action;
	global $IC;
	global $query;
	global $subs_cl;
	global $shop_cl;

	// UPDATE ORDER TEXT – comment = Membership renewed (01/05/2022 - 01/01/1970)
	$sql = "UPDATE ".$shop_cl->db_orders." AS o SET o.comment = 'Membership renewed (01/05/2022 - 01/05/2023)' WHERE o.comment = 'Membership renewed (01/05/2022 - 01/01/1970)'";
	$query->sql($sql);

	// UPDATE ORDER ITEM TEXT – name = Støttemedlem, automatic renewal (01/05/2022 - 01/01/1970)
	$sql = "UPDATE ".$shop_cl->db_order_items." AS o SET o.name = 'Støttemedlem, automatisk fornyelse (01/05/2022 - 01/05/2023)' WHERE o.name = 'Støttemedlem, automatic renewal (01/05/2022 - 01/01/1970)'";
	$query->sql($sql);

	// UPDATE ORDER ITEM TEXT – name = Frivillig, automatic renewal (01/05/2022 - 01/01/1970)
	$sql = "UPDATE ".$shop_cl->db_order_items." AS o SET o.name = 'Frivillig, automatisk fornyelse (01/05/2022 - 01/05/2023)' WHERE o.name = 'Frivillig, automatic renewal (01/05/2022 - 01/01/1970)'";
	$query->sql($sql);



	// SELECT ALL UNPAID MEMBERSHIP ORDERS FROM BEFORE 1/5 AND UPDATE THEM – CANCELLATION WILL CANCEL MEMBERSHIPS

	$sql = "SELECT o.id as order_id, o.user_id as user_id, oi.id AS order_item_id FROM ".$subs_cl->db_subscriptions." AS s, $shop_cl->db_orders AS o, $shop_cl->db_order_items AS oi WHERE s.order_id = o.id AND o.payment_status = 0 AND s.renewed_at = '2022-05-01 00:00:00' AND oi.order_id = o.id AND oi.name LIKE '%2023%'";
	// debug([$sql]);
	$query->sql($sql);
	$unpaid_orders = $query->results();

	foreach($unpaid_orders as $order) {

		// UPDATE ORDER ITEM PRICE = 0
		$sql = "UPDATE ".$shop_cl->db_order_items." AS o SET o.unit_price = 0, o.total_price = 0 WHERE o.id = ".$order["order_item_id"];
		$query->sql($sql);

		// Update payment status
		$shop_cl->validateOrder($order["order_id"]);

	}


	// UPDATE EXPIRES_AT DATE TO 15/5/2023
	$sql = "UPDATE ".$subs_cl->db_subscriptions." AS s SET s.expires_at = '2023-05-15 00:00:00' WHERE s.renewed_at = '2022-05-01 00:00:00' AND s.expires_at IS NULL";
	// debug([$sql]);
	$query->sql($sql);

};
// update_2023_05_05();

function update_2023_05_23() {

	global $query;
	global $subs_cl;


	// UPDATE EXPIRES_AT DATE TO 15/5/2023
	$sql = "UPDATE ".$subs_cl->db_subscriptions." AS s SET s.expires_at = '2024-05-01 00:00:00' WHERE s.expires_at = '2024-05-15 00:00:00'";
	debug([$sql]);
	$query->sql($sql);

}
// update_2023_05_23();



// $u = $user_cl->getDeletableInactiveUsers();
// debug([$u]);

// $user_cl->sendDeletionWarningToInactiveUsers();
//
// $user_cl->deleteInactiveUsers();



// $user_cl->sendCompleteSignupReminder();

// $user_cl->deleteIncompleteSignups();

	



?>
