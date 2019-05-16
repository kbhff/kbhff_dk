<?
global $action;
global $model;
global $IC;

include_once("classes/system/department.class.php");

$IC = new Items();
$UC = new User();
$DC = new Department();

$AC = new Afhentningsdage();


if ($action[0] == "edit") {
	$result = $AC->getDepartmentScheduleList(array("id" => $action[1]));
	$schedule = $result[0];
} else if ($action[0] == "new_dep_pickup_date") {

	$schedule = array();
	$schedule["department_id"] = $action[1];
	$schedule["schedule_date"] = $action[2];
	$schedule["opening_hours"] = "";
	$schedule["closed"] = "0";

} else if ($action[0] == "save") {
	// if we have _POST parameters, then it's an edit with errors
	$AC->getPostedEntities();
	$entities = $AC->getModel();

	foreach ($entities as $key => $value) {
		$schedule[$key] = $value['value'];
	}
}
$department = $UC->getUserDepartment();

$departments = $DC->getDepartments();



?>
<div class="scene product_new i:product_new">

	<h1>Tilføj ny afhentningsdag</h1>
	<h2>Afhentningsdag</h2>


	<?= $model->formStart("save".(isset($schedule["id"])?"/".$schedule["id"]:""), array("class" => "product_new labelstyle:inject")) ?>
	<?
		if (isset($schedule["id"])) {
			print $model->input("id", array("type" => "hidden", "value" => $schedule["id"])); 
		}
		?>

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

					<?= $model->input("department_id", array("value" => $schedule["department_id"], "label" =>'Department', "type" => "select", "required" => true, "hint_message" => "", "error_message" => "", 
					"options" => $HTML->toOptions($departments, "id", "name", ["add" => ["" => "Select Department"]]))); ?>
					
					<?= $model->input("schedule_date", array("value" => ($schedule["schedule_date"] ? date("Y-m-d",$schedule["schedule_date"]):""), "type" => "date", "label" => "Date")) ?>
					<!-- // Change to checkbox if needed visual. -->
					<?
					if ($action[0] == "new_dep_pickup_date") {
						$tmp_type = "checkbox";
					} else {
						$tmp_type = "hidden";
					}
					print $model->input("closed", array("value" => $schedule["closed"], "type" => $tmp_type, "label" => "Closed")) ?>

					<?= $model->input("opening_hours", array("value" => $schedule["opening_hours"], "label" => "Opening Hours (if different)", "required" => false, "hint_message" => ".", "error_message" => ".")); ?>


				</fieldset>
			<ul class="actions">
				<li class="reject"><a href="/afhentningsdage" class="button">Annuller</a></li>
				
				<?= $model->submit("Save Schedule", array("class" => "primary", "wrapper" => "li.product_new")) ?>
			</ul>				
		</div>
	<?= $model->formEnd() ?>

</div>