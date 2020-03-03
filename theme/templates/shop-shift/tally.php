<?php
$IC = new Items();
global $action;
global $TC;
$SC = new Shop();

include_once("classes/system/department.class.php");
$DC = new Department();

$tally_id = $action[1];
$tally = $TC->getTally(["id" => $tally_id]);
$department = $DC->getDepartment(["id" => $tally["department_id"]]);

$payouts = $TC->getPayouts($tally_id);
$revenues = $TC->getMiscRevenues($tally_id);
$calculated_sales_by_the_piece = $TC->calculateSalesByThePiece($tally_id);
$cash_order_items_summary = $TC->cashOrderItemsSummary($tally_id);

$this->pageTitle("Kasseregnskab");
?>

<div class="scene tally i:tally tally_id:<?=$tally_id?>">

	<div class="banner i:banner variant:1 format:jpg"></div>

	<? if(message()->hasMessages()): ?>
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

	<div class="c-wrapper">

		<div class="full-width">

			<div class="section intro">
				<h1>Dagens kasseregnskab</h1>
				<p>Afdeling: <?=$department["name"] ?></p>
				<p>Oprettet: <?=$tally["created_at"] ?></p>
				<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Delectus accusamus eos incidunt! Corrupti qui nostrum facere possimus. Aperiam sunt ullam, dolorem temporibus quam officiis iure possimus molestiae nesciunt dolor quidem quis sint porro commodi. Quod excepturi provident neque harum corrupti deserunt alias voluptatibus totam, ratione, a explicabo ad facilis possimus.</p>

			</div>

			<? if($tally["status"] == 1): ?>
			<div class="section tally">
				<div class="cash">
					<h3>Kassebeholdning</h3>

					<div class="start_cash">

						<div class="view">
							<ul class="actions">
								<li class="description">Ved vagtstart</li>
								<li class="amount"><?= $tally["start_cash"] ? $tally["start_cash"] . " kr." : "" ?></li>
								<li class="edit_btn"><a href="#" class="button">Redigér</a></li>
							</ul>
						</div>

						<div class="edit">
							<?= $TC->formStart("kasse/$tally_id/updateTally", ["class" => "labelstyle:inject start_cash"]); ?>
								<fieldset>
									<?= $TC->input("start_cash", ["label" => "Ved vagtstart", "value" => $tally["start_cash"]]); ?>
								</fieldset>
								
							<ul class="actions">
								<?= $TC->submit("Gem", ["wrapper" => "li.save", "class" => "primary"]); ?>
							</ul>
							<?= $TC->formEnd(); ?>

						</div>

					</div>

					<div class="end_cash">

						<div class="view">
							<ul class="actions">
								<li class="description">Ved vagtafslutning</li>
								<li class="amount"><?= $tally["start_cash"] ? $tally["start_cash"] . " kr." : "" ?></li>
								<li class="edit_btn"><a href="#" class="button">Redigér</a></li>
							</ul>
						</div>
						<div class="edit">
						
							<?= $TC->formStart("kasse/$tally_id/updateTally", ["class" => "labelstyle:inject end_cash"]); ?>
								<fieldset>
									<?= $TC->input("end_cash", ["label" => "Ved vagtslut - tæl kassen op inden evt. deponering", "value" => $tally["end_cash"]]); ?>
								</fieldset>
								
							<ul class="actions">
								<?= $TC->submit("Gem", ["wrapper" => "li.save", "class" => "primary"]); ?>
							</ul>
							<?= $TC->formEnd(); ?>
						</div>

					</div>
	
					<div class="deposited">

						<div class="view">
							<ul class="actions">
								<li class="description">Evt. deponeret</li>
								<li class="amount"><?= $tally["start_cash"] ? $tally["start_cash"] . " kr." : "" ?></li>
								<li class="edit_btn"><a href="#" class="button">Redigér</a></li>
							</ul>
						</div>

						<div class="edit">
						
							<?= $TC->formStart("kasse/$tally_id/updateTally", ["class" => "labelstyle:inject deposit"]); ?>
								<fieldset>
									<?= $TC->input("deposited", ["label" => "Evt. deponeret", "value" => $tally["deposited"]]); ?>
								</fieldset>
								
							<ul class="actions">
								<?= $TC->submit("Gem", ["wrapper" => "li.save", "class" => "primary"]); ?>
							</ul>
							<?= $TC->formEnd(); ?>
						</div>

					</div>
				</div>

				<div class="payouts">
					<h3>Udbetalinger <span class="sum"><?= $TC->getPayoutsSum($tally_id) ?> kr.</span></h3>
				

					<? if($payouts): ?>
					<? foreach($payouts as $payout):?>
					<? $payout_id = $payout["id"]; ?>

					<ul class="payout">
						<li class="name"><?= $payout["name"] ?></li>
						<li class="amount"><?= $tally["start_cash"] ? $tally["start_cash"] . " kr." : "" ?></li>
						<?= $HTML->oneButtonForm("Slet", "/butiksvagt/kasse/$tally_id/udbetaling/deletePayout/$payout_id", [
							"wrapper" => "li.delete",
							"confirm_value" => "Bekræft"

							]) ?>
					</ul>

					<? endforeach; ?>
					<? else: ?>

					<p>Ingen udbetalinger.</p>

					<? endif; ?>

					<div class="add_payout">

						<ul class="actions">
							<li class="add_payout"><a href="<?= $tally_id ?>/udbetaling" class="button">Ny udbetaling fra kassen</a></li>
						</ul>
					
					</div>


				</div>

				<div class="misc_revenues">
					<h3>Andre kontante indtægter <span class="sum"><?= $TC->getMiscRevenuesSum($tally_id) ?> kr.</span></h3>

					<? if($revenues): ?>
					<? foreach($revenues as $revenue):?>
					<? $revenue_id = $revenue["id"]; ?>

					<ul class="revenue">
						<li class="name"><?= $revenue["name"] ?></li>
						<li class="amount"><?= $tally["start_cash"] ? $tally["start_cash"] . " kr." : "" ?></li>
						<?= $HTML->oneButtonForm("Slet", "/butiksvagt/kasse/$tally_id/andre-indtaegter/deleteRevenue/$revenue_id", [
							"wrapper" => "li.delete",
							"confirm_value" => "Bekræft"

							]) ?>
					</ul>

					<? endforeach; ?>
					<? else: ?>

					<p>Ingen indtægter.</p>

					<? endif; ?>

					<div class="add_revenue">

						<ul class="actions">
							<li class="add_revenue"><a href="<?= $tally_id ?>/andre-indtaegter" class="button">Ny kontant indtægt</a></li>
						</ul>
					
					</div>

				</div>

				<div class="cash_sales">
					<? if($cash_order_items_summary): ?>
					
					<h3>Registreret kontantsalg <span class="sum"><?= $TC->calculateCashSalesSum($cash_order_items_summary) ?> kr.</span></h3>
					<? foreach($cash_order_items_summary as $item_id => $values): ?>					
					<ul class="<?= $values["itemtype"]." ".$values["name"] ?>">
						<li class="line_summary"><?= $values["count"] ?> <span class="x">x</span> <?= $values["name"]." (".$values["itemtype"].") á ".$values["unit_price"] ?> kr.</li>
						<li class="total_price"><?= $values["total_price"] ?> kr.</li>
					</ul>
					<? endforeach; ?>
					<? else: ?>
					<h3>Registreret kontantsalg <span class="sum">0 kr.</span></h3>
					<p>Intet registreret kontantsalg.</p>
					<? endif; ?>
					
					

				</div>

				<div class="calculated_sales">
					<h3>Beregnet løssalg <span class="sum"><?= $calculated_sales_by_the_piece ?> kr.</span></h3>

				</div>

				<div class="change">
					<h3>Byttepenge til næste uge (kassebeholdning ved slut minus deponerede penge) <span class="sum"><?= $TC->calculateChange($tally_id); ?> kr.</span></h3>
				</div>

				<?= $TC->formStart("kasse/$tally_id/updateTally", ["class" => "labelstyle:inject comment"]); ?>

					<fieldset>
						<?= $TC->input("comment"); ?>

					</fieldset>
				
					<ul class="actions">
						<?= $TC->submit("Gem og gå tilbage", ["wrapper" => "li.save"]); ?>
						<?= $TC->submit("Godkend regnskab og luk kasse", ["wrapper" => "li.save", "class" => "primary", "formaction" => "$tally_id/closeTally"]) ?>
					</ul>
				<?= $TC->formEnd(); ?>

				
			</div>
			<? else: ?>
			<div class="section tally closed">
				<p>Denne kasse er afstemt og lukket.</p>
			</div>
			<? endif; ?>

			

		</div>


	</div>
</div>
