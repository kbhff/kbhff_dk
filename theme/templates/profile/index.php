<?php
$UC = new User();
$SC = new Shop();
$user = $UC->getKbhffUser();
$department = $UC->getUserDepartment();

$is_member = $user["membership"]["id"];
$is_membership_paid = $user["membership"]["id"] && $user["membership"]["order"]["payment_status"] < 2 ? true : false;

?>



<div class="scene profile i:profile">
	<div class="banner i:banner variant:1 format:jpg"></div>

<?	if(message()->hasMessages()): ?>
	<p class="errormessage">
<?		$messages = message()->getMessages(array("type" => "error"));
	foreach($messages as $message): ?>
		<?= $message ?><br>
<?		endforeach;?>
	</p>
	<p class="message">
<?		$messages = message()->getMessages(array("type" => "message"));
	foreach($messages as $message): ?>
		<?= $message ?><br>
<?		endforeach;?>
	</p>
<?	message()->resetMessages(); ?>

<?	endif; ?>

	<div class="c-wrapper">

		<div class="c-two-thirds">

			<div class="section intro">
				<h2>Velkommen <span class="name"><?= $user['nickname'] ? $user['nickname'] : $user['firstname'] . " " . $user['lastname'] ?></span></h2>
				<p>
					På min side kan du se og rette oplysninger om dig og dit medlemsskab.
					Du kan også se og rette dine eksisterende bestillinger og lave en ny bestilling (åbner GrøntShoppen).
					På sigt er det desuden meningen at du her skal kunne book frivillig-vagter og se nyheder og beskeder fra din lokalafdeling.
				</p>
				<div class="c-box">
					<p>
						<span class="highlight">OBS!</span> Østerbro lokalafdeling mangler en ny team-koordinator.
						Kunne du have lyst til at hjælpe KBHFF med at blive endnu bedre federe at være frivillig i og være med på et helt vildt sjovt
						og sejt hold, så skynd dig at skriv til anders@kbhff.dk eller mød op til mødet d. 8 august kl. 2017
					</p>
				</div>
			</div>

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
					<li class="view-orders"><a href="#" class="button">Se gamle bestillinger</a></li>
					<li class="new-order"><a href="#" class="button primary">Ny bestilling</a></li>
				</ul>
			</div>

		</div>

		<div class="c-one-third">

			<ul class="actions">
				<li class="new-order full-width"><a href="#" class="button primary">Ny bestilling</a></li>
				<li class="book-shift full-width"><a href="#" class="button primary">Ta' en vagt</a></li>
			</ul>

			<div class="section department">
				<div class="c-box">
					<h3><?=$department["name"] ?> lokalafdeling</h3>

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
							<p class="over">Åbningtider</p>
							<p class="under"><?= $department["opening_hours"] ?></p>
						</div>

						<div class="department-info">
							<p class="over">Kontakt</p>
							<p class="under">Mail: <?= $department["email"] ?></p>
						</div>
					</div>

				</div>
			</div>


			<div class="section membership">
				<div class="c-box">
					<h3>Dit medlemsskab</h3>

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
							<p class="over">Lokalafdeling</p>
							<p class="under"><?= $department["name"] ? $department["name"] : "(ingen)" ?></p>
						</div>

						<ul class="actions">
							<li class="change-info third-width"><a href="/profil/afdeling" class="button">Ret</a></li>
							<li class="cancel-membership third-width"><a href="/profil/opsig" class="button warning">Opsig</a></li>
							<li class="pay-membership third-width"><a href="/profil/mine-ordrer<?= $is_membership_paid ? "" : "/".$user["membership"]["order"]["order_no"] ?>" class="button<?= $is_member && !$is_membership_paid ? " primary" : "" ?>"><?= $is_member && !$is_membership_paid ? "Betal" : "Ordrer" ?></a></li>
						</ul>
					</div>

				</div>
			</div>

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

		</div>


	</div>
</div>
