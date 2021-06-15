<? 
global $action;
global $DC;
global $TC;

$departments = $DC->getDepartments();

?>

<div class="scene accountant departments i:scene" itemscope itemtype="http://schema.org/NewsArticle">
	<h1>Bogholder</h1>

	<p>Her kan du downloade data indtastet i butiksvagtens kasseregnskab: nederst kan du klikke dig
igennem til en bestemt afdelings regnskab fra en bestemt dag. Alternativt, kan du øverst downloade
en csv fil med alle kasseregnskab fra en enkelt dato.</p>

	<p>NB. Hvis du skal åbne en csv fil med Excel og gerne vil kunne læse ‘æ’, ‘ø’ og ‘å’, skal du:
		<ol>
			<li>Åbne en ny Excel fil.</li>
			<li>Klik på Data -> Fra tekst (i gruppen Hent eksterne data).</li>
			<li>Vælg csv filen.</li>
			<li>Vælg “afgrænset” og under Filoprindelse/Tegnsæt vælg “65001: Unicode (UTF-8)”.
Klik på Næste.</li>
			<li>Kryds af i Tabulering og Komma og klik Færdig.</li>
		</ol>
	</p>
	
	<div class="c-wrapper">
		<h2>Download afregningsoversigt</h2>
		<?= $TC->formStart("download", ["class" => "labelstyle:inject form"]); ?>
		<fieldset>
			<div class="c-one-half">
				<?= $TC->input("creation_date", [
					"type" => "date",
					"label" => "Afregningsdato",
					"required" => true,
					"hint" => "Vælg åbningsdato for kasseafregninger",
					"error" => "Du skal vælge en dato"
				]); ?>
			</div>
		</fieldset>
		
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
