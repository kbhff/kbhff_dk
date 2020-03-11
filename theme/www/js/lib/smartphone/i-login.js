Util.Modules["login"] = new function() {
	this.init = function(scene) {

		scene.resized = function() {
			// u.bug("scene.resized", this);
		}

		scene.scrolled = function() {
			// u.bug("scene.scrolled", this);
		}

		scene.ready = function() {
//			// u.bug("scene.ready", this);

			this._form = u.qs("form", this);
			u.f.init(this._form);
		}

		// scene is ready
		scene.ready();
	}
}
