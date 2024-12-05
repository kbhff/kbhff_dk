Util.Modules["massmail"] = new function() {
	this.init = function(scene) {

		scene.resized = function() {
			// u.bug("scene.resized", this);
		}

		scene.scrolled = function() {
			// u.bug("scene.scrolled", this);
		}

		scene.ready = function() {
			u.bug("scene.ready", this);

			

			var form = u.qs("form");
			u.f.init(form, this);
			form.scene = this;
			form.p_status = u.qs(".status", form);

			form.updated = function(iN) {
				
				window.onbeforeunload = function() {
					return 'Du har ændringer, der ikke er gemt!';
				}
			}

			form.submitted = function(iN) {

				if(iN.hasAttribute("formaction")) {
		
					form.response = function(response) {
	
						if(u.qs(".scene.mass_mail_receipt", response)) {
							this.p_status.innerHTML = "Test-mail blev afsendt.";
						}
						else {
							this.p_status.innerHTML = "Noget gik galt...";
						}
						this.p_status.transitioned = function() {

							u.t.setTimer(this, function() {
								u.a.transition(this, "all 1s ease-in");
								u.as(this, "opacity", "0");
							}, 1500);
							
						}
						
						u.a.transition(this.p_status, "all 0.5s ease-in");
						u.as(this.p_status, "opacity", "1");
					}
	
					u.request(this, iN.getAttribute("formaction"), {"method":"POST", "params":u.f.getParams(this)});
				}
				else {

					form.DOMsubmit();
				}

			}

		}
		
		// scene is ready
		scene.ready();
	}
}
