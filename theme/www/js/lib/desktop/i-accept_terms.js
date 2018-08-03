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

			var form_accept = u.qs("form.accept", this);
			u.f.init(form_accept);

			form_accept.actions["reject"].clicked = function() {
				var overlay = u.overlay({title:"slet mig", height:200,width:600});
				var warning = u.ae(overlay.div_content, "p",{html:"Er du sikker på, at du IKKE vil acceptere vore retningslinjer for persondata? Hvis du trykker JA vil du straks blive udmeldt"});

				// Add action buttons to cancel and confirm
				var delete_me = u.f.addAction(overlay.div_content, {"type":"button", "name":"delete_me", "class":"action delete_me","value":"Ja, slet mig"});
				var regret = u.f.addAction(overlay.div_content, {"type":"button", "name":"regret", "class":"action regret primary", "value":"Fortryd"});
				
				// Add click event to go to main page
				u.e.click(delete_me)
				delete_me.clicked = function () {
					 window.location = "/"
				}
				// Add click event to cancel and close overlay
				u.e.click(regret)
				regret.clicked = function () {
						overlay.close ();
				}

				// Add "x"-close button to header
				overlay.x_close = u.ae(overlay.div_header, "div", {class: "close"});
				overlay.x_close.overlay = overlay;
				u.ce(overlay.x_close);

				// enable close/cancel buttons to close overlay
				overlay.x_close.clicked = function (event) {
					this.overlay.close(event);
				}
			}
		}

		// scene is ready
		scene.ready();
	}
}
