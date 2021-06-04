<?php
global $action;
$IC = new Items();

$recipient_count = session()->value("recipient_count");
$department_id = session()->value("department_id");
?>
<div class="scene mass_mail_receipt i:scene">
	<div class="article">
	<h1>Kvittering</h1>
		<div>
			<h2>Mailen blev afsendt</h2>
		
		<? if($recipient_count && $department_id): ?>

			<? if($department_id == "all_departments"): ?>

			<p>Din besked er blevet sendt til alle <?= $recipient_count ?> aktive medlemmer af KBHFF.</p>
			
			<? elseif($department_id == "all_departments_all_members"): ?>

			<p>Din besked er blevet sendt til alle <?= $recipient_count ?> aktive og inaktive medlemmer af KBHFF.</p>
			
			<? else: 
			global $DC;
			$department = $DC->getDepartment(["id" => $department_id]);
			?>
			
			<p>Din besked er blevet sendt til <?= $recipient_count ?> medlemmer i afdeling <?= $department["name"] ?>.</p>

			<? endif; ?>

		<? else: ?>

			<p>... men noget gik galt med kvitteringen.</p>
			
		<? endif; ?>
			<ul class="actions">
				<li><a class="button clickable" href="/profil">Gå til Min Side</a></li>
			</ul>

		</div>
	</div>

</div>
