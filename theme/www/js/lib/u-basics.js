u.f.fixFieldHTML = function(field) {
	if(field.indicator && field.label) {
		u.ae(field.label, field.indicator);
	}
}

u.f.customHintPosition = {};
u.f.customHintPosition["string"] = function() {}
u.f.customHintPosition["email"] = function() {}
u.f.customHintPosition["number"] = function() {}
u.f.customHintPosition["integer"] = function() {}
u.f.customHintPosition["password"] = function() {}
u.f.customHintPosition["tel"] = function() {}
u.f.customHintPosition["text"] = function() {}
u.f.customHintPosition["select"] = function() {}
u.f.customHintPosition["checkbox"] = function() {}
u.f.customHintPosition["radiobuttons"] = function() {}
u.f.customHintPosition["date"] = function() {}
u.f.customHintPosition["datetime"] = function() {}
u.f.customHintPosition["files"] = function() {}
u.f.customHintPosition["html"] = function() {}


u.productFilters = function(div) {
	u.bug("productFilters", div);

	// expects div to have products s

	div.div_filter = u.qs("div.filter", div);
	div.div_filter.div = div;


	div.filterItems = function() {


		var i, node, zebra_index = 0;
		var query = this.div_filter.input.val().toLowerCase();
		u.bug(this.current_filter, query+","+this.selected_tag.tag_value);
		if(this.current_filter !== query+","+this.selected_tag.tag_value) {

			this.current_filter = query + "," + this.selected_tag.tag_value;
			// u.bug("new search:", this.current_filter);

			for(i = 0; i < this.products.length; i++) {
				node = this.products[i];
				u.rc(node, "odd");

//				u.bug("match:" + node._c.match(query) + ", " + node._c + ", " + query)
				// u.bug(query, !node.text.match(query), this.selected_tag.tag_value, node.tags, node.tags.indexOf(this.selected_tag.tag_value));
				if((!query || node.text.match(query)) && (!this.selected_tag.tag_value || node.tags.indexOf(this.selected_tag.tag_value) !== -1)) {
					node._hidden = false;
					u.rc(node, "hidden", false);
					// u.as(node, "display", "flex", false);
					if(zebra_index % 2) {
						u.ac(node, "odd");
					}
					zebra_index++;
				}
				else {
					node._hidden = true;
					u.ac(node, "hidden", false);
					// u.as(node, "display", "none", false);
				}
			}

		}


		u.rc(this, "filtering");

	}


	div.div_filter.form = u.qs("form", div.div_filter);


	// Init all product nodes
	// Index text and tags for search
	var i, node, j, text_node, tag_node;

	for(i = 0; i < div.products.length; i++) {
		node = div.products[i];

		node.text = "";
		node.tags = [];

		var text_nodes = u.qsa("span.name", node);
		for(j = 0; j < text_nodes.length; j++) {
			text_node = text_nodes[j];
			node.text += u.text(text_node).toLowerCase().trim().replace(/[\n\t]+/g, " ")+" ";
		}

		var tag_nodes = u.qsa("li.tag", node);
		for(j = 0; j < tag_nodes.length; j++) {
			tag_node = tag_nodes[j];
			node.tags.push(tag_node.getAttribute("data-value"));
		}

	}


	// Init all tags
	// Enable selection and filtering
	div.tag_options = u.qsa("ul.tags li.tag", div.div_filter);
	
	for(i = 0; i < div.tag_options.length; i++) {
		tag_node = div.tag_options[i];
		u.bug("tag", tag_node, tag_node.getAttribute("data-value"));

		tag_node.div = div;
		tag_node.tag_value = tag_node.getAttribute("data-value");

		u.ce(tag_node);
		tag_node.clicked = function() {

			if(this.div.selected_tag) {
				u.rc(this.div.selected_tag, "selected");
			}

			this.div.selected_tag = this;
			u.ac(this, "selected");

			this.div.filterItems();

		}

	}



	u.f.init(div.div_filter.form);
	div.div_filter.input = div.div_filter.form.inputs["product_search"];

	div.div_filter.input.div = div;
	div.div_filter.input.updated = function() {

		u.t.resetTimer(this.t_filter);
		this.t_filter = u.t.setTimer(this.div, "filterItems", 400);

		u.ac(this.div, "filtering");
	}

	// Preselect all option
	var all_tag_node = u.qs("ul.tags li.tag.all", div.div_filter);
	all_tag_node.clicked();

}