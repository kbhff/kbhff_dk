<? 
global $TC;
include_once("classes/users/superuser.class.php");
$UC = new SuperUser;

$tally = $TC->getTally(["department_id" => $UC->getUserDepartment(["user_id" => session()->value("user_id")])["id"]]);
$tally_id = $tally["id"];

?>

<div class="scene shop_shift i:scene" itemscope itemtype="http://schema.org/NewsArticle">
	<h1>Shop shift</h1>
	<p>Shop shift dummy. Shop shift dummy. Shop shift dummy. Shop shift dummy. Shop shift dummy. Shop shift dummy.</p>
	<a href="butiksvagt/kasse/<?= $tally_id ?>">Åbn kasse</a>
	

	
</div>