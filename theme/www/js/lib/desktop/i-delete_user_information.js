Util.Objects["delete_user_information"] = new function() {
	this.init = function(scene) {

		scene.resized = function() {
			// u.bug("scene.resized:", this);
		}

		scene.scrolled = function() {
			// u.bug("scene.scrolled:", this);
		}

		scene.ready = function() {
			// u.bug("scene.ready:", this);


			var confirm_cancellation = u.qs("form.confirm_cancellation", this);
			u.f.init(confirm_cancellation);

		}

		// scene is ready
		scene.ready();
	}
}
