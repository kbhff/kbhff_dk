Util.Modules["confirm_account"] = new function() {
	this.init = function(scene) {

		scene.resized = function() {
			// u.bug("scene.resized:", this);
		}

		scene.scrolled = function() {
			// u.bug("scene.scrolled:", this);
		}

		scene.ready = function() {
			u.bug("scene.ready:", this);

			var confirm_account = u.qs("form.confirm_account", this);
			u.f.init(confirm_account);

		}

		// scene is ready
		scene.ready();
	}
}
