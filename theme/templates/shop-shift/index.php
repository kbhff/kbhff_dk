	<? 
global $TC;
global $SC;
global $action;

include_once("classes/users/superuser.class.php");
$UC = new SuperUser();

include_once("classes/shop/pickupdate.class.php");
$PC = new Pickupdate();

$department = $UC->getUserDepartment(["user_id" => session()->value("user_id")]);

$tally = $TC->getTally(["department_id" => $department ? $department["id"] : false]);
$tally_id = $tally ? $tally["id"] : false;

$pickupdate = false;

// get today's pickupdate, or closest past pickupdate (look 1 week back)
$previous_pickupdate = $PC->getPickupdates(["after" => date("Y-m-d", strtotime("-8 days")), "before" => date("Y-m-d")]);
// get future pickupdates
$upcoming_pickupdates = $PC->getPickupdates(["after" => date("Y-m-d"), "before" => date("Y-m-d", strtotime("+15 days"))]);

$all_pickupdates = false;
if($previous_pickupdate && $upcoming_pickupdates) {
	$all_pickupdates = array_merge($previous_pickupdate, $upcoming_pickupdates);
}
elseif ($previous_pickupdate) {
	$all_pickupdates = $previous_pickupdate;
}
elseif ($upcoming_pickupdates) {
	$all_pickupdates = $upcoming_pickupdates;
}

$pickupdate_is_today = true;
if($action) {
	$pickupdate = $PC->getPickupdate(["id" => $action[0]]);
	$pickupdate_is_today = false;
}
if(!$pickupdate) {
	$pickupdate = $PC->getPickupdate(["pickupdate" => date("Y-m-d")]);
}
if(!$pickupdate) {
	
	$next_pickupdate = $upcoming_pickupdates ? $upcoming_pickupdates[0] : false;
	$pickupdate = $next_pickupdate;
	$pickupdate_is_today = false;
}


$department_pickupdate_order_items = $pickupdate ? $SC->getPickupdateOrderItems($pickupdate["id"], ["department_id" => $department["id"], "order" => "nickname, created_at ASC"]) : false;

$order_items_total_quantity = 0;
$order_items_total_delivered_quantity = 0;
$order_items_total_undelivered_quantity = 0;

if($department_pickupdate_order_items) {

	foreach ($department_pickupdate_order_items as $order_item) {
		
		$order_items_total_quantity += $order_item["quantity"];
		if(isset($order_item["shipped_by"])) {
			$order_items_total_delivered_quantity += $order_item["quantity"];
		}
		else {
			$order_items_total_undelivered_quantity += $order_item["quantity"];
		}
	}
}
?>

<div class="scene shop_shift i:shop_shift">
	
	<div class="banner i:banner variant:1 format:jpg"></div>

	<h1>Ordreliste <span class="value"><?= $department["name"] ?: " - " ?></h1>
	<? if($all_pickupdates && $pickupdate): ?>
	<div class="c-wrapper">
		<div class="c-two-thirds info">

			<div class="intro">
				<p>I tabellen herunder kan du som kassemester se aktuelle og fremtidige bestillinger i din KBHFF-afdeling. Hvert punkt svarer til en vare, og derfor kan der godt være flere linjer med samme navn, hvis der er bestilt flere varer (grøntsagspose og frugtpose fx).</p>
				<p>Kassemestre kan på en aktuel afhentningsdag bruge ‘Udlever’-knapperne til at registrere, at medlemmer har fået udleveret deres varer.</p>
			</div>

			<?= $PC->formStart("selectPickupdate", ["class" => "labelstyle:inject form choose_date"]); ?>
				<?= $PC->input("pickupdate_id", ["label" => "Udleveringsdag", "type" => "select", "value" => $pickupdate ? $pickupdate["id"] : false, "options" => $PC->toOptions($all_pickupdates, "id", "pickupdate")]); ?>
				<ul class="actions">
					<?= $PC->submit("Vælg", ["wrapper" => "li.select", "class" => "primary"]); ?>
				</ul>
			<?= $PC->formEnd(); ?>

		</div>
		<div class="c-one-third tally">

			<ul class="actions">
				<li class="shop"><a href="/butiksvagt/kasse/<?= $tally_id ?>" class="button primary">Åbn kasseregnskab</a></li>
			</ul>

		</div>

		<ul class="status">
			<li class="department">Afdeling: <span class="value"><?= $department["abbreviation"] ?: " - " ?></span></li>
			<li class="ordered">Bestilt: <span class="value"><?= $order_items_total_quantity ?></span></li>
			<li class="delivered">Udleveret: <span class="value"><?= $order_items_total_delivered_quantity ?></span></li>
			<li class="undelivered">Afventer: <span class="value"><?= $order_items_total_undelivered_quantity ?></span></li>
		</ul>

		<div class="orders items">
		<? if($department_pickupdate_order_items): ?>
			<ul class="list">
				<li class="labels">
					<span class="user_names">Navn</span>
					<span class="product_names">Vare</span>
					<span class="buttons"></span>
				</li>
			<? foreach($department_pickupdate_order_items as $order_item): 

				$user = $UC->getUsers(["user_id" => $order_item["user_id"]]);
				
			?>
				<li class="listing order_item_id:<?= $order_item["id"] ?>">
					<span class="user_name"><?= $user ? $user["nickname"] : "" ?></span>
					<span class="product_name"><?= $order_item["quantity"] ?> x <?= $order_item["name"] ?></span>
					<span class="button">
						<? if($pickupdate_is_today): ?>
						<ul class="actions">
							<? if(!isset($order_item["shipped_by"])): ?>
							<?= $HTML->oneButtonForm("Udlevér", "/butiksvagt/updateShippingStatus/".$order_item["order_id"]."/".$order_item["id"], [
								"inputs" => [
									"shipped" => true,
								],
								"confirm-value" => "Bekræft",
								"wait-value" => "Vent",
								"class" => "primary",
							]) ?>
							<? else: ?>
							<?= $HTML->oneButtonForm("Fortryd", "/butiksvagt/updateShippingStatus/".$order_item["order_id"]."/".$order_item["id"], [
								"inputs" => [
									"shipped" => false,
								],
								"confirm-value" => "Bekræft",
								"wait-value" => "Vent",
							]) ?>
							<? endif; ?>
						</ul>
						<? endif; ?>
					</span>
				</li>
			<? endforeach; ?>
			</ul>
		<? else: ?>
			<p>Ingen produkter til udlevering</p>
		<? endif; ?>
		</div>
	</div>
	<? else: ?>
	<div class="c-wrapper">
		<h2>Fejl</h2>
		<p>Der blev ikke fundet nogen afhentningsdage.</p>
	</div>
	<? endif; ?>

	
</div>