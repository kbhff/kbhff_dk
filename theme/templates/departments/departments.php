<?php
$IC = new Items();
global $action;

$item = $IC->getItem(array("tags" => "page:departments", "status" => 1, "extend" => array("tags" => true, "user" => true, "mediae" => true, "comments" => true, "readstate" => true)));
if($item) {
	$this->sharingMetaData($item);
}

// define which model this controller is associated with
include_once("classes/system/department.class.php");

$model = new Department();
$departments = $model->getDepartments(["order" => "name ASC"]);

?>

<div class="scene departments i:departments">

	<?	if($item):
	$media = $IC->sliceMediae($item, "single_media"); ?>

	<div class="article i:article id:<?= $item["item_id"] ?>" itemscope itemtype="http://schema.org/NewsArticle">

		<? if($media): ?>
		<div class="image item_id:<?= $item["item_id"] ?> format:<?= $media["format"] ?> variant:<?= $media["variant"] ?>">
			<p>Image: <a href="/images/<?= $item["item_id"] ?>/<?= $media["variant"] ?>/500x.<?= $media["format"] ?>"><?= $media["name"] ?></a></p>
		</div>
		<? endif; ?>


		<h1 itemprop="headline"><?= $item["name"] ?></h1>

		<div class="articlebody" itemprop="articleBody">
			<?= $item["html"]?>
		</div>

		<?
		$mediae = $IC->filterMediae($item, "mediae");
		if($mediae): ?>
			<? foreach($mediae as $media): ?>
		<div class="image item_id:<?= $item["item_id"] ?> format:<?= $media["format"] ?> variant:<?= $media["variant"] ?>">
			<p>Image: <a href="/images/<?= $item["item_id"] ?>/<?= $media["variant"] ?>/500x.<?= $media["format"] ?>"><?= $media["name"] ?></a></p>
		</div>
			<? endforeach; ?>
		<? endif; ?>


	</div>


	<div class="departmentlist">
		<ul class="departments">

<?			foreach($departments as $department): ?>
			<li class="department department_id:<?= $department["id"] ?>" itemtype="http://schema.org/LocalBusiness" itemscope class="company">


				<h3 class="name" itemprop="name"><a href="/afdelinger/<?= $department["name"] ?>"><?= $department["name"] ?></a></h3>
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
				<div class="description" itemprop="description">
					<?= $department["description"] ?>
				</div>
			 </li>
<?			endforeach; ?>

		</ul>
	</div>


<? else: ?>


	<h1>Hov!</h1>
	<h2>Der skete en fejl.</h2>
	<p>Vi kunne ikke finde den ønskede side.</p>


<? endif; ?>


</div>
