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

			u.clickableElement(button_membership);
			button_membership.clicked = function() {
				this.membership_callback = function(response) {
					var form_membership = u.qs(".form_department", response);
					var div_fields = u.qs("div.fields", box_membership);

					u.ass(div_fields, {"display":"none"});
					u.ae(box_membership, form_membership);
					u.f.init(form_membership);
				}
				u.addClass(this, "disabled")
				u.request(this, "/profil/afdeling", {"callback":"membership_callback"});
			}

			u.clickableElement(button_userinfo);
			button_userinfo.clicked = function() {
				this.userinfo_callback = function(response) {
					var form_userinfo = u.qs(".form_user", response);
					var div_fields = u.qs("div.fields", box_userinfo);

					u.ass(div_fields, {"display":"none"});
					u.ae(box_userinfo, form_userinfo);
					u.f.init(form_userinfo);
				}
				u.addClass(this, "disabled")
				u.request(this, "/profil/bruger", {"callback":"userinfo_callback"});
			}

			u.clickableElement(button_password);
			button_password.clicked = function() {
				this.password_callback = function(response) {
					var form_password = u.qs(".form_password", response);
					var div_fields = u.qs("div.fields", box_password);

					u.ass(div_fields, {"display":"none"});
					u.ae(box_password, form_password);
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
