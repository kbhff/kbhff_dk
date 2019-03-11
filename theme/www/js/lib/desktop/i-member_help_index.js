Util.Objects["member_help"] = new function() {
	this.init = function(scene) {
//		u.bug("scene init:", scene);


		scene.resized = function() {
//			u.bug("scene.resized:", this);
		}

		scene.scrolled = function() {
//			u.bug("scrolled:", this);
		}

		scene.ready = function() {
//			u.bug("scene.ready:", this);

			page.cN.scene = this;

			
			var search_form = u.qs("form.search_user", this);
			
			search_form.users_ul = u.qs("ul.users", this);
			search_form.template = u.qs("li.template", this);
		
			var i = 0;
			
			search_form.visible_p = u.qs("p.visible");
			
			if(search_form) {
				 
				search_form.scene = this;
			
				search_form.timeOut = 300
				u.f.init(search_form);
			
				// function to run when user types more than 3 characters in search field.
				search_form.updated = function () {
					this.search_input = this.fields.search_member;
					if (this.search_input.val().length > 3) {
						this.readyToSearch()
					}
					if (this.search_input.val().length < 3) {
						this.users_ul.innerHTML = "";
						if (this.visible_p.style.display = "none") {
							u.as(this.visible_p, "display", "block");
						}
					}	
				}
				
				// function sets timer in order to control execution of search function.
				search_form.readyToSearch = function () {
					u.t.resetTimer(this.t_search);
					this.t_search = u.t.setTimer(this, this.search, this.timeOut)
				}
		
				// search function which executes when timer has run out.
				search_form.search = function () {
					
					this.response = function(response) {
						console.log(response);
						this.users_ul.innerHTML = "";
						u.as(this.visible_p, "display", "none");
						// parses user object and returns it as html node lists
						this.users = u.template(this.template, response.cms_object.users);
						console.log(response);
						while (this.users.length) {		
						
							this.user_info = u.qsa("ul.user_info li.search", this.users[0]);
							// loops through the relevant node lists
							for (var j = 0; j < (this.user_info.length); j++) {
								
								// creates a new RegExp object from the users input
								var match = this.search_input.val();
								var re = new RegExp(match, 'i');
								// checks if there is a match between user input and string in node list
								if (this.user_info[j].innerHTML.match(re)) {
								
									this.user_info[j].innerHTML = this.user_info[j].innerHTML.replace(re, "<span class=\"highlight_string\">$&</span>");
								}
							}
							
						 u.ae(this.users_ul, this.users[0]); 
						}
					}
					u.request(this, this.action+"soeg", {"method":"post", "data":u.f.getParams(this)});
				}
			}	
		
				page.resized();
		}

		
		// scene is ready
		scene.ready();

	}

}
