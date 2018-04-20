<?
//require("PHPMailerAutoload.php");
require_once($_SERVER["FRAMEWORK_PATH"]."/includes/PHPMailer-5.2.26/PHPMailerAutoload.php");

function sendenkeltmail ($subject,$text,$email, $from = 'robot@medlem.kbhff.dk', $recipientname = '', $html = '', $file = '')
{

	error_reporting(0);
	date_default_timezone_set('Europe/Copenhagen');


	$mail = new PHPMailer();

	if ($recipientname > '')
	{
		$emailname = $recipientname;
	}

        if ($file > '')
        {
		$mail->AddAttachment($file);             // attachment
        }

        if ($html == '')
        {
                $html = '<style type="text/css">
	body {
		color: Black;
		background:White;
		font-family: Tahoma, Geneva, Arial, Helvetica, sans-serif;
		font-size: 10pt;
	}
	
	.form1 td {
		background:#b0e0e6;
		padding: 20px;
	}
</style><table border="0" width="600" class="form1" style="text-align: left;">
<tr><td align="left"><img src="cid:logoimg" align="right"><br /><br />' .nl2br($text) . '</td></tr></table>';
}
		
		
	$mail->IsSMTP();
	$mail->SMTPAuth   = true;                  // enable SMTP authentication
	$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
	$mail->Host       = "mail.skrig.dk";      // sets GMAIL as the SMTP server
	$mail->Port       = 465;                   // set the SMTP port for the GMAIL server

	$mail->Username   = "robot@medlem.kbhff.dk";  // GMAIL username
	$mail->Password   = "robertKlump4";            // GMAIL password
	$mail->AddEmbeddedImage('/var/www/medlem.kbhff.dk/images/kbhfflogo.png', 'logoimg', 'logo.png');	
	
	$mail->AddReplyTo("$from","KBHFF Medlem");
	$mail->From       = "medlem.kbhff.dk";
	$mail->FromName   = "KBHFF Medlem";
	$mail->Subject    = $subject;
	$mail->AltBody    = $text;
	$mail->WordWrap   = 50; // set word wrap
	$mail->MsgHTML($html);
	$mail->AddAddress($email, $emailname);
//	$mail->AddCC('okonomi@kbhff.dk', 'Økonomigruppen');
	$mail->IsHTML(true); // send as HTML

	if(!$mail->Send()) {
//	  echo "Mailer Error: " . $mail->ErrorInfo;
	} 

}

?>
