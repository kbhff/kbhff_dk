Util.Modules["banner"] = new function() {
	this.init = function(div) {

			var variant = u.cv(div, "variant");
			var format = u.cv(div, "format");

			if (variant == "random") {
				variant = u.random(1, 4);
			}

			u.ae(div, "img", {class:"fit-width", src:"/img/banners/smartphone/pi_" + variant + "." + format});

	}
}
