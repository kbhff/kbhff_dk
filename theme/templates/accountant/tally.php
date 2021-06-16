<? 
global $action;
global $TC;
global $DC;

include_once("classes/users/superuser.class.php");
$UC = new SuperUser();

$department_id = $action[1];
$department = $DC->getDepartment(["id" => $department_id]);

$tally_id = $action[2];
$tally = $TC->getTally(["id" => $tally_id]);

$payouts = $TC->getPayouts($tally_id);
$revenues = $TC->getMiscRevenues($tally_id);
$calculated_sales_by_the_piece = $TC->calculateSalesByThePiece($tally_id);
$cash_order_items_summary = $TC->cashOrderItemsSummary($tally_id);
$opened_by = $UC->getUser(["user_id" => $tally["opened_by"]]);
$closed_by = $UC->getUser(["user_id" => $tally["closed_by"]]);

?>

<div class="scene accountant i:scene" itemscope itemtype="http://schema.org/NewsArticle">
<h1>Afregning</h1>

<div class="c-wrapper">

	<div class="info">
		<dl>
			<dt>Afdeling</dt>
			<dd><?= $department["name"] ?></dd>
			<dt>Oprettet</dt>
			<dd>d. <?= date("d.m.Y", strtotime($tally["created_at"])) ?> af <?= $opened_by["nickname"]." (".$opened_by["email"].")" ?></dd>
			<dt>Lukket</dt>
			<dd>d. <?= date("d.m.Y", strtotime($tally["modified_at"])) ?> af <?= $closed_by["nickname"]." (".$closed_by["email"].")" ?></dd>
			<? if($tally["comment"]): ?>	
			<dt>Kommentar</dt>
			<dd><?= $tally["comment"] ?></dd>
			<? endif; ?>
		</dl>
	</div>
	
	<div class="cash">
		<h3>Kassebeholdning</h3>
		<dl>
			<dt>Kassebeholdning ved vagtstart</dt>
			<dd><?= isset($tally["start_cash"]) ? $tally["start_cash"]." kr." : "Ingen data" ?></dd>
			<dt>Kassebeholdning ved vagtens slutning</dt>
			<dd><?= isset($tally["end_cash"]) ? $tally["end_cash"]." kr." : "Ingen data" ?></dd>
			<dt>Deponeret</dt>
			<dd><?= isset($tally["deposited"])? $tally["deposited"] : 0 ?> kr.</dd>
		</dl>

	</div>

	<div class="payouts">
		<h3>Udbetalinger fra kassen</h3> 
		<? if($payouts): ?>
		<dl class="payouts">
			<? foreach($payouts as $payout): ?> 
			<dt class="name"><?= $payout["name"] ?></dt>
			<dd class="amount"><?= $payout["amount"] ?> kr.</dd>
			<? endforeach; ?>
		</dl>
		<? endif; ?>
		<dl class="subtotal">
			<dt>Udbetalinger i alt</dt>
			<dd><?= $TC->getPayoutsSum($tally_id) ?> kr.</dd>
		</dl>
	</div>

	<div class="misc_revenues">
		<h3>Andre kontante indtægter</h3>
		<? if($revenues): ?>
		<dl class="revenues">
			<? foreach($revenues as $revenue):?>
			<dt class="name"><?= $revenue["name"] ?></dt>
			<dd class="amount"><?= $revenue["amount"] ?> kr.</dd>
			<? endforeach; ?>
		</dl>
		<? endif; ?>
		<dl class="subtotal">
			<dt>Andre kontante indtægter i alt</dt>
			<dd><?= $TC->getMiscRevenuesSum($tally_id) ?> kr.</dd>
		</dl>
	</div>

	<div class="cash_sales">
		<h3>Registreret kontantsalg</h3>

		<? if($cash_order_items_summary): ?>
		
		<? foreach($cash_order_items_summary as $item_id => $values): ?>					
		<dl class="<?= $values["itemtype"]." ".$values["name"] ?>">
			<dt class="line_summary"><?= $values["count"] ?> <span class="x">x</span> <?= $values["name"]." á ".$values["unit_price"] ?> kr.</dt>
			<dd class="total_price"><?= $values["total_price"] ?> kr.</dd>
		</dl>
		<? endforeach; ?>
		<? endif; ?>
		<dl class="subtotal">
			<dt>Registreret kontantsalg i alt</dt>
			<dd><?= $TC->calculateCashSalesSum($cash_order_items_summary) ?: 0 ?> kr.</dd>
		</dl>
	</div>

	<div class="calculated">
		<h3>Beregnede beløb</h3>
		<dl class="sales_by_the_piece">
			<dt>Løssalg</dt>
			<dd><?= $calculated_sales_by_the_piece ?: 0 ?> kr.</dd>
		</dl>
		<dl class="change">
			<dt>Byttepenge til næste uge</dt>
			<dd><?= $TC->calculateChange($tally_id) ?> kr.</dd>
		</dl>
	</div>
	








	</div>
	
</div>