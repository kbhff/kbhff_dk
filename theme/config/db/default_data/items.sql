# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.5.5-10.1.44-MariaDB-0ubuntu0.18.04.1)
# Database: kbhff_dk
# Generation Time: 2020-10-20 21:51:05 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table item_membership
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `SITE_DB`.`item_membership` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `classname` varchar(100) NOT NULL DEFAULT '',
  `subscribed_message_id` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `introduction` text NOT NULL,
  `html` text NOT NULL,
  `fixed_url_identifier` varchar(100) DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `fixed_url_identifier` (`fixed_url_identifier`),
  KEY `item_id` (`item_id`),
  KEY `item_membership_ibfk_2` (`subscribed_message_id`),
  CONSTRAINT `item_membership_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `item_membership_ibfk_2` FOREIGN KEY (`subscribed_message_id`) REFERENCES `items` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `SITE_DB`.`item_membership` WRITE;
/*!40000 ALTER TABLE `SITE_DB`.`item_membership` DISABLE KEYS */;

INSERT INTO `SITE_DB`.`item_membership` (`id`, `item_id`, `name`, `classname`, `subscribed_message_id`, `description`, `introduction`, `html`, `fixed_url_identifier`, `position`)
VALUES
	(2,7,'Støttemedlem','supporter',3,'','<p>Støttemedlem introduction TEST – this text can be edited via Janitor under members&gt;memberships&gt;støttemedlem (overview)</p>','<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>','stoettemedlem',0),
	(1,8,'Frivillig','volunteer',4,'','<p>Frivillig medlem introduction – this text can be edited via Janitor</p>','<p>Frivillig medlem introduction TEST – this text can be edited via Janitor</p>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>','frivillig',0);

/*!40000 ALTER TABLE `SITE_DB`.`item_membership` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table item_message
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `SITE_DB`.`item_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `html` text NOT NULL,
  `layout` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  CONSTRAINT `item_message_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `SITE_DB`.`item_message` WRITE;
/*!40000 ALTER TABLE `SITE_DB`.`item_message` DISABLE KEYS */;

INSERT INTO `SITE_DB`.`item_message` (`id`, `item_id`, `name`, `description`, `html`, `layout`)
VALUES
	(1,3,'Velkommen som støttemedlem','','<p>Hej {NICKNAME}</p>\n<p>Velkommen som støttemedlem.</p>\n<p>This text can be edited via Janitor</p>','template-b.html'),
	(2,4,'Velkommen som Frivillig medlem','','<p>Hej {NICKNAME}</p>\n<p>Velkommen som frivillig medlem.</p>\n<p>This text can be edited via Janitor</p>','template-b.html');

/*!40000 ALTER TABLE `SITE_DB`.`item_message` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table item_page
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `SITE_DB`.`item_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `subheader` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `html` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  CONSTRAINT `item_page_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `SITE_DB`.`item_page` WRITE;
/*!40000 ALTER TABLE `SITE_DB`.`item_page` DISABLE KEYS */;

INSERT INTO `SITE_DB`.`item_page` (`id`, `item_id`, `name`, `subheader`, `description`, `html`)
VALUES
	(1,2,'Persondata','','Her findes informationer om Københavns Fødevarefællesskabs opbevaring af persondata og personoplysninger.','<h2>Retningslinjer for behandling og opbevaring af persondata</h2>\r\n<p>Dataansvarlig: Københavns Fødevarefællesskab, Enghavevej 80 C 3. sal, 2450 KBH SV</p>\r\n<p>Dataopbevaring: Vi behandler dine data i nødvendigt omfang, så længe du er medlem.</p>\r\n<p>Datamodtagere: Udover i det omfang det er nødvendigt i forbindelse online betalingstransaktioner, videregiver vi ikke dine oplysninger til andre dataansvarlige uden dit samtykke.</p>\r\n<p>Dine rettigheder og oplysningspligt: Du har til enhver tid retten til at anmode om indsigt i, berigtigelse af, sletning af, begrænsning af behandlingen af dine personoplysninger samt retten til dataportabilitet (eksport af de automatisk behandlede personoplysninger, du har givet til os). Tilbagetrækning af samtykke til databehandling sker ved, at du melder dig ud af foreningen via den tilsvarende funktion i medlemssystemet.<br />Du har til enhver tid retten til at klage i overensstemmelse med Datatilsynet. Klager modtages på info@kbhff.dk. (https://www.datatilsynet.dk/borger/klage-til-datatilsynet/)</p>\r\n<h3>Hvad bruger vi dine personoplysninger til</h3>\r\n<ol><li>De oplyste personoplysninger i forbindelse med indmeldelse opbevares af Københavns Fødevarefællesskab og gemmes op til et år efter endt medlemskab. Det drejer sig om navn, telefonnummer og emailadresse.</li>\r\n	<li>Det er en betingelse for medlemskab af Københavns Fødevarefællesskab, at Københavns Fødevarefællesskab kan opbevare og behandle disse oplysninger.</li>\r\n	<li>Oplysninger om navn og kontaktoplysninger anvendes af Københavns Fødevarefællesskab i forbindelse med udførelse af foreningens arbejde. Dette indbefatter behandling af ordrer når du bestiller, udsendelse af medlemsbreve samt den nødvendige kommunikation forbundet med udførelsen af foreningens arbejde, i det omfang du indgår i dette.</li>\r\n	<li>Ved gennemførelse af køb registreres betalingsmåde, produkter, transaktionsbeløb og dato af køb og hvilken afdeling købet fandt sted i.</li>\r\n	<li>Oplysninger om betalingstransaktioner opbevares i 5 år. Oplysninger i øvrigt opbevares i op til 1 år efter ophør af medlemskab. Denne grænse dækker dog ikke oplysninger som er kritiske for foreningen så som emails, bidrag i fora, wiki og referater. Du er selv ansvarlig for at administrere dine personrelaterede oplysninger (herunder fjerne dem, hvis du ønsker dette) fra sider der bruges til kommunikation mellem medlemmer, men som ikke direkte administreres af KBHFF (f.eks. wiki).</li>\r\n	<li>Københavns Fødevarefællesskab videregiver oplysninger til en betalingsservice på gennemførelse af betalingstransaktioner, hvis du betaler med betalingskort.</li>\r\n	<li>Foruden det i punkt 6 anførte udveksler Københavns Fødevarefællesskab ikke oplysninger med nogen tredjepart.</li>\r\n</ol>'),
	(2,9,'Allerede medlem','','','<p>Editable in janitor (page:already-member).</p>\n<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>'),
	(3,10,'Tilmelding','','','<p>Editable in janitor (page:signup).</p>\n<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>'),
	(4,11,'Vælg medlemskab','','','<p>Editable in janitor (content&gt;pages&gt;\"Vaelg medlemskab\", tagged as \"page:persondata\" (was \"page:signupfees\")). Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>\r\n<p></p>'),
	(5,12,'Afdelinger i KBHFF','','Find lokalafdelinger i Københavns Fødevarefællesskab. Her kan du se hvor du kan hente dine lokale, økologiske grøntsager. Du kan finde åbningstider, adresser, og kontaktinformation på hver afdeling.','<p>&nbsp;&nbsp; <br /></p>'),
	(6,18,'For Leverandører','','Her findes information for Københavns Fødevarefællesskabs økologiske grøntsagsleverandører: hvor vores fælleslager er, hvor fakturaer skal sendes hen samt vores handelsbetingelser.','<h3>Handelsbetingelser</h3>\n<p>Læs <a href=\"/om/handelsbetingelser\">Københavns Fødevarefællesskabs handelsbetingelser</a>.</p>\n<p>Har du spørgsmål til handelsbetingelserne, er du velkommen til at kontakte KBHFF’s bestyrelse på <a href=\"mailto:bestyrelse@kbhff.dk\">bestyrelse@kbhff.dk</a>.</p>\n<h3>Fakturaer</h3>\n<p>Fakturaer sendes elektronisk til: <a href=\"mailto:okonomi@kbhff.dk\">okonomi@kbhff.dk</a></p>\n<p>Faktureringsoplysninger:<br />Københavns Fødevarefællesskab f.m.b.a.<br />c/o Karens Hus<br />Bispebjerg Bakke 8<br />2400 København N</p>\n<p>CVR:  32 99 66 71<br />Bank: 8401 1136136</p>\n<h3>Leveringsadresse</h3>\n<p>Københavns Fødevarefællesskab f.m.b.a.<br />c/o Københavns Professionshøjskole<br />Sigurdsgade 36<br />2200 København N</p>\n<p>OBS! Levering skal ske til Sigurds Bar – indkørsel er gennem den store port vest for hovedindgangen til Professionshøjskolen.</p>'),
	(8,20,'Handelsbetingelser','','Her findes vigtige salgsbetingelser og handelsbetingelser for leverandører af økologisk frugt og grøntsager til Københavns Fødevarefællesskab samt for medlemmerne.','<h2>Københavns Fødevarefællesskabs Handels-, Salgs- og Leveringsbetingelser</h2>\n<h3>Hovedkontor</h3>\n<p>Københavns Fødevarefællesskab f.m.b.a.<br />c/o Karens Hus<br />Bispebjerg Bakke 8<br />2400 København N</p>\n<p>CVR-nummer: 32996671<br />Etableringsår: 2008</p>\n<p>e-mail: <a href=\"mailto:info@kbhff.dk\">info@kbhff.dk</a><br />web: <a href=\"/\">www.kbhff.dk</a></p>\n<h3>Butikker / Besøgsadresser</h3>\n<p>Se siden for <a href=\"/afdelinger\">lokalafdelinger</a>.</p>\n<h3>For leverandører</h3>\n<p>Københavns Fødevarefællesskab (KBHFF) ønsker at være en del af en bæredygtig fremtid ved at gøre bæredygtige og økologiske fødevarer tilgængelige for alle. KBHFF støtter fair og direkte handel og ønsker at skabe et økonomisk bæredygtigt, selvstændigt og transparent alternativ til kommercielle fødevarevirksomheder.</p>\n<h4>1. Generelt</h4>\n<p>Medmindre andet er aftalt, er disse handelsbetingelser gældende for alle leverancer til KBHFF, hvad enten ordre er afgivet mundtligt eller skriftligt.<br /><strong>1.1.</strong> Sælgers evt. salgsvilkår finder alene anvendelse i det omfang, de er vedtaget ved skriftlig aftale mellem KBHFF og sælger.</p>\n<h4>2. Betalingsvilkår</h4>\n<p>Forfaldsdato er løbende uge + 14 dage fra fakturadato.</p>\n<h4>3. Levering</h4>\n<p>Levering skal finde sted på de af KBHFF angivne&nbsp; leveringsadresser. Risikoens overgang sker på leveringsadressen, når varerne er aflæsset.<br /><strong>3.1.</strong> En angiven leveringstid må overholdes. Levering foregår hver onsdag inden kl. 11.00. Ved overskridelse af leveringstid er KBHFF berettiget til annullering af ordren.</p>\n<h4>4. Reklamationer</h4>\n<p>Alle varer modtages med forbehold af godkendelse, uanset om betaling har fundet sted.<br />Leverandøren underrettes straks og ellers uden ugrundet ophold om mangler i forhold til kvalitet eller leverance.</p>\n<h4>5. Mangelfulde varer</h4>\n<p>Er en vare mangelfuld i leverance eller kvalitet og har KBHFF ved modtagelse straks eller uden ugrundet ophold givet meddelelse til leverandøren herom, er KBHFF berettiget til omlevering inden for leveringstid eller hel eller delvis ophævelse af købet.</p>\n<h4>6. Følgeseddel</h4>\n<p>Enhver leverance skal ledsages af en følgeseddel, hvorpå er anført leveringssted samt nøjagtig angivelse af forsendelsens indhold. Det skal fremgå af følgesedlen, om varen er økologisk. Emballagen, varen er leveret i, skal være udspecificeret på følgesedlen med antal og art.</p>\n<h4>7. Faktura</h4>\n<p>Med mindre andet er aftalt sendes en samlet faktura elektronisk til KBHFF\'s økonomigruppe: <a href=\"mailto:okonomi@kbhff.dk\">okonomi@kbhff.dk</a><br /><strong>7.1.</strong> Emballagen, varen er leveret i, skal være udspecificeret på fakturaen med antal, art og pris.<br />Evt. transportudgift skal ligeledes selvstændigt opgives.</p>\n<h4>8. Kreditnota på emballage</h4>\n<p>Emballage der returneres fra KBHFF til leverandøren, forsynes med en følgeseddel med angivelse af antal, art og modtager. Disse emballager faktureres de pågældende leverandører.</p>\n<h4>9. Ændring af ordre</h4>\n<p>Enhver ændring eller annullering af en ordre må afgives skriftligt for at være bindende. En ændring eller annullering af en ordre skal være KBHFF i hænde 48 timer inden rette leveringstid.</p>\n<h4>10. Lovvalg og værneting</h4>\n<p>Enhver tvist mellem en samarbejdspartner og KBHFF skal løses i overensstemmelse med dansk ret ved den kompetente danske domstol.</p>\n<p><em>januar 2020</em></p>\n<p>e-mail: <a href=\"mailto:info@kbhff.dk\">info@kbhff.dk</a></p>\n<h3>For medlemmer</h3>\n<p>Aftaler med Københavns Fødevarefællesskab indgås på dansk.</p>\n<p>Disse handelsbetingelser er gældende for al handel på <a href=\"/\">www.kbhff.dk</a>.<br />I det følgende omtales Københavns Fødevarefællesskab under forkortelsen KBHFF.</p>\n<h4>Betaling</h4>\n<p>KBHFF’s standardvare er en pose med blandet lokalt dyrket økologisk grønt i sæsonbaseret udvalg, typisk 6-8 kg i alt. Indholdet varierer fra uge til uge alt afhængig af sæson og udbud. Posen afhentes i dit lokale afhentningssted i den angivne åbningstid. Derudover sælger KBHFF poser med frugt, asparges og kartofler, afhængig af sæson.</p>\n<p>KBHFF’s frugt- og grøntsagsposer er forudbetalte. Du skal betale før du kan hente din pose. Når du tilmelder dig, får du tildelt en personlig konto på KBHFF’s hjemmeside <a href=\"/\">www.kbhff.dk</a>, hvor du kan bestille varer. For bestilling af varer til afhentning i butikken den næstkommende onsdag skal din bestilling typisk senest være gennemført den forudgående onsdag kl 23:59. Du skal betale forud med betalingskort over internettet eller med kontanter eller mobilepay i din lokale afdeling. Kvitteringer for hver bestilling på din KBHFF-konto sendes til din email.</p>\n<p>Kontingent bliver trukket ved den første bestilling i maj måned. Indmeldelsesgebyr for nye medlemmer betales ved indmeldelse. </p>\n<p>Betaling sker i danske kroner.</p>\n<h4>Priser</h4>\n<p>Alle priser på <a href=\"/\">www.kbhff.dk</a> er inklusiv moms og alle afgifter.</p>\n<h4>Gebyrer</h4>\n<p>KBHFF har følgende kort-gebyrer på handel på <a href=\"/\">www.kbhff.dk</a>:</p>\n<ul><li>Dankort og Visa/Dankort: Gratis</li>\n	<li>Andre kort: kan ikke bruges til betaling på hjemmesiden.</li>\n</ul><h4>Sikkerhed</h4>\n<p>Når du handler på <a href=\"/\">www.kbhff.dk</a>, skal du benytte et betalingskort. For at beskytte dine kortoplysninger vil du ved indtastningen af kortoplysninger blive ført over til en betalingsside leveret af OnPay via DanDomain A/S – der er certificeret til at håndtere betalingsdata, og DanDomain A/S sørger for, at alle oplysninger behandles krypteret, så hverken KBHFF eller andre kan få adgang til dine kortoplysninger.</p>\n<h4>Afhentning af bestilte varer</h4>\n<p>Det er dit eget ansvar at huske at hente de fødevarer, som du har bestilt i din lokale KBHFF-butik indenfor denne butikkens åbningstid. Hvis du ikke afhenter dine varer, går de tabt og der gives ikke kredit til medlemmer, der glemmer at afhente deres pose.&nbsp;</p>\n<h4>Reklamation</h4>\n<p>Du har lov til at klage over fejl og/eller mangler på en vare i op til to år efter, du har købt den. Det gælder selvfølgelig kun, hvis det ikke er dig, der har behandlet varen forkert.<br />Du skal returnere varen (gælder ikke ferske varer) til os i en af vores butikker og oplyse dit kundenummer, og hvad du mener, der er galt med varen. </p>');

/*!40000 ALTER TABLE `SITE_DB`.`item_page` ENABLE KEYS */;

UNLOCK TABLES;



# Dump of table item_signupfee
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `SITE_DB`.`item_signupfee` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `classname` varchar(100) NOT NULL DEFAULT '',
  `associated_membership_id` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `html` text NOT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  `fixed_url_identifier` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  KEY `item_signupfee_ibfk_2` (`associated_membership_id`),
  CONSTRAINT `item_signupfee_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `item_signupfee_ibfk_2` FOREIGN KEY (`associated_membership_id`) REFERENCES `items` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `SITE_DB`.`item_signupfee` WRITE;
/*!40000 ALTER TABLE `SITE_DB`.`item_signupfee` DISABLE KEYS */;

INSERT INTO `SITE_DB`.`item_signupfee` (`id`, `item_id`, `name`, `classname`, `associated_membership_id`, `description`, `html`, `position`, `fixed_url_identifier`)
VALUES
	(1,5,'Støttemedlem','supporter',7,'','<p>Alle medlemmer betaler et indmeldelsesgebyr for at blive oprettet, som går til driften af KBHFF.</p>\r\n<h3 class=\"price\">Indmeldelsesgebyr: <span class=\"price\">100,00 DKK</span></h3>\r\n<p>Derefter kræves et årligt kontingent af til tiden 200kr. hver år i maj måned. Når du melder dig ind i KBHFF støtter du en forening, der støtter små økologiske og biodynamiske avlere.</p>\r\n<h3 class=\"price\">Årligt kontingent:<span class=\"price\"> 200,00 DKK</span></h3>\r\n<p>Som støttemedlem betaler du en lidt højere pris for en grøntsagspose, men du behøver ikke lægge 3 timers frivilligt arbejde hver måned.</p>\r\n<h3 class=\"price\">Pris for ugens pose: <span class=\"price\">140,00 DKK<br /></span></h3>',2,NULL),
	(2,6,'Frivillig','volunteer',8,'','<p>Alle medlemmer betaler et indmeldelsesgebyr for at blive oprettet, som går til driften af KBHFF.</p>\r\n<h3 class=\"price\">Indmeldelsesgebyr: <span class=\"price\">100,00 DKK</span></h3>\r\n<p>Derefter kræves et årligt kontingent af til tiden 200kr. hver år i maj måned. Når du melder dig ind i KBHFF støtter du en forening, der støtter små økologiske og biodynamiske avlere.</p>\r\n<h3 class=\"price\">Årligt kontingent:<span class=\"price\"> 200,00 DKK</span></h3>\r\n<p>Som frivillig forpligter du dig til at lægge 3 timers frivilligt arbejde i foreningen hver måned – til gengæld du ugens pose lidt billigere.</p>\r\n<h3 class=\"price\">Pris for ugens pose: <span class=\"price\">115,00 DKK</span></h3>',1,NULL);

/*!40000 ALTER TABLE `SITE_DB`.`item_signupfee` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table item_weeklybag
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `SITE_DB`.`item_weeklybag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `week` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `html` text NOT NULL,
  `full_description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  CONSTRAINT `item_weeklybag_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `SITE_DB`.`item_weeklybag` WRITE;
/*!40000 ALTER TABLE `SITE_DB`.`item_weeklybag` DISABLE KEYS */;

INSERT INTO `SITE_DB`.`item_weeklybag` (`id`, `item_id`, `name`, `week`, `year`, `html`, `full_description`)
VALUES
	(7,118,'Uge 2',2,2020,'<p>Godt nytår til jer alle sammen! Årets første pose er fyldt med økologiske, biodynamiske og grønne smukstykker… vi håber i nydder det!</p>\r\n<h4>Biodynamiske varer<br /></h4>\r\n<ul><li>1 kg Kartofler, Alliance, Birkemosegaard (Øko/bio ), løssalgspris: 14 kr / kg</li>\r\n	<li>1 stk Grønkål (pose á 400g), Birkemosegaard (Øko/bio), løssalgspris: 26kr/stk</li>\r\n	<li>0,9 kg Porrer, Birkemosegaard (Øko/bio), løssalgspris: 31 kr / kg</li>\r\n</ul><h4>Økologiske varer<br /></h4>\r\n<ul><li>1 bdt Timian , Grønholtgaard (Øko), løssalgspris: 17 kr / bdt</li>\r\n	<li>0,3 kg Løg, gule , Grønholtgaard (Øko), løssalgspris: 21 kr / kg</li>\r\n	<li>0,5 kg Champignon (brune), Tvedemose (Øko), løssalgspris: 71 kr/ kg</li>\r\n</ul><h4>Ugens frugpose</h4>\r\n<ul><li>2 kg Æbler, Dr. Louise, Bellingehus (Øko/bio), løssalgspris: 31 kr / kg</li>\r\n</ul><p>Næste ugens frugtpose: biodynamiske æbler fra Bellingehus</p>','<h3>Løssalg</h3>\r\n<ul><li>Løg, Birkemosegaard (Øko), løssalgspris: 21 kr / kg</li>\r\n	<li>Knoldselleri, Grønholtgaard (Øko), løssalgspris: 21 kr / Stk</li>\r\n	<li>Æbler, Topaz, Poul og Lise Lone Nørby (Øko), løssalgspris: 31 kr / kg</li>\r\n	<li>Hvidløg, (blandede), Hvidløg og Vin (Øko), løssalgspris: 15 kr / 100 g</li>\r\n</ul><h3>Extra poser<br /></h3>\r\n<h4>Grøntposen</h4>\r\n<ul><li>Amager 26 / 2<br /></li>\r\n	<li>Sydhavnen 7 / 3<br /></li>\r\n	<li>Valby / 0<br /></li>\r\n	<li>Vesterbro 14 / 2<br /></li>\r\n	<li>Frederiksberg 10 / 3<br /></li>\r\n	<li>Vanløse 11 / 1<br /></li>\r\n	<li>Østerbro 10 / 2<br /></li>\r\n	<li>Nørrebro 22 / 2<br /></li>\r\n	<li>KP Nørrebro / 0<br /></li>\r\n	<li>Nordvest 19 / 3<br /></li>\r\n	<li>I alt 119 / 18</li>\r\n</ul><h4>Frugtposen</h4>\r\n<ul><li>Amager 6 / 0<br /></li>\r\n	<li>Sydhavnen 4 / 0<br /></li>\r\n	<li>Valby / 0<br /></li>\r\n	<li>Vesterbro 2 / 0<br /></li>\r\n	<li>Frederiksberg 1 / 2<br /></li>\r\n	<li>Vanløse 7 / 0<br /></li>\r\n	<li>Østerbro 2 / 1<br /></li>\r\n	<li>Nørrebro 6 / 0<br /></li>\r\n	<li>KP Nørrebro / 0<br /></li>\r\n	<li>Nordvest 2 / 1<br /></li>\r\n	<li>I alt 30 / 4</li>\r\n</ul>'),
	(10,121,'Uge 3',3,2020,'<h4>Biodynamiske varer:</h4>\r\n<ul><li>1 kg Kartofler, røde, Birkemosegaard (Øko/bio ), løssalgspris: 14 kr / kg</li>\r\n	<li>0,6 kg Rødbede, rond , Birkemosegaard (Øko/bio), løssalgspris: 17 kr / kg</li>\r\n</ul><h4>Økologiske varer:</h4>\r\n<ul><li>1 kg Gulerødder, Svanholm (Øko), løssalgspris: 21 kr / kg</li>\r\n	<li>1 stk Blomstrende rosenkål, Svanholm (Øko), løssalgspris: 24 kr / stk</li>\r\n	<li>2 stk Hvidløg, Stensbølgaard (Øko), løssalgspris: 9 kr / stk</li>\r\n	<li>0,3 kg Østershatte, Beyond Coffee (Øko), løssalgspris: 135 kr / kg</li>\r\n</ul><h4>Ugens frugtpose:</h4>\r\n<p>2 kg Æbler, Katrina, Bellingehus (Øko/bio), løssalgspris: 31 kr / kg</p>','<h3>Løssalg</h3>\r\n<ul><li>Pastinak , Svanholm (Øko), løssalgspris: 34 kr / kg</li>\r\n	<li>Grønkål, Svanholm (Øko), løssalgspris: 51 kr / kg</li>\r\n	<li>Jordskokker , Birkemosegaard (Øko/bio), løssalgspris: 31 kr / kg</li>\r\n	<li>Ræddike, Stensbølgaard (Øko), løssalgspris: 9 kr / stk</li>\r\n	<li>Æbler, Katrina, Bellingehus (Øko/bio), løssalgspris: 31 kr / kg</li>\r\n</ul>'),
	(11,122,'Uge 4',4,2020,'<h4>Biodynamiske varer:<br /></h4>\r\n<ul><li>1 kg Kartofler, Estima (til mos), Birkemosegaard, løssalgspris: 14 kr / kg</li>\r\n	<li>0,3 kg Sorte ræddike, Birkemosegaard, løssalgspris: 31 kr / kg</li>\r\n</ul><h4>Økologiske varer:</h4>\r\n<ul><li>1 stk Knoldselleri, Svanholm, løssalgspris: 24 kr / stk</li>\r\n	<li>1 stk Rødkål, Svanholm, løssalgspris: 21 kr / stk</li>\r\n	<li>0,7 kg Jordskokker, Nygaardens Økogrønt, løssalgspris: 26 kr / kg</li>\r\n	<li>0,5 kg Hvide champignon, Tvedemose, løssalgspris: 68 kr / kg</li>\r\n	<li>50 g Hvidløg, Therador (mild), Hvidløg &amp; Vin, løssalgspris: 15 kr / 100 g</li>\r\n</ul><h4>Frugtpose - biodynamiske varer</h4>\r\n<p>2 kg Æbler, Dronning Louise, Bellingehus, løssalgspris: 31 kr / kg</p>\r\n<h4>Kartoffelpose - biodynamiske varer</h4>\r\n<p>1,5 kg Kartofler, røde (bage), Birkemosegaard, løssalgspris: 14 kr / kg</p>','<p>I den uge får vi to spændende sorter hvidløg fra Hvidløg og Vin – en i posen og en til løssalg – måske vil du prøve at smage forskellen?! – så skynd dig og få begge med :)<br /></p>\r\n<p>Her kommer HVIDLØG &amp; VIN‘s beskrivelse af sorterne:</p>\r\n<h4>Therador hvidløg (i posen) – PRODUKTIV OG ROBUST, MILD, FYLDIG SMAG</h4>\r\n<p>Nyere sort der stammer fra Sydfrankrig. Hvidt løg med violette striber og hvid-beige fed. Mellemstore til store hvidløg med pæne fed og mild krydret smag.</p>\r\n<p>Smagsbeskrivelse: RÅ:  Umodne nødder, jordskokker, syrlig, anelse frugt/sødme. Lidt brændende til sidst. RISTET: meget mild, cremet, sød. Bitre v. overristning. (fra Anne Hjernøe og Lisbeth Ankersen)</p>\r\n<p>Messidrôme hvidløg (i løssalg) – HVID SKALFARVE, STORE FED, STÆRK SMAG, KLASSISK HVIDLØG</p>\r\n<p>Sydfransk sort der giver velformede hvidløg med store fed. Helt hvidt løg med hvid-beige fed. God afrundet men stærk smag.</p>\r\n<h3>Løssalg</h3>\r\n<h4>Økologiske varer:</h4>\r\n<ul><li>Gulerødder, farvede, Svanholm, løssalgspris: 21 kr / kg</li>\r\n	<li>Rosmarin, Svanholm, løssalgspris: 24 kr / bdt<br /></li>\r\n	<li>Citrontimian, Svanholm, løssalgspris: 24 kr / bdt</li>\r\n	<li>Løg, røde, Svanholm, løssalgspris: 24 kr / kg</li>\r\n	<li>Hvidløg, Messidrôme (stærkt), Hvidløg &amp; Vin, løssalgspris: 15 kr / 100 g</li>\r\n</ul><h4>Biodynamiske varer:</h4>\r\n<p>Æbler, Bellingehus, løssalgspris: 31 kr / kg</p>'),
	(12,123,'Uge 5',5,2020,'<h4>Biodynamiske varer:</h4>\r\n<ul><li>1 kg Kartofler, Elfe, Birkemosegaard, løssalgspris: 14 kr / kg</li>\r\n	<li>0,5 kg Porrer, Birkemosegaard, løssalgspris: 34 kr / kg</li>\r\n</ul><h4>Økologiske varer:</h4>\r\n<ul><li>0,35 kg Pastinak, Birkemosegaard/Rønnely, løssalgspris: 27 kr / kg</li>\r\n	<li>2 stok Rosenkål, Grønholtgaard, løssalgspris: 17 kr / stok</li>\r\n	<li>2 stok Rosenkål, Grønholtgaard, løssalgspris: 17 kr / stok</li>\r\n	<li>0,25 kg Østershatte, Beyond Coffee, løssalgspris: 135 kr / kg</li>\r\n</ul><h4>Frugtpose - biodynamiske varer</h4>\r\n<p>2 kg Æbler, Bramley og Pederstrup (Mad), Bellingehus, løssalgspris: 31 kr / kg</p>\r\n<h4>Kartofelpose</h4>\r\n<p>1,5 kg Kartofler, Estima (Mos), Birkemosegaard, løssalgspris: 14 kr / kg</p>','<h3>Løssalg</h3>\r\n<h4>Biodynamiske varer:</h4>\r\n<ul><li>Rødebeder, runde, Birkemosegaard, løssalgspris: 17 kr / kg</li>\r\n	<li>Æbler, Bramley og Pederstrup (Mad), Bellingehus, løssalgspris: 31 kr / kg</li>\r\n</ul><h4>Økologiske varer:</h4>\r\n<p>Knoldselleri, Grønholtgaard (Øko), løssalgspris: 21 kr / kg</p>');

/*!40000 ALTER TABLE `SITE_DB`.`item_weeklybag` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table items
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `SITE_DB`.`items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sindex` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL,
  `itemtype` varchar(40) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_at` timestamp NULL DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `sindex` (`sindex`),
  CONSTRAINT `items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


LOCK TABLES `SITE_DB`.`items` WRITE;
/*!40000 ALTER TABLE `SITE_DB`.`items` DISABLE KEYS */;

INSERT INTO `SITE_DB`.`items` (`id`, `sindex`, `status`, `itemtype`, `user_id`, `created_at`, `modified_at`, `published_at`)
VALUES
	(2,'persondata',1,'page',2,'2018-06-23 00:53:17','2020-02-05 19:11:22','2018-08-16 17:37:00'),
	(3,'velkommen-som-stoettemedlem',1,'message',2,'2018-08-07 20:34:01','2019-04-30 11:43:53','2018-08-07 20:33:01'),
	(4,'velkommen-som-frivillig-medlem',1,'message',2,'2018-12-19 21:48:34','2019-05-01 02:47:00','2018-12-19 21:48:34'),
	(5,'stoettemedlem-signupfee',1,'signupfee',2,'2018-12-19 21:47:09','2020-03-30 13:28:59','2020-03-30 13:28:00'),
	(6,'frivillig-signupfee',1,'signupfee',2,'2018-12-20 10:58:01','2020-03-30 13:28:35','2020-03-30 13:28:00'),
	(7,'stoettemedlem',1,'membership',2,'2018-12-20 10:56:34','2020-03-30 13:14:45','2020-03-30 13:14:00'),
	(8,'frivillig',1,'membership',2,'2018-12-19 21:49:17','2020-03-30 13:18:20','2020-03-30 13:18:00'),
	(9,'allerede-medlem',1,'page',2,'2018-12-20 11:02:44','2018-12-20 18:12:59','2018-12-20 11:02:59'),
	(10,'tilmelding',1,'page',2,'2018-12-20 11:06:27','2018-12-20 11:22:22','2018-12-20 11:06:22'),
	(11,'vaelg-medlemskab',1,'page',2,'2018-12-20 11:10:00','2020-01-13 18:17:09','2018-12-20 11:10:00'),
	(12,'afdelinger-i-kbhff',1,'page',2,'2019-07-05 20:59:11','2019-12-15 17:32:13','2019-07-05 20:59:00'),
	(18,'for-leverandoerer',1,'page',2,'2019-07-13 19:40:00','2019-12-15 18:41:55','2019-07-13 19:40:00'),
	(20,'handelsbetingelser',1,'page',2,'2019-07-13 19:43:59','2020-01-13 19:06:11','2019-07-13 19:43:00'),
	(118,'uge-2',1,'weeklybag',2,'2020-02-15 15:24:35','2020-02-17 20:13:56','2020-02-17 20:13:00'),
	(121,'uge-3',1,'weeklybag',2,'2020-02-18 20:59:14','2020-02-18 21:02:13','2020-02-18 21:02:00'),
	(122,'uge-4',1,'weeklybag',2,'2020-02-18 21:03:04','2020-02-18 21:06:50','2020-02-18 21:06:00'),
	(123,'uge-5',1,'weeklybag',2,'2020-02-18 21:07:23','2020-02-18 21:09:12','2020-02-18 21:09:00');

/*!40000 ALTER TABLE `SITE_DB`.`items` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table items_prices
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `SITE_DB`.`items_prices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `price` float NOT NULL,
  `currency` varchar(3) NOT NULL,
  `vatrate_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL DEFAULT '1',
  `quantity` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  KEY `currency` (`currency`),
  KEY `vatrate_id` (`vatrate_id`),
  KEY `type_id` (`type_id`),
  CONSTRAINT `items_prices_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `items_prices_ibfk_2` FOREIGN KEY (`currency`) REFERENCES `system_currencies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `items_prices_ibfk_3` FOREIGN KEY (`vatrate_id`) REFERENCES `system_vatrates` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `items_prices_ibfk_4` FOREIGN KEY (`type_id`) REFERENCES `system_price_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `SITE_DB`.`items_prices` WRITE;
/*!40000 ALTER TABLE `SITE_DB`.`items_prices` DISABLE KEYS */;

INSERT INTO `SITE_DB`.`items_prices` (`id`, `item_id`, `price`, `currency`, `vatrate_id`, `type_id`, `quantity`)
VALUES
	(1,7,200,'DKK',1,1,NULL),
	(2,8,200,'DKK',1,1,NULL),
	(3,5,100,'DKK',1,1,NULL),
	(4,6,100,'DKK',1,1,NULL);

/*!40000 ALTER TABLE `SITE_DB`.`items_prices` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table items_subscription_method
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `items_subscription_method` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `subscription_method_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  KEY `subscription_method_id` (`subscription_method_id`),
  CONSTRAINT `items_subscription_method_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `items_subscription_method_ibfk_2` FOREIGN KEY (`subscription_method_id`) REFERENCES `system_subscription_methods` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `items_subscription_method` WRITE;
/*!40000 ALTER TABLE `items_subscription_method` DISABLE KEYS */;

INSERT INTO `items_subscription_method` (`id`, `item_id`, `subscription_method_id`)
VALUES
	(1,7,2),
	(2,8,2);

/*!40000 ALTER TABLE `SITE_DB`.`items_subscription_method` ENABLE KEYS */;
UNLOCK TABLES;


/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
