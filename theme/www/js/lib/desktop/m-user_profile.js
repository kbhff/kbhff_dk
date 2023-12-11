Util.Modules["user_profile"] = new function() {
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
			this.initRenewalBox();
			this.initOrderList();
		}
		
		// Medlemskab box
		scene.initMembershipBox = function() {
			// Query needed elements
			var box_membership = u.qs(".membership > .c-box", this);
			var button_membership = u.qs(".membership li.change-membership", this);
			var button_cancel = u.qs(".membership li.cancel-membership", this);
			var button_department = u.qs(".membership li.change-department", this);

			var section_user_group = u.qs(".section.user_group");

			// Query elements for syncing
			var right_panel = u.qs(".c-one-third", this);


			if(button_department) {

				// Create references to scene
				button_department.scene = this;

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
						var warning = u.qs("p.warning", response);

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

						if(warning) {
							u.ae(div_fields, warning);
						}
				
						// Update button
						form_department.submitted = function() {
							var data = this.getData();

							this.response = function(response) {
								// Update request state
								this.is_requesting = false;
								u.rc(this, "loading");
					
								// Replace form
								var div_membership = u.qs(".membership .fields", response);
							
								box_membership.replaceChild(div_membership, form_department);
							
								message = u.qs("div.messages", response);
								if (message) {
							
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

			}

			if(button_membership) {

				button_membership.scene = this;

				// "Ret Medlemskab" button
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
							var data = this.getData();

							this.response = function(response) {
								// Update request state
								this.is_requesting = false;
								u.rc(this, "loading");
					
								// Replace form
								var div_membership = u.qs(".membership .fields", response);
							
								box_membership.replaceChild(div_membership, form_membership);
							
								var new_section_user_group = u.qs(".section.user_group", response);
								section_user_group.parentNode.replaceChild(new_section_user_group, section_user_group);
							
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

			}


			if(button_cancel) {

				button_cancel.scene = this; 


				// "Opsig" button
				u.clickableElement(button_cancel);
			
				button_cancel.clicked = function() {
				
					this.scene.url = this.url;
					console.log(this.scene.url);
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
							
								var data = this.getData();
						
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
							u.request(this, this.scene.url);
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

		// Renewal box
		scene.initRenewalBox = function() {

			var box_renewal = u.qs(".renewal > .c-box", this);
			var button_renewal = u.qs(".renewal li", this);

			if(button_renewal) {
				button_renewal.scene = this;

				u.clickableElement(button_renewal);
				button_renewal.clicked = function() {

					this.response = function(response) {
						// Update request state
						this.is_requesting = false;
						u.rc(this, "loading");

						// Query form and create reference to scene
						var form_renewal = u.qs(".form_renewal", response);
						form_renewal.scene = this.scene;

						// Query current static content and replace with form
						var div_fields = u.qs("div.fields", box_renewal);
						box_renewal.replaceChild(form_renewal, div_fields);

						// Init form
						u.f.init(form_renewal);

						// Update button
						form_renewal.submitted = function() {
							var data = this.getData();
							this.response = function(response, request_id) {
								// Update request state
								this.is_requesting = false;
								u.rc(this, "loading");
							
								// Prevent multiple errors
								if (message = u.qs("p.error", this)) {
									message.parentNode.removeChild(message);
								}

								// in case of error, the message needs to show in the form_renewal. 
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
								var div_renewal = u.qs(".renewal .fields", response);
								if(div_renewal) {

									box_renewal.replaceChild(div_renewal, this);

									// Message needs to show when form_renewal is replaced with box_renewal.
									if (message = u.qs("p.message", response)) {
								
										// Insert
										var fields = u.qs("div.fields", box_renewal);
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
									this.scene.initRenewalBox();

								}
								else {

									if(this[request_id].request_url != this[request_id].response_url) {
										location.href = this[request_id].response_url;
									}

								}

							}

							// Prevent making the request more than once
							if (!this.is_requesting) {
								this.is_requesting = true;
								u.ac(this, "loading");
								u.request(this, this.action, {"data":data, "method":"POST"});
							}

						}

						// Cancel button
						form_renewal.actions["cancel"].clicked = function() {

							this.response = function(response) {
								// Update request state
								this.is_requesting = false;
								u.rc(this, "loading");

								// Get div containing static content
								var div_userinfo = u.qs(".renewal .fields", response);

								// Replace form with static content
								box_renewal.replaceChild(div_userinfo, this._form);

								// Initialize the new passwordbox
								this._form.scene.initRenewalBox();
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

		}


		// Order list
		scene.initOrderList = function() {

			var orders = u.qsa("li.order_item", this);
			var i, order;
			for(i = 0; i < orders.length; i++) {
				
				order = orders[i];

				order.order_item_id = u.cv(order, "order_item_id");
				order.span_pickupdate = u.qs("span.pickupdate", order);
				order.span_date = u.qs("span.date", order.span_pickupdate);
				order.bn_edit = u.qs("li.change a.button:not(.disabled)", order);

				if(order.bn_edit) {

					order.bn_edit.order = order;

					u.ce(order.bn_edit);
					order.bn_edit.clicked = function() {

						if(u.hc(this.order, "edit")) {

							this.form.submit();

						}
						else {
							u.ac(this.order, "edit");

							this.response = function(response) {

								this.form = u.qs("form", response);
								if(this.form) {

									this.form.order = this.order;

									this.innerHTML = "Gem";
									u.ac(this, "primary");
									u.ae(this.order.span_pickupdate, this.form);
									u.f.init(this.form);

									this.form.submitted = function() {

										this.response = function(response) {
											this.order.bn_edit.form.parentNode.removeChild(this.order.bn_edit.form);
											delete this.order.bn_edit.form;

											this.order.span_date.innerHTML = u.qs("span.date", u.ge("order_item_id:"+this.order.order_item_id, response)).innerHTML;

											u.rc(this.order, "edit");
											this.order.bn_edit.innerHTML = "Ret";
											u.rc(this.order.bn_edit, "primary");
										}

										u.request(this, this.action, {"method":"post", "data":this.getData()});

									}

								}

							}

							u.request(this, this.url);
						}
					}
				}

			}

		}

		// scene is ready
		scene.ready();
	}
}
