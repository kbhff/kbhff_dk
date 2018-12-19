Util.Objects["banner"] = new function() {
	this.init = function(div) {

			var variant = u.cv(div, "variant");
			var format = u.cv(div, "format");

			u.ae(div, "img", {class:"fit-width", src:"/img/banners/desktop/pi_" + variant + "." + format});	

	}
}
