<?php
global $action;
global $IC;
global $UC;
global $DC;
global $model;

$user = $UC->getKbhffUser(["user_id" => session()->value("user_id")]);
$department = $user ? $user["department"] : false;

$departments = $DC->getDepartments();

$send_to_department_members = $this->checkpermissions("/massemail", "/sendToDepartmentMembers", ["/sendToDepartmentMembers" => true]);
$send_to_all_members = $this->checkpermissions("/massemail", "/sendToAllMembers", ["/sendToAllMembers" => true]);


?>
<div class="scene massmail i:massmail">
	<h1>Massemail</h1>

	<div class="c-wrapper">
		<div class="c-one-half">

			<? if($send_to_department_members && $send_to_all_members): ?>
			<p>Her kan du udsende beskeder til KBHFF's medlemmer. Du kan vælge at sende en mail til alle medlemmer eller til medlemmerne af én specifik afdeling.</p>
			<? else: ?>
			<p>Her kan du udsende beskeder til medlemmerne af din afdeling (<?= $department["name"] ?>).</p>
			<? endif; ?>
			<p>Det anbefales, at du <strong>sender en test-mail</strong> til dig selv, før du afsender til alle modtagere.</p>
			<div class="c-box">
				<h3>Hvordan bruger jeg mail-editoren?</h3>
				<p>Vores mail-editor er designet til at lave en korrekt formatering af tekstens delelementer, dvs. overskrifter, tekstafsnit og lister. Herved sikrer vi bl.a. at vores mails vises ensartet i alle mailprogrammer.</p>
				<p>De to øverste felter til beskedens er til beskedens emne og mailprogrammets forhåndsvisning. Herunder begynder selve mail-editoren. Bemærk det grønne 'P' i venstre side.</p>
				<p>P står for 'paragraph' og bruges til at skrive et almindeligt tekstafsnit. Tryk <em>Enter</em> for at starte et nyt afsnit.</p>
				<p>Hvis du vil lave en overskrift, kan du trykke på P'et og vælge H2, H3 eller H4 i stedet. H står for 'header' og tallene angiver overskriftens størrelse, hvor 2 er størst og 4 mindst.</p>
				<p>Hvis du skal bruge punktopstilling, kan du trykke på det grønne plus-symbol, der dukker op i højre side, når du holder musen over et felt. Vælg derefter 'List'. Du har nu i venstre side mulighed for at vælge UL ('unordered list') for almindelige punkttegn eller OL ('ordered list') for nummererede punkttegn.</p>
				<p>Endelig kan du markere ét eller flere ord, og du vil få muligheden for at formatere teksten med fremhævninger, links m.m.</p>
			</div>



		</div>

		<div class="c-one-half">
	
			<?= $model->formStart("sendKbhffMessage", array("class" => "labelstyle:inject")) ?>

				<? if($send_to_department_members && $send_to_all_members): ?>
				<fieldset>
					<h3>Vælg modtagere</h3>
					<?= $model->input("department_id", ["options" => $model->toOptions($departments, "id", "name", ["add" => [
						"all_departments" => "Alle afdelinger",
						"all_departments_all_members" => "Alle afdelinger (inkl. inaktive medlemmer)"
					]])]) ?>
				</fieldset>
				<? endif; ?>

				<fieldset>
					<h3>Indhold</h3>
					<?= $model->input("name", [
						"label" => "Emne",
						"hint_message" => "Hvad handler din mail om?",
						"error_message" => "Emnefeltet skal udfyldes",
					]) ?>
					<?= $model->input("description", [
						"class" => "autoexpand short", 
						"label" => "Kort tekst til mail-preview",
						"hint_message" => "Skriv en kort tekst til beskedens forhåndsvisning – max 155 tegn.",
						"error_message" => "Din besked skal have en preview-tekst på max 155 tegn.",
					]) ?>
					<?= $model->input("html", [
						"label" => "Indhold",
						"hint_message" => "Skriv ét afsnit eller overskrift pr. felt. Se vejledningen til venstre. ",
						"error_message" => "",						
					]) ?>
				</fieldset>

				<ul class="actions">
					<?= $model->submit("Send test til ".$user["email"], array("wrapper" => "li.submit", "formaction" => "/massemail/sendKbhffMessageTest")) ?>
					<?= $model->submit("Send", array("class" => "primary", "wrapper" => "li.submit")) ?>
				</ul>
				<p class="status"></p>

			<?= $model->formEnd() ?>

		</div>

	</div>


</div>