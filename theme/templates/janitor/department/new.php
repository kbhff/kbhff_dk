
<?php
global $action;
global $model;
?>
<div class="scene i:scene defaultNew">
	<h1>New department</h1>

	<ul class="actions">
		<?= $JML->newList(array("label" => "List")) ?>
	</ul>

	<?= $model->formStart("saveDepartment", array("class" => "i:defaultNew labelstyle:inject")) ?>
		<fieldset>
			<?= $model->input("name") ?>
			<?= $model->input("abbreviation") ?>
			<?= $model->input("address1") ?>
			<?= $model->input("address2", array("class" => "autoexpand short"))?>
			<?= $model->input("postal") ?>
			<?= $model->input("city") ?>
			<?= $model->input("email") ?>
			<?= $model->input("opening_hours", array("class" => "autoexpand short")) ?>
			<?= $model->input("mobilepay_id") ?>
			<?= $model->input("accepts_signup", array("value" => 1)) ?>
		</fieldset>

		<fieldset>
			<?= $model->inputLocation("geolocation", "latitude", "longitude") ?>
		</fieldset>

		<fieldset>
			<?= $model->input("description") ?>
			<?= $model->input("html") ?>
		</fieldset>

		<?= $JML->newActions() ?>
	<?= $model->formEnd() ?>
</div>
