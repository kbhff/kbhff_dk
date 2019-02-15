<?php
global $model;
global $action;

include_once("classes/system/department.class.php");
$DC = new Department();
$departments = $DC->getDepartments();

$UC = new User();
$user_department = $UC->getUserDepartment();
$users = $model->searchUsers("searchUsers");
?>

<div class="scene member_help i:member_help i:scene" itemscope itemtype="http://schema.org/NewsArticle">
	<div class="banner i:banner variant:1 format:jpg"></div>
	
		

	<h1>Medlemshjælp</h1>	
	<h2>Find medlem</h2>
	
	<?= $model->formStart("", array("class" => "search_user labelstyle:inject")) ?>
	<div class="c-wrapper">
	
		<div class="c-three-quarters">
			<fieldset>
				<?= $model->input("search_member", array("label" => "Navn, email, mobilnr eller medlemsnr")) ?>
				<?= $model->input("department_id", array("type" => "select", "hint_message" => "Du kan søge et medlem frem ved at indtaste vedkommendes lokalafdeling.", "error_message" => "Du kan søge på den enkelte afdeling eller vælge 'alle afdelinger'.", "options" => $HTML->toOptions($departments, "id", "name",  array(["1" => [$user_department["name"]], "0" => "Alle afdelinger"])))); ?>
			</fieldset>
		</div>
		<div class="c-one-quarter">
			<ul class="actions">
				<li class = "new_member"><a class="button primary clickable" href="/medlemshjaelp/tilmelding">Opret nyt medlem</a></li>
				<?= $model->submit("Søg", array("class" => "primary", "wrapper" => "li.search")) ?>
			</ul>
		</div>

	
	</div>
	
<? // show error messages 

if(message()->hasMessages(array("type" => "error"))): ?>
	<p class="errormessage">
<?	$messages = message()->getMessages(array("type" => "error"));
		message()->resetMessages();
		foreach($messages as $message): ?>
		<?= $message ?><br>
<?	endforeach;?>
	</p>
	<p class="message">
<?	$messages = message()->getMessages(array("type" => "message"));
	foreach($messages as $message): ?>
		<?= $message ?><br>
<?	endforeach; ?>
	</p>
	<? message()->resetMessages(); ?>
<?	endif; ?>
<?= $model->formEnd() ?>

	<div class="c-wrapper">
		<h3 class="hidden">
			<span class="user_name">Navn</span><span class="user_email">Mail</span><span class="user_mobile">Mobilnr</span><span class="user_member_no">Medl.nr</span><span class="user_department">Lokalafd.</span>
		</h3>
		<ul class="users">
			<li class="template">
				<ul class=user_info>
					<li class="user_name">{name}</li>
					<li class="user_email">{email}</li>
					<li class="user_mobile">{mobile}</li>
					<li class="user_member_no">{member_no}</li>
					<li class="user_department">{department}</li>
					<li class="user_profile">
						<ul class="actions">
							<li><a class="button clickable" href="/medlemshjaelp/brugerprofil/{user_id}">Åbn</a></li>
						</ul>
					</li>
				</ul>
			</li>
		</ul>
	
		
		
	<? if($users):  ?>
		<div class="users">
			<h3>
				<span class="user_name">Navn</span><span class="user_email">Mail</span><span class="user_mobile">Mobilnr</span><span class="user_member_no">Medl.nr</span><span class="user_department">Lokalafd.</span>
			</h3>
			<ul class="users">
			<? foreach($users as $u => $user): ?>
				<li>
					<ul class="user_info">
						<? print '<li class="user_name">'.$user["name"].'</li>' ?>
						<? print '<li class="user_email">'.$user["email"].'</li>' ?>
						<? print '<li class="user_mobile">'.$user["mobile"].'</li>'?>
						<? print '<li class="user_member_no">'.$user["member_no"].'</li>' ?>
						<? print '<li class="user_department">'.$user["department"].'</li>' ?>
						<li class="user_profile">
							<ul class="actions">
								<li><a class="button clickable" href="/medlemshjaelp/brugerprofil">Åbn</a></li>
							</ul>
						</li>
					</ul>
				</li>
		<? endforeach; ?>
			</ul> 
		</div>
	<?	endif; ?>		
	</div>
</div>