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
		
		// Medlemskab box
		scene.initMembershipBox = function() {
			// Query needed elements
			var box_membership = u.qs(".membership > .c-box", this);
			var button_membership = u.qs(".membership li.change-info", this);
			button_membership.scene = this; // Create reference to scene
			var button_cancel = u.qs(".membership li.cancel-membership", this);
			// Query elements for syncing
			var right_panel = u.qs(".c-one-third", this);
			var box_department = u.qs(".department", this);

			// "Ret" button
			u.clickableElement(button_membership); // Add click event to button and ignore href redirect.
			button_membership.clicked = function() {

				this.response = function(response) {
					// Update request state
					this.is_requesting = false;
					u.rc(this, "loading");

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
							// Update request state
							this.is_requesting = false;
							u.rc(this, "loading");
							
							// Replace form
							var div_membership = u.qs(".membership .fields", response);
							box_membership.replaceChild(div_membership, form_department);

							// Replace department box with updated box
							var new_department_box = u.qs(".department", response);
							right_panel.replaceChild(new_department_box, box_department);

							// Init new box on scene
							this.scene.initMembershipBox();
						}

						// Prevent double requesting
						if (!this.is_requesting) {
							this.is_requesting = true;
							u.ac(this, "loading");
							u.request(this, this.action, {"data":data, "method":"POST"});
						}

					}

					// Cancel button
					form_department.actions["cancel"].clicked = function() {

						this.response = function(response) {
							// Update request state
							this.is_requesting = false;
							u.rc(this, "loading");

							var div_membership = u.qs(".membership .fields", response);
							box_membership.replaceChild(div_membership, form_department);
							form_department.scene.initMembershipBox(); // this.scene not working from here?
						}

						// Prevent double requesting
						if (!this.is_requesting) {
							this.is_requesting = true;
							u.ac(this, "loading");
							u.request(this, "/profil");
						}

					}
				}

				// Prevent double requesting
				if (!this.is_requesting) {
					this.is_requesting = true;
					u.ac(this, "loading");
					u.request(this, "/profil/afdeling");
				}

			}

			// "Opsig" button
			u.clickableElement(button_cancel);
			button_cancel.clicked = function() {
				var overlay = u.overlay({title:"Vil du udmeldes?", height:200,width:600, class:"confirm_cancel_membership"});
				var p_warning = u.ae(overlay.div_content, "p", {
					html:"Du er ved at melde dig ud af KBHFF. Er du sikker?"
				});
				var ul_actions = u.ae(overlay.div_content, "ul", {
					class:"actions"
				})

				// Add action buttons to cancel and confirm
				var delete_me = u.f.addAction(ul_actions, {"type":"button", "name":"delete_me", "class":"button delete_me","value":"Meld mig ud af KBHFF"});
				var regret = u.f.addAction(ul_actions, {"type":"button", "name":"regret", "class":"button regret primary", "value":"Fortryd udmelding"});
				
				// Add click event to go to password confirmation
				u.e.click(delete_me)
				delete_me.clicked = function () {

					// Inject 'confirm cancellation' form
					this.response = function(response) {
						this.is_requesting = false;
						u.rc(this, "loading");

						// Query form to inject
						var form_confirm_cancellation = u.qs(".confirm_cancellation", response);

						// Hide elements to be replaced
						u.ass(p_warning, {"display":"none"});
						u.ass(ul_actions, {"display":"none"});
						// Append form and initialize it
						u.ae(overlay.div_content, form_confirm_cancellation);
						u.f.init(form_confirm_cancellation);
						//u.f.addAction(overlay.div_content, {"type":"button", "name":"regret", "class":"action regret primary", "value":"Fortryd udmelding"});

						form_confirm_cancellation.submitted = function () {
							var data = u.f.getParams(this);

							this.response = function(response) {
								this.is_requesting = false;
								u.rc(this, "loading");

								var div_scene_login = u.qs("div.scene.login", response);
								console.log(div_scene_login);
								if (div_scene_login) {
									location.href = "/";
								}
								else {
									var error_message = u.qs("p.errormessage", response);
									u.ass(form_confirm_cancellation, {"display":"none"})
									
									
									u.ae(overlay.div_content, error_message);
									var ul_actions = u.ae(overlay.div_content, "ul", {
										class:"actions"
									});
									var button_close = u.f.addAction(ul_actions, {"type":"button", "name":"button_close", "class":"button button_close primary","value":"Luk"});

									u.e.click(button_close)
									button_close.clicked = function () {
										overlay.close ();
									}
								}
							}
							
							if (!this.is_requesting) {
								this.is_requesting = true;
								u.ac(this, "loading");
								u.request(this, this.action, {"data":data, "method":"POST"});
							}

						}
					}

					if (!this.is_requesting) {
						this.is_requesting = true;
						u.ac(this, "loading");
						u.request(this, "/profil/opsig");
					}

				}
				// Add click event to cancel and close overlay
				u.e.click(regret)
				regret.clicked = function () {
					overlay.close ();
				}
			}

		}

		// Brugeroplysninger box
		scene.initUserinfoBox = function() {
			var box_userinfo = u.qs(".user > .c-box", this);
			var button_userinfo = u.qs(".user li", this);
			button_userinfo.scene = this;
			var intro_header = u.qs(".section.intro > h2", this);
			var span_name = u.qs("span.name", this);

			u.clickableElement(button_userinfo);
			button_userinfo.clicked = function() {

				this.response = function(response) {
					this.is_requesting = false;
					u.rc(this, "loading");

					var form_userinfo = u.qs(".form_user", response);
					form_userinfo.scene = this.scene;
					var div_fields = u.qs("div.fields", box_userinfo);

					box_userinfo.replaceChild(form_userinfo, div_fields);
					u.f.init(form_userinfo);

					// Update button
					form_userinfo.submitted = function() {
						var data = u.f.getParams(this);

						this.response = function(response) {
							this.is_requesting = false;
							u.rc(this, "loading");

							// Replace form with updated box
							var div_userinfo = u.qs(".user .fields", response);
							box_userinfo.replaceChild(div_userinfo, form_userinfo);

							//sync name update
							var new_name = u.qs("span.name", response);
							intro_header.replaceChild(new_name, span_name);

							// Init new box
							form_userinfo.scene.initUserinfoBox();
						}

						if (!this.is_requesting) {
							this.is_requesting = true;
							u.ac(this, "loading");
							u.request(this, this.action, {"data":data, "method":"POST"});

						}

					}

					// Cancel button
					form_userinfo.actions["cancel"].clicked = function() {
						this.response = function(response) {
							this.is_requesting = false;
							u.rc(this, "loading");

							// Replace form with box
							var div_userinfo = u.qs(".user .fields", response);
							box_userinfo.replaceChild(div_userinfo, form_userinfo);
							form_userinfo.scene.initUserinfoBox();
						}

						if (!this.is_requesting) {
							this.is_requesting = true;
							u.ac(this, "loading");
							u.request(this, "/profil");
						}

					}
				}

				if (!this.is_requesting) {
					this.is_requesting = true;
					u.ac(this, "loading");
					u.request(this, "/profil/bruger");
				}

			}
		
		}

		// Kodeord box
		scene.initPasswordBox = function() {
			var box_password = u.qs(".password > .c-box", this);
			var button_password = u.qs(".password li", this);
			button_password.scene = this;

			u.clickableElement(button_password);
			button_password.clicked = function() {

				this.response = function(response) {
					this.is_requesting = false;
					u.rc(this, "loading");

					var form_password = u.qs(".form_password", response);
					form_password.scene = this.scene;
					var div_fields = u.qs("div.fields", box_password);

					box_password.replaceChild(form_password, div_fields);
					u.f.init(form_password);

					// Update button
					form_password.submitted = function() {
						var data = u.f.getParams(this);

						this.response = function(response) {
							this.is_requesting = false;
							u.rc(this, "loading");

							var div_password = u.qs(".password .fields", response);
							box_password.replaceChild(div_password, form_password);
							this.scene.initPasswordBox();
						}

						if (!this.is_requesting) {
							this.is_requesting = true;
							u.ac(this, "loading");
							u.request(this, this.action, {"data":data, "method":"POST"});
						}

					}

					// Cancel button
					form_password.actions["cancel"].clicked = function() {

						this.response = function(response) {
							this.is_requesting = false;
							u.rc(this, "loading");

							var div_userinfo = u.qs(".password .fields", response);
							box_password.replaceChild(div_userinfo, form_password);
							form_password.scene.initPasswordBox();
						}

						if (!this.is_requesting) {
							this.is_requesting = true;
							u.ac(this, "loading");
							u.request(this, "/profil");
						}

					}

				}

				if (!this.is_requesting) {
					this.is_requesting = true;
					u.ac(this, "loading");
					u.request(this, "/profil/kodeord");
				}

			}
		}

		// scene is ready
		scene.ready();
	}
}
