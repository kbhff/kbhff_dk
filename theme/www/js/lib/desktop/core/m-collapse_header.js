Util.Modules["collapseHeader"] = new function() {
	this.init = function(div) {
		// u.bug("init collapseHeader");

		// add collapsable header
		u.ac(div, "togglable");
		div._toggle_header = u.qs("h2,h3,h4", div);

		if(div._toggle_header) {

			u.wc(div, "div", {"class":"togglable_content"});
			u.ie(div, div._toggle_header);

			div._toggle_header.div = div;
			u.e.click(div._toggle_header);
			div._toggle_header.clicked = function() {

				if(this.div._toggle_is_closed) {
					// add class (for detailed open settings)
					u.ac(this.div, "open");

					u.ass(this.div, {
						height: "auto"
					});
					this.div._toggle_is_closed = false;
					u.saveNodeCookie(this.div, "open", 1, {"ignore_classvars":true, "ignore_classnames":"open"});
					u.addCollapseArrow(this);

					// callback
					if(typeof(this.div.headerExpanded) == "function") {
						this.div.headerExpanded();
					}
				}
				else {
					// remove class (for detailed closed settings)
					u.rc(this.div, "open");

					u.ass(this.div, {
						height: this.offsetHeight+"px"
					});
					this.div._toggle_is_closed = true;
					u.saveNodeCookie(this.div, "open", 0, {"ignore_classvars":true, "ignore_classnames":"open"});
					u.addExpandArrow(this);

					// callback
					if(typeof(this.div.headerCollapsed) == "function") {
						this.div.headerCollapsed();
					}
				}
			}

			var state = u.getNodeCookie(div, "open", {"ignore_classvars":true, "ignore_classnames":"open"});
			// console.log("state:" + state + ", " + typeof(state));
			// no state value (or state value = 0), means collapsed
			if(state === 0 || (state === false && !u.hc(div, "open"))) {
				div._toggle_header.clicked();
			}
			else {
				u.addCollapseArrow(div._toggle_header);

				// add class (for detailed open settings)
				u.ac(div, "open");

				// callback
				if(typeof(div.headerExpanded) == "function") {
					div.headerExpanded();
				}
			}
		}

	}

}

