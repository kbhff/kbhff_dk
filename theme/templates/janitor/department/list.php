<?php
global $action;
global $model;

$departments = $model->getDepartments();

?>

<div class="scene i:scene defaultList departmentList">
	<h1>Departments</h1>

	<ul class="actions">
		<?= $JML->listNew(array("label" => "New department")) ?>
	</ul>

	<div class="all_items i:defaultList filters"<?= $HTML->jsData(["search"]) ?>>
<?		if($departments): ?>
		<ul class="items">
<?			foreach($departments as $department): ?>
			<li class="item item_id:<?= $department["id"] ?>">
				<h3><?= strip_tags($department["name"]) ?></h3>


				<?= $JML->listActions($department, ["modify"=>[
					"delete"=>[
						"url"=>"/janitor/department/deleteDepartment/".$department["id"]
					]]]); ?>
			 </li>
<?			endforeach; ?>
		</ul>
<?		else: ?>
		<p>No departments.</p>
<?		endif; ?>
	</div>

</div>
