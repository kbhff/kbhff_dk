<?php
global $action;
$order_no = $action[1];
$SC = new SuperShop();
$IC = new Items();

$order = $SC->getOrders(["order_no" => $order_no]);

$is_signupfee = false;
if($order && count($order["items"]) == 1) {
	$item = $IC->getItem(["id" => $order["items"][0]["item_id"], "extend" => true]);
	$is_signupfee = $item["itemtype"] == "signupfee" ? true : false;
}

?>
<div class="scene member_help_receipt i:scene">
	<div class="article">
	<h1>Kvittering</h1>
		<? if($is_signupfee): ?>
		<div class="membership">
			<h2>Medlemskab er oprettet</h2>
		
			<p>Medlemmet er nu oprettet og betalt. Hvis medlemmet ikke før har været logget ind på medlemssystemet, skal vedkommende finde bekræftelsesmailen, som er sendt til den angivne e-mailadresse, og klikke på linket heri.</p>
			<p>Som kassemester kan du dog allerede nu hjælpe medlemmet med at bestille den første pose ved at klikke nedenfor, eller du kan hjælpe medlemmet med at skrive sig op til den første vagt.</p>
			<ul class="actions">
				<li><a class="button clickable" href="/medlemshjaelp/brugerprofil/<?=$order["user_id"]?>">Gå til medlemsprofil</a></li>
				<li><a class="button primary clickable" href="/medlemshjaelp/butik/<?=$order["user_id"]?>">Bestil pose til medlem</a></li>
			</ul>

		</div>
		<? else: ?>
			<h2>Bestillingen er gennemført</h2>
			<p>Medlemmet har modtaget en e-mail med en ordrebekræftelse.</p>
			<ul class="actions">
				<li><a class="button clickable" href="/medlemshjaelp/butik/<?=$order["user_id"]?>">Bestil igen</a></li>
				<li><a class="button primary clickable" href="/medlemshjaelp/brugerprofil/<?=$order["user_id"]?>">Gå til medlemsprofil</a></li>
			</ul>
		<? endif; ?>
	</div>

</div>
