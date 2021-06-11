Util.Modules["department_pickupdates"] = new function() {
	this.init = function(div) {
		// u.bug("scene init:");

		var pickupdates = u.qsa("li.item", div);

		for(var i = 0; i < pickupdates.length; i++) {
			var pickupdate = pickupdates[i];

			var add = u.qs("ul.actions li.add", pickupdate);
			add.pickupdate = pickupdate;
			var remove = u.qs("ul.actions li.remove", pickupdate);
			remove.pickupdate = pickupdate;

			add.added = function(response) {
				console.log(response);

				u.addClass(this.pickupdate, "added");
			}

			remove.removed = function(response) {
				u.removeClass(this.pickupdate, "added");
			}

		}

		//var allButtons = u.qsa("#content .scene.taglistList .all_items ul.items");
/*
*/
		// scene is ready
		//taglist_tags.ready();
	}
}