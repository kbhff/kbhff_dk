<?php
global $model;
global $action;

include_once("classes/system/department.class.php");
$DC = new Department();
$departments = $DC->getDepartments();

$UC = new User();
$user_department = $UC->getUserDepartment();
$search_users = $model->searchUsers($action);

$search_value = $search_users["search_value"];
$users = $search_users["users"];
$department_id = $search_users["department_id"];
if (!$department_id) {
	$department_id = $user_department["id"];
}
	

?>

<div class="scene member_help i:member_help i:scene" itemscope itemtype="http://schema.org/NewsArticle">
	<div class="banner i:banner variant:random format:jpg"></div>
	<h1>Medlemshjælp</h1>

	<div class="c-wrapper find-member">
		<div class="c-three-quarters">
			<h2>Find medlem</h2>
		</div>

		<div class="c-one-quarter">
			<ul class="actions">
				<li class = "new_member"><a class="button primary clickable" href="/medlemshjaelp/tilmelding">Opret nyt medlem</a></li>
			</ul>
		</div>
	</div>


	
	<?= $model->formStart("", array("class" => "search_user labelstyle:inject")) ?>
	<div class="c-wrapper search">
		<div class="c-three-quarters">
			<fieldset>
				<?= $model->input("search_member", array("label" => "Navn, email, mobilnr eller medlemsnr")) ?>
				<?= $model->input("department_id", array("type" => "select", "hint_message" => "Du kan søge et medlem frem ved at indtaste vedkommendes lokalafdeling.", "error_message" => "Du kan søge på den enkelte afdeling eller vælge 'alle afdelinger'.", "value" => $department_id, "options" => $HTML->toOptions($departments, "id", "name", ["add" => ["all" => "Alle afdelinger"]]),)); ?>
			</fieldset>
		</div>
		<div class="c-one-quarter">
			<ul class="actions">
				<?= $model->submit("Søg", array("class" => "primary", "wrapper" => "li.search")) ?>
			</ul>
		</div>
	</div>
	<?= $model->formEnd() ?>

<? // show error messages 
if(message()->hasMessages()): ?>
<div class="messages">
<?
$all_messages = message()->getMessages();
message()->resetMessages();
foreach($all_messages as $type => $messages):
	foreach($messages as $message): ?>
	<p class="<?= $type ?>"><?= $message ?></p>
	<? endforeach;?>
<? endforeach;?>
</div>
<? endif; ?>


	<div class="c-wrapper users">
		<h3>
			<span class="name">Navn</span><span class="email">Mail</span><span class="mobile">Mobilnr</span><span class="member_no">Medl.nr</span><span class="department">Lokalafd.</span>
		</h3>
		<ul class="users">
			<li class="user template">
				<ul class=user_info>
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
		<p class ="visible <?=$users? "invisible": ""?>">
			Her på siden kan du som kassemester søger efter medlemmer. 
		Du søger et medlem frem ved at indtaste enten navn, mail, mobilnr eller medlemsnr på det pågældende medlem. 
		Når du har søgt et medlem frem, har du mulighed for at åbne medlemmets brugerprofil. 
		Her kan du handle på vejne af medlemmet og hjælpe med at bestille grøntsager, betale kontigent eller redigere brugeroplysninger eller medlemsskabsinfo. 
		</p>
	</div>
</div>