Util.Objects["signupfees"] = new function() {
	this.init = function(scene) {

		scene.resized = function() {
			// u.bug("scene.resized:", this);
		}

		scene.scrolled = function() {
			// u.bug("scene.scrolled:", this);
		}

		scene.ready = function() {
			// u.bug("scene.ready 1:", this);
			
			// finds the largest height of the signupfees-descriptions 
			var signupfees = u.qsa("ul.offer li.description");
			var largestHeight = 0;
			for (var i = 0; i < signupfees.length; i++) {
				if (u.actualHeight(signupfees[i]) > largestHeight) {
					var largestHeight = u.actualHeight(signupfees[i]);
				}
			}
			// adds the largest height to all of the signupfees-descriptions
			var j = signupfees.length;
			while (j--) {
				u.ass(signupfees[j], {"height":largestHeight+"px"})
			}
	
			var bg1 = u.ae(scene, "div", {class:"bg volunteer"});
			var bg2 = u.ae(scene, "div", {class:"bg supporter"});

			page.resized();
			// adds height and width to bg images 
			u.ass(bg1, {
				height: page.offsetHeight + "px",
				top: -(u.absY(scene)) + "px",
				right: -(page.browser_w - u.absX(scene) - scene.offsetWidth) + "px",
				width: (page.browser_w / 2) + "px",
			});
			u.ass(bg2, {
				height: page.offsetHeight + "px",
				top: -(u.absY(scene)) + "px",
				left: -(page.browser_w - u.absX(scene) - scene.offsetWidth) + "px",
				width: (page.browser_w / 2) + "px",
			});

		}

		// scene is ready
		scene.ready();
	}
}
