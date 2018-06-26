Util.Objects["forgot"] = new function() {
	this.init = function(scene) {

		scene.resized = function() {
			// u.bug("scene.resized:", this);
		}

		scene.scrolled = function() {
			// u.bug("scene.scrolled:", this);
		}

		scene.ready = function() {
			// u.bug("scene.ready:", this);

			var form = u.qs("form", this);
			u.f.init(form);

			form.submitted = function() {
				var data = u.f.getParams(this); // Gets returned form input
				console.log(data);

				this.response = function(response) {
					// Submit goes through and gets "nulstilling" page
					if (u.qs("form.verify", response)) {
						// Getting form from "nulstilling" page
						var form_code = u.qs("div.login form", response);
						var p_code = u.qs("div.login p", response);
	
						// Remove current form from HTML DOM, insert new one and initialize it
						form.parentNode.replaceChild(form_code, form);
						u.ie(form_code, p_code);
						u.f.init(form_code);

						// Using the new verify form
						form_code.submitted = function() {
							data = u.f.getParams(this, {"send_as":"json"});
							console.log(data)

							if (data.code === "12345") {
								
							}
							else {
								
							}
						}
					}
					// Submit fails and gets "glemt" page, as specified in the controller
					else {
						if (!u.qs("p.errormessage")) {
							// Inserting errormessage from external "glemt" page
							var p_error = u.qs("p.errormessage", response);
							u.ie(form, p_error);
						}
						else {
							// if p.errormessage exists, Animate it instead
							var p_error = u.qs("p.errormessage", this);
							u.a.transition(p_error, "all 0.15s linear", animationDone);
							u.a.scale(p_error, 1.05);
							
							function animationDone() {
								u.a.transition(p_error, "all 0.15s linear");
								u.a.scale(this, 1);
							}
						}
					}
				}

				u.request(this, this.action, {"data":data, "method":"POST"}); // Get this templates "action" (requestReset), and send "data" to it
			}
		}
		// scene is ready
		scene.ready();
	}
}
