<?php
global $model;
global $action;

include_once("classes/system/department.class.php");
$DC = new Department();
$departments = $DC->getDepartments();

$UC = new User();
$user_department = $UC->getUserDepartment();
$department_name = $user_department["name"];

$user = $UC -> getUser();
$users = $model->getUsersByDepartment("GetUsersByDepartment");

?>

<div class="scene member_help i:member_help i:scene" itemscope itemtype="http://schema.org/NewsArticle">
	
	<h1>Medlemshjælp</h1>	
	
<?= $model->formStart("soeg", array("class" => "search_user labelstyle:inject")) ?>
<? // show error messages 
if(message()->hasMessages(array("type" => "error"))): ?>
	<p class="errormessage">
<?	$messages = message()->getMessages(array("type" => "error"));
		message()->resetMessages();
		foreach($messages as $message): ?>
		<?= $message ?><br>
<?	endforeach;?>
	</p>
<?	endif; ?>
	
	<fieldset>
		<?= $model->input("user_id", array("type" => "hidden", "value" => $user["id"])); ?>
		<?= $model->input("search", array("hint_message" => "Navn, email, mobilnr eller medlemsnr", "error_message" => "Du skal som minimum angive 3 tegn")) ?>
		<?= $model->input("department_id", array("type" => "select", "required" => false, "hint_message" => "Vælg en lokalafdeling", "options" => $HTML->toOptions($departments, "id", "name", ["1" => [$user_department["name"]]]),)); ?>
		<? // $model->input("active_member", array("type" => "select", "options" => array("1" => "Kun aktive medlemmer", "0" => "Medtag ikke-aktive medlemmer"),)); ?>
	</fieldset>
		
	<ul class="actions">
		<?= $model->submit("Søg", array("class" => "primary", "wrapper" => "li.search")) ?>
		<li><a class="button primary clickable" href="/medlemshjaelp/tilmelding">Opret nyt medlem</a></li>
	</ul>
<? 
if($users):  
	?>
	<h2> Medlemmer</h2>
	<ul class="users">
		 <? // print_r($users)?>  
	<? foreach($users as $u => $user): ?>
		<ul class ="user_id:<?=$user["user_id"]?>">
		<? array_key_exists("user_id", $user) ? print '<li class="user_id">'.$user["user_id"].'</li>' : '' ?>
		<? array_key_exists("navn", $user) ? print '<li class="user_name">'.$user["navn"].'</li>' : '' ?>
		<? array_key_exists("email", $user) ? print '<li class="user_email">'.$user["email"].'</li>' : '' ?>
		<? array_key_exists("mobilnr", $user) ? print '<li class="user_mobile">'.$user["mobilnr"].'</li>' : '' ?>
		<? array_key_exists("member_no", $user) ? print '<li class="user_no">'.$user["member_no"].'</li>' : '' ?>
		<? array_key_exists("afdeling", $user) ? print '<li class="user_department">'.$user["afdeling"].'</li>' : '' ?>
		</ul>
	<? endforeach; ?>
	</ul>
<?	endif; ?>		

</div>