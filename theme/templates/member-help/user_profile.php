<?php
global $model;
global $action;
// Get methods for user and shop data manipulation
$UC = new SuperUser();
$SC = new SuperShop();

// Get current user and related department
$user_id = $action[1];

$user = $UC->getKbhffUser(["user_id" => $user_id]);
$department = $UC->getUserDepartment(["user_id" => $user_id]);
$user_name = $user['nickname'] ? $user['nickname'] : $user['firstname'] . " " . $user['lastname'];

// Get membership status
$is_member = $user["membership"] ? $user["membership"]["id"] : false;
$is_membership_paid = $is_member && $user["membership"]["order"]["payment_status"] == 2 ? true : false;

$has_accepted_terms = $UC->hasAcceptedTerms(["user_id" => $user_id]);


$unpaid_membership = $UC->hasUnpaidMembership(["user_id" => $user_id]);
$unpaid_orders = $SC->getUnpaidOrders(["user_id" => $user_id]);
?>



<div class="scene profile user_profile i:user_profile">


	<? if(message()->hasMessages()): ?>
	<div class="messages">
	<?
	$all_messages = message()->getMessages();
	message()->resetMessages();
	foreach($all_messages as $type => $messages):
		foreach($messages as $message): ?>
		<p class="<?= $type ?>"><?= $message ?></p>
		<? endforeach;?>
	<? endforeach;?>
	</div>
	<? endif; ?>

	
	<div class="c-wrapper">
		<div class="c-box obs">
			<h2 class="obs"><span class="highlight">OBS! </span>Handler på vegne af <span class="highlight"><?= $user['nickname'] ? $user['nickname'] : $user['firstname'] . " " . $user['lastname'] ?></span></h2>
		</div>
		
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
				<p>Hvis man har et ubetalt indmeldelsesgebyr eller kontingent, vil det blive indkrævet i forbindelse med den næste grøntsagsbestilling. Man kan også betale med det samme ved at klikke nedenfor.</p>
				<? endif; ?>
				<ul class="actions">
					<li class="pay"><a href="/butik/betalinger" class="button">Betal udestående</a></li>
				</ul>
			</div>
			<? elseif($unpaid_orders && count($unpaid_orders) == 1 && !$unpaid_membership): ?>
			<div class="c-box alert unpaid orders">
				<h3>OBS! <?= $user_name ?> har en ubetalt ordre</h3>
				<p>Ubetalte grøntsagsbestillinger vil blive automatisk slettet en uge inden den førstkommende afhentningsdag.</p>
				<ul class="actions">
					<li class="pay"><a href="/butik/betalinger" class="button">Betal udestående</a></li>
				</ul>
			</div>
			<? elseif($unpaid_membership && $unpaid_membership["type"] == "signupfee"): ?>
			<div class="c-box alert unpaid signupfee">
				<h3>OBS! <?= $user_name ?> mangler at betale sit indmeldelsesgebyr</h3>
				<p>Indmeldelsesgebyret vil blive indkrævet i forbindelse med næste grøntsagsbestilling. Man kan også betale det separat ved at klikke nedenfor.</p>
				<ul class="actions">
					<li class="pay"><a href="/butik/betaling/<?= $unpaid_membership["order_no"] ?>" class="button">Betal indmeldelsesgebyr nu</a></li>
				</ul>
			</div>
			<? elseif($unpaid_membership && $unpaid_membership["type"] == "membership"): ?>
			<div class="c-box alert unpaid membership">
				<h3>OBS! <?= $user_name ?> mangler at betale kontingent</h3>
				<p>Kontingentbetaling vil blive indkrævet i forbindelse med næste grøntsagsbestilling. Man kan også betale det separat ved at klikke nedenfor.</p>
				<ul class="actions">
					<li class="pay"><a href="/butik/betaling/<?= $unpaid_membership["order_no"] ?>" class="button">Betal kontingent nu</a></li>
				</ul>
			</div>
			<? endif; ?>
			<div class="section orders">
				<h2>Eksisterende bestillinger</h2>

				<div class="order-headings">
					<h4 class="pickup-date">AFH.DATO</h4>
					<h4 class="order-place">STED</h4>
					<h4 class="order-products">VARE(R)</h4>
					<h4 class="change-untill">RET INDTIL</h4>
				</div>

				<div class="order">
					<p class="pickup-date">23.05.2018</p>
					<p class="order-place">Vesterbro</p>
					<p class="order-products">
						2x Ugens pose
						Aspargespose
						Frugtpose
					</p>
					<p class="change-untill">20/5 kl. 23.59</p>
					<ul class="actions change"><li class="change"><a href="#" class="button disabled">ret</a></li></ul>
				</div>

				<div class="order">
					<p class="pickup-date">30.05.2018</p>
					<p class="order-place">Vesterbro</p>
					<p class="order-products">Ugens pose</p>
					<p class="change-untill">23/5 kl. 23.59</p>
					<ul class="actions change"><li class="change"><a href="#" class="button disabled">ret</a></li></ul>
				</div>

				<div class="order">
					<p class="pickup-date">06.06.2018</p>
					<p class="order-place">Vesterbro</p>
					<p class="order-products">Ugens pose</p>
					<p class="change-untill">30/5 kl. 23.59</p>
					<ul class="actions change"><li class="change"><a href="#" class="button disabled">ret</a></li></ul>
				</div>

				<div class="order">
					<p class="pickup-date">13.06.2018</p>
					<p class="order-place">Vesterbro</p>
					<p class="order-products">
						2x Ugens pose
						Aspargespose
						Frugtpose
					</p>
					<p class="change-untill">6/6 kl. 23.59</p>
					<ul class="actions change"><li class="change"><a href="#" class="button">ret</a></li></ul>
				</div>

				<ul class="actions">
					<!-- <li class="view-orders"><a href="#" class="button">Se gamle bestillinger</a></li> -->
					<li class="new-order"><?= !$has_accepted_terms ? '<a class="button disabled link">' : '<a href="/medlemshjaelp/butik/'.$user_id.'" class="button primary">'?>Ny bestilling</a></li>
				</ul>

			</div>
		</div>

		<div class="c-one-third">

			<div class="section membership">
				<div class="c-box">
					<h3>Medlemskabsinfo</h3>

					<div class="fields">
						<div class="membership-info">
							<p class="over">Medlemsnummer</p>
							<p class="under"><?= $is_member ? $user["membership"]["id"] : "(intet)" ?></p>
						</div>

						<div class="membership-info">
							<p class="over">Kontingent</p>
							<p class="under <?= $is_member ? ["unpaid", "partial", "paid"][$user["membership"]["order"]["payment_status"]] : "" ?>"><?= $is_member ? $SC->payment_statuses_dk[$user["membership"]["order"]["payment_status"]] : "(intet)" ?></p>
						</div>

						<div class="membership-info">
							<p class="over">Medlemstype</p>
							<p class="under"><?= $is_member ? $user["membership"]["item"]["name"] : "(ingen)" ?></p>
						</div>

						<div class="membership-info">
							<p class="over">Afdeling</p>
							<p class="under"><?= $department["name"] ? $department["name"] : "(ingen)" ?></p>
						</div>

						<ul class="actions">
							<li class="change-department third-width"><a href="<?=$action[1]?>/afdeling" class="button">Ret afd.</a></li>
							<li class="change-membership third-width"><a href="<?=$action[1]?>/medlemsskab" class="button">Ret Med.</a></li>
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
							<p class="under"><?= $user['nickname'] ? $user['nickname'] : "(Ikke angivet)" ?></p>
						</div>

						<div class="user-info">
							<p class="over"> Fulde navn</p>
							<p class="under">
								<?= $user['firstname'] ? $user['firstname'] : "(Ikke angivet)", " ", $user["lastname"] ? $user["lastname"] : "(Ikke angivet)" ?>
							</p>
						</div>

						<div class="user-info">
							<p class="over">Email</p>
							<p class="under"><?= $user["email"] ? $user["email"] : "(Ikke angivet)" ?></p>
						</div>

						<div class="user-info">
							<p class="over">Mobil</p>
							<p class="under"><?= $user["mobile"] ? $user["mobile"] : "(Ikke angivet)" ?></p>
						</div>

						<ul class="actions">
							<li class="change-info full-width"><a href="<?=$action[1]?>/oplysninger" class="button">Ret</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

