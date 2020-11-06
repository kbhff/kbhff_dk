Util.Modules["banner"] = new function() {
	this.init = function(div) {

		var variant = u.cv(div, "variant");
		var format = u.cv(div, "format");

		if (variant == "random" || !variant) {
			variant = u.random(1, 4);
		}

		
		var image = u.ae(div, "img", {class:"fit-width"});	
		u.ae(div, "div", {class:"logo"});

		image.loaded = function(queue) {

			this.src = queue[0].image.src;

			if(page) {
				page.resized();
			}

			// this._image = u.ie(this, "img");
			// this._image.image = this;
			// this._image.src = ;

		}
		u.preloader(image, ["/img/banners/desktop/pi_" + variant + "." + format]);


	}
}
