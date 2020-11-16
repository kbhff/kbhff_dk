Util.Modules["update_userinfo_form"] = new function() {
	this.init = function(scene) {

		scene.resized = function() {
			// u.bug("scene.resized", this);
		}

		scene.scrolled = function() {
			// u.bug("scene.scrolled", this);
		}

		scene.ready = function() {
//			// u.bug("scene.ready", this);
			var form = u.qs("form");
			u.f.init(form, this);

		}

		// scene is ready
		scene.ready();
	}
}
