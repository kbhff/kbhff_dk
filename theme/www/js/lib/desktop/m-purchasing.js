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

			var form = u.qs("form.choose_date");
			u.f.init(form);
			form.updated = function() {
				this.submit();
			}

			this.div_products = u.qs("div.products", this);
			var ul_list = u.qs("ul.list", this.div_products);
			var div_filter = u. ae(this.div_products, "div", {"class": "filter"});
			this.div_products.insertBefore(div_filter, ul_list);
			
			this.form_product_filter = u.f.addForm(div_filter, {"class": "labelstyle:inject"});
			this.form_product_filter.div = this;

			var fieldset = u.f.addFieldset(this.form_product_filter);
			u.f.addField(fieldset, {"type":"string", "name": "query", "label":"Filtrer produkter"});

			u.f.init(this.form_product_filter);
			this.form_product_filter.updated = function() {
				var query = this.inputs["query"].val().toLowerCase();

				var i, product, odd_even = 0;
				for(i = 0; i < this.div.products.length; i++) {
					product = this.div.products[i];
					if(!query || product.search_string.match(query)) {
						u.ac(product, "show");
						odd_even++;
						// u.ass(product, {
						// 	"display": "flex"
						// });
					}
					else {
						u.rc(product, "show");
						// u.ass(product, {
						// 	"display": "none"
						// });
					}
					u.rc(product, "odd");
					if(odd_even%2) {
						u.ac(product, "odd");
					}
				}
			}


			this.products = u.qsa(" li.listing", this.div_products);
			var i, product, image;
			for(i = 0; i < this.products.length; i++) {

				product = this.products[i];
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

			// Update filter
			this.form_product_filter.updated();

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
			var form = u.qs("form");
			u.f.init(form, this);

			form.scene = this;

			next_wednesday = " - ";

			this.first_pickupdate_span = u.qs(".first_pickupdate span", this);
			form.inputs["start_availability_date"].changed = function(iN) {
				
				var first_pickupdate = "-";

				if(iN.value) {
					var next_wednesday_date = this.form.scene.getNextDayOfTheWeek("Wednesday", false, new Date(iN.value));
					first_pickupdate = next_wednesday_date.toLocaleDateString('da-DA', {year:"numeric", month:"2-digit", day:"2-digit"});
				}
				

				this.form.scene.first_pickupdate_span.innerHTML = first_pickupdate;
			}

			this.last_pickupdate_span = u.qs(".last_pickupdate span", this);
			form.inputs["end_availability_date"].changed = function(iN) {
				
				var last_pickupdate = "-";
				
				if(iN.value) {
					var previous_wednesday_date = this.form.scene.getPreviousDayOfTheWeek("Wednesday", false, new Date(iN.value));
					last_pickupdate = previous_wednesday_date.toLocaleDateString('da-DA', {year:"numeric", month:"2-digit", day:"2-digit"});
				}
				
				this.form.scene.last_pickupdate_span.innerHTML = last_pickupdate;
			}
		
		}

		scene.getNextDayOfTheWeek = function(dayName, excludeToday = true, refDate = new Date()) {
			var dayOfWeek = ["sun","mon","tue","wed","thu","fri","sat"]
							  .indexOf(dayName.slice(0,3).toLowerCase());
			if (dayOfWeek < 0) return;
			refDate.setHours(0,0,0,0);
			refDate.setDate(refDate.getDate() + (!!excludeToday ? 1 : 0) + 
							(dayOfWeek + 7 - refDate.getDay() - (!!excludeToday ? 1 : 0)) % 7);
			return refDate;
		}

		scene.getPreviousDayOfTheWeek = function(dayName, excludeToday = true, refDate = new Date()) {
			var dayOfWeek = ["sun","mon","tue","wed","thu","fri","sat"]
							  .indexOf(dayName.slice(0,3).toLowerCase());
			if (dayOfWeek < 0) return;
			refDate.setHours(0,0,0,0);
			refDate.setDate(refDate.getDate() + (!!excludeToday ? 1 : 0) +
							(dayOfWeek - 7 - refDate.getDay() - (!!excludeToday ? 1 : 0)) % 7);
			return refDate;
		}
		
		// scene is ready
		scene.ready();
	}
}
