<?php
/**
* This file contains the site upgrade override functionality.
*/
include_once("classes/system/upgrade.core.class.php");

class Upgrade extends UpgradeCore {

	function __construct() {
		parent::__construct();
	}

	function preUpgrade() {

		$query = new Query();

		$pdpoi = SITE_DB.".project_department_pickupdate_order_items";
		$pdpci = SITE_DB.".project_department_pickupdate_cart_items";
		$pdp = SITE_DB.".project_department_pickupdates";
		$pdpoi_info = $this->tableInfo($pdpoi);
		$pdpci_info = $this->tableInfo($pdpoi);

		// project_department_pickupdate_order_items has old design
		if($pdpoi_info && isset($pdpoi_info["columns"]["department_id"]) && isset($pdpoi_info["columns"]["pickupdate_id"])) {

			// add and populate department_pickupdate_id column
			// table will be restructured later by UpgradeCore::synchronizeTable
			$sql = "SELECT * FROM ".$pdpoi;
			if($query->sql($sql)) {
				$pdpoi_rows = $query->results();
	
				$add_column = $this->addColumn($pdpoi, "department_pickupdate_id", "int(11) NOT NULL", "id");
				$this->process($add_column);

				if($add_column["success"]) {

					foreach ($pdpoi_rows as $pdpoi_row) {
						
						$sql = "SELECT * FROM ".$pdp." WHERE department_id = ".$pdpoi_row["department_id"]." AND pickupdate_id = ".$pdpoi_row["pickupdate_id"];
						if($query->sql($sql)) {
							
							$department_pickupdate_id = $query->result(0, "id");
							$sql = "UPDATE ".$pdpoi." SET department_pickupdate_id = $department_pickupdate_id WHERE department_id = ".$pdpoi_row["department_id"]." AND pickupdate_id = ".$pdpoi_row["pickupdate_id"];
							$query->sql($sql);
						}
					}
					
				}
			}
		}

		// project_department_pickupdate_cart_items has old design
		if($pdpci_info && isset($pdpci_info["columns"]["department_id"]) && isset($pdpci_info["columns"]["pickupdate_id"])) {

			// add and populate department_pickupdate_id column
			// table will be restructured later by UpgradeCore::synchronizeTable
			$sql = "SELECT * FROM ".$pdpci;
			if($query->sql($sql)) {
				$pdpci_rows = $query->results();
	
				$add_column = $this->addColumn($pdpci, "department_pickupdate_id", "int(11) NOT NULL", "id");
				$this->process($add_column);

				if($add_column["success"]) {

					foreach ($pdpci_rows as $pdpci_row) {
						
						$sql = "SELECT * FROM ".$pdp." WHERE department_id = ".$pdpci_row["department_id"]." AND pickupdate_id = ".$pdpci_row["pickupdate_id"];
						if($query->sql($sql)) {
							
							$department_pickupdate_id = $query->result(0, "id");
							$sql = "UPDATE ".$pdpci." SET department_pickupdate_id = $department_pickupdate_id WHERE department_id = ".$pdpci_row["department_id"]." AND pickupdate_id = ".$pdpci_row["pickupdate_id"];
							$query->sql($sql);
						}
					}
				}
			}
		}		

	}

}

?>