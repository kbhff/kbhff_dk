Util.Objects["member_help_payment"] = new function() {
	this.init = function(scene) {

		scene.resized = function() {
//			u.bug("scene.resized:" + u.nodeId(this));
		}

		scene.scrolled = function() {
//			u.bug("scene.scrolled:" + u.nodeId(this))
		}

		scene.ready = function() {
			u.bug("scene.ready:", this);

			var payment_form = u.qs("form.card");
			var card_fieldset = u.qs("fieldset")
			var payment_explanation = u.ie(payment_form, "div", {"class":"payment_explanation","html":"Her står en tekst der fortæller dig at du skal ned i afdelingen for at betale dette og at du kan klikke 'spring over'"})
			var mobilepay_fan = u.ie(payment_form, "div", {"class":"fan mobilepay_fan","html":"Kontant"});
			var visa_fan = u.insertElement(payment_form, "div", {"class":"fan visa_fan","html":"MobilePay"});
			var fieldset_height = u.actualHeight(card_fieldset);

			u.e.click(mobilepay_fan);
			mobilepay_fan.clicked = function () {
				u.as(card_fieldset, "display", "none");
				u.ass(payment_explanation, {"display":"block", "height":fieldset_height+"px"});
				u.as(visa_fan, "backgroundColor", "#BBBBBB");
				u.as(mobilepay_fan, "backgroundColor", "#f2f2f2f2");
			}

			u.e.click(visa_fan);
			visa_fan.clicked = function () {
				u.as(payment_explanation, "display", "none");
				u.as(card_fieldset, "display", "block");
				u.as(mobilepay_fan, "backgroundColor", "#BBBBBB");
				u.as(visa_fan, "backgroundColor", "#f2f2f2f2")
			}



		}

		// scene is ready
		scene.ready();
	}
}
