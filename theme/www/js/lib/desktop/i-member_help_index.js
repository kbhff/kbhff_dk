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
			search_form.h3 = u.qs("h3.hidden", this)
			
			// set flag if users are already found by default search
			if (search_form.user_search = u.qs("div.users")) {
				search_form.user_search_exists = true;
			}
		
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
					
				}
				
				// function sets timer in order to control execution of search function.
				search_form.readyToSearch = function () {
					u.t.resetTimer(this.t_search);
					this.t_search = u.t.setTimer(this, this.search, this.timeOut)
				}
		
				// search function which executes when timer has run out.
				search_form.search = function () {
					
					// hides user list if it is already found by default search. 
					if(this.user_search_exists == true) {
						this.user_search.innerHTML = "";
					}
					// makes headlines visible 
					u.sc(this.h3, "visible");
					
					
					this.response = function(response) {
						// hides user list if it already exists
						this.users_ul.innerHTML = "";
						// parses user object and returns it as html node lists
						this.users = u.template(this.template, response.cms_object, {"append":this.users_ul});
						
						// loops through the user li's
						for (var i = 0; i < this.users.length; i++) {
							
							// queries a node list with the user information
							this.user_info = u.qsa("ul.user_info li", this.users[i]);
							// loops through the relevant node lists
							for (var j = 0; j < (this.user_info.length - 3); j++) {
								// creates a new RegExp object from the users input
								var match = this.search_input.val();
								var re = new RegExp(match, 'i');
								// checks if there is a match between user input and string in node list
								if (this.user_info[j].innerHTML.match(re)) {
									var to_be_replaced = this.user_info[j].innerHTML.match(re);
									// Convert first letter in all words in string to upper case
									var match_words = match.split(" ");
									for (var k = 0; k < match_words.length; k++ ) {
										var l = match_words[k].charAt(0).toUpperCase();
										match_words[k] = l + match_words[k].substr(1);
									}
									var match_uppercase = match_words.join(" ");
									// creates a new RegExp object to replace the maching string 
									var replacement = '<span class="highlight_string">'+match_uppercase+'</span>'
									var regex = new RegExp(replacement, 'i');
								
									this.li_html = this.user_info[j].innerHTML;
									
									this.user_info[j].innerHTML = this.li_html.replace(to_be_replaced, replacement);
								}
							}
							
							// u.ae(this.users_ul, this.users[i]); 
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
