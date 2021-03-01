Util.Modules["add_edit_product"] = new function() {
	this.init = function(scene) {

		scene.resized = function() {
			// u.bug("scene.resized", this);
		}

		scene.scrolled = function() {
			// u.bug("scene.scrolled", this);
		}

		scene.ready = function() {
//			// u.bug("scene.ready", this);
			var form = u.qs("form");
			u.f.init(form, this);

			form.scene = this;

			next_wednesday = " - ";

			this.first_pickupdate_span = u.qs(".first_pickupdate span", this);
			form.inputs["start_availability_date"].changed = function(iN) {
				
				var first_pickupdate = "";
				var next_wednesday_date = this.form.scene.getNextDayOfTheWeek("Wednesday", true, new Date(iN.value));
				
				var one_day = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
				var diff_days = Math.round(Math.abs((next_wednesday_date - new Date(iN.value)) / one_day));
				
				if(diff_days >= 7) {
					first_pickupdate = next_wednesday_date.toLocaleDateString();
				}
				else {
					first_pickupdate = this.form.scene.getNextDayOfTheWeek("Wednesday", true, next_wednesday_date).toLocaleDateString();
				}
				this.form.scene.first_pickupdate_span.innerHTML = first_pickupdate;
			}
		}

		scene.getNextDayOfTheWeek = function(dayName, excludeToday = true, refDate = new Date()) {
			var dayOfWeek = ["sun","mon","tue","wed","thu","fri","sat"]
							  .indexOf(dayName.slice(0,3).toLowerCase());
			if (dayOfWeek < 0) return;
			refDate.setHours(0,0,0,0);
			refDate.setDate(refDate.getDate() + +!!excludeToday + 
							(dayOfWeek + 7 - refDate.getDay() - +!!excludeToday) % 7);
			return refDate;
		}

		// scene is ready
		scene.ready();
	}
}
