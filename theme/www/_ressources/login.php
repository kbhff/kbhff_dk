<?
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}

	include($_SERVER["CI_PATH"]."/custom/mysql_common.php");
	require_once("class.inputfilter_clean.php");
?>
<?php 
$tags = '';
$attr = '';
$tag_method = 0;
$attr_method = 0;
$xss_auto = 1;
$myFilter = new InputFilter($tags, $attr, $tag_method, $attr_method, $xss_auto);


// $_POST["url"] = stripslashes($_POST["url"]);
$user = $myFilter->process($_POST["user"]);
$pw = $myFilter->process($_POST["pw"]);

$msg = checklogin($user, $pw);

echo $msg; 

function checklogin($user, $pw)
{
	$query = 'select firstname from ff_persons where uid = ' . doubleval($user) . ' and password = "' . addslashes($pw) . '"';
	$res = doquery($query);
//	if (mysql_num_rows($res) == 0)
	if ($res->num_rows == 0)
	{
		return '?? fejl i medlemsnummer eller password';
	} else {
		return 'OK';
	}
}

?>
