Util.Objects["member_help_payment"] = new function() {
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
			var mobilepay_fieldset = u.qs("fieldset", mobilepay_form);
			var mobilepay_checkbox_field = u.qs(".field.checkbox", mobilepay_fieldset);
			var mobilepay_checkbox = u.qs("input[type=checkbox]", mobilepay_checkbox_field);
			var mobilepay_code_div = u.qs("div.code", mobilepay_fieldset);
			var cash_form = u.qs("form.cash", payment_options);
			var cash_fieldset = u.qs("fieldset", cash_form);
			var cash_checkbox_field = u.qs(".field.checkbox", cash_fieldset);
			var cash_checkbox = u.qs("input[type=checkbox]", cash_checkbox_field);
			var cash_instructions = u.qs("div.instructions", cash_fieldset);
			
			// adjust mobile and cash forms to the same height
			var fieldset_height = u.actualHeight(mobilepay_fieldset);
			var mobilepay_code_div_height = u.actualHeight(mobilepay_code_div);
			u.as(cash_fieldset, "height", fieldset_height + "px"); 
			u.as(cash_instructions, "height", mobilepay_code_div_height + "px"); 

			// initialize forms
			if(mobilepay_form) {
				u.f.init(mobilepay_form);
			}
			if(cash_form) {
				u.f.init(cash_form);
			}

			this.card_form = u.qs("form.card", this);

			// Validate card number
			u.f.customValidate["card"] = function(iN) {
//				u.bug("local validation");

				var card_number = iN.val().replace(/ /g, "");
				if(u.paymentCards.validateCardNumber(card_number)) {
					u.f.inputIsCorrect(iN);
					u.f.validate(iN._form.inputs["card_cvc"]);
				}
				else {
					u.f.inputHasError(iN);
				}

			}

			//  Validate expiration month
			u.f.customValidate["exp_month"] = function(iN) {
//				u.bug("local month validation: from year: " + iN.validating_year + " " + u.randomString(3));

				var month = iN.val();
				var year = iN._form.inputs["card_exp_year"].val();

				if(year && parseInt(year) < 100) {
					year = parseInt("20"+year);
				}
//				u.bug("month:" + month + ", year:" + year);


				if(u.paymentCards.validateExpMonth(month)) {
					u.f.inputIsCorrect(iN);
				}
				else {
					u.f.inputHasError(iN);
				}

				// validate year - performs combined validation if year is filled out
				// don't do it if this validation was looped from year validation (will cause endless loop)
				if(!iN.validating_year) {
					iN._form.inputs["card_exp_year"].validating_month = true;
					u.f.validate(iN._form.inputs["card_exp_year"]);
					iN._form.inputs["card_exp_year"].validating_month = false;
				}

			}

			// Validate expiration year
			u.f.customValidate["exp_year"] = function(iN) {
//				u.bug("local year validation: from month: " + iN.validating_month + " " + u.randomString(3));

				var year = iN.val();
				var month = iN._form.inputs["card_exp_month"].val();

				if(year && parseInt(year) < 100) {
					year = parseInt("20"+year);
				}
//				u.bug("month:" + month + ", year:" + year);

				// validate month, with new year value
				// don't do it if this validation was looped from month validation (will cause endless loop)
				if(!iN.validating_month) {
					iN._form.inputs["card_exp_month"].validating_year = true;
					u.f.validate(iN._form.inputs["card_exp_month"]);
					iN._form.inputs["card_exp_month"].validating_year = false;
				}

				if(u.paymentCards.validateExpDate(month, year)) {
					u.f.inputIsCorrect(iN);
				}
				else if(!month && u.paymentCards.validateExpYear(year)) {

					// year is only fully correct when month is also present
					u.rc(iN, "correct");
					u.rc(iN.field, "correct");
				}
				// mark both fields as errors (one of them is wrong)
				else {
					u.f.inputHasError(iN);
					u.f.inputHasError(iN._form.inputs["card_exp_month"]);
				}

			}

			// Validate CVC
			u.f.customValidate["cvc"] = function(iN) {
//				u.bug("local cvc validation");

				var cvc = iN.val();
				var card_number = iN._form.inputs["card_number"].val().replace(/ /g, "");

				if(u.paymentCards.validateCVC(cvc, card_number)) {
					u.f.inputIsCorrect(iN);
				}
				else {
					u.f.inputHasError(iN);
				}
			}


			// initalize form
			u.f.init(this.card_form);

			this.card_form.submitted = function() {
				
				if(!this.is_submitting) {
					this.is_submitting = true;

					this.DOMsubmit();
				}

			}


			// format card as you type
			this.card_form.inputs["card_number"].updated = function(iN) {
				var value = this.val();
				this.value = u.paymentCards.formatCardNumber(value.replace(/ /g, ""));

				var card = u.paymentCards.getCardTypeFromNumber(value);
				if(card && card.type != this.current_card) {
					if(this.current_card) {
						u.rc(this, this.current_card);
					}
					this.current_card = card.type;
					u.ac(this, this.current_card);
				}
				else if(!card) {
					if(this.current_card) {
						u.rc(this, this.current_card);
					}
				}
			}

			// remove first two digits in 2000-fullyears
			this.card_form.inputs["card_exp_year"].changed = function(iN) {
				var year = parseInt(this.val());
				if(year > 99) {
					if(year > 2000 && year < 2100) {
						this.val(year-2000);
					}
				}
			}

			// prefix month with "0" if less than 10
			this.card_form.inputs["card_exp_month"].changed = function(iN) {
				var month = parseInt(this.val());
				if(month < 10) {
					this.val("0"+month);
				}
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
			var cash_tab = u.insertElement(payment_options, "h4", {"class":"tab cash_tab","html":"Kontant"});
			var card_tab = u.ie(payment_options, "h4", {"class":"tab card_tab","html":"Betalingskort"});
			var mobilepay_tab = u.ie(payment_options, "h4", {"class":"tab mobilepay_tab","html":"MobilePay"});

			u.e.click(mobilepay_tab);
			mobilepay_tab.clicked = function () {
				u.as(cash_form, "display", "none");
				u.as(mobilepay_form, "display", "block");
				u.as(cash_tab, "backgroundColor", "#BBBBBB");
				u.as(mobilepay_tab, "backgroundColor", "#f2f2f2f2");
			}
			
			u.e.click(cash_tab);
			cash_tab.clicked = function () {
				u.as(mobilepay_form, "display", "none");
				u.as(cash_form, "display", "block");
				u.as(mobilepay_tab, "backgroundColor", "#BBBBBB");
				u.as(cash_tab, "backgroundColor", "#f2f2f2f2")
			}



		}

		// scene is ready
		scene.ready();
	}
}
