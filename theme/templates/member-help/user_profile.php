<?php
global $model;
global $action;
// Get methods for user and shop data manipulation
$UC = new SuperUser();
$SC = new SuperShop();

// Get current user and related department
$user_id = $action[1];

$member_user = $UC->getKbhffUser(["user_id" => $user_id]);
$department = $UC->getUserDepartment(["user_id" => $user_id]);
$user_name = $member_user['nickname'] ? $member_user['nickname'] : $member_user['firstname'] . " " . $member_user['lastname'];

// Get membership status
$is_member = $member_user["membership"] ? $member_user["membership"]["id"] : false;
$is_active = isset($member_user["membership"]["subscription_id"]) ? true : false;
$is_membership_paid = $is_member && $is_active && $member_user["membership"]["order"]["payment_status"] == 2 ? true : false;

$orders = $SC->getOrders(["user_id" => $user_id]);
$order_items_pickupdates = false;
if($orders) {
	$order_items_pickupdates = $SC->getOrderItemsPickupdates($user_id, ["after" => date("Y-m-d")]);
}

$has_accepted_terms = $UC->hasAcceptedTerms(["user_id" => $user_id]);


$unpaid_membership = $UC->hasUnpaidMembership(["user_id" => $user_id]);
$unpaid_orders = $SC->getUnpaidOrders(["user_id" => $user_id]);
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
				<h3><span class="highlight">OBS! </span><?= $user_name ?> har ikke accepteret betingelserne.</h3>
				<?= $model->formStart("brugerprofil/$action[1]/accepter", array("class" => "accept_terms labelstyle:inject")) ?>
				<fieldset>
					<div class="terms">
						<h3>Godkend brug af oplysninger</h3>
						<p class="metatext">Vis denne side til personen og bed personen selv om at sætte krydset.</p>
						<p>Vi opbevarer og anvender følgende informationer om dig: Dit navn, e-mailadresse, telefonnumer og indkøb hos os.</p>
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
				<h3>OBS! <?= $user_name ?> har ubetalte ordrer</h3>
				<p>Ubetalte grøntsagsbestillinger vil blive automatisk slettet en uge inden den førstkommende afhentningsdag.</p>
				<? if($unpaid_membership): ?>
				<p>Hvis man har et ubetalt indmeldelsesgebyr eller kontingent, skal det betales før medlemmet kan bestille grøntsager.</p>
				<? endif; ?>
				<ul class="actions">
					<li class="pay"><a href="/medlemshjaelp/betalinger/<?= $user_id ?>" class="button">Betal udestående</a></li>
				</ul>
			</div>
			<? elseif($unpaid_orders && count($unpaid_orders) == 1 && !$unpaid_membership): ?>
			<div class="c-box alert unpaid orders">
				<h3>OBS! <?= $user_name ?> har en ubetalt ordre</h3>
				<p>Ubetalte grøntsagsbestillinger vil blive automatisk slettet en uge inden den førstkommende afhentningsdag.</p>
				<ul class="actions">
					<li class="pay"><a href="/medlemshjaelp/betaling/<?= $unpaid_orders[0]["order_no"] ?>" class="button">Betal udestående</a></li>
				</ul>
			</div>
			<? elseif($unpaid_membership && $unpaid_membership["type"] == "signupfee"): ?>
			<div class="c-box alert unpaid signupfee">
				<h3>OBS! <?= $user_name ?> mangler at betale sit indmeldelsesgebyr</h3>
				<p>Indmeldelsesgebyret skal betales før medlemmet kan bestille grøntsager.</p>
				<ul class="actions">
					<li class="pay"><a href="/medlemshjaelp/betaling/<?= $unpaid_membership["order_no"] ?>" class="button">Betal indmeldelsesgebyr nu</a></li>
				</ul>
			</div>
			<? elseif($unpaid_membership && $unpaid_membership["type"] == "membership"): ?>
			<div class="c-box alert unpaid membership">
				<h3>OBS! <?= $user_name ?> mangler at betale kontingent</h3>
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
				<div class="order_item-headings">
					<h4 class="pickupdate">AFH.DATO</h4>
					<h4 class="order_item-product">VARE(R)</h4>
					<h4 class="order-status">STATUS</h4>
					<h4 class="change-untill">RET INDTIL</h4>
				</div>


				<? foreach($order_items_pickupdates as $pickupdate): 
					$pickupdate_order_items = $SC->getPickupdateOrderItems($pickupdate["id"], ["user_id" => $member_user["id"]]);
				?>
					<? if($pickupdate_order_items): ?>
					<div class="order_items">
						<? foreach($pickupdate_order_items as $order_item): ?>
						<? $order = $SC->getOrders(["order_id" => $order_item["order_id"]]) ?>
						<div class="order_item">
							<p class="pickupdate"><?= $pickupdate["pickupdate"] ?></p>
							<p class="order_item-product"><?= $order_item["quantity"] > 1 ? $order_item["quantity"]." x " : ""?><?= $order_item["name"] ?></p>
							<? if($order["payment_status"] == 2): ?>
							<p class="order-status"><span class='paid'>Betalt</span></p>
							<? else: ?>
							<p class="order-status"><a href="/medlemshjaelp/betaling/<?= $order["order_no"] ?>" class="unpaid">Ikke betalt</a></p>
							<? endif; ?>
							<p class="change-untill"><span class="date"><?= date("d/m", strtotime($pickupdate["pickupdate"]." - 1 week")) ?></span> kl. <span class="time">23:59</span></p>
							<ul class="actions change"><li class="change"><a href="/medlemshjaelp/ret-bestilling/<?= $order_item["id"] ?>/<?= $user_id ?>" class="button <?= date("Y-m-d") > date("Y-m-d", strtotime($pickupdate["pickupdate"]." - 1 week")) ? "disabled" : "" ?>">Ret</a></li></ul>
						</div>
						<? endforeach; ?>
					</div>
					<? endif; ?>
				<? endforeach; ?>	
				<? else: ?>
				<div>
					<p><?= $user_name ?> har ingen aktuelle grøntsagsbestillinger.</p>					
				</div>
				<? endif; ?>

				<ul class="actions">
					<!-- <li class="view-orders"><a href="#" class="button">Se gamle bestillinger</a></li> -->
					<li class="new-order"><a <?= $unpaid_membership ? "" : "href='/medlemshjaelp/butik/$user_id'" ?> class="button primary <?= $unpaid_membership ? "disabled" : "" ?>">Ny bestilling</a></li>

				</ul>
			</div>
			<? else: ?>
			<div class="section not_member">
			<h3><?= $user_name ?> er ikke medlem</h3>
			<? 

			$carts = $SC->getCarts(["user_id" => $user_id]);
			$cart = $carts ? $carts[0] : false;

			if($cart && $SC->hasSignupfeeInCart($cart["id"])): ?>
			<p><?= $user_name ?> er endnu ikke medlem, men har et indmeldelsesgebyr i sin kurv – <a href="/medlemshjaelp/butik/kurv/<?= $cart["cart_reference"] ?>">Gå til kurv</a></p>
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
							<li class="change-department third-width"><a href="<?=$action[1]?>/afdeling" class="button">Ret afd.</a></li>
							<li class="change-membership third-width"><a href="<?=$action[1]?>/medlemskab" class="button">Ret Med.</a></li>
							<li class="cancel-membership third-width"><a href="<?=$action[1]?>/opsig" class="button warning">Opsig</a></li>
							
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

			<div class="section renewal">
				<div class="c-box">
					<h3>Medlemskabsfornyelse</h3>

					<div class="fields">
						<div class="membership-info renewal">

							<p class="over">Automatisk fornyelse</p>
							<p class="under"><?= $UC->getUserRenewalOptOut($user_id) ? "Nej" : "Ja" ?></p>
							
						</div>
						<ul class="actions">
							<li class="change-renewal full-width"><a href="/medlemshjaelp/brugerprofil/<?= $user_id ?>/fornyelse" class="button">Ret</a></li>
						</ul>
					</div>

				</div>
			</div>

		</div>
	</div>
</div>

