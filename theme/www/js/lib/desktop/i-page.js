Util.Objects["page"] = new function() {
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
			// u.bug("page.resized:" + u.nodeId(this));

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
			if(page.cN.scene && typeof(page.cN.scene.resized) == "function") {
				page.cN.scene.resized();
			}
		}

		// global scroll handler 
		page.scrolled = function() {
			// u.bug("page.scrolled:" + u.nodeId(this));

			page.scrolled_y = u.scrollY();

			// forward scroll event to current scene
			if(page.cN.scene && typeof(page.cN.scene.scrolled) == "function") {
				page.cN.scene.scrolled();
			}
		}

		// Page is ready
		page.ready = function() {
			u.bug("page.ready:" + u.nodeId(this));

			// page is ready to be shown - only initalize if not already shown
			if(!this.is_ready) {

				// page is ready
				this.is_ready = true;

				// set resize handler
				u.e.addWindowEvent(this, "resize", this.resized);
				// set scroll handler
				u.e.addWindowEvent(this, "scroll", this.scrolled);

				// Initialize header
				this.initHeader();

				// Initial size adjustment
				this.resized();
			}

		}

		// initialize header
		page.initHeader = function() {
			var frontpage_link = u.qs("li.front a", this.nN);
			if(frontpage_link) {
				var logo = u.ie(this.hN, "a", {"class":"logo", "href":frontpage_link.href, "html": 'KBHFF <span class="highlight">' + document.title + '</span>'});
				u.ce(logo, {"type":"link"});
				frontpage_link.parentNode.remove();
			}

		}

		// ready to start page builing process
		page.ready();
	}
}

u.e.addDOMReadyEvent(u.init);
