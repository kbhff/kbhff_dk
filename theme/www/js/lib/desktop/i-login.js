Util.Objects["login"] = new function() {
	this.init = function(scene) {

		scene.resized = function() {
			// u.bug("scene.resized", this);
		}

		scene.scrolled = function() {
			// u.bug("scene.scrolled", this);
		}

		scene.ready = function() {
			// u.bug("scene.ready", this);

			var form_login = u.qs("form.login", this);
			u.f.init(form_login);
		}

		// scene is ready
		scene.ready();
	}
}
