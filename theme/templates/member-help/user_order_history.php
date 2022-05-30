<?php
global $model;
global $action;
// Get methods for user and shop data manipulation
$UC = new SuperUser();
$SC = new SuperShop();

// Get current user and related department
$member_user_id = $action[1];

$member_user = $UC->getKbhffUser(["user_id" => $member_user_id]);

$department = $UC->getUserDepartment(["user_id" => $member_user_id]);
$member_user_name = $member_user['nickname'] ? $member_user['nickname'] : $member_user['firstname'] . " " . $member_user['lastname'];

// Get membership status
$is_member = $member_user["membership"] ? $member_user["membership"]["id"] : false;

$orders = $SC->getOrders(["user_id" => $member_user_id]);

$orders = $SC->getOrders(["user_id" => $member_user_id]);
$upcoming_order_items = $SC->getOrderItems(["user_id" => $member_user_id, "order" => "pp.pickupdate ASC, so.order_no", "department_pickupdate" => "only", "where" => "i.itemtype REGEXP '^product.*' AND pp.pickupdate >= CURDATE()"]);
$past_order_items = $SC->getOrderItems(["user_id" => $member_user_id, "order" => "pp.pickupdate DESC, so.order_no", "department_pickupdate" => "only", "where" => "i.itemtype REGEXP '^product.*' AND pp.pickupdate < CURDATE()"]);
$order_items_no_pickupdate = $SC->getOrderItems(["user_id" => $member_user_id, "order" => "so.order_no","department_pickupdate" => "none", "where" => "i.itemtype REGEXP '^product.*'"]);

?>



<div class="scene order_history i:order_history">

	<div class="c-wrapper">
		<div class="c-box obs">
			<h2 class="obs"><span class="highlight">OBS! </span>Handler på vegne af <span class="highlight"><?= $member_user['nickname'] ? $member_user['nickname'] : $member_user['firstname'] . " " . $member_user['lastname'] ?></span></h2>
		</div>
	</div>

	<h1>Alle bestillinger</h1>
	
	<div class="c-wrapper">

		<?= $model->serverMessages(["type" => "error"]) ?>

		<? if($is_member): ?>
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
								$log_user_name = $log_user["nickname"];
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
								$log_user_name = $log_user["nickname"];
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
								$log_user_name = $log_user["nickname"];
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
			<p><?= $member_user_name ?> har ingen aktuelle grøntsagsbestillinger.</p>
			<? endif; ?>

		</div>
		<? else: ?>
		<div class="section not_member">
		<h3><?= $member_user_name ?> er ikke medlem</h3>
		</div>
		<? endif; ?>

	</div>
</div>

