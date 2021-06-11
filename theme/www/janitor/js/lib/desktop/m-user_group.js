Util.Modules["user_group"] = new function() {
	this.init = function(div) {
		// u.bug("scene init:");

		var users = u.qsa("li.item", div);

		for(var i = 0; i < users.length; i++) {
			var user = users[i];
			user.user_group = u.qs("dd.user_group", user)
			
			var update = u.qs("ul.actions li.update", user);
			if(update) {
				update.user = user;

				update.confirmed = function(response) {

					if(response.cms_status == "success" && response.cms_object) {

						u.bug(this.user.user_group);
						this.user.user_group.innerHTML = response.cms_object.user_group;
						u.as(this, "display", "none");
					}
				}
			}

			

		}

		//var allButtons = u.qsa("#content .scene.taglistList .all_items ul.items");
/*
*/
		// scene is ready
		//taglist_tags.ready();
	}
}