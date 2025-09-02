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
if($action) {
	$pickupdate = $PC->getPickupdate(["id" => $action[0]]);
	$pickupdate_is_today = false;
}
else {
	$pickupdate = $next_pickupdate;
}

$departments = $DC->getDepartments();

$products = $IC->getItems(["where" => "itemtype REGEXP '^product'", "status" => 1, "order" => "created_at", "extend" => ["mediae" => true, "tags" => true]]);
$legacy_products = $IC->getItems(["where" => "itemtype = 'legacyproduct'", "order" => "created_at", "extend" => ["mediae" => true]]);
$all_order_products = array_merge($products, $legacy_products);


// debug([$products]);

if($pickupdate) {

	foreach ($all_order_products as $key => $product) {

		$pickupdate_product_order_items = $SC->getPickupdateOrderItems($pickupdate["id"], ["item_id" => $product["id"]]);
		$pickupdate_product_order_item_count = 0;
		if($pickupdate_product_order_items) {
			foreach ($pickupdate_product_order_items as $order_item) {
				$pickupdate_product_order_item_count += $order_item["quantity"];
			}
		}
		// no orders for legacyproduct -> remove from array
		else if($product["itemtype"] == "legacyproduct") {
			unset($all_order_products[$key]);
			continue;
		}

		$all_order_products[$key]["total_order_item_count"] = $pickupdate_product_order_item_count;

	}

	foreach($departments as $department_key => $department) {
		foreach($all_order_products as $product_key => $product) {
			
			$department_pickupdate_product_order_items = $SC->getPickupdateOrderItems($pickupdate["id"], ["department_id" => $department["id"], "item_id" => $product["id"]]);
			$department_pickupdate_product_order_item_count = 0;
			if($department_pickupdate_product_order_items) {
				foreach ($department_pickupdate_product_order_items as $order_item) {
					$department_pickupdate_product_order_item_count += $order_item["quantity"];
				}
			}

			$departments[$department_key]["product_quantities"][$product["id"]] = $department_pickupdate_product_order_item_count;
		}

	}

	// debug([$all_order_products]);

}

// Sort combined product array (products and legacy products)
usort($all_order_products, function ($a, $b) {
	return ucfirst($a["name"]) <=> ucfirst($b["name"]);
});

// Sort products
usort($products, function ($a, $b) {
	return ucfirst($a["name"]) <=> ucfirst($b["name"]);
});


$disabled_products = $IC->getItems(["where" => "itemtype REGEXP '^product'", "status" => 0, "order" => "created_at", "extend" => ["mediae" => true, "tags" => true]]);
// Sort disabled products
usort($disabled_products, function ($a, $b) {
	return ucfirst($a["name"]) <=> ucfirst($b["name"]);
});

$all_products = array_merge($products, $disabled_products);


// Get product tags
$product_tags = $IC->getTags(["context" => "productgroup"]);

?>

<div class="scene purchasing i:purchasing">

	<div class="banner i:banner variant:random format:jpg"></div>

	<h1>Indkøb</h1>

	<?= $HTML->serverMessages(); ?>

	<div class="c-wrapper choose-pickupdate i:collapseHeader">
		<h2>Vælg afhentningsdag</h2>
	<? if($upcoming_pickupdates): ?>
		<?= $HTML->formStart("selectPickupdate", ["class" => "labelstyle:inject form choose_date"]); ?>
			<?= $HTML->input("pickupdate_id", ["label" => "Udleveringsdag", "type" => "select", "value" => $pickupdate ? $pickupdate["id"] : false, "options" => $HTML->toOptions($upcoming_pickupdates, "id", "pickupdate")]); ?>
			<ul class="actions">
				<?= $HTML->submit("Vælg", ["wrapper" => "li.select", "class" => "primary"]); ?>
			</ul>
		<?= $HTML->formEnd(); ?>
	<? else: ?>
		<p>Der er ingen afhentningsdage.</p>
		<ul class="actions">
			<li class="add"><a href="/indkoeb/ny-afhentningsdag" class="button primary">Tilføj ny afhentningsdag</a></li>
		</ul>
	<? endif; ?>
	</div>

	<? if($pickupdate): ?>
	<div class="c-wrapper order-list i:collapseHeader">
		<h2>Ordrer til udlevering <?= $pickupdate["pickupdate"] ?></h2>

		<div class="c-box">
			<ul class="actions">
				<li class="hide-no-orders">Vis kun bestilte produkter</li>
				<li class="show-no-orders">Vis alle</li>
			</ul>
		</div>
		<table class="orders">
			<tr class="col-labels">
				<th class="departments" title="Afdelinger" scope="col">&nbsp;</th>
				<? foreach($departments as $department): ?>
				<th class="department" scope="col" title="<?= $department["name"] ?>"><span><?= $department["name"] ?></span></th>
				<? endforeach; ?>
				<th class="total" scope="row"><span>Total</span></th>
			</tr>

			<?
			$any_orders = false;
			foreach($all_order_products as $product):
				 if($product["total_order_item_count"] !== 0) {
				 	$any_orders = true;
				 } ?>
			<tr class="product-quantities<?= $product["total_order_item_count"] === 0 ? " no-orders" : "" ?>">
				<th class="product" scope="col" title="<?= $product["name"] ?>"><?= $product["name"] ?></th>
				<? foreach($departments as $department): ?>
				<td class="quantity"><?= $department["product_quantities"][$product["id"]] ?></td>
				<? endforeach; ?>
				<td class="total_quantity"><?= $product["total_order_item_count"] ?></td>
			</tr>
			<? endforeach; ?>
		</table>
		<? if(!$any_orders): ?>
		<p class="no-orders">Der er endnu ingen ordrer til udlevering <?= $pickupdate["pickupdate"] ?>.</p>
		<? endif; ?>

	</div>
	<? endif; ?>

	<div class="c-wrapper products i:collapseHeader">
		<h2>Produkter
			<span class="infohint i:infohint">
				<span class="p">Hej indkøber! Dette afsnit bruges til at oprette og vedligeholde de posetyper og læssalgsvarer som tilbydes til KBHFFs medlemmer.</span>
				<span class="p">Produkterne kan enten oprettes som faste ugentlige valg, eller som specielle tilbud i specifikke perioder. Ved at trykke ‘tilføj nyt produkt’ til højre åbnes en ny menu, hvori disse funktioner kan vælges. Du kan også redigere eller fjerne poser, ved at trykke på de relevante knapper ud for det pågældende produkt.</span>
			</span>
		</h2>
		<ul class="actions">
			<li class="add"><a href="/indkoeb/nyt-produkt" class="button primary">Tilføj nyt produkt</a></li>
		</ul>
	<? if($all_products): ?>
		<div class="filter c-box">
			<ul class="tags">
				<li class="tag all">Alle</li>
				<? foreach($product_tags as $tag): ?>
				<li class="tag" data-context="<?= $tag["context"] ?>" data-value="<?= $tag["value"] ?>"><?= $tag["value"] ?></li>
				<? endforeach; ?>
			</ul>
			<form class="search">
				<div class="field string">
					<label for="product_search">Søg</label>
					<input type="text" name="product_search" id="product_search" />
				</div>
				<ul class="actions">
					<li class="search"><input type="submit" value="Søg" /></li>
				</ul>
			</form>
		</div>
		<ul class="list">
			<li class="labels">
				<span class="images"></span>
				<span class="name">Navn</span>
				<span class="availability">Tilgængelighed</span>
				<span class="price">Pris</span>
				<span class="available">Tilgængelig nu?</span>
				<span class="buttons"></span>
			</li>
		<? foreach($all_products as $product): 

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
			<li class="listing<?= $product["status"] ? "" : " archived" ?>">
			<? if($media): ?>
				<span class="image item_id:<?= $media["item_id"] ?> format:<?= $media["format"] ?> variant:<?= $media["variant"] ?>"></span>
			<? else: ?>
				<span class="image"></span>
			<? endif; ?>
				<span class="name">
					<?= $product["name"] ?>
					<? if($product["tags"]): ?>
					<ul class="tags">
						<? foreach($product["tags"] as $tag):
							if($tag["context"] == "productgroup"): ?>
						<li class="tag" data-context="<?= $tag["context"] ?>" data-value="<?= $tag["value"] ?>"><?= $tag["value"] ?></li>
							<?	endif; 
						endforeach; ?>
					</ul>
					<? endif; ?>
				</span>

			<? if($product["status"]): ?>
				<span class="availability"><?= $product_availability ?></span>
			<? else: ?>
				<span class="availability">Arkiveret</span>
			<? endif; ?>

				<span class='prices'>
					<? if($product_prices && $product["status"]): ?>
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
				<span class="available">
					<? if($product_prices && $product["status"]): ?>
					<?= $product_available ? "Ja" : "Nej" ?>
					<? else: ?>
					<?= "-" ?>
					<? endif; ?>
				</span>

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


	<div class="c-wrapper pickupdates i:collapseHeader">
		<h2>Afhentningsdage og lokale åbningsdage
			<span class="infohint i:infohint">
				<span class="p">Dette afsnit bruges til at oprette og vedligeholde afhentningsdage (de dage produkter kan afhentes i afdelingerne), samt hvilke afdelinger har åbent på disse dage.</span>
				<span class="p">Når du opretter en ny afhentningsdag, kan du kun vælge blandt onsdage, hvor der ikke allerede er oprettet en afhentningsdag. Alle afdelinger er som standard åbne på hver afhentningsdag, så du skal klikke af hvis en afdeling f.eks. holder ferielukket.</span>
			</span>
		</h2>
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
