<?php
// Get methods for user and shop data manipulation
include_once("classes/users/superuser.class.php");
$UC = new SuperUser();
$SC = new Shop();
$HTML = new HTML();

// Get current user and related department
$user_id = session()->value("user_id");
$user = $UC->getKbhffUser(["user_id" => $user_id]);
$department = $UC->getUserDepartment(["user_id" => $user_id]);


$orders = $SC->getOrders();
$upcoming_order_items = $SC->getOrderItems(["user_id" => $user["id"], "order" => "pp.pickupdate ASC, so.order_no", "department_pickupdate" => "only", "where" => "i.itemtype REGEXP '^product.*' AND pp.pickupdate >= CURDATE()"]);
$past_order_items = $SC->getOrderItems(["user_id" => $user["id"], "order" => "pp.pickupdate DESC, so.order_no", "department_pickupdate" => "only", "where" => "i.itemtype REGEXP '^product.*' AND pp.pickupdate < CURDATE()"]);
// $order_items_no_pickupdate = $SC->getOrderItems(["user_id" => $user["id"], "order" => "so.order_no","department_pickupdate" => "none", "where" => "i.itemtype REGEXP '^product.*'"]);
$order_items_no_pickupdate = $SC->getOrderItems(["user_id" => $user["id"], "order" => "so.order_no","department_pickupdate" => "none"]);

?>

<div class="scene order_history i:order_history">

	<div class="banner i:banner variant:random format:jpg"></div>

	<?= $HTML->serverMessages(["type" => "error"]); ?>

	<h1>Alle Bestillinger</h1>
	<div class="c-wrapper">

		<div class="section orders">

		<? if($upcoming_order_items || $past_order_items || $order_items_no_pickupdate): ?>

			<? if($upcoming_order_items): ?>
			<div class="upcoming_order_items">
				<h3>Kommende bestillinger</h3>

				<div class="info header">
					<span class="pickupdate">Afhentes</span>
					<span class="department">Afdeling</span>
					<span class="product">Vare</span>
					<span class="order_no">Ordre nr.</span>
					<span class="order_date">Bestilt</span>
				</div>
				<ul class="order_items">

				<? foreach($upcoming_order_items as $order_item):
					$order_item_log = $SC->getOrderItemLog($order_item["id"]);

				 ?>

					<li class="order_item<?= $order_item_log && count($order_item_log) > 1 ? " log_entries" : "" ?>">
						<div class="info">
							<span class="pickupdate"><?= date("d.m.Y", strtotime($order_item["pickupdate"])) ?></span>
							<span class="department"><?= $order_item["department"] ?></span>
							<span class="product"><?= $order_item["quantity"] > 1 ? $order_item["quantity"]." x " : ""?><?= $order_item["name"] ?></span>
							<span class="order_no"><?= $order_item["order_no"] ?></span>
							<span class="order_date"><span class="date"><?= date("d.m.Y", strtotime($order_item["created_at"])) ?></span></span>
						</div>
						<? if($order_item_log && count($order_item_log) > 1): ?>
						<ul class="order_item_log_entries">
						<? foreach($order_item_log AS $key => $entry): 
							$log_user = $UC->getKbhffUser(["user_id" => $entry["user_id"]]);
							?>
							<? if($key !== 0): ?>
							<? 
							$log_user_name = $log_user["id"] == $user_id ? false : $log_user["nickname"];
							$log_user_member_no = isset($log_user["member_no"]) ? " (medlem nr. ".$log_user["member_no"].")" : false;
							?>
							<li class="order_item_log_entry">
								<span class = "created_at"><?= date("d.m.Y H:i:s", strtotime($entry["created_at"])) ?></span>: 
								<span class="log_user"><?= $log_user_name ?: "Du" ?></span> 
								<? if($log_user_member_no && $log_user_name): ?>
								<span class="log_user_member_no"><?= $log_user_member_no ?></span> 
								<? endif; ?>
								<? if($order_item_log[$key-1]["pickupdate"] && $entry["pickupdate"]): ?>
								flyttede bestillingen fra
								<span class="old_pickupdate"><?= date("d.m.Y", strtotime($order_item_log[$key-1]["pickupdate"])) ?></span> 
								<span class="old_department">(<?= $order_item_log[$key-1]["department"] ?>)</span> 
								til 
								<span class="new_pickupdate"><?= date("d.m.Y", strtotime($entry["pickupdate"])) ?></span> 
								<span class="new_department">(<?= $entry["department"] ?>)</span>.
								<? elseif($order_item_log[$key-1]["pickupdate"]): ?>
								fjernede bestillingen fra afhentningsdagen
								<span class="old_pickupdate"><?= date("d.m.Y", strtotime($order_item_log[$key-1]["pickupdate"])) ?></span> 
								<span class="old_department">(<?= $order_item_log[$key-1]["department"] ?>)</span>
								<? elseif($entry["pickupdate"]): ?>
								planlagde afhentning af bestillingen:  
								<span class="new_pickupdate"><?= date("d.m.Y", strtotime($entry["pickupdate"])) ?></span> 
								<span class="new_department">(<?= $entry["department"] ?>)</span>
								<? endif; ?>
							</li>
							<? endif; ?>
							<? endforeach; ?>
						</ul>
						<? endif; ?>
					</li>

				<? endforeach; ?>

				</ul>
			</div>
			<? endif; ?>

			<? if($past_order_items): ?>
			<div class="past_order_items">
				<h3>Tidligere bestillinger</h3>

				<div class="info header">
					<span class="pickupdate">Afhentes</span>
					<span class="department">Afdeling</span>
					<span class="product">Vare</span>
					<span class="order_no">Ordre nr.</span>
					<span class="order_date">Bestilt</span>
					<span class="status">Status</span>
				</div>
				<ul class="order_items">

				<? foreach($past_order_items as $order_item):
					$order_item_log = $SC->getOrderItemLog($order_item["id"]);

				 ?>

					<li class="order_item<?= $order_item_log && count($order_item_log) > 1 ? " log_entries" : "" ?>">
						<div class="info">
							<span class="pickupdate"><?= date("d.m.Y", strtotime($order_item["pickupdate"])) ?></span>
							<span class="department"><?= $order_item["department"] ?></span>
							<span class="product"><?= $order_item["quantity"] > 1 ? $order_item["quantity"]." x " : ""?><?= $order_item["name"] ?></span>
							<span class="order_no"><?= $order_item["order_no"] ?></span>
							<span class="order_date"><span class="date"><?= date("d.m.Y", strtotime($order_item["created_at"])) ?></span></span>
							<span class="status"><?= $order_item["shipped_by"] ? "Leveret" : "Ikke leveret" ?></span>
						</div>
						<? if($order_item_log && count($order_item_log) > 1): ?>
						<ul class="order_item_log_entries">
						<? foreach($order_item_log AS $key => $entry): 
							$log_user = $UC->getKbhffUser(["user_id" => $entry["user_id"]]);
							?>
							<? if($key !== 0): ?>
							<? 
							$log_user_name = $log_user["id"] == $user_id ? false : $log_user["nickname"];
							$log_user_member_no = isset($log_user["member_no"]) ? " (medlem nr. ".$log_user["member_no"].")" : false;
							?>
							<li class="order_item_log_entry">
								<span class = "created_at"><?= date("d.m.Y H:i:s", strtotime($entry["created_at"])) ?></span>: 
								<span class="log_user"><?= $log_user_name ?: "Du" ?></span> 
								<? if($log_user_member_no && $log_user_name): ?>
								<span class="log_user_member_no"><?= $log_user_member_no ?></span> 
								<? endif; ?>
								<? if($order_item_log[$key-1]["pickupdate"] && $entry["pickupdate"]): ?>
								flyttede bestillingen fra 
								<span class="old_pickupdate"><?= date("d.m.Y", strtotime($order_item_log[$key-1]["pickupdate"])) ?></span> 
								<span class="old_department">(<?= $order_item_log[$key-1]["department"] ?>)</span> 
								til 
								<span class="new_pickupdate"><?= date("d.m.Y", strtotime($entry["pickupdate"])) ?></span> 
								<span class="new_department">(<?= $entry["department"] ?>)</span>.
								<? elseif($order_item_log[$key-1]["pickupdate"]): ?>
								fjernede bestillingen fra afhentningsdagen
								<span class="old_pickupdate"><?= date("d.m.Y", strtotime($order_item_log[$key-1]["pickupdate"])) ?></span>									<span class="old_department">(<?= $order_item_log[$key-1]["department"] ?>)</span>
								<? elseif($entry["pickupdate"]): ?>
								planlagde afhentning af bestillingen:  
								<span class="new_pickupdate"><?= date("d.m.Y", strtotime($entry["pickupdate"])) ?></span> 
								<span class="new_department">(<?= $entry["department"] ?>)</span>
								<? endif; ?>
							</li>
							<? endif; ?>
							<? endforeach; ?>
						</ul>
						<? endif; ?>
					</li>

				<? endforeach; ?>

				</ul>
			</div>
			<? endif; ?>

			<? if($order_items_no_pickupdate): ?>
			<div class="order_items_no_pickupdate">
				<h3>Bestillinger uden afhentningstid og -sted</h3>
				<p>Skriv til <a href="mailto:it@kbhff.dk">it@kbhff.dk</a> for at føje tid/sted til disse bestillinger.</p>

				<div class="info header">
					<span class="product">Vare</span>
					<span class="order_no">Ordre nr.</span>
					<span class="order_date">Bestilt</span>
				</div>

				<ul class="order_items">

				<? foreach($order_items_no_pickupdate as $order_item):
					$order_item_log = $SC->getOrderItemLog($order_item["id"]);

				 ?>

					<li class="order_item<?= $order_item_log && count($order_item_log) > 1 ? " log_entries" : "" ?>">
						<div class="info">
							<span class="product"><?= $order_item["quantity"] > 1 ? $order_item["quantity"]." x " : ""?><?= $order_item["name"] ?></span>
							<span class="order_no"><?= $order_item["order_no"] ?></span>
							<span class="order_date"><span class="date"><?= date("d.m.Y", strtotime($order_item["created_at"])) ?></span></span>
						</div>

						<? if($order_item_log && count($order_item_log) > 1): ?>
						<ul class="order_item_log_entries">
							<? foreach($order_item_log AS $key => $entry): 
							$log_user = $UC->getKbhffUser(["user_id" => $entry["user_id"]]);
							?>
							<? if($key !== 0): ?>
							<? 
							$log_user_name = $log_user["id"] == $user_id ? false : $log_user["nickname"];
							$log_user_member_no = isset($log_user["member_no"]) ? " (medlem nr. ".$log_user["member_no"].")" : false;
							?>
							<li class="order_item_log_entry">
								<span class = "created_at"><?= date("d.m.Y H:i:s", strtotime($entry["created_at"])) ?></span>: 
								<span class="log_user"><?= $log_user_name ?: "Du" ?></span> 
								<? if($log_user_member_no && $log_user_name): ?>
								<span class="log_user_member_no"><?= $log_user_member_no ?></span> 
								<? endif; ?>
								<? if($order_item_log[$key-1]["pickupdate"] && $entry["pickupdate"]): ?>
								flyttede bestillingen fra 
								<span class="old_pickupdate"><?= date("d.m.Y", strtotime($order_item_log[$key-1]["pickupdate"])) ?></span>									<span class="old_department">(<?= $order_item_log[$key-1]["department"] ?>)</span> 
								til 
								<span class="new_pickupdate"><?= date("d.m.Y", strtotime($entry["pickupdate"])) ?></span> 
								<span class="new_department">(<?= $entry["department"] ?>)</span>.
								<? elseif($order_item_log[$key-1]["pickupdate"]): ?>
								fjernede bestillingen fra afhentningsdagen
								<span class="old_pickupdate"><?= date("d.m.Y", strtotime($order_item_log[$key-1]["pickupdate"])) ?></span>									<span class="old_department">(<?= $order_item_log[$key-1]["department"] ?>)</span>
								<? elseif($entry["pickupdate"]): ?>
								planlagde afhentning af bestillingen:  
								<span class="new_pickupdate"><?= date("d.m.Y", strtotime($entry["pickupdate"])) ?></span> 
								<span class="new_department">(<?= $entry["department"] ?>)</span>
								<? endif; ?>
							</li>
							<? endif; ?>
							<? endforeach; ?>
						</ul>
						<? endif; ?>
					</li>

				<? endforeach; ?>

				</ul>
			</div>
			<? endif; ?>
			
		<? else: ?>
			<p><?= $user["nickname"] ?> har ingen grøntsagsbestillinger.</p>
		<? endif; ?>

		</div>

	</div>
</div>
