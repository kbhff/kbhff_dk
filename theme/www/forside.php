<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}

header("Location: /");
exit();

?>
 