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
	
	<!-- If you want to use or contribute to this code, visit http://parentnode.dk -->

	
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
	<link type="text/css" rel="stylesheet" media="all" href="/css/seg_<?= $this->segment() ?>.css?rev=20240328-105151" />
	<script type="text/javascript" src="/js/seg_<?= $this->segment() ?>.js?rev=20240328-105151"></script>
<? } ?>

	<?= $this->headerIncludes() ?>
</head>

<body<?= $HTML->attribute("class", $this->bodyClass()) ?>>

<div id="page" class="i:page">

	<div id="header">
		<a class="logo" href="/">Københavns <span class="highlight">Fødevarefællesskab</span></a>
		<ul class="servicenavigation">
			<li class="keynav navigation nofollow"><a href="#navigation">To navigation</a></li>
			<?		if(session()->value("user_id") && session()->value("user_group_id") > 1): ?>
			<li class="keynav wiki nofollow"><a href="https://wiki.kbhff.dk" target="_blank">Wiki</a></li>
			<?= $HTML->link("Janitor", "/janitor", ["wrapper" => "li.keynav.front"]) ?>
			<li class="keynav user nofollow"><a href="/login/logoff">Logoff</a></li>
<?		else: ?>
			<li class="keynav user login nofollow"><a href="<?= SITE_LOGIN_URL ?>">Login</a></li>
<?		endif; ?>
		</ul>
	</div>

	<div id="content">
