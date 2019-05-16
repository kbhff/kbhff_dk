<?php

/**
* Manages Departemnt Schedules: exceptions for the department.opening_weekday and department.opening_hours.
*/
class Afhentningsdage extends Model
{
	
	function __construct()
	{
		// Construct Model class, passing the Department model as a parameter. 
		// The Model class is constructed *before* adding values to Department model to ensure Department overwrites the standard values of Model 
		parent::__construct(get_class());

		// Define the name of departments table in database
		$this->db = SITE_DB.".system_departments";
		$this->department_schedule_db = SITE_DB.".department_schedule";


		// department_id
		$this->addToModel("department_id", array(
			"type" => "string",
			"label" => "Department",
			"required" => true,
			"hint_message" => "Department for the exception schedule", 
			"error_message" => "A department must be defined."
		));
		// schedule_date
		$this->addToModel("schedule_date", array(
			"type" => "date",
			"label" => "Date",
			"required" => true,
			"hint_message" => "Date of the the exception schedule", 
			"error_message" => "A date must be defined."
		));
		// hours
		$this->addToModel("opening_hours", array(
			"type" => "string",
			"label" => "Hours",
			"required" => false,
			"hint_message" => "Hours (if diferent to default", 
			"error_message" => ""
		));
		// closed
		$this->addToModel("closed", array(
			"type" => "boolean",
			"label" => "Closed",
			"required" => false,
			"hint_message" => "Department is Closed on the date", 
			"error_message" => ""
		));
		
		
	}


	## functions for department schedule exceptions.
	## department has opening_weekday (1 -7) and opening_hours (TODO: DEfeine)
	## an exception can be:
	## "closed" true/false 1/0
	## "schedule_date" date when the exception applies. 
	## "opening_hours" can be null - if present, use the hours that apply

	function getDepartmentScheduleList ($_options = false) {
		// Define default values
		$id = false;
		$department_id = false;

		## YYYY-MM-DD
		$schedule_date = false;
		$schedule_period_weeks = 1;
		
		// Search through $_options to find recognized parameters
		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {
					case "id"            : $id             = $_value; break;
					case "department_id" : $department_id  = $_value; break;
					case "schedule_date"          : $schedule_date           = $_value; break;
					case "schedule_period_weeks" : $schedule_period_weeks = $_value; break;

				}
			}
		}

		// Query database for departments and schedule exceptions.
		$query = new Query();
		$sql = "SELECT sys_dep.name, dep_sch.id, dep_sch.department_id, closed, UNIX_TIMESTAMP(schedule_date) as schedule_date, dep_sch.opening_hours  FROM ".$this->db." sys_dep, ".$this->department_schedule_db." dep_sch
				WHERE sys_dep.id = dep_sch.department_id";
		if ($id != false) {
			$sql .= " AND dep_sch.id = $id";
		}
		if ($department_id != false) {
			$sql .= " AND sys_dep.id = $department_id";
		}

		if ($schedule_date != false) {
			## YYYY-MM-DD
			$sql .= " AND dep_sch.schedule_date BETWEEN CAST('$schedule_date' AS DATE) AND DATE_ADD(CAST('$schedule_date' AS DATE), INTERVAL ".$schedule_period_weeks ." WEEK)";
		}
		$sql .= " ORDER BY dep_sch.department_id, schedule_date";
		

		# do query
		if($query->sql($sql)) {
			return $query->results();
		}

		return false;		
	}

	function saveDepartmentSchedule ($action) {

		// Get content of $_POST array which have been "quality-assured" by Janitor 
		$this->getPostedEntities();
		
		// Check that the number of REST parameters is as expected and that the listed entries are valid.
		if (!$this->validateList(array("department_id", "schedule_date"))) {
			message()->addMessage("Schedult could not be saved.", array("type" => "error"));
			return false;

		}

		$id = false;

		if(count($action) == 2) {
			$id = $action[1];
		}	
		$department_id = $this->getProperty("department_id", "value");
		$closed = $this->getProperty("closed", "value");

		$opening_hours = $this->getProperty("opening_hours", "value");
		
		$schedule_date = $this->getProperty("schedule_date", "value");
		// arrives with format YYYY-m-d
		// we want it in format dd-mm-YYYY

		$query = new Query();
		if (!$id) {
			$sql = "INSERT INTO ".$this->department_schedule_db." 
					SET department_id = $department_id, closed = $closed, schedule_date = '$schedule_date'";
			if ($opening_hours) {
				$sql .= ", opening_hours = '$opening_hours'";
			}

			//$query->sql($sql);
			if($query->sql($sql)) {
				$id = $query->lastInsertId();
				message()->addMessage("Department Schedule event created (ID: $id)");
				return $this->getDepartmentScheduleList(array("id" => $id));
			} 
		} else {
			$sql = "UPDATE ".$this->department_schedule_db." 
					SET department_id = $department_id, closed = $closed, schedule_date = '$schedule_date', opening_hours = '$opening_hours'
					WHERE id = $id";

			if($query->sql($sql)) {
				message()->addMessage("Department Schedule event updated (ID: $id)");
				return $this->getDepartmentScheduleList(array("id" => $id));
			} else {
				message()->addMessage("ERROR (ID: $id)");
			}
		}
		return false;
	}



	function removeDepartmentSchedule ($action) {

		// Get content of $_POST array which have been "quality-assured" by Janitor 
		$this->getPostedEntities();
		
		$id = false;

		if(count($action) == 2) {
			$id = $action[1];
		}	
		
		$query = new Query();
		if (!$id) {
			message()->addMessage("ID Missing ");
			return false;
		} else {
			$sql = "DELETE FROM ".$this->department_schedule_db." 
					WHERE id = $id";

			if($query->sql($sql)) {
				message()->addMessage("Department Schedule event removed (ID: $id)");
				return $this->getDepartmentScheduleList(array("id" => $id));
			} else {
				message()->addMessage("ERROR (ID: $id)");
			}
		}
		return false;
	}


}
?>
