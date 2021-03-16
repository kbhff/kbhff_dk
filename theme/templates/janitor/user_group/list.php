
<?php
global $action;
global $model;
include_once("classes/users/supermember.class.php");
$MC = new SuperMember();

$user = $model->getKbhffUser(["user_id" => session()->value("user_id")]);
$user_department = $user ? $user["department"] : false;
$user_group = $model->getUserGroups(["user_group_id" => $user["user_group_id"]]);

if($user_group && $user_group["user_group"] == "Shop shift") {

	$list_users = $model->getDepartmentUsers($user_department ? $user_department["id"] : false);
}
else {

	$list_users = $model->getAllActiveUsers();
}


?>

<div class="scene i:scene defaultList">
	<h1>User groups</h1>

	<? if($user_group && $user_group["user_group"] == "Developer"): ?>
	<p>Hey, you are a developer – you should manage users and user groups in the dedicated control panel.</p>
	<ul class="actions">
		<?= $model->link("Go to User Management", "/janitor/admin/user/list", ["class" => "button", "wrapper" => "li.link"]) ?>
	</ul>
	<? else: ?>
	<div class="all_items i:defaultList filters i:user_group"<?= $HTML->jsData(["search"]) ?>>
<?		if($list_users && $user_group): ?>
		<ul class="items">
<?			foreach($list_users as $list_user): 
			$member_no = $model->getUserNames(["type" => "member_no", 
			"user_id" => $list_user["id"]]);
			$list_user_membership = $MC->getMembers(["user_id" => $list_user["id"]]);

			if($list_user_membership && $list_user_membership["item"]) {
				$list_user_membership["type"] = isset($list_user_membership["item"]["fixed_url_identifier"]) ? $list_user_membership["item"]["fixed_url_identifier"] : "";
			}
			elseif($list_user_membership) {
				$list_user_membership["type"] = "inactive" ;
			}

			$list_user_group = $model->getUserGroups(["user_group_id" => $list_user["user_group_id"]]);

			$list_user_department = $model->getUserDepartment(["user_id" => $list_user["id"]]);

			$allow_display = false;
			$allow_update = false;
			
			// list user is member (active or inactive)
			if($list_user_membership) {

				// Shop shifts cannot see stoettemedlemmer or inactive members
				if(!($user_group["user_group"] == "Shop shift" && ($list_user_membership["type"] == "stoettemedlem" || $list_user_membership["type"] == "inactive"))) {
					$allow_display = true;

					if($list_user_membership["type"] == "frivillig") {
						
						if ($user_group["user_group"] == "Shop shift" && $list_user_group["user_group"] == "User") {
							$allow_update = true;
						}
						elseif (
							(
								$user_group["user_group"] == "Local administrator"
								|| $user_group["user_group"] == "Purchasing group"
								|| $user_group["user_group"] == "Communication group"
							)
							&& (
								$list_user_group["user_group"] == "User"
								|| $list_user_group["user_group"] == "Shop shift"
								|| $list_user_group["user_group"] == "Local administrator"
								|| $list_user_group["user_group"] == "Purchasing group"
								|| $list_user_group["user_group"] == "Communication group"
							)
							&& $list_user_group["user_group"] != $user_group["user_group"]
						) {
							$allow_update = true;
						}
					}

				}
				
			}

?>
			<? if($allow_display): ?>
			<li class="item item_id:<?= $list_user["id"] ?>">
				<h3><?= strip_tags($list_user["nickname"]) ?></h3>

				<dl class="info">
					<dt class="member_no">Member no.</dt>
					<dd class="member_no"><?= $member_no ? $member_no["username"] : "N/A" ?></dd>;

					<dt class="membership_type">Membership type</dt>
					<dd class="membership_type"><?= isset($list_user_membership["item"]["name"]) ? $list_user_membership["item"]["name"] : "Inactive" ?></dd>;

					<dt class="department">Department</dt>
					<dd class="department"><?= $list_user_department ? $list_user_department["name"] : "N/A" ?></dd>;
					
					<dt class="user_group">User group.</dt>
					<dd class="user_group"><?= $list_user_group ? $list_user_group["user_group"] : "N/A" ?></dd>;

				</dl>

				<? if($allow_update): ?>
				<ul class="actions">
					<?= $model->oneButtonForm(
						'Change user group to "'.$user_group["user_group"].'"', 
						"/janitor/user_group/updateUserUserGroup/".$list_user["id"], [
							"inputs" => [
								"user_group_id" => 	$user_group["id"]
								],
							// "confirm-value" => "Are you sure? This can only be undone by a system administrator.",
							"confirm-value" => "Are you sure?",
							"wrapper" => "li.update",
						]) 
					?>
				</ul>
				<? endif; ?>
			 </li>
			<? endif; ?>
<?			endforeach; ?>
		</ul>
<?		else: ?>
		<p>No users.</p>
<?		endif; ?>
<?		endif; ?>
	</div>

</div>
