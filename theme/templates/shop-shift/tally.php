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

	<div class="banner i:banner variant:random format:jpg"></div>

	<?= $HTML->serverMessages(); ?>

	<div class="c-wrapper">

		<div class="section intro">
			<h1>Dagens kasseregnskab</h1>

			<dl class="info">
				<dt>Afdeling:</dt>
				<dd><?=$department["name"] ?></dd>
				<dt>Oprettet:</dt>
				<dd><?=date("d.m.Y H:i:s") ?></dd>
			</dl>

			<p>Som kassemester/butiksvagt skal du holde øje med ind- og udbetalinger af kontanter under din vagt. På denne side skal du derfor udfylde nedenstående felter i løbet af vagten. Du kan løbende klikke “Gem og gå tilbage”, men til sidst i vagten er det vigtigt at du klikker “Godkend regnskab og luk kasse”, så kassen er klar til at blive udfyldt i næste uge./p>
			<p>Ved vagtstart åbnes kassen, og den allerede eksisterende kassebeholdning indtastes i feltet vagtstart. På samme måde indtastes ved afdelingens lukketid slutbeløbet i feltet vagtslut inden deponering.
Deponering af penge sker, hvis penge fra kassebeholdningen udtrækkes for at blive overført til KBHFFs økonomiansvarlige via mobilepay eller bankoverførsel. Udbetalinger fra kassen kan f.eks. være udgifter i afdelingen til kaffe, rengøringsmidler, skraldeposer osv. Andre kontante indtægter er f.eks. betalinger for fællesspisning eller kogebøger.</p>
			<p>Til slut kan der aflægges eventuelle kommentarer, inden kassen godkendes og lukkes. Sker der vagtskifte undervejs, kan de pågældende medlemmer henholdsvis lukke og åbne kassen, så længe regnskabet ikke godkendes ved lukning.</p>
			<p>Skriv til <a href="mailto:okonomi@kbhff.dk">okonomi@kbhff.dk</a> og <a href="mailto:it@kbhff.dk">it@kbhff.dk</a> hvis du har spørgsmål til kassen.</p>
		</div>


		<? if($tally["status"] == 1): ?>
		<div class="section tally">
			<div class="cash">
				<h2>Kassebeholdning</h2>

				<div class="start_cash">

					<div class="view">
						<?= $TC->formStart("/butiksvagt/kasse/$tally_id/updateTally", ["class" => "labelstyle:inject start_cash"]); ?>

							<span class="description">Ved vagtstart</span>
							<span class="amount">
								
								<span class="value"><?= $tally["start_cash"] ? $tally["start_cash"] . " kr." : "" ?></span>
								<fieldset>
									<?= $TC->input("start_cash", ["label" => "Ved vagtstart", "value" => $tally["start_cash"]]); ?>
								</fieldset>
							
							</span>
							<span class="edit">

								<ul class="actions">
									<li class="edit"><a href="#" class="button">Redigér</a></li>
									<?= $TC->submit("Gem", ["wrapper" => "li.save", "class" => "primary"]); ?>
								</ul>

							</span>

						<?= $TC->formEnd(); ?>
					</div>

				</div>

				<div class="end_cash">

					<div class="view">
						<?= $TC->formStart("/butiksvagt/kasse/$tally_id/updateTally", ["class" => "labelstyle:inject end_cash"]); ?>

							<span class="description">Ved vagtafslutning - inden evt. Deponering</span>
							<span class="amount">

								<span class="value"><?= $tally["end_cash"] ? $tally["end_cash"] . " kr." : "" ?></span>
								<fieldset>
									<?= $TC->input("end_cash", ["label" => "Ved vagtslut - tæl kassen op inden evt. deponering", "value" => $tally["end_cash"]]); ?>
								</fieldset>

							</span>
							<span class="edit">
							
								<ul class="actions">
									<li class="edit"><a href="#" class="button">Redigér</a></li>
									<?= $TC->submit("Gem", ["wrapper" => "li.save", "class" => "primary"]); ?>
								</ul>

							</span>

						<?= $TC->formEnd(); ?>
					</div>

				</div>

				<div class="deposited">

					<div class="view">
						<?= $TC->formStart("/butiksvagt/kasse/$tally_id/updateTally", ["class" => "labelstyle:inject deposit"]); ?>

							<span class="description">Evt. deponeret</span>
							<span class="amount">

								<span class="value"><?= $tally["deposited"] ? $tally["deposited"] . " kr." : "" ?></span>
								<fieldset>
									<?= $TC->input("deposited", ["label" => "Evt. deponeret", "value" => $tally["deposited"]]); ?>
								</fieldset>

							</span>
							<span class="edit">
								<ul class="actions">
									<li class="edit"><a href="#" class="button">Redigér</a></li>
									<?= $TC->submit("Gem", ["wrapper" => "li.save", "class" => "primary"]); ?>
								</ul>
							</span>

						<?= $TC->formEnd(); ?>
					</div>

				</div>
			</div>


			<div class="payouts">
				<h2>Udbetalinger <span class="sum"><?= $TC->getPayoutsSum($tally_id) ? "-" . $TC->getPayoutsSum($tally_id) : "0" ?> kr.</span></h2>
			

				<? if($payouts): ?>

				<ul class="payouts">

				<? foreach($payouts as $payout):?>
				<? $payout_id = $payout["id"]; ?>

					<li class="payout">
						<span class="description"><?= $payout["name"] ?></span>
						<span class="amount"><?= "-" . $payout["amount"] ?> kr.</span>
						<span class="edit">
							<ul class="actions">
								<?= $HTML->oneButtonForm("Slet", "/butiksvagt/kasse/$tally_id/udbetaling/deletePayout/$payout_id", [
									"wrapper" => "li.delete",
									"confirm-value" => "Bekræft sletning",

								])?>
							</ul>
						</span>
					</li>

				<? endforeach; ?>

				</ul>

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
				<h2>Andre kontante indtægter <span class="sum"><?= $TC->getMiscRevenuesSum($tally_id) ?> kr.</span></h2>

				<? if($revenues): ?>

				<ul class="revenues">

				<? foreach($revenues as $revenue):?>
				<? $revenue_id = $revenue["id"]; ?>

					<li class="revenue">
						<span class="description"><?= $revenue["name"] ?></span>
						<span class="amount"><?= $revenue["amount"] ?> kr.</span>
						<span class="edit">
							<ul class="actions">
								<?= $HTML->oneButtonForm("Slet", "/butiksvagt/kasse/$tally_id/andre-indtaegter/deleteRevenue/$revenue_id", [
									"wrapper" => "li.delete",
									"confirm-value" => "Bekræft sletning",
								]) ?>
							</ul>
						</span>
					</li>

				<? endforeach; ?>

				</ul>

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

				<h2>Registreret kontantsalg <span class="sum"><?= $TC->calculateCashSalesSum($cash_order_items_summary) ?> kr.</span></h2>

				<ul class="cash_sales">
				<? foreach($cash_order_items_summary as $item_id => $values): ?>					
					<li class="<?= $values["itemtype"]." ".$values["name"] ?>">
						<span class="line_summary"><?= $values["count"] ?> <span class="x">x</span> <?= $values["name"]." (".$values["itemtype"].") á ".$values["unit_price"] ?> kr.</span>
						<span class="total_price"><?= $values["total_price"] ?> kr.</span>
					</li>
				<? endforeach; ?>
				</ul>

				<? else: ?>

				<h2>Registreret kontantsalg <span class="sum">0 kr.</span></h2>
				<p>Intet registreret kontantsalg.</p>

				<? endif; ?>

			</div>


			<div class="calculated_sales">
				<h2>Beregnet løssalg <span class="sum"><?= $calculated_sales_by_the_piece ?> kr.</span></h2>

			</div>


			<div class="change">
				<h2>Byttepenge til næste uge <span class="sum"><?= $TC->calculateChange($tally_id); ?> kr.</span></h2>
				<p> (Kassebeholdning ved slut minus deponerede penge)</p>
			</div>


			<?= $TC->formStart("/butiksvagt/kasse/$tally_id/saveTally", ["class" => "labelstyle:inject comment"]); ?>
				<fieldset>
					<?= $TC->input("comment", ["value" => $tally["comment"]]); ?>

				</fieldset>
			
				<ul class="actions">
					<?= $TC->submit("Gem og gå tilbage", ["wrapper" => "li.save", "formaction" => "/butiksvagt/kasse/$tally_id/saveTally"]); ?>
					<?= $TC->submit("Godkend regnskab og luk kasse", ["wrapper" => "li.save", "class" => "primary", "formaction" => "/butiksvagt/kasse/$tally_id/closeTally"]) ?>
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
