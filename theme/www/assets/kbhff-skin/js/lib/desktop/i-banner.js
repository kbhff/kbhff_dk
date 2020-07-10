Util.Modules["banner"] = new function() {
	this.init = function(div) {

		var variant = u.cv(div, "variant");
		var format = u.cv(div, "format");

		if (variant == "random" || !variant) {
			variant = u.random(1, 4);
		}

		u.ae(div, "img", {class:"fit-width", src:"/img/banners/desktop/pi_" + variant + "." + format});	
		u.ae(div, "div", {class:"logo"});

	}
}
