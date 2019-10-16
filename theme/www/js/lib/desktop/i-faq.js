Util.Objects["faq"] = new function() {
	this.init = function(scene) {

		scene.resized = function() {
			// u.bug("scene.resized:", this);
		}

		scene.scrolled = function() {
			// u.bug("scene.scrolled:", this);
		}

		scene.ready = function() {
			// u.bug("scene.ready:", this);

			var questions = u.qsa("ul.items li.question", this);
			var i, question, header;

			for(i = 0; i < questions.length; i++) {

				question = questions[i];

				header = u.qs("h2,h3", question);
				header.question = question;
				u.addExpandArrow(header);

				u.ce(header);
				header.clicked = function() {

					if(this.is_open) {
						this.is_open = false;
						u.rc(this.question, "open");

						u.addExpandArrow(this);
						u.deleteNodeCookie(this.question, "state");

					}
					else {
						this.is_open = true;
						u.ac(this.question, "open");

						u.addCollapseArrow(this);
						u.saveNodeCookie(this.question, "state", "open", {ignore_classnames:"open"});

					}

					if(!this.answer) {
						this.response = function(response) {
							if(response.isHTML) {

								this.answer = u.qs(".scene .article .articlebody", response);
								u.ae(this.question, this.answer);

							}
						}
						u.request(this, this.url);
					}
				}


				// check prev state
				if(u.getNodeCookie(question, "state") == "open") {
					header.clicked();
				}

			}

		}

		// scene is ready
		scene.ready();
	}
}
