<?php


$access_item["/"] = true;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


// get REST parameters
$action = $page->actions();

include_once("classes/shop/tally.class.php");
$TC = new Tally();
include_once("classes/system/department.class.php");
$DC = new Department();


// page info
$page->bodyClass("accountant");
$page->pageTitle("Bogholder");

if($action) {

	if($action[0] == "afregninger") {

		// /bogholder/afregninger/#department_id#
		if(count($action) == 2) {

			$page->page([
				"templates" => "accountant/tallies.php"
			]);
			exit();
		}

		// /bogholder/afregninger/#department_id#/#tally_id#
		elseif(count($action) == 3) {

			$page->page([
				"templates" => "accountant/tally.php"
			]);
			exit();
		}

	}
	elseif($action[0] == "download") {

		// /bogholder/download
		if(count($action) == 1) {

			$TC->getPostedEntities();
			
			$creation_date = getPost("creation_date");

			$csv = $TC->createCsv($creation_date);

			header('Content-Description: File Transfer');
			header('Content-Type: text/text');
			header("Content-Type: application/force-download");
			header('Content-Disposition: attachment; filename='.$creation_date."_kbhff_regnskab.csv");
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . strlen($csv));
			ob_clean();
			flush();
			print $csv;
			exit();
		}
	}
}

// standard template
$page->page(array(
	"templates" => "accountant/index.php",
	// "type" => "accountant"
));
exit();


?>
 