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
			// Query and initialize form
			var form_accept = u.qs("form.accept", this);
			form_accept.scene = this;
			u.f.init(form_accept);
	
			// Add click event to reject-button and create overlay 
			form_accept.actions["reject"].clicked = function() {
				this._form.scene.overlay = u.overlay({title:"Vil du udmeldes?", height:200,width:600, class:"confirm_cancel_membership"});
				var p_warning = u.ae(this._form.scene.overlay.div_content, "p", {
					html:"Du er ved at melde dig ud af KBHFF. Pga. lovgivning og hensyn til persondata kan du ikke være medlem af KBHFF uden at acceptere vores vilkår. Vi håber du vil genoverveje."
				});
				var ul_actions = u.ae(this._form.scene.overlay.div_content, "ul", {
					class:"actions"
				})

				// Add action buttons to cancel and confirm
				var delete_me = u.f.addAction(ul_actions, {"type":"button", "name":"delete_me", "class":"button","value":"Meld mig ud af KBHFF"});
				var regret = u.f.addAction(ul_actions, {"type":"button", "name":"regret", "class":"button primary", "value":"Fortryd udmelding"});
				
				// Add click event to go to password confirmation
				u.e.click(delete_me)
				delete_me.scene = this._form.scene;
				delete_me.clicked = function () {
					// Inject 'confirm cancellation' form
					this.response = function(response) {
					
						// Query form to inject
						var form_confirm_cancellation = u.qs(".confirm_cancellation", response);
						form_confirm_cancellation.scene = this.scene;
						
						// Hide elements to be replaced
						u.ass(p_warning, {"display":"none"});
						u.ass(ul_actions, {"display":"none"});
						// Append form and initialize it
						u.ae(this.scene.overlay.div_content, form_confirm_cancellation);
						u.f.init(form_confirm_cancellation);
						
						// Go to login when confirm_cancellation is submitted. Else hide form and show error message.
						form_confirm_cancellation.submitted = function () {
							var data = u.f.getParams(this);
							this.response = function(response) {
								var div_scene_login = u.qs("div.scene.login", response);
								if (div_scene_login) {
									location.href = "/";
								}
								else {
									var error_message = u.qs("p.errormessage", response);
									u.ass(this, {"display":"none"})
									u.ae(this.scene.overlay.div_content, error_message);
									var ul_actions = u.ae(this.scene.overlay.div_content, "ul", {
										class:"actions"
									});
									
									// Generate a clickable close-button 
									var button_close = u.f.addAction(ul_actions, {"type":"button", "name":"button_close", "class":"button button_close primary","value":"Luk"});
									u.e.click(button_close);
									button_close.scene = form_confirm_cancellation.scene;
									button_close.clicked = function () {
										this.scene.overlay.close ();
									}
								}
							}
							
							u.request(this, this.action, {
								"data":data,
								"method":"POST"
							})
						}
					}
	
					u.request(this, "/profil/opsig");
				}
				// Add click event to cancel and close overlay
				u.e.click(regret);
				regret.scene = this._form.scene;
				regret.clicked = function () {
					this.scene.overlay.close ();
				}
			}
		}

		// scene is ready
		scene.ready();
	}
}
