Util.Modules["page"] = new function() {
	this.init = function(page) {

		// header reference
		page.hN = u.qs("#header");
		page.hN.service = u.qs("ul.servicenavigation", page.hN);

		// content reference
		page.cN = u.qs("#content", page);

		// navigation reference
		page.nN = u.qs("#navigation", page);
		page.nN = u.ie(page.hN, page.nN);
		page.nN.nav = u.qs("ul.navigation", page.nN)

		// footer reference
		page.fN = u.qs("#footer");
		page.fN.service = u.qs("ul.servicenavigation", page.fN);


		// global resize handler 
		page.resized = function() {
			// u.bug("page.resized:", this);

			this.browser_h = u.browserH();
			this.browser_w = u.browserW();

			// adjust content height
			this.available_height = this.browser_h - this.hN.offsetHeight - this.nN.offsetHeight - this.fN.offsetHeight;

//			u.bug("page.cN.offsetHeight:" + page.cN.offsetHeight)

			if(this.bn_nav) {
				// Update navigation if open
				if (this.bn_nav.is_open) {
					// Update heights
					u.ass(page.hN, {
						"height":window.innerHeight + "px"
					});

					u.ass(page.nN, {
						"height":(window.innerHeight - page.hN.service.offsetHeight) + "px"
					});

					// Update drag coordinates
					u.e.setDragPosition(page.nN.nav, 0, 0);
					u.e.setDragBoundaries(page.nN.nav, page.nN);
				}
			}


			u.as(this.cN, "min-height", "auto");
			if(this.available_height >= this.cN.offsetHeight) {
				u.as(this.cN, "min-height", this.available_height+"px", false);
			}


			// forward scroll event to current scene
			if(this.cN && this.cN.scene && typeof(this.cN.scene.resized) == "function") {
				this.cN.scene.resized();
			}
		}

		// iOS scroll fix
		page.fixiOSScroll = function() {

			u.ass(this.hN, {
				"position":"absolute",
			});


			u.ass(this.hN, {
				"position":"fixed",
			});

		}

		// global scroll handler 
		page.scrolled = function() {
			// u.bug("page.scrolled:", this);

			// Fix issue with fixed element after scroll
			u.t.resetTimer(this.t_fix);
			this.t_fix = u.t.setTimer(this, "fixiOSScroll", 200);

			page.scrolled_y = u.scrollY();

			// forward scroll event to current scene
			if(this.cN && this.cN.scene && typeof(this.cN.scene.scrolled) == "function") {
				this.cN.scene.scrolled();
			}
		}

		// global orientationchange handler
		page.orientationchanged = function() {

			// forward scroll event to current scene
			if(this.cN && this.cN.scene && typeof(this.cN.scene.orientationchanged) == "function") {
				this.cN.scene.orientationchanged();
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

				// set orientation change handler
				u.e.addWindowEvent(this, "orientationchange", this.orientationchanged);

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


			this.nN.list = u.qs("ul.navigation", this.nN);


			// create burger menu
			this.bn_nav = u.qs(".servicenavigation li.navigation", this.hN);
			if(this.bn_nav) {
				u.ae(this.bn_nav, "div");
				u.ae(this.bn_nav, "div");
				u.ae(this.bn_nav, "div");

				// enable nav link
				u.ce(this.bn_nav);
				this.bn_nav.clicked = function(event) {

					// close navigation
					if(this.is_open) {
						// Update open state
						this.is_open = false;
						u.rc(this, "open");

						// var i, node;
						// // set hide animation for nav nodes
						// for(i = 0; node = page.nN.nodes[i]; i++) {
						//
						// 	u.a.transition(node, "all 0.2s ease-in "+(i*100)+"ms");
						// 	u.ass(node, {
						// 		"opacity": 0,
						// 		"transform":"translate(0, -30px)"
						// 	});
						// }

						// hide navigation when hidden
						// page.hN.transitioned = function() {
							u.ass(page.nN, {
								"display": "none"
							});
						// }

						// // collapse header
						// u.a.transition(page.nN, "all 0.3s ease-in");
						// u.ass(page.nN, {
						// 	"height": "0px"
						// });

						// Disable nav scroll 
						u.ass(page.nN, {
							"overflow-y":"hidden"
						});

						// Enable body scroll
						u.ass(page.parentNode, {
							"overflow-y":"scroll"
						});

					}
					// open navigation
					else {
						// Update open state
						this.is_open = true;
						u.ac(this, "open");

						// Clear hN transitioned, in order to prevent bugs
						// delete page.hN.transitioned;

						// var i, node;
						// // set initial animation state for nav nodes
						// for(i = 0; node = page.nN.nodes[i]; i++) {
						// 	u.ass(node, {
						// 		"opacity": 0,
						// 		"transform":"translate(0, 30px)"
						// 	});
						// }

						// set animation for header
						// u.a.transition(page.nN, "all 0.2s ease-in");

						// Set height of hN
						// u.ass(page.hN, {
						// 	"height": window.innerHeight+"px",
						// });

						// Set height on navigation
						u.ass(page.nN, {
							"height":(window.innerHeight - page.hN.service.offsetHeight) + "px"
						});

						u.ass(page.nN, {
							"display": "block"
						});

						// // set animation for nav nodes
						// for(i = 0; node = page.nN.nodes[i]; i++) {
						//
						// 	u.a.transition(node, "all 0.3s ease-in "+(100 + (i*100))+"ms");
						// 	u.ass(node, {
						// 		"opacity": 1,
						// 		"transform":"translate(0, 0)"
						// 	});
						// }
						
						// Enable nav scroll 
						u.ass(page.nN, {
							"overflow-y":"scroll"
						});

						// Disable body scroll
						u.ass(page.parentNode, {
							"overflow-y":"hidden"
						});
					}

					// Update drag coordinates
					u.e.setDragPosition(page.nN.nav, 0, 0);
					u.e.setDragBoundaries(page.nN.nav, page.nN);

				}
				// enable dragging on navigation
				u.e.drag(this.nN.nav, this.nN, {"strict":false, "elastica":200, "vertical_lock":true, "overflow":"scroll"});
			}


			var i, node;

			// append footer servicenavigation to header servicenavigation
			if(page.fN.service) {
				nodes = u.qsa("li:not(.copyright)", page.fN.service);
				for(i = 0; node = nodes[i]; i++) {
					u.ae(page.nN.list, node, {"class":"footer"});
				}
				page.fN.removeChild(page.fN.service);
			}

			// append header servicenavigation to header servicenavigation
			if(page.hN.service) {
				nodes = u.qsa("li:not(.navigation)", page.hN.service);
				for(i = 0; node = nodes[i]; i++) {
					u.ae(page.nN.list, node, {"class":"header"});
				}
			}

			var i, node, nodes;
			// enable animation on submenus and logo
			nodes = u.qsa("#navigation li,a.logo", page.hN);
			for(i = 0; node = nodes[i]; i++) {

				// build first living proof model of CEL clickableElementLink
				u.ce(node, {"type":"link"});

				// // add over and out animation
				// u.e.hover(node);
				// node.over = function() {
				//
				// 	this.transitioned = function() {
				//
				// 		this.transitioned = function() {
				// 			u.a.transition(this, "none");
				// 		}
				//
				// 		u.a.transition(this, "all 0.1s ease-in-out");
				// 		u.a.scale(this, 1.15);
				// 	}
				//
				// 	u.a.transition(this, "all 0.1s ease-in-out");
				// 	u.a.scale(this, 1.22);
				// }
				// node.out = function() {
				//
				// 	this.transitioned = function() {
				//
				// 		this.transitioned = function() {
				// 			u.a.transition(this, "none");
				// 		}
				//
				// 		u.a.transition(this, "all 0.1s ease-in-out");
				// 		u.a.scale(this, 1);
				// 	}
				//
				// 	u.a.transition(this, "all 0.1s ease-in-out");
				// 	u.a.scale(this, 0.9);
				// }

			}

			// get clean set of navigation nodes (for animation on open and close)
			page.nN.nodes = u.qsa("li", page.nN.list);

			if(page.hN.service) {
				u.ass(page.hN.service, {
					"opacity":1
				});
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
