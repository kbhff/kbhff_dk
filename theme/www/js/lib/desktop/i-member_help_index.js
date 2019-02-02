Util.Objects["member_help"] = new function() {
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
			var search_form = u.qs("form.search_user", this);
			search_form.scene = this;
	
			if(search_form) {
				u.f.init(search_form);
			}
			
			

			

			page.resized();
		}


		// scene is ready
		scene.ready();

	}

}
