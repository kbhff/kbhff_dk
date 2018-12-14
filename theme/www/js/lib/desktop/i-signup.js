Util.Objects["signup"] = new function() {
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

			var signup_form = u.qs("form.signup", this);
			var place_holder = u.qs("div.articlebody .placeholder.signup", this);

			if(signup_form && place_holder) {
				place_holder.parentNode.replaceChild(signup_form, place_holder);
			}

			if(signup_form) {
				u.f.init(signup_form);
			}

			// // accept cookies?
			// page.acceptCookies();

			page.resized();
		}


		// scene is ready
		scene.ready();

	}

}
