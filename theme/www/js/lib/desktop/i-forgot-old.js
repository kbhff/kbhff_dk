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
				var data = u.f.getParams(this, {"send_as":"json"}); // Gets returned form input and stores it as json in a variable
				var validData = ["12345", "11223344", "email@email.dk"]; // Temporary array to simulate a database

				console.log(data);
				console.log(validData.indexOf(data.username));

				// if form.verify doesn't exists and "data" matches an index in "validData" array, then execute code;	--if no match then indexOf returns -1
				if (!u.qs("form.verify", this) && validData.indexOf(data.username) !== -1) {
					// Building new form
					var form_verify = u.f.addForm(this.parentNode, {"class":"form verify", "action":"#", "method":"POST"}); // "this" being form.login 
					var fieldset_verify = u.f.addFieldset(form_verify, {"class":"verify"});
					var field_verify = u.f.addField(fieldset_verify, {"label":"kode", "name":"verify", "type":"string", "id":"input_verify", "hint_message":"hint", "error_message":""});
					var button_verify = u.f.addAction(fieldset_verify, {"name":"save", "type":"submit", "value":"lav nyt password", "class":"button primary"});
					var p_verify = u.ie(fieldset_verify, "p");
					p_verify.innerHTML = "<span>TAK.</span> Vi har nu sendt dig en mail, i mailen er der en kode som du kan indtaste her og lave et nyt password.";

					// Remove login form from HTML DOM, store the new verify form in the form variable
					form.parentNode.removeChild(form);
					u.f.init(form_verify);

					// Use new form
					form_verify.submitted = function() {
						data = u.f.getParams(this, {"send_as":"json"});
						console.log(data)
						console.log(validData.indexOf(data.verify));
						
						if (validData.indexOf(data.verify) !== -1) {
							console.log("correct");
						}
						else {
							console.log("wrong")
							u.a.transition(p_verify, "all 0.15s ease-in", animationDone);
							u.a.scale(p_verify, 1.05);

							function animationDone() {
								u.a.transition(p_verify, "all 0.15s ease-in");
								u.a.scale(this, 1);
							}
						}
					}
				}
				
				else { // if data doesnt match validData (result is -1), do this
					if (!u.qs("div.notfound", this)) {
						// Creating div.notfound
						var div_notfound = u.ie(u.qs("fieldset"), "div", {"class":"notfound"});
						var p_notfound = u.ie(div_notfound, "p");
						p_notfound.innerHTML = "Vi kunne desværre ikke finde dette login i databasen. Prøv igen eller <a href='#'>kontakt os</a>";
					}

					else {
						// if div.notfound exists, Animate it instead
						var div_notfound = u.qs("div.notfound", this);
						u.a.transition(div_notfound, "all 0.15s ease-in", animationDone);
						u.a.scale(div_notfound, 1.05);
						
						function animationDone() {
							u.a.transition(div_notfound, "all 0.15s ease-in");
							u.a.scale(this, 1);
						}
					}
				}
			
			}
		}
		// scene is ready
		scene.ready();
	}
}
