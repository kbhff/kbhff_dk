Util.Modules["shop_shift"] = new function() {
	this.init = function(scene) {
		// u.bug("scene init:", scene);


		scene.resized = function() {
			// u.bug("scene.resized:", this);
		}

		scene.scrolled = function() {
			// u.bug("scrolled:", this);
		}

		scene.ready = function() {
			// u.bug("scene.ready:", this);

			var form = u.qs("form.choose_date");
			u.f.init(form);
			form.updated = function() {
				this.submit();
			}
			
		}

		// scene is ready
		scene.ready();

	}

}
