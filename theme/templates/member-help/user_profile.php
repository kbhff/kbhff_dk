<?php
global $model;
global $action;
// Get methods for user and shop data manipulation
$UC = new SuperUser();
$SC = new SuperShop();

// Get current user and related department
$member_user_id = $action[1];

$clerk_user = $UC->getKbhffUser(["user_id" => session()->value("user_id")]);
$clerk_user_user_group = $UC->getUserGroups(["user_group_id" => $clerk_user["user_group_id"]]);

$member_user = $UC->getKbhffUser(["user_id" => $member_user_id]);
$member_user_user_group = $UC->getUserGroups(["user_group_id" => $member_user["user_group_id"]]);

$department = $UC->getUserDepartment(["user_id" => $member_user_id]);
$member_user_name = $member_user['nickname'] ? $member_user['nickname'] : $member_user['firstname'] . " " . $member_user['lastname'];

// Get membership status
$is_member = $member_user["membership"] ? $member_user["membership"]["id"] : false;
$is_active = isset($member_user["membership"]["subscription_id"]) ? true : false;
$is_membership_paid = $is_member && $is_active && $member_user["membership"]["order"]["payment_status"] == 2 ? true : false;

$orders = $SC->getOrders(["user_id" => $member_user_id]);
$order_items_pickupdates = false;
if($orders) {
	$order_items_pickupdates = $SC->getOrderItemsPickupdates($member_user_id, ["after" => date("Y-m-d")]);
}

$has_accepted_terms = $UC->hasAcceptedTerms(["user_id" => $member_user_id]);


$unpaid_membership = $UC->hasUnpaidMembership(["user_id" => $member_user_id]);
$unpaid_orders = $SC->getUnpaidOrders(["user_id" => $member_user_id]);

// User groups
$allow_user_group_display = false;
$allow_user_group_update = false;
if($is_member && $is_active && $member_user["membership"]["item"]["name"] == "Frivillig" && !($clerk_user_user_group["user_group"] == "Shop shift" && $member_user_user_group["user_group"] != "User")) {
	$allow_user_group_display = true;

	if ($clerk_user_user_group["user_group"] == "Shop shift" && $member_user_user_group["user_group"] == "User") {
		$allow_user_group_update = true;
	}
	// Local administrators can update a User to either Shop shift or Local admin
	elseif ($clerk_user_user_group["user_group"] == "Local administrator" && $member_user_user_group["user_group"] == "User") {
		$allow_user_group_update = ["Shop shift", "Local administrator"];
	}
	elseif (
		(
			$clerk_user_user_group["user_group"] == "Local administrator"
			|| $clerk_user_user_group["user_group"] == "Purchasing group"
			|| $clerk_user_user_group["user_group"] == "Communication group"
		)
		&& (
			$member_user_user_group["user_group"] == "User"
			|| $member_user_user_group["user_group"] == "Shop shift"
			|| $member_user_user_group["user_group"] == "Local administrator"
			|| $member_user_user_group["user_group"] == "Purchasing group"
			|| $member_user_user_group["user_group"] == "Communication group"
		)
		&& $member_user_user_group["user_group"] != $clerk_user_user_group["user_group"]
	) {
		$allow_user_group_update = true;
	}
}
?>



<div class="scene profile user_profile i:user_profile">

	<div class="c-wrapper">
		<div class="c-box obs">
			<h2 class="obs"><span class="highlight">OBS! </span>Handler på vegne af <span class="highlight"><?= $member_user['nickname'] ? $member_user['nickname'] : $member_user['firstname'] . " " . $member_user['lastname'] ?></span></h2>
		</div>
	</div>

	<h1>Brugerprofil</h1>
	
	<div class="c-wrapper">

		<?= $model->serverMessages(["type" => "error"]) ?>
		
		<div class="c-two-thirds">
			<? if(!$has_accepted_terms):?>
			<div class="c-box obs">
				<h3><span class="highlight">OBS! </span><?= $member_user_name ?> har ikke accepteret betingelserne.</h3>
				<?= $model->formStart("brugerprofil/$action[1]/accepter", array("class" => "accept_terms labelstyle:inject")) ?>
				<fieldset>
					<div class="terms">
						<h3>Godkend brug af oplysninger</h3>
						<p class="metatext">Vis denne side til personen og bed personen selv om at sætte krydset.</p>
						<p><em>You must accept the terms of use of your personal information. Please read the <a href="http://kbhff.dk/persondata">English translation of our privacy policy</a>.</em></p>
						<p>Vi opbevarer og anvender følgende informationer om dig: Dit navn, e-mailadresse, telefonnummer og indkøb hos os.</p>
						<p>Vi videregiver ikke disse oplysninger til nogen tredjepart, men du er selv ansvarlig for de persondata, du skriver på foreningens offentlige sider, eksempelvis wikien.</p>
						<p>Det er en forudsætning for dit fortsatte medlemskab, at du giver dit samtykke til disse betingelser. Alternativt kan du melde dig ud af foreningen, og de persondata, vi har om dig, slettes fra vores server i overensstemmelse med vore retningslinjer.</p>
					</div>
					<?= $model->input("terms", array("label" => 'Jeg accepterer <a href="/persondata" target="_blank">KBHFF\'s vilkår og betingelser</a>.', "required" => true, "hint_message" => "Nødvendigt for at blive medlem af KBHFF.", "error_message" => "Man kan ikke være medlem, hvis man ikke accepterer KBHFF's betingelser.")); ?>
				</fieldset>
				<ul class="actions">
					<?= $model->submit("Accepter", array("class" => "primary", "wrapper" => "li.accept_terms")) ?>
				</ul>
				<?= $model->formEnd() ?>
			
			</div>
			<? endif; ?>
			<? if($unpaid_orders && count($unpaid_orders) > 1): ?>
			<div class="c-box alert unpaid orders">
				<h3>OBS! <?= $member_user_name ?> har ubetalte ordrer</h3>
				<p>Ubetalte grøntsagsbestillinger vil blive automatisk slettet en uge inden den førstkommende afhentningsdag.</p>
				<? if($unpaid_membership): ?>
				<p>Hvis man har et ubetalt indmeldelsesgebyr eller kontingent, skal det betales før medlemmet kan bestille grøntsager.</p>
				<? endif; ?>
				<ul class="actions">
					<li class="pay"><a href="/medlemshjaelp/betalinger/<?= $member_user_id ?>" class="button">Betal udestående</a></li>
				</ul>
			</div>
			<? elseif($unpaid_orders && count($unpaid_orders) == 1 && !$unpaid_membership): ?>
			<div class="c-box alert unpaid orders">
				<h3>OBS! <?= $member_user_name ?> har en ubetalt ordre</h3>
				<p>Ubetalte grøntsagsbestillinger vil blive automatisk slettet en uge inden den førstkommende afhentningsdag.</p>
				<ul class="actions">
					<li class="pay"><a href="/medlemshjaelp/betaling/<?= $unpaid_orders[0]["order_no"] ?>" class="button">Betal udestående</a></li>
				</ul>
			</div>
			<? elseif($unpaid_membership && $unpaid_membership["type"] == "signupfee"): ?>
			<div class="c-box alert unpaid signupfee">
				<h3>OBS! <?= $member_user_name ?> mangler at betale sit indmeldelsesgebyr</h3>
				<p>Indmeldelsesgebyret skal betales før medlemmet kan bestille grøntsager.</p>
				<ul class="actions">
					<li class="pay"><a href="/medlemshjaelp/betaling/<?= $unpaid_membership["order_no"] ?>" class="button">Betal indmeldelsesgebyr nu</a></li>
				</ul>
			</div>
			<? elseif($unpaid_membership && $unpaid_membership["type"] == "membership"): ?>
			<div class="c-box alert unpaid membership">
				<h3>OBS! <?= $member_user_name ?> mangler at betale kontingent</h3>
				<p>Kontingentet skal betales før medlemmet kan bestille grøntsager.</p>
				<ul class="actions">
					<li class="pay"><a href="/medlemshjaelp/betaling/<?= $unpaid_membership["order_no"] ?>" class="button">Betal kontingent nu</a></li>
				</ul>
			</div>
			<? endif; ?>
			<? if($is_member): ?>
			<div class="section order_items">
				<h2>Bestillinger</h2>
				
				<? if($order_items_pickupdates): ?>

				<div class="order_items">
					<ul class="order_items">
						<li class="headings">
							<span class="pickupdate">AFH.DATO</span>
							<span class="department">AFH.STED</span>
							<span class="product">VARE(R)</span>
							<span class="status">STATUS</span>
							<span class="change-until">RET INDTIL</span>
							<span class="button"></span>
						</li>
				<? foreach($order_items_pickupdates as $pickupdate): 
					$pickupdate_order_items = $SC->getPickupdateOrderItems($pickupdate["id"], ["user_id" => $member_user["id"]]);

					if($pickupdate_order_items): ?>
						<? foreach($pickupdate_order_items as $order_item): ?>
						<? $order = $SC->getOrders(["order_id" => $order_item["order_id"]]) ?>
						<? $order_item_department_pickupdate = $SC->getOrderItemDepartmentPickupdate($order_item["id"]) ?>
						<li class="order_item order_item_id:<?= $order_item["id"] ?>">
							<span class="pickupdate"><span class="date"><?= date("d.m.Y", strtotime($pickupdate["pickupdate"])) ?></span></span>
							<span class="department"><?= $order_item_department_pickupdate["department"] ?></span>
							<span class="product"><?= $order_item["quantity"] > 1 ? $order_item["quantity"]." x " : ""?><?= $order_item["name"] ?></span>
							<? if($order["payment_status"] == 2): ?>
							<span class="status"><span class="paid">Betalt</span></span>
							<? else: ?>
							<span class="status"><a href="/medlemshjaelp/betaling/<?= $order["order_no"] ?>" class="unpaid">Ikke betalt</a></span>
							<? endif; ?>
							<span class="change-until"><span class="date"><?= date("d.m", strtotime($pickupdate["pickupdate"]." - 1 week")) ?></span> kl. <span class="time">23:59</span></span>
							<span class="button">
								<ul class="actions change">
									<li class="change">
										<? if(date("Y-m-d") > date("Y-m-d", strtotime($pickupdate["pickupdate"]." - 1 week"))): ?>
										<a class="button disabled">Ret</a>
										<? else: ?>
										<a href="/medlemshjaelp/ret-bestilling/<?= $order_item["id"] ?>/<?= $member_user_id ?>" class="button">Ret</a>
										<? endif; ?>
									</li>
								</ul>
							</span>
						</li>
						<? endforeach; ?>
					<? endif; ?>
				<? endforeach; ?>	

						</ul>
					</div>

				<? else: ?>
				<div>
					<p><?= $member_user_name ?> har ingen aktuelle grøntsagsbestillinger.</p>					
				</div>
				<? endif; ?>

				<ul class="actions">
					<!-- <li class="view-orders"><a href="#" class="button">Se gamle bestillinger</a></li> -->
					<li class="new-order"><a <?= $unpaid_membership ? "" : "href='/medlemshjaelp/butik/$member_user_id'" ?> class="button primary <?= $unpaid_membership ? "disabled" : "" ?>">Ny bestilling</a></li>
					<li class="all-orders"><a href="/medlemshjaelp/brugerprofil/<?= $member_user_id ?>/ordre-historik" class="button">Se ældre bestillinger</a></li>

				</ul>
			</div>
			<? else: ?>
			<div class="section not_member">
			<h3><?= $member_user_name ?> er ikke medlem</h3>
			<? 

			$carts = $SC->getCarts(["user_id" => $member_user_id]);
			$cart = $carts ? $carts[0] : false;

			if($cart && $SC->hasSignupfeeInCart($cart["id"])): ?>
			<p><?= $member_user_name ?> er endnu ikke medlem, men har et indmeldelsesgebyr i sin kurv – <a href="/medlemshjaelp/butik/kurv/<?= $cart["cart_reference"] ?>">Gå til kurv</a></p>
			<? else: ?>
			<p>Denne bruger er oprettet i systemet, men har ikke tilknyttet et medlemskab. Kontakt gerne <a href="mailto:it@kbhff.dk">IT-gruppen</a> og send dem et screenshot af dette skærmbillede.
			</p><p>Brugeren kan selv oprette et medlemskab ved at logge ind på sin egen konto med brugernavnet <em><?= $member_user["email"] ?></em>.</p>
			<? endif; ?>
			</div>
			<? endif; ?>

		</div>

		<div class="c-one-third">

			<div class="section membership">
				<div class="c-box">
					<h3>Medlemskabsinfo</h3>

					<div class="fields">
						<div class="membership-info member_no">
							<p class="over">Medlemsnummer</p>
							<p class="under"><?= $is_member ? $member_user["membership"]["id"] : "(intet)" ?></p>
						</div>

						<div class="membership-info payment_status">
							<p class="over">Kontingent</p>
							<p class="under <?= $is_member && $is_active ? ["unpaid", "partial", "paid"][$member_user["membership"]["order"]["payment_status"]] : "" ?>"><?= $is_member && $is_active ? $SC->payment_statuses_dk[$member_user["membership"]["order"]["payment_status"]] : "(intet)" ?></p>
						</div>

						<div class="membership-info type">
							<p class="over">Medlemstype</p>
							<? if($is_member && $is_active): ?>
							<p class="under"><?= $member_user["membership"]["item"]["name"] ?></p>
							<? elseif($is_member): ?>
							<p class="under">Inaktivt medlem</p>
							<? else: ?>
							<p class="under">(intet)</p>
							<? endif; ?>
						</div>

						<div class="membership-info department">
							<p class="over">Afdeling</p>
							<p class="under"><?= $department ? $department["name"] : "(ingen)" ?></p>
						</div>

						<ul class="actions">

							<? if($is_member && $is_active): ?>
							
							<li class="change-department third-width"><a href="<?=$action[1]?>/afdeling" class="button">Ret afd.</a></li>
							<li class="change-membership third-width"><a href="<?=$action[1]?>/medlemskab" class="button">Ret Med.</a></li>
							<li class="cancel-membership third-width"><a href="<?=$action[1]?>/opsig" class="button warning">Opsig</a></li>
							<? elseif($is_member): ?>

							<li class="reactivate-membership half-width"><a href="<?=$action[1]?>/genaktiver" class="button">Genaktiver</a></li>
							<li class="cancel-membership half-width"><a href="<?=$action[1]?>/opsig" class="button warning">Opsig</a></li>
							
							<? endif; ?>
							
						</ul>
					</div>
				</div>
			</div>

			<div class="section user">
				<div class="c-box">
					<h3>Brugeroplysninger</h3>

					<div class="fields">

						<div class="user-info">
							<p class="over">Kaldenavn</p>
							<p class="under"><?= $member_user['nickname'] ? $member_user['nickname'] : "(Ikke angivet)" ?></p>
						</div>

						<div class="user-info">
							<p class="over"> Fulde navn</p>
							<p class="under">
								<?= $member_user['firstname'] ? $member_user['firstname'] : "(Ikke angivet)", " ", $member_user["lastname"] ? $member_user["lastname"] : "(Ikke angivet)" ?>
							</p>
						</div>

						<div class="user-info">
							<p class="over">Email</p>
							<p class="under"><?= $member_user["email"] ? $member_user["email"] : "(Ikke angivet)" ?></p>
						</div>

						<div class="user-info">
							<p class="over">Mobil</p>
							<p class="under"><?= $member_user["mobile"] ? $member_user["mobile"] : "(Ikke angivet)" ?></p>
						</div>

						<ul class="actions">
							<li class="change-info full-width"><a href="<?=$action[1]?>/oplysninger" class="button">Ret</a></li>
						</ul>
					</div>
				</div>
			</div>

			<? if($is_member && $is_active): ?>
			
			<div class="section renewal">
				<div class="c-box">
					<h3>Medlemskabsfornyelse</h3>

					<div class="fields">
						<div class="membership-info renewal">

							<p class="over">Automatisk fornyelse</p>
							<p class="under"><?= $UC->getUserRenewalOptOut($member_user_id) ? "Nej" : "Ja" ?></p>
							
						</div>
						<ul class="actions">
							<li class="change-renewal full-width"><a href="/medlemshjaelp/brugerprofil/<?= $member_user_id ?>/fornyelse" class="button">Ret</a></li>
						</ul>
					</div>

				</div>
			</div>
			<? endif; ?>

			<div class="section user_group">
			<? if($allow_user_group_display): ?>
				<div class="c-box">
					<h3>Brugerrettigheder</h3>

					<? 
						$t_user_groups = [
							"Shop shift" => "Butiksvagt",
							"Local administrator" => "Lokaladministrator",
							"Purchasing group" => "Indkøber",
							"Communication group" => "Kommunikator",
						];
						$user_groups_info = [
							"User" => $member_user_name." har for øjeblikket status som almindeligt frivillig-medlem, der kan købe produkter i webshoppen.",
							"Shop shift" => $member_user_name." har for øjeblikket status som butiksvagt.",
							"Local administrator" => $member_user_name." har for øjeblikket status som lokaladministrator.",
							"Purchasing group" => $member_user_name." har for øjeblikket status som medlem af indkøbsgruppen.",
							"Communication group" => $member_user_name." har for øjeblikket status som medlem af kommunikationsgruppen.",
							"Super User" => $member_user_name." er superbruger.",
							"Developer" => $member_user_name." er medlem af IT-gruppen.",
						];
						$clerk_upgrade_option = [
							"Shop shift" => "Du har mulighed for at indlemme ".($member_user["firstname"] ?: $member_user_name)." i gruppen af butiksvagter.",
							"Local administrator" => "Du har mulighed for at gøre ".($member_user["firstname"] ?: $member_user_name)." til lokaladministrator.",
							"Purchasing group" => "Du har mulighed for at gøre ".($member_user["firstname"] ?: $member_user_name)." til medlem af indkøbsgruppen.",
							"Communication group" => "Du har mulighed for at gøre ".($member_user["firstname"] ?: $member_user_name)." til medlem af kommunikationsgruppen.",
						];
					?>

					<div class="fields">
						<div class="user_group">

							<p><?= $user_groups_info[$member_user_user_group["user_group"]] ?></p>
							<? 
							// Member-user can be upgraded to several user groups
							if($allow_user_group_update && is_array($allow_user_group_update)): 
								$user_groups = $UC->getUsergroups();
							?>
							<p>Du har mulighed for at gøre <?= $member_user["firstname"] ?: $member_user_name ?> til medlem af nedenstående brugergrupper.</p>
								<? foreach($allow_user_group_update as $option): 
									$user_group_key = arrayKeyValue($user_groups, "user_group", $option);
									$user_group_id = $user_group_key ? $user_groups[$user_group_key]["id"] : false;
								?>
								
							<ul class="actions">
								<?= $HTML->oneButtonForm("Opgrader til ".$t_user_groups[$option], "/medlemshjaelp/updateUserUserGroup/".$member_user_id, [
									"confirm-value" => "Bekræft opgradering",
									"wait-value" => "Vent...",
									"wrapper" => "li.upgrade",
									"dom-submit" => true,
									"class" => "full-width",
									"inputs" => [
										"user_group_id" => $user_group_id
									]
								]) ?>
							</ul>
								<? endforeach; ?>
							<? 
							// Member-user can be upgraded to clerk-user's user group
							elseif($allow_user_group_update): ?>
						
							<p><?= $clerk_upgrade_option[$clerk_user_user_group["user_group"]] ?></p>
							
							<ul class="actions">
								<?= $HTML->oneButtonForm("Opgrader til ".$t_user_groups[$clerk_user_user_group["user_group"]], "/medlemshjaelp/updateUserUserGroup/".$member_user_id, [
									"confirm-value" => "Bekræft opgradering",
									"wait-value" => "Vent...",
									"wrapper" => "li.upgrade",
									"dom-submit" => true,
									"class" => "full-width",
									"inputs" => [
										"user_group_id" => $clerk_user_user_group["id"]
									]
								]) ?>
							</ul>
							<? elseif($clerk_user_user_group["user_group"] == "Super User" || $clerk_user_user_group["user_group"] == "Developer"): ?>
							<p>Du har mulighed for at ændre brugergrupper via Janitor.</p>
							<? endif; ?>
							
						</div>
					</div>

				</div>
			<? endif; ?>
			</div>


		</div>
	</div>
</div>

