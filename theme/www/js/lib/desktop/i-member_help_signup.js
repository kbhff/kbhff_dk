Util.Objects["member_help_signup"] = new function() {
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
			var signup_form = u.qs("form.member_help_signup", this);
			if(signup_form) {
				u.f.init(signup_form);
			}

			page.resized();
		}


		// scene is ready
		scene.ready();

	}

}
