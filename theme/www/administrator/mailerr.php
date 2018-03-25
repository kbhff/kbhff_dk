<?
	include("../ressources/.mysql_common.php");
	include("../ressources/.sendmail.php");

        require_once("PHPMailerAutoload.php");

senderrmail(40988);
senderrmail(40989);
senderrmail(40991);
senderrmail(40992);
senderrmail(40993);
senderrmail(40994);
senderrmail(40995);
senderrmail(40996);
senderrmail(40997);
senderrmail(40998);
senderrmail(40999);
senderrmail(41000);
senderrmail(41001);
senderrmail(41002);
senderrmail(41003);
senderrmail(41004);
senderrmail(41005);
senderrmail(41006);
senderrmail(41007);
senderrmail(41008);
senderrmail(41009);
senderrmail(41010);
senderrmail(41011);
senderrmail(41012);
senderrmail(41013);
senderrmail(41014);
senderrmail(41017);
senderrmail(41018);
senderrmail(41020);
senderrmail(41021);
senderrmail(41025);
senderrmail(41027);
senderrmail(41030);
senderrmail(41033);
senderrmail(41035);
senderrmail(41037);
senderrmail(41041);
senderrmail(41042);
senderrmail(41043);
senderrmail(41053);
senderrmail(41061);
senderrmail(41065);
senderrmail(41066);
senderrmail(41069);
senderrmail(41073);
senderrmail(41074);
senderrmail(41075);
senderrmail(41076);
senderrmail(41082);
senderrmail(41083);
senderrmail(41085);
senderrmail(41086);
senderrmail(41087);
senderrmail(41088);
senderrmail(41090);
senderrmail(41091);
senderrmail(41096);
senderrmail(41100);
senderrmail(41103);
senderrmail(41104);
senderrmail(41105);
senderrmail(41108);
senderrmail(41109);
senderrmail(41112);
senderrmail(41116);
senderrmail(41120);
senderrmail(41123);
senderrmail(41126);
senderrmail(41127);
senderrmail(41129);
senderrmail(41130);
senderrmail(41131);
senderrmail(41133);
senderrmail(41135);
senderrmail(41138);
senderrmail(41139);
senderrmail(41147);
senderrmail(41148);
senderrmail(41151);
senderrmail(41154);
senderrmail(41159);
senderrmail(41160);
senderrmail(41161);
senderrmail(41162);
senderrmail(41164);
senderrmail(41168);
senderrmail(41169);
senderrmail(41170);
senderrmail(41173);
senderrmail(41175);
senderrmail(41179);
senderrmail(41183);
senderrmail(41184);
senderrmail(41185);
senderrmail(41187);
senderrmail(41190);
senderrmail(41192);
senderrmail(41195);
senderrmail(41197);
senderrmail(41198);
senderrmail(41200);
senderrmail(41202);
senderrmail(41203);
senderrmail(41204);
senderrmail(41208);
senderrmail(41210);
senderrmail(41211);
senderrmail(41212);
senderrmail(41215);
senderrmail(41217);
senderrmail(41233);
senderrmail(41237);



function senderrmail($orderno)
{

$query = 'SELECT  ff_persons.firstname as forn, ff_persons.middlename, ff_persons.lastname, ff_persons.email, cc_trans_amount as bel
		FROM  ff_orderhead, ff_persons
		WHERE ff_orderhead.puid = ff_persons.uid
		AND ff_orderhead.orderno = ' . (int)$orderno . ';';
		
$result = doquery($query);
	$num = mysql_num_rows($result);
     if ($num>0) {
		$row = mysql_fetch_row($result);
$content = 'Kære ' . $row[0] . "\n\nVedrørende din ordre #" . $orderno . ', 1. oktober' . "\n\n" .
		'Da der var en fejl i IT-systemet, kom der ingen kvittering, og systemet stoppede med en blank skærm.' . "\n\n" .
		'Da du derfor ikke har set din ordre blive registreret, har vi for en sikkerheds skyld annulleret den, ' .
		'og tilbageført din betaling (kr. ' . $row[4] . '). Tilbageførslen vil dukke op på din banks kontoudtog i løbet ' .
		'af et par dage.' . "\n\n"  .
		'Vi er klar over, at du måske regnede med at ordren var registreret - men for at undgå dobbeltordrer og madspild har vi valgt denne '.
		'løsning. '. "\n\nHar du spørgsmål kan du henvende dig til Torsten Arendrup (torsten@arendrup.dk).\n\n" .
		'Med venlig hilsen' ."\nKBHFF Kommunikation / IT og Økonomigruppen";
		sendenkeltmail('KBHFF: Refusion af fejlagtig ordre #' .  $orderno, $content , $row[3], 'svar-ikke-her@kbhff.dk',$row[0]);
	}

}

?>
