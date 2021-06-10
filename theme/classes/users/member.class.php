<?php
/**
* @package janitor.member
* This file contains simple member extensions
* Meant to allow local member additions/overrides
*/

/**
* Member customization class
*/
class Member extends MemberCore {


	/**
	* Init, set varnames, validation rules
	*/
	function __construct() {

		parent::__construct(get_class());


	}

	function upgraded($member, $item) {

		// reset user_group to User if new membership is Støttemedlem
		if(isset($item["fixed_url_identifier"]) && $item["fixed_url_identifier"] == "stoettemedlem") {

			include_once("classes/users/superuser.class.php");
			$UC = new SuperUser();

			$user_groups = $UC->getUserGroups();
			$user_key = arrayKeyValue($user_groups, "user_group", "User");
			$_POST["user_group_id"] = $user_groups[$user_key] ? $user_groups[$user_key]["id"] : false;
			$UC->update(["update", $member["user_id"]]);
			unset($_POST);
			
			message()->resetMessages();
		}
	}
}

?>