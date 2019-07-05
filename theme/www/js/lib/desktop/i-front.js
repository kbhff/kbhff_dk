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
					// Init article (inject image)

					node.image = u.qs("div.image", node);
					if(node.image) {

						// // remove link from caption
						// node.image.caption = u.qs("p a", node.image);
						// if(node.image.caption) {
						// 	node.image.caption.removeAttribute("href");
						// }

						// get image variables
						node.image._id = u.cv(node.image, "item_id");
						node.image._format = u.cv(node.image, "format");
						node.image._variant = u.cv(node.image, "variant");


						// if image
						if(node.image._id && node.image._format) {

							// add image
							node.image._image_src = "/images/" + node.image._id + "/" + (node.image._variant ? node.image._variant+"/" : "") + "300x200." + node.image._format;

							// u.ass(image, {
							// 	"opacity": 0
							// });

							node.image.loaded = function(queue) {

								u.ac(this, "loaded");

								u.ass(this, {
									"backgroundImage": "url("+queue[0].image.src+")"
								})
								// this._image = u.ie(this, "img");
								// this._image.image = this;
								// this._image.src = queue[0].image.src;

								// correct scroll for image expansion
								// if(this.node.article_list) {
								// 	this.node.article_list.correctScroll(this.node, this, -10);
								// }

								// u.a.transition(this, "all 0.5s ease-in-out");
								// u.ass(this, {
								// 	//"height": (this._image.offsetHeight + this.wrapper_height) +"px",
								// 	"opacity": 1
								// });
							}
							u.preloader(node.image, [node.image._image_src]);
						}
						

					}


					// Reference on node to article link
					node.link = u.qs("h3 > a", node).href;

					// Redirect to post anchor link
					u.ce(node);
					node.clicked = function() {
						location.href = this.link;
					}

				}
			}


		}

		// scene is ready
		scene.ready();
	}
}
