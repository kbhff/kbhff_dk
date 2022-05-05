Util.Modules["order_history"] = new function() {
	this.init = function(scene) {
//		u.bug("comment init:", div);

		var log_entries = u.qsa(".log_entries", scene);
		var i, log_entry;
		for(i = 0; i < log_entries.length; i++) {
			log_entry = log_entries[i];
			u.addExpandArrow(log_entry);

			u.ce(log_entry);
			log_entry.clicked = function() {
				u.tc(this, "open");
			}
		}


	}
}
