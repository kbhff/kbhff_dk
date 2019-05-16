Util.Objects["product_new"] = new function() {
	this.init = function(scene) {
//		u.bug("scene init:", scene);


		scene.resized = function() {
//			u.bug("scene.resized:", this);
		}

		scene.scrolled = function() {
//			u.bug("scrolled:", this);
		}

		scene.ready = function() {
//			u.bug("scene.ready:", this);

			page.cN.scene = this;

			// initialize signup form
			var signup_form = u.qs("form.product_new", this);
			if(signup_form) {
				u.f.init(signup_form);
			}

			page.resized();
		}


		// scene is ready
		scene.ready();

	}

}
