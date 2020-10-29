Util.Modules["profile"] = new function() {
	this.init = function(scene) {

		scene.resized = function() {
			// u.bug("scene.resized", this);
		}

		scene.scrolled = function() {
			// u.bug("scene.scrolled", this);
		}

		scene.ready = function() {
//			// u.bug("scene.ready", this);
			this.initMembershipBox();
			this.initUserinfoBox();
			this.initPasswordBox();
		}
		
		// Medlemskab box
		scene.initMembershipBox = function() {
			// Query needed elements
			var box_membership = u.qs(".membership > .c-box", this);
			var button_membership = u.qs(".membership li.change-info", this);
			var button_cancel = u.qs(".membership li.cancel-membership", this);

			
			// Query elements for syncing
			var right_panel = u.qs(".c-one-third", this);
			var box_department = u.qs(".department", this);

			// "Ret" button
			if(button_membership) {
				// Create references to scene
				button_membership.scene = this;

				u.clickableElement(button_membership); // Add click event to button and ignore href redirect.
				button_membership.clicked = function() {

					this.response = function(response) {
						// Update request state
						this.is_requesting = false;
						u.rc(this, "loading");

						// Query form to inject and create a reference to scene on it
						var form_department = u.qs(".form_department", response);
						form_department.scene = this.scene;

						// Query elements to use
						var div_fields = u.qs("div.fields", box_membership);
						var divs_membership = u.qsa(".membership-info", div_fields);
						var ul_buttons = u.qs("ul.actions", div_fields);

						// Hide department field and buttons
						u.ass(divs_membership[3], {"display":"none"});
						u.ass(ul_buttons, {"display":"none"});

						// Append form and initialize it
						u.ae(box_membership, form_department);
						u.f.init(form_department);

						// Insert form into fields
						u.ae(div_fields, form_department);

						// Update button
						form_department.submitted = function() {
							var data = this.getData();

							this.response = function(response) {
								// Update request state
								this.is_requesting = false;
								u.rc(this, "loading");
							
								// Replace current fields div with the updated one
								var new_fields = u.qs(".membership .fields", response);
								box_membership.replaceChild(new_fields, div_fields);

								// Replace department box with updated box
								var new_department_box = u.qs(".department", response);
								right_panel.replaceChild(new_department_box, box_department);


								if (message = u.qs("p.message", response)) {
									var fields = u.qs("div.fields", box_membership)
									u.ie(fields, message);
								
									// If previous message didn't finnish deleting
									if (message.t_done) {
										u.t.resetTimer(t_done);
									}

									message.done = function() {
										u.ass(this, {
											"transition":"all .5s ease",
											"transform":"translate3d(0px, -10px, 0px)",
											"opacity":"0"
										});

										u.t.setTimer(this, function() {
											this.parentNode.removeChild(this);
										}, 500);
									}

									message.transitioned = function() {
										this.t_done = u.t.setTimer(this, this.done, 2400);
									}

									// State before animation
									u.ass(message, {
										"color":"#3e8e17",
										"padding-bottom":"5px",
										"transform":"translate3d(0px, -10px, 0px)",
										"opacity":"0"
									});

									// Animate
									u.a.transition(message, "all .5s ease");
									u.ass(message, {
										"transform":"translate3d(0px, 0, 0px)",
										"opacity":"1"
									});
								}

								// Init new box on scene
								this.scene.initMembershipBox();
							}

							// Prevent making the request more than once
							if (!this.is_requesting) {
								// Update request state
								this.is_requesting = true;
								u.ac(this, "loading");
								// Make request
								u.request(this, this.action, {"data":data, "method":"POST"});
							}

						}

						// Cancel button
						form_department.actions["cancel"].clicked = function() {

							this.response = function(response) {
								// Update request state
								this.is_requesting = false;
								u.rc(this, "loading");

								// Replce with non-updated fields
								var new_fields = u.qs(".membership .fields", response);
								box_membership.replaceChild(new_fields, div_fields);

								// "this" is the cancel button.
								//  the "_form" property refers to the inputs form even though its not in HTML form scope.
								this._form.scene.initMembershipBox();
							}

							// Prevent making the request more than once
							if (!this.is_requesting) {
								// Update request state
								this.is_requesting = true;
								u.ac(this, "loading");
								// Make request
								u.request(this, "/profil");
							}

						}
					}

					// Prevent making the request more than once
					if (!this.is_requesting) {
						// Update request state
						this.is_requesting = true;
						u.ac(this, "loading");
						// Make request
						u.request(this, "/profil/afdeling");
					}

				}
			}

			// "Opsig" button
			if(button_cancel) {
				button_cancel.scene = this; 

				u.clickableElement(button_cancel);
				button_cancel.clicked = function() {
					this.scene.overlay = u.overlay({title:"Vil du udmeldes?", height:200,width:600, class:"confirm_cancel_membership"});
					var p_warning = u.ae(this.scene.overlay.div_content, "p", {
						html:"Du er ved at melde dig ud af KBHFF. Er du sikker?"
					});
					var ul_actions = u.ae(this.scene.overlay.div_content, "ul", {
						class:"actions"
					});

					// Add action buttons to cancel and confirm
					var delete_me = u.f.addAction(ul_actions, {"type":"button", "name":"delete_me", "class":"button delete_me","value":"Meld mig ud af KBHFF"});
					var regret = u.f.addAction(ul_actions, {"type":"button", "name":"regret", "class":"button regret primary", "value":"Fortryd udmelding"});

					// Give references to scene on each button
					delete_me.scene = this.scene;
					regret.scene = this.scene;

					// Add click event to go to password confirmation
					u.e.click(delete_me)
					delete_me.clicked = function () {

						// Inject 'confirm cancellation' form
						this.response = function(response) {
							// Update request state
							this.is_requesting = false;
							u.rc(this, "loading");
				
							// Query form to inject
							var form_confirm_cancellation = u.qs(".confirm_cancellation", response);
							form_confirm_cancellation.scene = this.scene;

							// Hide elements to be replaced
							u.ass(p_warning, {"display":"none"});
							u.ass(ul_actions, {"display":"none"});
							// Append form and initialize it
							u.ae(this.scene.overlay.div_content, form_confirm_cancellation);
							u.f.init(form_confirm_cancellation);

							form_confirm_cancellation.submitted = function () {
								var data = this.getData();

								this.response = function(response) {
									// Update request state
									this.is_requesting = false;
									u.rc(this, "loading");
								
									if (response.cms_object == "JS-request") {
										console.log(response);
										location.href = "/";
								
									}
								
									else if (response != "JS-request") {
									
										if (message = u.qs("div.messages", response)) {
											u.ass(this, {"display":"none"})
											console.log(this);
											u.ae(this.scene.overlay.div_content, message);
											var ul_actions = u.ae(this.scene.overlay.div_content, "ul", {
												class:"actions"
											});
										
											var button_close = u.f.addAction(ul_actions, {"type":"button", "name":"button_close", "class":"button button_close primary","value":"Luk"});
											button_close.scene = this.scene;
											u.e.click(button_close)
											button_close.clicked = function () {
												this.scene.overlay.close ();
											}
										}
										else {
											location.href = "/";
										}
									
									}
								
								}
							
								// Prevent making the request more than once
								if (!this.is_requesting) {
									// Update request state
									this.is_requesting = true;
									u.ac(this, "loading");
									// Make request
						
									u.request(this, this.action, {"data":data, "method":"POST", "headers":{"X-Requested-With":"XMLHttpRequest"}});
								}

							}
						}

						// Prevent making the request more than once
						if (!this.is_requesting) {
							// Update request state
							this.is_requesting = true;
							u.ac(this, "loading");
							// Make the request
						
							u.request(this, "/profil/opsig");
						}

					}

					// Close overlay on regret button
					u.e.click(regret)
					regret.clicked = function () {
						this.scene.overlay.close ();
					}
				}
				
			}

		}

		// Brugeroplysninger box
		scene.initUserinfoBox = function() {
			var box_userinfo = u.qs(".user > .c-box", this);
			var button_userinfo = u.qs(".user li", this);

			var intro_header = u.qs(".section.intro > h2", this);
			var span_name = u.qs("span.name", this);

			if(button_userinfo) {
				button_userinfo.scene = this;

				u.clickableElement(button_userinfo);
				button_userinfo.clicked = function() {

					this.response = function(response) {
						// Update request state
						this.is_requesting = false;
						u.rc(this, "loading");

						// Query form and create scene reference on it
						var form_userinfo = u.qs(".form_user", response);
						form_userinfo.scene = this.scene;

						// Query current userinfo content and replace with form
						var div_fields = u.qs("div.fields", box_userinfo);
						box_userinfo.replaceChild(form_userinfo, div_fields);

						// Init form
						u.f.init(form_userinfo);

						// Update button
						form_userinfo.submitted = function() {
							var data = this.getData();

							this.response = function(response) {
								// Update request state
								this.is_requesting = false;
								u.rc(this, "loading");

								// Replace form with updated box
								var div_userinfo = u.qs(".user .fields", response);
								box_userinfo.replaceChild(div_userinfo, form_userinfo);

								// Sync new name in headline
								var new_name = u.qs("span.name", response);
								intro_header.replaceChild(new_name, span_name);

								// Message
								if (message = u.qs("p.message", response)) {
									// Insert message
									var fields = u.qs("div.fields", box_userinfo);
									u.ie(fields, message);

									// If previous message didn't finnish deleting
									if (message.t_done) {
										u.t.resetTimer(t_done);
									}

									message.done = function() {
										u.ass(this, {
											"transition":"all .5s ease",
											"transform":"translate3d(0px, -10px, 0px)",
											"opacity":"0"
										});

										u.t.setTimer(this, function() {
											this.parentNode.removeChild(this);
										}, 500);
									}

									message.transitioned = function() {
										this.t_done = u.t.setTimer(this, this.done, 2400);
									}

									// State before animation
									u.ass(message, {
										"color":"#3e8e17",
										"padding-bottom":"5px",
										"transform":"translate3d(0px, -10px, 0px)",
										"opacity":"0"
									});

									// Animate
									u.a.transition(message, "all .5s ease");
									u.ass(message, {
										"transform":"translate3d(0px, 0, 0px)",
										"opacity":"1"
									});
								}
							
								// Init new box
								this.scene.initUserinfoBox();
							}

							// Prevent making the request more than once
							if (!this.is_requesting) {
								this.is_requesting = true;
								u.ac(this, "loading");
								u.request(this, this.action, {"data":data, "method":"POST"});
							}

						}

						// Cancel button
						form_userinfo.actions["cancel"].clicked = function() {
							this.response = function(response) {
								// Update request state
								this.is_requesting = false;
								u.rc(this, "loading");

								// Replace form with current content
								var div_userinfo = u.qs(".user .fields", response);
								box_userinfo.replaceChild(div_userinfo, form_userinfo);

								// "this" is the cancel button, which has a "form" property that refers to it's form (_form in manipulator).
								this._form.scene.initUserinfoBox();
							}

							// Prevent making the request more than once
							if (!this.is_requesting) {
								this.is_requesting = true;
								u.ac(this, "loading");
								u.request(this, "/profil");
							}

						}
					}

					// Prevent making the request more than once
					if (!this.is_requesting) {
						this.is_requesting = true;
						u.ac(this, "loading");
						u.request(this, "/profil/bruger");
					}

				}

			}

		}

		// Kodeord box
		scene.initPasswordBox = function() {

			var box_password = u.qs(".password > .c-box", this);
			var button_password = u.qs(".password li", this);

			if(button_password) {
				button_password.scene = this;

				u.clickableElement(button_password);
				button_password.clicked = function() {

					this.response = function(response) {
						// Update request state
						this.is_requesting = false;
						u.rc(this, "loading");

						// Query form and create reference to scene
						var form_password = u.qs(".form_password", response);
						form_password.scene = this.scene;

						// Query current static content and replace with form
						var div_fields = u.qs("div.fields", box_password);
						box_password.replaceChild(form_password, div_fields);

						// Init form
						u.f.init(form_password);

						// Update button
						form_password.submitted = function() {
							var data = this.getData();
							this.response = function(response) {
								// Update request state
								this.is_requesting = false;
								u.rc(this, "loading");
							
								// Prevent multiple errors
								if (message = u.qs("p.error", this)) {
									message.parentNode.removeChild(message);
								}

								// in case of error, the message needs to show in the form_password. 
								if (message = u.qs("div.messages > p.error", response)) {
							
									// State before animation
									u.ass(message, {
										"padding-bottom":"5px",
										"transform":"translate3d(0px, -10px, 0px)",
										"opacity":"0"
									});
									// Insert message
									u.ie(this, message);	
							
									// Animate
									u.a.transition(message, "all .5s ease");
									u.ass(message, {
										"transform":"translate3d(0px, 0, 0px)",
										"opacity":"1"
									});
								}

								// Query new static content and replace with current form
								var div_password = u.qs(".password .fields", response);
								box_password.replaceChild(div_password, this);

								// Message needs to show when form_password is replaced with box_password.
								if (message = u.qs("p.message", response)) {
								
									// Insert
									var fields = u.qs("div.fields", box_password);
									u.ie(fields, message);

									// If previous message didn't finnish deleting
									if (message.t_done) {
										u.t.resetTimer(t_done);
									}

									message.done = function() {
										u.ass(this, {
											"transition":"all .5s ease",
											"transform":"translate3d(0px, -10px, 0px)",
											"opacity":"0"
										});

										u.t.setTimer(this, function() {
											this.parentNode.removeChild(this);
										}, 500);
									}

									message.transitioned = function() {
										this.t_done = u.t.setTimer(this, this.done, 2400);
									}

									// State before animation
									u.ass(message, {
										"color":"#3e8e17",
										"padding-bottom":"5px",
										"transform":"translate3d(0px, -10px, 0px)",
										"opacity":"0"
									});

									// Animate
									u.a.transition(message, "all 1s ease");
									u.ass(message, {
										"transform":"translate3d(0px, 0, 0px)",
										"opacity":"1"
									});
								}
							
							
								// Initialize the new passwordbox
								this.scene.initPasswordBox();
							}

							// Prevent making the request more than once
							if (!this.is_requesting) {
								this.is_requesting = true;
								u.ac(this, "loading");
								u.request(this, this.action, {"data":data, "method":"POST"});
							}

						}

						// Cancel button
						form_password.actions["cancel"].clicked = function() {

							this.response = function(response) {
								// Update request state
								this.is_requesting = false;
								u.rc(this, "loading");

								// Get div containing static content
								var div_userinfo = u.qs(".password .fields", response);

								// Replace form with static content
								box_password.replaceChild(div_userinfo, this._form);

								// Initialize the new passwordbox
								this._form.scene.initPasswordBox();
							}

							// Prevent making the request more than once
							if (!this.is_requesting) {
								this.is_requesting = true;
								u.ac(this, "loading");
								u.request(this, "/profil");
							}

						}

					}

					// Prevent making the request more than once
					if (!this.is_requesting) {
						this.is_requesting = true;
						u.ac(this, "loading");
						u.request(this, "/profil/kodeord");
					}

				}

			}

		}

		// scene is ready
		scene.ready();
	}
}
