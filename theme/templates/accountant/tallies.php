<? 
global $action;
global $TC;
global $DC;

$department_id = $action[1];
$department = $DC->getDepartment(["id" => $department_id]);
$tallies = $TC->getTallies(["department_id" => $department_id, "order" => "created_at", "status" => 2]);

?>

<div class="scene accountant i:scene" itemscope itemtype="http://schema.org/NewsArticle">
	<h1>Afregninger for <?= $department["name"] ?></h1>
	
	<div class="c-wrapper">
		<ul class="tallies">

	<? if($tallies): ?>

		<li class="labels">
			<ul>
				<li class="time">Tidspunkt/periode</li>
				<li class="turnover">Kontant omsætning</li>
			</ul>
		</li>

	<? foreach($tallies as $tally): ?>
		<? 
		$created_at = date("d/m-Y", strtotime($tally["created_at"]));
		$closed_at = date("d/m-Y", strtotime($tally["modified_at"]));
		?>

		<li class="tally tally_id:<?= $tally["id"] ?>">
			<ul class="details">
				<li class="created_at">
					<a href="/bogholder/afregninger/<?= $department_id."/".$tally["id"] ?>"><? print $created_at; if($closed_at != $created_at): print " - ".$closed_at; endif; ?></a>
				</li>
				<li class="total_cash_revenue"><?= $TC->getTotalCashRevenue($tally["id"]) ?: 0 ?> kr.</li>
			</ul>
		</li>
	<? endforeach; ?>
		</ul>	
	</div>

<? else: ?>
	<p>Ingen afregninger</p>
<? endif; ?>

</div>