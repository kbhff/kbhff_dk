<? 
global $action;
global $DC;
global $TC;

$departments = $DC->getDepartments();

?>

<div class="scene accountant departments i:scene" itemscope itemtype="http://schema.org/NewsArticle">
	<h1>Bogholder</h1>
	
	<div class="c-wrapper">
		<h2>Download afregningsoversigt</h2>
		<?= $TC->formStart("download", ["class" => "labelstyle:inject form"]); ?>
		<?= $TC->input("creation_date", [
			"type" => "date",
			"label" => "Afregningsdato",
			"required" => true,
			"hint" => "Vælg åbningsdato for kasseafregninger",
			"error" => "Du skal vælge en dato"
		]); ?>
		
		<ul class="actions">
		<?= $TC->submit("Download fil"); ?>
		</ul>
		<?= $TC->formEnd(); ?>

		<h2>Se afregninger pr. afdeling</h2>
		<ul>
		<? foreach($departments as $department): ?>
			<li><a href="/bogholder/afregninger/<?= $department["id"] ?>"><?= $department["name"] ?></a></li>
		<? endforeach; ?>
		</ul>
	</div>
	
</div>