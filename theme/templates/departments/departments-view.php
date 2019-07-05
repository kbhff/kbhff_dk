<?php
$IC = new Items();
global $action;


// define which model this controller is associated with
include_once("classes/system/department.class.php");

$model = new Department();
$departments = $model->getDepartments(["order" => "name ASC"]);

$department_name = $action[0];

$department = $model->getDepartment(array("name" => $department_name));

?>

<div class="scene departments i:departmentView">

	<?	if($department): ?>


	<div class="c-wrapper">
		
		<div class="c-two-thirds article i:article" itemscope itemtype="http://schema.org/NewsArticle">

			<h1 itemprop="headline"><?= $department["name"] ?></h1>

			<div class="articlebody" itemprop="articleBody">
				<?= $department["html"]?>
			</div>

		</div>

		<div class="c-one-third">

			<div class="c-box address">
				<h3>Adresse</h3>
				<ul class="info">
					<li class="address">
						<ul class="address" itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
							<li class="streetaddress" itemprop="streetAddress">
								<?= $department["address1"] ?><? if($department["address2"]): ?>, <?= $department["address2"] ?><? endif; ?>
							</li>

							<li class="city"><span class="postal" itemprop="postalCode"><?= $department["postal"] ?></span> <span class="locality" itemprop="addressLocality"><?= $department["city"] ?></span></li>
							<li class="country" itemprop="addressCountry" content="Danmark"></li>
						</ul>
					</li>
					<li class="location" itemprop="location" itemscope itemtype="http://schema.org/Place">
						<ul class="geo" itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">
							<li class="name" itemprop="name"><?= $department["geolocation"] ?></li>
							<li class="latitude" itemprop="latitude" content="<?= $department["latitude"] ?>"></li>
							<li class="longitude" itemprop="longitude" content="<?= $department["longitude"] ?>"></li>
						</ul>
					</li>
					<li class="image" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
						<span class="image_url" itemprop="url" content="/img/logo-large.png"></span>
						<span class="image_width" itemprop="width" content="720"></span>
						<span class="image_height" itemprop="height" content="405"></span>
					</li>
			 		<li class="contact">
						<ul class="contact">
							<li class="email"><a href="mailto:<?= $department["email"] ?>" itemprop="email" content="<?= $department["email"] ?>"><?= $department["email"] ?></a></li>
						</ul>
					</li>
					<li class="pricerange" itemprop="priceRange" content="0-100 DKK">1 - 100 kr.</li>
				</ul>
				
			</div>

			<div class="c-box opening_hours">
				<h3>Åbningstider</h3>
				<p><?= nl2br($department["opening_hours"]) ?></p>
			</div>

			<div class="c-box mobilepay">
				<h3>MobilePay</h3>
				<p>MobilePay betalinger direkte til afdelingen kan se på: <br /><?= $department["mobilepay_id"] ?></p>
			</div>
		
		</div>
		
	</div>

	<? else: ?>


	<h1>Hov!</h1>
	<h2>Der skete en fejl.</h2>
	<p>Vi kunne ikke finde den angivne afdeling. Find din afdeling på listen nedenfor.</p>


	<? endif; ?>

	<div class="departmentlist">
		<h2>Alle afdelinger i KBHFF</h2>

		<ul class="departments">

<?			foreach($departments as $department): ?>
			<li class="department department_id:<?= $department["id"] ?>" itemtype="http://schema.org/LocalBusiness" itemscope class="company">


				<h3 class="name" itemprop="name"><a href="/afdelinger/<?= $department["name"] ?>"><?= $department["name"] ?></a></h3>
				<ul class="info">
					<li class="address">
						<ul class="address" itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
							<li class="streetaddress" itemprop="streetAddress">
								<?= $department["address1"] ?>
								<? if($department["address2"]): ?>
								, <?= $department["address2"] ?>
								<? endif; ?>
							</li>

							<li class="city"><span class="postal" itemprop="postalCode"><?= $department["postal"] ?></span> <span class="locality" itemprop="addressLocality"><?= $department["city"] ?></span></li>
							<li class="country" itemprop="addressCountry" content="Danmark"></li>
						</ul>
					</li>
					<li class="location" itemprop="location" itemscope itemtype="http://schema.org/Place">
						<ul class="geo" itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">
							<li class="name" itemprop="name"><?= $department["geolocation"] ?></li>
							<li class="latitude" itemprop="latitude" content="<?= $department["latitude"] ?>"></li>
							<li class="longitude" itemprop="longitude" content="<?= $department["longitude"] ?>"></li>
						</ul>
					</li>
					<li class="image" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
						<span class="image_url" itemprop="url" content="/img/logo-large.png"></span>
						<span class="image_width" itemprop="width" content="720"></span>
						<span class="image_height" itemprop="height" content="405"></span>
					</li>
			 		<li class="contact">
						<ul class="contact">
							<li class="email"><a href="mailto:<?= $department["email"] ?>" itemprop="email" content="<?= $department["email"] ?>"><?= $department["email"] ?></a></li>
						</ul>
					</li>
					<li class="pricerange" itemprop="priceRange" content="0-100 DKK">1 - 100 kr.</li>
				</ul>
				<div class="description" itemprop="description">
					<?= $department["description"] ?>
				</div>
			 </li>
<?			endforeach; ?>

		</ul>
	</div>




</div>
