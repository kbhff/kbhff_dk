<?php
global $action;
global $model;

$IC = new Items();
$page_item = $IC->getItem(array("tags" => "page:bliv-medlem-verificer-skip", "extend" => array("user" => true, "tags" => true, "mediae" => true)));
if($page_item) {
	$this->sharingMetaData($page_item);
}


?>
<div class="scene signup i:scene">
	<h1>Okay cool... </h1>
	<h2>Glem ikke at verificere senere!</h2>
	<p>Hvis du vil have adgang til din konto og medlemskab hos KBHFF, skal du verificere din email.</p>
	<p>Hvis du har mistet din verificeringsemail, kan du få tilsendt en ny, hvis du forsøger at logge ind.</p>
</div>
