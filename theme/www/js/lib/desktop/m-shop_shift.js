Util.Modules["shop_shift"] = new function() {
	this.init = function(scene) {
		// u.bug("scene init:", scene);


		scene.resized = function() {
			// u.bug("scene.resized:", this);
		}

		scene.scrolled = function() {
			// u.bug("scrolled:", this);
		}

		scene.ready = function() {
			// u.bug("scene.ready:", this);

			var form = u.qs("form.choose_date");
			u.f.init(form);
			form.updated = function() {
				this.submit();
			}

			var confirm_delivery_listings = u.qsa(".orders.items .listing");
			
			var i, listing;
			for(i = 0; i < confirm_delivery_listings.length; i++) {

				listing = confirm_delivery_listings[i];
				this.initDeliveryButton(listing);

			}

		}

		scene.initDeliveryButton = function(listing) {

			// u.bug(listing);
			listing.order_item_id = u.cv(listing, "order_item_id");
			listing.list = u.qs(".orders.items .list");
			listing.scene = this;
			listing.li = u.qs("li.confirm", listing);
			listing.li.listing = listing;

			listing.li.confirmed = function(response) {

				response.listing = u.ge("order_item_id:" + listing.order_item_id, response);
				response.listing.li = u.qs("li.confirm", response.listing);


				this.listing.list.replaceChild(response.listing, this.listing);
				u.m.oneButtonForm.init(response.listing.li);
				this.listing.scene.initDeliveryButton(response.listing);
			}
		}

		// scene is ready
		scene.ready();

	}

}
