<?php
$IC = new Items();

$page_item = $IC->getItem(array("tags" => "page:front", "status" => 1, "extend" => array("user" => true, "mediae" => true, "tags" => true)));
if($page_item) {
	$this->sharingMetaData($page_item);
}

$WBC = $IC->typeObject("weeklybag");
$weeklybag_item = $WBC->getWeeklyBag();

$post_items = $IC->getItems([
	"itemtype" => "post",
	"tags" => "on:frontpage",
	"status" => 1,
	"limit" => 6,
	"extend" => [
		"tags" => true,
		"readstate" => true,
		"user" => true,
		"mediae" => true
	]
]);
?>
<div class="scene front i:scene i:front">
	<div class="banner i:banner variant:random format:jpg"></div>

	<h1>Velkommen til KBHFF</h1>
	<div class="c-wrapper">

		<ul class="actions">
		<? if(session()->value("user_id") != 1): ?>
			<li class="shift"><a href="https://wiki.kbhff.dk/tiki-index.php?page=Vagtplaner" class="button primary">Ta' en vagt</a></li>
			<li class="order"><a href="/butik" class="button primary">Bestil en pose</a></li>
		<? else: ?>
			<li class="member"><a href="/bliv-medlem" class="button primary">Bliv medlem</a></li>
			<li class="login"><a href="/login" class="button primary">Login</a></li>
		<? endif; ?>
		</ul>

		<!-- icons from https://icons.getbootstrap.com/ -->
		<div class="steps" itemscope itemtype="http://schema.org/NewsArticle">
			<h2 itemprop="headline">Nemt at komme i gang</h2>
			<ul class="steps" itemprop="description">
				<li class="step one">
					<a href="/bliv-medlem">
						<div class="image">
							<!-- https://icon666.com/icon/verified_user_ztwkjmjl87n1 -->
							<svg viewBox="-2 -2 62 62" width="64" height="64" stroke-width="0" xmlns="http://www.w3.org/2000/svg">
								<path d="m46 32a13.927 13.927 0 0 0 -9.06 3.346c-.35-.41-.709-.809-1.093-1.193a20.881 20.881 0 0 0 -9.177-5.364 14.012 14.012 0 0 0 8.33-12.789v-2a14 14 0 0 0 -28 0v2a14.013 14.013 0 0 0 8.344 12.8 21.022 21.022 0 0 0 -15.344 20.2v5a4 4 0 0 0 4 4h34a4.009 4.009 0 0 0 .707-.072 13.992 13.992 0 1 0 7.293-25.928zm-37-16v-2a12 12 0 0 1 24 0v2a12 12 0 0 1 -24 0zm-5 40a2 2 0 0 1 -2-2v-5a19 19 0 0 1 32.434-13.432c.381.381.734.779 1.078 1.19a13.937 13.937 0 0 0 .709 19.242zm42 2a12 12 0 1 1 12-12 12.013 12.013 0 0 1 -12 12z" />
								<path d="m50.211 40.386-7.082 9.106-1.422-1.422a1 1 0 0 0 -1.414 1.414l2.223 2.223a1 1 0 0 0 .707.293h.062a1 1 0 0 0 .727-.384l7.777-10a1 1 0 1 0 -1.578-1.228z" />
							</svg>
						</div>
						<h3>1.</h3>
						<div class="description">
							<p>Bliv medlem</p>
						</div>
					</a>
				</li>
				<li class="step two">
					<a href="/butik">
						<div class="image"> 
							<!-- https://icon666.com/icon/vegetable_dsyqaf66i4p5 – modified -->
							<svg  width="64" height="64" stroke-width="1" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
								<path d="M63.6,14.6c-.1-.1-.3-.2-.5-.3-.1,0-.3-.1-.5-.1,0-.4,0-1-.2-1.6-.2-.5-.6-.8-.9-1l.6-.9c.6-.9.4-2.1-.4-2.7-.4-.3-.9-.4-1.2-.4,0-.6-.1-1.4-.7-2.1,0,0,0,0,0,0-.4-.5-1.1-.6-1.7-.5-.6.1-1.2.6-1.4,1.2l-1,3v-1.4c0-.3-.2-.5-.3-.7-.2-.3-.6-.7-1.2-.8-.4,0-.8,0-1.1.3-.3.3-.5.7-.5,1.1v6.3c-1.4.3-2.5,1.1-3.1,2.4l-.8,1.6c-.5-.4-1.1-.9-1.6-1.8-.3-.6-.5-1.2-.6-1.6-.1-.6-.3-1.2-.9-1.7-.8-.6-2-.6-3.5,0-.5-.4-1-.6-1.4-.7-.3-1-1.1-1.8-2.2-2.1-.9-.3-1.8-.1-2.6.3-.1-.7-.4-1.4-1-1.8-1-.7-2.5-.7-4,.1-1,.5-2.2.7-3.4.6-1.3-.2-2.5.2-3.5.9-1.1.9-1.5,2.2-1.5,3.1-.2,0-.5-.2-.8-.2-.4,0-.8,0-1.2,0,.7-2,1.4-3.9,2.1-5.7,0-.2,0-.4,0-.5-.1-.1-.3-.2-.5-.2-.9.1-2.2.5-3.5,1.5-.6.5-1,1-1.4,1.5l.2-5c0-.2,0-.3-.2-.4-.1,0-.3-.1-.5,0-.8.4-1.9,1.1-2.8,2.5-.6.9-.8,1.7-1,2.5-1.2-2.3-2.8-4.5-4.5-6.5-.1-.2-.4-.2-.6-.1-.2,0-.3.3-.3.5.1,2.8.6,5.7,1.3,8.4-.5-.1-1-.2-1.5-.2-1.2-.1-2.4,0-3.5,0-.2,0-.4.2-.4.4,0,.2,0,.4.2.5.8.7,1.7,1.3,2.6,1.9.1,0,.3.2.4.2-1.2-.1-2.4-.2-3.6-.1-.2,0-.4.1-.5.3,0,.2,0,.4.1.5,1.3,1.2,2.6,2.3,4,3.4-2,1.1-3.3,3.1-3.3,5.2-.7,1-1.1,2.1-1.1,3.4s0,.8.1,1.2H.6c-.2,0-.3,0-.4.2,0,.1-.1.3,0,.4.7,3.1,1.1,6.1,1.1,9.2,0,2.9-.3,5.1-.6,7.2-.4,2.6-.7,5.1-.5,8.6.1,2.4.5,4.8,1.1,7.2,0,.2.3.4.5.4h57.8c.2,0,.4-.2.5-.4.6-2.4,1-4.8,1.1-7.2.2-3.5-.1-6-.5-8.6-.3-2.1-.6-4.3-.6-7.2,0-3,.4-6.1,1.1-9.2,0-.2,0-.3,0-.4,0-.1-.2-.2-.4-.2h-1.7c.2-.2.5-.4.7-.6.3-.3.5-.6.8-.9,1.1-1.5,1.2-3.5.2-5l3.2-5.2c.4-.6.3-1.4-.2-1.9ZM61.5,14.5c-.2.1-.4.3-.5.6l-1.4,2.9c.1-1,.3-2.3.5-4l1-1.5c.2.1.4.3.5.6.3.6,0,1.3,0,1.5ZM60.3,8.6c.2,0,.6,0,.9.2.4.3.4.8.2,1.3l-4,6c-.1-.2-.2-.3-.4-.5l3.4-6.9ZM57.8,17.1l.9-1.4c-.2,1.7-.4,3.4-.5,4.3-.2,0-.4-.1-.6-.1.3-.9.4-1.9.1-2.7ZM55.8,12h0s1.9-5.5,1.9-5.5c0-.3.3-.5.6-.6.3,0,.5,0,.7.2.5.5.5,1.2.5,1.6,0,0,0,.2,0,.3l-3.3,6.8s0,0,0,0h0c-.3-.3-.7-.4-1-.6l.8-2.2ZM53.6,7.7c0-.1,0-.2.2-.3,0,0,.2-.1.3,0,.3,0,.4.3.5.4,0,0,0,.1,0,.2l.2,3.9-.7,2.2c-.2,0-.4,0-.6,0v-6.2ZM51.5,23.1c0,0,.2,0,.2,0,.2,0,.3,0,.4-.3.1-.2,0-.6-.2-.7l-2.2-1.3c.2-.3.3-.5.3-1,0-.6-.3-1-.7-1.4l.9-1.7c.8-1.6,2.7-2.3,4.3-1.7.3.1.6.3.9.5h0c1.4,1,1.8,2.9,1,4.5-.5,1-1.1,2-1.7,3-1,1.7-2.1,3.5-3.1,5.2h-4c0-.4,0-.8-.1-1.2,0-.2-.3-.4-.5-.4h-.6c.8-.4,1.4-1,1.7-1.7.1-.4.2-.7.2-1,0-.4.1-.8.4-1.4.1-.3.3-.6.4-.8l2.4,1.3ZM18.5,28.8c-.1,4,1.2,7.9,3.8,10.9,2.3,2.7,5.5,4.3,8.4,4.3s6-1.6,8.4-4.3c2.6-3,3.9-6.9,3.8-10.9,0,0,0,0,0,0s0,0,0,0c0-.4,0-.7,0-1.1h3.9c.6,5.1-.9,10.1-4.3,14-3.1,3.6-7.4,5.7-11.7,5.7s-8.6-2.1-11.7-5.7c-3.3-3.9-4.9-8.9-4.3-14h3.9c0,.4,0,.7,0,1.1,0,0,0,0,0,0s0,0,0,0ZM19.5,29.3h10.3s0,0,0,0c0,0,0,0,0,0h2.3s0,0,0,0,0,0,0,0h9.7c0,3.6-1.2,7.1-3.5,9.7-2.2,2.5-5,4-7.6,4s-5.5-1.5-7.6-4c-2.3-2.7-3.6-6.1-3.5-9.7ZM45.1,13.7c.3.2.4.5.5,1,.1.5.2,1.1.6,1.9.6,1.2,1.5,1.8,2.1,2.3.6.5.9.7.9,1,0,.2,0,.3-.3.6-.2.2-.4.5-.7,1,0-.2-.1-.5-.2-.8-.2-.6-.5-1.2-.9-1.7-.2-.2-.5-.3-.7-.1-.2.2-.3.5-.1.7.3.4.6.9.8,1.4.5,1.4.4,2.6.2,3.5-.4.8-1.4,1.5-3.2,1.8.6-.9.8-1.7.6-2.6-.2-1.3-1.3-2.2-2.1-2.8-.5-.4-1.1-.8-1-1.1,0-.3.7-.5,1.2-.7.9-.3,2-.8,2.2-1.8.3-1.2-.6-2.2-1.4-3.1-.2-.2-.4-.4-.5-.6.9-.3,1.6-.3,2,0ZM42.8,14.9c.6.7,1.3,1.5,1.1,2.2-.1.5-.9.8-1.6,1.1-.3-.7-.7-1.4-1.2-1.8-.2-.2-.5-.4-.7-.5v-1.3c0-.3-.2-.5-.5-.5s-.5.2-.5.5v.9c-.4,0-.7-.1-1.1-.2-.6,0-1.2-.2-1.9-.4.6-.1,1.1-.3,1.5-.6.5-.3.8-.6,1-.8.3-.2.4-.4.7-.4.5-.1,1.6,0,3.1,1.8ZM38.3,11c.6.2,1,.5,1.3,1,0,0,0,0-.1,0-.6.1-.9.4-1.2.7,0,0,0,0-.1.1-.4-.7-1-1.2-1.6-1.6.5-.3,1.2-.4,1.7-.2ZM37.3,13.5c-.7.4-1.6.6-2.6.6-.3-.3-.6-.5-.8-.8-.3-.4-.7-.9-1.4-1.1.5-.3,1.2-.6,2-.5,1.1,0,2.2.8,2.7,1.8ZM24.8,11c.7-.6,1.7-.9,2.7-.7,1.4.2,2.8,0,4-.7,1.1-.6,2.2-.7,2.9-.2.4.3.6.9.6,1.3-.1,0-.2,0-.3,0-1.8-.1-3.1.9-3.6,
								1.4-1.3,0-2.4.8-3.4,1.5-.4.3-.9.6-1.3.8,0-.4,0-.8.1-1.2.1-.4.3-.8.6-1.1.2-.2.1-.5,0-.7-.2-.2-.5-.1-.7,0-.3.4-.6.9-.7,1.4-.2.7-.2,1.4-.2,1.9-.4,0-.7,0-1,0-.3-.1-.6-.3-.8-.6,0-.5,0-2,1.2-3ZM21.7,14.1c.5,0,.8.3,1.1.5,0,0,0,0,0,0,0,0,0,0,.1.1.3.3.6.6,1.1.8,1.5.6,2.9-.3,4.2-1.2,1.2-.8,2.4-1.6,3.7-1.3.6.1.8.4,1.2.9.4.5,1,1.2,2.4,1.8,1,.4,1.9.5,2.6.6.6,0,1.1.1,1.5.3,0,0,0,0,0,0,.3.1.5.3.8.5.4.4.7.9,1,1.5-.5.3-.9.6-1,1.1-.1.9.6,1.5,1.4,2.1.8.6,1.6,1.2,1.8,2.2.1.8-.2,1.6-1.1,2.6h-.4c-.1,0-.3,0-.4.2,0,.1-.1.3-.1.4,0,.4,0,.7.1,1.1h-9.1l.6-9.7c0-1.3-.5-2.6-1.5-3.5,0,0,0,0,0,0-.2-.2-.5-.1-.7,0-.2.2-.1.5,0,.7.8.7,1.2,1.7,1.1,2.7l-.6,9.7h-1.4c-.2-1.2-.4-2.4-.5-3.5-.1-1.2-.1-2.4-.1-3.6,0-.3-.2-.5-.5-.5-.3,0-.5.2-.5.5,0,1.2,0,2.5.1,3.7.1,1.2.3,2.3.5,3.4h-1.2c0-1.7-.3-3.5-.7-5.2-.4-1.5-.9-3-1.6-4.4-.1-.3-.4-.4-.7-.2-.3.1-.4.4-.2.7.7,1.4,1.2,2.8,1.5,4.2.4,1.6.6,3.3.6,4.9h-1.1c0-.2,0-.5,0-.7,0-2.1-1.1-3.9-2.8-5-.6-1.4-1.3-2.9-2.1-4.3-.2-.4-.4-.7-.5-1-.8-1.5-1.1-2.1-.9-2.5.3-.5,1.3-.8,2.2-.7ZM21.6,21.8c-.8-.3-1.7-.5-2.6-.5s-.4,0-.6,0c.3-1.3.6-2.7,1-4,0,.1.1.2.2.4.2.3.3.6.5,1,.5,1,1,2,1.5,3ZM17.2,12c.3-.8.9-2,2.1-3,.7-.6,1.5-.9,2.1-1.1-.7,1.8-1.4,3.7-2,5.6-.3.2-.6.5-.8.8-.3.5-.2,1,0,1.7-.5,1.8-1,3.7-1.4,5.5-.1,0-.2,0-.3,0l.3-9.6ZM19,22.4c3.3,0,5.9,2.3,5.9,5.2s0,.5,0,.7h-5.3c0-.4,0-.7.1-1.1,0-.1,0-.3-.1-.4,0-.1-.2-.2-.4-.2h-3.1c0-1.1-.4-2-1-2.9,1.1-.9,2.5-1.4,4-1.4ZM14.8,7.3c.5-.8,1.1-1.3,1.6-1.7l-.4,11.9c-.4-1.9-1-3.7-1.7-5.5-.1-.3-.2-.6-.4-.8,0-.7-.2-2.3.9-3.9ZM8.7,4.6c1.9,2.4,3.5,5,4.7,7.8,1.3,3.1,2.1,6.3,2.4,9.7-.3.1-.5.3-.8.4-1.7-2.6-3.2-5.5-4.2-8.4-.2-.7-.5-1.3-.7-2,0,0,0,0,0,0-.7-2.4-1.2-4.9-1.4-7.5ZM5.2,12.2c.7,0,1.4,0,2,0,.7,0,1.3.2,2,.3.2.6.4,1.2.6,1.8,0,.1,0,.3.1.4-1.1-.4-2.2-1-3.2-1.6-.5-.3-1-.6-1.5-1ZM4.3,15.2c1.1,0,2.1,0,3.2.2,1,.1,2,.4,2.9.6.8,2,1.7,3.9,2.9,5.7-1.7-1-3.3-2.1-4.9-3.3-1.4-1-2.7-2.1-4-3.3ZM7.5,19.1c0,0,.1.1.2.2.7.5,1.5,1.1,2.2,1.6-.3,0-.6,0-.9,0-1.8,0-3.4.6-4.6,1.6.4-1.4,1.5-2.6,3-3.3ZM9.1,21.8c3.1,0,5.6,2.1,5.9,4.8h-.8c-.3,0-.5.2-.5.4,0,.4,0,.8-.1,1.2H3.4c-.1-.4-.2-.8-.2-1.2,0-2.9,2.6-5.2,5.9-5.2ZM60,29.3c-.7,2.9-1,5.9-1,8.8,0,3,.3,5.2.6,7.4.4,2.6.7,5,.5,8.4-.1,2.2-.5,4.4-1,6.6H2.1c-.5-2.2-.9-4.4-1-6.6-.2-3.4.1-5.9.5-8.4.3-2.2.6-4.4.6-7.4,0-2.9-.3-5.8-1-8.8h1.8s0,0,0,0,0,0,0,0h10.5c-.1,4.7,1.5,9.4,4.6,13,3.3,3.9,7.9,6.1,12.5,6.1s9.2-2.2,12.5-6.1c3.1-3.6,4.8-8.3,4.6-13h12.3ZM59.6,26.1c-.2.3-.4.6-.7.8-.5.5-1,1-1.6,1.3h-4.4c.7-1.1,1.4-2.3,2.1-3.5l1.6.7c0,0,.1,0,.2,0,.2,0,.4-.1.5-.3.1-.3,0-.6-.3-.7l-1.5-.6c0-.1.1-.2.2-.3.5-.9,1-1.8,1.6-2.7.5,0,1.5.3,2.3,1.2,0,0,.1.2.2.2.8,1.1.8,2.7-.1,3.9ZM62.9,16l-3,4.9c-.2-.2-.3-.3-.5-.4l2.5-5c.1-.3.5-.4.8-.3,0,0,.1,0,.2.1.2.2.2.5,0,.7Z"/>
								<path d="M34.4,27.6c0,0,.1,0,.2,0,.2,0,.4-.1.5-.3.4-.9.7-1.8.9-2.8.2-1,.4-2,.4-3,0-.3-.2-.5-.5-.5-.3,0-.5.2-.5.5,0,.9-.2,1.9-.4,2.8-.2.9-.5,1.8-.9,2.6-.1.3,0,.6.3.7Z"/>
								<path d="M38,27.5c.2,0,.3,0,.4-.2.4-.6.8-1.2,1.1-1.8.3-.6.6-1.3.8-2,0-.3,0-.6-.3-.6-.3,0-.6,0-.6.3-.2.6-.4,1.3-.7,1.8-.3.6-.6,1.1-1,1.7-.2.2-.1.5.1.7,0,0,.2,0,.3,0Z"/>
							</svg>
						</div>
						<h3>2.</h3>
						<div class="description">
							<p>Bestil en pose</p>
						</div>
					</a>
				</li>
				<li class="step three">
					<a href="/afdelinger">
						<div class="image"> 
							<!-- https://icon666.com/icon/location_pin_mbco4vafsxwn – modified -->
							<svg width="64" height="64" stroke-width="1" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
								<path d="M60.3,55.4V16.3c0-2.6-2.1-4.8-4.8-4.8s-4.8,2.1-4.8,4.8v2.7h-8.4c.2-.7.4-1.3.5-1.8.5-2.6.3-5-.6-7.1-.8-1.8-2.1-3.4-3.8-4.5-3.8-2.5-9.2-2.5-13,0-1.7,1.1-3,2.7-3.8,4.5-.8,2.1-1,4.4-.6,7.1,0,.5.3,1.1.5,1.8h-1.4s0,0,0,0h-5.4c0,0-.2,0-.3,0h-1.2v-2.7c0-2.6-2.1-4.8-4.8-4.8s-4.8,2.1-4.8,4.8v38.9c0,0,0,.2,0,.3,0,2.6,2.1,4.8,4.8,4.8h36.2s0,0,0,0h10.8c2.6,0,4.8-2.1,4.8-4.8,0,0,0,0,0-.1ZM52,16.3c0-1.9,1.6-3.5,3.5-3.5s3.5,1.6,3.5,3.5v36c-.9-.9-2.1-1.6-3.5-1.6s-2.7.6-3.5,1.6v-21.8s0,0,0,0v-14.1ZM43.7,38.1l5.6,20.9h-4.1l-5.2-19.9c0-.2-.1-.3-.3-.4-.1,0-.3-.1-.5,0l-13.1,3.5c-.3,0-.5.4-.4.8l4.3,16.1h-4.1c-1.8-6.7-3.7-14-4.1-15.1,0-.3-.4-.5-.8-.4l-7.9,2.1v-4.3l6.7-1.8c.2,0,.3-.1.4-.3s0-.3,0-.5c-.7-2.6-2.6-9.5-5-18.5h4.3l4.7,17.5c0,.2.1.3.3.4.1,0,.2,0,.3,0s.1,0,.2,0l5.9-1.6c.1.3.3.5.4.8.1.2.3.3.5.3s.4-.1.6-.3c.2-.4.5-.9.7-1.4l4.6-1.2c.3,0,.5-.4.4-.8l-1.2-4.6c1-1.7,2-3.5,2.9-5.2l2.4,8.8c0,.3.3.5.6.5s.1,0,.2,0l7.7-2.1v4.3l-6.6,1.8c-.3,0-.5.4-.4.8ZM31.3,59l-4.2-15.8,11.9-3.2,5,19h-12.6ZM36.2,30.8l.8,2.8-2.8.8c.4-.8.9-1.6,1.4-2.5.2-.4.4-.7.6-1.1ZM50.8,20.2v9.7l-7.4,2-2.6-9.5c.4-.8.7-1.5,1-2.2h8.9ZM26.2,6.7c1.7-1.1,3.8-1.7,5.8-1.7s4.1.6,5.8,1.7c3.2,2.1,4.6,5.8,3.8,10.4-.5,2.9-4.3,9.5-7.1,14.3-.8,1.4-1.6,2.7-2.1,3.8,0,0,0,0,0,0-.1.2-.2.4-.3.6,0,0,0-.2-.1-.2,0,0,0,0,0,0-.6-1.1-1.4-2.5-2.3-4.1-2.8-4.8-6.6-11.4-7.1-14.3-.8-4.6.6-8.2,3.8-10.4ZM28.5,31.9c.7,1.3,1.4,2.5,2,3.5l-5,1.4-4.4-16.6h1.2c1.4,3.3,4.1,7.9,6.3,11.7ZM14.1,20.2c2.3,8.5,4.1,15.2,5,18.2l-5.8,1.6v-19.8h.9ZM5,16.3c0-1.9,1.6-3.5,3.5-3.5s3.5,1.6,3.5,3.5v24.4s0,0,0,0v5.5s0,0,0,0v6c-.9-1-2.1-1.6-3.5-1.6s-2.6.6-3.5,1.6V16.3ZM5,55.5c0-1.9,1.6-3.5,3.5-3.5s3.5,1.6,3.5,3.5h0c0,.4.3.6.6.6s.6-.3.6-.6v-8.6l7.6-2c.6,2.1,2.2,8.3,3.8,14.2H8.5c-1.9,0-3.5-1.6-3.5-3.5ZM55.5,59h-4.9l-5.5-20.7,5.7-1.5v18.7c0,.3.3.6.6.6s.3,0,.4-.2c.1-.1.2-.3.2-.4,0-1.9,1.6-3.5,3.5-3.5s3.4,1.5,3.5,3.4h0c0,.2,0,.2,0,.2,0,1.9-1.6,3.5-3.5,3.5Z"/>
								<path d="M32,20c2.8,0,5.2-2.3,5.2-5.2s-2.3-5.2-5.2-5.2-5.2,2.3-5.2,5.2,2.3,5.2,5.2,5.2ZM32,10.9c2.2,0,3.9,1.8,3.9,3.9s-1.8,3.9-3.9,3.9-3.9-1.8-3.9-3.9,1.8-3.9,3.9-3.9Z"/>
							</svg>
						</div>
						<h3>3.</h3>
						<div class="description">
							<p>Hent din ordre</p>
						</div>
					</a>
				</li>
				<li class="step four">
					<a href="/ugens-pose<?= $weeklybag_item ? "/".$weeklybag_item["sindex"] : "" ?>">
						<div class="image"> 
							<!-- https://icon666.com/icon/salad_fyu3eoe7c2af – modified -->
							<svg width="64" height="64" stroke-width="0" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
								<path d="M62,11h-2v9c0,2.8-2.2,5-5,5s-5-2.2-5-5v-9h-2v3.1c-9.4-8.4-23.7-8.1-32.7.7-.6-1.2-1.6-2.2-2.8-2.9,5.2-5.1,12.2-7.9,19.5-7.9,7.5,0,14.7,3,20,8.4v8.6h2v-10h-.6c-5.6-5.8-13.3-9-21.4-9-8.1,0-15.9,3.3-21.5,9.1-.5-.1-1-.2-1.4-.2-3.9,0-7,3.1-7,7v2c0,1.4.4,2.7,1.1,3.8-2.3,8-1.1,16.5,3.1,23.6l-.2,6.6c0,.8.3,1.6.8,2.1.8.8,2,1.1,3.1.7,1.1-.4,1.8-1.4,1.9-2.5,11.4,10.3,28.8,10.3,40.2,0,0,.7.3,1.3.8,1.8.6.6,1.3.9,2.1.9s1.6-.3,2.1-.9c.6-.6.9-1.3.8-2.1l-.2-6.6c4.3-7.1,5.4-15.6,3.1-23.6.8-1.1,1.2-2.5,1.1-3.9v-9ZM14.1,24.8c.6-.6,1-1.3,1.4-2.1.5-.4,1.1-.6,1.8-.7.3,0,.6-.2.8-.4.2-.3.2-.6.1-.8h0c-.1-.4-.2-.8-.2-1.2,0-1.9,1.6-3.5,3.5-3.5,1.5,0,2.8,1,3.3,2.4,0,.2.1.5.2.7,0,.3.2.5.4.7.2.2.5.2.8.2.2,0,.3,0,.5,0,1.4,0,2.7.8,3.2,2.1.5,1.3.3,2.8-.7,3.8-.2.2-.3.5-.3.8,0,.3.2.6.5.7,1,.5,1.6,1.5,1.8,2.6l-.5,2.3c-.3.5-.8.9-1.4,1.2-.3.2-.5.5-.5.9,0,.4.2.7.5.9.4.2.7.4,1,.7.6.6.9,1.5.9,2.4,0,1.4-.8,2.6-2.1,3.2-.4.2-.6.6-.6,1l.6,6.7v.7c0,0-.9.2-.9.2l-4.9-27.5-2,.4,4.9,27.5-1.2.2-2-7.2c-.1-.3-.4-.6-.7-.7-1.7-.2-3-1.7-3-3.5,0-.7.2-1.4.6-2,.2-.3.2-.7,0-1s-.6-.5-.9-.5h-.2c-1.4,0-2.7-.8-3.2-2.1-.5-1.3-.3-2.8.7-3.8.2-.2.3-.5.3-.8,0-.3-.2-.6-.5-.7-1.1-.6-1.8-1.8-1.8-3,0-.2,0-.5,0-.7ZM38.3,19c.1.2.4.4.6.5h.4c1.5.8,2.4,2.4,2,4.1-3.1,1.3-5.1,4.3-5,7.6-1.6,1.5-2.5,3.6-2.5,5.8s.4,1,1,1h1.5c0,.1,0,.3-.1.4-.5,1.3-1.8,2.1-3.2,2.1-.4,0-.7.2-.9.6.4-.8.7-1.7.7-2.6,0-1.2-.4-2.4-1.1-3.3l.5-2.1c.4-.8.7-1.7.7-2.6v-.3l2.1-9.3-2-.4-1.5,6.6c-.2-.2-.3-.4-.5-.5,1-1.6,1.1-3.5.4-5.2-.8-1.7-2.3-2.9-4.1-3.1.9-.4,1.9-.5,2.8-.2.3,0,.6,0,.9-.1.3-.2.4-.5.4-.8,0-1.3.9-2.5,2.1-3,1.2-.5,2.7-.3,3.7.6,1,.9,1.4,2.3,1,3.6,0,.2,0,.5,0,.7ZM45.4,48.9c-4.5,1.2-9.2,1.4-13.8.6l1-2.9c1.5.2,3,.3,4.5.3,3.7,0,7.4-.7,10.8-2.1.1.8,0,1.6-.4,2.3h0c-.4.8-1.2,1.5-2.2,1.7ZM30.9,51.4c2.1.4,4.1.6,6.2.6,1.6,0,3.1-.1,4.7-.3-5.2,2.6-11.3,3-16.8,1.2l5.2-.8c.3,0,.6-.3.7-.6ZM30.3,43.2c.8-.5,1.5-1.2,1.9-2l-1.7,5.1-.3-3.1ZM39.5,27.5l1.5,1.5c-.9.1-1.7.3-2.5.7.2-.8.5-1.6,1-2.2ZM40.9,31.1v3.7l-2.9-2.4c.8-.7,1.8-1.2,2.9-1.3ZM39.2,36h-3.2c.1-.7.4-1.4.7-2l2.4,2ZM38.2,39.2c.1-.4.3-.8.3-1.2h3.8c-2.1,2.1-4.6,3.8-7.2,5.1h0c-.6.2-1.1.5-1.6.7l.5-1.3c1.9-.3,3.5-1.5,4.3-3.3ZM44.4,43h0c-.7.6-1.3,1-1.8,1.4-1.8.4-3.6.6-5.4.6h-1.2.2c3.3-1.7,6.3-3.9,8.8-6.7.5.6.7,1.4.7,2.2,0,1-.5,1.9-1.2,2.5ZM45.3,36c-.2-.1-.4-.2-.6-.2-.1,0-.2,0-.3,0l2.4-2.4c.5.7.9,1.6,1,2.5h-2.5ZM43,34.6v-3.5c.8.1,1.7.5,2.4,1l-2.4,2.5ZM43.4,28.5l-2.4-2.4c.7-.5,1.6-.9,2.5-1v3.4ZM6.4,43.4c-1.6-3.6-2.4-7.5-2.4-11.4,0-2.2.3-4.3.8-6.4.7.5,1.4.9,2.2,1.1l-.6,16.7ZM9.7,54.7c-.4.3-1,.3-1.3,0-.2-.2-.3-.5-.3-.7l.2-6.8.7-20.1.9,27c0,.3,0,.5-.2.7ZM9,25c-2.8,0-5-2.2-5-5v-2c0-2.8,2.2-5,5-5s5,2.2,5,5v2c0,2.8-2.2,5-5,5ZM11,26.7c.3-.1.7-.3,1-.4.2,1.3.8,2.4,1.8,3.3-.5.9-.8,1.9-.8,2.9,0,2.6,1.8,4.8,4.2,5.4-.2.5-.3,1.1-.2,1.6,0,2.5,1.6,4.6,4,5.3l2,7.2c-5.3-2.4-9.4-6.7-11.5-12.1l-.5-13.1ZM52.1,51.4c-5.3,5.5-12.5,8.6-20.1,8.6-7.6,0-14.9-3.1-20.1-8.6l-.2-6.8c4.4,7.1,12.1,11.4,20.4,11.4s16-4.3,20.4-11.4l-.2,6.8ZM52.5,39.8c-.7,1.7-1.5,3.4-2.6,4.9,0-.3-.1-.6-.2-.9l-.2-.5c0-.3-.3-.5-.5-.6-.3-.1-.5-.1-.8,0-.5.2-.9.4-1.4.5.5-.8.8-1.7.8-2.7,0-.9-.2-1.8-.6-2.5h2c.6,0,1-.4,1-1,0-2.7-1.3-5.1-3.5-6.6l3.6-3.6c.2-.2.3-.4.3-.7s-.1-.5-.3-.7c-1.5-1.5-3.5-2.3-5.6-2.3h0c-.3,0-.6,0-.9,0,0-2.2-1.2-4.2-3.1-5.2.4-2.7-1.3-5.3-4-6-2.7-.7-5.4.6-6.5,3.2-.1.2-.2.5-.2.7-1.3-.2-2.5.1-3.6.8-1-1.6-2.7-2.5-4.6-2.5-2.7,0-5,2-5.4,4.7v-.7c0-.3,0-.7,0-1,4.2-4.5,10-7,16.1-7,6.1,0,11.8,2.5,16,6.9v3.1c0,3.1,2,5.8,5,6.7l-.4,13.1ZM45.4,28.5v-3.5c.9.1,1.7.5,2.5,1l-2.5,2.5ZM55.6,54.7c-.4.3-1,.3-1.3,0-.2-.2-.3-.4-.3-.7l.9-27,.7,20.2.3,6.8c0,.3,0,.5-.3.7ZM57.5,43.3l-.6-16.6c.8-.2,1.6-.6,2.2-1.1,1.4,5.9.8,12.2-1.6,17.8Z" />
								<rect x="56" y="11" width="2" height="10"/>
							</svg>
						</div>
						<h3>4.</h3>
						<div class="description">
							<p>Nyd ugens pose</p>
						</div>
					</a>
				</li>
			</ul>
		</div>
		<div class="whatisit">
			<h2>Hvad er KBHFF egentlig?</h2>
			<video controls>
				<source src="/assets/videos/interview.mp4">
				Your browser does not support HTML video.
			</video>
			<ul class="actions">
				<li class="readmore"><a href="/om" class="button primary">Læs mere</a></li>
			</ul>
		</div>
		
		<? if($post_items): ?>
		<div class="news">
			<h2 itemprop="headline">Udvalgte nyheder</h2>
			<ul class="items articles">
			<? foreach($post_items as $item): 
				$media = $IC->sliceMediae($item, "mediae"); ?>
				<li class="item article id:<?= $item["item_id"] ?>" itemscope itemtype="http://schema.org/NewsArticle" data-readstate="<?= $item["readstate"] ?>">

					<? if($media): ?>
					<div class="image item_id:<?= $media["item_id"] ?> format:<?= $media["format"] ?> variant:<?= $media["variant"] ?>"></div>
					<? endif; ?>


					<?= $HTML->articleTags($item, [
						"context" => ["post"],
						"url" => "/nyheder/tag"
					]) ?>


					<h3 itemprop="headline"><a href="/nyheder/<?= $item["sindex"] ?>"><?= preg_replace("/<br>|<br \/>/", "", $item["name"]) ?></a></h3>


					<?= $HTML->articleInfo($item, "/nyheder/".$item["sindex"], [
						"media" => $media, 
						"sharing" => true
					]) ?>


					<? if($item["description"]): ?>
					<div class="description" itemprop="description">
						<p><?= nl2br($item["description"]) ?></p>
					</div>
					<? endif; ?>

				</li>
			<? endforeach; ?>
			</ul>

			<ul class="actions">
				<li class="news"><a href="/nyheder" class="button primary">Se alle nyheder</a></li>
			</ul>
		</div>
		<? endif ?>

		<!--div class="c-box newsletter i:newsletter">
			<h3>Tilmeld Nyhedsbrev</h3>
	
			<form action="//kbhff.us15.list-manage.com/subscribe/post?u=d2a926649ebcf316af87a05bb&amp;id=141ae6f59f" method="post" target="_blank">
				<input type="hidden" name="b_d2a926649ebcf316af87a05bb_141ae6f59f" value="">
				<div class="field email required">
					<label for="input_email">E-mail</label>
					<input type="email" value="" name="EMAIL" id="input_email" />
				</div>

				<ul class="actions">
					<li class="submit"><input type="submit" value="Tilmeld" name="subscribe" class="button" /></li>
				</ul>
			</form>

		</div-->

		<div class="hero">
			<img src="https://kbhff.dk/images/273/single_media/380x.png" alt="KBHFF veggie bag" />
		</div>

	</div>



</div>
