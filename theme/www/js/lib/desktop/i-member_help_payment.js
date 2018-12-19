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

			var payment_options = u.qs("div.payment_options", this);
			var mobilepay_form = u.qs("form.mobilepay", payment_options);
			var mobilepay_fieldset = u.qs("fieldset", mobilepay_form);
			var mobilepay_code_div = u.qs("div.code", mobilepay_fieldset);
			var cash_form = u.qs("form.cash", payment_options);
			var cash_fieldset = u.qs("fieldset", cash_form);
			var cash_instructions = u.qs("div.instructions", cash_fieldset);
			
			var fieldset_height = u.actualHeight(mobilepay_fieldset);
			var mobilepay_code_div_height = u.actualHeight(mobilepay_code_div);
			u.as(cash_fieldset, "height", fieldset_height + "px"); 
			u.as(cash_instructions, "height", mobilepay_code_div_height + "px"); 
			


			var cash_fan = u.insertElement(payment_options, "h4", {"class":"fan cash_fan","html":"Kontant"});
			var mobilepay_fan = u.ie(payment_options, "h4", {"class":"fan mobilepay_fan","html":"MobilePay"});

			u.e.click(mobilepay_fan);
			mobilepay_fan.clicked = function () {
				u.as(cash_form, "display", "none");
				u.as(mobilepay_form, "display", "block");
				u.as(cash_fan, "backgroundColor", "#BBBBBB");
				u.as(mobilepay_fan, "backgroundColor", "#f2f2f2f2");
			}
			
			u.e.click(cash_fan);
			cash_fan.clicked = function () {
				u.as(mobilepay_form, "display", "none");
				u.as(cash_form, "display", "block");
				u.as(mobilepay_fan, "backgroundColor", "#BBBBBB");
				u.as(cash_fan, "backgroundColor", "#f2f2f2f2")
			}



		}

		// scene is ready
		scene.ready();
	}
}
