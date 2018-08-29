Util.Objects["profile"] = new function() {
	this.init = function(scene) {

		scene.resized = function() {
//			u.bug("scene.resized:" + u.nodeId(this));
		}

		scene.scrolled = function() {
//			u.bug("scene.scrolled:" + u.nodeId(this))
		}

		scene.ready = function() {
//			u.bug("scene.ready:" + u.nodeId(this));
			var box_membership = u.qs(".membership > .c-box", this);
			var button_membership = u.qs(".membership li.change-info", this);
			var box_userinfo = u.qs(".user > .c-box", this);
			var button_userinfo = u.qs(".user li", this);
			var box_password = u.qs(".password > .c-box", this);
			var button_password = u.qs(".password li", this);

			// Medlemsskab box
			u.clickableElement(button_membership);
			button_membership.clicked = function() {
				this.membership_callback = function(response) {
					// Query form to inject
					var form_department = u.qs(".form_department", response);
					var form_fieldset = u.qs("fieldset", form_department);
					// Query elements to use
					var div_fields = u.qs("div.fields", box_membership);
					var divs_membership = u.qsa(".membership-info", div_fields);
					var ul_buttons = u.qs("ul.actions", div_fields);

					// Hide fields to be replaced
					u.ass(divs_membership[3], {"display":"none"});
					u.ass(ul_buttons, {"display":"none"});

					// Append form and initialize it
					u.ae(box_membership, form_department);
					u.f.init(form_department);

					// Insert fields into form
					u.ie(form_department, div_fields);

					// Move select into leftover field spot
					u.ae(div_fields, form_fieldset);
				}
				u.addClass(this, "disabled")
				u.request(this, "/profil/afdeling", {"callback":"membership_callback"});
			}

			// Brugeroplysninger box
			u.clickableElement(button_userinfo);
			button_userinfo.clicked = function() {
				this.userinfo_callback = function(response) {
					var form_userinfo = u.qs(".form_user", response);
					var div_fields = u.qs("div.fields", box_userinfo);

					box_userinfo.replaceChild(form_userinfo, div_fields);
					u.f.init(form_userinfo);
				}
				u.addClass(this, "disabled")
				u.request(this, "/profil/bruger", {"callback":"userinfo_callback"});
			}

			// Kodeord box
			u.clickableElement(button_password);
			button_password.clicked = function() {
				this.password_callback = function(response) {
					var form_password = u.qs(".form_password", response);
					var div_fields = u.qs("div.fields", box_password);

					box_password.replaceChild(form_password, div_fields);
					u.f.init(form_password);
				}
				u.addClass(this, "disabled")
				u.request(this, "/profil/kodeord", {"callback":"password_callback"});
			}

			/*
			u.e.addEvent(button_userinfo, "onclick", button_userinfo.onclick);
			button_userinfo.onclick = function() {}
			*/

		}

		// scene is ready
		scene.ready();
	}
}
