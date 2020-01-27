<?php
$IC = new Items();
global $action;
global $TC;

include_once("classes/system/department.class.php");
$DC = new Department();

$tally_id = $action[1];
$tally = $TC->getTally(["id" => $tally_id]);
$department = $DC->getDepartment(["id" => $tally["department_id"]]);

$payouts = $TC->getPayouts($tally_id);
$revenues = $TC->getMiscRevenues($tally_id);

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

			<div class="section tally">
				<div class="cash">
					<h2>Kassebeholdning</h2>

					<div class="start_cash">

						<div class="view">
							<ul class="actions">
								<li class="description">Ved vagtstart</li>
								<li class="amount"><?= $tally["start_cash"] ?></li>
								<li class="edit_btn"><a href="#" class="button">Redigér</a></li>
							</ul>
						</div>

						<div class="edit">
							<?= $TC->formStart("kasse/$tally_id/updateTally", ["class" => "labelstyle:inject start_cash"]); ?>
								<fieldset>
									<?= $TC->input("start_cash", ["label" => "Ved vagtstart", "value" => $tally["start_cash"]]); ?>
								</fieldset>
								
							<ul class="actions">
								<?= $TC->submit("Gem", ["wrapper" => "li.save"]); ?>
							</ul>
							<?= $TC->formEnd(); ?>

						</div>

					</div>

					<div class="end_cash">

						<div class="view">
							<ul class="actions">
								<li class="description">Ved vagtafslutnig</li>
								<li class="amount"><?= $tally["end_cash"] ?></li>
								<li class="edit_btn"><a href="#" class="button">Redigér</a></li>
							</ul>
						</div>
						<div class="edit">
						
							<?= $TC->formStart("kasse/$tally_id/updateTally", ["class" => "labelstyle:inject end_cash"]); ?>
								<fieldset>
									<?= $TC->input("end_cash", ["label" => "Ved vagtslut - tæl kassen op inden evt. deponering", "value" => $tally["end_cash"]]); ?>
								</fieldset>
								
							<ul class="actions">
								<?= $TC->submit("Gem", ["wrapper" => "li.save"]); ?>
							</ul>
							<?= $TC->formEnd(); ?>
						</div>

					</div>
	
					<div class="deposited">

						<div class="view">
							<ul class="actions">
								<li class="description">Evt. deponeret</li>
								<li class="amount"><?= $tally["deposited"] ?></li>
								<li class="edit_btn"><a href="#" class="button">Redigér</a></li>
							</ul>
						</div>

						<div class="edit">
						
							<?= $TC->formStart("kasse/$tally_id/updateTally", ["class" => "labelstyle:inject deposit"]); ?>
								<fieldset>
									<?= $TC->input("deposited", ["label" => "Evt. deponeret", "value" => $tally["deposited"]]); ?>
								</fieldset>
								
							<ul class="actions">
								<?= $TC->submit("Gem", ["wrapper" => "li.save"]); ?>
							</ul>
							<?= $TC->formEnd(); ?>
						</div>

					</div>
				</div>

				<div class="payouts">
					<h2>Udbetalinger</h2>
				

					<? if($payouts): ?>
					<? foreach($payouts as $payout):?>
					<? $payout_id = $payout["id"]; ?>

					<ul class="payout">
						<li class="name"><?= $payout["name"] ?></li>
						<li class="amount"><?= $payout["amount"] ?></li>
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
					<h2>Andre kontante indtægter</h2>

					<? if($revenues): ?>
					<? foreach($revenues as $revenue):?>
					<? $revenue_id = $revenue["id"]; ?>

					<ul class="revenue">
						<li class="name"><?= $revenue["name"] ?></li>
						<li class="amount"><?= $revenue["amount"] ?></li>
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
					<h2>Registreret kontantsalg</h2>
				</div>

				<div class="calculated_sales">
					<h2>Beregnet løssalg</h2>
				</div>

				<div class="change">
					<h2>Byttepenge til næste uge (kassebeholdning ved slut minus deponerede penge)</h2>
				</div>

				<?= $TC->formStart("kasse/$tally_id/updateTally", ["class" => "labelstyle:inject comment"]); ?>

					<fieldset>
						<?= $TC->input("comment"); ?>

					</fieldset>
				
					<ul class="actions">
						<?= $TC->submit("Gem og gå tilbage", ["wrapper" => "li.save"]); ?>
					</ul>
				<?= $TC->formEnd(); ?>

				
			</div>

			

		</div>


	</div>
</div>
