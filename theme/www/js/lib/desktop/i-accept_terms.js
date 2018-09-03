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

			u.qs("div.field.checkbox label").innerHTML = "Jeg accepterer <a href='/terms' target='_blank'>retningslinjerne</a>."
			u.qs("div.field.checkbox .error").innerHTML = "Du skal acceptere retningslinjerne for at fortsætte."
			u.qs("div.field.checkbox .hint").innerHTML = ""

			form_accept.actions["reject"].clicked = function() {
				var overlay = u.overlay({title:"Vil du udmeldes?", height:200,width:600});
				var warning = u.ae(overlay.div_content, "p",{html:"Du er ved at melde dig ud af KBHFF. Pga. lovgivning og hensyn til persondata kan du ikke være medlem af KBHFF uden at acceptere vores vilkår. Vi håber du vil genoverveje."});

				// Add action buttons to cancel and confirm
				var delete_me = u.f.addAction(overlay.div_content, {"type":"button", "name":"delete_me", "class":"button delete_me","value":"Meld mig ud af KBHFF"});
				var regret = u.f.addAction(overlay.div_content, {"type":"button", "name":"regret", "class":"button regret primary", "value":"Fortryd udmelding"});
				
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
