<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}

	unset($_GET["_"]);
	require_once($_SERVER["CI_PATH"]."/InputFilter/class.inputfilter.php");

$tags = '';
$attr = '';
$tag_method = 0;
$attr_method = 0;
$xss_auto = 1;
$myFilter = new InputFilter($tags, $attr, $tag_method, $attr_method, $xss_auto);

$sms = "45" . $myFilter->process($_GET["sms"]);
$navn = $myFilter->process($_GET["navn"]);
$afd = $myFilter->process($_GET["afd"]);

//$message = urlencode("Husk at hente de bestilte varer hos KBHFF i dag! mvh KBHFF " . $afd);
$message = "Husk at hente de bestilte varer hos KBHFF i dag! Mvh KBHFF " . $afd;


// Define recipients
$recipients = [(int)$sms];
$url = "https://gatewayapi.com/rest/mtsms";
$api_token = "lqCvInX-TaywkHMRtbQf-41dDNYyqbC2_SjnyIL-rpEcXxu6zqfDLge4aW94YkqN";
$json = [
    'sender' => 'KBHFF',
    'message' => $message,
    'recipients' => [],
];
foreach ($recipients as $msisdn) {
    $json['recipients'][] = ['msisdn' => $msisdn];}

$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
curl_setopt($ch,CURLOPT_USERPWD, $api_token.":");
curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($json));
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);
//print($result);
$json = json_decode($result);
if(property_exists($json, "ids")) {
	echo ("alert('Sendt SMS til $navn');");
}
else {
	echo ("Fejl $content');");
	
}

//print_r($json);

//print_r($json->ids);



//
//
// $username = 'Arendrup';                      //username used in HQSMS
// $password = md5('4711');
// $encoding = 'utf8';
// $to = '45' . $sms;                      //destination number
// $from = urlencode("KBHFF");                //sender name have to be activated
// $message = urlencode("Husk at hente de bestilte varer hos KBHFF i dag! mvh KBHFF " . $afd);
// $url = 'https://ssl.hqsms.com/api/sms.do';
// $c = curl_init();
//     curl_setopt($c, CURLOPT_URL, $url);
//     curl_setopt($c, CURLOPT_POST, true);
//     curl_setopt($c, CURLOPT_POSTFIELDS, 'username='.$username.'&password='.$password.'&from='.$from.'&to='.$to.'&encoding=' . $encoding . '&message='.$message);
//     curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
//     $content = curl_exec ($c);
//     curl_close ($c);
// // echo $content;
//
// 	if (substr($content,0,2) == 'OK')
// 	{
// 		echo ("alert('Sendt SMS til $navn');");
// 	} else {
// 		echo ("Fejl $content');");
// 	}

?>
