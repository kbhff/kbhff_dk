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
			this.initMembershipBox();
			this.initUserinfoBox();
			this.initPasswordBox();

		}
		
		// Medlemsskab box
		scene.initMembershipBox = function() {
			// Query needed elements
			var box_membership = u.qs(".membership > .c-box", this);
			var button_membership = u.qs(".membership li.change-info", this);
			button_membership.scene = this; // Create reference to scene
			
			// Add click event to button and ignore href redirect.
			u.clickableElement(button_membership);

			button_membership.clicked = function() {
				this.membership_callback = function(response) {
					// Query form to inject
					var form_department = u.qs(".form_department", response);
					form_department.scene = this.scene; // Create reference to scene

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

					// Update button
					form_department.submitted = function() {
						var data = u.f.getParams(this);
						this.response = function(response) {
							var div_membership = u.qs(".membership .fields", response);
							box_membership.replaceChild(div_membership, form_department);
							this.scene.initMembershipBox(); // Run and query on scene
						}
						u.request(this, this.action, {"data":data, "method":"POST"});
					}
				}
				// u.addClass(this, "disabled")
				u.request(this, "/profil/afdeling", {"callback":"membership_callback"});
			}
		}

		// Brugeroplysninger box
		scene.initUserinfoBox = function() {
			var box_userinfo = u.qs(".user > .c-box", this);
			var button_userinfo = u.qs(".user li", this);
			button_userinfo.scene = this;

			u.clickableElement(button_userinfo);
			button_userinfo.clicked = function() {
				this.userinfo_callback = function(response) {
					var form_userinfo = u.qs(".form_user", response);
					form_userinfo.scene = this.scene;
					var div_fields = u.qs("div.fields", box_userinfo);

					box_userinfo.replaceChild(form_userinfo, div_fields);
					u.f.init(form_userinfo);

					// Update button
					form_userinfo.submitted = function() {
						var data = u.f.getParams(this);

						this.response = function(response) {
							var div_userinfo = u.qs(".user .fields", response);
							box_userinfo.replaceChild(div_userinfo, form_userinfo);
							this.scene.initUserinfoBox();
						}
						u.request(this, this.action, {"data":data, "method":"POST"});
					}

					// Cancel button
					form_userinfo.actions["cancel"].clicked = function() {
						this.response = function(response) {
							var div_userinfo = u.qs(".user .fields", response);
							box_userinfo.replaceChild(div_userinfo, form_userinfo);
							form_userinfo.scene.initUserinfoBox();
						}
						u.request(this, "/profil");
					}
				}
				u.request(this, "/profil/bruger", {"callback":"userinfo_callback"});
			}
		}

		// Kodeord box
		scene.initPasswordBox = function() {
			var box_password = u.qs(".password > .c-box", this);
			var button_password = u.qs(".password li", this);
			button_password.scene = this;

			u.clickableElement(button_password);
			button_password.clicked = function() {
				this.password_callback = function(response) {
					var form_password = u.qs(".form_password", response);
					form_password.scene = this.scene;
					var div_fields = u.qs("div.fields", box_password);

					box_password.replaceChild(form_password, div_fields);
					u.f.init(form_password);

					// Update button
					form_password.submitted = function() {
						var data = u.f.getParams(this);

						this.response = function(response) {
							var div_password = u.qs(".password .fields", response);
							box_password.replaceChild(div_password, form_password);
							this.scene.initPasswordBox();
						}
						u.request(this, this.action, {"data":data, "method":"POST"});
					}
				}
				u.request(this, "/profil/kodeord", {"callback":"password_callback"});
			}
		}

		// scene is ready
		scene.ready();
	}
}
