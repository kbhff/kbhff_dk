Util.Modules["member_help_payment"] = new function() {
	this.init = function(scene) {

		scene.resized = function() {
			// u.bug("scene.resized", this);
		}

		scene.scrolled = function() {
			// u.bug("scene.scrolled", this);
		}

		scene.ready = function() {
			u.bug("scene.ready:", this);

			var payment_options = u.qs("div.payment_options", this);
			var mobilepay_form = u.qs("form.mobilepay", payment_options);
			if(mobilepay_form) {
				var mobilepay_fieldset = u.qs("fieldset", mobilepay_form);
				var mobilepay_checkbox_field = u.qs(".field.checkbox", mobilepay_fieldset);
				var mobilepay_checkbox = u.qs("input[type=checkbox]", mobilepay_checkbox_field);
				var mobilepay_code_div = u.qs("div.code", mobilepay_fieldset);
			}

			var cash_form = u.qs("form.cash", payment_options);
			if(cash_form) {
				var cash_fieldset = u.qs("fieldset", cash_form);
				var cash_checkbox_field = u.qs(".field.checkbox", cash_fieldset);
				var cash_checkbox = u.qs("input[type=checkbox]", cash_checkbox_field);
				var cash_instructions = u.qs("div.instructions", cash_fieldset);
			}

			var card_form = u.qs("div.card", payment_options);
			if(card_form) {
				var card_fieldset = u.qs("div.fieldset", card_form);
			}

			// adjust forms to the same height
			var fieldset_height = u.actualHeight(mobilepay_fieldset);
			var mobilepay_code_div_height = u.actualHeight(mobilepay_code_div);


			// initialize forms
			if(mobilepay_form) {

				u.f.init(mobilepay_form);
			}

			if(cash_form) {
				u.as(cash_fieldset, "height", fieldset_height + "px"); 
				u.as(cash_instructions, "height", mobilepay_code_div_height + "px"); 

				u.f.init(cash_form);
			}


			if(card_form) {

				u.as(card_fieldset, "height", fieldset_height + "px"); 

				// initalize form
				// u.f.init(card_form);

				card_form.submitted = function() {
				
					if(!this.is_submitting) {
						this.is_submitting = true;

						this.DOMsubmit();
					}

				}

				//
				// // format card as you type
				// card_form.inputs["card_number"].updated = function(iN) {
				// 	var value = this.val();
				// 	this.value = u.paymentCards.formatCardNumber(value.replace(/ /g, ""));
				//
				// 	var card = u.paymentCards.getCardTypeFromNumber(value);
				// 	if(card && card.type != this.current_card) {
				// 		if(this.current_card) {
				// 			u.rc(this, this.current_card);
				// 		}
				// 		this.current_card = card.type;
				// 		u.ac(this, this.current_card);
				// 	}
				// 	else if(!card) {
				// 		if(this.current_card) {
				// 			u.rc(this, this.current_card);
				// 		}
				// 	}
				// }
				//
				// // remove first two digits in 2000-fullyears
				// card_form.inputs["card_exp_year"].changed = function(iN) {
				// 	var year = parseInt(this.val());
				// 	if(year > 99) {
				// 		if(year > 2000 && year < 2100) {
				// 			this.val(year-2000);
				// 		}
				// 	}
				// }
				//
				// // prefix month with "0" if less than 10
				// card_form.inputs["card_exp_month"].changed = function(iN) {
				// 	var month = parseInt(this.val());
				// 	if(month < 10) {
				// 		this.val("0"+month);
				// 	}
				// }

			}






			// make checkboxes mutually exclusive
			if(mobilepay_form && cash_form) {
				u.e.addEvent(mobilepay_checkbox_field, "change", function() {
					if(u.hc(mobilepay_checkbox_field, "checked")) {
						if(u.hc(cash_checkbox_field, "checked")) {
							u.rc(cash_checkbox_field, "checked")
							cash_checkbox.checked = false;
							u.f.validate(cash_checkbox);
						}
					}

				});

				u.e.addEvent(cash_checkbox_field, "change", function() {
					if(u.hc(cash_checkbox_field, "checked")) {
						if(u.hc(mobilepay_checkbox_field, "checked")) {
							u.rc(mobilepay_checkbox_field, "checked")
							mobilepay_checkbox.checked = false;
							u.f.validate(mobilepay_checkbox);
						}
					}

				});

			}



			
			// add clickable tabs for mobilepay/cash

			if(cash_form) {

				var cash_tab = u.ie(payment_options, "h4", {"class":"tab cash_tab","html":"Kontant"});

				u.e.click(cash_tab);
				cash_tab.clicked = function () {
					u.as(mobilepay_form, "display", "none");
					u.as(card_form, "display", "none");
					u.as(cash_form, "display", "block");
					u.as(mobilepay_tab, "backgroundColor", "#BBBBBB");
					u.as(card_tab, "backgroundColor", "#BBBBBB");
					u.as(cash_tab, "backgroundColor", "#f2f2f2")
				}
			}

			if(card_form) {

				var card_tab = u.ie(payment_options, "h4", {"class":"tab card_tab","html":"Betalingskort"});

				u.e.click(card_tab);
				card_tab.clicked = function () {
					u.as(mobilepay_form, "display", "none");
					u.as(cash_form, "display", "none");
					u.as(card_form, "display", "block");
					u.as(mobilepay_tab, "backgroundColor", "#BBBBBB");
					u.as(cash_tab, "backgroundColor", "#BBBBBB");
					u.as(card_tab, "backgroundColor", "#f2f2f2")
				}
			}

			if(mobilepay_form) {

				var mobilepay_tab = u.ie(payment_options, "h4", {"class":"tab mobilepay_tab","html":"MobilePay"});

				u.e.click(mobilepay_tab);
				mobilepay_tab.clicked = function () {
					u.as(cash_form, "display", "none");
					u.as(card_form, "display", "none");
					u.as(mobilepay_form, "display", "block");
					u.as(cash_tab, "backgroundColor", "#BBBBBB");
					u.as(card_tab, "backgroundColor", "#BBBBBB");
					u.as(mobilepay_tab, "backgroundColor", "#f2f2f2");
				}
			}






		}

		// scene is ready
		scene.ready();
	}
}
