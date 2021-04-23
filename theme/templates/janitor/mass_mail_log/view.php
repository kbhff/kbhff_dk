<?php

global $action;
global $model;

$log_entry_id = $action[1];
$log_entry = $model->getLogEntries(["id" => $log_entry_id]);

$IC = new Items();

?>
<div class="scene i:scene defaultEdit massMailView">
	<h1>View mass mail content</h1>
	<h2><?= strip_tags($log_entry["name"]) ?></h2>

	<ul class="actions">
		<?= $JML->newList(array("label" => "List")) ?>
	</ul>


	<div class="item i:defaultEdit">
		<div class="html">
			<?= $log_entry["html"] ?>
		</div>
	</div>

</div>
