Util.Objects["signupfees"] = new function() {
	this.init = function(scene) {

		scene.resized = function() {
//			u.bug("scene.resized:" + u.nodeId(this));
		}

		scene.scrolled = function() {
//			u.bug("scene.scrolled:" + u.nodeId(this))
		}

		scene.ready = function() {
			u.bug("scene.ready:", this);

			var signupfees = u.qsa("ul.offer");
			var largestHeight = 0;
			for (var i = 0; i < signupfees.length; i++) {
				if (u.actualHeight(signupfees[i]) > largestHeight) {
					largestHeight = u.actualHeight(signupfees[i]);
				}
			}
			u.ass(signupfees[1], {"height":largestHeight+"px"});
		}


		// scene is ready
		scene.ready();
	}
}
