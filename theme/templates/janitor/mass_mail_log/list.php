<?php
global $action;
global $model;

$log_entries = $model->getLogEntries();

?>

<div class="scene i:scene defaultList departmentList">
	<h1>Mass mail log</h1>

	<div class="all_items i:defaultList filters"<?= $HTML->jsData(["search"]) ?>>
<?		if($log_entries): ?>
		<ul class="items">
<?			foreach($log_entries as $log_entry): ?>
			<li class="item item_id:<?= $log_entry["id"] ?>">
				<h3><span class="timestamp"><?= $log_entry["created_at"] ?></span> --- <?= strip_tags($log_entry["name"]) ?></h3>

				<ul class="actions">
					<?= $HTML->link("View", "/janitor/mass-mail-log/view/".$log_entry["id"], array("class" => "button", "wrapper" => "li.view")) ?>
				</ul>
			 </li>
<?			endforeach; ?>
		</ul>
<?		else: ?>
		<p>No log entries.</p>
<?		endif; ?>
	</div>

</div>
