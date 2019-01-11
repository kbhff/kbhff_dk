Util.Objects["accept_terms"] = new function() {
	this.init = function(scene) {

		scene.resized = function() {
//			u.bug("scene.resized:" + u.nodeId(this));
		}

		scene.scrolled = function() {
//			u.bug("scene.scrolled:" + u.nodeId(this))
		}

		scene.ready = function() {
			u.bug("scene.ready:" + u.nodeId(this));
			// query and initialize form
			var form_accept = u.qs("form.accept", this);
			u.f.init(form_accept);
			// update text for checkbox label and hint messages
			u.qs("div.field.checkbox label").innerHTML = "Jeg accepterer <a href='/terms' target='_blank'>retningslinjerne</a>."
			u.qs("div.field.checkbox .error").innerHTML = "Du skal acceptere retningslinjerne for at fortsætte."
			u.qs("div.field.checkbox .hint").innerHTML = ""
			// add click event to reject-button. Creates overlay with information text and action ul
			form_accept.actions["reject"].clicked = function() {
				var overlay = u.overlay({title:"Vil du udmeldes?", height:200,width:600, class:"confirm_cancel_membership"});
				var p_warning = u.ae(overlay.div_content, "p", {
					html:"Du er ved at melde dig ud af KBHFF. Pga. lovgivning og hensyn til persondata kan du ikke være medlem af KBHFF uden at acceptere vores vilkår. Vi håber du vil genoverveje."
				});
				var ul_actions = u.ae(overlay.div_content, "ul", {
					class:"actions"
				})

				// Add action buttons to cancel and confirm
				// Should we change the css-classes of the buttons so they are compliant with the skin?
				var delete_me = u.f.addAction(ul_actions, {"type":"button", "name":"delete_me", "class":"button delete_me","value":"Meld mig ud af KBHFF"});
				var regret = u.f.addAction(ul_actions, {"type":"button", "name":"regret", "class":"button regret primary", "value":"Fortryd udmelding"});
				
				// Add click event to go to password confirmation
				u.e.click(delete_me)
				delete_me.clicked = function () {
					// Inject 'confirm cancellation' form
					this.delete_me_callback = function(response) {
					
						// Query form to inject
						var form_confirm_cancellation = u.qs(".confirm_cancellation", response);

						// Hide elements to be replaced
						u.ass(p_warning, {"display":"none"});
						u.ass(ul_actions, {"display":"none"});
						// Append form and initialize it
						u.ae(overlay.div_content, form_confirm_cancellation);
						u.f.init(form_confirm_cancellation);
						//u.f.addAction(overlay.div_content, {"type":"button", "name":"regret", "class":"action regret primary", "value":"Fortryd udmelding"});
						
						// Go to login when confirm_cancellation is submitted. Else hide form and show error message.
						form_confirm_cancellation.submitted = function () {
							var data = u.f.getParams(this);
							this.response = function(response) {
								console.log(response);
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
							
							u.request(this, this.action, {
								"data":data,
								"method":"POST"
							})
						}
					}
	
					u.request(this, "/profil/opsig", {
						"callback":"delete_me_callback"
					});
				}
				// Add click event to cancel and close overlay
				u.e.click(regret)
				regret.clicked = function () {
						overlay.close ();
				}
			}
		}

		// scene is ready
		scene.ready();
	}
}
