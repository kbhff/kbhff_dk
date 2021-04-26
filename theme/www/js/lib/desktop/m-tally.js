Util.Modules["tally"] = new function() {
	this.init = function(scene) {
		// u.bug("scene init:", scene);


		scene.resized = function() {
			// u.bug("scene.resized:", this);
		}

		scene.scrolled = function() {
			// u.bug("scrolled:", this);
		}

		scene.ready = function() {
			// u.bug("scene.ready:", this);


			if(!u.qs(".section.tally.closed")) {

				this.tally_id = u.cv(this, "tally_id");


				this.initStartCash();
				this.initEndCash();
				this.initDeposited();


				this.initPayouts();
				this.initMiscRevenues();
		
				this.comment_form = u.qs("form.comment", this);
				this.comment_form.scene = this;
				u.f.init(this.comment_form);
		
				this.comment_form.submitted = function(iN) {
		
					if(iN.hasAttribute("formaction")) {
		
						this.action = iN.getAttribute("formaction");
					}
		
					this.response = function(response) {

						var error_message = u.qs(".messages .error", response);

						if(error_message) {
							if(this.p_error) {

								this.p_error.parentNode.removeChild(this.p_error);
							} 

							this.p_error = u.ie(u.qs(".section.tally"), "p", {
								html:error_message.innerHTML,
								class:"error"
							});
							u.scrollTo(window, {node: this.p_error, "offset_y": 20});

						}
						else if(u.qs(".scene.shop_shift", response)) {
		
							location.href = "/butiksvagt"; 
						}
						else {
		
							location.href = "/butiksvagt/kasse/"+this.scene.tally_id;
						}
					} 
		
					u.request(this, this.action, {"method":"POST", "params":u.f.getParams(this)});
		
				}
		
				this.calculated_sales_by_the_piece = u.qs(".calculated_sales span.sum", this);
				this.change = u.qs(".change span.sum", this);
			}
	


			// // accept cookies?
			// page.acceptCookies();

			page.resized();
		}

		scene.initStartCash = function() {

			this.start_cash = u.qs(".start_cash .view");
			this.start_cash.bn_edit = u.qs("li.edit", this.start_cash);
			this.start_cash.bn_edit.scene = this;

			this.start_cash.amount = u.qs("span.value", this.start_cash);

			this.start_cash.form = u.qs("form", this.start_cash);
			this.start_cash.form.scene = this;
			u.f.init(this.start_cash.form);

			// u.ce(this.start_cash.bn_edit);
			this.start_cash.form.actions["edit"].clicked = function() {
				u.ac(this._form.scene.start_cash, "edit");
				this._form.inputs["start_cash"].focus();
			}
			
			this.start_cash.form.submitted = function() {

				this.response = function(response) {
					var new_amount = u.text(u.qs(".start_cash .view .amount span.value", response));
					this.scene.start_cash.amount.innerHTML = new_amount;

					u.rc(this.scene.start_cash, "edit");

					this.scene.updateCalculatedValues(response);
				}
				u.request(this, this.action, {"method":"post", "data":this.getData()});

			}

		}

		scene.initEndCash = function() {

			this.end_cash = u.qs(".end_cash .view");
			this.end_cash.bn_edit = u.qs("li.edit", this.end_cash);
			this.end_cash.bn_edit.scene = this;

			this.end_cash.amount = u.qs("span.value", this.end_cash);

			this.end_cash.form = u.qs("form", this.end_cash);
			this.end_cash.form.scene = this;
			u.f.init(this.end_cash.form);

			this.end_cash.form.actions["edit"].clicked = function() {
				u.ac(this._form.scene.end_cash, "edit");
				this._form.inputs["end_cash"].focus();
			}

			this.end_cash.form.submitted = function() {

				this.response = function(response) {
					var new_amount = u.text(u.qs(".end_cash .view .amount span.value", response));
					this.scene.end_cash.amount.innerHTML = new_amount;

					u.rc(this.scene.end_cash, "edit");

					this.scene.updateCalculatedValues(response);
				}
				u.request(this, this.action, {"method":"post", "data":this.getData()});

			}

		}

		scene.initDeposited = function() {

			this.deposited = u.qs(".deposited .view");
			this.deposited.bn_edit = u.qs("li.edit", this.deposited);
			this.deposited.bn_edit.scene = this;

			this.deposited.amount = u.qs("span.value", this.deposited);

			this.deposited.form = u.qs("form", this.deposited);
			this.deposited.form.scene = this;
			u.f.init(this.deposited.form);

			this.deposited.form.actions["edit"].clicked = function() {
				u.ac(this._form.scene.deposited, "edit");
				this._form.inputs["deposited"].focus();
			}

			this.deposited.form.submitted = function() {

				this.response = function(response) {
					var new_amount = u.text(u.qs(".deposited .view .amount span.value", response));
					this.scene.deposited.amount.innerHTML = new_amount;

					u.rc(this.scene.deposited, "edit");

					this.scene.updateCalculatedValues(response);
				}
				u.request(this, this.action, {"method":"post", "data":this.getData()});

			}

		}

		scene.initPayouts = function() {

			this.tally_section = u.qs(".section.tally", this);
			this.payouts = u.qs(".payouts", this);


			this.payouts.delete_forms = u.qsa("li.payout .delete", this.payouts);

			if(this.payouts.delete_forms) {
				var i, delete_form;
				for (i = 0; i < this.payouts.delete_forms.length; i++) {

					delete_form = this.payouts.delete_forms[i];
					delete_form.scene = this;

					u.m.oneButtonForm.init(delete_form);

					delete_form.confirmed = function(response) {

						this.payouts = u.qs("div.payouts", response);
						this.scene.tally_section.replaceChild(this.payouts, this.scene.payouts);

						this.scene.updateCalculatedValues(response);

						this.scene.initPayouts();
					}
				}
			}


			this.payouts.div_add = u.qs("div.add_payout", this.payouts);

			this.payouts.bn_add = u.qs("li.add_payout", this.payouts);
			this.payouts.bn_add.scene = this;

			u.ce(this.payouts.bn_add);
			this.payouts.bn_add.clicked = function() {


				this.response = function(response) {

					u.ac(this.scene.payouts.div_add, "open");

					this.scene.payouts.div_add.form = u.qs("form.add_payout", response);
					this.scene.payouts.div_add.form.scene = this.scene;

					u.ae(this.scene.payouts.div_add, this.scene.payouts.div_add.form);

					u.f.init(this.scene.payouts.div_add.form);
					this.scene.payouts.div_add.form.inputs["payout_name"].focus();

					this.scene.payouts.div_add.form.submitted = function() {

						this.response = function(response) {
							u.rc(this.scene.payouts.div_add, "open");
							this.parentNode.removeChild(this);

							this.payouts = u.qs("div.payouts", response);
							this.scene.tally_section.replaceChild(this.payouts, this.scene.payouts);
							
							this.scene.updateCalculatedValues(response);

							this.scene.initPayouts();
						}

						u.request(this, this.action, {"method":"post", "data":this.getData()});
					}

				}

				u.request(this, "/butiksvagt/kasse/" + this.scene.tally_id + "/udbetaling");

			}
		}

		scene.initMiscRevenues = function() {

			this.tally_section = u.qs(".section.tally", this);
			this.revenues = u.qs(".misc_revenues", this);


			this.revenues.delete_forms = u.qsa("li.revenue .delete", this.revenues);

			if(this.revenues.delete_forms) {
				var i, delete_form;
				for (i = 0; i < this.revenues.delete_forms.length; i++) {

					delete_form = this.revenues.delete_forms[i];
					delete_form.scene = this;

					u.m.oneButtonForm.init(delete_form);

					delete_form.confirmed = function(response) {

						this.revenues = u.qs("div.misc_revenues", response);
						this.scene.tally_section.replaceChild(this.revenues, this.scene.revenues);

						this.scene.updateCalculatedValues(response);

						this.scene.initMiscRevenues();
					}
				}
			}
			
			this.revenues.div_add = u.qs("div.add_revenue", this.revenues);

			this.revenues.bn_add = u.qs("li.add_revenue", this.revenues);
			this.revenues.bn_add.scene = this;

			u.ce(this.revenues.bn_add);
			this.revenues.bn_add.clicked = function() {

				this.response = function(response) {

					u.ac(this.scene.revenues.div_add, "open");

					this.scene.revenues.div_add.form = u.qs("form.add_revenue", response);
					this.scene.revenues.div_add.form.scene = this.scene;

					u.ae(this.scene.revenues.div_add, this.scene.revenues.div_add.form);

					u.f.init(this.scene.revenues.div_add.form);
					this.scene.revenues.div_add.form.inputs["revenue_name"].focus();

					this.scene.revenues.div_add.form.submitted = function() {

						this.response = function(response) {
							u.rc(this.scene.revenues.div_add, "open");
							this.parentNode.removeChild(this);

							this.revenues = u.qs("div.misc_revenues", response);
							this.scene.tally_section.replaceChild(this.revenues, this.scene.revenues);
							
							this.scene.updateCalculatedValues(response);

							this.scene.initMiscRevenues();
						}

						u.request(this, this.action, {"method":"post", "data":this.getData()});
					}

				}

				u.request(this, "/butiksvagt/kasse/" + this.scene.tally_id + "/andre-indtaegter");

			}
		}

		scene.updateCalculatedValues = function(response) {
			
			var calculated_sales_by_the_piece = u.qs(".calculated_sales span.sum", response);
			if(calculated_sales_by_the_piece) {

				u.pn(this.calculated_sales_by_the_piece).replaceChild(calculated_sales_by_the_piece, this.calculated_sales_by_the_piece);
				this.calculated_sales_by_the_piece = calculated_sales_by_the_piece;
			}


			var change = u.qs(".change span.sum", response);
			if(change) {
				
				u.pn(this.change).replaceChild(change, this.change);
				this.change = change;
			}


		}

		// scene is ready
		scene.ready();

	}

}
