Util.Modules["purchasing"] = new function() {
	this.init = function(scene) {

		scene.resized = function() {
			// u.bug("scene.resized", this);
		}

		scene.scrolled = function() {
			// u.bug("scene.scrolled", this);
		}

		scene.ready = function() {
			// u.bug("scene.ready", this);


			// Choose date

			var form_choose_date = u.qs("form.choose_date");
			if(form_choose_date) {
				u.f.init(form_choose_date);
				form_choose_date.updated = function() {
					this.submit();
				}
			}



			// Orders

			this.div_order_list = u.qs("div.order-list", this);
			if(this.div_order_list) {

				// Orders filter
				this.bn_hide = u.qs("ul.actions li.hide-no-orders", this.div_order_list);
				this.bn_hide.scene = this
				this.bn_show = u.qs("ul.actions li.show-no-orders", this.div_order_list);
				this.bn_show.scene = this

				u.ce(this.bn_hide);
				this.bn_hide.clicked = function() {
					u.rc(this.scene.div_order_list, "show-all")
				}
				u.ce(this.bn_show);
				this.bn_show.clicked = function() {
					u.ac(this.scene.div_order_list, "show-all")
				}

			}



			// Products
			this.div_products = u.qs("div.products", this);
			if(this.div_products) {

				this.div_products.products = u.qsa("li.listing", this.div_products);
				var i, product, image;
				for(i = 0; i < this.div_products.products.length; i++) {

					product = this.div_products.products[i];
					product.search_string = u.qs(".name", product).innerHTML.toLowerCase();
					image = u.qs("span.image", product);

					image._id = u.cv(image, "item_id");
					image._format = u.cv(image, "format");
					image._variant = u.cv(image, "variant");

					if(image._id && image._format && image._variant) {

						// add image
						u.ass(image, {
							backgroundImage: "url(/images/" + image._id + "/" + (image._variant ? image._variant+"/" : "") + "50x50." + image._format+")"
						});

					}

				}

				u.productFilters(this.div_products);

			}

		}
		
		// scene is ready
		scene.ready();
	}
}


Util.Modules["add_edit_product"] = new function() {
	this.init = function(scene) {

		scene.resized = function() {
			// u.bug("scene.resized", this);
		}

		scene.scrolled = function() {
			// u.bug("scene.scrolled", this);
		}

		scene.ready = function() {
//			// u.bug("scene.ready", this);


			// Basics

			var form_basics = u.qs("form.basics");
			u.f.init(form_basics, this);



			// Availability

			var form_availability = u.qs("form.availability");
			if(form_availability) {
				u.f.init(form_availability, this);
				form_availability.scene = this;


				next_wednesday = " - ";

				form_availability.first_pickupdate_span = u.qs(".first_pickupdate span", form_availability);
				form_availability.inputs["start_availability_date"].changed = function(iN) {

					var first_pickupdate = "-";
					if(iN.value) {
						var next_wednesday_date = this.form.scene.getNextDayOfTheWeek("Wednesday", false, new Date(iN.value));
						first_pickupdate = next_wednesday_date.toLocaleDateString('da-DA', {year:"numeric", month:"2-digit", day:"2-digit"});
					}

					this.form.first_pickupdate_span.innerHTML = first_pickupdate;
				}

				form_availability.last_pickupdate_span = u.qs(".last_pickupdate span", form_availability);
				form_availability.inputs["end_availability_date"].changed = function(iN) {

					var last_pickupdate = "-";
					if(iN.value) {
						var previous_wednesday_date = this.form.scene.getPreviousDayOfTheWeek("Wednesday", false, new Date(iN.value));
						last_pickupdate = previous_wednesday_date.toLocaleDateString('da-DA', {year:"numeric", month:"2-digit", day:"2-digit"});
					}

					this.form.last_pickupdate_span.innerHTML = last_pickupdate;
				}
			}


			// Prices

			var form_prices = u.qs("form.prices");
			if(form_prices) {
				u.f.init(form_prices, this);
			}



			// Tags

			var form_tags = u.qs("form.tags");
			if(form_tags) {
				u.f.init(form_tags, this);
			}


		}

		scene.getNextDayOfTheWeek = function(dayName, excludeToday = true, refDate = new Date()) {
			var dayOfWeek = ["sun","mon","tue","wed","thu","fri","sat"].indexOf(dayName.slice(0,3).toLowerCase());
			if (dayOfWeek < 0) return;
			refDate.setHours(0,0,0,0);
			refDate.setDate(refDate.getDate() + (!!excludeToday ? 1 : 0) + (dayOfWeek + 7 - refDate.getDay() - (!!excludeToday ? 1 : 0)) % 7);
			return refDate;
		}

		scene.getPreviousDayOfTheWeek = function(dayName, excludeToday = true, refDate = new Date()) {
			var dayOfWeek = ["sun","mon","tue","wed","thu","fri","sat"].indexOf(dayName.slice(0,3).toLowerCase());
			if (dayOfWeek < 0) return;
			refDate.setHours(0,0,0,0);
			refDate.setDate(refDate.getDate() + (!!excludeToday ? 1 : 0) + (dayOfWeek - 7 - refDate.getDay() - (!!excludeToday ? 1 : 0)) % 7);
			return refDate;
		}




		// scene is ready
		scene.ready();
	}
}
