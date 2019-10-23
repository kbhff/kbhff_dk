Util.Objects["forgot"] = new function() {
	this.init = function(scene) {

		scene.resized = function() {
			// u.bug("scene.resized:", this);
		}

		scene.scrolled = function() {
			// u.bug("scene.scrolled:", this);
		}

		scene.ready = function() {
			// Assign form to scene
			this.form = u.qs("form.request_password", this);

			// Initialize form
			u.f.init(this.form);

			// Create a scene property on "this.form" that refers to scene
			this.form.scene = this;

			this.form.submitted = function() {
				// Gets form input values
				var data = this.getData();

				this.response = function(response) {
					// Submit goes through and gets "nulstilling" page
					if (u.qs("form.verify_code", response)) {
						this.scene.verifyForm(response);
					}
					// Submit fails and gets "glemt" page, as specified in the controller
					else {
						this.scene.showMessage(this, response);
					}
				}

				// Get this template's "action" (requestReset), and send "data" (user's input) to it
				u.request(this, this.action, {"data":data, "method":"POST"});
			}
		}
		

		scene.verifyForm = function(response) {
			// Getting form from "nulstilling" page
			this.form_code = u.qs("form.verify_code", response);
			var p_code = u.qs("div.login p", response);

			// Remove current form from HTML DOM, insert new form and initialize it
			this.form.parentNode.replaceChild(this.form_code, this.form);
			u.ie(this.form_code, p_code);
			u.f.init(this.form_code);

			// Create reference to scene on "form_code"
			this.form_code.scene = this;

			// Using the new verify form
			this.form_code.submitted = function() {
				data = this.getData();

				this.response = function(response) {
					if (u.qs("form.reset_password", response)) {
						this.scene.resetForm(response);
					}
					else {
						this.scene.showMessage(this, response);
					}
				}

				u.request(this, this.action, {"data":data, "method":"POST"});
			}
		}

		scene.resetForm = function(response) {
			// Getting form from "nulstilling" page
			this.form_reset = u.qs("form.reset_password", response);

			// Remove current form from HTML DOM, insert new one and initialize it
			this.form_code.parentNode.replaceChild(this.form_reset, this.form_code);
			u.f.init(this.form_reset);
			this.form_reset.scene = this;
		}

		scene.showMessage = function(form, response) {
			// Getting new error and current error
			var response_error = u.qs("p.errormessage", response);
			var scene_error = u.qs("p.errormessage", this);
			

			if (!scene_error) {
				u.ie(form, response_error);
			}
			else {
				// Updating error message
				form.replaceChild(response_error, scene_error);

				// Animating error message
				u.a.transition(response_error, "all 0.15s linear", animationDone);
				u.a.scale(response_error, 1.05);
				
				function animationDone() {
					u.a.transition(this, "all 0.15s linear");
					u.a.scale(this, 1);
				}
			}
		}

		// scene is ready
		scene.ready();
	}
}
