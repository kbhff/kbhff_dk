Util.Modules["shop"] = new function() {
	this.init = function(scene) {
//		u.bug("scene init:", this);

		scene.resized = function() {
//			u.bug("scene.resized:", this);

			if(this.sidebar) {
				this.sidebar.start_y = u.absY(this.sidebar);
			}

		}

		scene.scrolled = function() {
			// u.bug("scrolled:", this, this.sidebar.start_y, page.offsetHeight, page.scrolled_y, this.sidebar.offsetHeight);

			if(this.sidebar) {

				if(page.offsetHeight - 52 - page.scrolled_y < this.sidebar.offsetHeight) {
					// do nothing
				}
				else if(this.sidebar.start_y < page.scrolled_y) {
					if(!this.sidebar.is_fixed) {
						this.sidebar.is_fixed = true;
						u.ac(this.sidebar, "fixed");
					}
					u.ass(this.sidebar, {
						top: page.scrolled_y - this.sidebar.start_y + "px"
					});
				}
				else if(this.sidebar.is_fixed) {
					this.sidebar.is_fixed = false;
					u.rc(this.sidebar, "fixed");
				}

			}

		}

		scene.ready = function() {
//			u.bug("scene.ready:", this);

			var form_login = u.qs("form.login", this);
			if(form_login) {
				u.f.init(form_login);
				form_login.inputs["username"].focus();
			}

			var products = u.qsa("li.product", this);
			if(products) {
				var i, j, pickupdate, product, image;
				for(i = 0; i < products.length; i++) {

					product = products[i];

					// Images
					var images = u.qsa("div.image,div.media", product);
					for(j = 0; j < images.length; j++) {

						image = images[j];
						image.product = product;

						// get image variables
						image._id = u.cv(image, "item_id");
						image._format = u.cv(image, "format");
						image._variant = u.cv(image, "variant");


						// if image
						if(image._id && image._format) {

							// add image
							image._image_src = "/images/" + image._id + "/" + (image._variant ? image._variant+"/" : "") + "380x." + image._format;

							// u.ass(image, {
							// 	"opacity": 0
							// });

							image.loaded = function(queue) {

								u.ac(this, "loaded");
								u.ass(this, {
									"backgroundImage": "url("+queue[0].image.src+")"
								})
								// this._image = u.ie(this, "img");
								// this._image.image = this;
								// this._image.src = ;

							}
							u.preloader(image, [image._image_src]);
						}
					}

					// Pickup dates
					var pickupdate;
					var div_pickupdates = u.qs("div.pickupdates", product);
					div_pickupdates.ul_pickupdates = u.qs("ul.pickupdates", product);
					div_pickupdates.pickupdates = u.qsa("li.pickupdate", product);
					div_pickupdates.total_width = div_pickupdates.pickupdates.length*56;

					if(div_pickupdates.total_width >= div_pickupdates.offsetWidth) {
						div_pickupdates.current_x = 0;
						u.ac(div_pickupdates, "scroll");

						u.ass(div_pickupdates.ul_pickupdates, {
							width: div_pickupdates.total_width+"px"
						});
						div_pickupdates.bn_left = u.ae(div_pickupdates, "span", {"class":"left"});
						div_pickupdates.bn_left.div_pickupdates = div_pickupdates;
						u.ce(div_pickupdates.bn_left);
						div_pickupdates.bn_left.clicked = function() {
							if(this.div_pickupdates.current_x < 0) {
								this.div_pickupdates.current_x = this.div_pickupdates.current_x + 56;
								u.ass(this.div_pickupdates.ul_pickupdates, {
									transition: "all 0.5s ease-in-out",
									transform: "translate(" + this.div_pickupdates.current_x + "px, 0)"
								});


							}
							this.div_pickupdates.updateArrows();
						}
						div_pickupdates.bn_right = u.ae(div_pickupdates, "span", {"class":"right"});
						div_pickupdates.bn_right.div_pickupdates = div_pickupdates;
						u.ce(div_pickupdates.bn_right);
						div_pickupdates.bn_right.clicked = function() {
							if(this.div_pickupdates.current_x > this.div_pickupdates.offsetWidth - this.div_pickupdates.total_width) {
								this.div_pickupdates.current_x = this.div_pickupdates.current_x - 56;
								u.ass(this.div_pickupdates.ul_pickupdates, {
									transition: "all 0.5s ease-in-out",
									transform: "translate(" + this.div_pickupdates.current_x + "px, 0)"
								});
							}
							this.div_pickupdates.updateArrows();
						};

					}
					div_pickupdates.updateArrows = function() {

						if(this.current_x <= this.offsetWidth - this.total_width) {
							u.ass(this.bn_right, {opacity: 0.3});
						}
						else {
							u.ass(this.bn_right, {opacity: 1});
						}
						if(this.current_x >= 0) {
							u.ass(this.bn_left, {opacity: 0.3});
						}
						else {
							u.ass(this.bn_left, {opacity: 1});
						}

					}

					div_pickupdates.updateArrows();

					for(j = 0; j < div_pickupdates.pickupdates.length; j++) {

						pickupdate = div_pickupdates.pickupdates[j];
						pickupdate.scene = this;

						pickupdate.bn_add = u.qs("div.add", pickupdate);
						if(pickupdate.bn_add) {
							pickupdate.bn_add.title = "Tilføj til kurven";
							pickupdate.bn_add.pickupdate = pickupdate;

							pickupdate.bn_add.confirmed = function(response) {

								if(response.isHTML) {
									var scene_cart = u.qs("div.cart", this.pickupdate.scene);
									var response_cart = u.qs("div.cart", response);
									u.ass(response_cart, {
										opacity: 0.5
									});
									scene_cart.parentNode.replaceChild(response_cart, scene_cart);
									u.ass(response_cart, {
										transition: "opacity 0.1s ease-in-out",
										opacity: 1
									});

								}
							}
						}

					}

				}

			}

			this.sidebar = u.qs("div.sidebar", this);
			if(this.sidebar) {
				this.sidebar.start_y = u.absY(this.sidebar);
			}
		}

		scene.ready();
	}
}

Util.Modules["cart"] = new function() {
	this.init = function(scene) {
		// u.bug("scene init:", this);

		scene.resized = function() {
//			u.bug("scene.resized:", this);


			// refresh dom
			//this.offsetHeight;
		}

		scene.scrolled = function() {
//			u.bug("scrolled:", this);;
		}

		scene.ready = function() {
//			u.bug("scene.ready:", this);

			this.total_cart_price = u.qs("div.total span.total_price", this);
			u.bug("this.total_cart_price", this.total_cart_price);
			this.cart_nodes = u.qsa("ul.items li.item", this);


			var i, node;
			for(i = 0; node = this.cart_nodes[i]; i++) {


				node.scene = this;
				node.item_id = u.cv(node, "id");
				node.pickupdate = u.cv(node, "date");

				node.unit_price = u.qs("span.unit_price", node);
				node.total_price = u.qs("span.total_price", node);
				node.quantity = u.qs("input[name=quantity]", node);

				// look for quantity update form
				var quantity_form = u.qs("form.updateCartItemQuantity", node)

				// initialize quantity form
				if(quantity_form) {
					quantity_form.node = node;

					u.f.init(quantity_form);


					quantity_form.inputs["quantity"].updated = function() {
						if(parseInt(this.val()) < 1) {
							this.val(1);
						}
						else {
							u.ac(this._form.actions["update"], "primary");
							this._form.submit();
						}
					}


					quantity_form.submitted = function() {

						this.response = function(response) {

							if(response) {

								var total_price = u.qs("div.scene div.total span.total_price", response);
								var item_row;
								if(this.node.pickupdate) {
									item_row = u.ge("id:"+this.node.item_id+" date:"+this.node.pickupdate, response);
								}
								else {
									item_row = u.ge("id:"+this.node.item_id, response);
								}
								var item_total_price = u.qs("span.total_price", item_row);
								var item_unit_price = u.qs("span.unit_price", item_row);
								var item_quantity = u.qs("input[name=quantity]", item_row);
								

								// update prices and quantity
								this.node.scene.total_cart_price.innerHTML = total_price.innerHTML;
								this.node.total_price.innerHTML = item_total_price.innerHTML;
								this.node.unit_price.innerHTML = item_unit_price.innerHTML;
								this.node.quantity.value = item_quantity.value;


					 			u.rc(this.actions["update"], "primary");

							}
						}

						u.request(this, this.action, {"method":"post", "data":this.getData()});
					}
				}


			

				var bn_delete = u.qs("ul.actions li.delete", node);
				if(bn_delete) {
					u.m.oneButtonForm.init(bn_delete);

					bn_delete.node = node;	
					bn_delete.confirmed = function(response) {

						if(response) {

							var total_price = u.qs("div.scene div.total span.total_price", response);

							// update total price
							this.node.scene.total_cart_price.innerHTML = total_price ? total_price.innerHTML : "0,00 DKK";

							this.node.parentNode.removeChild(this.node);
						}
					}

				}

			}

		}

		scene.ready();
	}
}



Util.Modules["checkout"] = new function() {
	this.init = function(scene) {
//		u.bug("scene init:", this);

		scene.resized = function() {
//			u.bug("scene.resized:", this);
		}

		scene.scrolled = function() {
//			u.bug("scrolled:", this);;
		}

		scene.ready = function() {
//			u.bug("scene.ready:", this);

			var form_login = u.qs("form.login", this);
			if(form_login) {
				u.f.init(form_login);
				form_login.inputs["username"].focus();
			}

		}

		scene.ready();
	}
}



Util.Modules["shopProfile"] = new function() {
	this.init = function(scene) {
		// u.bug("scene init:", this);

		scene.resized = function() {
//			u.bug("scene.resized:", this);
		}

		scene.scrolled = function() {
//			u.bug("scrolled:", this);;
		}

		scene.ready = function() {
//			u.bug("scene.ready:", this);

			var form = u.qs("form.details", this);
			if(form) {
				u.f.init(form);
			}

		}

		scene.ready();
	}
}



Util.Modules["shopAddress"] = new function() {
	this.init = function(scene) {
//		u.bug("scene init:", this);
		

		scene.resized = function() {
//			u.bug("scene.resized:", this);
		}

		scene.scrolled = function() {
//			u.bug("scrolled:", this);;
		}

		scene.ready = function() {
//			u.bug("scene.ready:", this);

			var addresses = u.qsa("ul.addresses li.address", this);
			if(addresses) {

				var i, address, highest = 0;
				for(i = 0; i < addresses.length; i++) {
					address = addresses[i];
					if(address.offsetHeight > highest) {
						highest = address.offsetHeight;
					}
				}

				for(i = 0; i < addresses.length; i++) {
					address = addresses[i];
					u.ass(address, {
						height: highest+"px"
					})
				}

			}


			var form = u.qs("form.address", this);
			if(form) {
				u.f.init(form);
			}

		}

		scene.ready();
	}
}


