<?php
global $model;
global $action;
global $page;

include_once("classes/system/department.class.php");
$DC = new Department();
$departments = $DC->getDepartments();

$UC = new User();
$user_department = $UC->getUserDepartment();
$department_id = false;
$users = false;
$search_value = "";

$global_search_allowed = $page->validatePath("/medlemshjaelp/globalSearch"); 

$search_users = $model->searchUsers($action);
if($search_users) {

	$search_value = $search_users["search_value"];
	$users = $search_users["users"];
	$department_id = $search_users["department_id"];
}

if (!$department_id && $user_department) {
	$department_id = $user_department["id"];
}

?>

<div class="scene member_help i:member_help i:scene" itemscope itemtype="http://schema.org/NewsArticle">
	<div class="banner i:banner variant:random format:jpg"></div>
	<h1>Medlemshjælp</h1>

	<div class="c-wrapper find-member">
		<div class="c-three-quarters">
		<h2>Find medlem <? if(!$global_search_allowed):?>i afdeling <?=$user_department["name"]?><? endif; ?></h2>
		</div>

		<div class="c-one-quarter">
			<ul class="actions">
				<li class="new_member"><a class="button primary" href="/medlemshjaelp/tilmelding">Opret nyt medlem</a></li>
			</ul>
		</div>
	</div>


	
	<?= $model->formStart("", array("class" => "search_user labelstyle:inject")) ?>
	<div class="c-wrapper search">
		<div class="c-three-quarters">
			<fieldset>
				<?= $model->input("search_member") ?>
				<? if($page->validatePath("/medlemshjaelp/globalSearch")): ?>
				<?= $model->input("department_id", array("type" => "select", "hint_message" => "Begræns din søgning til en afdeling.", "error_message" => "Du kan søge på den enkelte afdeling eller vælge 'alle afdelinger'.", "value" => $department_id, "options" => $HTML->toOptions($departments, "id", "name", ["add" => ["all" => "Alle afdelinger"]]),)); ?>
				<? else: ?>
				<?= $model->input("department_id", ["type" => "hidden", "value" => $department_id]) ?>
				<? endif; ?>
			</fieldset>
		</div>
		<div class="c-one-quarter">
			<ul class="actions">
				<?= $model->submit("Søg", array("class" => "primary", "wrapper" => "li.search")) ?>
			</ul>
		</div>
	</div>
	<?= $model->formEnd() ?>


	<?= $HTML->serverMessages(["type" => "error"]) ?>


	<div class="c-wrapper users">
		<h3 class="header<?= (!$users ? " hidden" : "") ?>">
			<span class="name">Navn</span>
			<span class="email">Mail</span>
			<span class="mobile">Mobilnr</span>
			<span class="member_no">Medl.nr</span>
			<span class="department">Lokalafd.</span>
		</h3>

		<p class="type_to_search<?= ($users ? " hidden" : "") ?>">Indtast mindst 4 tegn for at søge.</p>
		<p class="no_results<?= ($users === false ? "" : " hidden") ?>">Ingen resultater.</p>

		<ul class="users">
			<li class="user template">
				<ul class="user_info">
					<li class="name search">{firstname} {lastname}</li>
					<li class="email search">{email}</li>
					<li class="mobile search">{mobile}</li>
					<li class="member_no search">{member_no}</li>
					<li class="department">{department}</li>
					<li class="profile">
						<ul class="actions">
							<li><a class="button" href="/medlemshjaelp/brugerprofil/{user_id}">Åbn</a></li>
						</ul>
					</li>
				</ul>
			</li>
		<? if($users): 
			foreach($users as $u => $user): ?>
			<li class="user">
				<ul class="user_info">
					<?= '<li class="name search">'.preg_replace("/$search_value/i", "<span class=highlight_string>$0</span>", $user["firstname"]." ".$user["lastname"]).'</li>' ?>
					<?= '<li class="email search">'.preg_replace("/$search_value/i", "<span class=highlight_string>$0</span>", $user["email"]).'</li>' ?>
					<?= '<li class="mobile search">'.preg_replace("/$search_value/i", "<span class=highlight_string>$0</span>", $user["mobile"]).'</li>'?>
					<?= '<li class="member_no search">'.preg_replace("/$search_value/i", "<span class=highlight_string>$0</span>", $user["member_no"]).'</li>' ?>
					<?= '<li class="department">'.$user["department"].'</li>' ?>
					<li class="profile">
						<ul class="actions">
							<li><a class="button" href="/medlemshjaelp/brugerprofil/<?=$user["user_id"]?>">Åbn</a></li>
						</ul>
					</li>
				</ul>
			</li>
			<? endforeach;
			endif; ?>
		</ul>

	</div>
</div>