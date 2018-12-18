Util.Objects["header_image"] = new function() {
	this.init = function(div) {

			var variant = u.cv(div, "variant");
			var format = u.cv(div, "format");

			u.ae(div, "img", {class:"fit-width", src:"/img/banners/smartphone/pi_" + variant + "." + format});	

	}
}
