Util.Modules["stripe"] = new function() {
	this.init = function(scene) {
//		u.bug("stripe init:", this);
		

		scene.resized = function() {
//			u.bug("scene.resized:", this);
		}

		scene.scrolled = function() {
//			u.bug("scrolled:", this);;
		}

		scene.beforeUnload = function(event) {

			// event.preventDefault();
			// event.returnValue = 'test';

			// u.bug(event); 

			// this.confirm_close = u.overlay({title:"Glemmer du ikke noget?", height:200,width:600, class:"confirm_close"});
			// this.confirm_close.warning = u.ae(this.confirm_close.div_content, "p", {
			// 	html:"Du mangler at betale dit medlemskab. Hvis du forlader siden, vil du blive bedt om at betale, når du forsøger at logge på."
			// });
			// this.confirm_close.actions = u.ae(this.confirm_close.div_content, "ul", {
			// 	class:"actions"
			// })
			
			// // Add action buttons to cancel and confirm
			// this.confirm_close.continue = u.f.addAction(this.confirm_close.actions, {"type":"button", "name":"continue", "class":"button","value":"Forlad siden"});
			// this.confirm_close.regret = u.f.addAction(this.confirm_close.actions, {"type":"button", "name":"regret", "class":"button primary", "value":"Fortryd"});
			
			
			// // Add click event to cancel and close overlay
			// u.e.click(this.confirm_close.regret);
			// this.confirm_close.regret.clicked = function () {
			// 	this.confirm_close.close ();
			// }

			return "test";
			
		}

		scene.ready = function() {
//			u.bug("scene.ready:", this);

			page.cN.scene = this;

			// u.e.addWindowEvent(this, "beforeunload", this.beforeUnload);

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

			// // remove first two digits in 2000-fullyears
			// this.card_form.inputs["card_cvc"].updated = function(iN) {
			// 	if(this.val().length == 2) {
			// 		this.used = true;
			// 	}
			// }


			// enable button when form is filled out
// 			this.card_form.validationPassed = function() {
// //				u.bug("validationPassed")
// 				u.rc(this.actions["pay"], "disabled");
// 			}
// 			// disable button on form error
// 			this.card_form.validationFailed = function(errors) {
// //				u.bug("validationFailed")
// 				u.ac(this.actions["pay"], "disabled");
// 			}


			page.resized();
		}

		// scene is ready
		scene.ready();
	}
}