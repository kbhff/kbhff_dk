Util.Objects["front"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
			// u.bug("scene.resized:", this);
		}

		scene.scrolled = function() {
			// u.bug("scrolled:", this);
		}

		scene.ready = function() {
			// u.bug("scene.ready:", this);

			page.cN.scene = this;

			var nodes = u.qsa("div.news ul.items li.item", scene);
			var i, node
			if(nodes) {
				for(i = 0; node = nodes[i]; i++) {
					u.o.article.init(node);
				}
			}


		}

		// scene is ready
		scene.ready();
	}
}
