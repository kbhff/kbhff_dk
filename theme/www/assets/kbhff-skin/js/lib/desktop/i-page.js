Util.Modules["page"] = new function() {
	this.init = function(page) {

		// header reference
		page.hN = u.qs("#header");
		page.hN.ul_service = u.qs("ul.servicenavigation", page.hN);

		// content reference
		page.cN = u.qs("#content", page);

		// navigation reference
		page.nN = u.qs("#navigation", page);
		page.nN = page.insertBefore(page.nN, page.cN);

		// footer reference
		page.fN = u.qs("#footer");
		page.fN.ul_service = u.qs("ul.servicenavigation", page.fN);


		// global resize handler 
		page.resized = function() {
			// u.bug("page.resized:", this);

			this.browser_h = u.browserH();
			this.browser_w = u.browserW();

			// adjust content height
			this.available_height = this.browser_h - this.hN.offsetHeight - this.nN.offsetHeight - this.fN.offsetHeight;

//			u.bug("page.cN.offsetHeight:" + page.cN.offsetHeight)

			u.as(this.cN, "min-height", "auto");
			if(this.available_height >= this.cN.offsetHeight) {
				u.as(this.cN, "min-height", this.available_height+"px", false);
			}


			// forward scroll event to current scene
			if(this.cN && this.cN.scene && typeof(this.cN.scene.resized) == "function") {
				this.cN.scene.resized();
			}
		}

		// global scroll handler 
		page.scrolled = function() {
			// u.bug("page.scrolled:", this);

			page.scrolled_y = u.scrollY();

			// forward scroll event to current scene
			if(this.cN && this.cN.scene && typeof(this.cN.scene.scrolled) == "function") {
				this.cN.scene.scrolled();
			}
		}

		// Page is ready
		page.ready = function() {
			// u.bug("page.ready", this);

			// page is ready to be shown - only initalize if not already shown
			if(!this.is_ready) {

				// page is ready
				this.is_ready = true;

				this.cN.scene = u.qs(".scene", this);

				// set resize handler
				u.e.addWindowEvent(this, "resize", this.resized);
				// set scroll handler
				u.e.addWindowEvent(this, "scroll", this.scrolled);

				// Initialize header
				this.initHeader();

				// Initialize navigation
				this.initNavigation();

				// accept cookies?
				this.acceptCookies();

				// Initial size adjustment
				this.resized();
			}

		}

		// initialize header
		page.initHeader = function() {
			// var logo = u.ie(this.hN, "a", {"class":"logo", "href":"/","html": 'KBHFF <span class="highlight">' + document.title + '</span>'});
			// u.ce(logo, {"type":"link"});
		}

		// initialize navigation
		page.initNavigation = function() {

			page.nN_nodes = u.qsa("li.indent0", page.nN);
			
			var z_index_counter = 100;

			for (var i = 0; i < page.nN_nodes.length; i++) {
				var nav_node = page.nN_nodes[i];
				nav_node.subnav = u.qs("ul", nav_node);
								
				if (nav_node.subnav) {
					
					u.e.hover(nav_node, {
						"delay":"200"
					});

					nav_node.is_over = false;

					nav_node.over = function(event) {
						nav_node.is_over = true;

						z_index_counter++;

						u.ass(this.subnav, {
							"display":"block",
							"z-index": z_index_counter
						});
						
						u.a.transition(this.subnav, "all 0.3s ease-out")

						u.ass(this.subnav, {
							"opacity":"1"
						});
					}
					
					nav_node.out = function(event) {
						nav_node.is_over = false;

						this.subnav.transitioned = function() {
							if(!nav_node.is_over) {
								u.ass(this, {
									"display":"none"
								});
							}
						};

						u.a.transition(this.subnav, "all 0.15s ease-out");
						
						u.ass(this.subnav, {
							"opacity":"0"
						});

					}
				}
			}
		}

		// show accept cookies dialogue
		page.acceptCookies = function() {
			// u.bug("acceptCookies", u.terms_version);

			// show terms notification
			if(u.terms_version && !u.getCookie(u.terms_version)) {


				var terms = u.ie(document.body, "div", {"class":"terms_notification"});
				u.ae(terms, "h3", {"html":u.stringOr(u.txt["terms-headline"], "Flere grøntsager, <br />færre kager")});
				u.ae(terms, "p", {"html":u.stringOr(u.txt["terms-paragraph"], "Vi beskytter dit privatliv og bruger kun funktionelle cookies.")});

				var bn_accept = u.ae(terms, "a", {"class":"accept", "html":u.stringOr(u.txt["terms-accept"], "Accepter")});
				bn_accept.terms = terms;
				u.ce(bn_accept);
				bn_accept.clicked = function() {
					this.terms.parentNode.removeChild(this.terms);
					u.saveCookie(u.terms_version, true, {"path":"/", "expires":false});
				}

				if(!location.href.match(u.terms_link)) {
					var bn_details = u.ae(terms, "a", {"class":"details", "html":u.stringOr(u.txt["terms-details"], "Læs mere"), "href":u.terms_link});
					u.ce(bn_details, {"type":"link"});
				}

				// show terms/cookie approval
				u.a.transition(terms, "all 0.5s ease-in");
				u.ass(terms, {
					"opacity": 1
				});

			}

		}

		// ready to start page builing process
		page.ready();
	}
}

u.e.addDOMReadyEvent(u.init);
