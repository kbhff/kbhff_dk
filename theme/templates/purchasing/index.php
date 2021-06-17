<?php
global $action;
global $page;

include_once("classes/system/department.class.php");
$DC = new Department();
include_once("classes/shop/pickupdate.class.php");
$PC = new Pickupdate();
include_once("classes/shop/supershop.class.php");
$SC = new SuperShop();
$UC = new User();
$IC = new Items();

$upcoming_pickupdates = $PC->getPickupdates(["after" => date("Y-m-d")]);
$next_pickupdate = $upcoming_pickupdates ? $upcoming_pickupdates[0] : false;

$departments = $DC->getDepartments();
$products = $IC->getItems(["where" => "itemtype REGEXP '^product'", "status" => 1, "order" => "created_at", "extend" => ["mediae" => true]]);

?>

<div class="scene purchasing i:purchasing">

	<div class="banner i:banner variant:random format:jpg"></div>

	<h1>Indkøb</h1>

	<?= $HTML->serverMessages(); ?>

	<? if($next_pickupdate): ?>
	<div class="c-wrapper order-list">
		<h2>Ordrer til udlevering <?= date("d.m.Y", strtotime($next_pickupdate["pickupdate"])) ?></h2>
		<ul class="list">
			<li class="labels">
				<span class="departments" title="Afdelinger">Afdelinger</span>
				<? foreach($products as $product): ?>
				<span class="product" title="<?= $product["name"] ?>"><?= $product["name"] ?></span>
				<? endforeach; ?>
			</li>

			<? foreach($departments as $department): ?>
			<li class="listing">
				<span class="department" title="<?= $department["name"] ?>"><?= $department["name"] ?></span>
				
				<? foreach($products as $product): 
					$department_pickupdate_product_order_items = $SC->getPickupdateOrderItems($next_pickupdate["id"], ["department_id" => $department["id"], "item_id" => $product["id"]]);
					$department_pickupdate_product_order_item_count = 0;
					if($department_pickupdate_product_order_items) {
						foreach ($department_pickupdate_product_order_items as $order_item) {
							$department_pickupdate_product_order_item_count += $order_item["quantity"];
						}
					}
				?>

				<span class="order_item_count"><?= $department_pickupdate_product_order_item_count?></span>
				<? endforeach; ?>
			</li>
			<? endforeach; ?>

			<li class="totals">
				<span class="total">Total</span>
				<? foreach($products as $product): 
					$pickupdate_product_order_items = $SC->getPickupdateOrderItems($next_pickupdate["id"], ["item_id" => $product["id"]]);
					$pickupdate_product_order_item_count = 0;
					if($pickupdate_product_order_items) {
						foreach ($pickupdate_product_order_items as $order_item) {
							$pickupdate_product_order_item_count += $order_item["quantity"];
						}
					}
				?>
				<span class="order_item_count"><?= $pickupdate_product_order_item_count ?></span>
				<? endforeach; ?>
			</li>

		</ul>
	</div>
	<? endif; ?>


	<div class="c-wrapper products">
		<h2>Produkter</h2>
		<p>Hej indkøber! Dette afsnit bruges til at oprette og vedligeholde de posetyper som tilbydes til KBHFFs medlemmer.</p>
		<p>Poserne kan enten oprettes som faste ugentlige valg, eller som specielle tilbud i specifikke perioder. Ved at trykke ‘tilføj nyt produkt’ til højre åbnes en ny menu, hvori disse funktioner kan vælges. Du kan også redigere eller fjerne poser, ved at trykke på de relevante knapper ud for det pågældende produkt.</p>

		<ul class="actions">
			<li class="add"><a href="/indkoeb/nyt-produkt" class="button primary">Tilføj nyt produkt</a></li>
		</ul>

	<? if($products): ?>
		<ul class="list">
			<li class="labels">
				<span class="images"></span>
				<span class="name">Navn</span>
				<span class="availability">Tilgængelighed</span>
				<span class="price">Pris</span>
				<span class="available">Tilgængelig nu?</span>
				<span class="buttons"></span>
			</li>
		<? foreach($products as $product): 

			if( 
				$product["start_availability_date"] 
				&& $product["start_availability_date"] <= date("Y-m-d") 
				&& $product["end_availability_date"] 
				&& $product["end_availability_date"] >= date("Y-m-d")
			) {
				$product_available = true;
				$product_availability = "Fra ".date("d.m.Y", strtotime($product["start_availability_date"]))."<br />Til ".date("d.m.Y", strtotime($product["end_availability_date"]));
			}
			else if( 
				$product["start_availability_date"] 
				&& $product["end_availability_date"] 
			) {
				$product_available = false;
				$product_availability = "Fra ".date("d.m.Y", strtotime($product["start_availability_date"]))."<br />Til ".date("d.m.Y", strtotime($product["end_availability_date"]));
			}
			else if( 
				$product["start_availability_date"] 
				&& $product["start_availability_date"] <= date("Y-m-d") 
			) {
				$product_available = true;
				$product_availability = "Altid";
			}
			else if( 
				$product["start_availability_date"] 
			) {
				$product_available = false;
				$product_availability = "Fra ".date("d.m.Y", strtotime($product["start_availability_date"]));
			}
			else {
				$product_available = false;
				$product_availability = "Ikke tilgængelig";
			}
			
			$product_prices = $IC->getPrices(["item_id" => $product["id"]]);

			$media = $IC->sliceMediae($product, "single_media");
			
			
		?>
			<li class="listing">
			<? if($media): ?>
				<span class="image item_id:<?= $media["item_id"] ?> format:<?= $media["format"] ?> variant:<?= $media["variant"] ?>"></span>
			<? else: ?>
				<span class="image"></span>
			<? endif; ?>
				<span class="name"><?= $product["name"] ?></span>
				<span class="availability"><?= $product_availability ?></span>
			<? if($product_prices): ?>
				<span class='prices'>
					<ul class="prices">
						<? foreach($product_prices as $price): 
						
							switch($price["type"]) {
								case "default"       : $price_type       = "Standard"; break;
								case "offer"         : $price_type       = "Tilbud"; break;
								case "bulk"          : $price_type       = "Mængderabat"; break;
								case "stoettemedlem" : $price_type       = "Støttemedlem"; break;
								case "frivillig"     : $price_type       = "Frivillig"; break;
							
								default              : $price_type       = ""; break;
							}
						
						?>
						<li class="price"><?= $price["price"]." kr. (".$price_type.")" ?></li>
						<? endforeach; ?>
					</ul>
					<? else: ?>
					<?= "-" ?>
					<? endif; ?>
				</span>
				<span class="available"><?= $product_available ? "Ja" : "Nej" ?></span>
				<span class="button">
					<ul class="actions">
						<li class="edit"><a href="/indkoeb/rediger-produkt/<?= $product["id"] ?>" class="button">Rediger</a></li>
					</ul>
				</span>
			</li>
		<? endforeach; ?>
		</ul>
	<? else: ?>
		<p>Ingen produkter</p>
	<? endif; ?>
	</div>


	<div class="c-wrapper pickupdates">
		<h2>Afhentningsdage og lokale åbningsdage</h2>
		<p>
			Dette afsnit bruges til at oprette og vedligeholde afhentningsdage (de dage produkter kan afhentes i afdelingerne), samt hvilke afdelinger 
			har åbent på disse dage.
		</p>
		<p>
			Når du opretter en ny afhentningsdag, kan du kun vælge blandt onsdage, hvor der ikke allerede er oprettet en afhentningsdag. Alle
			afdelinger er som standard åbne på hver afhentningsdag, så du skal klikke af hvis en afdeling f.eks. holder ferielukket.
		</p>
		<ul class="actions">
			<li class="add"><a href="/indkoeb/ny-afhentningsdag" class="button primary">Tilføj ny afhentningsdag</a></li>
		</ul>
	<? if($departments && $upcoming_pickupdates): ?>
		<ul class="list">
			<li class="labels">
				<span class="pickupdates">Afhentningsdage</span>
				<? foreach($departments as $department): ?>

				<span class="availability" title="<?= $department["name"] ?>"><?= $department["abbreviation"] ?></span>
				
				<? endforeach; ?>
				<span class="buttons"></span>
			</li>
		<? foreach($upcoming_pickupdates as $pickupdate): 
			$pickupdate_departments = $DC->getPickupdateDepartments($pickupdate["id"]);
		?>
			<li class="listing">
				<span class="pickupdate"><?= date("d.m.Y", strtotime($pickupdate["pickupdate"])) ?> (uge <?= (int)date("W", strtotime($pickupdate["pickupdate"])) ?>)</span>
			<? foreach($departments as $department): 
				$department_open = arrayKeyValue($pickupdate_departments, "id", $department["id"]);
			?>
				<span class="status <?= $department_open !== false ? "open" : "closed" ?>"><?= $department_open !== false ? "Åben" : "Lukket" ?></span>
			<? endforeach; ?>
				<span class="button">
					<ul class="actions">
						<li class="edit"><a href="/indkoeb/rediger-afhentningsdag/<?= $pickupdate["id"] ?>" class="button">Rediger</a></li>
					</ul>
				</span>
			</li>
		<? endforeach; ?>
		</ul>
	<? else: ?>
		<p>Ingen afhentningsdage</p>
	<? endif; ?>
	</div>
</div>
