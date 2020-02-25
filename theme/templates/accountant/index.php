<? 
global $action;
global $DC;

$departments = $DC->getDepartments();

?>

<div class="scene accountant i:scene" itemscope itemtype="http://schema.org/NewsArticle">
	<h1>Bogholder</h1>
	
	<div class="c-wrapper">

		<h2>Se afregninger pr. afdeling</h2>
		<ul>
		<? foreach($departments as $department): ?>
			<li><a href="/bogholder/afregninger/<?= $department["id"] ?>"><?= $department["name"] ?></a></li>
		<? endforeach; ?>
		</ul>
	</div>
	
</div>