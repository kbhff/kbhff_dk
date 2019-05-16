<?
// Get methods for user and shop data manipulation
$UC = new User();
$SC = new Shop();
$IC = new Items();

$user = $UC->getKbhffUser();
$user_department = $UC->getUserDepartment();

$DC = new Department();

$time = time();
$monday = $time-(24*60*60*(date("N", $time)-1));
$schedule_period_weeks = 6;

// place $user_department in 1st position?
$departments = $DC->getDepartments(array("schedule_date" => date("Y-m-d", $monday), "schedule_period_weeks" => $schedule_period_weeks));

// data for Afhentningsdage og lokale åbningsdage
$AC = new Afhentningsdage();

function check_in_range($start_date, $end_date, $date_from_user)
{
	// received date("Y-m-d",timestamp) to avoid loosing in the hours of day.... too lazy/tired to do it properly reached this poit
  // Convert to timestamp
  $start_ts = strtotime($start_date);
  $end_ts = strtotime($end_date);
  $user_ts = strtotime($date_from_user);

  // Check that user date is between start & end
  return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
}

?>

<div class="scene profile i:profile">

	<div class="banner i:banner variant:1 format:jpg"></div>

	<?	// Display any backend generated messages
		if(message()->hasMessages()): ?>
		
			<p class="errormessage">
		<?	$messages = message()->getMessages(array("type" => "error"));
			foreach($messages as $message): ?>
				<?= $message ?><br>
		<?	endforeach;?>
			</p>

			<p class="message">
		<?	$messages = message()->getMessages(array("type" => "message"));
			foreach($messages as $message): ?>
				<?= $message ?><br>
		<?	endforeach; ?>
			</p>

			<? message()->resetMessages(); ?>
	<?	endif; ?>

	<div class="c-wrapper">



		// Afhentningsdage og lokale åbningsdage
		<div class="c-two-thirds">

			<div class="section intro">
				<h2>Afhentningsdage og lokale åbningsdage</h2>
			</div>
		</div>
		<p>
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet. Proin gravida dolor sit amet lacus accumsan et viverra justo commodo. Proin sodales pulvinar sic tempor. Sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nam fermentum, nulla luctus pharetra vulputate, felis tellus mollis orci, sed rhoncus pronin sapien nunc accuan eget.
		</p>
		<p>
<?

		print "<table border=1>";
		print "<tr>";
		print "<td>AFHENTNINGSDAGE</td>";
		foreach ($departments as $key => $department) {
				if ($key > 5)
					break;
				print "<td>";
				print $department['abbreviation']." ";
				print "</td>";
		}
		print "</tr>\n\n";
		for ($i = 0; $i < (7*$schedule_period_weeks); $i+=7) {
			$week_time = $monday+(24*60*60*$i);
			print "<tr>";
			print "<td>".date("d.m.Y", $week_time)." (UGE ".date("W", ($week_time)).")</td>";
			
			foreach ($departments as $key => $department) {
				if ($key > 5)
					break;
				print "<td>";
				$day_text = "";

				if ($department['schedules']) {
					foreach ($department['schedules'] as $schedule) {

						if (check_in_range(date("Y-m-d", $week_time), date("Y-m-d", $monday+(24*60*60*($i+7	))), date("Y-m-d", $schedule['schedule_date']))) {
							$day_text .= "<a href='/afhentningsdage/edit/".$schedule['id']."' >".date("D d.m.Y", $schedule['schedule_date'])."<br>";
							if ($schedule['closed']) {
								$day_text .= "<font>CLOSED!</font>";	
							} else {
								$day_text .= "Alt. OPEN!";
							}
							if ($schedule["opening_hours"] != "") {
								$day_text .= "<br>".$schedule["opening_hours"];
							} else {
								$day_text .= "<br>".$department["opening_hours"];
							}
							$day_text .= "<a/><br>";
							$day_text .= "<a href='/afhentningsdage/remove/".$schedule['id']."' >REMOVE</>";
						}
					}
				}
				if ($day_text == "") {
					$tmp = $monday+(24*60*60*($i+$department['opening_weekday']));
					$day_text .= "<a href='/afhentningsdage/new_dep_pickup_date/".$department["id"]."/".$tmp."'>";
					$day_text .= "<font color='FF00CC'>N. Open!<br>".date("D d.m.Y", $tmp)."<br>".$department["opening_hours"]."</font>";
					$day_text .= "<a/>";
				}
				print $day_text."<br>";
				print "</td>";
			}
			print "</tr>\n\n";
		}
		print "</table>";


		
?>
	</p>
	</div>

</div>
