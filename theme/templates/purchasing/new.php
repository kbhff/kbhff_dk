<?
global $action;
global $model;
global $IC;

include_once("classes/system/department.class.php");

$IC = new Items();
$UC = new User();
$DC = new Department();

$user = $UC->getKbhffUser();
$department = $UC->getUserDepartment();

$departments = $DC->getDepartments();


$productAvailabilityOptions = array(
	"0" => array("id" => "1", "name" => "Altid"), 
	"1" => array("id" => "2", "name" => "KUN i følgende periode"), 
	"2" => array("id" => "2", "name" => "Altid UNDTAGEN følgende periode"), 
	"3" => array("id" => "3", "name" => "Vælg afhentningsdage")
	);

$supplierlist = array(
	"0" => array("id" => "1", "name" => "Supplier 1"), 
	"1" => array("id" => "2", "name" => "Supplier 2"), 
	"2" => array("id" => "3", "name" => "Supplier 3")
	);

$productTypes = array(
	"0" => array("id" => "1", "name" => "Fasteposer"), 
	"1" => array("id" => "2", "name" => "Sæson-poser"), 
	"2" => array("id" => "3", "name" => "Løssalg")
	);
?>
<div class="scene product_new i:product_new">

	<h1>Opret nyt produkt</h1>
	<h2>Produktoplysninger</h2>

	<?= $model->formStart("save", array("class" => "product_new labelstyle:inject")) ?>
	<?= $model->input("status", array("type" => "hidden", "value" => "false")) ?>
		<div class="c-wrapper">				
			<? if(message()->hasMessages(array("type" => "error"))): ?>
				<p class="errormessage">
			<?	$messages = message()->getMessages(array("type" => "error"));
					message()->resetMessages();
					foreach($messages as $message): ?>
					<?= $message ?><br>
			<?	endforeach;?>
				</p>
			<?	endif; ?>
			
				<fieldset>
					<?= $model->input("name", array("required" => true, "label" => "Navn", "hint_message" => "Skriv produkts navn her", "error_message" => "Navn er obligatorisk. Det kan kun indeholde bogstaver.")) ?>
					<?= $model->input("price_default", array("required" => true, "label" => "Pris (alm medlem)", "hint_message" => "Skriv medlemmets efternavn her", "error_message" => "Pris er obligatorisk.")) ?>
					<?= $model->input("price_offer", array("required" => false, "label" => "Pris 2 (støttemedlem)", "hint_message" => "Pris for støttemedlem.", "error_message" => "")); ?>
					<?= $model->input("image", array("type" => "files", "required" => false, "label" => "Produktbillede", "hint_message" => "", "error_message" => "")); ?>
					<?= $model->input("description", array("label" => "Produktbeskrivelse", "required" => false, "hint_message" => ".", "error_message" => ".")); ?>
					
					<?= $model->input("producttype", array("label" => "Produkttype", "type" => "select", "required" => true, "hint_message" => "", "error_message" => "", 
					"options" => $HTML->toOptions($productTypes, "id", "name", ["add" => ["" => "Vælg afdeling"]]))); ?>
					<!-- Avler/leverandør -->
					<?= $model->input("supplier", array("label" =>'Avler/leverandør', "type" => "select", "required" => true, "hint_message" => "", "error_message" => "", 
					"options" => $HTML->toOptions($supplierlist, "id", "name", ["add" => ["" => "Vælg afdeling"]]))); ?>

					<h2>Bestilling og afhentning</h2>
					
					<?= $model->input("productAvailability", array("label" =>'Hvornår varen kan købes', "type" => "select", "required" => true, "hint_message" => "", "error_message" => "", 
					"options" => $HTML->toOptions($productAvailabilityOptions, "id", "name", ["add" => ["" => "Vælg afdeling"]]))); ?>

					<p>Kan bestilles hos:</p>
					<? foreach ($departments as $id => $dep) : ?>
					<?= $model->input("departments[".$dep["id"]."]", array("type" => "checkbox", "label" => $dep["name"])) ?>
					<?	endforeach;?>
				</fieldset>
			<ul class="actions">
				<li class="reject"><a href="/indkoeb" class="button">Annuller</a></li>
				
				<?= $model->submit("Opret som kladde", array("class" => "primary", "wrapper" => "li.product_new")) ?>
				<?= $model->button("Opret og aktivér", array("class" => "primary", "wrapper" => "li.product_new", "script" => array("onClick","document.getElementById('input_status').value=1;this.form.submit();"))); ?>
			</ul>				
		</div>
	<?= $model->formEnd() ?>

</div>