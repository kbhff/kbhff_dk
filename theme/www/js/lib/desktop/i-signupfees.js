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

			var signupfees = u.qsa("ul.offer li.description");
			console.log(signupfees)
			var largestHeight = 0;
			for (var i = 0; i < signupfees.length; i++) {
				if (u.actualHeight(signupfees[i]) > largestHeight) {
					var largestHeight = u.actualHeight(signupfees[i]);
					console.log(largestHeight)
					console.log(i)
				}
			}
			var j = signupfees.length;
			while (j--) {
				u.ass(signupfees[j], {"height":largestHeight+"px"})
			}


			var bg1 = u.ae(scene, "div", {class:"bg volunteer"});
			var bg2 = u.ae(scene, "div", {class:"bg supporter"});
		}

		// scene is ready
		scene.ready();
	}
}
