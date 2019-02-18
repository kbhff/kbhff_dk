<?php
// Get methods for user and shop data manipulation
global $model;
global $action;

include_once("classes/system/department.class.php");
$DC = new Department();
$departments = $DC->getDepartments();

$UC = new User();
$user_department = $UC->getUserDepartment();
$department_name = $user_department["name"];

$user = $UC -> getUser();
$orders = $model->getOrders();

$order_state_options = array("0" => "Udleverede &amp; afventer", "1" => "Kun afventer", "2" => "Kun udleverede");
//$order_state_options = $model->getOrderStatuses();

$department_options = array_merge(array("0" => "Alle departments"), $HTML->toOptions($departments, "id", "name", ["1" => [$user_department["name"]]]));

?>
<div class="scene profile i:profile">

	<div class="banner i:banner variant:1 format:jpg"></div>

	<?	// Display any backend generated messages
		if(message()->hasMessages()): ?>
		
			<p class="errormessage">
		<?	$messages = message()->getMessages(array("type" => "error"));
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

	<div class="c-wrapper">
		<div class="c-two-thirds">
			<div class="section intro">
				<h2>DAGENS BESTILLINGER<span>sexyspan</span></h2>
				<p>
<?= $model->formStart("soeg", array("class" => "search_user labelstyle:inject")) ?>
<? // show error messages 
if(message()->hasMessages(array("type" => "error"))): ?>
					<p class="errormessage">
<?	$messages = message()->getMessages(array("type" => "error"));
		message()->resetMessages();
		foreach($messages as $message): ?>
		<?= $message ?><br>
<?	endforeach;?>
					</p>
<?	endif; ?>
	
					<fieldset>
						<?= $model->input("user_id", array("type" => "hidden", "value" => $user["id"])); ?>
						<?= $model->input("search", array("hint_message" => "Navn, email, mobilnr eller medlemsnr", "error_message" => "Du skal som minimum angive 3 tegn")) ?>
						<?= $model->input("status", array("type" => "select", "options" => $order_state_options, "value" => getPost("status")));?>
					</fieldset>
				</p>
			</div>
	<ul class="actions">
		<?= $model->submit("Søg", array("class" => "primary", "wrapper" => "li.search")) ?>
		
	</ul>

			<div class="section orders">
				<h2>Eksisterende bestillinger</h2>

				<div class="order-headings">
					<h4 class="name">Navn</h4>
					<h4 class="vare">VARE</h4>
				</div>
<?php
if($orders):  
	?>
	<ul class="orders">
		 <? // print_r($users)?>  
	<? foreach($orders as $u => $order): 
	print_r($order);?>
				<div class="order">
					<p class="status"><?=$order["status"];?></p>
					<p class="name"><?=$order["billing_name"];?></p>
					<p class="vare">??</p>
					<ul class="actions change">
						<li class="change"><a href="#" class="button disabled">Udlevér</a></li>
						<li class="change"><a href="#" class="button disabled">Genbestil</a></li>
						<li class="change"><a href="#" class="button disabled">Send SMS</a></li>
					</ul>
				</div>
	<? endforeach; ?>
	</ul>
<?	endif; ?>		

				
			</div>

		</div>

		<div class="c-one-third">

			<ul class="actions">
				<li class="new-order full-width"><a href="#" class="button primary">Åbn kasse (angiv kassebeholdning)</a></li>
				<li class="book-shift full-width"><a href="#" class="button primary">Luk kasse (angiv kassebeholdning)</a></li>
				<li class="book-shift full-width"><a href="#" class="button primary">Ny udbetaling fra kassen</a></li>
			</ul>

			<div class="section department">
				<div class="c-box">
					<h3>Dagens kasseregnskab</h3>

					<div class="fields">
						<div class="department-info">
							<p><span class="items">Registreret kontantsalg</span><span class="price">1.400 kr.</span></p>
							<p class="under">
								<ul>
									<li>5 Grøntsagsposer à 120 kr. <span class="price">600 kr.</span></li>
									<li>6 Frugtposer à 60 kr. <span class="price">300 kr.</span></li>
									<li>1 indmeldelsesgebyr à 100kr. <span class="price">100 kr.</span></li>
									<li>2 kontingenter à 200 kr. <span class="price">400 kr.</span></li>
								</ul>
							</p>
						</div>

						<div class="department-info">
							<p><span class="items">Udbetalinger fra kassen</span><span class="price">-300 kr.</span></p>
							<p class="under">
								<ul>
									<li>Tilbagebet. udlæg <span class="price">-230 kr.</span></li>
									<li>Tilbagebet. udlæg <span class="price">-70 kr.</span></li>
								</ul>
							</p>
						</div>

						<div class="department-info">
							<p><span class="items">Registreret resultat</span><span class="price">1.100 kr.</span></p>
						</div>

						<div class="department-info">
							<p><span class="items">Optalt resultat</span><span class="price">1.050 kr.</span></p>
							<p class="under">
								<ul>
									<li>Tilbagebet. udlæg <span class="price">-230 kr.</span></li>
									<li>Tilbagebet. udlæg <span class="price">-70 kr.</span></li>
								</ul>
							</p>
						</div>

						<div class="department-info">
							<p><span class="items">Difference</span><span class="price">- 50 kr.</span></p>
						</div>
					</div>

				</div>
			</div>

		</div>

	</div>
</div>

