<?php
// Get methods for user and shop data manipulation
$UC = new User();
$SC = new Shop();
$HTML = new HTML();

// Get current user and related department
$user = $UC->getKbhffUser();
$department = $UC->getUserDepartment();

// Get membership status
$is_member = $user["membership"] ? $user["membership"]["id"] : false;
$is_active = isset($user["membership"]["subscription_id"]) ? true : false;
$is_membership_paid = $is_member && $is_active && $user["membership"]["order"]["payment_status"] == 2 ? true : false;

$orders = $SC->getOrders();
$order_items_pickupdates = $SC->getOrderItemsPickupdates($user["id"], ["after" => date("Y-m-d")]);

$order_items_without_pickupdate = $SC->getOrderItems(["department_pickupdate" => "none"]);

$unpaid_membership = $UC->hasUnpaidMembership();
$unpaid_orders = $SC->getUnpaidOrders();

?>



<div class="scene profile i:profile">

	<div class="banner i:banner variant:1 format:jpg"></div>

	<?= $HTML->serverMessages(["type" => "error"]); ?>

	<div class="c-wrapper">

		<div class="c-two-thirds">

			<div class="section intro">
				<h1>Velkommen <span class="name"><?= $user['nickname'] ? $user['nickname'] : $user['firstname'] . " " . $user['lastname'] ?></span></h1>
				<p>
					På denne side kan du se og rette oplysninger om dig og dit medlemskab.
					Du kan også se og rette dine eksisterende bestillinger og lave en ny bestilling (åbner GrøntShoppen).
					På sigt er det desuden meningen at du her skal kunne booke frivillig-vagter og se nyheder og beskeder fra din lokalafdeling.
				</p>

				<? if($unpaid_orders && count($unpaid_orders) > 1): ?>
				<div class="c-box alert unpaid orders">
					<h3>OBS! Du har ubetalte ordrer</h3>
					<p>Ubetalte grøntsagsbestillinger vil blive automatisk slettet en uge inden den førstkommende afhentningsdag.</p>
					<? if($unpaid_membership): ?>
					<p>Hvis du har et ubetalt indmeldelsesgebyr eller kontingent, vil det blive indkrævet i forbindelse med din næste grøntsagsbestilling. Du kan også betale med det samme ved at klikke nedenfor.</p>
					<? endif; ?>
					<ul class="actions">
						<li class="pay"><a href="/butik/betalinger" class="button">Betal dit udestående</a></li>
					</ul>
				</div>
				<? elseif($unpaid_orders && count($unpaid_orders) == 1 && !$unpaid_membership): ?>
				<div class="c-box alert unpaid orders">
					<h3>OBS! Du har en ubetalt ordre</h3>
					<p>Ubetalte grøntsagsbestillinger vil blive automatisk slettet en uge inden den førstkommende afhentningsdag.</p>
					<ul class="actions">
						<li class="pay"><a href="/butik/betaling/<?= $unpaid_orders[0]["order_no"] ?>" class="button">Betal dit udestående</a></li>
					</ul>
				</div>
				<? elseif($unpaid_membership && $unpaid_membership["type"] == "signupfee"): ?>
				<div class="c-box alert unpaid signupfee">
					<h3>OBS! Du mangler at betale dit indmeldelsesgebyr</h3>
					<p>Indmeldelsesgebyret vil blive indkrævet i forbindelse med din næste grøntsagsbestilling. Du kan også betale det separat ved at klikke nedenfor.</p>
					<ul class="actions">
						<li class="pay"><a href="/butik/betaling/<?= $unpaid_membership["order_no"] ?>" class="button">Betal indmeldelsesgebyr nu</a></li>
					</ul>
				</div>
				<? elseif($unpaid_membership && $unpaid_membership["type"] == "membership"): ?>
				<div class="c-box alert unpaid membership">
					<h3>OBS! Du mangler at betale kontingent</h3>
					<p>Kontingentbetaling vil blive indkrævet i forbindelse med din næste grøntsagsbestilling. Du kan også betale det separat ved at klikke nedenfor.</p>
					<ul class="actions">
						<li class="pay"><a href="/butik/betaling/<?= $unpaid_membership["order_no"] ?>" class="button">Betal kontingent nu</a></li>
					</ul>
				</div>
				<? endif; ?>

				<!-- <div class="c-box obs">
					<p>
						<span class="highlight">OBS!</span> Østerbro lokalafdeling mangler en ny team-koordinator.
						Kunne du have lyst til at hjælpe KBHFF med at blive endnu bedre federe at være frivillig i og være med på et helt vildt sjovt
						og sejt hold, så skynd dig at skriv til anders@kbhff.dk eller mød op til mødet d. 8 august kl. 2017
					</p>
				</div> -->
			</div>

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
					$pickupdate_order_items = $SC->getPickupdateOrderItems($pickupdate["id"], ["user_id" => $user["id"]]);
					
					if($pickupdate_order_items): ?>
						<? foreach($pickupdate_order_items as $order_item): ?>
						<? $order = $SC->getOrders(["order_id" => $order_item["order_id"]]) ?>
						<? $order_item_department_pickupdate = $SC->getOrderItemDepartmentPickupdate($order_item["id"]); ?>
						<li class="order_item order_item_id:<?= $order_item["id"] ?>">
							<span class="pickupdate"><span class="date"><?= date("d.m.Y", strtotime($pickupdate["pickupdate"])) ?></span></span>
							<span class="department" title="<?= $order_item_department_pickupdate["department"] ?>"><?= $order_item_department_pickupdate["department"] ?></span>
							<span class="product" title="<?= $order_item["name"] ?>"><?= $order_item["quantity"] > 1 ? $order_item["quantity"]." x " : "" ?><?= $order_item["name"] ?></span>
							<? if($order["payment_status"] == 2): ?>
							<span class="status"><span class="paid">Betalt</span></span>
							<? else: ?>
							<span class="status"><a href="/butik/betaling/<?= $order["order_no"] ?>" class="unpaid">Ikke betalt</a></span>
							<? endif; ?>
							<span class="change-until"><span class="date"><?= date("d.m", strtotime($pickupdate["pickupdate"]." - 1 week")) ?></span> kl. <span class="time">23:59</span></span>
							<span class="button">
								<ul class="actions change">
									<li class="change">
									<? if(date("Y-m-d") > date("Y-m-d", strtotime($pickupdate["pickupdate"]." - 1 week"))): ?>
										<a class="button disabled">Ret</a>
									<? else: ?>
										<a href="/butik/ret-bestilling/<?= $order_item["id"] ?>" class="button">Ret</a>
									<? endif; ?>
									</li>
								</ul>
							</span>
						</li>
						<? endforeach; ?>
					<? endif; ?>
				<? endforeach; ?>
				</div>
				
				<? else: ?>
				<div>
					<p>Du har ingen aktuelle grøntsagsbestillinger.</p>
				</div>
				<? endif; ?>

				<ul class="actions">
					<!-- <li class="view-orders"><a href="#" class="button">Se gamle bestillinger</a></li> -->
					<li class="new-order"><a href="/butik" class="button primary">Ny bestilling</a></li>
					<li class="all-orders"><a href="/profil/ordre-historik" class="button">Se alle bestillinger</a></li>
				</ul>
			</div>

			<? else: ?>

			<div class="section not_member">
				<h2>Du er ikke medlem</h2>
				<? 
				$cart = $SC->getCart();
				if($cart && $SC->hasSignupfeeInCart($cart["id"])): ?>
				<p>Du er endnu ikke medlem, men du har et indmeldelsesgebyr i din kurv.</p>
				<ul class="actions">
					<li class="pay"><a href="/butik/betal" class="button primary">Gå til betaling</a></li>
				</ul>
				<? else: ?>
				<p>Meld dig ind nu – <a href="/bliv-medlem">se vores medlemskaber her</a>.</p>
				<? endif; ?>

			</div>

			<? endif; ?>

		</div>

		<div class="c-one-third">

			<? if($is_member): ?>
			<ul class="actions">
				<li class="new-order full-width"><a href="/butik" class="button primary">Ny bestilling</a></li>
				<li class="book-shift full-width"><a href="/medlem/tag-en-vagt" class="button primary">Ta' en vagt</a></li>
			</ul>
			<? endif; ?>

			<? if($department): ?>
			<div class="section department">
				<div class="c-box">
					<h3><?= $department["name"] ?> lokalafdeling</h3>

					<div class="fields">
						<div class="department-info">
							<p class="over">Adresse</p>
							<p class="under">
								<?=
									$department["address2"]
									? $department["address1"] . " (" . $department["address2"] . "), " . $department["postal"] . " " . $department["city"]
									: $department["address1"] . ", " . $department["postal"] . " " . $department["city"]
								?>
							</p>
						</div>

						<div class="department-info">
							<p class="over">Åbningstider</p>
							<p class="under"><?= $department["opening_hours"] ?></p>
						</div>

						<div class="department-info">
							<p class="over">Kontakt</p>
							<p class="under">Mail: <?= $department["email"] ?></p>
						</div>
					</div>

				</div>
			</div>
			<? endif; ?>

			<? if($is_member): ?>
			<div class="section membership">
				<div class="c-box">
					<h3>Dit medlemskab</h3>

					<div class="fields">
						<div class="membership-info member_no">
							<p class="over">Medlemsnummer</p>
							<p class="under"><?= $is_member ? $user["membership"]["id"] : "(intet)" ?></p>
						</div>

						<div class="membership-info payment_status">
							<p class="over">Kontingent</p>
							<p class="under <?= $is_member && $is_active ? ["unpaid", "partial", "paid"][$user["membership"]["order"]["payment_status"]] : "" ?>"><?= $is_member && $is_active ? $SC->payment_statuses_dk[$user["membership"]["order"]["payment_status"]] : "(intet)" ?></p>
						</div>
						
						

						<div class="membership-info type">
							<p class="over"><a href="/bliv-medlem">Medlemstype</a></p>
							<? if($is_member && $is_active): ?>
							<p class="under"><?= $user["membership"]["item"]["name"] ?></p>
							<? elseif($is_member): ?>
							<p class="under">Inaktivt medlem</p>
							<? else: ?>
							<p class="under">(intet)</p>
							<? endif; ?>
						</div>

						<div class="membership-info department">
							<p class="over">Afdeling</p>
							<p class="under"><?= ($department && $department["name"]) ? $department["name"] : "(ingen)" ?></p>
						</div>

						<ul class="actions">
							<? if($is_member && $is_active): ?>
							
							<? $unpaid_membership ? $width = "third-width" : $width = "half-width" ?>
							<li class="change-info <?= $width ?>"><a <?= $is_active ? 'href="/profil/afdeling"' : "" ?> class="button <?= $is_active ? "" : "disabled"?>">Ret</a></li>
							<li class="cancel-membership <?= $width ?>"><a href="/profil/opsig" class="button warning">Opsig</a></li>
							<? if($unpaid_membership): ?>
							<li class="pay-membership <?= $width ?>"><a href="/butik/betaling/"<?=$user["membership"]["order"]["order_no"] ?>" class="button primary">Betal</a></li>"
							<? endif; ?>

							<? elseif($is_member): ?>
							
							<li class="reactivate-membership half-width"><a href="/profil/medlemskab/genaktiver" class="button">Genaktiver</a></li>
							<li class="cancel-membership half-width"><a href="/profil/opsig" class="button warning">Opsig</a></li>
							<? endif; ?>
						</ul>
					</div>

				</div>
			</div>
			<? endif; ?>

			<div class="section user">
				<div class="c-box">
					<h3>Dine brugeroplysninger</h3>

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
							<li class="change-info full-width"><a href="/profil/bruger" class="button">Ret</a></li>
						</ul>

					</div>

				</div>
			</div>

			<div class="section password">
				<div class="c-box">
					<h3>Adgangskode</h3>

					<div class="fields">
						<p class="over">Adgangskode</p>
						<p class="under">***********</p>

						<ul class="actions">
							<li class="change-info full-width"><a href="/profil/kodeord" class="button">Skift adgangskode</a></li>
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
							<p class="under"><?= $UC->getRenewalOptOut() ? "Nej" : "Ja" ?></p>

						</div>
						<ul class="actions">
							<li class="renewal full-width"><a href="/profil/medlemskab/fornyelse" class="button">Ret</a></li>
						</ul>
					</div>

				</div>
			</div>
			<div class="c-box newsletter i:newsletter">
				<h3>Tilmeld Nyhedsbrev</h3>
		
				<form action="//kbhff.us15.list-manage.com/subscribe/post?u=d2a926649ebcf316af87a05bb&amp;id=141ae6f59f" method="post" target="_blank">
					<input type="hidden" name="b_d2a926649ebcf316af87a05bb_141ae6f59f" value="">
					<div class="field email required">
						<label for="input_email">E-mail</label>
						<input type="email" value="" name="EMAIL" id="input_email" />
					</div>

					<ul class="actions">
						<li class="submit full-width"><input type="submit" value="Tilmeld" name="subscribe" class="button full-width" /></li>
					</ul>
				</form>

			</div>
			<div class="c-box message_center i:message_center">
				<h3>Beskedpræferencer</h3>
				<ul class="actions">
					<li class="link full-width">
						<?= $HTML->link("Gå til beskedcenter", "/profil/beskedcenter", ["class" => "button"]) ?>
					</li>
				</ul>
			</div>
			<? endif; ?>

		</div>

	</div>
</div>
