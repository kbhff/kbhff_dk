<div class="scene restructure i:restructure">

	<h1>KBHFF Restructure tool</h1>
	<p>Click the button to run restructure tool</p>
	<ul class="actions">
		<?= $JML->oneButtonForm("Perform DB restructuring", "/janitor/restructure/run", [
			"wrapper" => "li.restrucure",
			"success-location" => "/janitor/restructure/done"
		]) ?>
	</ul>

	<div class="result">
		<code></code>
	</div>
</div>
