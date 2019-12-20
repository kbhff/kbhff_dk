Util.Objects["member_help_accept_terms"] = new function() {
	this.init = function(scene) {

		scene.resized = function() {
			// u.bug("scene.resized", this);
		}

		scene.scrolled = function() {
			// u.bug("scene.scrolled", this);
		}

		scene.ready = function() {
			// u.bug("scene.ready", this);

			var form_accept_terms = u.qs("form.accept", this);
			u.f.init(form_accept_terms);
		}

		// scene is ready
		scene.ready();
	}
}
