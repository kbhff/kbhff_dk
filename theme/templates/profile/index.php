<?php 
$UC = new User();
$user = $UC->getKbhffUser();
$department = $UC->getUserDepartment();
?>

<div class="scene profile i:profile">
	<img src="/img/deprecated/banner.jpg"	/>

	<div class="c-wrapper">

		<div class="c-two-thirds">

			<div class="section intro">
				<h2>Velkommen <?= $user['nickname'] ? $user['nickname'] : $user['firstname'] . " " . $user['lastname'] ?></h2>
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
					<li class="new-order"><a href="#" class="button primary">Ny bestilling</a></li>
					<li class="view-orders"><a href="#" class="button">Se gamle bestillinger</a></li>
				</ul>
			</div>

		</div>

		<div class="c-one-third">

			<ul class="actions">
				<li class="new-order full-width"><a href="#" class="button primary">Ny bestilling</a></li>
				<li class="book-shift full-width"><a href="#" class="button primary">Book en vagt</a></li>
			</ul>

			<div class="section department">
				<div class="c-box">
					<h3><?=$department["name"] ?> lokalafdeling</h3>

					<div class="department-info">
						<p class="over">Adresse:</p>
						<p class="under">
							<?= 
								$department["address2"] 
								? $department["address1"] . " (" . $department["address2"] . "), " . $department["postal"] . " " . $department["city"]
								: $department["address1"] . ", " . $department["postal"] . " " . $department["city"]
							?>
						</p>
					</div>

					<div class="department-info">
						<p class="over">Åbningtider:</p>
						<p class="under"><?= $department["opening_hours"] ?></p>
					</div>

					<div class="department-info">
						<p class="over">Kontakt:</p>
						<p class="under">Mail: <?= $department["email"] ?></p>
					</div>

				</div>
			</div>


			<div class="section membership">
				<div class="c-box">
					<h3>Dit medlemsskab</h3>
					
					<div class="membership-info">
						<p class="over">Medlemsnummer:</p>
						<p class="under">54321</p>
					</div>

					<div class="membership-info">
						<p class="over">Status:</p>
						<p class="under system-warning">Ikke betalt</p>
					</div>

					<div class="membership-info">
						<p class="over">Medlemstype:</p>
						<p class="under">Frivillig</p>
					</div>

					<div class="membership-info">
						<p class="over">Lokalafdeling:</p>
						<p class="under"><?= $department["name"] ?></p>
					</div>

					<ul class="actions">
						<li class="change-info third-width"><a href="/profil/afdeling" class="button">Ret</a></li>
						<li class="cancel-membership third-width"><a href="#" class="button warning">Opsig</a></li>
						<li class="cancel-membership third-width"><a href="#" class="button primary">Betal</a></li>
					</ul>

				</div>
			</div>

			<div class="section user">
				<div class="c-box">
					<h3>Dine brugeroplysninger</h3>
					
					<div class="user-info">
						<p class="over">Navn:</p>
						<p class="under"><?= $user["nickname"] ?></p>
					</div>

					<div class="user-info">
						<p class="over">Email:</p>
						<p class="under"><?= $user["email"] ?></p>
					</div>

					<div class="user-info">
						<p class="over">Mobil:</p>
						<p class="under"><?= $user["mobile"] ?></p>
					</div>

					<div class="user-info">
						<p class="over">Password:</p>
						<p class="under">.........</p>
					</div>

					<ul class="actions">
						<li class="change-info half-width"><a href="/profil/bruger" class="button">Ret</a></li>
					</ul>
					
				</div>
			</div>

		</div>


	</div>
</div>