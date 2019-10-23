Util.Objects["member_help"] = new function() {
	this.init = function(scene) {
		// u.bug("scene init:", scene);


		scene.resized = function() {
			// u.bug("scene.resized:", this);
		}

		scene.scrolled = function() {
			// u.bug("scrolled:", this);
		}

		scene.ready = function() {
			// u.bug("scene.ready:", this);

			page.cN.scene = this;

			var search_form = u.qs("form.search_user", this);

			// Is search form available
			if(search_form) {

				search_form.scene = this;

				search_form.ul_users = u.qs("ul.users", this);
				search_form.h3_header = u.qs("div.users h3", this);
				search_form.p_no_results = u.qs("div.users p.no_results", this);

				search_form.template = u.qs("li.template", this);

				search_form.search_timeout = 300


				// Init form
				u.f.init(search_form);


				// function to run when user types more than 3 characters in search field.
				search_form.updated = function () {

					u.t.resetTimer(this.t_search);
					this.current_search = this.inputs.search_member.val();

					// Ready to search
					if (this.current_search.length > 3) {
						this.readyToSearch()

					}

					// Not enough input for AJAX search
					else {

						this.ul_users.innerHTML = "";

						u.ac(this.h3_header, "hidden");
						u.rc(this.p_no_results, "hidden");

					}

				}


				// function sets timer in order to control execution of search function.
				search_form.readyToSearch = function () {
					this.t_search = u.t.setTimer(this, this.search, this.search_timeout)
				}
		
				// search function which executes when timer has run out.
				search_form.search = function () {


					this.response = function(response) {
						console.log(response);

						// Clear existing result set
						this.ul_users.innerHTML = "";

						// parses user object and returns it as html node lists
						this.users = u.template(this.template, response.cms_object.users);

						if(this.users) {

							u.rc(this.h3_header, "hidden");
							u.ac(this.p_no_results, "hidden");

							while (this.users.length) {

								this.user_info = u.qsa("ul.user_info li.search", this.users[0]);

								var i, user_info;
								// loops through the relevant node lists
								for (i = 0; i < this.user_info.length; i++) {

									user_info = this.user_info[i];

									// creates a new RegExp object from the users input
									var match = this.current_search;
									var re = new RegExp(match, 'i');

									// checks if there is a match between user input and string in node list
									if (user_info.innerHTML.match(re)) {
										user_info.innerHTML = user_info.innerHTML.replace(re, "<span class=\"highlight_string\">$&</span>");
									}

								}

								// Append result to list
								u.ae(this.ul_users, this.users[0]); 

							}

						}
						else {

							u.ac(this.h3_header, "hidden");
							u.rc(this.p_no_results, "hidden");

						}

					}
					u.request(this, this.action+"soeg", {"method":"post", "data":this.getData()});
				}
			}	
		
				page.resized();
		}

		
		// scene is ready
		scene.ready();

	}

}
