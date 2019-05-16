<?
// Get methods for user and shop data manipulation
$UC = new User();
$SC = new Shop();
$IC = new Items();

$user = $UC->getKbhffUser();
$department = $UC->getUserDepartment();

foreach ($IC->getMemberships() as $p) {
	$memberships[$p["item_id"]] = "Pris ".$p["name"];

}

$products = $IC->GetItems(["itemtype" => "product", "extend" => ["prices" => true, "mediae" => true]]);

$productAvailabilityOptions = array(
	"0" => array("id" => "1", "name" => "Altid"), 
	"1" => array("id" => "2", "name" => "KUN i følgende periode"), 
	"2" => array("id" => "2", "name" => "Altid UNDTAGEN følgende periode"), 
	"3" => array("id" => "3", "name" => "Vælg afhentningsdage")
	);

// data for Afhentningsdage og lokale åbningsdage
$DC = new Department();
$departments = $DC->getDepartments();


?>

<div class="scene profile i:profile">

	<div class="banner i:banner variant:1 format:jpg"></div>

	<?	// Display any backend generated messages
		if(message()->hasMessages()): ?>
		
			<p class="errormessage">
		<?	$messages = message()->getMessages(array("type" => "error"));
			foreach($messages as $message): ?>
				<?= $message ?><br>
		<?	endforeach;?>
			</p>

			<p class="message">
		<?	$messages = message()->getMessages(array("type" => "message"));
			foreach($messages as $message): ?>
				<?= $message ?><br>
		<?	endforeach; ?>
			</p>

			<? message()->resetMessages(); ?>
	<?	endif; ?>

	<div class="c-wrapper">

		<div class="c-two-thirds">

			<div class="section intro">
				<h2>Produkter</h2>
			</div>
		</div>
		<div class="c-one-third">
			<ul class="actions">
				<li class="new-order full-width"><a href="/indkoeb/new" class="button primary">+ Tilføj nyt produkt</a></li>
			</ul>
		</div>
		<p>
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet. Proin gravida dolor sit amet lacus accumsan et viverra justo commodo. Proin sodales pulvinar sic tempor. Sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nam fermentum, nulla luctus pharetra vulputate, felis tellus mollis orci, sed rhoncus pronin sapien nunc accuan eget.
		</p>

		<div class="order-headings">
			<p class="order-products">&nbsp;</p>
			<p class="order-products">Navn</p>
			<p class="order-products">TILGÆNGEpGHED</p>
			<p class="order-products">Pris</p>
			<p class="order-products">Status</p>
			<p class="order-products">Actions</p>
		</div>
<? 
	if ($products) :
		$status_text[0] = "Aktivér";
		$status_text[1] = "DeAktivér";

		foreach ($products as $product) :
			if (!isset($product['id'])) {
				continue;
			}
			$price_string = "";
			foreach ($product['prices'] as $price) {
				$price_string .= $memberships[$price['type']]." - ".$price['price_without_vat']." ".$price['currency']."<br>";
			}

			$new_status = ($product['status'] ? 0:1);
			

?>
		<div class="order">
			<p class="file">
<?
if (1) {
?>
<link type="text/css" rel="stylesheet" media="all" href="/janitor/css/seg_desktop.css?rev=1" />
	<script type="text/javascript" src="/janitor/js/seg_desktop.js?rev=1"></script>

					<?= $JML->editSingleMedia($product) ?>
				<?
} else {
				$media = $JML->getMedia($product); 
				$_ = "";
				if($media) {
					if(preg_match("/^(jpg|png)$/", $media["format"])) {
						$_ .= '<a href="/images/'.$media["item_id"].'/'.$media["variant"].'/480x.'.$media["format"].'" target="__blank">';
						$_ .= '<img src="/images/'.$media["item_id"].'/'.$media["variant"].'/x68.'.$media["format"].'" /><br>';
						$_ .= $media["name"].'</a>';
					}
					else if(preg_match("/^(mp3|ogv)$/", $media["format"])) {
						$_ .= '<a href="/audios/'.$item["id"].'/'.$variant.'/128.'.$media["format"].'">'.$media["name"].'</a>';
					}
					else if(preg_match("/^(mp4|mov)$/", $media["format"])) {
						$_ .= '<a href="/videos/'.$item["id"].'/'.$variant.'/480x.'.$media["format"].'">'.$media["name"].'</a>';
					}
//					$_ .= '<p>'.$media["name"].'</p>';
					
				} else {
					$_ .= '<a >Upload</a>&nbsp;';	
				}
				
				print "$_";
}
				?>
			</p>
			
			<p class="order-products">(<?=$product['id'];?>)<?=htmlentities($product['name'], ENT_COMPAT, "UTF-8");?></p>
			<p class="order-products"><?=$productAvailabilityOptions[$product["productAvailability"]]['name'];?></p>
			<p class="order-products"><?=$price_string;?></p>
			<p class="order-products"><?= ($product['status'])? "Aktiv":"Ikke Aktiv";?></p>
			<p class="order-products"><ul class="actions change">
				<li class="change"><a href="/indkoeb/edit/<?=$product['id'];?>" class="button primary">Rediger</a></li>
				<li class="change"><a id="<?=$product['id'];?>" href="/indkoeb/status/<?=$product['id']."/".$new_status;?>" class="button primary"><?=$status_text[$product['status']];?></a></li>
			</ul></p>
		<?	endforeach;
	 endif; ?>
		</div>

	</div>

</div>