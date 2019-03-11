Util.Objects["user_profile"] = new function() {
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
			var button_membership = u.qs(".membership li.change-membership", this);
			var button_cancel = u.qs(".membership li.cancel-membership", this);
			var button_department = u.qs(".membership li.change-department", this);
			// Create references to scene
			button_membership.scene = this;
			button_cancel.scene = this; 
			button_department.scene = this;
			
			// Query elements for syncing
			var right_panel = u.qs(".c-one-third", this);
		
			// "Ret Afdeling" button
			u.clickableElement(button_department); // Add click event to button and ignore href redirect.
			button_department.clicked = function() {

				this.response = function(response) {
					// Update request state
					this.is_requesting = false;
					u.rc(this, "loading");

					// Query form to inject and create a reference to scene on it
					var form_department = u.qs(".form_department", response);
					form_department.scene = this.scene;

					// Query elements to use
					var form_fieldset = u.qs("fieldset", form_department);
					var div_fields = u.qs("div.fields", box_membership);
					var divs_membership = u.qsa(".membership-info", div_fields)	;
					var ul_buttons = u.qs("ul.actions", div_fields);

					// Hide department field and buttons
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
							
							
							if (message = u.qs("div.messages", response)) {
							
								u.ie(box_membership, message);
								
								message.transitioned = function() {
									message.innerHTML = "";
								}

								u.a.transition(message, "all 4s ease-in");
								u.a.opacity(message, 0.5);	
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
							// Query membershipbox and replace form
							var div_membership = u.qs(".membership .fields", response);
							box_membership.replaceChild(div_membership, this._form);

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
							u.request(this, this.baseURI);
						}
				
					}
				}

				// Prevent making the request more than once
				if (!this.is_requesting) {
					// Update request state
					this.is_requesting = true;
					u.ac(this, "loading");
					// Make request
					u.request(this, this.url);
				}

			}
			
			// "Ret Medlemsskab" button
			u.clickableElement(button_membership); // Add click event to button and ignore href redirect.
			button_membership.clicked = function() {

				this.response = function(response) {
					// Update request state
					this.is_requesting = false;
					u.rc(this, "loading");

					// Query form to inject and create a reference to scene on it
					var form_membership = u.qs(".form_membership", response);
					form_membership.scene = this.scene;

					// Query elements to use
					var form_fieldset = u.qs("fieldset", form_membership);
					var div_fields = u.qs("div.fields", box_membership);
					var divs_membership = u.qsa(".membership-info", div_fields)	;
					var ul_buttons = u.qs("ul.actions", div_fields);

					// Hide department field and buttons
					u.ass(divs_membership[2], {"display":"none"});
					u.ass(ul_buttons, {"display":"none"});

					// Append form and initialize it
					u.ae(box_membership, form_membership);
					u.f.init(form_membership);

					// Insert fields into form
					u.ie(form_membership, div_fields);
				
					// Move select into leftover field spot
					div_fields.insertBefore(form_fieldset, divs_membership[1].nextSibling);
					// u.ae(div_fields, form_fieldset);
				
					// Update button
					form_membership.submitted = function() {
						var data = u.f.getParams(this);

						this.response = function(response) {
							// Update request state
							this.is_requesting = false;
							u.rc(this, "loading");
					
							// Replace form
							var div_membership = u.qs(".membership .fields", response);
							
							box_membership.replaceChild(div_membership, form_membership);
							
							
							if (message = u.qs("div.messages", response)) {
							
								u.ie(box_membership, message);
								
								message.transitioned = function() {
									message.innerHTML = "";
								}

								u.a.transition(message, "all 4s ease-in");
								u.a.opacity(message, 0.5);	
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
					form_membership.actions["cancel"].clicked = function() {

						this.response = function(response) {
							// Update request state
							this.is_requesting = false;
							u.rc(this, "loading");
							// Query membershipbox and replace form
							var div_membership = u.qs(".membership .fields", response);
							box_membership.replaceChild(div_membership, this._form);

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
							u.request(this, this.baseURI);
						}
				
					}
				}

				// Prevent making the request more than once
				if (!this.is_requesting) {
					// Update request state
					this.is_requesting = true;
					u.ac(this, "loading");
					// Make request
					u.request(this, this.url);
				}

			}


			// "Opsig" button
			u.clickableElement(button_cancel);
			
			button_cancel.clicked = function() {
				
				this.scene.action_url = this.url;
				
				this.scene.overlay = u.overlay({title:"Du er ved at udmelde et medlem.", height:200,width:600, class:"confirm_cancel_membership"});
				var p_warning = u.ae(this.scene.overlay.div_content, "p", {
					html:"Du er ved at melde et medlem ud af KBHFF. Er du sikker?"
				});
				var ul_actions = u.ae(this.scene.overlay.div_content, "ul", {
					class:"actions"
				});
				
				// Add action buttons to cancel and confirm
				var delete_me = u.f.addAction(ul_actions, {"type":"button", "name":"delete_me", "class":"button delete_me","value":"Meld medlemmet ud af KBHFF"});
				var regret = u.f.addAction(ul_actions, {"type":"button", "name":"regret", "class":"button regret primary", "value":"Fortryd udmelding"});

				// Give references to scene on each button
				delete_me.scene = this.scene;
				regret.scene = this.scene;

				// Add click event to go to confirmation
				u.e.click(delete_me)
				delete_me.clicked = function () {
				
					// Inject 'confirm cancellation' form
					this.response = function(response) {
						// Update request state
						this.is_requesting = false;
						u.rc(this, "loading");

						// Query form to inject
						var confirm_cancellation = u.qs(".scene.delete_user_information", response);
						confirm_cancellation.scene = this.scene;

						// Hide elements to be replaced
						u.ass(this.scene.overlay.div_header.h2, {"display":"none"});
						u.ass(p_warning, {"display":"none"});
						u.ass(ul_actions, {"display":"none"});
						// Append form and initialize it
						u.ae(this.scene.overlay.div_content, confirm_cancellation);
						var form_confirm_cancellation = u.qs("form.confirm_cancellation");
						form_confirm_cancellation.scene = this.scene;
						u.f.init(form_confirm_cancellation);

						form_confirm_cancellation.submitted = function () {
							
							var data = u.f.getParams(this);
						
							this.response = function(response) {
								// Update request state
								this.is_requesting = false;
								u.rc(this, "loading");
								
								if (response.cms_object == "JS-request") {
									
									location.href = "/medlemshjaelp";
								}
								
								else if (response.cms_object != "JS-request") {
									
									if (message = u.qs("div.messages", response)) {
										u.ass(this, {"display":"none"})
										
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
										location.href = "/medlemshjaelp";
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
						u.request(this, this.scene.action_url);
					}

				}

				// Close overlay on regret button
				u.e.click(regret)
				regret.clicked = function () {
					this.scene.overlay.close ();
				}
			}

		}

		// Brugeroplysninger box
		scene.initUserinfoBox = function() {
			var box_userinfo = u.qs(".user > .c-box", this);
			var button_userinfo = u.qs(".user li", this);
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
						var data = u.f.getParams(this);
						
						this.response = function(response) {
							// Update request state
							this.is_requesting = false;
							u.rc(this, "loading");

							// Replace form with updated box
							var div_userinfo = u.qs(".user .fields", response);
							box_userinfo.replaceChild(div_userinfo, form_userinfo);

							if (message = u.qs("div.messages", response)) {

								u.ie(box_userinfo, message);
								
								message.transitioned = function() {
									message.innerHTML = "";
								}

								u.a.transition(message, "all 4s ease-in");
								u.a.opacity(message, 0.5);	
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
							u.request(this, this.url);
						}

					}
				}

				// Prevent making the request more than once
				if (!this.is_requesting) {
					this.is_requesting = true;
					u.ac(this, "loading");
					u.request(this, this.url);
				}

			}
		
		}

		// scene is ready
		scene.ready();
	}
}
