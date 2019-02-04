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

			// initialize signup form
			var search_form = u.qs("form.search_user", this);
			search_form.scene = this;
	
			if(search_form) {
				u.f.init(search_form);
			}
			
			var allUsers = u.qs("ul.users");
		
			var users = u.cn(allUsers);
			var search_input = document.getElementById("input_search");
			
			var input_value = search_input.value.toUpperCase();
			console.log(input.value);
			var found 
			search_input.addEventListener('keyup', filterNames);
				
			function filterNames() {
				console.log(input_value);
				for (var i = 0; i < users.length; i++) {
					li = u.cn(users[i]);
					
					for (var j = 0; j < li.length; j++) {
						console.log(input_value);
						if (li[j].innerHTML.toUpperCase().indexOf(input_value) > -1) {
							found == true;
						
						}
					}
					if (found) {
						console.log(users[i]);
						users[i].style.display = "";
						found == false;
					}
					else {
						users[i].style.display = "none";
					}
				}
			}
			
		
				// function filterNames(search_input) {
				// 	return users.filter(function(uf) {
				// 		return uf.toLowerCase().indexOf(search_input.toLowerCase()) > -1;
				// 	});
				// }
				// console.log(filterNames("eli"));

			page.resized();
		}


		// scene is ready
		scene.ready();

	}

}
