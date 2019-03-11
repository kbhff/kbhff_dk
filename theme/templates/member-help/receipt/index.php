<?php
global $action;
$order_no = $action[2];
$SC = new SuperShop();


$order = $SC->getOrders(["order_no" => $order_no]);


?>
<div class="scene member_help_signup_receipt i:scene">
	<div class="article">
	<h1>Opret nyt medlem</h1>
		<div class="articlebody">
			<h2>Medlemskab er oprettet</h2>
		
			<p>Medlemmet er nu oprettet og betalt. Hvis medlemmet ikke før har været logget ind på medlemssystemet, skal vedkommende finde bekræftelsesmailen, som er sendt til den angivne e-mailadresse, og klikke på linket heri.</p>
			<p>Som kassemester kan du dog allerede nu hjælpe medlemmet med at bestille den første pose ved at klikke nedenfor, eller du kan hjælpe medlemmet med at skrive sig op til den første vagt.</p>
			<ul class="actions">
				<li><a class="button clickable" href="/medlemshjaelp/brugerprofil/<?=$order["user_id"]?>">Gå til medlemsside</a></li>
				<li><a class="button primary clickable" href="/medlemshjaelp/brugerprofil/<?=$order["user_id"]?>">Bestil pose til medlem</a></li>
			</ul>

		</div>
	</div>

</div>
