<!DOCTYPE html>
<html lang="<?= $this->language() ?>">
<head>
	<!--
	Foodnet platform
    Copyright (C) 2018  Københavns Fødevarefællesskab and think.dk

    Københavns Fødevarefællesskab
    KPH-Projects
    Enghavevej 80 C, 3. sal
    2450 København SV
    Denmark
    mail: bestyrelse@kbhff.dk

    think.dk
    Æbeløgade 13, 402
    2100 København Ø
    Denmark
    mail: start@think.dk

    This source code is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This source code is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
	along with this source code.  If not, see <http://www.gnu.org/licenses/>. -->

	<!-- If you want to use or contribute to this code, visit https://parentnode.dk -->


	<title><?= $this->pageTitle() ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="keywords" content="Økologi grøntsager lokal sæson mad fødevarer fællesskab" />
	<meta name="description" content="<?= $this->pageDescription() ?>" />
	<meta name="viewport" content="initial-scale=1" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />

	<?= $this->sharingMetaData() ?>

	<link rel="apple-touch-icon" href="/touchicon.png">
	<link rel="icon" href="/favicon.png">

<? if(session()->value("dev")) { ?>
	<link type="text/css" rel="stylesheet" media="all" href="/css/lib/seg_<?= $this->segment() ?>_include.css?cb=<?=randomKey(4); ?>" />
	<script type="text/javascript" src="/js/lib/seg_<?= $this->segment() ?>_include.js?cb=<?=randomKey(4); ?>"></script>
<? } else { ?>
	<link type="text/css" rel="stylesheet" media="all" href="/css/seg_<?= $this->segment() ?>.css?rev=20210616-155136" />
	<script type="text/javascript" src="/js/seg_<?= $this->segment() ?>.js?rev=20210616-155136"></script>
<? } ?>

</head>

<body>

<div id="page">
	<div id="content">
		<div class="downtime">
			<h1>Opdatering i gang</h1>
			<h2>Vi er i gang med en stor opdatering af kbhff.dk.</h2>
			<p>Vi glæder os til at vise dig vores nye medlemsystem – vi forventer at sitet er klar til nye besøg igen ca. kl. 19.</p>
		</div>
	</div>
</div>

</body>
</html>