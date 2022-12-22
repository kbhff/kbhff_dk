<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}


include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


include_once("classes/users/superuser.class.php");
$UC = new SuperUser();
	
$users = $UC->getAllActiveUsers();
$recipients = [];

if($users) {

	foreach($users as $user) {

		$kbhff_user = $UC->getKbhffUser(["user_id" => $user["id"]]);

		if(!in_array($kbhff_user["email"], $recipients) && !$UC->getUserLogAgreement("disable_ordering_reminder", ["user_id" => $user["id"]])) {

			// add to recipients
			$recipients[] = $kbhff_user["email"];
			$values[$kbhff_user["email"]] = [
				"NICKNAME" => $kbhff_user["nickname"],
				"DEADLINE_DATE" => date("d.m.Y", strtotime(ORDERING_DEADLINE_TIME)),
				"DEADLINE_TIME" => date("H:i", strtotime(ORDERING_DEADLINE_TIME))
			];

			// send reminder
			// mailer()->send([
			// 	"recipients" => [$kbhff_user["email"]],
			// 	"template" => "ordering_reminder",
			// 	"values" => [
			// 		"NICKNAME" => $kbhff_user["nickname"],
			// 		"DEADLINE_DATE" => date("d.m.Y", strtotime(ORDERING_DEADLINE_TIME)),
			// 		"DEADLINE_TIME" => date("H:i", strtotime(ORDERING_DEADLINE_TIME))
			// 	]
			// ]);
		}
	}

	debug([count($recipients), count(array_unique($recipients))]);

}


